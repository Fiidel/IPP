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

    public function InsertLast(InstructionBase $instruction) : void
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

    public function GetInstructionWithOrder(int $order) : ?InstructionBase
    {
        $currentNode = $this->head;

        while ($currentNode != null && $currentNode->getInstruction()->getOrder() != $order)
        {
            $currentNode = $currentNode->getNextNode();
        }
        return $currentNode;
    }
}