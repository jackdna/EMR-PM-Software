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
$title = "Batch Transactions List";
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/buttons.js"></script>
<script type="text/javascript">
var landingSectionLoader = '<div id="div_loading_image" class="text-center" style="display: block;position: initial;padding-top: 50px;height: 100%;width: 100%;background-color: rgba(0,0,0,0.1);"><div class="loading_container" style="width: auto;"><div class="process_loader"></div><div id="div_loading_text" class="text-info">Loading Data....</div></div></div>';
function load_batch_file(file_name,id){
	if(file_name.indexOf("_chk")!=-1){
		$('#enc_view').prop("checked",false);
	}else{
		$('#chk_view').prop("checked",false);
	}
	var div_id='batch_transactions_list';
	if(file_name=="makePayment"){
		$('#batch_payment_buttons').show();
		$('#batch_trans_buttons').hide();
		var url='<?php echo $GLOBALS['webroot']; ?>/interface/accounting/'+file_name+'.php?'+id;
	}else{
		$('#batch_payment_buttons').hide();
		$('#batch_trans_buttons').show();
		var url='<?php echo $GLOBALS['webroot']; ?>/interface/billing/'+file_name+'.php?'+id;
	}
	$('#'+div_id).html(landingSectionLoader);
	if($('[defertag=1]').length > 0) $('[defertag=1]').remove();
	$('<script>').attr('src', '<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js?cachebuster='+ new Date().getTime()).attr('defertag', 1).appendTo('head');
	$(".dropdown-toggle").dropdown();
	$('#'+div_id).load(url, function(){
		if(file_name=="makePayment"){
			$(this).find('div.mainwhtbox').addClass('margin_0');
			$('#enter_payment_div').css({'height':parseInt($('#enter_payment_div').height() - 45)+'px'});
			$('#pt_name_div').show();
		}
	});
}
function close_fun(){
	var b_id = '<?php echo $_REQUEST['b_id']; ?>';
	var chk_view_val = '<?php echo $chk_view; ?>';
	window.location.href="batch_transactions.php?b_id="+b_id+"&chk_view="+chk_view_val;
}
function download_file_name(){
	var batch_file = $('#be_filePath').val();
	var url = "../billing/downloadFile.php?batch_file="+batch_file;
	window.location=url;
}
function chk_balance(b_id){
	if(document.getElementById('del_records').value!=""){
		var del_records_data = document.getElementById('del_records').value;
		del_records_data +="Please remove transaction for this CPT.";
		alert(del_records_data);
		return false;
	}
	window.open("chk_balance_batch.php?b_id="+b_id,'','width=920px,height=280px,top=200px,left=270px,location=1,scrollbars=no,resizable=1');
}
function delTrans(b_id,trans_id,sort_by,chk_view,act_show_view){
	var ask="";
	ask += "Do you want to delete selected record?";
	fancyConfirm(ask,'','window.location.href="batch_transactions.php?b_id='+b_id+'&deltrans_id='+trans_id+'&sort_by='+sort_by+'&chk_view='+chk_view+'&act_show_view='+act_show_view+'"');	
}
function editTrans(b_id,trans_id,sort_by,chk_view,act_show_view){
	window.open("batch_edit_trans.php?b_id="+b_id+'&edit_transid='+trans_id+'&sort_by='+sort_by+'&chk_view='+chk_view+'&act_show_view='+act_show_view,'','width=920px,height=250px,top=200,left=170,location=1,scrollbars=no,resizable=1');
}
function delTransId(b_id,enc_id,transId,batch_pat_id){
	var ask="";
	ask += "Do you want to delete selected record?";
	var arg= 'b_id='+b_id+'&encounter_id='+enc_id+'&batch_trans_del_id='+transId+'&batch_pat_id='+batch_pat_id;
	fancyConfirm(ask,'',"load_batch_file('makePayment','"+arg+"')");
}
function editCreditTrans(b_id,trans_id,sort_by,chk_view,act_show_view){
	window.open("batch_crd_edit_trans.php?b_id="+b_id+'&edit_transid='+trans_id+'&sort_by='+sort_by+'&chk_view='+chk_view+'&act_show_view='+act_show_view,'','width=920px,height=250px,top=200,left=170,location=1,scrollbars=no,resizable=1');
}
function delCreditTransId(b_id,enc_id,transId,batch_pat_id){
	var ask="";
	ask += "Do you want to delete selected record?";
	var arg= 'b_id='+b_id+'&encounter_id='+enc_id+'&batch_trans_credit_del_id='+transId+'&batch_pat_id='+batch_pat_id;
	fancyConfirm(ask,'',"load_batch_file('makePayment','"+arg+"')");
}
function delTxTransId(b_id,enc_id,transId,batch_pat_id,batch_trans_id){
	var ask="";
	ask += "Do you want to delete selected record?";
	var arg= 'b_id='+b_id+'&encounter_id='+enc_id+'&batch_trans_tx_del_id='+transId+'&batch_pat_id='+batch_pat_id+'&batch_trans_del_id='+batch_trans_id;
	fancyConfirm(ask,'',"load_batch_file('makePayment','"+arg+"')");
}

function refresh_trans(b_id,e_id,sort_by,chk_view,act_show_view){
	parent.location.href='batch_transactions.php?b_id='+b_id+'&sort_by='+sort_by+'&chk_view='+chk_view+'&act_show_view='+act_show_view;
}

function batch_submit(){
	if($('#makePaymentFrm').serializeArray()!=""){
		$("#batch_trans_save").prop("disabled",true);
		var formData = $('#makePaymentFrm').serializeArray();
		$.ajax({
			url: top.JS_WEB_ROOT_PATH+'/interface/accounting/payments.php',
			method: 'POST',
			data: formData,
			success: function(resp)
			{
				load_batch_file("makePayment",resp);
				$("#batch_trans_save").prop("disabled",false);
			}
		});
	}
}

</script>
<?php
// DELETE Batch Transaction RECORD.
if($_REQUEST['deltrans_id']){
	$deltrans_id = $_REQUEST['deltrans_id'];
	$row=imw_query("select del_status from manual_batch_transactions where del_status>1 and trans_id='$deltrans_id'");
	if(imw_num_rows($row)>0){
		$fet_rec=imw_fetch_array($row);
		$del_status_chk=$fet_rec['del_status'];
		$deltransStr1 = "UPDATE  manual_batch_creditapplied  SET delete_credit  = '1' WHERE crAppId  = '$del_status_chk'";
		$deltranQry1 = imw_query($deltransStr1);
	}
	$deltransStr = "UPDATE manual_batch_transactions  SET  del_status = '1' WHERE trans_id = '$deltrans_id'";
	$deltranQry = imw_query($deltransStr);
}
$sort_by=$_REQUEST['sort_by'];
$batch_id=$_REQUEST['b_id'];
$chk_view=$_REQUEST['chk_view'];
$act_show_view=$_REQUEST['act_show_view'];
$sel_file_status=imw_query("select post_status,total_bill_amount,total_payment from manual_batch_file where batch_id='$batch_id'");
$fet_status=imw_fetch_array($sel_file_status);
$file_status=$fet_status['post_status'];
$post_status=$fet_status['post_status'];
$total_bill_amount=$fet_status['total_bill_amount'];
$total_payment =$fet_status['total_payment'];

$sel_tot_paid=imw_query("select sum(trans_amt) as tot_amt from manual_batch_transactions where batch_id='$batch_id'
			  and (payment_claims='Paid' or payment_claims='Deposit' or payment_claims='Interest Payment') and del_status!=1");
$fet_tot_amt=imw_fetch_array($sel_tot_paid);
$tot_amt_batch=$fet_tot_amt['tot_amt'];

$sel_tot_paid=imw_query("select sum(trans_amt) as neg_tot_amt from manual_batch_transactions where batch_id='$batch_id'
			  and (payment_claims='Negative Payment') and del_status!=1");
$fet_tot_amt=imw_fetch_array($sel_tot_paid);
$neg_tot_amt=$fet_tot_amt['neg_tot_amt'];
$tot_amt_batch=$tot_amt_batch-$neg_tot_amt;

$bal_amt_batch= $total_payment - $tot_amt_batch;

$grp_qry=imw_query("select group_color,gro_id,name from groups_new");
while($grp_row=imw_fetch_array($grp_qry)){
	$grp_detail[$grp_row['gro_id']]=$grp_row;	
}
?>
<table class="table" style="margin:0px;">
    <tr class="purple_bar">
        <td>Batch Transaction</td>
        <td>
        	 <div class="checkbox checkbox-inline">
                <input type="checkbox" name="all_enc" id="all_enc" onClick="if($('#txt_for').val()!=''){chk('pat_srh');}">
                <label for="all_enc" title="All Encounters">All Enc</label>
            </div> 
        </td>
        <td>
        	 <select name="batch_grp_srh" id="batch_grp_srh" class="form-control minimal" data-actions-box="true" data-size="10" style="width:130px;" onChange="if($('#txt_for').val()!=''){chk('pat_srh');}">
				 <option value="">Select Group</option>
				 <?php
						foreach($grp_detail as $g_key => $g_id){
							if(in_array($g_key,$_REQUEST['grp_srh'])){
								$sel = 'selected="selected"';
							}else{
								$sel = '';
							}
					?>
							<option value="<?php echo $g_key; ?>" <?php echo $sel; ?>><?php echo ucfirst($grp_detail[$g_key]['name']); ?></option>
							<?php
						}
					?>
            </select>
        </td>
        <td style="width:18%;">
            <?php if($file_status==0){
                include("search_patient_batch.php"); 	
            } ?>
        </td>
        <td class="text-right">
            <div class="checkbox checkbox-inline text-center">
                <input type="checkbox" name="enc_view" id="enc_view" checked="checked" onClick="load_batch_file('batch_transactions_list','b_id=<?php echo $batch_id; ?>');">
                <label for="enc_view">Encounter View</label>
            </div>    
            <div class="checkbox checkbox-inline text-center pdl_25">
                <input type="checkbox" name="chk_view" id="chk_view" onClick="load_batch_file('batch_transactions_chk_list','b_id=<?php echo $batch_id; ?>&chk_view=yes');">
                <label for="chk_view">Check Total View</label>
            </div> 
            <?php if($file_status==0){?>
                <span class="pdl_10"><strong>Total Amount : </strong><span id="batch_tot_pay"><?php echo numberFormat($total_payment,2,'yes');?></span></span>
                <span class="pdl_25"><strong>Applied Amount : </strong><span id="batch_applied_amt"><?php echo numberFormat($tot_amt_batch,2,'yes');?></span></span>
                <span class="pdl_25"><strong>Remaining Amount : </strong><span id="batch_remaining_amt"><?php echo numberFormat($bal_amt_batch,2,'yes');?></span></span>
            <?php } ?>
        </td>
    </tr>
</table>
<?php $tot_hg=$_SESSION['wn_height']-320; ?>
<div class="row">
	<div class="col-sm-2 hide" id="outstanding_amount_main">
        <h4 class="outstanding pd10 mb0 mt5">Outstanding Amount</h4>
        <div id="outstanding_amount" style="height:<?php echo $tot_hg-40;?>px; overflow-y:auto;"></div>
    </div>
    <div class="col-sm-12" id="batch_transactions_list" style="height:<?php echo $tot_hg; ?>px; overflow-y:auto;"></div>
</div> 
<iframe name="export_batch" id="export_batch" height="0" width="0" style="display:none;"></iframe>
<div class="row ad_modal_footer mt5">	
    <div class="col-sm-12 text-center" id="module_buttons">
		<span id="batch_trans_buttons">
			<?php if($post_status==0){ ?>
                <input type="button" class="btn btn-success" align="bottom" name="balance_batch" id="balance_batch" value="Balance Batch" onClick="chk_balance('<?php echo $b_id; ?>')">	
            <?php } ?>
        	<input type="button" class="btn btn-success" align="bottom" name="print_batch" id="print_batch" value="Print Batch" onClick="window.open('batch_trans_list_print.php?b_id=<?php echo $b_id; ?>&grp_name=<?php echo $sel_grp_data['name']; ?>');">	
        	<input type="button" class="btn btn-success" align="bottom" name="export_batch" id="export_batch" value="Export Batch" onClick="download_file_name();">	
    	</span>
        <span id="batch_payment_buttons">
        	<input type="button" class="btn btn-success" align="bottom" name="batch_list" id="batch_list" value="Batch Transaction List" onClick="window.location.href='batch_transactions.php?b_id=<?php echo $batch_id; ?>&chk_view=<?php echo $chk_view; ?>'">
        	<input type="button" class="btn btn-success" align="bottom" name="batch_trans_save" id="batch_trans_save" value="Save" onClick="batch_submit();">	
        </span>
        <input type="button" class="btn btn-danger" align="bottom" name="close_batch" id="close_batch" value="Close" onClick="window.close();">	
    </div>
</div>
<script type="text/javascript">
	load_batch_file('batch_transactions_list','b_id=<?php echo $batch_id; ?>&chk_view=<?php echo $batch_id; ?>');
</script>	