<?php
/**
 * The model file of patch module of Z.
 *
 * @copyright   Copyright 2009-2022222222222222222222222222222222222222222222 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     patch
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class patchModel extends model
{
    public function checkVersion($versions)
    {
        $versionList = explode(',', $versions);
        foreach($versionList as $version)
        {
            if(!preg_match('/^(max|biz){0,1}\d+\.\d+(\.\w+\d+)*$/', $version)) return false;
        }
        return true;
    }
}
