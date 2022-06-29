<?php
/**
 * The index module english file of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
$lang->entry = new stdclass();
$lang->entry->help = <<<EOF
Welcome to the ZenTools for geeks. The current application is dev.
You can use 'z app list' and 'z app switch appName' to get all applications and switch to one.

App
 'z app list':            List available applications.
 'z app switch appName':  Switch current application to appName.

Usage
  z [feature] [command] [options]

Feature
  md5:     Calculates the md5 hash of the a string or given file.
  url:     Encodes the given string with URL or decodes a URL encoded string.
  base64:  Encodes the given string with base64 or decodes a base64 encoded string.
EOF;
