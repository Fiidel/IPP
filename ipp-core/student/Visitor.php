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
        if ($var != null)
        {
            $type = $instruction->getArg2Type();

            // for var
            if ($type == ArgTypeEnum::var)
            {
                $varNode = $this->GetDeclaredVariable($instruction->getArg2Value());
                // GetDeclaredVariable() is expected to exit if var is not declared, otherwise a null check is necessary here:
                $value = $varNode->getValue();
            }

            // for const
            else
            {
                switch ($type)
                {
                    case ArgTypeEnum::string:
                        $value = $instruction->getArg2Value();
                        break;
                    
                    // TODO: other

                    default:
                        break;
                }
            }

            // save value
            $var->setValue($value);
        }

        // TODO: convert to proper datatype based on $instruction->GetArg2Type()
    }

    // ADD
    public function visitArithmeticInstruction(ArithmeticInstruction $instruction)
    {
        echo "Arithmetic ";
        switch ($instruction->getOpcode())
        {
            case OperationCodeEnum::ADD:
                echo "of type ADD\n";
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