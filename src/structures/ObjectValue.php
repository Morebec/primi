<?php


namespace Smuuf\Primi\Structures;


use Smuuf\Primi\InternalUndefinedIndexException;
use Smuuf\Primi\ISupportsArrayAccess;

/**
 * The Object value is used as a bridge to pass PHP objects to Primi.
 */
class ObjectValue extends Value implements ISupportsArrayAccess
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
        if($this->value instanceof PSLObject) {
            return $this->value->hasMethod($name);
        }

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

    public function getProperties(): array
    {
        $props = [];
        $reflectionProperties = $this->reflection->getProperties();
        foreach ($reflectionProperties as $property) {
            $props[$property->getName()] = new ObjectValue($property);
        }
        return $props;
    }

    public function getMethods(): array
    {
        $reflectionMethods = $this->reflection->getMethods();
        $methods = [];
        foreach ($reflectionMethods as $method) {
            $methods[$method->getName()] = new ObjectValue($method);
        }
        return $methods;
    }

	public function getIterator(): \Iterator
    {
		return new \ArrayIterator($this->value);
	}

	public function arrayGet(string $key): Value
    {

        try {
            if (!isset($this->value[$key])) {
                throw new InternalUndefinedIndexException($key);
            }

            return $this->value[$key];
        } catch (\Error $error) {
            $shortName = $this->reflection->getShortName();
            throw new \TypeError("Cannot use object {$shortName} as array.", 0, $error);
        }
	}

	public function arraySet(?string $key, Value $value)
    {

		if ($key === \null) {
			$this->value[] = $value;
		} else {
			$this->value[$key] = $value;
		}

	}

	public function getArrayInsertionProxy(?string $key): ArrayInsertionProxy {
		return new ArrayInsertionProxy($this, $key);
	}
}
