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

require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");

//require_once("../../Billing/billing_globals.php");
//------SET TEMP KEY CHECKBOX TO 1 IN CHECKIN SCREEN---------
imw_query("UPDATE patient_data SET temp_key_chk_datetime = '".date('Y-m-d H:i:s')."', temp_key_chk_val = '1', temp_key_chk_opr_id = '".$_SESSION['authId']."' WHERE id = '".$_SESSION['patient']."'");

ob_start();
$operator_id=$_SESSION['authId'];
//--- GET CHECK IN / OUT PAYMENT DETAILS ---
$chk_id = $_REQUEST['id'];
$chk_pay_query = "select check_in_out_payment.created_by, check_in_out_payment.total_payment, 
			check_in_out_payment.payment_method,check_in_out_payment.check_no,check_in_out_payment.patient_id,
			check_in_out_payment.cc_type,check_in_out_payment.cc_no,check_in_out_payment.cc_expire_date,check_in_out_payment.co_comment, 
			check_in_out_payment_details.item_id, check_in_out_payment_details.item_payment,check_in_out_payment.ci_comments,
			date_format(check_in_out_payment.created_on, '%m-%d-%Y') as created_date,
			check_in_out_payment_details.payment_type
			from check_in_out_payment join check_in_out_payment_details on 
			check_in_out_payment_details.payment_id = check_in_out_payment.payment_id
			where check_in_out_payment.sch_id = '$chk_id'
			and check_in_out_payment.del_status = '0' 
			and check_in_out_payment_details.status = '0'
			order by check_in_out_payment_details.item_id";
$chek_qry_res_qry = imw_query($chk_pay_query);	
$chk_creted_date="";		
while($checkQryRow = imw_fetch_array($chek_qry_res_qry)){
	$checkQryRes[] = $checkQryRow;
}

for($i=0;$i<count($checkQryRes);$i++){
	if($checkQryRes[$i]['created_date']!="" && $checkQryRes[$i]['created_date']!="00-00-0000"){
		$chk_creted_date_arr[$checkQryRes[$i]['payment_type']] = $checkQryRes[$i]['created_date'];
		$created_by_arr[$checkQryRes[$i]['payment_type']] = $checkQryRes[$i]['created_by'];
	}
}

if($chk_creted_date_arr['checkin']!=""){
	$chk_creted_date=$chk_creted_date_arr['checkin'];
	$created_by=$created_by_arr['checkin'];
}else{
	$chk_creted_date=$chk_creted_date_arr['checkout'];
	$created_by=$created_by_arr['checkout'];
}
//--------Get HQ facility from Server Location----------//
$pat_hq_facility=$qry_fac_id="";
if($GLOBALS["LOCAL_SERVER"]=="Boston"){
	$sch_appt_qry="Select sa_facility_id from schedule_appointments where id='".$chk_id."' LIMIT 0,1";
	$res_appt_qry=imw_query($sch_appt_qry);
	$row_appt_qry=imw_fetch_assoc($res_appt_qry);
	$pat_fac_id=$row_appt_qry['sa_facility_id'];

	$arr_hq_facility=$arr_fac_loc=array();
	$qry_facility="select id,facility_type,server_location from facility";
	$res_facility=imw_query($qry_facility);
	while($row_facility=imw_fetch_assoc($res_facility)){
		$fac_id=$row_facility['id'];
		$fac_type=$row_facility['facility_type'];
		$fac_loc=$row_facility['server_location'];
		$arr_fac_loc[$fac_id]=$fac_loc;
		if($fac_type=="1"){
			$arr_hq_facility[$fac_loc]=$fac_id;
		}
	}
	$pat_fac_loc=$arr_fac_loc[$pat_fac_id];
	$pat_hq_facility=$arr_hq_facility[$pat_fac_loc];
	$qry_fac_id="";
	if($pat_hq_facility){
		$qry_fac_id=" AND facility.id ='".$pat_hq_facility."'";
	}
}
//===BELOW CODE USED TO DISPLAY THE SINGLE GROUP INFO BASED ON PT. POS FACILITY===
if($qry_fac_id==""){
	$schedule_appt_id="";
	$schedule_appt_id = $_REQUEST['id'];
	$sch_appt_qry="select sa_facility_id from schedule_appointments where id='".$schedule_appt_id."'";
	$res_facility=imw_query($sch_appt_qry);
	$row_facility=imw_fetch_assoc($res_facility);

	if($row_facility['sa_facility_id']){
		$qry_fac_id=" AND facility.id ='".$row_facility['sa_facility_id']."'";
	}
}

//--- GET PATIENT DETAILS ---
$patient_id = $checkQryRes[0]['patient_id'];
if($patient_id>0){
	$patient_id=$patient_id;
}else{
	$patient_id=$_SESSION['patient'];
}
$pat_query = "select * from patient_data where id = '$patient_id'";
$pat_qry = imw_query($pat_query);
$patQryRes = imw_fetch_assoc($pat_qry);
$pat_name_arr = array();
$pat_name_arr['LAST_NAME'] = $patQryRes['lname'];
$pat_name_arr['FIRST_NAME'] = $patQryRes['fname'];
$pat_name_arr['MIDDLE_NAME'] = $patQryRes['mname'];
$patientName = changeNameFormat($pat_name_arr);
$phone_home = $patQryRes['phone_home'];
$other_phone = $patQryRes['phone_biz'];
if(empty($other_phone) === true){
	$other_phone = $patQryRes['phone_cell'];
}
$patient_address = $patQryRes['street'];
if(empty($patQryRes['street2']) === false){
	$patient_address .= ', '.$patQryRes['street2'];
}
$patient_address .= '<br>';
$patient_address .= $patQryRes['city'].', ';
$patient_address .= $patQryRes['state'].' ';
$patient_address .= $patQryRes['postal_code'];

$default_facility = $patQryRes[0]['default_facility'];
 

//--- GET CREATED BY PROVIDER NAME ---
//$created_by = $checkQryRes[0]['created_by'];
//$created_by = $operator_id;
$pro_query = "select lname,fname,mname from users where id = '$created_by'";
$pro_qry = imw_query($pro_query);
$proQryRes = imw_fetch_array($pro_qry);
$pro_name_arr = array();
$pro_name_arr['LAST_NAME'] = $proQryRes['lname'];
$pro_name_arr['FIRST_NAME'] = $proQryRes['fname'];
//$pro_name_arr['MIDDLE_NAME'] = $proQryRes[0]['mname'];
//$chk_creted_by = $objManageData->__changeNameFormat($pro_name_arr);
$chk_creted_by  = strtoupper(substr($pro_name_arr['FIRST_NAME'],0,1)).strtoupper(substr($pro_name_arr['LAST_NAME'],0,1));
 
//--- GET CHECK IN FIELDS NAME ----
$check_in_fields_qry = "select id, item_name from check_in_out_fields where item_name != '' and item_show > 0";
$chk_in_field_qry = imw_query($check_in_fields_qry);
$fieldNameArr = array();
while($checkInFieldsQryRes = imw_fetch_array($chk_in_field_qry)){
	$item_id = $checkInFieldsQryRes['id'];
	$fieldNameArr[$item_id] = $checkInFieldsQryRes['item_name'];
}
/*$qryGroup = "select groups_new.* 
				from groups_new 
				join facility on facility.default_group = groups_new.gro_id
				where facility.fac_prac_code ='$default_facility'";*/
$qryGroup = "select groups_new.name,groups_new.group_Address1,groups_new.group_Address2,groups_new.group_Telephone,groups_new.group_Fax,
groups_new.group_Federal_EIN,groups_new.group_State,groups_new.group_City,groups_new.group_Zip,groups_new.zip_ext,
facility.billing_attention,facility.street,facility.city,facility.state,facility.postal_code,facility.zip_ext,facility.phone,facility.fax
				from groups_new 
				join facility on facility.default_group = groups_new.gro_id
				where facility.billing_location ='1' and groups_new.del_status='0' ".$qry_fac_id;
$qry_group_query = imw_query($qryGroup);				
while($groupQryRes = imw_fetch_array($qry_group_query)){
	$GroupName_arr[$groupQryRes['name']] = $groupQryRes['name'];
	$group_Address2="";
	if($groupQryRes['group_Address2']!="" && in_array(strtolower($billing_global_server_name), array('centerforsight'))){
		$group_Address2 = ', '.$groupQryRes['group_Address2'];
	}
	$address_arr[$groupQryRes['group_Address1'].$group_Address2] = $groupQryRes['group_Address1'].$group_Address2;
	if($groupQryRes['group_Telephone']!=""){
		$group_Telephone_arr[$groupQryRes['group_Telephone']] = core_phone_format($groupQryRes['group_Telephone']);
	}
	if($groupQryRes['group_Fax']!=""){
		$group_Fax_arr[$groupQryRes['group_Fax']] = core_phone_format($groupQryRes['group_Fax']);
	}
	$group_Federal_EIN_arr[$groupQryRes['group_Federal_EIN']] = $groupQryRes['group_Federal_EIN'];
	$group_State = $groupQryRes['group_State'];
	$group_City = $groupQryRes['group_City'];
	$group_Zip = $groupQryRes['group_Zip'];
	if($groupQryRes['zip_ext']!=""){
		$group_Zip= $group_Zip.'-'.$groupQryRes['zip_ext'];
	}
	
	$fac_name = $groupQryRes['billing_attention'];
	$fac_street = $groupQryRes['street'];
	$fac_state = $groupQryRes['state'];
	$fac_city = $groupQryRes['city'];
	$fac_postal_code = $groupQryRes['postal_code'];
	if($groupQryRes['zip_ext']!=""){
		$fac_postal_code = $fac_postal_code.'-'.$groupQryRes['zip_ext'];
	}
	$fac_phone = $groupQryRes['phone'];
	$fac_fax = $groupQryRes['fax'];
}
$GroupName=implode(',<br>',$GroupName_arr);
$group_Telephone=implode(', ',$group_Telephone_arr);
$group_Fax=implode(', ',$group_Fax_arr);
$group_address=implode(',<br>',$address_arr);
$group_Federal_EIN=implode(', ',$group_Federal_EIN_arr);
$group_address_csz=$group_City.', '.$group_State.' '.$group_Zip; 
$fac_address_csz=$fac_city.', '.$fac_state.' '.$fac_postal_code; 

$posFacGroup = isPosFacGroupEnabled();
if($posFacGroup){
$qryFac = "SELECT posfacilitygroup_id FROM `pos_facilityies_tbl` WHERE pos_facility_id IN (select fac_prac_code from facility where facility.billing_location ='1' ".$qry_fac_id.")";
$qry_fac_query = imw_query($qryFac);				
$facQryRes = imw_fetch_assoc($qry_fac_query);
$posGrpFacId = $facQryRes['posfacilitygroup_id'];

$qryPosGrp = "SELECT pos_facility_group as name, fac_group_address as group_Address1, fac_group_address as group_Address2, 
fac_group_city as group_City,
fac_group_state as fac_group_state,
fac_group_zip AS group_Zip,
fac_zip_ext	AS zip_ext,
fac_phone AS group_Telephone,
fac_fax, fac_tax_id
FROM `pos_facility_group` where pos_fac_grp_id =".$posGrpFacId." AND delete_status !=1";
$qry_pos_grp_query = imw_query($qryPosGrp);				
$posQryRes = imw_fetch_assoc($qry_pos_grp_query);
$posFacAdr1 = $posQryRes['group_Address1'];
$posFacAdr2 = $posQryRes['group_Address2'];
if(trim($posFacAdr1)){
	$posFacAdr = $posFacAdr1;
}else{
	$posFacAdr = $posFacAdr2;
}
$posFacAddress_csz = $posQryRes['group_City'].' '.$posQryRes['fac_group_state'].' '.$posQryRes['group_Zip'];
}
?>
<table width="100%" cellpadding="4" cellspacing="0" border="0">
	<?php if(strtolower($billing_global_server_name)=="edison"){ ?>
    <tr>
    	<td colspan="6" style="padding-bottom:10px;"> <img src="https://imwcloud.mednetworx.com/edison/data/edison/gn_images/Edison_ophthalmology_logo.jpg" ></td>
    </tr>
    <?php } ?>
    <tr>
        <td class="text_b_w" height="22" align="left" colspan="5">&nbsp;Payment Receipt</td>
        <td class="text_b_w" height="22" align="left" colspan="3"><?php echo get_date_format(date('Y-m-d')); ?></td>
        
    </tr>
    <tr><td colspan="6" style="height:3px;"></td></tr>
    <?php if($GroupName){ ?>
    	<tr>
        	<td colspan="6" class="gray_bg" valign="top">
            	<table>
                	<tr>
                        <td class="gray_bg" valign="top" width="280">
                            <table>
                                <tr>
                                    <td class="text_10b gray_bg">Phone :</td>
                                    <td class="text_10 gray_bg">
                                        <?php if(in_array(strtolower($billing_global_server_name), array('centerforsight'))){?>
                                            For appointments call <?php echo $group_Telephone; ?><br/>
                                            For billing questions call <?php echo $group_Telephone; ?> press option 4
                                        <?php }elseif(constant("SHOW_PR_FAC_ADDR")=="YES"){ 
												echo (trim($fac_phone)) ? $fac_phone : $group_Telephone;
												}elseif($posFacGroup){
													echo $posQryRes['group_Telephone'];
												}else {
													echo $group_Telephone;
												}  ?>
                                    </td>                        
                                </tr>
                                <tr>
                                    <td class="text_10b gray_bg">Fax :</td>
                                    <td class="text_10 gray_bg">
                                    <?php
										if(constant("SHOW_PR_FAC_ADDR")=="YES"){ 
											echo (trim($fac_fax)) ? $fac_fax : $group_Fax;
										}elseif($posFacGroup){
											echo (trim($posQryRes['fac_fax'])) ?  $posQryRes['fac_fax'] : $group_Fax;
										}else {
											echo $group_Fax;
									    }
									?>
                                    </td> 
                                </tr>
                            </table>                 
                        </td>
                        <?php 
							$middle_box_width="230px";
							if(in_array(strtolower($billing_global_server_name), array('centerforsight'))){ 
								$middle_box_width="172px";
							}
						?>
                        <td class="gray_bg" valign="top">
                            <table>
                                <tr>
                                    <td class="text_10b gray_bg" colspan="2" style="width:<?php echo $middle_box_width; ?>;">
									<?php 
										if($posFacGroup){
											echo (trim($posQryRes['name'])) ?  $posQryRes['name'] : $GroupName;
										}else {
											echo $GroupName;
									    }
									?>
								</td>
                                </tr>
                                <tr>
                                     <td class="text_10b gray_bg" valign="top" nowrap>Tax Id : </td>
                                     <td class="text_10 gray_bg" valign="top" align="left">
									 <?php 
										if($posFacGroup){
											echo (trim($posQryRes['fac_tax_id'])) ?  $posQryRes['fac_tax_id'] : $group_Federal_EIN;
										}else {
											echo $group_Federal_EIN;
									    }
									 ?>
									 </td>
                                </tr>
                            </table>                 
                        </td>
                        <td class="gray_bg" valign="top">
                            <table>
                                <?php if(in_array(strtolower($billing_global_server_name), array('centerforsight'))){?>
                                    <tr>
                                        <td class="text_10b gray_bg" valign="top">Center for Sight</td>
                                    </tr>
                                    <tr>
                                        <td class="text_10b gray_bg" valign="top">Main Office : </td>
                                    </tr>
                                    <tr>
                                        <td class="text_10 gray_bg" valign="top"><?php echo $fac_street; ?><br /><?php echo $fac_address_csz; ?></td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td class="text_10b gray_bg" valign="top">Center for Sight</td>
                                    </tr>
                                    <tr>
                                        <td class="text_10b gray_bg" valign="top">Payment Address : </td>
                                    </tr>
                                     <tr>
                                        <td class="text_10 gray_bg" valign="top" width="220"><?php echo $group_address; ?><br /><?php echo $group_address_csz; ?></td>
                                    </tr>
                                <?php }else if(constant("SHOW_PR_FAC_ADDR")=="YES"){ ?>   
                                     <tr>
                                        <td class="text_10b gray_bg" valign="top">Address : </td>
                                        <td width="160" class="text_10 gray_bg" valign="top"><?php echo $fac_street; ?><br /><?php echo $fac_address_csz; ?></td>
                                    </tr>
                                <?php }else if($posFacGroup){ ?>   
                                     <tr>
                                        <td class="text_10b gray_bg" valign="top">Address : </td>
                                        <td width="160" class="text_10 gray_bg" valign="top"><?php echo $posFacAdr; ?><br /><?php echo $posFacAddress_csz; ?></td>
                                    </tr>
                                <?php } else{ ?>   
                                     <tr>
                                        <td class="text_10b gray_bg" valign="top">Address : </td>
                                        <td width="160" class="text_10 gray_bg" valign="top"><?php echo $group_address; ?><br /><?php echo $group_address_csz; ?></td>
                                    </tr>
                                <?php } ?> 
                            </table>                 
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="6" style="height:3px;"></td></tr>
    <?php }?>
    <tr>
        <td width="100" class="text_10b" valign="top">Patient Name : </td>
        <td width="175" class="text_10" valign="top"><?php echo $patientName; ?><?php echo ' - '. $patient_id; ?></td>
        <td class="text_10b" width="100" valign="top">Pt Phone (H)# : </td>
        <td width="110" class="text_10" valign="top"><?php echo core_phone_format($phone_home); ?></td>
        <td class="text_10b" width="100" valign="top">Collected Date : </td>
        <td width="150" class="text_10" valign="top">
			<?php 
				echo $chk_creted_date;
				//echo getDateFormat(date('Y-m-d'));
			?>
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr>
    	<td class="text_10b" valign="top">Patient Address : </td>
        <td class="text_10" valign="top"><?php echo $patient_address; ?></td>
        <td class="text_10b" valign="top">Pt Phone (W/C)# : </td>
        <td class="text_10" valign="top"><?php echo core_phone_format($other_phone); ?></td>
        <td class="text_10b" valign="top">Collected By : </td>
        <td class="text_10" valign="top"><?php echo $chk_creted_by; ?></td>  
    </tr>
    <tr><td style="height:10px;" colspan="6">&nbsp;</td></tr>
    <?php
	$check_in_content = '';
	$total_amt_arr = array();
	$item_payment= array();
	$tot_chk_amt=array();
	$chk_out_tot_amt=array();
	$chk_in_tot_amt=array();
	$item_name_arr=array();
	$item_name_arr_uni=array();
	$final_payment_method=array();
	for($i=0;$i<count($checkQryRes);$i++){
		$item_id = $checkQryRes[$i]['item_id'];
		$item_name = $fieldNameArr[$item_id];
		$item_name_arr[] = $fieldNameArr[$item_id];
		$total_amt_arr[$item_name][$checkQryRes[$i]['payment_type']] = $checkQryRes[$i]['item_payment'];
		$item_payment[$item_name][$checkQryRes[$i]['payment_type']] = str_replace(',','',number_format($checkQryRes[$i]['item_payment'],2));
		$check_in_comment=$checkQryRes[$i]['ci_comments'];
		$check_out_comment=$checkQryRes[$i]['co_comment'];
		$payment_method = $checkQryRes[$i]['payment_method'];
		$payment_method_arg = $checkQryRes[$i]['payment_method'];
		$check_no = $checkQryRes[$i]['check_no'];
		$cc_type = $checkQryRes[$i]['cc_type'];
		$cc_no = substr($checkQryRes[$i]['cc_no'],-4);
		if($payment_method == 'Check' or $payment_method == 'EFT' or $payment_method == 'Money Order'){
			$payment_method .= " ( $check_no )";
		} 
		if($payment_method == 'Credit Card'){
			if(empty($cc_type) === false or empty($cc_no) === false){
				$cc_str = $cc_type;
				if($cc_str != '' and $cc_no != ''){
					$cc_str .= ' , ';
				}
				$cc_str .= $cc_no;
				$payment_method .= " ( $cc_str )";
			}
		}
		
		$final_payment_method[$checkQryRes[$i]['payment_type']][$payment_method_arg]= $payment_method;
		$final_payment_method_amt[$checkQryRes[$i]['payment_type']][$payment_method_arg][]= $checkQryRes[$i]['item_payment'];
	}
	
	$item_name_arr_uni=array_values(array_unique($item_name_arr));
	for($i=0;$i<count($item_name_arr_uni);$i++){
		$item_name = $item_name_arr_uni[$i];
		$chk_in_tot_amt[]=$item_payment[$item_name]['checkin'];
		$chk_out_tot_amt[]=$item_payment[$item_name]['checkout'];
		$sub_chk_amt=$item_payment[$item_name]['checkin']+$item_payment[$item_name]['checkout'];
		$tot_chk_amt[]=$sub_chk_amt;
		$check_in_content .= "
			<tr>
				<td width='80'>&nbsp;</td>
				<td valign='top' class='text_10b' width='150'>".$item_name." : </td>
      			<td valign='top' class='text_10' width='100'>".show_currency().str_replace(',','',number_format($item_payment[$item_name]['checkin'],2))."</td>
				<td valign='top' class='text_10' width='100'>".show_currency().str_replace(',','',number_format($item_payment[$item_name]['checkout'],2))."</td>
				<td valign='top' class='text_10' width='100'>".show_currency().str_replace(',','',number_format($sub_chk_amt,2))."</td>
			</tr>";
	}
	?>
     <?php
	 	if($check_in_comment==""){
			$comm_qry=imw_query("select ci_comments from check_in_out_payment where sch_id = '$chk_id' and payment_type='checkin' limit 0,1");
			$comm_row=imw_fetch_array($comm_qry);
			$check_in_comment = $comm_row['ci_comments'];
		}
	if($check_in_comment){
	?>
    <tr>
    	<td colspan="6">
        	<table width="100%" cellpadding="0" cellspacing="0">
            	 <tr>
                    <td width="150" class="text_10b" valign="top">Check In Comment:-</td>
                    <td class="text_10" width="580">
                        <?php 
                            echo $check_in_comment;
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	<?php } 
		if($check_out_comment==""){
			$comm_qry=imw_query("select co_comment from check_in_out_payment where sch_id = '$chk_id' and payment_type='checkout' limit 0,1");
			$comm_row=imw_fetch_array($comm_qry);
			$check_out_comment = $comm_row['co_comment'];
		}	
    	if($check_out_comment){
	?>
    <tr>
    	<td colspan="6">
        	<table width="100%" cellpadding="0" cellspacing="0">
            	 <tr>
                    <td width="150" class="text_10b" valign="top">Check Out Comment:-</td>
                    <td class="text_10" width="580">
                        <?php 
                            echo $check_out_comment;
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

	<?php } ?>
    <tr><td style="height:15px;" colspan="6">&nbsp;</td></tr>
    <tr>
    	<td colspan="6">
        	<table width="100%" cellpadding="0" cellspacing="0" border="0">
            	<?php if($check_in_content){?>
                    <tr>
                        <td width='80'>&nbsp;</td>
                        <td valign='top' class='text_b_w' width='150'></td>
                        <td valign='top' class='text_b_w' width='100'>Check In</td>
                        <td valign='top' class='text_b_w' width='100'>Check Out</td>
                        <td valign='top' class='text_b_w' width='100'>Sub Total</td>
                    </tr>
                    <?php print $check_in_content; ?>
                    <tr>
                        <td width="150" height="1px"></td>
                        <td bgcolor="#009933" height="1px" colspan="4"></td>
                    </tr>
                    <tr>
                        <td width="150">&nbsp;</td>
                        <td valign="top" class="text_10b" width="200">Total : </td>
                        <td valign="top" class="text_10"><?php print show_currency().str_replace(',','',number_format(array_sum($chk_in_tot_amt),2)); ?></td>
                        <td valign="top" class="text_10"><?php print show_currency().str_replace(',','',number_format(array_sum($chk_out_tot_amt),2)); ?></td>
                        <td valign="top" class="text_10"><?php print show_currency().str_replace(',','',number_format(array_sum($tot_chk_amt),2)); ?></td>
                    </tr>
                    <tr>
                        <td width="150" height="1px"></td>
                        <td bgcolor="#009933" height="1px" colspan="4"></td>
                    </tr>
                    <tr><td colspan="4"></td></tr>
                    <?php 
					
                    if(count($final_payment_method['checkin'])>0){
                    ?>
                        <tr>
                            <td width="150">&nbsp;</td>
                            <td class="text_10b" style="padding-left:30px;" colspan="4">
                                 <?php 
									if($final_payment_method['checkin']['Cash']!=""){
                                        echo "Check in Payment Method : ".$final_payment_method['checkin']['Cash']."  ".numberFormat(array_sum($final_payment_method_amt['checkin']['Cash']),2)."<br>"; 	
									}
									if($final_payment_method['checkin']['Check']!=""){
                                        echo "Check in Payment Method : ".$final_payment_method['checkin']['Check']."  ".numberFormat(array_sum($final_payment_method_amt['checkin']['Check']),2)."<br>"; 	
									}
									if($final_payment_method['checkin']['EFT']!=""){
                                        echo "Check in Payment Method : ".$final_payment_method['checkin']['EFT']."  ".numberFormat(array_sum($final_payment_method_amt['checkin']['EFT']),2)."<br>"; 	
									}
									if($final_payment_method['checkin']['Money Order']!=""){
                                        echo "Check in Payment Method : ".$final_payment_method['checkin']['Money Order']."  ".numberFormat(array_sum($final_payment_method_amt['checkin']['Money Order']),2)."<br>"; 	
									}
									if($final_payment_method['checkin']['Credit Card']!=""){
                                        echo "Check in Payment Method : ".$final_payment_method['checkin']['Credit Card']."  ".numberFormat(array_sum($final_payment_method_amt['checkin']['Credit Card']),2)."<br>"; 	
									}
                                ?>
                            </td>
                        </tr>
                    <?php 
                    }
					if(count($final_payment_method['checkout'])>0){
                    ?>
                        <tr>
                            <td width="150">&nbsp;</td>
                            <td class="text_10b" style="padding-left:30px;" colspan="4">
                                <?php 
									if($final_payment_method['checkout']['Cash']!=""){
                                        echo "Check Out Payment Method : ".$final_payment_method['checkout']['Cash']."  ".numberFormat(array_sum($final_payment_method_amt['checkout']['Cash']),2)."<br>"; 	
									}
									if($final_payment_method['checkout']['Check']!=""){
                                        echo "Check Out Payment Method : ".$final_payment_method['checkout']['Check']."  ".numberFormat(array_sum($final_payment_method_amt['checkout']['Check']),2)."<br>"; 	
									}
									if($final_payment_method['checkout']['EFT']!=""){
                                        echo "Check Out Payment Method : ".$final_payment_method['checkout']['EFT']."  ".numberFormat(array_sum($final_payment_method_amt['checkout']['EFT']),2)."<br>"; 	
									}
									if($final_payment_method['checkout']['Money Order']!=""){
                                        echo "Check Out Payment Method : ".$final_payment_method['checkout']['Money Order']."  ".numberFormat(array_sum($final_payment_method_amt['checkout']['Money Order']),2)."<br>"; 	
									}
									if($final_payment_method['checkout']['Credit Card']!=""){
                                        echo "Check Out Payment Method : ".$final_payment_method['checkout']['Credit Card']."  ".numberFormat(array_sum($final_payment_method_amt['checkout']['Credit Card']),2)."<br>"; 	
									}
                                ?>
                            </td>
                        </tr>
                <?php 	
					}
				}
				?>
            </table>
        </td>
    </tr>
    <tr>
    	<td style="padding-top:10px;" colspan="6">
        	<?php
				//-------GET FACILITY INFORMATION TO PRINT ON CHECK-IN PAYMENT RECEIPTS
				$reqQry = "	SELECT 
								fac.name as fac_name, 
								fac.street as fac_street, 
								fac.city as fac_city, 
								fac.state as fac_state, 
								fac.postal_code as fac_postal_code, 
								fac.zip_ext as fac_zip_ext, 
								fac.phone as fac_phone,  
								sa.id, 
								sa.sa_doctor_id,
								sa.procedureid, 
								sa.sa_patient_name, 
								DATE_FORMAT(sa.sa_app_start_date,'".get_sql_date_format()."') as sa_app_start_date, 
								TIME_FORMAT(sa.sa_app_starttime,'%h:%i:%s %p') as sa_app_starttime, 
								sp.proc, 
								sp.max_allowed, 
								CONCAT(users.fname,' ', if( users.mname = '', '', CONCAT(users.mname,' ') ) , users.lname) AS provider_name
							FROM 
								`schedule_appointments` sa
								LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
								LEFT JOIN users ON users.id = if(sa.facility_type_provider!='0',sa.facility_type_provider,sa.sa_doctor_id) 
								LEFT JOIN facility fac ON fac.id = sa.sa_facility_id 
							WHERE 
								sa.sa_app_start_date > (SELECT sa_app_start_date FROM schedule_appointments WHERE id = ".$chk_id.") 
							AND
								sa.sa_patient_app_status_id NOT IN (203,201,18,19,20)
							AND 
								sa.sa_patient_id = ".$patient_id."
							ORDER BY 
								sa.sa_app_start_date ASC
							LIMIT 0, 5";
 
				$future_appt_obj = imw_query($reqQry);
			?>
        	<table cellpadding="0" cellspacing="0" style="">
            	<tr>
                	<td class="text_b_w" align="center" colspan="5">Future Appointments </td>
                </tr>
            	<tr>
                	<td width="90" class="text_b_w">Appt. Date</td>
                	<td width="90" class="text_b_w">Appt. Time</td>                  
                	<td width="120" class="text_b_w" style="padding:5px;">Provider</td>
                	<td width="170" class="text_b_w">Procedure</td>
					<td width="240" class="text_b_w">Facility</td>
                </tr>
				<?php
				if(imw_num_rows($future_appt_obj)==0)
				{
					echo '<tr><td colspan="4" align="center"><b>No Future Appointments</b></td></tr>';
				}
				while($future_appt_row = imw_fetch_assoc($future_appt_obj))
				{
					$fac_phone = (!empty($future_appt_row['fac_phone'])) ? '<br/>Phone : '.$future_appt_row['fac_phone'] : '';
					
					$result_data = '<tr style="vertical-align:top;">
									<td style="padding-top:7px;">'.$future_appt_row['sa_app_start_date'].'</td>
									<td style="padding-top:7px;">'.$future_appt_row['sa_app_starttime'].'</td>						
									<td style="padding-top:7px;" height="25">'.$future_appt_row['provider_name'].'</td>
									<td style="padding-top:7px;width:150px;">'.$future_appt_row['proc'].'</td>
									<td style="padding-top:7px;">'.$future_appt_row['fac_name'].'<br/>'.$future_appt_row['fac_street'].',<br/>'.$future_appt_row['fac_city'].',&nbsp;'.$future_appt_row['fac_state'].'&nbsp;'.$future_appt_row['fac_postal_code'].'-'.$future_appt_row['fac_zip_ext'].$fac_phone.'</td>
									</tr>';
					echo $result_data;														
				}				
				?>	               
            </table>
        </td>
    </tr>
    <?php 
        $iportal_instructions_detail =$iportal_status= "";
		$reqInsDetFacQry = "SELECT iportal_instructions_detail,dis_iportal from facility where facility_type=1";
        $reqInsDetFacRes = imw_query($reqInsDetFacQry);
        if(imw_num_rows($reqInsDetFacRes)) {
        	$reqInsDetFacRow = imw_fetch_array($reqInsDetFacRes);
			$iportal_instructions_detail = $reqInsDetFacRow["iportal_instructions_detail"];
			$iportal_status= $reqInsDetFacRow["dis_iportal"];
		}
		$reqQry = "SELECT temp_key FROM patient_data WHERE temp_key!='' and (username='' OR username IS NULL)  and id = '".$patient_id."'";
        $result_obj = imw_query($reqQry);
        if($iportal_status!=1){
			$result_data = imw_fetch_assoc($result_obj);                
    ?>
    <tr>
        <td style="padding-top:20px;" colspan="6">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" class="text_b_w" style="padding:5px; width:720px;"> For login to Patient Portal </td>
                </tr>
                <?php if($result_data["temp_key"] && imw_num_rows($result_obj)>0){ ?>
                <tr>
                    <td class="text_10b" style="padding:3px; width:65px; text-align:left">Temp Key :</td>
                    <td style="padding:3px; width:500px; text-align:left"><?php echo $result_data["temp_key"]; ?></td>
                </tr>
                <?php } ?>
               	<tr>
                    <td class="text_10b" style="padding:3px; vertical-align:top;">Instructions :</td>
                    <td style="padding:3px;width:500px; vertical-align:top;"><?php echo stripslashes(nl2br($iportal_instructions_detail)); ?></td>
                </tr>                
            </table>
        </td>
    </tr> 
    <?php } ?>       
</table>
<?php
$print_file_content = ob_get_contents();
ob_end_clean();
$print_file_name = "chk_in_print_reciept_".$_SESSION["authId"];
$file_path = write_html($print_file_content);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	var file_name = '<?php print $print_file_name; ?>';
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	html_to_pdf('<?php echo $file_path; ?>','p',file_name);
	window.close();
</script>