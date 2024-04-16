<?php

namespace IPP\Student\Instruction;

use DOMNode;

class DefvarInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : DefvarInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get1ArgsTypeAndValue($args, $arg1type, $arg1value);

        $instruction = new DefvarInstruction($order, $arg1type, $arg1value);
        return $instruction;
    }
}