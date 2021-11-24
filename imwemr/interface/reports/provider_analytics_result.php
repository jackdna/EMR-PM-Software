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
?>
<?php
/*
FILE : provider_analytics_result.php
PURPOSE :  RESULT FOR PROVIDER ANALYTIC REPORT
ACCESS TYPE : DIRECT
*/
ini_set("memory_limit","3072M");

//require_once(dirname(__FILE__).'/../common/functions.inc.php');

$dateFormat=get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

//TO SHOW RVU DETAILS
if(trim($DateRangeFor)=='date_of_service' || trim($DateRangeFor)=='doc'){
	$rvu=1;
}

$printFile = true;
if($_POST['form_submitted']){

	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT FOR DATABASE -----------
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);

	//CHECK FOR PRIVILEGED FACILITIES
	if(isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}

	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$filing_provider= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$str_crediting_provider= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$cpt_cat_2= (sizeof($cpt_cat_2)>0) ? implode(',',$cpt_cat_2) : '';
	
	//--- GET MEDICARE AND MEDICAID INSURANCE COMPANY ID -------
	$qry = "select id, in_house_code from insurance_companies 
			where (in_house_code like '%medicare%' or in_house_code like '%medicaid%')";
	$insMedQryRs = imw_query($qry);;
	$medInsIdArr = array();
	while($insMedQryRes=imw_fetch_assoc($insMedQryRs)){
		$id = $insMedQryRes['id'];
		$in_house_code = $insMedQryRe['in_house_code'];
		if(trim(strtolower($in_house_code)) == 'medicare'){
			$medInsIdArr['MEDICARE'][$id] = $id;
		}
		else{
			$medInsIdArr['MEDICAID'][$id] = $id;
		}
	}

	//--- GET GROUP NAME ---
	$group_name = $CLSReports->report_display_selected($grp_id,'group','1');
	
	//--- GET RVU VALUES
	if($rvu=='1'){
		$rs=imw_query("Select rvu_records.cpt_fee_id, rvu_records.work_rvu, rvu_records.pe_rvu,
		rvu_records.mp_rvu, cpt_fee_tbl.cpt4_code FROM rvu_records LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = rvu_records.cpt_fee_id");
		while($res=imw_fetch_array($rs)){
			$allRVUValues[$res['cpt4_code']] = $res;
		}
	}
	
	//--- DATE FORMAT CHECK ---------
	$printFile = false;
	
	if(trim($DateRangeFor) == 'date_of_payment' || trim($DateRangeFor) == 'transaction_date'){
		require_once(dirname(__FILE__).'/provider_analytics_result_dot.php');
	}else{
		require_once(dirname(__FILE__).'/provider_analytics_result_dos.php');
	}	
	//--- CREATE HTML FILE FOR PDF ----	
	$fileData = false;	
	$HTMLCreated=0;
	if($printFile == true){
		$fileData = true;	
		$HTMLCreated=1;
		$styleHTML = '<style>'.file_get_contents('css/reports_html.css').'</style>';
		$csv_data= $styleHTML.$csv_data;
		
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$pdfData;

		$op='l';// Landscape
		$file_location = write_html($strHTML);
	}
	
	if($fileData){
		if($output_option=='view' || $output_option=='output_csv'){
			if($callFrom != 'scheduled'){
				echo $csv_data;
			}
		}
	}else{
		if($callFrom != 'scheduled'){
			echo $csv_data = '<div class="text-center alert alert-info">No Record Found.</div>';			
		}
	}
}
?>