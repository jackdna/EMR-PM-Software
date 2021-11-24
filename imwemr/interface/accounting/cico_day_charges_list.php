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
//----------------------- Auth Info -----------------------//
include_once(dirname(__FILE__)."/../../config/globals.php");


$patient_id=$_REQUEST['patient_id'];
$dos=$_REQUEST['dos'];
$sch_id=$_REQUEST['sch_id'];

$item_ids_arr = array();
$item_names_arr = array();
$sel_row_qry = imw_query("select id, item_name from check_in_out_fields");
while($row = imw_fetch_array($sel_row_qry)){
	$fldQryRes[] = $row;
}
for($i=0;$i<count($fldQryRes);$i++){
	$fld_id = $fldQryRes[$i]['id'];
	$item_ids_arr[] = $fld_id;
	$item_names_arr[$fld_id] = $fldQryRes[$i]['item_name'];
}
	
$cico_payment_detail=array();
$cico_item_arr=array();
$cico_item_arr_final=array();
$pay_query = imw_query("select check_in_out_payment.payment_id,check_in_out_payment_details.item_id,
			  	check_in_out_payment_details.item_payment,check_in_out_payment_details.payment_type
					from  
				check_in_out_payment join check_in_out_payment_details
				on check_in_out_payment.payment_id = check_in_out_payment_details.payment_id
				where check_in_out_payment.patient_id = '$patient_id'
				and check_in_out_payment.del_status = '0' 
				and check_in_out_payment_details.status='0'
				and check_in_out_payment.sch_id = '$sch_id'");
while($row = imw_fetch_array($pay_query)){
	$payQryRes[] = $row;
}				
for($i=0;$i<count($payQryRes);$i++){	
	$cico_item_arr[]=$payQryRes[$i]['item_id'];					
	$cico_payment_detail[$payQryRes[$i]['item_id']][$payQryRes[$i]['payment_type']]= $payQryRes[$i]['item_payment'];
}
$cico_item_arr_final=array_values(array_unique($cico_item_arr));
//----------------------- Auth Info -----------------------//
$auth_info="";
$auth_info='
	<table cellpadding="2" cellspacing="0" class="table table-bordered table-striped result_data" >
            <tr class="grythead">
                <th align="left">
                    Field Name
                </th>
                <th align="center">
                   CI Payment
                </th>
				 <th align="center">
                   CO Payment
                </th>
				 <th align="center">
                   Total Payment
                </th>
            </tr>';
			$total_ci_payment=array();
			$total_co_payment=array();
            if(count($cico_item_arr_final)>0){
                for($i=0;$i<count($cico_item_arr_final);$i++){
					$item_id=$cico_item_arr_final[$i];
					$total_cico_amt="";
					$total_cico_amt=$cico_payment_detail[$item_id]['checkout']+$cico_payment_detail[$item_id]['checkin'];
					$total_ci_payment[]=number_format($cico_payment_detail[$item_id]['checkin'],2);
					$total_co_payment[]=number_format($cico_payment_detail[$item_id]['checkout'],2);
					$auth_info.='<tr><td>';
					$auth_info.=$item_names_arr[$item_id];
					$auth_info.='</td><td align="right">';
					$auth_info.='$'.number_format($cico_payment_detail[$item_id]['checkin'],2);
					$auth_info.='</td><td align="right">';
					$auth_info.='$'.number_format($cico_payment_detail[$item_id]['checkout'],2);
					$auth_info.='</td><td align="right">';
					$auth_info.='$'.number_format($total_cico_amt,2);
					$auth_info.='</td> </tr>';
            	} 
			} 
		$auth_info.='<tr>
                <th class="text-right">
                    Total : 
                </th>
                <th class="text-right">';
        $auth_info.='$'.number_format(array_sum($total_ci_payment),2);
        $auth_info.='</th>
				 <th class="text-right">';
        $auth_info.='$'.number_format(array_sum($total_co_payment),2);
        $auth_info.='</th>
				<th class="text-right">';
        $auth_info.='$'.number_format(array_sum(array_merge($total_co_payment,$total_ci_payment)),2);
        $auth_info.='</th>
            </tr>';
$auth_info.='</table>';
echo $auth_info;
?>