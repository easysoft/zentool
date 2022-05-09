<?php
/**
 * The control file of commit module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class commit extends control
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
     * @access public
     * @return void
     */
    public function index()
    {
        $command = 'git commit ';
        foreach($this->app->args as $key => $arg)
        {
            if($key < 2) continue;
            $command .= $arg . ' ';
        }

        exec($command);
    }
}
