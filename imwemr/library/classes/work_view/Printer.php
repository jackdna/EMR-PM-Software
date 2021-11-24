<?php
class Printer{
	public $pid, $fid;
	public function __construct($pid,$fid=""){
		$this->pid = $pid; 
		$this->fid = $fid;
	}
	
	public function print_page($content, $page_title, $file_nm="", $flgDos="",$flg_ret_resp="",$flg_ret_html_add=""){
		
		$pid = $this->pid;
		if(empty($file_nm)){$file_nm = "pdffile";}
		$val="";
		
		//--
		if(!empty($this->fid)){
			$oCN = new ChartNote($this->pid, $this->fid);
			$date_of_service = $oCN->getDos();
		}
		
		//---get Detail For Patient -------
		$oPt = new Patient($this->pid);
		$patientName = $oPt->getName();
		$age =  $oPt->getAge();
		$arPtInfo = $oPt->getPtInfo();		
		$created_date = $arPtInfo["date"];
		$date_of_birth = $arPtInfo["DOB"];
		$cityAddress = $arPtInfo["city"];
		if(!empty($arPtInfo["state"])){ $cityAddress .= ", ".$arPtInfo["state"]; }
		$cityAddress .= " ".$arPtInfo["postal_code"];
		$primary_care_phy_name=$arPtInfo['primary_care_phy_name'];
		$reffPhyId = $arPtInfo['primary_care_id'];
		$default_facility = $arPtInfo['default_facility'];
		$created_by = $arPtInfo['created_by'];
		
		$patient_heading = $arPtInfo['title'].' '.$patientName."-".$arPtInfo['id'];
		$about_patient = $arPtInfo['sex'].'&nbsp;('.$age.')'.'&nbsp;'.$date_of_birth;
		$patient_address = core_address_format(' ', ' ', $arPtInfo['city'], $arPtInfo['state'], $arPtInfo['postal_code']);
		$patientName_display = $patientName.'-'.$arPtInfo['id'];
		$street = $arPtInfo['street'];
		$street2 = $arPtInfo['street2'];
		$phone_home = core_phone_format($arPtInfo['phone_home']);
		$phyId = $arPtInfo['providerID'];		
		
		//--- Get Physician Details --------		
		if(!empty($phyId)){
			$phyId = $oPt->getSchDoc();
		}
		$oUsr = new User($phyId);
		$phyName = $oUsr->getName();
		
		//--- Get Reffering Physician Details --------
		
		//--- Get Default Facility Details -------
		$oFclty = new Facility();
		$groupDetails = $oFclty->getFacilityInfo();
		
		//--Operator --
		$ou = new User();
		$opertator_name = $ou->getName(1);
		
		//--
		$printDt = wv_formatDate(wv_dt("now"),"",1);		

		//---
		//--
		// get hdr
		ob_start();
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);
		include($GLOBALS['incdir']."/chart_notes/view/chart_print.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		$val = $out2; 
		
		//add content
		$val .= $content;
		$val .= "</page>";
		$fp = "/tmp/".$file_nm.".html";
		//
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$resp = $oSaveFile->cr_file($fp,$val);
		if(!empty($flg_ret_resp)){
			//return $resp;
			return (!empty($flg_ret_html_add)) ? $resp : $file_nm;
		}else{
			//echo $resp;
			echo (!empty($flg_ret_html_add)) ? $resp : $file_nm;
		}
	}	
}
?>