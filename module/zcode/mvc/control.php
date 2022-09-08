<?php
class mvc extends control
{
    public function resources($module, $resourceFile = '')
    {
        $controlFile  = $this->config->runDir . DS . $module . DS . 'control.php';
        $className = $module;
        $methods = $this->zcode->getMethodsByClass($controlFile, $className);
        $resources = "\$lang->resource->$module = new stdclass();\n";
        foreach($methods as $method)
        {
            $resources = "\$lang->resource->$module->$method = '$method';\n";
        }

        file_put_contents($resourceFile, $resources, FILE_APPEND);
        return $resources;
    }
}
