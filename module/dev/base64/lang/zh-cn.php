<?php
$lang->base64 = new stdClass();
$lang->base64->help = new stdClass();
$lang->base64->help->base64 = <<<EOF
使用base64编码给定的字符串或解码base64编码的字符串。

用法
  z base64 <命令> [参数]

命令
  encode  -e  <字符串>      查看使用base64对给定字符串进行编码的结果。
  decode  -d  <字符串>      查看解码base64编码字符串的结果。
EOF;
$lang->base64->help->encode = <<<EOF
用法
  z base64 encode <字符串>

例如
  z base64 encode zentools
EOF;
$lang->base64->help->decode = <<<EOF
用法
  z base64  decode <字符串>

例如：
  z base64 decode zentools
EOF;
$lang->base64->error = '该字符串 %s 不是一个base64字符串！';
