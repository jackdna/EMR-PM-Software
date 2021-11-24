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
FILE : unfinalized_encounters_result.php
PURPOSE : Display results of Unfinalized Encounters report
ACCESS TYPE : Direct
*/
require_once('../../library/classes/work_view/Facility.php');

$without_pat = "yes";
$oFacility = new Facility("HQ");
$arrChartTimer = $oFacility->getChartTimers();


$globalphpdtformat = phpDateFormat();

$arrFacilitySel=array();
$arrDoctorSel=array();

$printFile = true;
$HTMLCreated=0;
$conditionChk = true;

if($_POST['form_submitted']){
	$printFile = false;

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

	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);

	function isDtPassed($dt, $strtime){
		//$datetime1 = strtotime($dt1);
		$curDate = strtotime(date("Y-m-d"));
		$mxTS = strtotime($dt.' + '.$strtime);
		return ($curDate<$mxTS)?0:1;
	}
	function dtCalc($dt,$formulea,$format){
		$tm = strtotime($dt.$formulea);
		return date($format,$tm);
	}

	$providers= implode(",", $phyId);
	
	//GET ALL PATIENT
	$ptRs=imw_query("Select id, fname, mname, lname FROM patient_data");
	while($ptRes=imw_fetch_array($ptRs)){
		$ptResName = core_name_format($ptRes['lname'], $ptRes['fname'], $ptRes['mname']);
		$ptResName.= ' - '.$ptRes['id'];
		$ptResNameArr[$ptRes['id']] = $ptResName;
	}
	
	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname, user_type FROM users");	
	while($res=imw_fetch_array($rs)){
		$id = $res['id'];
		$pro_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
		$providerNameArr[$id] = $pro_name;
		
		if(in_array($res['user_type'],array(1,7,12))) {
			$physicianNameArr[$id] = $pro_name;	
			$physicianIdArr[] = $id;	
		}
	}
	
	$physicianIdImp='0';
	if(empty($providers)===false){ 
		$arrProviderSel = explode(',', $providers); 
		$physicianIdImp.= ','.$providers;
	}else {
		//$physicianIdImp = implode(',',$physicianIdArr);	
	}
	//Condition for display only charts
	if($charts!=1){
		$testRecArr = array();
		$checkFormId = 'yes';
		$testRecAllArr = $CLSReports->getTestRecord($checkFormId,'', $st_date, $en_date);
		$testRecArr = $testRecAllArr[0];
		$testWithoutChartAllArr = $CLSReports->getTestRecord('',$physicianIdImp, $st_date, $en_date);
		$testWithoutChartPtIdArr = array_unique($testWithoutChartAllArr[1]);
		$testWithoutChartPhyIdArr = $testWithoutChartAllArr[2];
		$testWithoutChartExamDateArr = $testWithoutChartAllArr[3];
	}
	//--- GET RECORDS
	$content_part=$content_new_part='';
	$qry = "SELECT chart_master_table.id,
   				  patient_data.fname, 
				  patient_data.mname, 
				  patient_data.lname, 
				  DATE_FORMAT(chart_master_table.date_of_service, '".get_sql_date_format()."') AS 'DOS',
			      DATE_FORMAT(chart_master_table.create_dt, '".get_sql_date_format()."') AS 'CREATED_DATE',
				  chart_master_table.ptVisit,
		   		  users.fname AS provider_fname, 
				  users.mname AS provider_mname, 
				  users.lname AS provider_lname,  
		   	  chart_master_table.* 
		   FROM chart_master_table 
		   LEFT JOIN patient_data ON chart_master_table.patient_id = patient_data.id 
		   LEFT JOIN users ON users.id = chart_master_table.providerId 
		   WHERE chart_master_table.finalize = '0' 
			   AND chart_master_table.delete_status = '0' and patient_data.id != 0 and chart_master_table.not2show = '0'";
	if($exclude_user_types!='1'){
		$qry.=" AND users.user_type IN(1,7,12)";
	}
	if(empty($providers) === false){
		$qry.=" AND chart_master_table.providerId IN(".$providers.")";
	}
	if(empty($st_date)==false && empty($en_date)==false){
	   $qry.=" AND (chart_master_table.date_of_service BETWEEN '".$st_date."' AND '".$en_date."')";
	}

   
	//$odrBy = $_REQUEST['odrBy'];
	$qry.=" ORDER BY ";
	if(empty($odrBy)===false){
	   
	   $odrByExpl = explode(",",$odrBy);			
	   
	   for($aa = 0; $aa<count($odrByExpl); $aa++) {
			if($aa==0) {
				$qry.=" ".$odrByExpl[$aa]." ".$sortOrder;	   
			}else {
				$qry.=", ".$odrByExpl[$aa]." ".$sortOrder;
			}
	   }
	}else {
		$qry.=" patient_data.lname,patient_data.fname ";				   
	}
	$rs = imw_query($qry);

   $allTstArr = array('ascan'=>'A/Scan','bscan'=>'B-Scan','cellcount'=>'Cell Count','external_interior'=>'External/Anterior',
					  'fundus'=>'Fundus','gdx'=>'GDX','hrt'=>'HRT','icg'=>'ICG','iol_master'=>'IOL Master','ivfa'=>'IVFA',
					  'laboratories'=>'Laboratories','oct'=>'OCT','oct_rnfl'=>'OCT-RNFL','pachy'=>'PACHY','topography'=>'Topography',
					  'vf'=>'VF','vf_gl'=>'VF-GL','test_other'=>'Other Test - ');

   $sno=1;
   //pre($testWithoutChartPhyIdArr);
   if(count($testWithoutChartPtIdArr)>0) {
	   foreach($testWithoutChartPtIdArr as $testWithoutChartPtIdKey => $testWithoutChartPtIdVal) {
			if(count($testWithoutChartExamDateArr)>0) {
				$ptIdArr = $phyIdNewArr = $examDtNewArr = array();
				foreach($testWithoutChartExamDateArr as $exmDtKey => $exmDtVal) {
					foreach($allTstArr as $allTstNewKey => $allTstNewVal) {
						$phyIdNew = $testWithoutChartPhyIdArr[$exmDtKey][$allTstNewKey]['phyid'][$testWithoutChartPtIdVal];
						$examDtNew = $testWithoutChartExamDateArr[$exmDtKey][$allTstNewKey]['exm_dt'][$testWithoutChartPtIdVal];

						if($phyIdNewArr[$phyIdNew] && $examDtNew && $examDtNewArr[$examDtNew] && $testWithoutChartPtIdVal && $ptIdArr[$testWithoutChartPtIdVal]) {
							$showTestNameArr[$testWithoutChartPtIdVal][$phyIdNew][$examDtNew][] = $allTstNewVal;	
						}else if($examDtNew) {
							$showProvNameArr[] = $providerNameArr[$testWithoutChartPhyIdArr[$exmDtKey][$allTstNewKey]['phyid'][$testWithoutChartPtIdVal]];
							$showExamDtArr[] = $testWithoutChartExamDateArr[$exmDtKey][$allTstNewKey]['exm_dt'][$testWithoutChartPtIdVal];
							$showPtNameArr[] = $ptResNameArr[$testWithoutChartPtIdVal];
							$showPtIdArr[] = $testWithoutChartPtIdVal;
							$showPhyIdArr[] = $phyIdNew;
							$showTestNameArr[$testWithoutChartPtIdVal][$phyIdNew][$examDtNew][] = $allTstNewVal;
						}
						$ptIdArr[$testWithoutChartPtIdVal] = $testWithoutChartPtIdVal;
						$phyIdNewArr[$phyIdNew] = $phyIdNew;
						$examDtNewArr[$examDtNew] = $examDtNew;
					}
				}
			}
	   }
   }

   while($res=imw_fetch_array($rs)){
	   
	  	$printFile=true;
		$patient_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
		$patient_name.= ' - '.$res['patient_id'];

		//DOS
		if(empty($res['DOS']) || ($res['DOS']=="00-00-0000")){
			$dateOfService = $res["CREATED_DATE"];
		}else{
			$dateOfService = $res['DOS'];
		}
		$dd=explode('-', $dateOfService);
		$dos_ymd = $dd[2].'-'.$dd[0].'-'.$dd[1];

		// Label
		$docId = ($res['providerId']>0) ? $res['providerId'] : 0;

		if(!empty($docId)){
			$strReview = ($arrChartTimer["review"] * 24)." hours ";
			$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";
			$strWarn = $strFinal." - ".$strReview;
			
			//Check
			if(isDtPassed($dos_ymd, $strWarn)){
				$finalizeDt = dtCalc($dos_ymd,"+".$strFinal,"m/d/y");
				$patient_name.= "<br><span style=\"color:#CC0000\"> (Please finalize by ".$finalizeDt.")</span>";
			}
		}
	   $visit = $res['ptVisit'];
	   $tstNewArr = array();
	   foreach($allTstArr as $allTstkey=>$allTstVal) {
			if($testRecArr[$allTstkey][$res['patient_id']][$res['id']]){ 
				if($allTstkey=='test_other') {
					$tstNewArr[] = $allTstVal.$testRecArr['test_other_name'][$res['patient_id']][$res['id']]; 
				}else {
					$tstNewArr[] = $allTstVal; 		
				}
			}   
	   }
	   $tstNewArrImplode="";
	   if(count($tstNewArr)>0) {
		   $tstNewArrImplode = implode(", ",$tstNewArr);
	   }
	   $tstNewArrWithLabel='';
	   if($tstNewArrImplode) {$tstNewArrWithLabel = '<b>Chart - </b>'.$tstNewArrImplode;  }
	   $content_part.='
	   <tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;display:none;">&nbsp;'.$sno.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$providerNameArr[$docId].'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">&nbsp;'.$dateOfService.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; display:none;">&nbsp;'.$dos_ymd.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$patient_name.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$visit.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$tstNewArrWithLabel.'</td>
	   </tr>';
	   
	   $pdf_content_part.='
	   <tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$providerNameArr[$docId].'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">&nbsp;'.$dateOfService.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$patient_name.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$visit.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$tstNewArrWithLabel.'</td>
	   </tr>';
	   
	   $sno++;		   
   }
   //$snoNew=1;
   $showTestNameNewArr = array();
   //pre($showTestNameArr);
   if(count($showProvNameArr)>0) {
		$printFile=true;
		foreach($showProvNameArr as $showProvNameKey => $showProvNameVal) {
			$showTestNameNewArr = $showTestNameArr[$showPtIdArr[$showProvNameKey]][$showPhyIdArr[$showProvNameKey]][$showExamDtArr[$showProvNameKey]];
			//pre($showTestNameNewArr);
			$showTestNameImplode = implode(", ",$showTestNameNewArr);
			$showTestNameWithLabel='';
			if($showTestNameImplode) {$showTestNameWithLabel = '<b>Tests - </b>'.$showTestNameImplode;  }
			list($mmm,$ddd,$yyy) = explode('-',$showExamDtArr[$showProvNameKey]);
			$dosDDMMYY = $yyy.'-'.$mmm.'-'.$ddd;
			$content_new_part.='
				   <tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;display:none;">&nbsp;'.$sno.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$showProvNameArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">&nbsp;'.$showExamDtArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; display:none;">&nbsp;'.$dosDDMMYY.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$showPtNameArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$showTestNameWithLabel.'</td>
				   </tr>';			
					
					$pdf_content_part.='<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$showProvNameArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">&nbsp;'.$showExamDtArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$showPtNameArr[$showProvNameKey].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$showTestNameWithLabel.'</td>
				   </tr>';	
				   
			//$snoNew++;
			$sno++;
		}
   }
   
   
	//-- END FETCHING DATA


	if($printFile==true){
		$conditionChk = true;
		$HTMLCreated=1;
		$op='l';
		//--- PAGE HEADER DATA ---
		$curDate = date($globalphpdtformat.' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		/*
		$providerSelected='All';
		if(sizeof($arrProviderSel)>0){
			$providerSelected = (sizeof($arrProviderSel)>1) ? 'Multi' : $providerNameArr[$providers];  
		}*/
		$providerSelected = $CLSReports->report_display_selected($providers,'physician',1,$prov_cnt);
		if(empty($start_date)==true && empty($en_date)==true){
			$start_date=$end_date='--'; 
		}
		
		$page_header='
		<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr>
				<td class="rptbx1" style="width:33%">
					Unfinalized Encounters Report
				</td>
				<td class="rptbx2" style="width:33%">
					Selected Providers: '.$providerSelected.'
				</td>
				<td class="rptbx3" style="width:33%">
					Created by: '.$op_name.' on '.$curDate.'
				</td>
			</tr>	
			<tr>
				<td class="rptbx1">DOS: From '.$Start_date.' to '.$End_date.'</td>
				<td class="rptbx2"></td>
				<td class="rptbx3"></td>
			</tr>	
		</table>';
		
		$page_data .='
		<table id="unfinal_tbl" style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<thead>
				<tr>
					<td class="text_b_w" style="width:3%; text-align:left;display:none;">&nbsp;S.No.</td>
					<td class="text_b_w" style="width:18%; text-align:left;cursor:pointer;" onclick="sortTable(1,\'provider_sort\',\'unfinal_tbl\');">&nbsp;Provider<span id="provider_sortspan"></span><input size="1" type="hidden" id="provider_sort" value="0"></td>
					<td class="text_b_w" style="width:10%; text-align:center;cursor:pointer;" onclick="sortTable(3,\'dos_sort\',\'unfinal_tbl\');">&nbsp;Date of Service<span id="dos_sortspan"></span><input size="1" type="hidden" id="dos_sort" value="0"></td>
					<td class="text_b_w" style="width:10%; text-align:center;cursor:pointer; display:none;" onclick="sortTable(3,\'dos_sort1\',\'unfinal_tbl\');">&nbsp;Date of Service<span id="dos_sort1span"></span><input size="1" type="hidden" id="dos_sort1" value="0"></td>
					<td class="text_b_w" style="width:19%; text-align:left;cursor:pointer;" onclick="sortTable(5,\'ptnameid_sort\',\'unfinal_tbl\');">&nbsp;Patient Name-Id<span id="ptnameid_sortspan"></span><input size="1" type="hidden" id="ptnameid_sort" value="0"></td>
					<td class="text_b_w" style="width:10%; text-align:left;cursor:pointer;" onclick="sortTable(4,\'visit_sort\',\'unfinal_tbl\');">&nbsp;Visit<span id="visit_sortspan"></span><input size="1" type="hidden" id="visit_sort" value="0"></td>
					
					<td class="text_b_w" style="width:30%; text-align:left;cursor:pointer;" onclick="sortTable(6,\'chrttest_sort\',\'unfinal_tbl\');">&nbsp;Chart/Tests<span id="chrttest_sortspan"></span><input size="1" type="hidden" id="chrttest_sort" value="0"></td>
				</tr>
			</thead>
			<tbody>';
			
			$pdf_page_data .='<table id="unfinal_tbl" style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<thead>
				<tr>
					<td class="text_b_w" style="width:25%; text-align:left;cursor:pointer;">&nbsp;Provider</td>
					<td class="text_b_w" style="width:10%; text-align:center;cursor:pointer;">&nbsp;Date of Service</td>
					<td class="text_b_w" style="width:25%; text-align:left;cursor:pointer;">&nbsp;Patient Name-Id</td>
					<td class="text_b_w" style="width:10%; text-align:left;cursor:pointer;">&nbsp;Visit</td>
					<td class="text_b_w" style="width:30%; text-align:left;cursor:pointer;">&nbsp;Chart/Tests</td>
				</tr>
			</thead>
			<tbody>';
			
				if($content_part) {	
					$page_data .= $content_part;
				}
				if($content_new_part) {	
					$page_data .= $content_new_part;
				}
				
				$pdf_page_data.=$pdf_content_part;
				
		$page_data .= '
			</tbody>
		</table>
		
		';
		$pdf_page_data.='
			</tbody>
		</table>
		
		';
		$page_content= $page_header.$page_data;
		
		
		if(trim($pdf_page_data) != ''){				

			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = 
			$stylePDF.'
			<page backtop="9mm" backbottom="5mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			'.$page_header.'
			</page_header>
			'.$pdf_page_data.'
			</page>';

			$file_location = write_html($html_page_content, 'unapplied_encounters.html');
		}
	}else{
		$page_content = '<div class="text-center alert alert-info">No Record Found.</div>';		
	}
}

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom!='scheduled'){	
		echo $page_content;	
	}
}

//--- SET CHECK IN/OUT REPORT TEMPLATE ---
/*if($callFrom == 'scheduled'){
	if($html_page_content != ""){
		$op='p';
		$page_html_script = $page_content;
		$html_file_name = get_scheduled_pdf_name('unfinalized_enc', '../common/new_html2pdf');
		file_put_contents('../common/new_html2pdf/'.$html_file_name.'.html',$html_page_content);
	}
	
}else{
}
*/?>