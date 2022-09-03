<?php
/**
 * The model file of workflow module of Zcoder.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     商业软件，非开源软件
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     workflow
 * @version     $Id$
 * @link        http://www.cnezsoft.com
 */
class workflowModel extends model
{
    /**
     * Get model file of a module.
     *
     * @param  object $project
     * @param  object $module
     * @access public
     * @return string
     */
    public function getModelFile($project = null, $module = null)
    {
        $copyright   = isset($project->settings->copyright) ? $project->settings->copyright : '';
        $license     = isset($project->settings->license) ? $project->settings->license : '';
        $author      = isset($project->settings->author) ? $project->settings->author : '';
        $email       = isset($project->settings->email) ? $project->settings->email: '';
        $package     = $module->module;
        $version     = isset($project->settings->version) ? $project->settings->version : '';
        $link        = isset($project->settings->link) ? $project->settings->link : '';
        $table       = 'TABLE_' . strtoupper($module->module);

        if($email) $author .= " <$email>";

        $find      = array('%MODULE%', '%PROJECT%', '%COPYRIGHT%', '%LICENSE%', '%AUTHOR%', '%PACKAGE%', '%VERSION%', '%LINK%', '%TABLE%');
        $replace   = array($module->module, $project->code, $copyright, $license, $author, $package, $version, $link, $table);
        $modelFile = file_get_contents($this->app->getWwwRoot() . 'template' . DS . 'model.php');
        $modelFile = str_replace($find, $replace, $modelFile);
        $modelFile = $this->fixModelFile($project, $module, $modelFile);

        return $modelFile;
    }

    /**
     * Fix model file.
     *
     * @param  object $project
     * @param  object $module
     * @param  string $modelFile
     * @access public
     * @return string
     */
    public function fixModelFile($project = null, $module = null, $modelFile = '')
    {
        $hasFile      = false;
        $fixerList    = array();
        $fixList      = array();
        $skipList     = array();
        $checkList    = array();
        $returnList   = array();
        $idField      = $this->getIdField($project, $module);
        $lastInsertID = $idField == 'id' ? "\$this->dao->lastInsertID()" : "\$this->dao->select('LAST_INSERT_ID() as id')->fetch('id')";

        if(isset($module->id))
        {
            $actions = $this->getActionList($module->id);
            foreach($actions as $action)
            {
                $fields = $this->getActionFields($module->id, $action->id, $decodeOptions = false);
                foreach($fields as $field)
                {
                    if($hasFile or !$field->show) continue;
                    if($field->field == 'file') $hasFile = true;
                }

                if($action->action == 'browse' or $action->action == 'view' or $action->action == 'delete') continue;

                if($action->action == 'create')
                {
                    $fixerList[$action->action]  = "\n            ->add('createdBy', \$this->app->user->account)";
                    $fixerList[$action->action] .= "\n            ->add('createdDate', helper::now())";
                }
                else
                {
                    $fixerList[$action->action]  = "\n            ->add('editedBy', \$this->app->user->account)";
                    $fixerList[$action->action] .= "\n            ->add('editedDate', helper::now())";
                }
                $fixerList[$action->action] .= "\n            ";

                $fixList[$action->action]   = '';
                $skipList[$action->action]  = '';
                $checkList[$action->action] = '';

                if($action->action == 'create')
                {
                    $returnList[$action->action] = "\n        return $lastInsertID;";
                }
                else
                {
                    $returnList[$action->action] = "\n        return commonModel::createChanges(\$oldData, \$data);";
                }

                foreach($fields as $field)
                {
                    if(!$field->show) continue;

                    if($field->control == 'checkbox')
                    {
                        $fixList[$action->action] .= "\n        if(\$this->post->$field->field) \$data->$field->field = trim(join(',', \$this->post->$field->field), ',');";
                    }

                    if($field->rules)
                    {
                        $rules = array_unique(explode(',', $field->rules));
                        foreach($rules as $rule)
                        {
                            if(!$rule) continue;

                            if($rule == 'unique' && $action->action != 'create')
                            {
                                $checkList[$action->action] .= "\n            ->check('$field->field', '$rule', \"id != \${$module->module}ID\")";
                            }
                            else
                            {
                                $checkList[$action->action] .= "\n            ->check('$field->field', '$rule')";
                            }
                        }
                    }

                    if($field->field == 'file')
                    {
                        if($action->action == 'create')
                        {
                            $returnList[$action->action]  = "\n       \${$module->module}ID = $lastInsertID;";
                            $returnList[$action->action] .= "\n       \$this->loadModel('file')->saveUpload('$module->module', \${$module->module}ID);\n";
                            $returnList[$action->action] .= "\n       return \${$module->module}ID;";
                        }
                        else
                        {
                            $returnList[$action->action] = "\n        \$this->loadModel('file')->saveUpload('$module->module', \${$module->module}ID);\n" . $returnList[$action->action];
                        }
                        $skipList[$action->action] .= ", \$skip = 'files, labels'";
                    }
                }
                if($fixList[$action->action])   $fixList[$action->action]   .= "\n            ";
                if($checkList[$action->action]) $checkList[$action->action] .= "\n            ";
            }
        }

        $table = 'TABLE_' . strtoupper($module->module);
        if($hasFile)
        {
            $getByID  = "\$$module->module = \$this->dao->select('*')->from($table)->where('$idField')->eq(\${$module->module}ID)->fetch();";
            $getByID .= "\n        if(\$$module->module) \$$module->module->files = \$this->loadModel('file')->getByObject('$module->module', \${$module->module}ID);";
            $getByID .= "\n        return \$$module->module;";
        }
        else
        {
            $getByID = "return \$this->dao->select('*')->from($table)->where('$idField')->eq(\${$module->module}ID)->fetch();";
        }

        $find    = array('%GETBYID%', '%IDFIELD%');
        $replace = array($getByID, $idField);
        foreach($fixerList as $action => $fixer)
        {
            $find[] = strtoupper("%{$action}FIXER%");
            $find[] = strtoupper("%{$action}FIX%");
            $find[] = strtoupper("%{$action}SKIP%");
            $find[] = strtoupper("%{$action}CHECK%");
            $find[] = strtoupper("%{$action}RETURN%");

            $replace[] = $fixer;
            $replace[] = $fixList[$action];
            $replace[] = $skipList[$action];
            $replace[] = $checkList[$action];
            $replace[] = $returnList[$action];
        }
        $modelFile = str_replace($find, $replace, $modelFile);

        return $modelFile;
    }

    /**
     * Get primary key of a table.
     *
     * @param  object $project
     * @param  object $module
     * @access public
     * @return string
     */
    public function getIdField($project = null, $module = null)
    {
        $idField = 'id';
        $fields  = $this->loadModel('project')->getTableFields("$project->dbName.$project->dbPrefix$module->module");
        foreach($fields as $field)
        {
            if(stripos($field->extra, 'auto_increment') !== false) $idField = $field->field;
        }

        return $idField;
    }

    /**
     * Get config file of a module.
     *
     * @param  object $project
     * @param  object $module
     * @access public
     * @return string
     */
    public function getConfigFile($project = null, $module = null)
    {
        $config  = '';
        $actions = $this->getActionList($module->id);
        foreach($actions as $action)
        {
            if($action->action == 'browse' or $action->action == 'view') continue;

            $requireFields = array();
            $fields = $this->getActionFields($module->id, $action->id);
            foreach($fields as $field)
            {
                if(!$field->show) continue;

                $rules = array_unique(explode(',', $field->rules));
                foreach($rules as $rule)
                {
                    if(!$rule) continue;

                    if($rule == 'notempty')
                    {
                        $requireFields[] = $field->field;
                        break;
                    }
                }
            }
            if($requireFields)
            {
                $requireFields = implode(',', $requireFields);
                $config .= "\n\$config->$module->module->require->$action->action = '$requireFields';";
            }
        }
        if($config) $config = "<?php\n\$config->$module->module->require = new stdclass();" . $config;

        return $config;
    }

    /**
     * Get control file of a module.
     *
     * @param  object $project
     * @param  object $module
     * @access public
     * @return string
     */
    public function getControlFile($project = null, $module = null)
    {
        if(!$project or !$module) return '';

        $copyright = isset($project->settings->copyright) ? $project->settings->copyright : '';
        $license   = isset($project->settings->license) ? $project->settings->license : '';
        $author    = isset($project->settings->author) ? $project->settings->author : '';
        $email     = isset($project->settings->email) ? $project->settings->email: '';
        $package   = $module->module;
        $version   = isset($project->settings->version) ? $project->settings->version : '';
        $link      = isset($project->settings->link) ? $project->settings->link : '';
        $idField   = $this->getIdField($project, $module);
        $viewVars  = $this->getViewVars($module);

        if($email) $author .= " <$email>";

        $find        = array('%MODULE%', '%PROJECT%', '%COPYRIGHT%', '%LICENSE%', '%AUTHOR%', '%PACKAGE%', '%VERSION%', '%LINK%', '%IDFIELD%', '%VIEWVARS%');
        $replace     = array($module->module, $project->code, $copyright, $license, $author, $package, $version, $link, $idField, $viewVars);
        $controlFile = file_get_contents($this->app->getWwwRoot() . 'template' . DS . 'control.php');
        $controlFile = str_replace($find, $replace, $controlFile);

        return $controlFile;
    }

    /**
     * Get vars display in view file.
     *
     * @param  string $module
     * @access public
     * @return string
     */
    public function getViewVars($module = null)
    {
        if(!$module) return $controlFile;

        $viewVars = '';
        $vars     = array();

        $fields = $this->getFieldList($module->id, 'options_desc');
        foreach($fields as $field)
        {
            if(!$field->options or is_array($field->options)) continue;

            $options = "{$field->field}List";
            if($field->options == 'user' or $field->options == 'dept')
            {
                $options = "{$field->options}List";
            }
            if(isset($vars[$options])) continue;
            $vars[$options] = $options;

            if($field->options == 'user')
            {
                $viewVars .= "\n        \$this->view->$options = \$this->loadModel('user')->getPairs();";
            }
            elseif($field->options == 'dept')
            {
                $viewVars .= "\n        \$this->view->$options = \$this->loadModel('tree')->getPairs('', 'dept');";
            }
            elseif($field->options == 'sql')
            {
                $data = $this->getSqlAndVars($module->id, $field->id);
                if($data)
                {
                    $sql = rtrim(trim(ltrim(trim(strtolower($data->sql), ''), 'select')), ';');
                    $viewVars .= "\n        \$this->view->$options = \$this->dao->select('$sql')->fetchPairs();";
                }
            }
        }

        return $viewVars;
    }

    /**
     * Get lang file of a module.
     *
     * @param  object $project
     * @param  object $module
     * @access public
     * @return string
     */
    public function getLangFile($project = null, $module = null)
    {
        $clientLang = $this->app->getClientLang();
        $copyright  = isset($project->settings->copyright) ? $project->settings->copyright : '';
        $license    = isset($project->settings->license) ? $project->settings->license : '';
        $author     = isset($project->settings->author) ? $project->settings->author : '';
        $email      = isset($project->settings->email) ? $project->settings->email: '';
        $package    = $module->module;
        $version    = isset($project->settings->version) ? $project->settings->version : '';
        $link       = isset($project->settings->link) ? $project->settings->link : '';

        $find     = array('%CLIENTLANG%', '%MODULE%', '%PROJECT%', '%COPYRIGHT%', '%LICENSE%', '%AUTHOR%', '%PACKAGE%', '%VERSION%', '%LINK%');
        $replace  = array($clientLang, $module->module, $project->code, $copyright, $license, $author, $package, $version, $link);
        $langFile = file_get_contents($this->app->getWwwRoot() . 'template' . DS . 'lang.php');
        $langFile = str_replace($find, $replace, $langFile);

        $fields  = $this->getFieldPairs($module->id);
        $actions = $this->getActionList($module->id);

        $fieldPairs = array('common' => $module->name);
        foreach($actions as $action)
        {
            $fieldPairs[$action->action] = $action->name;
        }
        $fieldPairs['basic'] = $this->lang->workflowaction->layout->positionList['view']['basic'];
        $fieldPairs['info']  = $this->lang->workflowaction->layout->positionList['view']['info'];
        $fieldPairs += $fields;

        /* Compute max length of fields to align them.*/
        $maxLen = 0;
        foreach($fieldPairs as $field => $name)
        {
            if(strlen($field) > $maxLen) $maxLen = strlen($field);
        }
        $langFile .= "if(!isset(\$lang->{$module->module})) \$lang->{$module->module} = new stdclass();\n";
        foreach($fieldPairs as $field => $name)
        {
            if(!$name)
            {
                $langFile .= "\n";
                continue;
            }

            $blank = '';
            for($i = 0; $i < $maxLen - strlen($field); $i++) $blank .= ' ';
            $langFile .= "\$lang->{$module->module}->$field$blank = '$name';\n";
        }

        $fields = $this->getFieldList($module->id);
        foreach($fields as $field)
        {
            if(!$field->options or !is_array($field->options)) continue;

            $langFile .= "\n\$lang->{$module->module}->{$field->field}List = array();\n";
            foreach($field->options as $key => $value)
            {
                $langFile .= "\$lang->{$module->module}->{$field->field}List['$key'] = '$value';\n";
            }
        }

        return $langFile;
    }

    /**
     * Get view file of a action.
     *
     * @param  object $project
     * @param  object $module
     * @param  object $action
     * @access public
     * @return string
     */
    public function getViewFile($project = null, $module = null, $action = null)
    {
        if($action->open == 'none') return '';

        $pageHeader = '';
        $pageFooter = '';
        if($action->open == 'none')
        {
            return '';
        }
        elseif($action->open == 'modal')
        {
            $pageHeader = "<?php include '../../common/view/header.modal.html.php';?>";
            $pageFooter = "<?php include '../../common/view/footer.modal.html.php';?>";
        }
        else
        {
            $pageHeader = "<?php include '../../common/view/header.html.php';?>";
            $pageFooter = "<?php include '../../common/view/footer.html.php';?>";
        }

        $copyright = isset($project->settings->copyright) ? $project->settings->copyright : '';
        $license   = isset($project->settings->license) ? $project->settings->license : '';
        $author    = isset($project->settings->author) ? $project->settings->author : '';
        $email     = isset($project->settings->email) ? $project->settings->email: '';
        $package   = $module->module;
        $version   = isset($project->settings->version) ? $project->settings->version : '';
        $link      = isset($project->settings->link) ? $project->settings->link : '';
        $idField   = $this->getIdField($project, $module);

        if($email) $author .= " <$email>";

        $fields = $this->getActionFields($module->id, $action->id, $decodeOptions = false);

        $actionCode = ($action->action == 'browse' or $action->action == 'view') ? $action->action : 'operate';
        $result   = $this->{'get' . $actionCode . 'View'}($module->module, $action, $fields, $idField);
        $find     = array_merge(array('%ACTION%', '%MODULE%', '%PROJECT%', '%COPYRIGHT%', '%LICENSE%', '%AUTHOR%', '%PACKAGE%', '%VERSION%', '%LINK%', '%PAGEHEADER%', '%PAGEFOOTER%', '%IDFIELD%'), $result['find']);
        $replace  = array_merge(array($action->action, $module->module, $project->code, $copyright, $license, $author, $package, $version, $link, $pageHeader, $pageFooter, $idField), $result['replace']);
        $viewFile = file_get_contents($this->app->getWwwRoot() . 'template' . DS . 'view' .DS ."$actionCode.html.php");
        $viewFile = str_replace($find, $replace, $viewFile);

        return $viewFile;
    }

    /**
     * Get view file of browse action.
     *
     * @param  string $module
     * @param  object $action
     * @param  array  $fields
     * @param  string $idField
     * @access public
     * @return string
     */
    public function getBrowseView($module = '', $action = null, $fields = array(), $idField = 'id')
    {
        $tableHeader = '';
        $tableBody   = '';
        foreach($fields as $fieldObj)
        {
            if(!$fieldObj->show) continue;

            $field = $fieldObj->field;
            $class = $fieldObj->width != 'auto' ? " class='w-{$fieldObj->width}px'" : '';
            $tableHeader .= "\n        <th$class>";
            if($field == 'desc' or $field == 'asc')
            {
                $tableHeader .= "<?php echo \$lang->$module->$field;?>";
            }
            elseif($field == 'actions')
            {
                $tableHeader .= "<?php echo \$lang->actions;?>";
            }
            else
            {
                $tableHeader .= "<?php commonModel::printOrderLink('$field', \$orderBy, \$vars, \$lang->$module->$field);?>";
            }
            $tableHeader .= "</th>";

            $tableBody .= "\n        <td>";
            if($field == $idField)
            {
                $tableBody .= "<?php if(!commonModel::printLink('$module', 'view', \"{$module}ID=\${$module}->$idField\", \${$module}->$idField)) echo \${$module}->$idField;?>";
            }
            elseif($field == 'actions')
            {
                $tableBody .= "\n          <?php commonModel::printLink('$module', 'view', \"{$module}ID=\${$module}->$idField\", \$lang->detail);?>";
                $tableBody .= "\n          <?php commonModel::printLink('$module', 'edit', \"{$module}ID=\${$module}->$idField\", \$lang->edit);?>";
                $tableBody .= "\n          <?php commonModel::printLink('$module', 'delete', \"{$module}ID=\${$module}->$idField\", \$lang->delete, \"class='deleter'\");?>";
            }
            elseif($fieldObj->control == 'date' or $field == 'createdDate' or $field == 'editedDate')
            {
                $tableBody .= "<?php echo formatTime(\$$module->$field, 'Y-m-d');?>";
            }
            elseif($fieldObj->control == 'select' or $fieldObj->control == 'radio' or $fieldObj->control == 'checkbox')
            {
                if($fieldObj->options == 'user' or $fieldObj->options == 'dept')
                {
                    $options = "{$fieldObj->options}List";
                }
                elseif(is_array($fieldObj->options))
                {
                    $options = "lang->$module->{$field}List";
                }
                else
                {
                    $options = "{$field}List";
                }
                if($fieldObj->control == 'select' or $fieldObj->control == 'radio')
                {
                    $tableBody .= "<?php echo zget(\$$options, \$$module->$field);?>";
                }
                elseif($fieldObj->control == 'checkbox')
                {
                    $tableBody .= "<?php foreach(explode(',', \$$module->$field) as \$$field) echo zget(\$$options, \$$field) . ' ';?>";
                }
            }
            else
            {
                $tableBody .= "<?php echo \$$module->$field;?>";
            }
            $tableBody .= '</td>';
        }

        $result = array();
        $result['find']    = array('%TABLEHEADER%', '%TABLEBODY%');
        $result['replace'] = array($tableHeader, $tableBody);

        return $result;
    }

    /**
     * Get view file of view action.
     *
     * @param  string $module
     * @param  object $action
     * @param  array  $fields
     * @param  string $idField
     * @access public
     * @return string
     */
    public function getViewView($module = '', $action = null, $fields = array(), $idField = 'id')
    {
        $mainContent = '';
        $sideContent = '';

        $pageActions  = "\n      <div class='btn-group'>";
        $pageActions .= "\n        <?php commonModel::printLink('$module', 'edit', \"{$module}ID=\${$module}->$idField\", \$lang->edit, \"class='btn btn-primary'\");?>";
        $pageActions .= "\n        <?php commonModel::printLink('$module', 'delete', \"{$module}ID=\${$module}->$idField\", \$lang->delete, \"class='btn btn-primary deleter'\");?>";
        $pageActions .= "\n      </div>";

        $i = 0;
        foreach($fields as $fieldObj)
        {
            if(!$fieldObj->show) continue;

            $field   = $fieldObj->field;
            $display = "\$$module->$field";
            if($fieldObj->control == 'date' or $field == 'createdDate' or $field == 'editedDate')
            {
                $display = "formatTime(\$$module->$field, 'Y-m-d')";
            }
            elseif($fieldObj->control == 'select' or $fieldObj->control == 'radio' or $fieldObj->control == 'checkbox')
            {
                if($fieldObj->options == 'user' or $fieldObj->options == 'dept')
                {
                    $options = "{$fieldObj->options}List";
                }
                elseif(is_array($fieldObj->options))
                {
                    $options = "lang->$module->{$field}List";
                }
                else
                {
                    $options = "{$field}List";
                }
                if($fieldObj->control == 'select' or $fieldObj->control == 'radio')
                {
                    $display = "zget(\$$options, \$$module->$field)";
                }
                elseif($fieldObj->control == 'checkbox')
                {
                    $display = "<?php foreach(explode(',', \$$module->$field) as \$$field) echo zget(\$$options, \$$field) . ' ';?>";
                }
            }
            if($field == 'file') $fieldObj->position = 'info';
            if($fieldObj->position == 'info')
            {
                if($field == 'file')
                {
                    $mainContent .= "\n        <p><?php echo \$this->fetch('file', 'printFiles', array('files' => \${$module}->files, 'fieldset' => 'false'));?></p>";
                }
                else
                {
                    if($fieldObj->control == 'checkbox')
                    {
                        $mainContent .= "\n        <p>";
                        $mainContent .= "\n          <?php echo \$lang->$module->$field . \$lang->colon;?>";
                        $mainContent .= "\n          $display";
                        $mainContent .= "\n        </p>";
                    }
                    else
                    {
                        $mainContent .= "\n        <p><?php echo \$lang->$module->$field . \$lang->colon . $display;?></p>";
                    }
                }
            }
            else
            {
                $thClass = $i == 0 ? " class='w-80px'" : '';
                $sideContent .= "\n          <tr>";
                $sideContent .= "\n            <th$thClass><?php echo \$lang->$module->$field;?></th>";
                if($fieldObj->control == 'checkbox')
                {
                    $sideContent .= "\n            <td>$display</td>";
                }
                else
                {
                    $sideContent .= "\n            <td><?php echo $display;?></td>";
                }
                $sideContent .= "\n          </tr>";
                $i++;
            }
        }

        $result = array();
        $result['find']    = array('%MAINCONTENT%', '%PAGEACTIONS%', '%SIDECONTENT%');
        $result['replace'] = array($mainContent, $pageActions, $sideContent);

        return $result;
    }

    /**
     * Get view file of a operate action, such as create or edit or other actions.
     *
     * @param  string $module
     * @param  object $action
     * @param  array  $fields
     * @param  string $idField
     * @access public
     * @return string
     */
    public function getOperateView($module = '', $action = null, $fields = array(), $idField = 'id')
    {
        $formAction   = '';
        $tableContent = '';
        $pageActions  = '';

        if($action->action == 'create')
        {
            $formAction = " action='<?php echo inlink('create');?>'";
        }
        else
        {
            $formAction = " action='<?php echo inlink('$action->action', \"{$module}ID=\${$module}->$idField\");?>'";
        }

        $i = 0;
        foreach($fields as $fieldObj)
        {
            if(!$fieldObj->show) continue;
            if($action->action == 'create' && $fieldObj->field == 'primaryKey') continue;

            $field = $fieldObj->field;
            $value = $fieldObj->defaultValue;
            switch($value)
            {
            case 'today'       :
            case 'now'         :
            case 'currentTime' : $value = $fieldObj->control == 'date' ? "date('Y-m-d')" : "date('Y-m-d H:i:s')";
                break;
            case 'actor'       :
            case 'currentUser' : $value = "\$this->app->user->account";
                break;
            case 'currentDept' : $value = "\$this->app->user->dept";
                break;
            }

            if(!$value) $value = "''";
            if($action->action != 'create')
            {
                if($value == "''")
                {
                    $value = "\$$module->$field";
                }
                else
                {
                    $value = "\$$module->$field ? \$$module->$field : $value";
                }
            }

            $control = $this->buildControlForView($module, $fieldObj, $value);
            $thClass = $i == 0 ? " class='w-100px'" : '';
            $tdClass = $i == 0 && $action->open != 'modal' ? " class='w-p50'" : '';

            $tableContent .= "\n    <tr>";
            if($field == 'file')
            {
                $tableContent .= "\n      <th$thClass><?php echo \$lang->files;?></th>";
                $tableContent .= "\n      <td$tdClass><?php echo \$this->fetch('file', 'buildForm');?></td>";
            }
            else
            {
                $tableContent .= "\n      <th$thClass><?php echo \$lang->$module->$field;?></th>";
                $tableContent .= "\n      <td$tdClass>$control</td>";
            }
            $tableContent .= "\n      <td></td>";
            $tableContent .= "\n    </tr>";

            $i++;
        }

        $pageActions .= "\n    <?php echo html::submitButton();?>";
        if($action->open == 'modal')
        {
            $pageActions .= "\n    <?php echo html::commonButton(\$lang->close, 'btn', \"data-dismiss='modal'\");?>";
        }
        else
        {
            $pageActions .= "\n    <?php echo html::backButton();?>";
        }

        $result = array();
        $result['find']    = array('%FORMACTION%', '%TABLECONTENT%', '%PAGEACTIONS%');
        $result['replace'] = array($formAction, $tableContent, $pageActions);

        return $result;
    }

    /**
     * Build control.
     *
     * @param  string $module
     * @param  int    $fieldObj
     * @param  string $value
     * @access public
     * @return string
     */
    public function buildControlForView($module = '', $fieldObj = null, $value = '')
    {
        $field   = $fieldObj->field;
        $options = "{$field}List";
        if($fieldObj->options == 'user' or $fieldObj->options == 'dept')
        {
            $options = "\${$fieldObj->options}List";
        }
        elseif(is_array($fieldObj->options))
        {
            $options = "\$this->lang->$module->{$field}List";
        }

        switch($fieldObj->control)
        {
        case 'input':
            return "<?php echo html::input('$field', $value, \"class='form-control'\");?>";
        case 'textarea':
            return "<?php echo html::textarea('$field', $value, \"rows='1' class='form-control'\");?>";
        case 'select':
            return "<?php echo html::select('$field', $options, $value, \"class='form-control chosen'\");?>";
        case 'radio':
            return "<div id='$field' class='checkboxDIV'><?php echo html::radio('$field', $options, $value);?></div>";
        case 'checkbox':
            return "<div id='$field' class='radioDIV'><?php echo html::checkbox('$field', $options, $value);?></div>";
        case 'date':
            return "<?php echo html::input('$field', $value, \"class='form-control form-date'\");?>";
        case 'datetime':
            return "<?php echo html::input('$field', $value, \"class='form-control form-datetime'\");?>";
        case 'file':
            return "<?php \$this->fetch('file', 'buildForm');?>";
        default :
            return "<label><?php echo $value;?></label><?php echo html::hidden('$field', $value);?>";
        }
    }

    public function createDefaultFields($projectID, $module, $table)
    {
        $sql         = array();
        $fields      = array();
        $tableFields = array();

        $project = $this->loadModel('project')->getById($projectID);
        $user    = $this->app->user->account;
        $now     = helper::now();
        if($table)
        {
            $aiField     = '';
            $primaryKey  = '';
            $tableFields = $this->project->getTableFields("$project->dbName.`$table`");
            foreach($tableFields as $key => $tableField)
            {
                $field = new stdclass();
                $field->module      = $module->id;
                $field->field       = $tableField->field;
                $field->name        = $tableField->field;
                $field->control     = 'input';
                $field->options     = '[]';
                $field->default     = $tableField->default;
                $field->canSearch   = 1;
                $field->createdBy   = $user;
                $field->createdDate = $now;

                /* Mark aiColumn */
                if(stripos($tableField->extra, 'auto_increment') !== false)
                {
                    $aiField = $tableField->field;
                    $field->control = 'label';
                }

                if($tableField->key == 'PRI') $primaryKey = $tableField->field;

                if(stripos($tableField->type, 'text') !== false)
                {
                    $field->control = 'textarea';
                }
                elseif(stripos($tableField->type, 'datetime') !== false or stripos($tableField->type, 'timestamp') !== false)
                {
                    $field->control = 'datetime';
                }
                elseif(stripos($tableField->type, 'date') !== false)
                {
                    $field->control = 'date';
                }
                $fields[$tableField->field] = $field;

                $tableFields[$key] = $tableField->field;
            }
        }
        else
        {
            $sql[] = "CREATE TABLE IF NOT EXISTS $project->dbName.`$project->dbPrefix$module->module` ( ";
        }

        foreach($this->config->workflow->defaultFields as $name => $param)
        {
            if($aiField && $aiField != 'id' && $name == 'id') continue;

            $field = new stdclass();
            $field->module      = $module->id;
            $field->field       = $name;
            $field->name        = $this->lang->workflowfield->defaultFields[$name];
            $field->control     = $this->config->workflow->defaultControls[$name];
            $field->options     = $this->config->workflow->defaultOptions[$name];
            $field->default     = $this->config->workflow->defaultValues[$name];
            $field->canSearch   = $name == 'deleted' ? 0 : 1;
            $field->createdBy   = $user;
            $field->createdDate = $now;
            if(is_array($field->options)) $field->options = helper::jsonEncode($field->options);

            $fields[$name] = $field;

            if($table)
            {
                $param = str_ireplace('auto_increment', '', $param);
                if(in_array($name, $tableFields))
                {
                    /* If the default fields had be in the table, replace them. */
                    if($name == 'id' && $aiField == 'id') $param .= ' AUTO_INCREMENT';
                    $sql[] = "ALTER TABLE $project->dbName.`$table` CHANGE `$name` `$name` $param;";
                }
                else
                {
                    /* Else add default fields into the table. */
                    $sql[] = "ALTER TABLE $project->dbName.`$table` ADD `$name` $param;";
                }
            }
            else
            {
                $sql[] = "`$name` $param, ";
            }
        }

        foreach($fields as $field)
        {
            $this->dao->insert(TABLE_WORKFLOWFIELD)->data($field)->autoCheck()->exec();
        }
        $this->dao->update(TABLE_WORKFLOWFIELD)->set('`order` = `id`')->where('module')->eq($module->id)->exec();

        if($table)
        {
            if(!$aiField)
            {
                /* Change id column to auto_increment column. */
                if($primaryKey)
                {
                    $sql[] = "ALTER TABLE $project->dbName.`$table` ADD UNIQUE `id` (`id`);";
                }
                else
                {
                    $sql[] = "ALTER TABLE $project->dbName.`$table` ADD PRIMARY KEY (`id`);";
                }
                $sql[] = "ALTER TABLE $project->dbName.`$table` CHANGE `id` `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT FIRST;";
            }
            /* Rename table. */
            $sql[] = "RENAME TABLE $project->dbName.`$table` TO $project->dbName.`$project->dbPrefix$table`;";
        }
        else
        {
            $sql[] = $this->config->workflow->defaultIndexes . ") ENGINE=MyISAM DEFAULT CHARSET=utf8";
        }

        try
        {
            $sql = implode('', $sql);
            if(!$this->dbh->query($sql))
            {
                return array('result' => 'fail', 'message' => $this->lang->workflow->error->createTableFail);
            }
        }
        catch(PDOException $exception)
        {
            return array('result' => 'fail', 'message' => $this->lang->workflow->error->createTableFail);
        }

        return true;
    }

    public function createDefaultActions($moduleID = 0)
    {
        $action = new stdclass();
        $action->module = $moduleID;
        foreach($this->config->workflow->defaultActions as $actionName)
        {
            $action->action   = $actionName;
            $action->name     = $this->lang->workflowaction->defaultActions[$actionName];
            $action->position = $this->config->workflow->defaultPositions[$actionName];
            $action->show     = $actionName == 'delete' ? 'dropdownlist' : 'direct';

            $this->createAction($action);
        }
    }

    public function createDefaultMenu($moduleID = 0)
    {
        $menu = new stdclass();
        $menu->module = $moduleID;
        $menu->label  = $this->lang->workflowmenu->default['all'];
        $menu->params = array();
        foreach($this->config->workflowmenu->defaultParams['all']['field'] as $i => $key)
        {
            $menu->params[$i]['key']      = $key;
            $menu->params[$i]['operator'] = $this->config->workflowmenu->defaultParams['all']['operator'][$i];
            $menu->params[$i]['value']    = $this->config->workflowmenu->defaultParams['all']['value'][$i];
        }
        $this->createModuleMenu($moduleID, $menu);
    }

    /**
     * Set js of a action or a module.
     *
     * @param  string $type
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function setJS($type = 'module', $id = 0)
    {
        $data = fixer::input('post')
            ->stripTags('js', $this->config->allowedTags)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->get();

        $table = $type == 'module' ? TABLE_WORKFLOW : TABLE_WORKFLOWACTION;

        $this->dao->update($table)->data($data, $skip = 'uid')->autoCheck()->where('id')->eq($id)->exec();

        return !dao::isError();
    }

    /**
     * Set css of a action or a module.
     *
     * @param  string $type
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function setCSS($type = 'module', $id = 0)
    {
        $data = fixer::input('post')
            ->stripTags('css', $this->config->allowedTags)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->get();

        $table = $type == 'module' ? TABLE_WORKFLOW : TABLE_WORKFLOWACTION;

        $this->dao->update($table)->data($data, $skip = 'uid')->autoCheck()->where('id')->eq($id)->exec();

        return !dao::isError();
    }

    /**
     * Check sql vars.
     *
     * @param  string $sql
     * @param  string $sign
     * @access public
     * @return array
     */
    public function checkSqlVar($sql = '', $sign = '\$')
    {
        $sql = $sql . ' ';
        preg_match_all("/{$sign}(\w+)/i", $sql, $out);
        return array_unique($out[1]);
    }

    /**
     * replace defined table names.
     *
     * @param  string $sql
     * @access public
     * @return void
     */
    public function replaceTableNames($sql)
    {
        if(preg_match_all("/TABLE_[A-Z]+/", $sql, $out))
        {
            rsort($out[0]);
            foreach($out[0] as $table)
            {
                if(!defined($table)) continue;
                $sql = str_replace($table, trim(constant($table), '`'), $sql);
            }
        }
        $sql = preg_replace("/= *'\!/U", "!='", $sql);
        return $sql;
    }

    /**
     * Check input sql and vars.
     *
     * @param  string $sql
     * @param  array  $vars
     * @access public
     * @return array || bool
     */
    public function checkSqlAndVars($sql = '', $vars = array())
    {
        $sqlVars = $this->checkSqlVar($sql, '\$');
        if($sqlVars)
        {
            foreach($sqlVars as $sqlVar)
            {
                if(isset($vars[$sqlVar])) $sql = str_replace("'$" . $sqlVar . "'", $this->dbh->quote($vars[$sqlVar]), $sql);
            }
        }
        $formVars = $this->checkSqlVar($sql, '\#');
        if($formVars)
        {
            foreach($formVars as $formVar)
            {
                if(isset($vars[$formVar])) $sql = str_replace("'#" . $formVar . "'", $this->dbh->quote($vars[$formVar]), $sql);
            }
        }
        $recordVars = $this->checkSqlVar($sql, '\@');
        if($recordVars)
        {
            foreach($recordVars as $recordVar)
            {
                if(isset($vars[$recordVar])) $sql = str_replace("'@" . $formVar . "'", $this->dbh->quote($vars[$recordVar]), $sql);
            }
        }

        $sql = $this->replaceTableNames($sql);

        try
        {
            $dataList = $this->dbh->query($sql)->fetchAll();
        }
        catch(PDOException $exception)
        {
            return $this->lang->workflow->error->wrongSQL . str_replace("'", "\'", $exception->getMessage());
        }
        return true;
    }

    /**
     * Get sql and vars.
     *
     * @param  int    $moduleID
     * @param  int    $fieldID
     * @param  int    $actionID
     * @access public
     * @return object
     */
    public function getSqlAndVars($moduleID = 0, $fieldID = 0, $actionID = 0)
    {
        $data = $this->dao->select('*')->from(TABLE_WORKFLOWSQL)
            ->where('module')->eq($moduleID)
            ->beginIF($fieldID)->andWhere('field')->eq($fieldID)->fi()
            ->beginIF($actionID)->andWhere('action')->eq($actionID)->fi()
            ->limit(1)
            ->fetch();
        if($data) $data->vars = json_decode($data->vars);
        return $data;
    }

    /**
     * Create sql and vars.
     *
     * @param  int    $moduleID
     * @param  int    $fieldID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function createSqlAndVars($moduleID = 0, $fieldID = 0, $actionID = 0)
    {
        $sqlVars   = $this->post->sqlVars;
        $varValues = $this->post->varValues;
        if(!$sqlVars) $sqlVars = array();
        foreach($sqlVars as $varName => $sqlVar)
        {
            $sqlVar = json_decode($sqlVar);
            if(!empty($varValues[$varName])) $sqlVar->default = $varValues[$varName];
            $sqlVars[$varName] = $sqlVar;
        }

        $data = new stdclass();
        $data->module      = $moduleID;
        $data->field       = $fieldID;
        $data->action      = $actionID;
        $data->sql         = $this->post->sql;
        $data->vars        = helper::jsonEncode($sqlVars);
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();
        $this->dao->insert(TABLE_WORKFLOWSQL)->data($data)->autoCheck()->exec();

        unset($_SESSION['sqlVars']);
    }

    /**
     * Delete sql and vars.
     *
     * @param  int    $moduleID
     * @param  int    $fieldID
     * @param  int    $actionID
     * @access public
     * @return bool
     */
    public function deleteSqlAndVars($moduleID = 0, $fieldID = 0, $actionID = 0)
    {
        $this->dao->delete()->from(TABLE_WORKFLOWSQL)
            ->where('module')->eq($moduleID)
            ->beginIF($fieldID)->andWhere('field')->eq($fieldID)->fi()
            ->beginIF($actionID)->andWhere('action')->eq($actionID)->fi()
            ->exec();
        return !dao::isError();
    }

    /**
     * Get field list.
     *
     * @param  int     $moduleID
     * @param  string  $orderBy
     * @access public
     * @return array
     */
    public function getFieldList($moduleID, $orderBy = 'order')
    {
        $fields = $this->dao->select('*')->from(TABLE_WORKFLOWFIELD)
            ->where('module')->eq($moduleID)
            ->orderBy($orderBy)
            ->fetchAll('id');

        foreach($fields as $field)
        {
            $field->sql     = '';
            $field->sqlVars = array();
            if($field->options == 'sql')
            {
                $data = $this->getSqlAndVars($field->module, $field->id);
                if($data)
                {
                    $field->sql     = $data->sql;
                    $field->sqlVars = $data->vars;
                }
            }
            elseif($field->options != 'user' && $field->options != 'dept' && !is_int($field->options))
            {
                $field->options = json_decode($field->options, true);
            }
        }

        return $fields;
    }

    /**
     * Get field pairs.
     *
     * @param  int     $moduleID
     * @param  string  $orderBy
     * @access public
     * @return array
     */
    public function getFieldPairs($moduleID = 0, $orderBy = 'order')
    {
        $fields = $this->dao->select('field, name')->from(TABLE_WORKFLOWFIELD)
            ->where(1)
            ->beginIF($moduleID)->andWhere('module')->eq($moduleID)->fi()
            ->orderBy($orderBy)
            ->fetchPairs();

        return array('' => '') + $fields;
    }

    /**
     * Get field options.
     *
     * @param  object $field
     * @access public
     * @return array
     */
    public function getFieldOptions($field = null)
    {
        $options = (array)$field->options;
        if(isset($field->control) && $field->control == 'select') $options = array('' => '') + $options;
        if($field->options == 'sql')
        {
            return $this->getOptionsBySql($field->sql, $field->sqlVars);
        }
        elseif($field->options == 'user')
        {
            return $this->loadModel('user')->getPairs('nodeleted,noclosed');
        }
        elseif($field->options == 'dept')
        {
            return $this->loadModel('tree')->getPairs($category = 0, $type = 'dept');
        }
        elseif(is_int($field->options))
        {
            $datasource = $this->getDatasourceById($field->options);
            if(!$datasource) return array();

            if($datasource->type == 'option')
            {
                $options = json_decode($datasource->datasource, true);
            }
            elseif($datasource->type == 'system')
            {
                $module = $datasource->module;
                $method = $datasource->method;

                $defaultParams = $this->getDefaultParams($module, $method);
                foreach($datasource->params as $param)
                {
                    if(isset($defaultParams[$param->name])) $defaultParams[$param->name] = $param->value;
                }

                $className = $module . 'model';
                $class     = new $className();
                $options = call_user_func_array(array($class, $method), $defaultParams);

                if(!is_array($options)) return array();

                foreach($options as $option)
                {
                    if(is_object($option) || is_array($option))
                    {
                        return array();
                    }
                }
            }
            elseif($datasource->type == 'sql')
            {
                return $this->getOptionsBySql($datasource->sql);
            }
            elseif($datasource->type == 'func')
            {
            }
        }
        return $options;
    }

    /**
     * Get options by sql and sqlvars.
     *
     * @param  string $sql
     * @param  array  $sqlVars
     * @access public
     * @return array
     */
    public function getOptionsBySql($sql = '', $sqlVars = array())
    {
        foreach($sqlVars as $sqlVar)
        {
            $sql = str_replace("'$" . $sqlVar->varName . "'", $this->dbh->quote($sqlVar->default), $sql);
        }
        $sql = $this->replaceTableNames($sql);

        try
        {
            $options  = array('' => '');
            $dataList = $this->dbh->query($sql)->fetchAll();
            foreach($dataList as $data)
            {
                $data = (array)$data;
                if(count($data) > 1)
                {
                    $key   = current($data);
                    $value = next($data);
                    $options[$key] = $value;
                }
                elseif(count($data) > 0)
                {
                    $key   = current($data);
                    $value = current($data);
                    $options[$key] = $value;
                }
            }

            return $options;
        }
        catch(PDOException $exception)
        {
            return array();
        }
    }

    /**
     * Get default params of a method.
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return array
     */
    public function getDefaultParams($module = '', $method = '')
    {
        $params = array();

        $defaultValueFiles = glob($this->app->getTmpRoot() . "defaultvalue/*.php");
        if($defaultValueFiles) foreach($defaultValueFiles as $file) include $file;

        $model = $this->loadModel($module);
        $methodReflect = new reflectionMethod($model, $method);

        foreach($methodReflect->getParameters() as $param)
        {
            $name    = $param->getName();
            $default = '';
            if(isset($paramDefaultValue[$module][$method][$name]))
            {
                $default = $paramDefaultValue[$module][$method][$name];
            }
            elseif($param->isDefaultValueAvailable())
            {
                $default = $param->getDefaultValue();
            }
            $params[$name] = $default;
        }
        return $params;
    }

    /**
     * Create a field.
     *
     * @param  int    $moduleID
     * @access public
     * @return bool|array
     */
    public function createField($moduleID)
    {
        $module = $this->getById($moduleID);
        if(!$module) return false;

        $project = $this->loadModel('project')->getById($module->project);
        if(!$project) return false;

        $orderField = $this->getFieldById($this->post->order);

        $field = fixer::input('post')
            ->add('module', $moduleID)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->setForce('options', '[]')
            ->setForce('order', $orderField->order + 1)
            ->remove('optionType, sql, varValues, sqlVars')
            ->get();

        if(strpos('select,radio,checkbox', $field->control) !== false)
        {
            if($this->post->optionType == 'sql')
            {
                $result = $this->checkSqlAndVars($this->post->sql, $this->post->varValues);
                if($result !== true) return array('result' => 'fail', 'message' => array('sql' => $result));
            }
            if($this->post->optionType == 'custom')
            {
                $options = array();
                foreach($this->post->options['value'] as $key => $value)
                {
                    if(empty($value)) continue;
                    if(empty($this->post->options['text'][$key])) continue;

                    $options[$value] = $this->post->options['text'][$key];
                }
                if(empty($options))
                {
                    return array('result' => 'fail', 'message' => array('optionsDIV' => $this->lang->workflow->error->emptyOptions));
                }
                $field->options = helper::jsonEncode($options);
            }
            else
            {
                $field->options = $this->post->optionType;
            }
        }

        if($field->default)
        {
            foreach($this->config->workflowfield->lengthList as $length => $controlList)
            {
                if(strpos($controlList, ',' . $field->control . ',') !== false and strlen($field->default) > $length)
                {
                    return array('result' => 'fail', 'message' => sprintf($this->lang->workflowfield->defaultValue, $length));
                }
            }
        }
        $this->dao->insert(TABLE_WORKFLOWFIELD)->data($field)->autoCheck()
            ->check('field', 'unique', "module={$field->module}")
            ->batchCheck($this->config->workflowfield->require->create, 'notempty')
            ->exec();

        if(dao::isError()) return false;

        $fieldID = $this->dao->lastInsertId();

        $query    = "ALTER TABLE $project->dbName.`$project->dbPrefix$module->module` ADD `{$field->field}` {$this->config->workflowfield->controlTypeList[$field->control]} NOT NULL";
        if($field->default != '') $query .= " DEFAULT '{$field->default}'";
        $query   .= " AFTER `{$orderField->field}`";
        if(!$this->dbh->query($query)) return false;

        /* Update other fields's order. */
        $this->dao->update(TABLE_WORKFLOWFIELD)->set('`order` = `order` + 1')
            ->where('id')->ne($fieldID)
            ->andWhere('`order`')->ge($field->order)
            ->exec();

        /* Create sql and vars. */
        if($this->post->optionType == 'sql') $this->createSqlAndVars($moduleID, $fieldID);

        return $fieldID;
    }

    /**
     * Update a field.
     *
     * @param  int    $fieldID
     * @access public
     * @return bool|array
     */
    public function updateField($fieldID)
    {
        $field = $this->getFieldByID($fieldID);
        if(!$field) return false;

        $module = $this->getById($field->module);
        if(!$module) return false;

        $project = $this->loadModel('project')->getById($module->project);
        if(!$project) return false;

        $orderField = $this->getFieldById($this->post->order);

        $data = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->setForce('options', '[]')
            ->setIF($this->post->order != $this->post->oldOrder, 'order', $orderField->order + 1)
            ->removeIF($this->post->order == $this->post->oldOrder, 'order')
            ->remove('optionType, oldOrder, sql, varValues, sqlVars')
            ->get();

        if($data->default)
        {
            foreach($this->config->workflowfield->lengthList as $length => $controlList)
            {
                if(strpos($controlList, ',' . $data->control . ',') !== false and strlen($data->default) > $length)
                {
                    return array('result' => 'fail', 'message' => sprintf($this->lang->workflowfield->defaultValue, $length));
                }
            }
        }

        if(strpos('select,radio,checkbox', $data->control) !== false)
        {
            if($this->post->optionType == 'sql')
            {
                $result = $this->checkSqlAndVars($this->post->sql, $this->post->varValues);
                if($result !== true) return array('result' => 'fail', 'message' => array('sql' => $result));
            }
            if($this->post->optionType == 'custom')
            {
                $options = array();
                foreach($this->post->options['value'] as $key => $value)
                {
                    if(empty($value)) continue;
                    if(empty($this->post->options['text'][$key])) continue;

                    $options[$value] = $this->post->options['text'][$key];
                }
                if(empty($options))
                {
                    return array('result' => 'fail', 'message' => array('optionsDIV' => $this->lang->workflow->error->emptyOptions));
                }
                $data->options = helper::jsonEncode($options);
            }
            else
            {
                $data->options = $this->post->optionType;
            }
        }

        $this->dao->update(TABLE_WORKFLOWFIELD)->data($data)->autoCheck()
            ->batchCheck($this->config->workflowfield->require->edit, 'notempty')
            ->where('id')->eq($fieldID)
            ->exec();

        if(dao::isError()) return false;

        /* Update other fields's order. */
        if(isset($data->order) && $data->order != $field->order)
        {
            $this->dao->update(TABLE_WORKFLOWFIELD)->set('`order` = `order` + 1')
                ->where('id')->ne($fieldID)
                ->andWhere('`order`')->ge($data->order)
                ->exec();
        }

        if($field->field != $data->field || $field->control != $data->control || $field->default != $data->default || (isset($data->order) && $data->order != $field->order))
        {
            $query    = "ALTER TABLE $project->dbName.`$project->dbPrefix$module->module` CHANGE `{$field->field}` `$data->field` {$this->config->workflowfield->controlTypeList[$data->control]} NOT NULL";
            if($data->default) $query .= " DEFAULT '{$data->default}'";
            $query   .= " AFTER `{$orderField->field}`";
            if(!$this->dbh->query($query)) return false;
        }

        if($field->field != $data->field)
        {
            $this->dao->update(TABLE_WORKFLOWLAYOUT)->set('field')->eq($data->field)
                ->where('module')->eq($field->module)
                ->andWhere('field')->eq($field->field)
                ->exec();
        }

        if($this->post->optionType == 'sql')
        {
            $this->deleteSqlAndVars($field->module, $fieldID);
            /* Create sql and vars. */
            $this->createSqlAndVars($field->module, $fieldID);
        }

        return true;
    }

    /**
     * Delete a field.
     *
     * @param  int    $fieldID
     * @access public
     * @return bool
     */
    public function deleteField($fieldID)
    {
        $field = $this->getFieldByID($fieldID);
        if(!$field) return false;

        $this->dao->delete()->from(TABLE_WORKFLOWFIELD)->where('id')->eq($fieldID)->exec();
        if(dao::isError()) return false;

        $this->dao->update(TABLE_WORKFLOWFIELD)->set('`order` = `order` - 1')->where('`order`')->gt($field->order)->exec();

        $this->dao->delete()->from(TABLE_WORKFLOWLAYOUT)
            ->where('module')->eq($field->module)
            ->andWhere('field')->eq($field->field)
            ->exec();
        if(dao::isError()) return false;

        $module   = $this->getByID($field->module);
        $project  = $this->loadModel('project')->getById($module->project);
        $query    = "ALTER TABLE $project->dbName.`$project->dbPrefix$module->module` DROP `{$field->field}`";
        if(!$this->dbh->query($query)) return false;

        if($field->options == 'sql') $this->deleteSqlAndVars($field->module, $fieldID);
        return true;
    }

    /**
     * Get an action by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getActionById($id = 0)
    {
        $action = $this->dao->select('*')->from(TABLE_WORKFLOWACTION)->where('id')->eq($id)->fetch();
        if($action)
        {
            $action->conditions = json_decode($action->conditions);
            $action->results    = json_decode($action->results);
        }

        return $action;
    }

    /**
     * Get action by module and action.
     *
     * @param  int    $moduleID
     * @param  string $action
     * @access public
     * @return object
     */
    public function getActionByModuleAndAction($moduleID = 0, $action = '')
    {
        $action = $this->dao->select('*')->from(TABLE_WORKFLOWACTION)
            ->where('deleted')->eq('0')
            ->andWhere('module')->eq($moduleID)
            ->andWhere('action')->eq($action)
            ->fetch();

        if($action)
        {
            $action->conditions = json_decode($action->conditions);
            $action->results    = json_decode($action->results);
        }

        return $action;
    }

    /**
     * Get action list.
     *
     * @param  int    $module
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getActionList($module = 0, $orderBy = 'id')
    {
        $actions = $this->dao->select('*')->from(TABLE_WORKFLOWACTION)
            ->where('deleted')->eq('0')
            ->beginIF($module)->andWhere('module')->eq($module)->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');

        foreach($actions as $action)
        {
            $action->conditions = json_decode($action->conditions);
            $action->results    = json_decode($action->results);
        }

        return $actions;
    }

    /**
     * Get action pairs.
     *
     * @param  int    $module
     * @access public
     * @return void
     */
    public function getActionPairs($module = 0)
    {
        $actions = $this->dao->select("id, name")->from(TABLE_WORKFLOWACTION)
            ->where('deleted')->eq('0')
            ->beginIF($module)->andWhere('module')->eq($module)->fi()
            ->fetchPairs();

        return array('' => '') + $actions;
    }

    /**
     * Get fields of an action.
     *
     * @param  int    $moduleID
     * @param  int    $actionID
     * @param  bool   $decodeOptions
     * @access public
     * @return void
     */
    public function getActionFields($moduleID = 0, $actionID = 0, $decodeOptions = true)
    {
        $actionFields = $this->dao->select("t1.*, t2.id as fieldID, t2.name, t2.control, t2.options, t2.default, t2.placeholder, t2.canSearch, t2.desc")
            ->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
            ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.module=t2.module and t1.field=t2.field')
            ->where('t1.module')->eq($moduleID)
            ->andWhere('t1.action')->eq($actionID)
            ->orderBy('t1.order')
            ->fetchAll('field');

        foreach($actionFields as $field)
        {
            $field->sql     = '';
            $field->sqlVars = array();
            if($field->options == 'sql')
            {
                $data = $this->getSqlAndVars($moduleID, $field->fieldID);
                if($data)
                {
                    $field->sql     = $data->sql;
                    $field->sqlVars = $data->vars;
                }
            }
            elseif($field->options != 'user' && $field->options != 'dept' && !is_int($field->options))
            {
                $field->options = json_decode($field->options, true);
            }
        }

        $action         = $this->getActionById($actionID);
        $fieldPairs     = $this->getFieldPairs($moduleID);
        $fieldList      = $this->getFieldList($moduleID);
        $moduleChildren = $this->getPairs($projectID = 0, $moduleID);

        $fields = array();
        if(empty($actionFields))
        {
            foreach($fieldList as $field)
            {
                if(!$field) continue;

                $options = $field->options;
                if($decodeOptions)
                {
                    $options = $this->getFieldOptions($field);
                    if($field->control == 'date' || $field->control == 'datetime')
                    {
                        $options = $this->lang->workflowaction->layout->defaultTime + $options;
                    }
                    elseif($field->options == 'user')
                    {
                        $options = $this->lang->workflowaction->layout->defaultUser + $options;
                    }
                    elseif($field->options == 'dept')
                    {
                        $options = $this->lang->workflowaction->layout->defaultDept + $options;
                    }
                }

                $fields[$field->field] = new stdclass();
                $fields[$field->field]->field        = $field->field;
                $fields[$field->field]->name         = $field->name;
                $fields[$field->field]->control      = $field->control;
                $fields[$field->field]->show         = '1';
                $fields[$field->field]->width        = 'auto';
                $fields[$field->field]->position     = '';
                $fields[$field->field]->defaultValue = '';
                $fields[$field->field]->rules        = '';
                $fields[$field->field]->options      = $options;
            }

            if($action->action == 'browse')
            {
                $fields['actions'] = new stdclass();
                $fields['actions']->field        = 'actions';
                $fields['actions']->name         = $this->lang->actions;
                $fields['actions']->control      = '';
                $fields['actions']->show         = '1';
                $fields['actions']->width        = '120';
                $fields['actions']->position     = '';
                $fields['actions']->defaultValue = '';
                $fields['actions']->rules        = '';
                $fields['actions']->options      = '';
            }
            if($action->action != 'browse')
            {
                $fields['file'] = new stdclass();
                $fields['file']->field        = 'file';
                $fields['file']->name         = $this->lang->files;
                $fields['file']->control      = '';
                $fields['file']->show         = '0';
                $fields['file']->width        = 'auto';
                $fields['file']->position     = '';
                $fields['file']->defaultValue = '';
                $fields['file']->rules        = '';
                $fields['file']->options      = '';

                foreach($moduleChildren as $id => $name)
                {
                    $fields[$id] = new stdclass();
                    $fields[$id]->field        = $id;
                    $fields[$id]->name         = $name;
                    $fields[$id]->control      = '';
                    $fields[$id]->show         = '1';
                    $fields[$id]->width        = 'auto';
                    $fields[$id]->position     = '';
                    $fields[$id]->defaultValue = '';
                    $fields[$id]->rules        = '';
                    $fields[$id]->options      = '';
                }
            }
        }
        else
        {
            foreach($actionFields as $key => $field)
            {
                if(!$field) continue;

                $options = $field->options;
                if($decodeOptions)
                {
                    $options = $this->getFieldOptions($field);
                    if($field->control == 'date' || $field->control == 'datetime')
                    {
                        $options = $this->lang->workflowaction->layout->defaultTime + $options;
                    }
                    elseif($field->options == 'user')
                    {
                        $options = $this->lang->workflowaction->layout->defaultUser + $options;
                    }
                    elseif($field->options == 'dept')
                    {
                        $options = $this->lang->workflowaction->layout->defaultDept + $options;
                    }
                }

                $fields[$key] = new stdclass();
                $fields[$key]->name         = is_numeric($key) ? zget($moduleChildren, $key) : zget($fieldPairs, $key);
                $fields[$key]->field        = $field->field;
                $fields[$key]->control      = $field->control;
                $fields[$key]->show         = $action == 'browse' && !$field->canSearch ? '0' : '1';
                $fields[$key]->width        = $field->width ? $field->width : 'auto';
                $fields[$key]->position     = $field->position;
                $fields[$key]->defaultValue = empty($field->defaultValue) ? $field->default : $field->defaultValue;
                $fields[$key]->rules        = $field->rules;
                $fields[$key]->options      = $options;

                if($key == 'file')    $fields[$key]->name = $this->lang->files;
                if($key == 'actions') $fields[$key]->name = $this->lang->actions;
            }

            foreach($fieldList as $id => $field)
            {
                if(!$field or isset($actionFields[$field->field])) continue;

                if($decodeOptions)
                {
                    $options = $this->getFieldOptions($field);
                    if($field->control == 'date' || $field->control == 'datetime')
                    {
                        $options = $this->lang->workflowaction->layout->defaultTime + $options;
                    }
                    elseif($field->options == 'user')
                    {
                        $options = $this->lang->workflowaction->layout->defaultUser + $options;
                    }
                    elseif($field->options == 'dept')
                    {
                        $options = $this->lang->workflowaction->layout->defaultDept + $options;
                    }
                }

                $fields[$field->field] = new stdclass();
                $fields[$field->field]->field        = $field->field;
                $fields[$field->field]->name         = $field->name;
                $fields[$field->field]->control      = $field->control;
                $fields[$field->field]->show         = '0';
                $fields[$field->field]->width        = 'auto';
                $fields[$field->field]->position     = '';
                $fields[$field->field]->defaultValue = '';
                $fields[$field->field]->rules        = '';
                $fields[$field->field]->options      = $options;
            }

            if(!isset($fields['file']) and $action->action != 'browse')
            {
                $fields['file'] = new stdclass();
                $fields['file']->field        = 'file';
                $fields['file']->name         = $this->lang->files;
                $fields['file']->control      = '';
                $fields['file']->show         = '0';
                $fields['file']->width        = 'auto';
                $fields['file']->position     = '';
                $fields['file']->defaultValue = '';
                $fields['file']->rules        = '';
                $fields['file']->options      = '';
            }

            if($action->action != 'browse')
            {
                foreach($moduleChildren as $id => $moduleChild)
                {
                    if(!$id or isset($actionFields[$id])) continue;

                    $fields[$id] = new stdclass();
                    $fields[$id]->field        = $id;
                    $fields[$id]->name         = $moduleChild;
                    $fields[$id]->control      = '';
                    $fields[$id]->show         = '0';
                    $fields[$id]->width        = 'auto';
                    $fields[$id]->position     = '';
                    $fields[$id]->defaultValue = '';
                    $fields[$id]->rules        = '';
                    $fields[$id]->options      = array();
                }
            }
        }

        return $fields;
    }

    /**
     * Create an action.
     *
     * @param  object $action
     * @access public
     * @return int
     */
    public function createAction($action = null)
    {
        if(!$action) $action = fixer::input('post')->get();
        $action->action      = strtolower(str_replace(' ', '', $action->action));
        $action->conditions  = '[]';
        $action->results     = '[]';
        $action->createdBy   = $this->app->user->account;
        $action->createdDate = helper::now();

        if(!empty($action->action) && !validater::checkREG($action->action, '|^[A-Za-z]+$|')) dao::$errors['action'][] = sprintf($this->lang->workflow->error->wrongCode, $this->lang->workflowaction->action);

        if(dao::isError()) return;

        $this->dao->insert(TABLE_WORKFLOWACTION)->data($action)
            ->autoCheck()
            ->batchCheck($this->config->workflow->require->createaction, 'notempty')
            ->batchCheck($this->config->workflowaction->uniqueFields, 'unique', "module={$action->module}")
            ->exec();

        return $this->dao->lastInsertId();
    }

    /**
     * Update an action.
     *
     * @param  int    $actionID
     * @access public
     * @return array
     */
    public function updateAction($actionID = 0)
    {
        $oldAction = $this->getActionById($actionID);

        $action = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->setIF($this->post->action, 'action', strtolower(str_replace(' ', '', $this->post->action)))
            ->get();

        if(!empty($action->action) && !validater::checkREG($action->action, '|^[A-Za-z]+$|')) dao::$errors['action'][] = sprintf($this->lang->workflow->error->wrongCode, $this->lang->workflowaction->action);

        if(dao::isError()) return;

        $this->dao->update(TABLE_WORKFLOWACTION)->data($action)
            ->where('id')->eq($actionID)
            ->autoCheck()
            ->batchCheckIF(in_array($oldAction->action, $this->config->workflow->defaultActions), 'name', 'notempty')
            ->batchCheckIF(!in_array($oldAction->action, $this->config->workflow->defaultActions), $this->config->workflow->require->editaction, 'notempty')
            ->batchCheck($this->config->workflowaction->uniqueFields, 'unique', "id!={$actionID} AND module={$action->module}")
            ->exec();

        return commonModel::createChanges($oldAction, $action);
    }

    /**
     * Browse conditions of an action.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function adminCondition($actionID = 0)
    {
        $condition = new stdclass();
        $condition->conditionType = $this->post->conditionType;
        $condition->fields        = array();
        $condition->sql           = $this->post->sql;
        $condition->sqlResult     = $this->post->sqlResult;

        if($this->post->conditionType == 'data')
        {
            foreach($this->post->field as $key => $data)
            {
                if(empty($data)) continue;
                $field = new stdclass();
                $field->field    = $data;
                $field->operator = $this->post->operator[$key];
                $field->param    = $this->post->param[$key];
                $condition->fields[] = $field;
            }
        }

        $condition = helper::jsonEncode($condition);
        $this->dao->update(TABLE_WORKFLOWACTION)->set('conditions')->eq($condition)->where('id')->eq($actionID)->exec();
    }

    /**
     * Get module menu by id.
     *
     * @param  int    $menuID
     * @access public
     * @return object|bool
     */
    public function getModuleMenuByID($menuID)
    {
        $menu = $this->dao->select('*')->from(TABLE_WORKFLOWMENU)->where('id')->eq($menuID)->fetch();
        if(!$menu) return false;

        $menu->params = json_decode($menu->params, true);
        return $menu;
    }

    /**
     * Get module menu list.
     *
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function getModuleMenuList($moduleID)
    {
        $moduleMenuList = $this->dao->select('*')->from(TABLE_WORKFLOWMENU)
            ->where('module')->eq($moduleID)
            ->andWhere('deleted')->eq('0')
            ->orderBy('order')
            ->fetchAll('id');

        foreach($moduleMenuList as $id => $moduleMenu) $moduleMenuList[$id]->params = json_decode($moduleMenu->params, true);

        return $moduleMenuList;
    }

    /**
     * Create module menu.
     *
     * @param  int    $moduleID
     * @access public
     * @return bool
     */
    public function createModuleMenu($moduleID, $menu)
    {
        if(!$menu)
        {
            $menu = new stdclass();
            $menu->module = $moduleID;
            $menu->label  = $this->post->label;
            $menu->params = array();
            foreach($this->post->keys as $i => $key)
            {
                if(empty($key)) continue;

                $menu->params[$i]['key']      = $key;
                $menu->params[$i]['operator'] = $this->post->operators[$i];
                $menu->params[$i]['value']    = $this->post->values[$i];
            }
        }
        $menu->createdBy   = $this->app->user->account;
        $menu->createdDate = helper::now();

        if(empty($menu->params))
        {
            dao::$errors = $this->lang->workflow->error->emptyParams;
            return false;
        }

        $menu->params = helper::jsonEncode($menu->params);

        $this->dao->insert(TABLE_WORKFLOWMENU)->data($menu)
            ->autoCheck()
            ->batchCheck($this->config->workflow->require->createModuleMenu, 'notempty')
            ->exec();

        return !dao::isError();
    }

    /**
     * Update module menu.
     *
     * @param  int    $menuID
     * @access public
     * @return bool
     */
    public function updateModuleMenu($menuID)
    {
        $menu = new stdclass();
        $menu->label      = $this->post->label;
        $menu->editedBy   = $this->app->user->account;
        $menu->editedDate = helper::now();

        $menu->params = array();
        foreach($this->post->keys as $i => $key)
        {
            if(empty($key)) continue;

            $menu->params[$i]['key']      = $key;
            $menu->params[$i]['operator'] = $this->post->operators[$i];
            $menu->params[$i]['value']    = $this->post->values[$i];
        }

        if(empty($menu->params))
        {
            dao::$errors = $this->lang->workflow->error->emptyParams;
            return false;
        }

        $menu->params = helper::jsonEncode($menu->params);

        $this->dao->update(TABLE_WORKFLOWMENU)->data($menu)
            ->autoCheck()
            ->batchCheck($this->config->workflow->require->editModuleMenu, 'notempty')
            ->where('id')->eq($menuID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Custom layout for an action.
     *
     * @param  int    $moduleID
     * @param  int    $actionID
     * @access public
     * @return bool
     */
    public function customLayout($moduleID, $actionID)
    {
        $this->dao->delete()->from(TABLE_WORKFLOWLAYOUT)->where('action')->eq($actionID)->exec();

        $order = 1;
        foreach($this->post->show as $field => $show)
        {
            if(!$show) continue;

            $data = new stdclass();
            $data->module       = $moduleID;
            $data->action       = $actionID;
            $data->field        = $field;
            $data->width        = (isset($this->post->width[$field]) and $this->post->width[$field] != 'auto') ? $this->post->width[$field] : 0;
            $data->order        = $order;
            $data->position     = isset($this->post->position[$field]) ? $this->post->position[$field] : '';
            $data->defaultValue = isset($this->post->defaultValue[$field]) ? (is_array($this->post->defaultValue[$field]) ? implode(',', $this->post->defaultValue[$field]) : $this->post->defaultValue[$field]) : '';
            $data->rules        = isset($this->post->rules[$field]) ? implode(',', $this->post->rules[$field]) : '';
            $this->dao->insert(TABLE_WORKFLOWLAYOUT)->data($data)->autoCheck()->exec();
            $order ++;
        }

        if(dao::isError()) return;

        if($this->post->children)
        {
            foreach($this->post->children as $moduleID => $child)
            {
                $order = 1;
                foreach($child['show'] as $field => $show)
                {
                    if(!$show) continue;

                    $data = new stdclass();
                    $data->module       = $moduleID;
                    $data->action       = $actionID;
                    $data->field        = $field;
                    $data->width        = (isset($child['width'][$field]) and $child['width'][$field] != 'auto') ? $child['width'][$field] : 0;
                    $data->order        = $order;
                    $data->position     = '';
                    $data->defaultValue = isset($child['defaultValue'][$field]) ? (is_array($child['defaultValue'][$field]) ? implode(',', $child['defaultValue'][$field]) : $child['defaultValue'][$field]) : '';
                    $data->rules        = isset($child['rules'][$field]) ? implode(',', $child['rules'][$field]) : '';
                    $this->dao->insert(TABLE_WORKFLOWLAYOUT)->data($data)->autoCheck()->exec();
                    $order ++;
                }
            }
        }

        return !dao::isError();
    }

    /**
     * Get field param value by paramType.
     *
     * @param  string $paramType
     * @access public
     * @return string
     */
    public function getParamRealValue($param = '')
    {
        switch($param)
        {
        case 'today'       : return date('Y-m-d');
        case 'now'         :
        case 'currentTime' : return date('Y-m-d H:i:s');
        case 'actor'       :
        case 'currentUser' : return $this->app->user->account;
        case 'currentDept' : return $this->app->user->dept;
        }
    }

    /**
     * Check result.
     *
     * @param  object $result
     * @access public
     * @return array || string
     */
    public function checkResult($result = null)
    {
        if(isset($this->config->workflowresult->tables[$result->table]))
        {
            $table = $this->config->workflowresult->tables[$result->table];
        }
        else
        {
            $module   = $this->getById($module = $result->table);
            $project  = $this->loadModel('project')->getById($module->project);
            $table    = "$project->dbName.$project->dbPrefix$module->module";
        }
        if($result->action == 'insert')
        {
            $sql = "INSERT INTO `{$table}` (";
        }
        elseif($result->action == 'update')
        {
            $sql = "UPDATE `{$table}` ";
        }
        else
        {
            $sql = "DELETE FROM `{$table}` ";
        }

        if($result->action == 'insert')
        {
            $values = '';
            foreach($result->fields as $key => $field)
            {
                $sql .= "`{$field->field}`, ";
                if($field->paramType == 'form')
                {
                    $values .= "'#" . $field->param . "', ";
                }
                elseif($field->paramType == 'record')
                {
                    $values .= "'@" . $field->param . "', ";
                }
                elseif(strpos('today, now, actor, deptManager', $field->paramType) !== false)
                {
                    $values .= "'$" . $field->param . "', ";
                }
                else
                {
                    $values .= $this->dbh->quote($field->param ) . ', ';
                }
            }
            $values = rtrim($values, ', ');
            $sql    = rtrim($sql, ', ') . ") VALUES ({$values}) ";
        }
        elseif($result->action == 'update')
        {
            $sql .= ' SET ';
            foreach($result->fields as $key => $field)
            {
                if($field->paramType == 'form')
                {
                    $sql .= " `{$field->field}` = '" . '#' . $field->param . "', ";
                }
                elseif($field->paramType == 'record')
                {
                    $sql .= " `{$field->field}` = '" . '@' . $field->param . "', ";
                }
                elseif(strpos('today, now, actor, deptManager', $field->paramType) !== false)
                {
                    $sql .= " `{$field->field}` = '" . '$' . $field->param . "', ";
                }
                else
                {
                    $sql .= " `{$field->field}` = " . $this->dbh->quote($field->param) . ', ';
                }
            }
            $sql = rtrim($sql, ', ');
        }
        if($result->action != 'insert')
        {
            $sql .= ' WHERE 1 AND (1 ';
            foreach($result->wheres as $where)
            {
                $operator = $this->lang->workflowaction->operatorList[$where->operator];
                if($where->paramType == 'form')
                {
                    $sql .= ' ' . strtoupper($where->logicalOperator) . " `{$where->field}` {$operator} '" . '#' . $where->param . "' ";
                }
                elseif($where->paramType == 'record')
                {
                    $sql .= ' ' . strtoupper($where->logicalOperator) . " `{$where->field}` {$operator} '" . '@' . $where->param . "' ";
                }
                elseif(strpos('today, now, actor, deptManager', $where->paramType) !== false)
                {
                    $sql .= ' ' . strtoupper($where->logicalOperator) . " `{$where->field}` {$operator} '" . '$' . $where->param . "' ";
                }
                else
                {
                    $sql .= ' ' . strtoupper($where->logicalOperator) . " `{$where->field}` {$operator} " . $this->dbh->quote($where->param) . ' ';
                }
            }
            $sql .= ')';
        }

        if($result->action == 'insert')
        {
            $checkSql = $sql;
        }
        else
        {
            /* Add a false condition to ensure the sql won't be execute. */
            $checkSql = $sql . ' AND 1 = 2';
        }

        $sqlVars = $this->checkSqlVar($checkSql, '\$');
        foreach($sqlVars as $var)
        {
            $value    = $this->getParamRealValue($var);
            $checkSql = str_replace("'$" . $var . "'", $this->dbh->quote($value), $checkSql);
        }

        $formVars = $this->checkSqlVar($checkSql, '#');
        foreach($formVars as $var)
        {
            $value    = '';
            $checkSql = str_replace("'#" . $var . "'", $this->dbh->quote($value), $checkSql);
        }

        $recordVars = $this->checkSqlVar($checkSql, '@');
        foreach($recordVars as $var)
        {
            $value    = '';
            $checkSql = str_replace("'@" . $var . "'", $this->dbh->quote($value), $checkSql);
        }

        try
        {
            $this->dbh->exec($checkSql);
            if($result->action == 'insert')
            {
                $id = $this->dbh->lastInsertID();
                $this->dbh->exec("delete from `{$table}` where `id` = '{$id}'");
            }
        }
        catch(PDOException $exception)
        {
            $error = $this->lang->workflow->error->wrongSQL . str_replace("'", "\'", $exception->getMessage());
            return array('result' => 'fail', 'message' => $error);
        }

        return $sql;
    }

    /**
     * Create a result.
     *
     * @param  object $action
     * @access public
     * @return void
     */
    public function createResult($action = null)
    {
        $errors     = array();
        $fields     = array();
        $sqlVars    = array();
        $formVars   = array();
        $recordVars = array();

        $result = new stdclass();
        $result->action        = $this->post->action;
        $result->table         = $this->post->table;
        $result->conditionType = $this->post->conditionType;
        if($result->action != 'delete')
        {
            foreach($this->post->fields['field'] as $key => $value)
            {
                if(!$value) continue;
                $field = new stdclass();
                $field->field     = $value;
                $field->paramType = $this->post->fields['paramType'][$key];
                if($field->paramType == 'form')
                {
                    $field->param     = $this->post->fields['param'][$key];
                    $formVars[$value] = $this->post->fields['param'][$key];
                }
                elseif($field->paramType == 'record')
                {
                    $field->param       = $this->post->fields['param'][$key];
                    $recordVars[$value] = $this->post->fields['param'][$key];
                }
                elseif(!empty($field->paramType) && strpos('today, now, actor, deptManager', $field->paramType) !== false)
                {
                    $field->param    = $field->paramType;
                    $sqlVars[$value] = $field->paramType;
                }
                else
                {
                    $field->param = $this->post->fields['param'][$key];
                }
                $fields[] = $field;
            }
            if(empty($fields)) $errors['fieldsfield'] = sprintf($this->lang->error->notempty, $this->lang->workflowfield->field);
        }
        $result->fields = $fields;

        $conditions = array();
        if($this->post->condition == 1)
        {
            if($result->conditionType == 'data')
            {
                foreach($this->post->conditions['field'] as $key => $field)
                {
                    if(!$field) continue;
                    $condition = new stdclass();
                    $condition->field           = $field;
                    $condition->logicalOperator = $this->post->conditions['logicalOperator'][$key];
                    $condition->operator        = $this->post->conditions['operator'][$key];
                    $condition->paramType       = $this->post->conditions['paramType'][$key];
                    if($condition->paramType == 'form')
                    {
                        $condition->param = $this->post->conditions['param'][$key];
                        $formVars[$field] = $this->post->conditions['param'][$key];
                    }
                    elseif($condition->paramType == 'record')
                    {
                        $condition->param   = $this->post->conditions['param'][$key];
                        $recordVars[$field] = $this->post->conditions['param'][$key];
                    }
                    elseif(!empty($condition->paramType) && strpos('today, now, actor, deptManager', $condition->paramType) !== false)
                    {
                        $condition->param = $condition->paramType;
                        $sqlVars[$field]  = $condition->paramType;
                    }
                    else
                    {
                        $condition->param = $this->post->conditions['param'][$key];
                    }
                    $conditions[] = $condition;
                }
            }
            elseif($result->conditionType == 'sql')
            {
                if(!$this->post->sql)
                {
                    $errors['sql'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->sql);
                }
                else
                {
                    $vars      = array();
                    $varValues = array();
                    foreach($this->post->varName as $key => $varName)
                    {
                        if(!$varName) continue;

                        $var = new stdclass();
                        $var->varName   = $varName;
                        $var->paramType = $this->post->paramType[$key];
                        $var->param     = $this->post->param[$key];

                        $varValues[$varName] = $var->param;
                        $vars[] = $var;
                    }

                    $checkResult = $this->checkSqlAndVars($this->post->sql, $varValues);
                    if($checkResult !== true) $errors['sql'] = $checkResult;

                    $conditions = new stdclass();
                    $conditions->sql       = $this->post->sql;
                    $conditions->sqlVars   = $vars;
                    $conditions->sqlResult = $this->post->sqlResult;
                }
            }
        }
        $result->conditions = $conditions;

        $wheres = array();
        if($result->action != 'insert')
        {
            foreach($this->post->wheres['field'] as $key => $field)
            {
                if(!$field) continue;
                $where = new stdclass();
                $where->field           = $field;
                $where->logicalOperator = $this->post->wheres['logicalOperator'][$key];
                $where->operator        = $this->post->wheres['operator'][$key];
                $where->paramType       = $this->post->wheres['paramType'][$key];
                if($where->paramType == 'form')
                {
                    $where->param     = $this->post->wheres['param'][$key];
                    $formVars[$field] = $this->post->wheres['param'][$key];
                }
                elseif($where->paramType == 'record')
                {
                    $where->param       = $this->post->wheres['param'][$key];
                    $recordVars[$field] = $this->post->wheres['param'][$key];
                }
                elseif(!empty($where->paramType) && strpos('today, now, actor, deptManager', $where->paramType) !== false)
                {
                    $where->param    = $where->paramType;
                    $sqlVars[$field] = $where->paramType;
                }
                else
                {
                    $where->param = $this->post->wheres['param'][$key];
                }
                $wheres[] = $where;
            }
            if(empty($wheres)) $errors['wheresfield'] = sprintf($this->lang->error->notempty, $this->lang->workflowfield->field);
        }
        $result->wheres = $wheres;

        if(!empty($errors)) return array('result' => 'fail', 'message' => $errors);

        $checkResult = $this->checkResult($result);
        if(is_array($checkResult)) return $checkResult;

        $result->sql        = $checkResult;
        $result->sqlVars    = $sqlVars;
        $result->formVars   = $formVars;
        $result->recordVars = $recordVars;

        $action->results[] = $result;
        $this->dao->update(TABLE_WORKFLOWACTION)
            ->set('results')->eq(helper::jsonEncode($action->results))
            ->autoCheck()
            ->where('id')->eq($action->id)
            ->exec();
        return !dao::isError();
    }

    /**
     * Update a result.
     *
     * @param  object $action
     * @param  int    $resultKey
     * @access public
     * @return void
     */
    public function updateResult($action = null, $resultKey = 0)
    {
        $errors     = array();
        $fields     = array();
        $sqlVars    = array();
        $formVars   = array();
        $recordVars = array();

        $result = new stdclass();
        $result->action        = $this->post->action;
        $result->table         = $this->post->table;
        $result->conditionType = $this->post->conditionType;
        $result->message       = $this->post->message;
        if($result->action != 'delete')
        {
            foreach($this->post->fields['field'] as $key => $value)
            {
                if(!$value) continue;
                $field = new stdclass();
                $field->field     = $value;
                $field->paramType = $this->post->fields['paramType'][$key];
                if($field->paramType == 'form')
                {
                    $field->param     = $this->post->fields['param'][$key];
                    $formVars[$value] = $this->post->fields['param'][$key];
                }
                elseif($field->paramType == 'record')
                {
                    $field->param       = $this->post->fields['param'][$key];
                    $recordVars[$value] = $this->post->fields['param'][$key];
                }
                elseif(!empty($field->paramType) && strpos('today, now, actor, deptManager', $field->paramType) !== false)
                {
                    $field->param    = $field->paramType;
                    $sqlVars[$value] = $field->paramType;
                }
                else
                {
                    $field->param = $this->post->fields['param'][$key];
                }
                $fields[] = $field;
            }
            if(empty($fields)) $errors['fieldsfield'] = sprintf($this->lang->error->notempty, $this->lang->workflowfield->field);
        }
        $result->fields = $fields;

        $conditions = array();
        if($this->post->condition == 1)
        {
            if($result->conditionType == 'data')
            {
                foreach($this->post->conditions['field'] as $key => $field)
                {
                    if(!$field) continue;
                    $condition = new stdclass();
                    $condition->field           = $field;
                    $condition->logicalOperator = $this->post->conditions['logicalOperator'][$key];
                    $condition->operator        = $this->post->conditions['operator'][$key];
                    $condition->paramType       = $this->post->conditions['paramType'][$key];
                    if($condition->paramType == 'form')
                    {
                        $condition->param = $this->post->conditions['param'][$key];
                        $formVars[$field] = $this->post->conditions['param'][$key];
                    }
                    elseif($condition->paramType == 'record')
                    {
                        $condition->param   = $this->post->conditions['param'][$key];
                        $recordVars[$field] = $this->post->conditions['param'][$key];
                    }
                    elseif(!empty($condition->paramType) && strpos('today, now, actor, deptManager', $condition->paramType) !== false)
                    {
                        $condition->param = $condition->paramType;
                        $sqlVars[$field]  = $condition->paramType;
                    }
                    else
                    {
                        $condition->param = $this->post->conditions['param'][$key];
                    }
                    $conditions[] = $condition;
                }
            }
            elseif($result->conditionType == 'sql')
            {
                if(!$this->post->sql)
                {
                    $errors['sql'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->sql);
                }
                else
                {
                    $vars      = array();
                    $varValues = array();
                    foreach($this->post->varName as $key => $varName)
                    {
                        if(!$varName) continue;

                        $var = new stdclass();
                        $var->varName   = $varName;
                        $var->paramType = $this->post->paramType[$key];
                        $var->param     = $this->post->param[$key];

                        $varValues[$varName] = $var->param;
                        $vars[] = $var;
                    }

                    $checkResult = $this->checkSqlAndVars($this->post->sql, $varValues);
                    if($checkResult !== true) $errors['sql'] = $checkResult;

                    $conditions = new stdclass();
                    $conditions->sql       = $this->post->sql;
                    $conditions->sqlVars   = $vars;
                    $conditions->sqlResult = $this->post->sqlResult;
                }
            }
        }
        $result->conditions = $conditions;

        $wheres = array();
        if($result->action != 'insert')
        {
            foreach($this->post->wheres['field'] as $key => $field)
            {
                if(!$field) continue;
                $where = new stdclass();
                $where->field           = $field;
                $where->logicalOperator = $this->post->wheres['logicalOperator'][$key];
                $where->operator        = $this->post->wheres['operator'][$key];
                $where->paramType       = $this->post->wheres['paramType'][$key];
                if($where->paramType == 'form')
                {
                    $where->param     = $this->post->wheres['param'][$key];
                    $formVars[$field] = $this->post->wheres['param'][$key];
                }
                elseif($where->paramType == 'record')
                {
                    $where->param       = $this->post->wheres['param'][$key];
                    $recordVars[$field] = $this->post->wheres['param'][$key];
                }
                elseif(!empty($where->paramType) && strpos('today, now, actor, deptManager', $where->paramType) !== false)
                {
                    $where->param    = $where->paramType;
                    $sqlVars[$field] = $where->paramType;
                }
                else
                {
                    $where->param = $this->post->wheres['param'][$key];
                }
                $wheres[] = $where;
            }
            if(empty($wheres)) $errors['wheresfield'] = sprintf($this->lang->error->notempty, $this->lang->workflowfield->field);
        }
        $result->wheres = $wheres;

        if(!empty($errors)) return array('result' => 'fail', 'message' => $errors);

        $checkResult = $this->checkResult($result);
        if(is_array($checkResult)) return $checkResult;

        $result->sql        = $checkResult;
        $result->sqlVars    = $sqlVars;
        $result->formVars   = $formVars;
        $result->recordVars = $recordVars;

        $action->results[$resultKey] = $result;
        $this->dao->update(TABLE_WORKFLOWACTION)
            ->set('results')->eq(helper::jsonEncode($action->results))
            ->autoCheck()
            ->where('id')->eq($action->id)
            ->exec();
        return !dao::isError();
    }

    /**
     * Delete a result.
     *
     * @param  int    $actionID
     * @param  int    $key
     * @access public
     * @return void
     */
    public function deleteResult($actionID = 0, $key = 0)
    {
        $action = $this->getActionById($actionID);
        if(isset($action->results[$key])) unset($action->results[$key]);

        $this->dao->update(TABLE_WORKFLOWACTION)
            ->set('results')->eq(helper::jsonEncode($action->results))
            ->autoCheck()
            ->where('id')->eq($action->id)
            ->exec();
        return !dao::isError();
    }

    /**
     * Set notice.
     *
     * @param  int    $actionID
     * @access public
     * @return bool
     */
    public function setNotice($actionID)
    {
        $toList = ',' . trim(implode(',', $this->post->toList), ',') . ',';
        $this->dao->update(TABLE_WORKFLOWACTION)->set('toList')->eq($toList)->where('id')->eq($actionID)->exec();
        return !dao::isError();
    }

    /**
     * Operate
     *
     * @param  object $module
     * @param  object $action
     * @param  int    $dataID
     * @param  object $chilData
     * @access public
     * @return void
     */
    public function operate($module = null, $action = null, $dataID = 0, $childData = null)
    {
        $user = $this->app->user->account;
        $now  = helper::now();

        $editorFields = isset($this->config->flow->editor->displaylayout['id']) ? trim($this->config->flow->editor->displaylayout['id'], ',') : '';
        if($childData)
        {
            $data = $childData;
        }
        else
        {
            $data = fixer::input('post')
                ->stripTags($editorFields, $this->config->allowedTags)
                ->remove('file,files,labels,children')
                ->removeIF($action->position == 'menu', 'dataID')
                ->get();
        }

        if($action->position == 'menu' && $action->action != 'create' && $this->post->dataID == '')
        {
            dao::$errors['dataID'] = sprintf($this->lang->error->notempty, $module->name);
            return false;
        }

        $fields = $this->getActionFields($module->id, $action->id);
        foreach($fields as $field)
        {
            if(!$field->show || $field->control != 'checkbox') continue;

            if(isset($data->{$field->field}))
            {
                /* If the checkboxes are checked, encode them. */
                $data->{$field->field} = helper::jsonEncode($data->{$field->field});
            }
        }

        $skip = 'uid';
        foreach($data as $field => $value)
        {
            /* If the field don't show in view, skip it. */
            if(empty($fields[$field]->show))
            {
                unset($data->$field);
                $skip .= ',' . $field;
            }
            else
            {
                $data->$field = htmlspecialchars_decode($value);
            }

            if(in_array($value, $this->config->workflow->variables)) $data->$field = $this->getParamRealValue($value);
        }

        $project = $this->loadModel('project')->getById($module->project);
        if($action->action == 'create')
        {
            $data->createdBy   = $user;
            $data->createdDate = $now;

            if(isset($this->config->flow->editor->displaylayout['id'])) $data = $this->loadModel('file')->processEditor($data, $this->config->flow->editor->displaylayout['id']);
            $dao  = $this->dao->insert("$project->dbName.`$project->dbPrefix$module->module`")->data($data);
            /* Check rules of fields. */
            foreach($data as $field => $value)
            {
                /* If the field don't show in view, don't check it. */
                if(empty($fields[$field]->show) || empty($fields[$field]->rules)) continue;

                $rules = explode(',', $fields[$field]->rules);
                foreach($rules as $rule)
                {
                    if(empty($rule)) continue;
                    if($rule == 'phone')
                    {
                        $dao->checkIF(!empty($value), $field, 'length', 20, 7);
                    }
                    elseif(strpos($rule, 'regex_') !== false)
                    {
                        $tmpdao  = clone $dao;
                        $regRule = $this->getRuleById(str_replace('regex_', '', $rule));
                        $dao = $tmpdao;
                        $dao->check($field, 'reg', $regRule->rule);
                    }
                    elseif(strpos($rule, 'func_') !== false)
                    {
                        $funcRule = $this->getRuleById(str_replace('func_', '', $rule));
                        /* To do something. */
                    }
                    else
                    {
                        $dao->check($field, $rule);
                    }
                }
            }
            $dao->autoCheck($skip)->exec();
            $dataID = $this->dao->lastInsertId();
        }
        else
        {
            $oldData = $this->getModuleDataById($module->id, $dataID, $decode = false);

            $data->editedBy   = $user;
            $data->editedDate = $now;

            $data = $this->loadModel('file')->processEditor($data, $this->config->flow->editor->displaylayout['id']);
            $dao  = $this->dao->update("$project->dbName.`$project->dbPrefix$module->module`")->data($data);
            foreach($data as $field => $value)
            {
                /* If the field don't show in view, don't check it. */
                if(empty($fields[$field]->show) || empty($fields[$field]->rules)) continue;

                $rules = explode(',', $fields[$field]->rules);
                foreach($rules as $rule)
                {
                    if(empty($rule)) continue;
                    if($rule == 'phone')
                    {
                        $dao->checkIF(!empty($value), $field, 'length', 20, 7);
                    }
                    elseif(strpos($rule, 'regex_') !== false)
                    {
                        $tmpdao  = clone $dao;
                        $regRule = $this->getRuleById(str_replace('regex_', '', $rule));
                        $dao = $tmpdao;
                        $dao->check($field, 'REG', $regRule->rule);
                    }
                    elseif(strpos($rule, 'func_') !== false)
                    {
                        $funcRule = $this->getRuleById(str_replace('func_', '', $rule));
                        /* To do something. */
                    }
                    else
                    {
                        $condition = $rule == 'unique' ? "`id` != {$dataID}" : null;
                        $dao->check($field, $rule, $condition);
                    }
                }
            }
            $dao->where('id')->eq($dataID)->autoCheck($skip)->exec();
        }

        /* Check action results. */
        if(!empty($action->results))
        {
            $message = '';
            $data = $this->getModuleDataById($module->id, $dataID, $decode = false);
            foreach($action->results as $result)
            {
                if(!$result->sql) continue;

                $canResult = true;
                if(is_object($result->conditions))
                {
                    $sql = $result->conditions->sql;
                    foreach($result->conditions->sqlVars as $sqlVar)
                    {
                        if($sqlVar->paramType == 'form')
                        {
                            if(!isset($this->post->{$sqlVar->param}))
                            {
                                $canResult = false;
                                break;
                            }
                            $sqlVar->param = $this->post->{$sqlVar->param};
                        }
                        elseif($sqlVar->paramType == 'record')
                        {
                            if(!isset($data->{$sqlVar->param}))
                            {
                                $canResult = false;
                                break;
                            }
                            $sqlVar->param = $data->{$sqlVar->param};
                        }
                        elseif(!empty($sqlVar->paramType) && strpos('today, now, actor, deptManager', $sqlVar->paramType) !== false)
                        {
                            $sqlVar->param = $this->getParamRealValue($condition->paramType);
                        }
                        $sql = str_replace("'$" . $sqlVar->varName . "'", $this->dbh->quote($sqlVar->param), $sql);
                    }
                    $sql = $this->replaceTableNames($sql);

                    try
                    {
                        $sqlResult = $this->dbh->query($sql)->fetch();
                        if($result->conditions->sqlResult == 'empty')
                        {
                            $canResult = empty($sqlResult);
                        }
                        elseif($result->conditions->sqlResult == 'notempty')
                        {
                            $canResult = !empty($sqlResult);
                        }
                    }
                    catch(PDOException $exception)
                    {
                        $canResult = false;
                    }
                }
                elseif(is_array($result->conditions))
                {
                    foreach($result->conditions as $condition)
                    {
                        if(!$condition->field || !$condition->operator) continue;

                        /* 扩展动作的触发条件中的字段数据源不包含表单数据和当前数据，仅需要判断下列3个类型。 */
                        if(strpos('today, now, actor, deptManager', $condition->paramType) !== false)
                        {
                            $condition->param = $this->getParamRealValue($condition->paramType);
                        }

                        $checkFunc = 'check' . $condition->operator;
                        $curResult = validater::$checkFunc($data->{$condition->field}, $condition->param);
                        $canResult = $condition->logicalOperator == 'and' ? $canResult && $curResult : $canResult || $curResult;
                    }
                }
                if($canResult)
                {
                    $sql = $result->sql;
                    $wherePos = strpos($sql, 'where');
                    /* Replace sql vars. */
                    foreach($result->sqlVars as $field => $var)
                    {
                        $varPos = strpos($sql, "'$" . $var . "'");
                        $value  = $this->getParamRealValue($var);
                        $sql    = str_replace("'$" . $var . "'",  $this->dbh->quote($value), $sql);

                        /* If the result updates the table of current module, add field value to data to create changes. */
                        /* 如果更新的是当前流程对应的表，并且变量是在更新字段中，则把字段的值赋予$data，方便生成历史记录 */
                        if($result->table == $module->id && $wherePos !== false && $varPos < $wherePos) $data->$field = $value;
                    }
                    /* Replace form vars. */
                    foreach($result->formVars as $field => $var)
                    {
                        $varPos = strpos($sql, "'#" . $var . "'");
                        $value  = $this->post->$var;
                        $sql    = str_replace("'#" . $var . "'",  $this->dbh->quote($value), $sql);

                        if($result->table == $module->id && $wherePos !== false && $varPos < $wherePos) $data->$field = $value;
                    }
                    /* Replace record vars. */
                    foreach($result->recordVars as $field => $var)
                    {
                        $varPos = strpos($sql, "'@" . $var . "'");
                        $value  = $data->$var;
                        $sql    = str_replace("'@" . $var . "'",  $this->dbh->quote($value), $sql);

                        if($result->table == $module->id && $wherePos !== false && $varPos < $wherePos) $data->$field = $value;
                    }

                    $this->dbh->exec($sql);
                    $message .= isset($result->message) ? $result->message : '';
                }
            }
        }

        /* 如果传入了子模块数据，跳过. */
        if(!$childData && $this->post->children) $this->updateChildrenData($action, $this->post->children, $dataID);
        if(dao::isError()) return;

        if($action->action == 'create')
        {
            return array('id' => $dataID, 'message' => $message);
        }
        else
        {
            return array('changes' => commonModel::createChanges($oldData, $data), 'message' => $message);
        }
    }

    public function updateChildrenData($parentAction = null, $children = array(), $parentID = 0)
    {
        foreach($children as $childModuleID => $child)
        {
            $module = $this->getById($childModuleID);
            $fields = $this->getFieldPairs($childModuleID);
            $idList = array();

            foreach($child['id'] as $key => $id)
            {
                $dataIsEmpty = true;

                /* Get post data for each field. */
                $data = new stdclass();
                foreach($fields as $field => $name)
                {
                    if(!$field) continue;
                    if(empty($child[$field])) continue;
                    $data->$field = is_array($child[$field][$key]) || is_object($child[$field][$key]) ? helper::jsonEncode($child[$field][$key]) : $child[$field][$key];

                    if($data->$field) $dataIsEmpty = false;
                }
                if($dataIsEmpty) continue;
                $data->parent = $parentID;

                if($data->id)
                {
                    /* 更新数据，调用子模块自己的动作，这样可以执行动作的结果. */
                    $childAction = $this->getActionByModuleAndAction($childModuleID, $action = 'edit');
                    $changes     = $this->operate($module, $childAction, $data->id, $data);
                    $idList[]    = $data->id;
                }
                else
                {
                    /* 插入数据，调用子模块自己的动作，这样可以执行动作的结果. */
                    $childAction = $this->getActionByModuleAndAction($childModuleID, $action = 'create');
                    $dataID      = $this->operate($module, $childAction, $id = 0, $data);
                    $idList[]    = $dataID;
                }
            }
            /* Delete data. */
            //if(!dao::isError())
            //{
            //    /* 删除其他数据 */
            //    /*这里需要添加其他条件以确保删除的数据仅为和该动作关联显示的数据*/
            //    $this->update("$project->dbPrefix$module->module")->set('deleted')->eq('1')
            //        ->where('parent')->eq($parentID)
            //        ->andWhere('id')->ne($idList)
            //        ->exec();
            //}
        }
    }

    /**
     * Build control.
     *
     * @param  string    $field
     * @param  string    $value
     * @param  int       $childModuleID
     * @access public
     * @return string
     */
    public function buildControl($field, $value, $childModuleID = 0)
    {
        if(empty($value)) $value = $field->defaultValue;
        if($field->control == 'date' && $value == '0000-00-00') $value == $field->defaultValue;
        if($field->control == 'datetime' && $value == '0000-00-00 00:00:00') $value == $field->defaultValue;

        if(in_array($value, $this->config->workflow->variables)) $value = $this->getParamRealValue($value);

        $options = $this->getFieldOptions($field);

        $element     = $field->field;
        $multiple    = '';
        $placeholder = '';
        if($childModuleID)
        {
            $element = "children[$childModuleID][{$field->field}][]";
            if($field->control == 'radio') $field->control = 'select';
            if($field->control == 'checkbox')
            {
                $field->control = 'select';
                $multiple = "multiple='multiple'";
            }

            $placeholder = "placeholder='{$field->name}'";
        }
        switch($field->control)
        {
        case 'input':
            return html::input($element, $value, "class='form-control' $placeholder");
        case 'textarea':
            return html::textarea($element, $value, "rows='1' class='form-control' $placeholder");
        case 'select':
            return html::select($element, $options, $value, "class='form-control chosen' $multiple $placeholder");
        case 'radio':
            return "<div id='{$field->field}' class='checkboxDIV'>" . html::radio($element, $options, $value) . '</div>';
        case 'checkbox':
            return "<div id='{$field->field}' class='radioDIV'>" . html::checkbox($element, $options, $value) . '</div>';
        case 'date':
            return html::input($element, $value, "class='form-control form-date' $placeholder");
        case 'datetime':
            return html::input($element, $value, "class='form-control form-datetime' $placeholder");
        default :
            return "<label>$value</label>" . html::hidden($element, $value);
        }
    }

    /**
     * Build operate menu.
     *
     * @param  object $module
     * @param  object $data
     * @param  string $type
     * @access public
     * @return void
     */
    public function buildOperateMenu($module, $data, $type = 'browse')
    {
        if($type != 'menu' && $data->deleted == '1') return '';

        $viewAction = $this->getActionByModuleAndAction($module->id, 'view');

        $dataID       = isset($data->id) ? $data->id : 0;
        $btn          = $type == 'menu' ? 'btn btn-primary' : ($type == 'view' ? 'btn' : '');
        $menu         = $type == 'view' ? "<div class='btn-group'>" : '';
        $dropdownMenu = '';
        $deleteMenu   = '';
        foreach($module->actions as $action)
        {
            if($action->action == 'browse') continue;
            if($action->action == 'view' && $action->open == 'none') continue;
            if($type == 'menu' && $action->action == 'delete') continue;
            if($type == 'menu' && $action->open == 'none') continue;

            if(strpos($action->position, $type) === false) continue;

            $enabled = true;
            if($type != 'menu' && !empty($action->conditions))
            {
                if($action->conditions->conditionType == 'data' and !empty($action->conditions->fields))
                {
                    foreach($action->conditions->fields as $field)
                    {
                        if(!$this->checkCondition($field, $data))
                        {
                            $enabled = false;
                            break;
                        }
                    }
                }
                elseif($action->conditions->conditionType == 'sql')
                {
                    $sql = $this->replaceTableNames($action->conditions->sql);
                    try
                    {
                        $sqlResult = $this->dbh->query($sql)->fetch();
                        if($action->conditions->sqlResult == 'empty')
                        {
                            $enabled = empty($sqlResult);
                        }
                        elseif($action->conditions->sqlResult == 'notempty')
                        {
                            $enabled = !empty($sqlResult);
                        }
                    }
                    catch(PDOException $exception)
                    {
                        $enabled = false;
                    }
                }
            }
            if($enabled)
            {
                if(commonModel::hasPriv($module->module, $action->action))
                {
                    $icon = "<i class='icon-cogs'> </i>";
                    if($action->action == 'create') $icon = "<i class='icon-plus'> </i>";
                    if($action->action == 'edit')   $icon = "<i class='icon-pencil'> </i>";

                    $label       = $type == 'menu' ? $icon . $action->name : $action->name;
                    $loadInModal = $type == 'view' && $viewAction->open == 'modal' && $action->open == 'modal' ? "loadInModal" : '';
                    $reload      = $type != 'menu' && $action->open == 'none'  ? 'reload' : '';
                    $attr        = $type != 'menu' && $action->open == 'modal' && $loadInModal == '' ? "data-toggle='modal'" : '';
                    $link        = html::a(helper::createLink('workflow', 'displaylayout', "mode=preview&moduleID={$module->id}&method={$action->action}&id={$dataID}"), $label, "class='$loadInModal $reload $btn' $attr");

                    if($action->action == 'delete')
                    {
                        $deleteMenu .= $link;
                    }
                    else
                    {
                        if($type == 'browse' && $action->show == 'dropdownlist')
                        {
                            $dropdownMenu .= "<li>" . $link . "</li>";
                        }
                        else
                        {
                            $menu .= $link;
                        }
                    }
                }
                else
                {
                    if($type == 'browse') $dropdownMenu .= html::a('#', $label, "class='$btn' disabled='disabled'");
                    if($type == 'view')   $menu         .= html::a('#', $label, "class='$btn' disabled='disabled'");
                }
            }
        }

        if($type == 'browse' && $dropdownMenu != '')
        {
            $dropdownBefore  = "<div class='dropdown'><a href='javascript::' data-toggle='dropdown'>{$this->lang->more}<span class='caret'> </span></a>";
            $dropdownBefore .= "<ul class='dropdown-menu pull-right'>";
            $dropdownMenu    = $dropdownBefore . $dropdownMenu . "<li>" . $deleteMenu . "</li></ul></div>";
        }
        else
        {
            $dropdownMenu .= $deleteMenu;
        }

        $menu .= $dropdownMenu;
        if($type == 'view') $menu .= '</div>';

        return $menu;
    }

    /**
     * Check a condition is available.
     *
     * @param  int    $condition
     * @param  int    $order
     * @access public
     * @return bool
     */
    public function checkCondition($condition, $data)
    {
        $checkFunc = 'check' . $condition->operator;
        $var = $data->{$condition->field};

        if(in_array($condition->param, $this->config->workflow->variables)) $condition->param = $this->getParamRealValue($var);

        return validater::$checkFunc($var, $condition->param);
    }

    /**
     * Get data of a module by Id.
     *
     * @param  int    $moduleID
     * @param  int    $id
     * @param  bool   $decode
     * @access public
     * @return object
     */
    public function getModuleDataById($moduleID = 0, $id = 0, $decode = true)
    {
        $module  = $this->getById($moduleID);
        $project = $this->loadModel('project')->getById($module->project);
        $fields  = $this->getFieldList($moduleID);
        $data    = $this->dao->select('*')->from("$project->dbName.`$project->dbPrefix$module->module`")->where('id')->eq($id)->fetch();

        if($data)
        {
            if($decode)
            {
                foreach($fields as $field)
                {
                    if($field->control == 'checkbox') $data->{$field->field} = json_decode($data->{$field->field}, true);
                }
            }

            $data->files = $this->loadModel('file', 'sys')->getByObject($module->module, $id);
        }

        return $data;
    }

    /**
     * Get module data list.
     *
     * @param  int    $moduleID
     * @param  string $mode
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getModuleDataList($moduleID = 0, $mode = '', $moduleMenuID = 0, $parent = 0, $orderBy = 'id_desc', $pager = null)
    {
        $module = $this->getById($moduleID);
        $fields = $this->getFieldList($moduleID);

        $query = $module->module . 'Query';
        if($this->session->{$query} == false) $this->session->set($query, ' 1 = 1');
        $$query = $this->loadModel('search')->replaceDynamic($this->session->{$query});

        $menuQuery = '';
        if($moduleMenuID)
        {
            $moduleMenu = $this->getModuleMenuByID($moduleMenuID);

            $menuQuery .= '(1';
            foreach($moduleMenu->params as $param)
            {
                $key      = $param['key'];
                $value    = $param['value'];
                $operator = zget($this->lang->workflowaction->operatorList, $param['operator']);

                if(in_array($value, $this->config->workflow->variables)) $value = $this->getParamRealValue($value);

                $menuQuery .= " AND (1 AND `$key` $operator '$value')";
            }
            $menuQuery .= ')';
        }

        $project  = $this->loadModel('project')->getById($module->project);
        $dataList = $this->dao->select('*')->from("$project->dbName.`$project->dbPrefix$module->module`")
            ->where('deleted')->eq('0')
            ->beginIF($mode == 'search')->andWhere($$query)->fi()
            ->beginIF($moduleMenuID)->andWhere($menuQuery)->fi()
            ->beginIF($parent)->andWhere('parent')->eq($parent)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        foreach($dataList as $data)
        {
            foreach($fields as $field)
            {
                if($field->control == 'checkbox') $data->{$field->field} = json_decode($data->{$field->field});
            }
        }

        return $dataList;
    }

    /**
     * Get module data pairs.
     *
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function getModuleDataPairs($moduleID = 0)
    {
        $module    = $this->getById($moduleID);
        $project   = $this->loadModel('project')->getById($module->project);
        $dataPairs = $this->dao->select('id')->from("$project->dbName.`$project->dbPrefix$module->module`")->where('deleted')->eq('0')->fetchPairs();
        foreach($dataPairs as $key => $data)
        {
            $dataPairs[$key] = $module->name . $data;
        }

        return array('' => '') + $dataPairs;
    }

    /**
     * Get a datasource by id.
     *
     * @param  int    $datasourceID
     * @access public
     * @return objcet
     */
    public function getDatasourceById($datasourceID = 0)
    {
        $datasource = $this->dao->select('*')->from(TABLE_WORKFLOWDATASOURCE)->where('id')->eq($datasourceID)->fetch();
        if($datasource)
        {
            $datasource->options = array();
            $datasource->module     = '';
            $datasource->method     = '';
            $datasource->methodDesc = '';
            $datasource->params     = array();
            $datasource->sql        = '';

            if($datasource->type == 'option')
            {
                $datasource->options = json_decode($datasource->datasource, true);
            }
            elseif($datasource->type == 'system')
            {
                $data = json_decode($datasource->datasource);
                $datasource->module     = $data->module;
                $datasource->method     = $data->method;
                $datasource->methodDesc = $data->methodDesc;
                $datasource->params     = $data->params;
            }
            elseif($datasource->type == 'sql')
            {
                $datasource->sql = $datasource->datasource;
            }
            elseif($datasource->type == 'func')
            {
            }
        }
        return $datasource;
    }

    /**
     * Get datasource list.
     *
     * @param  string $orderBy
     * @param  objcet $pager
     * @access public
     * @return array
     */
    public function getDatasourceList($orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('*')->from(TABLE_WORKFLOWDATASOURCE)
            ->where('deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get datasource pairs.
     *
     * @access public
     * @return array
     */
    public function getDatasourcePairs()
    {
        return array('' => '') + $this->dao->select('id, name')->from(TABLE_WORKFLOWDATASOURCE)
            ->where('deleted')->eq('0')
            ->fetchPairs();
    }

    /**
     * Create a datasource.
     *
     * @access public
     * @return int
     */
    public function createDatasource()
    {
        $datasource = fixer::input('post')
            ->setDefault('datasource', '')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            //->skipSpecial('sql')
            ->get();

        $result = $this->fixDatasource($datasource);
        if(!empty($result))
        {
            dao::$errors = $result;
            return;
        }

        $this->dao->insert(TABLE_WORKFLOWDATASOURCE)->data($datasource, $skip = 'options, module, method, methodDesc, paramName, paramType, paramDesc, paramValue, sql')
            ->autoCheck()
            ->check('datasource', 'notempty')
            ->exec();

        return $this->dao->lastInsertId();
    }

    /**
     * Update a datasource.
     *
     * @param  int    $datasourceID
     * @access public
     * @return array
     */
    public function updateDatasource($datasourceID = 0)
    {
        $oldDatasource = $this->getDatasourceById($datasourceID);

        $datasource = fixer::input('post')
            ->setDefault('datasource', '')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->skipSpecial('sql')
            ->get();

        $result = $this->fixDatasource($datasource);
        if(!empty($result))
        {
            dao::$errors = $result;
            return;
        }

        $this->dao->update(TABLE_WORKFLOWDATASOURCE)->data($datasource, $skip = 'options, module, method, methodDesc, paramName, paramType, paramDesc, paramValue, sql')
            ->where('id')->eq($datasourceID)
            ->autoCheck()
            ->check('datasource', 'notempty')
            ->exec();

        unset($oldDatasource->options);
        unset($datasource->options);

        return commonModel::createChanges($oldDatasource, $datasource);
    }

    /**
     * Fix a datasource.
     *
     * @param  object $datasource
     * @access public
     * @return array
     */
    public function fixDatasource($datasource = object)
    {
        $errors = array();

        if(!$datasource->name) $errors['name'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->name);
        if($datasource->type == 'option')
        {
            $options = array();
            foreach($datasource->options['value'] as $key => $value)
            {
                if(empty($value)) continue;
                if(empty($datasource->options['text'][$key])) continue;

                $options[$value] = $datasource->options['text'][$key];
            }

            if(empty($options)) $errors['options'] = $this->lang->workflow->error->emptyOptions;

            $datasource->datasource = helper::jsonEncode($options);
        }
        elseif($datasource->type == 'system')
        {
            if(!$datasource->method) $errors['method'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->method);
            if(!$datasource->module) $errors['module'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->module);

            $params = array();
            if($this->post->paramName)
            {
                foreach($this->post->paramName as $key => $name)
                {
                    $param = new stdclass();
                    $param->name  = $name;
                    $param->type  = $this->post->paramType[$key];
                    $param->desc  = $this->post->paramDesc[$key];
                    $param->value = $this->post->paramValue[$key];

                    $params[$key] = $param;
                }
            }

            $data = new stdclass();
            $data->module     = $datasource->module;
            $data->method     = $datasource->method;
            $data->methodDesc = $datasource->methodDesc;
            $data->params     = $params;

            $datasource->datasource = helper::jsonEncode($data);
        }
        elseif($datasource->type == 'sql')
        {
            if(!$datasource->sql)
            {
                $errors['sql'] = sprintf($this->lang->error->notempty, $this->lang->workflowdatasource->sql);
            }
            else
            {
                $result = $this->checkSqlAndVars($datasource->sql);
                if($result !== true) $errors['sql'] = $result;

                $datasource->datasource = $datasource->sql;
            }
        }
        elseif($datasource->type == 'func')
        {
        }

        return $errors;
    }

    public function getMethodComments($module = '', $method = '', $methodDescOnly = false)
    {
        $model = $this->loadModel($module);
        $methodReflect = new ReflectionMethod($model, $method);
        $comment = $methodReflect->getDocComment();

        /* Strip the opening and closing tags of the docblock. */
        $comment = substr($comment, 3, -2);

        /* Split into arrays of lines. */
        $comment = preg_split('/\r?\n\r?/', $comment);

        /* Group the lines together by @tags */
        $blocks = array();
        $b = -1;
        foreach ($comment as $line)
        {
            /* Trim asterisks and whitespace from the beginning and whitespace from the end of lines. */
            $line = ltrim(rtrim($line), "* \t\n\r\0\x0B");
            if (isset($line[1]) && $line[0] == '@' && ctype_alpha($line[1]))
            {
                $b++;
                $blocks[] = array();
            } else if($b == -1) {
                $b = 0;
                $blocks[] = array();
            }
            $blocks[$b][] = $line;
        }

        $result = array();
        /* Parse the blocks */
        foreach ($blocks as $block => $body)
        {
            $body = trim(implode("\n", $body));
            if($block == 0 && !(isset($body[1]) && $body[0] == '@' && ctype_alpha($body[1])))
            {
                /* This is the description block */
                if($methodDescOnly) return $body;

                $result['desc'] = $body;
                continue;
            }
            else
            {
                /* This block is tagged */
                if(preg_match('/^@[a-z0-9_]+/', $body, $matches))
                {
                    $tag  = substr($matches[0], 1);
                    $body = substr($body, strlen($tag)+2);
                    if($tag == 'param')
                    {
                        $parts          = preg_split('/\s+/', trim($body), 3);
                        $parts          = array_pad($parts, 3, null);
                        $property       = array('type', 'var', 'desc');
                        $param          = array_combine($property, $parts);
                        $param['var']   = substr($param['var'], 1);
                        $result['param'][$param['var']] = $param;
                    }
                    else
                    {
                        $result[$tag][] = $body;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get a rule by id.
     *
     * @param  int    $ruleID
     * @access public
     * @return object
     */
    public function getRuleById($ruleID = 0)
    {
        return $this->dao->select('*')->from(TABLE_WORKFLOWRULE)->where('id')->eq($ruleID)->fetch();
    }

    /**
     * Get rule list.
     *
     * @param  string $type
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getRuleList($type = '', $orderBy = 'id_desc')
    {
        return $this->dao->select('*')->from(TABLE_WORKFLOWRULE)
            ->where('deleted')->eq('0')
            ->beginIF($type)->andWhere('type')->eq($type)->fi()
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * Get rule pairs.
     *
     * @param  string $type
     * @access public
     * @return array
     */
    public function getRulePairs($type = '')
    {
        return $this->dao->select('id, name')->from(TABLE_WORKFLOWRULE)
            ->where('deleted')->eq('0')
            ->beginIF($type)->andWhere('type')->eq($type)->fi()
            ->orderBy('name')
            ->fetchPairs();
    }

    /**
     * Create a rule.
     *
     * @access public
     * @return int
     */
    public function createRule()
    {
        $rule = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->remove('uid')
            ->get();

        $this->dao->insert(TABLE_WORKFLOWRULE)->data($rule)
            ->autoCheck()
            ->batchCheck($this->config->workflow->require->createRules, 'notempty')
            ->check('name', 'unique')
            ->exec();

        return $this->dao->lastInsertId();
    }

    /**
     * Update a rule.
     *
     * @param  int    $ruleID
     * @access public
     * @return array
     */
    public function updateRule($ruleID = 0)
    {
        $oldRule = $this->getRuleById($ruleID);

        $rule = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->remove('uid')
            ->get();

        $this->dao->update(TABLE_WORKFLOWRULE)->data($rule)
            ->where('id')->eq($ruleID)
            ->autoCheck()
            ->batchCheck($this->config->workflow->require->editRules, 'notempty')
            ->check('name', 'unique', "id!={$ruleID}")
            ->exec();

        return commonModel::createChanges($oldRule, $rule);
    }
}
