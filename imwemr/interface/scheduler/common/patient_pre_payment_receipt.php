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
Purpose: Get All Appointment Slot
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../../config/globals.php');
ob_start();
$operator_id=$_SESSION['authId'];
//--- GET CHECK IN / OUT PAYMENT DETAILS ---
$chk_id = $_REQUEST['id'];
$pmt_ids = $_REQUEST['pmt_ids'];
//--- GET PATIENT DETAILS ---
$patient_id = $_REQUEST['patient_id'];
if($patient_id>0){
	$patient_id=$patient_id;
}else{
	$patient_id=$_SESSION['patient'];
}
$cur_date=date('Y-m-d');
$pat_query = imw_query("select * from patient_data where id = '$patient_id'");
$patQryRes = imw_fetch_assoc($pat_query);
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

$default_facility = $patQryRes['default_facility'];
 

//--- GET CREATED BY PROVIDER NAME ---

$qry_usr=imw_query("select * from users");		
while($row_usr=imw_fetch_array($qry_usr)){
	$ins_operator_name_arr[$row_usr['id']] = strtoupper(substr($row_usr['fname'],0,1).substr($row_usr['mname'],0,1).substr($row_usr['lname'],0,1));
	$ins_operator_full_name_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
}

$pre_date_whr=$pre_fac_whr="";
/*$pre_fac_whr=" facility.billing_location ='1'";
if(in_array(strtolower($billing_global_server_name), array('hattiesburg'))){
	$pre_date_whr= " and entered_date='$cur_date'";
	$pre_fac_whr ="";
}*/
if($pmt_ids!=""){
	$pre_date_whr= " and id in($pmt_ids)";
}else{
	$pre_date_whr= " and entered_date='$cur_date'";
}
$depo_qry = " select * from patient_pre_payment where patient_id='$patient_id' and del_status='0' $pre_date_whr order by entered_date desc";
$depo_mysql = imw_query($depo_qry);
while($dpRows = imw_fetch_array($depo_mysql)) 
{
	$pre_data_arr[$dpRows['facility_id']][]=$dpRows;
	$pre_fac_arr[$dpRows['facility_id']]=$dpRows['facility_id'];
}

$pre_fac_imp=implode("','",$pre_fac_arr);
if($pre_fac_whr==""){
	$pre_fac_whr= " facility.id in('".$pre_fac_imp."')";
}
if(count($pre_fac_arr)==0){
	$pre_fac_whr= " facility.id in('".$_SESSION['login_facility']."')";
}

$qryGroup = imw_query("select groups_new.name,groups_new.group_Address1,groups_new.group_Address2,groups_new.group_Telephone,groups_new.group_Fax,
					groups_new.group_Federal_EIN,groups_new.group_State,groups_new.group_City,groups_new.group_Zip,groups_new.zip_ext,
					group_concat(facility.id) as grp_fac_id,facility.id,facility.billing_attention,facility.street,facility.city,facility.state,facility.postal_code,facility.zip_ext
					from facility
					left join groups_new on groups_new.gro_id = facility.default_group
					where $pre_fac_whr group by groups_new.gro_id");
while($groupQryRes =imw_fetch_assoc($qryGroup)){
	$GroupName_arr=$group_Telephone_arr=$group_Fax_arr=$address_arr=$group_Federal_EIN_arr=array();
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
	
	$GroupName=implode(',<br>',$GroupName_arr);
	$group_Telephone=implode(', ',$group_Telephone_arr);
	$group_Fax=implode(', ',$group_Fax_arr);
	$group_address=implode(',<br>',$address_arr);
	$group_Federal_EIN=implode(', ',$group_Federal_EIN_arr);
	$group_address_csz=$group_City.', '.$group_State.' '.$group_Zip; 
	$fac_address_csz=$fac_city.', '.$fac_state.' '.$fac_postal_code; 
	$grp_fac_ids_arr=explode(',',$groupQryRes['grp_fac_id']);
?>
<page backtop="4mm" backbottom="5mm">
<table width="100%" cellpadding="4" cellspacing="0" border="0">
	<tr>
        <td class="text_b_w" height="22" align="left" colspan="4">&nbsp;Prepayments</td>
        <td class="text_b_w" height="22" align="left" colspan="2">Printed On: <?php echo get_date_format(date('Y-m-d')); ?></td>
        
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
                                        <?php }else{?>
                                           <?php echo $group_Telephone; ?>
                                        <?php } ?>
                                    </td>                        
                                </tr>
                                <tr>
                                    <td class="text_10b gray_bg">Fax :</td>
                                    <td class="text_10 gray_bg"><?php echo $group_Fax; ?> </td> 
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
                                    <td class="text_10b gray_bg" colspan="2" style="width:<?php echo $middle_box_width; ?>;"><?php echo $GroupName; ?></td>
                                </tr>
                                <tr>
                                     <td class="text_10b gray_bg" valign="top" nowrap>Tax Id : </td>
                                     <td class="text_10 gray_bg" valign="top" align="left"><?php echo $group_Federal_EIN; ?></td>
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
                                <?php }else{ ?>   
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
        <td width="150" class="text_10" valign="top"><?php echo get_date_format(date('Y-m-d')); ?></td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr>
    	<td class="text_10b" valign="top">Patient Address : </td>
        <td class="text_10" valign="top"><?php echo $patient_address; ?></td>
        <td class="text_10b" valign="top">Pt Phone (W/C)# : </td>
        <td class="text_10" valign="top"><?php echo core_phone_format($other_phone); ?></td>
        <td class="text_10b" valign="top">Collected By : </td>
        <td class="text_10" valign="top"><?php echo $ins_operator_name_arr[$operator_id]; ?></td>
    </tr>
    
    <tr><td style="height:10px;" colspan="6">&nbsp;</td></tr>
    <tr>
    	<td colspan="6">
    	<?php
			$data='<table class="table_collapse cellBorder3" style="background-color:#FFF3E8";>
					<tr style="height:25px;">
						<td class="text_b_w" style="text-align:center;width:120px;">Date of Transaction</td>
						<td class="text_b_w" style="text-align:center;width:60px;">Paid Date</td>
						<td class="text_b_w" style="text-align:center;width:75px;">Payment</td>
						<td class="text_b_w" style="text-align:center;width:90px;">Payment Mode</td>
						<td class="text_b_w" style="text-align:center;width:110px;">Payment Method</td>
						<td class="text_b_w" style="text-align:center;width:120px;">Comment</td>
						<td class="text_b_w" style="text-align:center;width:50px;">Applied</td>
						<td class="text_b_w" style="text-align:center;width:55px;">Operator</td>
					</tr>';
			$i=0;
			foreach($grp_fac_ids_arr as $grp_fac_key=>$grp_fac_val){
				foreach($pre_data_arr[$grp_fac_val] as $pre_key=>$pre_val){
					$dpRows=$pre_data_arr[$grp_fac_val][$pre_key];	
					$id=$dpRows['id'];
					$delClass='';
					$hyperlinkOpen='';
					$hyperLinkClose='';
					$show_entered_date="";
					$show_deleted_date='';
					$hyperlinkOpen='<a target="_parent" href="patient_pre_payment.php?edit_id='.$dpRows['id'].'" class="text_10">';
					$hyperlinkClose='</a>';
					
					if($dpRows['entered_date']!='0000-00-00' && $dpRows['entered_date']!=""){
						$show_entered_date = get_date_format(date("Y-m-d",strtotime($dpRows['entered_date']))).' '. date("h:i A",strtotime($dpRows['entered_time'])); 
					}
						
					if($dpRows['del_status']=='1'){ 
						$delClass=' del_text';
						$hyperlinkOpen='<a class="text_10">';
						$hyperlinkClose='</a>';
						if($dpRows['trans_del_date']!='0000-00-00' && $dpRows['trans_del_date']!=""){
							$show_deleted_date = get_date_format(date("Y-m-d",strtotime($dpRows['trans_del_date']))).' '. date("h:i A",strtotime($dpRows['trans_del_date'])); 
						}
						if($dpRows['del_operator_id']>0){
							$show_deleted_date.=' '.$ins_operator_name_arr[$dpRows['del_operator_id']];
						}
					}
					
					$bgcolor = (($i%2) == 0) ? "alt3" : "";
					$i++;
					$tot_pay_qry=imw_query("select sum(paidForProc+overPayment) as tot_applied_amt from patient_charges_detail_payment_info where patient_pre_payment_id='$id' and deletePayment='0'");
					$tot_pay_row=imw_fetch_array($tot_pay_qry);
					$applied_color="";
					if($dpRows['apply_payment_type']=="manually"){
						$applied_color="green";
						$bgcolor="";
					}else if($tot_pay_row['tot_applied_amt']>0 && $tot_pay_row['tot_applied_amt']>=$dpRows['paid_amount']){
						$applied_color="green";
						$bgcolor="";
					}
				?>	
					
				  <?php  
					$data .='
					<tr style="background-color:'.$applied_color.'">
						<td class="'.$bgcolor.$delClass.' text_10" style="text-align:center;vertical-align:top;width:120px;" >
							'.$show_entered_date.'
						</td>';
						
							$show_paid_date="";
							if($dpRows['paid_date']!='0000-00-00' && $dpRows['paid_date']!=""){
								$show_paid_date = get_date_format(date("Y-m-d",strtotime($dpRows['paid_date']))); 
							}
					$data .='<td class="'.$bgcolor.$delClass.' text_10" style="text-align:center;vertical-align:top;width:60px;">
						'.$show_paid_date.'
					</td>
					<td class="text_10 '.$bgcolor.$delClass.' alignRight" style="padding-right:5px; text-align:right;vertical-align:top;width:75px;">
						'.numberFormat($dpRows['paid_amount'],2,'yes').'
					</td>';
						
							
						
						$data .='<td class="text_10 '.$bgcolor.$delClass.'" style="text-align:center;vertical-align:top;width:90px;">
							'.$dpRows['payment_mode'].'
						</td>
						<td class="text_10 '.$bgcolor.$delClass.'" style="text-align:left; padding-left:10px; width:110px;vertical-align:top;">
						';	
						
						if($dpRows['payment_mode']=='Check' || $dpRows['payment_mode']=='Money Order'){ 
							$data .=$dpRows['check_no'];
							}else if($dpRows['payment_mode']=='Credit Card'){ 
									$credit_card_company="";
									if($dpRows['credit_card_co']=="AX"){
										$credit_card_company="American Express";
									}
									if($dpRows['credit_card_co']=="Dis"){
										$credit_card_company="Discover";
									}
									if($dpRows['credit_card_co']=="MC"){
										$credit_card_company="Master Card";
									}
									if($dpRows['credit_card_co']=="Visa"){
										$credit_card_company="Visa";
									}
									if($dpRows['credit_card_co']=="Care Credit"){
										$credit_card_company="Care Credit";
									}
									
						$data .=$credit_card_company.' - '.$dpRows['cc_no'].' - '.$dpRows['cc_exp_date'];
							} 
						$data .='
						</td>
						<td class="text_10 alignLeft '.$bgcolor.$delClass.'" style="padding-left:5px; width:120px;vertical-align:top;">
							'.htmlentities($dpRows['comment']).'
						</td>
						 <td class="text_10 alignRight '.$bgcolor.$delClass.'" style="padding-right:5px;text-align:right;vertical-align:top;width:50px;">';
						if($dpRows['apply_payment_type']=="manually"){
							$data .= "Manually";
						}else if($tot_pay_row['tot_applied_amt']>0){
							$data .= numberFormat($tot_pay_row['tot_applied_amt'],2,'yes');
						}
						$data .='</td>
						<td class="text_10 '.$bgcolor.$delClass.'" style="text-align:center;vertical-align:top;width:55px;">
							  '.$ins_operator_name_arr[$dpRows['entered_by']].'
						</td>
						</tr>';
					}
				}
			if(imw_num_rows($depo_mysql)==0)  {
			?>
			<?php	
			$data.='
			<tr>
				<td class="warning alignCenter text12b" colspan="9" style="text-align:center; background:#FFFFFF;">No record found.</td>
			</tr>';	
	?>
	<?php		
			}
			echo $data.='</table>';
		?>	
		</td>
	</tr>
    <tr>
    	<td style="padding-top:10px;" colspan="6">
        	<?php
				$reqQry = "SELECT sa.id, sa.sa_doctor_id, fac.name as fac_name, sa.sa_patient_name, DATE_FORMAT(sa.sa_app_start_date,'".get_sql_date_format()."') as sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime,'%h:%i:%s %p') as sa_app_starttime, sa.procedureid, sp.proc, sp.max_allowed, 
							CONCAT( users.fname, if( users.mname = '', '', CONCAT( ' ', users.mname ) ) , users.lname ) AS provider_name
							FROM schedule_appointments sa
							LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
							LEFT JOIN users ON users.id = if(sa.facility_type_provider!='0',sa.facility_type_provider,sa.sa_doctor_id)  
							LEFT JOIN facility fac ON fac.id = sa.sa_facility_id 
							WHERE sa.sa_app_start_date > '$cur_date' and sa.sa_patient_app_status_id NOT IN (203,201,18,19,20)
							AND sa.sa_patient_id = ".$patient_id."
							ORDER BY sa.sa_app_start_date ASC
							LIMIT 0 , 5";
							
				$future_appt_obj = imw_query($reqQry);
			?>
        	<table cellpadding="0" cellspacing="0">
            	<tr>
                	<td class="text_b_w" align="center" colspan="5">Future Appointments </td>
                </tr>
            	<tr>
                	<td width="100" class="text_b_w">Appt. Date</td>
                	<td width="100" class="text_b_w">Appt. Time</td>                  
                	<td width="170" class="text_b_w" style="padding:5px;">Provider</td>
                	<td width="170" class="text_b_w">Procedure</td>
					<td width="170" class="text_b_w">Facility</td>
                </tr>
				<?php
				if(imw_num_rows($future_appt_obj)==0)
				{
					echo '<tr><td colspan="4" align="center"><b>No Future Appointments</b></td></tr>';
				}
				while($future_appt_row = imw_fetch_assoc($future_appt_obj))
				{
					$result_data = '<tr>								
									<td>'.$future_appt_row['sa_app_start_date'].'</td>
									<td>'.$future_appt_row['sa_app_starttime'].'</td>						
									<td height="25">'.$future_appt_row['provider_name'].'</td>
									<td style="width:150px;">'.$future_appt_row['proc'].'</td>
									<td>'.$future_appt_row['fac_name'].'</td>
									</tr>';
					echo $result_data;														
				}				
				?>	               
            </table>
        </td>
    </tr>    
</table>
</page>
<?php } ?>
<style type="text/css">
	.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#000000;
		background-color:#BCD5E1;
	}
	.text_10b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#FFFFFF;
	}
	.text_10{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
	}
	.gray_bg{
		background-color:#CCCCCC;
	}
	
</style>
<?php
$print_file_content = ob_get_contents();
ob_end_clean();
$print_file_name = "chk_in_print_reciept_".$_SESSION["authId"];
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html
$file_location = write_html($print_file_content);

//file_put_contents("../../reports/new_html2pdf/".$print_file_name.".html",$print_file_content);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','p','',true);
</script>