<?php

namespace IPP\Student;

use IPP\Core\Interface\OutputWriter;
use IPP\Student\Instruction\ArgTypeEnum;
use IPP\Student\Instruction\ArithmeticInstruction;
use IPP\Student\Instruction\ConditionalJumpInstruction;
use IPP\Student\LinkedList\VarLinkedList;
use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\ExitInstruction;
use IPP\Student\Instruction\JumpInstruction;
use IPP\Student\Instruction\LabelInstruction;
use IPP\Student\Instruction\MoveInstruction;
use IPP\Student\Instruction\OperationCodeEnum;
use IPP\Student\Instruction\PlaceholderInstruction;
use IPP\Student\Instruction\WriteInstruction;
use IPP\Student\LinkedList\VarListNode;

class Visitor
{
    private OutputWriter $stdout;
    private VarLinkedList $globalFrame;

    public function __construct(OutputWriter $stdout)
    {
        $this->stdout = $stdout;
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
    public function visitConditionalJumpInstruction(ConditionalJumpInstruction $instruction)
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