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
		<th class="text-center">Total Payment</th>
		<th class="text-center">Payment Method</th>
		<th class="text-center">CC / Ch.#</th>
		<th class="text-center">CC Exp. Date</th>
		<th class="text-center">Collected On</th>
		<th class="text-center">Collected By</th>
	</tr>	
		<?php
			$qry_usr=imw_query("select * from users");		
			while($row_usr=imw_fetch_array($qry_usr)){
				$ins_operator_name_arr[$row_usr['id']] = substr($row_usr['fname'],0,1).substr($row_usr['lname'],0,1);
				$ins_operator_full_name_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
			}

			$depo_qry = "select * from patient_pre_payment where del_status = '0' and  patient_id='$patient_id' order by entered_date desc";
			$depo_mysql = imw_query($depo_qry);
			$i=0;
			$pre_payment_count=0;
			while($dpRows = imw_fetch_array($depo_mysql)) 
			{
			$i++;
			$pre_payment_count=$i;
		?>
		<tr style="background:#ffffff;">
			<td class="text-center">
				<?php echo $i.'.';?>
			</td>
			<td class="text-center">
				<?php echo '$'.number_format($dpRows['paid_amount'],2); ?>
			</td>
			<td class="text-center">
				<?php echo $dpRows['payment_mode']; ?>
			</td>
			<td class="text-center">
				&nbsp;<?php 
				 if($dpRows['payment_mode']=='Check' or $dpRows['payment_mode']=='EFT' or $dpRows['payment_mode']=='Money Order' or $dpRows['payment_mode']=='VEEP'){
					echo $dpRows['check_no'];
				}else if($dpRows['payment_mode']=='Credit Card'){
					$cc_no=substr($dpRows['cc_no'],-4);
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
					echo $credit_card_company.' - '.$cc_no;
				}
				 ?>
			</td>
			<td class="text-center">
				<?php echo $dpRows['cc_exp_date']; ?>
			</td>
			<td class="text-center">
				<?php 
					$show_entered_date="";
					$opr_name_exp="";
					if($dpRows['entered_date']!='0000-00-00' && $dpRows['entered_date']!=""){
						 $opr_name_exp=explode(', ',$get_prov[$dpRows['entered_by']]);
						$show_entered_date = date("m-d-y",strtotime($dpRows['entered_date'])).' '. date("h:i A",strtotime($dpRows['entered_time'])); 
					}
					echo $show_entered_date;
				?>
			</td>
			<td class="text-center">
				 <?php 
					echo $ins_operator_full_name_arr[$dpRows['entered_by']];
				?>
			</td>
		</tr>
		<?php
			}
		?>
	</tr>	
</table>
<?php
$pp_data=ob_get_contents();
ob_end_clean();
?>
