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
<?php if(!empty($this->config->%MODULE%->editor->%ACTION%)):?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php endif;?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<style>
  .page-actions {text-align: left; margin-left: 85px;}
</style>
<form id='ajaxForm' class='form-inline' method='post'%FORMACTION%>
  <table class='table table-form'>%TABLECONTENT%
  </table>
  <div class='page-actions'>%PAGEACTIONS%
  </div>
</form>
%PAGEFOOTER%
