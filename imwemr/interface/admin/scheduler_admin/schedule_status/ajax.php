<?php 
require_once("../../../../config/globals.php");
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'status_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "schedule_status";
$pkId	= "id";
$chkFieldAlreadyExist="status_name";

$masterIdArr = array();
//Get All the avaialble status from DB
$chkQry = imw_query(' select id,col_type from schedule_status WHERE (col_type = 0 || col_type = 1) ');
if($chkQry && imw_num_rows($chkQry) > 0){
	while($rowFetch = imw_fetch_assoc($chkQry)){
		$colType = $rowFetch['col_type'];
		switch($colType){
			case 0:
				//Mandatory Status Fields
				$masterIdArr['nonDisableIds'][] = $rowFetch['id'];
			break;
			
			case 1:
				//Only Status modify fields
				$masterIdArr['nonRenameIds'][] = $rowFetch['id'];
			break;
		}
	}
}

if(isset($masterIdArr['nonRenameIds']) && count($masterIdArr['nonRenameIds']) > 0){
	$masterIdArr['nonRenameIds'] = array_merge($masterIdArr['nonRenameIds'], $masterIdArr['nonDisableIds']);
}

//status list that can't be disabled
//$nonDisableIds=array(11=>11,13=>13,18=>18,201=>201,202=>202,271=>271);
//status list that can't be renamed
//$nonRenameIds=array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 12=>12, 13=>13, 14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 21=>21, 22=>22, 23=>23, 100=>100, 101=>101, 200=>200, 201=>201, 202=>202, 203=>203, 271=>271);

switch($task){
	case 'action':
		$id = $_REQUEST['id'];
		$status = ($_REQUEST['mode'] == 1) ? 0 : 1;
		
		$q 		= "update $table set status =$status WHERE id IN (".$id.")";
		$res 	= imw_query($q);
		$erp_error=array();
		if($res){
                        /* MVE PORTAL CREATE(active)/DELETE(inactive) APPOINTMENT STATUS */
                        if(isERPPortalEnabled()) {
							try {
								include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
								$obj_appointments = new Appointments();
								if($status==1){
									$obj_appointments->addUpdateAppointmentStatuses($id);
								}else{
									$obj_appointments->deleteAppointmentStatuses($id);
								}
							} catch(Exception $e) {
								$erp_error[]='Unable to connect to ERP Portal';
							}
                        }
                        
			echo '1';
		}else{
			echo '0';
		}
		break;
	case 'save_update':
		$id = $_POST['status_id'];
		$qry_con = "";
		if($id){$qry_con=" AND id!='".$id."'";}
		$q_c="SELECT id from ".$table." WHERE (status_name='".imw_real_escape_string($_POST['status_name'])."' 
			OR alias='".imw_real_escape_string($_POST['status_alias'])."')".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET status_name='".imw_real_escape_string($_POST['status_name'])."', 
				alias='".imw_real_escape_string($_POST['status_alias'])."',
				status_color='".imw_real_escape_string($_POST['status_color'])."',
				status=1,
				added_by='$_SESSION[authId]',
				added_datetime='".date('Y-m-d H:i:s')."',
				modify_by='$_SESSION[authId]',
				modify_datetime='".date('Y-m-d H:i:s')."'";
			}else{
				$q = "UPDATE ".$table." SET status_name='".imw_real_escape_string($_POST['status_name'])."', 
				alias='".imw_real_escape_string($_POST['status_alias'])."',
				status_color='".imw_real_escape_string($_POST['status_color'])."',
				modify_by='$_SESSION[authId]',
				modify_datetime='".date('Y-m-d H:i:s')."' 
				WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			$erp_error=array();
			if($res){
                            $status_id = ($id=='') ? imw_insert_id($res) : $id;
                            /* MVE PORTAL CREATE APPOINTMENT STATUS */
                            if(isERPPortalEnabled()) {
								try {
									include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
									$obj_appointments = new Appointments();
									$obj_appointments->addUpdateAppointmentStatuses($status_id);
								} catch(Exception $e) {
									$erp_error[]='Unable to connect to ERP Portal';
								}
                            }
                            
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$where = 'WHERE status=1';
		if(isset($_GET['filter']) && $_GET['filter'] != '') {
			switch($_GET['filter']) {
				case 'active':
					$where = 'WHERE status=1';
					break;
				case 'inactive':
					$where = 'WHERE status=0';
					break;
				case 'all':
					$where = '';
					break;	
			}
		}
		$q = "SELECT status_name, status_color, alias, status, id FROM $table $where ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			$strHTML="<tr>";
			while($rs = imw_fetch_object($r)){
                $row++;
				$msg='';
				if($rs->id==203)$msg='(for system only)';
				//$onClick=' class="disabled"';
				$onClick=' class="disabled" onClick="addNew(\''.$rs->id.'\',\''.$rs->status_name.'\',\''.$rs->alias.'\',\''.$rs->status_color.'\',\'0\');"';
				if(!in_array($rs->id,$masterIdArr['nonRenameIds'])){
					$onClick=' onClick="addNew(\''.$rs->id.'\',\''.$rs->status_name.'\',\''.$rs->alias.'\',\''.$rs->status_color.'\',\'1\');"';
				}
				$strHTML.=
				"<td $onClick>$rs->status_name $msg</td>
				<td $onClick class='text-center'><div style='background-color:$rs->status_color;height:20px; width:80%'>&nbsp;</div></td>
				<td $onClick>$rs->alias</td>
				<td>";
				
				$success="success";
				$danger="danger";
				//stop permanent status being disabled
				if(!in_array($rs->id,$masterIdArr['nonDisableIds'])){
				$strHTML.='<a class="" href="javascript:activeDeactive(\''.$rs->id.'\',\''.$rs->status.'\');">';
				}else{
					$success="muted";
					$danger="muted";
				}
				
				if($rs->status == 1){ 
					$strHTML .= '<span class="glyphicon glyphicon-stop text-'.$success.'" title="Active"></span>';
				}else{ 
					$strHTML .= '<span class="glyphicon glyphicon-stop text-'.$danger.'" title="Inactive"></span>';
				}
				
				if(!in_array($rs->id,$masterIdArr['nonDisableIds'])){
				$strHTML .= '</a>';
				}
				
				$strHTML.='</td>';
				if($row%2==0){
                    $strHTML .= '</tr>';
				}
			}
		}
		echo $strHTML;
		break;
	default: 
}

?>