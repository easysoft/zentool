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

        $patchList = $this->patch->getPatchList($params);

        return $this->printList($patchList, array('type', 'code', 'name', 'date', 'installed'), $this->lang->patch);
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

        $patchID = $params['patchID'];

        $name  = '这是一个标题';
        $desc  = '这是描述信息';
        $files = "a.php, test/b.php";
        $logs  = "改了几个已知的bug，调整了用户交互";

        return $this->output(sprintf($this->lang->patch->viewPage, $patchID, $name, $desc, $files, $logs));
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

        /* Check whether the parameter is an ID or a path. */
        if(strpos($params['patchID'], '.zip') !== false)
        {
            $patchPath = $this->getRealPath($params['patchID']);
            if(!$patchPath) return $this->output(sprintf($this->lang->patch->error->invalidName, $params['patchID']), 'err');

            /* Verification name format. */
            $pathList    = explode(DS, $patchPath);
            $packageKey  = count($pathList) - 1;
            $packageName = $pathList[$packageKey];
            if(!$this->patch->checkPatchName($packageName)) return $this->output(sprintf($this->lang->patch->error->invalidName, $params['patchID']), 'err');

            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $packageName . DS;
        }
        else
        {
            $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $params['patchID'] . DS;
        }

        /* Check whether installed. */
        if(file_exists($saveDir . 'install.lock')) return $this->output($this->lang->patch->error->installed, 'err');
        if($params['patchID'] == 'none')           return $this->output($this->lang->patch->error->invalid, 'err');
        if($params['patchID'] == 'incompatible')   return $this->output($this->lang->patch->error->incompatible, 'err');

        if(!file_exists($saveDir) && !mkdir($saveDir, 0777, true)) return $this->output(sprintf($this->lang->patch->error->notWritable, $saveDir), 'err');

        $backupPath = $saveDir . 'backup.zip';
        if(!isset($patchPath))
        {
            $patchPath = $saveDir . 'patch.zip';

            if(!file_exists($patchPath))
            {
                $this->output($this->lang->patch->downloading);
                $url = 'http://cyy.oop.cc/data/upload/config.zip';
                if(!@copy($url, $patchPath)) $this->output(error_get_last() . PHP_EOL, 'err');
                $this->output($this->lang->patch->down);
            }
        }

        $this->app->loadClass('pclzip', true);
        $zip   = new pclzip($patchPath);
        $files = $zip->listContent();
        if($files === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        $this->output($this->lang->patch->backuping);
        $fileNames = array();
        foreach($files as $file) $fileNames[] = $this->config->zt_webDir . DS . $file['filename'];

        $zip->changeFile($backupPath);
        if($zip->create($fileNames, PCLZIP_OPT_REMOVE_PATH, $this->config->zt_webDir) === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');
        $this->output($this->lang->patch->down);

        $this->output($this->lang->patch->installing);
        $zip->changeFile($patchPath);
        if($zip->extract(PCLZIP_OPT_PATH, $this->config->zt_webDir) === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');
        @touch($saveDir . 'install.lock');
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

        $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $params['patchID'] . DS;
        if(!file_exists($saveDir . 'install.lock')) return $this->output($this->lang->patch->error->notInstall, 'err');

        $backupPath = $saveDir . 'backup.zip';

        $this->app->loadClass('pclzip', true);
        $zip = new pclzip($backupPath);

        $this->output($this->lang->patch->restoring);
        if($zip->extract(PCLZIP_OPT_PATH, '/') === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');
        @unlink($saveDir . 'install.lock');
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
                    if($field == 'id' && is_string($result))
                    {
                        $this->output(sprintf($this->lang->patch->error->build->patch, $result), 'err');
                        continue;
                    }

                    $buildInfo->$field = $inputValue;

                    if($field == 'id') $buildInfo->patchName = $result;
                    break;
                }

                $tryTime++;
                $this->output(sprintf($this->lang->patch->error->build->$field, $inputValue), 'err');
            }
        }

        /* Zip create. */
        $this->output($this->lang->patch->building);

        $savePath = $this->config->runDir . DS . $buildInfo->patchName[0];

        $this->app->loadClass('pclzip', true);
        $zip = new pclzip($savePath);
        if($zip->create($buildInfo->buildPath, PCLZIP_OPT_REMOVE_PATH, $buildInfo->buildPath) === 0) return $this->output($zip->errorInfo() . PHP_EOL, 'err');

        if(count($buildInfo->patchName) > 1)
        {
            for($i = 1; $i < count($buildInfo->patchName); $i++) @copy($savePath, $this->config->runDir . DS . $buildInfo->patchName[$i]);
        }

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

        /* Input release info. */
        $releaseInfo = new stdclass();

        $this->output($this->lang->patch->release->descTip);
        $releaseInfo->desc = $this->readInput();

        $this->output($this->lang->patch->release->changelogTip);
        $releaseInfo->changelog = $this->readInput();

        /* Release patch by api. */
        $this->patch->release($patchPath, $releaseInfo);
        $this->output($this->lang->patch->releaseSuccess);
    }
}
