<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<?php
session_start();
include_once("./common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$pConfId = $_REQUEST["pConfId"];
if(!$pConfId) {
	$pConfId = $_SESSION["pConfId"]; /*Conf Id*/
}	
$ascID = $_REQUEST['ascId'];

$loginUserId = $_SESSION['loginUserId'];
$progrModifyVisi = 'hidden';
if($_SESSION['loginUserType']=='Surgeon' || $_SESSION['loginUserType']=='Anesthesiologist' || $_SESSION['loginUserType']=='Nurse'){  
	$progrModifyVisi = 'visibile';
}

$confirmationDetails		=	$objManageData->getExtractRecord('patientconfirmation','patientConfirmationId',$pConfId,'surgeonId');
$confirmationSurgeonID	=	$confirmationDetails['surgeonId'];
$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review');
$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
$practiceNameMatch	=	'';
if($surgeryCenterPeerReview == 'Y' && $_SESSION['loginUserType'] == 'Surgeon')
{
	$practiceNameMatch	=	$objManageData->getPracMatchUserId($loginUserId,$confirmationSurgeonID);
	if($practiceNameMatch)
	{
			$progrModifyVisi	=	'hidden';
	}
}
							

$pConfId = $_REQUEST["pConfId"];
if(!$pConfId){
	$pConfId = $_SESSION["pConfId"];
}	
$ascID = $_REQUEST['ascId'];
$id_del = $_REQUEST['delid'];  


$query_rsNotes = "SELECT tblprogress_report.intProgressID, tblprogress_report.txtNote, tblprogress_report.confirmation_id, users.fname, users.mname, users.lname, users.user_type, tblprogress_report.dtDateTime, tblprogress_report.tTime FROM tblprogress_report, users WHERE tblprogress_report.confirmation_id = '".$pConfId."'  AND users.usersId = tblprogress_report.usersId ORDER BY dtDateTime DESC, tTime DESC";
$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error()); /* Progress Notes */
$totalRows_rsNotes = imw_num_rows($rsNotes);

$id_edit = $_REQUEST['editid'];

if($id_edit!=''){
	$qry_Notes = "select * from tblprogress_report where intProgressID = $id_edit";
	$fetch_Notes = imw_query($qry_Notes);
	$row_notes = imw_fetch_array($fetch_Notes);
	$EditUserType = $row_notes['userType'];
	$EditusersId = $row_notes['usersId'];
	
	$EditUserNameQry = "select * from users where usersId = '$EditusersId'";
	$EditUserNameRes = imw_query($EditUserNameQry) or die(imw_error());
	$EditUserNumRow = imw_num_rows($EditUserNameRes);
	if($EditUserNumRow>0) {
		$EditUserRow = imw_fetch_array($EditUserNameRes);
		$EditUserName = $EditUserRow["fname"]." ".$EditUserRow["mname"]." ".$EditUserRow["lname"];
	}
}

//GET LOGIN USER NAME AND USER TYPE
$LoginUserNameQry = "select * from users where usersId = '$loginUserId'";
$LoginUserNameRes = imw_query($LoginUserNameQry) or die(imw_error());
$LoginUserNumRow = imw_num_rows($LoginUserNameRes);
if($LoginUserNumRow){
	$LoginUserRow = imw_fetch_array($LoginUserNameRes);
	$LoginUserType = $LoginUserRow["user_type"];
	$LoginUserName = $LoginUserRow["fname"]." ".$LoginUserRow["mname"]." ".$LoginUserRow["lname"];
}
if($EditUserType=="") {
	$EditUserType = $LoginUserType;
}
if($EditusersId=="") {
	$EditusersId = $loginUserId;
	$EditUserName = $LoginUserName;
}


//print $EditusersId." - ".$EditUserName." - ".$EditUserType." - ".$LoginUserName;
//END GET LOGIN USER NAME AND USER TYPE	
?>
<html>	
<head>
	<meta name="viewport" content="width=device-width, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title>Progress Notes</title>
	
	<link rel="stylesheet" href="./css/sfdc_header.css" type="text/css" />
	<link rel="stylesheet" href="./css/simpletree.css" type="text/css" />
	
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="./css/style.css" />
	<link rel="stylesheet" type="text/css" href="./css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="./css/bootstrap-select.css" />
	<link rel="stylesheet" type="text/css" href="./css/ion.calendar.css" />
	
	
	
	<script type="text/javascript" src="./js/jquery-1.11.3.js"></script>
	<script type="text/javascript" src="./js/bootstrap.js"></script>
			
	<script type="text/javascript" src="./js/wufoo.js"></script>
	<script type="text/javascript" src="./js/jsFunction.js"></script>
	<script type="text/javascript" src="./js/cur_timedate.js"></script>
	<script type="text/javascript" src="./js/simpletreemenu.js"></script>
	<script type="text/javascript" src="./js/jscript.js"></script>
	<script type="text/javascript" src="./js/epost.js"></script>
	
	<script type="text/javascript" src="./js/moment.js"></script>
	<script type="text/javascript" src="./js/ion.calendar.js"></script>
	<!--<script type="text/javascript" src="./js/overflow.js"></script>-->
	<script type="text/javascript" src="./js/bootstrap-select.js"></script>

	<script>
		$(window).load(function() {
			$(".loader").fadeOut(1000).hide(1000); 
			window.resizeTo(screen.availWidth*0.7,screen.availHeight*0.90);
			bodySize();
		});
		
		$(window).resize(function(){
			
			bodySize();
		});
		
		var bodySize = function(){
			var HH	=	$(".header").outerHeight(true);
			var FH	=	$(".footer").outerHeight(true);
			var TH	=	$("#myTab").outerHeight(true);
			var DH	=	$(window).height();
			var BH	=	DH - ( HH + FH + TH + 40 )   ;
			//console.log('BH = ' + DH + ' -  ( ' +  HH + ' + '  + FH + ' + ' + TH +' + 20 )   >>>' + BH);
			//$("#myTabContent").css({'min-height':BH+'px', 'max-height':BH+'px', 'overflow' : 'hidden', 'overflow-y' : 'auto' })
			
			$("#Epost .form_inside_modal").css({'min-height' : BH+'px', 'max-height':BH+'px'})
			$("#AddEpost").css({'min-height':BH+'px', 'max-height':BH+'px' })
			
		}
		
	</script>
</head>
<body>
<!-- Loader -->
<div class="loader">
	<span><b class='fa fa-spinner fa-pulse'></b>&nbsp;Loading...</span>
</div>
<!-- Loader-->
<div class="box box-sizing">
	<div class="dialog box-sizing">
		<div class="content box-sizing">
			<div class="header box-sizing text-left ">
				<h4>Progress Notes</h4>
				<span class="right-box"><?php echo $EditUserType; ?>&nbsp;&nbsp;<span style="font-weight:normal"><?php echo $EditUserName; ?></span></span>
			</div>
			<div class="clearfix margin_adjustment_only"></div>
			<div class="body" >
				<div class="bs-example-tabs">
					<ul id="myTab" class="nav nav-tabs nav-justified" style="padding:0px !important;">
						<li class="active">
							<a id="listNotesLink" href="#Epost" data-title="Epost" data-toggle="tab">
								ProgressNotes (<b id="pnotesCount"><?php echo $totalRows_rsNotes; ?></b>)
							</a>
						</li>
						<li id="addNotesLink" style="visibility: <?php echo $progrModifyVisi; ?>">
							<a href="#AddEpost" data-title="AddEpost" data-toggle="tab"><b class="fa fa-plus"></b> Add New</a>
						</li>
					</ul>
				</div>
				<div class="clearfix margin_adjustment_only"></div>
				
                <div id="myTabContent" class="tab-content" >
					<div class="clearfix margin_adjustment_only"></div>	
					
                    <div class="tab-pane" id="AddEpost">
						<div class="form_inside_modal" >
							<div class="col-lg-3 visible-lg"></div>
							<div class="col-md-3 visible-md"></div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
									<form id="addForm">
									<div class="form_inner_m eposter">
										<div class="row">
											<span id="processing"></span>
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												<label for="select_Epost" class="text-left">Enter Progress Note</label>
											</div>
											
											<div class="clearfix margin_adjustment_only"></div>
											
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												<textarea class="form-control" name="txtNote" id="txtNote"></textarea>
											</div>
											
											<div class="clearfix margin_adjustment_only"></div>
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="text-align:right">
												<!-- Hidden Fields -->
												<input type="hidden" name="action" value="insert">
												<input type="hidden" name="asc_id" value="<?php echo $ascID; ?>">
												<input type="hidden" name="confirmation_id" value="<?php echo $pConfId; ?>">
												<input type="hidden" name="hidd_userid" id="hidd_userid" value="<?php echo $_REQUEST["editid"];?>">
												<input type="hidden" name="progress_UserType" value="<?php echo $EditUserType; ?>">
												<input type="hidden" name="hidd_progressUserId" value="<?php echo $EditusersId; ?>">
												<a href="javascript:void(0)" class="btn btn-success" onClick="addNote()">
													<b class="fa fa-save"></b>&nbsp;Save
												</a>
												<a href="javascript:void(0)" class="btn btn-danger" onClick="addNote_reset()">
													<b class="fa fa-times"></b>&nbsp;Reset
												</a>
											</div>
										</div><!-- ROW -->
									</div>
								</form>
							</div>
						</div>
					</div><!----- ---------------------  Tab content for Add New Progress Notes----------------------------- -->
					
					<div class="tab-pane fadein active" id="Epost">						
						<div class="form_inside_modal" >
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
								<form id="editForm" style="display: none;">
									<div class="form_inner_m eposter">
										<div class="row">
											<span id="processing"></span>
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												<label for="select_Epost" class="text-left">Edit Progress Note</label>
											</div>
											
											<div class="clearfix margin_adjustment_only"></div>
											
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												<textarea class="form-control" name="txtNote" id="txtNote_edit"></textarea>
											</div>
											
											<div class="clearfix margin_adjustment_only"></div>
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="text-align:right">
												<!-- Hidden Fields -->
												<input type="hidden" name="action" value="edit">
												<input type="hidden" name="editid" id="editid" value="">
												<input type="hidden" name="asc_id" value="<?php echo $ascID; ?>">
												<input type="hidden" name="confirmation_id" value="<?php echo $pConfId; ?>">
												<input type="hidden" name="hidd_userid" id="hidd_userid" value="<?php echo $_REQUEST["editid"];?>">
												<input type="hidden" name="progress_UserType" value="<?php echo $EditUserType; ?>">
												<input type="hidden" name="hidd_progressUserId" value="<?php echo $EditusersId; ?>">
												<a href="javascript:void(0)" class="btn btn-success" onClick="editNote_submit()">
													<b class="fa fa-save"></b>&nbsp;Save
												</a>
												<a href="javascript:void(0)" class="btn btn-danger" onClick="editNote_cancel()">
													<b class="fa fa-times"></b>&nbsp;cancel
												</a>
											</div>
										</div><!-- ROW -->
									</div>
								</form>
								
                                <div class="form_inner_m"  id="notesContainer" >
<?php
	
	if($totalRows_rsNotes > 0){
		$a='';  
		while ($row_rsNotes = imw_fetch_array($rsNotes)){
			if($i%2==0) { 
				$a="#FFFFFF"; 
			}
			else { 
				$a="#F1F4F0";
			} 
			$ProgressNotesTime = $row_rsNotes['tTime'];
			//CODE TO SET $ProgressNotesTime 
			if($ProgressNotesTime=="00:00:00" || $ProgressNotesTime=="") {
				$ProgressNotesTime = $objManageData->getTmFormat(date("H:i:s"));
			}
			else{
				$ProgressNotesTime = $objManageData->getTmFormat($ProgressNotesTime);
			}

			$datestring= $row_rsNotes['dtDateTime']; 
			$d=explode("-",$datestring);
				
?>
			<div class="well well-lg">
				<p>
					<span class="epost_dt">
						<i class="fa fa-clock-o"></i> <?php echo $d[1]."/".$d[2]."/".$d[0]." ".$ProgressNotesTime; ?>
					</span>
					<span class="epost_dt">
						<b><?php echo $row_rsNotes['user_type'];?></b> <?php echo $row_rsNotes['fname']." ".$row_rsNotes['lname']; ?>
					</span>
				</p>
				<p class="epost_content" id="epost_content_<?php echo $row_rsNotes['intProgressID']; ?>"><?php echo stripslashes($row_rsNotes['txtNote']); ?></p>
				<a style="visibility: <?php echo $progrModifyVisi; ?>" href="javascript:void(0)" class="btn btn-info edit_epost" onClick="editNote(<?php echo $row_rsNotes['intProgressID']; ?>,<?php echo $pConfId;?>)">
					<span class="glyphicon glyphicon-edit"></span> Edit
				</a>&nbsp;
				<a style="visibility: <?php echo $progrModifyVisi; ?>" href="javascript:void(0)" class="btn btn-danger delete_epost" onClick="delNote(<?php echo $row_rsNotes['intProgressID']; ?>,<?php echo $pConfId;?>)" />
					<span class="fa fa-trash"></span>   Delete
				</a>
			</div>
<?php
		}
	}
?>
								</div>
							</div>
						</div> <!-- Form Inside Modal -->
					</div>
					<!----- ---------------------  Tab Content for Progress Notes Tab ----------------------------- -->
				</div><!-- End Overall Tab Content -->
			</div><!-- Body End Here --->
			
            <div class="footer" style="margin-top:0px;">
				<span id="backDelScan">
					<a id="PrintBtnPg" class="btn btn-info"onClick="prntPgNotesFun('<?php echo $pConfId; ?>');" href="javascript:void(0)">
						<b class="fa fa-print"></b>&nbsp;Print
					</a>
					<a class="btn btn-danger" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton">
						<b class="fa fa-close" ></b>&nbsp;Close
					</a>
				</span>
			</div>
		</div><!-- End Modal Content -->
	</div><!-- End Modal Dialog -->
</div><!-- End Modal Fade -->

<script type="text/javascript">
function prntPgNotesFun(pConfId) {
	var url;
	url='progress_report_pdf.php?pConfId='+pConfId;
	window.open(url,'','width=850,height=600,top=100,left=100,resizable=yes,scrollbars=yes');
}
function addNote(){
	var val = $("form#addForm #txtNote").val();
	if($.trim(val)=="") {
		alert("Please enter progress note.");
		return(false);
	}
	var data = $("form#addForm").serialize();
	var resp_status = "error";
	var resp_count	 = 0;
	$(".loader").fadeIn(1000).show(1000); 
	$.ajax({
		url : './common/progressNotesAction.php',
		type:'POST',
		dataType:'json',
		data : data,
		complete: function(data){
				
			if(resp_status == "success") {
				
				$("form#addForm #txtNote").val('');
				if(resp_count > 0)
				{
					if(!window.opener.$("#progressNotesBg").hasClass('bg-orange'))
					{
						window.opener.$("#progressNotesBg").addClass('bg-orange')	
					}
				}
				window.opener.$("#progressNotesBtn").html('Progress Notes&nbsp;<span class="badge">'+resp_count + '</span>');
				window.location.reload();
			}
		},
		success: function(data){
			
			if(data.status=="success") {
				resp_status = "success";
				resp_count  = data.count;
				
				//$("#pnotesCount").html(data.count);
				//$("#notesContainer").prepend(data.content);
			}
			else{
				resp_status = "error";
				alert("Progress note not added.");
			}
		},
		error: function(status, data){
			alert("Error in saving progress note.");
		}
		
	});
}
function addNote_reset(){
	$("#txtNote").val('');
}
function editNote(noteId, confId)
{
	
	var data = $("#epost_content_"+noteId).text();
	
	$("form#editForm #txtNote_edit").val(data);
	$("form#editForm #hidd_userid").val(noteId);
	
	$("#notesContainer").slideUp('slow', function(){$("#editForm").slideDown();});
}
function editNote_cancel(){
	$("#editForm").slideUp('slow', function(){$("#notesContainer").slideDown();});
}


function editNote_submit()
{
	
	var val = $("form#editForm #txtNote_edit").val();
	if($.trim(val)=="") {
		alert("Please enter progress note.");
		return(false);
	}
	var data = $("form#editForm").serialize();
	var resp_status = "error";
	$(".loader").fadeIn(1000).show(1000); 
	$.ajax({
		url : './common/progressNotesAction.php',
		type:'POST',
		dataType:'json',
		data : data,
		complete: function(data){
			
			if(resp_status = "success") {
				$("form#editForm #txtNote_edit").val('');
				$("form#editForm #editid").val('');
				window.location.reload();
			}
		},
		success: function(data){
			
			if(data.status=="success") {
				resp_status = "success";
			}
			else{
				resp_status = "error";
				alert("Error in saving progress note.");
			}
		},
		error: function(status, data){
			alert("Error in saving progress note.");
		}
	});
}

function delNote(noteId, confId){
	
	if(!confirm('Do you really want to delete.')){
		return(false);
	}
	
	data = "noteid="+noteId+"&action=delete";
	$(".loader").fadeIn(1000).show(1000); 
	var resp_count	=	0;
	$.ajax({
		url : './common/progressNotesAction.php',
		type:'POST',
		dataType:'json',
		data : data,
		complete: function(data){
				
			if(resp_status = "success") {
				$("form#editForm #txtNote_edit").val('');
				
				if(resp_count == 0)
				{
					if(window.opener.$("#progressNotesBg").hasClass('bg-orange') )
					{
						window.opener.$("#progressNotesBg").removeClass('bg-orange');
					}
					window.opener.$("#progressNotesBtn").html(' Progress Notes ');
				}
				else
				{
						window.opener.$("#progressNotesBtn").html('Progress Notes&nbsp;<span class="badge">'+resp_count + '</span>');
				}
				
				
				window.location.reload();
			}
		},
		success: function(data){
			
			if(data.status=="success") {
				resp_status = "success";
				resp_count	=	data.count;
			}
			else{
				resp_status = "error";
				alert("Error in deleting progress note. -1");
			}
		},
		error: function(status, data){
			alert("Error in deleting progress note.");
		}
	});
}
</script>
</body>
</html>