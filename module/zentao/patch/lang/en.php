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
$lang->patch = new stdclass();
$lang->patch->help->patch = <<<EOF
Display the list, details and install the zentao patches.

Usage
  z patch <command> [options]

Actions
  list      -l              List all avaliable patches for current zentao version.
  view      -v   <patchid>  View a patch.
  install   -i   <patchid>  Install a patch.
  revert    -r   <patchid>  Revert an installed patch.

EOF;
$lang->patch->help->list = <<<EOF
Usage
  z patch list [options]

Options
  -a, all    List all the patches (include installed).
  -l, local  List all installed patches.

Example
  z patch list -a

EOF;
$lang->patch->help->view = <<<EOF
Usage
  z patch view <patchid>

Example
  z patch view 1  View the patch details which id is 1

EOF;
$lang->patch->viewPage = <<<EOF
          ID : %s
       Title : %s
 Description : %s
Change Files : %s
 Change Logs : %s

EOF;
