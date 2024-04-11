<?php

namespace IPP\Student\Instruction;

use DOMNode;

class MoveInstructionFactory extends InstructionFactoryBase
{
    public function CreateInstruction(DOMNode $XmlNode) : MoveInstruction
    {
        $args = $XmlNode->childNodes;
        foreach ($args as $arg)
        {
            switch ($arg->nodeName) {
                case 'arg1':
                    $arg1type = parent::Convert2ArgType($arg->attributes->getNamedItem("type")->nodeValue);
                    $arg1value = trim($arg->nodeValue);
                    break;
                
                case 'arg2':
                    $arg2type = parent::Convert2ArgType($arg->attributes->getNamedItem("type")->nodeValue);
                    $arg2value = trim($arg->nodeValue);
                    break;

                default:
                    break;
            }
        }

        // TODO: check arg1 is var - or is it confirmed validated in the assignment?
        // TODO: push through transformative method that takes the type and converts it to ArgType (protected method in base class)
        // TODO: frames? or is that up to the Executioner part of the program?

        // TODO: convert to proper data type? in some unified way pls (wg. what if it's int and not string)

        // TODO: REFACTOR? processing is very repetitive -> method to base class?

        $moveInstruction = new MoveInstruction($arg1type, $arg1value, $arg2type, $arg2value);
        return $moveInstruction;
    }
}