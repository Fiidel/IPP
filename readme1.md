Documentation of Project 1 Implementation for IPP 2023/2024
Name and surname: Adam Helešic
Login: xheles06

## Arguments
The script supports a single argument, `--help` or `-h`, which prints the script's description. It is implemented with python's `argparse` library.

## Classes
### `Instruction`
The `Instruction` class is there to only hold the data of an instruction, and potentially a reference to the next instruction. The stored data are `opcode`, `arg1`, `arg2`, `arg3`, and `next`. `arg1`, `arg2`, `arg3`, and `next` have a default value of `None`.

### `InstructionList`
The `InstructionList` stores a reference to the beginning of a list in `head` that is initialized with `None`. It also has a function, `InsertNextInstruction()`, that takes in an opcode and up to 3 arguments and stores them in a new instance of the `Instruction` class. The new instruction is then set as the `head` of the linked list (if the list is empty), or it is set as the following instruction of the last instruction in the list, becoming the new last instruction.

## Program Flow
### Parse source code to a list
`ParseLinesToList()` reads each line from `stdin`. If it isn't whitespace (detected using the `re` library) the line is stripped of whitespace at the start and end (using the in-built `strip()` function) and stripped of comments. It is then saved to a list.

It also removes the `.IPPcode24` header (or throws an error if it's missing).

### Parse each line to an instruction linked list
An instance of `InstructionList` is created. The list of parsed non-whitespace and non-comment lines is taken, its elements split using the in-built `split()` function. A check is made to assert that no instruction has more than 3 arguments. Then each line element is passed to the `InsertNextInstruction()` function that inserts an instruction (as an opcode - converted to all caps for unification - and arguments) into the given `InstructionList`.

### Generating XML
An XML document is generated using the `minidom` library. A root element `program` is added with the attribute `IPPcode24` and appended to the document. Then the script goes through every instruction in a given `InstructionList` and generates their `instruction` and `arg` elements.

Each instruction has an `order` (an order numbered sequentially from 1 up) and `opcode` attributes.

Then the arguments are generated. An error check is made for the number of given and the number of expected arguments, as well as for the type of argument given and expected based on the `opcode` and the argument's order. Identifiers (variable names, labels) are checked for invalid characters. Value validation is done for integer, bool and nil literals.

If there are no errors, the argument element is appended to the instruction, and the next argument is processed. After all arguments have been processed, the instruction is appended to the program.

Finally, the XML is written to `stdout` using `writexml()`.
