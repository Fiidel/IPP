<?php

namespace IPP\Student;

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
        $frame = preg_filter("/@[.]/", "", $variable);
        $identifier = preg_filter("/[.]@/", "", $variable);

        if ($frame == "GF")
        {
            $varInGF = $this->globalFrame->GetVarWithIdentifier($identifier);
            if ($varInGF == null)
            {
                echo "var $variable not declared in GF\n";
                return null;
                // TODO: error, exit code
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
                echo "var $value already declared in GF\n";
                // TODO: error, exit code
            }
        }

        // todo: other frames
    }

    // MOVE
    public function visitMoveInstruction(MoveInstruction $instruction)
    {
        echo "Move\n";

        $var = $this->GetDeclaredVariable($instruction->GetArg1Value());
        if ($var != null)
        {
            $var->setValue($instruction->GetArg2Value());
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