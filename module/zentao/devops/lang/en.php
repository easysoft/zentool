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
$lang->devops = new stdclass();
$lang->devops->help = new stdClass();
$lang->devops->help->devops = <<<EOF
Perform devops operations based on zentao.

Usage
  z devops <command> [options]

Commands
  mr <targetbranch>    Merge code from two branches and test it by pipeline.
EOF;
$lang->devops->help->mr = <<<EOF
Usage
  z [devops] mr <targetbranch>    Merge code from two branches and test it by pipeline.

Example
 z mr master
 z devops mr master
EOF;

$lang->devops->urlTip         = 'The zentao host url, eg http://zentaopms.com:';
$lang->devops->accountTip     = 'username:';
$lang->devops->pwdTip         = 'password:';
$lang->devops->checking       = 'Checking...';
$lang->devops->logging        = 'Logging...';
$lang->devops->urlInvalid     = 'The url %s is not a zentao host, please check again:';
$lang->devops->loginFailed    = 'Login failed. Please check your account and password:';
$lang->devops->loginLimit     = 'Please contact the administrator to unlock your account or try 10 minutes later.';
$lang->devops->notRepository  = 'The current directory is not a git repository';
$lang->devops->noTracking     = 'There is no tracking information for the current branch.';
$lang->devops->noTargetBranch = 'The traget %s is not a valid branch of the repository';
$lang->devops->pipelineTip    = 'The pipeline from zentao, eg zentaoci:';
$lang->devops->pipelineFail   = 'The %s is not associated with the current repository in zentao, please check again:';
$lang->devops->repoNotFound   = 'The repo was not found in zentao!';
$lang->devops->createFail     = 'Create Fail';
$lang->devops->createSuccess  = 'Create Success';
$lang->devops->noAccess       = 'Access is denied, please contact your Administer to grant your permissions.';
