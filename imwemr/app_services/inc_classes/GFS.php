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
set_time_limit(900);
include_once(dirname(__FILE__).'/chart_notes.php');
class GFS extends chart_notes{	
	public function __construct(){
		parent::__construct();
	}
	
	function get_GFS(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$proc_type = (isset($_REQUEST['proc_type']) && $_REQUEST['proc_type'] !="")?$_REQUEST['proc_type']:"";
		$this->db_obj->qry = "SELECT
							cp.site,
							op.procedure_name,
							cp.intravit_meds,
							IF(cp.complication=1,'Y','N') AS comp,
							cp.dx_code,
							CONCAT(c1.sel_od,' ',c1.txt_od) AS od_va,
							CONCAT(c1.sel_os,' ',c1.txt_os) AS os_va,
							cp.cmt,
							CONCAT(cp.iop_type,' ',iop_od) AS iop_od,
							CONCAT(cp.iop_type,' ',iop_os) AS iop_os,
							cp.user_id,
							date_format(cmt.date_of_service,'%m-%d-%Y') as dos,cmt.id as form_id
							FROM chart_procedures AS cp 
							JOIN chart_master_table AS cmt ON (cmt.id=cp.form_id AND cmt.patient_id=cp.patient_id) 
							LEFT JOIN operative_procedures op ON op.procedure_id = cp.proc_id
							LEFT JOIN chart_vis_master cv ON (cv.form_id = cp.form_id AND cv.patient_id=cp.patient_id)
							LEFT JOIN chart_acuity c1 ON c1.id_chart_vis_master = cv.id AND c1.sec_name = 'Distance' AND c1.sec_indx = '1'
							WHERE cp.patient_id='".$this->patient."'";
							
		if($form_id != "")
		$this->db_obj->qry .="  AND cp.form_id = '".$this->form_id."'";
		$this->db_obj->qry .="	AND (cp.site = '".$site."' OR cp.site = 'OU')
								AND cmt.finalize='1' 
								AND op.ret_gl = 2
								AND op.del_status !='1'
								ORDER BY cmt.date_of_service DESC
							";
		$result = $this->db_obj->get_resultset_array();	
		foreach($result as $record){
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$record['od_va'] = $this->getPatentOct($this->patient,$this->form_id);
			$record['os_va'] = $this->getPatentOct($this->patient,$this->form_id);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
			}
			$arrReturn[$site][] = $record;
		}
		return $arrReturn;
	}
	
	function get_GFS_app(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$proc_type = (isset($_REQUEST['proc_type']) && $_REQUEST['proc_type'] !="")?$_REQUEST['proc_type']:"";
		$this->db_obj->qry = "SELECT
							cp.site,
							op.procedure_name,
							cp.intravit_meds,
							IF(cp.complication=1,'Y','N') AS comp,
							cp.dx_code,
							CONCAT(c1.sel_od,' ',c1.txt_od) AS od_va,
							CONCAT(c1.sel_os,' ',c1.txt_os) AS os_va,
							cp.cmt,
							CONCAT(cp.iop_type,' ',iop_od) AS iop_od,
							CONCAT(cp.iop_type,' ',iop_os) AS iop_os,
							cp.user_id,
							date_format(cmt.date_of_service,'%m-%d-%Y') as dos,cmt.id as form_id
							FROM chart_procedures AS cp 
							JOIN chart_master_table AS cmt ON (cmt.id=cp.form_id AND cmt.patient_id=cp.patient_id) 
							LEFT JOIN operative_procedures op ON op.procedure_id = cp.proc_id
							LEFT JOIN chart_vis_master cv ON (cv.form_id = cp.form_id AND cv.patient_id=cp.patient_id)
							LEFT JOIN chart_acuity c1 ON c1.id_chart_vis_master = cv.id AND c1.sec_name = 'Distance' AND c1.sec_indx = '1'
							WHERE cp.patient_id='".$this->patient."'";
							
		if($form_id != "")
		$this->db_obj->qry .="  AND cp.form_id = '".$this->form_id."'";
		$this->db_obj->qry .="	AND (cp.site = '".$site."' OR cp.site = 'OU')
								AND cmt.finalize='1' 
								AND op.ret_gl = 2
								AND op.del_status !='1'
								ORDER BY cmt.date_of_service DESC
							";
		$result = $this->db_obj->get_resultset_array();	
		$count=0;
		$count_1=0;
		foreach($result as $record){
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$record['od_va'] = $this->getPatentOct($this->patient,$this->form_id);
			$record['os_va'] = $this->getPatentOct($this->patient,$this->form_id);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			//$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
			}
			if($record['site'] == "OU"){
				$record['site'] = "OS";
				$arrReturn["OU"][] = $record;
				$record['site'] = "OD";
				$arrReturn["OU"][] = $record;
				$count++;
			}else{
				$arrReturn["OS & OD"][]= $record;
				$count_1++;
			}
			// new array for app
			if($count==0)
			$arrReturn["OU"]=array(); 
			if($count_1==0)
			$arrReturn["OS & OD"]=array(); 
			// end of new code //
			//$arrReturn[$site][] = $record;
		}
		return $arrReturn;
	}
	
	
	function getPatentOct($patientID,$formID){
		$ret="N";
		if($patientID && $formID){
			$qryOctTest="SELECT oct_id from oct where form_id='".$formID."' AND patient_id='".$patientID."'";
			$resOctTest=imw_query($qryOctTest);
			if(imw_num_rows($resOctTest)>0){
				$ret="Y";	
			}
		}
		return $ret;
	}
	function get_GFS_print(){
		$patient=$this->patient;
		include "chart_glucoma.php";
		die();
	}
	
	//------Start of get_Description---------------//
	function get_Description(){
	
		$patient_id = $_REQUEST['patId'];
		$result = array();
		
		//  glucoma main //
		$glucoma_main_qry="SELECT glucomaId ,activate,dateActivation,diagnosis_description,diagnosisOd ,diagnosisOs , staging_code_od ,staging_code_os,
						elem_dateHighTmaxOd	, elem_dateHighTmaxOs, elem_highTmaxOdOd, elem_highTmaxOsOs ,riskFactors, warnings ,iopTrgtOd ,iopTrgtOs,notes
						FROM glucoma_main WHERE patientId = '".$patient_id."'  
						order by glucomaId  DESC limit 0,1";
		
		$row_glucoma_main=imw_query($glucoma_main_qry);
		$result_glucoma_main = imw_fetch_assoc($row_glucoma_main);
		
		$exploded_data = explode('-',$result_glucoma_main['dateActivation']);
		
		if($exploded_data[1]<=12){
			$result_glucoma_main['dateActivation'] = date("m-d-Y", strtotime($result_glucoma_main['dateActivation']));
		}
		else {
			$result_glucoma_main['dateActivation'] = str_replace("-","/",$result_glucoma_main['dateActivation']);
			
			$result_glucoma_main['dateActivation'] = date("m-d-Y", strtotime($result_glucoma_main['dateActivation']));
		}
			
		//print_r($result_glucoma_main);die();
		if($result_glucoma_main==false){
			$result_glucoma_main=array();
		}
		else{
			if(!empty($result_glucoma_main['riskFactors']) && trim($result_glucoma_main['riskFactors'])!=","){
				
				$flag=false;
				$rowTempRisk['riskFactors']=explode(",",$result_glucoma_main['riskFactors']);
				$matchRisk['riskFactors']=array("Family History"=>0,"Diabetes"=>0,"PXF"=>0,"Steroid Responder"=>0,"PDS"=>0,
												"Race"=>array("Race-American Indian or Alaska Native"=>0,"Race-Asian"=>0,"Race-Black or African American"=>0,"Race-Native Hawaiian or Other Pacific Islander"=>0,"Race-Latin American"=>0,"Race-White"=>0));
				
				foreach($rowTempRisk['riskFactors'] as $value){
					foreach($matchRisk['riskFactors'] as $key=>$valueRisk){
						if($value==$key){
							$matchRisk['riskFactors'][$key]=1;
						}
					}
				}
				foreach($rowTempRisk['riskFactors'] as $value){
					foreach($matchRisk['riskFactors']['Race'] as $key=>$valueRisk){
						if($value==$key){
							$matchRisk['riskFactors']["Race"][$key]=1;
							$flag=true;
						}
					}
				}
				if($flag==false)$matchRisk['riskFactors']["Race"]=array();
				unset($result_glucoma_main['riskFactors']);
				$result_glucoma_main['riskFactors']=$matchRisk['riskFactors'];
			}
			else{
				unset($result_glucoma_main['riskFactors']);
				$result_glucoma_main['riskFactors']=array();
			}
			if(!empty($result_glucoma_main['warnings'])){
					
				$matchWarn['warnings']=array("Arrhythmia"=>0,"Asthma/COPD"=>0,"Bradycardia"=>0, "CHF"=>0,"Sulfa Allergy"=>0,"Depression"=>0);
					
				$rowTempWarn['warnings']=explode(",",$result_glucoma_main['warnings']);
					
				foreach($matchWarn['warnings'] as $key=>$valueWarn){					
					foreach($rowTempWarn['warnings'] as $value){
						if($value==$key){
							$matchWarn['warnings'][$key]=1;
						}	
					}
				}
				unset($result_glucoma_main['warnings']);
				$result_glucoma_main['warnings']=$matchWarn['warnings'];
			}
			else{
				unset($result_glucoma_main['warnings']);
				$result_glucoma_main['warnings']=array();
			}
		}
		// end glucoma main //
				
		/* Pachy */
		
	$result_pachy_data = array();					
	$pachy_req_qry = "SELECT pachy_id,DATE_FORMAT(examDate,'%m-%d-%Y') AS exam_Date,pachy_od_readings,pachy_od_average,pachy_od_correction_value,pachy_os_readings,pachy_os_average,pachy_os_correction_value FROM pachy WHERE patientId = '".$patient_id."' order by pachy_id DESC";	
	$pachy_req_qry_obj = imw_query($pachy_req_qry);
	$result_pachy_data = array();
	$pachy_inc = 0;
	while($pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj))
	{
		//$result_pachy_data[$pachy_inc]["date"] = get_date_format($pachy_dos_data["examDate"]);
		if($pachy_dos_data["exam_Date"]!='00-00-0000'){
			$result_pachy_data[$pachy_inc]["date"] = $pachy_dos_data["exam_Date"];
		}
		//$result_pachy_data[$pachy_inc]["date"] = $pachy_dos_data["exam_Date"];
		$result_pachy_data[$pachy_inc]["od_readings"] = $pachy_dos_data["pachy_od_readings"];
		$result_pachy_data[$pachy_inc]["os_readings"] = $pachy_dos_data["pachy_os_readings"];
		$result_pachy_data[$pachy_inc]["od_correction"] = $pachy_dos_data["pachy_od_correction_value"];
		$result_pachy_data[$pachy_inc]["os_correction"] = $pachy_dos_data["pachy_os_correction_value"];				
		$result_pachy_data[$pachy_inc]["od_avg"] = $pachy_dos_data["pachy_od_average"];				
		$result_pachy_data[$pachy_inc]["os_avg"] = $pachy_dos_data["pachy_os_average"];						
		
		$pachy_inc++;
	}
	
	$pachy_req_qry = "SELECT cor_id,DATE_FORMAT(cor_date,'%m-%d-%Y') AS corDate,reading_od,reading_os,cor_val_od,cor_val_os,avg_od,avg_os FROM chart_correction_values WHERE patient_id = '".$patient_id."' and (reading_od!='' or reading_os!='') order by cor_id DESC";	
	$pachy_req_qry_obj = imw_query($pachy_req_qry);
	//$pachy_dos_data_arr = array();
	while($pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj))
	{
		$cor_date = get_date_format($pachy_dos_data["cor_date"]);
		if(trim($cor_date) != "")
		{
			$matched = 0;
			foreach($result_pachy_data as $pachy_val_arr)
			{
				if(trim($pachy_val_arr["date"]) == $cor_date)
				{															
					if($pachy_val_arr["od_readings"] == $pachy_dos_data["reading_od"] && $pachy_val_arr["os_readings"] == $pachy_dos_data["reading_os"] && $pachy_val_arr["od_correction"] == $pachy_dos_data["cor_val_od"] && $pachy_val_arr["os_correction"] == $pachy_dos_data["cor_val_os"] && $pachy_val_arr["od_avg"] == $pachy_dos_data["avg_od"] && $pachy_val_arr["os_avg"] == $pachy_dos_data["avg_os"])
					{
						//break;
						$matched = 1;
					}
				}				
			}
			if($matched == 0)
			{
				//$result_pachy_data[$pachy_inc]["date"] = get_date_format($pachy_dos_data["cor_date"]);
				if($pachy_dos_data["corDate"]!='00-00-0000'){
					$result_pachy_data[$pachy_inc]["date"] = $pachy_dos_data["corDate"];
				}
				$result_pachy_data[$pachy_inc]["od_readings"] = $pachy_dos_data["reading_od"];
				$result_pachy_data[$pachy_inc]["os_readings"] = $pachy_dos_data["reading_os"];
				$result_pachy_data[$pachy_inc]["od_correction"] = $pachy_dos_data["cor_val_od"];
				$result_pachy_data[$pachy_inc]["os_correction"] = $pachy_dos_data["cor_val_os"];				
				$result_pachy_data[$pachy_inc]["od_avg"] = $pachy_dos_data["avg_od"];				
				$result_pachy_data[$pachy_inc]["os_avg"] = $pachy_dos_data["avg_os"];	
				$pachy_inc++;			
			}
		}
	}
		if($result_pachy_data==false)$result_pachy_data=array();
		//end of pachy //
		
		// vf-gl //	
		$result_vf_gl_data = array();	
		$vf_gl_txt_qry = "SELECT vf_gl_id, DATE_FORMAT(examDate,'%m-%d-%Y') AS examDate, synthesis_od, synthesis_os 	
							FROM vf_gl WHERE patientId = '".$patient_id."' order by vf_gl_id DESC ";
		$row_vf_gl = imw_query($vf_gl_txt_qry);
		while($result_vf_gl = imw_fetch_assoc($row_vf_gl)){
			if($result_vf_gl['examDate']=='00-00-0000'){
				$result_vf_gl['examDate']='';
			}
			$result_vf_gl_data[]=$result_vf_gl;
		}
		if($result_vf_gl_data==false)$result_vf_gl_data=array();
		// end of vf-gl //
		
		// rnfl //
		$result_rnfl_gl_data = array();
		$rnfl_gl_txt_qry = "SELECT oct_rnfl_id, DATE_FORMAT(examDate,'%m-%d-%Y') AS examDate, synthesis_od,synthesis_os 
						FROM oct_rnfl WHERE patient_id = '".$patient_id."' order by oct_rnfl_id DESC";
		$row_rnfl_gl=imw_query($rnfl_gl_txt_qry);
		while($result_rnfl_gl = imw_fetch_assoc($row_rnfl_gl)){
			if($result_rnfl_gl['examDate']=='00-00-0000'){
				$result_rnfl_gl['examDate']='';
			}
			$result_rnfl_gl_data[] = $result_rnfl_gl;
		}
		
		if($result_rnfl_gl_data==false)$result_rnfl_gl_data=array();
		//end of rnfl //
		
		// goino //
		
		$gonio_req_qry = "SELECT gonio_id,DATE_FORMAT(examDateGonio,'%m-%d-%Y') AS examDateGonio, gonio_od_summary, gonio_os_summary 
						FROM chart_gonio WHERE  patient_id = '".$patient_id."' order by gonio_id DESC";

		$row_gonio =imw_query($gonio_req_qry);
		$arr=array();
		$result_gonio_data=array();
		while($result_gonio = imw_fetch_assoc($row_gonio)){
			if($result_gonio['examDateGonio']=='00-00-0000'){
				$result_gonio['examDateGonio']='';
			}
			$result_gonio["gonio_od_summary"]=(!empty($result_gonio["gonio_od_summary"])) ? "Done" : "Not Done";  
			$result_gonio["gonio_os_summary"]=(!empty($result_gonio["gonio_os_summary"])) ? "Done" : "Not Done";  
			$result_gonio_data[] = $result_gonio;
		}
		if($result_gonio_data==false)$result_gonio_data=array();	
		// end of gonio //
		
		// disc apperance //
		$result_optic_data = array();
		$optic_qry="select optic_id , DATE_FORMAT(exam_date,'%m-%d-%Y') AS exam_date, od_text, os_text
					from chart_optic where  patient_id='$patient_id' order by optic_id DESC";	

		$row_optic=imw_query($optic_qry);
		while($result_optic  = imw_fetch_assoc($row_optic)){
			if($result_optic['exam_date']=='00-00-0000'){
				$result_optic['exam_date']='';
			}
			$result_optic_data[] = $result_optic;
		}
		if($result_optic_data==false)$result_optic_data=array();
		// end of disc apperance //
		
		
		$result['GLUCOMA-MAIN']=$result_glucoma_main;
		$result['PACHY']=$result_pachy_data;
		$result['VF-GL']=$result_vf_gl_data;
		$result['RNFL-GL']=$result_rnfl_gl_data;
		$result['GONIO'] = $result_gonio_data;
		$result['DISC'] = $result_optic_data;
		
		//echo "<pre>";
		//print_r($result);
		//$result=json_encode($result);
		
		return $result;
	}
	//------------end of get_Description-----------//
	
	
	
	
	//-----------Start of get_GFS_new-------------//
	public function get_GFS_new(){
		$patient=$this->patient;
		$row_data=array();
		$arrReturn=array();
		
	$arrReturn = array();
	$gfs_qry = imw_query("SELECT 
						  c1.*,
						  c2.purge_status,		
						  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
						  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
						  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth			  			  
						  FROM glaucoma_past_readings c1
						  LEFT JOIN chart_master_table c2 ON c2.id = c1.formId
						  WHERE c1.patientId ='".$patient."' && (c2.purge_status=0 OR c2.purge_status is null)
						  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, c1.id DESC")or die(imw_error());
	$i=0;					
	$rec_num=imw_num_rows($gfs_qry);
	while($record=imw_fetch_array($gfs_qry)){
		$i++;
		
		$log_ta_time=$record["ta_time"];
		$log_tp_time=$record["tp_time"];
		$log_tx_time=$record["tx_time"];
		$logTaOd = $record['taOd'];
		$logTaOs = $record['taOs'];
		$logTpOd = $record['tpOd'];
		$logTpOs = $record['tpOs'];
		$logTxOd = $record['txOd'];
		$logTxOs = $record['txOs'];
		
		$log_va_od_summary_data="";
		$log_va_os_summary_data="";
		
		$log_iop_od_summary_data="";
		$log_iop_os_summary_data="";
		//-------New var added-------//
		$log_tp_od_summary_data="";
		$log_tp_os_summary_data="";
		$log_tx_od_summary_data="";
		$log_tx_os_summary_data="";
		//-------end of new var------//
				
		if($record['va_od_summary']!=""){
			$log_va_od_summary_data=$record['va_od_summary'];
		}
		
		if($record['va_os_summary']!=""){
			$log_va_os_summary_data= $record['va_os_summary'];
		}
		
			$iop_od_summary_val_arr['ta']=$logTaOd;
			$iop_os_summary_val_arr['ta']=$logTaOs;
	
		
	
			$iop_od_summary_val_arr['tx']=$logTxOd;
			$iop_os_summary_val_arr['tx']=$logTxOs;
		
		
		
			$iop_od_summary_val_arr['tp']=$logTpOd;
			$iop_os_summary_val_arr['tp']=$logTpOs;
		
		
		arsort($iop_od_summary_val_arr);
		arsort($iop_os_summary_val_arr);						
		$log_iop_od_summary=implode(',',$iop_od_summary_val_arr);
		$log_iop_os_summary=implode(',',$iop_os_summary_val_arr);	
		

		$iop_od_summary_val_arr['ta']=($iop_od_summary_val_arr['ta'])?$iop_od_summary_val_arr['ta']:'';
		$iop_od_summary_val_arr['tx']=($iop_od_summary_val_arr['tx'])?$iop_od_summary_val_arr['tx']:'';
		$iop_od_summary_val_arr['tp']=($iop_od_summary_val_arr['tp'])?$iop_od_summary_val_arr['tp']:'';
		
		$iop_os_summary_val_arr['ta']=($iop_os_summary_val_arr['ta'])?$iop_os_summary_val_arr['ta']:'';
		$iop_os_summary_val_arr['tx']=($iop_os_summary_val_arr['tx'])?$iop_os_summary_val_arr['tx']:'';
		$iop_os_summary_val_arr['tp']=($iop_os_summary_val_arr['tp'])?$iop_os_summary_val_arr['tp']:'';
		
		if($log_iop_od_summary!="" || $log_iop_os_summary!=""){
			$log_iop_od_summary_col=$iop_od_summary_val_arr['ta'];
			$log_iop_os_summary_col=$iop_os_summary_val_arr['ta'];
			
			$log_tp_od_summary_col=$iop_od_summary_val_arr['tp'];
			$log_tp_os_summary_col=$iop_os_summary_val_arr['tp'];
			$log_tx_od_summary_col=$iop_od_summary_val_arr['tx'];
			$log_tx_os_summary_col=$iop_os_summary_val_arr['tx'];
		}else{
			$log_iop_od_summary_col = '';
		}
		$log_iop_od_summary_data=$log_iop_od_summary_col;
		$log_iop_os_summary_data=$log_iop_os_summary_col;
		
		$log_tp_od_summary_data=$log_tp_od_summary_col;
		$log_tp_os_summary_data=$log_tp_os_summary_col;
		
		$log_tx_od_summary_data=$log_tx_od_summary_col;
		$log_tx_os_summary_data=$log_tx_os_summary_col;
		
		$log_time_mil_arr=array();
		$log_iop_time_data="";
		if($log_ta_time!=""){
			if(strpos($log_ta_time,'AM')>0){
				$log_ta_time_mil=str_replace(':','',str_replace('AM',' ',$log_ta_time));
			}else{
				$log_ta_time=str_replace('PM',' PM',$log_ta_time);
				$log_ta_time_mil = date('Hi', strtotime($log_ta_time));
			}
			
			$log_time_mil_arr[]=$log_ta_time;
		}
		if($log_tp_time!=""){
			if(strpos($log_tp_time,'AM')>0){
				$log_tp_time_mil=str_replace(':','',str_replace('AM','',$log_tp_time));
			}else{
				$log_tp_time=str_replace('PM',' PM',$log_tp_time);
				$log_tp_time_mil = date('Hi', strtotime($log_tp_time));
			}
			
			$log_time_mil_arr[]=$log_tp_time;
		}
		if($log_tx_time!=""){
			if(strpos($log_tx_time,'AM')>0){
				$log_tx_time_mil=str_replace(':','',str_replace('AM','',$log_tx_time));
			}else{
				$log_tx_time=str_replace('PM',' PM',$log_tx_time);
				$log_tx_time_mil = date('Hi', strtotime($log_tx_time));
			}
			$log_time_mil_arr[]=$log_tx_time;
		}
		sort($log_time_mil_arr);
		
		$log_iop_time_data=str_replace('PM',' PM',str_replace('AM',' AM',$log_time_mil_arr[0]))."";
		
		$record['assessment']=str_replace('<div style="border-bottom:1px solid #CCE6FF; width:100%; height:4px;">&nbsp;</div>',"",$record['assessment']);
		$record['assessment']=str_replace("<div style='float:left; font-weight:bold'>","",$record['assessment']);
		$record['assessment']=str_replace(".&nbsp;</div><div style='padding-left:20px;'>"," ",$record['assessment']);
		$temp_Assessment=explode("</div>",$record['assessment']);
		$temp_Assess=array();
		
		foreach($temp_Assessment as $value){
			if($value!=""){
				$temp_Assess[]["key"]=$value;	
			}
		}
		$record['assessment']=$temp_Assess;

		$record['plan']=str_replace('<div style="border-bottom:1px solid #CCE6FF; width:100%; height:4px;">&nbsp;</div>',"",$record['plan']);
		$record['plan']=str_replace("<div style='float:left; font-weight:bold'>","",$record['plan']);
		$record['plan']=str_replace(".&nbsp;</div><div style='padding-left:20px;'>"," ",$record['plan']);
		
		$temp_Plans=explode("</div>",$record['plan']);
		$temp_Plan=array();
		
		foreach($temp_Plans as $value){
			if($value!=""){
				$temp_Plan[]["key"]=$value;	
			}
		}
		$record['plan']=$temp_Plan;
		$row_data["id"]=$record["id"];
		$row_data["Date"]=$record['dateReading'];//date
		
		$row_data["Visual Acuity Od"]=$log_va_od_summary_data;// od os reading
		$row_data["Visual Acuity OS"]=$log_va_os_summary_data;// os reading
		$row_data["IOP Time"]=$log_iop_time_data;
		$row_data["TaOD"]= $log_iop_od_summary_data;
		$row_data["TaOS"]= $log_iop_os_summary_data;
				
		$row_data["TxOD"]= $log_tx_od_summary_data;
		$row_data["TxOS"]= $log_tx_os_summary_data;
		
		$row_data["TpOD"]= $log_tp_od_summary_data;
		$row_data["TpOS"]= $log_tp_os_summary_data;
		
		
		$row_data["Ocular meds"]=$record['ocular_med'];				//ocular med
		
		$row_data['assessment'] = $record['assessment'];			//assessment
		$row_data['plan'] = $record['plan'];						//plan
		$row_data['gonio_od']=$record['gonio_od_summary'];			//gonio_od
		
		$row_data['gonio_os']=$record['gonio_os_summary'];			//gonio_os
		
		$row_data['disc_od_cd']=$record['fundus_od_cd_ratio'];			//fundus_od
		
		$row_data['disc_os_cd']=$record['fundus_os_cd_ratio'];	
		
		$row_data['disc_od']=$record['fundus_od_summary'];	
		
		$row_data['disc_os']=$record['fundus_os_summary'];	
		
		$row_data["Comment"]=$record['medication'];				//comments
		$row_data["Eye_med"]=$record['glucoma_med'];		// glucoma med
		
		$test_record=imw_query("SELECT * FROM `glaucoma_past_test` where `glaucoma_past_id`='".$record['id']."'");
		//echo "SELECT * FROM `glaucoma_past_test` where `glaucoma_past_id`='".$record['id']."'";
		$test_str="";
		while($test_data=imw_fetch_array($test_record)){
			 $test_str.= $test_data['test_type'].", ";
			
		}
		$row_data["test"]=$test_str;
		$arrReturn[]=$row_data;

	}
	if(count($rec_num)>0){	
		return $arrReturn;
		}
		
	}
	//-------end of get_GFS_new------------//
	
	public function insert_Gfs_Log(){
		
		$query="insert into glaucoma_past_readings 
					set taOd='".$_REQUEST['taOd']."',
						taOs='".$_REQUEST['taOs']."',
						tpOd='".$_REQUEST['tpOd']."',
						tpOs='".$_REQUEST['tpOs']."',
						txOd='".$_REQUEST['txOd']."',
						txOs='".$_REQUEST['txOs']."',
						medication='".$_REQUEST['comment']."',
						patientId='".$_REQUEST['patId']."',
						dateReading='".$_REQUEST['date']."',
						highTaOdDate='".$_REQUEST['date']."',
						highTaOsDate='".$_REQUEST['date']."',
						highTxOdDate='".$_REQUEST['date']."',
						ocular_med='".$_REQUEST['ocular_med']."',
						assessment='".$_REQUEST['assessment']."',
						plan='".$_REQUEST['plan']."',
						glucoma_med='".$_REQUEST['eye_med']."',
						gonio_od_summary='".$_REQUEST['gonio_od_summary']."',
						gonio_os_summary='".$_REQUEST['gonio_os_summary']."',
						va_od_summary='".$_REQUEST['va_od_summary']."',
						va_os_summary='".$_REQUEST['va_os_summary']."',
						fundus_od_cd_ratio='".$_REQUEST['disc_od_cd_ratio']."',
						fundus_os_cd_ratio='".$_REQUEST['disc_os_cd_ratio']."',
						fundus_od_summary='".$_REQUEST['disc_od_summary']."',
						fundus_os_summary='".$_REQUEST['disc_os_summary']."'
						
					";
		$result=imw_query($query);
		$id=imw_insert_id();
		$test_type=trim($_REQUEST['test_type']);
		if($test_type!="" && !empty($test_type)){
			$result_1=imw_query("insert into glaucoma_past_test set glaucoma_past_id='".$id."', test_type='".$test_type."'"); 
			return $result_1;
		}
		return $result;
	
	}
	public function update_Gfs_Log(){
		$_REQUEST['comment']=trim($_REQUEST['comment']);
		$qry="Update glaucoma_past_readings set medication='".$_REQUEST['comment']."' where id='".$_REQUEST['id']."'";
		$result=imw_query($qry);
		return $result;
	
	}
	public function get_procedure_app(){
		$result_data = array();
		$patient_id = $this->patient;
		$form_id = $_REQUEST['form_id'];
		// fetch dos date
		$query = imw_query("select DATE_FORMAT(cm.create_dt,'%m-%d-%Y %h:%i:%s') as 'create_dt'  from chart_master_table as cm where id = '".$form_id."'");
		$result_dos = imw_fetch_assoc($query);
		
		// end of dos
		$query = "Select procedure_id,procedure_name,ret_gl from operative_procedures where del_status!='1' and ret_gl=2 ";
		$record = imw_query($query);
		while($result = imw_fetch_assoc($record)){
			$result_1[] = $result;
			}
			foreach($result_1 as $value){
				$res[] = $value['procedure_id'];
			}
			$result_2 = implode(',',$res);
			$qry = "select cp.id, cp.patient_id, cp.proc_id, cp.site, cp.dx_code, cp.complication, cp.intravit_meds, cp.iop_type, 
			cp.iop_od, cp.iop_os, cp.providers, cp.cmt, pt.DOB, DATE_FORMAT(pt.DOB,'%m-%d-%Y') as 'age'
			 FROM chart_procedures as cp LEFT JOIN patient_data as pt
			 on  pt.id=cp.patient_id
			 where cp.proc_id IN('".$result_2."') AND cp.patient_id = '".$patient_id."' order by cp.id ASC";
			$chart_record = imw_query($qry);
			while($chart_result = imw_fetch_assoc($chart_record)){
				
				$birthdate = new DateTime($chart_result['DOB']);
        		$today   = new DateTime('today');
        		$age = $birthdate->diff($today)->y;
      			
				
				$med = str_replace('|~|',',',$chart_result['intravit_meds']);
				$result_data['id'] = $chart_result['id'];
				$result_data['patient_id'] = $chart_result['patient_id'];
				$result_data['DOS'] = $result_dos['create_dt'];
				$result_data['dob'] = $chart_result['age'];
				$result_data['age'] = $age;
				$result_data['med'] = $med;
				$result_data['site'] = $chart_result['site'];
				$result_data['dx'] = $chart_result['dx_code'];
				$result_data['cmt'] = $chart_result['cmt'];
				$result_data['iop_od'] = $chart_result['iop_type']." ".$chart_result['iop_od'];
				$result_data['iop_os'] = $chart_result['iop_type']." ".$chart_result['iop_os'];
				if($chart_result['complication'] == 0){
					$result_data['comp'] = 'N';
				}
				else{
					$result_data['comp'] = 'Y';
				}
				
				$qryOctTest="SELECT oct_id from oct where form_id='".$form_id."' AND patient_id='".$patient_id."'";
				$resOctTest=imw_query($qryOctTest);
				if(imw_num_rows($resOctTest)>0){
					$result_data['oct']="Y";	
				}
				else{
					$result_data['oct']="N";
				}	
				$query_get_name = imw_query("select procedure_name from operative_procedures where procedure_id = '".$chart_result['proc_id']."'");
				$query_res = imw_fetch_assoc($query_get_name);
				$result_data['procedure'] = $query_res['procedure_name'];
				 $result_data_1[] = $result_data;
				 $provider = $chart_result['providers'];
			}
			 	
			 $name = explode(' ',$provider);
			 foreach($name as $key => $value){
			 	$suffix_name .= $value[0];
			}
			 foreach($result_data_1 as $key => $value){
				$result_data_1[$key]['name'] = $suffix_name;
			}
			return $result_data_1;
		 	
	}
	public function update_GFS_app(){
//192.168.1.22/R6-Dev/app_services/?reqModule=chart_notes&service=GFS&action=update_GFS_app&patId=5557&diagnosis=&diag_od=&diag_os=&sta_od=&sta_os=&tmax_od_date=&tmax_od_value=&tmax_os_date=&tmax_os_value=&risk_factor=&warnings=&iop_od=&iop_os=&app=android	
		$diagnosis = (isset($_REQUEST['diagnosis']) && $_REQUEST['diagnosis'] !="")?$_REQUEST['diagnosis']:"";
		$diag_od = (isset($_REQUEST['diag_od']) && $_REQUEST['diag_od'] !="")?$_REQUEST['diag_od']:"";
		$diag_os = (isset($_REQUEST['diag_os']) && $_REQUEST['diag_os'] !="")?$_REQUEST['diag_os']:"";
		$sta_od = (isset($_REQUEST['sta_od']) && $_REQUEST['sta_od'] !="")?$_REQUEST['sta_od']:"";
		$sta_os = (isset($_REQUEST['sta_os']) && $_REQUEST['sta_os'] !="")?$_REQUEST['sta_os']:"";
		$tmax_od_date = (isset($_REQUEST['tmax_od_date']) && $_REQUEST['tmax_od_date'] !="")?$_REQUEST['tmax_od_date']:"";
		$tmax_od_value = (isset($_REQUEST['tmax_od_value']) && $_REQUEST['tmax_od_value'] !="")?$_REQUEST['tmax_od_value']:"";
		$tmax_os_date = (isset($_REQUEST['tmax_os_date']) && $_REQUEST['tmax_os_date'] !="")?$_REQUEST['tmax_os_date']:"";
		$tmax_os_value = (isset($_REQUEST['tmax_os_value']) && $_REQUEST['tmax_os_value'] !="")?$_REQUEST['tmax_os_value']:"";
		$risk_factor = (isset($_REQUEST['risk_factor']) && $_REQUEST['risk_factor'] !="")?$_REQUEST['risk_factor']:"";
		$warnings = (isset($_REQUEST['warnings']) && $_REQUEST['warnings'] !="")?$_REQUEST['warnings']:"";
		$iop_od = (isset($_REQUEST['iop_od']) && $_REQUEST['iop_od'] !="")?$_REQUEST['iop_od']:"";
		$iop_os = (isset($_REQUEST['iop_os']) && $_REQUEST['iop_os'] !="")?$_REQUEST['iop_os']:"";
		$query = "update glucoma_main set staging_code_od = '".$sta_od."',staging_code_os = '".$sta_os."'";
		if($diagnosis != ""){
			$query .= ", diagnosis_description = '".$diagnosis."'";
		}
		if($diag_od != ""){
			$query .= ", diagnosisOd = '".$diag_od."'";
		}
		if($diag_os != ""){
			$query .= ", diagnosisOs = '".$diag_os."'";
		}
		if($tmax_od_date != ""){
			$query .= ", elem_dateHighTmaxOd = '".$tmax_od_date."'";
		}
		if($tmax_od_value != ""){
			$query .= ", elem_highTmaxOdOd = '".$tmax_od_value."'";
		}
		if($tmax_os_date != ""){
			$query .= ", elem_dateHighTmaxOs = '".$tmax_os_date."'";
		}
		if($tmax_os_value != ""){
			$query .= ", elem_highTmaxOsOs = '".$tmax_os_value."'";
		}
		if($iop_od != ""){
			$query .= ", iopTrgtOd = '".$iop_od."'";
		}
		if($iop_os != ""){
			$query .= ", iopTrgtOs = '".$iop_os."'";
		}
		if($risk_factor != ""){
			$query .= ", riskFactors = '".$risk_factor."'";
		}
		else{
			$qry = imw_query("select race from patient_data where id = '".$this->patient."'");
			$race = imw_fetch_arrar($qry);
			$query .= ", riskFactors = '".$race['race']."'";
		}
		if($warnings != ""){
			$query .= ", warnings = '".$warnings."'";
		}
		$query .= "where patientId = '".$this->patient."' order by `glucomaId` DESC limit 1";
		$result = imw_query($query);
		$res = imw_affected_rows();
		if($res != 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function Activate_GFS_app(){
		
		
			$activate = $_REQUEST['activate'];
		
			// getting a race , diabetic and family_hx
			$qry = imw_query("select race from patient_data where id = '".$this->patient."'");
			$race = imw_fetch_assoc($qry);
		
			$arr = explode(',',$race['race']);
			foreach($arr as $value){
				if($value != ""){
					$race_value[] = 'Race-'.$value;
				}
			}
			$final_race = implode(',',$race_value);
			$diabetic = imw_query("select any_conditions_you from general_medicine where patient_id = '".$this->patient."'");
			$dia_res = imw_fetch_assoc($diabetic);
			$arr_dia = explode(',',$dia_res['any_conditions_you']);
			foreach($arr_dia as $value){
				if($value == 3){
					$str_dia = "Diabetes";
				}
			}
			if($final_race != "" && $str_dia == "Diabetes"){
				$final_arr = $final_race.','.$str_dia;
			}
			else if($final_race != "" && $str_dia != "Diabetes"){
					$final_arr = $final_race;
			}
			else if($final_race == "" && $str_dia == "Diabetes"){
					$final_arr = $str_dia;
			}
			$family_hx = imw_query("select any_conditions_relative from ocular where patient_id = '".$this->patient."'");
			$family_hx_arr = imw_fetch_assoc($family_hx);
		
			if($family_hx_arr['any_conditions_relative'] != ""){
				$risk_factor_arr = "Family History".','.$final_arr;
			}
			else{
				$risk_factor_arr = ','.$final_arr;
			}
		
			// end race , diabetic and family_hx
		
			//getting a last entry from main table
			$query = imw_query("select * from glucoma_main where patientId = '".$this->patient."' order by `glucomaId` DESC limit 1");
			$result = imw_fetch_assoc($query);
		
			// getting the iop values
			$query_iop = imw_query("select trgtOd, trgtOs from chart_iop where patient_id = '".$this->patient."' order by `iop_id` DESC limit 1");
			$result_iop = imw_fetch_assoc($query_iop);
			//end iop
		
			// After activate shown values
		 	$activation_date = date('d-m-Y');
		
			if($activate == 1 && $this->patient != 0 && $this->patient != ""){
				$qry = "Insert into glucoma_main set 
							dateActivation = '".$activation_date."',
							activate = '".$activate."',
							patientId = '".$result['patientId']."',
							dateHighTaOd = '".$result['dateHighTaOd']."',
							highTaOdOd = '".$result['highTaOdOd']."',
							highTaOdOs = '".$result['highTaOdOs']."',
							dateHighTaOs = '".$result['dateHighTaOs']."',
							highTaOsOd = '".$result['highTaOsOd']."',
							highTaOsOs = '".$result['highTaOsOs']."',
							dateHighTpOd = '".$result['dateHighTpOd']."',
							highTpOdOd = '".$result['highTpOdOd']."',
							highTpOdOs = '".$result['highTpOdOs']."',
							dateHighTpOs = '".$result['dateHighTpOs']."',
							highTpOsOd = '".$result['highTpOsOd']."',
							highTpOsOs = '".$result['highTpOsOs']."',
							dateVf = '".$result['dateVf']."',
							vfOdSummary = '".$result['vfOdSummary']."',
							vfOsSummary = '".$result['vfOsSummary']."',
							dateNfa = '".$result['dateNfa']."',
							nfaOdSummary = '".$result['nfaOdSummary']."',
							nfaOsSummary = '".$result['nfaOsSummary']."',
							dateGonio = '".$result['dateGonio']."',
							gonioOd = '".$result['gonioOd']."',
							gonioOs = '".$result['gonioOs']."',
							datePachy = '".$result['datePachy']."',
							pachyOdReads = '".$result['pachyOdReads']."',
							pachyOdAvg = '".$result['pachyOdAvg']."',
							pachyOdCorr = '".$result['pachyOdCorr']."',
							pachyOsReads = '".$result['pachyOsReads']."',
							pachyOsAvg = '".$result['pachyOsAvg']."',
							pachyOsCorr  = '".$result['pachyOsCorr']."',
							dateDiskPhoto = '".$result['dateDiskPhoto']."',
							diskPhotoOd = '".$result['diskPhotoOd']."',
							diskPhotoOs = '".$result['diskPhotoOs']."',
							dateCd = '".$result['dateCd']."',
							cdOd = '".$result['cdOd']."',
							cdOs = '".$result['cdOs']."',
							cdOdSummary = '".$result['cdOdSummary']."',
							cdOsSummary = '".$result['cdOsSummary']."',
							riskFactors = '".$risk_factor_arr."',
							warnings = ' ',
							cdAppOd = '".$result['cdAppOd']."',
							cdAppOs = '".$result['cdAppOs']."',
							notes = ' ',
							cee = '".$result['cee']."',
							ceeDate = '".$result['ceeDate']."',
							ceeNotes = '".$result['ceeNotes']."',
							iopTrgtOd = '".$result_iop['trgtOd']."',
							iopTrgtOs = '".$result_iop['trgtOs']."',
							dateHighTxOd = '".$result['dateHighTxOd']."',
							highTxOdOd = '".$result['highTxOdOd']."',
							highTxOdOs = '".$result['highTxOdOs']."',
							dateHighTxOs = '".$result['dateHighTxOs']."',
							highTxOsOd = '".$result['highTxOsOd']."',
							highTxOsOs = '".$result['highTxOsOs']."',
							imgcd_app_od = '".$result['imgcd_app_od']."',
							imgcd_app_os = '".$result['imgcd_app_os']."',
							staging_code_od = 'unspecified',
							staging_code_os = 'unspecified'
								";
							
					$result = imw_query($qry);
					$res = imw_affected_rows();
					if($res != 0 ){
						return true;
					}
				else{
						return false;
					}
			}
			else if($activate == 0 && $this->patient != 0 && $this->patient != ""){
				//update activate status 
				$query = imw_query("update glucoma_main set activate = 0 where patientId = '".$this->patient."' order by `glucomaId` DESC limit 1");
			
				// getting a past data
				$qry = imw_query("select * from glaucoma_past_readings where patientId = '".$this->patient."' order by id DESC limit 1");
				$past_result = imw_fetch_assoc($qry);
			
				// inserting a past data to generate new record
				$query = "Insert into glaucoma_past_readings Set
									dateReading = '".date('Y-m-d')."',
									timeReading = '".date('h:i A')."',
									taOd = '".$res['taOd']."',
									taOs = '".$res['taOs']."',
									tpOd = '".$res['tpOd']."',
									tpOs = '".$res['tpOs']."',
									vfOdSummary = '".$res['vfOdSummary']."',
									vfOsSummary = '".$res['vfOsSummary']."',
									nfaOdSummary = '".$res['nfaOdSummary']."',
									nfaOsSummary = '".$res['nfaOsSummary']."',
									cdOd = '".$res['cdOd']."',
									cdOs = '".$res['cdOs']."',
									gonioOdSummary = '".$res['gonioOdSummary']."',
									gonioOsSummary = '".$res['gonioOsSummary']."',
									medication = 'Activate Date:'.'".date('d-m-Y')."',
									patientId = '".$this->patient."',
									formId = '".$res['formId']."',
									pachyOdReads = '".$res['pachyOdReads']."',
									pachyOdAvg = '".$res['pachyOdAvg']."',
									pachyOdCorr = '".$res['pachyOdCorr']."',
									pachyOsReads = '".$res['pachyOsReads']."',
									pachyOsAvg = '".$res['pachyOsAvg']."',
									pachyOsCorr = '".$res['pachyOsCorr']."',
									diskPhotoOd = '".$res['diskPhotoOd']."',
									diskPhotoOs = '".$res['diskPhotoOs']."',
									vfOd = '".$res['vfOd']."',
									vfOs = '".$res['vfOs']."',
									scanOd = '".$res['scanOd']."',
									scanOs = '".$res['scanOs']."',
									diskFundus = '".$res['diskFundus']."',
									treatmentChange = '".$res['treatmentChange']."',
									diagnosisDate = '".$res['diagnosisDate']."',
									highTaOdDate = '".$res['highTaOdDate']."',
									highTaOsDate = '".$res['highTaOsDate']."',
									highTpOdDate = '".$res['highTpOdDate']."',
									highTpOsDate = '".$res['highTpOsDate']."',
									vfDate = '".$res['vfDate']."',
									nfaDate = '".$res['nfaDate']."',
									gonioDate = '".$res['gonioDate']."',
									pachyDate = '".$res['pachyDate']."',
									diskPhotoDate = '".$res['diskPhotoDate']."',
									cdDate = '".$res['cdDate']."',
									cee = '".$res['cee']."',
									ceeDate = '".$res['ceeDate']."',
									ceeNotes = '".$res['ceeNotes']."',
									rec_status = '".$res['rec_status']."',
									time_read_mil = '".date('h:i')."',
									txOd = '".$res['txOd']."',
									txOs = '".$res['txOs']."',
									highTxOdDate = '".$res['highTxOdDate']."',
									highTxOsDate = '".$res['highTxOsDate']."',
									va_od_summary = '".$res['va_od_summary']."',
									va_os_summary = '".$res['va_os_summary']."',
									va_od_summary_2 = '".$res['va_od_summary_2']."',
									va_os_summary_2 = '".$res['va_os_summary_2']."',
									va_od_summary_3 = '".$res['va_od_summary_3']."',
									va_os_summary_3 = '".$res['va_os_summary_3']."',
									glucoma_med = '".$res['glucoma_med']."',
									glucoma_med_allergies = '".$res['glucoma_med_allergies']."',
									gonio_od_summary = '".$res['gonio_od_summary']."',
									gonio_os_summary = '".$res['gonio_os_summary']."',
									sle_od_summary = '".$res['sle_od_summary']."',
									sle_os_summary = '".$res['sle_os_summary']."',
									fundus_od_cd_ratio = '".$res['fundus_od_cd_ratio']."',
									fundus_os_cd_ratio = '".$res['fundus_os_cd_ratio']."',
									fundus_od_summary = '".$res['fundus_od_summary']."',
									fundus_os_summary = '".$res['fundus_os_summary']."',
									assessment = '".$res['assessment']."',
									plan = '".$res['plan']."',
									show_data = '".$res['show_data']."',
									highlight_data = '".$res['highlight_data']."',
									ta_time = '".$res['ta_time']."',
									tp_time = '".$res['tp_time']."',
									tx_time = '".$res['tx_time']."',
									ttOd = '".$res['ttOd']."',
									ttOs = '".$res['ttOs']."',
									highTtOdDate = '".$res['highTtOdDate']."',
									highTtOsDate = '".$res['highTtOsDate']."',
									tt_time = '".$res['tt_time']."',
									ocular_med = '".$res['ocular_med']."'";
									
				$result = imw_query($query);
				$res = imw_affected_rows();
				if($res != 0){
					return true;
				}
				else{
					return false;
				}
		
			}
			else{
				return false;	
			}
		}
	
}

?>
