<?php
/*+**********************************************************************************
 * danzi.tn@20161013
 ************************************************************************************/

require_once('include/database/PearDatabase.php');
require_once 'include/utils/utils.php';

function handlePotentialDeadlines($entity) {
    global $adb, $log;
    
	$id = $entity->data['id'];
	$id_splitted = explode('x',$id);
	$potentialsid = $id_splitted[1];
	
	$potentialname = $entity->data['potentialname'];
    $log->debug("Entering handlePotentialDeadlines potentialsid=".$potentialsid);
    $log->debug("\thandlePotentialDeadlines potentialname=".$potentialname);
    // By Whom
	$id = $entity->data['cf_879'];
	$id_splitted = explode('x',$id);
	$byWhomId = $id_splitted[1];
	
	// Tech Manager
	$id = $entity->data['cf_881'];
	$id_splitted = explode('x',$id);
	$techManId = $id_splitted[1];
	
	$nextstep = $entity->data['nextstep'];
	$nextstep_date = $entity->data['cf_798'];
	$nextstep_action = $entity->data['cf_800'];
	$tactic_desc = $entity->data['cf_875'];
	$nextstep = $entity->data['nextstep'];
	$description = $entity->data['description'];
	$potential_no = $entity->data['potential_no'];
	
	// Assegnato a
	$id = $entity->data['assigned_user_id'];
	$id_splitted = explode('x',$id);
	$assigned_user_id = $id_splitted[1];
	
    $smownerid = $entity->data['smownerid'];
    
    $log->debug("\thandlePotentialDeadlines byWhomId=".$byWhomId);
    $log->debug("\thandlePotentialDeadlines techManId=".$techManId);
    $log->debug("\thandlePotentialDeadlines nextstep_date=".$nextstep_date);
    $log->debug("\thandlePotentialDeadlines nextstep=".$nextstep);
    $log->debug("\thandlePotentialDeadlines nextstep_action=".$nextstep_action);
    $log->debug("\thandlePotentialDeadlines assigned_user_id=".$assigned_user_id);
    $log->debug("\thandlePotentialDeadlines modifiedtime=".$entity->data["modifiedtime"]);
    
    if( !empty($nextstep_date)) {
        $data_scadenza = getValidDBInsertDateValue($nextstep_date);
        $log->debug("\t\thandlePotentialDeadlines data_scadenza=".$data_scadenza);
        $newEvent = CRMEntity::getInstance('Events');
	    vtlib_setup_modulevars('Events',$newEvent);
	    
	    $newEvent->column_fields['subject'] = $nextstep_action . " (".$potential_no.")";
    	$newEvent->column_fields['smownerid'] = $assigned_user_id;
		$newEvent->column_fields['assigned_user_id'] = $byWhomId;
		$newEvent->column_fields['createdtime'] = $entity->data["modifiedtime"];
		$newEvent->column_fields['modifiedtime'] = $entity->data["modifiedtime"];
		$newEvent->column_fields['parent_id'] = $potentialsid;
		$newEvent->column_fields['date_start'] = $data_scadenza;// 2013-05-27
		$newEvent->column_fields['time_start'] = '07:00:00';// 15:50
		$newEvent->column_fields['due_date'] =  $data_scadenza; // 2013-05-27
		$newEvent->column_fields['time_end'] = '18:00:00';// 15:55
		$newEvent->column_fields['duration_hours'] = 11;// 2
		$newEvent->column_fields['duration_minutes'] = 0;// 2
		$newEvent->column_fields['visibility'] = "Public";
		$newEvent->column_fields['priority'] = "Medium";
		$newEvent->column_fields['activitytype'] = "Next Step";
		$newEvent->column_fields['eventstatus'] = "Planned";// $insp_eventstatus 
		$newEvent->column_fields['description'] = "Next Step: " . $nextstep_action . " con scadenza " . $data_scadenza . " in relazione a " . $potentialname . "\n\n###########\n" . $tactic_desc;
		$newEvent->column_fields['sendnotification'] = 1; 
		$gg_promemoria = 1 + 5*24*60;
		$newEvent->column_fields['reminder_time'] = $gg_promemoria;
		$newEvent->save($module_name='Events',$longdesc=false);
		$newEvent->activity_reminder($newEvent->id,$gg_promemoria,1,0,'edit');
   	    
	    
	}
	
    $log->debug("handlePotentialDeadlines terminated ");
}

?>
