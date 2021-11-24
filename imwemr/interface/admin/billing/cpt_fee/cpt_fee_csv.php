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
set_time_limit(300);
require_once("../../../../config/globals.php");
require_once("../../../../library/classes/common_function.php");
require_once("../../../../library/classes/cpt_fee_class.php");

$selected_cat = $_REQUEST['cat_fee_tbl'];
$cpt_fee_obj = New CPT_Fee($operator_id, $selected_cat);
// Returns csv array 
$cpt_fee_obj->get_csv_cpt_master_arr($selected_cat);
$cptDetails = $cpt_fee_obj->csv_cpt_global_arr;

$page_title = "Export CSV CPT FEE TABLE";
$filename = data_path()."tmp/cpt_fee_csv.csv";

$currency = html_entity_decode(str_ireplace("&nbsp;"," ",show_currency()));

$pfx = ",";
$car_aux = "\r\n";
$csvtext = "";
$csvtext .= "\"Category\"".$pfx;
$csvtext .= "\"Practice Code\"".$pfx;
$csvtext .= "\"Description\"".$pfx;

$Detail =$cpt_fee_obj->cpt_fee_name_arr;
//----To get Table Column Name ------

//----- Start Loop For Table Heading Dynamicly ----
$headingData='';
for($i=0;$i<count($Detail);$i++){
	$displayColumnName = '';	
	$displayColumnName = $Detail[$i]['column_name'];
	
	$headingData .= "\"".$displayColumnName."\"".$pfx;
	if(stristr('Default', $displayColumnName) && $i=='0'){
		$headingData.= "\"Work RVU\"".$pfx."\"PE RVUMP\"".$pfx."\"RVU\"".$pfx;
	}
}
$csvtext.=$headingData;
$csvtext .= $car_aux;
//----- End Loop For Table Heading Dynamicly ----

//----- Start Query To Get Data From Cpt Main Table ---------
$arrCPTDet=array();
$cptFeeTableQry = "select cpt_fee_id,fee_table_column_id,cpt_fee,cpt_fee_table_id from cpt_fee_table";
$cptFeeTableRes = imw_query($cptFeeTableQry);
while($cptFeeTableRow = imw_fetch_array($cptFeeTableRes)) {
	$cptFee_Id 			= $cptFeeTableRow["cpt_fee_id"];
	$feeTableColumnId 	= $cptFeeTableRow["fee_table_column_id"];
	$cptFee 			= $cptFeeTableRow["cpt_fee"];
	$cptFeeTableId 		= $cptFeeTableRow["cpt_fee_table_id"];
	
	$arrCPTDet[$cptFee_Id][$feeTableColumnId]['cpt_fee'] = $cptFee;
	$arrCPTDet[$cptFee_Id][$feeTableColumnId]['cpt_fee_table_id'] = $cptFeeTableId;
}

$clr = 0;
$data1 = "";
$data = "";

$selCatArr = array();
if(isset($_REQUEST['cat_fee_tbl']) && empty($_REQUEST['cat_fee_tbl']) == false) $selCatArr = explode(',', $_REQUEST['cat_fee_tbl']);

//pre($cpt_fee_obj->csv_cpt_global_arr);die;

foreach($cpt_fee_obj->csv_cpt_global_arr as $obj){
	
	if($clr % 2 == 0){ $bgClr = 'alt3'; } else{ $bgClr = ''; }
	$cols=4;
	$cpt_id = $obj['cpt_fee_id'];
	$inputData='';

	$workRVU =	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['work_rvu'];
	$peRVU =	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['pe_rvu'];
	$mpRVU = 	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['mp_rvu'];
	
	//---- Start Query To get Text Box ---------
	for($d=0;$d<count($Detail);$d++){
		$columnId = $Detail[$d]['fee_table_column_id'];
		//----- Start Query To Get Fee Value Of Every Field ------
		$cpt_fee = $cpt_fee_obj->cpt_fee_table[$cpt_id][$columnId]['cpt_fee'];
		$cpt_fee_table_id = $cpt_fee_obj->cpt_fee_table[$cpt_id][$columnId]['cpt_fee_table_id'];
		$cpt_fee = $cpt_fee >= 0 ? $currency.number_format($cpt_fee,2, '.', '') : '';
		$workRVU= $workRVU >= 0 ? $currency.number_format($workRVU,2, '.', '') : '';
		$peRVU= $peRVU >= 0 ? $currency.number_format($peRVU,2, '.', '') : '';
		$mpRVU= $mpRVU >= 0 ? $currency.number_format($mpRVU,2, '.', '') : '';
		//----- End Query To Get Fee Value Of Every Field ------
		
		$inputData .="\"".$cpt_fee."\"".$pfx;
		if($d=='0'){
			$inputData.="\"".$workRVU."\"".$pfx."\"".$peRVU."\"".$pfx."\"".$mpRVU."\"".$pfx;
		}
		$cols++;
	}	

	$obj['cpt_category']=str_replace(",","; ", $obj['cpt_category']);
	$obj['cpt_prac_code']=str_replace(",","; ", $obj['cpt_prac_code']);
	$obj['cpt_desc']=str_replace(",","; ", $obj['cpt_desc']);
	$data .="\"".$obj['cpt_category']."\"".$pfx."\"".$obj['cpt_prac_code']."\"".$pfx."\"".$obj['cpt_desc']."\"".$pfx.$inputData.$car_aux;	
	$clr++;
}

$data=$headData.$data;
$data1 = $headData.$data1;
$totalcptRowsPrinted = $clr;

$csvtext .= $data;
file_put_contents($filename,$csvtext);
if(empty($csvtext) == false) {	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");	
	header("Content-Type: application/octet-stream;");	
	header("Content-disposition:attachment; filename=\"".pathinfo($filename, PATHINFO_BASENAME)."\"");	
	header("Content-Length: ".@filesize($filename));	
	@readfile($filename) or die("File not found.");
	exit;
}

?>