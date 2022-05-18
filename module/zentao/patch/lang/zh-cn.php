<?php
/**
 * The index module simplified chinese file of zentaoPHP.
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
对适合当前禅道版本的补丁包进行查看列表、详细信息和安装操作。

用法
  z patch <command> [options]

命令
  list      -l              列出当前禅道版本的所有可用补丁包。
  view      -v   <patchid>  查看补丁包。
  install   -i   <patchid>  安装补丁包。
  revert    -r   <patchid>  还原已安装的补丁。

EOF;
$lang->patch->help->list = <<<EOF
用法
  z patch list [options]

操作
  -a, --all   列出所有补丁（包括已安装的）。
  -l, --local 列出所有已安装的补丁。

例如
  z patch list -a

EOF;
$lang->patch->help->view = <<<EOF
用法
  z patch view <patchid>

例如
  z patch view 1 查看ID为1的补丁包信息

EOF;
$lang->patch->help->install = <<<EOF
用法
  z patch install <patchid>  * 需要操作禅道目录的权限。

例如
  z patch install 1  安装ID为1的补丁包。
  z patch install /zentao/patches/zentao.15.0.1.beta.bug.1.zip

EOF;
$lang->patch->help->revert = <<<EOF
用法
  z patch revert <patchid>  * 需要操作禅道目录的权限。

例如
  z patch revert 1  还原补丁包1的修改。

EOF;
$lang->patch->viewPage = <<<EOF
        ID：%s
      标题：%s
      描述：%s
修改的文件：%s
修改的内容：%s

EOF;
$lang->patch->title       = '标题';
$lang->patch->type        = '类型';
$lang->patch->code        = '编号';
$lang->patch->date        = '日期';
$lang->patch->installed   = '已安装';
$lang->patch->downloading = '正在下载补丁包...' . PHP_EOL;
$lang->patch->down        = '完成' . PHP_EOL;
$lang->patch->backuping   = '正在备份代码...' . PHP_EOL;
$lang->patch->installing  = '正在安装补丁...' . PHP_EOL;
$lang->patch->installDone = '安装成功，使用 z patch list local 可以查看已安装补丁包列表。' . PHP_EOL;
$lang->patch->restoring   = '正在还原...' . PHP_EOL;
$lang->patch->restored    = '还原成功' . PHP_EOL;

$lang->patch->error = new stdClass();
$lang->patch->error->runSet       = '需要先执行z set命令后操作！' .  PHP_EOL;
$lang->patch->error->notWritable  = '目录 %s 没有写权限！' .  PHP_EOL;
$lang->patch->error->installed    = '您已安装过该补丁包！' .  PHP_EOL;
$lang->patch->error->notInstall   = '您还未安装该补丁包！' .  PHP_EOL;
$lang->patch->error->invalid      = '该补丁包不存在！' .  PHP_EOL;
$lang->patch->error->incompatible = '该补丁包与当前禅道版本不兼容！' .  PHP_EOL;
