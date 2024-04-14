<?php

namespace IPP\Student\Instruction;

use DOMNode;

class RelationInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : RelationInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        $instruction = new RelationInstruction($opcode, $order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
        
        return $instruction;
    }
}