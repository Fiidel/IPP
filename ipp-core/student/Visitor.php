<?php

namespace IPP\Student;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Student\Instruction\AndInstruction;
use IPP\Student\Instruction\ArgTypeEnum;
use IPP\Student\Instruction\ArithmeticInstruction;
use IPP\Student\Instruction\ConditionalJumpInstruction;
use IPP\Student\LinkedList\VarLinkedList;
use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\ExitInstruction;
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

    public function __construct(OutputWriter $stdout, InputReader $input)
    {
        $this->stdout = $stdout;
        $this->input = $input;
        $this->globalFrame = new VarLinkedList;
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
            $varInGF = $this->globalFrame->GetVarWithIdentifier($identifier);
            if ($varInGF == null)
            {
                // TODO: remove echo (or print it to stderr, idk)
                echo "var $variable not declared in GF\n";
                exit(54);
            }
            else
            {
                return $varInGF;
            }
        }
        else
        {
            return null;
        }
        // TODO: other frames
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
                    // TODO: convert unicode sequences to chars in string
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

    // ===========================================
    // INSTRUCTION EXECUTION
    // ===========================================

    // DEFVAR
    public function visitDefvarInstruction(DefvarInstruction $instruction)
    {
        echo "Defvar\n";
        
        $value = preg_filter("/GF@/", "", $instruction->GetArg1Value());
        if ($value != null)
        {
            $isDeclared = $this->globalFrame->GetVarWithIdentifier($value);
            if ($isDeclared == null)
            {
                $this->globalFrame->InsertLast($value);
            }
            else
            {
                // TODO: remove echo (or print it to stderr, idk)
                echo "var $value already declared in GF\n";
                exit(52);
            }
        }

        // TODO: other frames
    }

    // MOVE
    public function visitMoveInstruction(MoveInstruction $instruction)
    {
        echo "Move\n";

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        // GetDeclaredVariable() exits if the given var isn't declared, otherwise a null check would be necessary here
        
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        // save value
        $var->setValue($value);
    }

    // ADD
    public function visitArithmeticInstruction(ArithmeticInstruction $instruction)
    {
        echo "Arithmetic ";
        
        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        // GetDeclaredVariable() exits if the given var isn't declared, otherwise a null check would be necessary here
        
        // TODO: check args are var or int
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::ADD:
                echo "of type ADD\n";
                $result = $value1 + $value2;
                break;
            
            case OperationCodeEnum::SUB:
                echo "of type SUB\n";
                $result = $value1 - $value2;
                break;

            case OperationCodeEnum::MUL:
                echo "of type MUL\n";
                $result = $value1 * $value2;
                break;

            case OperationCodeEnum::IDIV:
                echo "of type IDIV\n";
                if ($value2 == 0)
                {
                    exit(57);
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
        echo "Write\n";

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
        echo "Label\n";
    }

    // JUMP
    public function visitJumpInstruction(JumpInstruction $instruction) : bool
    {
        echo "Jump\n";
        return true;
    }

    // CONDITIONAL JUMP
    public function visitConditionalJumpInstruction(ConditionalJumpInstruction $instruction) : bool
    {
        echo "Conditional Jump\n";

        // TODO: check args are of the same type or nil
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

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
        echo "Exit\n";

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
            exit(57);
        }
    }

    // AND
    public function visitAndInstruction(AndInstruction $instruction)
    {
        echo "And\n";

        // TODO: check args types
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

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
        echo "Or\n";

        // TODO: check args types
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

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
        echo "Not\n";

        // TODO: check args type
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

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
        echo "Relation\n";

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
        }
        else
        {
            // TODO: error message?
            exit(53);
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
        echo "String manipulation\n";
        
        // TODO: check args are var or string
        $argType1 = $instruction->getArg2Type();
        $argValue1 = $instruction->getArg2Value();
        $value1 = $this->GetValueBasedOnType($argType1, $argValue1);

        $argType2 = $instruction->getArg3Type();
        $argValue2 = $instruction->getArg3Value();
        $value2 = $this->GetValueBasedOnType($argType2, $argValue2);

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
                    // TODO: error message? index out of range
                    exit(58);
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
                    // TODO: error message? index out of range
                    exit(58);
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
        echo "Strlen\n";

        // TODO: check arg is string
        $argType = $instruction->getArg2Type();
        $argValue = $instruction->getArg2Value();
        $value = $this->GetValueBasedOnType($argType, $argValue);

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());

        // necessary to convert to ASCII, otherwise escape sequences will create an offset
        $string = $this->Escape2ASCII($value);

        $var->setValue(mb_strlen($string, "UTF-8"));
    }

    // TYPE
    public function visitTypeInstruction(TypeInstruction $instruction)
    {
        echo "Type\n";

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
        echo "Read\n";

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
        echo "Int2Char\n";

        // TODO: check it's int
        // convert and save
    }

    // STR2INT
    public function visitStr2IntInstruction(Str2IntInstruction $instruction)
    {
        echo "Str2Int\n";

        // TODO: check it's string
        // get char from position, convert and save
    }
    
    // PLACEHOLDER
    public function visitPlaceholderInstruction(PlaceholderInstruction $instruction)
    {
        echo "Placeholder\n";
    }

    // ===========================================
    // DEBUG
    // ===========================================

    public function PrintGF()
    {
        echo "Current Global Frame\n------------------\n";
        $currentVar = $this->globalFrame->getHead();
        while ($currentVar != null)
        {
            echo $currentVar->getIdentifier() . ": " . $currentVar->getValue() . "\n";
            $currentVar = $currentVar->getNextNode();
        }
    }
}