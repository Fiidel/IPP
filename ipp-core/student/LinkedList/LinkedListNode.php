<?php

namespace IPP\Student\LinkedList;

use IPP\Student\Instruction\InstructionBase;

class LinkedListNode
{
    private $instruction;
    public function getInstruction() : ?InstructionBase
    {
        return $this->instruction;
    }

    private $nextNode;
    public function getNextNode() : ?LinkedListNode
    {
        return $this->nextNode;
    }
    public function setNextNode(LinkedListNode $node)
    {
        $this->nextNode = $node;
    }

    public function __construct(InstructionBase $instruction)
    {
        $this->instruction = $instruction;
        $this->nextNode = null;
    }
}