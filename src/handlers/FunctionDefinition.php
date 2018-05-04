<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

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

			Helpers::ensureIndexed($node['args']);

			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		$fn = FnContainer::build($node['body'], $argumentList, $context);
		$context->setVariable($functionName, new FuncValue($fn));

	}

}
