<?php
/**
 * The control file of index module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class patch extends control
{
    /**
     * The index page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function entry($params)
    {
        if(empty($params)) return $this->printHelp();

        foreach($params as $key => $param)
        {
            if(method_exists($this, $key))
            {
                if(isset($this->config->patch->paramKey[$key])) $params = array($this->config->patch->paramKey[$key] => $param);
                return $this->$key($params);
            }
            return $this->printHelp();
        }
    }

    public function printHelp($type = 'patch')
    {
        return $this->output($this->lang->patch->help->$type);
    }

    /**
     * The list page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function list($params)
    {
        global $argc;
        if(isset($params['help']) or ($argc > 3 && empty($params))) return $this->printHelp('list');
        if(!isset($this->config->zt_webDir) or empty($this->config->zt_webDir)) return $this->output($this->lang->patch->error->runSet, 'err');

        $patchList = $this->patch->getPatchList($params);

        return $this->printList($patchList, array('id', 'type', 'code', 'name', 'date', 'installed'), $this->lang->patch);
    }

    /**
     * The view page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function view($params)
    {
        if(empty($params) or !isset($params['patchID']) or empty($params['patchID']) or isset($params['help'])) return $this->printHelp('view');
        if(!isset($this->config->zt_webDir) or empty($this->config->zt_webDir)) return $this->output($this->lang->patch->error->runSet, 'err');

        $patchID = $params['patchID'];

        $patch = $this->patch->getPatchView($patchID);
        if(!isset($patch->result) or $patch->result == 'fail') return $this->output(isset($patch->message) ? $patch->message . PHP_EOL : 'error', 'err');

        if(!isset($patch->data->id)) return $this->output($this->lang->patch->error->notFound, 'err');

        $name  = $patch->data->extensionName;
        $desc  = $patch->data->extensionDesc;
        $logs  = $patch->data->changelog;
        $date  = substr($patch->data->addedTime, 0, 10);

        return $this->output(sprintf($this->lang->patch->viewPage, $patchID, $name, $desc, $date, $logs));
    }

    /**
     * Install patch.
     *
     * @param  array  $params
     * @access public
     * @return void
     */
    public function install($params)
    {
        if(empty($params) or !isset($params['patchID']) or empty($params['patchID']) or isset($params['help'])) return $this->printHelp('install');
        if(!isset($this->config->zt_webDir) or empty($this->config->zt_webDir)) return $this->output($this->lang->patch->error->runSet, 'err');
        if(!is_writable($this->config->zt_webDir)) return $this->output(sprintf($this->lang->patch->error->notWritable, $this->config->zt_webDir), 'err');

        $fileName = '';
        /* Check whether the parameter is an ID or a path. */
        if(strpos($params['patchID'], '.zip') !== false)
        {
            $patchPath = $this->getRealPath($params['patchID']);
            if(!$patchPath) return $this->output(sprintf($this->lang->patch->error->invalidName, $params['patchID']), 'err');

            /* Verification name format. */
            $pathList    = explode(DS, $patchPath);
            $packageKey  = count($pathList) - 1;
            $packageName = $pathList[$packageKey];
            $fileName    = $packageName;
            if(!$this->patch->checkPatchName($packageName)) return $this->output(sprintf($this->lang->patch->error->invalidName, $params['patchID']), 'err');

            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $packageName . DS;
        }
        else
        {
            $this->checkCZUser();
            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $params['patchID'] . DS;
        }

        /* Check whether installed. */
        if(file_exists($saveDir . 'install.lock')) return $this->output($this->lang->patch->error->installed, 'err');

        if(!file_exists($saveDir) && !mkdir($saveDir, 0777, true)) return $this->output(sprintf($this->lang->patch->error->notWritable, $saveDir), 'err');

        $backupPath = $saveDir . 'backup.zip';
        if(!isset($patchPath))
        {
            $patch = $this->patch->getPatchView((int)$params['patchID']);
            if(!isset($patch->data->id)) return $this->output($this->lang->patch->error->invalid, 'err');

            $fileName  = $patch->data->fileName;
            $patchPath = $saveDir . 'patch.zip';

            $this->output($this->lang->patch->downloading);

            $token = base64_encode($this->config->cz_account . ':' . $this->config->cz_password);
            $url = $this->config->patch->webStoreUrl . '/extension-apidownloadRelease-' . $patch->data->id . '-' . $token;

            if(!@copy($url, $patchPath)) return $this->output($this->lang->patch->error->notFound, 'err');
            $this->output($this->lang->patch->down);
        }
        else
        {
            @copy($patchPath, $saveDir . 'patch.zip');
        }

        $this->app->loadClass('pclzip', true);
        $zip   = new pclzip($patchPath);
        $files = $zip->listContent();
        if($files === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        $this->output($this->lang->patch->backuping);
        $fileNames = array();
        foreach($files as $file)
        {
            $name = $file['filename'];
            if($fileName)
            {
                $nameLen = mb_strlen($fileName) + 1;
                if(mb_substr($name, 0, $nameLen) == $fileName . DS) $name = mb_substr($name, $nameLen);
                if(mb_substr($name, 0, 10) == 'zentaopms' . DS)     $name = mb_substr($name, 10);
            }

            if($name) $fileNames[] = $this->config->zt_webDir . DS . $name;
        }

        $zip->changeFile($backupPath);
        if($zip->create($fileNames, PCLZIP_OPT_REMOVE_PATH, $this->config->zt_webDir) === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');
        $this->output($this->lang->patch->down);

        $this->output($this->lang->patch->installing);
        $zip->changeFile($patchPath);
        if($zip->extract(PCLZIP_OPT_PATH, $this->config->zt_webDir, PCLZIP_OPT_REMOVE_PATH, $fileName) === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');
        @touch($saveDir . 'install.lock');
        @file_put_contents($saveDir . 'patchName', $fileName);
        $this->output($this->lang->patch->installDone);
    }

    /**
     * Revert patch.
     *
     * @param  array  $params
     * @access public
     * @return void
     */
    public function revert($params)
    {
        if(empty($params) or !isset($params['patchID']) or empty($params['patchID']) or isset($params['help'])) return $this->printHelp('revert');

        if(!isset($this->config->zt_webDir) or empty($this->config->zt_webDir)) return $this->output($this->lang->patch->error->runSet, 'err');

        /* Check whether the parameter is an ID or a path. */
        if(strpos($params['patchID'], '.zip') !== false)
        {
            /* Verification name format. */
            $pathList    = explode(DS, $params['patchID']);
            $packageKey  = count($pathList) - 1;
            $packageName = $pathList[$packageKey];

            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $packageName . DS;
        }
        else
        {
            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $params['patchID'] . DS;
        }

        if(!file_exists($saveDir . 'install.lock')) return $this->output($this->lang->patch->error->notInstall, 'err');
        if(!is_writable($saveDir)) return $this->output(sprintf($this->lang->patch->error->notWritable, $saveDir), 'err');

        $this->app->loadClass('pclzip', true);
        if(file_exists($saveDir . 'patch.zip'))
        {
            $zip = new pclzip($saveDir . 'patch.zip');

            /* Remove files. */
            $files = $zip->listContent();
            if($files === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

            $fileName = @file_get_contents($saveDir . 'patchName');
            foreach($files as $file)
            {
                $name = $file['filename'];
                if($fileName)
                {
                    $nameLen = mb_strlen($fileName) + 1;
                    if(mb_substr($name, 0, $nameLen) == $fileName . DS) $name = mb_substr($name, $nameLen);
                    if(mb_substr($name, 0, 10) == 'zentaopms' . DS)     $name = mb_substr($name, 10);
                }

                if($name) @unlink( $this->config->zt_webDir . DS . $name);
            }
        }
        /* Restore files. */
        $backupPath = $saveDir . 'backup.zip';
        $zip = new pclzip($backupPath);

        $this->output($this->lang->patch->restoring);
        if($zip->extract(PCLZIP_OPT_PATH, '/') === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        $zfile = $this->app->loadClass('zfile');
        @$zfile->removeDir($saveDir);

        $this->output($this->lang->patch->restored);
    }

    /**
     * Build patch file.
     *
     * @param  array  $params
     * @access public
     * @return void
     */
    public function build($params)
    {
        if(isset($params['help'])) return $this->printHelp('build');

        if(!is_writable($this->config->runDir)) return $this->output(sprintf($this->lang->patch->error->notWritable, $this->config->runDir), 'err');

        $buildInfo   = new stdClass();
        $tryTime     = 0;
        $buildFields = explode(',', $this->config->patch->buildFields);
        foreach($buildFields as $field)
        {
            $this->output($this->lang->patch->build->{$field . 'Tip'});
            while(true)
            {
                if($tryTime > 2) return $this->output($this->lang->patch->tryTimeLimit, 'err');

                $inputValue = $this->readInput();
                $result     = $this->patch->checkInput($field, $inputValue, $buildInfo);
                if($result)
                {
                    $tryTime = 0;
                    if($field == 'id' && $result == 'exists')
                    {
                        $this->output(sprintf($this->lang->patch->error->build->patch, $result), 'err');
                        continue;
                    }

                    $buildInfo->$field = $inputValue;

                    if($field == 'id')        $buildInfo->patchName = $result;
                    if($field == 'buildPath') $buildInfo->buildPath = $result;
                    break;
                }

                $tryTime++;
                $this->output(sprintf($this->lang->patch->error->build->$field, $inputValue), 'err');
            }
        }

        /* Zip create. */
        $this->output($this->lang->patch->building);


        $yamlFile  = $this->config->runDir . DS . 'zh-cn.yaml';
        $version   = str_replace(' ', '', $buildInfo->version);
        $name      = substr($buildInfo->patchName, 0, -4);
        $code      = str_replace('.', '_', $name);
        $author    = $buildInfo->author;
        $desc      = $buildInfo->desc;
        $changelog = $buildInfo->changelog;
        $license   = $buildInfo->license;
        $date      = date('Y-m-d');
        $year      = date('Y');

        $this->app->loadClass('pclzip', true);

        $savePath = $this->config->runDir . DS . $buildInfo->patchName;

        $zip = new pclzip($savePath);
        if($zip->create($buildInfo->buildPath, PCLZIP_OPT_REMOVE_PATH, $buildInfo->buildPath, PCLZIP_OPT_ADD_PATH, 'zentaopms') === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        $yaml = fopen($yamlFile, 'w');
        fwrite($yaml, sprintf($this->lang->patch->buildDocTpl, $name, $code, $year, $author, $desc, $desc, str_replace(',', '_', $version), $license, $changelog, $date, $version));
        fclose($yaml);

        if($zip->add($yamlFile,PCLZIP_OPT_REMOVE_PATH, $this->config->runDir, PCLZIP_OPT_ADD_PATH, 'zentaopms' . DS . 'doc') === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        unset($zip);
        @unlink($yamlFile);

        return $this->output($this->lang->patch->buildSuccess);
    }

    /**
     * Release patch.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function release($params)
    {
        if(empty($params) or !isset($params['patchPath']) or empty($params['patchPath']) or isset($params['help'])) return $this->printHelp('release');

        /* Verify that the parameters are valid. */
        $patchPath = $this->getRealPath($params['patchPath']);
        if(!$patchPath) return $this->output(sprintf($this->lang->patch->error->invalidFile, $params['patchPath']), 'err');

        /* Verification name format. */
        $pathList    = explode(DS, $patchPath);
        $packageKey  = count($pathList) - 1;
        $packageName = $pathList[$packageKey];
        if(!$this->patch->checkPatchName($packageName)) return $this->output(sprintf($this->lang->patch->error->invalidFile, $params['patchPath']), 'err');

        $this->checkCZUser();
        /* Check whether the patch package exists. */
        $isExist = $this->patch->checkExist($packageName);
        if($isExist)
        {
            $wrongCount = 1;

            $this->output($this->lang->patch->release->replaceTip);
            while(true)
            {
                $result = $this->readInput();

                if($result == 'yes' or $result == 'y') break;
                if($result == 'no' or $result == 'n' or $wrongCount > 2) return false;

                ++$wrongCount;

                $this->output($this->lang->patch->release->replaceTip, 'err');
            }
        }

        /* Release patch by api. */
        $response = $this->patch->release($patchPath, $packageName);
        $this->output($response->message . PHP_EOL, 'err');
    }

    public function checkCZUser()
    {

        $needLogin = false;
        if(!isset($this->config->cz_account) or !isset($this->config->cz_password)) $needLogin = true;
        if(!$needLogin)
        {
            $loginResult = $this->patch->checkUser($this->config->cz_account, $this->config->cz_password);
            if(!isset($loginResult->result) or $loginResult->result == 'fail') $needLogin = true;
        }

        /* Check zentao account. */
        if($needLogin)
        {
            $this->output($this->lang->patch->release->needCzUser);
            while(true)
            {
                $this->output($this->lang->patch->release->accountTip);
                $account = $this->readInput();
                if(!$account) continue;

                $this->output($this->lang->patch->release->passwordTip);
                $password = $this->readInput();
                if(!$password) continue;

                $password    = md5($password);
                $loginResult = $this->patch->checkUser($account, $password);

                if(!isset($loginResult->result) or $loginResult->result == 'fail')
                {
                    $this->output(isset($loginResult->message) ? $loginResult->message . PHP_EOL : $this->lang->patch->release->userInvalid, 'err');
                }
                else
                {
                    $user = array(
                        'cz_account'  => $account,
                        'cz_password' => $password
                    );
                    $this->setUserConfigs($user);
                    break;
                }
            }
        }
    }
}
