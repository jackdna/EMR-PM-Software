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
//--- For The search Of Patient To Re Print HCFA Form --------

include('acc_header.php');
function searchPatientData($val,$fld,$status="Active"){
	$qry = '';
	if($status==""){
		$status="Active";
	}
	if($fld == "Resp.LN"){
		$qry = "select * from patient_data left join resp_party on
				patient_data.id = resp_party.patient_id where
				resp_party.lname = '$val'";
	}
	else if($fld == "Ins.Policy"){
		$qry = "SELECT
			insurance_data.policy_number,	
			patient_data.fname,patient_data.pid,patient_data.lname,patient_data.postal_code,
			patient_data.street,patient_data.phone_home,patient_data.ss,patient_data.DOB,patient_data.id
			FROM insurance_data 
			INNER JOIN patient_data ON insurance_data.pid = patient_data.id
			WHERE insurance_data.policy_number LIKE '$val%'
			GROUP BY patient_data.id	
			ORDER BY patient_data.fname";
	}
	else{
		if(($fld != 'Nothing') && ($fld != 'LastFirstName') && ($fld != 'phone')){
			$val = ($fld != "id") ? $val."%" : $val;
			$qry = "select * from patient_data where $fld like '$val' 
					AND patientStatus='$status' order by fname";
		}else if($fld == 'LastFirstName'){
			$searchArr = preg_split("/(,|;)/",$val);
			$val1 = trim($searchArr[0]);
			$val2 = trim($searchArr[1]);
			$val3 = trim($searchArr[2]);
			if(empty($val3) == false){
				$qry .= " and sex like '$val3%'";
			}
			$qry = "select * from patient_data where lname like '$val1%' 
					AND fname  like '$val2%' AND patientStatus='$status'  $qry
					order by fname";
		}else if($fld != 'phone'){
			$qry = "select * from patient_data where (phone_home like '$val%' OR phone_biz like '$val%' 
				OR phone_contact like '$val%' OR phone_cell like '$val%')  AND patientStatus='$status' order by fname";            
		}
	}
	$qryId = imw_query($qry);
	if(imw_num_rows($qryId) > 0){
		while($row = imw_fetch_assoc($qryId)){
			$return_array[] = $row;
		}
	}
	return $return_array;		
}


function getFindBy($search)
{
   $genderSearch = "";
   $arrSearch = explode(";",$search);
   $search = trim($arrSearch[0]);
   $genderSearch = trim($arrSearch[1]);
   if(strtoupper($genderSearch) == "M"){
		$genderSearch = "Male";
   }
   elseif(strtoupper($genderSearch) == "MALE"){
		$genderSearch = "Male";
   }
   elseif(strtoupper($genderSearch) == "F"){
		$genderSearch = "Female";
   }
   elseif(strtoupper($genderSearch) == "FEMALE"){
		$genderSearch = "Female";
   }
   
   $search = trim($search);    
   $retVal = "Last";
   $ptrnSSN = '/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/'; 
   $ptrnPhone = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'; 
   $ptrnDate = '/^((0[1-9])|(1[012]))[-\/](0[1-9]|[12][0-9]|3[01])[-\/]((18|19|20|21)?[0-9]{2})$/'; 
   if(is_numeric($search))
   {
     $retVal = "ID";
   }
   elseif(preg_match($ptrnSSN,$search))
   {
     $retVal = "SSN";   
   }
   elseif(preg_match($ptrnPhone,$search))
   {
     $retVal = "phone";  
   }
   elseif(preg_match($ptrnDate,$search))
   {
     $retVal = "DOB";  
   }   
   elseif(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is',$search))
   {
     $retVal = "email";    
   }
   elseif(preg_match('/\w+/',$search) && (preg_match('/\d+/',$search)) && (preg_match('/\s*/',$search)))
   {
     $retVal = "street";    
   }
   
   elseif(strpos($search,",") !== false)
   {
     $retVal = "LastFirstName";
   }
   elseif(is_string($search))
   {
     $retVal = "Last";  
   }
   
   return $retVal;
}


//Get Patient Search Data
if($_POST['btn_sub'] != '' && $_POST['newFile'] == ''){
	$txt_for = trim($_POST["txt_for"]);
	$sel_by = $_POST["sel_by"];
	if(empty($txt_for))
	{
	  $sel_by = "Nothing";      
	}
	else
	{
		if(($sel_by != "Resp.LN") && ($sel_by != "Ins.Policy") )
		{
			$elem_status=$sel_by;
			$sel_by=trim(getFindBy($txt_for));
		}
	}
	
	switch($sel_by)
	{
	   case "Last":
		   $sel_by="lname";
	   break;
	   case "LastFirstName":
		   $sel_by="LastFirstName";
	   break;
	   case "street":
		   $sel_by="street";
	   break;
	   case "phone":
		   $sel_by="phone";
	   break;
	   case "First":
		   $sel_by="fname";
	   break;
	   case "ID":
		   $sel_by="id";
	   break;
	   case "DOB":
		   $sel_by="DOB";
	   break;
	   case "SSN":
		   $sel_by="ss";
	   break;
	   case "Resp.LN":
		   $sel_by="Resp.LN";
	   break;
	   case "Ins.Policy":
		   $sel_by="Ins.Policy";
	   break;
	}
	
	//echo "findBy:".$sel_by."<br>";
	//echo "status:".$elem_status."<br>";
	//echo "patient:".$txt_for."<br>";
	### Test 
    
    $patientData = searchPatientData($txt_for,$sel_by,$elem_status);
	if(count($patientData)>0){
		$data = '
			<tr class="grythead">
				<th class="text-center">First Name</th>
				<th class="text-center">Last Name</th>
				<th class="text-center">Patient Id</th>		
				<th class="text-center">Address</th>
				<th class="text-center">City</th>
				<th class="text-center">State</th>
				<th class="text-center">Phone</th>
				<th class="text-center">Balance</th>
			</tr>
		';
		foreach($patientData as $obj){
			$id = $obj['id'];
			$patBalance_acc=$patOverPaid=0;
			
			$qry= "SELECT * FROM patient_charge_list WHERE del_status='0' and patient_id  = '$id'";
			$rs = imw_query($qry);
			$totalEncounterBalance_acc="";
			while($res = imw_fetch_array($rs)){
				$patBalance_acc+= $res['totalBalance'];
				$patOverPaid+= $res['overPayment'];
			}unset($rs);
			$patBalance_acc= $patBalance_acc-$patOverPaid;
			
			$data .='
				<tr>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['fname'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['lname'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['id'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['street'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['city'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['state'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">'.$obj['phone_home'].'</a>&nbsp;</td>
					<td class="text-left" ><a href="#"  onclick="getName('.$id.');">$'.number_format($patBalance_acc,2).'</a>&nbsp;</td>
				</tr>
			';
		}
	}
	else{
		$data .= '<tr class="grythead text-center">
				<td colspan="6">
					No Record Found.
				</td>
			</tr>
		';
	}
}

$patient_id=$_POST['sid'];
$qry = "select * from patient_data where pid = $patient_id";
$res = imw_query($qry);
if(@imw_num_rows($res)>0){
	$row = imw_fetch_array($res);
	$patientFname = $row['fname'];
	$patientLname = $row['lname'];
	$patientMname = $row['mname'];
	$pid = $row['pid'];
	$patientName = ucwords(trim($patientLname.", ".$patientFname." ".$patientMname));
	$DOB = $row['DOB'];
			$today = getdate();
			$today_year = $today['year'];
			$today_mon = $today['mon'];
			$today_day = $today['mday'];
			$date = $today_year. "-" .$today_mon. "-" .$today_day;
				list($year, $month, $day) = explode('-',$DOB);
				$DOB = $month."-".$day."-".$year;
				$dob_year = $year;
				$age = $today_year-$dob_year;
	
	$socialSecurity = $row['ss'];
	$noBalanceBill = $row['noBalanceBill'];
	$facility_id = $row['default_facility'];
	$getFacilityNameStr = "SELECT * FROM facility WHERE id='$facility_id'";
	$getFacilityNameQry = imw_query($getFacilityNameStr);
	$getFacilityNameRow = imw_fetch_array($getFacilityNameQry);
	$facility_name = $getFacilityNameRow['name'];	
}
$encounter_id="";
if($encounter_id==""){
	$getEncounterStr = "SELECT * FROM patient_charge_list
						WHERE del_status='0' and patient_id  = '$patient_id'";
	$getEncounterQry = imw_query($getEncounterStr);
	$totalEncounterBalance_acc="";
	$creditAmountBalance_acc="";
	$overPayment_acc="";
	if(imw_num_rows($getEncounterQry)>0){
		while($getEncounterRow = imw_fetch_array($getEncounterQry)){
				$totalEncounterBalance_acc+= $getEncounterRow['totalBalance'];
				if(($getEncounterRow['creditAmount'])>0){
					$creditAmountBalance_acc+= $getEncounterRow['creditAmount'];
				}
				$overPayment_acc+= $getEncounterRow['overPayment'];
			}
		}	
		if($_SESSION['patient']<>""){
			$chk_ins_ses=$_SESSION['patient'];
		}else{
			$chk_ins_ses=$patient_id;
		}
	$getEncounterStr1 = "SELECT * FROM patient_charge_list
						WHERE del_status='0' and patient_id  = '$chk_ins_ses'";
	$getEncounterQry1 = imw_query($getEncounterStr1);
	if(imw_num_rows($getEncounterQry1)>0){
		while($getEncounterRow1 = imw_fetch_array($getEncounterQry1)){
			
			// INSURANCE PROVIDERS 
			$primaryInsProviderId = $getEncounterRow1['primaryInsuranceCoId'];
			$secondaryInsProviderId = $getEncounterRow1['secondaryInsuranceCoId'];
			$tertiaryInsProviderId = $getEncounterRow1['tertiaryInsuranceCoId'];			
			// GETTING COMPANIES NAME
			// GETTING COMPANIES NAME
		$getPrimaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id = '$primaryInsProviderId'";
		$getPrimaryInsCoNameQry = imw_query($getPrimaryInsCoNameStr);
		$getPrimaryInsCoNameRow = imw_fetch_array($getPrimaryInsCoNameQry);
		$primaryInsCoId = $getPrimaryInsCoNameRow['id'];
		$primaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
		if($primaryInsCoName==""){
			$primaryInsCoName = $getPrimaryInsCoNameRow['name'];
		}	
		$getSecondaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$secondaryInsProviderId'";
		$getSecondaryInsCoNameQry = imw_query($getSecondaryInsCoNameStr);
		$getSecondaryInsCoNameRow = imw_fetch_array($getSecondaryInsCoNameQry);
		$secondaryInsCoId = $getSecondaryInsCoNameRow['id'];
		$secondaryInsCoName = $getSecondaryInsCoNameRow['in_house_code'];
		if($secondaryInsCoName==""){
			$secondaryInsCoName = $getSecondaryInsCoNameRow['name'];
		}	
		$getTertiaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$tertiaryInsProviderId'";
		$getTertiaryInsCoNameQry = imw_query($getTertiaryInsCoNameStr);
		$getTertiaryInsCoNameRow = imw_fetch_array($getTertiaryInsCoNameQry);
		$tertiaryInsCoId = $getTertiaryInsCoNameRow['id'];
		$tertiaryInsCoName = $getTertiaryInsCoNameRow['in_house_code'];
		if($tertiaryInsCoName==""){
			$tertiaryInsCoName = $getTertiaryInsCoNameRow['name'];
		}
		// GETTING COMPANIES NAME
		$insProvidersIdArr = array($primaryInsCoId, $secondaryInsCoId, $tertiaryInsCoId);
		$insProvidersNameArr = array($primaryInsCoName, $secondaryInsCoName, $tertiaryInsCoName);
		// GETTING PROCEDURE DETAILS.
		}
	}	
}
$chld_id=$_REQUEST['chld_id'];
$ins_id=$_REQUEST['ins_id'];
$paid_by=$_REQUEST['paid_by'];

if($_REQUEST['win_clos']<>""){
?>
<script language="javascript">
	var encounter_id = $("#encounter_id",window.opener.top.fmain.document.makePaymentFrm);
	encounter_id_val = encounter_id.val();
	if(window.opener.credit_tbl_id){
		window.opener.credit_tbl_id(encounter_id_val);
	}else if(window.opener.top.fmain.credit_tbl_id){
		window.opener.top.fmain.credit_tbl_id(encounter_id_val);
	}
	window.close();
</script>
<?php
}

//---get recent patient for search ----
$auth_id = $_SESSION['authId'];
$qry = "select patient_id,patientFindBy from recent_users 
where provider_id = $auth_id order by enter_date";
$qryRes = imw_query($qry);
$searchOption = '';
while($row_data = imw_fetch_array($qryRes)){
	$patient_id = $row_data['patient_id'];
	$patientFindBy = $row_data['patientFindBy'];
	$qry = imw_query("select concat(lname,', ',fname) as name , mname from patient_data
			where id = $patient_id");
	while($patientDetails = imw_fetch_array($qry)){
		$patient_name = $patientDetails['name'].' '.substr($patientDetails['mname'],0,1);
		$patient_name2 = $patientDetails['name'];
		$searchOption .= '
			<option value = "'.$patient_id.':'.$patient_name2.':'.$patientFindBy.'">'.ucwords($patient_name).' - '.$patient_id.'</option>
		';
	};
}

?>
<body>
	<head>
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
	</head>
	<div class="row">
		<!-- Search Header -->
		<div class="col-sm-12">
			<div class="row">
				<div class="purple_bar col-sm-12">
					<label>Search Patient</label>	
				</div>
				<div class="col-sm-12 pt10">
					<div class="row">
						<form name="frm_sel" action="search_outstanding_Patient.php" method="post">
							<input type="hidden" name="newFile" id="newFile" value="<?=$newFile?>" >					
							<input type="hidden" name="sid" id="sid" value="<?=$sid?>" >
							<input type="hidden" id="ovr_paid" name="ovr_paid" value="<?php echo $_REQUEST['ovr_paid']; ?>">
							<input type="hidden" name="chld_id" id="chld_id" value="<?php echo $_REQUEST['chld_id']; ?>">
							<input type="hidden" name="ins_id" id="ins_id" value="<?php echo $_REQUEST['ins_id']; ?>">
							<input type="hidden" name="paid_by" id="paid_by" value="<?php echo $_REQUEST['paid_by']; ?>">
							<input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id']; ?>">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3">
										<input type="text" name="txt_for" id="txt_for" value="<?php print $txt_for; ?>" class="form-control">	
									</div>
									<div class="col-sm-3">
										<select name="sel_by" id="sel_by" onChange="searchPatient2(this)"  class="selectpicker" data-width="100%" data-size="10" onkeypress="if (event.keyCode==13){ return chkNew() }">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
											<option value="Deceased">Deceased</option>
											<option value="Resp.LN">Resp.LN</option>
											<option value="Ins.Policy">Ins.Policy</option> 		
											<?php print $searchOption; ?>
										</select> 	
										<input type='hidden' name="date" id="date" value=<?php print $date; ?> >
										<input type='hidden' name="btn_sub" id="btn_sub" value='a' >
									</div>
									<div class="col-sm-2">
										<button class="btn btn-primary" type="button" onClick="javascript:chkNew();">Search</button>	
									</div>	
								</div>	
							</div>
							<div class="clearfix"></div>
							<?php if(!$newFile){ ?>
							<div class="col-sm-12 pt10" style="height:<?php echo $_SESSION['wn_height'] - 300 ?>px;overflow-x:scroll">
								<table class="table table-condensed table-bordered table-striped">
									<?php echo $data; ?>	
								</table>
							</div>	
							<?php } ?>
						</form>
					</div>	
				</div>	
			</div>	
		</div>	
		
		<!-- Patient Details Block-->
		<?php if($newFile){?>
			<div class="col-sm-12 pt10">
				<div class="row">
					<!-- Heading Row -->
					<div class="col-sm-12 pt10">
						<table class="table table-condensed table-bordered">
							<tr class="purple_bar">
								<th class="text-left">Charge Details</th>
								<th class="text-left">Patient Name:</th>
								<th class="text-left"><?php echo $patientName. " - " .$pid; ?></th>
								<th class="text-left"><?php if($noBalanceBill=='1') echo "No Balance Bill"; ?></th>
								<th class="text-right">DOB:</th>
								<th class="text-left"><?php echo $DOB." (".$age." Yrs.)"; ?></th>
								<th class="text-right">S S #:</th>
								<th class="text-left"><?php echo $socialSecurity; ?></th>	
							</tr>
						</table>
					</div>	
					<!-- Patient Due Details -->
					<div class="col-sm-12 pt10">
						<iframe id="iframe1" align="top" name="iframe1" frameborder="0" scrolling="auto" height="430" width="100%" src="patient_pending_balance.php?chld_id=<?php echo $chld_id; ?>&ovr_paid=<?php echo $_REQUEST['ovr_paid']; ?>&patient_id=<?php echo $pid; ?>&b_id=<?php echo $_REQUEST['b_id']; ?>"></iframe>	
					</div>
				</div>	
			</div>	
		<?php } ?>	
	
	<?php if($newFile){?>
	<div class="col-sm-12 pt10">
		<?php
			$ovr_paid_sum_arr = explode(',',$_REQUEST['ovr_paid']);
			$ovr_paid_sum = str_replace(',','',array_sum($ovr_paid_sum_arr));
		?>
		<div class="row">
			<form name="makePaymentFrm" action="search_outstanding_Patient.php" method="post">
				<input type="hidden" name="paymentEditId" id="paymentEditId" value="">
				<input type="hidden" name="ovr_paid" id="ovr_paid" value="<?php echo $_REQUEST['ovr_paid']; ?>">
				<input type="hidden" name="ovr_paid_sum" id="ovr_paid_sum" value="<?php echo $ovr_paid_sum; ?>">
				<input type="hidden" name="encounter_id" id="encounter_id" value="<?php echo $encounter_id; ?>">
				<input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id']; ?>">
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-4">
							<label>Amount</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-usd"></span>	
								</div>	
								<input name="paidAmount" id="paidAmountId" readonly type="hidden" value="<?php echo $amount; ?>"  />
								<input name="paidAmountNow" id="paidAmountNow" readonly type="text" value="<?php echo number_format($amount,2); ?>" class="form-control" />
							</div>	
						</div>
						<?php
							//----------------------- credit DETAILS -----------------------//
									$gettot_crd = "SELECT sum(credits) as credits 
									FROM patient_charge_list_details  WHERE  
									del_status='0' and (newBalance>0) and patient_id='$sid'";
									$gettot_crdQry = imw_query($gettot_crd);
									$gettot_crdrow = imw_fetch_array($gettot_crdQry);
										$credit_final  = $gettot_crdrow['credits'];
										if($credit_final>0){
											$credit_final=$credit_final;
										}else{
											$credit_final='0.00';
										}
								//----------------------- credit DETAILS -----------------------//
							?>
						<div class="col-sm-4">
							<label>Amount Due</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-usd"></span>	
								</div>	
								<input readonly type="text" value="<?php echo number_format($totalEncounterBalance_acc, 2); ?>" class="form-control" />
							</div>		
						</div>	
						<div class="col-sm-4">  	
							<label>Credit Balance</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-usd"></span>	
								</div>	
								<input readonly type="text" value="<?php echo number_format($overPayment_acc, 2); ?>" class="form-control" />
							</div>		
						</div>	
					</div>	
					<div class="row">
						<div class="col-sm-3">
							<label>Who Paid</label>
							<input type="hidden" name="insSelected" id="insSelected" value="">
							<select name="paidBy" id="paidById" onChange="return paymentModeFn();" class="selectpicker show-menu-arrow" data-width="100%">
								<option value="Patient" <?php if($paid_by=="Patient"){echo "selected";} ?>>Patient</option>
								<option value="Res. Party"<?php if($paid_by=="Res. Party"){echo "selected";} ?>>Res. Party</option>
								<option value="Insurance" <?php if($paid_by=="Insurance"){echo "selected";} ?>>Insurance</option>
							</select>
						</div>	
						<div id="insCoNames" class="col-sm-3" <?php if($ins_id=="" && $paid_by<>"Insurance"){ echo  "style='display:none;'";  } ?>>
							<div class="row" id="insProviderTr">
								<div class="col-sm-12">
									<label>Ins. Pr:</label>	
									<select name="insProviderName" id="insProviderCoId" class="selectpicker" data-width="100%" data-title="Select">
										<?php
											foreach($insProvidersNameArr as $id => $insCoName){
												foreach($insProvidersIdArr as $key => $insCoId){
													if($id==$key){
														if($insCoName==''){ $insCoName = 'N/A';  }
														$lenInsCoName = strlen($insCoName);
														if($lenInsCoName>10){
															$insCoName = substr($insCoName, 0, 10)."..";
														}
														?>
													<option value="<?php echo $insCoId; ?>" <?php if($ins_id<>""){if($ins_id==$insCoId){echo "selected";}} ?>><?php echo $insCoName; ?></option>
													<?php
													}
												}
											}
										?>
									</select>	
								</div>	
							</div>	
						</div>	
						<div class="col-sm-3">
							<label>Claims:</label>
							<select name="paymentClaims" id="paymentClaimsId" class="selectpicker" data-width="100%" onChange="return claimChange();">
								<option value="Debit_Credit">Debit/Credit (Adj)</option>
							</select>							
						</div>	
						<div class="col-sm-3" id="cr_deb_note">
							<?php $cur_ref=date('m/d/y'); ?>
							<label>Cr Note:</label>
							<input type="text"  id="cr_note_debit" name="cr_note_debit" class="form-control">	
						</div>		
					</div>	
					
					<div class="row pt10 text-center">
						<div class="col-sm-12" id="applySubmitTr">
							<input type="button" class="btn btn-success" id="applySubmit" name="applySubmit" value="Apply"  onClick="return apply_save('applySubmit','applyReceiptSubmit');" />&nbsp;
							<?php if($_REQUEST['b_id']>0){
							?>	
								<input type="button" class="btn btn-danger" id="cancel" name="cancel" value="Cancel" onClick="window.close();" /> &nbsp;
							<?php }else{?>
							<input type="button" class="btn btn-primary" id="applyReceiptSubmit" name="applyReceiptSubmit" value="Apply & Print Receipt" onClick="return apply_save('applySubmit','applyReceiptSubmit','print');" />
							<?php } ?>
						</div>	
					</div>
				</div>
			</form>		
		</div>		
	</div>	
	<?php } ?>
</div>	
<?php if($imp_ins_id<>""){?>
<script language="javascript">
	set_updat_crd('<?php echo $imp_ins_id; ?>');
</script>
<?php }?>
<script>
		function chkNew()
		{	
			if($('#txt_for').val() == ''){
				fAlert('Please provide some text to search');
				$('#txt_for').focus();
				return false;
			}
			var frm = document.frm_sel;
			frm.newFile.value ='';
			frm.submit();
		}
		
		function searchPatient2(obj){
			var patientdetails = $(obj).val().split(':');
			if(isNaN(patientdetails[0]) == false){
				$("#txt_for").val(patientdetails[1]);
				$("#sel_by").val(patientdetails[2]);
				document.frm_sel.submit();
			}
		}
		
		function getName(id){
			document.frm_sel.sid.value = id;
			document.frm_sel.date.value = 1;
			document.frm_sel.newFile.value = 'New';
			document.frm_sel.submit();
		}
		
		function ref_win(){
			var encounter_id = $("#encounter_id",window.opener.top.fmain.document.makePaymentFrm);
			encounter_id_val = encounter_id.val();
			if(window.opener.credit_tbl_id){
				window.opener.credit_tbl_id(encounter_id_val);
			}else if(window.opener.top.fmain.credit_tbl_id){
				window.opener.top.fmain.credit_tbl_id(encounter_id_val);
			}else if(window.opener.top.fmain.credit_tbl_id){
				window.opener.top.fmain.credit_tbl_id(encounter_id_val);
			}
			window.close();
		}
		
		function select_patient(){
			var chk = false;
			var sel_obj = document.getElementsByName("patient_id_arr[]");
			var patIdArr = new Array();
			var j = 0;
			for(i=0;i<sel_obj.length;i++){
				if(sel_obj[i].checked == true){
					chk = true;
					patIdArr[j] = sel_obj[i].value;
					j++;			
				}
			}
			var patId = patIdArr.join(",");
			if(chk == false){
				alert('ق│┬ Please select any patient to submit.');
				return false;
			}
			else{
				getName(patId);
			}	
		}
		function printReceipt(eId,ch_id,pat_id){
			if(!ch_id){
				var ch_id="";
			}
			if(eId){
				var eId="";
			}
			if(!pat_id){
				var pat_id="";
			}
			var parWidth = document.body.clientWidth+10;
			var parHeight = document.body.clientHeight+80;

			window.open("receipt.php?eId="+eId+'&ch_id='+ch_id+'&pat_id='+pat_id,'','width='+parWidth+',height='+parHeight+',top=10,left=40,scrollbars=yes,resizable=yes');
		}
		
		function apply_save(a,b,c){
			if(document.makePaymentFrm.ovr_paid_sum.value>0){
				var chk_ovr_paid=document.makePaymentFrm.ovr_paid_sum.value;
			}else{
				var chk_ovr_paid=document.makePaymentFrm.ovr_paid.value;
			}
			var chk_paid_all=document.getElementById("paidAmountNow").value;
			var credit_note = document.getElementById("cr_note_debit").value;
			var paidBy = document.getElementById("paidById").value;
			
			var tot_print_arr=top.iframe1.document.getElementsByName('chkbx[]').length;
			var sel_chk=0;
			for(f=1;f<=tot_print_arr;f++){
				if(top.iframe1.document.getElementById('chkbx'+f).checked==true){
					sel_chk++;
				}
			}
			
			var print_ch_arr1=new Array();
			for(g=1;g<=tot_print_arr;g++){
				if(top.iframe1.document.getElementById('chkbx'+g).checked==true){
					var print_ch_arr=top.iframe1.document.getElementById('chkbx'+g).value;
					print_ch_arr1.push(print_ch_arr);
				}
			}
			
			if(sel_chk==0){
				alert("Please select any procedure to make credit.")
				return false;
			}
			if(paidBy=='Insurance'){
				if(document.getElementById("insProviderCoId")){
					if(document.getElementById("insProviderCoId").value==''){
						alert("Please select insurance company. if selected may be N/A.")
						return false;
					}
				}
				if(document.getElementById("insProviderCoId")){
					var insCoIS = document.getElementById("insProviderCoId").selectedIndex;
					document.getElementById("insSelected").value = insCoIS;
					top.iframe1.document.paymentFrm.insProviderName.value = document.getElementById("insProviderCoId").value;
					top.iframe1.document.paymentFrm.insSelected.value = document.getElementById("insSelected").value;
				}
			}
			
			if(parseInt(chk_paid_all)>parseInt(chk_ovr_paid)){
				alert("Debit amount can not be greater than credit amount");
				//alert(chk_paid_all+'>'+chk_ovr_paid);
				return false;
			}
			top.iframe1.document.paymentFrm.credit_note.value = credit_note;
			top.iframe1.document.paymentFrm.paidBy.value = paidBy;
			sid=document.frm_sel.sid.value;
			document.getElementById(a).disabled='true';
			if(document.getElementById(b)){	
				document.getElementById(b).disabled='true';	
			}
			top.iframe1.document.paymentFrm.submit();
				if(c == 'print'){
					printReceipt('<?php echo $encounter_id; ?>',print_ch_arr1,sid);
				}
				/*document.location.href="search_outstanding_Patient.php?win_clos="+sid;*/
				//window.close();
		}
		function paymentModeFn(){
			var whoWillPay = document.getElementById("paidById").value;
			if(whoWillPay=='Patient'){
				document.getElementById("insCoNames").style.display="none";
			}
			if(whoWillPay=='Res. Party'){
				document.getElementById("insCoNames").style.display="none";
			}
			if(whoWillPay=='Insurance'){
				document.getElementById("insCoNames").style.display="block";
			}
		}
		
	</script>
</body>
</html>