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
$lang->patch->help = new stdClass();
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
  -a, --all    List all the patches (include installed).
  -l, --local  List all installed patches.

Example
  z patch list -a

EOF;
$lang->patch->help->view = <<<EOF
Usage
  z patch view <patchid>

Example
  z patch view 1  View the patch details which id is 1

EOF;
$lang->patch->help->install = <<<EOF
Usage
  z patch install <patchid>  *Need permission to operate the zentao root directory.

Example
  z patch install 1  Install the zentao patch which id is 1.

EOF;
$lang->patch->help->revert = <<<EOF
Usage
  z patch revert <patchid>  *Need permission to operate the zentao root directory.

Example
  z patch revert 1  Revert the installed zentao patch which id is 1.

EOF;
$lang->patch->viewPage = <<<EOF
          ID : %s
       Title : %s
 Description : %s
Change Files : %s
 Change Logs : %s

EOF;
$lang->patch->title       = 'Title';
$lang->patch->type        = 'Type';
$lang->patch->code        = 'Code';
$lang->patch->date        = 'Date';
$lang->patch->installed   = 'Installed';
$lang->patch->downloading = 'Downloading...' . PHP_EOL;
$lang->patch->down        = 'Down' . PHP_EOL;
$lang->patch->backuping   = 'Backuping...' . PHP_EOL;
$lang->patch->installing  = 'Installing...' . PHP_EOL;
$lang->patch->installed   = 'Install successfuly' . PHP_EOL;
$lang->patch->restoring   = 'Restoring...' . PHP_EOL;
$lang->patch->restored    = 'Revert successfuly' . PHP_EOL;

$lang->patch->error = new stdClass();
$lang->patch->error->runSet      = 'You should execute the set command first!' .  PHP_EOL;
$lang->patch->error->notWritable = 'Directory %s does not have write access!' .  PHP_EOL;
$lang->patch->error->installed   = 'You have already installed this patch package!' .  PHP_EOL;
$lang->patch->error->notInstall  = 'You have not installed this patch package!' .  PHP_EOL;
