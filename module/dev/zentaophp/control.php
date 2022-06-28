<?php
/**
 * The control file of zentaophp module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class zentaophp extends control
{
    /**
     * The construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
        if(isset($params['help'])) return $this->printHelp();
    }

    /**
     * Print help.
     *
     * @access public
     * @return void
     */
    public function printHelp()
    {
        return $this->output(sprintf($this->lang->entry->help, $this->app->appName));
    }

    public function create($params)
    {
        $this->zentaophp->create($params);
    }
}
