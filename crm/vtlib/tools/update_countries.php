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


$SINGLE_MODULENAME = 'Country';
$MODULENAME = 'Countries';


$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || file_exists('modules/'.$MODULENAME)) {
    $tabid = $moduleInstance->id;
    echo "\nModule ". $MODULENAME . " is present\n";
    Vtiger_Filter::deleteForModule($moduleInstance);
    // Create default custom filter (mandatory)
	$filterAll = new Vtiger_Filter();
	$filterAll->name = 'All';
	$filterAll->isdefault = true;
	$moduleInstance->addFilter($filterAll);
	$fieldName = "name";
	$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
	if( $field )  $filterAll->addField($field);
	else echo "\nField ". $fieldName . " does not exists\n"; 
    
    $block_name = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
    $block = Vtiger_Block::getInstance($block_name,$moduleInstance);
    if($block) {
        
             
        /**  Country Code Alpha 2**/
        $fieldName = strtolower($SINGLE_MODULENAME).'_code2';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Code';
            $field->uitype = 1;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(100)';
            $field->typeofdata = 'V~M';// Varchar~Mandatory
            $block->addField($field); 
        }
        $filterAll->addField($field,1);
        
         /**  Country Code Alpha 3**/
        $fieldName = strtolower($SINGLE_MODULENAME).'_code3';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = $SINGLE_MODULENAME.' Code (Alpha 3)';
            $field->uitype = 1;
            $field->columntype = 'VARCHAR(100)';
            $field->typeofdata = 'V~O';// Varchar~Mandatory
            $block->addField($field); 
        }
        
        $fieldName = strtolower($moduleInstance->name).'_no';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
            $field->delete();
            echo "\nField ". $fieldName . " deleted\n";
        } 

        /**  Country Region **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_region';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Region';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'Europe','Americas','Asia','Africa','Oceania') );                        
        }

                
        /**  Country Sub Region **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_sub_region';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Sub Region';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'Western Europe','Southern Europe','Northern America','Eastern Europe','Western Asia','Southern Asia','Caribbean','Middle Africa','South America','Polynesia','Australia and New Zealand','Northern Europe','Western Africa','Eastern Africa','South-Eastern Asia','Southern Africa','Central America','Eastern Asia','Northern Africa','Melanesia','Micronesia','Central Asia','Other') );                        
        }
        $filterAll->addField($field,2);
        
        /**  Country Rating **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_rating';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Rating';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'Low', 'Medium', 'High') );
        }
        $filterAll->addField($field,3);
        
        /** Key Country **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_iskeycountry';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Is a Key Country';
            $field->uitype = 56;
            $field->columntype = 'VARCHAR(100)';
            $field->typeofdata = 'c~O';// Varchar~Optional
            $block->addField($field);
        }
        $filterAll->addField($field,4);
        
        /** bloccco descrizione */
        $blockDescription = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION',$moduleInstance);
        if( $blockDescription ) {
             echo "\nDescriptio block for ". $MODULENAME . " is available\n";
        } else {
            $blockDescription = new Vtiger_Block();
		    $blockDescription->label = 'LBL_DESCRIPTION_INFORMATION';
		    $moduleInstance->addBlock($blockDescription);
            echo "Descriptio block for ". $MODULENAME . " created!\n";
        }
        
        /**  Country Description **/
        $fieldName = 'description';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = 'vtiger_crmentity';
            $field->label = 'Description';
            $field->uitype = 19;
            $field->typeofdata = 'V~O';// Varchar~Optional
            $blockDescription->addField($field);
        }
        
        /** n:1 relations with Accounts**/
        $relModule = Vtiger_Module::getInstance('Accounts');
        if($relModule) {
            $relBlock = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION',$relModule);
            if($relBlock) {
                $fieldName = 'countriesid';
                $field = Vtiger_Field::getInstance($fieldName, $relModule);
                if( $field ) {
                    echo "\nField ". $fieldName . " exists\n";
                } else {
                    $field = new Vtiger_Field();
                    $field->name = $fieldName;
                    $field->table = $relModule->basetable;
                    $field->label= $SINGLE_MODULENAME;
                    $field->uitype = 10;
                    $field->columntype = 'INT(19)';
                    $field->typeofdata = 'I~O';
                    $field->displaytype = 1;
                    $field->summaryfield = 1;
                    $field->quickcreate = 1;
                    $relBlock->addField($field);
                    $field->setRelatedModules(Array('Countries'));
                    //relazione 1 a n Accounts (for Customer and Competitor)
                    $moduleInstance->setRelatedList($relModule, 'Accounts', Array('ADD'), 'get_dependents_list');
                }
            } else {
                 echo "\nBlock LBL_ACCOUNT_INFORMATION does not exists\n";
            }
        }
        
        /** n:1 relations with Potentials**/
        $relModule = Vtiger_Module::getInstance('Potentials');
        if($relModule) {
            $relBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION',$relModule);
            if($relBlock) {
                $fieldName = 'countriesid';
                $field = Vtiger_Field::getInstance($fieldName, $relModule);
                if( $field ) {
                    echo "\nField ". $fieldName . " exists\n";
                } else {
                    $field = new Vtiger_Field();
                    $field->name = $fieldName;
                    $field->table = $relModule->basetable;
                    $field->label= $SINGLE_MODULENAME;
                    $field->uitype = 10;
                    $field->columntype = 'INT(19)';
                    $field->typeofdata = 'I~O';
                    $field->displaytype = 1;
                    $field->summaryfield = 1;
                    $field->quickcreate = 1;
                    $relBlock->addField($field);
                    $field->setRelatedModules(Array('Countries'));
                    //relazione 1 a n Accounts (for Customer and Competitor)
                    $moduleInstance->setRelatedList($relModule, 'Potentials', Array('ADD'), 'get_dependents_list');
                }
            } else {
                 echo "\nBlock LBL_OPPORTUNITY_INFORMATION does not exists\n";
            }
        }
        
        /** n:n relations with Documents**/
        $relModule = Vtiger_Module::getInstance('Documents');
        $moduleInstance->unsetRelatedList($relModule,'Documents','get_attachments');
        $moduleInstance->setRelatedList($relModule, 'Documents',Array('ADD','SELECT'),'get_attachments');
        /** n:n relations with Calendar**/
        $relModule = Vtiger_Module::getInstance('Calendar');
        $moduleInstance->unsetRelatedList($relModule,'Activities','get_activities');
        $moduleInstance->setRelatedList($relModule, 'Activities',Array('ADD'),'get_activities');
        $moduleInstance->unsetRelatedList($relModule,'Activity History','get_history');
        $moduleInstance->setRelatedList($relModule, 'Activity History',Array('ADD'),'get_history');
       
       
        /*Dashboard Widgets*/   
        $moduleInstance->addLink('DASHBOARDWIDGET', 'High rating Countries', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=TbvCountries','', '1');
        $moduleInstance->addLink('DASHBOARDWIDGET', 'Countries by rating', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=CountriesByRating','', '2');
        $home = Vtiger_Module::getInstance('Home');
        $home->addLink('DASHBOARDWIDGET', 'High rating Countries', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=TbvCountries','', '15');
        $home->addLink('DASHBOARDWIDGET', 'Countries by rating', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=CountriesByRating','', '16');
       
       
        if(file_exists('modules/ModTracker/ModTrackerUtils.php')) {
	        require_once 'modules/ModTracker/ModTrackerUtils.php';
	        ModTrackerUtils::modTrac_changeModuleVisibility($tabid, 'module_enable');
        }
        
        if(file_exists('modules/ModComments/ModComments.php')) {
	        require_once 'modules/ModComments/ModComments.php';
	        ModComments::removeWidgetFrom($MODULENAME);
	        ModComments::addWidgetTo($MODULENAME);
        }
    } else {
        echo "Block ". $block_name . " is not present\n";
    }
} else {
    echo "Module ". $MODULENAME . " is not present\n";
}

?>
