<?php
/**
 * The model file of patch module of Z.
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
class patchModel extends model
{
    /**
     * Check version.
     *
     * @param  array  $versions
     * @access public
     * @return bool
     */
    public function checkVersion($versions)
    {
        $versionList = explode(',', $versions);
        foreach($versionList as $version)
        {
            if(!preg_match('/^(max|biz|pro|lite|litevip){0,1}\d+\.\d+(\.\d+)?(\.(((beta|alpha|stable)+\d?)|(rc\d{1})))?$/', trim($version))) return false;
        }
        return true;
    }

    /**
     * Check patch name.
     *
     * @param  string $patchName
     * @access public
     * @return int
     */
    public function checkPatchName($patchName)
    {
        return preg_match('/^zentao\.[\d\.a-z]+\.(bug|story)\.[\d]+\.zip$/', $patchName);
    }

    /**
     * Check whether the patch package exists.
     *
     * @param  string $patchName
     * @access public
     * @return bool
     */
    public function checkExist($patchName)
    {
        $patchList = $this->getPatchList();

        foreach($patchList as $patch)
        {
            if($patch['name'] == $patchName) return true;
        }
        return false;
    }

    /**
     * Get patch List.
     *
     * @param  array  $params
     * @access public
     * @return array
     */
    public function getPatchList($params = array())
    {
        $patchList = array();
        for($i = 1; $i < 10; $i++)
        {
            $patch = array();
            $patch['type']      = 'bug';
            $patch['code']      = 'patch00' . $i;
            $patch['name']      = 'zentao.16.5.beta.story.' . $i . '.zip';
            $patch['desc']      = '描述：' . $patch['name'];
            $patch['changelog'] = 'changelog: ' . $patch['name'];
            $patch['date']      = '2022-01-0' . $i;
            $patch['installed'] = 'no';
            $patchList[] = $patch;
        }

        if(isset($params['showAll']))
        {
            $patchList[] = array(
                'type'      => 'story',
                'code'      => 'story',
                'name'      => '这个是需求标题',
                'desc'      => '描述：文档设计接口',
                'changelog' => 'changelog：变更需求',
                'date'      => '2022-05-01',
                'installed' => 'yes'
            );
        }
        elseif(isset($params['local']))
        {
            $patchList   = array();
            $patchList[] = array(
                'type'      => 'story',
                'code'      => 'story',
                'name'      => '这个是需求标题',
                'desc'      => '描述：文档设计接口',
                'changelog' => 'changelog：变更需求',
                'date'      => '2022-05-01',
                'installed' => 'yes'
            );
        }

        return $patchList;
    }

    /**
     * Release patch.
     *
     * @param  string $patchPath
     * @param  object $releaseInfo
     * @access public
     * @return void
     */
    public function release($patchPath, $releaseInfo)
    {
        return true;
    }
}
