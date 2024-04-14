<?php

namespace IPP\Student\Instruction;

use DOMNode;

class WriteInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : WriteInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get1ArgsTypeAndValue($args, $arg1type, $arg1value);

        $instruction = new WriteInstruction($order, $arg1type, $arg1value);
        return $instruction;
    }
}