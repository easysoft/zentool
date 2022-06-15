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
基于禅道执行devops操作。

用法
  z devops <command> [options]

命令
  mr  <targetbranch>    合并来自两个分支的代码，并通过流水线对其进行测试。
EOF;
$lang->devops->help->mr = <<<EOF
用法
  z [devops] mr <targetbranch>    合并来自两个分支的代码，并通过流水线对其进行测试。

例如
 z mr master
 z devops mr master
EOF;

$lang->devops->urlTip         = '请输入禅道地址： 例如：https://zentaopms.com：';
$lang->devops->accountTip     = '用户名：';
$lang->devops->pwdTip         = '密码：';
$lang->devops->checking       = '正在检验网址...';
$lang->devops->logging        = '正在登录...';
$lang->devops->urlInvalid     = '该网址 %s 不存在或不是禅道网址，请重新输入：';
$lang->devops->loginFailed    = '登录失败，请验证账号密码后重试：';
$lang->devops->loginLimit     = '密码尝试次数太多，请联系管理员解锁，或10分钟后重试。';
$lang->devops->notRepository  = '当前目录不是一个版本库';
$lang->devops->noTracking     = '当前分支还未提交到远程。';
$lang->devops->noTargetBranch = '目标分支不是一个有效分支。';
