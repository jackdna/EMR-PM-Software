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

/*
FILE : charges_notes.php
PURPOSE : PRODUCTIVITY CHARGES FOR PHYSICIAN
ACCESS TYPE : DIRECT
*/
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');

$dateFormat = get_sql_date_format();
$Start_date=$End_date=date('Y-m-d');

if($_REQUEST['start_date']!='' && $_REQUEST['end_date']!=''){
	$Start_date=$_REQUEST['start_date'];
	$End_date=$_REQUEST['end_date'];
}

//--- CHANGE DATE FORMAT ----
$startDate = getDateFormatDB($Start_date);
$endDate   = getDateFormatDB($End_date);


// -- GET ALL POS-FACILITIES
$arrAllFacilities=array();
$arrAllFacilities[0] = 'No Facility';
$qry = "select pos_facilityies_tbl.facilityPracCode as name,
	pos_facilityies_tbl.pos_facility_id as id,
	pos_tbl.pos_prac_code
	from pos_facilityies_tbl
	left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
	order by pos_facilityies_tbl.headquarter desc,
	pos_facilityies_tbl.facilityPracCode";
$qryRs = imw_query($qry);
while($qryRes  =imw_fetch_assoc($qryRs)){
	$id = $qryRes['id'];
	$name = $qryRes['name'];
	$pos_prac_code = $qryRes['pos_prac_code'];
	$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
}						
// ------------------------------


//MAKING OUTPUT DATA
//$file_name="charges_notes_".time().".csv";
$file_name="cases_notes.csv";
$csv_file_name= write_html("", $file_name);

//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$pfx=",";

$data_output='';
$data_output.="NextGen Practice".$pfx;
$data_output.="Location".$pfx;
$data_output.="Rendering Provider".$pfx;
$data_output.="Date of Service".$pfx;
$data_output.="Case Quantity".$pfx;
$data_output.="Case Type".$pfx;
$data_output.="Acquisition Note";
$data_output.= "\n";


//--- GET ALL CHARGES ----
$qry = "Select 
DATE_FORMAT(main.date_of_service,'".$dateFormat."') as date_of_service,
main.primary_provider_id_for_reports  as 'primaryProviderId', main.facility_id,
users.lname,users.fname, users.mname,
pos_facilityies_tbl.facilityPracCode,
pos_tbl.pos_prac_code, COUNT(encounter_id) as 'case_qty'  
FROM patient_charge_list main 
JOIN patient_data on patient_data.id = main.patient_id 
LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
WHERE (main.date_of_service between '$startDate' and '$endDate')				 
AND main.del_status='0' 
GROUP BY main.facility_id, main.primary_provider_id_for_reports, main.date_of_service 
ORDER BY pos_facilityies_tbl.facilityPracCode, users.lname,users.fname, main.date_of_service";
$res=imw_query($qry) or die(imw_error());

$dataexist=0;
while($rs = imw_fetch_assoc($res)){
	$dataexist=1;
	$primaryProviderId = $rs['primaryProviderId'];
	$facility_name= $rs['facilityPracCode'].' - '.$rs['pos_prac_code'];
	$provider_name = core_name_format($rs['lname'], $rs['fname'], $rs['mname']);

	$data_output.="".$pfx;
	$data_output.='"'.$facility_name.'"'.$pfx;
	$data_output.='"'.$provider_name.'"'.$pfx;
	$data_output.='"'.$rs['date_of_service'].'"'.$pfx;
	$data_output.='"'.$rs['case_qty'].'"'.$pfx;
	$data_output.="".$pfx;
	$data_output.="";
	$data_output.= "\n";
}unset($rs);


if($dataexist==1){
	$fp=fopen($csv_file_name,"w");
	@fwrite($fp,$data_output);
	@fclose($fp);
	
	echo "Output created from $Start_date to $End_date.<br><br>";
	echo $csv_file_name.'<br><br><br>';?>
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">	
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
		<input type="submit" name="sbtbtn" id="sbtbtn" value="Download CSV File">
	</form> 
<?php
}else{
	echo "No data found between $Start_date to $End_date";
}
?>
