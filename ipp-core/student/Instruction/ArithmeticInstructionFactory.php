<?php

namespace IPP\Student\Instruction;

use DOMNode;

class ArithmeticInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : ArithmeticInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);

        $instruction = new ArithmeticInstruction($opcode, $order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        return $instruction;
    }
}