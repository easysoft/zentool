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
        $code  = str_replace('.', '_', substr($patchName, 0, -4));
        $patch = $this->getPatchView($code, 'code');
        if(isset($patch->data->id)) return true;

        return false;
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
        if($field == 'type' and !in_array($value, array('bug', 'story'))) return false;

        if(method_exists($this, 'check' . $field)) return $this->{'check' . $field}($value, $obj);
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
        $path = helper::getRealPath($path);
        if(!empty($path) and file_exists($path) and @opendir($path)) return $path;

        return '';
    }

    /**
     * Check user.
     *
     * @param  string $account
     * @param  string $password
     * @access public
     * @return object
     */
    public function checkUser($account, $password)
    {
        $url  = $this->config->patch->webStoreUrl . 'user-loginByZ.json';
        $user = array(
            'account'  => $account,
            'password' => $password
        );
        return $this->http($url, $user);
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
        $version     = file_get_contents($versionFile);
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
        if(isset($patchs->result) and $patchs->result == 'fail') return $patchs;

        $patchList = array();
        foreach($patchs->list as $patch)
        {
            $data = array();
            $data['id']        = $patch->id;
            $data['code']      = $patch->code;
            $data['type']      = strpos($patch->code, 'story') ? 'story' : 'bug';
            $data['name']      = $patch->name;
            $data['date']      = substr($patch->updatedTime, 0, 10);
            $data['installed'] = 'No';

            $patchList[$patch->id] = $data;
        }

        $patchPath = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch';
        $zfile     = $this->app->loadClass('zfile');
        $list      = $zfile->readDir($patchPath);
        foreach($list as $path)
        {
            if(strpos($path, 'install.lock'))
            {
                $dirName = mb_substr(dirname($path), strlen($patchPath) + 1);
                if(isset($patchList[$dirName]))
                {
                    $patchList[$dirName]['installed'] = 'Yes';
                    continue;
                }

                /* If $key equal false, the patch is installed locally. */
                $patch = array();
                $patch['id']        = $dirName;
                $patch['code']      = $dirName;
                $patch['type']      = 'bug';
                $patch['name']      = $dirName;
                $patch['date']      = date('Y-m-d', filemtime($path));
                $patch['installed'] = 'Yes';
                $patchList[] = $patch;
            }
        }

        if(isset($params['local']))
        {
            foreach($patchList as $key => $patch)
            {
                if($patch['installed'] == 'No') unset($patchList[$key]);
            }
        }
        elseif(!isset($params['showAll']))
        {
            foreach($patchList as $key => $patch)
            {
                if($patch['installed'] == 'Yes') unset($patchList[$key]);
            }
        }

        return $patchList;
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
        if($type == 'code') $patchID = str_replace('.', '_', $patchID);

        $url = $this->config->patch->webStoreUrl . 'extension-apiViewRelease-' . $version . '-' . $patchID . '-' . $type . '.json';
        return $this->http($url);
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
        $releaseInfo['account']  = $this->config->cz_account;
        $releaseInfo['password'] = $this->config->ca_password;
        $releaseInfo['file']     = new \CURLFile($patchPath);
        $response = $this->http($this->config->patch->webStoreUrl . 'release-apiCreateRelease.json', $releaseInfo, array(), array(), 'data-form');
        return $response;
    }

    /**
     * Record dynamic to Zentao
     *
     * @param  string $patchName
     * @param  string $type
     * @access public
     * @return string
     */
    public function recordDynamic($patchName = '', $type = 'install')
    {
        $cmd = sprintf($this->config->patch->ztcliTpl, $this->config->zt_webDir, $type, $patchName);
        if($this->config->os == 'windows') $cmd = $this->config->php_file . ' ' . $cmd;

        $res = json_decode(exec($cmd));
        return $res->result;
    }

    /**
     * Check zentao path.
     *
     * @access public
     * @return string
     */
    public function checkConfig($fields = '')
    {
        if(empty($fields)) return '';

        $checkFields = explode(',', $fields);
        foreach($checkFields as $field)
        {
            if(!isset($this->config->{$field}) or empty($this->config->{$field})) return $this->lang->patch->error->runSet;
            if($field == 'zt_webDir' and !is_writable($this->config->zt_webDir)) return sprintf($this->lang->patch->error->notWritable, $this->config->zt_webDir);
        }

        return '';
    }
}
