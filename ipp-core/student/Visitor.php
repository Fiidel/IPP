<?php

namespace IPP\Student;

use Exception;
use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Student\Instruction\AndInstruction;
use IPP\Student\Instruction\ArgTypeEnum;
use IPP\Student\Instruction\ArithmeticInstruction;
use IPP\Student\Instruction\ConditionalJumpInstruction;
use IPP\Student\LinkedList\VarLinkedList;
use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\ExitInstruction;
use IPP\Student\Instruction\FrameInstruction;
use IPP\Student\Instruction\Int2CharInstruction;
use IPP\Student\Instruction\JumpInstruction;
use IPP\Student\Instruction\LabelInstruction;
use IPP\Student\Instruction\MoveInstruction;
use IPP\Student\Instruction\NotInstruction;
use IPP\Student\Instruction\OperationCodeEnum;
use IPP\Student\Instruction\OrInstruction;
use IPP\Student\Instruction\PlaceholderInstruction;
use IPP\Student\Instruction\ReadInstruction;
use IPP\Student\Instruction\RelationInstruction;
use IPP\Student\Instruction\Str2IntInstruction;
use IPP\Student\Instruction\StringManipulationInstruction;
use IPP\Student\Instruction\StrlenInstruction;
use IPP\Student\Instruction\TypeInstruction;
use IPP\Student\Instruction\WriteInstruction;
use IPP\Student\LinkedList\VarListNode;

class Visitor
{
    private OutputWriter $stdout;
    private InputReader $input;
    private VarLinkedList $globalFrame;
    private ?VarLinkedList $temporaryFrame;
    private array $localFrames;

    public function __construct(OutputWriter $stdout, InputReader $input)
    {
        $this->stdout = $stdout;
        $this->input = $input;
        $this->globalFrame = new VarLinkedList;
        $this->temporaryFrame = null;
        $this->localFrames = [];
    }

    // ===========================================
    // HELPER METHODS
    // ===========================================

    private function GetDeclaredVariable(string $variable) : ?VarListNode
    {
        $frame = preg_filter("/@(.*)$/", "", $variable);
        $identifier = preg_filter("/^(.*)@/", "", $variable);

        if ($frame == "GF")
        {
            return $this->RetrieveVarFromFrame($identifier, $this->globalFrame, $frame);
        }
        else if ($frame == "TF")
        {
            // check TF exists
            if ($this->temporaryFrame == null)
            {
                throw new Exception("TF doesn't exist.", 55);
            }

            return $this->RetrieveVarFromFrame($identifier, $this->temporaryFrame, $frame);
        }
        else if ($frame == "LF")
        {
            // get latest LF
            $localFrame = end($this->localFrames);

            // check LF exists
            if ($localFrame == false)
            {
                throw new Exception("LF doesn't exist.", 55);
            }

            return $this->RetrieveVarFromFrame($identifier, $localFrame, $frame);
        }
        else
        {
            return null;
        }
    }

    private function RetrieveVarFromFrame($identifier, $frameList, $frameName)
    {
        $var = $frameList->GetVarWithIdentifier($identifier);
        if ($var == null)
        {
            throw new Exception("Variable $identifier is not declared in $frameName.", 54);
        }
        else
        {
            return $var;
        }
    }

    private function GetValueBasedOnType(ArgTypeEnum $argType, string $argValue)
    {
        // for var
        if ($argType == ArgTypeEnum::var)
        {
            $varNode = $this->GetDeclaredVariable($argValue);
            // GetDeclaredVariable() is expected to exit if var is not declared, otherwise a null check is necessary here:
            $value = $varNode->getValue();
        }

        // for const
        else
        {
            switch ($argType)
            {
                case ArgTypeEnum::string:
                    $value = $argValue;
                    break;
                
                case ArgTypeEnum::int:
                    $value = (int) $argValue;
                    break;

                case ArgTypeEnum::bool:
                    if ($argValue == "true")
                    {
                        $value = true;
                    }
                    else
                    {
                        $value = false;
                    }
                    break;

                case ArgTypeEnum::nil:
                    $value = null;
                    break;

                case ArgTypeEnum::label:
                case ArgTypeEnum::type:
                    $value = $argValue;
                    break;

                default:
                    break;
            }
        }
        return $value;
    }

    private function Escape2ASCII(string $value) : string
    {
        // detect and extract unicode
        preg_match_all("/\\\\([0-9]{3})/", $value, $matches);

        foreach ($matches[0] as $match)
        {
            // get ASCII of extracted unicode
            $ascii = chr((int) (substr($match, 1)));

            // replace detected unicode with ASCII character
            $value = str_replace($match, $ascii, $value);
        }

        return $value;
    }

    private function DeclareVariable($identifier, $frameList, $frameName)
    {
        $isDeclared = $frameList->GetVarWithIdentifier($identifier);
        if ($isDeclared == null)
        {
            $frameList->InsertLast($identifier);
        }
        else
        {
            throw new Exception("Variable $identifier is already declared in $frameName.", 52);
        }
    }

    private function AssertVarValueIsNotNull($value)
    {
        if ($value == null)
        {
            throw new Exception("The value of a var operand is null. Expected not null.", 56);
        }
    }

    private function AssertIntOrIntVar(ArgTypeEnum $argType, $argValue)
    {
        if ($argType == ArgTypeEnum::int)
        {
            return true;
        }
        
        if ($argType == ArgTypeEnum::var)
        {
            $this->AssertVarValueIsNotNull($argValue);
            if (gettype($argValue) == "integer")
            {
                return true;
            }
        }

        throw new Exception("Invalid operand type. Expecting integer constant or variable with an integer value.", 53);
    }

    private function AssertBoolOrBoolVar(ArgTypeEnum $argType, $argValue)
    {
        if ($argType == ArgTypeEnum::bool)
        {
            return true;
        }
        
        if ($argType == ArgTypeEnum::var)
        {
            $this->AssertVarValueIsNotNull($argValue);
            if (gettype($argValue) == "boolean")
            {
                return true;
            }
        }

        throw new Exception("Invalid operand type. Expecting boolean constant or variable with a boolean value.", 53);
    }

    private function AssertStringOrStringVar(ArgTypeEnum $argType, $argValue)
    {
        if ($argType == ArgTypeEnum::string)
        {
            return true;
        }
        
        if ($argType == ArgTypeEnum::var)
        {
            $this->AssertVarValueIsNotNull($argValue);
            if (gettype($argValue) == "string")
            {
                return true;
            }
        }

        throw new Exception("Invalid operand type. Expecting string constant or variable with a string value.", 53);
    }

    private function AssertSameTypeOrNil(ArgTypeEnum $argType1, $argValue1, ArgTypeEnum $argType2, $argValue2)
    {
        if (($argType1 == $argType2)
            || ($argType1 == ArgTypeEnum::nil)
            || ($argType2 == ArgTypeEnum::nil)
            || ($argValue1 == null)
            || ($argValue2 == null))
        {
            return true;
        }
        else
        {
            throw new Exception("Expected operands of same type or nil.", 53);
        }
    }

    // ===========================================
    // INSTRUCTION EXECUTION
    // ===========================================

    // DEFVAR
    public function visitDefvarInstruction(DefvarInstruction $instruction)
    {
        $frame = preg_filter("/@(.*)$/", "", $instruction->GetArg1Value());
        $identifier = preg_filter("/^(.*)@/", "", $instruction->GetArg1Value());
        
        if ($frame == "GF")
        {
            $this->DeclareVariable($identifier, $this->globalFrame, $frame);
        }
        else if ($frame == "TF")
        {
            // check TF exists
            if ($this->temporaryFrame == null)
            {
                throw new Exception("TF doesn't exist.", 55);
            }

            $this->DeclareVariable($identifier, $this->temporaryFrame, $frame);
        }
        else if ($frame == "LF")
        {
            // get latest LF
            $localFrame = end($this->localFrames);

            // check LF exists
            if ($localFrame == false)
            {
                throw new Exception("LF doesn't exist.", 55);
            }

            $this->DeclareVariable($identifier, $localFrame, $frame);
        }
    }

    // MOVE
    public function visitMoveInstruction(MoveInstruction $instruction)
    {
        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        // save value
        $var->setValue($value);
    }

    // ADD
    public function visitArithmeticInstruction(ArithmeticInstruction $instruction)
    {        
        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // operand type check
        $this->AssertIntOrIntVar($argType1, $value1);
        $this->AssertIntOrIntVar($argType2, $value2);

        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::ADD:
                $result = $value1 + $value2;
                break;
            
            case OperationCodeEnum::SUB:
                $result = $value1 - $value2;
                break;

            case OperationCodeEnum::MUL:
                $result = $value1 * $value2;
                break;

            case OperationCodeEnum::IDIV:
                if ($value2 == 0)
                {
                    throw new Exception("Division by 0.", 57);
                }
                else
                {
                    $result = (int) ($value1 / $value2);
                }
                break;

            default:
                break;
        }

        $var->setValue($result);
    }

    // WRITE
    public function visitWriteInstruction(WriteInstruction $instruction)
    {
        $argType = $instruction->getArg1Type();
        $argValue = $instruction->getArg1Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        // some conversions are needed
        $valueType = gettype($value);
        if ($valueType == "boolean")
        {
            if ($value)
            {
                $value = "true";
            }
            else
            {
                $value = "false";
            }
        }
        else if ($valueType == "NULL")
        {
            $value = "";
        }
        else if ($valueType == "string")
        {
            $value = $this->Escape2ASCII($value);
        }
        
        $this->stdout->writeString($value);
    }

    // LABEL
    public function visitLabelInstruction(LabelInstruction $instruction)
    {
    }

    // JUMP
    public function visitJumpInstruction(JumpInstruction $instruction) : bool
    {
        return true;
    }

    // CONDITIONAL JUMP
    public function visitConditionalJumpInstruction(ConditionalJumpInstruction $instruction) : bool
    {
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // error check - operands must be of the same type or nil
        $this->AssertSameTypeOrNil($argType1, $value1, $argType2, $value2);

        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::JUMPIFEQ:
                if ($value1 == $value2)
                {
                    $jump = true;
                }
                else
                {
                    $jump = false;
                }
                break;
            
            case OperationCodeEnum::JUMPIFNEQ:
                if ($value1 != $value2)
                {
                    $jump = true;
                }
                else
                {
                    $jump = false;
                }
                break;

            default:
                $jump = false;
                break;
        }

        return $jump;
    }

    // EXIT
    public function visitExitInstruction(ExitInstruction $instruction)
    {
        $argType = $instruction->getArg1Type();
        $argValue = $instruction->getArg1Value();
        $exitCode = $this->GetValueBasedOnType($argType, $argValue);
        
        // exit code must be a number from 0 to 9, otherwise error
        if ($exitCode >= 0 && $exitCode <= 9)
        {
            exit($exitCode);
        }
        else
        {
            throw new Exception("Exit code of EXIT must be between 0 and 9 (included).", 57);
        }
    }

    // AND
    public function visitAndInstruction(AndInstruction $instruction)
    {
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // error check - operands are bool
        $this->AssertBoolOrBoolVar($argType1, $value1);
        $this->AssertBoolOrBoolVar($argType2, $value2);

        $var = $this->GetDeclaredVariable($instruction->getArg1Value());
        if ($value1 && $value2)
        {
            $var->setValue(true);
        }
        else
        {
            $var->setValue(false);
        }
    }

    // OR
    public function visitOrInstruction(OrInstruction $instruction)
    {
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // error check - operands are bool
        $this->AssertBoolOrBoolVar($argType1, $value1);
        $this->AssertBoolOrBoolVar($argType2, $value2);

        $var = $this->GetDeclaredVariable($instruction->getArg1Value());
        if ($value1 || $value2)
        {
            $var->setValue(true);
        }
        else
        {
            $var->setValue(false);
        }
    }

    // NOT
    public function visitNotInstruction(NotInstruction $instruction)
    {
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        // error check - operands are bool
        $this->AssertBoolOrBoolVar($argType, $value);
        
        $var = $this->GetDeclaredVariable($instruction->getArg1Value());
        if ($value)
        {
            $var->setValue(false);
        }
        else
        {
            $var->setValue(true);
        }
    }

    // RELATION
    public function visitRelationInstruction(RelationInstruction $instruction)
    {
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        $opcode = $instruction->getOpcode();

        switch ($opcode)
        {
            case OperationCodeEnum::LT:
                $result = $this->EvaluateAnyRelation($value1, $value2, $opcode);
                break;
            
            case OperationCodeEnum::GT:
                $result = $this->EvaluateAnyRelation($value1, $value2, $opcode);
                break;

            case OperationCodeEnum::EQ:
                $result = $this->EvaluateEqRelation($value1, $value2, $opcode);
                break;

            default:
                break;
        }

        $var = $this->GetDeclaredVariable($instruction->getArg1Value());
        $var->setValue($result);
    }

    private function EvaluateAnyRelation($value1, $value2, OperationCodeEnum $type) : bool
    {
        if (gettype($value1) == "integer" && gettype($value2) == "integer")
        {
            if ($type == OperationCodeEnum::LT)
            {
                return ($value1 < $value2);
            }
            else if ($type == OperationCodeEnum::GT)
            {
                return ($value1 > $value2);
            }
            else if ($type == OperationCodeEnum::EQ)
            {
                return ($value1 == $value2);
            }
            else
            {
                throw new Exception("Unrecognized opcode for relation instruction.", 52);
            }
        }
        else if (gettype($value1) == "boolean" && gettype($value2) == "boolean")
        {
            if ($type == OperationCodeEnum::LT)
            {
                if ($value1 == false && $value2 == true)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else if ($type == OperationCodeEnum::GT)
            {
                if ($value1 == true && $value2 == false)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else if ($type == OperationCodeEnum::EQ)
            {
                return ($value1 == $value2);
            }
            else
            {
                throw new Exception("Unrecognized opcode for relation instruction.", 52);
            }
        }
        else if (gettype($value1) == "string" && gettype($value2) == "string")
        {
            $strcmpValue = strcmp($value1, $value2);
            if ($type == OperationCodeEnum::LT)
            {
                return ($strcmpValue < 0);
            }
            else if ($type == OperationCodeEnum::GT)
            {
                return ($strcmpValue > 0);
            }
            else if ($type == OperationCodeEnum::EQ)
            {
                return ($strcmpValue == 0);
            }
            else
            {
                throw new Exception("Unrecognized opcode for relation instruction.", 52);
            }
        }
        else
        {
            throw new Exception("Incompatible types for a relation instruction.", 53);
        }
    }

    private function EvaluateEqRelation($value1, $value2, OperationCodeEnum $type) : bool
    {
        if (gettype($value1) == "NULL") 
        {
            return ($value2 == null);
        }
        else if (gettype($value2) == "NULL")
        {
            return ($value1 == null);
        }
        else
        {
            return $this->EvaluateAnyRelation($value1, $value2, $type);
        }
    }

    // STRING MANIPULATION
    public function visitStringManipulationInstruction(StringManipulationInstruction $instruction)
    {
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // error checking - operands must be string
        $this->AssertStringOrStringVar($argType1, $value1);
        $this->AssertStringOrStringVar($argType2, $value2);

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());

        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::CONCAT:
                $result = $value1 . $value2;
                break;
            
            case OperationCodeEnum::GETCHAR:
                // necessary to convert to ASCII, otherwise escape sequences will create an offset
                $string = $this->Escape2ASCII($value1);
                $index = $value2;
                if (mb_strlen($string, "UTF-8") <= $index)
                {
                    throw new Exception("String index out of range.", 58);
                }
                else
                {
                    // need to use mb_substr, otherwise there's an offset
                    $result = mb_substr($string, $index, 1, "UTF-8");
                }
                break;

            case OperationCodeEnum::SETCHAR:
                // necessary to convert to ASCII, otherwise escape sequences will create an offset
                $string = $this->Escape2ASCII((string) $var->getValue());
                $index = $value1;
                if (mb_strlen($string, "UTF-8") <= $index)
                {
                    throw new Exception("String index out of range.", 58);
                }
                else
                {
                    $charToReplaceWith = mb_substr($value2, 0, 1, "UTF-8");

                    // a little magic to account for special UTF-8 char offsets
                    // the string is parsed into sections delimited by the index characters and then concatenated with the correct char
                    $startOfString = mb_substr($string, 0, $index, "UTF-8"); // length is set to $index -> takes substr up until the wanted position
                    $endOfString = mb_substr($string, $index+1, null, "UTF-8"); // starts at $index+1 -> takes substr from after the wanted position until the end

                    $result = $startOfString . $charToReplaceWith . $endOfString;
                }
                break;

            default:
                break;
        }

        $var->setValue($result);
    }

    // STRLEN
    public function visitStrlenInstruction(StrlenInstruction $instruction)
    {
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        // error checking - operand must be string
        $this->AssertStringOrStringVar($argType, $value);

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());

        // necessary to convert to ASCII, otherwise escape sequences will create an offset
        $string = $this->Escape2ASCII($value);

        $var->setValue(mb_strlen($string, "UTF-8"));
    }

    // TYPE
    public function visitTypeInstruction(TypeInstruction $instruction)
    {
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();

        switch ($argType)
        {
            case ArgTypeEnum::int:
                $result = "int";
                break;

            case ArgTypeEnum::bool:
                $result = "bool";
                break;

            case ArgTypeEnum::string:
                $result = "string";
                break;

            case ArgTypeEnum::nil:
                $result = "nil";
                break;

            case ArgTypeEnum::var:
                $value = $this->GetValueBasedOnType($argType, $argValue);
                if ($value == null)
                {
                    $result = "";
                }
                else
                {
                    switch (gettype($value))
                    {
                        case "integer":
                            $result = "int";
                            break;
                        
                        case "boolean":
                            $result = "bool";
                            break;

                        case "string":
                            $result = "string";
                            break;

                        default:
                            break;
                    }
                }
                break;
            
            default:
                break;
        }

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        $var->setValue($result);
    }

    // READ
    public function visitReadInstruction(ReadInstruction $instruction)
    {
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $readType = $this->GetValueBasedOnType($argType, $argValue);

        $readValue = $this->input->readString();

        if ($readValue == null)
        {
            $result = null;
        }
        else if ($readType == "int")
        {
            $result = (int) $readValue;
        }
        else if ($readType == "bool")
        {
            $result = (boolean) $readValue;
        }
        else if ($readType == "string")
        {
            $result = (string) $readValue;
        }
        else
        {
            $result = null;
        }

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        $var->setValue($result);
    }

    // INT2CHAR
    public function visitInt2CharInstruction(Int2CharInstruction $instruction)
    {
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $intValue = $this->GetValueBasedOnType($argType, $argValue);

        // error checking - operand must be int
        $this->AssertIntOrIntVar($argType, $intValue);

        $char = mb_chr($intValue, "UTF-8");
        if ($char == false)
        {
            throw new Exception("INT2CHAR failed. Invalid integer value.", 58);
        }

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        $var->setValue($char);
    }

    // STR2INT
    public function visitStr2IntInstruction(Str2IntInstruction $instruction)
    {        
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        // error checking - first operand must be string, second int
        $this->AssertStringOrStringVar($argType1, $value2);
        $this->AssertIntOrIntVar($argType2, $value2);

        // necessary to convert to ASCII, otherwise escape sequences will create an offset
        $string = $this->Escape2ASCII($value1);
        $index = $value2;
        if (mb_strlen($string, "UTF-8") <= $index)
        {
            throw new Exception("String index out of range.", 58);
        }

        $char = mb_substr($string, $index, 1, "UTF-8");
        $unicode = mb_ord($char, "UTF-8");
        if ($unicode == false)
        {
            throw new Exception("STR2INT failed. Char conversion failure.", 58);
        }

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        $var->setValue($unicode);
    }

    // FRAME
    public function visitFrameInstruction(FrameInstruction $instruction)
    {
        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::CREATEFRAME:
                $this->temporaryFrame = new VarLinkedList;
                break;

            case OperationCodeEnum::PUSHFRAME:
                // error checking: no TF defined
                if ($this->temporaryFrame == null)
                {
                    throw new Exception("PUSHFRAME failed. No TF defined.", 55);
                }

                array_push($this->localFrames, $this->temporaryFrame);
                $this->temporaryFrame = null;
                break;

            case OperationCodeEnum::POPFRAME:
                // error checking: no LF available
                if (end($this->localFrames) == false)
                {
                    throw new Exception("POPFRAME failed. No LF available.", 55);
                }

                array_pop($this->localFrames);
                break;
            
            default:
                break;
        }
    }
    
    // PLACEHOLDER
    public function visitPlaceholderInstruction(PlaceholderInstruction $instruction)
    {
    }

    // ===========================================
    // DEBUG
    // ===========================================

    public function PrintGF()
    {
        echo "\nCurrent Global Frame\n------------------\n";
        $currentVar = $this->globalFrame->getHead();
        while ($currentVar != null)
        {
            echo $currentVar->getIdentifier() . ": " . $currentVar->getValue() . "\n";
            $currentVar = $currentVar->getNextNode();
        }
    }
}