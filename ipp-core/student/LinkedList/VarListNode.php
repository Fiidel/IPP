<?php

namespace IPP\Student\LinkedList;

class VarListNode
{
    private string $identifier;
    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    private $value;
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }

    private ?VarListNode $nextNode;
    public function getNextNode() : ?VarListNode
    {
        return $this->nextNode;
    }
    public function setNextNode(VarListNode $node)
    {
        $this->nextNode = $node;
    }

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->nextNode = null;
    }
}