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
    case TYPE;
    case READ;
    CASE INT2CHAR;
    case STR2INT;
    case CREATEFRAME;
    case PUSHFRAME;
    case POPFRAME;

    case PLACEHOLDER;
}