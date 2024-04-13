<?php

namespace IPP\Student;

use IPP\Student\Instruction\AddInstruction;
use IPP\Student\LinkedList\VarLinkedList;
use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\MoveInstruction;

class Visitor
{
    private VarLinkedList $globalFrame;

    public function __construct()
    {
        $this->globalFrame = new VarLinkedList;
    }

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

        $variable = preg_filter("/GF@/", "", $instruction->GetArg1Value());
        if ($variable != null)
        {
            $varInGF = $this->globalFrame->GetVarWithIdentifier($variable);
            if ($varInGF == null)
            {
                echo "var $variable not declared in GF\n";
                // TODO: error, exit code
            }
            else
            {
                $varInGF->setValue($instruction->GetArg2Value());
                // TODO: convert to proper datatype based on $instruction->GetArg2Type()
            }
        }
    }

    // ADD
    public function visitAddInstruction(AddInstruction $instruction)
    {
        echo "Add\n";
    }

    // ----------------------------------

    // DEBUG
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