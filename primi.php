#!/usr/bin/php
<?php

// Composer's autoload.
require __DIR__ . "/vendor/autoload.php";

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/temp/");
$loader->addDirectory(__DIR__ . "/src")->register();

array_shift($argv);
if (empty($argv[0])) {
	die("Input file not specified.");
}

$config = parse_arguments($argv);

if ($config['input_is_code']) {
	$source = $config['input'];
} else {
	$filepath = $config['input'];
	if (!is_file($filepath)) {
		die("Input file '$filepath' not found.");
	}
	$source = file_get_contents($filepath);
}

// Load source, create "VM" context and create the interpreter.

$context = new \Smuuf\Primi\Context;
$interpreter = new \Smuuf\Primi\Interpreter($context, __DIR__ . "/temp/");

try {

	// Get syntax tree.
	if ($config['tree']) {
		print_r($interpreter->getSyntaxTree($source));
		die;
	}

	// Run interpreter
	$interpreter->run($source);
	if ($config['print_context']) {
		var_dump($context->getVariables());
		die;
	}

} catch (\Smuuf\Primi\ErrorException $e) {

	die($e->getMessage());

}

function parse_arguments(array $args): array {

	$config = [
		'tree' => false,
		'print_context' => false,
		'input_is_code' => false,
		'input' => false,
	];

	while ($a = array_shift($args)) {
		switch ($a) {
			case "-t":
			case "--tree":
				$config['tree'] = true;
			break;
			case "-s":
			case "--source":
				$config['input_is_code'] = true;
			break;
			case "-c":
			case "--print-context":
				$config['print_context'] = true;
			break;
			default:
				$config['input'] = $a;
			break;
		}
	}

	return $config;

}
