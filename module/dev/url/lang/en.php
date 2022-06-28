<?php
$lang->url = new stdClass();
$lang->url->help = new stdClass();
$lang->url->help->url = <<<EOF
Encodes the given string with URL or decodes a URL encoded string.

Usage
  z url <command> [options]

Commands
  encode -e <string>     View the result of encodes the given string with URL.
  decode -d <string>     View the result of decodes a URL encoded string.
EOF;
$lang->url->help->encode = <<<EOF
Usage
  z url encode <string>

Example
  z url encode project
EOF;
$lang->url->help->decode = <<<EOF
Usage
  z url  decode <string>

Example
  z url decode my=project&are=zentao+xuanxuan
EOF;
