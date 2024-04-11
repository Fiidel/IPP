<?php

namespace IPP\Student\Instruction;

use DOMNode;

class MoveInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : MoveInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    parent::GetArgTypeAndValue($arg, $arg1type, $arg1value);
                    break;
                
                case 'arg2':
                    parent::GetArgTypeAndValue($arg, $arg2type, $arg2value);
                    break;

                default:
                    break;
            }
        }

        // TODO: check arg1 is var - or is it confirmed validated in the assignment?
        
        // TODO: convert to proper data type (eg. an int and not string)?
        //      or it's probably better to keep it as a string because the type might vary (eg. could be int or a string symbol)

        $instruction = new MoveInstruction($order, $arg1type, $arg1value, $arg2type, $arg2value);
        return $instruction;
    }
}