<?php
class module extends control
{
    public $moduleName;
    public $moduleRoot;
    public $table;

    public function init($moduleName, $table)
    {
        $this->moduleName = $moduleName;
        $this->table      = $table;
        $this->moduleRoot = $this->config->runDir . DS . $this->moduleName . DS;

        mkdir($this->moduleRoot);

        $this->initView(); die();

        $this->initControl();
        $this->initModel();
        $this->initLang();

        $this->initConfig();
        $this->initCss();
        $this->initJs();

        echo 'inited';
    }

    public function initControl()
    {
        $this->view->module    = $this->moduleName;
        $this->view->author    = $this->config->author;
        $this->view->copyright = $this->config->copyright;
        $this->view->idfield   = $this->config->idfield;
        $this->view->license   = $this->config->license;
        $this->view->link      = $this->config->link;
        $this->view->package   = $this->view->module;
        $this->view->version   = $this->config->version;
        $this->view->viewvars  = $this->config->viewvars;

        $controlFile = $this->moduleRoot . 'control.php';

        $controlCode = $this->parse('module', 'control.code');
        return $this->zcode->create($controlFile, $controlCode);
    }

    public function initConfig()
    {
        touch($this->moduleRoot . 'config.php');
    }


    public function initModel()
    {
        $this->view->module       = $this->moduleName;
        $this->view->author       = $this->config->author;
        $this->view->copyright    = $this->config->copyright;
        $this->view->idfield      = $this->config->idfield;
        $this->view->license      = $this->config->license;
        $this->view->link         = $this->config->link;
        $this->view->package      = $this->view->module;
        $this->view->version      = $this->config->version;
        $this->view->viewvars     = $this->config->viewvars;
        $this->view->table        = strtoupper($this->table);
        $this->view->createfix    = '';
        $this->view->createfixer  = '';
        $this->view->createreturn = '';
        $this->view->editfix      = '';
        $this->view->editfixer    = '';
        $this->view->editreturn   = '';
        $this->view->createcheck  = '';
        $this->view->createskip   = '';
        $this->view->editskip     = '';
        $this->view->editcheck    = '';
        $this->view->getbyid      = '';

        $modelFile = $this->moduleRoot . 'model.php';

        $modelCode = $this->parse('module', 'model.code');
        return $this->zcode->create($modelFile, $modelCode);
    }

    public function initLang()
    {
        mkdir($this->moduleRoot . 'lang');
        $lang   = "\$lang->{$this->moduleName} = new stdClass();" . PHP_EOL;
        $cnLang = $lang;
        $enLang = $lang;
        $fields = $this->module->getFieldList($this->moduleName);
        foreach($fields as $field)
        {
            $cnLang .= "\$lang->{$this->moduleName}->{$field['name']} = '{$field['label']}';" . PHP_EOL;
            $enLang .= "\$lang->{$this->moduleName}->{$field['name']} = '{$field['name']}';" . PHP_EOL;
        }

        foreach($this->config->module->langs as $lang)
        {
            $langPath = $this->moduleRoot . "lang/$lang.php";
            file_put_contents($langPath, strpos($lang, 'zh') !== false ? $cnLang : $enLang);
        }
    }

    public function initView()
    {
        $pageList = array("browse", 'view', 'create');
        $pageList = array('view');
        foreach($pageList as $page)
        {
            if(method_exists($this, "init{$page}View")) call_user_func_array(array($this, "init{$page}View"), array());
        }
        return true;
    }

    public function initViewView()
    {
        $action = new stdClass;
        $action->action = 'view';
        $action->open = '';
        $views = $this->module->getViewFile($this->moduleName, $action);
        foreach($views as $item => $value) $this->view->$item = $value;
        $viewCode = $this->parse('module', 'view.view');
        $this->zcode->create($this->moduleRoot . "view/view.html.php", $viewCode);
    }

    public function initJs()
    {
        mkdir($this->moduleRoot . 'js');
    }

    public function initCss()
    {
        mkdir($this->moduleRoot . 'css');
    }
}
