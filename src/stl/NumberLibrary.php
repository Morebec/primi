<?php

namespace Smuuf\Primi\Stl;

use \Smuuf\Primi\Library;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\ErrorException;

abstract class NumberLibrary extends Library {

	public static function sqrt(NumberValue $self): NumberValue {
		return new NumberValue(\sqrt($self->value));
	}

	public static function pow(NumberValue $self, NumberValue $power = \null): NumberValue {
		return new NumberValue($self->value ** ($power === \null ? 2 : $power->value));
	}

	public static function sin(NumberValue $self): NumberValue {
		return new NumberValue(\sin($self->value));
	}

	public static function cos(NumberValue $self): NumberValue {
		return new NumberValue(\cos($self->value));
	}

	public static function tan(NumberValue $self): NumberValue {
		return new NumberValue(\tan($self->value));
	}

	public static function atan(NumberValue $self): NumberValue {
		return new NumberValue(\atan($self->value));
	}

	public static function ceil(NumberValue $self): NumberValue {
		return new NumberValue(\ceil($self->value));
	}

	public static function floor(NumberValue $self): NumberValue {
		return new NumberValue(\floor($self->value));
	}

	public static function round(NumberValue $self, NumberValue $precision = \null): NumberValue {
		return new NumberValue(\round($self->value, $precision ? $precision->value : 0));
	}

}
