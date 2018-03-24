<?php

use \Tester\Assert;

use \Smuuf\Primi\InternalArgumentCountException;
use \Smuuf\Primi\Structures\{
	FuncValue,
	NumberValue,
	Value,
	FunctionContainer
};

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

$one = new NumberValue(1);
$two = new NumberValue(2);
$three = new NumberValue(3);
$five = new NumberValue(5);

//
// Function invocation.
//

// Create Primi function from a native PHP function.
$fn = new FuncValue(FunctionContainer::buildNative(function($a, $b) {
	return new NumberValue($a->getInternalValue() * $b->getInternalValue() ** 2);
}));

Assert::same(4, get_val($fn->invoke([$one, $two])));
Assert::same(45, get_val($fn->invoke([$five, $three])));

//
// Bound native function error handling.
//

// No arguments (but expected some).
Assert::exception(function() use ($fn) {
	$fn->invoke([]);
}, InternalArgumentCountException::class);

// Too many arguments (expected less).
Assert::exception(function() use ($fn, $one, $two, $three) {
	$fn->invoke([$one, $two, $three]);
}, InternalArgumentCountException::class);
