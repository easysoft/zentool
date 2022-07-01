<?php
$lang->base64 = new stdClass();
$lang->base64->help = new stdClass();
$lang->base64->help->base64 = <<<EOF
Encodes the given string with base64 or decodes a base64 encoded string.

Usage
  z base64 <command> [options]

Commands
  encode  -e   <string>     View the result of encodes the given string with base64.
  decode  -d   <string>     View the result of decodes a base64 encoded string.
EOF;
$lang->base64->help->encode = <<<EOF
Usage
  z base64 encode <string>

Example
  z base64 encode zentools
EOF;
$lang->base64->help->decode = <<<EOF
Usage
  z base64 decode <string>

Example
  z base64 decode zentools
EOF;
$lang->base64->error = 'The string %s is not base64 string!';
