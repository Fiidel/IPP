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

    private int $order;
    public function getOrder() : int
    {
        return $this->order;
    }

    // CONSTRUCTOR
    public function __construct(OperationCodeEnum $opcode, int $order)
    {
        $this->opcode = $opcode;
        $this->order = $order;
    }
}