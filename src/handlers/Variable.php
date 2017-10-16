<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Variable extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		if ($unaryResult = UnaryOperator::handle($node, $context)) {
			return $unaryResult;
		}

		$handler = HandlerFactory::get($node['core']['name']);
		return $handler::handle($node['core'], $context);

	}

	public static function reduce(array $node) {

		if (!isset($node['pre']) && !isset($node['post'])) {
			return $node['core'];
		}

	}

}
