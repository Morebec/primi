<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

class ArrayExtension extends Extension {

	public static function array_copy(ArrayValue $arr): ArrayValue {
		return clone $arr;
	}

	public static function array_length(ArrayValue $arr): NumberValue {
		return new NumberValue((string) count($arr->value));
	}

	public static function array_reverse(ArrayValue $arr): Value {
		return new ArrayValue(array_reverse($arr->value));
	}

	public static function array_random(ArrayValue $arr): Value {
		return $arr->value[array_rand($arr->value)];
	}

	public static function array_shuffle(ArrayValue $arr): ArrayValue {

		// Do NOT modify the original array argument (as PHP would do).
		$copy = clone $arr;
		shuffle($arr->value);

		return $arr;

	}

	public static function array_map(ArrayValue $arr, FuncValue $fn): ArrayValue {

		$result = [];
		foreach ($arr->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new ArrayValue($result);

	}

	public static function array_contains(ArrayValue $arr, Value $needle): BoolValue {

		// Allow only some value types.
		Common::allowTypes($needle, StringValue::class, NumberValue::class);

		// Let's search the $needle object in $arr's value (array of objects).
		return new BoolValue(\array_search($needle, $arr->value) !== \false);

	}

	public static function array_number_of(ArrayValue $arr, Value $needle): NumberValue {

		// Allow only some value types.
		Common::allowTypes($needle, StringValue::class, NumberValue::class);

		// We must convert Primi values back to PHP values for the
		// array_count_values function to work.
		$phpValues = array_map(function($item) {
			return $item->value;
		}, $arr->value);

		$valuesCount = \array_count_values($phpValues);
		$count = $valuesCount[$needle->value] ?? 0;

		return new NumberValue((string) $count);

	}

	public static function array_push(ArrayValue $arr, Value $value): Value {
		$arr->value[] = $value;
		return $value;
	}

	public static function array_pop(ArrayValue $arr): Value {
		return \array_pop($arr->value);
	}

}
