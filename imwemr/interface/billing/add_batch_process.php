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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 
include_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
$patient_id=$_SESSION['patient'];
$operator_id=$_SESSION['authId'];
$edit_id=$_REQUEST['edit_id'];

//---- GET INSURANCE COMPANIES NAME ---
$ins_th = insurance_provider('1','','typeahead');
$global_date_format = phpDateFormat();

if($_REQUEST['frm_submit']=='yes'){
	$batch_name=addslashes($_REQUEST['batch_name']);
	$tracking=$_REQUEST['tracking'];
	$batch_date=getDateFormatDB($_REQUEST['batch_date']);
	
	$total_bill_amount=$_REQUEST['total_bill_amount'];
	$total_allow_amount=$_REQUEST['total_allow_amount'];
	$total_payment=$_REQUEST['total_payment'];
	$batch_member=@implode(',',$_REQUEST['batch_member']);
	$created_date=date('Y-m-d');
	$created_time=date('h:i A');
	$modified_time=date('h:i A');
	$operator_id=$operator_id;
	$default_write_code=$_REQUEST['default_write_code'];
	$default_adj_code=$_REQUEST['default_adj_code'];
	$default_check_no=addslashes($_REQUEST['default_check_no']);
	$default_ins_id="";
	if($_REQUEST["default_insurance"]!=""){
		$default_insurance_exp=explode('*',$_REQUEST["default_insurance"]);
		if($default_insurance_exp[1]>0){
			$default_ins_id = $default_insurance_exp[1];
		}else{
			$default_ins_id = addslashes($_REQUEST["default_ins_id"]);
		}
	}
	
	$default_payment_date=getDateFormatDB($_REQUEST['default_payment_date']);
	$default_transaction_date=getDateFormatDB($_REQUEST['default_transaction_date']);
	$batch_owner_id=$_REQUEST['batch_owner_id'];
	$batch_desc=addslashes($batch_desc);
	$default_pay_location=$_REQUEST['default_pay_location'];
	$default_payment_method=$_REQUEST['default_payment_method'];
	$default_payment_method=$_REQUEST['default_payment_method'];
	if($_REQUEST['batch_id']){
		$batch_id=$_REQUEST['batch_id'];
		$update_batch="update manual_batch_file set 
		batch_name='$batch_name',tracking='$tracking',
		batch_date='$batch_date',total_bill_amount='$total_bill_amount',
		total_allow_amount='$total_allow_amount',
		total_payment='$total_payment',
		batch_desc='$batch_desc',
		default_payment_date='$default_payment_date',
		default_transaction_date='$default_transaction_date',
		default_check_no='$default_check_no',
		insurance_id='$default_ins_id',
		default_payment_method='$default_payment_method'";
		if($_REQUEST['post_status']<=0){
			$update_batch.=",batch_member='$batch_member',default_write_code='$default_write_code',default_adj_code='$default_adj_code',default_pay_location='$default_pay_location'";
		}
		$update_batch.=" where batch_id='$batch_id'";
		imw_query($update_batch);
		
		$ins_batch_modified="insert into manual_batch_modified set 
		batch_modified_date='$created_date',batch_modified_time='$modified_time',
		batch_modified_by='$operator_id',batch_id='$batch_id'";
		imw_query($ins_batch_modified);
	}else{
		$ins_batch="insert into manual_batch_file set 
		batch_name='$batch_name',tracking='$tracking',
		batch_date='$batch_date',batch_owner='$batch_owner_id',
		total_bill_amount='$total_bill_amount',
		total_allow_amount='$total_allow_amount',
		total_payment='$total_payment',batch_member='$batch_member',
		batch_desc='$batch_desc',operator_id='$operator_id',
		default_write_code='$default_write_code',default_adj_code='$default_adj_code',
		default_payment_date='$default_payment_date',
		default_transaction_date='$default_transaction_date',
		default_check_no='$default_check_no',
		insurance_id='$default_ins_id',
		created_date='$created_date',created_time='$created_time',
		default_pay_location='$default_pay_location',default_payment_method='$default_payment_method'";
		imw_query($ins_batch);
	}
	echo "sucess";
	exit();
}	
if($edit_id){
	$sel_batch=imw_query("select * from manual_batch_file where batch_id='$edit_id'");
	$row_batch=imw_fetch_array($sel_batch);
	$batch_date=get_date_format($row_batch['batch_date']);
	
	$batch_owner_id=$row_batch['batch_owner'];
	$default_write_code=$row_batch['default_write_code'];
	$default_adj_code=$row_batch['default_adj_code'];
	$default_payment_date=get_date_format($row_batch['default_payment_date']);
	$default_transaction_date=get_date_format($row_batch['default_transaction_date']);
	$tracking=$row_batch['tracking'];
	$total_bill_amount=$row_batch['total_bill_amount'];
	$total_allow_amount=$row_batch['total_allow_amount'];
	$total_payment=$row_batch['total_payment'];
	$default_ins_id = $row_batch["insurance_id"];
	$default_pay_location = $row_batch["default_pay_location"];
	$default_payment_method = $row_batch["default_payment_method"];
	$post_status = $row_batch["post_status"];
}else{
	$today_dat=date('Y-m-d');
	$sel_num_file=imw_query("select batch_id from manual_batch_file where created_date='$today_dat'");
	$fet_num_file=imw_num_rows($sel_num_file)+1;
	$batch_owner_id=$operator_id;
	$batch_date=date(''.$global_date_format.'');
	$tracking=date(''.$global_date_format.'').'-'.$fet_num_file;
	
	$sel_policies_batch=imw_query("select batch_payment_method from copay_policies");
	$row_policies_batch=imw_fetch_array($sel_policies_batch);
	$batch_payment_method=$row_policies_batch['batch_payment_method'];
	$row_batch["default_payment_method"]=$batch_payment_method;
}
if($row_batch['batch_member']){
	$batch_member_arr=explode(',',$row_batch['batch_member']);
}else{
	$batch_member_arr[]=$operator_id;
}

$usr_qry=imw_query("select id,fname,mname,lname from users where id in($batch_owner_id)");
$usr_row=imw_fetch_array($usr_qry);
$batch_owner = substr($usr_row['fname'],0,1).substr($usr_row['lname'],0,1);
$txt_readonly=$txt_disabled="";
$date_pick_css=" date-pick";
if($post_status>0){
	$txt_readonly="readonly";
	$date_pick_css="";
	$txt_disabled=" disabled";
}
?>
<style>.sel_prob_cls{ color:#F00;}</style>
<form name="batch_file" id="batch_file" method="post" action="add_batch_process.php">
	<input type="hidden" name="batch_id" value="<?php echo $edit_id;?>">
    <input type="hidden" name="post_status" id="post_status" value="<?php echo $post_status;?>">
	<input type="hidden" name="frm_submit" value="yes">
    <div class="row">
    	<div class="col-sm-3">
			<label for="batch_name">Batch Name</label>
            <input type="text" name="batch_name" id="batch_name" value="<?php echo $row_batch['batch_name'];?>" class="form-control">	
		</div>
        <div class="col-sm-3">
			<label for="tracking">Tracking#</label>
            <input type="text" name="tracking" id="tracking" value="<?php echo $tracking;?>" class="form-control">	
		</div>
        <div class="col-sm-3">
			<label for="batch_date">Date Created</label>
            <div class="input-group">
                <input type="text" name="batch_date" id="batch_date" value="<?php echo $batch_date; ?>" class="form-control <?php echo $date_pick_css; ?>" <?php echo $txt_readonly; ?>>
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
            </div>
		</div>
        <div class="col-sm-3">
			<label for="batch_owner">Batch Owner</label>
            <input type="text" name="batch_owner" id="batch_owner" value="<?php echo $batch_owner;?>" class="form-control" <?php echo $txt_readonly; ?>>
            <input type="hidden" name="batch_owner_id" id="batch_owner_id" value="<?php echo $batch_owner_id;?>">	
		</div>
    </div>
    <div class="row">
    	<div class="col-sm-3">
			<label for="total_payment">Batch Payments</label>
             <div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
            	<input type="text" name="total_payment" id="total_payment" value="<?php echo $total_payment;?>" class="form-control" <?php echo $txt_readonly; ?>>	
			</div>
        </div>
    	<div class="col-sm-3">
			<label for="total_bill_amount">Total Charged</label>
            <div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
            	<input type="text" name="total_bill_amount" id="total_bill_amount" value="<?php echo $total_bill_amount;?>" class="form-control" <?php echo $txt_readonly; ?>>	
			</div>
        </div>
        <div class="col-sm-3">
			<label for="total_allow_amount">Total Allowed</label>
             <div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
            	<input type="text" name="total_allow_amount" id="total_allow_amount" value="<?php echo $total_allow_amount;?>" class="form-control" <?php echo $txt_readonly; ?>>	
			</div>
        </div>
        
        <div class="col-sm-3">
			<label for="batch_member">Batch Members</label>
            <select multiple name="batch_member[]" id="batch_member" class="selectpicker" data-actions-box="true" data-width="99%"  data-size="15" <?php echo $txt_disabled; ?>>
                <?php
                    $andUsrQry="";
                    if(!$edit_id) { $andUsrQry = " AND delete_status = '0' "; }
                    $sel_usr=imw_query("select lname,fname,mname,id,delete_status from users where access_pri like'%Accounting%' ".$andUsrQry." order by  lname,fname");
                    while($urs_row=imw_fetch_array($sel_usr)){
                    $del_color_cls="";
                    if($urs_row['delete_status']>0){	
                        $del_color_cls='class="sel_prob_cls"';
                    }	
                    if(($urs_row['delete_status']<=0) || (in_array($urs_row['id'],$batch_member_arr))) {
                ?>
                    <option value="<?php echo $urs_row['id'];?>" <?php if(in_array($urs_row['id'],$batch_member_arr)){echo "selected";}?> <?php echo $del_color_cls; ?>>
                        <?php 
                            if($urs_row['lname']){
                                $comma=', ';
                            }
                            echo $urs_row['lname'].$comma.$urs_row['fname'];
                         ?>
                    </option>
                <?php	
                    }
                    } 
				?>
            </select>
		</div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <label for="default_write_code">Default Write Off Code</label>
            <select name="default_write_code" id="default_write_code" class="form-control minimal" data-width="99%" <?php echo $txt_disabled; ?>>
                <option value=""></option>
                <?php
                    $sel_wrt=imw_query("select w_code,w_id from write_off_code order by  w_code");
                    while($wrt_row=imw_fetch_array($sel_wrt)){
                ?>
                    <option value="<?php echo $wrt_row['w_id'];?>" <?php if($wrt_row['w_id']==$default_write_code){echo "selected";}?>>
                        <?php 
                            echo $wrt_row['w_code'];
                         ?>
                    </option>
                <?php } ?>
            </select>	
        </div>
        <div class="col-sm-3">
            <label for="default_adj_code">Default Adj. Code</label>
             <select name="default_adj_code" id="default_adj_code" class="form-control minimal" data-width="99%" <?php echo $txt_disabled; ?>>
                <option value=""></option>
                <?php
                    $sel_adj=imw_query("select a_code,a_id from adj_code order by a_code");
                    while($adj_row=imw_fetch_array($sel_adj)){
                ?>
                    <option value="<?php echo $adj_row['a_id'];?>" <?php if($adj_row['a_id']==$default_adj_code){echo "selected";}?>>
                        <?php 
                            echo $adj_row['a_code'];
                         ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-3">
        	<label for="default_payment_date">Default Remittance Date</label>
        	<div class="input-group">
                <input type="text" name="default_payment_date" id="default_payment_date" value="<?php echo $default_payment_date; ?>" class="form-control <?php echo $date_pick_css; ?>" <?php echo $txt_readonly; ?>>
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
            </div>
        </div>
        <div class="col-sm-3">
        	<label for="default_transaction_date">Default Transaction Date</label>
        	<div class="input-group">
                <input type="text" name="default_transaction_date" id="default_transaction_date" value="<?php echo $default_transaction_date; ?>" class="form-control <?php echo $date_pick_css; ?>" <?php echo $txt_readonly; ?>>
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
            </div>
        </div>
    </div>
    <?php
		$default_insurance="";
		foreach($ins_th['typeahead'] as $ins_key=>$ins_val){
			$default_insurance_exp=explode('*',$ins_th['typeahead'][$ins_key]);
			if($default_insurance_exp[1]==$row_batch['insurance_id']){
				$default_insurance_name_exp=explode('-',$ins_th['typeahead'][$ins_key]);
				$default_insurance=$default_insurance_name_exp[0];
			}
		}
	?>   
    <div class="row">
        <div class="col-sm-3">
            <label for="default_insurance">Default Insurance</label>
            <input type="text" name="default_insurance" id="default_insurance" value="<?php echo $default_insurance ;?>" class="form-control" <?php echo $txt_readonly; ?>>
            <input type="hidden" name="default_ins_id" id="default_ins_id" value="<?php echo $row_batch['insurance_id'];?>">
        </div>
        <div class="col-sm-3">
        	<label for="default_payment_method">Default Payment Method</label>
            <select name="default_payment_method" id="default_payment_method" class="form-control minimal" data-width="99%">
                <option value=""></option>
                <?php
                $sel_rec=imw_query("select pm_id,pm_name,del_status from payment_methods order by pm_name");
                while($sel_pm=imw_fetch_array($sel_rec)){
                    if($sel_pm['del_status']==0 || $sel_pm['pm_id']==$row_batch["default_payment_method"]){
                    $txt_color = ($sel_pm['del_status'])?'style="color:red;"':'';
                ?>
                    <option <?php echo $txt_color; ?> value="<?php echo $sel_pm['pm_id'];?>" <?php if($sel_pm['pm_id']==$row_batch["default_payment_method"]){ echo "selected";} ?>><?php echo $sel_pm['pm_name'];?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="col-sm-3">
            <label for="default_check_no">Default Reference No.</label>
            <input type="text" name="default_check_no" id="default_check_no" value="<?php echo $row_batch['default_check_no'];?>" class="form-control">
        </div>
        <div class="col-sm-3">
        	<label for="default_pay_location">Default Pay Location</label>
             <select name="default_pay_location" id="default_pay_location" class="form-control minimal" data-width="99%" <?php echo $txt_disabled; ?>>
                <option value="">Pay Location</option>
                <?php
                    $sel_fac=imw_query("select id,name,facility_type from facility order by name");
                    while($fac_row=imw_fetch_array($sel_fac)){
                	if($edit_id<=0 && $fac_row['facility_type']=="1"){
						$default_pay_location=$fac_row['id'];
					}
				?>
                    <option value="<?php echo $fac_row['id'];?>" <?php if($fac_row['id']==$default_pay_location){echo "selected";}?>><?php echo $fac_row['name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        	<label for="batch_desc">Batch Description</label>
        	<textarea rows="1" name="batch_desc" id="batch_desc" class="form-control" <?php echo $txt_readonly; ?>><?php echo $row_batch['batch_desc'];?></textarea>
        </div>
    </div>  
</form>

<script type="text/javascript">
function save_fun(){
	var formError=0;
	
	/*if($('#batch_name').val()==""){
		top.fAlert("Please enter the batch file name.");
		$('#batch_name').focus();
		formError=1;
		return false;
	}*/
	if($('#tracking').val()==""){
		top.fAlert("Please enter the tracking number.");
		$('#tracking').focus();
		formError=1;
		return false;
	}
	
	if(formError==0){
		var batch_data=$('#batch_file').serialize();
		var url="add_batch_process.php";
		$.ajax({
			type: "POST",
			url: url,
			data:batch_data,
			success: function(resp){
				if(resp!=""){
					$('#batch_new_file').modal('hide');
					window.location.reload();
				}
			}
		});
	}
}
$(function(){
	$('[data-toggle="tooltip"]').tooltip();
	$('.selectpicker').selectpicker();
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:top.global_date_format, //'m-d-Y',
		formatDate:'Y-m-d'
	});
});	
var ins_th_js = <?php echo json_encode($ins_th['typeahead']); ?>;
$('#default_insurance').typeahead({source:ins_th_js});
</script>
