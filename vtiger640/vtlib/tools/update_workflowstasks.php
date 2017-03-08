<?php
/***********************************************************
danzi.tn@20161013 template per la creazione di un metodo custom da chiamare nel workflow
                  
                  
**/
chdir(dirname(__FILE__) . '/../..');
require_once 'include/utils/utils.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $adb;
$emm = new VTEntityMethodManager($adb); 
$emm->addEntityMethod("Potentials", "Update Opportunity", "include/PotentialHandler.php", "handlePotentialDeadlines");

?>
