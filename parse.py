import sys
import re

###################################################
#####               FUNCTIONS                 #####
###################################################

# get all non-null lines into a list
# ==================================

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
print(lineList)
