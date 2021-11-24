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

$strHTML ='';
if($_REQUEST['form_submitted']){
	
	$facilities = implode(',', $facility_name);
	$arr_app_type = implode(',', $app_type);

	if(sizeof($phyId)>0){
		$strUsers = implode(',', $phyId);
	}else{
		$strUsers = $strUserIds;
	}
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
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	
	$from_date = $start_date;
	$to_date = $end_date;	
	
	$from_time = '00:00:00';
	$to_time = '23:59:59';	

	//GET USER NAMES
	$arrDoctors = array();
	$strDocIds= implode(',', $arrDocIds);
	$vquery_cf = "Select id,fname,mname,lname from users";
	$vsql_cf = imw_query($vquery_cf);
	while($vrsf=imw_fetch_array($vsql_cf)){
		$nameArr = array();
		$nameArr["LAST_NAME"] = $vrsf['lname'];
		$nameArr["FIRST_NAME"] = $vrsf['fname'];
		$nameArr["MIDDLE_NAME"] = $vrsf['mname'];
		$providerName = changeNameFormat($nameArr);
		$arrDoctors[$vrsf['id']] = $providerName;

		// two character array
		$operatorInitial = substr($vrsf['fname'],0,1);
		$operatorInitial .= substr($vrsf['lname'],0,1);
		$userNameTwoCharArr[$vrsf['id']] = strtoupper($operatorInitial);
	}

	function getConsumeTime($startTime, $endTime){
		$docTime='';
		$seconds = strtotime($endTime) - strtotime($startTime);
		$minutes = floor($seconds/60);
		if($minutes>60) {
			$hour=floor($minutes/60);
			$minutes = $minutes%60;
			$docTime=$hour.':'.$minutes;
		}else{
			$docTime='00:'.$minutes;
		}
		return $docTime;
	}

	function getTotTime($tH=0, $tM=0, $tS=0){
		$docTime='';
		if($tS>59) {
			$tM+=floor($tS/60);
			$tS=$tS%60;
		}
		if($tM>59) {
			$tH+=floor($tM/60);
			$tM=floor($tM%60);
		}
		
		if($tH>0 || $tM>0 || $tS>0){
			$tH= ($tH<10) ? '0'.$tH : $tH;
			$tM= ($tM<10) ? '0'.$tM : $tM;
			$tS= ($tS<10) ? '0'.$tS : $tS;

			$tH= ($tH==0) ? '00' : $tH;
			$tM= ($tM==0) ? '00' : $tM;
			$tS= ($tS==0) ? '00' : $tS;

			$docTime = $tH.':'.$tM.':'.$tS;
		}
		
		return $docTime;
	}

	function getConsumeTimeWR($startTime, $endTime){
		$docTime='';
		$seconds = strtotime($endTime) - strtotime($startTime);
		if($seconds<60){
			$seconds= $seconds;
		}else{
			$minutes = floor($seconds/60);
			$seconds = $seconds%60;
			if($minutes>60) {
				$hour=floor($minutes/60);
				$minutes = $minutes%60;
			}else{
				$minutes= $minutes;
			}
		}
		if($hour>0 || $minutes>0 || $seconds>0){
			$hour= ($hour<10) ? '0'.$hour : $hour;
			$minutes= ($minutes<10) ? '0'.$minutes : $minutes;
			$seconds= ($seconds<10) ? '0'.$seconds : $seconds;

			$hour= ($hour==0) ? '00' : $hour;
			$minutes= ($minutes==0) ? '00' : $minutes;
			$seconds= ($seconds==0) ? '00' : $seconds;

			$docTime = $hour.':'.$minutes.':'.$seconds;
		}
		
		return $docTime;
	}

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	//IF SEARCHED BY TECHNICIAN OR SX-COORDINATOR
	$arrTechSxSchIds=array();
	$strTechSxSchIds='';
	if($user_type=='3' || $user_type=='6'){
		$qry="Select scheduler_appt_id FROM patient_monitor WHERE user_type_id='".$user_type."' AND scheduler_appt_id>0";
		if($strUsers !=''){
			$qry.=" AND user_id IN(".$strUsers.")";
		}
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrTechSxSchIds[$res['scheduler_appt_id']]= $res['scheduler_appt_id'];
		}
		$strTechSxSchIds=implode(',', $arrTechSxSchIds);
	}
	//getting schedule appointments
	$strQry="Select patMon.*, patMon.user_type_id as 'user_type', sch.sa_patient_name, sch.sa_patient_id, 
			sch.sa_app_starttime, sch.sa_doctor_id, sch.sa_facility_id, 
			users.fname, users.mname, users.lname  
			FROM schedule_appointments sch 
			LEFT JOIN patient_monitor patMon ON patMon.scheduler_appt_id = sch.id 
			LEFT JOIN users ON users.id = patMon.user_id WHERE patMon.scheduler_appt_id>0";
	if($dayReport!='All' && $from_date!='' && $to_date!=''){
		$strQry.= " AND (patMon.action_date_time BETWEEN '$from_date $from_time' AND '$to_date $to_time') 
		AND (sch.sa_app_start_date BETWEEN '$from_date' AND '$to_date')";
	}
	if($facilities!=''){
		$strQry.= " AND sch.sa_facility_id IN(".$facilities.")";
	}
	
	if(($user_type=='' || $user_type=='1') && $strUsers !=''){
		$strQry.= " AND sch.sa_doctor_id IN(".$strUsers.")";		
	}
	if(($user_type=='3' || $user_type=='6') && empty($strTechSxSchIds)==false){
		$strQry.= " AND sch.id IN(".$strTechSxSchIds.")";
	}
	if($arr_app_type != ''){
		$strQry.= " AND sch.procedureid IN(".$arr_app_type.")";
	}
	$strQry.= "ORDER BY patMon.id";
	$sch_query_res = array();
	$rs=imw_query($strQry);
	while($res = imw_fetch_assoc($rs)){
		$sch_query_res[] = $res;
	}
	$tempArr=array();
	$reportData=array();
	$grandTime = array();
	$arrFacIds = array();
	$arrDocIds = array();
	for($i =0; $i < count($sch_query_res); $i++){

		$fac_id = $sch_query_res[$i]['sa_facility_id'];
		$doc_id = $sch_query_res[$i]['sa_doctor_id'];
		$arrFacIds[$fac_id] = $fac_id;
		$arrDocIds[$doc_id] = $doc_id;
		$patId = $sch_query_res[$i]['sa_patient_id'];
		$user_id = $sch_query_res[$i]['user_id'];
		$sch_id = $sch_query_res[$i]['scheduler_appt_id'];
		$sch_time = core_time_format($sch_query_res[$i]['sa_app_starttime']);
		$action_name = $sch_query_res[$i]['action_name'];
		$action_date = $sch_query_res[$i]['action_date'];
		$dd= explode(' ',$sch_query_res[$i]['action_date_time']);
		$action_date= $dd[0];
		$action_time= $dd[1];

		//IF PATIENT HAS CHECKIN BEFORE APPT TIME THEN WE ARE FETCHING ONLY APPT TIME THAT IS LATER TIME.
		if($action_name=='CHECK_IN' && $sch_query_res[$i]['sa_app_starttime']>$action_time){ 
			$action_time= $sch_query_res[$i]['sa_app_starttime']; 
		}
		
		
		// GET REAL CI/CO TIMES
		$reportData[$fac_id][$doc_id][$sch_id]['PATNAME'] = $sch_query_res[$i]['sa_patient_name'].'-'.$patId;
		$reportData[$fac_id][$doc_id][$sch_id]['APPT_DATE'] = $sch_time;
		if($action_name=='CHECK_IN' && $tempArrCI[$sch_id]==''){
			$tempArrCI[$sch_id] = $dd[1];
			$reportData[$fac_id][$doc_id][$sch_id]['CI_TIME'] = core_time_format($dd[1]);
		}
		if($action_name=='CHECK_OUT' && $tempArrCO[$sch_id]==''){
			$tempArrCO[$sch_id] = $action_time;
			$reportData[$fac_id][$doc_id][$sch_id]['CO_TIME'] = core_time_format($action_time);
		}
		
		
		//GET TOTAL TIME
		if(($action_name=='CHECK_IN') && (!$tempArrTot[$sch_id]['CHECKIN'])){
			$tempArrTot[$sch_id]['CHECKIN']= $action_time; 
		}
		if($action_name=='CHECK_OUT'){
			$tempArrTot[$sch_id]['CHECKOUT']= $action_time;
			$checkTime='';
			if($tempArrTot[$sch_id]['CHECKIN']!='' && $tempArrTot[$sch_id]['CHECKOUT']!=''){
				$checkTime = getConsumeTimeWR($tempArrTot[$sch_id]['CHECKIN'], $tempArrTot[$sch_id]['CHECKOUT']);

				$tt = explode(':', $checkTime);
				$arrSubTot[$fac_id][$doc_id]['TOT_HR']+=$tt[0];
				$arrSubTot[$fac_id][$doc_id]['TOT_MIN']+=$tt[1];
				$arrSubTot[$fac_id][$doc_id]['TOT_SEC']+=$tt[2];
				$arrCheckTime[$sch_id]['Hours'][]= $tt[0];
				$arrCheckTime[$sch_id]['Minutes'][]= $tt[1];
				$arrCheckTime[$sch_id]['Seconds'][]= $tt[2];

				$tH = array_sum($arrCheckTime[$sch_id]['Hours']);
				$tM = array_sum($arrCheckTime[$sch_id]['Minutes']);
				$tS = array_sum($arrCheckTime[$sch_id]['Seconds']);
				$checkTime='';
				$checkTime = getTotTime($tH, $tM, $tS);
				$tempArrTot[$sch_id]['CHECKIN']='';
				$tempArrTot[$sch_id]['CHECKOUT']='';

				$tt = explode(':', $checkTime);
				$reportData[$fac_id][$doc_id][$sch_id]['CHECK'] = $checkTime;
				$grandTime[$sch_id]['Hour']=$tt[0];
				$grandTime[$sch_id]['Minutes']=$tt[1];
				$grandTime[$sch_id]['Seconds']=$tt[2];
			}
		}
		
		//WAITING ROOM TIMES
		if($action_name=='CHECK_IN' || $action_name=='CHART_OPEN'){
			$wrTime='';
			if($action_name=='CHECK_IN' && $tempArrWR[$sch_id]['WR_START']==''){
				$tempArrWR[$sch_id]['WR_START']= $action_time; 
			}
			if(($action_name=='CHART_OPEN') && $tempArrWR[$sch_id]['WR_START']!='' && $tempArrWR[$sch_id]['WR_END']==''){
				$tempArrWR[$sch_id]['WR_END']= $action_time;
				if($tempArrWR[$sch_id]['WR_START']!='' && $tempArrWR[$sch_id]['WR_END']!=''){
					$wrTime = getConsumeTimeWR($tempArrWR[$sch_id]['WR_START'], $tempArrWR[$sch_id]['WR_END']);
					$tt = explode(':', $wrTime);
					$arrSubTot[$fac_id][$doc_id]['WR_HR']+=$tt[0];
					$arrSubTot[$fac_id][$doc_id]['WR_MIN']+=$tt[1];
					$arrSubTot[$fac_id][$doc_id]['WR_SEC']+=$tt[2];

					$reportData[$fac_id][$doc_id][$sch_id]['WR'] = $wrTime;
				}
			}
		}

		//DILATION/TEST TIMES
		if($action_name=='CHECK_IN' || $action_name=='CHART_OPEN' || $action_name=='STATUS_CHANGED' || $action_name=='DILATION'){
			$dialTime='';
			if($action_name=='DILATION' && $tempArrDIAL[$sch_id]['DIAL_START']==''){
				$tempArrDIAL[$sch_id]['DIAL_START']= $action_time; 
			}
			if(($action_name=='CHART_OPEN' || $action_name=='STATUS_CHANGED') && $tempArrDIAL[$sch_id]['DIAL_START']!=''){
				$tempArrDIAL[$sch_id]['DIAL_END']= $action_time;
				if($tempArrDIAL[$sch_id]['DIAL_START']!='' && $tempArrDIAL[$sch_id]['DIAL_END']!=''){
					$dialTime = getConsumeTimeWR($tempArrDIAL[$sch_id]['DIAL_START'], $tempArrDIAL[$sch_id]['DIAL_END']);
					$tt = explode(':', $dialTime);
					$arrSubTot[$fac_id][$doc_id]['DIL_HR']+=$tt[0];
					$arrSubTot[$fac_id][$doc_id]['DIL_MIN']+=$tt[1];
					$arrSubTot[$fac_id][$doc_id]['DIL_SEC']+=$tt[2];
					
					$arrDIALTime[$sch_id]['Hours'][]= $tt[0];
					$arrDIALTime[$sch_id]['Minutes'][]= $tt[1];
					$arrDIALTime[$sch_id]['Seconds'][]= $tt[2];
	
					$tempArrDIAL[$sch_id]['DIAL_START']='';
					$tempArrDIAL[$sch_id]['DIAL_END']='';
				}
			}
	
			$tH = array_sum($arrDIALTime[$sch_id]['Hours']);
			$tM = array_sum($arrDIALTime[$sch_id]['Minutes']);
			$tS = array_sum($arrDIALTime[$sch_id]['Seconds']);
			$dialTime='';
			$dialTime = getTotTime($tH, $tM, $tS);						
	
			$reportData[$fac_id][$doc_id][$sch_id]['DILATION'] = $dialTime;
		}

		//GET USERS TIMES
		if($sch_query_res[$i]['user_type']==1 || $action_name=='CHECK_OUT' || $action_name=='DONE_WITH_PT'){	//FOR PROVIDER
			$docTime='';
			$user_type = $sch_query_res[$i]['user_type'];
			if(($action_name=='CHART_OPEN') && (!$tempArr[$sch_id][$user_id]['START'])){
				$tempArr[$sch_id][$user_id]['START']= $action_time;
				$tempArr[$sch_id][$user_id]['USER_TYPE']= $sch_query_res[$i]['user_type'];
			}
			if($action_name=='CHECK_OUT'){
				$tempD = array_keys($tempArr[$sch_id]);
				$tempUsrId=end($tempD);
				if($tempArr[$sch_id][$tempUsrId]['START']!=''){
					$user_id = $tempUsrId;
					$user_type= $tempArr[$sch_id][$user_id]['USER_TYPE'];
				}
			}
			if($action_name=='CHECK_OUT' || $action_name=='CHART_CLOSE' || $action_name=='DONE_WITH_PT' || $action_name=='STATUS_CHANGED'){
				$tempArr[$sch_id][$user_id]['END']= $action_time;
				$tempArr[$sch_id][$user_id]['USER_TYPE']= $user_type;
				if($tempArr[$sch_id][$user_id]['START']!='' && $tempArr[$sch_id][$user_id]['END']!=''){
					$docTime = getConsumeTimeWR($tempArr[$sch_id][$user_id]['START'], $tempArr[$sch_id][$user_id]['END']);
					$tt = explode(':', $docTime);
					$arrSubTot[$fac_id][$doc_id]['DOC_HR']+=$tt[0];
					$arrSubTot[$fac_id][$doc_id]['DOC_MIN']+=$tt[1];
					$arrSubTot[$fac_id][$doc_id]['DOC_SEC']+=$tt[2];
					
					$arrDocTime[$sch_id]['Hours'][]= $tt[0];
					$arrDocTime[$sch_id]['Minutes'][]= $tt[1];
					$arrDocTime[$sch_id]['Seconds'][]= $tt[2];

					$tempArr[$sch_id][$user_id]['START']='';
					$tempArr[$sch_id][$user_id]['END']='';
				}
			}

			$tH = array_sum($arrDocTime[$sch_id]['Hours']);
			$tM = array_sum($arrDocTime[$sch_id]['Minutes']);
			$tS = array_sum($arrDocTime[$sch_id]['Seconds']);
			$docTime='';
			$docTime = getTotTime($tH, $tM, $tS);

			$reportData[$fac_id][$doc_id][$sch_id]['DOCTOR'] = $docTime; 
		}
		if($sch_query_res[$i]['user_type']==3 || $action_name=='CHECK_OUT' || $action_name=='DONE_WITH_PT'){	//FOR TECHNICIAN
			$techTime='';
			$user_type= $sch_query_res[$i]['user_type'];
			if(($action_name=='CHART_OPEN') && (!$tempArr[$sch_id][$user_id]['START'])){
				$tempArr[$sch_id][$user_id]['START']= $action_time;
				$tempArr[$sch_id][$user_id]['USER_TYPE']= $user_type;
			}
			if($action_name=='CHECK_OUT'){
				$tempD = array_keys($tempArr[$sch_id]);
				$tempUsrId=end($tempD);
				if($tempArr[$sch_id][$tempUsrId]['START']!=''){
					$user_id = $tempUsrId;
					$user_type = $tempArr[$sch_id][$user_id]['USER_TYPE'];
				}
			}
			if($action_name=='CHECK_OUT' || $action_name=='CHART_CLOSE' || $action_name=='DONE_WITH_PT' || $action_name=='STATUS_CHANGED'){
				$tempArr[$sch_id][$user_id]['END']= $action_time; 
				$tempArr[$sch_id][$user_id]['USER_TYPE']= $user_type; 
				if($tempArr[$sch_id][$user_id]['START']!='' && $tempArr[$sch_id][$user_id]['END']!=''){
					$techTime = getConsumeTimeWR($tempArr[$sch_id][$user_id]['START'], $tempArr[$sch_id][$user_id]['END']);
					$tt = explode(':', $techTime);
					$arrSubTot[$fac_id][$doc_id]['TECH_HR']+=$tt[0];
					$arrSubTot[$fac_id][$doc_id]['TECH_MIN']+=$tt[1];
					$arrSubTot[$fac_id][$doc_id]['TECH_SEC']+=$tt[2];
					
					$arrTechTime[$sch_id]['Hours'][]= $tt[0];
					$arrTechTime[$sch_id]['Minutes'][]= $tt[1];
					$arrTechTime[$sch_id]['Seconds'][]= $tt[2];

					$tempArr[$sch_id][$user_id]['START']='';
					$tempArr[$sch_id][$user_id]['END']='';
				}
			}

			$tH = array_sum($arrTechTime[$sch_id]['Hours']);
			$tM = array_sum($arrTechTime[$sch_id]['Minutes']);
			$tS = array_sum($arrTechTime[$sch_id]['Seconds']);
			$techTime='';
			$techTime = getTotTime($tH, $tM, $tS);						

			$reportData[$fac_id][$doc_id][$sch_id]['TECHNICIAN'] = $techTime;
			if($user_type=='3'){
				$reportData[$fac_id][$doc_id][$sch_id]['TECHNICIAN_NAME'][$userNameTwoCharArr[$user_id]] = $userNameTwoCharArr[$user_id];
			}
		}
	}	


	if(count($reportData) > 0)
	{

	$arrFacilites = array();
	if(sizeof($arrFacIds)>0){
		$strFacIds= implode(',', $arrFacIds);
		$vquery_cf = "Select id,name from facility WHERE id IN(".$strFacIds.")";
		$vsql_cf =imw_query($vquery_cf);
		while($vrsf=imw_fetch_array($vsql_cf)){
			$arrFacilites[$vrsf['id']] = $vrsf['name'];
		}
	}

	$rptDate = 'Appt. Date From '.$start_date.' To '.$end_date;
	if($dayReport=='All'){
		$rptDate = 'Appt. Date (ALL)';
	}
//echo '<pre>'; print_r($reportData);	
	$strHTML = '
		<style>
			.tb_heading{
				font-size:10px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
				background-color:#BCD5E1;
			}
			.text_b{
				font-size:10px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000;
				background-color:#BCD5E1;
			}
			.text_10{
				font-size:10px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
		</style>';

	$strHTML.='	
		<page backtop="10mm" backbottom="9mm">			
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Patient Monitor Report</td>
				<td class="rptbx2" style="width:350px;">'.$rptDate.'</td>
				<td class="rptbx3" style="width:350px;">Created By: '.$report_generator_name.' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
			</tr>
		</table>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr>
				<td width="195" class="text_b_w" style="text-align:center">Patient Name - ID</td>
				<td width="105" class="text_b_w" style="text-align:center">Appt. Time</td>
				<td width="105" class="text_b_w" style="text-align:center">CI Time</td>
				<td width="105" class="text_b_w" style="text-align:center">Waiting Room</td>
				<td width="105" class="text_b_w" style="text-align:center">Dilation/Test</td>
				<td width="105" class="text_b_w" style="text-align:center">Technician</td>
				<td width="105" class="text_b_w" style="text-align:center">Doctor</td>
				<td width="105" class="text_b_w" style="text-align:center">CO Time</td>
				<td width="105" class="text_b_w" style="text-align:center">Total Time</td>
			</tr>
		</table>
		</page_header>';			

		$strHTML.='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
		<tr>
			<td width="195" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
			<td width="105" style="text-align:left"></td>
		</tr>';
		
		
		$page_data.='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">
						Patient Monitor Report
					</td>
					<td class="rptbx2" style="width:350px;">
						'.$rptDate.'
					</td>
					<td class="rptbx3" style="width:350px;">
						Created By: '.$report_generator_name.' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;
					</td>
				</tr>
			</table>';
			$page_data.='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td width="210px" class="text_b_w" style="text-align:center">Patient Name - ID</td>
					<td width="100" class="text_b_w" style="text-align:center">Appt. Time</td>
					<td width="100" class="text_b_w" style="text-align:center">CI Time</td>
					<td width="130" class="text_b_w" style="text-align:center">Waiting Room</td>
					<td width="130" class="text_b_w" style="text-align:center">Dilation/Test</td>
					<td width="130" class="text_b_w" style="text-align:center">Technician</td>
					<td width="130" class="text_b_w" style="text-align:center">Doctor</td>
					<td width="100" class="text_b_w" style="text-align:center">CO Time</td>
					<td width="130" class="text_b_w" style="text-align:center">Total Time</td>
				</tr>';			
			
		$count=0; $totAppts=0; $grandHour= $grandMin = $grandSec =0; 
		$arrFacKeys = array_keys($reportData);
		for($i=0; $i<sizeof($arrFacKeys); $i++){
			$facId = $arrFacKeys[$i];
			$facName = $arrFacilites[$facId];
			$arrDocKeys = array_keys($reportData[$facId]);
			$strHTML .= '<tr><td class="text_b_w" align="left" colspan="9">Facility : '.$facName.'</td></tr>';
			$page_data.= '<tr class="rpt_headers"><td class="text_b_w" colspan="9">Facility : '.$facName.'</td></tr>';
						
			for($j=0; $j<sizeof($arrDocKeys); $j++){
				$docId = $arrDocKeys[$j];
				$docName = $arrDoctors[$docId];
				$arrSchIds = array_keys($reportData[$facId][$docId]);
				$patientData = array_values($reportData[$facId][$docId]);
				$strHTML .= '<tr><td class="text_b_w" align="left" colspan="9">Physician : '.$docName.'</td></tr>';
				$page_data.= '<tr class="rpt_headers"><td class="text_b_w" colspan="9">Physician : '.$docName.'</td></tr>';
					
				$docTotAppts=0;
				for($k=0; $k<sizeof($arrSchIds); $k++){
					$page_content='';
					$schId = $arrSchIds[$k];
					$patientName = $patientData[$k]['PATNAME'];
					$totTime = $patientData[$k]['CHECK'];
					$docTime = $patientData[$k]['DOCTOR'];
					$techTime = $patientData[$k]['TECHNICIAN'];
					$surgTime = $patientData[$k]['SURGICAL'];
					$grandHour+= $grandTime[$schId]['Hour'];
					$grandMin+= $grandTime[$schId]['Minutes'];
					$grandSec+= $grandTime[$schId]['Seconds'];
					$techNames = implode(', ', $patientData[$k]['TECHNICIAN_NAME']);
					

					$count++;
					$strHTML .= 
						'<tr>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientName.'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientData[$k]['APPT_DATE'].'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientData[$k]['CI_TIME'].'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientData[$k]['WR'].'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientData[$k]['DILATION'].'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF" width="70" style="padding-left:5px">'.$techTime.' '.$techNames.'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$docTime.'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$patientData[$k]['CO_TIME'].'</td>
							<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;'.$totTime.'</td>
						</tr>';
			
					$page_data.='
						<tr style="height:23px;">
							<td class="text_10 alignLeft white">&nbsp;'.$patientName.'</td>
							<td class="text_10 white" align="left">&nbsp;'.$patientData[$k]['APPT_DATE'].'</td>
							<td class="text_10 white" align="left">&nbsp;'.$patientData[$k]['CI_TIME'].'</td>
							<td class="text_10 white" align="left">&nbsp;'.$patientData[$k]['WR'].'</td>
							<td class="text_10 white" align="left">&nbsp;'.$patientData[$k]['DILATION'].'</td>
							<td class="text_10 white" align="left" width="130" style="padding-left:5px">'.$techTime.' '.$techNames.'</td>
							<td class="text_10 white" align="left">&nbsp;'.$docTime.'</td>
							<td class="text_10 white" align="left">&nbsp;'.$patientData[$k]['CO_TIME'].'</td>
							<td class="text_10 white" align="left">&nbsp;'.$totTime.'</td>
						</tr>';
					$docTotAppts++;
					$totAppts++;
				}
				//WR TIMES
				$totMin = ($arrSubTot[$facId][$docId]['WR_HR']*60) + $arrSubTot[$facId][$docId]['WR_MIN'];
				$wrTime = getTotTime('00', $totMin, $arrSubTot[$facId][$docId]['WR_SEC']);
				$totSec = ($totMin*60) + $arrSubTot[$facId][$docId]['WR_SEC'];
				$ts = round($totSec / $docTotAppts);
				$wrAvgTime = getTotTime('00', '00', $ts);
				//DILATION TIMES
				$totMin = ($arrSubTot[$facId][$docId]['DIL_HR']*60) + $arrSubTot[$facId][$docId]['DIL_MIN'];
				$dilTime = getTotTime('00', $totMin, $arrSubTot[$facId][$docId]['DIL_SEC']);
				$totSec = ($totMin*60) + $arrSubTot[$facId][$docId]['DIL_SEC'];
				$ts = round($totSec / $docTotAppts);
				$dilAvgTime = getTotTime('00', '00', $ts);
				//TECHNICIAN TIMES
				$totMin = ($arrSubTot[$facId][$docId]['TECH_HR']*60) + $arrSubTot[$facId][$docId]['TECH_MIN'];
				$techTime = getTotTime('00', $totMin, $arrSubTot[$facId][$docId]['TECH_SEC']);
				$totSec = ($totMin*60) + $arrSubTot[$facId][$docId]['TECH_SEC'];
				$ts = round($totSec / $docTotAppts);
				$techAvgTime = getTotTime('00', '00', $ts);
				//DOCTOR TIMES
				$totMin = ($arrSubTot[$facId][$docId]['DOC_HR']*60) + $arrSubTot[$facId][$docId]['DOC_MIN'];
				$docTime = getTotTime('00', $totMin, $arrSubTot[$facId][$docId]['DOC_SEC']);
				$totSec = ($totMin*60) + $arrSubTot[$facId][$docId]['DOC_SEC'];
				$ts = round($totSec / $docTotAppts);
				$docAvgTime = getTotTime('00', '00', $ts);
				//TOTAL TIMES
				$totMin = ($arrSubTot[$facId][$docId]['TOT_HR']*60) + $arrSubTot[$facId][$docId]['TOT_MIN'];
				$totTime = getTotTime('00', $totMin, $arrSubTot[$facId][$docId]['TOT_SEC']);
				$totSec = ($totMin*60) + $arrSubTot[$facId][$docId]['TOT_SEC'];
				$ts = round($totSec / $docTotAppts);
				$totAvgTime = getTotTime('00', '00', $ts);

				$page_data.='
				<tr style="height:23px;">
					<td class="text_10 alignLeft white" colspan="3"></td>
					<td class="text_10 white" align="left">&nbsp;<b>Total:</b> '.$wrTime.'<br>&nbsp;<b>Avg:</b> '.$wrAvgTime.'</td>
					<td class="text_10 white" align="left">&nbsp;<b>Total:</b> '.$dilTime.'<br>&nbsp;<b>Avg:</b> '.$dilAvgTime.'</td>
					<td class="text_10 white" align="left">&nbsp;<b>Total:</b> '.$techTime.'<br>&nbsp;<b>Avg:</b> '.$techAvgTime.'</td>
					<td class="text_10 white" align="left">&nbsp;<b>Total:</b> '.$docTime.'<br>&nbsp;<b>Avg:</b> '.$docAvgTime.'</td>
					<td class="text_10 white" align="left"></td>
					<td class="text_10 white" align="left">&nbsp;<b>Total:</b> '.$totTime.'<br>&nbsp;<b>Avg:</b> '.$totAvgTime.'</td>
				</tr>';	

				$strHTML .= 
				'<tr>
					<td class="text_10" align="left" bgcolor="#FFFFFF" colspan="3"></td>
					<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;Total: '.$wrTime.'<br>&nbsp;Avg: '.$wrAvgTime.'</td>
					<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;Total: '.$dilTime.'<br>&nbsp;Avg: '.$dilAvgTime.'</td>
					<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;Total: '.$techTime.'<br>&nbsp;Avg: '.$techAvgTime.'</td>
					<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;Total: '.$docTime.'<br>&nbsp;Avg: '.$docAvgTime.'</td>
					<td class="text_10" align="left" bgcolor="#FFFFFF"></td>
					<td class="text_10" align="left" bgcolor="#FFFFFF">&nbsp;Total: '.$totTime.'<br>&nbsp;Avg: '.$totAvgTime.'</td>
				</tr>';								
			}
		}

//GET AVERAGE TIME
$tm = round((($grandHour * 60) + $grandMin) / $totAppts);
if($tm>0){
	$totTime = getTotTime('00', $tm);
}else{
	$ts = round($grandSec / $totAppts);
	$totTime = getTotTime('00', '00', $ts);
}
		
$strHTML .= '
	<tr><td colspan="9" class="text_b_w" bgColor="#FFFFFF">Total Appointments:&nbsp;'.$totAppts.'</td></tr>
	<tr><td colspan="9" class="text_b_w" bgColor="#FFFFFF">Average time:&nbsp;'.$totTime.'</td></tr>
	</table></page>';
$page_data .= '
	<tr style="height:25px;"><td colspan="9" class="text_10" style="font-weight:bold; background-color:#FFFFFF">Total Appointments:&nbsp;'.$totAppts.'</td></tr>
	<tr style="height:25px;"><td colspan="9" class="text_10" style="font-weight:bold; background-color:#FFFFFF">Average time:&nbsp;'.$totTime.'</td></tr>
	</table>';
}

}
if($page_data!='') {
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	echo $styleHTML.$page_data;
	
	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML;
	$printFile = 0;
	if($strHTML!='') {
		$printFile = 1;
	}
	$file_location = write_html($strHTML);	
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}	
?>
