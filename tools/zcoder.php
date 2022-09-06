<?php
$pmsRoot = '/home/zhouxin/sites/zentaopms/';
$extRoot    = '';

$controlTemplate = <<<EOT
<?php
helper::importControl('%s');
class %s extends %s
{
%s
}
EOT;
$modelTemplate = <<<EOT
<?php
class %s extends %s
{
%s
}
EOT;

class control{}

/**
 * Create a file with content.
 *
 * @param  string    $file
 * @param  string    $content
 * @return int|false
 */
function create($file, $content)
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
 * @param  int    $module
 * @param  int    $type
 * @param  int    $functionList
 * @param  int    $extClass
 * @access public
 * @return mixed
 */
function init($module, $type, $functionList, $extClass)
{
    global $pmsRoot, $extRoot, $controlTemplate, $modelTemplate;

    if(empty($pmsRoot)) echo 'Please configure zentaopms root first';
    if(empty($extRoot)) $extRoot = __DIR__;

    if($type == 'control')
    {
        $rawFile = $pmsRoot . 'module/' . $module . '/control.php';
        $functionList = explode(",", $functionList);
        $functions = new stdclass;
        foreach($functionList as $function)
        {
            $functions->$function = getFuncPosition($rawFile, $module, $function);
        }

        foreach($functions as $functionName => $function)
        {
            $functionCode = `sed -n "{$function->startLine},{$function->endLine}p" $rawFile`;
            $extClass     = 'my' . ucfirst($module);
            $extCode      = sprintf($controlTemplate, $module, $extClass, $module, $functionCode);
            $extFile      = $extRoot . "control/" . $functionName . ".php";
            create($extFile, $extCode);
        }

    }

    if($type == 'model')
    {
        $rawFile = $pmsRoot . 'module/' . $module . '/model.php';
        $functionList = explode(",", $functionList);

        $functionCode = '';
        foreach($functionList as $function)
        {
            $position = getFuncPosition($rawFile, $module . 'Model', $function);
            $functionCode .= `sed -n "{$position->startLine},{$position->endLine}p" $rawFile`;
        }

        $extendModel  = $module . 'Model';
        $extCode      = sprintf($modelTemplate, $extClass, $extendModel, $functionCode);
        $extFile      = $extRoot . "model/model.php";
        create($extFile, $extCode);
    }
}

$module        = $argv[1];
$type          = $argv[2];
$functionList  = $argv[3];
if($type == 'model') $extClass = $argv[4];
$extClass = ($type == 'model' and !empty($argv[4])) ? $argc[4] : '';

init($module, $type, $functionList, $extClass);
