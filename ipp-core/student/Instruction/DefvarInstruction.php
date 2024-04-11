<?php

namespace IPP\Student\Instruction;

class DefvarInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgTypeEnum $arg1type;
    private string $arg1value;

    // CONSTRUCTOR
    public function __construct
        (int $order, ArgTypeEnum $arg1type, string $arg1value)
    {
        parent::__construct(OperationCodeEnum::DEFVAR, $order);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
    }
}