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
$title = "Credit to other Encounter";
require_once("acc_header.php");
require_once("../../library/classes/billing_functions.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/buttons.js"></script>
<script type="text/javascript">
var landingSectionLoader = '<div id="div_loading_image" class="text-center" style="display: block;position: initial;padding-top: 50px;height: 100%;width: 100%;background-color: rgba(0,0,0,0.1);"><div class="loading_container" style="width: auto;"><div class="process_loader"></div><div id="div_loading_text" class="text-info">Loading Data....</div></div></div>';
function load_batch_file(file_name,id){
	var div_id='batch_transactions_list';
	if(file_name=="makePayment"){
		$('#batch_payment_buttons').show();
		var url='<?php echo $GLOBALS['webroot']; ?>/interface/accounting/'+file_name+'.php?'+id+'&crd_trans=yes';
	}else{
		window.opener.top.fmain.editPaymentList(id,'0');
		window.close();
	}
	$('#'+div_id).html(landingSectionLoader);
	$('#'+div_id).load(url, function(){
		if(file_name=="makePayment"){
			$(this).find('div.mainwhtbox').addClass('margin_0');
			$('#enter_payment_div').css({'height':parseInt($('#enter_payment_div').height() - 45)+'px'});
			$('#pt_name_div').show();
		}
	});
}

function credit_submit(){
	var chkboxArr = $("input[name^='chkbx']").length;
	var j = 1;
	var crd_amt = 0;
	var deb_amt = $("#deb_amt").val();
	deb_amt = parseFloat(deb_amt).toFixed(2);
	var tot_crd_amt=0;
	if(chkboxArr>0){
		$("input[name^='chkbx']").each(function(index, element){
			if($('#chkbx'+j).is(':checked')){
				if($("#payment_method_"+j).val()=="Credit"){
					if($("#pri_paid_"+j).val()>0){
						crd_amt = $("#pri_paid_"+j).val();
					}else if($("#sec_paid_"+j).val()>0){
						crd_amt = $("#sec_paid_"+j).val();
					}else if($("#ter_paid_"+j).val()>0){
						crd_amt = $("#ter_paid_"+j).val();
					}else if($("#pat_paid_"+j).val()>0){
						crd_amt = $("#pat_paid_"+j).val();
					}
					tot_crd_amt = parseFloat(tot_crd_amt)+parseFloat(crd_amt);
				}
			}
			j++;
		});
		tot_crd_amt = tot_crd_amt.toFixed(2);
	}
	if(crd_amt==0){
		top.fAlert("Please select procedure to credit");
		return false;
	}else if(tot_crd_amt>deb_amt){
		top.fAlert("Credit amount can not be greater than debit amount");
		return false;
	}
	var formData = $('#makePaymentFrm').serializeArray();
	$("#credit_trans_save").prop("disabled",true);
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/accounting/payments.php',
		method: 'POST',
		data: formData,
		success: function(resp)
		{
			load_batch_file("",resp);
		}
	});
	
}

</script>
<?php

?>
<table class="table" style="margin:0px;">
    <tr class="purple_bar">
        <td>Credit to other Encounter</td>
        <td class="text-right" style="width:50%;">
            <?php  include("../billing/search_patient_batch.php");  ?>
        </td>
    </tr>
</table>
<?php $tot_hg=$_SESSION['wn_height']-320; ?>
<div class="row">
	<div class="col-sm-2 hide" id="outstanding_amount_main">
        <h4 class="outstanding pd10 mb0 mt5">Outstanding Amount</h4>
        <div id="outstanding_amount" style="height:<?php echo $tot_hg-40;?>px; overflow-y:auto;"></div>
    </div>
    <div class="col-sm-10" id="batch_transactions_list" style="height:<?php echo $tot_hg; ?>px; overflow-y:auto;"></div>
</div> 
<div class="row ad_modal_footer mt5">	
    <div class="col-sm-12 text-center" id="module_buttons">
        <span id="batch_payment_buttons" style="display:none;">
        	<input type="button" class="btn btn-success" align="bottom" name="credit_trans_save" id="credit_trans_save" value="Apply" onClick="credit_submit();">	
        </span>
        <input type="button" class="btn btn-danger" align="bottom" name="close_batch" id="close_batch" value="Close" onClick="window.close();">	
    </div>
</div>
<script>chk('<?php echo $_REQUEST['deb_patient_id']; ?>');</script>