#!/bin/bash

cd $(dirname $0)

php ./vendor/combyna/php-peg/bin/peg ./src/parser/Grammar.peg ./src/parser/CompiledParser.php
rm -f ./temp/*.json
