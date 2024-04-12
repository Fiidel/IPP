<?php

namespace IPP\Student\LinkedList;

use IPP\Student\Instruction\InstructionBase;

class LinkedList
{
    private $head;

    public function __construct()
    {
        $this->head = null;
    }

    public function InsertLast(InstructionBase $instruction)
    {
        $node = new LinkedListNode($instruction);
        
        if ($this->head == null)
        {
            $this->head = $node;
        }
        else
        {
            $currentNode = $this->head;
            while ($currentNode->getNextNode() != null)
            {
                $currentNode = $currentNode->getNextNode();
            }
            $currentNode->setNextNode($node);
        }
    }
}