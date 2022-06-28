<?php
/**
 * The model file of zentaophp module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     zentaophp
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class zentaophpModel extends model
{
    public function create($params)
    {
        $objectName = $params['objectName'];
        $this->createControl($objectName);
    }

    public function createControl($objectName)
    {
        $content = '<?php
class ' . $objectName . ' extends control
{
    public function browse($orderBy = "", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $uri = $this->app->getURI(true);
        $this->session->set("' . $objectName . 'List", $uri, $this->app->tab);

        $sort = common::appendOrder($orderBy);

        $this->app->loadClass("pager", $static = true);
        if($this->app->getViewType() == "mhtml" || $this->app->getViewType() == "xhtml") $recPerPage = 10;
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $objects = $this->' . $objectName . '->getList($sort, $pager);

        $this->view->title   = $this->lang->' . $objectName . '->common . $this->lang->colon . $this->lang->' . $objectName . '->common;
        $this->view->objects = $objects;
        $this->view->users   = $this->loadModel("user")->getPairs("noletter");
        $this->view->pager   = $pager;

        $this->display();
    }

    public function create()
    {
        if($_POST)
        {
            $objectID = $this->' . $objectName . '->create($projectID);
            if(dao::isError()) return $this->send(array("result" => "fail", "message" => dao::getError()));
            $this->loadModel("action")->create("' . $objectName . '", $objectID, "Opened");

            $link = $this->session->' . $objectName . 'List;
            return $this->send(array("result" => "success", "message" => $this->lang->saveSuccess, "locate" => $link, "id" => $objectID));
        }

        $this->view->title = $this->lang->' . $objectName . '->common . $this->lang->colon . $this->lang->' . $objectName . '->create;
        $this->display();
    }

    public function edit($objectID)
    {

    }
}
';
        $fileName = 'contorl.php';
        $filePath = $this->config->dev_ztDir . DS . 'module' . DS . strtolower($objectName);
        $this->createFile($filePath, $fileName, $content);
    }

    public function createFile($filePath, $fileName, $content)
    {
        $fileName = $filePath . DS . $fileName;

        if(!is_dir($filePath)) mkdir($filePath, 0755, true);
        if(!file_exists($fileName))
        {
            touch($fileName);
            file_put_contents($fileName, $content);
        }
    }
}
