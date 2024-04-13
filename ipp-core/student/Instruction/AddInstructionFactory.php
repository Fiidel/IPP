<?php

namespace IPP\Student\Instruction;

use DOMNode;

class AddInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : AddInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);

        $instruction = new AddInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        return $instruction;
    }
}