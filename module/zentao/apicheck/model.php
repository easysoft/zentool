<?php
/**
 * The model file of apicheck module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     patch
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class apicheckModel extends model
{
    public $ztPath;

    /**
     * Check user input.
     *
     * @param  string $path
     * @access public
     * @return bool
     */
    public function checkInput($path = '')
    {
        if(empty($path)) return false;

        $path = $this->checkWebDir($path);
        if(!$path)  return false;

        $this->ztPath = $path;
        $openRes = $this->checkOpen();
        return true;
    }

    /**
     * Check zentao path.
     *
     * @param  string $path
     * @access public
     * @return string
     */
    public function checkWebDir($path = '')
    {
        $configPath = $path . DS . 'config' . DS . 'config.php';
        $realPath   = helper::getRealPath($configPath);

        if($realPath) return dirname(dirname($realPath));
        return '';
    }

    public function checkOpen()
    {
        $zfile    = $this->app->loadClass('zfile');
        $apiFiles = $zfile->readDir($this->ztPath . DS . 'api' . DS . 'v1' . DS . 'entries' . DS);
        foreach ($apiFiles as $key => $filePath) {
            $fileContent = file($filePath);
            $controls    = array();
            foreach ($fileContent as $line => $code) {
                preg_match('/\$control\s=\s\$this->loadController\([\'"]([a-z]+)[\'"],\s[\'"]([a-zA-Z0-9]+)[\'"]\);/', $code, $controlNames);
                if(!empty($controlNames))
                {
                    $controls[] = $controlNames[1];
                    continue;
                }
                if(!preg_match('/\$control->([a-z0-9]+)\((\$[a-z0-9]+,\s|\$this->param\([\'\"][a-z0-9]+[\'\"],\s*[\'\"]?[a-z0-9-_\s]*[\'\"]?\)[,]?\s*|[\'\"]?[0-9a-z]+[\'\"]?,\s)+\)/i', $code)) continue;

                $res = preg_match_all('/(\$[a-z0-9]+[,\)])|([0-9]+[,\)])|((?<!param\()[\'\"][a-z0-9-_]*[\'\"][,\)])|((?<!this)\-\>[a-z0-9]+\()/i', $code, $execControls, PREG_PATTERN_ORDER);
                if(!empty($execControls[0]))
                {
                    $params = $execControls[0];
                    $pramsLen = count($params) - 1;
                    $methodName = trim(trim($params[0], '->'), '(');

                    $module = $controls[count($controls) - 1];
                    if(!$this->checkParamLen($module, $methodName, $pramsLen))
                    {
                        $line++;
                        helper::output("文件 $filePath 第 {$line} 行 $module -> $methodName 参数数量不正确！", 'err');
                    }
                }
            }
        }
    }

    /**
     * Check method params length.
     *
     * @param  string $module
     * @param  string $method
     * @param  int    $length
     * @access public
     * @return void
     */
    public function checkParamLen($module, $method, $length)
    {
        $controlFile     = $this->ztPath . DS . 'module' . DS . $module . DS . 'control.php';
        $controlExtFile  = $this->ztPath . DS . 'extension/max/' . $module . '/control.php';
        $controlFuncFile = $this->ztPath . DS . 'extension/max/' . $module . '/ext/control/' . $method . '.php';

        $realFile       = file_exists($controlFuncFile) ? $controlFuncFile : (file_exists($controlExtFile) ? $controlExtFile : $controlFile);
        $controlContent = file_get_contents($realFile);
        preg_match('/public\sfunction\s' . $method . '\((\$[a-z0-9]+(\s=\s[\'\"]?[a-z0-9-_,]*[\'\"]?)?,?\s?)*\)/i', $controlContent, $matches);
        if(empty($matches)) return false;

        $paramLen = substr_count($matches[0], '$');
        if($paramLen != $length)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
