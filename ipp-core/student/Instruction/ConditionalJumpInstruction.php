<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class ConditionalJumpInstruction extends InstructionBase
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

    private ArgTypeEnum $arg3type;
    public function getArg3Type() : ArgTypeEnum
    {
        return $this->arg3type;
    }

    private string $arg3value;
    public function getArg3Value() : string
    {
        return $this->arg3value;
    }

    // CONSTRUCTOR
    public function __construct
        (OperationCodeEnum $conditionalType, int $order, 
        ArgTypeEnum $arg1type, string $arg1value, ArgTypeEnum $arg2type, string $arg2value, ArgTypeEnum $arg3type, string $arg3value)
    {
        parent::__construct($conditionalType, $order);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
        $this->arg2type = $arg2type;
        $this->arg2value = $arg2value;
        $this->arg3type = $arg3type;
        $this->arg3value = $arg3value;
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : bool
    {
        return $visitor->visitConditionalJumpInstruction($this);
    }
}