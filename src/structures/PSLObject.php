<?php


namespace Smuuf\Primi\Structures;

/**
 * Property Access allowing dynamically defined objects
 */
class PSLObject
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __isset($name)
    {
        return isset($this->data['key']);
    }

    public function __get($name)
    {
        if(isset($this->data[$name])) {
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
        if(isset($this->data[$name]))
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
        return array_key_exists($name, $this->data) &&
            (   is_callable($this->data[$name]) ||
                $this->data[$name] instanceof FuncValue ||
                $this->data[$name] instanceof FnContainer
            );
    }
}