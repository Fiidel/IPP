<?php

namespace IPP\Student;

use IPP\Student\Instruction\InstructionParser;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;

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
        $parser->ParseDomList2Instructions($instructions);

        return 0;
    }
}
