import sys
import re

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


def


###################################################
#####                  MAIN                   #####
###################################################

lineList = ParseLinesToList()

print(lineList)

lineList = 

print(lineList)
