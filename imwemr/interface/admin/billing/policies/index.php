<?php
require_once("../../admin_header.php");
//----- Start Update Encounter Id From HQ Facility ------
$qry = imw_query("select encounterId from facility where facility_type = '1' LIMIT 0,1 ");
list($encounterId) = imw_fetch_array($qry);
if($encounterId){
	$readOnly = 'readonly="readonly"';
}
$data['Encounter_ID'] = $encounterId;

$writeOffAccess = $_REQUEST['writeOffAccess'];
$_POST['Telephone'] = core_phone_unformat($_POST['Telephone']);
$encounter_id = UpdateRecords(1,'policies_id',$data,'copay_policies');
//----- End Update Encounter Id From HQ Facility ------

//----- Start Save Or Update Query For Polocies -------

$sql_qry = imw_query("select * from cpt_fee_tbl WHERE status='active' AND delete_status = '0' order by cpt_prac_code ASC");
if(imw_num_rows($sql_qry) > 0){
	while($sql_row=imw_fetch_array($sql_qry)){
		$code = $sql_row["cpt_prac_code"];
		$stringAllProcedures.="'".str_replace("'","",$code)."',";	
	}
}		
$stringAllProcedures = substr($stringAllProcedures,0,-1);
if($txtsave){
	$qryId = imw_query("update facility set encounterId = '$Encounter_ID' where facility_type = 1");
	
	/* $qry = "update patient_data set erx_entry = '$erx_entry'";
	if($erx_entry == 0){
		$qry .= " where erx_patient_id = ''";
	} */
	$qryId = imw_query($qry);
	$item_name_arr=$_POST['item_name'];
	$item_id_arr=$_POST['item_id'];
	$item_show_arr=$_POST['item_show'];
	
	for($it=0;$it<count($item_name_arr);$it++){
		$item_name=$item_name_arr[$it];
		$item_id=$item_id_arr[$it];
		$item_show=$item_show_arr[$item_id];
		$item_qry="update check_in_out_fields set item_name ='$item_name',item_show ='$item_show' where id='$item_id'";
		$item_up=imw_query($item_qry);
	}
	
	unset($_POST['item_id']);
	unset($_POST['item_name']);
	unset($_POST['item_show']);

	$_POST['cpt_print_details'] = $txt_cpt_print;
	
	if($_POST['txt_no_details']=='no'){
		$_POST['cpt_print_details'] = 'no';
		$_POST['show_check_in']  = 0; 
		$_POST['show_check_out'] = 0; 
	}else{
		if($_POST['cpt_print_details']=='yes'){
			$_POST['show_check_in']  = $_POST['show_check_in']; 
			$_POST['show_check_out'] = $_POST['show_check_out']; 
		}else{
			$_POST['show_check_in']  = 0; 
			$_POST['show_check_out'] = 0; 
		}
	}
	if($_POST['ci_demographics']=='on'){
		$_POST['ci_demographics']=1;
	}else{
		$_POST['ci_demographics']=0;
	}

	if(!isset($_POST['visitcode_warn']))$_POST['visitcode_warn']=0;
	if(!isset($_POST['collapse_docs_default'])){
		$_POST['collapse_docs_default']=0;
	}

	if(!isset($_POST['checkin_on_done'])){
		$_POST['checkin_on_done']=0;
	}
	
	if($txtId == ''){
		if(!$_POST['no_balance_bill']){
			$_POST['no_balance_bill'] = 0;
		}
					
		$insertId = AddRecords($_POST,'copay_policies');		
		if($insertId) {
			$success=1;
			$err = 'Record Successfully Saved.';
		}	
	}else{
		if(!$_POST['no_balance_bill']){
			$_POST['no_balance_bill'] = 0;
		}
		if($_POST['secondary_copay']=='No'){
			$_POST['sec_copay_for_ins']="";
			$_POST['sec_copay_collect_amt']=0;
		}
		if($_POST['sec_copay_for_ins']==""){
			$_POST['sec_copay_collect_amt']=0;
		}
		if($_POST['vip_copay_not_collect']==""){
			$_POST['vip_copay_not_collect']=0;
		}
		if($_POST['vip_ref_not_collect']==""){
			$_POST['vip_ref_not_collect']=0;
		}
		if($_POST['vip_bill_not_pat']==""){
			$_POST['vip_bill_not_pat']=0;
		}
		if($_POST['ptinfodiv']==""){
			$_POST['ptinfodiv']=0;
		}
		
		if(!isset($_POST['refractionGiveOnly'])){
			$_POST['refractionGiveOnly']="";
		}

		if($_POST['statement_cycle']==""){
			$_POST['statement_cycle']=0;
		}
		if($_POST['ar_aging']==""){
			$_POST['ar_aging']=0;
		}
		
		if($_POST['del_proc_noti']==""){
			$_POST['del_proc_noti']=0;
		}
			
		if($_POST['accept_assignment']!=$_POST['accept_assignment_old']){
			$ins_update = imw_query("update insurance_companies set ins_accept_assignment='".$_POST['accept_assignment']."'");
			imw_query("insert into copay_policies_ae_modifiy set modifier_by='".$_SESSION['authId']."',modifier_on='".date('Y-m-d H:i:s')."',reason='".$_POST['accept_assignment_reason']."'");
		}else{
			unset($_POST['accept_assignment_reason']);
		}
		
		$connect_mode="T";
		if($_POST['emdeon_test_pro']=='1'){
			$connect_mode="P";
		}

		imw_query("update clearing_houses set status='0'");
		$cl_house_update_res = imw_query("update clearing_houses set status='1',connect_mode='".$connect_mode."' where id='".$_POST['house_name']."'");
		if(imw_affected_rows()>0){
			if($_POST['house_name']=='1') imw_query("UPDATE insurance_companies SET claim_type='0' WHERE claim_type='2'");
			else if($_POST['house_name']=='2') imw_query("UPDATE insurance_companies SET claim_type='2' WHERE claim_type='0'");
		}
		
		unset($_POST['accept_assignment_old']);
		unset($_POST['bil_pol_ae_reason']);
		unset($_POST['house_name']);
		unset($_POST['emdeon_test_pro']);
		$insertId = UpdateRecords($txtId,'policies_id',$_POST,'copay_policies');
		
		if($insertId){	
			$success=1;		
			$err = 'Record Successfully Updated.';
		}	
		// Write Off Access Updates
		if($writeOffAccess)
			$arrayData['writeOffAccess'] = 1;
		else
			$arrayData['writeOffAccess'] = 0;
			UpdateRecords($txtId,'policies_id',$arrayData,'copay_policies');

		$pvc = $_POST['patient_verbal_communication'];
		unset($_POST['patient_verbal_communication']);
		$pvcData = array();
		if($pvc == '') {
			$pvcData['patient_verbal_communication'] = 0;
		} else {
			$pvcData['patient_verbal_communication'] = 1;
		}
		UpdateRecords($txtId,'policies_id',$pvcData,'copay_policies');
	}

	if($Interchange_Control){
		//---- Start To Get Interchange Control ---
		$sql_qry = imw_query("select max(Interchange_control) as Interchange_control from batch_file_submitte");
		$batchQryRes = imw_fetch_array($sql_qry);
		
		$interchangeControl = $batchQryRes[0]['Interchange_control'];
		//---- End To Get Interchange Control ---				
		if($interchangeControl < $Interchange_Control){
			imw_query("update batch_file_submitte set Interchange_control = '$Interchange_Control'
						where Interchange_control = $res");						
		}
	}
}
//----- End Save Or Update Query For Polocies -------
//----- Start Get Data For Polocies -------
$policyDetail = getRecords('copay_policies','policies_id',1);
if($policyDetail["Name"] == ''){
	$policyDetail["Name"] = 'imwemr';
}
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/patient_info.js"></script>
<script type="text/javascript">
<?php
if($stringAllProcedures!=""){
?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php
}
?>
// JavaScript Document
document.onkeydown = keyCatcher;

<?php if($err){?>
	top.show_loading_image('hide');
	top.alert_notification_show('<?php echo $err;?>');
<?php }?>
function keyCatcher() 
{
	var e = event.srcElement.tagName;
	//var f= event.srcElement.tagType;
	//alert(f);
 	//
	if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
	{
		event.cancelBubble = true;
		event.returnValue = false;
	}
}
	function policyCheck(){
		top.show_loading_image('show');
		var myvar = document.policiesFrm;
		var msg = '';
		if(myvar.Interchange_Control.value == ''){
			msg += '&bull; Please Enter Interchange Control#.<br>';
		}

		if(myvar.Zip.value == ''){
			//msg+= '&bull; Please Enter Zip Code.<br>';
		}
		if(isNaN(myvar.Encounter_ID.value)){
			msg+= '&bull; Please Enter Numeric Value For Encounter Id.<br>';
		}
		if(myvar.Encounter_ID.value < myvar.txtDefaultEncounter.value){
			msg+= '&bull; Encounter Id Must Be Greater Then Default Value.<br>';
			myvar.Encounter_ID.value = myvar.txtDefaultEncounter.value;
		}
		if(parseInt(myvar.RTEValidDays.value) > 120){
			msg+= '&bull; Please Enter RTE Valid Days Lesser Then 120.<br>';
			document.getElementById("RTEValidDays").focus();
		}	
		if(msg != ''){
			parent.parent.show_loading_image('none');
			fAlert(msg);
			return false;
		}
		document.policiesFrm.submit();
	}
	
	function show_sec_ins(val){
		if(val=='Yes'){
			document.getElementById('ins_base_sec_copay').style.display='block';
		}else{
			document.getElementById('ins_base_sec_copay').style.display='none';
		}
	}
	
	function show_sec_amt(val){
		if(val==''){
			document.getElementById('sec_copay_collect_amt').style.display='none';
		}else{
			document.getElementById('sec_copay_collect_amt').style.display='inline';
		}
	}
	
	function sel_cpt_print_fun(obj,nextField){
		var nextChk = false;
		if(obj.checked == false){
			nextChk = true;
		}
		//dgi(nextField).checked = nextChk;
		var item_obj=dgn('item_name[]');
		var item_show_obj=dgn('item_show[]');
		if(obj.value=='no'){
			if(obj.checked == true){
				document.getElementById('show_check_in').disabled =true;
				document.getElementById('show_check_out').disabled=true;
				for(i=0;i<item_obj.length;i++){
					item_obj[i].disabled=true;
				}
			}else{
				document.getElementById('show_check_in').disabled=false;
				document.getElementById('show_check_out').disabled=false;
				for(i=0;i<item_obj.length;i++){
					item_obj[i].disabled=false;
				}
			}
		}else if(obj.value=='yes'){
			if(obj.checked == true){
				document.getElementById('show_check_in').disabled=false;
				document.getElementById('show_check_out').disabled=false;
				for(i=0;i<item_obj.length;i++){
					item_obj[i].disabled=false;
				}
			}else{
				document.getElementById('show_check_in').disabled=true;
				document.getElementById('show_check_out').disabled=true;
				for(i=0;i<item_obj.length;i++){
					item_obj[i].disabled=true;
				}
			}
		}
	}
	
	function sel_check_in_out_fun(obj,nextField){
		var chkArr = new Array("show_check_in","show_check_out");
		var chkdetail = false;
		for(i in chkArr){
			if(dgi(chkArr[i]).checked == true){
				chkdetail = true;
			}
		}
		dgi("txt_cpt_print").checked = chkdetail;
	}
	
	function hideDispCode(){
		if(dgi('vip_bill_not_pat').checked == true){
			dgi('vip_write_off_code').style.display='block';
		}else{
			dgi('vip_write_off_code').value='';
			dgi('vip_write_off_code').style.display='none';
		}
	}
	
	function set_ae(action){
		var accept_assignment_old = $('#accept_assignment_old').val();
		var accept_assignment_new = $('#accept_assignment').val();
		if(action==""){
				
				var ask = "<div id=\"policy_index\" class=\"modal fade\" role=\"dialog\"><div class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header bg-primary\">";
				ask += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button><h4 class=\"modal-title\">Modal Header</h4></div><div class=\"modal-body\">";
				ask += "<div class=\"form-group\">";
				ask += "<p>This will change all Insurance companies to selected setting. Are you sure?</p><label for=\"bil_pol_ae_reason\">Reason</label>";
				ask += "<textarea name=\"bil_pol_ae_reason\" id=\"bil_pol_ae_reason\" rows='1' class=\"form-control\"></textarea>";
				ask += "</div><div class=\"form-group\"><p class=\"text-danger\">Note: This setting will take effect, once you click on SAVE button</p></div>";
				ask += "</div><div class=\"modal-footer\"><input name='yes' class=\"btn btn-success\" id='yes' type='button' value='Yes' onClick='set_ae(\"yes\");'><input name='no' class='btn btn-danger' id='no' type='button' value='No' onClick='set_ae(\"no\")'></div></div></div></div>";
				if($('#policy_index').length == 0){
					$('body').append(ask);
				}
				$('#policy_index').modal('show');
			
		}else{
			if(action=="yes"){
				if($.trim($("#bil_pol_ae_reason").val())==""){	
					fAlert("Please enter the Reason.");
					return false;
				}
				var new_val=$('#policy_index #bil_pol_ae_reason').val();
				$('#accept_assignment_reason').val(new_val);
			}else{
				$('#accept_assignment').val(accept_assignment_old);
			}
			$('#policy_index').modal('hide');
		}
	}
</script>
</head>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
<style>
	#div_loading_image { position: inherit; top: inherit; }
</style>
<body>
<input type="hidden" name="preObjBack" id="preObjBack" value="">
<form name="policiesFrm" method="post" onSubmit="return policyCheck();">
<input type="hidden" name="txtId" id="txtId" value="<?php print $policyDetail["policies_id"];?>" />
<input type="hidden" name="txtDefaultEncounter" id="txtDefaultEncounter" value="<?php print $policyDetail["Encounter_ID"];?>" />
<input type="hidden" name="txtsave" id="txtsave">
<div class="whtbox tblBg">
	<div class="row pt10">
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="grpbox">
                <div class="head"><span>Accounting</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="Interchange_Control">Start Interchange Control#</label>
								<input class="form-control" name="Interchange_Control" id="Interchange_Control" value="<?php if($policyDetail["Interchange_Control"])  print $policyDetail["Interchange_Control"];?>" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="Encounter_ID">Start Encounter ID</label>
								<input <?php print $readOnly; ?> class="form-control" name="Encounter_ID" id="Encounter_ID" value="<?php if($policyDetail["Encounter_ID"]) print $policyDetail["Encounter_ID"];?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="elem_billingCode">Billing Code</label>
								<select name="elem_billingCode" id="elem_billingCode" class="form-control minimal">
									<option value="">--Select--</option>
									<option value="Eye Code"  <?php print ($policyDetail["elem_billingCode"]=="Eye Code") ? "selected" : "" ;?> >Eye Code</option>
									<option value="E/M Code"  <?php print ($policyDetail["elem_billingCode"]=="E/M Code") ? "selected" : "" ;?> >E/M Code</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="billing_amount">Billing Amount</label>
								<select name="billing_amount" id="billing_amount" class="form-control minimal">
									<option value="">--Select--</option>
									<option value="Default"  <?php print ($policyDetail["billing_amount"]=="Default") ? "selected" : "" ;?> >Default</option>
									<option value="Per Contract Price"  <?php print ($policyDetail["billing_amount"]=="Per Contract Price") ? "selected" : "" ;?> >Per Contract Price</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Accept Assignment</label>
								<input type="hidden" name="accept_assignment_old" id="accept_assignment_old" value="<?php echo $policyDetail["accept_assignment"]; ?>">
								<input type="hidden" name="accept_assignment_reason" id="accept_assignment_reason" value="<?php echo $policyDetail["accept_assignment_reason"]; ?>">
								<select id="accept_assignment" name="accept_assignment" class="form-control minimal" onChange="set_ae('');">
									<option value="0" <?php if($policyDetail["accept_assignment"]==0){echo "selected";} ?>>Accept Assignment</option>
									<option value="1" <?php if($policyDetail["accept_assignment"]==1){echo "selected";} ?>>NAA - Courtesy Billing</option>
									<option value="2" <?php if($policyDetail["accept_assignment"]==2){echo "selected";} ?>>NAA - No Courtesy Billing</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="auto_pt_balance">Auto Adjust Pt Bal</label>
								<select name="auto_pt_balance" id="auto_pt_balance" class="form-control minimal">
									<option value="">--Select--</option>
									<option value="0"  <?php print ($policyDetail["auto_pt_balance"]=="0") ? "selected" : "" ;?> >Default</option>
									<option value="1"  <?php print ($policyDetail["auto_pt_balance"]=="1") ? "selected" : "" ;?> >Contract Price</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="write_off_code">Write Off Code</label>
								<select name="write_off_code"  id="write_off_code" class="form-control minimal">
									<option value="">--Select--</option>
									<?php
									$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
									while($sel_write=imw_fetch_array($sel_rec)){
									?>
										<option value="<?php echo $sel_write['w_id'];?>" <?php if($policyDetail["write_off_code"]==$sel_write['w_id']){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="return_chk_proc">Returned Check Procedure</label>
								<input class="form-control" name="return_chk_proc" id="return_chk_proc" data-provide="multiple" data-seperator="semicolon" value="<?php echo $policyDetail["return_chk_proc"]; ?>" >
							</div>
						</div>
					</div>
					<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="return_chk_amt">Returned Check Amount	</label>
									<input type="text" class="form-control" name="return_chk_amt" id="return_chk_amt" value="<?php echo $policyDetail["return_chk_amt"]; ?>" >
								</div>
							</div>
							<div class="col-sm-6">
							<div class="form-group">
								<label for="in_batch_processing">In Batch Processing</label>
								<select name="in_batch_processing"  id="in_batch_processing" class="form-control minimal">
									<option value="0">Payment Date</option>
									<option value="1" <?php print ($policyDetail["in_batch_processing"]=="1") ? "selected" : "" ;?>>Batch Date</option>
								</select>   
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-5">
							<div class="form-group">
							<label for="discount_method">Self Pay – Discount</label>
							<select name="discount_method" id="discount_method" class="form-control minimal">
								<option value="">--Select--</option>
								<option value="All" <?php print ($policyDetail["discount_method"]=="All") ? "selected" : "" ;?> >All</option>
								<option value="Cash" <?php print ($policyDetail["discount_method"]=="Cash") ? "selected" : "" ;?> >Cash</option>
								<option value="Check" <?php print ($policyDetail["discount_method"]=="Check") ? "selected" : "" ;?> >Check</option>
								<option value="CC" <?php print ($policyDetail["discount_method"]=="CC") ? "selected" : "" ;?> >CC</option>
							</select>
							</div>
						</div>
						<div class="col-sm-3">
							<label for="discount_amount">Discount</label>
							<?php
									if($policyDetail["discount_amount"]) {
										if(substr($policyDetail["discount_amount"], -1,1) != "%"){
												$discount = $policyDetail["discount_amount"];
										}else{
												$discount = $policyDetail["discount_amount"];
										}
									}  
								?>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
								<input type="text" name="discount_amount" id="discount_amount" value="<?php echo $discount; ?>" class="form-control" onchange=autocomplete="off">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="discount_code">Discount Code</label>
								<select name="discount_code"  id="discount_code" class="form-control minimal">
									<option value="">--Select--</option>
									<?php
									$sel_rec=imw_query("select d_id,d_code from discount_code");
									while($sel_write=imw_fetch_array($sel_rec)){
									?>
										<option value="<?php echo $sel_write['d_id'];?>" <?php if($policyDetail["discount_code"]==$sel_write['d_id']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
									<?php } ?>
								</select>  
							</div>
						</div>
					</div>	
				</div>
			</div>
            <div class="grpbox"  style="min-height:107px;">
                <div class="head"><span>Batch Processing</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-6">
							<label for="batch_payment_method">Default Payment Method</label>
                            <select name="batch_payment_method" id="batch_payment_method" class="form-control minimal">
                            	<option value="">--Select--</option>
								<?php
                                $sel_rec=imw_query("select pm_id,pm_name,del_status from payment_methods order by pm_name");
                                while($sel_pm=imw_fetch_array($sel_rec)){
									if($sel_pm['del_status']==0 || $sel_pm['pm_id']==$policyDetail["batch_payment_method"]){
                               		$txt_color = ($sel_pm['del_status'])?'style="color:red;"':'';
							    ?>
                                    <option <?php echo $txt_color; ?> value="<?php echo $sel_pm['pm_id'];?>" <?php if($sel_pm['pm_id']==$policyDetail["batch_payment_method"]){ echo "selected";} ?>><?php echo $sel_pm['pm_name'];?></option>
                                <?php }} ?>
                            </select>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="grpbox" style="min-height:205px;">
                <div class="head"><span>Collection</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="copay_type">CoPay Type</label>
								<select name="copay_type" id="copay_type" class="form-control minimal">>
									<option value="0" <?php if($policyDetail["copay_type"]==0){echo "selected";} ?>>Practice</option>
									<option value="1" <?php if($policyDetail["copay_type"]==1){echo "selected";} ?>>Dilated/Non-Dilated</option>
									<option value="2" <?php if($policyDetail["copay_type"]==2){echo "selected";} ?>>Office/Test</option>
								</select>
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label for="">Collect Sec. CoPay</label>
								<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-3">
											<div class="radio radio-inline">
												<input type="radio" name="secondary_copay" id="secondary_copay_1" value="Yes" onClick="show_sec_ins(this.value);" <?php if($policyDetail["secondary_copay"] == 'Yes') print 'checked="checked"'; ?>>
											<label for="secondary_copay_1"> Yes </label>
											</div>
										</div>
										<?php $display='block'; if($policyDetail["secondary_copay"] == 'No'){ $display='none'; }?>
										<div class="col-sm-6" id="ins_base_sec_copay" style="display:<?php echo $display;?>;">
											<select name="sec_copay_for_ins" id="sec_copay_for_ins" class="form-control minimal" onChange="show_sec_amt(this.value);">
												<option value="" <?php if($policyDetail["sec_copay_for_ins"]==""){echo "selected";} ?>>All Ins. Carrier</option>
												<option value="Medicare as Primary" <?php if($policyDetail["sec_copay_for_ins"]=="Medicare as Primary"){echo "selected";} ?>>Medicare as Primary</option>
											</select>
										</div>
										<div class="col-sm-3">
											<div class="radio radio-inline">
												<input type="radio" name="secondary_copay"  id="secondary_copay_2" value="No" onClick="show_sec_ins(this.value);" <?php if($policyDetail["secondary_copay"] == 'No') print 'checked="checked"'; ?>>
												<label for="secondary_copay_2"> No </label>
											</div>
										</div>
									</div>	
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
							<label for="">Collect Ter. CoPay</label>
							<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="tertiary_copay" id="tertiary_copay_1" value="Yes" <?php if($policyDetail["tertiary_copay"] == 'Yes') print 'checked="checked"'; ?>>
									<label for="tertiary_copay_1"> Yes </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="tertiary_copay" id="tertiary_copay_2" value="No" <?php if($policyDetail["tertiary_copay"] == 'No') print 'checked="checked"'; ?>>
									<label for="tertiary_copay_2"> No </label>
								</div>
							</div>
						</div>
						<div class="col-sm-8">
							<div class="checkbox checkbox-inline">
								<?php $selectedCHK = ($policyDetail["no_balance_bill"]=="1") ? "checked" : "" ;?>
								<input type="checkbox" name="no_balance_bill" id="no_balance_bill" <?php print $selectedCHK;?> value="1">
								<label for="no_balance_bill"> No Balance Bill </label>
							</div>
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="refractionGiveOnly" id="refractionGiveOnly" value="Yes" <?php if($policyDetail["refractionGiveOnly"] == 'Yes') print 'checked="checked"'; ?>>
								<label for="refractionGiveOnly"> MR - Given Only </label>&nbsp;&nbsp;
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="">Collect Refraction</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="refraction" id="refraction_1" value="Yes" <?php if($policyDetail["refraction"] == 'Yes') print 'checked="checked"'; ?>>
									<label for="refraction_1"> Yes </label>&nbsp;&nbsp;
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="refraction" id="refraction_2" value="No" <?php if($policyDetail["refraction"] == 'No') print 'checked="checked"'; ?>>
									<label for="refraction_2"> No </label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="grpbox"  style="min-height:164px;">
                <div class="head"><span>RVU</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label for="work_gpci">Work GPCI</label>
								<input class="form-control" name="work_gpci" id="work_gpci" value="<?php print $policyDetail["work_gpci"]; ?>">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="practice_expense_gpci">PE GPCI</label>
								<input class="form-control" name="practice_expense_gpci" id="practice_expense_gpci" value="<?php print $policyDetail["practice_expense_gpci"]; ?>">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="malpractice_gpci">MP GPCI</label>
								<input type="" class="form-control" name="malpractice_gpci" id="malpractice_gpci" value="<?php print $policyDetail["malpractice_gpci"]; ?>">
							</div>
						</div><br />
						<div class="col-sm-4">
							<div class="form-group">
								<label for="bugdet_neu_adj_gpci">BNA</label>
								<input type="" class="form-control"  name="bugdet_neu_adj_gpci" id="bugdet_neu_adj_gpci" value="<?php print $policyDetail["bugdet_neu_adj_gpci"]; ?>">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="conversion_factor">Conv. Factor</label>
								<input type="" class="form-control" name="conversion_factor" id="conversion_factor" value="<?php print $policyDetail["conversion_factor"]; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="grpbox" style="min-height:247px;">
                <div class="head"><span>Facilities</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="">A/R Cycle</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="elem_arCycle" id="elem_arCycle_1" value="30" <?php if($policyDetail["elem_arCycle"] == '30') print 'checked="checked"'; ?>>
									<label for="elem_arCycle_1"> 30 Days </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="elem_arCycle" id="elem_arCycle_2" value="45" <?php if($policyDetail["elem_arCycle"] == '45') print 'checked="checked"'; ?>>
									<label for="elem_arCycle_2"> 45 Days </label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="icd_code">ICD Code</label>
								<select name="icd_code"  id="icd_code" class="form-control minimal">			
									<option value="ICD-9" <?php if($policyDetail["icd_code"]=="ICD-9") { echo "selected" ;} ?> >ICD-9</option>
									<option value="ICD-10" <?php if($policyDetail["icd_code"]=="ICD-10") { echo "selected" ;} ?> >ICD-10</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
							<label for="RTEValidDays">RTE Valid Days</label>
							<input class="form-control" name="RTEValidDays" id="RTEValidDays" onKeyPress="return isNumberKey(event)" value="<?php if($policyDetail["RTEValidDays"] > 0){ echo $policyDetail["RTEValidDays"]; }?>" >
							</div>
						</div>						
						<div class="col-sm-6">
						<div class="form-group">
							<label for="anes_time_divisor">Anes Time Divisor</label>
							<input class="form-control" id="anes_time_divisor" name="anes_time_divisor" value="<?php print $policyDetail["anes_time_divisor"]; ?>" />
						  </div>
						</div>

						<div class="col-sm-6">
						<label>Docs</label>
							<div class="checkbox">
								<input type="checkbox" name="collapse_docs_default" id="collapse_docs_default" value="1" <?php if($policyDetail["collapse_docs_default"] == '1') print 'checked="checked"'; ?>>
								<label for="collapse_docs_default"> Collapse Docs Tab Default</label>&nbsp;&nbsp;
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-6">
							<label>PVC</label>
							<div class="checkbox">
								<input type="checkbox" name="patient_verbal_communication" id="patient_verbal_communication" value="1" <?php if($policyDetail["patient_verbal_communication"] == '1') print 'checked="checked"'; ?>>
								<label for="patient_verbal_communication"> Patient Verbal Communication</label>&nbsp;&nbsp;
							</div>
						</div>
						
						<div class="col-sm-6 rvu">
							<label>Super Bill</label>
							<div class="form-group">
							<div class="checkbox">
								<input type="checkbox" name="visitcode_warn" id="visitcode_warn" value="1" <?php if($policyDetail["visitcode_warn"] == '1') print 'checked="checked"'; ?>>
								<label for="visitcode_warn"> Visit code warning </label>&nbsp;&nbsp;
							</div>
							</div>
							
							<div class="form-group">
							<div class="checkbox">
								<input type="checkbox" name="del_proc_noti" id="del_proc_noti" value="1" <?php if($policyDetail["del_proc_noti"] == '1') print 'checked="checked"'; ?>>
								<label for="del_proc_noti"> Delete procedure notification </label>&nbsp;&nbsp;
							</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="grpbox"  style="min-height:107px;">
                <div class="head"><span>VIP</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-6">
									<?php $selectedCHK = ($policyDetail["vip_copay_not_collect"]=="1") ? "checked" : "" ;?>
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="vip_copay_not_collect" id="vip_copay_not_collect" <?php print $selectedCHK;?> value="1">
										<label for="vip_copay_not_collect">  Do Not Collect Copay </label>
									</div>
								</div>
								<div class="col-sm-6">
									<?php $selectedCHK = ($policyDetail["vip_ref_not_collect"]=="1") ? "checked" : "" ;?>
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="vip_ref_not_collect" id="vip_ref_not_collect" <?php print $selectedCHK;?> value="1">
										<label for="vip_ref_not_collect"> Do Not Collect Refraction</label>
									</div>
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-6">
									<?php $selectedCHK = ($policyDetail["vip_bill_not_pat"]=="1") ? "checked" : "" ;?>
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="vip_bill_not_pat" id="vip_bill_not_pat" <?php print $selectedCHK;?> value="1">
										<label for="vip_bill_not_pat"> Do Not Bill Pt </label>
									</div>
								</div>
								<div class="col-sm-6">
									<?php $hideDisp='none'; if($policyDetail["vip_bill_not_pat"]=="1"){ $hideDisp='block'; } ?>
									<select name="vip_write_off_code"  id="vip_write_off_code" class="form-control minimal"style="display:<?php echo $hideDisp;?>;">
										<option value="">Write Off Code</option>
										<?php
										$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
										while($sel_write=imw_fetch_array($sel_rec)){
										?>
											<option value="<?php echo $sel_write['w_id'];?>" <?php if($policyDetail["vip_write_off_code"]==$sel_write['w_id']){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
										<?php } ?>
									</select>
								</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="grpbox">
                <div class="head"><span>Statements</span></div>
				<div class="clearfix"></div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="Statement_Elapsed">Statement Cycle</label>
								<input type="" class="form-control" id="Statement_Elapsed" name="Statement_Elapsed" value="<?php print $policyDetail["Statement_Elapsed"]; ?>">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="min_balance_bill">Minimum balance amount</label>
								<input type="" class="form-control" id="min_balance_bill" name="min_balance_bill" value="<?php print $policyDetail["min_balance_bill"]; ?>">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Statements Based</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="statement_base" id="statement_base_1" value="0" <?php if($policyDetail["statement_base"] == 0) print 'checked="checked"'; ?>>
									<label for="statement_base_1"> Enc </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="statement_base" id="statement_base_2" value="1" <?php if($policyDetail["statement_base"] == 1) print 'checked="checked"'; ?>>
									<label for="statement_base_2"> Acc </label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Print Fully Paid Statements</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="fully_paid" id="fully_paid_1" value="1" <?php if($policyDetail["fully_paid"] == 1) print 'checked="checked"'; ?>>
									<label for="fully_paid_1"> Yes </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="fully_paid" id="fully_paid_2" value="0" <?php if($policyDetail["fully_paid"] == 0) print 'checked="checked"'; ?>>
									<label for="fully_paid_2"> No </label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Print Full Encounter</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="full_enc" id="full_enc_1" value="1" <?php if($policyDetail["full_enc"] == 1) print 'checked="checked"'; ?>>
									<label for="full_enc_1"> Yes </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="full_enc" id="full_enc_2" value="0" <?php if($policyDetail["full_enc"] == 0) print 'checked="checked"'; ?>>
									<label for="full_enc_2"> No </label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Consolidated Statement</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="statement_consolidate" id="statement_consolidate_1" value="0" <?php if($policyDetail["statement_consolidate"] == 0) print 'checked="checked"'; ?>>
									<label for="statement_consolidate_1"> Yes </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="statement_consolidate" id="statement_consolidate_2" value="1" <?php if($policyDetail["statement_consolidate"] == 1) print 'checked="checked"'; ?>>
									<label for="statement_consolidate_2"> No </label>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="min_pay_due_stm">Minimum Payment Due in Statement</label>
								<input class="form-control" id="min_pay_due_stm" name="min_pay_due_stm" value="<?php print $policyDetail["min_pay_due_stm"]; ?>" >
							</div>
						</div>
						<div class="col-sm-12 pt10" style="height:117px;">
						<div class="row">
								<div class="col-sm-12 pt10">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="statement_cycle" id="statement_cycle" <?php if($policyDetail["statement_cycle"]=='1'){ echo 'checked';}?> value="1" />
										<label for="statement_cycle">Pt Cycle</label>
									</div>
								</div>
								<div class="col-sm-12 pt10">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="ar_aging" id="ar_aging" value="1" <?php if($policyDetail["ar_aging"] == '1') print 'checked="checked"'; ?>>
										<label for="ar_aging"> Pt A/R Aging </label>
									</div><br /><br />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="head"><span>Clearing House Details</span></div>
	</div>
	<div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4">
          	<div class="grpbox" style="min-height:160px;">
              	<div class="head"><span>House Information</span></div>
                <div class="clearfix"></div>
                <div class="tblBg">
                  	<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
                            	<?php 
									$cl_mode = array();
									$cl_mode_sel="";
								?>
								<label for="house_name">Name</label>
								<select name="house_name"  id="house_name" class="form-control minimal">
									<option value="">--Select--</option>
									<?php
									$sel_ch=imw_query("select * from clearing_houses");
									while($sel_ch_row=imw_fetch_array($sel_ch)){
										$cl_mode[$sel_ch_row['id']] = $sel_ch_row['connect_mode'];
										if($sel_ch_row["status"]==1){
											$cl_mode_sel=$sel_ch_row['connect_mode'];
										}
									?>
										<option value="<?php echo $sel_ch_row['id'];?>" <?php if($sel_ch_row["status"]==1){ echo "selected";} ?>><?php echo $sel_ch_row['house_name'];?></option>
									<?php } ?>
								</select>
								<input type="hidden" class="form-control" name="Name" id="Name" value="<?php print $policyDetail["Name"];?>" />
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="">Mode</label>
								<div class="clearfix"></div>
								<div class="radio radio-inline">
									<input type="radio" name="emdeon_test_pro" id="emdeon_test_pro_1" value="0" <?php if($cl_mode_sel == 'T' || $cl_mode_sel == '') print 'checked'; ?> >
									<label for="emdeon_test_pro_1"> Test </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" name="emdeon_test_pro" id="emdeon_test_pro_2" value="1" <?php if($cl_mode_sel == 'P') print 'checked'; ?>>
									<label for="emdeon_test_pro_2"> Production </label>
								</div>
							</div>
						</div>
					</div>
            	</div>
           	</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
          	<div class="grpbox" style="min-height:160px;">
              	<div class="head"><span>Contacts</span></div>
                <div class="clearfix"></div>
                <div class="tblBg">
                  	<div class="row">
						<div class="col-sm-12">
					<div class="form-group">
						<label for="">Contact Name</label>
						<input type="" class="form-control" name="Contact_Name" id="Contact_Name" value="<?php print $policyDetail["Contact_Name"];?>" />
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label for="Telephone">Telephone</label>
						<input class="form-control" name="Telephone" id="Telephone"  onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');" value="<?php print core_phone_format($policyDetail["Telephone"]);?>" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="">&nbsp;</label>
						<input type="" class="form-control" name="phone_ext" id="phone_ext"   size="6" value="<?php if($policyDetail["phone_ext"] >0)print $policyDetail["phone_ext"];?>" />
					</div>
				</div>
					</div>
            	</div>
           	</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
          	<div class="grpbox" style="min-height:160px;">
              	<div class="head"><span>Address</span></div>
                <div class="clearfix"></div>
                <div class="tblBg">
                  	<div class="row">
						<div class="col-sm-6">
					<div class="form-group">
						<label for="Address1_1">Street 1</label>
						<input class="form-control" name="Address1" id="Address1_1" value="<?php print $policyDetail["Address1"];?>" />
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
					<label for="Address1_2">Street 2</label>
					<input class="form-control" name="Address2" id="Address1_2" value="<?php print $policyDetail["Address2"];?>" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="Zip"><?php getZipPostalLabel(); ?></label>
						<div class="row">
							<div class="col-sm-6">
								<input type="text" class="form-control"  name="Zip" id="Zip" onBlur="zip_vs_state(this.value,'policy');" value="<?php if($policyDetail["Zip"]) print $policyDetail["Zip"];?>" />
							</div>
							<div class="col-sm-6">
								<?php if(inter_zip_ext()){?>
								<input type="text" class="form-control" name="zip_ext" id="zip_ext" value="<?php if($policyDetail["zip_ext"]) print $policyDetail["zip_ext"];?>" />
								<?php }?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="City">City</label>
						<input class="form-control"  name="City" id="City" value="<?php print $policyDetail["City"];?>" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="">State</label>
						<input type="text" class="form-control" name="State" id="State" value="<?php print $policyDetail["State"];?>" />
					</div>
				</div>
					</div>
            	</div>
           	</div>
		</div>
	</div>
	<div class="row">
		<div class="head"><span>Visit Payment</span></div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label for="">Visit Payment at Check-out</label>
				<div class="clearfix"></div>
				<div class="row ">
					<div class="col-sm-6">
						<div class="checkbox checkbox-inline">
							<input onClick="sel_cpt_print_fun(this,'txt_cpt_print');" type="checkbox" id="txt_no_details" name="txt_no_details" value="no" <?php if($policyDetail["cpt_print_details"] == 'no') print 'checked'; ?>>
							<label for="txt_no_details"> Encounter </label>
						</div>
						<div class="checkbox checkbox-inline" style="display:none;">
							<input onClick="sel_cpt_print_fun(this,'txt_no_details');" type="checkbox" name="txt_cpt_print" id="txt_cpt_print" value="yes" <?php if($policyDetail["cpt_print_details"] == 'yes') print 'checked'; ?>>
							<label for="txt_cpt_print"> Practice specific </label>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="ci_demographics" id="ci_demographics" style="cursor:pointer;" <?php if($policyDetail["ci_demographics"] == 1) print 'checked'; ?>>
							<label for="ci_demographics"> CI-Demographics </label>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group pt10">
				<label for="">Check In/Out Show</label>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-sm-6">
						<div class="checkbox checkbox-inline">
							<input onClick="sel_check_in_out_fun(this,'show_check_out');" type="checkbox" id="show_check_in" name="show_check_in" value="1" <?php if($policyDetail["show_check_in"] == '1') print 'checked'; ?>>
							<label for="show_check_in"> Check In </label>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="checkbox checkbox-inline">
							<input onClick="sel_check_in_out_fun(this,'show_check_in');" type="checkbox" name="show_check_out" id="show_check_out" value="1" style="cursor:pointer;" <?php if($policyDetail["show_check_out"] == '1') print 'checked'; ?>>
							<label for="show_check_out"> Check Out </label>
						</div>
					</div>

					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="checkin_on_done" id="checkin_on_done" value="1" style="cursor:pointer;" <?php if($policyDetail["checkin_on_done"] == '1') print 'checked="checked"'; ?>>
							<label for="checkin_on_done">Check In on Done</label>&nbsp;&nbsp;
						</div>
					</div>

				</div>
			</div>
		<div class="clearfix"></div>
		</div>
		<?php 
			$sel_item=imw_query("select * from check_in_out_fields where status='0' order by id");
			while($fet_item=imw_fetch_array($sel_item)){
				$fet_id_arr[]=$fet_item['id'];
				$fet_name_arr[]=$fet_item['item_name'];
				$fet_name_show_arr[]=$fet_item['item_show'];
			}
		?>
		<div class="col-sm-9">
			<div class="form-group">
				<label for="">Check In/Out Fields</label>
				<div class="clearfix"></div>
				<div class="row chkout">
					<div class="col-sm-3">
					<?php 
						for($t=0;$t<3;$t++){ 
						$ci_co_readonly="";
						$ci_co_readonly_css="";
						if(strtolower($fet_name_arr[$t])=="copay-visit" || strtolower($fet_name_arr[$t])=="refraction" || strtolower($fet_name_arr[$t])=="pt balance" ){
							$ci_co_readonly="readonly";
							$ci_co_readonly_css="style='background-color:#CCC;'";
						}
						$pt10 = ''; if ($t != 0){$pt10 = 'pt10';} 
					?>
					<div class="row <?php echo $pt10; ?>" id="tblitem_<?php echo $t; ?>">
						<div class="col-sm-1 text-center">
							 <input type="hidden" name="item_id[]" id="item_id_<?php echo $t; ?>" value="<?php echo $fet_id_arr[$t]; ?>">
							 <div class="checkbox">
								<input type="checkbox" name="item_show[<?php echo $fet_id_arr[$t]; ?>]" id="item_show_<?php echo $t; ?>" value="1" <?php if($fet_name_show_arr[$t]== '1') print 'checked'; ?>>
								<label for="item_show_<?php echo $t; ?>"></label>
							</div>
						</div>
						<div class="col-sm-11">
							<input type="text" class="form-control" <?php echo $ci_co_readonly_css; echo $ci_co_readonly; ?>  id="item_name_<?php echo $t; ?>" name="item_name[]" value="<?php echo $fet_name_arr[$t]; ?>">
						</div> 
					</div><?php } ?>
					</div>
					<div class="col-sm-3">
					<?php 
					for($t=3;$t<6;$t++){
						$ci_co_readonly="";
						$ci_co_readonly_css="";
						if(strtolower($fet_name_arr[$t])=="copay-visit" || strtolower($fet_name_arr[$t])=="refraction" || strtolower($fet_name_arr[$t])=="pt balance" ){
							$ci_co_readonly="readonly";
							$ci_co_readonly_css="style='background-color:#CCC;'";
						}
					$pt10 = ''; if ($t != 3){$pt10 = 'pt10';} 
					?>
					<div class="row <?php echo $pt10; ?>" id="tblitem_<?php echo $t; ?>">
						<div class="col-sm-1 text-center">
							 <input type="hidden" name="item_id[]" id="item_id_<?php echo $t;?>" value="<?php echo $fet_id_arr[$t]; ?>">
							 <div class="checkbox">
								<input type="checkbox" id="item_show_<?php echo $t;?>" name="item_show[<?php echo $fet_id_arr[$t]; ?>]" value="1" <?php if($fet_name_show_arr[$t]== '1') print 'checked'; ?>>
								<label for="item_show_<?php echo $t; ?>"></label>
							</div>
							 
						</div>
						<div class="col-sm-11">
							<input type="text" class="form-control" <?php echo $ci_co_readonly_css; echo $ci_co_readonly; ?> id="item_name_<?php echo $t;?>" name="item_name[]" value="<?php echo $fet_name_arr[$t]; ?>">
						</div> 
					</div><?php } ?>
					</div>
					<div class="col-sm-3">
					<?php 
					for($t=6;$t<9;$t++){
						$ci_co_readonly="";
						$ci_co_readonly_css="";
						if(strtolower($fet_name_arr[$t])=="copay-visit" || strtolower($fet_name_arr[$t])=="refraction" || strtolower($fet_name_arr[$t])=="pt balance" ){
							$ci_co_readonly="readonly";
							$ci_co_readonly_css="style='background-color:#CCC;'";
						}
					$pt10 = ''; if ($t != 6){$pt10 = 'pt10';} 
					?>
					<div class="row <?php echo $pt10; ?>" id="tblitem_<?php echo $t; ?>">
						<div class="col-sm-1 text-center">
							 <input type="hidden" name="item_id[]" id="item_id_<?php echo $t;?>" value="<?php echo $fet_id_arr[$t]; ?>">
							 <div class="checkbox">
								<input type="checkbox" id="item_show_<?php echo $t;?>" name="item_show[<?php echo $fet_id_arr[$t]; ?>]" value="1" <?php if($fet_name_show_arr[$t]== '1') print 'checked'; ?>>
								<label for="item_show_<?php echo $t; ?>"></label>
							</div>
						</div>
						<div class="col-sm-11">
							<input type="text" class="form-control" <?php echo $ci_co_readonly_css; echo $ci_co_readonly; ?> id="item_name_<?php echo $t;?>" name="item_name[]" value="<?php echo $fet_name_arr[$t]; ?>">
						</div> 
					</div><?php } ?>
					</div>
					<div class="col-sm-3">
					<?php 
					for($t=9;$t<12;$t++){
						$ci_co_readonly="";
						$ci_co_readonly_css="";
						if(strtolower($fet_name_arr[$t])=="copay-visit" || strtolower($fet_name_arr[$t])=="refraction" || strtolower($fet_name_arr[$t])=="pt balance" ){
							$ci_co_readonly="readonly";
							$ci_co_readonly_css="style='background-color:#CCC;'";
						}
					$pt10 = ''; if ($t != 9){$pt10 = 'pt10';} 
					?>
					<div class="row <?php echo $pt10; ?>" id="tblitem_<?php echo $t; ?>">
						<div class="col-sm-1 text-center">
							 <input type="hidden" name="item_id[]" id="item_id_<?php echo $t;?>" value="<?php echo $fet_id_arr[$t]; ?>">
							 <div class="checkbox">
								<input type="checkbox" id="item_show_<?php echo $t;?>" name="item_show[<?php echo $fet_id_arr[$t]; ?>]" value="1" <?php if($fet_name_show_arr[$t]== '1') print 'checked'; ?>>
								<label for="item_show_<?php echo $t; ?>"></label>
							</div>
						</div>
						<div class="col-sm-11">
							<input type="text" class="form-control" <?php echo $ci_co_readonly_css; echo $ci_co_readonly; ?> id="item_name_<?php echo $t;?>" name="item_name[]" value="<?php echo $fet_name_arr[$t]; ?>">
						</div> 
					</div><?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<?php 
	//include($GLOBALS['webroot']."/interface/reports/report_closed_day.php");
?>
<script type="text/javascript">	
	var loder_icon = '<div id="div_loading_image" class="text-center"><div class="loading_container"><div class="process_loader"></div><div id="div_loading_text" class="text-info"></div></div></div>';
	function report_daily_closed(){
		var content_str = '<div class="row"><div class="col-sm-12 alert alert-info text-center"><span></span></div><div class="col-sm-12">'+loder_icon+'</div></div><iframe name="iframe_ccda" id="iframe_reports_daily" frameborder="0" width="100%" height="0"></iframe>';
		var footer_cont ='<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="top.fmain.window.location.reload(true);">Close</button>';
		show_modal('report_create_div','Update Report Data',content_str,footer_cont,'600','modal_70');
	}
	$('body').on('show.bs.modal','#report_create_div',function(){
		$('#iframe_reports_daily').attr('src','<?php echo $GLOBALS['webroot']; ?>/interface/reports/report_closed_day.php');
	});
	
	var btnArr = new Array();
	btnArr[0] = new Array("save_policies","Save","top.fmain.document.getElementById('txtsave').value = 'save'; top.fmain.policyCheck();");
	btnArr[1] = new Array("report_daily_closed","Day Close Report","top.fmain.report_daily_closed();");
	top.btn_show("ADMN",btnArr);
	set_header_title('Policies');
	show_loading_image('none');
	sel_cpt_print_fun(document.getElementById('txt_no_details'),'txt_cpt_print');
	var obj7 = $('#return_chk_proc').typeahead({source:customarrayProcedure});
	$('[data-toggle="tooltip"]').tooltip();
	
	var CL_connect_mode_arr = JSON.parse('<?php echo json_encode($cl_mode);?>');
	$('#house_name').change(function(){
		cl_move_val = CL_connect_mode_arr[$(this).val()];
		if(cl_move_val=='T'){
			$("#emdeon_test_pro_1").prop("checked",true);
		}else if(cl_move_val=='P'){
			$("#emdeon_test_pro_2").prop("checked",true);
		}
	});
</script>
<?php 
	require_once("../../admin_footer.php");
?>