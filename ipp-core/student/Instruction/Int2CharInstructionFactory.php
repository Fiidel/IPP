<?php

namespace IPP\Student\Instruction;

use DOMNode;

class Int2CharInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : Int2CharInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get2ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value);
        $instruction = new Int2CharInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);

        return $instruction;
    }
}