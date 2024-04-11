<?php

namespace IPP\Student\Instruction;

abstract class InstructionBase implements IInstruction
{
    // PROPERTIES
    private OperationCodeEnum $opcode;
    public function getOpcode() : OperationCodeEnum
    {
        return $this->opcode;
    }
    public function setOpcode($opcode) : void
    {
        $this->opcode = $opcode;
    }

    // CONSTRUCTOR
    public function __construct(OperationCodeEnum $opcode)
    {
        $this->opcode = $opcode;
    }
}