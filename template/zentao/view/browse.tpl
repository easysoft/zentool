<?php
/**
 * The %ACTION% view file of %MODULE% module of %PROJECT%.
 *
 * @copyright   %COPYRIGHT% 
 * @license     %LICENSE%
 * @author      %AUTHOR%
 * @package     %PACKAGE% 
 * @version     %VERSION%
 * @link        %LINK% 
 */
?>
%PAGEHEADER%
<div id='menuActions'>
  <?php commonModel::printLink('%MODULE%', 'create', '', $lang->create, "class='btn btn-primary'");?>
</div>
<div class='panel'>
  <table class='table table-stripped table-hover tablesorter table-fixedHeader'>
    <thead>
      <tr>
        <?php $vars = "orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>%TABLEHEADER%
      </tr>
    </thead>
    <tbody>
      <?php foreach($%MODULE%List as $%MODULE%):?>
      <tr>%TABLEBODY%
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>  
  <div class='table-footer'><?php echo $pager->show();?></div>
</div>
%PAGEFOOTER%
