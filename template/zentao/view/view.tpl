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
<div class='row-table'>
  <div class='col-main'>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->%MODULE%->info;?></strong></div>
      <div class='panel-body'>%MAINCONTENT%
      </div> 
    </div> 
    <?php echo $this->fetch('action', 'history', "objectType=%MODULE%&objectID=$%MODULE%->%IDFIELD%");?>
    <div class='page-actions'>%PAGEACTIONS%
      <?php echo html::backButton();?>
    </div>
  </div>
  <div class='col-side'>
    <div class='panel'>
      <div class='panel-heading'>
        <strong><i class='icon-file-text-alt'></i> <?php echo $lang->%MODULE%->basic;?></strong>
      </div>
      <div class='panel-body'>
        <table class='table table-info'>%SIDECONTENT%
        </table>
      </div>
    </div>
  </div>
</div>
%PAGEFOOTER%
