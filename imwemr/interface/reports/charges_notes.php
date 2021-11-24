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
$file_name="charges_notes.csv";
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
$data_output.="Charge Post Date".$pfx;
$data_output.="CPT / Charge Code".$pfx;
$data_output.="CPT / Charge Code Description".$pfx;
$data_output.="CPT / Charge Code Quantity".$pfx;
$data_output.="Total Gross Charge Amount".$pfx;
$data_output.="Total Net Revenue".$pfx;
$data_output.="Acquisition Note";
$data_output.= "\n";


//--- GET ALL CHARGES ----
$qry = "Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, 
SUM((main.charges * main.units)) as totalAmt, SUM(main.units) as 'total_units',
DATE_FORMAT(main.date_of_service,'".$dateFormat."') as date_of_service,
DATE_FORMAT(main.entered_date,'".$dateFormat."') as entered_date,
main.primary_provider_id_for_reports  as 'primaryProviderId', 
main.proc_code_id,  
users.lname,users.fname, users.mname,
pos_facilityies_tbl.facilityPracCode,
pos_tbl.pos_prac_code,
cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc
FROM report_enc_detail main 
LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
WHERE (main.date_of_service between '$startDate' and '$endDate')				 
AND main.del_status='0' 
GROUP BY main.facility_id, main.primary_provider_id_for_reports, main.date_of_service, main.proc_code_id
ORDER BY pos_facilityies_tbl.facilityPracCode, users.lname,users.fname, main.date_of_service";
$res=imw_query($qry) or die(imw_error());;

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
	$data_output.='"'.$rs['entered_date'].'"'.$pfx;
	$data_output.='"'.$rs['cpt4_code'].'"'.$pfx;
	$data_output.='"'.$rs['cpt_desc'].'"'.$pfx;
	$data_output.='"'.$rs['total_units'].'"'.$pfx;
	$data_output.='"'.$rs['totalAmt'].'"'.$pfx;
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
