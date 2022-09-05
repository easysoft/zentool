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
        $this->config->debug = 2;
        $this->config->sourceRoot = "/home/z/repo/zentaopms/";
        $module        = $params['moduleName'];
        $type          = $params['type'];
        $functionList  = $params['functionList'];
        $this->extRoot = $this->config->runDir . DS . $this->moduleName . DS . "ext" . DS;
        if($type == 'control')
        {
            $rawFile = $this->config->sourceRoot . 'module' . DS . $module . DS . 'control.php';
            $functionList = explode(",", $functionList);
            $functions = new stdclass;
            foreach($functionList as $function)
            {
                $functions->$function = $this->zcode->getFuncPosition($rawFile, $module, $function);
            }

            foreach($functions as $function)
            {
                $functionCode = `sed -n "{$function->startLine},{$function->endLine}p"`;
                $extClass     = 'my' . ucfirst($function);
                $extCode      = sprintf($this->config->ext->template->control, $module, $extClass, $module, $functionCode);
                $extFile      = $this->extRoot . "control/" . $function . ".php";
                $this->zcode->create($extFile, $extCode);
            }

        }

        if($type == 'model')
        {
            $rawFile = $this->config->sourceRoot . 'module' . DS . $module . DS . 'model.php';
            $functionList = explode(",", $functionList);
            $functions = new stdclass;
            foreach($functionList as $function)
            {
                $functions->$function = $this->zcode->getFuncPosition($rawFile, $module . 'Model', $function);
            }
        }

    }
}

