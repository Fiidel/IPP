<?php

namespace IPP\Student\Instruction;

use DOMNode;

class MoveInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : MoveInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get2ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value);

        $instruction = new MoveInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);
        return $instruction;
    }
}