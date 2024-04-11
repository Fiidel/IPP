<?php

namespace IPP\Student\Instruction;

use DOMNodeList;

class InstructionParser
{
    public function ParseDomList2Instructions(DOMNodeList $instructions) : void // TODO: return success/fail? or specific return code
    {
        foreach ($instructions as $instr)
        {
            $attributes = $instr->attributes;
            $opcode = $attributes->getNamedItem("opcode")->nodeValue;

            // TODO: store instructions somewhere
            switch ($opcode)
            {
                case "DEFVAR":
                    $factory = new DefvarInstructionFactory;
                    break;

                case "MOVE":
                    $factory = new MoveInstructionFactory;
                    break;
                
                default:
                    $factory = null;
                    break;
            }

            if ($factory != null)
            {
                $factory->CreateInstruction($instr);
            }
        }
    }
}