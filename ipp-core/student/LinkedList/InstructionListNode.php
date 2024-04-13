<?php

namespace IPP\Student\LinkedList;

use IPP\Student\Instruction\InstructionBase;

class InstructionListNode
{
    private $instruction;
    public function getInstruction() : ?InstructionBase
    {
        return $this->instruction;
    }

    private $nextNode;
    public function getNextNode() : ?InstructionListNode
    {
        return $this->nextNode;
    }
    public function setNextNode(InstructionListNode $node)
    {
        $this->nextNode = $node;
    }

    public function __construct(InstructionBase $instruction)
    {
        $this->instruction = $instruction;
        $this->nextNode = null;
    }
}