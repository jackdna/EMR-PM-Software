<?php
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
class consult_letters extends patient_app{	
	var $reqModule;
	var $arrProvider = array();
	var $patient_consult_id;
	public function __construct($patient){
		parent::__construct($patient);
		$this->patient_consult_id = $_REQUEST['temp_id'];
	}
	
	public function get_consult_letters(){
		$arrReturn = array();
		$arrReturn['consult_letters'][0] = array("level"=>0,"name"=>"Trash");
		$arrReturn['consult_letters'][1] = array("level"=>0,"name"=>"Fax"); 
		
		//------------GET TRASH DATA------------------------------------------
		$this->db_obj->qry = "SELECT DATE_FORMAT(pcl.date,'%m/%d/%y') AS date,
									 GROUP_CONCAT(pcl.patient_consult_id) as patient_consult_id
									FROM patient_consult_letter_tbl  pcl
									WHERE pcl.patient_id='".$this->patient."' 
										AND pcl.status = 1
									GROUP BY pcl.date
									ORDER BY pcl.date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		foreach($arrImage as $key=>$arr){
			$arrReturn['consult_letters'][0]['Objects'][$key] = array("level"=>1,"name"=>$arr['date']);
			{
				$this->db_obj->qry = "SELECT pcl.templateName , pcl.cur_date, pcl.patient_consult_id as id,
									 		users.fname, users.lname
									FROM patient_consult_letter_tbl  pcl
									JOIN users ON users.id = pcl.operator_id
									WHERE pcl.patient_consult_id  IN (".$arr['patient_consult_id'].")
									ORDER BY pcl.date DESC
									";
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
					$mod_date 	= date("g:i A",strtotime($tempArr['cur_date']));
					$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
					$str 		= '('.$mod_date.' '.$opr.')';
					$arrReturn['consult_letters'][0]['Objects'][$key]['Objects'][] = array("level"=>2,"name"=>$tempArr['templateName'].$str,"id"=>$tempArr['id']);
				}
			}
		}
		//--------------------------------------------------------------------------
		
		//--------------------GET FAX DATA------------------------------------------
		$this->db_obj->qry = "SELECT DATE_FORMAT(pcl.date,'%m/%d/%y') AS date,
									 GROUP_CONCAT(pcl.patient_consult_id) as patient_consult_id
									FROM patient_consult_letter_tbl  pcl
									WHERE pcl.patient_id='".$this->patient."' 
										AND pcl.fax_status = 1
									GROUP BY pcl.date
									ORDER BY pcl.date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		foreach($arrImage as $key=>$arr){
			
			$arrReturn['consult_letters'][1]['Objects'][$key] = array("level"=>1,"name"=>$arr['date']);
			{
				$this->db_obj->qry = "SELECT pcl.templateName , pcl.cur_date, pcl.patient_consult_id as id,
									 	users.fname, users.lname
									FROM patient_consult_letter_tbl  pcl
									JOIN users ON users.id = pcl.operator_id
									WHERE pcl.patient_consult_id  IN (".$arr['patient_consult_id'].")
									ORDER BY pcl.date DESC
									";
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
					$mod_date 	= date("g:i A",strtotime($tempArr['cur_date']));
					$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
					$str 		= '('.$mod_date.' '.$opr.')';
					$arrReturn['consult_letters'][1]['Objects'][$key]['Objects'][] = array("level"=>2,"name"=>$tempArr['templateName'].$str,"id"=>$tempArr['id']);
				}
			}
		}
		//--------------------------------------------------------------------------
		
		//-----GET ACTIVE CONSENT FORMS------------------------------------------------
		$this->db_obj->qry = "SELECT DATE_FORMAT(pcl.date,'%m/%d/%y') AS date,
									 GROUP_CONCAT(pcl.patient_consult_id) as patient_consult_id
									FROM patient_consult_letter_tbl  pcl
									WHERE pcl.patient_id='".$this->patient."' 
										AND pcl.status = 0
									GROUP BY pcl.date
									ORDER BY pcl.date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		foreach($arrImage as $key=>$arr){
			$count = count($arrReturn['consult_letters']);
			$arrReturn['consult_letters'][$count] = array("level"=>0,"name"=>$arr['date']);
			{
				$this->db_obj->qry = "SELECT pcl.templateName , pcl.cur_date, pcl.patient_consult_id as id,
									 	users.fname, users.lname
									FROM patient_consult_letter_tbl  pcl
									LEFT JOIN users ON users.id = pcl.operator_id
									WHERE pcl.patient_consult_id  IN (".$arr['patient_consult_id'].")
									ORDER BY pcl.date DESC
									";
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
					$mod_date 	= date("g:i A",strtotime($tempArr['cur_date']));
					$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
					$str 		= '('.$mod_date.' '.$opr.')';
					$arrReturn['consult_letters'][$count]['Objects'][] = array("level"=>1,"name"=>$tempArr['templateName'].$str,"id"=>$tempArr['id']);
				}
			}
		}
		//--------------------------------------------------------------------------
		$arrReturn['patient_data'] = $this->get_patient_data();
		return $arrReturn;
	}
	public function create_pdf(){
		$arrReturn = array();
		$qry = "SELECT pcl.templateData
									FROM patient_consult_letter_tbl  pcl
									WHERE pcl.patient_id='".$this->patient."' 
										AND pcl.patient_consult_id  = '".$this->patient_consult_id."'
									";
		$res_consult=imw_query($qry) or die(imw_error());
		//global $webServerRootDirectoryName,$myInternalIP,$myExternalIP,$web_RootDirectoryName,$phpHTTPProtocol;
		$tempArr=imw_fetch_assoc($res_consult);
		$consultTemplateData=$tempArr["templateData"];
		
		//If file exists, unlink it
		$filePath = pathinfo($this->pDir.'/tmp/app_pdf.pdf');
		
		//If no directory, make directory
		if(is_dir($filePath['dirname']) === false){
			mkdir($filePath['dirname'], 0777);
		}
		
		//If file exists, unlink it
		if(file_exists($filePath['dirname'].'/'.$filePath['basename'])) unlink($filePath['dirname'].'/'.$filePath['basename']);
		//@unlink($webServerRootDirectoryName.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/pdffile_app.pdf');
		
		$consultTemplateData = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consultTemplateData);
		$consultTemplateData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$consultTemplateData);
		
		$html2pdf = new HTML2PDF('P','A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->WriteHTML($consultTemplateData);
		
		$html2pdf->Output($filePath['dirname'].'/'.$filePath['basename'], 'F');
		
		$arrReturn["URL"] = str_replace($GLOBALS['fileroot'], $GLOBALS['php_server'], $filePath['dirname'].'/'.$filePath['basename']);
		return $arrReturn; 
	}
}

?>