<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class ReadInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgTypeEnum $arg1type;
    private string $arg1value;
    public function getArg1Value() : string
    {
        return $this->arg1value;
    }

    private ArgTypeEnum $arg2type;
    public function getArg2Type() : ArgTypeEnum
    {
        return $this->arg2type;
    }

    private string $arg2value;
    public function getArg2Value() : string
    {
        return $this->arg2value;
    }

    // CONSTRUCTOR
    public function __construct
        (int $order, ArgTypeEnum $arg1type, string $arg1value, ArgTypeEnum $arg2type, string $arg2value)
    {
        parent::__construct(OperationCodeEnum::READ, $order);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
        $this->arg2type = $arg2type;
        $this->arg2value = $arg2value;
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : void
    {
        $visitor->visitReadInstruction($this);
    }
}