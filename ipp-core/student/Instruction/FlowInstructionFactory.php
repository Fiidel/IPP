<?php

namespace IPP\Student\Instruction;

use DOMNode;

class FlowInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : InstructionBase
    {
        $order = parent::GetInstructionOrder($XmlNode);
        $opcode = parent::GetInstructionOpcode($XmlNode);

        $args = $XmlNode->childNodes;
        switch ($opcode)
        {
            case OperationCodeEnum::LABEL:
                parent::Get1ArgsTypeAndValue($args, $arg1type, $arg1value);
                $instruction = new LabelInstruction($order, $arg1type, $arg1value);
                break;

            case OperationCodeEnum::JUMP:
                parent::Get1ArgsTypeAndValue($args, $arg1type, $arg1value);
                $instruction = new JumpInstruction($order, $arg1type, $arg1value);
                break;
                
            case OperationCodeEnum::EXIT:
                parent::Get1ArgsTypeAndValue($args, $arg1type, $arg1value);
                $instruction = new ExitInstruction($order, $arg1type, $arg1value);
                break;

            case OperationCodeEnum::JUMPIFEQ:
            case OperationCodeEnum::JUMPIFNEQ:
                parent::Get3ArgsTypeAndValue($args, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                $instruction = new ConditionalJumpInstruction($opcode, $order, $arg1type, $arg1value, $arg2type, $arg2value, $arg3type, $arg3value);
                break;
            
            default:
                break;
        }
        
        return $instruction;
    }
}