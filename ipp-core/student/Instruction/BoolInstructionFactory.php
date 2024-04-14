<?php

namespace IPP\Student\Instruction;

use DOMNode;

class BoolInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : InstructionBase
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        switch ($opcode)
        {
            case OperationCodeEnum::AND:
                parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                $instruction = new AndInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                break;

            case OperationCodeEnum::OR:
                parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                $instruction = new OrInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                break;

            case OperationCodeEnum::NOT:
                parent::Get2ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value);
                $instruction = new NotInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);
                break;
            
            default:
                break;
        }
        
        return $instruction;
    }
}