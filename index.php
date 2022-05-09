<?php
/**
 * 整个应用的入口文件。
 * The router file of zentaophp.
 *
 * All request should be routed by this router.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
/* 先关闭所有的错误输出。Turn off error reporting first. */
error_reporting(0);

/* 使用ob捕获所有的输出。Use ob to get output. */
ob_start();

/* 获取当前文件目录地址*/
$filePath = dirname(__FILE__);

/* 加载框架核心文件。Load the framework. */
include "$filePath/framework/router.class.php";
include "$filePath/framework/control.class.php";
include "$filePath/framework/model.class.php";
include "$filePath/framework/helper.class.php";

/* 响应请求。Response the request. */
$app    = router::createApp('demo', $filePath, 'router');     // 实例化router。  Instance the router class.
$common = $app->loadCommon();                                         // 加载common模块。Load the common module.
$app->parseRequest($argv);                                            // 解析请求。      Parse the request.
$app->loadModule();                                                   // 加载模块。      Load module.
echo helper::removeUTF8Bom(ob_get_clean());                           // 输出内容。      Print the output.
