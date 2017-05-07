<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

class StringLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$content = $node['text'];

		// Trim quotes from the start and the end using substr().
		// Using trim("\"'", ...) would make `"abc'"` into `abc` instead of `abc'`,
		// so do this a little more directly.
		$string = mb_substr($content, 1, mb_strlen($content) - 2);

		return self::expandSequences($string);

	}

	protected static function expandSequences(string $string) {

		// Out strings support escape sequences.

		$string = str_replace('\n', "\n", $string);
		return $string;

	}

}