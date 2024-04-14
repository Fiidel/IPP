<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

class PlaceholderInstruction extends InstructionBase
{
    // CONSTRUCTOR
    public function __construct(int $order)
    {
        parent::__construct(OperationCodeEnum::PLACEHOLDER, $order);
    }

    // VISITOR ACCEPT
    public function accept(Visitor $visitor) : void
    {
        $visitor->visitPlaceholderInstruction($this);
    }
}