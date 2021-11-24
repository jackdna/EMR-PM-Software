<?php
	//Get vendor details adn also typeahead
	$vendor_details = $optical_obj->get_vendor_details();
	$order_id = $_REQUEST['order_id'];
	$patient_id = $optical_obj->patient_id;
	
	if(!$order_id){
		$Mrvals = $optical_obj->chartVisionVals($patient_id);
		$rowVal = imw_fetch_array($Mrvals);
		if($rowVal!=false){
			$sphere_od = $rowVal['sph_r'];
			$sphere_os = $rowVal['sph_l'];
			$cyl_od = $rowVal['cyl_r'];
			$cyl_os = $rowVal['cyl_l'];
			$axis_od = $rowVal['axs_r'];
			$axis_os = $rowVal['axs_l'];
			$add_od = $rowVal['ad_r'];
			$add_os = $rowVal['ad_l'];
			
			$elem_visMrOdP = $rowVal["prsm_p_r"]; 
			$elem_visMrOdSel1 = $rowVal["sel_1_r"];
			$elem_visMrOdSlash = $rowVal["slash_r"]; 
			$elem_visMrOdPrism =  $rowVal["prism_r"]; 
			
			$elem_visMrOsP = $rowVal["prsm_p_l"]; 
			$elem_visMrOsSel1 = $rowVal["sel_1_l"]; 
			$elem_visMrOsSlash = $rowVal["slash_l"]; 
			$elem_visMrOsPrism = $rowVal["prism_l"];
			
		}else{
			$resMRVal1 = $optical_obj->chartVisionValsNotGiven($patient_id);
			$rowMRval = imw_fetch_array($resMRVal1);
			$sphere_od = $rowMRval['vis_mr_od_given_s'];
			$sphere_os = $rowMRval['vis_mr_os_given_s'];
			$cyl_od = $rowMRval['vis_mr_od_given_c'];
			$cyl_os = $rowMRval['vis_mr_os_given_c'];
			$axis_od = $rowMRval['vis_mr_od_given_a'];
			$axis_os = $rowMRval['vis_mr_os_given_a'];
			$add_od = $rowMRval['vis_mr_od_given_add'];
			$add_os = $rowMRval['vis_mr_os_given_add'];
		}
		
		
		
		/*
		$arrMRGiven=array();
		if(empty($rowVal['vis_mr_none_given'])===false){
			$arrMRGiven=explode(',',$rowVal['vis_mr_none_given']);
		}
		
		if(in_array('MR 1', $arrMRGiven)){
			$resMRVal = $optical_obj->getVisionVal($patient_id);
			$rowMRval = imw_fetch_array($resMRVal);
			
			$sphere_od = $rowMRval['vis_mr_od_s'];
			$sphere_os = $rowMRval['vis_mr_os_s'];
			$cyl_od = $rowMRval['vis_mr_od_c'];
			$cyl_os = $rowMRval['vis_mr_os_c'];
			$axis_od = $rowMRval['vis_mr_od_a'];
			$axis_os = $rowMRval['vis_mr_os_a'];
			$add_od = $rowMRval['vis_mr_od_add'];
			$add_os = $rowMRval['vis_mr_os_add'];
		}
		else if(in_array('MR 2', $arrMRGiven)){
			$resMRVal = $optical_obj->getVisionVal2($patient_id);
			$rowMRval = imw_fetch_array($resMRVal);
			$sphere_od = $rowMRval['vis_mr_od_given_s'];
			$sphere_os = $rowMRval['vis_mr_os_given_s'];
			$cyl_od = $rowMRval['vis_mr_od_given_c'];
			$cyl_os = $rowMRval['vis_mr_os_given_c'];
			$axis_od = $rowMRval['vis_mr_od_given_a'];
			$axis_os = $rowMRval['vis_mr_os_given_a'];
			$add_od = $rowMRval['vis_mr_od_given_add'];
			$add_os = $rowMRval['vis_mr_os_given_add'];
		}
		else if(in_array('MR 3', $arrMRGiven)){
			$resMRVal = $optical_obj->getVisionVal3($patient_id);
			$rowMRval = imw_fetch_array($resMRVal);
			$sphere_od = $rowMRval['visMrOtherOdS_3'];
			$sphere_os = $rowMRval['visMrOtherOsS_3'];
			$cyl_od = $rowMRval['visMrOtherOdC_3'];
			$cyl_os = $rowMRval['visMrOtherOsC_3'];
			$axis_od = $rowMRval['visMrOtherOdA_3'];
			$axis_os = $rowMRval['visMrOtherOsA_3'];
			$add_od = $rowMRval['visMrOtherOdAdd_3'];
			$add_os = $rowMRval['visMrOtherOsAdd_3'];
		}
		else if(in_array('None',$arrMRGiven) || sizeof($arrMRGiven)<=0){
			$resMRVal1 = $optical_obj->getVisionVal2($patient_id);
			$rowMRval1 = imw_fetch_array($resMRVal1);
			if(($rowMRval1['vis_mr_od_given_s'] != '' || $rowMRval1['vis_mr_od_given_c'] != '' || $rowMRval1['vis_mr_od_given_a'] != '' || $rowMRval1['vis_mr_od_given_add'] != '')
			 || ($rowMRval1['vis_mr_os_given_s'] != '' || $rowMRval1['vis_mr_os_given_c'] != '' || $rowMRval1['vis_mr_os_given_a'] != '' || $rowMRval1['vis_mr_os_given_add'] != '')){
				$resMRVal = $optical_obj->getVisionVal2($patient_id);
				$rowMRval = imw_fetch_array($resMRVal);
				$sphere_od = $rowMRval['vis_mr_od_given_s'];
				$sphere_os = $rowMRval['vis_mr_os_given_s'];
				$cyl_od = $rowMRval['vis_mr_od_given_c'];
				$cyl_os = $rowMRval['vis_mr_os_given_c'];
				$axis_od = $rowMRval['vis_mr_od_given_a'];
				$axis_os = $rowMRval['vis_mr_os_given_a'];
				$add_od = $rowMRval['vis_mr_od_given_add'];
				$add_os = $rowMRval['vis_mr_os_given_add'];
			 }
			 else{
				$resMRVal = $optical_obj->getVisionVal($patient_id);
				$rowMRval = imw_fetch_array($resMRVal);
				$sphere_od = $rowMRval['vis_mr_od_s'];
				$sphere_os = $rowMRval['vis_mr_os_s'];
				$cyl_od = $rowMRval['vis_mr_od_c'];
				$cyl_os = $rowMRval['vis_mr_os_c'];
				$axis_od = $rowMRval['vis_mr_od_a'];
				$axis_os = $rowMRval['vis_mr_os_a'];
				$add_od = $rowMRval['vis_mr_od_add'];
				$add_os = $rowMRval['vis_mr_os_add'];
			}
		}
		*/
	}
	/*
	if(!$order_id){
		$resPrism = $optical_obj->chartVisionVals($patient_id);
		$rowPrism = imw_fetch_array($resPrism);
		$arrMRGiven=array();
		if(empty($rowPrism['vis_mr_none_given'])===false){
			$arrMRGiven=explode(',',$rowPrism['vis_mr_none_given']);
		}

		if(in_array('MR 1', $arrMRGiven)){
			$elem_visMrOdP = $rowPrism["vis_mr_od_p"]; 
			$elem_visMrOdSel1 = $rowPrism["vis_mr_od_sel_1"];
			$elem_visMrOdSlash = $rowPrism["vis_mr_od_slash"]; 
			$elem_visMrOdPrism =  $rowPrism["vis_mr_od_prism"]; 
			
			$elem_visMrOsP = $rowPrism["vis_mr_os_p"]; 
			$elem_visMrOsSel1 = $rowPrism["vis_mr_os_sel_1"]; 
			$elem_visMrOsSlash = $rowPrism["vis_mr_os_slash"]; 
			$elem_visMrOsPrism = $rowPrism["vis_mr_os_prism"]; 
		}
		else if(in_array('MR 2', $arrMRGiven)){
			$elem_visMrOdP = $rowPrism["vis_mr_od_p"]; 
			$elem_visMrOdSel1 = $rowPrism["vis_mr_od_sel_1"]; 
			$elem_visMrOdSlash = $rowPrism["vis_mr_od_slash"]; 
			$elem_visMrOdPrism = $rowPrism["vis_mr_od_prism"];
			
			$elem_visMrOsP = $rowPrism["vis_mr_os_p"]; 
			$elem_visMrOsSel1 = $rowPrism["vis_mr_os_sel_1"]; 
			$elem_visMrOsSlash = $rowPrism["vis_mr_os_slash"]; 
			$elem_visMrOsPrism = $rowPrism["vis_mr_os_prism"]; 
		}
		else if(in_array('MR 3', $arrMRGiven)){
			$elem_visMrOdP = $rowPrism["vis_mr_od_p"]; 
			$elem_visMrOdSel1 = $rowPrism["vis_mr_od_sel_1"]; 
			$elem_visMrOdSlash = $rowPrism["vis_mr_od_slash"]; 
			$elem_visMrOdPrism = $rowPrism["vis_mr_od_prism"]; 
			$elem_visMrOsP = $rowPrism["vis_mr_os_p"]; 
			$elem_visMrOsSel1 = $rowPrism["vis_mr_os_sel_1"];  
			$elem_visMrOsSlash = $rowPrism["vis_mr_os_slash"]; 
			$elem_visMrOsPrism = $rowPrism["vis_mr_os_prism"];
		}
	}
	*/
	
	if($_POST['txt_save_type'] == 'save'){
		$_POST['operator_id'] = $_SESSION['authId'];
		$_POST['frame_cost'] = str_replace($global_currency,'',$frame_cost);
		$_POST['lenese_cost'] = str_replace($global_currency,'',$lenese_cost);
		$_POST['adminPatientLenseCost'] = str_replace($global_currency,'',$adminPatientLenseCost);
		$_POST['tint_cost'] = str_replace($global_currency,'',$tint_cost);
		$_POST['scr_cost'] = str_replace($global_currency,'',$scr_cost);
		$_POST['ar_cost'] = str_replace($global_currency,'',$ar_cost);
		$_POST['other_cost'] = str_replace($global_currency,'',$other_cost);
		$_POST['total'] = str_replace($global_currency,'',$total);
		$_POST['deposit'] = str_replace($global_currency,'',$deposit);
		$_POST['balance'] = str_replace($global_currency,'',$balance);	
		$_POST['frame_scr_price'] = str_replace($global_currency,'',$frame_scr_price);
		$_POST['tini_solid_price'] = str_replace($global_currency,'',$tini_solid_price);
		$_POST['tini_gradient_price'] = str_replace($global_currency,'',$tini_gradient_price);
		$_POST['transition_price'] = str_replace($global_currency,'',$transition_price);
		$_POST['frame_ar_price'] = str_replace($global_currency,'',$frame_ar_price);
		$_POST['polar_cost'] = str_replace($global_currency,'',$polar_cost);
		$_POST['trans_cost'] = str_replace($global_currency,'',$trans_cost);
		$_POST['Slad_Off_cost'] = str_replace($global_currency,'',$Slad_Off_cost);
		$_POST['hi_cost_price'] = str_replace($global_currency,'',$hi_cost_price);
		$_POST['tint_cost_price'] = str_replace($global_currency,'',$tint_cost_price);
		$_POST['Photochromatic_cost'] = str_replace($global_currency,'',$Photochromatic_cost);
		$_POST['framePrice'] = str_replace($global_currency,'',$txtframePrice);
		$_POST['prism_cost'] = str_replace($global_currency,'',$prism_cost);
		$_POST['uv_cost'] = str_replace($global_currency,'',$uv_cost);
		
		$_POST['order_place_date'] = date('Y-m-d');
		if(!$txt_order_save_id)
			$order_id = $optical_obj->AddRecords1($_POST,'optical_order_form');
		else
			$order_id = $optical_obj->UpdateRecords1($txt_order_save_id,'Optical_Order_Form_id',$_POST,'optical_order_form');
		if($order_id){	
			$msg = 'Order successfully saved';
		}
		if($txtpostCharges == 'Save & Post'){
			require_once('optical_enter_charges.php');
		}
	}
	
	$frame_options='';
	//--- Get Order Information To Update The Order ------
	if($order_id){
		$qry = imw_query("select * from optical_order_form where Optical_Order_Form_id = '$order_id'");
		$qrderQryRes = imw_fetch_array($qry);
		$orderDetails = (object)$qrderQryRes;
		$sphere_od = $orderDetails->sphere_od;
		$sphere_os = $orderDetails->sphere_os;
		$cyl_od = $orderDetails->cyl_od;
		$cyl_os = $orderDetails->cyl_os;
		$axis_od = $orderDetails->axis_od;
		$axis_os = $orderDetails->axis_os;
		$add_od = $orderDetails->add_od;
		$add_os = $orderDetails->add_od;
		$encounter_id = $orderDetails->encounter_id;
		$order_status = $orderDetails->order_status;
		
		$res_prism = $optical_obj->opticPrismVal($order_id);
		$row_prism_val = @imw_fetch_array($res_prism);
		$elem_visMrOdP = $row_prism_val["mr_od_p"];
		$elem_visMrOdSel1 = $row_prism_val["mr_od_sel"]; 
		$elem_visMrOdSlash = $row_prism_val["mr_od_splash"]; 
		$elem_visMrOdPrism = $row_prism_val["mr_od_prism"]; 
		
		$elem_visMrOsP = $row_prism_val["mr_os_p"]; 
		$elem_visMrOsSel1 = $row_prism_val["mr_os_sel"];
		$elem_visMrOsSlash = $row_prism_val["mr_os_splash"]; 
		$elem_visMrOsPrism = $row_prism_val["mr_os_prism"]; 
	}
	
	//--- Get Tint Price From Lenses Process ----
	$qry = "select * from lens_process";
	$lensQryId = @imw_query($qry);
	while($lensQryRes = @imw_fetch_assoc($lensQryId)){
		$lensProcessDetails = (object)$lensQryRes;
	}
	
	$TINT_Solid = $lensProcessDetails->tint_solid_price > 0 ? $global_currency.number_format($lensProcessDetails->tint_solid_price,2): '';
	$TINT_Gradient = $lensProcessDetails->tint_Gradient_price > 0 ? $global_currency.number_format($lensProcessDetails->tint_Gradient_price,2): '';
	
	$qry = imw_query("select * from patient_data where id = $patient_id");
	$patientDetails = imw_fetch_array($qry);
	$qryRes = (object)$patientDetails;
	
	$patient_name = $qryRes->lname.', '.$qryRes->fname.' '.$qryRes->mname;
	$patient_name = trim($patient_name).' - '.$qryRes->id;
	$patient_address='';
	
	if(trim($qryRes->street) || $qryRes->street2) {
		$patient_address .= trim($qryRes->street).' '.trim($qryRes->street2);
		$patient_address = trim($patient_address);
		$patient_address .= ', ';
	}
	
	if(trim($qryRes->city)) {
		$patient_address .= trim($qryRes->city).', ';
	}
	$patient_address .= $qryRes->state.' '.$qryRes->postal_code;
	$patient_address = trim($patient_address);
	$phone_home = '';
	if($qryRes->phone_home){
		$phone = explode('-',$qryRes->phone_home);
		$phone_home = '('.$phone[0].') '.$phone[1].'-'.$phone[2];
	}
	$providerID = $qryRes->providerID;
	
	$qry = imw_query("select concat(lname,', ',fname) as name, mname,id from users where id = $providerID");
	$phyDetails = imw_fetch_array($qry);
	$phyQryRes = (object)$phyDetails;
	$physicianName = $phyQryRes->name.' '.$phyQryRes->mname;	
	
	if($orderDetails->order_confirm == ''){
		$orderDetails->order_confirm = 'Patient';
	}
	
	if($orderDetails->discount_frames == '0'){
		$orderDetails->discount_frames = '';
	}
	
	if($orderDetails->discount == '0'){
		$orderDetails->discount = '';
	}
	
	list($y,$m,$d) = explode('-',$orderDetails->Notification_comments);
	$frameOrder = $m.'-'.$d.'-'.$y;
	if($frameOrder == '00-00-0000' || $frameOrder == '--')
	{	
		$frameOrder = '';
	}
	list($y,$m,$d) = explode('-',$orderDetails->lens_order);
	$lensOrder = $m.'-'.$d.'-'.$y;
	if($lensOrder == '00-00-0000' || $lensOrder == '--')
	{	
		$lensOrder = '';
	}
	list($y,$m,$d) = explode('-',$orderDetails->frame_recieve);
	$frameRecieve = $m.'-'.$d.'-'.$y;
	if($frameRecieve == '00-00-0000' || $frameRecieve == '--')
	{	
		$frameRecieve = '';
	}			
	list($y,$m,$d) = explode('-',$orderDetails->lens_recieve);
	$lensRecieve = $m.'-'.$d.'-'.$y;
	if($lensRecieve == '00-00-0000' || $lensRecieve == '--')
	{	
		$lensRecieve = '';
	}			
	list($y,$m,$d) = explode('-',$orderDetails->patient_notify);
	$notify = $m.'-'.$d.'-'.$y;
	if($notify == '00-00-0000' || $notify == '--')
	{	
		$notify = '';
	}			
	list($y,$m,$d) = explode('-',$orderDetails->patient_picked_up);
	$picked_up = $m.'-'.$d.'-'.$y;
	if($picked_up == '00-00-0000' || $picked_up == '--')
	{	
		$picked_up = '';
	}			
	list($y,$m,$d) = explode('-',$orderDetails->sale_date);
	$date_of_sale = $m.'-'.$d.'-'.$y;
	if($date_of_sale == '00-00-0000' || $date_of_sale == '--')
	{	
		$date_of_sale = '';
	}
?>
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/optical.css"/>
<style>
	.optical_content_div .costbox .form-control{height:inherit!important}
</style>
<script src="<?php echo $library_path; ?>/js/optical_order_form.js"></script>
<div class="row">
	<form action="index.php?showpage=optical_order_form&form_save=yes" name="order_form" id="order_form" method="post" >
		<?php include('previous_val.php');  ?>
		<input type="hidden" name="txt_save_type" id="txt_save_type" value="save">
		<input type="hidden" name="lenseTypeHide" id="lenseTypeHide" value="">
		<input type="hidden" name="frameTypeHide" id="frameTypeHide" value="">
		<input type="hidden" name="encounter_id" id="encounter_id" value="<?php print $encounter_id; ?>" >
		<input type="hidden" name="patient_id" id="patient_id" value="<?php print $patient_id; ?>" >
		<input type="hidden" name="txt_order_save_id" id="txt_order_save_id" value="<?php print $order_id; ?>" >
		<input type="hidden" name="physician_id" id="physician_id" value="<?php print $phyQryRes->id; ?>">
		<input type="hidden" name="frame_scr_price" id="frame_scr_price" value="<?php print $global_currency.number_format($orderDetails->frame_scr_price,2); ?>" >
		<input type="hidden" name="tini_solid_price" id="tini_solid_price" value="<?php print $global_currency.number_format($orderDetails->tini_solid_price,2); ?>">
		<input type="hidden" name="tini_gradient_price" id="tini_gradient_price" value="<?php print $global_currency.number_format($orderDetails->tini_gradient_price,2); ?>">
		<input type="hidden" name="transition_price" id="transition_price" value="<?php print $global_currency.number_format($orderDetails->transition_price,2); ?>">
		<input type="hidden" name="frame_ar_price" id="frame_ar_price" value="<?php print $global_currency.number_format($orderDetails->frame_ar_price,2); ?>">
		<input type="hidden" name="dis_actual_per" id="dis_actual_per" value="<?php echo $orderDetails->dis_actual_per ; ?>" />
		<input type="hidden" name="frameCostVal" id="frameCostVal" value="<?php echo $orderDetails->frameCostVal ; ?>" />
		<input type="hidden" name="saleOperatorVal" id="saleOperatorVal" value="<?php echo $orderDetails->saleOperatorVal ;?>" />
		<input type="hidden" name="txt_access_admin" id="txt_access_admin" value="<?php echo $optical_obj->accessAdmin("hahaha!");  ?>" />
		<input type="hidden" name="frame_dis_ap" id="frame_dis_ap" value="<?php echo $orderDetails->frame_dis_ap ; ?>" />
		<input type="hidden" name="cptVal" id="cptVal"/>
		<input type="hidden" name="adminLenseCost" id="adminLenseCost"/>
		<div class="col-sm-12">
			<div class="row">
				<table id="order_tab_id1" class="table table-striped table-bordered">
					<tr class="grythead">
						<th></th>
						<th>Demographics</th>
						<th>Physician Name</th>
						<th>Date</th>
						<th>Ref#</th>
						<th>Reorder</th>
					</tr>
					<tr>
						<td>
							<div class="row">
								<div class="col-sm-4">
									<div class="radio radio-inline">
										<input type="radio" id="order_confirm_medicare" name="order_confirm" onClick="getLensCost12(this.value);" <?php if($orderDetails->order_confirm == 'Medicare') print 'checked'; ?> value="Medicare">
										<label for="order_confirm_medicare">Medicare</label>	
									</div>
								</div>	
								<div class="col-sm-4">
									<div class="radio radio-inline">
										<input type="radio" name="order_confirm" onClick="getLensCost12(this.value);" <?php if($orderDetails->order_confirm == 'Commercial') print 'checked'; ?> value="Commercial" id="order_confirm_commercial">
										<label for="order_confirm_commercial">Glasses</label>
									</div>
								</div>	
								<div class="col-sm-4">
									<div class="radio radio-inline">
										<input type="radio" name="order_confirm" onClick="getLensCost12(this.value);" style="cursor:pointer;" <?php if($orderDetails->order_confirm == 'Patient') print 'checked'; ?> value="Patient" id="order_confirm_patient">
										<label for="order_confirm_patient">Patient</label>	
									</div>
								</div>	
							</div>
						</td>
						<td><?php echo $patient_address; ?>&nbsp;&nbsp;&nbsp;<?php echo core_phone_format($phone_home); ?></td>
						<td><?php echo $physicianName; ?></td>
						<td><?php echo date('m-d-y'); ?></td>
						<td>
							<input type="text" name="ref" id="ref" class="form-control" value="<?php echo $orderDetails->ref; ?>" />
						</td>
						<td class="text-center">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="reorder" id="reorder" value="1" onClick="setLastOrderVal()" />
								<label for="reorder"></label>	
							</div>
						</td>	
					</tr>	
				</table>
			</div>
		</div>

		<div class="col-sm-12 pt10">
			<div class="row">
				<table id="order_tab_id2" class="table table-striped table-bordered" >
					<tr class="grythead">
						<th>Vision</th>
						<th>Sphere</th>
						<th>Cyl</th>
						<th>Axis</th>
						<th>Add</th>
						<th>Prism</th>
						<th>HT</th>
						<th class="text-nowrap">DIST PD</th>
						<th class="text-nowrap">Near PD</th>
						<th>Base</th>
					</tr>
					<tr>
						<td><img src="<?php echo $GLOBALS['webroot'] ?>/library/images/tstodactive.png"></td>
						<td><input type="text" name="sphere_od" id="sphere_od" class="form-control" value="<?php print $sphere_od; ?>" /></td>
						<td><input type="text" name="cyl_od" id="cyl_od" class="form-control" value="<?php print $cyl_od; ?>" /></td>
						<td><input type="text" name="axis_od" id="axis_od" class="form-control" value="<?php print $axis_od; ?>" /></td>
						<td><input type="text" name="add_od" id="add_od" class="form-control" value="<?php print $add_od; ?>" /></td>
						<td class="form-inline text-nowrap">
							<div class="form-group">
								<label><strong>P</strong></label>&nbsp;
								<select id="elem_visMrOdP" name="elem_visMrOdP" class="form-control minimal" onChange="getPrismCode(this);" >
									<option value=""></option>
									<option value="1" <?php echo ($elem_visMrOdP == "1" ) ? "selected" : "" ;?>>1</option>
									<option value="1.5" <?php echo ($elem_visMrOdP == "1.5" ) ? "selected" : "" ;?>>1.5</option>
									<option value="2" <?php echo ($elem_visMrOdP == "2" ) ? "selected" : "" ;?>>2</option>
									<option value="2.5" <?php echo ($elem_visMrOdP == "2.5" ) ? "selected" : "" ;?>>2.5</option>
									<option value="3" <?php echo ($elem_visMrOdP == "3" ) ? "selected" : "" ;?>>3</option>
									<option value="3.5" <?php echo ($elem_visMrOdP == "3.5" ) ? "selected" : "" ;?>>3.5</option>
									<option value="4" <?php echo ($elem_visMrOdP == "4" ) ? "selected" : "" ;?>>4</option>
									<option value="4.5" <?php echo ($elem_visMrOdP == "4.5" ) ? "selected" : "" ;?>>4.5</option>
									<option value="5" <?php echo ($elem_visMrOdP == "5" ) ? "selected" : "" ;?>>5</option>
									<option value="5.5" <?php echo ($elem_visMrOdP == "5.5" ) ? "selected" : "" ;?>>5.5</option>
									<option value="6" <?php echo ($elem_visMrOdP == "6" ) ? "selected" : "" ;?>>6</option>
									<option value="6.5" <?php echo ($elem_visMrOdP == "6.5" ) ? "selected" : "" ;?>>6.5</option>
									<option value="7" <?php echo ($elem_visMrOdP == "7" ) ? "selected" : "" ;?>>7</option>
									<option value="7.5" <?php echo ($elem_visMrOdP == "7.5" ) ? "selected" : "" ;?>>7.5</option>
									<option value="8" <?php echo ($elem_visMrOdP == "8" ) ? "selected" : "" ;?>>8</option>
									<option value="8.25" <?php echo ($elem_visMrOdP == "8.25" ) ? "selected" : "" ;?>>8.25</option>
									<option value="8.5" <?php echo ($elem_visMrOdP == "8.5" ) ? "selected" : "" ;?>>8.5</option>
									<option value="8.75" <?php echo ($elem_visMrOdP == "8.75" ) ? "selected" : "" ;?>>8.75</option>
									<option value="9" <?php echo ($elem_visMrOdP == "9" ) ? "selected" : "" ;?>>9</option>
									<option value="9.25" <?php echo ($elem_visMrOdP == "9.25" ) ? "selected" : "" ;?>>9.25</option>
									<option value="9.5" <?php echo ($elem_visMrOdP == "9.5" ) ? "selected" : "" ;?>>9.5</option>
									<option value="9.75" <?php echo ($elem_visMrOdP == "9.75" ) ? "selected" : "" ;?>>9.75</option>
									<option value="10" <?php echo ($elem_visMrOdP == "10" ) ? "selected" : "" ;?>>10</option>
									<option value="10.25" <?php echo ($elem_visMrOdP == "10.25" ) ? "selected" : "" ;?>>10.25</option>
									<option value="10.5" <?php echo ($elem_visMrOdP == "10.5" ) ? "selected" : "" ;?>>10.5</option>
									<option value="10.75" <?php echo ($elem_visMrOdP == "10.75" ) ? "selected" : "" ;?>>10.75</option>
									<option value="11" <?php echo ($elem_visMrOdP == "11" ) ? "selected" : "" ;?>>11</option>
									<option value="11.25" <?php echo ($elem_visMrOdP == "11.25" ) ? "selected" : "" ;?>>11.25</option>
									<option value="11.5" <?php echo ($elem_visMrOdP == "11.5" ) ? "selected" : "" ;?>>11.5</option>
									<option value="11.75" <?php echo ($elem_visMrOdP == "11.75" ) ? "selected" : "" ;?>>11.75</option>
									<option value="12" <?php echo ($elem_visMrOdP == "12" ) ? "selected" : "" ;?>>12</option>
									<option value="12.25" <?php echo ($elem_visMrOdP == "12.25" ) ? "selected" : "" ;?>>12.25</option>
									<option value="12.5" <?php echo ($elem_visMrOdP == "12.5" ) ? "selected" : "" ;?>>12.5</option>
									<option value="12.75" <?php echo ($elem_visMrOdP == "12.75" ) ? "selected" : "" ;?>>12.75</option>
									<option value="13" <?php echo ($elem_visMrOdP == "13" ) ? "selected" : "" ;?>>13</option>
									<option value="13.25" <?php echo ($elem_visMrOdP == "13.25" ) ? "selected" : "" ;?>>13.25</option>
									<option value="13.5" <?php echo ($elem_visMrOdP == "13.5" ) ? "selected" : "" ;?>>13.5</option>
									<option value="13.75" <?php echo ($elem_visMrOdP == "13.75" ) ? "selected" : "" ;?>>13.75</option>
									<option value="14" <?php echo ($elem_visMrOdP == "14" ) ? "selected" : "" ;?>>14</option>
									<option value="14.25" <?php echo ($elem_visMrOdP == "14.25" ) ? "selected" : "" ;?>>14.25</option>
									<option value="14.5" <?php echo ($elem_visMrOdP == "14.5" ) ? "selected" : "" ;?>>14.5</option>
									<option value="14.75" <?php echo ($elem_visMrOdP == "14.75" ) ? "selected" : "" ;?>>14.75</option>
									<option value="15" <?php echo ($elem_visMrOdP == "15" ) ? "selected" : "" ;?>>15</option>
								</select>
								<img src="<?php echo $GLOBALS['webroot'] ?>/library/images/up-arrow.png">
								<select id="elem_visMrOdSel1" name="elem_visMrOdSel1" class="form-control minimal" onChange="getPrismCode(this);">
									<option value=""></option>
									<option value="BI" <?php echo ($elem_visMrOdSel1 == "BI" ) ? "selected" : "" ;?>>BI</option>
									<option value="BO" <?php echo ($elem_visMrOdSel1 == "BO" ) ? "selected" : "" ;?>>BO</option>	
								</select>&nbsp;/
								<select id="elem_visMrOdSlash" name="elem_visMrOdSlash" class="form-control minimal" onChange="getPrismCode(this);">
									  <option value=""></option>
									  <option value="1" <?php echo ($elem_visMrOdSlash == "1" ) ? "selected" : "" ;?>>1</option>
									  <option value="1.5" <?php echo ($elem_visMrOdSlash == "1.5" ) ? "selected" : "" ;?>>1.5</option>
									  <option value="2" <?php echo ($elem_visMrOdSlash == "2" ) ? "selected" : "" ;?>>2</option>
									  <option value="2.5" <?php echo ($elem_visMrOdSlash == "2.5" ) ? "selected" : "" ;?>>2.5</option>
									  <option value="3" <?php echo ($elem_visMrOdSlash == "3" ) ? "selected" : "" ;?>>3</option>
									  <option value="3.5" <?php echo ($elem_visMrOdSlash == "3.5" ) ? "selected" : "" ;?>>3.5</option>
									  <option value="4" <?php echo ($elem_visMrOdSlash == "4" ) ? "selected" : "" ;?>>4</option>
									  <option value="4.5" <?php echo ($elem_visMrOdSlash == "4.5" ) ? "selected" : "" ;?>>4.5</option>
									  <option value="5" <?php echo ($elem_visMrOdSlash == "5" ) ? "selected" : "" ;?>>5</option>
									  <option value="5.5" <?php echo ($elem_visMrOdSlash == "5.5" ) ? "selected" : "" ;?>>5.5</option>
									  <option value="6" <?php echo ($elem_visMrOdSlash == "6" ) ? "selected" : "" ;?>>6</option>
									  <option value="6.5" <?php echo ($elem_visMrOdSlash == "6.5" ) ? "selected" : "" ;?>>6.5</option>
									  <option value="7" <?php echo ($elem_visMrOdSlash == "7" ) ? "selected" : "" ;?>>7</option>
									  <option value="7.5" <?php echo ($elem_visMrOdSlash == "7.5" ) ? "selected" : "" ;?>>7.5</option>
									  <option value="8" <?php echo ($elem_visMrOdSlash == "8" ) ? "selected" : "" ;?>>8</option>
									  <option value="8.25" <?php echo ($elem_visMrOdSlash == "8.25" ) ? "selected" : "" ;?>>8.25</option>
									  <option value="8.5" <?php echo ($elem_visMrOdSlash == "8.5" ) ? "selected" : "" ;?>>8.5</option>
									  <option value="8.75" <?php echo ($elem_visMrOdSlash == "8.75" ) ? "selected" : "" ;?>>8.75</option>
									  <option value="9" <?php echo ($elem_visMrOdSlash == "9" ) ? "selected" : "" ;?>>9</option>
									  <option value="9.25" <?php echo ($elem_visMrOdSlash == "9.25" ) ? "selected" : "" ;?>>9.25</option>
									  <option value="9.5" <?php echo ($elem_visMrOdSlash == "9.5" ) ? "selected" : "" ;?>>9.5</option>
									  <option value="9.75" <?php echo ($elem_visMrOdSlash == "9.75" ) ? "selected" : "" ;?>>9.75</option>
									  <option value="10" <?php echo ($elem_visMrOdSlash == "10" ) ? "selected" : "" ;?>>10</option>
									  <option value="10.25" <?php echo ($elem_visMrOdSlash == "10.25" ) ? "selected" : "" ;?>>10.25</option>
									  <option value="10.5" <?php echo ($elem_visMrOdSlash == "10.5" ) ? "selected" : "" ;?>>10.5</option>
									  <option value="10.75" <?php echo ($elem_visMrOdSlash == "10.75" ) ? "selected" : "" ;?>>10.75</option>
									  <option value="11" <?php echo ($elem_visMrOdSlash == "11" ) ? "selected" : "" ;?>>11</option>
									  <option value="11.25" <?php echo ($elem_visMrOdSlash == "11.25" ) ? "selected" : "" ;?>>11.25</option>
									  <option value="11.5" <?php echo ($elem_visMrOdSlash == "11.5" ) ? "selected" : "" ;?>>11.5</option>
									  <option value="11.75" <?php echo ($elem_visMrOdSlash == "11.75" ) ? "selected" : "" ;?>>11.75</option>
									  <option value="12" <?php echo ($elem_visMrOdSlash == "12" ) ? "selected" : "" ;?>>12</option>
									  <option value="12.25" <?php echo ($elem_visMrOdSlash == "12.25" ) ? "selected" : "" ;?>>12.25</option>
									  <option value="12.5" <?php echo ($elem_visMrOdSlash == "12.5" ) ? "selected" : "" ;?>>12.5</option>
									  <option value="12.75" <?php echo ($elem_visMrOdSlash == "12.75" ) ? "selected" : "" ;?>>12.75</option>
									  <option value="13" <?php echo ($elem_visMrOdSlash == "13" ) ? "selected" : "" ;?>>13</option>
									  <option value="13.25" <?php echo ($elem_visMrOdSlash == "13.25" ) ? "selected" : "" ;?>>13.25</option>
									  <option value="13.5" <?php echo ($elem_visMrOdSlash == "13.5" ) ? "selected" : "" ;?>>13.5</option>
									  <option value="13.75" <?php echo ($elem_visMrOdSlash == "13.75" ) ? "selected" : "" ;?>>13.75</option>
									  <option value="14" <?php echo ($elem_visMrOdSlash == "14" ) ? "selected" : "" ;?>>14</option>
									  <option value="14.25" <?php echo ($elem_visMrOdSlash == "14.25" ) ? "selected" : "" ;?>>14.25</option>
									  <option value="14.5" <?php echo ($elem_visMrOdSlash == "14.5" ) ? "selected" : "" ;?>>14.5</option>
									  <option value="14.75" <?php echo ($elem_visMrOdSlash == "14.75" ) ? "selected" : "" ;?>>14.75</option>
									<option value="15" <?php echo ($elem_visMrOdSlash == "15" ) ? "selected" : "" ;?>>15</option>
								</select>&nbsp;
								<select id="elem_visMrOdPrism" name="elem_visMrOdPrism" class="form-control minimal" onChange="getPrismCode(this);">
								  <option value=""></option>
								  <option value="BD" <?php echo ($elem_visMrOdPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
								  <option value="BU" <?php echo ($elem_visMrOdPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
								</select>	
							</div>	
						</td>
						<td><input type="text" name="optic_ht" id="optic_ht" class="form-control" value="<?php print $orderDetails->optic_ht; ?>" /></td>
						<td><input type="text" name="dist_pd_od" id="dist_pd_od" class="form-control" value="<?php print $orderDetails->dist_pd_od; ?>" /></td>
						<td><input type="text" name="near_pd_od" id="near_pd_od" class="form-control" value="<?php print $orderDetails->near_pd_od; ?>" /></td>
						<td><input type="text" name="base_od" id="base_od" class="form-control" value="<?php print $orderDetails->base_od; ?>" /></td>
					</tr>
					<tr>
						<td><img src="<?php echo $GLOBALS['webroot'] ?>/library/images/tstosactive.png"></td>
						<td><input type="text" name="sphere_os" id="sphere_os"  class="form-control" value="<?php print $sphere_os; ?>" /></td>
						<td><input type="text" name="cyl_os" id="cyl_os" class="form-control" value="<?php print $cyl_os; ?>" /></td>
						<td><input type="text" name="axis_os" id="axis_os" class="form-control" value="<?php print $axis_os; ?>" /></td>
						<td><input type="text" name="add_os" id="add_os" class="form-control" value="<?php print $add_os; ?>" /></td>
						<td class="form-inline text-nowrap">
							<div class="form-group">
								<label><strong>P</strong></label>&nbsp;
								 <select id="elem_visMrOsP" name="elem_visMrOsP" class="form-control minimal" >
									<option value=""></option>
									<option value="1" <?php echo ($elem_visMrOsP == "1" ) ? "selected" : "" ;?>>1</option>
									<option value="1.5" <?php echo ($elem_visMrOsP == "1.5" ) ? "selected" : "" ;?>>1.5</option>
									<option value="2" <?php echo ($elem_visMrOsP == "2" ) ? "selected" : "" ;?>>2</option>
									<option value="2.5" <?php echo ($elem_visMrOsP == "2.5" ) ? "selected" : "" ;?>>2.5</option>
									<option value="3" <?php echo ($elem_visMrOsP == "3" ) ? "selected" : "" ;?>>3</option>
									<option value="3.5" <?php echo ($elem_visMrOsP == "3.5" ) ? "selected" : "" ;?>>3.5</option>
									<option value="4" <?php echo ($elem_visMrOsP == "4" ) ? "selected" : "" ;?>>4</option>
									<option value="4.5" <?php echo ($elem_visMrOsP == "4.5" ) ? "selected" : "" ;?>>4.5</option>
									<option value="5" <?php echo ($elem_visMrOsP == "5" ) ? "selected" : "" ;?>>5</option>
									<option value="5.5" <?php echo ($elem_visMrOsP == "5.5" ) ? "selected" : "" ;?>>5.5</option>
									<option value="6" <?php echo ($elem_visMrOsP == "6" ) ? "selected" : "" ;?>>6</option>
									<option value="6.5" <?php echo ($elem_visMrOsP == "6.5" ) ? "selected" : "" ;?>>6.5</option>
									<option value="7" <?php echo ($elem_visMrOsP == "7" ) ? "selected" : "" ;?>>7</option>
									<option value="7.5" <?php echo ($elem_visMrOsP == "7.5" ) ? "selected" : "" ;?>>7.5</option>
									<option value="8" <?php echo ($elem_visMrOsP == "8" ) ? "selected" : "" ;?>>8</option>
									<option value="8.25" <?php echo ($elem_visMrOsP == "8.25" ) ? "selected" : "" ;?>>8.25</option>
									<option value="8.5" <?php echo ($elem_visMrOsP == "8.5" ) ? "selected" : "" ;?>>8.5</option>
									<option value="8.75" <?php echo ($elem_visMrOsP == "8.75" ) ? "selected" : "" ;?>>8.75</option>
									<option value="9" <?php echo ($elem_visMrOsP == "9" ) ? "selected" : "" ;?>>9</option>
									<option value="9.25" <?php echo ($elem_visMrOsP == "9.25" ) ? "selected" : "" ;?>>9.25</option>
									<option value="9.5" <?php echo ($elem_visMrOsP == "9.5" ) ? "selected" : "" ;?>>9.5</option>
									<option value="9.75" <?php echo ($elem_visMrOsP == "9.75" ) ? "selected" : "" ;?>>9.75</option>
									<option value="10" <?php echo ($elem_visMrOsP == "10" ) ? "selected" : "" ;?>>10</option>
									<option value="10.25" <?php echo ($elem_visMrOsP == "10.25" ) ? "selected" : "" ;?>>10.25</option>
									<option value="10.5" <?php echo ($elem_visMrOsP == "10.5" ) ? "selected" : "" ;?>>10.5</option>
									<option value="10.75" <?php echo ($elem_visMrOsP == "10.75" ) ? "selected" : "" ;?>>10.75</option>
									<option value="11" <?php echo ($elem_visMrOsP == "11" ) ? "selected" : "" ;?>>11</option>
									<option value="11.25" <?php echo ($elem_visMrOsP == "11.25" ) ? "selected" : "" ;?>>11.25</option>
									<option value="11.5" <?php echo ($elem_visMrOsP == "11.5" ) ? "selected" : "" ;?>>11.5</option>
									<option value="11.75" <?php echo ($elem_visMrOsP == "11.75" ) ? "selected" : "" ;?>>11.75</option>
									<option value="12" <?php echo ($elem_visMrOsP == "12" ) ? "selected" : "" ;?>>12</option>
									<option value="12.25" <?php echo ($elem_visMrOsP == "12.25" ) ? "selected" : "" ;?>>12.25</option>
									<option value="12.5" <?php echo ($elem_visMrOsP == "12.5" ) ? "selected" : "" ;?>>12.5</option>
									<option value="12.75" <?php echo ($elem_visMrOsP == "12.75" ) ? "selected" : "" ;?>>12.75</option>
									<option value="13" <?php echo ($elem_visMrOsP == "13" ) ? "selected" : "" ;?>>13</option>
									<option value="13.25" <?php echo ($elem_visMrOsP == "13.25" ) ? "selected" : "" ;?>>13.25</option>
									<option value="13.5" <?php echo ($elem_visMrOsP == "13.5" ) ? "selected" : "" ;?>>13.5</option>
									<option value="13.75" <?php echo ($elem_visMrOsP == "13.75" ) ? "selected" : "" ;?>>13.75</option>
									<option value="14" <?php echo ($elem_visMrOsP == "14" ) ? "selected" : "" ;?>>14</option>
									<option value="14.25" <?php echo ($elem_visMrOsP == "14.25" ) ? "selected" : "" ;?>>14.25</option>
									<option value="14.5" <?php echo ($elem_visMrOsP == "14.5" ) ? "selected" : "" ;?>>14.5</option>
									<option value="14.75" <?php echo ($elem_visMrOsP == "14.75" ) ? "selected" : "" ;?>>14.75</option>
									<option value="15" <?php echo ($elem_visMrOsP == "15" ) ? "selected" : "" ;?>>15</option>
								</select>
								<img src="<?php echo $GLOBALS['webroot'] ?>/library/images/up-arrow.png">
								<select id="elem_visMrOsSel1" name="elem_visMrOsSel1" class="form-control minimal">
									<option value=""></option>
									<option value="BI" <?php echo ($elem_visMrOsSel1 == "BI" ) ? "selected" : "" ;?>>BI</option>
									<option value="BO" <?php echo ($elem_visMrOsSel1 == "BO" ) ? "selected" : "" ;?>>BO</option>	
								</select>&nbsp;/
								<select id="elem_visMrOsSlash" name="elem_visMrOsSlash" class="form-control minimal">
									<option value=""></option>
									<option value="1" <?php echo ($elem_visMrOsSlash == "1" ) ? "selected" : "" ;?>>1</option>
									<option value="1.5" <?php echo ($elem_visMrOsSlash == "1.5" ) ? "selected" : "" ;?>>1.5</option>
									<option value="2" <?php echo ($elem_visMrOsSlash == "2" ) ? "selected" : "" ;?>>2</option>
									<option value="2.5" <?php echo ($elem_visMrOsSlash == "2.5" ) ? "selected" : "" ;?>>2.5</option>
									<option value="3" <?php echo ($elem_visMrOsSlash == "3" ) ? "selected" : "" ;?>>3</option>
									<option value="3.5" <?php echo ($elem_visMrOsSlash == "3.5" ) ? "selected" : "" ;?>>3.5</option>
									<option value="4" <?php echo ($elem_visMrOsSlash == "4" ) ? "selected" : "" ;?>>4</option>
									<option value="4.5" <?php echo ($elem_visMrOsSlash == "4.5" ) ? "selected" : "" ;?>>4.5</option>
									<option value="5" <?php echo ($elem_visMrOsSlash == "5" ) ? "selected" : "" ;?>>5</option>
									<option value="5.5" <?php echo ($elem_visMrOsSlash == "5.5" ) ? "selected" : "" ;?>>5.5</option>
									<option value="6" <?php echo ($elem_visMrOsSlash == "6" ) ? "selected" : "" ;?>>6</option>
									<option value="6.5" <?php echo ($elem_visMrOsSlash == "6.5" ) ? "selected" : "" ;?>>6.5</option>
									<option value="7" <?php echo ($elem_visMrOsSlash == "7" ) ? "selected" : "" ;?>>7</option>
									<option value="7.5" <?php echo ($elem_visMrOsSlash == "7.5" ) ? "selected" : "" ;?>>7.5</option>
									<option value="8" <?php echo ($elem_visMrOsSlash == "8" ) ? "selected" : "" ;?>>8</option>
									<option value="8.25" <?php echo ($elem_visMrOsSlash == "8.25" ) ? "selected" : "" ;?>>8.25</option>
									<option value="8.5" <?php echo ($elem_visMrOsSlash == "8.5" ) ? "selected" : "" ;?>>8.5</option>
									<option value="8.75" <?php echo ($elem_visMrOsSlash == "8.75" ) ? "selected" : "" ;?>>8.75</option>
									<option value="9" <?php echo ($elem_visMrOsSlash == "9" ) ? "selected" : "" ;?>>9</option>
									<option value="9.25" <?php echo ($elem_visMrOsSlash == "9.25" ) ? "selected" : "" ;?>>9.25</option>
									<option value="9.5" <?php echo ($elem_visMrOsSlash == "9.5" ) ? "selected" : "" ;?>>9.5</option>
									<option value="9.75" <?php echo ($elem_visMrOsSlash == "9.75" ) ? "selected" : "" ;?>>9.75</option>
									<option value="10" <?php echo ($elem_visMrOsSlash == "10" ) ? "selected" : "" ;?>>10</option>
									<option value="10.25" <?php echo ($elem_visMrOsSlash == "10.25" ) ? "selected" : "" ;?>>10.25</option>
									<option value="10.5" <?php echo ($elem_visMrOsSlash == "10.5" ) ? "selected" : "" ;?>>10.5</option>
									<option value="10.75" <?php echo ($elem_visMrOsSlash == "10.75" ) ? "selected" : "" ;?>>10.75</option>
									<option value="11" <?php echo ($elem_visMrOsSlash == "11" ) ? "selected" : "" ;?>>11</option>
									<option value="11.25" <?php echo ($elem_visMrOsSlash == "11.25" ) ? "selected" : "" ;?>>11.25</option>
									<option value="11.5" <?php echo ($elem_visMrOsSlash == "11.5" ) ? "selected" : "" ;?>>11.5</option>
									<option value="11.75" <?php echo ($elem_visMrOsSlash == "11.75" ) ? "selected" : "" ;?>>11.75</option>
									<option value="12" <?php echo ($elem_visMrOsSlash == "12" ) ? "selected" : "" ;?>>12</option>
									<option value="12.25" <?php echo ($elem_visMrOsSlash == "12.25" ) ? "selected" : "" ;?>>12.25</option>
									<option value="12.5" <?php echo ($elem_visMrOsSlash == "12.5" ) ? "selected" : "" ;?>>12.5</option>
									<option value="12.75" <?php echo ($elem_visMrOsSlash == "12.75" ) ? "selected" : "" ;?>>12.75</option>
									<option value="13" <?php echo ($elem_visMrOsSlash == "13" ) ? "selected" : "" ;?>>13</option>
									<option value="13.25" <?php echo ($elem_visMrOsSlash == "13.25" ) ? "selected" : "" ;?>>13.25</option>
									<option value="13.5" <?php echo ($elem_visMrOsSlash == "13.5" ) ? "selected" : "" ;?>>13.5</option>
									<option value="13.75" <?php echo ($elem_visMrOsSlash == "13.75" ) ? "selected" : "" ;?>>13.75</option>
									<option value="14" <?php echo ($elem_visMrOsSlash == "14" ) ? "selected" : "" ;?>>14</option>
									<option value="14.25" <?php echo ($elem_visMrOsSlash == "14.25" ) ? "selected" : "" ;?>>14.25</option>
									<option value="14.5" <?php echo ($elem_visMrOsSlash == "14.5" ) ? "selected" : "" ;?>>14.5</option>
									<option value="14.75" <?php echo ($elem_visMrOsSlash == "14.75" ) ? "selected" : "" ;?>>14.75</option>
									<option value="15" <?php echo ($elem_visMrOsSlash == "15" ) ? "selected" : "" ;?>>15</option>
								</select>&nbsp;
								 <select id="elem_visMrOsPrism" name="elem_visMrOsPrism" class="form-control minimal" >
									<option value=""></option>
									<option value="BD" <?php echo ($elem_visMrOsPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
									<option value="BU" <?php echo ($elem_visMrOsPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
								</select>	
							</div>	
						</td>
						<td><input type="text" name="optic_ht_os" id="optic_ht_os" class="form-control" value="<?php print $orderDetails->optic_ht_os; ?>" /></td>
						<td><input type="text" name="dist_pd_os" id="dist_pd_os" class="form-control" value="<?php print $orderDetails->dist_pd_os; ?>" /></td>
						<td><input type="text" name="near_pd_os" id="near_pd_os" class="form-control" value="<?php print $orderDetails->near_pd_os; ?>" /></td>
						<td><input type="text" name="base_os" id="base_os" class="form-control" value="<?php print $orderDetails->base_os; ?>" /></td>
					</tr>	
				</table>
			</div>	
		</div>
		
		<div class="col-sm-12 pt10">
			<div class="row">
				<table class="table table-striped table-bordered" >
					<tr class="grythead">
						<th>Manufacturer</th>
						<th>Make</th>
						<th>Style</th>
						<th>Color</th>
						<th>Eye</th>
						<th>Bridge</th>
						<th>A</th>
						<th>B</th>
						<th>ED</th>
						<th>Templ</th>
						<th>SCR</th>
						<th>UV</th>
					</tr>
					<tr>
						<td>
							<div class="input-group">
								<input type="text" id="vendor_name" name="vendor_name" value="<?php print $orderDetails->vendor_name; ?>" onChange="return get_frames(this.value);"	class="form-control" />
								<label for="vendor_name" class="input-group-addon pointer" onClick="select_frame();">
									<span class="glyphicon glyphicon-search"></span>
								</label>	
							</div>
						</td>
						<td id="frames_td">
							<select id="frames_name" name="frames_name" onChange="get_frame_name(this)" class="form-control minimal">
								<option value=""> Select </option>
								<?php echo $frame_options;?>
							</select>	
						</td>
						<td><input type="text" name="frame_style" id="frame_style" value="<?php print $orderDetails->frame_style; ?>" class="form-control" /></td>
						<td><input type="text" name="frame_color" id="frame_color" value="<?php print $orderDetails->frame_color; ?>" class="form-control" ></td>
						<td><input type="text" name="frame_eye" id="frame_eye"  value="<?php print $orderDetails->frame_eye; ?>" class="form-control"></td>
						<td><input type="text" name="frame_bridge" id="frame_bridge" value="<?php print $orderDetails->frame_bridge; ?>" class="form-control"></td>
						<td><input type="text" name="frame_a" id="frame_a" value="<?php print $orderDetails->frame_a; ?>" class="form-control"></td>
						<td><input type="text" name="frame_b" id="frame_b" value="<?php print $orderDetails->frame_b; ?>" class="form-control"></td>
						<td><input type="text" name="frame_ed" id="frame_ed" value="<?php print $orderDetails->frame_ed; ?>" class="form-control"></td>
						<td><input type="text" name="temple" id="temple" value="<?php print $orderDetails->temple; ?>"	class="form-control"></td>
						<td>
							<select name="frame_scr" id="frame_scr" class="form-control minimal" onChange="fill_price();">
							  <option value="" <?php if($orderDetails->frame_scr == ''){ print'selected';} ?>>No</option>
							  <option value="Yes" <?php if($orderDetails->frame_scr == 'Yes'){ print'selected';} ?>>Yes</option>
							</select>
						</td>
						<td>
							<select name="frame_uv" id="frame_uv" class="form-control minimal" onChange="getUvCost();">
							  <option value="No" <?php if($orderDetails->frame_uv == 'No'){ print'selected';} ?>>No</option>
							  <option value="Yes" <?php if($orderDetails->frame_uv == 'Yes'){ print'selected';} ?>>Yes</option>
							</select>
						</td>	
					</tr>	
				</table>	
			</div>	
		</div>

		<div class="col-sm-12 pt10 lensopt">
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6">
					<div class="lenstype form-inline">
						<div class="form-group">
							<label for="">Lens Type</label>
							<div class="clearfix"></div>
							<div class="row">
								<div id="lens_opt_td" class="col-sm-6">
									<select name="lens_opt" id="lens_opt" class="form-control minimal" onChange="lensTypeSet(this.value); getLensCptCost(this.value); ">
									  <option value=""></option>
									  <option value="Single Vision" <?php if($orderDetails->lens_opt == 'Single Vision'){ print'selected';} ?> >Single Vision</option>
									  <option value="Bifocal" <?php if($orderDetails->lens_opt == 'Bifocal'){ print'selected';} ?>>Bifocal</option>
									  <option value="Trifocal" <?php if($orderDetails->lens_opt == 'Trifocal'){ print'selected';} ?>>Trifocal</option>
									  <option value="Progressive" <?php if($orderDetails->lens_opt == 'Progressive'){ print'selected';} ?>>Progressive</option>
									  <option value="Deluxe Progressive" <?php if($orderDetails->Tab_val == 'Deluxe Progressive ') print 'selected'; ?>>Deluxe Progressive </option>
									</select>
								</div>	
								<div id="bifocal" class="col-sm-6">
									<select name="bifocal_opt" id="bifocal_opt" class="form-control minimal">
									  <?php if($orderDetails->lens_opt == 'Single Vision'){ ?>
										<option value="DV" <?php if($orderDetails->bifocal_opt == 'DV') echo 'selected'; ?>>DV</option>
										<option value="NV" <?php if($orderDetails->bifocal_opt == 'NV') echo 'selected'; ?>>NV</option>
									  <?php
											}
											else if($orderDetails->lens_opt == 'Bifocal')
											{
											?>
									  <option value="FT 28" <?php if($orderDetails->bifocal_opt == 'FT 28') echo 'selected'; ?>>FT 28</option>
									  <option value="FT 35" <?php if($orderDetails->bifocal_opt == 'FT 35') echo 'selected'; ?>>FT 35</option>
									  <option value="FT 22" <?php if($orderDetails->bifocal_opt == 'FT 22') echo 'selected'; ?>>FT 22</option>
									  <option value="Blended" <?php if($orderDetails->bifocal_opt == 'Blended') echo 'selected'; ?>>Blended</option>
									  <?php
											}
											else if($orderDetails->lens_opt == 'Trifocal')
											{
											?>
									  <option value="FT 7 x 28" <?php if($orderDetails->bifocal_opt == 'FT 7 x 28') echo 'selected'; ?>>FT 7 x 28</option>
									  <option value="FT 8 x 35" <?php if($orderDetails->bifocal_opt == 'FT 8 x 35') echo 'selected'; ?>>FT 8 x 35</option>
									  <?php 
											}
											else if($orderDetails->lens_opt == 'Progressive')
											{
											?>
									  <option value="Creation" <?php if($orderDetails->bifocal_opt == 'Creation') echo 'selected'; ?>>Creation</option>
									  <option value="Varilux" <?php if($orderDetails->bifocal_opt == 'Varilux') echo 'selected'; ?>>Varilux</option>
									  <option value="Other" <?php if($orderDetails->bifocal_opt == 'Other') echo 'selected'; ?>>Other</option>
									  <?php 
											} 
										?>
									</select>
								</div>	
							</div>	
						</div>
					</div>
				</div>
	
				<div class="col-lg-2 col-md-2 col-sm-6 lensmater">
					<label>Lens Material</label>
					<select name="lens_material" id="lens_material" class="form-control minimal" onChange="setTint_opt();">
						<option value=""></option>
						<option value="Glass" <?php if($orderDetails->lens_material == 'Glass'){ print'selected';} ?>>Glass</option>
						<option value="Plastic" <?php if($orderDetails->lens_material == 'Plastic'){ print'selected';} ?>>Plastic</option>
						<option value="Polycarbonate" <?php if($orderDetails->lens_material == 'Polycarbonate'){ print'selected';} ?>>Polycarbonate</option>
						<option value="Hi_Index" <?php if($orderDetails->lens_material == 'Hi_Index'){ print'selected';} ?>>Hi Index</option>
						<option value="Trivax" <?php if($orderDetails->lens_material == 'Trivax'){ print'selected';} ?>>Trivax</option>
						<option value="Other" <?php if($orderDetails->lens_material == 'Other'){ print'selected';} ?>>Other</option>	
					</select>	
				</div>	

				<div class="col-lg-2 col-md-2 col-sm-6 lenstint">
					<label>TINT</label>
					<select name="tini_opt" id="tini_opt" class="form-control minimal" onChange="getTintCptCost(this.value);">
					  <option value="">None</option>
					  <option value="Solid" <?php if($orderDetails->tini_opt == 'Solid'){ print'selected';} ?>>Solid</option>
					  <option value="Gradient" <?php if($orderDetails->tini_opt == 'Gradient'){ print'selected';} ?>>Gradient</option>
					</select>	
				</div>

				<div class="col-lg-5 col-md-5 col-sm-6 lnsopchk text-center">
					<div class="checkbox checkbox-inline">
						<input type="checkbox" name="HT_lens" id="HT_lens" value="1" <?php if($orderDetails->HT_lens) print 'checked' ?> onClick="getHiCptCost();" />
						<label for="HT_lens">HI</label>	
					</div>
					<div class="checkbox checkbox-inline">
						<input type="checkbox" id="ar_charge" name="ar_charge" value="ar_val" <?php if($orderDetails->ar_charge == 'ar_val'){ echo 'checked'; } ?> onClick="getArCost();"  />
						<label for="ar_charge">AR</label>	
					</div>
					<div class="checkbox checkbox-inline">
						<input type="checkbox" id="Polaroid_material" name="Polaroid_material"  value="Polaroid" <?php if($orderDetails->Polaroid_material == 'Polaroid'){ echo 'checked'; } ?> onClick="getPolariodCost();" />
						<label for="Polaroid_material">Polaroid</label>	
					</div>
					<div class="checkbox checkbox-inline">
						<input type="checkbox" id="slad_off" name="slad_off"  value="slad_off" <?php if($orderDetails->slad_off == 'slad_off'){ echo 'checked'; } ?> onClick="getSladOffCost(this.checked);"  />
						<label for="slad_off">Slab-Off</label>	
					</div>
					<div class="checkbox checkbox-inline">
						<input type="checkbox" id="Photochromatic" name="Photochromatic"  value="Photochromatic" <?php if($orderDetails->Photochromatic == 'Photochromatic'){ echo 'checked'; } ?> onClick="getPhotochromaticCost();"  />
						<label for="Photochromatic">Photochromatic</label>	
					</div>	
				</div>	
			</div>	
		</div>
		
		<div class="col-sm-12">
			<div class="row pd10">
				<div class="col-lg-5 col-md-5 col-sm-12">
					<div class="costbox">
						<div class="head">
							<h2>Cost</h2>
						</div>
						<div class="clearfix"></div>
						<div class="form-horizontal pd15">
							<div class="form-group">
								<label for="txtframePrice" class="col-sm-3 control-label">Frames</label>
								<div class="col-sm-4">
									 <input readonly type="text" class="form-control" id="txtframePrice" name="txtframePrice"  value="<?php if($orderDetails->framePrice > 0) print $global_currency.number_format($orderDetails->framePrice,2); ?>" />
								</div>	
								<div class="col-sm-4">
									<input type="text" class="form-control" id="frame_cost" name="frame_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" <?php if(!$optical_obj->getPofVal($order_id)) { echo 'disabled="disabled"'; } ?>  value="<?php if($orderDetails->frame_cost > 0 && $optical_obj->getPofVal($order_id)) { print $global_currency.number_format($orderDetails->frame_cost,2); } ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="txtUnit" class="col-sm-3 control-label">Lenses</label>
								<div class="col-sm-2">
									 <input type="text" class="form-control" name="txtUnit"  id="txtUnit" value="<?php if($orderDetails->txtUnit > 0){echo $orderDetails->txtUnit;}else{ echo 2;}?>" onBlur="calLenseCost(this);" />
								</div>	
								<div class="col-sm-3">
									 <input size="7" type="text" class="form-control" id="lenese_cost" name="lenese_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->lenese_cost > 0) print $global_currency.number_format($orderDetails->lenese_cost,2); ?>" />
								</div>	
								<div class="col-sm-3">
									<input type="text" class="form-control" id="adminPatientLenseCost" onKeyDown="setCurrSign(this)" onChange="priceValid(this); get_total();" name="adminPatientLenseCost" value="<?php if($orderDetails->adminPatientLenseCost > 0) print $global_currency.number_format($orderDetails->adminPatientLenseCost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="prism_cost" class="col-sm-3 control-label">Prism</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="prism_cost" name="prism_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total()" value="<?php if($orderDetails->prism_cost > 0) print $global_currency.number_format($orderDetails->prism_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="polar_cost" class="col-sm-3 control-label">Polaroid</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="polar_cost" name="polar_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->polar_cost > 0) print $global_currency.number_format($orderDetails->polar_cost,2); ?>" />
								</div>	
							</div>	
							<div class="form-group">
								<label for="trans_cost" class="col-sm-3 control-label">Transition</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="trans_cost" name="trans_cost" onKeyDown="setCurrSign(this)" onKeyUp="getTransCost();" onClick="getTransCost();" onChange="priceValid(this);" value="<?php if($orderDetails->trans_cost > 0) print $global_currency.number_format($orderDetails->trans_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="scr_cost" class="col-sm-3 control-label">SCR</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="scr_cost" name="scr_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->scr_cost > 0) print $global_currency.number_format($orderDetails->scr_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="ar_cost" class="col-sm-3 control-label">AR</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="ar_cost" name="ar_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->ar_cost > 0) print $global_currency.number_format($orderDetails->ar_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="Slad_Off_cost" class="col-sm-3 control-label">Slab-Off</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="Slad_Off_cost" name="Slad_Off_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->Slad_Off_cost > 0) print $global_currency.number_format($orderDetails->Slad_Off_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="uv_cost" class="col-sm-3 control-label">UV</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="uv_cost" name="uv_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->uv_cost > 0) print $global_currency.number_format($orderDetails->uv_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="Photochromatic_cost" class="col-sm-3 control-label">Photochromatic</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="Photochromatic_cost" name="Photochromatic_cost" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); get_total();" value="<?php if($orderDetails->Photochromatic_cost > 0) print $global_currency.number_format($orderDetails->Photochromatic_cost,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="tint_cost_price" class="col-sm-3 control-label">Miscellaneous</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="tint_cost_price" id="tint_cost_price" onKeyDown="setCurrSign(this)" onChange="priceValid(this); get_total()" value="<?php if($orderDetails->tint_cost_price > 0) print $global_currency.number_format($orderDetails->tint_cost_price,2); ?>" />
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="hi_cost_price" id="hi_cost_price" onKeyDown="setCurrSign(this)" onChange="priceValid(this); get_total()" value="<?php if($orderDetails->hi_cost_price > 0) print $global_currency.number_format($orderDetails->hi_cost_price,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="discount_frames" class="col-sm-3 control-label">Discount&nbsp;(Frames)</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="discount_frames" id="discount_frames"  onChange="get_total();" value="<?php if($orderDetails->discount_frames != '' && $optical_obj->getPofVal($order_id)) print $orderDetails->discount_frames; ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="discount" class="col-sm-3 control-label">Discount (Lens)</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="discount" id="discount" onChange="get_total();"   value="<?php if($orderDetails->discount != '') print $orderDetails->discount; ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="total" class="col-sm-3 control-label">Total</label>
								<div class="col-sm-8">
									<input size="13" type="text" class="form-control" name="total" id="total"  value="<?php if($orderDetails->total > 0) print $global_currency.number_format($orderDetails->total,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="deposit" class="col-sm-3 control-label">Deposit</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="deposit" name="deposit" onFocus="fill_cost(this);" onChange="get_total();" value="<?php if($orderDetails->deposit > 0) print $global_currency.number_format($orderDetails->deposit,2); ?>" />
								</div>	
							</div>
							<div class="form-group">
								<label for="balance" class="col-sm-3 control-label">Balance</label>
								<div class="col-sm-8">
									<input type="text"  class="form-control" id="balance" name="balance" value="<?php if($orderDetails->balance > 0) print $global_currency.number_format($orderDetails->balance,2); ?>" />
								</div>	
							</div>	
						</div>	
					</div>
				</div>	
				<div class="col-lg-7 col-md-7 col-sm-12">
					<div class="costbox">
						<div class="head">
							<h2>SPECIAL INSTRUCTION</h2>	
						</div>
						<div class="form-horizontal pd15">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="Notification_comments" class="col-sm-6 control-label">Date Frame Ordered: </label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;" name="Notification_comments" id="Notification_comments" class="form-control datepicker" value="<?php print $frameOrder ; ?>" onClick="getcur_Dates(this)" />
												<label for="Notification_comments" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_frame_order" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text"  name="ref_frame_order" id="ref_frame_order" class="form-control" value="<?php echo $orderDetails->ref_frame_order; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>	
								<div class="col-sm-6">
									<div class="form-group">
										<label for="lens_order" id="incr" class="col-sm-6 control-label">Date Lens Ordered: </label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="lens_order" id="lens_order" class="form-control datepicker" value="<?php print $lensOrder; ?>" onClick="getcur_Dates(this)" />
												<label for="lens_order" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_lens_order" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text" name="ref_lens_order" id="ref_lens_order" class="form-control" value="<?php echo $orderDetails->ref_lens_order; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="frame_recieve" class="col-sm-6 control-label">Date Frame Received: </label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="frame_recieve" id="frame_recieve" class="form-control datepicker" value="<?php print $frameRecieve; ?>" onClick="getcur_Dates(this)" />
												<label for="frame_recieve" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_frame_recieve" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text"  name="ref_frame_recieve" id="ref_frame_recieve" class="form-control" value="<?php echo $orderDetails->ref_frame_recieve; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="lens_recieve" class="col-sm-6 control-label">Date Lens Received:</label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="lens_recieve" id="lens_recieve" class="form-control datepicker" value="<?php print $lensRecieve; ?>" onClick="getcur_Dates(this)" />
												<label for="lens_recieve" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_lens_recieve" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text"  name="ref_lens_recieve" id="ref_lens_recieve" class="form-control" value="<?php echo $orderDetails->ref_lens_recieve; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="patient_notify" class="col-sm-6 control-label">Date Pt. Notified:</label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="patient_notify" id="patient_notify" class="form-control datepicker" value="<?php print $notify; ?>" onClick="getcur_Dates(this)" />
												<label for="patient_notify" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_pt_notify" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text" name="ref_pt_notify" id="ref_pt_notify" class="form-control" value="<?php echo $orderDetails->ref_pt_notify; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="patient_picked_up" class="col-sm-6 control-label">Date dispensed:</label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="patient_picked_up" id="patient_picked_up" class="form-control datepicker" value="<?php print $picked_up; ?>" onClick="getcur_Dates(this)" />
												<label for="patient_picked_up" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_pt_picked" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text"  name="ref_pt_picked" id="ref_pt_picked" class="form-control" value="<?php echo $orderDetails->ref_pt_picked; ?>"  />
										</div>	
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="sale_date" class="col-sm-6 control-label">Date of Sale:</label>
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" onKeyDown="return false;"  name="sale_date" id="sale_date" class="form-control datepicker" value="<?php echo $date_of_sale; ?>" onClick="getcur_Dates(this)" />
												<label for="sale_date" class="input-group-addon pointer">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-5">
									<div class="form-group">
										<label for="ref_date_sale" class="col-sm-3 control-label">Ref. </label>
										<div class="col-sm-9">
											<input type="text" id="ref_date_sale"  name="ref_date_sale" class="form-control" value="<?php echo $orderDetails->ref_date_sale; ?>"  />
										</div>	
									</div>
								</div>
							</div>	
						</div>	
					</div>
					<div class="ordadva">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="promotions">Promotions:</label>
									<textarea rows="1" name="promotions" id="promotions" class="form-control"><?php print $orderDetails->promotions; ?></textarea>	
								</div>
							</div>	
							<div class="col-sm-3">
								<div class="form-group">
									<label for="paid_by">Paid By:</label>
									<div class="clearfix"></div>
									<div class="form-inline paidby">
										<select name="paid_by" id="paid_by" class="form-control minimal">
											<option value=""></option>
											<option value="Patient" <?php if($orderDetails->paid_by == 'Patient') print 'selected'; ?> >Patient</option>
											<option value="Third Party" <?php if($orderDetails->paid_by == 'Third Party') print 'selected'; ?>>Third Party</option>
											<option value="Insurance" <?php if($orderDetails->paid_by == 'Insurance') print 'selected'; ?> >Insurance</option>
										</select>
									</div>	
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="payment_method">Method:</label>
									<div class="clearfix"></div>
									<div class="form-inline paidby">
										<select name="payment_method" id="payment_method" class="form-control minimal">
										  <option value=""></option>
										  <option value="Cash" <?php if($orderDetails->payment_method == 'Cash') print 'selected'; ?>>Cash</option>
										  <option value="Cheque" <?php if($orderDetails->payment_method == 'Cheque') print 'selected'; ?>>Cheque</option>
										  <option value="CC" <?php if($orderDetails->payment_method == 'CC') print 'selected'; ?>>CC</option>
										</select>
									</div>	
								</div>
							</div>	
							<div class="col-sm-12">
								<div class="form-group">
									<label for="comments">Comments :</label>
									<textarea rows="4" name="comments" id="comments" class="form-control"><?php print $orderDetails->comments; ?></textarea>
								</div>
							</div>	
						</div>	
					</div>	
				</div>	
			</div>
		</div>
		<input type="hidden" name="txtpostCharges" id="txtpostCharges" value="Save & Post">
	</form>
	
	<div id="frame_search_modal" class="modal fade" role="dialog">
		<div class="modal-dialog modal_90">
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<div class="row">
						<div class="col-sm-5">
							<h4 class="modal-title">Manufacturer Search</h4>
						</div>	
						<div class="col-sm-3 col-sm-offset-3 text-right">
							<form id="vendor_search_form">
								<input type="hidden" name="vendor_name_val" value="" />
								<div class="input-group">
									<input type="text" id="txt_to_search" name="Vendor_name" value="" class="form-control" onkeydown="if (event.keyCode == 13)return false;">
									<label class="input-group-addon pointer" onclick="chkNew('txt_to_search')">
										<span class="glyphicon glyphicon-search"></span>
									</label>
								</div>
							</form>
						</div>	
						<div class="col-sm-1">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>	
					</div>
				</div>
				<div class="modal-body"></div>	
			</div>	
		</div>	
	</div>
</div>