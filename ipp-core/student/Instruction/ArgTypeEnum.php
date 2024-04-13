<?php

namespace IPP\Student\Instruction;

enum ArgTypeEnum
{
    case var;
    case string;
    case int;
    case bool;
    case nil;
    case label;
    case type;
}