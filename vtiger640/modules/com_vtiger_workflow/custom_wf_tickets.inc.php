<?php
require_once('include/database/PearDatabase.php');
require_once 'include/utils/utils.php';
function customTicket4GIT($entity){
	global $adb, $log;
	$log->debug("customTicket4GIT started!");
	$entityArray = get_object_vars($entity);
	$projectUrlsMap = array("T-Grout Node.js"=>"https://swshub.com", "T-Grout Docker"=>"https://git.sws-digital.com");
	$projectApiUrlsMap = array("T-Grout Node.js"=>"https://swshub.com/api/v3", "T-Grout Docker"=>"https://git.sws-digital.com/api/v3");
	$projectTokenMap = array("T-Grout Node.js"=>"AngyhWZXHT_cbypLkKn4", "T-Grout Docker"=>"r_CK6PrqT5XzT23cyX4j");
	$projectNameMap = array("T-Grout Node.js"=>"t-grout", "T-Grout Docker"=>"tgrout");
	$projectUserIdMap = array("T-Grout Node.js"=>5, "T-Grout Docker"=>5); // 13 è mirko in SWSHUB mentre in digital è 8
	$id = $entity->data['id'];
	$id_splitted = explode('x',$id);
	$id = $id_splitted[1];
    // cf_967 Boolean
	// cf_971 Project
	// cf_969 Descr
	// cf_973 Url
    $ticketTitle = $entity->data['ticket_title'];
    $project_name = $entity->data['cf_971'];
    $project_url = $entity->data['cf_973'];
    $project_descr = $entity->data['cf_969'];
    $if_git = $entity->data['cf_967'];
    $ticket_no = $entity->data['ticket_no'];
    if($if_git==1) {
	    // vtiger_troubletickets
	    // vtiger_ticketcf
	    // TOKEN SWS HUB AngyhWZXHT_cbypLkKn4
	    // TOKEN SWS DIGITAL r_CK6PrqT5XzT23cyX4j
        $private_token = $projectTokenMap[$project_name];	
        $project_url = $projectUrlsMap[$project_name];
        // API URL
        $apiurl = $projectApiUrlsMap[$project_name];
        $getRequest = $apiurl."/projects?per_page=100&private_token=".$private_token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $getRequest,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
            ));
        $resp = curl_exec($curl);
        $array_resp = json_decode($resp);
        $project_id = 0;
        $ticketFound = False;
        $issue_id = 0;
        $issue_url = $project_url;
        foreach($array_resp as $resp_item ) {
            if($resp_item->name == $projectNameMap[$project_name]) {
                $log->debug("customTicket4GIT Found " . $project_name   );
                $project_id = $resp_item->id;
                $log->debug("customTicket4GIT with id " . $project_id   );
                $getIssuesRequest = $apiurl."/projects/".$project_id."/issues?per_page=100&private_token=".$private_token;
                $log->debug("customTicket4GIT issue request id " . $getIssuesRequest   );
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $getIssuesRequest,
                    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                    ));
                $issue_resp = curl_exec($curl);
                $array_issue_resp = json_decode($issue_resp);
                foreach($array_issue_resp as $resp_issue_item ) {
                    $log->debug("customTicket4GIT issue " . $resp_issue_item->title . " and ticketTitle ". $ticketTitle   );
                    if( trim($resp_issue_item->title) == trim($ticketTitle) ) {
                        // EDIT PUT /projects/:id/issues/:issue_id
                        $issue_id = $resp_issue_item->id;
                        $ticketFound = True;
                        $putUrl = $apiurl."/projects/".$project_id."/issues/".$issue_id;
                        /*
                        $log->debug("customTicket4GIT issue found  " . $putUrl  );
                        $fields = array(
       	                    'assignee_id' => $projectUserIdMap[$project_name],
                            'private_token' => urlencode($private_token)
                        );
                        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                        rtrim($fields_string, '&');
                        curl_setopt_array($curl, array(
                            CURLOPT_CUSTOMREQUEST=> "PUT",
                            CURLOPT_POSTFIELDS => $fields_string,
                            CURLOPT_URL => $putUrl,
                            CURLOPT_RETURNTRANSFER => True,
                            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                            ));
                        $new_issue_resp = curl_exec($curl);
                        */
                    }
                }
                if(!$ticketFound) {
                    // INSERT POST /projects/:id/issues
                    $fields = array(
                        'title' => urlencode( $ticketTitle ),
                        'description' => urlencode($entity->data['description']),
                        'labels' => 'portal',
                        'assignee_id' => $projectUserIdMap[$project_name],
                        'private_token' => urlencode($private_token)
                    );
                    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                    rtrim($fields_string, '&');
                    $log->debug("customTicket4GIT fields " . $fields  );
                    $postUrl = $apiurl."/projects/".$project_id."/issues";
                    curl_setopt_array($curl, array(
                        CURLOPT_POST=> count($fields),
                        CURLOPT_POSTFIELDS => $fields_string,
                        CURLOPT_URL => $postUrl,
                        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                        ));
                    $new_issue_resp = curl_exec($curl);
                    $new_issue_resp_obj = json_decode($new_issue_resp);
                    $issue_id = $new_issue_resp_obj->id;
                }
                $issue_url = $project_url."/" . $resp_item->path_with_namespace . "/issues/".$issue_id;
            }
        }
        curl_close($curl);
        $sql="UPDATE vtiger_ticketcf SET cf_973 = ? WHERE ticketid = ?";
        
        
        $params = array($issue_url, $id);
        $adb->pquery($sql, $params);
    } else {
	    $log->debug("customTicket4GIT nothing to GIT!");
    }
	$log->debug("customTicket4GIT terminated!");
}
?>
