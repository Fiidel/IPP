<?php

namespace IPP\Student\Instruction;

enum OperationCodeEnum
{
    case DEFVAR;
    case MOVE;
    case ADD;
    // TODO: a lot of missing opcodes
}