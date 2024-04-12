<?php

namespace IPP\Student;

use IPP\Student\Instruction\InstructionParser;
use IPP\Student\LinkedList\LinkedList;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Student\AST\ASTConverter;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {
        // Check \IPP\Core\AbstractInterpreter for predefined I/O objects:
        // $val = $this->input->readString();
        // $this->stdout->writeString("stdout");
        // $this->stderr->writeString("stderr");
        // throw new NotImplementedException;

        $dom = $this->source->getDOMDocument();
        $instructions = $dom->getElementsByTagName("instruction");
        
        $parser = new InstructionParser();
        $instructionList = $parser->ParseDomList2Instructions($instructions);
        
        $ASTConverter = new ASTConverter;
        $AST = $ASTConverter->ParseInstructions2AST($instructionList);

        return 0;
    }
}
