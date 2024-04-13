<?php

namespace IPP\Student\Instruction;

use DOMNodeList;
use IPP\Student\LinkedList\InstructionLinkedList;

class InstructionParser
{
    public function ParseDomList2Instructions(DOMNodeList $instructions) : InstructionLinkedList
    {
        $instructionList = new InstructionLinkedList;

        foreach ($instructions as $instr)
        {
            $attributes = $instr->attributes;
            $opcode = $attributes->getNamedItem("opcode")->nodeValue;

            switch ($opcode)
            {
                case "DEFVAR":
                    $factory = new DefvarInstructionFactory;
                    break;

                case "MOVE":
                    $factory = new MoveInstructionFactory;
                    break;

                case "ADD":
                case "SUB":
                case "MUL":
                case "IDIV":
                    $factory = new ArithmeticInstructionFactory;
                    break;
                
                default:
                    $factory = null;
                    break;
            }

            if ($factory != null)
            {
                // create instruction and insert into list
                $instructionList->InsertLast($factory->CreateInstruction($instr));
            }
        }
        return $instructionList;
    }
}