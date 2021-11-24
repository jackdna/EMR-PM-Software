<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

/*
File: load_appt_hx.php
Purpose: Get appointment history
Access Type: Included
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');

$arr_fac_loc=get_facility_name();
$arr_fac=$arr_fac_loc[0];
 if(!isset($_REQUEST['server_id'])){ ?><div style="width:100%;overflow-x:scroll;"><?php }else{ $server_id = $_REQUEST['server_id'];
	$rms_qry = "SELECT server,url,ip FROM servers WHERE id = ".$server_id; 
	$rms_qry_obj = imw_query($rms_qry);
	$rms_data = imw_fetch_assoc($rms_qry_obj);
	$server_name = $rms_data['server'];	
	$rms_data['url'] = str_replace("localhost",$rms_data['ip'],$rms_data['url']);				
	echo '<div style="background-color:#4684ab;color:#fff;text-align:left;padding:5px;">'.$server_name.'</div>';
} ?><table class="table table-striped table-bordered table-hover"><?php			
$pat_id=$pid;
$mode = "tiny";	
if($pat_id<>""){			
?><thead><tr class="tophead"><th><strong><?php  echo "Provider"; ?></strong></th><th><strong><?php  echo "Facility";  ?></strong></th><th><strong><?php  echo "Procedure";  ?></strong></th><th><strong><?php  echo "Date Time";?></strong></th><th><strong><?php echo "Comments";  ?></strong></th></tr></thead><tbody>
<?php
	$server_url = '';
	$server_id = isset($_REQUEST['server_id']) ? trim($_REQUEST['server_id']) : 0;
	if($server_id > 0){ $server_url = $rms_data['url'];	}
	$server_id = $server_id.'--'.$server_url;
				
	$vquery_c = "SELECT 
	sa.sa_patient_id, sa.sa_patient_app_status_id, 
	sa.id,sa.sa_madeby,sa.sa_doctor_id,
	sa.sa_comments, sa.procedureid, 
	date_format( sa.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
	time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
	time_format( sa_app_starttime, '%H,%i,00' ) AS sa_app_starttime_js, 
	time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
	date_format( sa_app_start_date, '".get_sql_date_format('','y','/')."' ) AS sa_app_start_date_disp, 
	date_format( sa_app_start_date, '%Y,%m,%d' ) AS sa_app_start_date_disp_full, 
	slot_procedures.proc, 
	sa_app_start_date as sa_app_start_date_Db, 
	slot_procedures.acronym, sa.sa_facility_id  
	FROM 
	schedule_appointments sa
	INNER JOIN slot_procedures ON slot_procedures.id = sa.procedureid
	WHERE sa.sa_patient_id ='".$pat_id."'  
	/*AND slot_procedures.doctor_id = 0 */
	ORDER BY sa.sa_app_start_date DESC,sa.sa_app_starttime DESC 
				";								 
	$vsql_c = imw_query($vquery_c);
	while($vrs=imw_fetch_assoc($vsql_c)){	
		$all_record[$vrs["id"]]=$vrs;
	}
				
	//get records from archive table if any
	if($GLOBALS["CHK_ARCHIVE_TABLE"]){
		$vquery_arch = "SELECT 
		sa.sa_patient_id, sa.sa_patient_app_status_id, 
		sa.id,sa.sa_madeby,sa.sa_doctor_id,
		sa.sa_comments, sa.procedureid, 
		date_format( sa.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
		time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
		time_format( sa_app_starttime, '%H,%i,00' ) AS sa_app_starttime_js, 
		time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
		date_format( sa_app_start_date, '".get_sql_date_format('','y','/')."' ) AS sa_app_start_date_disp, 
		date_format( sa_app_start_date, '%Y,%m,%d' ) AS sa_app_start_date_disp_full, 
		slot_procedures.proc, 
		sa_app_start_date as sa_app_start_date_Db, 
		slot_procedures.acronym, sa.sa_facility_id  
		FROM 
		schedule_appointments_archive sa
		INNER JOIN slot_procedures ON slot_procedures.id = sa.procedureid
		WHERE sa.sa_patient_id ='".$pat_id."'  
		/*AND slot_procedures.doctor_id = 0 */
		ORDER BY sa.sa_app_start_date DESC,sa.sa_app_starttime DESC";								 
		$vsql_arch = imw_query($vquery_arch);
		while($vrs_a=imw_fetch_assoc($vsql_arch)){	
			if(!$all_record[$vrs_a["id"]])$all_record[$vrs_a["id"]]=$vrs_a;
		}
	}
				
foreach($all_record as $key=>$vrs)		
{
	$id=$vrs["id"];
	$prc_id=$vrs["procedureid"];
	$doctor_id=$vrs["sa_doctor_id"];
	$facility_id=$vrs["sa_facility_id"];
	$sa_patient_id=$vrs["sa_patient_id"];

	$strOpMadeByName = getOperatorInitialByUsername($vrs['sa_madeby']);								

	//tr style
	//$onclick = "onclick=\"refresh_patient_infopage('".$sa_patient_id."','".$id."','to_do');\"";
	//$onclick = "";
	if($vrs['sa_patient_app_status_id'] == '18' || $vrs['sa_patient_app_status_id'] == '203'){
//						$onclick="";						
		$sty=" hx_cancel";//cancelled
	}elseif($vrs['sa_patient_app_status_id'] == '3'){
//						$onclick="";						
		$sty=" hx_noshow";//noshow				
	}elseif($vrs['sa_patient_app_status_id'] == '201'){
//						$onclick="";						
		$sty=" hx_todo";//todo				
	}else if($vrs['sa_patient_app_status_id']=='271'){
		$sty=" hx_fa";//first				
	}else if($vrs['sa_patient_app_status_id']=='202'){
		$sty=" hx_rsh";//resch				
	}else{						
		$sty="";			
	}

	list($yar, $mar, $dar) = explode("-", $vrs["sa_app_start_date_Db"]);
	$daya = date("l", mktime(0, 0, 0, $mar, $dar, $yar));
	$proc_full = $proc = $vrs['proc'];
	if(strlen($proc) > 25)
	{
		$proc = substr($proc,0,22).'...';	
	}

	//get provider name
	if(!$pro_arr[$doctor_id]){
		$pro_arr[$doctor_id]=getProvider_name($doctor_id, "");
	}

	$pro_name = $pro_name_full = $pro_arr[$doctor_id];
	if(strlen($pro_name_full) > 20)
	{
		$pro_name = substr($pro_name_full,0,17).'...';	
	}
	?>
<tr onDblClick="javascript:popUpMe('<?php echo($vrs["id"]);?>','<?php echo($pat_id);?>','<?php echo $server_id; ?>');" class="link_cursor"><td data-label="Provider" onclick="javascript:load_calendar('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', 'nonono');pre_load_front_desk('<?php echo $sa_patient_id;?>','<?php echo $id;?>');load_appt_schedule('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', '', 'nonono')" class="<?php if($mode == "tiny"){ echo "text_10"; } echo $sty;?>"  nowrap align="left" title="<?php echo $pro_name_full;?>" ><?php if($mode == "tiny"){ echo $pro_name; } ?></td><td data-label="Facility" class="<?php echo $sty;?>" onclick="javascript:load_calendar('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', 'nonono');pre_load_front_desk('<?php echo $sa_patient_id;?>','<?php echo $id;?>');load_appt_schedule('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', '', 'nonono')"><?php echo $arr_fac[$facility_id]; ?></td><td data-label="Procedure" class="<?php echo $sty;?>" onclick="javascript:load_calendar('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', 'nonono');pre_load_front_desk('<?php echo $sa_patient_id;?>','<?php echo $id;?>');load_appt_schedule('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', '', 'nonono')" title="<?php echo $proc_full; ?>" ><?php if($mode == "tiny"){ echo $proc; } ?></td><td data-label="Date Time" class="<?php echo $sty;?>" onclick="javascript:load_calendar('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', 'nonono');pre_load_front_desk('<?php echo $sa_patient_id;?>','<?php echo $id;?>');load_appt_schedule('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', '', 'nonono')" nowrap><?php echo($vrs["sa_app_start_date_disp"]." ".$vrs["sa_app_starttime"]);?></td><td data-label="Comments" class="<?php echo $sty;?>" onclick="javascript:load_calendar('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', 'nonono');pre_load_front_desk('<?php echo $sa_patient_id;?>','<?php echo $id;?>');load_appt_schedule('<?php echo $vrs["sa_app_start_date_Db"];?>', '<?php echo $daya;?>', '', 'nonono')"><?php echo($vrs['sa_comments']);?></td></tr><input type="hidden" name="my_main_appt[]" id="" value="<?php echo $vrs["id"]; ?>"><?php		
		}
	}else{?><tr bgcolor="#4684ab"><td colspan="5" align="center">&nbsp;</td></tr><?php } ?></tbody></table>
<?php if(!isset($_REQUEST['server_id'])): ?></div><?php endif; ?>