<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;

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
		shuffle($copy->value);

		return $copy;

	}

	public static function array_map(ArrayValue $arr, FuncValue $fn): ArrayValue {

		$result = [];
		foreach ($arr->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new ArrayValue($result);

	}

    /**
     * Filters an array by a given function
     * @param ArrayValue $arr
     * @param FuncValue $fn
     * @return ArrayValue
     */
    public static function array_filter(ArrayValue $arr, FuncValue $fn): ArrayValue
    {
        $filtered = [];
        foreach ($arr->value as $k => $v) {
            /** @var BoolValue $returnedValue */
            $returnedValue = $fn->invoke([$v]);
            Common::allowTypes($returnedValue,BoolValue::class);

            if($returnedValue->value === true) {
                $filtered[$k] = $v;
            }
        }

        return new ArrayValue($filtered);
	}

    /**
     * Converts an array to json
     * @param ArrayValue $arr
     * @return StringValue
     */
    public static function array_json(ArrayValue $arr): StringValue
    {
        $flatten = static function (array $array) use (&$flatten) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $flatten($array[$key]);
                } elseif ($value instanceof ArrayValue) {
                    $array[$key] = $flatten($value->value);
                } else {
                    /** @var Value $value */
                    $array[$key] = $value->value;
                }
            }
            return $array;
        };

        $flattened = $flatten($arr->value);

        return new StringValue(json_encode($flattened));
	}

	public static function array_contains(ArrayValue $arr, Value $needle): BoolValue {

		// Allow only some value types.
		Common::allowTypes($needle, StringValue::class, NumberValue::class);

		// Let's search the $needle object in $arr's value (array of objects).
		return new BoolValue(\array_search($needle, $arr->value) !== \false);

	}

	public static function array_has(ArrayValue $arr, Value $key): BoolValue {

		// Allow only some value types.
		Common::allowTypes($key, StringValue::class, NumberValue::class);

		// Return true if the key exists in this array.
		return new BoolValue(isset($arr->value[$key->value]));

	}

    /**
     * Returns the keys of an array as an array
     * @param ArrayValue $arr
     * @return ArrayValue
     */
    public static function array_keys(ArrayValue $arr): ArrayValue {
	    $keys = \array_keys($arr->value);

	    return new ArrayValue(array_map(static function ($k) {
	        return Value::buildAutomatic($k);
	        }, $keys)
        );
	}

    /**
     * Return all the values of an array as an indexed array of values
     * @param ArrayValue $arr
     * @return ArrayValue
     */
	public static function array_values(ArrayValue $arr): ArrayValue {
        return new ArrayValue(\array_values($arr->value));
    }

	public static function array_get(ArrayValue $arr, Value $key, Value $default = null): Value {

		// Allow only some value types.
		Common::allowTypes($key, StringValue::class, NumberValue::class);
		return $arr->value[$key->value] ?? $default ?? new NullValue;

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
