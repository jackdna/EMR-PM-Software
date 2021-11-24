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
$without_pat="yes"; 
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
$pg_title = 'Batch Process List';

$patient_id=$_SESSION['patient'];
$srh_status=$_REQUEST['srh_status'];
if($_REQUEST['era_action']=="del_batch"){
	$batch_files_id_imp=implode(',',$_REQUEST['batch_files_id']);
	imw_query("update manual_batch_file set del_status='1' where batch_id in($batch_files_id_imp)");
	imw_query("update manual_batch_transactions set del_status='1' where batch_id in($batch_files_id_imp)");
}
$wrt_foot='<button type="button" class="btn btn-success" id="save_btn" onclick="save_fun();">Save</button>';
show_modal('batch_new_file','Batch Info','',$wrt_foot,'','modal-lg');
?>
<form action="" name="frm_post" method="post">
<input type="hidden" name="era_action" id="era_action" value="">
<div class="table-responsive" style="height:<?php echo $_SESSION['wn_height']-325;?>px; overflow:auto; width:100%;">
	<table class="table table-bordered table-hover table-striped" style="margin-bottom:2px;">
    	<tr class="grythead">
            <th>
                <div class="checkbox">
                    <input type="checkbox" id="chkbx_all" name="chkbx_all" onClick="return chk_all();">
                    <label for="chkbx_all"></label>
                </div>
            </th>
            <th>Batch Date</th>
            <th>Batch Name</th>
            <th>Tracking <?php getHashOrNo();?></th>
            <th>Batch Amount</th>
            <th>T. Charged</th>
            <th>T. Allowed</th>
            <th>T. Paid</th>
            <th>T. Adj.</th>
            <th>DOR</th>
            <th>DOT</th>
            <th>Batch Owner</th>
            <th>Batch Description &nbsp;
            	<select name="srh_status" class="selectpicker" data-width="auto" onChange="batch_files('search');">
                    <option value="open" <?php if($srh_status=='open'){echo "selected";} ?>>OPEN</option>
                    <option value="posted" <?php if($srh_status=='posted'){echo "selected";} ?>>POSTED</option>
                    <option value="deleted" <?php if($srh_status=='deleted'){echo "selected";} ?>>DELETED</option>
                </select>
            </th>
            <th>Function</th>
        </tr>
        <?php
		$chk_status=0; $showDeleted = false;
		if($_REQUEST['srh_status']=='posted'){
			$chk_status=1;
		}else if($_REQUEST['srh_status']=='open'){
			$chk_status=0;
		}
		if($_REQUEST['srh_status']=='deleted'){
			$delpost_querypart = "del_status=1 ";
			$showDeleted = true;
		}else{ 
			$delpost_querypart = "del_status!=1 and post_status='$chk_status' ";
			$showDeleted = false;
		}

		$sql=imw_query("select * from manual_batch_file where ".$delpost_querypart." order by batch_id desc");
		while($row=imw_fetch_array($sql)){
			$file_arr[$row['batch_id']]=$row;
			$file_opr_arr[$row['operator_id']]=$row['operator_id'];
			$file_id_arr[$row['batch_id']]=$row['batch_id'];
		}
		
		$file_opr_imp=implode(',',$file_opr_arr);
		$usr_qry=imw_query("select id,fname,mname,lname from users where id in($file_opr_imp)");
		while($usr_row=imw_fetch_array($usr_qry)){
			$phy_ins_name[$usr_row['id']]=substr($usr_row['fname'],0,1).substr($usr_row['lname'],0,1);
		}
		
		$file_id_imp=implode(',',$file_id_arr);
		$qry=imw_query("select batch_id,trans_amt,payment_claims from manual_batch_transactions where batch_id in($file_id_imp) and del_status!='1'");
		while($row=imw_fetch_array($qry)){
			$batch_trans_arr[$row['batch_id']][]=$row;
			if($row['payment_claims']=='Paid' or $row['payment_claims']=='Deposit' or $row['payment_claims']=='Interest Payment'){
				$batch_paid_trans_arr[$row['batch_id']][]=$row['trans_amt'];
			}else if($row['payment_claims']=='Negative Payment'){
				$batch_paid_trans_arr[$row['batch_id']][]=-$row['trans_amt'];
			}
		}
		
		$qry=imw_query("select upload_lab_rad_data_id,uplaod_primary_id from upload_lab_rad_data where uplaod_primary_id in($file_id_imp) and upload_status='0'");
		while($row=imw_fetch_array($qry)){
			$upload_lab_rad_arr[$row['uplaod_primary_id']][]=$row;
		}

		$qry=imw_query("select batch_id,batch_modified_by,batch_modified_date,batch_modified_time from manual_batch_modified where batch_id in($file_id_imp) order by batch_modified_id desc");
		while($row=imw_fetch_array($qry)){
			$batch_mod_arr[$row['batch_id']][]=$row;
		}

		foreach($file_arr as $file_key => $file_val){
			$row_qry=$file_arr[$file_key];
			$default_payment_date=$default_transaction_date=$in_house_code=$insurance_id="";
			$insurance_id=$row_qry['insurance_id'];
			$batch_id=$row_qry['batch_id'];
			$post_status=$row_qry['post_status'];
			$lock_to_user=$row_qry['lock_to_user'];
			$srh_member=$row_qry['batch_member'];
			$batch_date = get_date_format($row_qry['batch_date']);
			
			if($row_qry['default_payment_date']!='0000-00-00'){
				$default_payment_date = get_date_format($row_qry['default_payment_date']);
			}
			if($row_qry['default_transaction_date']!='0000-00-00'){
				$default_transaction_date = get_date_format($row_qry['default_transaction_date']);
			}
			
			if($srh_member){
				$batch_member=explode(',',$row_qry['batch_member']);
			}
		
			$created_date = get_date_format($row_qry['created_date']);
			$created_time=$row_qry['created_time'];
			$operator_name = $phy_ins_name[$row_qry['operator_id']];
			
			if(in_array($_SESSION['authId'],$batch_member)){
				$sel_mem_arr=1;
			}else{
				$sel_mem_arr=0;
			}
			
			$pay_amt=array_sum($batch_trans_arr[$batch_id]['trans_amt']);
			if(!$pay_amt){
				$pay_amt=0;
			}
			if($pay_amt==$row_qry['total_payment']){
				$show_balance_status="<font color='Green'>Balanced</font>";
			}else{
				$show_balance_status="<font color='red'>Out Of Balance";
			}
			$chk_box_disabled="disabled";
			$chk_box_cls="";
			if($sel_mem_arr>0 && $_REQUEST['srh_status']!='deleted'){
				$chk_box_disabled="";
				$chk_box_cls="chk_box_css";
			}
	?>
		<tr>
			<td class="text-center">
            	<div class="checkbox">
                    <input class="<?php echo $chk_box_cls; ?>" type="checkbox" id="batch_files_id_<?php echo $batch_id; ?>" name="batch_files_id[]" value="<?php echo $batch_id; ?>" <?php echo $chk_box_disabled; ?>>
                    <label for="batch_files_id_<?php echo $batch_id; ?>"></label>
                </div>
			</td>
            <td class="text-nowrap"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $batch_date; ?></a></td>
			<td><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $row_qry['batch_name']; ?></a></td>
			<td><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $row_qry['tracking']; ?></a></td>
            <td class="text-right"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo numberformat($row_qry['total_payment'],2); ?></a></td>
            <td class="text-right"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo numberformat($row_qry['total_bill_amount'],2); ?></a></td>
			<td class="text-right"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo numberformat($row_qry['total_allow_amount'],2); ?></a></td>
            <td class="text-right"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo numberformat(array_sum($batch_paid_trans_arr[$batch_id]),2); ?></a></td>
			<td class="text-right"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo numberformat(($row_qry['total_writeoff_amt']+$row_qry['total_adj_amt']),2); ?></a></td>
			<td class="text-nowrap"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $default_payment_date; ?></a></td>
            <td class="text-nowrap"><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $default_transaction_date; ?></a></td>
			<td><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $operator_name; ?></a></td>
			<td><a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?>new_transaction('<?php echo $batch_id; ?>','<?php echo $sel_mem_arr; ?>'); <?php } ?>"><?php echo $row_qry['batch_desc']; ?></a></td>
			<td nowrap>
			<?php 
				if($showDeleted==false){
					$batch_img_chk=0;
					$batch_img_chk=count($upload_lab_rad_arr[$batch_id]);
					if($batch_img_chk>0){
						$batch_link_img="../../library/images/scanDcs_active.png";	
					}else{
						$batch_link_img="../../library/images/scanDcs_deactive.png";
					}
			?>
				<a id="scn_id_<?php echo $batch_id; ?>"  href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0); <?php }else{ ?> openScan('<?php echo $batch_id; ?>'); <?php } ?>" >
				   <img src="<?php echo $batch_link_img; ?>" alt="Scan" border="0">
				</a>
                <a href="javascript:<?php if($bi_edit_batch == 0 && $post_status>0){ ?> view_only_acc_call(0); <?php }else{ ?> batch_files('new_batch','<?php echo $batch_id; ?>'); <?php } ?>"><img src="../../library/images/edit.png" alt="Edit" border="0"></a>
			<?php }?>
			</td>
		</tr>
	<?php } ?>
	<?php if(count($file_arr)==0){ ?>
		<tr>
			<td colspan="14" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
		</tr>
	<?php } ?>
    </table>
</div>
</form>
<script type="text/javascript">
function diff_balance(batch_tot,trans_tot,dis){
	var balance_tot=eval(batch_tot)-eval(trans_tot);
	var val="Batch File Total Amount: "+batch_tot+"<br>";
	val +="Transactions Total Amount: "+trans_tot+"<br>";
	val +="Balance Amount: "+balance_tot;
	var cursor_point = getPosition();
	document.getElementById("ballance_diff").style.top= (parseInt(cursor_point.y) + parseInt(0));
	document.getElementById("ballance_diff").style.left= 358;
	document.getElementById("ballance_diff").innerHTML=val;
	document.getElementById("ballance_diff").style.display=dis;
}
function new_transaction(id,sel_mem_arr){
	if(sel_mem_arr==0){
		top.fAlert("You can not create the batch transaction.");
	}else{
		var sc_wd=(screen.availWidth)-100;
		var sc_hg=(screen.availHeight)-120;
		window.open("batch_transactions.php?b_id="+id,'Batch','width='+sc_wd+',height='+sc_hg+',top=10,left=10,location=1,scrollbars=no,resizable=1');
	}
}
function post_apply(id,lock_id,sel_mem_arr){
	var chk_lock='<?php echo $_SESSION['authId'];?>';
	if(lock_id>0 && chk_lock!=lock_id){
		top.fAlert("You can not post the batch file.");
	}else if(sel_mem_arr==0 && lock_id==0){
		top.fAlert("You can not post the batch file.");
	}else{
		top.fAlert("File has been posted successfully");
		document.getElementById('post_rec').value=id;
		document.frm_post.submit();
	}
}
function openScan(bat_id){
	var url = top.JS_WEB_ROOT_PATH+'/interface/billing/scan/view_batch_images.php?scanOrUpload=upload&upload_from=batch_processing&lab_id='+bat_id;
	top.popup_win(url,'resizable=yes');
	
	/*top.fancyModal('<iframe name="messiframe" id="messiframe" src="'+url+'" frameborder=0 style="height:550px;width:670px;"></iframe>','Scan/Upload');*/
}

function batch_files(arg,id){
	var flag = 0;
	$('#era_action').val('');
	if(arg=="del_batch"){
		$('#era_action').val(arg);
		$('.chk_box_css').each(function(){
			if($(this).is(':checked')==true){
				flag = flag+1;
			}
		});
		if(flag<=0){
			top.fAlert("Please select checkbox to delete.");
			return false;
		}else{
			var ask = "Do you want to delete all selected records?";
			top.fancyConfirm(ask,'', "window.top.fmain.document.frm_post.submit()");
		}
	}else if(arg=="new_batch"){
		$('#batch_new_file .modal-content .modal-body').load('add_batch_process.php?edit_id='+id);
		$('#batch_new_file').modal('show');
	}else{
		document.frm_post.submit();
	}
}



var mainBtnArr = new Array();
mainBtnArr[0] = new Array("new_batch","New Batch","top.fmain.batch_files('new_batch','');");
mainBtnArr[1] = new Array("del_batch","Delete","top.fmain.batch_files('del_batch');");
top.btn_show("PPR",mainBtnArr);
top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
</script>