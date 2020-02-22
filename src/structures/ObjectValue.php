<?php


namespace Smuuf\Primi\Structures;

/**
 * The Object value is used as a bridge to pass PHP objects to Primi.
 */
class ObjectValue extends Value
{
    public const TYPE = 'object';

    public function __construct(array $properties, array $methods)
    {
        $this->value = [
            'properties' => $properties,
            'methods' => $methods
        ];
    }

    /**
     * Indicates if a method exists on an object
     * @param string $name
     * @return FuncValue
     */
    public function hasMethod(string $name): bool
    {
        return isset($this->value['methods'][$name]);
    }

    /**
     * Returns a method by its name
     * @param string $name
     * @return FuncValue
     */
    public function getMethodByName(string $name): FuncValue
    {
        if($this->hasMethod($name)) {
            return $this->value['methods'][$name];
        }

        throw new \RuntimeException('Method not found on object');
    }

    public function getStringValue(): string
    {
        return self::TYPE;
    }

    public function getProperties(): array
    {
        return $this->value['properties'];
    }

    public function getMethods(): array
    {
        return $this->value['methods'];
    }
}