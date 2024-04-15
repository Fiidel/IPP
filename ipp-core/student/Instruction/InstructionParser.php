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

                case "WRITE":
                    $factory = new WriteInstructionFactory;
                    break;

                case "LABEL":
                case "JUMP":
                case "JUMPIFEQ":
                case "JUMPIFNEQ":
                case "EXIT":
                    $factory = new FlowInstructionFactory;
                    break;

                case 'AND':
                case "OR":
                case "NOT":
                    $factory = new BoolInstructionFactory;
                    break;

                case "LT":
                case "GT":
                case "EQ":
                    $factory = new RelationInstructionFactory;
                    break;
                
                case "CONCAT":
                case "GETCHAR":
                case "SETCHAR":
                case "STRLEN":
                    $factory = new StringInstructionFactory;
                    break;

                case "TYPE":
                    $factory = new TypeInstructionFactory;
                    break;

                case "READ":
                    $factory = new ReadInstructionFactory;
                    break;

                case "INT2CHAR":
                    $factory = new Int2CharInstructionFactory;
                    break;

                case "STR2INT":
                    $factory = new Str2IntInstructionFactory;
                    break;

                default:
                    $factory = new PlaceholderInstructionFactory;
                    break;
            }

            // create instruction and insert into list
            $instructionList->InsertLast($factory->CreateInstruction($instr));
        }
        return $instructionList;
    }
}