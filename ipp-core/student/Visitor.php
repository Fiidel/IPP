<?php

namespace IPP\Student;

use IPP\Student\Instruction\ArgTypeEnum;
use IPP\Student\Instruction\ArithmeticInstruction;
use IPP\Student\LinkedList\VarLinkedList;
use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\MoveInstruction;
use IPP\Student\Instruction\OperationCodeEnum;
use IPP\Student\LinkedList\VarListNode;

class Visitor
{
    private VarLinkedList $globalFrame;

    public function __construct()
    {
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
                    $value = (boolean) $argValue;
                    break;

                case ArgTypeEnum::nil:
                    $value = "";
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
                break;

            case OperationCodeEnum::MUL:
                echo "of type MUL\n";
                break;

            case OperationCodeEnum::IDIV:
                echo "of type IDIV\n";
                break;

            default:
                break;
        }

        $var->setValue($result);
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