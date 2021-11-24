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
$title = "Accounting Notes";  
require_once('acc_header.php');
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");

$patient_id = $_SESSION['patient'];
$sel_pat=imw_query("select fname,lname from patient_data where id='$patient_id' limit 0,1");
$fet_pat=imw_fetch_array($sel_pat);
$pat_lname=$fet_pat['lname'];
if($fet_pat['fname']){
	$pat_fname=', '.$fet_pat['fname'];
}
$pat_name=$pat_lname.$pat_fname.' - '.$patient_id;
$tm_patient_name=$pat_lname.$pat_fname;
if($_REQUEST['frm_sub']>0 && $_REQUEST['notes']!=""){
	//Saving Request values
	$saveValArr = array();
	if(isset($patient_id) && empty($patient_id) == false) $saveValArr['patient_id'] = $patient_id;
	if(isset($_REQUEST['enc_id']) && empty($_REQUEST['enc_id']) == false) $saveValArr['encounter_id'] = $_REQUEST['enc_id'];
	if(isset($_REQUEST['notes_type']) && empty($_REQUEST['notes_type']) == false) $saveValArr['commentsType'] = $_REQUEST['notes_type'];
	if(isset($_REQUEST['notes']) && empty($_REQUEST['notes']) == false) $saveValArr['encComments'] = core_refine_user_input($_REQUEST['notes']);
	if(empty($_REQUEST['general']) == false) $saveValArr['commentsType'] = 'general';
	$task_on_reminder=(isset($_REQUEST['task_on_reminder']) && $_REQUEST['task_on_reminder']=='yes') ?'1':'0';
	
	//Task Assign values
	if(isset($_REQUEST['assignFor']) && empty($_REQUEST['assignFor']) == false && is_array($_REQUEST['assignFor']) && count($_REQUEST['assignFor']) > 0){
		$saveValArr['task_assign'] = 2;
		$saveValArr['task_assign_by'] = $_SESSION['authId'];
		$saveValArr['task_assign_for'] = implode(',', $_REQUEST['assignFor']);
		$saveValArr['task_assign_date'] = date('Y-m-d H:i:s');
	}
	
	$saveValArr['encCommentsDate'] = date('Y-m-d');
	$saveValArr['encCommentsTime'] = date('H:i:s');
	$saveValArr['encCommentsOperatorId'] = $_SESSION['authId'];
	$saveValArr['reminder_date'] = getDateFormatDB($_REQUEST['reminder_date']);
	$saveValArr['task_onreminder'] = $task_on_reminder;

	$insertId = AddRecords($saveValArr, 'paymentscomment','true',false);
    if($insertId) {
        if($saveValArr['encounter_id']) {
            $getdos_sql = "SELECT encounter_id,date_of_service FROM patient_charge_list 
								WHERE patient_id = '$patient_id' and encounter_id='".$saveValArr['encounter_id']."' and del_status='0' ";
            $getdosQry=imw_query($getdos_sql);
			$getDosRow = imw_fetch_assoc($getdosQry);
			$notes_pat_dos=$getDosRow['date_of_service'];
        }
        $tm_insert_qry = "INSERT INTO tm_assigned_rules(section_name,status,changed_value,date_of_service,encounter_id,
                        patientid, patient_name, operatorid, payment_comtId,notes_users,reminder_date,task_on_reminder)
                     VALUES('Accounting Notes', '0', '" . $saveValArr['encComments'] . "', '" . $notes_pat_dos . "', '" . $saveValArr['encounter_id'] . "',
                    '" . $patient_id . "', '" . $tm_patient_name . "', '" . $saveValArr['task_assign_by'] . "', '" . $insertId . "', '" . $saveValArr['task_assign_for'] . "', '" . $saveValArr['reminder_date'] . "','".$saveValArr['task_onreminder']."') ";

        imw_query($tm_insert_qry);
    }
	// imw_query("insert into paymentscomment set patient_id='$patient_id',encounter_id='$enc_id',commentsType='$commentsType',
	// 			encComments='$encComments',encCommentsDate='$encCommentsDate',encCommentsOperatorId='$encCommentsOperatorId'");
	if($insertId) echo "<script>window.location.href='ac_notes.php?status_up=yes';</script>";			
}
if($_REQUEST['del_comm']){
	$commentId = $_REQUEST['del_comm'];
	$delComments = "DELETE FROM paymentscomment WHERE commentId = '$commentId'";
	$delCommentsQry = imw_query($delComments);
    if($delCommentsQry) {
        $tm_del = "DELETE FROM tm_assigned_rules WHERE payment_comtId = '$commentId'";
        imw_query($tm_del);
    }
}

//Save the modified task status
$saveTaskStaus = false;
if(isset($_REQUEST['saveCall']) && empty($_REQUEST['saveCall']) == false && $_REQUEST['saveCall'] == 'saveTask'){
	$returnVal = false;
	$commentID = (isset($_REQUEST['commentId']) && empty($_REQUEST['commentId']) == false) ? $_REQUEST['commentId'] : '';
	$reqTaskAssign = (isset($_REQUEST['assigntask']) && empty($_REQUEST['assigntask']) == false) ? $_REQUEST['assigntask'] : 1;
	$reqTaskDone = (isset($_REQUEST['taskDone']) && empty($_REQUEST['taskDone']) == false) ? $_REQUEST['taskDone'] : 1;
	$reqTaskFor = (isset($_REQUEST['taskFor']) && empty($_REQUEST['taskFor']) == false && is_array($_REQUEST['taskFor'])) ? $_REQUEST['taskFor'] : '';
	$reqTaskNotes = (isset($_REQUEST['task']) && empty($_REQUEST['task']) == false) ? $_REQUEST['task'] : '';
	
	if(empty($commentID) == false){
		//Fetch current record
		$chkQry = imw_query('SELECT commentId FROM paymentscomment WHERE commentId = '.$commentID.' ');
		if($chkQry && imw_num_rows($chkQry) > 0){
			$rowFetch = imw_fetch_assoc($chkQry);
			
			$valArr = array();
			$valArr['task_assign'] = $reqTaskAssign;
			$valArr['task_done'] = $reqTaskDone;
			$valArr['task_assign_for'] = implode(',', $reqTaskFor);
			$valArr['encComments'] = $reqTaskNotes;
			
			if($reqTaskAssign == 2 && $reqTaskDone == 1) $valArr['task_assign_date'] = date('Y-m-d H:i:s');
			if($reqTaskAssign == 1 && $reqTaskDone == 2) $valArr['task_modify_date'] = date('Y-m-d H:i:s');
			
			$updateStatus = UpdateRecords($commentID, 'commentId', $valArr, 'paymentscomment','true',false);
            if($updateStatus) {
                $task_status='0';
                if($reqTaskDone == 2) $task_status='1';
                $sql ="Update tm_assigned_rules set status='".$task_status."',operatorid='".$_SESSION['authId']."',changed_value='".$valArr['encComments']."',notes_users='".$valArr['task_assign_for']."' where payment_comtId = '$commentId'";
                imw_query($sql);
            }
			if($updateStatus) $returnVal = true;
		}
	}
	if($returnVal == true) $saveTaskStaus = $returnVal;
	unset($_REQUEST);
}

//Setting > Billing > Phrases Typeahead
$phraseArr = array();
$sel_rec_comm=imw_query("select * from int_ext_comment where status='0' order by comment");
while($sel_comm=imw_fetch_assoc($sel_rec_comm)){
	$coment = addslashes($sel_comm['comment']);
 	array_push($phraseArr, $coment);
}		
if(count($phraseArr) > 0) $phraseArr = json_encode($phraseArr);
?>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css">
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//Phrases Typeahead
		var phraseArr = <?php echo $phraseArr; ?>;
		$('#notes,textarea[id^="notes_comm_"],#taskNote').typeahead({source:phraseArr});
		window.focus();
		
		//For task Assign Modal
		$('body').on('click', '[type=checkbox]#task, [type=checkbox]#taskdone', function(){
			var taskAssign = 'task';
			var taskDone = 'taskdone';
			
			var idname = $(this).attr('id');
			
			switch(idname){
				case 'task':
					if($(this).is(':checked') && $('#'+taskDone).is(':checked') == false)  $('#'+taskDone).prop('checked', true);
					else $('#'+taskDone).prop('checked', false);
				break;
				
				case 'taskdone':
					if($(this).is(':checked') && $('#'+taskAssign).is(':checked') == false)  $('#'+taskAssign).prop('checked', true);
					else $('#'+taskAssign).prop('checked', false);
				break;
			}
		});
		
		<?php
			if($saveTaskStaus == true){
		 ?>
		 	fAlert('Task updated successfully');
		 <?php 
	 		}
		 ?>
	});
	
	$(function(){
		//To toggle Assign user selectpicker
		$('body').on('click', '#assignTask',function(){
			var propCheck = $(this).prop('checked');
			var toggleEle = $($(this).data('select'));
			if(propCheck == true){
				if(toggleEle.hasClass('hide') == true) toggleEle.removeClass('hide');
				if(toggleEle.find('select').prop('disabled') == true) toggleEle.find('select').prop('disabled', false);
			}else{
				if(toggleEle.hasClass('hide') == false) toggleEle.addClass('hide');
				if(toggleEle.find('select').prop('disabled') == false) toggleEle.find('select').prop('disabled', true);
			}
			
			toggleEle.find('select').selectpicker('val', '').selectpicker('refresh');
		});
	});
	
	function print_note(file_path){
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
 		top.html_to_pdf(file_path,'l');
	}
	function delComment(comId, eId, cnfrm){
		if($("#acc_view_pay_only").val()==1  || $("#acc_edit_financials").val()==0){
			view_only_acc_call(0);
			return false;
		}
		
		if(typeof(cnfrm)=="undefined"){
			top.fancyConfirm("Are you sure you want to delete this comment?", '', "top.delComment("+comId+","+eId+",true)"); 
		}
		else{
			reloadLocation("ac_notes.php?encounter_id="+eId+"&del_comm="+comId);
		}
	}
	function editCommentDetails(commId){
		if($("#acc_view_pay_only").val()==1  || $("#acc_edit_financials").val()==0){
			view_only_acc_call(0);
			return false;
		}
		var newComments = $("#notes_comm_"+commId).val();
		var typeComment = $("#notes_type_"+commId).val();
		var reminder_date = $("#reminder_date_"+commId).val();
		var task_on_reminder = $("#task_on_reminder"+commId).val();
		var type_task_for = $("#taskassigned_for"+commId).data("taskfor");
		
		if(typeComment==''){
			top.fAlert("Please select Comment type.")
			return false;
		}
		
		$.ajax({
			type: "POST",
			url: "editComments.php?commId="+commId+"&newComments="+escape(newComments)+"&typeComment="+typeComment+"&reminder_date="+reminder_date+"&type_task_for="+type_task_for+"&task_on_reminder="+task_on_reminder,
			success: function(r){
				top.fAlert("Comment updated successfully.",'','top.reloadLocation("ac_notes.php")');
			}
		});
	}
	
	function reloadLocation(file) {
		window.location.href=file;
	}
</script>
<style>.table tbody tr td{vertical-align: top !important;}</style>
<div class="table-responsive" style="height:350px; overflow:auto; width:100%;">
<?php
	//Users/Providers array
	$userArr = array();
	$chkQry = imw_query('SELECT id, fname, mname, lname FROM users WHERE delete_status = 0 and locked=0');
	if($chkQry && imw_num_rows($chkQry) > 0){
		while($rowFetch = imw_fetch_assoc($chkQry)){
			$phyName = core_name_format($rowFetch['lname'], $rowFetch['fname'], $rowFetch['mname']);
			$phyIni = substr($rowFetch['fname'],0,1).substr($rowFetch['mname'],0,1).substr($rowFetch['lname'],0,1);
			$phyId = $rowFetch['id'];
			
			if(empty($phyName) == false) $userArr[$phyId] = array('phyName' => $phyName, 'phyIni' => $phyIni);
		}
	}

    $data_txt='<div class="purple_bar"> 
				<span>Accounting Notes</span>
				<span style="padding-left:20%;">'.$pat_name.'</span>
			</div>
	<table class="table table-bordered table-hover table-striped">';
	 $pdf_data_txt='<table class="table_collapse">
        <tr height="25" class="text_b_w">
            <td style="text-align:left; width:400px" class="text_b_w">&nbsp;&nbsp;Accounting Notes</td>
            <td style="text-align:left;width:666px" class="text_b_w">&nbsp;&nbsp;'.$pat_name.'</td>
        </tr></table><table class="table_collapse" style="background:#FFF3E8;">';	
		?>
        <?php
			$getCaseTypeStr = "SELECT encounter_id,date_format(date_of_service,'%m-%d-%Y') as dateOfService,
								date_of_service as dateOfService_db FROM patient_charge_list 
								WHERE patient_id = '$patient_id' and del_status='0' order by date_of_service desc";
			$getCaseTypeQry = imw_query($getCaseTypeStr);
			while($getCaseTypeRow = imw_fetch_array($getCaseTypeQry)){
				$pat_dos_arr[]=$getCaseTypeRow['dateOfService'];
				$pat_dos_db_arr[$getCaseTypeRow['dateOfService']]=$getCaseTypeRow['dateOfService_db'];
				$pat_dos_enc_arr[$getCaseTypeRow['encounter_id']]=$getCaseTypeRow['dateOfService'];
			}
			 $getCommentsStr = "SELECT paymentscomment.*
                                FROM paymentscomment 
								WHERE  paymentscomment.c_type!='batch'
								and paymentscomment.patient_id='$patient_id' 
                                order by paymentscomment.encCommentsDate DESC,paymentscomment.encCommentsTime DESC,commentId desc";					
            $getCommentsQry = imw_query($getCommentsStr);
            if(imw_num_rows($getCommentsQry)>0){
        
				$data_txt .='
						<tr class="grythead">
							<th class="text-nowrap">Notes Date</th>
							<th>Int. / Ext.</th>
							<th>DOS</th>
							<th class="text-nowrap">E. ID</th>
							<th>Notes</th>
							<th>Task For</th>
							<th class="text-nowrap">Task on Rem. Date</th>
							<th>Reminder Date</th>
							<th>Mod. By</th>
							<th>Action</th>
						</tr>';
				$pdf_data_txt .='
						<tr class="text_b" style="height:22px;">
							<td class="text_b_w" style="text-align:left; white-space:nowrap;"> &nbsp;&nbsp;Notes Date</td>
							<td class="text_b_w" style="text-align:left; white-space:nowrap;">&nbsp;&nbsp;Int. / Ext.</td>
							<td class="text_b_w" style="text-align:left;">&nbsp;&nbsp;DOS</td>
							<td class="text_b_w" style="text-align:left; white-space:nowrap;">&nbsp;&nbsp;E. ID</td>
							<td class="text_b_w" style="text-align:left;">&nbsp;&nbsp;Notes</td>
							<td class="text_b_w" style="text-align:left;">&nbsp;&nbsp;Reminder Date</td>
							<td class="text_b_w" style="text-align:left;">&nbsp;&nbsp;Modified By</td>
						</tr>';		
		?>			
        <?php
				while($getCommentsRows = imw_fetch_array($getCommentsQry)){
					$dateOfService = $pat_dos_enc_arr[$getCommentsRows['encounter_id']];
					$encounter_id = $getCommentsRows['encounter_id'];
					if($encounter_id==0){$encounter_id="";}
					$commentId = $getCommentsRows['commentId'];
					$commentsType = $getCommentsRows['commentsType'];
					$encCommentsDate = $getCommentsRows['encCommentsDate'];
					if(isset($getCommentsRows['encCommentsTime']) && $getCommentsRows['encCommentsTime']!='00:00:00' && $getCommentsRows['encCommentsTime']!=''){
						$encCommentsDate = $encCommentsDate.' '.$getCommentsRows['encCommentsTime'];
						$encCommentsDate = date('m-d-Y h:i A', strtotime($encCommentsDate));
					}else{
						list($commentsYear, $commentsMonth, $commentsDay) = explode("-", $encCommentsDate);
						//$encCommentsDate = $commentsMonth."-".$commentsDay."-".$commentsYear;
						$encCommentsDate = date('m-d-Y',mktime(0,0,0,$commentsMonth,$commentsDay,$commentsYear));
					}
					$encComments = core_extract_user_input($getCommentsRows['encComments']);
					$encCommentsOperatorId = $getCommentsRows['encCommentsOperatorId'];
					$reminder_date = get_date_format($getCommentsRows['reminder_date']);
					//---------------------- GETTING OPERATOR NAME FROM ID ----------------------//
						// $getOperatorNameStr = "SELECT * FROM users where id = '$encCommentsOperatorId'";
						// $getOperatorNameQry = imw_query($getOperatorNameStr);
						// $getOperatorNamedRow = @imw_fetch_array($getOperatorNameQry);
						// $operatorFName = $getOperatorNamedRow['fname'];
						// $operatorLName = $getOperatorNamedRow['lname'];
						// $operatorMName = $getOperatorNamedRow['mname'];
						// $operatorName = substr($operatorFName,0,1).substr($operatorMName,0,1).substr($operatorLName,0,1);
						
						$operatorName = (isset($userArr[$encCommentsOperatorId]) && empty($userArr[$encCommentsOperatorId]['phyIni']) == false) ? $userArr[$encCommentsOperatorId]['phyIni'] : '';
						
					//---------------------- GETTING OPERATOR NAME FROM ID ----------------------//
				$sel_int_comm="selected";
				$sel_ext_comm="";		
		   		if(strtolower($commentsType)=="external"){
					$sel_ext_comm="selected";	
					$sel_int_comm="";
				}
				$hide_comm_type="";
				$gen_comm_opt="";
				if(strtolower($commentsType)=="general"){
					$hide_comm_type=" disabled";
					$sel_ext_comm="";
					$sel_int_comm="";
					$gen_comm_opt='<option value="general" selected="selected">General</option>';
				}
				
				//Assigned Task Details
				$taskAssign = (isset($getCommentsRows['task_assign']) && empty($getCommentsRows['task_assign']) == false) ? $getCommentsRows['task_assign'] : '';
				$taskDone = (isset($getCommentsRows['task_done']) && empty($getCommentsRows['task_done']) == false) ? $getCommentsRows['task_done'] : '';
				
				//Getting Assigned Users
				$taskAssignFor = '';
				if(isset($getCommentsRows['task_assign_for']) && empty($getCommentsRows['task_assign_for']) == false ){
					$taskFor = array();
					$taskFor = explode(',' ,$getCommentsRows['task_assign_for']);
					if(count($taskFor) > 0){
						$tmpArr = array();
						foreach($taskFor as $userId){
							if(isset($userArr[$userId]) && isset($userArr[$userId]['phyName'])) $tmpArr[] = $userArr[$userId]['phyName'];
						}
                        if(count($tmpArr) > 0) {$taskAssignFor = implode(',',$tmpArr);}
                        if(count($tmpArr)>1) {$taskAssignForUsers = 'Multi';}else {$taskAssignForUsers = implode(';',$tmpArr);}
					}	
				} else {
                    $taskAssignForUsers = 'Not Assigned';
                }
					
				$data_txt .='<tr>
					<td>'.$encCommentsDate.'</td>
					<td>
						<select name="notes_type_'.$commentId.'" id="notes_type_'.$commentId.'" '.$hide_comm_type.' class="selectpicker" data-width="100%">
                            <option value="Internal" '.$sel_int_comm.'>Internal</option>
                            <option value="External" '.$sel_ext_comm.'>External</option>
							'.$gen_comm_opt.'
                        </select>
					</td>
					<td class="text-nowrap">'.$dateOfService.'</td>
					<td>'.$encounter_id.'</td>';
					
					//Adding Update task code
					$textraeaTxt = '<td><textarea rows="2" cols="45" name="notes_comm_'.$commentId.'" id="notes_comm_'.$commentId.'" class="form-control">'.$encComments.'</textarea></td>';
					$toolTipVal = '';
					//if(empty($taskAssign) == false && $taskAssign == 2 && empty($taskAssignFor) == false && $taskDone == 1){
						$taskDoneVal = ($taskDone == 1) ? 'No' : 'Yes';
						$taskForVal = $taskAssignFor;
						
						if(empty($taskForVal) == false){
							$toolString = '<p><strong>Task For</strong> : '.$taskForVal.'</p><p><strong>Task Done</strong> : '.$taskDoneVal.'</p>';
							
							//$toolTipVal = show_tooltip($toolString, 'left');
						}
						
						$backgroundColor = ($taskAssign == 2 && $taskDone == 1) ? 'style="background-color:#f8940685"' : '';
						
						$textraeaTxt = '<td>
							<div class="row">
								<div id="acc_notes">
									<textarea rows="2" cols="50" name="notes_comm_'.$commentId.'" id="notes_comm_'.$commentId.'" class="form-control">'.$encComments.'</textarea>
								</div>
							</div>
						</td>';
						$textraeaTxt.= '<td '.$backgroundColor.'>
							<div class="row">
								<div class="input-group">
									<div class="text_purple pointer" id="taskassigned_for'.$commentId.'" data-comId="'.$commentId.'" data-taskfor="'.$getCommentsRows['task_assign_for'].'" data-taskdone="'.$taskDone.'" data-notes="'.$encComments.'" data-task="'.$taskAssign.'" '.$toolTipVal.' onClick="editNotes(this);">
										'.$taskAssignForUsers.'
									</div>
								</div>
							</div>
						</td>';
						unset($toolTipVal);
					//}
					
				$task_on_reminder = 'no';
				$task_check = ' ';
				if($getCommentsRows['task_onreminder']==1){
					$task_on_reminder = 'yes';
					$task_check = ' checked="checked" ';
				}
				
				$data_txt .='	
					'.$textraeaTxt.'
					<!-- <td><textarea rows="2" cols="45" name="notes_comm_'.$commentId.'" id="notes_comm_'.$commentId.'" class="form-control">'.$encComments.'</textarea></td> -->
					<td class="text-nowrap" style="width:140px;">
						<div class="checkbox">
							<input type="checkbox" id="task_on_reminder'.$commentId.'" value="'.$task_on_reminder.'" name="task_on_reminder'.$commentId.'" onClick="task_reminder_date('.$commentId.');"  '.$task_check.' />
							<label for="task_on_reminder'.$commentId.'">&nbsp;</label>
						</div>
					</td>
					<td class="text-nowrap" style="width:140px;">
					<div class="input-group">
						<input type="text" name="reminder_date_'.$commentId.'" id="reminder_date_'.$commentId.'" value="'.$reminder_date.'" class="form-control date-pick">
						<label class="input-group-addon pointer" for="reminder_date_'.$commentId.'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                    </div></td>
					<td>'.$operatorName.'</td>
					<td class="text-center">';
					if(strtolower($commentsType)=="general"){
						$encounter_id=0;
					}
				$data_txt .='<a href="javascript:void(0);" onClick="javascript:editCommentDetails('.$commentId.');"><img src="../../library/images/save.gif" alt="Edit" style="border:none;"></a>
							&nbsp;&nbsp;<a href="javascript:void(0);" onClick="javascript:delComment('.$commentId.', '.$encounter_id.');"><img src="../../library/images/del.png" alt="Del" style="border:none;"></a>
					</td>
				</tr>';
				
				$pdf_data_txt .='<tr>
					<td style="text-align:center; width:126px; white-space:nowrap;"  class="text_10">'.$encCommentsDate.'</td>
					<td style="text-align:left; width:101px;" class="text_10">'.$commentsType.'</td>
					<td style="text-align:center; width:101px; white-space:nowrap;" class="text_10">'.$dateOfService.'</td>
					<td style="text-align:left; width:107px;" class="text_10">'.$encounter_id.'</td>
					<td style="text-align:left; width:436px;"  class="text_10">'.core_extract_user_input($encComments).'</td>
					<td style="text-align:center; width:96px;"  class="text_10">'.$reminder_date.'</td>
					<td style="text-align:left; width:71px;"  class="text_10">'.$operatorName.'</td>
				</tr>';
				
			}
     }else{
        $data_txt .='<tr><td colspan="8" class="text-center lead">'.imw_msg("no_rec").'</td></tr>';
		$pdf_data_txt .='<tr bgcolor="#FFFFFF"><td colspan="7" class="text_10b" style="height:25px;text-align:center;padding-left:150px;"><strong>No Record Found</strong></td></tr>';
    } 
     $data_txt .='</table>';
	 $pdf_data_txt .='</table>';
	 echo $data_txt;
	if(trim($data_txt) != ""){
		$PdfText = $pdf_data_txt;
		$hrml_file_path=write_html($PdfText);
	}
   ?> 
</div>
<div>
	<form action="ac_notes.php" method="post" name="note_form">	
		<div class="purple_bar">
			<div class="row">
				<div class="col-sm-6"><span>Add New Note</span></div>
			</div>
		</div>
	  
		<input type="hidden" name="frm_sub" value="1">
	   <table class="table table-bordered table-hover table-striped">
			<tr class="text-left">
				<td>
					<label for="general">General</label>
					<div class="checkbox">
						<input type="checkbox" name="general" id="general" onClick="show_other_opt();"/>
						<label for="general"></label>
					</div>
				</td>
				<td>
					<label for="enc_dos">DOS</label>
					<select name="enc_dos" id="enc_dos" class="selectpicker" onChange="get_enc_fun(this.value,'<?php echo $patient_id;?>');" data-width="100%">
						<option value="">Select DOS</option>
						<?php 
							foreach($pat_dos_db_arr as $dat_key => $dat_val){   
							if($g==0){$srh_dos=$dat_val;}
						?>
							<option value="<?php echo $dat_val; ?>"><?php echo $dat_key; ?></option>
						<?php		
							}
						?>
					</select>
				</td>
				<td>
					<label for="enc_id">Encounter ID</label>
					<select name="enc_id" id="enc_id" class="selectpicker" data-width="100%">
						<option value="">Encounter ID</option>
					</select>
				</td>
				<td>
					<label for="notes_type">Type</label>
					<select name="notes_type" id="notes_type" class="selectpicker" data-width="100%">
						<option value="Internal">Internal</option>
						<option value="External">External</option>
					</select>
				</td>
				<td>
					<label for="selectAssignFor">Assign as a Notes/Task for : </label>
					<select class="selectpicker" id="selectAssignFor" name="assignFor[]" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Notes" data-size="5" multiple data-selected-text-format="count > 1">
						<?php 
							$optString = '';
							if(count($userArr) > 0){
								foreach($userArr as $phyId => $phyVal){
									$sel="";
									if($phyId==$_SESSION['authId'] && isDefaultUserSelected()){
										$sel=" selected";
									}
									$optString .= '<option value="'.$phyId.'" '.$sel.'>'.$phyVal['phyName'].'</option>';
								}
							}
							echo $optString;
						?>
					</select>
				</td>
                <td>
					<label for="reminder_date">Reminder Date : </label>
                    <div class="input-group">
						<input type="text" name="reminder_date" id="reminder_date" value="<?php echo date('m-d-Y'); ?>" class="form-control date-pick">
						<label class="input-group-addon pointer" for="reminder_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
					<label for="task_on_reminder">Task On Reminder Date</label>
					<div class="checkbox">
						<input type="checkbox" name="task_on_reminder" id="task_on_reminder" value="yes" checked="checked" onClick="task_reminder_date();"/>
						<label for="task_on_reminder">&nbsp;</label>
					</div>
				</td>
				<td colspan="5">
					<label for="notes">Note</label>
					<textarea cols="75" rows="2" name="notes" id="notes" class="form-control"></textarea>
				</td>
			</tr>
		</table>
	 </form>   
</div>
</div>
	<footer>
		<div class="text-center" id="module_buttons">
			<input type="button" id="save" class="btn btn-success" value="Done" onClick="note_sub();" name="save">
			<input type="button" id="print" class="btn btn-success" value="Print" onClick="print_note('<?php echo $hrml_file_path; ?>');" name="print">
			<input type="button" id="close" class="btn btn-danger" value="Close" name="close" onClick="window.close();">
		</div>
	</footer>
	
	<!-- Task Assign Modal -->
	<div id="assignModal" class="modal " role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary">
		        	<button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title">Manage Task</h4>
		      	</div>
				<div class="modal-body">
					<form name="assignTaskForm" id="assignTaskForm" method="POST">
						<input type="hidden" id="commentId" name="commentId" value="" />
						<input type="hidden" name="saveCall" value="saveTask" />
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-6">
										<div class="checkbox">
											<input type="checkbox" id="task" value="2" name="assigntask"/>
											<label for="task">Task Assigned</label>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="checkbox">
											<input type="checkbox" id="taskdone" value="2" name="taskDone"/>
											<label for="taskdone">Task Done</label>
										</div>
									</div>
								</div>
							</div>	
							<div class="col-sm-12 pt5">
								<div class="input-group">
									<label class="input-group-addon">Assign Task for</label>
									<select class="selectpicker" name="taskFor[]" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Please select" data-size="5" multiple id="taskfor" data-selected-text-format="count > 1">
										<?php 
											$optString = '';
											if(count($userArr) > 0){
												foreach($userArr as $phyId => $phyVal){
													$optString .= '<option value="'.$phyId.'">'.$phyVal['phyName'].'</option>';
												}
											}
											echo $optString;
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-12 pt5">
								<div class="form-group">
									<div class="input-group">
										<label class="input-group-addon">Task</label>
										<textarea name="task" class="form-control" id="taskNote" rows="2"></textarea>
									</div>
								</div>
								
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center" id="module_buttons">
					<button type="button" class="btn btn-success" id="saveAssignTask">Done</button>&nbsp;
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
</body>
</html>
<?php
if($_REQUEST['status_up']==""){
	echo "<script type='text/javascript'>get_dos_fun('".$srh_dos."','".$patient_id."');</script>";
}
?> 
