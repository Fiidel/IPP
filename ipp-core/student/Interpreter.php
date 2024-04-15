<?php

namespace IPP\Student;

use IPP\Student\Instruction\InstructionParser;
use IPP\Core\AbstractInterpreter;
use IPP\Student\AST\ASTConverter;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {
        // $this->stderr->writeString("stderr");

        $dom = $this->source->getDOMDocument();
        $instructions = $dom->getElementsByTagName("instruction");
        
        $parser = new InstructionParser();
        $instructionList = $parser->ParseDomList2Instructions($instructions);
        
        $ASTConverter = new ASTConverter;
        $AST = $ASTConverter->ParseInstructions2AST($instructionList);

        $currentNode = $AST->GetHead();
        $visitor = new Visitor($this->stdout, $this->input);
        while ($currentNode != null)
        {
            // if $jump is returned true, the next node is the LABEL node that it's supposed to jump to
            $jump = $currentNode->instruction->accept($visitor);
            if ($jump)
            {
                $currentNode = $currentNode->labelNode;
            }
            else
            {
                $currentNode = $currentNode->nextInstruction;
            }
        }

        $visitor->PrintGF();

        return 0;
    }
}
