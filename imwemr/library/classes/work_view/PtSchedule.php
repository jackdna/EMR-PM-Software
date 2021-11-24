<?php
//Patient
class PtSchedule{
	public $pid;	
	public function __construct($id){
		$this->pid = $id ;
	}
	
	//POS FROM scheduler: returns pos_facility ID based on schedule appointment
	// Superbill should save pos_prac_code, not pos_id
	function getPatientPOS($dt){
		$pid = $this->pid;
		$ret="";
		$sql = "SELECT c4.pos_prac_code
				FROM schedule_appointments c1
				LEFT JOIN facility c2 ON c2.id = c1.sa_facility_id
				LEFT JOIN pos_facilityies_tbl c3 ON c3.pos_facility_id = c2.fac_prac_code
				LEFT JOIN pos_tbl c4 ON c4.pos_id = c3.pos_id
				WHERE sa_patient_id='".$pid."' AND sa_app_start_date ='".$dt."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$ret = $row["pos_prac_code"];
		}
		return $ret;
	}
	
	//START
	public function getApptInfo($providerIds=0,$report_start_date,$report_end_date){
		$patient_id = $this->pid;
		$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = "";
		$schDataQryRes=array();		
		if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}
		
		if($report_start_date || $report_end_date){
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext, fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						WHERE sa_patient_id = '".$patient_id."'
						AND sc.sa_app_start_date BETWEEN '".$report_start_date."' AND '".$report_end_date."'
						AND sc.sa_patient_app_status_id NOT IN('18','203')
						$andSchProvQry
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,1";
			$schDataQryRes[0] = sqlQuery($schDataQry);
		}
		
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date >= current_date() 
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							AND sc.sa_patient_app_status_id IN('0','13','17','202')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date ASC
							LIMIT 0,1";
			$schDataQryRes[0] = sqlQuery($schDataQry);
		}		
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date() 
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes[0] = sqlQuery($schDataQry);		
		}
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."'') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = sc.sa_doctor_id 
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date() 
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes[0] = sqlQuery($schDataQry);		
		}
		if(count($schDataQryRes)>0) {
			for($i=0;$i<count($schDataQryRes);$i++){
				$appStrtDate 			= $schDataQryRes[$i]['appStrtDate'];
				$appStrtDate_FORMAT 	= $schDataQryRes[$i]['appStrtDate_FORMAT'];
				$facName 				= $schDataQryRes[$i]['facName'];
				$facStreet 				= $schDataQryRes[$i]['facStreet'];
				$facCity 				= $schDataQryRes[$i]['facCity'];
				$facState 				= $schDataQryRes[$i]['facState'];
				$facPostal_code			= $schDataQryRes[$i]['facPostal_code'];
				$faczip_ext				= $schDataQryRes[$i]['faczip_ext'];
				$facPhone 				= $schDataQryRes[$i]['facPhone'];
				$facPhoneFormat			= $facPhone;
				if(trim($facPhoneFormat)) {
					$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}
				
				$procName 				= $schDataQryRes[$i]['procName'];
				$doctorName 			= $schDataQryRes[$i]['doctorName'];
				$doctorLastName 		= $schDataQryRes[$i]['doctorLastName'];
				
				$appSite 				= ucfirst($schDataQryRes[$i]['appSite']);
				$appSiteShow 			= $appSite;
				if($appSite == "Bilateral") {$appSiteShow="Both"; }
				
				$appStrtTime 			= $schDataQryRes[$i]['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

				$appComments 			= $schDataQryRes[$i]['sa_comments'];
				$appComments 			= htmlentities($appComments);
				$appcasetypeid			= $schDataQryRes[$i]['casetypeid'];
			}
		}
		$appInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$procName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments,$facStreet,$facCity,$facState,$facPostal_code,$faczip_ext,$appcasetypeid);
		return $appInfo;
	}
	//END
	
	function add_future_sch_tests_appoints($form_id){
		
		$patient_id = $this->pid;
	
		if(isset($_POST["elem_FSTA_editid"])){
				
			$sql_insert = "INSERT INTO chart_schedule_test_external SET ";
			$sql_update = "UPDATE chart_schedule_test_external SET ";
			
			$sql_data =	"
						reff_phy = '".sqlEscStr($_POST["elem_fsta_phy_name"])."' , 
						appoint_test = '".$_POST["elem_fsta_test_appoint"]."' , 
						phy_address = '".sqlEscStr($_POST["elem_fsta_phy_address"])."',
						reason = '".sqlEscStr($_POST["elem_fsta_reason"])."', 
						schedule_date = '".getDateFormatDB($_POST["elem_fsta_sch_date"])."', 
						variation = '".sqlEscStr($_POST["elem_fsta_variation"])."',
						test_type = '".$_POST["elem_fsta_test_type"]."', 
						snomed = '".sqlEscStr($_POST["elem_fsta_sch_snomed"])."', 
						cpt = '".sqlEscStr($_POST["elem_fsta_sch_cpt"])."',
						loinc = '".sqlEscStr($_POST["elem_fsta_sch_loinc"])."', 
						operator_id = '".$_SESSION["authId"]."', 
						modi_date = '".wv_dt('now')."',
						test_name= '".sqlEscStr($_POST["elem_fsta_test_name"])."'
					";
			
			if(!empty($_POST["elem_FSTA_editid"])){
				//edit
				$edrow = sqlQuery($sql_update . $sql_data . " WHERE id = '".$_POST["elem_FSTA_editid"]."' ");
				$editId = $_POST["elem_FSTA_editid"];
				
			}else{
				//insert
				$sql_data .=",
					patient_id = '".$patient_id."' , 
					form_id = '".$form_id."', 
					DOS = '".wv_formatDate($_POST["elem_dos"],0,0,"insert")."'
				";
				
				$editId = sqlInsert($sql_insert . $sql_data);
			}		
			
		}else if(!empty($_GET["elem_showId"])){
			$editId = $_GET["elem_showId"];
		}else if(isset($_GET["elem_delid"]) && !empty($_GET["elem_delid"])){
			
			$str_delid  = urldecode($_GET["elem_delid"]);
			$str_delid = rtrim($str_delid,",");
			//$arr_delid  = explode(",", $str_delid);
			
			$sql = "UPDATE chart_schedule_test_external SET deleted_by = '".$_SESSION["authId"]."' WHERE id IN (".$str_delid.") ";				
			$row = sqlQuery($sql);
		}
		
		include($GLOBALS['fileroot']."/interface/chart_notes/future_sch_tests_appoints.php");		
		
	}
	
	
	public function getFutureSchTestCount(){
		
		$patient_id = $this->pid;
		
		$query = "SELECT id FROM chart_schedule_test_external 
											 WHERE patient_id = '".$patient_id."' 
											 AND deleted_by = '0' ORDER BY id ";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		return $cnt;
	}
	
	//
	public function getFutureAppointments($mode="", $dt="")
	{
		$arr_fa_ret = array();		
		$stts="201,18,19,20,203"; 
		$phse_time = " and CONCAT(schedule_appointments.sa_app_start_date ,' ', schedule_appointments.sa_app_starttime) > CONCAT(CURDATE(),' ', CURTIME()) ";
		if($mode == "2"){ //superbill printing
			$stts="201,203";
			
			$phse_time = " and schedule_appointments.sa_app_start_date>'".$dt."'  ";
		}else if($mode == "3"){ //consult letters
			$stts="18";
		}
		
		//array status names
		$appStatusArr = array(0=>"New", 2=>"Chart Pulled", 3=>"No-Show", 6=>"Left Without Visit", 7=>"Insurance/Financial Issue",
								11=>"Check-out", 13=>"Check-in", 17=>"Confirmed", 18=>"Cancel",
								100=>"Waiting for Surgery", 101=>"Scheduled for Surgery",
								200=>"Room # assignment", 201=>"To-Do", 202=>"Reschedule");
	
		$data="";
		$qry = "SELECT schedule_appointments.sa_app_start_date as start_date,
				TIME_FORMAT(schedule_appointments.sa_app_starttime,'%h:%i %p') as start_time,
				 users . fname,users . lname , facility . name ,slot_procedures.acronym as procName,
				 schedule_appointments.sa_patient_app_status_id
				FROM schedule_appointments
				LEFT JOIN users ON users.id = schedule_appointments.sa_doctor_id
				LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
				LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
				WHERE schedule_appointments.sa_patient_id = '".$this->pid."' and
				schedule_appointments.sa_patient_app_status_id NOT IN(".$stts.")
				".$phse_time."
				ORDER BY sa_app_start_date, sa_app_starttime
				";
		
		$rez = sqlStatement($qry);
		//return $rez;
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$name = explode(' ',$row['name']);
			$start_date = wv_formatDate($row['start_date'],1);
			$n = '';
			for($j = 0;$j<count($name);$j++){
			    if(count($name) > 1){
				$n .= substr($name[$j],0,1);
			    }else{
			    $n .= substr($name[$j],0,2);
			    }
			}
			
			if($mode == "3"){
				$data.='
					<tr >
						<td>'.$start_date.'</td>
						<td>'.$row['fname'].' '.substr($row['lname'],0,1).'</td>
						<td>'.$n.'</td>
						<td>'.$row['procName'].'</td>
					</tr>
					';
			}else{			
			
				$data .= "<b>".$start_date."</b>" .' '.
				$row['start_time'].'&nbsp;&nbsp;'.
				$row['fname'].' '.substr($row['lname'],0,1).'&nbsp;&nbsp;'.
				$row['procName'].'&nbsp;&nbsp;'.
				strtoupper($n).', <br>';
				
				//--
				if($mode == "2"){
					$intAppStatus = $row['sa_patient_app_status_id'];
					$strAppStatus = "";
					$strAppStatus = $appStatusArr[$intAppStatus];
					
					//--
					
					$arr_fa_ret[$i]["sch_app_date_imp"]=$start_date;
					//$sch_app_time_exp=explode(':',$row['start_time']);
					$arr_fa_ret[$i]["sch_app_time_imp"]=$row['start_time'] ; //date('h:i A',mktime($sch_app_time_exp[0],$sch_app_time_exp[1],$sch_app_time_exp[2],$sch_app_date_exp[1],$sch_app_date_exp[2],$sch_app_date_exp[0]));
					$arr_fa_ret[$i]["strAppStatus"]=$strAppStatus;
					$arr_fa_ret[$i]["procName"]=$row['procName'];
				
				}
			}
			//--
		}
		
		if($mode == "3"){
			if($data == ''){
				$data = "No Future Appointments";
			}else{
				$data = '	<table  class=\"table\">
						<tr >
							<td><strong>Date</strong></td>
							<td><strong>Doctor</strong></td>
							<td><strong>Facility</strong></td>
							<td><strong>Procedure</strong></td>
						</tr>'.$data.'
						</table>';
			}
			
		}else if($mode == "2"){
		
			$data = $arr_fa_ret;
		
		}else{
		
			if($data == ''){
				$data = "No Future Appointments";
			}else{
				$data =  substr_replace($data,"",-6);
			}
		
		}
		
		return $data;
	}
	
	function getVisitType($dos){
		$arrSite = array("Left"=>"OS","Right"=>"OD","Bilateral"=>"OU");
		$vt = "";
		$sql = "
				SELECT
				schedule_appointments.id AS apptid,
				slot_procedures.proc AS procname,
				schedule_appointments.`procedureid` ,
				schedule_appointments.`procedure_site`

				FROM slot_procedures, schedule_appointments
				WHERE slot_procedures.id = `schedule_appointments`.`procedureid`
				AND schedule_appointments.`sa_app_start_date` = '".$dos."'
				AND schedule_appointments.`sa_patient_id` = '".$this->pid."'
				";			
		$row = sqlQuery($sql);
		if($row != false){
			$prcdr = $row["procname"];
			$site = trim($row["procedure_site"]);
			$tmp = $arrSite[$site];
			if(empty($tmp)) $tmp = $site;
			if(!empty($prcdr)){
				$vt = trim($prcdr);
				if(!empty($tmp)){$vt = $vt."-".$tmp;}
			}
		}
		return $vt;
	}

}
?>