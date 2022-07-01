<?php
/**
 * The model file of set module of Z.
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
class setModel extends model
{
    /**
     * Check user input.
     *
     * @param  string $field
     * @param  string $value
     * @access public
     * @return bool
     */
    public function checkInput($field = '', $value = '')
    {
        if(empty($value)) return false;

        if(method_exists($this, 'check' . $field)) return $this->{'check' . $field}($value);
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
        $configPath = $path . DS . 'config' . DS . 'my.php';
        $realPath   = helper::getRealPath($configPath);

        if($realPath) return dirname(dirname($realPath));
        return '';
    }

    /**
     * Check php path.
     *
     * @param  string $path
     * @access public
     * @return string
     */
    public function checkPHPfile($path = '')
    {
        if(!is_file($path)) $path = realpath($this->config->runDir . DS . $path);

        if($path and is_file($path)) return $path;
        return '';
    }
}
