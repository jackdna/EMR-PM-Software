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
$patient_id = $_SESSION['patient'];
ob_start();
?>
<table class="table table-striped table-bordered table-hover">
	<tr class="grythead">					
		<th class="text-center">S.No.</th>
		<?php
		$get_prov=array();
		$getPhysicianNameStr="SELECT fname,lname,mname,id FROM users";
		$getPhysicianNameQry=imw_query($getPhysicianNameStr);
		while($getPhysicianNameRow=imw_fetch_array($getPhysicianNameQry)){
			$phy_arr['FIRST_NAME']=$getPhysicianNameRow['fname'];
			$phy_arr['LAST_NAME']=$getPhysicianNameRow['lname'];
			$phy_arr['MIDDLE_NAME']=$getPhysicianNameRow['mname'];
			$get_prov[$getPhysicianNameRow['id']]=changeNameFormat($phy_arr);
		}
					
		$item_ids_arr = array();
		$item_names_arr = array();
		$sel_row_qry=imw_query("select id, item_name from check_in_out_fields");
		while($fldQryRes=imw_fetch_array($sel_row_qry)){	
			$fld_id = $fldQryRes['id'];
			$item_ids_arr[] = $fld_id;
			$item_names_arr[$fld_id] = $fldQryRes['item_name'];
		}					
		?>
		<th class="text-center">Field Name</th>
		<th class="text-center">Total Payment</th>
		<th class="text-center">Payment Method</th>
		<th class="text-center">CC / Ch.#</th>
		<th class="text-center">CC Exp. Date</th>
		<th class="text-center">Collected On</th>
		<th class="text-center">Collected By</th>
	</tr>
	<?php
	$payment_id_arr = array();
	$total_payment_arr = array();
	$payment_method_arr = array();
	$payment_check_cc_arr = array();
	$payment_cc_date_arr = array();
	$created_on_arr = array();
	$created_by_arr = array();
	$payment_type=array();
	//--- GET ALL PAYMENTS HISTORY OF A PATIENT ---
	$pay_query = imw_query("select * from  check_in_out_payment where patient_id = '$patient_id'
				and del_status = '0' order by payment_id");
	while($payQryRes=imw_fetch_array($pay_query)){	
		$payment_id = $payQryRes['payment_id'];
		$payment_id_arr[] = $payQryRes['payment_id'];
		$total_payment_arr[$payment_id] = $payQryRes['total_payment'];
		$payment_method_arr[$payment_id] = $payQryRes['payment_method'];
		$payment_type[$payment_id]=$payQryRes['payment_type'];
		
		if($payQryRes['payment_method'] == 'Check' or $payQryRes['payment_method'] == 'EFT' or $payQryRes['payment_method'] == 'Money Order' or $payQryRes['payment_method'] == 'VEEP'){
			$payment_check_cc_arr[$payment_id] = $payQryRes['check_no'];
		}
		else if($payQryRes['payment_method'] == 'Credit Card'){
			$cc_no = substr($payQryRes['cc_no'],-4);
			$payment_check_cc_arr[$payment_id] = $payQryRes['cc_type'] .'-'. $cc_no;
			$payment_cc_date_arr[$payment_id] = $payQryRes['cc_expire_date'];
		}

		$created_on_arr[$payment_id] = get_date_format($payQryRes['created_on']).' '.$payQryRes['created_time'];
		$created_by_arr[$payment_id] = $payQryRes['created_by'];
	}
	
	$item_ids_imp = implode("','",$item_ids_arr);	
	$payment_id_imp = implode("','",$payment_id_arr);	
	$payment_detail = array();
	
	//--- GET CHECK IN/OUT PAYMENT DETAILS ---
	$qry_payment = imw_query("select * from check_in_out_payment_details where payment_id in('$payment_id_imp') and status='0'");
	while($pay_query_res=imw_fetch_array($qry_payment)){	
		$pay_id = $pay_query_res['payment_id'];
		$payment_detail[$pay_id][] = $pay_query_res;
	}
	$seq_rec=0;
	for($p=0,$seq=1;$p<count($payment_id_arr);$p++,$seq++){
		$pay_id = $payment_id_arr[$p];
		if($total_payment_arr[$pay_id]>0){
			$pay_detail = $payment_detail[$pay_id];
			for($d=0;$d<count($pay_detail);$d++){
				$seq_rec++;
				$item_id = $pay_detail[$d]['item_id'];
				if($pay_detail[$d]['item_payment']>0){
				?>
				<tr style="background:#ffffff;">
                	<td class="text-center">
                        <?php echo $seq_rec."."; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $item_names_arr[$item_id]; ?>
                    </td>
                    <td  class="text-right">
                        <?php echo '$'.number_format($pay_detail[$d]['item_payment'],2); ?>
                    </td>
                    <td style="text-align:center;" class="text-center">
                        <?php echo $payment_method_arr[$pay_id]; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $payment_check_cc_arr[$pay_id]; ?>
                        &nbsp;
                    </td>
                    <td class="text-center">
                        <?php echo $payment_cc_date_arr[$pay_id]; ?>
                        &nbsp;
                    </td>
                    <td class="text-center">
                        <?php echo $created_on_arr[$pay_id]; ?>
                    </td>
                    <td class="text-center">
                        <?php 
                            $opr_name_exp=explode(', ',$get_prov[$created_by_arr[$pay_id]]);
                            echo substr($opr_name_exp[1],0,1).substr($opr_name_exp[0],0,1);
                        ?>
                    </td>
				</tr>
				<?php	
				}		
			}
		}
	}
	?>
</table>
<?php
$ci_co_data=ob_get_contents();
ob_end_clean();
?>
