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
                  da utilizzare dopo aver creato il modulo con la console es. Countries + Name
                  
Nota: per avere le related activities bisogna 

in modules/Countries/Countries.php i metodi get_activitie e get_history sostituendo il nome dell'entità
in modules/Countries aggiungere
        - views/Detail.php sostituendo il nome della Classe
        - models/DetailView.php sostituendo il nome della Classe
        - models/Module.php sostituendo il nome della Classe e tutte le occorrenze 
        - models/Record.php sostituendo il nome della Classe

in languages/it_it/Countries.php e languages/en_en/Countries.php metterci le traduzioni delle label principali

in layouts/vlayout/modules/Countries aggiungere tutto in blocco e cambiare nomi classi etc etc
                  
                  
                  
**/
chdir(dirname(__FILE__) . '/../..');
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Package.php';
include_once 'includes/main/WebUI.php';

include_once 'include/Webservices/Utils.php';

$Vtiger_Utils_Log = true;


$SINGLE_MODULENAME = 'Potential';
$MODULENAME = 'Potentials';


$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || file_exists('modules/'.$MODULENAME)) {
    $tabid = $moduleInstance->id;
    echo "\nModule ". $MODULENAME . " is present\n";
    
    $block_name = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
    $block = Vtiger_Block::getInstance($block_name,$moduleInstance);
    if($block) {
                
        $fieldName = 'employerid';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " already exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label= "Employer";
            $field->uitype = 10;
            $field->columntype = 'INT(19)';
            $field->typeofdata = 'I~O';
            $field->displaytype = 1;
            $field->helpinfo = 'Employer as an existing Account';
            $field->quickcreate = 0;
            $block->addField($field);
            $field->setRelatedModules(Array('Accounts'));
        }
        
    } else {
        echo "Block ". $block_name . " is not present\n";
    }
} else {
    echo "Module ". $MODULENAME . " is not present\n";
}

?>
