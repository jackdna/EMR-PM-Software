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
		$counter=0;
		foreach($delChkBoxes as $fac_id){
				$setDeleteStatusQry = "update `facility_tbl` set fac_del_status = '1', 
									fac_del_date = '".date('Y-m-d')."', 
									fac_del_time = '".date('H:i:s')."',
									fac_del_by = '".$_SESSION['loginUserId']."'
									where fac_id = '$fac_id'";
				$setDeleteStatusRes = imw_query($setDeleteStatusQry) or die(imw_error());
				if($setDeleteStatusRes)$counter++;
		}
		if($setDeleteStatusRes)
		{
			echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
		}
	}	
}
//get list of idoc facilities by connecting it to idoc database
require("../connect_imwemr.php");
$query=imw_query("select name,id from facility order by name asc");
while($data=imw_fetch_object($query))
{
	$facility_iDoc[$data->id]=$data->name;
}
//$query.close;
//reconnect to surgery center database
require("../common/conDb.php");

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
	
	
	.table-striped > tbody > tr.headquarter > td {
    background-color: #FC9 !important;
	}
</style>
<script src="../js/jscript.js"></script>
<script>
function editRecord(id){
	//alert(top.frames[0].frames[0].frames[0].name); //
	//top.frames[0].frames[0].document.getElementById('tdFrameUserRegistration').style.display = 'none';
	//top.frames[0].frames[0].document.getElementById('formTr').style.display = 'block';
	//var objFrm = top.frames[0].frames[0].frames[0].location.href = 'userRegistrationForm.php?user='+id;
	
	top.frames[0].frames[0].document.getElementById('userFrame').src= 'facilityRegistrationForm.php?facility='+id;
	top.frames[0].document.getElementById('addNew').style.display = 'none';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
}
function deleteRecord(id){
	if(confirm("Do you want to delete this facility?")){
		var objFrm = document.frmDelete;
		objFrm.elem_facId.value = id;
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
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	
	//'width='+W+', height='+ H+',
	//window.open('scanPopUpUser.php?user_id='+id+'&selectedFolder=true','scanWin', 'width=775, height=650,location=yes,status=yes');
	window.open('scanPopUpUser.php?user_id='+id+'&selectedFolder=true','scanWin', 'width='+W+', height='+ H+',location=yes,status=yes');
}

var LD	=	function()
{
		//alert(parent.$("#userFrame").height())
		var T	=	parent.$("#userFrame").height() - $(".head_scheduler").outerHeight(true);
		//console.log('List Users : '+T)
		$('#data-body').css( {'overflow':'hidden', 'overflow-y':'auto', 'min-height': T+'px', 'max-height': T+'px'} );
}
$(window).load(function()	{ LD(); });
$(window).resize(function(){ LD(); });	


</script>
</head>
<body>

				<div class="padding_0 clear ">	         <!-- all_content1_slider-->
                     
					<div class="head_scheduler new_head_slider padding_head_adjust_admin">
					
                        <span>ASC</span>
					
						<?php
							if($_REQUEST['updatePassword']=='False')
							{
							
								echo '<span id="updatePassErr" style="float:right; color:#F00;" >';
								echo 'Password matched with recently used passwords.';
								echo '</span>';
							}
						?>
							
					</div>
					
					
                    <div class="wrap_inside_admin ">
           
           				<div class="scheduler_table_Complete ">
					
                  			<div class="my_table_Checkall" id="data-body">
								
								<form name="listUsersFrm" action="listFacility.php?delSelected=true" method="post">
								
									<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-striped">
                                  
										<thead class="cf">
											<tr>
												<th width="8%" class="text-center"><input type="checkbox" id="checkall" onClick="return selectAllFn();"></th>
											  	<th width="14%" class="text-left">ASC Name</th>
												<th width="17%" class="text-left">Contact Name</th>
												<th width="16%" class="text-left">Contact Number</th>
												<th width="9%" class="text-left">City</th>
												<th width="10%" class="text-left">State</th>
												<th width="8%" class="text-left">Zip</th>
												<th width="18%" class="text-left">iASC Facility</th>
											 
											</tr>
										</thead>
									
										<tbody>
											
											<?php
												
												$conditionArr['fac_del_status']=0;
												//$facList = $objManageData->getArrayRecords('facility_tbl', '', '','fac_name','ASC');	
												$facList = $objManageData->getMultiChkArrayRecords('facility_tbl', $conditionArr, 'fac_name','ASC', $extraCondition)	;
												if($facList)
												{
													foreach($facList as $fac)
													{
													
														
											?>
														<tr style="height:25px;" class="<?php if($fac->fac_head_quater==1)echo'headquarter';?>">
															<td class="text-center"><input type="checkbox" name="chkBox[]" value="<?php echo $fac->fac_id; ?>"></td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_name; ?></a></td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_contact_name; ?></a></td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_contact_phone; ?></a> </td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_city; ?></a> </td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_state; ?></a></td>
                                                            <td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php echo $fac->fac_zip; ?></a> </td>
															<td class="text-left"><a class="con" href="javascript:editRecord('<?php echo $fac->fac_id; ?>')"><?php 
															$fac_idoc_link_id_arr=explode(',',$fac->fac_idoc_link_id);
															foreach($fac_idoc_link_id_arr as $id){
																$facStr.=($facStr)?', '.$facility_iDoc[$id]:$facility_iDoc[$id];
															}
															echo $facStr;
															unset($facStr,$fac_idoc_link_id_arr);
															?></a></td>
															
														</tr>
														
											<?php
													
													}
												}
											
											?>
											
										</tbody>
									
                                	</table>
									
								</form>
								
							</div><!-- my_table_Checkall -->
                      
                      	</div>	<!-- scheduler_table_Complete -->
                   	
					</div> <!-- wrap_inside_admin -->
					
				</div><!-- all_content1_slider -->
			
			

	<!-- Delete From -->		
	<form name="frmDelete" action="saveForm.php" method="post">
		<input type="hidden" name="frmName" value="New Facility">
		<input type="hidden" name="elem_facId" value="">
		<input type="hidden" name="elem_mode" value="3">
	</form>
	<!-- Delete From -->


</body>
</html>