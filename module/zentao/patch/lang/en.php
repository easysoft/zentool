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
Show list, display details, or perform the installation for patches fit for the current zentao version

Usage
  z patch <command> [options]

Commands
  list      -l                  List all available patches for the current zentao version.
  view      -v   <patchid>      View a patch.
  install   -i   <patchid>      Install a patch.
  revert    -r   <patchid>      Revert an installed patch.
  build     -b                  Build a patch.
  release   -rel                Release a patch.
EOF;
$lang->patch->help->list = <<<EOF
Usage
  z patch list [options]

Options
  -a, --all    List all the patches (including installed).
  --local      List all installed patches.

Example
  z patch list -a
EOF;
$lang->patch->help->view = <<<EOF
Usage
  z patch view <patchid>     View the patch details which id is patched

Example
  z patch view 1
EOF;
$lang->patch->help->install = <<<EOF
Usage
  z patch install <id | path>     Need permission to operate the zentao root directory.

Example
  z patch install 1
  z patch install /patches/zentao.bug.1.zip
EOF;
$lang->patch->help->revert = <<<EOF
Usage
  z patch revert <id | path>     Need permission to operate the zentao root directory.

Example
  z patch revert 1
  z patch revert /patches/zentao.bug.1.zip
EOF;
$lang->patch->help->build = <<<EOF
Usage
  z patch build      Build a zentao patch.

Example
  z patch build
EOF;
$lang->patch->help->release = <<<EOF
Usage
  z patch release      release a zentao patch.

Example
  z patch release /patches/zentao.bug.1.zip
EOF;
$lang->patch->viewPage = <<<EOF
          ID: %s
        Name: %s
 Description: %s
 Create Date: %s
 Change Logs: %s
EOF;
$lang->patch->name           = 'Name';
$lang->patch->type           = 'Type';
$lang->patch->code           = 'Code';
$lang->patch->id             = 'ID';
$lang->patch->date           = 'Date';
$lang->patch->installed      = 'Installed';
$lang->patch->downloading    = 'Downloading...' . PHP_EOL;
$lang->patch->down           = 'Done' . PHP_EOL;
$lang->patch->backuping      = 'Backuping...' . PHP_EOL;
$lang->patch->installing     = 'Installing...' . PHP_EOL;
$lang->patch->installDone    = 'Install successfully, using "z patch list --local" to view all installed patches.' . PHP_EOL;
$lang->patch->restoring      = 'Restoring...' . PHP_EOL;
$lang->patch->restored       = 'Revert successfully' . PHP_EOL;
$lang->patch->building       = 'Building...' . PHP_EOL;
$lang->patch->buildSuccess   = 'Build successfully' . PHP_EOL;
$lang->patch->releaseSuccess = 'Release successfully' . PHP_EOL;
$lang->patch->tryTimeLimit   = 'Please re-execute and enter' . PHP_EOL;
$lang->patch->buildDocTpl    = <<<EOF
---
name: %s
code: %s
type: patch
copyright: >
  青岛易软天创网络科技有限公司
  %s
site: http://www.zentao.net
author: '%s'
abstract: %s
desc: %s
install:
releases:
  %s:
    charge: free
    license: %s
    changelog: %s
    date: %s
    zentao:
      compatible: %s
      incompatible:
    depends: null
    conflicts: null
EOF;

$lang->patch->build = new stdClass();
$lang->patch->build->versionTip   = 'Please input the current installed Zentao version, e.g. 16.5, biz6.5, max3.0.  For multi-version installed, seperate them with "," :' . PHP_EOL;
$lang->patch->build->typeTip      = 'Please set the patch type, story or bug:' . PHP_EOL;
$lang->patch->build->idTip        = 'Please set the id of the story or bug:' . PHP_EOL;
$lang->patch->build->buildPathTip = 'Please set the patch directory, eg /zentao/build:' . PHP_EOL;
$lang->patch->build->descTip      = 'Please set the description:' . PHP_EOL;
$lang->patch->build->changelogTip = 'Please set the change log:' . PHP_EOL;
$lang->patch->build->authorTip    = 'Please set the author:' . PHP_EOL;
$lang->patch->build->licenseTip   = 'Please set the license:' . PHP_EOL;

$lang->patch->release = new stdClass();
$lang->patch->release->replaceTip   = 'Are you sure to replace the released patch? (y/n)' . PHP_EOL;
$lang->patch->release->descTip      = 'The description:' . PHP_EOL;
$lang->patch->release->changelogTip = 'The change log:' . PHP_EOL;
$lang->patch->release->needCzUser   = 'You need to configure the Zentao website account:' . PHP_EOL;
$lang->patch->release->accountTip   = 'Please set the account:' . PHP_EOL;
$lang->patch->release->passwordTip  = 'Please set the password:' . PHP_EOL;
$lang->patch->release->userInvalid  = 'Login failed. Please check your account and password:' . PHP_EOL;

$lang->patch->error = new stdClass();
$lang->patch->error->runSet       = 'Please use "z set" to set the zentao directory!' .  PHP_EOL;
$lang->patch->error->notWritable  = 'Directory %s does not have write access!' .  PHP_EOL;
$lang->patch->error->installed    = 'You have already installed this patch package!' .  PHP_EOL;
$lang->patch->error->notInstall   = 'You have not installed this patch package!' .  PHP_EOL;
$lang->patch->error->invalid      = 'The patch id is invalid!' .  PHP_EOL;
$lang->patch->error->incompatible = 'This patch is incompatible with current ZenTao version!' .  PHP_EOL;
$lang->patch->error->invalidName  = 'The path %s is not a zentao patch, please check.' .  PHP_EOL;
$lang->patch->error->invalidFile  = 'The file %s is not a zentao patch, please check.' .  PHP_EOL;
$lang->patch->error->notFound     = 'The patch not found, please check.' .  PHP_EOL;

$lang->patch->error->build = new stdClass();
$lang->patch->error->build->version   = 'The version %s is invalid, please check again:' . PHP_EOL;
$lang->patch->error->build->type      = 'The type %s is invalid, please check again:' . PHP_EOL;
$lang->patch->error->build->id        = 'The ID %s is invalid, please check again:' . PHP_EOL;
$lang->patch->error->build->patch     = 'The patchId %s is exist, please try another one:' . PHP_EOL;
$lang->patch->error->build->buildPath = 'The directory %s is invalid, please check again:' . PHP_EOL;
