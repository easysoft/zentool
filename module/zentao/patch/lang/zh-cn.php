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
  z patch <命令> [参数]

命令
  list      -l                  列出当前禅道版本的所有可用补丁包。
  view      -v   <补丁包ID>     查看补丁包。
  install   -i   <补丁包ID>     安装补丁包。
  revert    -r   <补丁包ID>     还原已安装的补丁。
  build     -b                  构建补丁包。
  release   -rel                发布补丁包。
EOF;
$lang->patch->help->list = <<<EOF
用法
  z patch list [参数]

操作
  -a, --all   列出所有补丁（包括已安装的）。
  --local     列出所有已安装的补丁。

例如
  z patch list -a
EOF;
$lang->patch->help->view = <<<EOF
用法
  z patch view <补丁包ID>     查看补丁包详细信息

例如
  z patch view 1
EOF;
$lang->patch->help->install = <<<EOF
用法
  z patch install <补丁包ID | 本地路径>    需要操作禅道目录的权限。

例如
  z patch install 1
  z patch install /patches/zentao.bug.1.zip
EOF;
$lang->patch->help->revert = <<<EOF
用法
  z patch revert <补丁包ID | 本地路径>     需要操作禅道目录的权限。

例如
  z patch revert 1
  z patch revert /patches/zentao.bug.1.zip
EOF;
$lang->patch->help->build = <<<EOF
用法
  z patch build      构建补丁包。

例如
  z patch build
EOF;
$lang->patch->help->release = <<<EOF
用法
  z patch release     发布一个补丁包。

例如
  z patch release /patches/zentao.bug.1.zip
EOF;
$lang->patch->viewPage = <<<EOF
        ID：%s
      标题：%s
      描述：%s
  发布时间：%s
修改的内容：%s
EOF;
$lang->patch->name           = '标题';
$lang->patch->type           = '类型';
$lang->patch->code           = '编号';
$lang->patch->id             = 'ID';
$lang->patch->date           = '日期';
$lang->patch->installed      = '已安装';
$lang->patch->downloading    = '正在下载补丁包...';
$lang->patch->down           = '完成';
$lang->patch->backuping      = '正在备份代码...';
$lang->patch->installing     = '正在安装补丁...';
$lang->patch->installDone    = '安装成功，使用 z patch list --local 可以查看已安装补丁包列表。';
$lang->patch->restoring      = '正在还原...';
$lang->patch->restored       = '还原成功';
$lang->patch->building       = '正在构建...';
$lang->patch->buildSuccess   = '构建成功';
$lang->patch->releaseSuccess = '发布成功';
$lang->patch->tryTimeLimit   = '请重新运行工具后重试！';
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
$lang->patch->build->versionTip   = '请输入禅道版本，例如 16.5, biz6.5, max3.0, 使用英文,分隔多个版本：';
$lang->patch->build->typeTip      = '请设置补丁包类型，story 或 bug：';
$lang->patch->build->idTip        = '请设置需求或bug的ID：';
$lang->patch->build->buildPathTip = '请设置补丁包路径，例如 /zentao/build：';
$lang->patch->build->descTip      = '请设置描述信息：';
$lang->patch->build->changelogTip = '请设置修改记录：';
$lang->patch->build->authorTip    = '请设置提交者：';
$lang->patch->build->licenseTip   = '请设置授权协议：';

$lang->patch->release = new stdClass();
$lang->patch->release->replaceTip   = '您确定要覆盖已发布的同名补丁包吗？ (y/n)';
$lang->patch->release->descTip      = '描述:';
$lang->patch->release->changelogTip = '变更记录:';
$lang->patch->release->needCzUser   = '请输入禅道官网的账号和密码，可通过https://www.zentao.net进行注册：';
$lang->patch->release->accountTip   = '请输入账号：';
$lang->patch->release->passwordTip  = '请输入密码：';
$lang->patch->release->userInvalid  = '登录失败！请验证账号或密码是否正确：';

$lang->patch->error = new stdClass();
$lang->patch->error->runSet       = '需要先执行 z set 命令后操作！';
$lang->patch->error->notWritable  = '目录 %s 没有写权限！';
$lang->patch->error->installed    = '您已安装过该补丁包！';
$lang->patch->error->notInstall   = '您还未安装该补丁包！';
$lang->patch->error->invalid      = '该补丁包不存在！';
$lang->patch->error->incompatible = '该补丁包与当前禅道版本不兼容！';
$lang->patch->error->invalidName  = '输入的地址 %s 不正确，请确认。';
$lang->patch->error->invalidFile  = '输入的文件 %s 不是一个禅道补丁包，请确认。';
$lang->patch->error->notFound     = '该信息不存在，请确认！';

$lang->patch->error->build = new stdClass();
$lang->patch->error->build->version   = '该版本号 %s 无效，请重新输入：';
$lang->patch->error->build->type      = '该类型 %s 无效，请重新输入：';
$lang->patch->error->build->id        = '该ID %s 不是有效的需求或bug ID，请重新输入：';
$lang->patch->error->build->patch     = '该ID %s 已存在，请输入其他名称：';
$lang->patch->error->build->buildPath = '该目录 %s 无效，请重新输入：';

$lang->patch->api = new stdClass();
$lang->patch->api->notDevloper     = '你现在还不是开发者, 不能上传插件。';
$lang->patch->api->emptyFile       = '请上传待发布的文件。';
$lang->patch->api->uploadFail      = '文件上传失败。';
$lang->patch->api->emptyLang       = '语言列表不能为空。';
$lang->patch->api->emptyName       = '补丁名称不能为空。';
$lang->patch->api->emptyVersion    = '版本号不能为空。';
$lang->patch->api->emptyID         = 'ID不能为空。';
$lang->patch->api->createFail      = '上传失败';
$lang->patch->api->uploadSuccess   = '上传成功';
$lang->patch->api->releaseNotFound = '未查询到该版本';
