<?php

namespace IPP\Student\Instruction;

use DOMNode;

abstract class InstructionFactoryBase
{
    public abstract function CreateInstruction(DOMNode $XmlNode) : InstructionBase;

    protected function Convert2ArgType(string $argType) : ArgTypeEnum
    {
        switch ($argType)
        {
            case 'var':
                return ArgTypeEnum::var;
                break;
            
            case 'string':
                return ArgTypeEnum::string;
                break;
            
            // TODO: the rest of the cases

            default:
                break;
        }
    }

    protected function ProcessArgument(DOMNode $arg)
    {
        // TODO

    }
}