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
    case AND;
    case OR;
    case NOT;
    case LT;
    case GT;
    case EQ;
    case CONCAT;
    case GETCHAR;
    case SETCHAR;
    case STRLEN;

    case PLACEHOLDER;
    // TODO: a lot of missing opcodes
}