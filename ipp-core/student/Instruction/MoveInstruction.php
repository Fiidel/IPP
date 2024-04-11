<?php

namespace IPP\Student\Instruction;

class MoveInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgTypeEnum $arg1type;
    private string $arg1value;
    private ArgTypeEnum $arg2type;
    private string $arg2value;

    // CONSTRUCTOR
    public function __construct
        (ArgTypeEnum $arg1type, string $arg1value, ArgTypeEnum $arg2type, string $arg2value)
    {
        parent::__construct(OperationCodeEnum::MOVE);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
        $this->arg2type = $arg2type;
        $this->arg2value = $arg2value;
    }
}