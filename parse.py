import sys
import re
from xml.dom import minidom as md
import argparse


###################################################
#####              HELP ARGUMENT              #####
###################################################

argparser = argparse.ArgumentParser(
    description="The script reads IPPcode24 source code from standard input, checks its lexical and syntactic correctness, and writes the XML representation of the program to standard output."
)
args = argparser.parse_args()


###################################################
#####                CLASSES                  #####
###################################################

class Instruction:
    def __init__(self, opcode, arg1 = None, arg2 = None, arg3 = None):
        self.opcode = opcode
        self.arg1 = arg1
        self.arg2 = arg2
        self.arg3 = arg3
        self.next = None


class InstructionList:
    def __init__(self):
        self.head = None
    def InsertNextInstruction(self, opcode, arg1 = None, arg2 = None, arg3 = None):
        newInstruction = Instruction(opcode, arg1, arg2, arg3)
        if self.head is None:
            self.head = newInstruction
        else:
            lastInstruction = self.head
            while lastInstruction.next != None:
                lastInstruction = lastInstruction.next
            lastInstruction.next = newInstruction


###################################################
#####               FUNCTIONS                 #####
###################################################

# <summary>
# Returns expected argument number and types for a given IPPcode24 instruction opcode.
# </summary>
def GetInstructionArgTypes(instructionOpcode):
    match instructionOpcode:
        # opcode
        case "CREATEFRAME" | "PUSHFRAME" | "POPFRAME" | "RETURN" | "BREAK":
            return (0)
        
        # opcode (var)
        case "DEFVAR" | "POPS":
            return (1, "var")
        
        # opcode (symb)
        case "PUSHS" | "WRITE" | "EXIT" | "DPRINT":
            return (1, "symb")
        
        # opcode (var) (symb)
        case "MOVE" | "INT2CHAR" | "STRLEN" | "TYPE":
            return (2, "var", "symb")
        
        # opcode (var) (type)
        case "READ":
            return (2, "var", "type")
        
        # opcode (label)
        case "CALL" | "LABEL" | "JUMP":
            return (1, "label")
        
        # opcode (label) (symb1) (symb2)
        case "JUMPIFEQ" | "JUMPIFNEQ":
            return (3, "label", "symb", "symb")
        
        # opcode (var) (symb1) (symb2)
        case "ADD" | "SUB" | "MUL" | "IDIV" | "LT" | "GT" | "EQ" | "AND" | "OR" | "NOT" | "STRI2INT" | "CONCAT" | "GETCHAR" | "SETCHAR":
            return (3, "var", "symb", "symb")
        
        # opcode not found
        case _:
            print(f"Error: Unknown instruction opcode {instructionOpcode}.", file=sys.stderr)
            sys.exit(22)

    
# <summary>
# Parses each line into a list element. Empty lines and .IPPcode24 line are ignored.
# </summary>
def ParseLinesToList():
    lineList = []

    for line in sys.stdin:
        # if a line isn't just whitespace
        if re.search(r"^\s*$", line) == None:
            line = line.strip()
            line = RemoveCommentsFromLine(line)
            if line != "":
                lineList.append(line)

    # remove useless .IPPcode24 intro
    if lineList[0] == ".IPPcode24":
        lineList.remove(".IPPcode24")
    else:
        print("Error: missing '.IPPcode24' head in src file.", file=sys.stderr)
        sys.exit(21)
    return lineList


# <summary>
# Removes # comments from a given line.
# </summary>
def RemoveCommentsFromLine(line):
    comment = re.search(r"[#](.*)", line)
    if comment != None:
        line = line.replace(comment[0], "")
        line = line.strip()
    return line


# <summary>
# Validates that an instruction has no more than 3 arguments.
# </summary>
def ValidateNumberOfInstructionArguments(args):
    if len(args) > 3:
        print(f"Error: No instruction accepts more than 3 arguments.", file=sys.stderr)
        sys.exit(23)
    return


# <summary>
# Parses each line into its parts - instruction and arguments.
# </summary>
def ParseLinesToInstructionElements(lineList, iList):
    for line in lineList:
        # parse individual lines into instructions and arguments and insert them into InstructionList
        # use regex for whitespace
        instructionParts = line.split()
        ValidateNumberOfInstructionArguments(instructionParts[1:])
        iList.InsertNextInstruction(instructionParts[0].upper(), *instructionParts[1:])
    return iList


# <summary>
# Returns the type of the given argument.
# </summary>
def GetArgType(instruction, arg):
    if "@" in arg:
        type = arg.partition("@")[0]
        # var
        if type == "GF" or type == "LF" or type == "TF":
            type = "var"
        # error - not int, bool, string nor nil
        elif type != "int" and type != "bool" and type != "string" and type != "nil":
            print("Error: unrecognized literal or variable argument type.", file=sys.stderr)
            sys.exit(23)
        return type
    else:
        if instruction.opcode == "READ":
            return "type"
        elif instruction.opcode == "CALL" or instruction.opcode == "LABEL" or instruction.opcode == "JUMP" or instruction.opcode == "JUMPIFEQ" or instruction.opcode == "JUMPIFNEQ":
            return "label"
        else:
            print(f"Error: unrecognized argument type {arg}.", file=sys.stderr)
            sys.exit(23)


# <summary>
# Validates the value of a given argument based on its type. Exits with an exit code if the value is invalid.
# </summary>
def ValidateArgValue(value, type):
    # if var or label, validate identifier characters
    if type == "var" or type == "label":
        if re.search(r"^[a-zA-Z0-9_\-$&%*!?]+$", value) == None:
            print(f"Error: Identifier name {value} contains invalid characters.", file=sys.stderr)
            sys.exit(23)
    
    # if int, validate that its int
    if type == "int":
        if re.search(r"^[+-]?[0-9]+$", value) == None:
            print(f"Error: Invalid integer value {value}.", file=sys.stderr)
            sys.exit(23)
            
    # if bool, validate that it's true/false
    if type == "bool":
        if re.search(r"^true$", value) == None and re.search(r"^false$", value) == None:
            print(f"Error: Invalid bool value {value}. Must be 'true' or 'false'.", file=sys.stderr)
            sys.exit(23)
    
    # if nil, validate nil
    if type == "nil":
        if re.search(r"^nil$", value) == None:
            print(f"Error: Invalid nil value {value}. Must be 'nil'.", file=sys.stderr)
            sys.exit(23)


# <summary>
# Returns the value of the given argument.
# </summary>
def GetArgValue(arg, type):
    if "@" in arg:
        value = arg.partition("@")[2]
        ValidateArgValue(value, type)
        if type == "var":
            return arg # need to return the scope too (GF, LF)
        else:
            return value
    else:
        ValidateArgValue(arg, type)
        return arg
    # note: minidom automatically transforms problematic characters in string


# <summary>
# Generates a given instruction argument to XML.
# </summary>
def GenerateXMLArgument(XML, instructionXML, instruction, arg, argNumber):
    argXML = XML.createElement(f"arg{argNumber}")
    
    argTypeAttribute = GetArgType(instruction, arg)
    argXML.setAttribute("type", argTypeAttribute)
    
    # TODO: ValidateArgTypeForOpcode(instruction.opcode, argTypeAttribute)
    # will be based on argNumber and the specific opcode
    
    argValue = GetArgValue(arg, argTypeAttribute)
    argValueElement = XML.createTextNode(argValue)
    argXML.appendChild(argValueElement)
    
    instructionXML.appendChild(argXML)


# <summary>
# Returns the number of arguments of a given instruction based on which last argument isn't None.
# (The instructions arguments are filled sequentially from arg1 to arg3, with implicit None values unless set otherwise.)
# </summary>
def GetNumOfArgs(instruction):
    if instruction.arg3 != None:
        return 3
    elif instruction.arg2 != None:
        return 2
    elif instruction.arg1 != None:
        return 1
    else:
        return 0


# <summary>
# Generates a given instruction to XML.
# </summary>
def GenerateXMLInstruction(XML, programXML, instruction, instructionNumber):
    instructionXML = XML.createElement("instruction")
    instructionXML.setAttribute("order", str(instructionNumber))
    instructionXML.setAttribute("opcode", instruction.opcode)
    
    # TODO:
    numOfGivenArgs = GetNumOfArgs(instruction)
    expectedArgs = GetInstructionArgTypes(instruction.opcode)
    expectedNumOfArgs = expectedArgs[0]
    expectedArgTypes = expectedArgs[1:]
    
    if expectedNumOfArgs != numOfGivenArgs:
        print(f"Error: Unexpected number of arguments for instruction {instruction.opcode}.", file=sys.stderr)
        sys.exit(23)
    
    # handle args
    if instruction.arg1 != None:
        GenerateXMLArgument(XML, instructionXML, instruction, instruction.arg1, 1)

        if instruction.arg2 != None:
            GenerateXMLArgument(XML, instructionXML, instruction, instruction.arg2, 2)
        
            if instruction.arg3 != None:
                GenerateXMLArgument(XML, instructionXML, instruction, instruction.arg3, 3)
                
    programXML.appendChild(instructionXML)


# <summary>
# Parses the instruction list to an XML format.
# </summary>
def GenerateXML(iList):
    #XML head: <?xml version="1.0" encoding="UTF-8"?>
    
    # create xml document and add root program element
    XML = md.Document()
    programXML = XML.createElement("program")
    programXML.setAttribute("language", "IPPcode24")
    XML.appendChild(programXML)
    
    # add all instructions from list to element
    currentInstruction = iList.head
    instructionNumber = 1
    while currentInstruction != None:
        GenerateXMLInstruction(XML, programXML, currentInstruction, instructionNumber)
        instructionNumber += 1
        currentInstruction = currentInstruction.next
    
    XML.writexml(sys.stdout, indent="", addindent="\t", encoding="UTF-8", newl="\n")


###################################################
#####            DEBUG FUNCTIONS              #####
###################################################

# <summary>
# Prints all instruction elements in a list.
# </summary>
def Debug_PrintInstructionList(iList):
    instruction = iList.head
    instructionNumber = 1
    while instruction != None:
        print(f"--- INSTRUCTION {instructionNumber} ---")
        print(instruction.opcode)
        print(instruction.arg1)
        print(instruction.arg2)
        print(instruction.arg3)
        print("")
        instructionNumber += 1
        instruction = instruction.next


###################################################
#####                  MAIN                   #####
###################################################

lineList = ParseLinesToList()

iList = InstructionList()
iList = ParseLinesToInstructionElements(lineList, iList)
# DEBUG:
# Debug_PrintInstructionList(iList)

GenerateXML(iList)

sys.exit(0)
