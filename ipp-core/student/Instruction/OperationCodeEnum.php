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
    // TODO: a lot of missing opcodes
}