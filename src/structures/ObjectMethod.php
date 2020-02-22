<?php


namespace Smuuf\Primi\Structures;


class ObjectMethod extends FuncValue
{
    public function __construct(ObjectValue $objectValue, FnContainer $fn)
    {
        parent::__construct($fn);
        $this->object = $objectValue;
    }

    /**
     * @return ObjectValue
     */
    public function getObject(): ObjectValue
    {
        return $this->object;
    }


    public function __set(string $name, $value)
    {
        //Skip parent
    }

    public function __get(string $name)
    {
        // Skip parent
        // parent::__get($name);
    }
}