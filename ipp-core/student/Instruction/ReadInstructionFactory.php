<?php

namespace IPP\Student\Instruction;

use DOMNode;

class ReadInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : ReadInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get2ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value);
        $instruction = new ReadInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);
        
        return $instruction;
    }
}