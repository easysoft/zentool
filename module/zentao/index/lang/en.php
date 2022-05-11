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
$lang->index = new stdclass();
$lang->index->index = 'Home';
$lang->index->help  = <<<EOF
Welcome to use the Z tools for geek.The current application is %s(default).
You can use z app list and z app switch appName to get all applications and switch to one.

App
 z app list: List avaliable applications.
 z app switch appName: Switch current application to appName.

Usage
  z [feature] [command] [options]

Feature
  patch:  Install the zentao patch.
  set:  Display and change configuration settings for current application.

Options
  -h,--help  Provide help for any command in the z.ny cohemmand in the application
  -v, --version  Display the z version.

Use "z [feature] --help or -h" for more information about a module.

EOF;
