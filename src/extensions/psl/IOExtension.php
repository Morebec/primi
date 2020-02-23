<?php


namespace Smuuf\Primi\Psl;


use Smuuf\Primi\Extension;
use Smuuf\Primi\Structures\Value;

class IOExtension extends Extension
{
    public static function print($value): void
    {
        if($value instanceof Value) {
            echo $value->getStringValue();
        } else {
            echo $value;
        }
    }
}