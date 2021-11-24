<?php
require_once("../../../config/globals.php");

$task = (empty($_REQUEST['task']) == false) ? $_REQUEST['task'] : '';
$ajax_request = (empty($_REQUEST['ajax_request']) == false) ? $_REQUEST['ajax_request'] : '';

if(isset($ajax_request) && empty($ajax_request) == false){
	switch($task){
		case 'get_listing':
			$intOn = 0;
			$intOff = 0;
			$qryAllPol = "SELECT policy_id FROM audit_policies WHERE policy_status = 1 AND policy_display = 1";
			$rsData = imw_query($qryAllPol);
			if($rsData){
				$intOn = imw_num_rows($rsData);
			}

			$qryAllPol2 = "SELECT policy_id FROM audit_policies WHERE policy_status = 0 AND policy_display = 1";
			$rsData2 = imw_query($qryAllPol2);
			if($rsData2){
				$intOff = imw_num_rows($rsData2);
			}

			//loading schedule templates
			$rsData = "";
			$data = "<div class=\"row head\">
						<div class=\"col-sm-6\"><span>Switch All Audit Policies</span></div>
						<div class=\"col-sm-6\">
							<div class=\"row\">
								<div class=\"radio radio-inline\">
									<input style=\"cursor:hand\" type=\"radio\" onclick=\"javascript:set_all_policies(1);\" id=\"policy_status_all$intOn\" name=\"policy_status_all\"";
									if($intOff == 0){ $data .= "checked=\"checked\"";} 
									$data .= " value=\"1\"><label for=\"policy_status_all$intOn\"> On </label>
								</div>
								<div class=\"radio radio-inline\">
									<input style=\"cursor:hand\" type=\"radio\" onclick=\"javascript:set_all_policies(0);\" id=\"policy_status_all\" name=\"policy_status_all\"";
									if($intOn == 0){ $data .= "checked=\"checked\"";} 
									$data .= " value=\"0\"><label for=\"policy_status_all\"> Off </label>
								</div>
							</div>
						</div>
					</div>";

				$data .= "<table class=\"table table-bordered adminnw\">	
								<thead>	
									<tr>
										<th>S.No.</th>            
										<th>Policy Name</th>
										<th style=\"width:12%\" class=\"text-center\">Status</th>
										<th>Last Modified On</th>
										<th>Last Modified By</th>
									</tr>
								</thead><tbody>";

			$strQry = "select users.fname, users.lname, policy_id, policy_name, date_format(policy_modified_on,'".get_sql_date_format()." %h:%i %p') as policy_modified_on, policy_modified_by, policy_status from audit_policies left join users on users.id = audit_policies.policy_modified_by where policy_display = 1 order by policy_display_order";
			$rsData = imw_query($strQry);
			$strPolicies = "-1";
			if($rsData){
				$intCnt = imw_num_rows($rsData);
				if($intCnt > 0){
					$j = 0;
					while($arrRow = imw_fetch_array($rsData,MYSQL_ASSOC)){
						$id = $arrRow['policy_id'];
						$schedule_name = $arrRow['policy_name'];
						$showName = ($arrRow['lname'] != "") ? $arrRow['lname'].", ".$arrRow['fname'] : $arrRow['fname'];
						$data .= "<tr>
									<td>".($j+1)."</td>
									<td>".stripslashes(str_replace("<br />","",$arrRow['policy_name']))."</td>
									<td><input type=\"hidden\" name=\"this_policy_status".$id."\" id=\"this_policy_status".$id."\" value=\"".$arrRow['policy_status']."\">";
									$data .="<div class=\"radio radio-inline pull-left\">
											<input style=\"cursor:hand\" type=\"radio\" onclick=\"javascript:set_this_policy('this_policy_status".$id."',1);\" id=\"policy_status".$id."_on\" name=\"policy_status".$id."_on\"";
									if($arrRow['policy_status'] == 1){ $data .= " checked=\"checked\" "; } 
									$data .= "><label for=\"policy_status".$id."_on\"> On </label></div>";
									$data .="<div class=\"radio radio-inline pull-right\">
											<input style=\"cursor:hand\" type=\"radio\" onclick=\"javascript:set_this_policy('this_policy_status".$id."',0);\" id=\"policy_status".$id."_off\" name=\"policy_status".$id."_off\"";
									if($arrRow['policy_status'] == 0){ $data .= " checked=\"checked\" "; } 
									$data .= "><label for=\"policy_status".$id."_off\"> Off </label></div></td>                        
									<td>".$arrRow['policy_modified_on']."</td>
									<td>".$showName."</td>
								  </tr>";
						$strPolicies .= ",this_policy_status".$id;
						$j++;
					}
				}else{
					$data .= "<tr><td class=\"warning alignCenter\">No Record Found.</td></tr>";
				}
			}
			$data .= "<tbody></table>
			<input type=\"hidden\" name=\"hid_policies\" id=\"hid_policies\" value=\"".$strPolicies."\">";
			echo $data;
		break;
		
		case 'save_policy':
			$intCnt = count($_REQUEST);
			$counter = 0;
			foreach($_REQUEST as $key=>$value){
				if(substr($key, 0, 18) == "this_policy_status"){
					$policy_id = intval(substr($key, 18));
					$chkQry = "SELECT policy_status FROM audit_policies WHERE policy_id = '".$policy_id."'";
					$rsData = imw_query($chkQry);
					if($rsData){
						list($intCurrentStatus) = imw_fetch_row($rsData);
					}

					if($intCurrentStatus != $value){
						$updQry = "UPDATE audit_policies SET policy_status = '".$value."', policy_modified_on = '".date("Y-m-d H:i:s")."', policy_modified_by = '".$_SESSION['authId']."' WHERE policy_id = '".$policy_id."'";
						imw_query($updQry);
						$counter = ($counter + imw_affected_rows());
					}
				}
			}
			$arrAuditPolicies = array();
			$getAuditPolicies = "select policy_name,policy_status from audit_policies order by policy_id";
			$rsAuditPolicies = imw_query($getAuditPolicies);
			if($rsAuditPolicies){
				if(imw_num_rows($rsAuditPolicies) > 0){
					$arrRep = array("/"," ");
					while($rowAuditPolicies = imw_fetch_array($rsAuditPolicies)){
						$intPolicyStatus = 0;										
						$strPolicyName = "";
						$strPolicyName = $rowAuditPolicies['policy_name'];
						$intPolicyStatus = $rowAuditPolicies['policy_status'];					
						$strPolicyName = str_replace($arrRep,"_",$strPolicyName);
						$arrAuditPolicies[$strPolicyName] = $intPolicyStatus;					
					}
				}
				imw_free_result($rsAuditPolicies);
				unset($_SESSION['AUDIT_POLICIES']);
				$_SESSION['AUDIT_POLICIES'] = $arrAuditPolicies;
			}
			echo $counter;
		break;
	}
	exit();
}
?>