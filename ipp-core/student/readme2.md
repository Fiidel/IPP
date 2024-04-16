# Documentation of Project 2 Implementation for IPP 2023/2024
Name and surname: Adam Helešic
Login: xheles06

## Flow of the Interpreter
### XML to Instruction Objects
A DOMDocument is obtained through `ippcore`'s getDOMDocument method which takes the source XML file and returns a DOMDocument. A list of nodes with the tag name `instruction` is then extracted and passed to a parsing method of an `InstructionParser` instance.

The parsing method takes each instruction node from the list and obtains its `opcode` attribute. A new factory is instantiated, the type of which is based on the `opcode`. The factory's `CreateInstruction` method takes the instruction node from the original list, parses its contents, creates an instruction object of the appropriate type and inserts the instance into a linked list.

#### Instruction Object
All instructions have the `opcode` and `order` properties, as well as the `accept` method (explained later). Derived instruction classes may also have up to 3 sets of an argument type and value. The argument type is of a value from the `ArgType` enum (`var`, `int`, `string`, `label`, ...) and the argument value is a string (a unified data type is preferable at this stage but conversions to proper data types are done at a later point).

#### Factories and Instructions
Not all instructions have separate instruction classes and factories. In some cases the instructions vary only slightly (such as arithmetic instructions) and can be conjoined into a "superclass" of their own. The instruction execution code is then decided by the instruction's `opcode` value.

### List of Instructions to AST
The linked list of instruction instances is then passed to a parsing method of an `ASTConverter` instance that returns an Abstract Syntax Tree (AST). The method searches the instructions based on `order`, starting at 1 and incrementing by 1 with each instruction found. Each instruction is stored in an `ASTNode` instance and linked to one another as linked list nodes would be. If the instruction instance is of type `LABEL`, it is also stored in an array with its identifier (first argument) as key.

#### AST and ASTNode
An `ASTNode` stores a given instruction instance and 2 other `ASTNodes` - a label node and a next instruction node. The next instruction node is the one following based on `order`. The label node is `null` by default. A second pass through the AST is necessary to fill in the missing label nodes for jump instructions. The instruction instance is retrieved from the label array based on the first argument of the jump instruction (the label's identifier).

### Traversing the AST, Executing Instructions
The Visitor pattern was chosen for the instruction execution, allowing the all execution code to be in one place.

The `Interpreter` takes the first node of the AST and invokes the instruction's `accept` method which in turn invokes one of the `Visitor`'s `VisitXyzInstruction` method. Jump instructions where the condition whether or not to jump is evaluated as `true` return `true`, allowing the `Interpreter` to decide whether the next instruction should be the `ASTNode`'s label node or next instruction node.

## Visitor
### Frames
Frames are stored as linked lists. The global frame is initialized as a linked list from the start, the temporary frame is null by default and local frames (as there can be multiple) are stored in an array which is empty by default.

#### Frame Variables (Linked List Nodes)
All nodes store an `identifier`, a `value` and the next node.

#### Temporary and Local Frames
When a temporary frame is pushed it is appended to the array of local frames. Whenever variables refer to the local frame, only the last element (the last local frame) is worked with. This allows the addition and removal of more recent local frames while keeping older ones accessible.