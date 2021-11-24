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
set_time_limit(600);
require_once("../../../../config/globals.php");

$field = $_REQUEST["field"];
$id = $_REQUEST["id"];
$page_title = "Export CSV Dx Code";
$filename = "dx_csv.csv";


if($field == "cat_id"){
	$sql = "SELECT diagnosis_category.category, diagnosis_code_tbl.* ".
			   "FROM diagnosis_code_tbl ".			  
			   "INNER JOIN diagnosis_category ".
			   "ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id ".
			   "WHERE diagnosis_code_tbl.diag_cat_id = '$id' ".				   
			   "ORDER BY diagnosis_code_tbl.dx_code ";
}
else if($field == "prcCode"){
	$sql = "SELECT diagnosis_category.category, diagnosis_code_tbl.* ".
			   "FROM diagnosis_code_tbl ".			  
			   "INNER JOIN diagnosis_category ".
			   "ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id ".
			   "WHERE diagnosis_code_tbl.d_prac_code LIKE '$id%' ".				   
			   "ORDER BY diagnosis_code_tbl.d_prac_code ";

}
else{
	$sql = "SELECT diagnosis_category.category, diagnosis_code_tbl.* FROM diagnosis_code_tbl ".
			   "INNER JOIN diagnosis_category ".
			   "ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id ".
			   "ORDER BY diagnosis_category.category";
}
$rez = imw_query($sql);

$pfx = ",";
$car_aux = "\r\n";
$csvtext = "";
$csvtext .= "Category".$pfx;
$csvtext .= "Dx Code".$pfx;	// [ Old Data ] = Medication
$csvtext .= "Practice Code".$pfx;
$csvtext .= "PQRI.".$pfx;
$csvtext .= "Recall".$pfx;
$csvtext .= "SNOMED CT".$pfx;
$csvtext .= "Description".$car_aux;

for($i=1;$row = imw_fetch_array($rez);$i++){
	if($row != false){
		$tmpz = "category".$i;
		$$tmpz = $row["category"];		
		$tmpz = "dx_code".$i;
		$$tmpz = $row["dx_code"];
		$tmpz = "d_prac_code".$i;
		$$tmpz = $row["d_prac_code"];		
		$tmpz = "diag_description".$i;
		$$tmpz = $row["diag_description"];		
		$tmpz = "pqriCode".$i;
		$$tmpz = $row["pqriCode"];		
		$tmpz = "recall".$i;
		$$tmpz = $row["recall"];		
		$tmpz = "snowmed_ct".$i;
		$$tmpz = $row["snowmed_ct"];
	}
	if($cl%2 == 0){
		$class = 'bgcolor';
	}
	else{
		$class = '';
	}
	$category = "category".$i;
	$dx_code = "dx_code".$i;
	$d_prac_code = "d_prac_code".$i;
	$diag_description = "diag_description".$i;
	$pqriCode = "pqriCode".$i;
	$recall = "recall".$i;
	$snowmedCt = "snowmed_ct".$i;

//	if(trim($$category) || trim($$dx_code) || trim($$d_prac_code) || trim($$diag_description) || trim($$pqriCode) || trim($$recall)) 
	{
		
		$csvtext .= '" '.$$category.'"';
		$csvtext .= $pfx;			
		$csvtext .= '" '.$$dx_code.'"';
		$csvtext .= $pfx;			
		$csvtext .= '" '.$$d_prac_code.'"';
		$csvtext .= $pfx;			
		$csvtext .= '" '.$$pqriCode.'"';
		$csvtext .= $pfx;
		$csvtext .= '" '.$$recall.'"';
		$csvtext .= $pfx;
		$csvtext .= '" '.$$snowmedCt.'"';
		$csvtext .= $pfx;
		$csvtext .= '" '.$$diag_description.'"';
		$csvtext .= $car_aux;
	}
}
file_put_contents($filename,$csvtext);
if(empty($csvtext) == false) {	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");	
	header("Content-Type: application/octet-stream;");	
	header("Content-disposition:attachment; filename=\"".$filename."\"");	
	header("Content-Length: ".@filesize($filename));	
	@readfile($filename) or die("File not found.");
	exit;
}
?>