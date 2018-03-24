<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Helpers;
use \Smuuf\Primi\InternalArgumentCountException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * method: Method name.
 * args: List of arguments.
 */
class MethodCall extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		$methodName = $node['method']['text'];

		// Evaluate and prepare a list of arguments for later.
		$argList = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$argList = $handler::handle($node['args'], $context);
		}

		try {
			return Helpers::invokeValueMethod($subject, $methodName, $argList, $node);
		} catch (InternalArgumentCountException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

}
