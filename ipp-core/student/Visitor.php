<?php

namespace IPP\Student;

use IPP\Student\Instruction\DefvarInstruction;
use IPP\Student\Instruction\MoveInstruction;

class Visitor
{
    public function visitDefvarInstruction(DefvarInstruction $instruction)
    {
        echo "Defvar\n";
    }

    public function visitMoveInstruction(MoveInstruction $instruction)
    {
        echo "Move\n";
    }
}