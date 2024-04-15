<?php

namespace IPP\Student\Instruction;

use DOMNode;

class StringInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : InstructionBase
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        switch ($opcode)
        {
            case OperationCodeEnum::CONCAT:
            case OperationCodeEnum::GETCHAR:
            case OperationCodeEnum::SETCHAR:
                parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                $instruction = new StringManipulationInstruction($opcode, $order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                break;

            case OperationCodeEnum::STRLEN:
                parent::Get2ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value);
                $instruction = new StrlenInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);
                break;
            
            default:
                break;
        }
        
        return $instruction;
    }
}