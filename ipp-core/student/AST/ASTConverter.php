<?php

namespace IPP\Student\AST;

use Exception;
use IPP\Student\Instruction\ConditionalJumpInstruction;
use IPP\Student\Instruction\JumpInstruction;
use IPP\Student\Instruction\LabelInstruction;
use IPP\Student\Instruction\OperationCodeEnum;
use IPP\Student\LinkedList\InstructionLinkedList;

class ASTConverter
{
    public function ParseInstructions2AST(InstructionLinkedList $instructionList) : AST
    {
        // note: this method presumes that the order of instructions in the input XML is correct
        // (starting at 1 and with no order number left out)
        // the last parsed instruction is the one with the last correct sequential order
        
        $AST = new AST;

        // first pass
        $order = 1;
        $labels = [];

        while (true)
        {
            $instruction = $instructionList->GetInstructionWithOrder($order);
            if ($instruction == null)
            {
                break;
            }

            $node = $AST->InsertNext($instruction);
            
            // save LABEL instruction nodes under their name
            if ($instruction instanceof LabelInstruction)
            {
                $labelName = $instruction->getArg1Value();

                // error checking: trying to define a label of the same identifier more than once
                if (array_key_exists($labelName, $labels))
                {
                    throw new Exception("Redefinition of label.", 52);
                }

                $labels[$labelName] = $node;
            }

            $order += 1;
        }

        // second pass - filling in labels
        $currentNode = $AST->GetHead();
        while ($currentNode != null)
        {
            if ($currentNode->instruction instanceof JumpInstruction
                || $currentNode->instruction instanceof ConditionalJumpInstruction)
            {
                // get the ID of the label from instruction
                $label = $currentNode->instruction->getArg1Value();

                // get the ASTNode based on label ID from array
                $labelNode = $labels[$label];

                // set ASTNode as label node to jump to
                $currentNode->labelNode = $labelNode;
            }
            
            $currentNode = $currentNode->nextInstruction;
        }

        return $AST;
    }
}