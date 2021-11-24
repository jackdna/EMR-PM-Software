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
/**
 * File: inbound_fax.php
 * Purpose: Holding area for the inbound fax messages
 * Access Type: Direct
 **/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/Functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/dhtmlgoodies_tree.class.php');
$library_path = $GLOBALS['webroot'].'/library';
$tree = new dhtmlgoodies_tree();

$patient_id = $_SESSION['patient'];

if( isset($_POST['action']) && $_POST['action']==='moveToPtDoc' ){
	
	$faxDocs = $_POST['faxData'];
	if( !is_array($faxDocs) || count($faxDocs)==0){
		echo 'Please provide correct fax id.';
		exit;
	}
	
	foreach($faxDocs as $faxDoc){
		$sqlMoveDoc = 'UPDATE `inbound_fax` SET `patient_id`='.$faxDoc['ptId'].', `allocated_at`=\''.date('Y-m-d H:i:s').'\', `allocated_by`='.((int)$_SESSION['authId']).', `fax_folder`=\''.$faxDoc['folder'].'\' WHERE `id`='.$faxDoc['faxId'];
		imw_query($sqlMoveDoc);
	}
	
	echo 'success';
	exit;
}

//------------------------
if( isset( $_POST['id'] ) && $_POST['task']== 'delete' )
{
	$update_qry="UPDATE 
					`inbound_fax`
				SET
					del_status = 1,
					del_by = '".$_SESSION['authId']."',
					del_at = '".date('Y-m-d H:i:s')."'
				WHERE	
					id = '".$_POST['id']."'
				";
	$exe_qry = imw_query( $update_qry );
	if($exe_qry)
	{	
		echo 'success';
	}
	exit;	
}

$qry = imw_query("SELECT `id`, `from_number`, `files`, `message`, date_format(`received_at`, '%m-%d-%Y') AS 'date_received', date_format(`received_at`, '%h:%i %p') AS 'time_received' FROM `inbound_fax` WHERE `patient_id`=0 AND `from_number`!='' AND `del_status`=0 ORDER BY `received_at` DESC");
while($data=imw_fetch_assoc($qry))
{
	$faxes[] = $data;
}

$fax_count =  count($faxes);

?>
<!DOCTYPE html>
<html>
<head>
<title>Consult Letter</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript">
	var REF_PHY_FORMAT = '<?php $GLOBALS['REF_PHY_FORMAT'];?>';
</script>
<!--<script type="text/javascript" src="js/jsscript.js"></script>-->

<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>

<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
	<script src="<?php echo $library_path; ?>/js/html5shiv.min.js"></script>
	<script src="<?php echo $library_path; ?>/js/respond.min.js"></script>
<![endif]-->

<style type="text/css">
body{padding: 1px;background-color:#d9e4f2;}
.vision_bg{padding: 5px 10px;}
.container{width: 100%;}
.left, #inbound_fax{border: 2px inset #ddd;width:100%;}
.left{
	padding: 2px 0 0 2px;
	height: 590px;
	overflow-y: scroll;
	overflow-x: hidden;
}
#inbound_fax{height:592px;}
input[type="button"]{cursor: pointer;}
#fName, #lName{width: 100px;}
.faxData tr>td{
	border: 1px solid #FCFCFC;
}
.faxData > tbody tr:last-child > td{border-bottom: 0px;}
.faxData > tbody > tr>td{padding: 4px 2px 4px 4px;}
/*.faxData > tbody > tr>td:first-child{text-align: center;}*/
.faxData > tbody > tr>td:last-child{padding-left: 4px;}
#pdfLink{cursor: pointer;color: #9900DF;}
.selicon{
	width: 14px;
	margin-right: 2px;
	vertical-align: text-bottom;
	visibility: hidden;
}
#faxList{
	overflow-y: scroll;
	overflow-x: hidden;
	height: 620px;
	border-bottom: 2px solid #FCFCFC
}
#bottom{padding-top: 10px;text-align: center;}
</style>
</head>
<body>
<div class="mainwhtbox">
	<!--Page Title-->
	<div class="admpophead"><h3>Received Fax Messages</h3></div>
<div id="faxList" class="whtbox">
	<table class="table table-bordered table-hover adminnw faxData" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th style="width: 36px;">Del</th>
				<th style="width: 40px;">Sr.No.</th>
				<th style="width: 156px;">Date</th>
				<th style="width: 80px;">From</th>
				<th style="width: 150px;">Fax</th>
				<th style="width: 90px;">Folder</th>
				<th style="width:182px;">Patient Search</th>
				<!--<th class="txt_11b vision_bg white_color">Allocate</th>-->
			</tr>
		</thead>
		<tbody>
<?php
	if($fax_count<1):
?>
			<tr>
				<td colspan="7" style="text-align: center;">No fax received/pending.</td>
			</tr>
<?php
	else:
		$i=0;
		foreach($faxes as $fax):
?>
			<tr  id="<?php echo $fax['id']; ?>">
				<td class="txt_11">
					<img class="del-record" src="<?php echo $GLOBALS['php_server']; ?>/library/images/del.png">
				</td>
				<td class="txt_11">
					<img class="selicon" id="selicon_<?php echo ++$i; ?>" src="<?php echo $GLOBALS['php_server']; ?>/library/images/checkmark.png">
					<?php echo $i; ?>
				</td>
				<td class="txt_11"><?php echo $fax['date_received'].' '.$fax['time_received']; ?></td>
				<td class="txt_11"><?php echo $fax['from_number']; ?></td>
				<td class="txt_11">
					<span id="pdfLink" onclick="viewPdf('<?php echo $GLOBALS['php_server'].'/data/'.constant('PRACTICE_PATH').'/fax_files/'.$fax['files']; ?>');"><?php echo $fax['files']; ?></span>
				</td>
				<td class="txt_11">
					<select id="sel_folder_<?php echo $i; ?>" class="selectpicker">
						<option value="">Please Select</option>
						<option value="consult_letters">Consult Letters</option>
						<option value="consent_forms">Consent Forms</option>
						<option value="pt_docs">Pt. Docs.</option>
					</select>
				</td>
				<td class="form-inline">
					<label>
					<input type="text" id="patientSearch_<?php echo $i; ?>" onkeypress="{if (event.keyCode==13) return selPatient(<?php echo $i; ?>);}" onKeyDown="blank_pId(<?php echo $i; ?>)" class="form-control" style="width: 150px" />
					<input type="hidden" id="pateintId_<?php echo $i; ?>" class="patientIds" itemKey="<?php echo $i; ?>" />
					<input type="hidden" id="fax_id_<?php echo $i; ?>" value="<?php echo $fax['id']; ?>" />
					<a href="javascript:void(0);" id="imgSel_<?php echo $i; ?>" onclick="selPatient(<?php echo $i; ?>);" tabindex="3" onkeypress="{if (event.keyCode==13)return selPatient(<?php echo $i; ?>);}"><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/search.png"></a>
					</label>
				</td>
				<!--<td class="txt_11">
					<input type="button" value="Allocate" name="allocateFax" id="allocateFax" class="dff_button" onClick="allocateFaxtoPatient(<?php echo $i; ?>);" />
				</td>-->
			</tr>
<?php
		endforeach;
	endif;
?>
		</tbody>
	</table>
</div>
<div id="bottom">
	<button type="button" value="Allocate" name="allocateFax" id="allocateFax" class="btn btn-success" onClick="allocateFaxtoPatients();">Allocate</button>
	<button name="closeBtn" class="btn btn-danger" id="closeBtn" onclick="window.close();" type="button" value="Close" autocomplete="off">Close</button>
</div>
</div>
<script type="text/javascript">

function viewPdf(link){
	window.open(link,"ViewPdf","width=700,height=500,top=150,left=150,scrollbars=yes");
}

function blank_pId(fieldKey){	
	$('#pateintId_'+fieldKey).val('');
	$('#selicon_'+fieldKey).css('visibility', 'hidden');
}

function selPatient(fieldKey){
	
	var search_val = $('#patientSearch_'+fieldKey).val();
	search_val = $.trim(search_val);
	
	if( isNaN(search_val) ){
		window.open("<?php echo $GLOBALS['webroot'];?>/interface/scheduler/search_patient_popup.php?btn_enter=Active&from=inbound_fax&fieldKey="+fieldKey+"&btn_sub=Search&sel_by=Active&txt_for="+search_val,"PatientWindow","width=800,height=500,top=150,left=150,scrollbars=yes"); 
	}else{
		$.ajax({
			url: '<?php echo $GLOBALS['webroot'];?>/interface/scheduler/chk_patient_exists.php',
			type: 'POST',
			data: 'pid='+search_val+'&findBy=Active&from=inbound_fax',
			success: function(resultData){
				resultData = $.parseJSON(resultData);
				if(resultData.status === 'failed'){
					fAlert('Patient not found');
					$('#patientSearch_'+fieldKey).val('');
					$('#pateintId_'+fieldKey).val('');
					$('#selicon_'+fieldKey).css('visibility', 'hidden');
				}
				else{
					pid = $.trim(resultData.pId);
					var pattern = new RegExp('^[0-9]+$');
					
					if( pattern.test(pid) ){
						$('#patientSearch_'+fieldKey).val(resultData.pname+' - '+pid);
						$('#pateintId_'+fieldKey).val(pid);
						$('#selicon_'+fieldKey).css('visibility', 'visible');
						$('#imgSel_'+fieldKey).focus();
					}
					else{
						fAlert('Invalid Patient Id');
						$('#patientSearch_'+fieldKey).val('');
						$('#pateintId_'+fieldKey).val('');
						$('#selicon_'+fieldKey).css('visibility', 'hidden');
					}
				}				
			}
		});		
	}
	
}

function allocateFaxtoPatients(){
	
	var selectedPatients = $('.patientIds').filter(function(){return $(this).val() != "";});
	var dataList = [];
	var folder_not_selected = false;
	
	if(selectedPatients.length===0){
		fAlert('Please select the Pateint.');
		return false;
	}
	else{
		$.each(selectedPatients, function(index, obj){
			var data = {};
			var itemKey = $(obj).attr('itemkey');
			var folder = $.trim($('#sel_folder_'+itemKey).val());
			
			if(folder==='')
				folder_not_selected = true;
			
			data.ptId = $(obj).val();
			data.faxId = $('#fax_id_'+itemKey).val();
			data.folder = folder;
			
			dataList.push(data);
		});
	}
	
	if(dataList.length===0){
		fAlert('Unable to allocate fax.');
		return false;
	}
	if(folder_not_selected){
		fAlert('Please select the folder.');
		return false;
	}
	

	params = {action:'moveToPtDoc', faxData:dataList};
	
	/*Send Ajax Call to save the docuemnt*/
	$.ajax({
		url: '<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/inbound_fax.php',
		data: params,
		type: 'POST',
		success: function(resp){
			if(resp==='success')
				window.location.reload();
			else if(resp!=='')
				fAlert('resp');
			else
				fAlert('Error in moving docuemnt to Pt. Docs.');
		}
	});
}

//---Received Fax Record Deletion Work Starts Here---
$(".del-record").click(function(){
	var id = $(this).parents("tr").attr("id");
	if(id != '')
	{
		fancyConfirm("Are you sure you want to delete?","","deleteModifiers('"+id+"')");
	}
});

function deleteModifiers(id) {
		del_data = 'id='+id+'&task=delete';
		$.ajax({
			type: "POST",
			 url: '<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/inbound_fax.php',
			data: del_data,
			success: function(resp) {
				if(resp=='success')
				{ 
					//fAlert("Record Deleted");
					window.location.reload();
				}
				else
				{ 
					fAlert('Record delete failed. Please try again.');
				}
			}
		});
	}

</script>
</body>
</html>