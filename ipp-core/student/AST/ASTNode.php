<?php

namespace IPP\Student\AST;

use IPP\Student\Instruction\InstructionBase;

class ASTNode
{
    // TODO: set to private with setters and getters?
    public InstructionBase $instruction;
    public ?ASTNode $labelNode;
    public ?ASTNode $nextInstruction;

    public function __construct(InstructionBase $instruction)
    {
        $this->instruction = $instruction;
        $this->labelNode = null;
        $this->nextInstruction = null;
    }
}