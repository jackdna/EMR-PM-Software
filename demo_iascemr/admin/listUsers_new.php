<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$today = date('Y-m-d');
if($_GET['delSelected']){
	$delChkBoxes = $_POST['chkBox'];  
	if(is_array($delChkBoxes)){
		foreach($delChkBoxes as $usersId){
			//$objManageData->delRecord('users', 'usersId', $usersId);
			//$objManageData->delRecord('lasusedpassword', 'user_id', $usersId);
			
			//DO NOT DELETE RECORD, SET 'DELETE STATUS FILED' TO YES
				
				//SET USER STATUS IN ADMIN TO EXPIRED BY SETTING DATE(passCreatedOn FIELD) TO 1 YEAR BACK
					$userPassCreateOnDetails = $objManageData->getRowRecord('users', 'usersId', $usersId);
					$passDateCreate = $userPassCreateOnDetails->passCreatedOn;
					list($createYear,$createMonth,$createDay,) = explode('-',$passDateCreate);
					$setExpireDate = date("Y-m-d",mktime(0,0,0,$createMonth,$createDay,$createYear-1));
				//END SET USER STATUS IN ADMIN TO EXPIRED BY SETTING DATE(passCreatedOn FIELD) TO 1 YEAR BACK
				
				$setDeleteStatusQry = "update `users` set deleteStatus = 'Yes', passCreatedOn = '$setExpireDate' where usersId = '$usersId'";
				$setDeleteStatusRes = imw_query($setDeleteStatusQry) or die(imw_error());
			//DO NOT DELETE RECORD, SET 'DELETE STATUS FIELD' TO YES
		}
	}	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>List Users</title>
<?php include("adminLinkfile.php");?>
<style>
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
</style>
<script src="../js/jscript.js"></script>
<script>
function editRecord(id){
	//alert(top.frames[0].frames[0].frames[0].name); //
	top.frames[0].frames[0].document.getElementById('tdFrameUserRegistration').style.display = 'none';
	var objFrm = top.frames[0].frames[0].frames[0].location.href = 'userRegistrationForm.php?user='+id;
	top.frames[0].frames[0].document.getElementById('formTr').style.display = 'block';
	
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	top.frames[0].document.getElementById('saveButton').style.display = 'block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'block';
}
function deleteRecord(id){
	if(confirm("Do you want to delete this user?")){
		var objFrm = document.frmDelete;
		objFrm.elem_usersId.value = id;
		objFrm.submit();
	}
}
function selectAllFn(){
	obj = document.getElementsByName("chkBox[]");
	objLength = document.getElementsByName("chkBox[]").length;
	for(i=0; i<objLength; i++){
		if(obj[i].checked == true){
			var boxesChk = true;
		}
	}
	for(i=0; i<objLength; i++){
		if(boxesChk==true){
			obj[i].checked = false;
		}else{
			obj[i].checked = true;
		}
	}
}
<?php
if(isset($_GET["op"])){
	$op = $_GET["op"];
	if(($op == "1")){
		echo "top.frames[0].frames[0].frames[0].emptyForm();";
	}else if($op == "2"){					
		if($_GET["updatePassword"]!='False'){
			echo "top.frames[0].frames[0].frames[0].emptyForm();";
		}else{
			echo "top.frames[0].frames[0].document.getElementById('formTr').style.display = 'none'";
		}
	}
	
}	
?>
function scan_userPoP(id) {
	window.open('scanPopUpUser.php?user_id='+id+'&selectedFolder=true','scanWin', 'width=775, height=650,location=yes,status=yes');
}
</script>
</head>
<body>
<form name="listUsersFrm" action="listUsers.php?delSelected=true" method="post">
	<div class="middle_wrap margin_bottom_mid_adjustment scheduler_margins_head">
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle">
			
				<div style="" id="" class="all_content1_slider ">	         
                     
					<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                    
					    <span>Users</span>
					
						<?php
							if($_REQUEST['updatePassword']=='False')
							{
							
								echo '<span id="updatePassErr" style="float:right; color:#F00;" >';
								echo 'Password matched with recently used passwords.';
								echo '</span>';
							}
						?>
							
					</div>
					
					
                    <div class="wrap_inside_admin all_admin_content">
           
           				<div class="scheduler_table_Complete ">
					
                  			<div class="my_table_Checkall">
							
								<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-striped">
                                  
                                    <thead class="cf">
                                        <tr>
                                        	<td class="text-center"><input type="checkbox" id="checkall" onClick="return selectAllFn();"></td>
                                          
                                            <th class="text-left">Name</th>
                                            <th class="text-left">Phone </th>
                                            <th class="text-left">Type</th>
                                            <th class="text-left">Login Name</th>
                                            <th class="text-left">Status</th>
                                            <th class="text-left">Scan</th>
                                         
                                        </tr>
									</thead>
									
									<tbody>
                                   	    
										<?php
											
											//$res = getUsers();
											function getUsersNew() {
												$sql = "SELECT * FROM `users` WHERE deleteStatus <> 'Yes' ORDER BY lname, address";
												return sqlQuery($sql);
											}
											
											$res = getUsersNew();
											
											if(imw_num_rows($res) > 0)
											{
												for($i = 0; $row = sqlFetchArray($res); $i++ )
												{
												
													$fname	=	ucfirst($row["fname"]);
													$lname	=	ucfirst($row["lname"]);
													
													$name	=	(!empty($lname) ? $lname.', ' : '') . $fname;
													$name	=	stripslashes($name);
													
													
													$address	=	$row["address"];
													$address2	=	$row["address2"];
													$user_city	=	$row["user_city"];
													$user_state	=	$row["user_state"];
													$user_zip	=	$row["user_zip"];
				
													$address	=	!empty($address2)	?	'<br>'.$address2	:	'';
													$address	=	!empty($user_city)	?	'<br>'.$user_city.', '.$user_state.' '.$user_zip	:	'';
													
													$practiceName=	$row["practiceName"];
													$type 		=	$row["user_type"];
													$type		=	($type == 'Coordinator')	?	'Surgical Coordinator'	:	$type	;
													$type		=	($type == 'Anesthesiologist' && $sub_type=='CRNA')	?	'CRNA'	:	$type;
													
													
													$sub_type	=	$row["user_sub_type"];
													$npi 		=	$row["npi"];
													$id			=	$row["usersId"];
													$phone		=	$row["phone"];
													
													$phone		=	!(phone)	?	$row['fax']		:	$phone;
													$fax		=	$row["fax"];
													$email		=	$row["email"];
													$federalEin	=	$row["federalEin"];
													$privileges	=	$row["user_privileges"];
													$loginName	=	$row["loginName"];
													$pass		=	$row["user_password"];
													$signature	=	$row["signature"];
													
													//void setValue(String objName, double value)	
													$signOnFile	=	$row["signOnFile"];
													
													
													
													$priviligeUser			=	$row["priviligeUser"];
													$priviligePreMedication =	$row["priviligePreMedication"];
													$priviligePredefines	=	$row["priviligePredefines"];
													$privilegeDischargeSummary=	$row["privilegeDischargeSummary"];
													
													$locked		= $row["locked"];
													
													if($locked==1){
														$status='Locked';
													}else{
															//GET USERS EXPIRES ALERT//
														$maxExpireDays		=	$objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
														$maxDaysToExpire	=	$maxExpireDays->maxPassExpiresDays;	
									
														$userDetails		=	$objManageData->getRowRecord('users', 'usersId', $id);
														$passChangedLatDate	=	$userDetails->passCreatedOn;
														
														if($today!=$passChangedLatDate)
														{
															$differanceBetween	=	$objManageData->getDateDifferance($today, $passChangedLatDate);
															$expireDaysLeft		=	$maxDaysToExpire-$differanceBetween;
															
															if($expireDaysLeft <= 0){
																$status='Expired';
															}else if(($expireDaysLeft >= 1) && ($expireDaysLeft<=7)){
																$status='Expire after '.$expireDaysLeft.'days.';
															}else{
																$status='Active';
															}
														}
														else{
																$status='Active';
														}
														//GET USERS EXPIRES ALERT//
													}
													
													
													
													$chkScnExistQry	=	"SELECT sdu.document_id FROM scan_documents_user sdu 
																			INNER JOIN scan_upload_tbl_user sutu ON sutu.document_id = sdu.document_id 
																			WHERE sdu.user_id='".$id."'";
													$chkScnExistRes	=	imw_query($chkScnExistQry) or die(imw_error());
																
													$scan_class		=	imw_num_rows($chkScnExistRes)>0	?	'tab_bg'	:	''	;
														
													++$seq;
													
										?>
													<tr style="height:25px;">
														<td class="text-center"><input type="checkbox" name="chkBox[]" value="<?php echo $id; ?>"></td>
														<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $id; ?>')"><?php echo $name; ?></a></td>
														<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $id; ?>')"><?php echo $phone; ?></a></td>
														<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $id; ?>')"><?php echo $type; ?></a> </td>
														<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $id; ?>')"><?php echo $loginName; ?></a> </td>
														<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $id; ?>')"><?php echo $status; ?></a></td>
														<td class="text-left <?php echo $scan_class;?>" id="scan_user_bgId<?php echo $id;?>" >
															<a href="javascript:scan_userPoP('<?php echo $id; ?>'); "><img src="images/scanicon.png"> </a>
														</td>
													</tr>
													
										<?php
												
												}
												
											}
										
										?>
										
									</tbody>
									
									
                                </table>
								
							</div><!-- my_table_Checkall -->
                      
                      	</div>	<!-- scheduler_table_Complete -->
                   	
					</div> <!-- wrap_inside_admin -->
					
				
				</div><!-- all_content1_slider -->
			
			
			
			</div><!-- inner_surg_middle -->
		</div><!-- container-fluid padding_0 -->
	</div><!-- middle_wrap -->
	
</form>

<!-- Delete From -->		
<form name="frmDelete" action="saveForm.php" method="post">
	<input type="hidden" name="frmName" value="User Registration">
	<input type="hidden" name="elem_usersId" value="">
	<input type="hidden" name="elem_mode" value="3">
</form>
<!-- Delete From -->
</body>
</html>