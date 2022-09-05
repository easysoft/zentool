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
$lang->apicheck = new stdclass();
$lang->apicheck->webDirTip     = '请输入禅道目录，例如：/opt/zentao ：';
$lang->apicheck->webDirNotReal = '目录%s不是禅道目录，请重新输入：';
$lang->apicheck->checking      = '正在检查...';
$lang->apicheck->checkFail     = '文件 %s 第 %d 行参数数量不正确！';
$lang->apicheck->checkSuccess  = '检查完成，没有问题。';
$lang->apicheck->errorSaved    = '检查完成，详细对比异常的信息已保存到 %s。';
$lang->apicheck->saveContent   = <<<EOF
%d、文件 %s 第 %d 行参数数量不正确！
    模块名：%s，  方法名： %s
    模块方法路径：    %s
    API部分代码：     %s
    Control部分代码： %s

EOF;
