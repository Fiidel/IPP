<?php

namespace IPP\Student\Instruction;

use DOMNode;

class DefvarInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : DefvarInstruction
    {
        $order = parent::GetInstructionOrder($XmlNode);

        $args = $XmlNode->childNodes;
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    parent::GetArgTypeAndValue($arg, $arg1type, $arg1value);
                    break;

                default:
                    break;
            }
        }

        // TODO: check arg1 is var - or is it confirmed validated in the assignment?

        $instruction = new DefvarInstruction($order, $arg1type, $arg1value);
        return $instruction;
    }
}