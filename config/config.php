<?php
/**
 * ZenTaoPHP的config文件。如果更改配置，不要直接修改该文件，复制到my.php修改相应的值。
 * The config file of zentaophp.  Don't modify this file directly, copy the item to my.php and change it.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/* 保证在命令行环境也能运行。Make sure to run in ztcli env. */
if(!class_exists('config')){class config{}}
if(!function_exists('getWebRoot')){function getWebRoot(){}}

/* 基本设置。Basic settings. */
$config->version    = '1.0';                // ZenTaoPHP的版本。 The version of ZenTaoPHP. Don't change it.
$config->charset    = 'UTF-8';              // ZenTaoPHP的编码。 The encoding of ZenTaoPHP.
$config->timezone   = 'Asia/Shanghai';      // 时区设置。        The time zone setting, for more see http://www.php.net/manual/en/timezones.php.
$config->webRoot    = '';                   // URL根目录。       The root path of the url.
$config->debug      = 2;

$config->apps['zentao'] = 'zentao';
$config->apps['dev']    = 'dev';
$config->apps['zcode']  = 'zcode';

/* 命令配置。 Command settings. */
$config->command = new stdclass();
$config->command->zentao = new stdclass();
$config->command->zentao->patch  = array('list', 'view', 'install', 'revert', 'build', 'release', 'help');
$config->command->zentao->devops = array('mr', 'help');
$config->command->zentao->set    = array();

$config->command->dev = new stdclass();
$config->command->dev->url    = array('encode', 'decode', 'help');
$config->command->dev->base64 = array('encode', 'decode', 'help');
$config->command->dev->md5    = array('calculate', 'help');
$config->command->dev->json   = array('decode', 'help');

$config->abbreviations = new stdClass();

/* 支持的主题和语言。Supported thems and languages. */
$config->themes['default'] = 'default';
$config->langs['zh-cn']    = '简体';
$config->langs['en']       = 'English';

/* 默认值设置。Default settings. */
$config->default = new stdclass();
$config->default->lang   = 'en';          //默认语言。 Default language.
$config->default->module = 'entry';       //默认模块。 Default module.
$config->default->method = 'entry';       //默认方法。 Default method.

/* 系统框架配置。Framework settings. */
$config->framework = new stdclass();
$config->framework->multiLanguage  = true; // 是否启用多语言功能。              Whether enable multi lanuage or not.
$config->framework->extensionLevel = 0;     // 0=>无扩展,1=>公共扩展,2=>站点扩展 0=>no extension, 1=> common extension, 2=> every site has it's extension.
$config->framework->filterBadKeys  = true;  // 是否过滤不合要求的键值。          Whether filter bad keys or not.
$config->framework->filterTrojan   = true;  // 是否过滤木马攻击代码。            Whether strip trojan code or not.
$config->framework->filterXSS      = true;  // 是否过滤XSS攻击代码。             Whether strip xss code or not.
$config->framework->filterParam    = 2;     // 1=>默认过滤，2=>开启过滤参数功能。0=>default filter 2=>Whether strip param.
$config->framework->logDays        = 14;    // 日志文件保存的天数。              The days to save log files.

$config->framework->detectDevice['zh-cn'] = false; // 在zh-cn语言情况下，是否启用设备检测功能。 Whether enable device detect or not.
$config->framework->detectDevice['zh-tw'] = false; // 在zh-tw语言情况下，是否启用设备检测功能。 Whether enable device detect or not.
$config->framework->detectDevice['en']    = false; // 在en语言情况下，是否启用设备检测功能。    Whether enable device detect or not.

/* 配置参数过滤。Filter param settings. */
$filterConfig = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'filter.php';
if(file_exists($filterConfig)) include $filterConfig;
