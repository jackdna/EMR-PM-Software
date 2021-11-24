<?php
class CcHxPrint extends CcHx{
	private $pid, $fid;	
	
	public function __construct($pid, $fid){
		$this->fid = $fid;
		$this->pid = $pid; 
		parent::__construct($pid,$fid);
	}
	
	//add Ocular Meds --
	//*
	function getOrderedMeds($arrChk , $flgPrsMed=""){
		$form_id = $this->fid;
		$pid = $this->pid;
		
		//
		if(!isset($form_id) || empty($form_id)){ $optOrder="ALL"; }else{ $optOrder="VISIT"; }
		
		$ret="";
		$tmpArrChk_OcuMeds=array();
		$tmpArrChk_OcuMeds_2=array();
		$val_OcMd="";$flg_OcMd=0;
		$oChartAP =  new ChartAP($pid, $form_id);
		$arrOrder_1 = $oChartAP->getOrdersofAsmt("", $optOrder);
		$lenOrdrMeds = count($arrOrder_1["Meds"]);
		
		if( $lenOrdrMeds > 0 ){
			$medExist=true;
			
			//echo "<pre>";
			//print_r($arrOrder_1["Meds"]);
			//exit();
			
			//foreach($arrOrder_1["Meds"] as $key => $arVal){
				//$val_OcMd .=  '<table cellpadding="0" cellspacing="0" >';		
				for($i=0;$i<$lenOrdrMeds;$i++) {
					$tmp_ordr_nm = $arrOrder_1["Meds"][$i][0];
					$tmp_ordr_nm_pure = trim($arrOrder_1["Meds"][$i][4]);
					$tmpsite="";
					if(strpos($tmp_ordr_nm, "(OU)")!==false){$tmpsite="(OU)";}
					if(strpos($tmp_ordr_nm, "(OD)")!==false){$tmpsite="(OD)";}
					if(strpos($tmp_ordr_nm, "(OS)")!==false){$tmpsite="(OS)";}
					if(strpos($tmp_ordr_nm, "(PO)")!==false){$tmpsite="(PO)";}
					
					
					//echo "<br>".$tmp_ordr_nm_pure." ".$tmpsite;
					
					////check in med array
					if(in_array($tmp_ordr_nm_pure." ".$tmpsite, $arrChk)){ continue; }
					
					if(!empty($tmp_ordr_nm)){
						if(!in_array($tmp_ordr_nm, $tmpArrChk_OcuMeds)){	
							$tmp_ordr_nm = str_replace(array("OS","OD","OU","PO"),array("Left Eye","Right Eye","Both Eyes","Oral"),$tmp_ordr_nm);
							if(!empty($flgPrsMed)){//show in pres medication
								$ret .= '<tr class="txt_10"><td height="18">&nbsp;<a href="#" class="txt_10">'.$tmp_ordr_nm.'</a></td><td></td><td></td></tr>';
							}else{
								$ret .= '<tr><td>'.$tmp_ordr_nm.'</td></tr>';
							}						
							$tmpArrChk_OcuMeds_2[]=$tmp_ordr_nm_pure." ".$tmpsite;					
							$tmpArrChk_OcuMeds[$tmp_ordr_nm_pure." ".$tmpsite]=$tmp_ordr_nm;
						}
					}
				}
				//$val_OcMd .='</table>';
			//}
			
		}
		
		return $ret;
	}//
	
	function cpoe_getOrderForPrint($final_flag=0){
		//* -- Ocu Meds -- */
		$pid = $this->pid;
		$form_id = $this->fid;
		$val = "";
		$oMedHx = new MedHx($this->pid);
		$oMedHx->setFormId($this->fid);
		$arr_tmp_chk_meds = array();
		$ocMedArr = $arrC = array();
		if(!isset($_REQUEST['ocu']) || empty($_REQUEST['ocu'])){
		$medExist=false;
		if($final_flag=='0') {
			$ocMedArr= $oMedHx->getOcularMedication($_REQUEST['medication']);
			if(count($ocMedArr[0])>0) {
				$medExist=true;
				
				$val .=  '<table cellpadding="0" cellspacing="0" >';
				$val .= '<!--<tr><td style="text-align:left"><u><strong>Patient ID# '.$pid.' - Ocular Medication </strong></u></td></tr>-->';
				
				array_multisort($ocMedArr[0],SORT_ASC,$ocMedArr[1],SORT_ASC,$ocMedArr[2],SORT_ASC,$ocMedArr[3],
					SORT_ASC,$ocMedArr[4],SORT_ASC,$ocMedArr[5],SORT_ASC,$ocMedArr[6],SORT_ASC,$ocMedArr[7],SORT_ASC);
					
					for($i=0;$i<count($ocMedArr);$i++) { 
						if(empty($ocMedArr[0][$i])){ continue; }
						$val .= '<tr>
								<td style="text-align:left">';
						
						$arr_tmp_chk_meds = array();

						for($j=0;$j<count($ocMedArr[$i]);$j++) {	

							$ocular_medi	= trim($ocMedArr[0][$i]);
							$ocular_sig		= trim($ocMedArr[1][$i]);
							$ocular_comments= trim($ocMedArr[4][$i]);
							$ocular_sites= trim($ocMedArr[5][$i]);
							$ocular_dosage= trim($ocMedArr[7][$i]);

							$ocular_sites_str='';
							if($ocular_sites=='1') 		{$ocular_sites_str_1 = '(OS)';$ocular_sites_str = 'Left Eye'; 
							}else if($ocular_sites=='2'){$ocular_sites_str_1 = '(OD)';$ocular_sites_str = 'Right Eye'; 
							}else if($ocular_sites=='3'){$ocular_sites_str_1 = '(OU)';$ocular_sites_str = 'Both Eyes';
							}else if($ocular_sites=='4'){$ocular_sites_str_1 = '(PO)';$ocular_sites_str = 'Oral';}
							
							///chkarray
							$arr_tmp_chk_meds[] = $ocular_medi." ".$ocular_sites_str_1;
							
							$ocMed = $ocular_medi;				
							
							if($ocular_dosage) 		{$ocMed .=' '.$ocular_dosage;  	}
							if($ocular_sites_str) 	{$ocMed .=' '.$ocular_sites_str;  	}
							if($ocular_sig) 		{$ocMed .=' '.$ocular_sig;  		}
							if($ocular_comments) 	{$ocMed .='; '.$ocular_comments;  	}

						}
								$val .= $ocMed.'
								</td>
							</tr>';
					}
					
					$val .=$this->getOrderedMeds($arr_tmp_chk_meds); $flg_OcMd=1;						
					$val .='</table>';
		
			}
			
			
		}else {
			$qryMed = "SELECT ocularMeds FROM chart_left_provider_issue WHERE patient_id = '".$pid."' AND form_id = '".$form_id."' ";
			$resMed = sqlStatement($qryMed);
			if(imw_num_rows($resMed)>0) {
				$rowMed=sqlFetchArray($resMed);
				$om_c = stripslashes($rowMed["ocularMeds"]);
				$sepOctMeds = "<+OMeds&%+>";
				$arrC = (!empty($om_c)) ? explode($sepOctMeds,$om_c) : array();
				if(count($arrC)>0) {
					$medExist=true;

					$val .=  '<table cellpadding="0" cellspacing="0" >';
					$val .= '	<!--<tr><td style="text-align:left"><u><strong>Patient ID# '.$pid.' - Ocular Medication </strong></u></td></tr>-->';
					sort($arrC);
					for($i=0;$i<count($arrC);$i++) {  
						if(!empty($arrC[$i])){
							
							//get ocumedw/eye
							$ocuMedP=$oMedHx->getOcuMedP($arrC[$i]);
							if(!empty($ocuMedP)){ $arr_tmp_chk_meds[] = $ocuMedP; }
							
							$val .= '<tr><td>'.str_replace(array("OS","OD","OU","PO"),array("Left Eye","Right Eye","Both Eyes","Oral"),$arrC[$i]).'</td></tr>';
						}			
					}
					$val .=$this->getOrderedMeds($arr_tmp_chk_meds);$flg_OcMd=1;
					$val .='</table>';
				}
			}
			
			//--
			//Check in Gen Hx Table
			if($medExist==false){
				//Get Values from archive database 
				list($arrC,$flg)=$oMedHx->getArcOcuMedHx($_REQUEST['medication']);
				
				if(count($arrC)>0) {
					$medExist=true;

					$val .=  '<table cellpadding="0" cellspacing="0" >';
					$val .= '	<!--<tr><td style="text-align:left"><u><strong>Patient ID# '.$pid.' - Ocular Medication </strong></u></td></tr>-->';
					sort($arrC);
					for($i=0;$i<count($arrC);$i++) {           
						if(!empty($arrC[$i])){
							//get ocumedw/eye
							$ocuMedP=$oMedHx->getOcuMedP($arrC[$i]);
							if(!empty($ocuMedP)){ $arr_tmp_chk_meds[] = $ocuMedP; }
						
							$val .= '<tr><td>'.str_replace(array("OS","OD","OU","PO"),array("Left Eye","Right Eye","Both Eyes","Oral"),$arrC[$i]).'</td></tr>';
						}
					}
					$val .=$this->getOrderedMeds($arr_tmp_chk_meds);$flg_OcMd=1;
					$val .='</table>';
				}
			}				
		
		}
		}
		
		// if sttill not prited
		if($flg_OcMd==0){
			$tmp =$this->getOrderedMeds($arr_tmp_chk_meds);				
			if(!empty($tmp)){
			$medExist=true;
			$val .=  '<table cellpadding="0" cellspacing="0">';
			$val .= $tmp;
			$val .='</table>';				
			}
		}
		
		return $val;
		
	}
	
	function chart_plan_print($finalize_flag=0){
		$final_flag = $_REQUEST['final_flag'];
		$content = $this->cpoe_getOrderForPrint($finalize_flag); //
		
		$oPrinter = new Printer($this->pid, $this->fid);
		$oPrinter->print_page($content, "Ocular Medication","print_meds","","","");
	}
	
}
?>