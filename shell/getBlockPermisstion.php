<?php
require_once 'abstract.php';
class Mage_shell_GetPermissionBlock extends Mage_Shell_Abstract
{
    protected $_patten_block='/\{\{block\ type=[\"\']\w+(\/\w+)*[\"\']/i';
    protected $_patten_variable='/\{\{config\ path=[\"\']\w+(\/\w+)*[\"\']/i';
    public function run(){
     $resource = Mage::getSingleton('core/resource');
     $r = $resource->getConnection('core_read');
     $w = $resource->getConnection('core_write');
     /*table cms_block cms_page permission_block permission_variable*/
     $tables = array('cms_block','cms_page');
     $is_allowed = 1;
     foreach ($tables as $table) {
         $sql_table = "SELECT `content` FROM `{$table}`;";
         foreach ($r->fetchAll($sql_table) as $_content) {
             preg_match_all($this->_patten_block, $_content['content'], $_blocks);
             $blocks = $_blocks[0];
             if(!empty($blocks) && count($blocks)>0){
                foreach ($blocks as $block) {
                    $block = trim(substr($block,13),'"');
                    $block = trim($block,"'");
                    if($block){
                      var_dump($block);
                      $sql_find_block = "SELECT `block_id` FROM `permission_block` WHERE `block_name`='{$block}';";
                      if(!$r->fetchOne($sql_find_block)){
                        $sql_insert_block =  "INSERT INTO `permission_block`(`block_name`,`is_allowed`)VALUES('{$block}','{$is_allowed}');";
                        $w->query($sql_insert_block);
                      }
                    }
                }
             }
            preg_match_all($this->_patten_variable, $_content['content'], $_variables);
            $variables=$_variables[0];
            if(!empty($variables) && count($variables)>0){
                foreach ($variables as $variable) {
                    $variable = trim(substr($variable,15),'"');
                    $variable = trim($variable,"'");
                    if($variable){
                      var_dump($variable);
                      $sql_find_variable = "SELECT `variable_id` FROM `permission_variable` WHERE `variable_name`='{$variable}';";
                      if(!$r->fetchOne($sql_find_variable)){
                        $sql_insert_variable =  "INSERT INTO `permission_variable`(`variable_name`,`is_allowed`)VALUES('{$variable}','{$is_allowed}');";
                        $w->query($sql_insert_variable);
                      }
                    }
                }
             }
          }
      }
    }
}
$shell = new Mage_shell_GetPermissionBlock ();
$shell ->run();

