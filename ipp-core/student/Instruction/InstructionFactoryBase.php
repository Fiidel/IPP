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
            
            case 'int':
                return ArgTypeEnum::int;
                break;

            case 'bool':
                return ArgTypeEnum::bool;
                break;

            case 'nil':
                return ArgTypeEnum::nil;
                break;

            case 'label':
                return ArgTypeEnum::label;
                break;

            case 'type':
                return ArgTypeEnum::type;
                break;
            
            default:
                // TODO: error message?
                exit(52);
                break;
        }
    }

    protected function Convert2OpcodeType(string $opcodeType) : OperationCodeEnum
    {
        switch ($opcodeType)
        {
            case 'DEFVAR':
                return OperationCodeEnum::DEFVAR;
                break;
            
            case 'MOVE':
                return OperationCodeEnum::MOVE;
                break;

            case 'ADD':
                return OperationCodeEnum::ADD;
                break;

            case 'SUB':
                return OperationCodeEnum::SUB;
                break;

            case 'MUL':
                return OperationCodeEnum::MUL;
                break;

            case 'IDIV':
                return OperationCodeEnum::IDIV;
                break;

            case "LABEL":
                return OperationCodeEnum::LABEL;
                break;

            case "JUMP":
                return OperationCodeEnum::JUMP;
                break;

            case "JUMPIFEQ":
                return OperationCodeEnum::JUMPIFEQ;
                break;

            case "JUMPIFNEQ":
                return OperationCodeEnum::JUMPIFNEQ;
                break;

            case "EXIT":
                return OperationCodeEnum::EXIT;
                break;

            case 'AND':
                return OperationCodeEnum::AND;
                break;

            case "OR":
                return OperationCodeEnum::OR;
                break;

            case "NOT":
                return OperationCodeEnum::NOT;
                break;

            case "LT":
                return OperationCodeEnum::LT;
                break;

            case "GT":
                return OperationCodeEnum::GT;
                break;

            case "EQ":
                return OperationCodeEnum::EQ;
                break;

            case "CONCAT":
                return OperationCodeEnum::CONCAT;
                break;

            case "GETCHAR":
                return OperationCodeEnum::GETCHAR;
                break;

            case "SETCHAR":
                return OperationCodeEnum::SETCHAR;
                break;

            case "STRLEN":
                return OperationCodeEnum::STRLEN;
                break;

            case "TYPE":
                return OperationCodeEnum::TYPE;
                break;

            case "READ":
                return OperationCodeEnum::READ;
                break;

            case "INT2CHAR":
                return OperationCodeEnum::INT2CHAR;
                break;

            case "STR2INT":
                return OperationCodeEnum::STR2INT;
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

    protected function GetInstructionOpcode(DOMNode $node) : OperationCodeEnum
    {
        return $this->Convert2OpcodeType($node->attributes->getNamedItem("opcode")->nodeValue);
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