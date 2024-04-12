<?php

namespace IPP\Student\AST;

use IPP\Student\Instruction\InstructionBase;

class ASTNode
{
    // TODO: set to private with setters and getters?
    public $instruction;
    public $conditionJump;
    public $nextInstruction;

    public function __construct(InstructionBase $instruction)
    {
        $this->instruction = $instruction;
        $this->conditionJump = null;
        $this->nextInstruction = null;
    }
}