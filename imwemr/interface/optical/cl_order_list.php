<?php 
	include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php');
	$authUserID = $optical_obj->auth_id;
	$displayCurrDate = date("m-d-Y");
	
	// GET LENSE CODES AND COLORS in ARRAY
	$arrLensCode	=	getLensCodeArr(false);
	$arrLensColor	=	getLensColorArr(false);
	//---------------------------------

	//GET ALL LENS MANUFACTURER IN ARRAY
	$arrLensManuf = getLensManufacturer();
	
	$orderMainStatus = $_REQUEST["orderMainStatus"];
	$whereStatusQuery = "";
	if($orderMainStatus != "" && $orderMainStatus != "1"){
		$whereStatusQuery="and order_status='".$orderMainStatus."'";
	}else{
		$orderMainStatus='1';
		$whereStatusQuery="";
	}
	
	//START CODE TO GET AUTHORIZATION NUMBER
	$unusedAuthorization='';
	$AuthAmount='';
	$authInfoQry = "
	SELECT patient_auth.auth_name,patient_auth.AuthAmount 
	FROM patient_auth,insurance_data
	WHERE insurance_data.pid='".$optical_obj->patient_id."'
	AND insurance_data.type='primary'
	AND insurance_data.auth_required='Yes'
	AND insurance_data.id=patient_auth.ins_data_id
	ORDER BY patient_auth.a_id DESC 
	";
	$authInfoRes = imw_query($authInfoQry) or die(imw_error());				
	$authInfoNumRow = imw_num_rows($authInfoRes);
	if($authInfoNumRow<=0) {
		$authInfoQry = "
		SELECT patient_auth.auth_name,patient_auth.AuthAmount
		FROM patient_auth,insurance_data
		WHERE insurance_data.pid='".$optical_obj->patient_id."'
		AND insurance_data.type='secondary'
		AND insurance_data.auth_required='Yes'
		AND insurance_data.id=patient_auth.ins_data_id
		ORDER BY patient_auth.a_id DESC 
		";
		$authInfoRes = imw_query($authInfoQry) or die(imw_error());			
		$authInfoNumRow = imw_num_rows($authInfoRes);
	}

	if($authInfoNumRow>0) {
		$authInfoRow = imw_fetch_array($authInfoRes);
	}
	
	if(empty($optical_obj->patient_id) === true){
		header('location:search_patient_result.php');
	}
	
	//Get pt. details
	if(empty($optical_obj->patient_id) === false){
		$row_address = $optical_obj->get_pt_data_row($optical_obj->patient_id);
		
		//--- GET PATIENT NAME ---
		$patNameArr = array();
		$patNameArr['LAST_NAME'] = $row_address["lname"];
		$patNameArr['FIRST_NAME'] = $row_address["fname"];
		$patNameArr['MIDDLE_NAME'] = $row_address["mname"];
		$patientName = changeNameFormat($patNameArr);
	}
	
	$spaceNbsp = '&nbsp;';
	$statusOptions=array('Pending', 'Ordered', 'Received', 'Dispensed');
	
	$orderIDS = array();	$orderIDSStr='';
	$orderQuery= imw_query("SELECT * FROM clprintorder_master	WHERE patient_id='".$optical_obj->patient_id."' $whereStatusQuery ORDER BY clprintorder_master.print_order_savedatetime DESC");
	while($row = imw_fetch_array($orderQuery)){
		$clOrderMaster[] = $row;
	}
	$orderMasterNumRow = sizeof($clOrderMaster);
	if($orderMasterNumRow > 0){
		for($i=0;$i<$orderMasterNumRow;$i++)
		{
			$orderIDS[] = $clOrderMaster[$i]['print_order_id'];
		}
		$orderIDSStr  =implode(",",$orderIDS);
	}
	
	//START CODE TO GET ORDER HISTORY
	$currentWorksheetExist = 0;
	$arr_clDetId = array();
	$clOrderResOD = array();
	$clOrderResOS = array();
	$clOrderResArr = array();
	$clws_id = '';
	if($optical_obj->patient_id && $orderIDSStr!=''){
		$orderHxQuery = imw_query("SELECT clprintorder_det.* FROM clprintorder_master
		LEFT JOIN clprintorder_det ON clprintorder_det.print_order_id =  clprintorder_master.print_order_id 
		WHERE clprintorder_det.print_order_id IN(".$orderIDSStr.") ORDER BY clprintorder_master.print_order_savedatetime DESC,clprintorder_det.id ASC");
		while($row = imw_fetch_array($orderHxQuery)){
			$currentWorksheetExist = 1;
			$clOrderRes[] = $row;
		}
		$orderHxNumRow = sizeof($clOrderRes);
		$clws_id = 	$clOrderRes[0]['clws_id'];
		for($i=0;$i< $orderHxNumRow; $i++)
		{
			if($clOrderRes[$i]['LensBoxOD_ID']!='0')
			{
				$clOrderResOD[$clOrderRes[$i]['print_order_id']][] = $clOrderRes[$i];
			}
			if($clOrderRes[$i]['LensBoxOS_ID']!='0')
			{
				$clOrderResOS[$clOrderRes[$i]['print_order_id']][] = $clOrderRes[$i];
			}

			if($clOrderRes[$i]['cl_det_id']!='' && $clOrderRes[$i]['cl_det_id']!=0){
				$arr_clDetId[] = 	$clOrderRes[$i]['cl_det_id'];
			}
		}
		$strClDetID = implode(",",$arr_clDetId);
	}
	
	$CLResDataArr = array();
	if($strClDetID!=''){
		$workSheetQuery = imw_query("SELECT cm.clGrp, cm.clws_type, cm.clws_trial_number, cm.cpt_evaluation_fit_refit, cdet.* FROM contactlensmaster cm 
		LEFT JOIN contactlensworksheet_det cdet ON cdet.clws_id = cm.clws_id 
		WHERE cdet.id IN(".$strClDetID.")");
		if(imw_num_rows($workSheetQuery) > 0){
			$clResSize = imw_num_rows($workSheetQuery);
			while($CLResData = imw_fetch_array($workSheetQuery)){
				$clwID = $CLResData['clws_id'];
				$id = $CLResData['id'];
				$CLResDataArr[$clwID][$id] =  $CLResData;
			}
		}
	}
	$status_opt = '';
?>
<div class="row pt10">
	<div class="col-sm-12 bg-info" style="padding:0px 10px 0px 10px">
		<div class="pd5"><div class="row">
			<div class="col-sm-4 ">
				<label>CL Order List</label>
			</div>	
			<div class="col-sm-4  text-center">
				<label class="text_purple pointer" onClick="showClSupplyOrderFromOptical();">New CL-Order</label>
			</div>	
			<div class="col-sm-2 col-sm-offset-2 pull-right">
				<select name="orderMainStatus" id="orderMainStatus" onChange="loadStatusForOrder(this.value);" class="selectpicker" data-width="100%">
					<option value="1">Show All</option>
						<?php 
						foreach($statusOptions as $key=>$val){
							$sel="";
							if($orderMainStatus == $val){$sel="selected";}
							$status_opt .= "<option value='$val' $sel >$val</option>";
						}
						echo $status_opt;	
					?>
				</select>
			</div>	
		</div>	</div>
	</div>
	<div class="col-sm-12 pt10">
		<input type="hidden" name="contactLens_clws_id" id="contactLens_clws_id" value="<?php echo $contactLens_clws_id;?>" />
		<input type="hidden" name="contactLens_Dos" id="contactLens_Dos" value="<?php echo $contactLens_Dos;?>" />
		<input type="hidden" name="currentWorksheetid" id="currentWorksheetid" value="<?php echo $currentWorksheetid;?>" />
		<input type="hidden" name="currentWorksheetExist" id="currentWorksheetExist" value="<?php echo $currentWorksheetExist; ?>" />
		<div class="row">
			<table class="table table-bordered table-hover table-striped">
				<tr class="grythead text-center">
					<th>Date</th>
					<th>Eye</th>
					<th>Type</th>
					<th>Color</th>
					<th>LC</th>
					<th>S</th>
					<th>C</th>
					<th>A</th>
					<th>Dia</th>
					<th>BC</th>
					<th>Add</th>
					<th>Qty.</th>
					<th>Cost</th>
					<th>Dis</th>
					<th>Balance</th>
					<th>CL Exam.</th>
					<th>Auth Amt</th>                                                                        
					<th>Total</th>
					<th>Auth <?php getHashOrNo();?></th>
					<th>Status</th>
					<th>Opr</th>	
				</tr>
				<?php 
					$print_order_id = $print_order_idOLD ='';
					if($orderHxNumRow>0){
						for($i =0; $i<$orderMasterNumRow; $i++){
							$main_row = '';
							$deliveryAt = '';	$clExamAmt='';
							$clws_id = $clOrderMaster[$i]['clws_id'];
							$print_order_id  = $clOrderMaster[$i]['print_order_id'];						
							
							$unusedAuthorization = $clOrderMaster[$i]['auth_number'];
							
							$AuthAmount = $clOrderMaster[$i]['auth_amount'];
							$print_AuthAmount = '';
							if($AuthAmount!='' && $AuthAmount!='0') { $print_AuthAmount = $dlr.$AuthAmount;}
							
							
							$orderHxDateTime = $clOrderMaster[$i]['print_order_savedatetime'];
							if($orderHxDateTime!='0000-00-00') {
								$orderHxDate = $optical_obj->displayDateFormatMMDDYY($orderHxDateTime);
							}
							
							$orderHxOperatorId = $clOrderMaster[$i]['operator_id'];
							$operatorInitial='';
							if($orderHxOperatorId) {
								$operatorInitial = $optical_obj->getUsrNme($orderHxOperatorId,$initial='');
							}
							
							$displayOtherCmnt=' ';
							if($clOrderMaster[$i]['OrderedComment'])  {  $displayOtherCmnt.=$clOrderMaster[$i]['OrderedComment'].'<br><br>';}	//'Date Ordered: '.		
							if($clOrderMaster[$i]['ReceivedComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['ReceivedComment'].'<br><br>';}//'Date Received: '.
							if($clOrderMaster[$i]['NotifiedComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['NotifiedComment'].'<br><br>';}//'Date Notified: '.			
							if($clOrderMaster[$i]['PickedUpComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['PickedUpComment'].'<br><br>';}//'Date Picked Up: '.		
							
							if($clOrderMaster[$i]['checkBoxShipToHomeAddress']=='PtPickYes'){
								$deliveryAt = 'Office';
							}else if($clOrderMaster[$i]['checkBoxShipToHomeAddress']=='HomeAddressYes'){
								$deliveryAt = "Home<br><strong>Address:</strong><br>".$clOrderMaster[$i]['ShipToHomeAddress'];
							}
							
							$orderStatus = $clOrderMaster[$i]['order_status'];
							
							$row_status_opt = '';
							foreach($statusOptions as $key=>$val){
								 $sel="";
								if($orderStatus==$val){$sel="selected";}
								$row_status_opt .= "<option value='$val' $sel >$val</option>";
							}
							
							//Settng rowspan for elements
							$rowspan = (sizeof($clOrderResOD[$print_order_id])+sizeof($clOrderResOS[$print_order_id]));
							$dlr='$';
							$od_row_str = '';
							if(sizeof($clOrderResOD[$print_order_id])>=0){
								$j=1;
								foreach($clOrderResOD[$print_order_id] as $clOrdData){
									if($print_order_id==$clOrdData['print_order_id']){
										$cl_det_id = $clOrdData['cl_det_id'];
										$orderHxType				='';
										$orderHxS					='';
										$orderHxC					='';
										$orderHxA					='';
										$orderHxDia					='';
										$orderHxBc					='';
										$orderHxAdd					='';
										//START CODE TO GET COLOR-NAME
										$colorNameList='';	$lensNameList='';
										$colorNameList 	= $arrLensColor[$clOrdData['colorNameIdList']];
										$lensNameList 	= $arrLensCode[$clOrdData['lensNameIdList']];
										
										$orderHxType= $arrLensManuf[$clOrdData['LensBoxOD_ID']]['det'];
										
										$clwSize = sizeof($CLResDataArr[$clws_id][$cl_det_id]);
										if($clwSize > 0){
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='scl'){
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['SclsphereOD'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['SclCylinderOD'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['SclaxisOD']."&#176;";
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['SclDiameterOD'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['SclBcurveOD'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['SclAddOD'];
											}
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='rgp'){
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpPowerOD'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCylinderOD'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpAxisOD'];
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpDiameterOD'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpBCOD'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpAddOD'];
											}
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='cust_rgp'){
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomPowerOD'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomCylinderOD'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAxisOD'];
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomDiameterOD'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomBCOD'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAddOD'];
											}

											$LabelsTrial="";
											if($CLResDataArr[$clws_id][$cl_det_id]['clws_type']=="Current Trial"){
												$LabelsTrial="<b> (Trial ".$CLResDataArr[$clws_id][$cl_det_id]['clws_trial_number'].")</b>";
											}
											$orderHxType.=$LabelsTrial;	
											
											//GET CL EXAM AMOUNT
											$clExamAmt = $CLResDataArr[$clws_id][$cl_det_id]['cpt_evaluation_fit_refit'];
										}	
											$sub_total_OD = $discount_OD = $balanace_OD = '&nbsp;';
											if($clOrdData['SubTotalOD']>0){
												$sub_total_OD = $dlr.$clOrdData['SubTotalOD'];
											}
											
											if($clOrdData['DiscountOD']>0){
												$discount_OD = $dlr.$clOrdData['DiscountOD'];
											}
											
											if($clOrdData['BalanceOD']>0){
												$balanace_OD = $dlr.$clOrdData['BalanceOD'];
											}
											
											
											//Fields tht will be appended after the site data
											$oth_fields = '<td rowspan='.$rowspan.'>'.$clExamAmt.'</td>
											<td rowspan='.$rowspan.' class="text-nowrap">'.$print_AuthAmount.'</td>        
											<td rowspan='.$rowspan.'>'.$dlr.$clOrderMaster[$i]['totalCharges'].'</td>
											<td rowspan='.$rowspan.'>'.$unusedAuthorization.'</td>
											<td rowspan='.$rowspan.'>
												<select class="selectpicker" data-width="100%" name="orderStatus_'.$print_order_id.'" id="orderStatus_'.$print_order_id.'" onChange="changeStatusForOrder('.$print_order_id.',this.value);">
													'.$row_status_opt.'	
												</select>
											</td>
											<td rowspan='.$rowspan.' class="text-left">'.$operatorInitial.'</td>';
											
											
											if($j > 1){
												$od_row_str .= '<tr>';
											}
											$od_row_str .= '
												<td class="od"><strong>OD</strong></td>
												<td>'.$orderHxType.'</td>	
												<td>'.$colorNameList.'</td>	
												<td>'.$lensNameList.'</td>	
												<td>'.$orderHxS.'</td>	
												<td>'.$orderHxC.'</td>	
												<td>'.$orderHxA.'</td>	
												<td>'.$orderHxDia.'</td>	
												<td>'.$orderHxBc.'</td>	
												<td>'.$orderHxAdd.'</td>
												<td>'.$clOrdData['QtyOD'].'</td>
												<td>'.$sub_total_OD.'</td>
												<td>'.$discount_OD.'</td>
												<td>'.$balanace_OD.'</td>
											';
											if($j == 1){
												$od_row_str .= $oth_fields;
											}
											
											$od_row_str .= '</tr>';
									}
									$j++;
								}
							}
							
							//OS Row section
							$os_row_str = '';
							if(sizeof($clOrderResOS[$print_order_id])>0){
								$j=1;
								foreach($clOrderResOS[$print_order_id] as $clOrdData){
									if($print_order_id==$clOrdData['print_order_id']){
										$cl_det_id = $clOrdData['cl_det_id'];
										$orderHxType				='';
										$orderHxS					='';
										$orderHxC					='';
										$orderHxA					='';
										$orderHxDia					='';
										$orderHxBc					='';
										$orderHxAdd					='';
										//START CODE TO GET COLOR-NAME
										$colorNameList='';	$lensNameList='';
										$colorNameList 	= $arrLensColor[$clOrdData['colorNameIdListOS']];
										$lensNameList 	= $arrLensCode[$clOrdData['lensNameIdListOS']];
										
										$orderHxType= $arrLensManuf[$clOrdData['LensBoxOS_ID']]['det'];
										
										$clwSize = sizeof($CLResDataArr[$clws_id][$cl_det_id]);
										
										if($clwSize > 0){
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='scl'){
												//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['SclTypeOS'];
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['SclsphereOS'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['SclCylinderOS'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['SclaxisOS']."&#176;";
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['SclDiameterOS'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['SclBcurveOS'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['SclAddOS'];
											}
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='rgp'){
												//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpTypeOS'];
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpPowerOS'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCylinderOS'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpAxisOS'];
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpDiameterOS'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpBCOS'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpAddOS'];
											}
											if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='cust_rgp'){
												//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomTypeOS'];
												$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomPowerOS'];
												$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomCylinderOS'];
												$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAxisOS'];
												$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomDiameterOS'];
												$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomBCOS'];
												$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAddOS'];
											}

											$LabelsTrial="";
											if($CLResDataArr[$clws_id][$cl_det_id]['clws_type']=="Current Trial"){
												$LabelsTrial="<b> (Trial ".$CLResDataArr[$clws_id][$cl_det_id]['clws_trial_number'].")</b>";
											}
											$orderHxType.=$LabelsTrial;	

											//GET CL EXAM AMOUNT
											$clExamAmt = $CLResDataArr[$clws_id][$cl_det_id]['cpt_evaluation_fit_refit'];
										}
										$SubTotalOS = '';
										$DiscountOS = '';
										$BalanceOS = '';
										if($clOrdData['SubTotalOS']>0){ $SubTotalOD = $dlr.$clOrdData['SubTotalOS'];}
										if($clOrdData['DiscountOS']>0){ $DiscountOD = $dlr.$clOrdData['DiscountOS']; }
										if($clOrdData['BalanceOS']!='' && $clOrdData['BalanceOS']!=0){ $BalanceOS = $dlr.$clOrdData['BalanceOS'];}
										if(sizeof($clOrderResOD[$print_order_id]) > 0){
											$os_row_str .= '<tr>';
										}
										//Fields tht will be appended after the site data
											$oth_fields = '<td rowspan='.$rowspan.'>'.$clExamAmt.'</td>
											<td rowspan='.$rowspan.' class="text-nowrap">'.$print_AuthAmount.'</td>        
											<td rowspan='.$rowspan.'>'.$dlr.$clOrderMaster[$i]['totalCharges'].'</td>
											<td rowspan='.$rowspan.'>'.$unusedAuthorization.'</td>
											<td rowspan='.$rowspan.'>
												<select class="selectpicker" data-width="100%" name="orderStatus_'.$print_order_id.'" id="orderStatus_'.$print_order_id.'" onChange="changeStatusForOrder('.$print_order_id.',this.value);">
													'.$row_status_opt.'	
												</select>
											</td>
											<td rowspan='.$rowspan.' class="text-left">'.$operatorInitial.'</td>';
										$os_row_str .='
											<td class="text-left os"><strong>OS</strong></td>
											<td class="text-left">'.$orderHxType.'</td>
											<td class="text-left">'.$colorNameList.'</td>
											<td class="text-left">'.$lensNameList.'</td>
											<td>'.$orderHxS.'</td>
											<td>'.$orderHxC.'</td>
											<td>'.$orderHxA.'</td>
											<td>'.$orderHxDia.'</td>
											<td>'.$orderHxBc.'</td>
											<td>'.$orderHxAdd.'</td>
											<td>'.$clOrdData['QtyOS'].'</td>
											<td>'.$SubTotalOD.'</td>
											<td>'.$DiscountOD.'</td>
											<td>'.$BalanceOD.'</td>';
											if($j == 1 && sizeof($clOrderResOD[$print_order_id]) == 0){
												$os_row_str .= $oth_fields;
											}
										$os_row_str	.= '</tr>';	
									}
									$j++;
								}
							}
							
							//Main Row
							$main_row .= '
								<tr>
									<td rowspan='.$rowspan.' class="text-nowrap">'.$orderHxDate.$od_row_str.$os_row_str.'</td>
								</tr>';
							echo $main_row;
							$od_row_str = $os_row_str = '';
						}
					}else{
						echo '<tr><td class="text-center text-info" colspan="21">No record </td></tr>';
					}
				?>
			</table>	
		</div>	
	</div>	
</div>
<script  type="text/javascript">
function changeStatusForOrder(OrderId,StatusVal){
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
		data:'ajax_request=yes&update_order_id='+OrderId+"&update_order_status="+StatusVal+"&orderMainStatus="+StatusVal,
		type:'POST',
		success:function(response){
			if($.trim(response) != '' && response > 0){
				window.location.reload();
			}
		}
	});
}
function loadStatusForOrder(StatusVal){
	if(StatusVal!=""){
		parent.show_loading_image('block');
		window.location.href = top.JS_WEB_ROOT_PATH+'/interface/optical/index.php?showpage=cl_order_list&orderMainStatus='+StatusVal;
	}
}

function showClSupplyOrderFromOptical() {
	var clwsID="";
	var dos="";
	var sheetExist="0";
	if(document.getElementById("currentWorksheetExist")){
		sheetExist=document.getElementById("currentWorksheetExist").value;
		clwsID=document.getElementById("contactLens_clws_id").value;
		dos=document.getElementById("contactLens_Dos").value;
	}
	if(sheetExist==1){
		var SupplyUrl="../chart_notes/print_order.php?newOrder=1&clws_id="+clwsID+"&dos="+dos+"&callFrom=order";
		window.open(SupplyUrl,"ClSupplyOrderWindow","width=1000,scrollbars=0,height=435,top=2,left=0");
	}else{
		top.fAlert("Please create work sheet first.");
	}
}
</script>