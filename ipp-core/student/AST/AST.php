<?php

namespace IPP\Student\AST;

use IPP\Student\Instruction\InstructionBase;

class AST
{
    private $head;
    public function GetHead() : ?ASTNode
    {
        return $this->head;
    }

    public function __construct()
    {
        $this->head = null;
    }

    public function InsertNext(InstructionBase $instruction) : ASTNode
    {
        $node = new ASTNode($instruction);

        if ($this->head == null)
        {
            $this->head = $node;
        }
        else
        {
            $currentNode = $this->head;
            while ($currentNode->nextInstruction != null)
            {
                $currentNode = $currentNode->nextInstruction;
            }
            $currentNode->nextInstruction = $node;
        }
        return $node;
    }
}