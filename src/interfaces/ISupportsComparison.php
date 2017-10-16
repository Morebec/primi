<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

interface ISupportsComparison {

	public function doComparison(string $operator, Value $operand);

}
