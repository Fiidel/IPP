<?php

namespace IPP\Student\Instruction;

enum OperationCodeEnum
{
    case DEFVAR;
    case MOVE;
    case ADD;
    case SUB;
    case MUL;
    case IDIV;
    case WRITE;
    case LABEL;
    case JUMP;
    case JUMPIFEQ;
    case JUMPIFNEQ;
    case EXIT;

    case PLACEHOLDER;
    // TODO: a lot of missing opcodes
}