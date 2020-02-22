<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsInvocation;
use Smuuf\Primi\MaxRecursionException;

class FuncValue extends Value implements ISupportsInvocation {

	const TYPE = "function";

	// This number is used to align with PHP
    public const MAX_RECURSION_DEPTH = 256;

    private $nestedCalls;

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
		$this->nestedCalls = 0;
	}

	public function getStringValue(): string {
		return "function";
	}

	public function invoke(array $args = []) {

        $this->nestedCalls++;

        if($this->nestedCalls === self::MAX_RECURSION_DEPTH) {
            $this->nestedCalls = 0;
            throw new MaxRecursionException('Maximum recursion depth reached.');
        }

		// Simply execute the closure with passed arguments.
		$ret = ($this->value->getClosure())(...$args);

		$this->nestedCalls--;
        return $ret;

	}
}
