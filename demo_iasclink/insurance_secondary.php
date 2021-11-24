<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

?>
<?php include_once("common/conDb.php"); ?>
<?php include_once("common/functions.php"); ?>
<?php include_once("admin/classObjectFunction.php"); ?>
<?php include("common/iOLinkCommonFunction.php"); ?>
<?php include("common/link_new_file.php"); ?>

<?php

	$patient_id = $_REQUEST['pid'];
	$waiting_id = $_REQUEST['wid'];

	$obj = new manageData;
	
	$relats = array('self','Father','Mother','Son','Daughter','Spouse','Guardian','POA','Employee','Other');	
	$arrayInsDataRecord = array();
	
//	print_r($row);
	
	if($_REQUEST['expPreviousPri']<>"") {

	$active_date = $_POST["active_date"];
	$expiry_Date = $_POST["expiry_date"];
	$patient_dob = $_POST["dob"];
	
	if($active_date){
		$active_date_split = explode("-",$active_date);
		$active_date = $active_date_split[2]."-".$active_date_split[0]."-".$active_date_split[1];	
	}
	
	if($expiry_Date){
		$expiry_Date_split = explode("-",$expiry_Date);
		$expiry_Date = $expiry_Date_split[2]."-".$expiry_Date_split[0]."-".$expiry_Date_split[1]; 
	}
	
	if($patient_dob) {
		$patient_dob_split = explode("-",$patient_dob);
		$dob = $patient_dob_split[2]."-".$patient_dob_split[0]."-".$patient_dob_split[1];	
	}
	
	$chkQry1 = "select * from insurance_data where  waiting_id = '$waiting_id' and type = 'secondary'";
	$chkRes1 = imw_query($chkQry1);
	
	
	unset($arrayInsDataRecord);
	$arrayInsDataRecord['ins_provider'] = addslashes($_POST["insprovider"]);
	$arrayInsDataRecord['policy'] = addslashes($_POST["policy"]);
	$arrayInsDataRecord['group_name'] = addslashes($_POST["group"]);
	$arrayInsDataRecord['plan_name'] = addslashes($_POST["plan_name"]);
	$arrayInsDataRecord['copay'] = addslashes($_POST["copay"]);
	$arrayInsDataRecord['authorization_number'] = addslashes($_POST["authorization_number"]);
	$arrayInsDataRecord['refer_req'] = addslashes($_POST["refer_req"]);
	$arrayInsDataRecord['active_date'] = $active_date;
	$arrayInsDataRecord['expiry_Date'] = $expiry_Date;
	$arrayInsDataRecord['fname'] = addslashes($_POST["fname"]);
	$arrayInsDataRecord['mname'] = addslashes($_POST["mname"]);
	$arrayInsDataRecord['lname'] = addslashes($_POST["lname"]);
	$arrayInsDataRecord['sub_relation'] = addslashes($_POST["sub_relation"]);
	$arrayInsDataRecord['ssn'] = addslashes($_POST["ssn"]);
	$arrayInsDataRecord['dob'] = $dob;
	$arrayInsDataRecord['gender'] = $_POST["gender"];
	$arrayInsDataRecord['payment_auth'] = addslashes($_POST["payment_auth"]);
	$arrayInsDataRecord['sign_on_file'] = addslashes($_POST["sign_on_file"]);
	
	$address1Post = $_POST["address1"];
	$address1Post=str_ireplace(', ',',',$address1Post);
	$address1Post=str_ireplace(',',', ',$address1Post);
	
	$address2Post = $_POST["address2"];
	$address2Post=str_ireplace(', ',',',$address2Post);
	$address2Post=str_ireplace(',',', ',$address2Post);
	
	$arrayInsDataRecord['address1'] = addslashes($address1Post);
	$arrayInsDataRecord['address2'] = addslashes($address2Post);
	$arrayInsDataRecord['zip_code'] = $_POST["zip_code"];
	$arrayInsDataRecord['city'] = addslashes($_POST["city"]);
	$arrayInsDataRecord['state'] = addslashes($_POST["state"]);
	$arrayInsDataRecord['home_phone'] = addslashes($_POST["home_phone"]);
	$arrayInsDataRecord['work_phone'] = addslashes($_POST["work_phone"]);
	$arrayInsDataRecord['mbl_phone'] = addslashes($_POST["mbl_phone"]);
	$arrayInsDataRecord['type'] = addslashes($_POST["type"]);
	
	$arrayInsDataRecord['waiting_id'] = $waiting_id;
	$arrayInsDataRecord['patient_id'] = $patient_id;
	//print_r($arrayInsDataRecord); die;
	if(imw_num_rows($chkRes1) <= 0){
		$obj->addRecords($arrayInsDataRecord, 'insurance_data');
	}else{
		$chkRow1 = imw_fetch_array($chkRes1);
		$insID = $chkRow1['id']; 
		$obj->updateRecords($arrayInsDataRecord, 'insurance_data','id',$insID);
	}
	if($waiting_id) {
		echo "<script>opener.waitingPatient_info('$patient_id','$waiting_id');</script>";
	}
	?>
	<script>
		alert('Record Saved');
		window.close();
	</script>
	<?php
}
$chkQry = "select * from insurance_data where  waiting_id = '$waiting_id' and type = 'secondary'";
$chkRes = imw_query($chkQry);
$row = imw_fetch_array($chkRes);
$insuranceId 	= $row['id'];
$insScan1Upload = $row['insScan1Upload'];
$insScan1Status = $row['insScan1Status'];
$insScan2Upload = $row['insScan2Upload'];
$insScan2Status = $row['insScan2Status'];
?>
<html>
<head>
<script src="js/webtoolkit.aim.js"></script>
<script type="text/javascript">
	window.focus();
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	var mon=month+1;
	if(mon<=9){
		mon='0'+mon;
	}
	var todaydate=mon+'-'+day+'-'+year;
	function y2k(number){
		return (number < 1000)? number+1900 : number;
	}
	function newWindow(q){
		
		mywindow=open('mycal1.php?md='+q+'&rf=yes','','width=200,height=250,top=200,left=300');
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
	function restart(q){
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				alert("Date Of Service can not be a future date")
				return false;
			}
		}
		
		document.getElementById(q).value=fillDate;
		//START CODE TO SET DATE FORMAT (MM-DD-YY)
		/*
		var fillDateSplit = fillDate.split('-');
		var fillDateNew = fillDateSplit[0]+'-'+fillDateSplit[1]+'-'+fillDateSplit[2].substr(2,2);
		if(document.getElementById('hidd_'+q)) {
			document.getElementById('hidd_'+q).value=fillDate;
		}
		*/
		//END CODE TO SET DATE FORMAT (MM-DD-YY)
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}


function doDateCheck(from, to) {
	if(chkdate(to) && chkdate(from) ){
	
	if (Date.parse(from.value) >= Date.parse(to.value)) {
	//alert("The dates are valid.");
	}
	else {
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		to.value="";
		alert("Date of birth can not be greater than current date.");
		   }
		}
	}
}


	function frmSubmit(){
		document.secondary_ins.submit();
	}
	
//START FUNCTION TO SCAN/UPLOAD DISCHARGE SUMMARY SHEET
function startCallback() {	
	return true;
}
function completeCallback(response){
	setTimeout('getImage()', 1000);
}
function getImage(){
	document.frames['iframeIOL'].location.reload();
	var objFrm = document.frm_uploadInsSecImage;
	if(objFrm.hidd_delImage.value=='yes' || objFrm.hidd_delImage.value=='yes2') {
		//alert(objFrm.hidd_delImage.value);
		
		//if(!top.iframeIOL.document.getElementById('imgThumbNail') && !top.iframeIOL.document.getElementById('imgThumbNail2')){
			//document.getElementById('iframeIOL').style.height = '0px';
			//document.getElementById('iframeIOL').style.width = '0px';
			document.frm_uploadInsSecImage.hidd_delImage.value='';
		//}
	}else {
		document.getElementById('iframeIOL').style.height = '100px';
		document.getElementById('iframeIOL').style.width = '400px';
		document.frm_uploadInsSecImage.hidd_delImage.value='';
	}	
}
function showImgDiv(){
	document.getElementById('imgDiv').style.display = 'block';
}

//END FUNCTION TO SCAN/UIPLOAD DISCHARGE SUMMARY SHEET
		
</script>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
</head>
<body>

<form action="insurance_secondary.php" method="post" name="secondary_ins">
<table width="100%" align="left" cellpadding="2" cellspacing="2" border="0" bgcolor="#ECF1EA">
	<tr>
		<td>
			<table  width='100%' border="0" align="center" cellpadding="0" cellspacing="0" class="text_10" >
				<tr>
					<td class="text_10b" width="67">
						<table cellpadding="0" cellspacing="4" align="center" width="100%">
							<tr>

								<td colspan="4" background="<?php echo $bgHeadingImage;?>" class="text_10b" height="30px" style="padding-left:5px;" >
									Secondary Ins. Case [Normal-75]
								</td>
										
							</tr>
							<tr>
								<td width="267" valign="top">
									<table width="235" height="125" cellpadding="1" cellspacing="0" border="0" class="tblBg"  id="provider1_table">
										<tr>
											<td class="text_10b" align="left" colspan="5">
												Provider :
											</td>
										</tr>
										<tr class="bgcolor">
											<td  align="left" nowrap="nowrap"  style="padding-left:10px;" colspan="4">
												<table cellpadding="0" cellspacing="0" border="0" width="35%">
													<td width="105">
															<input type="hidden" name="waiting_id" value="<?php echo $waiting_id ?>">
															<input type="hidden" name="wid" value="<?php echo $waiting_id; ?>">
															<input type="hidden" name="pid" value="<?php echo $patient_id; ?>">
															<input type="hidden" name="expPreviousPri" value="test" />
															<input type="hidden" name="actPreviousPri" value="<?php print $actPreviousPriDate; ?>" />
															<input type="hidden" name="primaryId" value="<?php print $primaryComid; ?>" />
															<input type="hidden" name="primaryMainId" value="<?php print $primaryComDetail->id; ?>" />										
															<input type="hidden" name="primaryInsName" value="<?php print $insName; ?>" />
															<input type="hidden" name="i1provider" id="i1provider" value="<?php print $primaryComDetail->provider; ?>" />
															<input type="hidden" name="insurenceProviderExit" id="insurenceProviderExit" value="<?php if(strlen($insComDetails->name)>12){print (substr($insComDetails->name,0,12)."..");}else{print $insComDetails->name;}?>" />
															<?php echo $row['ins_provider ']; ?>
															<input type="hidden" name="type" value="secondary" />
															<input type="text" name="insprovider" class="field text_small" value="<?php echo $row['ins_provider']; ?>" />
														
													</td>
													<td>
														&nbsp;<input type="text" size="20" name="policy" value="<?php echo $row['policy']; ?>" class="field text_small" >
													</td>
												</table>
											</td>
										</tr>
										<tr> 
											<td  align="left" nowrap="nowrap"  style="padding-left:10px;" colspan="4">
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<td width="114" class="text_smalli">
														Ins. Carrier													
													</td>
													
													<td width="108" class="text_smalli">
														Policy
													</td>
												</table>
											</td>
										</tr>
										
										<tr >
											<td  style="padding-left:11px;" align="left" colspan="4">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr class="bgcolor">
														<td><input type="text" size="16" name="group" value="<?php echo $row['group_name']; ?>" class="field text_small">
														</td>
														<td>
															<?php //if(trim($primaryComDetail->plan_name)=='' and $InsPriPlanName==1){$class="mandatory";} else{$class="text_10";} ?>
															<input type="text" size="16" name="plan_name" value="<?php echo $row['plan_name']; ?>" class="field text_small">
														</td>
													</tr>
													<tr>
														<td class="text_smalli">
															Group#
														</td>
														<td class="text_smalli">
															Plan Name
														</td>
													</tr>
													<tr class="bgcolor">	
														<td >
															<input type="text" size="16" name="copay" value="<?php echo $row['copay']; ?>" class="field text_small" >
														</td>
														<td >
															<select  class="field text_small" style="width:77px" name="refer_req" >
																<option value="No" <?php if($row['refer_req'] == 'No'){ echo 'selected=selected'; } ?>>No</option>
																<option value="Yes" <?php if($row['refer_req'] == 'Yes'){ echo 'selected=selected'; } ?>>Yes</option>
															</select>
														</td>
													</tr>
													<tr>
														<td  align="left" class="text_smalli right" >CoPay</td>
														<td  colspan="5" class="text_smalli right">Refer&nbsp;Req :</td>
													</tr>
													<tr class="bgcolor">
														<td>
															<input type="text" size="16" name="authorization_number" value="<?php echo $row['authorization_number']; ?>" class="field text_small">
														</td>
														<td></td>
													</tr>
													<tr>
														<td class="text_smalli">
															Authorization#
														</td>
														<td class="text_smalli"></td>
													</tr>
												</table>
											</td>
									  </tr>
									  <tr class="bgcolor">
											<td class="tr_bg" style="padding-left:11px;" align="left" colspan="5">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td>
														<?php  if($row['active_date'] != '' &&  $row['active_date'] != '0000-00-00') { 
																	list($y,$m,$d) = explode('-',$row['active_date']);
																	$actDate = $m.'-'.$d.'-'.$y;
																}
																
																if($row['expiry_Date'] != '' &&  $row['expiry_Date'] != '0000-00-00') { 
																	list($ey,$em,$ed) = explode('-',$row['expiry_Date']);
																	$expDate = $em.'-'.$ed.'-'.$ey;
																}
														?>
														
															<input name="active_date" id="active_date" onBlur="checkdate(this);" type="text" size="12" value="<?php echo $actDate; ?>" maxlength=10 class="field text_small" title='mm-dd-yyyy' >
															<a href="#"><img src='images/icon_cal.jpg' align='absbottom' width='24' height='22' border='0' onClick="newWindow('active_date')"></a>
														</td>
														<td>
															<input name="priExpirationDate" value="<?php print $expirationDate; ?>" type="hidden" >
															<?php //if(trim($expiration_date)=='' and $InsPriExpDate==1){$class="mandatory";} else{$class="text_10";} ?>
															<!-- <input name="i1expiration_date" onclick="fillDate(this);" value="<?php print $expiration_date; ?>" type="text" size="15" class=<?php echo $class; ?> title='mm-dd-yyyy' onBlur="checkdate(this); lostFocus(this,'<?php echo $class; ?>');"  maxlength=10 onKeyUp="callme(this);" > -->
															<?php //$selectQry = "select DATE_FORMAT(expiration_date, '%m-%d-%Y') as expiration_date from insurance_data where type = 'primary' and pid = '".$_SESSION['patient']."'
																				//and ins_caseid = '".$_SESSION['currentCaseid']."' and id='$primaryComDetail->id'";
															/* $qry = imw_query($selectQry);
															while($row=imw_fetch_array($qry))
																$exitingExpirationDate=$row['expiration_date']; */
															?>
															
															<input type="hidden" name="exitingExpirationDate" id="exitingExpirationDate" value="" class="field text_small"/>
															<input name="expiry_date" id="i1expiration_date" onBlur="checkdate(this);" value="<?php echo $expDate; ?>" type="text" size="14" class="field text_small" title='mm-dd-yyyy' >
															<a href="#"><img src='images/icon_cal.jpg' align='absbottom' width='24' height='22' border='0' onClick="newWindow('i1expiration_date')"></a>
														</td>
													</tr>
												</table>
											</td>
									  	</tr>
										<tr >
											<td style="padding-left:11px;" align="left" colspan="5">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td class="text_smalli">
															Act. Date
														</td>
														<td class="text_smalli">
															Exp. Date
														</td>
													</tr>
												</table>
											</td>
										</tr>
								  </table>
								</td>
								<td height="125" width="9"></td>
								<td width="249" valign="top">																																		
									<table width="210" height="125" border="0" cellpadding="0" cellspacing="0" class="tblBg" id="insPolicy1_table">
										<tr>
											<td class="text_10b" align="left" colspan="3">
												Ins. Policy
											</td>
										</tr>
										
										<tr class="bgcolor">
											<td width="77" style="padding-left:10px;">
												<input type="text" class="field text_small" size="11" vspace="0" hspace="0" name="fname" id="i1subscriber_fname" value="<?php echo $row['fname']; ?>" >&nbsp;
										  </td>
											<?php if(trim($patientMname)=="" and $InsPriInsName==1){$class="mandatory";} else{$class="text_10";} ?>
											<td width="36">
												<input type="text" class="field text_small" size="5" vspace="0" hspace="0" name="mname" value="<?php echo $row['mname']; ?>" >&nbsp;
										  </td>
											<?php if(trim($patientLname)=="" and $InsPriInsName==1){$class="mandatory";} else{$class="text_10";} ?>
											<td width="97">
												<input size="11" class="field text_small" type="text" hspace="0" vspace="0" name="lname" id="lastName" value="<?php echo $row['lname']; ?>"  />
										  </td>
										</tr>
										<tr> 
											<td class="text_smalli right" style="padding-left:10px;">
												First Name
											</td>
											<td class="text_smalli" >
												Middle
											</td>
											<td class="text_smalli" >
												Last Name
											</td>
										</tr>
										<tr class="bgcolor">
											<td colspan="2"  class="text_10" style="padding-left:10px;">
												<?php if(trim($primaryComDetail->subscriber_relationship)=='' and $InsPriSubRelation==1){$class="mandatory";} else{$class="text_10";} ?>
												<select style="width:80px" name='sub_relation' class="field text_small" >
													<option value="">--Select--</option>
													<?php
														foreach($relats as $val){
															 if($val == $row['sub_relation']){
																$sel = 'selected="selected"';
															} 
															else{
																$sel = '';
															}
															$selectOption .= '
																<option value="'.$val.'" '.$sel.'>'.ucfirst($val).'</option>
															';
														}
														print $selectOption; 
													?>
												</select>
											</td>
											<td colspan="1">
												<?php
													if($primaryComDetail->subscriber_ss == ''){
														$subscriber_ss = $patientDetail->ss;
													}
													else{
														$subscriber_ss = $primaryComDetail->subscriber_ss;
													}
												?>	
												<?php if(trim($subscriber_ss)=="000-00-0000" or trim($subscriber_ss)=="" and $InsPriSS==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" size="16" name="ssn" value="<?php echo $row['ssn']; ?>" class="field text_small" >
											</td>
										</tr>
										<tr>
											<td colspan="2" class="text_smalli right" >
												&nbsp;&nbsp;Sub.Relation
											</td>
											<td colspan="1" class="text_smalli right" >
												S.S
											</td>
										</tr>
										<tr >
											<td colspan="5" align="left" class="text_10" style="padding-left:10px;">
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr class="bgcolor">
														<td colspan="2">
														<?php
															if($row['dob'] != '' &&  $row['dob'] != '0000-00-00') { 
																	list($dy,$dm,$dd) = explode('-',$row['dob']);
																	$dob = $dm.'-'.$dd.'-'.$dy;
																}
														?>
															<input type="hidden" name="from_date_subscriber1" value="<?php echo(date("m-d-Y"));?>" >
															<input type="text" size="11" id="dob" onBlur="checkdate(this);"  maxlength="10" name='dob' value='<?php echo $dob; ?>' title='mm-dd-yyyy' class="field text_small" />
															<a href="#"><img src='images/icon_cal.jpg' align='absbottom' width='24' height='22' border='0' onClick="newWindow('dob')"></a>
														</td>
														<td colspan="1">
															<?php
																if($primaryComDetail->subscriber_sex == ''){
																	$subscriber_sex = ucfirst($patientDetail->sex);
																}
																else{
																	$subscriber_sex = ucfirst($primaryComDetail->subscriber_sex);
																}
															?>	
															<select  name="gender" id="i1subscriber_sex" class="field text_small">				
																<option value="m" <?php if($row['gender'] == 'm') print 'selected="selected"'; ?> >Male</option>
																<option value="f" <?php if($row['gender'] == 'f') print 'selected="selected"'; ?> >Female</option>
															</select>																					
														</td>
													</tr>
													<tr class="text_smalli">
														<td colspan="2">
															DOB
														</td>
														<td colspan="1" class="text_smalli">
															Gender																		
														</td>
													</tr>
													<tr class="bgcolor">
														<td colspan="2" align="left" class="text_10" style="padding-left:10px;">
															<input  name="payment_auth" value="1" type="checkbox" <?php if($row['payment_auth'] == '1') print 'checked="checked"'; ?>>
														</td>
														<td colspan="1" align="left" class="text_10" style="padding-left:10px;">
															<input  name="sign_on_file" value="1" type="checkbox" <?php if($row['sign_on_file'] == '1') print 'checked="checked"'; ?>>
														</td>
													</tr>
													<tr>
														<td colspan="2" class="text_smalli right" >
															Pymt. Auth
														</td>
														<td colspan="1" class="text_smalli right" >
															Sign. on File
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="348" valign="top">
									<table width="220" height="125" class="tblBg" cellpadding="1" cellspacing="0"  id="Contacts1_table">
										<tr>
											<td class="text_10b" align="left" colspan="3">Contacts
											</td>
										</tr>
										<tr class="bgcolor">
											<td style="padding-left:11px;" colspan="3">
												<?php 
													if($primaryComDetail->subscriber_street == ''){
														$subscriber_street = ucwords($patientDetail->street);
													}
													else{
														$subscriber_street = ucwords($primaryComDetail->subscriber_street);
													}
													$streetPri = explode(" ",$subscriber_street);
													$firstStreetLen = strlen($streetPri[0]);
													$firstNameVar = '';
													for($l=0;$l<$firstStreetLen;$l++){
														if(intval($streetPri[0][$l]) == 0){
															$firstNameVar .= $streetPri[0][$l];
														}
														else{
															$intName .= $streetPri[0][$l];
														}
													}
													$subscriber_street = $intName.ucfirst($firstNameVar);
													for($l=1;$l<=count($streetPri);$l++){
														$subscriber_street .= ' '.$streetPri[$l];
													}				
												?>
												<?php if(trim($subscriber_street)=="" and $InsPriAddress==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" class="field text_small" size="45" name="address1" value="<?php echo $row['address1']; ?>"  />
											</td>
										</tr>
										<tr>
											<td class="text_smalli right" colspan="3" style="padding-left:11px;">
												Address 1
											</td>
										</tr>
										<tr class="bgcolor">
											<td style="padding-left:11px;" colspan="3">
												<?php $subscriber_street ="";
													if($primaryComDetail->subscriber_street_2 == ''){
														$subscriber_street = ucwords($patientDetail->street2);
													}
													else{
														$subscriber_street = ucwords($primaryComDetail->subscriber_street_2);
													}
													$subscriber_street = ucwords($primaryComDetail->subscriber_street_2);
													$streetPri = explode(" ",$subscriber_street);
													$firstStreetLen = strlen($streetPri[0]);
													$firstNameVar = '';
													$intName = '';
													for($l=0;$l<$firstStreetLen;$l++){
														if(intval($streetPri[0][$l]) == 0){
															$firstNameVar .= $streetPri[0][$l];
														}
														else{
															$intName .= $streetPri[0][$l];
														}
													}
													$subscriber_street = $intName.ucfirst($firstNameVar);
													for($l=1;$l<=count($streetPri);$l++){
														$subscriber_street .= ' '.$streetPri[$l];
													}	
														
												?>
												<?php //if(trim($subscriber_street)=="" and $InsPriAddress==1){$class="mandatory";} else{$class="text_10";}
													$class="text_10";
												 ?>
												<input type="text" class="field text_small" size="45" name="address2" value="<?php echo $row['address2']; ?>" />
											</td>
										</tr>
										<tr>
											<td class="text_smalli right" colspan="3" style="padding-left:10px;">
												Address 2
											</td>
										</tr>
										<tr class="bgcolor">
											<td align="left" class="text_10" style="padding-left:10px;">
												<?php 
													if($primaryComDetail->subscriber_postal_code == ''){
														$postal_code = ucfirst($patientDetail->postal_code);
													}
													else{
														$postal_code = ucfirst($primaryComDetail->subscriber_postal_code);
													}
												?>
												<?php if(trim($postal_code)=="" and $InsPriZip==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" id="zip" name="zip_code" value="<?php if($row['zip_code']) {echo $row['zip_code']; } ?>" class="field text_small" style="white-space:nowrap; width:55px; height:18px;" onBlur="return getCityStateFn(this,document.getElementById('city'),document.getElementById('state'));"  />
											</td>
											<td align="left" class="text_10" >
												<?php 
													if($primaryComDetail->subscriber_city == ''){
														$subscriber_city = ucfirst($patientDetail->city);
													}
													else{
														$subscriber_city = ucfirst($primaryComDetail->subscriber_city);
													}
												?>
												<?php if(trim($subscriber_city)=="" and $InsPriCity==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" size="14" name="city" id="city" value="<?php echo $row['city']; ?>" class="field text_small" />
										  	</td>
										  	<td align="left" class="text_10">
												<?php 
													if($primaryComDetail->subscriber_state == ''){
														$subscriber_state = ucfirst($patientDetail->state);
													}
													else{
														$subscriber_state = ucfirst($primaryComDetail->subscriber_state);
													}
												?>
												<?php if(trim($subscriber_state)=="" and $InsPriSate==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" size="2" name="state" id="state" value="<?php echo $row['state']; ?>" class="field text_small" />
										  	</td>
										</tr>		
										<tr>
											<td class="text_smalli right" style="padding-left:10px;">
												Zip
											</td>
											<td class="text_smalli" >
												City
											</td>
											<td class="text_smalli" >
												State
											</td>
										</tr>
										<tr class="bgcolor">
											<td style="padding-left:11px;" >
												<?php 
													if($primaryComDetail->subscriber_phone == ''){
														$subscriber_phone = ucfirst($patientDetail->phone_home);
													}
													else{
														$subscriber_phone = ucfirst($primaryComDetail->subscriber_phone);
													}
												?>
												<?php if(trim($subscriber_phone)=="" and $InsPriPhone==1){$class="mandatory";} else{$class="text_10";} ?>
												<input type="text" size="10"   maxlength="12" name="home_phone" class="field text_small" value="<?php echo $row['home_phone']; ?>" onBlur="ValidatePhone(this);" />
											</td>
											<td  >
												<?php 
													/*if($primaryComDetail->subscriber_biz_phone == ''){
														$subscriber_biz_phone = ucfirst($patientDetail->phone_biz);
													}
													else{*/
														$subscriber_biz_phone = ucfirst($primaryComDetail->subscriber_biz_phone);
													//}
												?>
												<?php //if(trim($subscriber_phone)=="" and $InsPriPhone==1){$class="mandatory";} else{$class="text_10";}
												$class="text_10";
												 ?>
												<input type="text" size="14"   maxlength="12" name="work_phone" class="field text_small" value="<?php echo $row['work_phone']; ?>" onBlur="ValidatePhone(this);" />
											</td>
											<td  >
												<?php 
													/*if($primaryComDetail->subscriber_mobile == ''){
														$subscriber_mobile = ucfirst($patientDetail->phone_cell);
													}
													else{*/
														$subscriber_mobile = ucfirst($primaryComDetail->subscriber_mobile);
													//}
												?>
												<?php //if(trim($subscriber_phone)=="" and $InsPriPhone==1){$class="mandatory";} else{$class="text_10";} 
												$class="text_10";
												?>
												<input type="text" size="14"   maxlength="12" name="mbl_phone" class="field text_small" value="<?php echo $row['mbl_phone']; ?>" onBlur="ValidatePhone(this);" />
											</td>
										</tr>
										<tr>
											<td class="text_smalli right" style="padding-left:10px;">
												Home Tel:
											</td>
											<td class="text_smalli" >
												Work Tel:      
											</td>
											<td class="text_smalli" >
												Mobile
											</td>
										</tr>
								</table>
							</td>
						</tr><!-- ravi -->
					</table>
					</td>
				</tr>
				<tr>
					<td class="text_10b" align="left" colspan="3" height="8"><a href="#"  onClick="MM_swapImage('scanBtn','','images/scan_click.gif',1)"  onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('scanBtn','','images/scan_hover.gif',1)" name="scanBtn" width="70" height="25" border="0"><img src="images/scan.gif"  name="scanBtn"  height="25" border="0" id="scanBtn" alt="scan" onClick="javascript:window.open('admin/scanPopUp.php?patient_in_waiting_id=<?php echo $waiting_id; ?>&patient_id=<?php echo $patient_id; ?>&INSURANCEScan=true&insuranceType=secondary','scanWinINSURANCE', 'width=775, height=650,location=yes,status=yes'); "/></a></td>
				</tr>
				<tr valign="top">
					<td colspan="6" id="imageTd" style="display:<?php echo $disDivIdDisplay;?>; ">
						<?php 
						if($insScan1Upload || $insScan2Upload){ $existInsImage='yes'; $iframe_ins_Height='height="100"'; $iframe_ins_width='width="400"'; }else {$iframe_ins_Height='height="0"';  }
						?>
						<input type="hidden" name="hidd_anyOneImageExist" id="hidd_anyOneImageExist" value="<?php echo $existInsImage;?>" />															
						<iframe name="iframeIOL" id="iframeIOL" frameborder="0" <?php echo $iframe_ins_Height;?> <?php echo $iframe_ins_width;?> scrolling="no" src="insurance_card_image.php?patient_in_waiting_id=<?php echo $waiting_id; ?>&insuranceType=secondary"></iframe>
					</td> 
				</tr>
				<tr height="10"><td>&nbsp;</td></tr>
				<tr>
					<td class="text_10b" align="center" colspan="3" height="8">
						<div id="saveBtnDivId" style="position:absolute; left:350px; top:390px;">
							<a href="#" onClick="frmSubmit();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" name="saveBtn" border="0" id="saveBtn" alt="save" /></a>
						</div>
					</td>
				</tr>
  		</table>
	</td>
</tr>
</table>
</form>

<script>
	function changeImgSize(){
			var target = 100;
			var imgHeight = top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').height;
			var imgWidth = top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').width;
			
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').height = heightNew;
				top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').width = widthNew;
			}
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgInsuranceThumbNail2').height;
			var imgWidth2 = document.getElementById('imgInsuranceThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgInsuranceThumbNail2').height = heightNew2;
				document.getElementById('imgInsuranceThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
	// IMAGE THUMBNAIL
	if(document.getElementById('imgInsuranceThumbNail2')){
		setTimeout('changeImgSize()', 100);
	}
	if(document.getElementById('imgInsuranceThumbNail22')){
		setTimeout('changeImgSize2()', 100);
	}
	
</script>
<form action="uploadInsuranceImage.php" name="frm_uploadInsSecImage" enctype="multipart/form-data" method="post" onSubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})">
	<div id="uploadImageDivId" style="position:absolute; left:90px; top:237px;  ">
		<input type="file"  id="upload_image_id" class="button" name="uploadImage" />		
	</div>	
	<div id="uploadBtnDivId" style="position:absolute; left:350px; top:235px;">
		<input type="image" src="images/upload.gif" name="uploadBtn" id="uploadBtn" alt="Upload" onClick="MM_swapImage('uploadBtn','','images/upload_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('uploadBtn','','images/upload_hover.gif',1)" >
	</div>	
	<input type="hidden" name="patient_in_waiting_id" value="<?php echo $waiting_id; ?>" />
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>" />
	<input type="hidden" name="insuranceId" value="<?php echo $insuranceId; ?>" />	
	<input type="hidden" name="insuranceType" value="secondary" />	
	<input type="hidden" name="hidd_delImage" id="hidd_delImage" value="" />
	
</form>
</body>
</html>