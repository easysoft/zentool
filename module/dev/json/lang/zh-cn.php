<?php
$lang->json = new stdClass();
$lang->json->help = new stdClass();
$lang->json->help->json = <<<EOF
解码JSON字符串或给定文件。

用法
  z json <命令> [参数]

命令
  decode  -d  <本地文件>     采用JSON编码的并将其转换为PHP变量。
EOF;
$lang->json->help->decode = <<<EOF
用法
  z json decode <本地文件>

选项
  -a, --associative     使用该选项，JSON对象将作为关联数组返回；否则，JSON对象将作为对象返回。

例如
  z json decode /z/example.json
EOF;
$lang->json->notJson = '该内容不是一个json文件！';
