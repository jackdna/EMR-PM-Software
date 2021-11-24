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
require_once(dirname(__FILE__).'/../../../config/globals.php');
include_once(dirname(__FILE__)."/../../accounting/accounting_session.php");
$operator_id = $_SESSION['authId'];
$patient_id=$_SESSION['patient'];
$phy_id_cn=$GLOBALS['arrValidCNPhy'];
$curr_date=date('Y-m-d');
$qry_usr=imw_query("select * from users order by lname,fname asc");		
while($row_usr=imw_fetch_array($qry_usr)){
	$ins_operator_name_arr[$row_usr['id']] = substr($row_usr['fname'],0,1).substr($row_usr['lname'],0,1);
	$ins_operator_full_name_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
	if($row_usr["Enable_Scheduler"]=='1' || in_array($row_usr["user_type"],$phy_id_cn)){
		$users_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
	}
}

//------------------------ Payment Method Detail ------------------------//
$qry = imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
while($row = imw_fetch_array($qry)){ 
	$payment_method_arr[$row['pm_id']]=$row['pm_name'];
}
//------------------------ Payment Method Detail ------------------------//

$login_facility=$_SESSION['login_facility'];
if($_REQUEST['btn_submit']!="" && $patient_id>0 && $_REQUEST['tsys_void_id']=="")
{
	if($pmt_fac>0){
		$login_facility=$pmt_fac;
	}
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	$paymentDate = getDateFormatDB($paid_date);
	$paid_amount=str_replace(',','',$paid_amount);
	if($edit_id>0){
		if($payment_mode=="Cash"){
			$check_no="";
			$cc_no="";
			$credit_card_co="";
			$cc_exp_date="";
		}
		if($payment_mode=="Check" || $payment_mode=="Money Order"){
			$cc_no="";
			$credit_card_co="";
			$cc_exp_date="";
		}
		if($payment_mode=="Credit Card"){
			$check_no="";
		}
		$ins_qry=imw_query("update patient_pre_payment set paid_amount='".imw_real_escape_string($paid_amount)."',paid_date='".imw_real_escape_string($paymentDate)."',
		payment_mode='".imw_real_escape_string($payment_mode)."',check_no='".imw_real_escape_string($check_no)."',cc_no='".imw_real_escape_string($cc_no)."',
		credit_card_co='".imw_real_escape_string($credit_card_co)."',cc_exp_date='".imw_real_escape_string($cc_exp_date)."',
		modified_date='".imw_real_escape_string($entered_date)."',modified_time='".imw_real_escape_string($entered_time)."',
		modified_by='".imw_real_escape_string($operator_id)."',comment='".imw_real_escape_string($comment)."',facility_id='".$login_facility."',provider_id='".$pmt_prov."' 
		where id='$edit_id' and patient_id='$patient_id'");
		echo "<script type='text/javascript'>window.location.href='patient_pre_payment.php'</script>";
	}else{
        if(isset($_REQUEST['card_details_str_id']) && $_REQUEST['card_details_str_id']!='' && $credit_card_co=='' && $cc_no=='' && $cc_exp_date==''){
            $card_details_arr=explode('~~',trim($_REQUEST['card_details_str_id']));
            $credit_card_co=$card_details_arr[0];
            $cc_no=$card_details_arr[1];
            $cc_exp_date=$card_details_arr[2];
        }
        $ins_qry=imw_query("insert into patient_pre_payment set patient_id='$patient_id',paid_amount='".imw_real_escape_string($paid_amount)."',paid_date='".imw_real_escape_string($paymentDate)."',
        payment_mode='".imw_real_escape_string($payment_mode)."',check_no='".imw_real_escape_string($check_no)."',cc_no='".imw_real_escape_string($cc_no)."',
        credit_card_co='".imw_real_escape_string($credit_card_co)."',cc_exp_date='".imw_real_escape_string($cc_exp_date)."',
        entered_date='".imw_real_escape_string($entered_date)."',entered_time='".imw_real_escape_string($entered_time)."',
        entered_by='".imw_real_escape_string($operator_id)."',comment='".imw_real_escape_string($comment)."',facility_id='".$login_facility."',provider_id='".$pmt_prov."',
        log_referenceNumber='".$log_referenceNumber."', tsys_transaction_id='".$tsys_transaction_id."' ");
        echo "<script type='text/javascript'>window.location.href='patient_pre_payment.php?prnt=$patient_id'</script>";
	}
	
}

if($_REQUEST['tsys_void_id']!="") {
    $tsys_void_id=$_REQUEST['tsys_void_id'];
    $log_referenceNumber=$_REQUEST['log_referenceNumber'];
    $tsys_last_status=$_REQUEST['tsys_last_status'];
    $updvoid="update patient_pre_payment set tsys_status='".$tsys_last_status."' where id='".$tsys_void_id."' ";
    imw_query($updvoid);
    if($tsys_last_status=='VOIDED') {
        $_REQUEST['delete_record']=$tsys_void_id;
    }
} 
if($_REQUEST['delete_record'])
{
	$del_id=$_REQUEST['delete_record'];
	$trans_del_date=date('Y-m-d H:i:s');
	imw_query("update patient_pre_payment set del_status = '1',del_operator_id='$operator_id',trans_del_date='$trans_del_date' where id = '$del_id' and patient_id='$patient_id'");
}
$pat_query = imw_query("select lname,fname,mname,providerID from patient_data where id = '$patient_id'");
$patQryRes = imw_fetch_assoc($pat_query);
$patient_name_arr = array();
$patient_name_arr["LAST_NAME"] = $patQryRes["lname"];
$patient_name_arr["FIRST_NAME"] = $patQryRes["fname"];
$patient_name_arr["MIDDLE_NAME"] = $patQryRes["mname"];
$patient_name = changeNameFormat($patient_name_arr);
$patient_name .= ' - '.$patient_id;
$patient_provider_id = $patQryRes["providerID"];

$fac_qry=imw_query("select id,name from facility order by name");
while($fac_row=imw_fetch_array($fac_qry)){
	$fac_arr[$fac_row['id']]=$fac_row['name'];
}

$qry_case_id = "select sa_doctor_id from schedule_appointments where sa_doctor_id>0 and sa_app_start_date='$curr_date' and sa_patient_id='$patient_id'
				and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1";												
$run_case_id = imw_query($qry_case_id);	
$fet_case_list=imw_fetch_array($run_case_id);
$sa_doctor_id =$fet_case_list['sa_doctor_id'];


$login_facility=$_SESSION['login_facility'];
$pos_device=false;
$devices_sql="Select tsys_device_details.id from tsys_device_details
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id
              WHERE device_status=0
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0
              ";
$resp = imw_query($devices_sql);
if($resp && imw_num_rows($resp)>0){
    $pos_device=true;
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>imwemr - Prepayments</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        
        <!-- Bootstrap -->
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css">
    	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-multiselect.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/core.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
        
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap-dropdownhover.min.js"></script>

		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
        <!--jquery to suport discontinued functions-->
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-migrate-1.2.1.js"></script> 
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
        
		<!--<script type="text/javascript" src="js_checkout.php"></script>-->
        <script type="text/javascript">
		//var jquery_date_format = "<?php echo jQueryIntDateFormat();?>";
		var pos_device='<?php echo $pos_device; ?>';
        var pos_patient_id='<?php echo $patient_id;?>';
        var edit_id='<?php echo $edit_id;?>';
		var jquery_date_format = window.opener.top.jquery_date_format;
			function dep_delete(id)
			{
				if($("#acc_view_pay_only").val()==1  || $("#acc_edit_financials").val()==0){
					view_only_acc_call(0);
					return false;
				}
				var r=confirm("Are you sure to delete this record?")
				if (r==true)
				{
					window.location.href="patient_pre_payment.php?delete_record="+id;
				}	
			}
			
			function changeMode(){
				var thisVal = document.getElementById('payment_mode').value;
				if(thisVal == 'Cash'){
					document.getElementById('checkTd').style.display = 'none';
					document.getElementById('ccTd').style.display = 'none';
				}else if(thisVal == 'Check' || thisVal == 'EFT' || thisVal == 'Money Order'){
					document.getElementById('checkTd').style.display = 'block';
					document.getElementById('ccTd').style.display = 'none';
				}else if(thisVal == 'Credit Card'){
					document.getElementById('checkTd').style.display = 'none';
					document.getElementById('ccTd').style.display = 'block';
				}
			}
			function save_but_disable(patient_id){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				if(document.getElementById('paid_amount').value==""){
					alert("Please enter Pre Payment amount");
					return false;
				}else if($('#payment_mode').val()=='Credit Card' && $('#credit_card_co').val()=='' && (!pos_device || no_pos_device==true) ){
					alert("Please select the Credit Card Type.");
					return false;
				}else if($('#payment_mode').val()=='Credit Card' && $('#cc_no').val()=='' && (!pos_device || no_pos_device==true) ){
					alert("Please enter the Credit Card Number.");
					return false;
				}else if($('#payment_mode').val()=='Credit Card' && $('#cc_exp_date').val()=='' && (!pos_device || no_pos_device==true) ){
					alert("Please enter the Credit Card Exp. Date.");
					return false;
				}/*else if(document.getElementById('comment').value==""){
					alert("Please enter the comment");
					return false;
				}*/else{
					var v = parseFloat(document.getElementById('paid_amount').value);
					if( isNaN(v) ){
						alert("Please enter valid Pre Payment amount.");
						return false;
					} else if( v < 0 ) {
						alert("Negative value for Pre Payment amount is not allowed.");
						return false;
					}
					else {
                        if($('#payment_mode').val()=='Credit Card' && typeof(make_cccard_payment)!='undefined' && pos_device && no_pos_device==false ){
                            make_cccard_payment();
                        }else {
                            pos_submit_frm();
                        }
					}
				}
				if(patient_id>0){
					//print_fun(patient_id);
				}
			}
            
            function pos_submit_frm(){
                dgi("btn_submit").click();
                if(document.getElementById('submit_btn'))
                {
                    document.getElementById('submit_btn').disabled=true;
                }
            }
            
			function new_form(id)
			{
				window.location.href="patient_pre_payment.php";	
			}
			function print_fun(patient_id){
				var id=0;
				var pmt_ids='';
				$('.chk_box_css').each(function(){
					if($(this).is(':checked')==true){
						if(pmt_ids!=''){
							pmt_ids += ','+$(this).val();
						}else{
							pmt_ids = $(this).val();
						}
					}
				});
				window.open("patient_pre_payment_receipt.php?id="+id+"&pid="+patient_id+"&pmt_ids="+pmt_ids,'pre_receipt','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');

			}
            
            function expDate() {
                return true;
            }
        
            function stop_editing_pos_trans() {
                alert("You are not allowed to edit POS transactions.");
                //return false;
            }
		
			$(document).ready( function() {
				$('#paid_date').datetimepicker({
					timepicker:false,
					format:jquery_date_format,
					formatDate:'Y-m-d',
					scrollInput:false
				});
                
                if(edit_id>0) {
                    $('#tsys_device_url').prop('disabled', true);
                    $('#moto_trans_mode').prop('disabled', true);
                }else {
                    $('#tsys_device_url').prop('disabled', false);
                    $('#moto_trans_mode').prop('disabled', false);
                }
                
			});
					
		</script>
    </head>
	<body>
     <div class="container-fluid">  
        <form name="patient_pre_payment_form" id="patient_pre_payment_form" action="patient_pre_payment.php" method="post">
        <input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_id;?>">
        <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
        <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
        <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
        <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
        <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />
        <input type="hidden" name="acc_view_only" id="acc_view_only" value="<?php echo $acc_view_only; ?>">
        <input type="hidden" name="acc_view_pay_only" id="acc_view_pay_only" value="<?php echo $acc_view_pay_only; ?>">
        <input type="hidden" name="acc_view_chr_only" id="acc_view_chr_only" value="<?php echo $acc_view_chr_only; ?>">
        <input type="hidden" name="acc_edit_financials" id="acc_edit_financials" value="<?php echo $acc_edit_financials; ?>">
        <div id="main_form" class="whitebox">
        
            <div class="row boxheadertop">
                <div class="col-sm-6 text-left"><h3><?php echo 'Prepayments - '.get_date_format(date('Y-m-d')).' '.date('h:i A').' '.$ins_operator_name_arr[$operator_id]; ?></h3></div>
                <div class="col-sm-6 text-right"><strong><?php echo $patient_name;?></strong></div>
            </div>                            
            <!-- start listing -->
            <div style="height:405px; overflow-y:auto; overflow-x:auto;">
                <table class="table table-striped table-bordered table-hover adminnw">
                <thead>
                    <tr>
                    	<th nowrap><span class="glyphicon glyphicon-print"></span></th>
                        <th nowrap>Date of Transaction</th>
                        <th nowrap>Paid Date</th>
                        <th>Payment</th>
                        <th nowrap>Payment Mode</th>
                        <th nowrap>Payment Method</th>
                        <th>Facility</th>
                        <th>Comment</th>
                        <th>Applied</th>
                        <th>Provider</th>
                        <th>Operator</th>
                        <th nowrap>Deleted By</th>
                        <th>Action</th>
                        <?php if($pos_device) { ?>
                           <th nowrap>POS Status</th>
                        <?php }?>
                    </tr>
                 </thead>
                 <tbody>
                <?php
                 
                    $depo_qry = " select * from patient_pre_payment where patient_id='$patient_id' order by entered_date desc";
                    $depo_mysql = imw_query($depo_qry);
                    $i=0;
                    while($dpRows = imw_fetch_array($depo_mysql)) 
                    {
                        $id=$dpRows['id'];
                        $delClass='';
                        $hyperlinkOpen='';
                        $hyperLinkClose='';
                        $show_entered_date="";
                        $show_deleted_date='';
						if($acc_view_pay_only == 1 || $acc_edit_financials ==0){
							$hyperlinkOpen='<a target="_parent" href="javascript:view_only_acc_call(0);" class="text_10">';
                        }else if($dpRows['log_referenceNumber'] && $dpRows['tsys_status']=='' && $pos_device){
							$hyperlinkOpen='<a target="_parent" href="javascript:stop_editing_pos_trans(0);"   class="text_10">';
                        }else{
							$hyperlinkOpen='<a target="_parent" href="patient_pre_payment.php?edit_id='.$dpRows['id'].'" class="text_10">';
                        }
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
                        $tot_pay_qry=imw_query("select sum(paidForProc+overPayment) as tot_applied_amt from patient_charges_detail_payment_info where patient_pre_payment_id='$id' and deletePayment='0' and unapply='0'");
                        $tot_pay_row=imw_fetch_array($tot_pay_qry);
                        $applied_color="";
                        if($dpRows['apply_payment_type']=="manually"){
                            $applied_color="green";
                            $bgcolor="";
                        }else if($tot_pay_row['tot_applied_amt']>0 && $tot_pay_row['tot_applied_amt']>=$dpRows['paid_amount']){
                            $applied_color="green";
                            $bgcolor="";
                        }
						
						$chk_box_disabled="";
						$chk_box_cls="chk_box_css";
						if($dpRows['del_status']=='1'){
							$chk_box_disabled="disabled";
							$chk_box_cls="";
						}
						
                        $callTo='16';
                        $returncallTo='02';
                        
                    ?>	
                        <tr style="background-color:<?php echo $applied_color; ?>">
                        	<td class="<?php echo $bgcolor.$delClass;?>">
                               <div class="checkbox">
                                    <input class="<?php echo $chk_box_cls; ?>" type="checkbox" id="pmt_id_<?php echo $dpRows['id']; ?>" name="pmt_id[]" value="<?php echo $dpRows['id']; ?>" <?php echo $chk_box_disabled; ?>>
                                    <label for="pmt_id_<?php echo $dpRows['id']; ?>"></label>
                                </div>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $show_entered_date; echo $hyperlinkClose; ?>
                            </td>
                            <?php 
                                $show_paid_date="";
                                if($dpRows['paid_date']!='0000-00-00' && $dpRows['paid_date']!=""){
                                    $show_paid_date = get_date_format(date("Y-m-d",strtotime($dpRows['paid_date']))); 
                                }
                            ?>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $show_paid_date; echo $hyperlinkClose; ?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo numberFormat($dpRows['paid_amount'],2,'yes'); echo $hyperlinkClose; ?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $dpRows['payment_mode']; echo $hyperlinkClose; ?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                
                                    <?php 
                                        echo $hyperlinkOpen;
                                        if($dpRows['payment_mode']=='Check' || $dpRows['payment_mode']=='Money Order'){
                                            echo $dpRows['check_no'];
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
                                            
                                            $dpRows['cc_no']=substr(trim($dpRows['cc_no']),-4);
                                            echo $credit_card_company.' - '.$dpRows['cc_no'].' - '.$dpRows['cc_exp_date'];
                                        }
                                        echo $hyperlinkClose;
                                    ?>
                                    
                            </td>
       				 		<td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $fac_arr[$dpRows['facility_id']]; echo $hyperlinkClose;?>
                            </td>
                            <td style="width:10%" class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $dpRows['comment']; echo $hyperlinkClose;?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                    <?php 
                                        echo $hyperlinkOpen;
                                        if($dpRows['apply_payment_type']=="manually"){
                                            echo "Manually";
                                        }else if($tot_pay_row['tot_applied_amt']>0){
                                            echo numberFormat($tot_pay_row['tot_applied_amt'],2,'yes');
                                        }
                                        echo $hyperlinkClose;
                                    ?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                <?php echo $hyperlinkOpen; echo $users_arr[$dpRows['provider_id']]; echo $hyperlinkClose;?>
                            </td>
                            <td class="<?php echo $bgcolor.$delClass;?>">
                                  <?php
                                        echo $hyperlinkOpen;	 
                                        echo $ins_operator_full_name_arr[$dpRows['entered_by']];
                                        echo $hyperlinkClose;
                                ?>
                            </td>
                            <td class="<?php echo $bgcolor;?>">
                              <?php
                                    echo $show_deleted_date;
                              ?>
                            </td>
                            <td class="<?php echo $bgcolor;?>">
                            <?php   $log_is_api=0;
                                    if($dpRows['log_referenceNumber'] && $dpRows['tsys_status']=='' && $pos_device){
                                    $logSql='select TransactionNumber,is_api from tsys_possale_transaction where log_referenceNumber="'.$dpRows['log_referenceNumber'].'" and patient_id="'.$patient_id.'" ';
                                    $logRs=imw_query($logSql);
                                    $logRow=imw_fetch_assoc($logRs);
                                    $log_is_api=$logRow['is_api'];
                            }?>
                                
                                <?php if($dpRows['del_status']=='0'){ ?>
                                    <a href="<?php if($acc_view_pay_only == 1 || $acc_edit_financials ==0 ){ ?> javascript:view_only_acc_call(0); 
                                    <?php } else if($dpRows['log_referenceNumber'] && $dpRows['tsys_status']=='' && $pos_device ){ ?> javascript:stop_editing_pos_trans(0); 
                                    <?php }else{ ?>patient_pre_payment.php?edit_id=<?php echo $dpRows['id']; }?>" title="Edit Record">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                    </a>
                                &nbsp;
                                <?php if($dpRows['log_referenceNumber'] && $dpRows['tsys_status']=='' && $pos_device){
                                    $tsys_paid_amount=$dpRows['paid_amount'];
                                    ?>
                                    <a id="voidbtn_<?php echo $dpRows['tsys_transaction_id'];?>" data-is_api="<?php echo $log_is_api;?>" onClick="void_transaction('<?php echo $dpRows['tsys_transaction_id'];?>','<?php echo $tsys_paid_amount;?>','<?php echo $callTo;?>','<?php echo $dpRows['log_referenceNumber'];?>','<?php echo $id;?>',this);" href="#" title="Void Transaction">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                <?php } else { ?>
                                    <a onClick="javascript:dep_delete('<?php echo $dpRows['id']; ?>')" href="#" title="Delete Record">
                                      <span class="glyphicon glyphicon-trash"></span>
                                   </a>
                                <?php } ?>
                            <?php } ?>
                            </td>
                            
                            <?php if($pos_device) { ?>
                                <td class="<?php echo $bgcolor;?>">
                                    <?php if($dpRows['log_referenceNumber'] && $dpRows['tsys_status']=='' && $dpRows['del_status']=='0'){
                                              $tsys_paid_amount=$dpRows['paid_amount'];
                                              echo '&nbsp;<button type="button" class="btn btn-primary" data-is_api="'.$log_is_api.'"  id="returnbtn_'.$dpRows['tsys_transaction_id'].'" onclick="return_transaction(\''.$dpRows['tsys_transaction_id'].'\',\''.$tsys_paid_amount.'\',\''.$returncallTo.'\',\''.$dpRows['log_referenceNumber'].'\',\''.$id.'\',this)">Return</button>';
                                          } else if($dpRows['tsys_status']!='') {
                                              echo $dpRows['tsys_status'];
                                          } else {
                                              echo '&nbsp;';
                                          }
                                    ?>
                                </td>
                            
                            <?php } ?>
                            
                        </tr>
                      <?php  
                    }
                    if(imw_num_rows($depo_mysql)==0)  {
                        $colspan="13";
                        if($pos_device) {$colspan="14";}
                    ?>
                        <tr>
                            <td colspan="<?php echo $colspan;?>">No record found.</td>
                        </tr>
                   <?php		
                    }
                	?>	
                </table>
            </div>
            <!-- end listing -->
            <?php
                if($edit_id>0)
                {
                    $editRecordQry = "select * from patient_pre_payment where id ='$edit_id'";
                    $editRecordQryMysql = imw_query($editRecordQry);
                    $editRow = imw_fetch_array($editRecordQryMysql);
                }
            ?> 
            <div class="row">
                <div class="col-sm-12">     
             	<table class="table table-striped">
                 <thead>
                     <tr class="grythead">
                        <td colspan="3">
                        <?php 
                            if($edit_id>0){ 
                                echo 'Edit Patient Pre Payment';
                            }else{
                                echo 'Add Patient Pre Payment';
                            }
                         ?>
                        </td>
                     </tr>
                 </thead>
                 <tbody>
                 	<tr>
                        <td colspan="3">
                        	<div class="row">
                            	<div class="col-sm-4">
                                	<div class="row">                   
                            			<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Pre Payment</label>
                                                 <div class="input-group">
            									 	<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                                                	<input name="paid_amount" id="paid_amount" type="text" value="<?php echo $editRow['paid_amount']; ?>" class="form-control"/>
                                            	</div>
                                            </div>
                                        </div>
                            			<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Paid Date</label>
                                                <?php 
													$paid_date="";
													if($editRow['paid_date']!='0000-00-00' && $editRow['paid_date']!="" && $edit_id>0){
														$paid_date = date("Y-m-d",strtotime($editRow['paid_date'])); 
													}else{
														$paid_date = date("Y-m-d"); 
													}
												?>
												<input name="paid_date" id="paid_date" type="text" value="<?php echo get_date_format($paid_date); ?>" class="form-control" onBlur="checkdate(this);"/>
                                            </div>
                                        </div>
                            			<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Method</label>
                                                <select name="payment_mode" id="payment_mode" class="form-control minimal" onChange="return changeMode();">
                                                    <?php foreach($payment_method_arr as $method_key=>$method_val){?>
                                                        <option value="<?php echo $method_val; ?>" <?php if(strtolower($editRow['payment_mode'])==strtolower($method_val)) echo 'SELECTED'; ?>><?php echo $method_val; ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="col-sm-5">
                                	<div class="row" id="checkTd" style="display:<?php if($editRow['payment_mode']=="Check" || $editRow['payment_mode']=="Money Order"){ echo "block"; } else { echo "none"; } ?>">
                            			<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Check #</label>
                                        		<input name="check_no" id="check_no" type="text" value="<?php echo $editRow['check_no']; ?>" class="form-control"/>  
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row" id="ccTd" style="display:<?php if($editRow['payment_mode']=="Credit Card"){ echo "block"; } else { echo "none"; } ?>;">
                            			<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Type</label>
                                                <select name="credit_card_co" id="credit_card_co" class="form-control minimal">
                                                        <option value=""></option>
                                                        <option <?php if($editRow['credit_card_co']=="AX"){ echo "selected='selected'"; } ?> value="AX">American Express</option>
                                                        <option <?php if($editRow['credit_card_co']=="Care Credit"){ echo "selected='selected'"; } ?> value="Care Credit">Care Credit</option>
                                                        <option <?php if($editRow['credit_card_co']=="Dis"){ echo "selected='selected'"; } ?> value="Dis">Discover</option>
                                                        <option <?php if($editRow['credit_card_co']=="MC"){ echo "selected='selected'"; } ?> value="MC">Master Card</option>
                                                        <option <?php if($editRow['credit_card_co']=="Visa"){ echo "selected='selected'"; } ?> value="Visa">Visa</option>
                                                        <option <?php if($editRow['credit_card_co']=="Other"){ echo "selected='selected'"; } ?> value="Other">Other</option>
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>CC#</label>
                                                <input name="cc_no" id="cc_no" type="text" value="<?php echo $editRow['cc_no']; ?>" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label>Exp. Date</label>
                                                <input type="text" name="cc_exp_date" id="cc_exp_date" value="<?php echo $editRow['cc_exp_date']; ?>" class="form-control" />
                                            </div>
                                       </div>
                                    </div>
                                    
                                </div>
                                <?php if($pos_device) { ?>
                                    <div class="col-sm-3">
                                        <?php
                                            $laneID = '10000003';
                                            $cc_class = 'pre_payment';
                                            $target_ids = 'payment_mode';
                                            $editRow_tsys_trans_id = $editRow['tsys_transaction_id'];
                                            $chk_trans_edit_id=$edit_id;
                                            include (dirname(__FILE__).'/../../accounting/pos/include_cc_payment.php');
                                        ?>
                                    </div>
                                <?php } ?>
                                    
                            </div>
                            <div class="row">
                            	<div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Facility</label>
                                       <select name="pmt_fac" id="pmt_fac" class="form-control minimal">
                                            <?php 
                                                foreach($fac_arr as $fac_key=>$fac_val){
                                                    if($editRow['facility_id']>0){
                                                        $pmt_fac=$editRow['facility_id'];
                                                    }else{
                                                        $pmt_fac=$_SESSION['login_facility'];
                                                    }
                                            ?>
                                            <option <?php if($fac_key==$pmt_fac){ echo "selected='selected'"; } ?> value="<?php echo $fac_key; ?>"><?php echo $fac_val; ?></option>
                                            <?php } ?>
                                       </select>
                                     </div> 
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Provider</label>
                                       <select name="pmt_prov" id="pmt_prov" class="form-control minimal">
                                       <option value=""></option>
                                            <?php 
                                                foreach($users_arr as $prov_key=>$prov_val){
                                                    if($editRow['provider_id']>0 || $edit_id>0){
                                                        $pmt_prov=$editRow['provider_id'];
                                                    }else{
														if($patient_provider_id>0){
															$pmt_prov=$patient_provider_id;
														}else{
															$pmt_prov=$sa_doctor_id;
														}
                                                    }
                                            ?>
                                            <option <?php if($prov_key==$pmt_prov){ echo "selected='selected'"; } ?> value="<?php echo $prov_key; ?>"><?php echo $prov_val; ?></option>
                                            <?php } ?>
                                       </select>
                                     </div> 
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>Comment</label>
                                        <textarea cols="145" rows="2" name="comment" id="comment" class="form-control"><?php echo $editRow['comment']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                 </tbody>
                </table>
            	</div>
            </div>
              
       </div>
       
        <footer id="footer_bar" class="text-right">     
			<?php if($edit_id>0){ ?>
                 <button type="button" name="update" value="Update" class="btn btn-success" onClick="save_but_disable();">Update</button>
                 <button type="button" name="new" value="New" class="btn btn-success" onClick="new_form();">New</button>
            <?php } else { ?>
                 <button type="button" name="save" value="Save & Print Receipt" class="btn btn-success" onClick="save_but_disable('<?php echo $patient_id;?>');" >Save & Print Receipt</button>
            <?php } ?>
                <button type="button" id="print_btn" name="print_btn" value="Print Receipt" class="btn btn-default" onClick="window.print_fun('<?php echo $patient_id; ?>');"><span class="glyphicon glyphicon-print"></span> Print</button>
                <button type="submit" id="btn_submit" name="btn_submit" value="Save" class="btn btn-success hide">Save</button>
                <button type="button" name="close" value="Close" class="btn btn-danger" onClick="window.close();">Close</button>
            <?php if($pos_device) { ?>
                <button type="button" name="pos_log" value="pos_log" class="btn btn-primary" onClick="window.show_transaction_popup();">POS Log</button>
            <?php } ?>    
       </footer> 
       </form>
    </div>
    <?php
    if($_REQUEST['prnt']>0){
		echo "<script type='text/javascript'>print_fun(".$_REQUEST['prnt'].");</script>";
	}
	 ?>
    <div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
        <div class="loading_container">
            <div class="process_loader"></div>
            <div id="div_loading_text" class="text-info"></div>
        </div>
    </div>
    </body>
</html>