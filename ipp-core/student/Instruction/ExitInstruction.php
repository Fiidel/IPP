<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class ExitInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgTypeEnum $arg1type;
    private string $arg1value;
    public function getArg1Value() : string
    {
        return $this->arg1value;
    }

    // CONSTRUCTOR
    public function __construct(int $order, ArgTypeEnum $arg1type, string $arg1value)
    {
        parent::__construct(OperationCodeEnum::EXIT, $order);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : void
    {
        $visitor->visitExitInstruction($this);
    }
}