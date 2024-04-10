<?php

namespace IPP\Student\Instruction;

abstract class InstructionBase implements IInstruction
{
    // PROPERTIES
    private OperationCode $opcode;
    public function getOpcode() : OperationCode
    {
        return $this->opcode;
    }
    public function setOpcode($opcode) : void
    {
        $this->opcode = $opcode;
    }

    // CONSTRUCTOR
    public function __construct(OperationCode $opcode)
    {
        $this->opcode = $opcode;
    }
}