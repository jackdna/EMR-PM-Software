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
 Purpose: MySQLi Extension Functions
 Access Type: Indirect Access.
*/

namespace IMW;

/**
 * DB
 *
 * Main DB connection Class
 */
class Prescription
{
	public function __construct($db_obj = '',$cur_sec = 1){
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		if(empty($cur_sec) == false){
			$this->current_sec = $cur_sec;
		}
	}
	public function get_patient_default_messages(){
		$arr_return=array();
		$qryPatientAcess="SELECT * from facility where facility_type = 1";
		$resultSetPatientAcess=($this->dbh_obj->imw_query($qryPatientAcess));
		$resPatientAcess=($this->dbh_obj->imw_fetch_assoc($resultSetPatientAcess));
		list($eve,$glrx)=explode("~||~",$resPatientAcess["iportal_eve"]);
		$resPatientAcess['iportal_prescription']=$glrx;
		if($this->dbh_obj->imw_num_rows($resPatientAcess)>0){
			$arr_return=$resPatientAcess;
		}
		return $arr_return;
	}
	public function glassPrint($patientId){
		
		$patient_default_messages = $this->get_patient_default_messages();
		if($patient_default_messages['iportal_prescription']==1){
			if(trim($patient_default_messages['iportal_prescription_desc'])==""){
				$patient_default_messages['iportal_prescription_desc']="Please contact the practice for a copy of your eyeglass or contact lens prescription";
			}
			return $patient_default_messages['iportal_prescription_desc'];			
		}
		
		$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='1'";	
		
		$res = $this->dbh_obj->imw_query($qryGetTempData);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$rsGetTempData = $this->dbh_obj->imw_fetch_assoc($res);
		}
		
		$numRowGetTempData = $this->dbh_obj->imw_num_rows($res);
		if($numRowGetTempData<=0){
			$var=false;
		}else if($numRowGetTempData>0){
			$resArrayTemplate = ($rsGetTempData);
			$printOptionType = $resArrayTemplate["printOption"];
			$prescriptionTemplateContentData = stripslashes($resArrayTemplate["prescriptionTemplateContentData"]);
			if(strpos($prescriptionTemplateContentData,'{TEXTBOX_XSMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_SMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_MEDIUM}')>0){
				$getInputForTextBoxes=true;
			}
		
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$showHtmlPage = stripos($prescriptionTemplateContentData,"[".$val."]");
					if($showHtmlPage !== false){//smarttag found
						$getInputForTextBoxes = true;
						break;
					}
				}
			}
		}
		 //End Function 
			$qryMr="SELECT GROUP_CONCAT(c3.mr_none_given) AS vis_mr_none_given
					FROM chart_vis_master c2					
					INNER JOIN chart_pc_mr c3 ON c2.id = c3.id_chart_vis_master
					WHERE c2.patient_id = '".$patientId."'  
					AND c3.mr_none_given != '' AND c3.ex_type = 'MR'					
					GROUP BY c2.form_id
					ORDER BY c2.form_id DESC LIMIT 0,1";
			$resMr=$this->dbh_obj->imw_query($qryMr);
			if($this->dbh_obj->imw_num_rows($resMr)>0){
				$resMr = $this->dbh_obj->imw_fetch_assoc($resMr);
				$rowMr=($resMr);
				$mrArray=explode(",",$rowMr['vis_mr_none_given']);
			}
			
		if(count($mrArray)>0){
			
			$qryFormId="SELECT id  FROM `chart_master_table` WHERE `patient_id` = '".$patientId."' order by id desc LIMIT 1";
			$rowMr=$this->dbh_obj->imw_query($qryFormId);
			$resFormId = $this->dbh_obj->imw_fetch_assoc($rowMr);
			
			$rowFormId=($resFormId);
			$form_id=$rowFormId['id'];
			$printType=1;
			if(in_array("MR 1",$mrArray)){
				
				$tmp = $this->getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 1",$prescriptionTemplateContentData);
				if(!empty($tmp)){
					$getFinalHTMLForGivenMR="<page>".$tmp."</page>";
				}
			}
			if(in_array("MR 2",$mrArray)){
				$tmp = $this->getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 2",$prescriptionTemplateContentData);
				if(!empty($tmp)){
					$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";	
				}
			}
			if(in_array("MR 3",$mrArray)){
				$tmp = $this->getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 3",$prescriptionTemplateContentData);
				if(!empty($tmp)){
					$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";
				}
			}
		}else{
			$var=false;
		}
		if($getInputForTextBoxes==false){
			$signatureReplace=$GLOBALS["include_root"].'/common/new_html2pdf/tmp/';
			return  $getFinalHTMLForGivenMR=str_ireplace('../../interface/common/new_html2pdf/',$GLOBALS['idoc_external_ip'].'interface/common/new_html2pdf/',$getFinalHTMLForGivenMR);		
		}else{
			return $getFinalHTMLForGivenMR=$getFinalHTMLForGivenMR;
		}
	}

	//get MR values when Given was actually Given--
	public function chkMRGivenActual($patientId, $sql, $mr, $sel){
		if($mr == "MR 3"){
			$mr_ind="3";
			$stts_chk="elem_providerNameOther_3=1";	
		}else if($mr == "MR 2"){
			$mr_ind="2";
			$stts_chk="elem_providerNameOther=1";
		}else if($mr == "MR 1"){//MR 1
			$mr_ind="1";
			$stts_chk="elem_providerName=1";	
		}else if(!empty($mr) && preg_match("/MR \d+/",$mr)){
			$mr_ind="";
			$mr_ind=str_replace("MR","",$mr); 
			$mr_ind = trim($mr_ind);
			$stts_chk="elem_providerNameOther_".$mr_ind."=1";		
		}
		
		//
		$flg_chk=0;
		$stts_chk2="elem_mrNoneGiven".$mr_ind."=1";
		$qryGetSpacialCharValue = $sql;
		$resultSetChar = ($this->dbh_obj->imw_query($qryGetSpacialCharValue));
		$row = $this->dbh_obj->imw_fetch_assoc($resultSetChar);   
		if($this->dbh_obj->imw_num_rows($row)>0){
			//check given 
			if(strpos($row["vis_statusElements"], $stts_chk2)===false){
				//get given values when given was actually given			
				$givendt="";
				if(!empty($row["vis_mr_pres_dt"])){  
					$givendt=$row["vis_mr_pres_dt"]; 				
					$qryGetSpacialCharValue = "
						SELECT 
						".$sel."		
						FROM chart_vis_master c4 
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
							
						WHERE c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$mr_ind."' 
						AND c1.mr_pres_date='".$givendt."'
						AND c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%'
						AND c4.status_elements like  '%".$stts_chk."%'
						AND c1.delete_by='0'  
						Order By c4.id;
					";
					$resMr = $this->dbh_obj->imw_query($qryGetSpacialCharValue);
					$row = $this->dbh_obj->imw_fetch_assoc($resMr);   
					if($this->dbh_obj->imw_num_rows($row)>0){	
						$flg_chk=1;
					}
				}			
			}else{
				$flg_chk=1;
			}
		}
		
		//
		if($flg_chk==0){
			$qryGetSpacialCharValue = "
						SELECT 
						".$sel."		
						FROM chart_vis_master c4 
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'					 	
						WHERE c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$mr_ind."' 
						AND c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%' AND c4.status_elements like  '%".$stts_chk."%'
						AND c1.delete_by='0'  
						Order By mr_pres_date DESC, c4.id DESC; 
						";
		}	
		
		return $qryGetSpacialCharValue;
	}
	
	function get_mr_dos($patientId, $form_id){
		$qryGetDOS="select cmt.date_of_service as dos,cmt.id as form_id,cmt.patient_id as patient_id from chart_vis_master as cv 
			 LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
			 LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id
		 where cv.status_elements!='' and cv.patient_id='".$patientId."' and cv.form_id='".$form_id."' and cpm.ex_type='MR' AND cpm.delete_by='0' AND
		(cv.status_elements like  '%elem_visMrOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
		|| cv.status_elements like  '%elem_visMrOdS=1,%'
		|| cv.status_elements like  '%elem_visMrOdC=1,%'
		|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
		|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
		|| cv.status_elements like  '%elem_visMrOdP=1,%'
		|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
		|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
		|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
		|| cv.status_elements like  '%elem_providerName=1,%'
		
		|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
		|| cv.status_elements like  '%elem_providerNameOther=1,%'
		|| cv.status_elements like  '%elem_providerIdOther=1,%'
		
		|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
		)
		";
		//$qry1=mysql_query("select vis_statusElements,exam_date from chart_vision where vis_mr_none_given!='' and patient_id='".$patientId."' and form_id='".$form_id."'");
		$qry1=$this->dbh_obj->imw_query($qryGetDOS);
		$co=$this->dbh_obj->imw_num_rows($qry1);
		if(($co > 0)){
			$crow=$this->dbh_obj->imw_fetch_assoc($qry1);
			//$date_of_service = date("m-d-Y", strtotime($crow["dos"]));	
			$date_of_service = wv_formatDate($crow["dos"]);
			$form_id_cv=$crow["form_id"];
			$patient_id_cv=$crow["patient_id"];
			
		}else{
		
			$qryGetDOS="select cmt.date_of_service as dos,cmt.id as form_id,cmt.patient_id as patient_id from chart_vis_master as cv 
			 LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
			  LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id 
		 where cv.status_elements!='' and cv.patient_id='".$patientId."' and 
		(cv.status_elements like  '%elem_visMrOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
		|| cv.status_elements like  '%elem_visMrOdS=1,%'
		|| cv.status_elements like  '%elem_visMrOdC=1,%'
		|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
		|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
		|| cv.status_elements like  '%elem_visMrOdP=1,%'
		|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
		|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
		|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
		|| cv.status_elements like  '%elem_providerName=1,%'
		
		|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
		|| cv.status_elements like  '%elem_providerNameOther=1,%'
		|| cv.status_elements like  '%elem_providerIdOther=1,%'
		
		|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
		|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
		) 
		ORDER BY  cmt.date_of_service DESC, cmt.id DESC limit 1
		";	
			/*
			$qryGetDOS="select form_id,patient_id from chart_vision 
				 where vis_statusElements!='' and patient_id='".$patientId."'
				  and 
				(vis_statusElements like  '%elem_visMrOdA=1,%'
				|| vis_statusElements like  '%elem_visMrOdA=1,%'
				|| vis_statusElements like  '%elem_visMrOdAdd=1,%'
				|| vis_statusElements like  '%elem_visMrOdS=1,%'
				|| vis_statusElements like  '%elem_visMrOdC=1,%'
				|| vis_statusElements like  '%elem_visMrOdTxt1=1,%'
				|| vis_statusElements like  '%elem_visMrOdTxt2=1,%'
				|| vis_statusElements like  '%elem_visMrOdP=1,%'
				|| vis_statusElements like  '%elem_visMrOdSel1=1,%'
				|| vis_statusElements like  '%elem_visMrOdSlash=1,%'
				|| vis_statusElements like  '%elem_visMrOdPrism=1,%'
				|| vis_statusElements like  '%elem_providerName=1,%'
				
				|| vis_statusElements like  '%elem_visMrOtherOdA=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdAdd=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdTxt2=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdTxt1=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdS=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdC=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdP=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdSel1=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdSlash=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdPrism=1,%'
				|| vis_statusElements like  '%elem_providerNameOther=1,%'
				|| vis_statusElements like  '%elem_providerIdOther=1,%'
				
				|| vis_statusElements like  '%elem_visMrOtherOdS_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdC_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdAdd_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdTxt1_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdA_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdP_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOdPrism_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOsAdd_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOsS_3=1,%'
				|| vis_statusElements like  '%elem_visMrOtherOsPrism_3=1,%'
				) order by form_id DESC limit 1
				";
			*/	
			$qryGetPrevious=$this->dbh_obj->imw_query($qryGetDOS);
			$resGetPrivious=$this->dbh_obj->imw_num_rows($qryGetPrevious);
			if($resGetPrivious>0){
				$rowExamDate=$this->dbh_obj->imw_fetch_assoc($qryGetPrevious);
				$form_id_cv	   = $rowExamDate["form_id"];
				$patient_id_cv = $rowExamDate["patient_id"];
				
			}
			
		}	
		return array($date_of_service,$form_id_cv,$patient_id_cv);
	}

	//-
	public function getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue,$prescriptionTemplateContentData){
		/////get patient data////
		$qryGetpatientDetail = "select *,date_format(DOB,'%m-%d-%Y') as pat_dob,date_format(date,'%m-%d-%Y') as reg_date,
								DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge
								from patient_data where id = '$patientId'";
		$resultPT	= ($this->dbh_obj->imw_query($qryGetpatientDetail));
		$rsGetpatientDetail = $this->dbh_obj->imw_fetch_assoc($resultPT);
		
		$numRowGetpatientDetail	= $this->dbh_obj->imw_num_rows($resultPT);
		if($numRowGetpatientDetail>0){
			
			extract($rsGetpatientDetail);
			$patientname = $fname.' '.$lname; 
			//$patientAddressFull = $street.' '.$street2.','.$city.','.$state.','.$postal_code;
			if($street){
				$patientAddressFull = $street;
			}
			//$patientAddressFull = $street.' '.$street2.','.$city.','.$state.','.$postal_code;
			if($street2){
				//$patientAddressFull .= ' '.$street2;
				$patientAddressFull .= ' '.$street2.',';
			}
			if($city){		
				if(!$street2){
					$patientAddressFull .= ',';
				}
				$patientAddressFull .= ' '.$city.', '.$state.' '.$postal_code;
				
				$patientGeoData = $city.', '.$state.' '.$postal_code;
			}
			$ptAgeShow = "";
			if($ptAge != ""){
				$ptAgeShow = $ptAge."&nbsp;Yr.";
			}
		}
		
		list($date_of_service, $form_id_cv,$patient_id_cv) = $this->get_mr_dos($patientId, $form_id);
		  
			if($form_id_cv && $patient_id_cv){
				$qryGetDos="select date_of_service from chart_master_table where patient_id='".$patient_id_cv."' and id='".$form_id_cv."'";
				$resultSetGetDos = ($this->dbh_obj->imw_query($qryGetDos));
				$resGetDos = $this->dbh_obj->imw_fetch_assoc($resultSetGetDos);
				if($this->dbh_obj->imw_num_rows($resultSetGetDos)>0){
					$rowGetDos=($resGetDos);
					$date_of_service = date("m-d-Y", strtotime($rowGetDos["date_of_service"]));
				}
			}
			
		/////End date of sevice Code////////////////
		//get today date//
			$today = date('m-d-Y');
		//end today date//
		
		if($form_id_cv){$form_id=$form_id_cv;}
		$qryGetDos="select date_of_service from chart_master_table where patient_id='".$patientId."' and id='".$form_id."'";
		$resultSetGetDos=($this->dbh_obj->imw_query($qryGetDos));
		$resGetDos = $this->dbh_obj->imw_fetch_assoc($resultSetGetDos);
			if($this->dbh_obj->imw_num_rows($resultSetGetDos)>0){ 
				$rowGetDos=($resGetDos);
				$date_of_service = date("m-d-Y", strtotime($rowGetDos["date_of_service"]));
			}
			
		if(!empty($givenMrValue) && preg_match("/MR \d+/",$givenMrValue)){
			$ex_number = str_replace("MR","",$givenMrValue);
			$ex_number = trim($ex_number);
			$sel = "
				c1.provider_id, c1.ex_desc as notes, c1.mr_pres_date as vis_mr_pres_dt, c1.form_id as  vis_form_id,
				c2.sph as OdSpherical, c2.cyl as odCylinder, c2.axs as odAxis, c2.ad as odAdd, c2.prsm_p as odPrism1, c2.prism as odBase2, c2.slash as odBase1, c2.sel_1 as odPrism2,				
				c3.sph as osSpherical, c3.cyl as osCylinder, c3.axs as osAxis, c3.ad as osAdd, c3.prsm_p as osPrism1, c3.prism as osBase2, c3.slash as osBase1, c3.sel_1 as osPrism2,  
				c4.status_elements as vis_statusElements
			";
			
			$qryGetSpacialCharValue = "
				SELECT 
				".$sel."		
				FROM chart_vis_master c4
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'		 	
				WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$ex_number."' AND c1.delete_by='0'  
				Order By ex_number;
			";
			
			$indx1=$indx2="";
			if($ex_number>1){
				$indx1="Other";
				if($ex_number>2){
					$indx2="_".$ex_number;
				}
			}
			$stts_chk="elem_providerName".$indx1.$indx2."=1";
			
			//get MR values when Given was actually Given--
			$qryGetSpacialCharValue = $this->chkMRGivenActual($qryGetSpacialCharValue, $givenMrValue, $sel);

		}
				
			if($qryGetSpacialCharValue!=""){
					$rsGetValue = ($this->dbh_obj->imw_query($qryGetSpacialCharValue));
					$rsGetSpacialCharValue = $this->dbh_obj->imw_fetch_assoc($rsGetValue);
					$numRowGetSpacialCharValue = $this->dbh_obj->imw_num_rows($rsGetValue);		
					
					$flgLF=0;
					//if No Record
					if($numRowGetSpacialCharValue<=0 || $numRowGetSpacialCharValue==''){
						$flgLF=1;
					}
					
						if($numRowGetSpacialCharValue){
							
							if($flgLF==0){
								$rowTmp = ($rsGetSpacialCharValue);
							}else{
								$rowTmp = $rsGetSpacialCharValue;
							}
							
							extract($rowTmp);
							
								$odPrism ="";
								$osBase ="";
								if($odPrism1){
									$odPrism = $odPrism1;
								}
								// Commented Because it is code according to R6 //
								/*if($odPrism2 && $odPrism1){
									$prismimage="../../common/new_html2pdf/pic_vision_pc.jpg";
									$odPrism .= "<img src='".$prismimage."'/>". $odPrism2;
								}*/
								if($odBase1){
									$odBase = $odBase1;
								}
								if($odBase2 && $odBase1){
									$odBase .= ' '. $odBase2;
								}
	
								if($osPrism1){
									$osPrism = $osPrism1;
								}
								// Commented Because it is code according to R6 //
								/*if($osPrism2 && $osPrism1){
									$prismimage="../../common/new_html2pdf/pic_vision_pc.jpg";
									$osPrism .= "<img src='".$prismimage."'/>". $osPrism2;
								}*/							
								if($osBase1){
									$osBase = $osBase1;
								}
								if($osBase2 && $osBase1){
									$osBase .= ' '. $osBase2;
								}
	
								if($odAxis){
									$odAxis .= "&deg;";
								}
								
								if($osAxis){
									$osAxis .= "&deg;";
								}
	
							$vis_mr_pres_dt_show = (!empty($vis_mr_pres_dt) && $vis_mr_pres_dt!="0000-00-00") ? date("m-d-Y", strtotime($vis_mr_pres_dt)):$date_of_service ; 
							if(!empty($vis_mr_pres_dt) && $vis_mr_pres_dt!="0000-00-00"){
								$vis_mr_pres_dt_show =  date("m-d-Y", strtotime($vis_mr_pres_dt)); 					
							}else if(!empty($vis_form_id)){
								$qry="SELECT date_of_service FROM chart_master_table id='".$vis_form_id."'";
								$resChartMaster=($this->dbh_obj->imw_query($qry));
								$res=($this->dbh_obj->imw_fetch_assoc($resChartMaster));
								$vis_mr_pres_dt_show = $res['date_of_service'];
							}else{
								$vis_mr_pres_dt_show =  $date_of_service ;
							}
				}
			}
		
		if($prescriptionTemplateContentData){
			$print_pdf=true;
			$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
			
			$prescriptionTemplateContentData = str_ireplace('%20',' ',$prescriptionTemplateContentData);
			/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
				}	
			}
			/*--SMART TAG REPLACEMENT END--*/
			//$prescriptionTemplateContentData=str_ireplace("/".$GLOBALS['iDoc_dir']."/","../../../".$GLOBALS['iDoc_dir']."/",stripslashes($prescriptionTemplateContentData));	
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT GEOGRAPHICAL DATA}',$patientGeoData,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$OdSpherical,$prescriptionTemplateContentData);
			
			if($odCylinder!=""){
				$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$odCylinder,$prescriptionTemplateContentData);
			}else{	
				
				$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
			}
			
			$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',$odAxis,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD PRISM}',$odPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD HORIZONTAL PRISM}',$odPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$odAdd,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$osSpherical,$prescriptionTemplateContentData);
			
			if($osCylinder!=""){
				$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
			}else{	
				$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
			}
	
			$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',$osAxis,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS PRISM}',$osPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS HORIZONTAL PRISM}',$osPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$osAdd,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
				
			//Modified Variables
			$prescriptionTemplateContentData = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD VERTICAL PRISM}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS VERTICAL PRISM}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DOS}',$vis_mr_pres_dt_show,$prescriptionTemplateContentData);
			
			//New variable added	
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PatientID}',$patientId,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{STATE, ZIP CODE}',$patientGeoData,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);	
			
			$raceShow						 = trim($race);
			$otherRace						 = trim($otherRace);
			if($otherRace) { 
				$raceShow					 = $otherRace;
			}
			$languageShow					 = str_ireplace("Other -- ","",$language);
			$ethnicityShow					 = trim($ethnicity);			
			$otherEthnicity					 = trim($otherEthnicity);
			if($otherEthnicity) { 
				$ethnicityShow				 = $otherEthnicity;
			}
			// To Replace Image Path //
			$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
			$prescriptionTemplateContentData = str_ireplace('src="','src="'.$protocol.$_SERVER['SERVER_NAME'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace("src='","src='".$protocol.$_SERVER['SERVER_NAME'],$prescriptionTemplateContentData);
			// End Of Code To Replace Image Path //
			$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
			
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','',$prescriptionTemplateContentData);
	
			$expirationDate = date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
			$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);
			
			
			$apptFacInfo = $this->getApptInfo($patientId,'','','');
			$apptFacname = $apptFacInfo[2];
			if(!empty($apptFacInfo[10])){
				$apptFacstreet = $apptFacInfo[10].', ';	
			}
			if(!empty($apptFacInfo[11])){
				$apptFaccity = $apptFacInfo[11].', ';	
			}
			$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('&nbsp;',' ',$prescriptionTemplateContentData);
		////////////////////////Statrt Signature Logic/////////	
		$signaTure=false;
		$phy_licence='';
		
		//Give Prioity To Master Chart Notes Provider//
		
		//====get physician who save the MR1,MR2,MR3 value===// 
			if($form_id_cv && $patient_id_cv){
				$form_id=$form_id_cv;
				$patientId=$patient_id_cv;
			}
		//====================================================//
			$qryGetProvider = "SELECT id,providerId,finalize,finalizerId FROM chart_master_table WHERE  id ='".$form_id."' and patient_id ='".$patientId."'";
			$resultSetMaster = ($this->dbh_obj->imw_query($qryGetProvider));
			$rsGetProviderId = $this->dbh_obj->imw_fetch_assoc($resultSetMaster);
			$numRowProviderGetSig = $this->dbh_obj->imw_num_rows($resultSetMaster);
			if($numRowProviderGetSig && $signaTure==false){
				extract($rsGetProviderId);
				if($providerId>0){
					if($providerId){
						if($finalize=='1'){
							$providerId = $finalizerId;
						}
						$resultgetNameQry = ($this->dbh_obj->imw_query("SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,fname,mname,lname,pro_suffix,licence,sign_path FROM users WHERE id = '".$providerId."'"));	
						$getNameQry = $this->dbh_obj->imw_fetch_assoc($resultSetMaster);
						$getNameRow = ($getNameQry);
						$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
						$phy_fname = $getNameRow['fname'];
						$phy_mname = $getNameRow['mname'];
						$phy_lname = $getNameRow['lname'];
						$phy_suffix = $getNameRow['pro_suffix'];
						$phy_licence = $getNameRow['licence'];
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
						$sign_path=$getNameRow['sign_path'];
						if($sign_path){
							$path_img="<img src='".data_path().$sign_path."'>";
							$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$path_img,$prescriptionTemplateContentData);
						}
					}else{
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
					}
					
					$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"_________",$prescriptionTemplateContentData);	
					$signaTure=true;
				}	
			}
			
			$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$phy_licence,$prescriptionTemplateContentData);
		//End Give Prioity To Master Chart Notes Provider
			if($signaTure==false){
				$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"_________",$prescriptionTemplateContentData);		
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
			}
		/////////////////////////End Signature Logic/////////
		}// End Template HTml Blank Check
			
		return $prescriptionTemplateContentData;
	}

	public function getApptInfo($patient_id,$providerIds=0,$report_start_date,$report_end_date){
		$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = "";
			$schDataQryRes=array();		
			if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}
			
			if($report_start_date || $report_end_date){
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
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
				$resultSetSchRes = ($this->dbh_obj->imw_query($schDataQry));
				while($record = ($this->dbh_obj->imw_fetch_assoc($resultSetSchRes))){
					$schDataQryRes[] = $record;
				}
				
			}
			
			if($this->dbh_obj->imw_num_rows($resultSetSchRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
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
				$resultSetSchRes = ($this->dbh_obj->imw_query($schDataQry));
				while($record = ($this->dbh_obj->imw_fetch_assoc($resultSetSchRes))){
					$schDataQryRes[] = $record;
				}
			}		
			if($this->dbh_obj->imw_num_rows($resultSetSchRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
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
				$resultSetSchRes = ($this->dbh_obj->imw_query($schDataQry));
				while($record = ($this->dbh_obj->imw_fetch_assoc($resultSetSchRes))){
					$schDataQryRes[] = $record;
				}
			}		
			if($this->dbh_obj->imw_num_rows($resultSetSchRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
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
				$resultSetSchRes = ($this->dbh_obj->imw_query($schDataQry));
				while($record = ($this->dbh_obj->imw_fetch_assoc($resultSetSchRes))){
					$schDataQryRes[] = $record;
				}
			}		
			if($this->dbh_obj->imw_num_rows($resultSetSchRes)<=0) {
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
	
	public function getPatientForm($patientId){
		$arr_return=array();
		$qryPatientAcess = "SELECT id,providerId FROM chart_master_table  WHERE patient_id ='".$patientId."' ORDER BY id DESC LIMIT 1";
		$resultSetPatientAcess=($this->dbh_obj->imw_query($qryPatientAcess));
		$resPatientAcess=($this->dbh_obj->imw_fetch_assoc($resultSetPatientAcess));
		return $resPatientAcess['id'];
	}
	
	// SET LENSE CODE FUNCTION
	public function getLensManufacturer(){
		$arrLensManuf= array();
		$lensListQry= "SELECT clmk.*, cpttbl.cpt_fee_id, cpttbl.cpt4_code, cpttbl.cpt_prac_code, cpttbl.cpt_prac_code FROM contactlensemake clmk 
	LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id order by clmk.make_id";
		$resListRes = ($this->dbh_obj->imw_query($lensListQry));
		while($row = ($this->dbh_obj->imw_fetch_assoc($resListRes))){
			$lensListRes[] = $row;
		}
		$lensListNumRow = $this->dbh_obj->imw_num_rows($resListRes);
		if($lensListNumRow>0) {
			$i=0;
			foreach($lensListRes as $lensListRow ) {
					$lensManuf='';
					$arrLensManuf[$lensListRow['make_id']]['cpt_fee_id'] = $lensListRow['cpt_fee_id'];
					$arrLensManuf[$lensListRow['make_id']]['cpt4Code'] = $lensListRow['cpt4_code'];
					$arrLensManuf[$lensListRow['make_id']]['cpt_prac_code'] = $lensListRow['cpt_prac_code'];
					if($lensListRow['type']==''){
						$lensManuf = $lensListRow['style'];
					}else{
						$lensManuf = $lensListRow['style'].'-'.$lensListRow['type'];
					}
					$arrLensManuf[$lensListRow['make_id']]['det'] = $lensManuf;
			}
		}
		
		return $arrLensManuf;
	}
	
	public function printContactLens($patientId){
		
		$patient_default_messages = $this->get_patient_default_messages();
		if($patient_default_messages['iportal_prescription']==1){
			if(trim($patient_default_messages['iportal_prescription_desc'])==""){
				$patient_default_messages['iportal_prescription_desc']="Please contact the practice for a copy of your eyeglass or contact lens prescription";
			}
			return $patient_default_messages['iportal_prescription_desc'];
		}
	
		$printMethod = 1;
		//GET ALL LENS MANUFACTURER IN ARRAY
		$arrLensManuf = $this->getLensManufacturer();

		$clTemplate=0;
		$form_id = $this->getPatientForm($patientId);
		/// get Patient  Data	
		
		$qryGetpatientDetail = "select *, date_format(DOB,'%m-%d-%Y') as pat_dob,date_format(date,'%m-%d-%Y') as reg_date, 
								DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge
								from patient_data where id = '".$patientId."'";
		$resultSetGetpatientDetail	= ($this->dbh_obj->imw_query($qryGetpatientDetail));
		$rsGetpatientDetail	= ($this->dbh_obj->imw_fetch_assoc($resultSetGetpatientDetail));
		
		$numRowGetpatientDetail	= $this->dbh_obj->imw_num_rows($resultSetGetpatientDetail);
		if($numRowGetpatientDetail){
			extract($rsGetpatientDetail);
			$patientname = $fname.' '.$lname; 
			if($street){
				$patientAddressFull = $street;
			}
			if($street2){
				$patientAddressFull .= ' '.$street2.',';
			}
			if($city){
				if(!$street2){
					$patientAddressFull .= ',';
				}
				$patientAddressFull .= ''.$city.', '.$state.' '.$postal_code;
			}
			$ptAgeShow = "";
			if($ptAge != ""){
				$ptAgeShow = $ptAge."&nbsp;Yr.";
			}
			
			
		}
		/// End get Patient Data
			
		
		///get Work sheet Data
		 
		$GetDataQuery= "SELECT DATE_FORMAT(dos, '%m-%d-%Y') AS worksheetdate,  contactlensmaster.* FROM contactlensmaster 
		where patient_id='".$patientId."' ORDER BY clws_id DESC limit 1";
		$resultSetGetDataRes = ($this->dbh_obj->imw_query($GetDataQuery));
		$GetDataRes = ($this->dbh_obj->imw_fetch_assoc($resultSetGetDataRes));
		$GetDataNumRow = $this->dbh_obj->imw_num_rows($resultSetGetDataRes);
		if($GetDataNumRow>0){
			$resRow=($GetDataRes);
			extract($resRow);
		}
		
		
		/// End get Work sheet Data	
		
		//============== Get DOS from chart_master_table by form id==========================//
		$qryGetDOS="SELECT cl.clws_id,DATE_FORMAT(cmt.date_of_service, '%m-%d-%Y') as date_of_service from chart_master_table as cmt INNER JOIN contactlensmaster as cl on(cmt.id=cl.form_id) where cl.patient_id='".$patientId."' order by cl.clws_id DESC limit 1";
		$resultSetGetDOS=($this->dbh_obj->imw_query($qryGetDOS));
		$resGetDOS=($this->dbh_obj->imw_fetch_assoc($resultSetGetDOS));
		if($this->dbh_obj->imw_num_rows($resultSetGetDOS)>0){
			$rowDos=($resGetDOS);
			$date_of_service=$rowDos['date_of_service'];
			$clws_id=$rowDos['clws_id'];
		}
		//==================================================================================//
		$today = date('m-d-Y');
		
		$GetDataQuery="SELECT cleval.CLSLCEvaluationCommentsOD, cleval.CLSLCEvaluationCommentsOS, cleval.CLRGPEvaluationCommentsOD, cleval.CLRGPEvaluationCommentsOS, cldet.*  
		FROM contactlensmaster clMaster 
		LEFT JOIN contactlensworksheet_det cldet ON cldet.clws_id =  clMaster.clws_id 
		LEFT JOIN contactlens_evaluations cleval ON cleval.clws_id = clMaster.clws_id 
		where clMaster.clws_id='".trim($clws_id)."' ORDER BY cldet.id";
		$resultSetDetail=($this->dbh_obj->imw_query($GetDataQuery));
		
		while($row=($this->dbh_obj->imw_fetch_assoc($resultSetDetail))){
			//$resDetail[] = $row;
			$resDet[]=$row;
			$arrCLTypes[$row['clType']]=$row['clType'];
		}
		
		// TYPES OF CL ORDERED
		
		
		
		// GET SCL TEMPLATE
		if(in_array('scl', $arrCLTypes)){
			$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type=2";
			$resultSetGetTempData = ($this->dbh_obj->imw_query($qryGetTempData));
			$rsGetTempData = ($this->dbh_obj->imw_fetch_assoc($resultSetGetTempData));
			$numRowGetTempData = $this->dbh_obj->imw_num_rows($resultSetGetTempData);
			if($numRowGetTempData>0){
				extract($rsGetTempData);	
				$prescriptionTemplateContentData = '<page>'.stripslashes($prescriptionTemplateContentData);
				//$printOptionType = $printOption;
				$prescriptionTemplateContentData.='</page>';
				$clTemplate=1;
			}
		}
		// GET RGP TEMPLATE
		if(in_array('rgp', $arrCLTypes) || in_array('cust_rgp', $arrCLTypes)){
			$qryGetTempDataRGP = "Select prescription_template_content as prescriptionTemplateContentDataRGP,printOption from prescription_template where prescription_template_type=4";
			$resultSetGetTempDataRGP = ($this->dbh_obj->imw_query($qryGetTempDataRGP));
			$rsGetTempDataRGP = ($this->dbh_obj->imw_fetch_assoc($resultSetGetTempDataRGP));
			if($this->dbh_obj->imw_num_rows($resultSetGetTempDataRGP)>0){
				$prescriptionTemplateContentDataRGP='';
				
				extract($rsGetTempDataRGP);	
				$tempVar = $prescriptionTemplateContentDataRGP;
				
				if(in_array('rgp', $arrCLTypes)){
					$prescriptionTemplateContentDataRGP= '<page>'.stripslashes($prescriptionTemplateContentDataRGP);
					//$printOptionType = $printOption;
					$prescriptionTemplateContentDataRGP.='</page>';
				}
				if(in_array('cust_rgp', $arrCLTypes)){
					$prescriptionTemplateContentDataCRGP= '<page>'.stripslashes($tempVar);
					//$printOptionType = $printOption;
					$prescriptionTemplateContentDataCRGP.='</page>';
					
				}
				$clTemplate=1;
			}
		}
		if($clTemplate==1){
			
			$raceShow						 = trim($race);
			$otherRace						 = trim($otherRace);
			if($otherRace) { 
				$raceShow					 = $otherRace;
			}
			$languageShow					 = str_ireplace("Other -- ","",$language);
			$ethnicityShow					 = trim($ethnicity);			
			$otherEthnicity					 = trim($otherEthnicity);
			if($otherEthnicity) { 
				$ethnicityShow				 = $otherEthnicity;
			}
			
			if(in_array('scl', $arrCLTypes)){
				
				$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
				//Modified Variable
				$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentData);
				//New Variable
				$prescriptionTemplateContentData = str_ireplace('{PatientID}',$patientId,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{STATE, ZIP CODE}',$state.", ".$postal_code,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);
			
				$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
				$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
				$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
				
				$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
				
			}
		
			if(in_array('rgp', $arrCLTypes)){
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentDataRGP);
				//Modified Variable
				$prescriptionTemplateContentDataRGP = str_ireplace('{DATE}',$today,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentDataRGP);
				//New Variable
				$prescriptionTemplateContentDataRGP = str_ireplace('{PatientID}',$patientId,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{STATE, ZIP CODE}',$state.", ".$postal_code,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentDataRGP);
			
				$prescriptionTemplateContentDataRGP = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentDataRGP);	
				$prescriptionTemplateContentDataRGP = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentDataRGP);	
				$prescriptionTemplateContentDataRGP = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentDataRGP);	
				
				$prescriptionTemplateContentDataRGP = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentDataRGP);
				
			}
		
			if(in_array('cust_rgp', $arrCLTypes)){
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentDataCRGP);
				//Modified Variable
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DATE}',$today,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentDataCRGP);
				//New Variable
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PatientID}',$patientId,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{STATE, ZIP CODE}',$state.", ".$postal_code,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentDataCRGP);
			
				$prescriptionTemplateContentDataCRGP = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentDataCRGP);	
				$prescriptionTemplateContentDataCRGP = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentDataCRGP);	
				$prescriptionTemplateContentDataCRGP = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentDataCRGP);	
				
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentDataCRGP);
			}
		
		
		 if($resDet[0]['CLSLCEvaluationCommentsOD']!=''){ $notes=$resDet[0]['CLSLCEvaluationCommentsOD']."<br>";}
		 if($resDet[0]['CLSLCEvaluationCommentsOS']!=''){ $notes.=$resDet[0]['CLSLCEvaluationCommentsOS']."<br>";}
		 if($resDet[0]['CLRGPEvaluationCommentsOD']!=''){ $notesRGP=$resDet[0]['CLRGPEvaluationCommentsOD']."<br>";}
		 if($resDet[0]['CLRGPEvaluationCommentsOS']!=''){ $notesRGP.=$resDet[0]['CLRGPEvaluationCommentsOS']."<br>";}
		
		 $resSize = sizeof($resDet);
		 
		 for($i=0; $i<$resSize;$i++)
		 {
			if($printMethod==1){
			
				if($resDet[$i]['clType']=='scl'){
					if($resDet[$i]['clEye']=="OD" || $resDet[$i]['clEye']=="OU"){
						
						$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$resDet[$i]['SclBcurveOD'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$resDet[$i]['SclsphereOD'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$resDet[$i]['SclCylinderOD'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',$resDet[$i]['SclaxisOD']."&deg;",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$resDet[$i]['SclAddOD'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OD DIAMETER}',$resDet[$i]['SclDiameterOD'],$prescriptionTemplateContentData);
						$BRANDOD.=$arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['det'];
					}
					if($resDet[$i]['clEye']=="OS" || $resDet[$i]['clEye']=="OU"){	
						$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$resDet[$i]['SclBcurveOS'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$resDet[$i]['SclsphereOS'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$resDet[$i]['SclCylinderOS'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',$resDet[$i]['SclaxisOS']."&deg;",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$resDet[$i]['SclAddOS'],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{OS DIAMETER}',$resDet[$i]['SclDiameterOS'],$prescriptionTemplateContentData);
						$BRANDOS.=$arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det'];
					}
					$BRAND.=$arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det'];
				}
		
					
				if($resDet[$i]['clType']=='rgp'){
					if($resDet[$i]['clEye']=="OD" ||$resDet[$i]['clEye']=="OU"){		
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD BASE CURVE}',$resDet[$i]['RgpBCOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD SPHERICAL}',$resDet[$i]['RgpPowerOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD CYLINDER}',$resDet[$i]['RgpOZOD']."/".$resDet[$i]['RgpCTOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD AXIS}',$resDet[$i]['RgpLatitudeOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD ADD}',$resDet[$i]['RgpAddOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD DIAMETER}',$resDet[$i]['RgpDiameterOD'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OD COLOR}',$resDet[$i]['RgpColorOD'],$prescriptionTemplateContentDataRGP);
						
						$BRANDODRGP.=$arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det'];
					}
					if($resDet[$i]['clEye']=="OS" || $resDet[$i]['clEye']=="OU"){		
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS BASE CURVE}',$resDet[$i]['RgpBCOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS SPHERICAL}',$resDet[$i]['RgpPowerOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS CYLINDER}',$resDet[$i]['RgpOZOS']."/".$resDet[$i]['RgpCTOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS AXIS}',$resDet[$i]['RgpLatitudeOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS ADD}',$resDet[$i]['RgpAddOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS DIAMETER}',$resDet[$i]['RgpDiameterOS'],$prescriptionTemplateContentDataRGP);
						$prescriptionTemplateContentDataRGP = str_ireplace('{OS COLOR}',$resDet[$i]['RgpColorOS'],$prescriptionTemplateContentDataRGP);
						
						$BRANDOSRGP.=$arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
					}
					$BRANDRGP.=$arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];	
				}	
				if($resDet[$i]['clType']=='cust_rgp'){
					if($resDet[$i]['clEye']=="OD" ||$resDet[$i]['clEye']=="OU"){		
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD BASE CURVE}',$resDet[$i]['RgpCustomBCOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD SPHERICAL}',$resDet[$i]['RgpCustomPowerOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD CYLINDER}',$resDet[$i]['RgpCustomOZOD']."/".$resDet[$i]['RgpCustomCTOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD AXIS}',$resDet[$i]['RgpCustomLatitudeOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD ADD}',$resDet[$i]['RgpCustomAddOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD DIAMETER}',$resDet[$i]['RgpCustomDiameterOD'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OD COLOR}',$resDet[$i]['RgpCustomColorOD'],$prescriptionTemplateContentDataCRGP);
			
						$BRANDODCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det'];
					}
					if($resDet[$i]['clEye']=="OS" || $resDet[$i]['clEye']=="OU"){		
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS BASE CURVE}',$resDet[$i]['RgpCustomBCOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS SPHERICAL}',$resDet[$i]['RgpCustomPowerOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS CYLINDER}',$resDet[$i]['RgpCustomOZOS']."/".$resDet[$i]['RgpCustomCTOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS AXIS}',$resDet[$i]['RgpCustomLatitudeOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS ADD}',$resDet[$i]['RgpCustomAddOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS DIAMETER}',$resDet[$i]['RgpCustomDiameterOS'],$prescriptionTemplateContentDataCRGP);
						$prescriptionTemplateContentDataCRGP = str_ireplace('{OS COLOR}',$resDet[$i]['RgpCustomColorOS'],$prescriptionTemplateContentDataCRGP);
		
						$BRANDOSCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['det'];
					}
					$BRANDCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['det'];
				}					
				 
			 }
			 if($BRANDOD){$BRANDOD.="<br>";}
			 if($BRANDOS){$BRANDOS.="<br>";}
			 if($BRAND){$BRAND.="<br>";}
			 
			 $BRANDODRGP.="<br>";
			 $BRANDOSRGP.="<br>";
			 $BRANDRGP.="<br>";
		
			 $BRANDODCRGP.="<br>";
			 $BRANDOSCRGP.="<br>";
			 $BRANDCRGP.="<br>";
		 }
		
			if(in_array('scl', $arrCLTypes)){
				$BRAND=strip_tags($BRAND,"<br>");
				$BRANDOD=str_replace("<br>","",$BRANDOD);
				$BRANDOS=str_replace("<br>","",$BRANDOS);
				
				$prescriptionTemplateContentData = str_ireplace('{BRAND}',$BRAND,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{BRAND OD}',$BRANDOD,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{BRAND OS}',$BRANDOS,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
			}
			if(in_array('rgp', $arrCLTypes)){
				$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND}',$BRANDRGP,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND OD}',$BRANDODRGP,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND OS}',$BRANDOSRGP,$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{NOTES}',$notesRGP,$prescriptionTemplateContentDataRGP);
				
				$prescriptionTemplateContentData.=$prescriptionTemplateContentDataRGP;
			}
		
			if(in_array('cust_rgp', $arrCLTypes)){
				$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND}',$BRANDCRGP,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND OD}',$BRANDODCRGP,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND OS}',$BRANDOSCRGP,$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{NOTES}',$notesCRGP,$prescriptionTemplateContentDataCRGP);
				
				$prescriptionTemplateContentData.=$prescriptionTemplateContentDataCRGP;
			}
				
			list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
			$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt+12,$dos_dy,$dos_yr));
			
			$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);
		
			$findTextBox=false;
			
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','',$prescriptionTemplateContentData); 
		
			
		if($form_id!=""){
				////////////////////////Start Signature Logic/////////	
			$signaTure=false;
			$phy_licence='';
			$qryGetSig = "SELECT doctorId,sign_coords,id,sign_path FROM chart_assessment_plans WHERE form_id ='".$form_id."' and patient_id = $patientId ";	
				$resultSetGetSig = ($this->dbh_obj->imw_query($qryGetSig));
				$rsGetSig = ($this->dbh_obj->imw_fetch_assoc($resultSetGetSig));
				$numRowGetSig = $this->dbh_obj->imw_num_rows($resultSetGetSig);
				if($numRowGetSig>0){
					extract(($rsGetSig));	
					if($doctorId>0){
						//print Of Physcian Title First name Second name and Suffix//
						if($doctorId){
							$qryName = "SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,fname,mname,lname,pro_suffix,licence,sign_path FROM users WHERE id = '".$doctorId."'";
							$resultSetgetNameQry = ($this->dbh_obj->imw_query($qryName));
							$getNameQry = ($this->dbh_obj->imw_fetch_assoc($resultSetgetNameQry));
							$getNameRow = ($getNameQry);
							$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
							$phy_fname = $getNameRow['fname'];
							$phy_mname = $getNameRow['mname'];
							$phy_lname = $getNameRow['lname'];
							$phy_suffix = $getNameRow['pro_suffix'];
							$phy_licence = $getNameRow['licence'];
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
							$sign_path=$getNameRow['sign_path'];
							if($sign_path){
								$path_img="<img src='".data_path().$sign_path."'>";
								$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$path_img,$prescriptionTemplateContentData);
							}
						}else{
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
						}
					}
				}
			//Give Prioity To Master Chart Notes Provider//
			
				$qryGetProvider = "SELECT id,providerId FROM chart_master_table  WHERE  id ='".$form_id."' and patient_id ='".$patientId."'";	
				$resultSetGetProviderId = ($this->dbh_obj->imw_query($qryGetProvider));
				$rsGetProviderId = ($this->dbh_obj->imw_fetch_assoc($resultSetGetProviderId));
				$numRowProviderGetSig = $this->dbh_obj->imw_num_rows($resultSetGetProviderId);
				if($numRowProviderGetSig > 0 && $signaTure==false){
					extract($rsGetProviderId);
					if($providerId>0){
						//print Of Physcian Title First name Second name and Suffix//
						if($providerId){
							$qryProviderName = "SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,fname,mname,lname,pro_suffix,licence FROM users WHERE id = '".$providerId."'";
							$resultSetgetNameQry = ($this->dbh_obj->imw_query($qryProviderName));
							$getNameQry = ($this->dbh_obj->imw_fetch_assoc($resultSetgetNameQry));	
							$getNameRow = ($getNameQry);
							$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
							$phy_fname = $getNameRow['fname'];
							$phy_mname = $getNameRow['mname'];
							$phy_lname = $getNameRow['lname'];
							$phy_suffix = $getNameRow['pro_suffix'];
							$phy_licence = $getNameRow['licence'];
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
						}else{
							$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
						}
						$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"_________",$prescriptionTemplateContentData);	
						$signaTure=true;
					}
				}
				$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$phy_licence,$prescriptionTemplateContentData);
			
				if($signaTure==false){
					$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"",$prescriptionTemplateContentData);		
					$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
				}
		}
		
		/*--searching SMART TAGS (IF FOUND)--*/
		$showHtmlPage = false;
		
		//	In Case of Contact Lens Selected both SCL AND RGP in Interface 
		
			$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD DIAMETER}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD ADD}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD COLOR}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{BRAND}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{BRAND OD}',"",$prescriptionTemplateContentData);
		
			$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS DIAMETER}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS ADD}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS COLOR}',"",$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{BRAND OS}',"",$prescriptionTemplateContentData);
			
		//====================================================================//
			
		//end of else of $showHtmlPage
		
		}
		////////on Submit Print The Data//
				
		// To Replace Image Path //
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$prescriptionTemplateContentData = str_ireplace('src="','src="'.$protocol.$_SERVER['SERVER_NAME'],$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace("src='","src='".$protocol.$_SERVER['SERVER_NAME'],$prescriptionTemplateContentData);
		// End Of Code To Replace Image Path //

		return $prescriptionTemplateContentData;
	}
	///End On Submit Print The Data//
}