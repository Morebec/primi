<?php


namespace Smuuf\Primi\Structures;


/**
 * The Object value is used as a bridge to pass PHP objects to Primi.
 */
class ObjectValue extends Value
{
    public const TYPE = 'object';

    private $reflection;

    public function __construct($object)
    {
        if(!is_object($object)) {
            throw new \RuntimeException(sprintf("Expected object, got '%s'", gettype($object)));
        }

        $this->value = $object;
        $this->reflection = new \ReflectionClass($object);
    }

    /**
     * Indicates if a method exists on an object
     * @param string $name
     * @return FuncValue
     */
    public function hasMethod(string $name): bool
    {
        return $this->reflection->hasMethod($name);
    }

    /**
     * Returns a method by its name
     * @param string $name
     * @return FuncValue
     */
    public function getMethodByName(string $name): ObjectMethod
    {
        if(!$this->hasMethod($name)) {
            throw new \RuntimeException('Method not found on object ' . $this->reflection->getShortName());
        }

        $fnC = FnContainer::buildFromClosure([$this->value, $name]);

        return new ObjectMethod($this, $fnC);
    }

    public function getStringValue(): string
    {
        if($this->reflection->hasMethod('__toString')) {
            return (string)$this->value;
        }

        return $this->reflection->getShortName();
    }
}