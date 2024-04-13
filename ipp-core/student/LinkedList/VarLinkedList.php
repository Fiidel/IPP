<?php

namespace IPP\Student\LinkedList;

class VarLinkedList
{
    private $head;

    public function __construct()
    {
        $this->head = null;
    }

    public function InsertLast(string $identifier) : void
    {
        $node = new VarListNode($identifier);
        
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

    public function GetVarWithIdentifier(string $identifier) : ?VarListNode
    {
        $currentNode = $this->head;

        while ($currentNode != null && $currentNode->getIdentifier() != $identifier)
        {
            $currentNode = $currentNode->getNextNode();
        }
        
        if ($currentNode == null)
        {
            return null;
        }
        else
        {
            return $currentNode;
        }
    }
}