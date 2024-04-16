<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class FrameInstruction extends InstructionBase
{
    // CONSTRUCTOR
    public function __construct(OperationCodeEnum $instructionType, int $order)
    {
        parent::__construct($instructionType, $order);
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : void
    {
        $visitor->visitFrameInstruction($this);
    }
}