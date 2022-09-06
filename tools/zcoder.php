#!/usr/bin/env php
<?php
if(count($argv) < 3) die('Usage: ' . __FILE__ . " control|model bug create,delete [extensionName]\n");

define('DS', DIRECTORY_SEPARATOR);

$config = new stdclass;
$config->pmsRoot = '/home/z/sites/zentao/dev/';
$config->extRoot = __DIR__ . DS;

$config->controlTemplate = <<<EOT
<?php
helper::importControl('%s');
class %s extends %s
{
%s
}
EOT;
$config->modelTemplate = <<<EOT
<?php
class %s extends %s
{
%s
}
EOT;

class control{}
class model{}

/**
 * Create a file with content.
 *
 * @param  string    $file
 * @param  string    $content
 * @return int|false
 */
function createFile($file, $content)
{
    if(!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
    return file_put_contents($file, $content);
}

/**
 * Get function's first line and last line.
 *
 * @param  string  $file
 * @param  string  $class
 * @param  string  $function
 * @return object
 */
function getFuncPosition($file, $class, $function)
{
    if(!class_exists($class)) include $file;

    $reflection = new ReflectionMethod("$class::$function");

    $position = new stdclass;
    $position->startLine = $reflection->getStartLine();
    $position->endLine   = $reflection->getEndLine();
    return $position;
}

/**
 * Init.
 *
 * @param  string   $module
 * @param  string   $type
 * @param  string   $functionList
 * @param  string   $extClass
 * @access public
 * @return mixed
 */
function init()
{
    global $config;
    if(!is_dir(realpath($config->pmsRoot))) die("Please configure zentaopms root first\n");

    $type         = $GLOBALS['argv'][1];
    $module       = $GLOBALS['argv'][2];
    $functionList = $GLOBALS['argv'][3];

    if($type == 'model') $extName = $GLOBALS['argv'][4];

    if($type == 'control') return initControl($module, $functionList);
    if($type == 'model') return initModel($module, $functionList, $extName);
}

function initControl($module, $functionList)
{
    global $config;

    $functions = new stdclass;
    $rawFile   = $config->pmsRoot . 'module/' . $module . '/control.php';

    $functionList = explode(",", $functionList);
    foreach($functionList as $function)
    {
        $functions->$function = getFuncPosition($rawFile, $module, $function);
    }

    foreach($functions as $functionName => $function)
    {
        $functionCode = `sed -n "{$function->startLine},{$function->endLine}p" $rawFile`;
        $extClass     = 'my' . ucfirst($module);
        $extCode      = sprintf($config->controlTemplate, $module, $extClass, $module, $functionCode);
        $extFile      = $config->extRoot . "$module/ext/control/" . $functionName . ".php";

        createFile($extFile, $extCode);
    }
}

function initModel($module, $functionList, $extName)
{
    global $config;

    $functionCode = '';
    $rawFile      = $config->pmsRoot . 'module/' . $module . '/model.php';

    $functionList = explode(",", $functionList);
    $modelFunctions = new stdClass();
    $modelCodes = '<?php' . "\n";
    foreach($functionList as $function)
    {
        $position = getFuncPosition($rawFile, $module . 'Model', $function);
        $functionCode .= `sed -n "{$position->startLine},{$position->endLine}p" $rawFile`;

        $modelFunctions->function = trim(`sed -n "{$position->startLine}p" $rawFile`);
        $modelCodes .= $modelFunctions->function;
        $modelCodes .= "\n{\n";
        $modelCodes .= "    return \$this->loadExtension('" . $extName . "')->" . preg_replace("/^.*function\s+$function/i", "$function", $modelFunctions->function) . ";\n";
        $modelCodes .= "}\n\n";
    }

    $extCode = sprintf($config->modelTemplate, $extName, $module . 'Model', $functionCode);
    $extClassFile = $config->extRoot . "$module/ext/model/class/$extName.class.php";
    createFile($extClassFile, $extCode);

    $extModelFile = $config->extRoot . "$module/ext/model/$extName.php";
    createFile($extModelFile, $modelCodes);

}

init();
