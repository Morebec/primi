<?php


namespace Smuuf\Primi\Handlers;


use Smuuf\Primi\Context;
use Smuuf\Primi\Helpers\ChainedHandler;
use Smuuf\Primi\InternalException;
use Smuuf\Primi\Structures\Value;

class PropertyAccess extends ChainedHandler
{
    public static function chain(array $node, Context $context, Value $subject)
    {
        $propertyName = $node['fn']['core']['text'];

        if (isset($subject->getInternalValue()->$propertyName)) {
            return $subject->getInternalValue()->$propertyName;
        }

        throw new InternalException("Property not found $propertyName on {$subject->getStringValue()}");
    }
}