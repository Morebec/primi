<?php


namespace Smuuf\Primi\Psl;


use Smuuf\Primi\Extension;
use Smuuf\Primi\ExtensionHub;
use Smuuf\Primi\Structures\ArrayValue;
use Smuuf\Primi\Structures\FuncValue;
use Smuuf\Primi\Structures\ObjectValue;

class ObjectExtension extends Extension
{
    public static function object_new(ArrayValue $arrayValue): ObjectValue
    {
        $properties = [];
        $methods = [];

        foreach ($arrayValue->value as $key => $value) {
            if($value instanceof FuncValue) {
                $methods[$key] = $value;
            } else {
                $properties[$key] = $value;
            }
        }

        var_dump($properties);
        ExtensionHub::addCallable([self::class, 'object_test']);

        return new ObjectValue($properties, $methods);
    }

    public static function object__properties(ObjectValue $objectValue): ArrayValue
    {
        return new ArrayValue($objectValue->getProperties());
    }

    public static function object__methods(ObjectValue $objectValue): ArrayValue
    {
        return new ArrayValue($objectValue->getMethods());
    }
}