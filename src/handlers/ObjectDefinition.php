<?php


namespace Smuuf\Primi\Handlers;


use http\Exception\RuntimeException;
use Smuuf\Primi\Context;
use Smuuf\Primi\HandlerFactory;
use Smuuf\Primi\Helpers\Common;
use Smuuf\Primi\Helpers\SimpleHandler;
use Smuuf\Primi\Structures\NumberValue;
use Smuuf\Primi\Structures\ObjectValue;
use Smuuf\Primi\Structures\PSLObject;

class ObjectDefinition extends SimpleHandler
{
	public static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new ObjectValue(new PSLObject());
		}

		Common::ensureIndexed($node['items']);
		return new ObjectValue(self::buildObject($node['items'], $context));

	}

	protected static function buildObject(array $itemNodes, Context $context): PSLObject
    {

		$result = [];
		$index = 0;

		foreach ($itemNodes as $itemNode) {

			// Key doesn't have to be defined.
			if (isset($itemNode['key'])) {

				// But if it is defined for this item, use it.
				$keyHandler = HandlerFactory::get($itemNode['key']['name']);
				$key = $keyHandler::handle($itemNode['key'], $context);
				$key = $key->getInternalValue();

				// And if it is a numeric integer, use it as a base for the
				// index counter we would have used if the key was not provided.
				if (NumberValue::isNumericInt($key)) {
					$index = $key + 1;
					throw new \RuntimeException('The name of an object key must be a string starting with a character');
				}

			} else {

				// The key was not provided, so assign a key for this item using
				// our private index counter.
				$key = $index++;

			}

			$valueHandler = HandlerFactory::get($itemNode['value']['name']);
			$value = $valueHandler::handle($itemNode['value'], $context);

			$result[$key] = $value;

		}

		return new PSLObject($result);

	}
}