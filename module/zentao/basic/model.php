<?php
/**
 * The model file of basic module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     basic
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class basicModel extends model
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

        if(in_array($field, array('source', 'target'))) return $this->checkPath($value);
        if(method_exists($this, 'check' . $field)) return $this->{'check' . $field}($value);
        return true;
    }

    /**
     * Check path.
     *
     * @param  string $path
     * @access public
     * @return string
     */
    public function checkPath($path = '')
    {
        $realPath = helper::getRealPath($path);

        if($realPath) return $realPath;
        return '';
    }

    /**
     * Get files.
     *
     * @param  string $path
     * @access public
     * @return array
     */
    public function getFiles($path = '')
    {
        $zfile = $this->app->loadClass('zfile');
        return $zfile->readDir($path);
    }

    /**
     * Backup and cover dir.
     *
     * @param  string $oldPath
     * @param  string $newPath
     * @access public
     * @return void
     */
    public function backupAndCover($oldPath = '', $newPath = '')
    {
        $oldPath  = helper::getRealPath($oldPath);
        $newPath  = helper::getRealPath($newPath);
        $pathName = explode('/', $oldPath);
        $backPath = dirname($this->config->userConfigFile, 2) . DS . 'backcode' . DS . $pathName[count($pathName) - 1] . '.zip';

        $zfile    = $this->app->loadClass('zfile');
        if(!file_exists($backPath)) $zfile->mkdir(dirname($backPath));

        /* Backup. */
        $this->app->loadClass('pclzip', true);
        $zip = new pclzip($backPath);
        if($zip->create($oldPath, PCLZIP_OPT_REMOVE_PATH, dirname($oldPath)) === 0) return false;

        $copyDir = '/tmp/' . $pathName[count($pathName) - 1];
        $zfile->copyDir($oldPath, $copyDir);
        $zfile->removeDir($oldPath);

        $zfile->copyDir($newPath, $oldPath);
        return $copyDir;
    }
}
