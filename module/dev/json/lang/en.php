<?php
$lang->json = new stdClass();
$lang->json->help = new stdClass();
$lang->json->help->json = <<<EOF
Decodes a JSON string or given file.

Usage
  z json <command> [options]

Commands
  decode  -d  <string | filepath>     Takes a JSON encoded string and converts it into a PHP variable.
EOF;
$lang->json->help->decode = <<<EOF
Usage
  z json decode <string | filepath>

Options
  -a,--associative     Use the option, JSON objects will be returned as associative arrays; otherwise, JSON objects will be returned as objects.

Example
  z json decode /z/example.json
  z json decode '{\"name\":\"ZenTools\",\"command\":\"z\"}'
EOF;
