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
$patient_id=$_SESSION['patient'];
$str=rawurldecode($_REQUEST['str']);
$str = imw_real_escape_string(htmlentities($str));	/** Prevent arbitrary values - Security Fix */
$pInsCaseId=$_REQUEST['pInsCaseId'];
$pInsId= (int)xss_rem($_REQUEST['pInsId'], 3);	/** Sanitization to prevent arbitrary values - Security Fix */
$qry_pol=imw_query("select billing_amount from copay_policies");
$row_pol=imw_fetch_array($qry_pol);
$billing_amount=$row_pol['billing_amount'];
if($billing_amount=='Default'){
	$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee,b.dx_codes FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										b.cpt_prac_code='$str'
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
	$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
	$cpt_fee = $getCPTPriceRow['cpt_fee'];
	$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
	$dx_codes=$getCPTPriceRow['dx_codes'];
	if(imw_num_rows($getCPTPriceQry)==0){
		$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee,b.dx_codes FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										(b.cpt4_code='$str' or b.cpt_desc='$str')
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$cpt_fee = $getCPTPriceRow['cpt_fee'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$dx_codes=$getCPTPriceRow['dx_codes'];
	}
	echo $proc = str_replace(',','~~~',$cpt_prac_code).", ".$cpt_fee.", ".str_replace(',','~~~',$dx_codes);
}else{
	if($pInsId){
		$qryId = imw_query("select FeeTable from insurance_companies where id = '$pInsId'");
		list($FeeTable) = imw_fetch_array($qryId);
		$qry = "select cpt_fee_tbl.cpt_prac_code,cpt_fee_table.cpt_fee,cpt_fee_tbl.dx_codes from cpt_fee_tbl
				join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
				where cpt_fee_tbl.cpt_prac_code='$str'
				and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id AND cpt_fee_tbl.delete_status = '0' order by status asc";
		//$get=imw_query("SELECT * FROM cpt_fee_tbl WHERE cpt_prac_code='$str' OR cpt4_code='$str' OR cpt_desc='$str'");
		$get=imw_query("$qry");
		$get_row=@imw_fetch_array($get);
		$fee=$get_row['cpt_fee'];
		$cpt_prac_code=$get_row['cpt_prac_code'];
		$dx_codes=$get_row['dx_codes'];
		if(imw_num_rows($get)==0){
			$qry = "select cpt_fee_tbl.cpt_prac_code,cpt_fee_table.cpt_fee,cpt_fee_tbl.dx_codes from cpt_fee_tbl
				join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
				where (cpt_fee_tbl.cpt4_code='$str' or cpt_fee_tbl.cpt_desc='$str')
				and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id AND cpt_fee_tbl.delete_status = '0' order by status asc";
			//$get=imw_query("SELECT * FROM cpt_fee_tbl WHERE cpt_prac_code='$str' OR cpt4_code='$str' OR cpt_desc='$str'");
			$get=imw_query("$qry");
			$get_row=@imw_fetch_array($get);
			$fee=$get_row['cpt_fee'];
			$cpt_prac_code=$get_row['cpt_prac_code'];
			$dx_codes=$get_row['dx_codes'];
		}
		echo $proc = str_replace(',','~~~',$cpt_prac_code).", ".$fee.", ".str_replace(',','~~~',$dx_codes);
	}else{	
		$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee,b.dx_codes FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										b.cpt_prac_code='$str'
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$cpt_fee = $getCPTPriceRow['cpt_fee'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$dx_codes=$getCPTPriceRow['dx_codes'];
		if(imw_num_rows($getCPTPriceQry)==0){
			$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee,b.dx_codes FROM cpt_fee_table a,
											cpt_fee_tbl b
											WHERE 
											(b.cpt4_code='$str' or b.cpt_desc='$str')
											AND a.cpt_fee_id = b.cpt_fee_id
											AND a.fee_table_column_id = '1'
											AND delete_status = '0' order by status asc");
			$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
			$cpt_fee = $getCPTPriceRow['cpt_fee'];
			$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
			$dx_codes=$getCPTPriceRow['dx_codes'];
		}
		echo $proc = str_replace(',','~~~',$cpt_prac_code).", ".$cpt_fee.", ".str_replace(',','~~~',$dx_codes);
	}
}
?>