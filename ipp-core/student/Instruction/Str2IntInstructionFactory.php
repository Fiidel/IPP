<?php

namespace IPP\Student\Instruction;

use DOMNode;

class Str2IntInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : Str2IntInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        $instruction = new Str2IntInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        
        return $instruction;
    }
}