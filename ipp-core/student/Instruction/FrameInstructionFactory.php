<?php

namespace IPP\Student\Instruction;

use DOMNode;

class FrameInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : FrameInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $instruction = new FrameInstruction($opcode, $order);
        return $instruction;
    }
}