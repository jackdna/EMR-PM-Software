<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

$spec = isset($spec) ? $spec : '';
if(trim($spec)) {
?>
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">		
<?php	
}
?>
<!--<link rel="stylesheet" href="css/form.css" type="text/css" />-->
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >
<style>
body { text-align:left; margin: 0px; background: #ECF1EA; width:100%; font:normal 11px Verdana, Arial, sans-serif;color:#000}
.link_slid_right{ color:#000000; text-decoration:none;}
.link_slid_right:hover{ color:#F10; text-decoration:none;}
</style>
<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="js/jsFunction.js"></script>
<script type="text/javascript" src="js/moocheck.js"></script>
<script src="js/epost.js"></script>
<script type="text/javascript" src="js/disableKeyBackspace.js"></script>
<script type="text/javascript" src="js/actb.js"></script>
<script type="text/javascript" src="js/common.js"></script>

<!--RESPONSIVE CSS AND JAVASCRIPT-->
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="css/ion.calendar.css" />
<link rel="stylesheet" type="text/css" href="css/datepicker.css" />


<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-select.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>    
<script type="text/javascript" src="js/ion.calendar.js"></script>
<script type="text/javascript" src="js/overflow.js"></script>
<script type="text/javascript" src="js/front_page.js"></script>
<script type="text/javascript" src="js/list-item.js"></script>
<script type="text/javascript" src="js/alert_file.js"></script>
<!--RESPONSIVE CSS AND JAVASCRIPT-->
<script>

$(function(){		
	
	$(".selectpicker").selectpicker();
	
	$('.selectpicker').on( 'change', function (e) {
		// take jquery val()s array for multiple
		//store the selected value
		var $selectedOption = $(this).find(':selected');
		var attending = $selectedOption.data('attending');
		var checkAttendings = [];
		$selectedOption.each(function(index){
			checkAttendings.push($(this).data('attending'));
		});
		if(typeof attending == 'undefined' || attending === '') return; 
		// take jquery vals() array for multiple
		var value = checkAttendings || [];
		
		// take the existing old data or create new
		var old = $(this).data('old') || [];
		//alert('OLD IS '+JSON.stringify(old));
		// take the old order or create a new
		var order = $(this).data('order') || [];
		//alert('ORDER'+JSON.stringify(order));
		// find the new items
		var newone = value.filter(function (val) {
			return old.indexOf(val) == -1;
		});
		//alert('NEW One'+JSON.stringify(newone));
		// find missing items
		var missing = old.filter(function (val) {
			return value.indexOf(val) == -1;
		});
		// console.log(missing,newone)
		// remove missing items from order array and add new ones to it
		$.each(missing, function (i, miss) {
			order.splice(order.indexOf(miss), 1);
		})
		$.each(newone, function (i, thing) {
			order.push(thing);
		})
	
		// save the order and old in data()
		$(this).data('old', value).data('order', order);
		
		if(JSON.stringify(order) == JSON.stringify([1,2]) || JSON.stringify(order) == JSON.stringify([0,2]))
		{
			attending = 2;
		}
		
		
		//if current selected array equal to [0,2] that means user trying to check TBD
		if(JSON.stringify(order) == JSON.stringify([1,0]) || JSON.stringify(order) == JSON.stringify([2,0]) )
		{
			attending = 0;
		}
	
		//if current selected array equal to [0,1]  or [2,1] that means user trying to check Attending date
		if(JSON.stringify(order) == JSON.stringify([0,1]) || JSON.stringify(order) == JSON.stringify([2,1])){
			attending = 1;
		}
		//alert(attending);
		console.log(order, 'attending :' + attending);
	
		if(attending == 1)
		{
	
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-success');
			var tbd = $(this).find('[data-attending="2"]');
			var notAttending = $(this).find('[data-attending="0"]');
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
	
			if(notAttending.is(':selected'))
			{
				notAttending.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}
		 
		/*if(attending == 2)
		{
	
			$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-default');
	
			var coming = $(this).find('[data-attending="1"]');
			var not = $(this).find('[data-attending="0"]');
	
			if(coming.is(':selected'))
			{
				console.log('comming');
				coming.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(not.is(':selected'))
			{
				console.log('not comming');
				not.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}*/
		
		if(attending == 0 )
		{
			var screening_date_id 	= 0;
			
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-danger');
	
			var yesComing	=	$(this).find('[data-attending="1"]');
			var tbd 		=	$(this).find('[data-attending="2"]');
			
			if(yesComing.is(':selected'))
			{
				yesComing.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
			   
				$(this).selectpicker('refresh');
			}
			
			order.reverse();
			console.log(order, 'attending :'+attending);
		}
		
		if(typeof attending == 'undefined' || attending == '' )
		{	
			$(this).find('[data-attending="0"]').prop('selected',true);	
			$(this).selectpicker('refresh');
		}
		//order.length = 0;
	});
	 
	$('[id^="datetimepicker"], .datepickertxt').datetimepicker({ format: 'MM-DD-YYYY'});
	$('body').on('focus','.datepickertxt',function(){
		$(this).datetimepicker({ format: 'MM-DD-YYYY'})
	})
	
});
	
	
</script>
<?php
$classLinkTopMouseOverOut = 'onMouseOver="this.style.color=\'#cccccc\';" onMouseOut="this.style.color=\'#cc0000\';"';
echo $spec;
include_once("common/functions.php");

$title1_color="#FFFFFF"; //white 
$title2_color="#000000"; //black
$commonbg_color="#BCD2B0"; //Green
$bottom_common_bg_color="#ECF1EA";
// Done BY Mamta 
	//Pre_op_physician_order (2 Forms)
$bgdark_orange_physician="#C06E2D";
$bgmid_orange_physician="#DEA068";
$border_color_physician="#BB5E00";
$bglight_orange_physician="#FFE6CC";
$row1color_physician="#FFF2E6";
	//Local/gen_anes_record(4 forms)
//$border_blue_local_anes="#080E4C";
$border_blue_local_anes="#323CC0";
$bgdark_blue_local_anes="#3232F0";
$bgmid_blue_local_anes="#80AFEF";
$bglight_blue_local_anes="#EAF4FD";
$tablebg_local_anes="#C5D8FD";
$white="#FFFFFF";

//End edit by mamta

//SET BACKGROUND COLOR OF MANDATORY FIELDS FOR ALL CHART NOTES
$whiteBckGroundColor='background-color:#FFFFFF;';
$chngBckGroundColor='background-color:#F6C67A';
//END SET BACKGROUND COLOR OF MANDATORY FIELDS FOR ALL CHART NOTES


// Done BY Munisha
           //Post_op_nursing_order
$title_post_op_nursing_order="#C0AA1E";
$bgcolor_post_op_nursing_order="#F5EEBD";
$border_post_op_nursing_order="#C0AA1E";
$heading_post_op_nursing_order="#EFE492";
$rowcolor_post_op_nursing_order="#FDFAEB";
          //pre_op_nursing_order
$title_pre_op_nursing_order="#C0AA1E";
$bgcolor_pre_op_nursing_order="#F5EEBD";
$border_pre_op_nursing_order="#C0AA1E";
$heading_pre_op_nursing_order="#EFE492";
//$rowcolor_pre_op_nursing_order="#FAF6DC";
$rowcolor_pre_op_nursing_order="#FDFAEB";
         
		 //op_room_record
$title_op_room_record="#004587";
$bgcolor_op_room_record="#CFE1F7";
$border_op_room_record="#004587";
$heading_op_room_record="#80A7D6";
$rowcolor_op_room_record="#E2EDFB";

 		//laser procedure
$bgcolor_laser_procedure="#F5EEBD";

      //discharge summary sheet
$title_discharge_summary_sheet="#FF950E";
$bgcolor_discharge_summary_sheet="#FBE8D2";
$border_discharge_summary_sheet="#FF950E";
$heading_discharge_summary_sheet="#FCBE6F";
$rowcolor_discharge_summary_sheet="#FBF5EE";
  //Amendments_notes
$title_Amendments_notes="#A0A0C8";
$bgcolor_Amendments_notes="#EEEEFA";
$border_Amendments_notes="#A0A0C8";
$heading_Amendments_notes="#D0D0ED";
$rowcolor_Amendments_notes="#F0F0FA";
//End edit by Munisha


//START FUNCTION TO MAINTAIN LOG IN imwemr WHEN CANCEL THE PATIENT
function logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId='18', $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername='surgercenter', $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false,$connectionFileName,$closeConnectionFileName){
	include($connectionFileName); // imwemr connection
	
	$strQry = "	SELECT 
					procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, 
					sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments  
				FROM 
					schedule_appointments 
				WHERE
					id = '".$intApptId."'";
	$rsData = imw_query($strQry);
	if(imw_num_rows($rsData)>0) {
		$arrData 					= imw_fetch_array($rsData);
		
		$intPatientId 				= $arrData['sa_patient_id'];			//patient id
		
		$dtOldApptDate 				= $arrData['sa_app_start_date'];		//old_appt_date
		$tmOldApptStartTime 		= $arrData['sa_app_starttime'];			//old_appt_start_time
		$tmOldApptEndTime 			= $arrData['sa_app_endtime'];			//old_appt_end_time
		$intOldApptStatusId 		= $arrData['sa_patient_app_status_id'];	//old_status
		$intOldApptProviderId		= $arrData['sa_doctor_id'];				//old_provider
		$intOldApptFacilityId 		= $arrData['sa_facility_id'];			//old_facility
		$strOldApptOpUsername 		= $arrData['sa_madeby'];				//oldMadeBy
		$intOldApptProcedureId 		= $arrData['procedureid'];				//oldMadeBy
		$strOldApptComments 		= $arrData['sa_comments'];				//oldMadeBy
		
		if($blUpdateNew == false){
			$dtNewApptDate 			= $arrData['sa_app_start_date'];		//New_appt_date
			$tmNewApptStartTime 	= $arrData['sa_app_starttime'];			//New_appt_start_time
			$tmNewApptEndTime 		= $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId	= $arrData['sa_doctor_id'];				//New_provider
			$intNewApptFacilityId 	= $arrData['sa_facility_id'];			//New_facility
			$intNewApptProcedureId 	= $arrData['procedureid'];				//NewMadeBy
		}
		
		//making log
		$strInsQry = "INSERT INTO previous_status SET
						sch_id 				= '".$intApptId."',
						patient_id 			= '".$intPatientId."',
						status_time 		= TIME(NOW()),
						status_date 		= CURDATE(),
						status 				= '".$intNewApptStatusId."',
						old_date 			= '".$dtOldApptDate."',
						old_time 			= '".$tmOldApptStartTime."',
						old_provider 		= '".$intOldApptProviderId."',
						old_facility 		= '".$intOldApptFacilityId."',
						statusComments 		= CONCAT('".addslashes($strNewApptComments)."',statusComments),
						oldStatusComments 	= '".$strOldApptComments."',
						oldMadeBy 			= '".$strOldApptOpUsername."',
						statusChangedBy 	= '".$strNewApptOpUsername."',
						dateTime 			= '".date("Y-m-d H:i:s")."',
						new_facility 		= '".$intNewApptFacilityId."',
						new_provider 		= '".$intNewApptProviderId."',
						old_status 			= '".$intOldApptStatusId."',
						old_appt_end_time 	= '".$tmOldApptEndTime."',
						new_appt_date 		= '".$dtNewApptDate."',
						new_appt_start_time	= '".$tmNewApptStartTime."',
						new_appt_end_time 	= '".$tmNewApptEndTime."',
						old_procedure_id 	= '".$intOldApptProcedureId."',
						new_procedure_id 	= '".$intNewApptProcedureId."'";
		
		@imw_query($strInsQry);
	}
	@imw_close($closeConnectionFileName); //CLOSE IMWEMR CONNECTION
}
//END FUNCTION TO MAINTAIN LOG IN imwemr WHEN CANCEL THE PATIENT

//START FUNCTION TO RESTORE LABELS WHEN APPOINTMENT CANCELLED IN iDOC THROUGH SXEMR
function restore_imw_appt_label($fac_id, $waiting_id, $sch_id, $default_time_slot,$connectionFileName,$closeConnectionFileName)
{
	#connection to iDoc database
	include_once($connectionFileName); // imwemr connection
	
	//if we do not have sch_id then get on the basis of waiting id and fac id
	if(empty($sch_id) && $fac_id && $waiting_id)
	{
		$query 	= imw_query("select id from schedule_appointments WHERE iolink_iosync_waiting_id='".$waiting_id."'  AND sa_facility_id = '".$fac_id."'");
		$res	= imw_fetch_assoc($query);
		$sch_id	= $res['id'];
	}
	
	if(!empty($sch_id) && $default_time_slot)
	{
		$q = "SELECT sa_doctor_id, sa_facility_id, sa_app_start_date, sa_app_starttime, sa_app_endtime FROM schedule_appointments WHERE id = '".$sch_id."'";
		$r = imw_query($q);	
		$a = imw_fetch_assoc($r);
		
		$sttm = strtotime($a["sa_app_starttime"]);
		$edtm = strtotime($a["sa_app_endtime"]);
		
		unset($row_arr);
		$q2 = "SELECT id, l_text, l_show_text, labels_replaced, start_time, end_time FROM scheduler_custom_labels 
			WHERE provider 	= '".$a["sa_doctor_id"]."' 
			AND facility 	= '".$a["sa_facility_id"]."' 
			AND start_date 	= '".$a["sa_app_start_date"]."'";
		$r2 = imw_query($q2);
		while($row = imw_fetch_assoc($r2))
		{
			$row_arr[$row['start_time']][$row['end_time']]['id']=$row['id'];
			$row_arr[$row['start_time']][$row['end_time']]['l_text']=$row['l_text'];
			$row_arr[$row['start_time']][$row['end_time']]['l_show_text']=$row['l_show_text'];
			$row_arr[$row['start_time']][$row['end_time']]['labels_replaced']=$row['labels_replaced'];
		}
		
		for($looptm = $sttm; $looptm < $edtm; $looptm += ($default_time_slot * 60))
		{
			$edtm2 = $looptm + ($default_time_slot * 60);
			
			$start_loop_time = date("H:i:00", $looptm);
			$end_loop_time = date("H:i:00", $edtm2);
			
			if($row_arr[$start_loop_time][$end_loop_time]['id'])
			{
				unset($row);
				$row=$row_arr[$start_loop_time][$end_loop_time];
				$new_entry = $row["labels_replaced"];
				$l_text = $row["l_show_text"];
				#temp fix to retrive labels if it wasn't in label_replaced field
				if(trim($row["labels_replaced"]) == "" && trim($row["l_text"]))
				{ 
					if(trim($row["l_text"]) != trim($row["l_show_text"]) && stristr($row["l_text"],';')===false)
					{
						$row["labels_replaced"]	= '::'.$sch_id.':'.$row["l_text"];
					}
				}
				#temp fix ends here
			
				if(trim($row["labels_replaced"]) != "")
				{ 
					$arr_lbl_replaced = explode("::", $row["labels_replaced"]);
					if(count($arr_lbl_replaced) > 0)
					{ 
						foreach($arr_lbl_replaced as $this_lbl_replaced)
						{
							$arr_this_replaced2 = explode(":", $this_lbl_replaced);
							if(trim($arr_this_replaced2[0]) == $sch_id)
							{ 
								$new_entry = str_replace("::".$arr_this_replaced2[0].":".$arr_this_replaced2[1], "", $row["labels_replaced"]);
						
								if(trim($row["l_show_text"]) != ""){
									$l_text = $row["l_show_text"]."; ".$arr_this_replaced2[1];
								}else{
									$l_text = $arr_this_replaced2[1];
								}
							}
						}
					}
				}
				$upd22 = "UPDATE scheduler_custom_labels SET l_show_text = '".$l_text."', labels_replaced = '".$new_entry."' WHERE id =	'".$row["id"]."'";
				imw_query($upd22);
			}
		}
	}
	@imw_close($closeConnectionFileName); //CLOSE IMWEMR CONNECTION
}
//END FUNCTION TO RESTORE LABELS WHEN APPOINTMENT CANCELLED IN iDOC THROUGH SXEMR


?>