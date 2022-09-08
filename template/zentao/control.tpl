<?php
/**
 * The control file of %MODULE% module of %PROJECT%.
 *
 * @copyright   %COPYRIGHT% 
 * @license     %LICENSE%
 * @author      %AUTHOR%
 * @package     %PACKAGE% 
 * @version     %VERSION%
 * @link        %LINK% 
 */
class %MODULE% extends control
{
    /**
     * Index 
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('browse'));
    }

    /**
     * Browse %MODULE%. 
     * 
     * @param  string $orderBy 
     * @param  int    $pageTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function browse($orderBy = '%IDFIELD%_desc', $pageTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', true);
        $pager = new pager($pageTotal, $recPerPage, $pageID);

        $this->view->title = $this->lang->%MODULE%->browse;
        $this->view->%MODULE%List = $this->%MODULE%->getList($orderBy, $pager);%VIEWVARS%
        $this->view->orderBy = $orderBy;
        $this->view->pager = $pager;
        $this->display();
    }

    /**
     * Create a %MODULE%. 
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $%MODULE%ID = $this->%MODULE%->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('%MODULE%', $%MODULE%ID, 'created');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        $this->view->title = $this->lang->%MODULE%->create;%VIEWVARS%
        $this->display();
    }

    /**
     * Edit a %MODULE%.  
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return void
     */
    public function edit($%MODULE%ID = 0)
    {
        if($_POST)
        {
            $changes = $this->%MODULE%->update($%MODULE%ID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('%MODULE%', $%MODULE%ID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            $link = isset($this->server->http_referer) ? $this->server->http_referer : inlink('browse');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $this->view->title = $this->lang->%MODULE%->edit;
        $this->view->%MODULE% = $this->%MODULE%->getById($%MODULE%ID);%VIEWVARS%
        $this->display();
    }

    /**
     * View detail of a %MODULE%. 
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return void
     */
    public function view($%MODULE%ID = 0)
    {
        $this->view->title = $this->lang->%MODULE%->view;
        $this->view->%MODULE% = $this->%MODULE%->getById($%MODULE%ID);%VIEWVARS%
        $this->display();
    }

    /**
     * Delete a %MODULE%. 
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return void
     */
    public function delete($%MODULE%ID = 0)
    {
        $this->%MODULE%->delete($%MODULE%ID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->send(array('result' => 'success'));
    }
}
