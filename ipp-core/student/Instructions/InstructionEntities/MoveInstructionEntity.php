<?php

namespace IPP\Student\Instruction;

class MoveInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgType $arg1type;
    private string $arg1value;
    private ArgType $arg2type;
    private string $arg2value;

    // CONSTRUCTOR
    public function __construct
        (ArgType $arg1type, string $arg1value, ArgType $arg2type, string $arg2value)
    {
        parent::__construct(OperationCode::MOVE);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
        $this->arg2type = $arg2type;
        $this->arg2value = $arg2value;
    }
}