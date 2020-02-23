<?php


namespace Smuuf\Primi\Structures;

/**
 * Property Access allowing dynamically defined objects
 */
class PSLObject implements \ArrayAccess
{
    protected $data = [];

    protected $reflection;


    public function __construct(array $data = [])
    {
        $this->data = $data;

        $this->reflection = new \ReflectionClass($this);
    }


    public function _reflect(): \ReflectionClass
    {
        return $this->reflection;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name)
    {
        if($this->__isset($name)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        if($name === null) {
            $this->data[] = $value;
        } else {
            $this->data[$name] = $value;
        }
    }

    public function __unset($name)
    {
        if($this->__isset($name))
        {
            unset($this->data[$name]);
        }
    }

    public function __call($name, $arguments)
    {
        if(!$this->hasMethod($name)) {
            throw new \RuntimeException("No callable method $name found on object");
        }

        $func = $this->data[$name];

        if ($func instanceof FuncValue) {
            return $func->invoke($arguments);
        }
        return $func($arguments);
    }

    public function hasMethod(string $name): bool
    {
        if($this->reflection->hasMethod($name)) {
            return true;
        }

        return array_key_exists($name, $this->data) &&
            (   is_callable($this->data[$name]) ||
                $this->data[$name] instanceof FuncValue ||
                $this->data[$name] instanceof FnContainer
            );
    }

    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->__unset($offset);
    }
}