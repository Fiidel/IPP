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
        # if a line isn't just whitespace
        if re.search(r"^\s*$", line) == None:
            line = line.strip()
            line = RemoveCommentsFromLine(line)
            if line != "":
                lineList.append(line)

    # remove useless .IPPcode24 intro
    lineList.remove(".IPPcode24")
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


###################################################
#####                  MAIN                   #####
###################################################

lineList = ParseLinesToList()
# print(lineList)

### INSTRUCTION LIST TESTS
# iList = InstructionList()
# iList.InsertNextInstruction("LABEL", "loopStart")

# print("printing first instruction:")
# print(iList.head.opcode)
# print(iList.head.arg1)
# print(iList.head.arg2)
# print(iList.head.arg3)
