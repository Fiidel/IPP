import sys
import re

# ==================================
# get all non-null lines into a list
# ==================================

lineList = []

for line in sys.stdin:
    if re.search(r"^[\s]", line) == None:
        line = line.strip()
        lineList.append(line)

# remove useless .IPPcode24 intro
lineList.remove(".IPPcode24")

# print(lineList)
