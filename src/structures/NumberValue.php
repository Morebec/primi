<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsUnary;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsDivision,
	ISupportsUnary,
	ISupportsComparison
{

	const TYPE = "number";

	public function __construct(string $value) {
		$this->value = self::isNumericInt($value) ? (int) $value : (float) $value;
	}

	public function getStringValue(): string {
		return (string) $this->value;
	}

	public static function isNumericInt(string $input) {

		// Trim any present sign, because it screws up the detection.
		// "+1" _is_ int, but the equation below would wrongly return false,
		// because it's casted to (int) and the sign disappears there -> false.
		$input = \ltrim($input, "+-");

		// The same with zeroes at the beginning.
		// But only if the input is not a zero.
		$input = $input !== "0" ? \ltrim($input, "0") : $input;

		return (string) (int) $input === (string) $input;

	}

	public static function isNumeric(string $input): bool {
		return
			(bool) \preg_match('#^[+-]?\d+(\.\d+)?$#', $input)
			&& (int) $input !== \PHP_INT_MAX
			&& (int) $input !== \PHP_INT_MIN;
	}

	public function doAddition(Value $right): Value {

		Common::allowTypes($right, self::class);
		return new self($this->value + $right->value);

	}

	public function doSubtraction(Value $right): self {
		Common::allowTypes($right, self::class);
		return new self($this->value - $right->value);
	}

	public function doMultiplication(Value $right) {

		Common::allowTypes($right, self::class, StringValue::class);

		if ($right instanceof StringValue) {
			$multiplier = $this->value;
			if (\is_int($multiplier) && $multiplier >= 0) {
				$new = \str_repeat($right->value, $multiplier);
				return new StringValue($new);
			}
			throw new \TypeError;
		}

		return new self($this->value * $right->value);

	}

	public function doDivision(Value $right): self {

		Common::allowTypes($right, self::class);

		// Avoid division by zero.
		if ($right->value === 0) {
			throw new \Smuuf\Primi\ErrorException("Division by zero");
		}

		return new self($this->value / $right->value);

	}

	public function doUnary(string $op): self {

		switch ($op) {
			case "++":
				return new self($this->value + 1);
			case "--":
				return new self($this->value - 1);
			default:
				throw new \TypeError;
		}

	}

	public function doComparison(string $op, Value $right): BoolValue {

		Common::allowTypes(
			$right,
			self::class,
			BoolValue::class,
			StringValue::class
		);

		// Numbers and strings can only be compared for equality.
		// And are never equal.
		if ($right instanceof StringValue) {

			if ($op === "==") {
				return new BoolValue(false);
			}

			if ($op === "!=") {
				return new BoolValue(true);
			}

			throw new \TypeError;

		}

		$l = $this->value;
		$r = $right->value;

		// Numbers and boolean comparison will use default PHP rules.

		switch ($op) {
			case "==":
				// Don't do strict comparison - it's wrong for floats and ints.
				return new BoolValue($l == $r);
			case "!=":
				// Don't do strict comparison - it's wrong for floats and ints.
				return new BoolValue($l != $r);
			case ">":
				return new BoolValue($l > $r);
			case "<":
				return new BoolValue($l < $r);
			case ">=":
				return new BoolValue($l >= $r);
			case "<=":
				return new BoolValue($l <= $r);
			default:
				throw new \TypeError;
		}

	}

}
