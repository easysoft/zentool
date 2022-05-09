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
class index extends control
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
    public function index($params)
    {
        foreach($params as $key => $param)
        {
            if($param == '-h' or $param == '--help')
            {
                echo sprintf($this->lang->index->help, '禅道');
                return;
            }
        }
    }
}
