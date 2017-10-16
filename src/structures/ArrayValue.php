<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsDereference;

class ArrayValue extends Value implements ISupportsIteration, ISupportsDereference {

	const TYPE = "array";

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function __clone() {

		// ArrayValue is really a PHP array of other Primi value objects, so we need to do deep copy.
		array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function dereference(Value $index) {

		$phpIndex = $index->value;

		if (!isset($this->value[$phpIndex])) {
			throw new \Smuuf\Primi\ErrorException("Undefined index '$index'");
		}

		return $this->value[$phpIndex];

	}

	public function callContains(Value $value) {

		// I expect a bug here, since StringValues ought to be equal (how array_search() works) if all properties are
		// equal. Strings can have a cached split buffer which could differ for the "same" internal strings (different
		// objects containing the same string). We'll see, he he.

		return new BoolValue(\array_search($value, $this->value) !== false);

	}

	public function callPush(Value $value) {

		$this->value[] = $value;
		return $value;

	}

	public function callPop() {
		return array_pop($this->value);
	}

}
