import sys
for line in sys.stdin.readlines():
    print("".join(line.split(":")[1:]).strip()+";")
