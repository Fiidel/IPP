<?php

namespace IPP\Student\Instruction;

use DOMNode;

class PlaceholderInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : PlaceholderInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $instruction = new PlaceholderInstruction($order);
        return $instruction;
    }
}