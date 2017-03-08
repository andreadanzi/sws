<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/***********************************************************
danzi.tn@20150427 template per la creazione di un modulo
                  da utilizzare dopo aver creato il modulo con la console
**/
chdir(dirname(__FILE__) . '/../..');

require_once 'include/utils/utils.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
$emm = new VTEntityMethodManager($adb); 
$emm->addEntityMethod("HelpDesk", "Process Ticket for GIT", "modules/com_vtiger_workflow/custom_wf_tickets.inc.php", "customTicket4GIT");

?>
