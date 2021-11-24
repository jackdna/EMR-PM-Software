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

$cpt_cat_id = $_REQUEST["cpt_cat_id"];
$practice_code = $_REQUEST["practice_code"];

$page_title = "Export CSV CPT Code";
$userFolder = $_SESSION['authId'] ? "UserId_".$_SESSION['authId']."/" : 'tmp/'; 
$filepath = "../../../../data/".PRACTICE_PATH."/".$userFolder;
$filename = "cpt_csv.csv";
// Make an array for CPT Category
function fetch_query_data($query){
	$return_arr = array();
	$sql_query = imw_query($query);
	if(imw_num_rows($sql_query)>0){
		while($row=imw_fetch_array($sql_query)){
			$return_arr[] = $row; 
		}
	}
	return $return_arr;
}

$cpt_cat_qry = "select cpt_cat_id, cpt_category from cpt_category_tbl order by cpt_category";
$cpt_cat_qry_res = fetch_query_data($cpt_cat_qry);

$category_data_arr = array();
for($c=0;$c<count($cpt_cat_qry_res);$c++){
	$cptCatId = $cpt_cat_qry_res[$c]['cpt_cat_id'];
	$category_data_arr[$cptCatId] = $cpt_cat_qry_res[$c]['cpt_category'];
}

//--- GET ALL REV CODE ---
$rev_qry = "select r_id, r_code from revenue_code";
$revQryRes = fetch_query_data($rev_qry);
$revDataArr = array();
for($i=0;$i<count($revQryRes);$i++){
	$r_id = $revQryRes[$i]['r_id'];
	$revDataArr[$r_id] = $revQryRes[$i]['r_code'];
}

//--- GET ALL POS FACILITY DETAILS ---
$pos_qry = "select pos_id, pos_code from pos_tbl";
$posQryRes = fetch_query_data($pos_qry);
$posFacDataArr = array();
for($i=0;$i<count($posQryRes);$i++){
	$pos_facility_id = $posQryRes[$i]['pos_id'];
	$posFacDataArr[$pos_facility_id] = $posQryRes[$i]['pos_code'];
}

//--- GET ALL TOS FACILITY DETAILS ---
$tos_qry = "select tos_id, tos_prac_cod from tos_tbl";
$tosQryRes = fetch_query_data($tos_qry);
$tosFacDataArr = array();
for($i=0;$i<count($tosQryRes);$i++){
	$tos_id = $tosQryRes[$i]['tos_id'];
	$tosFacDataArr[$tos_id] = $tosQryRes[$i]['tos_prac_cod'];
}

// Make Array for POE
$poeQry = "select poe_messages_id, poe_name from poe_messages where trim(poe_name) != ''";
$poeQryRes = fetch_query_data($poeQry);
$poeResDataArr = array();
for($i=0;$i<count($poeQryRes);$i++){
	$poe_messages_id = $poeQryRes[$i]['poe_messages_id'];
	$poeResDataArr[$poe_messages_id] = $poeQryRes[$i]['poe_name'];
}

//--- GET ALL DEPARTMENT DETAILS ---
$dept_qry = "select DepartmentId, DepartmentCode from department_tbl";
$deptQryRes = fetch_query_data($dept_qry);
$deptDataArr = array();
for($i=0;$i<count($deptQryRes);$i++){
	$DepartmentId = $deptQryRes[$i]['DepartmentId'];
	$deptDataArr[$DepartmentId] = $deptQryRes[$i]['DepartmentCode'];
}

$pfx = ",";
$car_aux = "\r\n";
$csvtext = "";
$csvtext .= "Category".$pfx;
$csvtext .= "Cpt Category2".$pfx;	// [ Old Data ] = Medication
$csvtext .= "Cpt4 Code".$pfx;	// [ Old Data ] = Medication
$csvtext .= "Practice Code".$pfx;
$csvtext .= "Description.".$pfx;
$csvtext .= "NDC#/Comments".$pfx;
$csvtext .= "CVX Code".$pfx;
$csvtext .= "Rev Code".$pfx;
$csvtext .= "TOS".$pfx;
$csvtext .= "Units".$pfx;
$csvtext .= "MOd1".$pfx;
$csvtext .= "Mod2".$pfx;
$csvtext .= "Mod3".$pfx;
$csvtext .= "Department".$pfx;
$csvtext .= "Status".$pfx;
$csvtext .= "POE".$car_aux;
//--- GET ALL MEDICATION RECORDS ----
$cptListQry = "select * from cpt_fee_tbl where delete_status = '0'";
if(empty($cpt_cat_id) === false){
	$cptListQry .= " and cpt_cat_id in ($cpt_cat_id)";
}
if(empty($practice_code) === false){
	$cptListQry .= " and cpt_fee_id in ($practice_code)";
}
$cptListQry .= ' ORDER BY cpt_fee_tbl.cpt_prac_code';

$rez = imw_query($cptListQry);
for($i=1;$row = imw_fetch_array($rez);$i++){
	if($row != false){
		$tmpz = "cpt_cat_id".$i;
		$$tmpz = $row["cpt_cat_id"];
        $tmpz = "cpt_category2".$i;
        if($row["cpt_category2"]==1) {
            $$tmpz = 'Service';
        } else if($row["cpt_category2"]==2) {
            $$tmpz = 'Material';
        } else {
            $$tmpz = '';
        }
		$tmpz = "cpt4_code".$i;
		$$tmpz = $row["cpt4_code"];
		$tmpz = "cpt_prac_code".$i;
		$$tmpz = $row["cpt_prac_code"];		
		$tmpz = "cpt_desc".$i;
		$$tmpz = $row["cpt_desc"];		
		$tmpz = "cpt_comments".$i;
		$$tmpz = $row["cpt_comments"];		
		$tmpz = "cvx_code".$i;
		$$tmpz = $row["cvx_code"];		
		$tmpz = "rev_code".$i;
		$$tmpz = $row["rev_code"];
		$tmpz = "tos_id".$i;
		$$tmpz = $row["tos_id"];
		$tmpz = "units".$i;
		$$tmpz = $row["units"];
		$tmpz = "mod1".$i;
		$$tmpz = $row["mod1"];
		$tmpz = "mod2".$i;
		$$tmpz = $row["mod2"];
		$tmpz = "mod3".$i;
		$$tmpz = $row["mod3"];
		$tmpz = "departmentId".$i;
		$$tmpz = $row["departmentId"];
		$tmpz = "status".$i;
		$$tmpz = $row["status"];
		$tmpz = "elem_poe".$i;
		$$tmpz = $row["elem_poe"];
	}
	if($cl%2 == 0){
		$class = 'bgcolor';
	}
	else{
		$class = '';
	}
	$cpt_cat_id = "cpt_cat_id".$i;
	$cpt_category2 = "cpt_category2".$i;
	$cpt4_code = "cpt4_code".$i;
	$cpt_prac_code = "cpt_prac_code".$i;
	$cpt_desc = "cpt_desc".$i;
	$cpt_comments = "cpt_comments".$i;
	$cvx_code = "cvx_code".$i;
	$rev_code = "rev_code".$i;
	$tos_id = "tos_id".$i;
	$units = "units".$i;
	$mod1 = "mod1".$i;
	$mod2 = "mod2".$i;
	$mod3 = "mod3".$i;
	$departmentId = "departmentId".$i;
	$status = "status".$i;
	$elem_poe = "elem_poe".$i;

	if(trim($$cpt_cat_id) || trim($$cpt_category2) || trim($$cpt4_code) || trim($$cpt_prac_code) || trim($$cpt_desc) || trim($$cpt_comments) || trim($$cvx_code) || trim($$rev_code) || trim($$tos_id) || trim($$mod1) || trim($$mod2) || trim($$mod3) || trim($$units) || trim($$departmentid) || trim($$status) || trim($$elem_poe)) {
		
		$csvtext .= '"'.$category_data_arr[$$cpt_cat_id].'"';
        $csvtext .= $pfx;			
		$csvtext .= '"'.$$cpt_category2.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$cpt4_code.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$cpt_prac_code.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$cpt_desc.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$cpt_comments.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$cvx_code.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$revDataArr[$$rev_code].'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$tosFacDataArr[$$tos_id].'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$units.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$mod1.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$mod2.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$mod3.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$deptDataArr[$$departmentId].'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$status.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$poeResDataArr[$$elem_poe].'"';
		$csvtext .= $car_aux;
	}
}

file_put_contents($filepath.$filename,$csvtext);
if(empty($csvtext) == false) {	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");	
	header("Content-Type: application/octet-stream;");	
	header("Content-disposition:attachment; filename=\"".$filename."\"");	
	header("Content-Length: ".@filesize($filepath.$filename));	
	@readfile($filepath.$filename) or die("File not found.");
	exit;
}
?>