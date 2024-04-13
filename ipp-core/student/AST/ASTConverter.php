<?php

namespace IPP\Student\AST;

use IPP\Student\LinkedList\InstructionLinkedList;

class ASTConverter
{
    public function ParseInstructions2AST(InstructionLinkedList $instructionList) : AST
    {
        $AST = new AST;

        // TODO: missing order number? (this method presumes that once it gets null, that's where the program ends)

        $order = 1;
        while (true)
        {
            $instruction = $instructionList->GetInstructionWithOrder($order);
            if ($instruction == null)
            {
                break;
            }

            $AST->InsertNext($instruction);
            $order += 1;
        }

        return $AST;
    }
}