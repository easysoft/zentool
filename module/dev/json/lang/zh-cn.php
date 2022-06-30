<?php
$lang->json = new stdClass();
$lang->json->help = new stdClass();
$lang->json->help->json = <<<EOF
解码JSON字符串或给定文件。

用法
  z json <command> [options]

命令
  decode  -d  <string | filepath>     采用JSON编码的字符串并将其转换为PHP变量。
EOF;
$lang->json->help->decode = <<<EOF
用法
  z json decode <string | filepath>

选项
  -a,--associative     使用该选项，JSON对象将作为关联数组返回；否则，JSON对象将作为对象返回。

例如
  z json decode /z/example.json
  z json decode '{\"name\":\"ZenTools\",\"command\":\"z\"}'
EOF;
