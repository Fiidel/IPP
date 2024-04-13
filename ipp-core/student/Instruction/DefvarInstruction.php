<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class DefvarInstruction extends InstructionBase
{
    // PROPERTIES
    private ArgTypeEnum $arg1type;
    
    private string $arg1value;
    public function GetArg1Value() : string
    {
        return $this->arg1value;
    }

    // CONSTRUCTOR
    public function __construct
        (int $order, ArgTypeEnum $arg1type, string $arg1value)
    {
        parent::__construct(OperationCodeEnum::DEFVAR, $order);
        $this->arg1type = $arg1type;
        $this->arg1value = $arg1value;
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : void
    {
        $visitor->visitDefvarInstruction($this);
    }
}