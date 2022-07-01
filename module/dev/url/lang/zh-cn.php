<?php
$lang->url = new stdClass();
$lang->url->help = new stdClass();
$lang->url->help->url = <<<EOF
使用URL编码给定的字符串或解码URL编码的字符串。

用法
  z url <命令> [参数]

命令
  encode  -e  <字符串>      查看使用URL对给定字符串进行编码的结果。
  decode  -d  <字符串>      查看解码URL编码字符串的结果。
EOF;
$lang->url->help->encode = <<<EOF
用法
  z url encode <字符串>

例如
  z url encode 'my=project\&are=zentao\+xuanxuan'
EOF;
$lang->url->help->decode = <<<EOF
用法
  z url  decode <字符串>

例如：
  z url decode project
EOF;
