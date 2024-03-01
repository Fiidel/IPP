import sys
import re

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
# Parses each line into a list element. Empty lines and .IPPcode24 line are ignored.
# </summary>
def ParseLinesToList():
    lineList = []

    for line in sys.stdin:
        if re.search(r"^[\s]", line) == None:
            line = line.strip()
            lineList.append(line)

    # remove useless .IPPcode24 intro
    lineList.remove(".IPPcode24")
    return lineList


###################################################
#####                  MAIN                   #####
###################################################

lineList = ParseLinesToList()


### INSTRUCTION LIST TESTS
# iList = InstructionList()
# iList.InsertNextInstruction("LABEL", "loopStart")

# print("printing first instruction:")
# print(iList.head.opcode)
# print(iList.head.arg1)
# print(iList.head.arg2)
# print(iList.head.arg3)
