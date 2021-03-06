<?php
require_once('data/CRMEntity.php');
function process_ticket($ticketid,$element, $user){
    global $log,$adb,$current_user;
	$entityIds = array();
    $log->debug("starting vtws_process_ticket(".$ticketid.")...");
    $log->debug("Element: " .print_r($element, True));
    $log->debug("User id: " . $user->id);
    $contactData = $element["contact"];
    $eventData = $element["event"];
    
    $remove_five_minutes = strtotime("-5 minutes");
    $add_fiftyfive_minutes = strtotime("+115 minutes");
    $startStamp = gmdate("Y-m-d", $remove_five_minutes); // gmdate instead of gmdate
    $endStamp =  gmdate("Y-m-d",$add_fiftyfive_minutes);
    $duration_hours=1;
    $time_start=gmdate("H:i", $remove_five_minutes);
    $time_end=gmdate("H:i", $add_fiftyfive_minutes);
    // 10xid
    $sql = "SELECT 
            vtiger_leaddetails.leadid as id
            , vtiger_ws_entity.id as wsid
            , vtiger_leaddetails.firstname
            , vtiger_leaddetails.lastname
            , vtiger_leaddetails.company
            , vtiger_leaddetails.email
            FROM vtiger_leaddetails
            JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid AND vtiger_crmentity.deleted = 0
            JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Leads'
            WHERE vtiger_leaddetails.email = ?";
    $result = $adb->pquery($sql, array($email));
    
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Leads'][] = $resultrow['wsid']."x".$resultrow['id'];
    		$newEvent = CRMEntity::getInstance('Events');
            vtlib_setup_modulevars('Events',$newEvent);
            $newEvent->column_fields['subject'] = $eventData['subject'];
            $newEvent->column_fields['visibility'] = 'Public';
            $newEvent->column_fields['assigned_user_id'] = $user->id;
            $newEvent->column_fields['createdtime'] = $startStamp;
            $newEvent->column_fields['modifiedtime'] = $startStamp;
            $newEvent->column_fields['parent_id'] = $resultrow['id'];
        //    $newEvent->column_fields['contact_id'] = $focus->id;
            $newEvent->column_fields['date_start'] = $startStamp;
            $newEvent->column_fields['time_start'] = $time_start.":00";// 15:50
            $newEvent->column_fields['due_date'] =  $endStamp;
            $newEvent->column_fields['time_end'] = $time_end.":00";
            $newEvent->column_fields['duration_hours'] = 1;// 2
            $newEvent->column_fields['activitytype'] = $eventData['type'];
            $newEvent->column_fields['is_all_day_event'] = 0;
            $newEvent->column_fields['eventstatus'] = 'Planned';// $insp_eventstatus 
            $newEvent->column_fields['location'] = $eventData['location'];
            $newEvent->column_fields['description'] = $eventData['description'];
            $newEvent->save($module_name='Events',$longdesc=false);
    	}
    }
    // 12xid
    $sql = "SELECT 
    vtiger_contactdetails.contactid as id
    , vtiger_ws_entity.id as wsid
    , vtiger_contactdetails.firstname
    , vtiger_contactdetails.lastname
    , vtiger_account.accountname as company
    , vtiger_account.accountid as company_id
    , vtiger_contactdetails.email
    FROM vtiger_contactdetails
    JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid AND vtiger_crmentity.deleted = 0
    JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Contacts'
    LEFT JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
    LEFT JOIN vtiger_crmentity accent on accent.crmid = vtiger_account.accountid AND accent.deleted = 0
    WHERE vtiger_contactdetails.email = ?";
    $result = $adb->pquery($sql, array($email));
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Contacts'][] = $resultrow['wsid']."x".$resultrow['id'];
    		$newEvent = CRMEntity::getInstance('Events');
            vtlib_setup_modulevars('Events',$newEvent);
            $newEvent->column_fields['subject'] = $eventData['subject'];
            $newEvent->column_fields['visibility'] = 'Public';
            $newEvent->column_fields['assigned_user_id'] = $user->id;
            $newEvent->column_fields['createdtime'] = $startStamp;
            $newEvent->column_fields['modifiedtime'] = $startStamp;
            $newEvent->column_fields['parent_id'] = $resultrow['company_id'];
            $newEvent->column_fields['contact_id'] = $resultrow['id'];
            $newEvent->column_fields['date_start'] = $startStamp;
            $newEvent->column_fields['time_start'] = $time_start.":00";// 15:50
            $newEvent->column_fields['due_date'] =  $endStamp;
            $newEvent->column_fields['time_end'] = $time_end.":00";
            $newEvent->column_fields['duration_hours'] = 1;// 2
            $newEvent->column_fields['activitytype'] = $eventData['type'];
            $newEvent->column_fields['is_all_day_event'] = 0;
            $newEvent->column_fields['eventstatus'] = 'Planned';// $insp_eventstatus 
            $newEvent->column_fields['location'] = $eventData['location'];
            $newEvent->column_fields['description'] = $eventData['description'];
            $newEvent->save($module_name='Events',$longdesc=false);
    	}
    }
    // 11xid
    $sql = "SELECT 
    vtiger_account.accountid as id
    , vtiger_ws_entity.id as wsid
    , '' as firstname
    , '' as lastname
    , vtiger_account.accountname
    , vtiger_account.email1 as email
    FROM vtiger_account
    JOIN vtiger_crmentity accent on accent.crmid = vtiger_account.accountid AND accent.deleted = 0
    JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Accounts'
    WHERE vtiger_account.email1 = ?";
    $result = $adb->pquery($sql, array($email));
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Accounts'][] = $resultrow['wsid']."x".$resultrow['id'];
    		$newEvent = CRMEntity::getInstance('Events');
            vtlib_setup_modulevars('Events',$newEvent);
            $newEvent->column_fields['subject'] = $eventData['subject'];
            $newEvent->column_fields['visibility'] = 'Public';
            $newEvent->column_fields['assigned_user_id'] = $user->id;
            $newEvent->column_fields['createdtime'] = $startStamp;
            $newEvent->column_fields['modifiedtime'] = $startStamp;
            $newEvent->column_fields['parent_id'] = $resultrow['id'];
            $newEvent->column_fields['date_start'] = $startStamp;
            $newEvent->column_fields['time_start'] = $time_start.":00";// 15:50
            $newEvent->column_fields['due_date'] =  $endStamp;
            $newEvent->column_fields['time_end'] = $time_end.":00";
            $newEvent->column_fields['duration_hours'] = 1;// 2
            $newEvent->column_fields['activitytype'] = $eventData['type'];
            $newEvent->column_fields['is_all_day_event'] = 0;
            $newEvent->column_fields['eventstatus'] = 'Planned';// $insp_eventstatus 
            $newEvent->column_fields['location'] = $eventData['location'];
            $newEvent->column_fields['description'] = $eventData['description'];
            $newEvent->save($module_name='Events',$longdesc=false);
    	}
    }
    if( empty($entityIds) ) {
        $map_vt_lead_array = array(
                            'first_name'=>'firstname',
                            'last_name'=>'lastname',
                            'company_name'=>'company',
                            'user_email'=>'email',
                            'addr1'=>'lane',
                            'city'=>'city',
                            'thestate'=>'state',
                            'country'=>'country',
                            'zip'=>'code',
                            'phone1'=>'phone',
                            'user_url'=>'website',
                            'status'=>'leadstatus',
                            'business_type'=>'industry',
                            'description'=>'description'
                            );
        $newLead = CRMEntity::getInstance('Leads');
        vtlib_setup_modulevars('Leads',$newLead);
        $newLead->column_fields['assigned_user_id'] = $user->id;
        $newLead->column_fields['createdtime'] = $startStamp;
        $newLead->column_fields['modifiedtime'] = $startStamp;
        foreach($map_vt_lead_array as $key=>$value)
        {
            if(array_key_exists($key,$contactData))   $newLead->column_fields[$value] = $contactData[$key];
        }
        $newLead->column_fields['description'] .= "\n".$eventData['subject'];
        $newLead->column_fields['description'] .= "\n".$eventData['description'];
        $newLead->column_fields['leadsource'] = $eventData['type'];
        $newLead->column_fields['rating'] = 'Acquired';
        $newLead->column_fields['leadstatus'] = 'Pre Qualified';
        $newLead->save($module_name='Leads',$longdesc=false);
        // Associate Event to New Lead
        $newEvent = CRMEntity::getInstance('Events');
        vtlib_setup_modulevars('Events',$newEvent);
        $newEvent->column_fields['subject'] = $eventData['subject'];
        $newEvent->column_fields['visibility'] = 'Public';
        $newEvent->column_fields['assigned_user_id'] = $user->id;
        $newEvent->column_fields['createdtime'] = $startStamp;
        $newEvent->column_fields['modifiedtime'] = $startStamp;
        $newEvent->column_fields['parent_id'] = $newLead->id;
        $newEvent->column_fields['date_start'] = $startStamp;
        $newEvent->column_fields['time_start'] = $time_start.":00";// 15:50
        $newEvent->column_fields['due_date'] =  $endStamp;
        $newEvent->column_fields['time_end'] = $time_end.":00";
        $newEvent->column_fields['duration_hours'] = 1;// 2
        $newEvent->column_fields['activitytype'] = $eventData['type'];
        $newEvent->column_fields['is_all_day_event'] = 0;
        $newEvent->column_fields['eventstatus'] = 'Planned';// $insp_eventstatus 
        $newEvent->column_fields['location'] = $eventData['location'];
        $newEvent->column_fields['description'] = $eventData['description'];
        $newEvent->save($module_name='Events',$longdesc=false);
        $entityIds['Leads'][] = "10x".$newLead->id;
    }
    $log->debug("vtws_process_ticket terminated!");
    return $entityIds;
}
?>
