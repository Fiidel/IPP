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

    protected function GetInstructionOrder(DOMNode $node) : int
    {
        return $node->attributes->getNamedItem("order")->nodeValue;
    }

    protected function Get1ArgsTypeAndValue($args, &$type, &$value) : void
    {
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    $this->GetTypeAndValue($arg, $type, $value);
                    break;

                default:
                    break;
            }
        }
    }

    protected function Get2ArgsTypeAndValue($args, &$type1, &$value1, &$type2, &$value2) : void
    {
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    $this->GetTypeAndValue($arg, $type1, $value1);
                    break;
                
                case 'arg2':
                    $this->GetTypeAndValue($arg, $type2, $value2);
                    break;

                default:
                    break;
            }
        }
    }

    protected function Get3ArgsTypeAndValue($args, &$type1, &$value1, &$type2, &$value2, &$type3, &$value3) : void
    {
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    $this->GetTypeAndValue($arg, $type1, $value1);
                    break;
                
                case 'arg2':
                    $this->GetTypeAndValue($arg, $type2, $value2);
                    break;

                case 'arg3':
                    $this->GetTypeAndValue($arg, $type3, $value3);
                    break;

                default:
                    break;
            }
        }
    }

    private function GetTypeAndValue($arg, &$type, &$value) : void
    {
        $type = $this->Convert2ArgType($arg->attributes->getNamedItem("type")->nodeValue);
        $value = trim($arg->nodeValue);
    }
}