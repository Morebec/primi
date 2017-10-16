<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class FunctionDefinition extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$functionName = $node['function']['text'];

		$argumentList = [];
		if (isset($node['args'])) {

			// Handle situation with solo arguments (which wouldn't be represented as array).
			// Do it by placing solo arguments into arrays.
			if (!isset($node['args'][0])) {
				$node['args'] = [$node['args']];
			}

			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		$context->setFunction(
			$functionName,
			new \Smuuf\Primi\Structures\Func(
				$functionName,
				$argumentList,
				$node['body']
			)
		);

	}

}
