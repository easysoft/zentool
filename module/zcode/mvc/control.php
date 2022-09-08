<?php
class mvc extends control
{
    public function resources($module, $resourceFile = '')
    {
        $controlFile  = $this->config->pmsRoot . 'module' . DS . $module . DS . 'control.php';
        if(empty($resourceFile)) $resourceFile = $this->config->pmsRoot . 'module/group/lang/resource.php';

        $className = $module;
        $methods = $this->zcode->getMethodsByClass($controlFile, $className);
        $resources = "\$lang->resource->$module = new stdclass();\n";
        foreach($methods as $method)
        {
            if($method == '__construct') continue;
            $resources .= "\$lang->resource->$module->$method = '$method';\n";
        }

        file_put_contents($resourceFile, $resources, FILE_APPEND);
        return $resources;
    }
}
