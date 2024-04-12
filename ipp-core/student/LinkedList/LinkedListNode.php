<?php

namespace IPP\Student\LinkedList;

use IPP\Student\Instruction\InstructionBase;

class LinkedListNode
{
    private $instruction;
    public function getInstruction() : InstructionBase
    {
        return $this->instruction;
    }
    public function setInstruction(InstructionBase $instruction)
    {
        $this->instruction = $instruction;
    }

    private $nextInstruction;
    public function getNextInstruction() : InstructionBase
    {
        return $this->nextInstruction;
    }
    public function setNextInstruction(InstructionBase $instruction)
    {
        $this->nextInstruction = $instruction;
    }
}