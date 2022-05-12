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
        if(isset($params['help'])) return $this->printHelp('list');

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
        if(empty($params) or isset($params['help'])) return $this->printHelp('view');

        $patchID = $params['patchID'];

        $title = '这是一个标题';
        $desc  = '这是描述信息';
        $files = "a.php ,test/b.php";
        $logs  = "改了几个已知的bug，调整了用户交互";

        return fwrite(STDOUT, sprintf($this->lang->patch->viewPage, $patchID, $title, $desc, $files, $logs));
    }
}
