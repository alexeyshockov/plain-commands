<?php

namespace PlainCommands;

use Stringy\StaticStringy;

function dasherize(string $s): string
{
    // In Stringy 2.* all StaticStringy methods return Stringy instance, while in Striny 3.* the same methods return
    // a string
    return (string) StaticStringy::dasherize($s);
}
