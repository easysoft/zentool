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
        return preg_match('/^zentao\.(bug|story)\.[\d]+\.zip$/', $patchName);
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
        $patch = $this->getPatchView(substr($patchName, 0, -4), 'code');
        if(isset($patch->data->id)) return true;

        return false;
    }

    /**
     * Get zentao version.
     *
     * @access public
     * @return string
     */
    public function getZtVersion()
    {
        $versionFile = $this->config->zt_webDir . DS . 'VERSION';
        $version = file_get_contents($versionFile);
        return trim($version);
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
        $version = $this->getZtVersion();
        $url     = $this->config->patch->webStoreUrl . 'extension-apiBrowseRelease-' . $version . '.json';
        $patchs  = $this->http($url);
        if(!isset($patchs->result) or $patchs->result == 'fail') return array();

        $patchList = array();
        $patchIDs  = array();
        foreach($patchs->list as $one)
        {
            $patch = array();
            $patch['id']        = $one->id;
            $patch['code']      = $one->code;
            $patch['type']      = strpos($one->code, 'story') ? 'story' : 'bug';
            $patch['name']      = $one->name;
            $patch['date']      = substr($one->updatedTime, 0, 10);
            $patch['installed'] = 'No';
            $patchList[] = $patch;

            $patchIDs[] = $one->id;
        }

        if(isset($params['showAll']) or isset($params['local']))
        {
            $patchPath = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch';

            $zfile = $this->app->loadClass('zfile');
            $list = $zfile->readDir($patchPath);
            foreach($list as $path)
            {
                if(strpos($path, 'install.lock'))
                {
                    $dirName = mb_substr(dirname($path), strlen($patchPath) + 1);
                    $key     = array_search($dirName, $patchIDs);
                    if($key !== false)
                    {
                        $patchList[$key]['installed'] = 'Yes';
                        continue;
                    }

                    $patch = array();
                    $patch['id']        = '';
                    $patch['code']      = $dirName;
                    $patch['type']      = 'bug';
                    $patch['name']      = $dirName;
                    $patch['date']      = date('Y-m-d', filemtime($path));
                    $patch['installed'] = 'Yes';
                    $patchList[] = $patch;
                }
            }
        }

        if(isset($params['local']))
        {
            foreach($patchList as $key => $patch)
            {
                if($patch['installed'] == 'No') unset($patchList[$key]);
            }
        }

        return $patchList;
    }

    /**
     * Release patch.
     *
     * @param  string $patchPath
     * @param  string $packageName
     * @access public
     * @return bool
     */
    public function release($patchPath, $packageName = '')
    {
        $releaseInfo = array();
        $releaseInfo['name']     = $packageName;
        $releaseInfo['size']     = filesize($patchPath);
        $releaseInfo['uuid']     = '';
        $releaseInfo['account']  = $this->config->cz_account;
        $releaseInfo['password'] = $this->config->ca_password;
        $releaseInfo['file']     = new \CURLFile($patchPath);
        $response = $this->http($this->config->patch->webStoreUrl . 'release-apiCreateRelease.json', $releaseInfo, array(), array(), 'data-form');
        return $response;
    }

    /**
     * Check user input.
     *
     * @param  string $field
     * @param  string $value
     * @param  object $obj
     * @access public
     * @return bool
     */
    public function checkInput($field = '', $value = '', $obj = null)
    {
        if(empty($value)) return false;

        if(method_exists($this, 'check' . $field)) return $this->{'check' . $field}($value, $obj);

        if($field == 'type' and in_array($value, array('bug', 'story'))) return true;

        return true;
    }

    /**
     * Check ID.
     *
     * @param  int    $id
     * @param  object $object
     * @access public
     * @return bool|string
     */
    public function checkID($id, $object)
    {
        if((int)$id)
        {
            $patchName = sprintf($this->config->patch->nameTpl, $object->type, (int)$id);

            $patch = $this->getPatchView(substr($patchName, 0, -4), 'code');
            if(isset($patch->data->id)) return 'exists';

            return $patchName;
        }

        return false;
    }

    /**
     * Check build path.
     *
     * @param  string $path
     * @access public
     * @return string
     */
    public function checkBuildPath($path)
    {
        if(!empty($path) and !file_exists($path)) $path = realpath($this->config->runDir . DS .$path);

        if(!empty($path) and file_exists($path) and @opendir($path)) return $path;

        return '';
    }

    /**
     * Get patch view.
     *
     * @param  int|string $patchID
     * @param  string     $type
     * @access public
     * @return object
     */
    public function getPatchView($patchID = 0, $type = 'id')
    {
        $version = $this->getZtVersion();
        $url     = $this->config->patch->webStoreUrl . 'extension-apiViewRelease-' . $version . '-' . $patchID . '-' . $type . '.json';
        return $this->http($url);
    }

    public function checkUser($account, $password)
    {
        $url  = $this->config->patch->webStoreUrl . 'user-loginByZ.json';
        $user = array(
            'account'  => $account,
            'password' => $password
        );
        return $this->http($url, $user);
    }
}
