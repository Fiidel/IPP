<?php

namespace IPP\Student\Instruction;

abstract class InstructionBase implements IInstruction
{
    private OperationCode $opcode;
    public function getOpcode() : OperationCode
    {
        return $this->opcode;
    }
    public function setOpcode($opcode) : void
    {
        $this->opcode = $opcode;
    }
}