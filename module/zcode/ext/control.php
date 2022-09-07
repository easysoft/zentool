<?php
/**
 * The control file of ext module of chandao.net.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      dingguodong <dingguodong@easycorp.ltd>
 * @package     ext
 * @version     $Id$
 * @link        https://www.chandao.net
 */
class ext extends control
{
    public function init($params)
    {
        $module        = $params[2];
        $type          = $params[3];
        $functionList  = $params[4];
        if($type == 'model') $extName = $params[5];

        $this->moduleRoot = $this->config->runDir . DS . $this->moduleName . DS;

        mkdir($this->config->extRoot . $module . '/ext', 0777, true);

        $this->config->debug = 2;

        if($type == 'control')
        {
            $rawFile = $this->config->ext->sourceRoot . 'module' . DS . $module . DS . 'control.php';
            $functionList = explode(",", $functionList);
            $functions = new stdclass;
            foreach($functionList as $function)
            {
                $functions->$function = $this->zcode->getFuncPosition($rawFile, $module, $function);
            }

            foreach($functions as $functionName => $function)
            {
                $functionCode = `sed -n "{$function->startLine},{$function->endLine}p" $rawFile`;
                $extClass     = 'my' . ucfirst($function);
                $extCode      = sprintf($this->config->ext->template->control, $module, $extClass, $module, $functionCode);
                $extFile      = $this->config->ext->extRoot . "control/" . $functionName . ".php";
                $this->zcode->create($extFile, $extCode);
            }

        }

        if($type == 'model')
        {
            $functionCode = '';
            $rawFile = $this->config->ext->sourceRoot . 'module' . DS . $module . DS . 'model.php';

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

            $extCode      = sprintf($this->config->ext->template->model, $extName, $module . 'Model', $functionCode);
            $extClassFile = $this->config->ext->extRoot . "$module/ext/model/class/$extName.class.php";
            createFile($extClassFile, $extCode);

            $extModelFile = $this->config->ext->extRoot . "$module/ext/model/$extName.php";
            createFile($extModelFile, $modelCodes);
        }
    }
}

