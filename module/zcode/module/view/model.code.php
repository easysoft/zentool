<?php
/**
 * The model file of %MODULE% module of %PROJECT%.
 *
 * @copyright   %COPYRIGHT% 
 * @license     %LICENSE%
 * @author      %AUTHOR%
 * @package     %PACKAGE% 
 * @version     %VERSION%
 * @link        %LINK% 
 */
class %MODULE%Model extends model
{
    /**
     * Get %MODULE% by %IDFIELD%.  
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return object 
     */
    public function getById($%MODULE%ID = 0)
    {
        %GETBYID%
    }

    /**
     * Get %MODULE% list. 
     * 
     * @param  string $orderBy 
     * @param  int    $pager 
     * @access public
     * @return array
     */
    public function getList($orderBy = '%IDFIELD%_desc', $pager = null)
    {
        return $this->dao->select('*')->from(%TABLE%)
            ->where('deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('%IDFIELD%');
    }
    
    /**
     * Get %MODULE% pairs. 
     * 
     * @access public
     * @return array
     */
    public function getPairs()
    {
        $dataPairs = array('' => '');
        $dataList  = $this->getList();
        foreach($dataList as $data)
        {
            $data = (array)$data;
            if(count($data) > 1)
            {
                $key   = current($data);
                $value = next($data);
                $dataPairs[$key] = $value;
            }
            elseif(count($data) > 0)
            {
                $key   = current($data);
                $value = current($data);
                $dataPairs[$key] = $value;
            }
        }

        return $dataPairs;
    }

    /**
     * Create a %MODULE%. 
     * 
     * @access public
     * @return int
     */
    public function create()
    {
        $data = fixer::input('post')%CREATEFIXER%->get();
        %CREATEFIX%
        $this->dao->insert(%TABLE%)->data($data%CREATESKIP%)->autoCheck()%CREATECHECK%->exec();
        %CREATERETURN%
    }

    /**
     * Update a %MODULE%. 
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return bool | array
     */
    public function update($%MODULE%ID = 0)
    {
        $oldData = $this->getById($%MODULE%ID);

        $data = fixer::input('post')%EDITFIXER%->get();
        %EDITFIX%
        $this->dao->update(%TABLE%)->data($data%EDITSKIP%)->autoCheck()%EDITCHECK%->where('%IDFIELD%')->eq($%MODULE%ID)->exec();
        if(dao::isError()) return false;
        %EDITRETURN%
    }

    /**
     * Delete a %MODULE%. 
     * 
     * @param  int    $%MODULE%ID
     * @access public
     * @return bool
     */
    public function delete($%MODULE%ID = 0, $null = null)
    {
        $this->dao->update(%TABLE%)->set('deleted')->eq('1')->where('%IDFIELD%')->eq($%MODULE%ID)->exec();
        return !dao::isError();
    }
}
