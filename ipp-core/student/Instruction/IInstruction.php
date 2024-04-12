<?php

namespace IPP\Student\Instruction;

use IPP\Student\Visitor;

interface IInstruction
{
    public function accept(Visitor $visitor) : void;
}
