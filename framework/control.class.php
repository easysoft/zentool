<?php /**
 * ZenTaoPHP的baseControl类。
 * The baseControl class file of ZenTaoPHP framework.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class control
{
    /**
     * 全局对象 $app。
     * The global $app object.
     *
     * @var object
     * @access public
     */
    public $app;

    /**
     * 应用名称 $appName
     * The global $appName.
     *
     * @var string
     * @access public
     */
    public $appName;

    /**
     * 全局对象 $config。
     * The global $config object.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * 全局对象 $lang。
     * The global $lang object.
     *
     * @var object
     * @access public
     */
    public $lang;

    /**
     * $post对象，用户可以通过$this->post->key来引用$_POST变量。
     * The $post object, useer can access a post var by $this->post->key.
     *
     * @var ojbect
     * @access public
     */
    public $post;

    /**
     * $get对象，用户可以通过$this->get->key来引用$_GET变量。
     * The $get object, useer can access a get var by $this->get->key.
     *
     * @var ojbect
     * @access public
     */
    public $get;

    /**
     * $server对象，用户可以通过$this->server->key来引用$_SERVER变量。
     * The $server object, useer can access a server var by $this->server->key.
     *
     * @var ojbect
     * @access public
     */
    public $server;

    /**
     * 当前模块的名称。
     * The name of current module.
     *
     * @var string
     * @access public
     */
    public $moduleName;

    /**
     * 客户端设备。
     * The client device.
     *
     * @var string
     * @access public
     */
    public $clientDevice;

    /**
     * 不同设备下视图文件的前缀。
     * The prefix of view file for mobile or PC.
     *
     * @var string
     * @access public
     */
    public $devicePrefix;

    /**
     * 构造方法。
     *
     * 1. 将全局变量设为baseControl类的成员变量，方便baseControl的派生类调用；
     * 2. 设置当前模块，读取该模块的model类；
     * 3. 初始化$view视图类。
     *
     * The construct function.
     *
     * 1. global the global vars, refer them by the class member such as $this->app.
     * 2. set the pathes of current module, and load it's model class.
     * 3. auto assign the $lang and $config to the view.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  string $appName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '', $appName = '')
    {
        /*
         * 将全局变量设为baseControl类的成员变量，方便baseControl的派生类调用。
         * Global the globals, and refer them as a class member.
         */
        global $app, $config, $lang, $common, $params;
        $this->app      = $app;
        $this->config   = $config;
        $this->lang     = $lang;
        $this->appName  = $appName ? $appName : $this->app->getAppName();

        /**
         * 设置当前模块，读取该模块的model类。
         * Load the model file auto.
         */
        $this->setModuleName($moduleName);
        $this->setMethodName($methodName);
        $this->loadModel($this->moduleName, $appName);

        /**
         * 设置超级变量，从$app引用过来。
         * Set super vars.
         */
        $this->setSuperVars();
    }

    //-------------------- Model相关方法(Model related methods) --------------------//

    /*
     * 设置模块名。
     * Set the module name.
     *
     * @param   string  $moduleName  模块名，如果为空，则从$app中获取. The module name, if empty, get it from $app.
     * @access  public
     * @return  void
     */
    public function setModuleName($moduleName = '')
    {
        $this->moduleName = $moduleName ? strtolower($moduleName) : $this->app->getModuleName();
    }

    /**
     * 设置方法名。
     * Set the method name.
     *
     * @param   string  $methodName   方法名，如果为空，则从$app中获取。The method name, if empty, get it from $app.
     * @access  public
     * @return  void
     */
    public function setMethodName($methodName = '')
    {
        $this->methodName = $methodName ? strtolower($methodName) : $this->app->getMethodName();
    }

    /**
     * 加载指定模块的model文件。
     * Load the model file of one module.
     *
     * @param   string  $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param   string  $appName    The app name, if empty, use current app's name.
     * @access  public
     * @return  object|bool 如果没有model文件，返回false，否则返回model对象。If no model file, return false, else return the model object.
     */
    public function loadModel($moduleName = '', $appName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($appName))    $appName    = $this->appName;

        global $loadedModels;
        if(isset($loadedModels[$appName][$moduleName]))
        {
            $this->$moduleName = $loadedModels[$appName][$moduleName];
            return $this->$moduleName;
        }

        $modelFile = $this->app->setModelFile($moduleName, $appName);

        /**
         * 如果没有model文件，尝试加载config配置信息。
         * If no model file, try load config.
         */
        if(!helper::import($modelFile))
        {
            $this->app->loadModuleConfig($moduleName, $appName);
            $this->app->loadLang($moduleName, $appName);
            return false;
        }

        /**
         * 如果没有扩展文件，model类名是$moduleName + 'model'，如果有扩展，还需要增加ext前缀。
         * If no extension file, model class name is $moduleName + 'model', else with 'ext' as the prefix.
         */
        $modelClass = class_exists('ext' . $appName . $moduleName. 'model') ? 'ext' . $appName . $moduleName . 'model' : $appName . $moduleName . 'model';
        if(!class_exists($modelClass))
        {
            $modelClass = class_exists('ext' . $moduleName. 'model') ? 'ext' . $moduleName . 'model' : $moduleName . 'model';
            if(!class_exists($modelClass)) $this->app->triggerError(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);
        }

        /**
         * 初始化model对象，在control对象中可以通过$this->$moduleName来引用。
         * Init the model object thus you can try $this->$moduleName to access it.
         */
        $loadedModels[$appName][$moduleName] = new $modelClass($appName);
        $this->$moduleName = $loadedModels[$appName][$moduleName];
        return $this->$moduleName;
    }

    /**
     * 设置超级全局变量，方便直接引用。
     * Set the super vars.
     *
     * @access public
     * @return void
     */
    public function setSuperVars()
    {
        $this->post    = $this->app->post;
        $this->get     = $this->app->get;
        $this->server  = $this->app->server;
    }

    /**
     * 创建一个模块方法的链接。
     * Create a link to one method of one module.
     *
     * @param   string         $moduleName    module name
     * @param   string         $methodName    method name
     * @param   string|array   $vars          the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param   string         $viewType      the view type
     * @access  public
     * @return  string the link string.
     */
    public function createLink($moduleName, $methodName = 'index', $vars = array(), $viewType = '', $onlybody = false)
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars, $viewType, $onlybody);
    }

    /**
     * 创建当前模块的一个方法链接。
     * Create a link to the inner method of current module.
     *
     * @param   string         $methodName    method name
     * @param   string|array   $vars          the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param   string         $viewType      the view type
     * @access  public
     * @return  string  the link string.
     */
    public function inlink($methodName = 'index', $vars = array(), $viewType = '', $onlybody = false)
    {
        return helper::createLink($this->moduleName, $methodName, $vars, $viewType, $onlybody);
    }
}
