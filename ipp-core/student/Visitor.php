<?php

namespace IPP\Student;

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

    public function visitMoveInstruction(MoveInstruction $instruction)
    {
        echo "Move\n";
    }
}