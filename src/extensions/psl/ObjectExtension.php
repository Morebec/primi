<?php


namespace Smuuf\Primi\Psl;


use Smuuf\Primi\Extension;
use Smuuf\Primi\Structures\ArrayValue;
use Smuuf\Primi\Structures\ObjectValue;

class ObjectExtension extends Extension
{
    public static function object_new(): ObjectValue
    {
        return new ObjectValue(new SimpleObject('Root'));
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

class SimpleObject {

    /**
     * @var string
     */
    private $name;

    /** @var string */
    public $property = 'i am public';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function hello(): string
    {
        return 'Hello! My name is ' . $this->name;
    }

    public function obj(): self
    {
        return new SimpleObject('inner object');
    }
}