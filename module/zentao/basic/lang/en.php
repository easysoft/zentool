<?php
$lang->basic = new stdClass();
$lang->basic->diff = new stdClass();
$lang->basic->diff->needPath    = 'Please pass the latest catalog and comparison catalog!';
$lang->basic->diff->pathNotReal = 'The path %s not exists, please check again!';

$lang->basic->diff->help = <<<EOF
Usage
  z diff -n <newDir> -d <diffDir> [-v]    Compare two folder changes.

  -n <newDir>     The latest floder.
  -d <diffDir>    The diff floder.
  -v              Show compare result.

Example
  z diff -n path1 -d path2
EOF;
