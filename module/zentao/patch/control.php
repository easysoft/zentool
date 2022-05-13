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
            if($key == 'help') return $this->printHelp();
            if(method_exists($this, $key)) return $this->$key(array('patchID' => $param));
        }
    }

    public function printHelp($type = 'patch')
    {
        return fwrite(STDOUT, $this->lang->patch->help->$type);
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

        $patchList = array();
        for($i = 1; $i < 10; $i++)
        {
            $patch = array();
            $patch['type']      = 'bug' . $i;
            $patch['code']      = 'patch00' . $i;
            $patch['title']     = mb_substr('这个包处理了任务相关的bug', 15 - $i) . $i;
            $patch['date']      = '2022-01-0' . $i;
            $patch['installed'] = $this->lang->no;
            $patchList[] = $patch;
        }

        if(isset($params['showAll']))
        {
            $patchList[] = array(
                'type'      => 'story',
                'code'      => 'story',
                'title'     => '这个是需求标题',
                'date'      => '2022-05-01',
                'installed' => $this->lang->yes
            );
        }
        elseif(isset($params['local']))
        {
            $patchList = array();
            $patchList[] = array(
                'type'      => 'story',
                'code'      => 'story',
                'title'     => '这个是需求标题',
                'date'      => '2022-05-01',
                'installed' => $this->lang->yes
            );
        }

        return $this->printList($patchList, array('type', 'code', 'title', 'date', 'installed'), $this->lang->patch);
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

        $title = '这是一个标题';
        $desc  = '这是描述信息';
        $files = "a.php ,test/b.php";
        $logs  = "改了几个已知的bug，调整了用户交互";

        return fwrite(STDOUT, sprintf($this->lang->patch->viewPage, $patchID, $title, $desc, $files, $logs));
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

        if(!isset($this->config->zt_webDir) or empty($this->config->zt_webDir)) return fwrite(STDERR, 'You need run z set!');

        $url     = 'https://cyy.oop.cc/data/upload/config.zip';
        $saveDir = $this->config->zt_webDir . DS . 'tmp' . DS . 'patch' . DS . $params['patchID'] . DS;
        if(!file_exists($saveDir) && !mkdir($saveDir, 0777, true)) return fwrite(STDERR, 'No write access!');

        $patchPath  = $saveDir . 'patch.zip';
        $backupPath = $saveDir . 'backup.zip';

        fwrite(STDOUT, 'Downloading...');
        // file_put_contents($savePath, fopen(file_get_contents($url), 'r'));
        fwrite(STDOUT, 'Down');

        $this->app->loadClass('pclzip', true);
        $zip    = new pclzip($patchPath);
        $files  = $zip->listContent();
        if($files === 0) return fwrite(STDERR, $zip->errorInfo());

        fwrite(STDOUT, 'Backuping...');
        $fileNames = array();
        foreach($files as $file) $fileNames[] = $this->config->zt_webDir . DS . $file['filename'];

        $zip->changeFile($backupPath);
        if($zip->create($fileNames) === 0) return fwrite(STDERR,  $zip->errorInfo());
        fwrite(STDOUT, 'Down');

        fwrite(STDOUT, 'Installing...');
        $zip->changeFile($patchPath);
        if($zip->extract(PCLZIP_OPT_PATH, $this->config->zt_webDir) === 0) return fwrite(STDERR, $zip->errorInfo());
        fwrite(STDOUT, 'Install successfuly!');
        echo PHP_EOL;
    }
}
