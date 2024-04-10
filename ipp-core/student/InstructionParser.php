<?php

namespace IPP\Student;

use DOMNodeList;

class InstructionParser
{
    public function ParseDomList2Instructions(DOMNodeList $instructions) : void // TODO: return success/fail? or specific return code
    {
        foreach ($instructions as $instr)
        {
            $attributes = $instr->attributes;
            $opcode = $attributes->getNamedItem("opcode")->nodeValue;

            switch ($opcode) {
                case "MOVE":
                    // TODO: create MOVE instruction and store somewhere
                    break;
                
                default:
                    break;
            }
        }
    }

    // TODO: instructions should be a ParseDomList2Instructions arg rather than parser property
}