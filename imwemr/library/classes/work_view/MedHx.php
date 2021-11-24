<?php
//MedHx.php
class MedHx{
	private $pid, $fid;
	public function __construct($pid){
		$this->pid = $pid;
	}
	
	function setFormId($fid){
		$this->fid = $fid;
	}

	//
	function getOcularMedication($medication = '',$type=''){
		$ocular_medi=$ocular_dos=$ocular_sig=$ocular_type=$ocular_titleSite=array();
		//Ocular Medication
		 $check_data="select title, destination, type, sig, DATE_FORMAT(date,'%Y-%m-%d') as 'eDate', med_comments, sites, 
					compliant from lists where pid='".$this->pid."'";
		
		//if(!empty($medication) && $medication == "ocular")	
		if(!empty($type)){ 
			$type_more = " OR type = '".$type."' ";
		}
		
		$check_data .= "and (type='4' ".$type_more.")";
		
		/*$check_data .= "AND allergy_status != 'Deleted' AND allergy_status != 'Discontinue' AND allergy_status != 'Stop' 
						ORDER BY begdate DESC
						";*/
		$check_data .= "AND allergy_status In( 'Active', 'Order' )
						ORDER BY begdate DESC
						";				
		$checkSql = sqlStatement($check_data);
		for($i=1; $checkl = sqlFetchArray($checkSql); $i++){
			
			//$tmp = "";
			//$tmp = $checkl['title'];
			//$tmp .= (!empty($checkl['destination'])) ? $checkl['destination'] : "";
			$ocular_medi[] = stripslashes($checkl['title']);
			$ocular_sig[] = stripslashes($checkl['sig']);
			$ocular_type[] = stripslashes($checkl['type']);
			$ocular_date[] = stripslashes($checkl['eDate']);
			$ocular_comments[] = stripslashes($checkl['med_comments']);
			$ocular_sites[] = stripslashes($checkl['sites']);
			$ocular_compliant[] = stripslashes($checkl['compliant']);
			$ocular_dosage[] = stripslashes($checkl['destination']);
			$ocular_titleSite[] = trim(stripslashes($checkl['title'])." ".stripslashes($checkl['sites']));

		}
		//$ocular_medi=substr($ocular_medi1,0,strlen($ocular_medi1)-2);

		return array($ocular_medi,$ocular_sig,$ocular_type,$ocular_date,$ocular_comments,$ocular_sites, $ocular_compliant, $ocular_dosage,$ocular_titleSite);
	}
	
	//Get Archived  Ocular Med.
	function getArcOcuMedHx($medication = ''){
		$flg=0;
		$arrC=array();
		$arrC_v2=array();
		
		$sql = "SELECT lists FROM chart_genhealth_archive WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$arrC_tmp = array();	
			$arrLists = unserialize($row["lists"]);		
			
			$medQryRes = $arrLists[4];
			$len=is_array($medQryRes) ? count($medQryRes) : 0 ;
			$arrFields=array();
			for($m=0;$m<$len;$m++){
				if($medQryRes[$m]['allergy_status']!='Active' && $medQryRes[$m]['allergy_status']!='Order'){continue;}				
				$med_name = ucfirst($medQryRes[$m]['title']);
				
				//$med_destination = $medQryRes[$m]['destination'];
				if($medQryRes[$m]['sites'] == 3){
					$site = "OU";
				}elseif($medQryRes[$m]['sites'] == 2){
					$site = "OD";
				}elseif($medQryRes[$m]['sites'] == 1){
					$site = "OS";
				}elseif($medQryRes[$m]['sites'] == 4){
					$site = "PO";
				}else $site = '';
				$med_sig = $medQryRes[$m]['sig'];
				$dosage = $medQryRes[$m]['destination'];
				$comment = $medQryRes[$m]['med_comments'];
				$compliant = $medQryRes[$m]['compliant'];
				$date = $medQryRes[$m]['date'];
				$tmp ="";
				$tmp = trim($med_name);
				if(!empty($dosage))
				$tmp .=" ".$dosage;
				if(!empty($site))
				$tmp .=" ".$site;
				if(!empty($med_sig))
				$tmp .=" ".$med_sig;
				if(!empty($comment))
				$tmp .="; ".$comment;
				
				$bgdate = $medQryRes[$m]['begdate'];
				$arrFields[$med_name]['compliant']= $compliant;
				$arrFields[$med_name]['date']= $date;
				$arr_med_inf = array($med_name, $dosage, $site, $med_sig);
				if(!empty($bgdate)){
					$arrC_tmp[$bgdate][]=array($tmp,$med_name,$compliant,$date,$arr_med_inf);
				}else{
					$arrC_tmp["0000-00-00"][]=array($tmp,$med_name." ".$site,$compliant,$date,$arr_med_inf);
				}
			}		

			//Check for Duplicacy
			if(count($arrC_tmp)>0){
				$arrUniqueCheck = array();
				//sort by key
				krsort($arrC_tmp);
				$arrC=array();
				foreach($arrC_tmp as $key=>$val){
					if(count($val)>0){
						foreach($val as $key2=>$val2){
							$med_name=$val2[1];
							if(!empty($med_name) && in_array($med_name,$arrUniqueCheck)){continue;}
							$arrUniqueCheck[]=$med_name;
							$arrC[]=$val2[0];
							$arrC_v2[]=$val2[4];
						}	
					}
				}
			}		
			
			if(count($arrC)>0){
				$flg=1;
			}
			
		}
		return array($arrC,$flg,$arrFields, $arrC_v2);
	}
	
	//R8 function1
	function processOcuMeds($flgF){
		global $datahtm_ocu_meds;
		
		$arrSitesData = array(1=>'OS',2=>'OD',3=>'OU',4=>'PO');
		$edit_chart=0;
		
		$objChartRec = new ChartRecArc($this->pid, $this->fid,$_SESSION['authId']);
		if($objChartRec->getArcRecId(1) || $flgF == 1){
		//if($flgF == 1){
			$edit_chart=1;
			//Get Values from archive database 
			list($arrC,$flg,$arrFields,$arrC_v2)=$this->getArcOcuMedHx();
			$arrC_ln = count($arrC);
			//Finalize chart: show what is saved in chart notes tables
			/*
			// FETCH DATA OF PATIENT FROM LIST TABLE	
			list($arrM,$arrSig,$arrType,$arrDate,$arrComments,$arrSites,$arrCompliant) = $this->getOcularMedication();		
			$arrM_t=@array_reverse($arrM);
			$arrSig_t=@array_reverse($arrSig);
			$arrType_t=@array_reverse($arrType);
			$arrDate_t=@array_reverse($arrDate);
			$arrComments_t=@array_reverse($arrComments);
			$arrSites_t=@array_reverse($arrSites);
			$arrCompliant_t=@array_reverse($arrCompliant);	
			$arrDosage_t=@array_reverse($arrDosage);
			$ar_ttl_site_t=@array_reverse($ar_ttl_site);
			$ar_bgdt_t=@array_reverse($ar_bgdt);
			$ar_eddt_t=@array_reverse($ar_eddt);
			$ar_usr_t=@array_reverse($ar_usr);	
			*/	
			
			$cTmp=0;
			while($arrC_ln>$cTmp){
			$i = $cTmp;
			if(!empty($arrC[$i])){
				$keyVal = $medicine= $tDate= '';
				$arrC_tmp = explode(" ",$arrC[$i]);
				$medicine = $arrC_tmp[0];
				$arrC_fld = $arrC_v2[$i];	
				
				$tCompliant = $arrFields[$medicine]['compliant'];$arrCompliant = array();
				if($tCompliant == 0 || $tCompliant == ''){
					foreach($arrC_tmp as $tmp_val){//echo  $tmp_val."<br>";
						if(strrpos ($tmp_val,'compliant:')!==false){
							$arrCompliant = explode(":",$tmp_val);
						}
					}
					
					if(count($arrCompliant)>1)
					$tCompliant = '0';
				}
				$tDate = $arrFields[$medicine]['date'];
				if($tDate == 0 || $tDate == ''){
					foreach($arrC_tmp as $tmp_val){
						if(strrpos ($tmp_val,'date:')!==false){
							$arrDate = explode(":",$tmp_val);
						}
					}
					
					if(is_array($arrDate) && count($arrDate)>1){
					$tDate = $arrDate[1];
					}
				}
				
				$tc =""; $tS='';
				$tc =$arrC_fld[0];
				$tDosage =$arrC_fld[1];
				$tS=$arrC_fld[2];
				$tSg =$arrC_fld[3];
				
				/*
				$tc =""; $tS='';
				$flg = 0;
				$tc = $arrM_t[$i];
				$tSg = $arrSig_t[$i];
				$tType = 4;
				$tDate = $arrDate_t[$i];
				$tComments = $arrComments_t[$i];
				$tSites = trim($arrSites_t[$i]);
				$tCompliant = trim($arrCompliant_t[$i]);
				if($tCompliant==2){ $tCompliant=""; }elseif($tCompliant==1){ $tCompliant="Yes";}elseif($tCompliant==0){ $tCompliant="No";}
				$tDosage = trim($arrDosage_t[$i]);
				if($tSites!=''){
					$tS = $arrSitesData[$tSites];
				}
				$tBgDt = (!empty($ar_bgdt_t[$i]) && strpos($ar_bgdt_t[$i],"0000")===false) ? FormatDate_show(trim($ar_bgdt_t[$i])) : "" ;
				$tEdDt = (!empty($ar_eddt_t[$i]) && strpos($ar_eddt_t[$i],"0000")===false) ? FormatDate_show(trim($ar_eddt_t[$i])) : "" ;
				$tUsr = trim($ar_usr_t[$i]);
				
				//
				$oUsrfun = new User($tUsr);
				$tUsr = $oUsrfun->getName(8);

				$arrC_tmp = explode(" ",$arrC[$i]);
				$medicine = $arrC_tmp[0];
				
				
				$tCompliant = $arrFields[$medicine]['compliant'];$arrCompliant = array();
				if($tCompliant == 0 || $tCompliant == ''){
					foreach($arrC_tmp as $tmp_val){//echo  $tmp_val."<br>";
						if(strrpos ($tmp_val,'compliant:')!==false){
							$arrCompliant = explode(":",$tmp_val);
						}
					}
					
					if(count($arrCompliant)>1)
					$tCompliant = '0';
				}
				$tDate = $arrFields[$medicine]['date'];
				if($tDate == 0 || $tDate == ''){
					foreach($arrC_tmp as $tmp_val){
						if(strrpos ($tmp_val,'date:')!==false){
							$arrDate = explode(":",$tmp_val);
						}
					}
					
					if(count($arrDate)>1){
					$tDate = $arrDate[1];
					}
				}
				*/
				/*$pattern = '/ od| os| ou| po| OD| OS| OU| PO/';
				preg_match($pattern, $arrC[$i], $matches);
				
				$matches[0] = trim($matches[0]);
				$parts = (!empty($matches[0])) ? explode($matches[0], $arrC[$i]) : array();				
				$medicine = (!empty($parts[0])) ?  trim($parts[0]) : "" ;
				$tCompliant = (!empty($parts[0])) ?  trim($parts[0]) : "" ;
				if(array_keys($arrM_t, $medicine)){
					$keyVal=array_keys($arrM_t, $medicine);
					$tDate =  $arrDate_t[$keyVal[0]];
					$tCompliant =  $arrCompliant_t[$keyVal[0]];
				}*/
				
				//$tCompliant = $arrFields[$medicine]['compliant'];
				//$tDate = $arrFields[$medicine]['date'];
				//$$cssStyle = 'color:green;';
				//$$cssStyle = 'color:#390;font-size:12px';	// GREEN TEXT COLOR
				$med_color = "";
				/*if($medicine == $arrAlertMed[$medicine]){
					//$$cssStyle = 'color:#CC0000;';
					$$cssStyle = 'color:#390;';
				}*/
				if($tDate == date('Y-m-d')){
					//$$cssStyle = 'color:#FF8000; background-color:#49606D;';
					//$$cssStyle = 'color:#00FFFF; background-color:#49606D;';
					//$$cssStyle = 'color:#36F;font-size:12px';	// BLUE TEXT COLOR
					$med_color = "text-primary";
				}
				if($tCompliant=="No" && $tCompliant!=''){
					//$$cssStyle = 'color:#FFF; background-color:#900;';
					//$$cssStyle = 'color:#F00;font-size:12px';	// RED TEXT COLOR
					$med_color = "text-danger";
				}
				
				//echo preg_replace('/\s+compliant.*$/','',$arrC[$i]);
				$$tmp = preg_replace('/\s+compliant.*$/','',$arrC[$i]);
				$$tmp = preg_replace('/\s+date.*$/','',$$tmp);
				
				$str_htm = "". 
						//"<li>".
						//"<input name=\"".$elem_ocMedsKey."\" id=\"".$elem_ocMedsKey."\" value=\"".$$elem_ocMedsKey."\"  default=\"".$$elem_ocMedsKey."\" type=\"text\" onKeyPress=\"return ocularMedsHandler(this)\" onblur=\"checkValidOcuMed(this)\" style=\"".$$elem_style."\" readonly  onclick=\"open_medgrid();\"></li>".
						//"<li><input name=\"".$elem_ocMedsKey2."\"  id=\"".$elem_ocMedsKey2."\" value=\"".$$elem_ocMedsKey2."\"  default=\"".$$elem_ocMedsKey."\" type=\"text\" onKeyPress=\"return ocularMedsHandler(this)\" onblur=\"checkValidOcuMed(this)\" style=\"".$$elem_style2."\" readonly  onclick=\"open_medgrid();\"></li> ".							
						"<tr>".
						"<td data-label=\"Ocular : \" class=\"".$med_color."\">".$tc."</td>".
						"<td data-label=\"Dosage: \">".$tDosage."</td>".
						"<td data-label=\"Site : \">".$tS."</td>".
						"<td data-label=\"Sig : \">".$tSg."</td>".
						//"<td data-label=\"Compliant : \">".$tCompliant."</td>".
						//"<td align=\"center\" data-label=\"Begin Date : \">".$tBgDt."</td>".
						//"<td align=\"center\" data-label=\"End Date : \">".$tEdDt."</td>".
						//"<td data-label=\"Facility : \">".$tComments."</td>".
						//"<td data-label=\"Facility : \">".$tUsr."</td>".
						"</tr>".
						"";
				//		
				$datahtm_ocu_meds= $datahtm_ocu_meds . $str_htm;
			}
			$cTmp=$cTmp+1;
			}
		
		}else{
		
			list($arrM,$arrSig,$arrType,$arrDate,$arrComments,$arrSites,$arrCompliant,$arrDosage, $ar_ttl_site, $ar_bgdt, $ar_eddt, $ar_usr) = $this->getOcularMedication();
			
			$arrM_t=@array_reverse($arrM);
			$arrSig_t=@array_reverse($arrSig);
			$arrType_t=@array_reverse($arrType);
			$arrDate_t=@array_reverse($arrDate);
			$arrComments_t=@array_reverse($arrComments);
			$arrSites_t=@array_reverse($arrSites);
			$arrCompliant_t=@array_reverse($arrCompliant);	
			$arrDosage_t=@array_reverse($arrDosage);
			$ar_ttl_site_t=@array_reverse($ar_ttl_site);
			$ar_bgdt_t=@array_reverse($ar_bgdt);
			$ar_eddt_t=@array_reverse($ar_eddt);
			$ar_usr_t=@array_reverse($ar_usr);
			
			//Get Values from list table directly
			$len = count($arrM_t);
			if($len > 0){
				$cTmp=0;
				while($len>$cTmp){
					
					//
					$i = $cTmp;
				
					$tmp = "elem_ocMeds".($i+1);
					$cssStyle = "elem_ocMedsStyle".($i+1);
					/*
					$el_og_meds = "el_og_meds".($i+1);
					$el_og_dsg = "el_og_dsg".($i+1);
					$el_og_site = "el_og_site".($i+1);
					$el_og_sig = "el_og_sig".($i+1);
					$el_og_cmplnt = "el_og_cmplnt".($i+1);
					$el_og_bg_dt = "el_og_bg_dt".($i+1);
					$el_og_ed_dt = "el_og_ed_dt".($i+1);
					$el_og_cmnts = "el_og_cmnts".($i+1);
					$el_og_ordr_by = "el_og_ordr_by".($i+1);
					
					
					global $$tmp, $$el_og_dsg, $$el_og_site, $$el_og_sig, $$el_og_cmplnt, $$el_og_bg_dt, 
						$$el_og_ed_dt, $$el_og_cmnts, $$el_og_ordr_by;
					global $$cssStyle;
					*/
					$tc =""; $tS='';			
				
					$flg = 0;
					$tc = $arrM_t[$i];
					$tSg = $arrSig_t[$i];
					$tType = 4;
					$tDate = $arrDate_t[$i];
					$tComments = $arrComments_t[$i];
					$tSites = trim($arrSites_t[$i]);
					$tCompliant = trim($arrCompliant_t[$i]);
					if($tCompliant==2){ $tCompliant=""; }elseif($tCompliant==1){ $tCompliant="Yes";}elseif($tCompliant==0){ $tCompliant="No";}
					
					$tDosage = trim($arrDosage_t[$i]);
					if($tSites!=''){	$tS = $arrSitesData[$tSites];	}
					$tBgDt = (!empty($ar_bgdt_t[$i]) && strpos($ar_bgdt_t[$i],"0000")===false) ? FormatDate_show(trim($ar_bgdt_t[$i])) : "" ;
					$tEdDt = (!empty($ar_eddt_t[$i]) && strpos($ar_eddt_t[$i],"0000")===false) ? FormatDate_show(trim($ar_eddt_t[$i])) : "" ;
					$tUsr = trim($ar_usr_t[$i]);
					//
					$oUsrfun = new User($tUsr);
					$tUsr = $oUsrfun->getName(8);
					
					
					//$r = (!empty($tc)) ? array_lsearch($tc,$arrC) : true;
					//$r = (!empty($tc)) ? false : true;
					if(!empty($tc)){
						//Check for Uniqueness
						//$tmpUniCheck="".$tc." ".$tSites;
						//if(!in_array($tmpUniCheck,$arrUniChecker) ){	
						//	$arrUniChecker[]=$tmpUniCheck;
							
							$t = $tc;
							if(!empty($tDosage)) $t .= " ".$tDosage;
							if(!empty($tS)) $t .= " ".$tS;
							if(!empty($tSg)) $t .= " ".$tSg;
							if(!empty($tComments)) $t .=" ; ".$tComments;
							$$tmp = $t;
							
							//$$cssStyle = 'color:#390;font-size:12px';
							$med_color = "";
							/*if($tc == $arrAlertMed[$tc]){
								//$$cssStyle = 'color:#CC0000;';
								$$cssStyle = 'color:#390;';	// GREEN TEXT COLOR
							}*/
							if($tDate == date('Y-m-d')){
								//$$cssStyle = 'color:#FF8000; background-color:#49606D;';
								//$med_color = 'color:#36F;';	// BLUE TEXT COLOR
								$med_color = "text-primary";
							}
							if($tCompliant=="No"){
								//$$cssStyle = 'color:#FFF; background-color:#900;';
								$med_color = "text-danger"; //'color:#F00;';	// RED TEXT COLOR
							}
							
							//--
							/*
							$$el_og_meds = $tc;
							$$el_og_dsg = $tDosage;
							$$el_og_site = $tS;
							$$el_og_sig = $tSg;
							$$el_og_cmplnt = $tCompliant;
							$$el_og_bg_dt = $tBgDt;
							$$el_og_ed_dt = $tEdDt;
							$$el_og_cmnts = $tComments;
							$$el_og_ordr_by = $tUsr;
							*/
							//--						
							
							$str_htm = "". 
								//"<li>".
								//"<input name=\"".$elem_ocMedsKey."\" id=\"".$elem_ocMedsKey."\" value=\"".$$elem_ocMedsKey."\"  default=\"".$$elem_ocMedsKey."\" type=\"text\" onKeyPress=\"return ocularMedsHandler(this)\" onblur=\"checkValidOcuMed(this)\" style=\"".$$elem_style."\" readonly  onclick=\"open_medgrid();\"></li>".
								//"<li><input name=\"".$elem_ocMedsKey2."\"  id=\"".$elem_ocMedsKey2."\" value=\"".$$elem_ocMedsKey2."\"  default=\"".$$elem_ocMedsKey."\" type=\"text\" onKeyPress=\"return ocularMedsHandler(this)\" onblur=\"checkValidOcuMed(this)\" style=\"".$$elem_style2."\" readonly  onclick=\"open_medgrid();\"></li> ".							
								"<tr>".
								"<td data-label=\"Ocular : \" class=\"".$med_color."\">".$tc."</td>".
								"<td data-label=\"Dosage: \">".$tDosage."</td>".
								"<td data-label=\"Site : \">".$tS."</td>".
								"<td data-label=\"Sig : \">".$tSg."</td>".
								//"<td data-label=\"Compliant : \">".$tCompliant."</td>".
								//"<td align=\"center\" data-label=\"Begin Date : \">".$tBgDt."</td>".
								//"<td align=\"center\" data-label=\"End Date : \">".$tEdDt."</td>".
								//"<td data-label=\"Facility : \">".$tComments."</td>".
								//"<td data-label=\"Facility : \">".$tUsr."</td>".
								"</tr>".
								"";						
							//
							$datahtm_ocu_meds= $datahtm_ocu_meds . $str_htm;
							
						//}
						
					}
					
					$cTmp=$cTmp+1;

				};
			
			}
		
		}
		
		
		//
		$datahtm_ocu_meds = "<table id=\"tbl_ocu_grid\" class=\"table table-bordered table-striped table-hover\" onclick=\"openMedHX('medication_grid','800');\" data-edit_chart=\"".$edit_chart."\">
								<tr class=\"grythead\">
							      <th valign=\"middle\">Ocular Name</th>
							      <th valign=\"middle\">Dosage</th>
							      <th valign=\"middle\">Site  </th>
							      <th valign=\"middle\"> Sig.  </th>".
							      //"<th valign=\"middle\"> Compliant</th>".
								//"<th align=\"center\" valign=\"middle\"> Begin Date</th>".
							      //"<th align=\"center\" valign=\"middle\"> End Date</th>".
							      //"<th valign=\"middle\"> Comments</th>".
							      //"<th valign=\"middle\">User</th>".
							      
							    "</tr>".$datahtm_ocu_meds."</table>";
		
		
	}
	//--
	
	//Provides Meds in parts
	function checkValidOcuMed($str,$flgcheckdb=0){
		if(empty($str))return false;	
		
		//Get Comments
		$comment="";
		$part =explode(";",$str);
		$part[1] = trim("".$part[1]);
		if(!empty($part[1])){$comment=$part[1];}
		$str="".$part[0]; //redefined str
		
		//divide by site
		$site="";
		$arrSites = array(" OU "," OD "," OS "," PO "," OU"," OD"," OS"," PO");
		foreach($arrSites as $key => $val){		
			$tmpIndx = stripos($str, $val);
			if($tmpIndx!==false){
				//$arrMed = explode($val, $str);
				$site=trim($val);
				$site=strtoupper($site);
				
				$slen = strlen($val); 
				
				$arrMed[0] = substr($str,0,$tmpIndx);
				$arrMed[1] = substr($str,$tmpIndx+$slen);		
				
				break;
			}
		}
		//else
		if(!isset($arrMed)){		
			$arrMed[0] = trim($str);
			$arrMed[1] = "";
		}else{		
			$arrMed[0]=trim($arrMed[0]);
			$arrMed[1]=trim($arrMed[1]);
		}
		
		$sig = "".trim($arrMed[1]);	
		//-------------------
		$ocuname = trim($arrMed[0]);
		if($flgcheckdb==1){
			$sql = "SELECT count(*) as num FROM medicine_data WHERE LOWER(medicine_name) = LOWER('".sqlEscStr($ocuname)."')  AND ocular = '1' AND del_status = '0'";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$isOcu = 1;
			}else{
				if(!empty($arrMed[0]) && strpos($arrMed[0], " ")!==false){		
			
					$endIdx = strrpos(trim($arrMed[0]), " ");
					
					$ocuname_2 = trim($arrMed[0]);
					$ocuname = substr($arrMed[0], 0, $endIdx);
					$ocuname=trim($ocuname);	
					
					$dosg = substr($arrMed[0], $endIdx);
					$dosg = trim($dosg);
					
				}else{		
					$ocuname_2 = $ocuname=trim($arrMed[0]);
				}
				$isOcu = 0;
				if($flgcheckdb==1){
					$sql = "SELECT count(*) as num FROM medicine_data WHERE (LOWER(medicine_name) = LOWER('".sqlEscStr($ocuname)."') || LOWER(medicine_name) = LOWER('".sqlEscStr($ocuname_2)."'))  AND ocular = '1' AND del_status = '0'"; 
					$row = sqlQuery($sql);
					if($row!=false && $row["num"]>0){
						$isOcu = 1;
					}
				}
			}
		}
		//-------------------
		//Separate Dosage
		/*if(!empty($arrMed[0]) && strpos($arrMed[0], " ")!==false){		
			
			$endIdx = strrpos(trim($arrMed[0]), " ");
			
			$ocuname_2 = trim($arrMed[0]);
			$ocuname = substr($arrMed[0], 0, $endIdx);
			$ocuname=trim($ocuname);	
			
			$dosg = substr($arrMed[0], $endIdx);
			$dosg = trim($dosg);
			
		}else{		
			$ocuname_2 = $ocuname=trim($arrMed[0]);
		}
		
		//Check in admin->console-Med in Ocu. Meds
		$isOcu = 0;
		if($flgcheckdb==0){
			$sql = "SELECT count(*) as num FROM medicine_data WHERE (LOWER(medicine_name) = LOWER('".$ocuname."') || LOWER(medicine_name) = LOWER('".$ocuname_2."'))  AND ocular = '1' "; 
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$isOcu = 1;
			}
		}*/

		//Med Dosage site sig commnets
		$strOcGridMed = "".$ocuname." ";
		if(!empty($dosg)){ $strOcGridMed .= "".$dosg." "; }
		if(!empty($site)){ $strOcGridMed .= "".$site." "; }
		if(!empty($sig)){ $strOcGridMed .= "".$sig." "; }
		if(!empty($comment)){ $strOcGridMed = trim($strOcGridMed); $strOcGridMed .= "; ".$comment." "; }		
		
		return array("isOcu"=>$isOcu, "med"=>sqlEscStr($ocuname), "dosg"=>sqlEscStr($dosg), "site"=>sqlEscStr($site), "sig"=>sqlEscStr($sig), "comment"=>sqlEscStr($comment),"compOcuMed"=>sqlEscStr($strOcGridMed));
	}
	
	//ocu meds save
	function ocuMedsSave($arr){

		if(count($arr)>0){

		extract($arr);

		$title_2 = $title; //
		$title_2 = str_replace(" ","", $title_2);
		$title_2 = strtoupper($title_2);

		$flg="";
		//$sql="SELECT count(*) as num, id FROM lists WHERE title = '".$title."' AND type='".$type."' AND sites='".$sites."' AND pid = '".$pid."' AND allergy_status !='Deleted' ";
		$sql="SELECT count(*) as num, id, begdate, allergy_status FROM lists WHERE LOCATE('".sqlEscStr($title_2)."', UPPER(REPLACE(title, ' ', '')))>0 AND type='".$type."' AND sites='".$sites."' AND pid = '".$pid."' AND allergy_status !='Deleted' ";

		$row = sqlQuery($sql);
		if($row!=false && $row["num"] > 0){
			$id=$row["id"];
			$flg="UPDATE";
			$begdate_db=$row["begdate"];
			
			//so that it do not set administered meds to active.
			if($row["allergy_status"]=="Administered"){ $allergy_status=$row["allergy_status"]; }
			
		}else{

			if(!empty($title) && !empty($pid)){
				$flg="INSERT";
			}
			
		}

		$date=" '".wv_dt('now')."' ";
		$timestamp=" '".wv_dt('now')."' ";
		$user=$_SESSION["authId"];

		if($flg=="UPDATE"){
			$sql ="UPDATE lists ";
		}else if($flg=="INSERT"){
			$sql ="INSERT INTO lists ";}

		$sql .=" SET 
			date = ".$date.", 
			type = '".$type."', ";
			
		if($flg=="INSERT"){	
			$sql .="title = '".sqlEscStr($title)."', ";
		}

		//6456 - Dedham Eye - Dr. Gillies - Medication begin dates seem to be defaulting to the patient last visit
		if(!empty($begdate) && (!isset($begdate_db) || empty($begdate_db))){
			$sql .="begdate = '".$begdate."', ";
		}

		if(!isset($referredby)){ $referredby=""; }
		if(!isset($proc_type)){ $proc_type=""; }

		$sql .="sig = '".$sig."', ".
			"enddate = '".$enddate."', 
			pid = '".$pid."', 
			user = '".$user."', 
			destination = '".$destination."', 
			allergy_status = '".$allergy_status."', 
			med_comments = '".$med_comments."', 
			sites = '".$sites."', 
			timestamp = ".$timestamp.", 
			ccda_code_system = '".$ccda_code_system."', 
			ccda_code_system_name = '".$ccda_code_system_name."', 
			qty = '".$qty."',
			refills='".$refills."',
			referredby='".$referredby."',
			proc_type='".$proc_type."',
			procedure_type='".$procedure_type."'
			";

		if($flg=="UPDATE"){
			$sql .="WHERE id= '".$id."' ";	
		}

		if($flg=="UPDATE" || $flg=="INSERT"){	
			$row=sqlQuery($sql);
		}

		}

	}
	
function optical_order_action()
{
	$operator_id=$_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	
	$form_id = $this->fid;
	$patient_id = $this->pid;
	if(!empty($form_id)){
	$proc_sel=imw_query("select * from chart_procedures where form_id='$form_id'");
	while($proc_qry=imw_fetch_array($proc_sel)){
		$order_id=$proc_qry['order_id'];
		$patient_id=$proc_qry['patient_id'];
		$proc_id=$proc_qry['proc_id'];
		$pre_meds_arr=$proc_qry['pre_op_meds'];
		$pre_meds_arr .=$proc_qry['intravit_meds'];
		$pre_meds_arr .=$proc_qry['post_op_meds'];
		$pre_meds_exp=explode('|~|',$pre_meds_arr);
		$pre_meds_imp="'".implode("','",$pre_meds_exp)."'";
		$chart_proc_id=$proc_qry['id'];
		$proc_lot_sel=imw_query("select * from chart_procedures_med_lot where chart_procedure_id='$chart_proc_id' and lot_number!='' order by id");
		while($proc_lot_qry=imw_fetch_array($proc_lot_sel)){
			$proc_lot_arr[trim($proc_lot_qry['med_name'])][]=$proc_lot_qry['lot_number'];
		}
		
		$sel_med=imw_query("select * from medicine_data where medicine_name in($pre_meds_imp) and tracked_inventory>0");
		if(imw_num_rows($sel_med)>0){
			$pre_meds_exp_arr=array();
			foreach($pre_meds_exp as $key_all=>$val_all){
				$pre_meds_exp_arr[trim($val_all)][]=$val_all;
			}
			foreach($pre_meds_exp as $key=>$val){
				$lot_no="";
				$med_nam=trim($val);
				$sel_med_qry=imw_query("select * from medicine_data where medicine_name='$med_nam' and tracked_inventory>0");
				if(imw_num_rows($sel_med_qry)>0){
					$sel_med_row=imw_fetch_array($sel_med_qry);
					$opt_med_id=$sel_med_row['opt_med_id'];
					$opt_med_qry=imw_query("select * from in_item where id='$opt_med_id' and module_type_id='6'");
					/*if($opt_med_id>0){
						$opt_med_qry=mysql_query("select * from in_item where id='$opt_med_id' and module_type_id='6'");
					}else{
						$opt_med_qry=mysql_query("select * from in_item where name='$med_nam' and module_type_id='6'");
					}*/
					if(imw_num_rows($opt_med_qry)>0){
						$opt_med_row=imw_fetch_array($opt_med_qry);
						$item_id=$opt_med_row['id'];
						$item_prac_code=$opt_med_row['item_prac_code'];
						$dx_code=$opt_med_row['dx_code'];
						$upc_code=$opt_med_row['upc_code'];
						$item_name=$opt_med_row['name'];
						$retail_price=$opt_med_row['retail_price'];
						$manufacturer_id=$opt_med_row['manufacturer_id'];
						
						$sel_ord_det=imw_query("select id from in_order_details where order_id='$order_id' and item_id='$item_id'");
						$sel_ord_det_num=imw_num_rows($sel_ord_det);
						$lot_no=$proc_lot_arr[$med_nam][$sel_ord_det_num];
						
						if($order_id<=0 && $lot_no!=""){
							$ins_qry="insert into in_order set patient_id='$patient_id',entered_date='$entered_date',entered_time='$entered_time',operator_id='$operator_id'";
							imw_query($ins_qry);
							$order_id=imw_insert_id();
						}
						if(count($pre_meds_exp_arr[$med_nam])>$sel_ord_det_num && $lot_no!=""){
							if($_SESSION['remote_opt_loc_id']>0){
								$loc_id=$_SESSION['remote_opt_loc_id'];
							}else{
								$sel_loc_det=imw_query("select loc_id from in_item_lot_total where item_id='$item_id' and lot_no='$lot_no'");
								$get_loc_det = imw_fetch_array($sel_loc_det);
								$loc_id=$get_loc_det['loc_id'];
							}
							
							$sel_dx = imw_query("select diagnosis_id from diagnosis_code_tbl where (d_prac_code='$dx_code' or diag_description='$dx_code') and delete_status='0'");
							$get_dx_id = imw_fetch_array($sel_dx);
							$diagnosisId=$get_dx_id['diagnosis_id'];
							
							$ins_qry="insert into in_order_details set order_id='$order_id',patient_id='$patient_id',item_id='$item_id',item_prac_code='$item_prac_code',
							dx_code='$diagnosisId',upc_code='$upc_code',item_name='$item_name',module_type_id='$module_type_id',manufacturer_id='$manufacturer_id',
							qty='1',price='$retail_price',allowed='$retail_price',total_amount ='$retail_price',entered_date ='$entered_date',
							entered_time='$entered_time',operator_id='$operator_id',order_status='dispensed',dispensed='$entered_date',
							modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id',loc_id='$loc_id',pt_resp='$retail_price'";
							imw_query($ins_qry);
							$order_detail_id=imw_insert_id();
							
							$ins = "insert in_order_detail_status set patient_id='$patient_id',item_id='$item_id',order_id='$order_id', order_detail_id='$order_detail_id',order_qty='1', order_status='dispensed', order_date='$entered_date', order_time='$entered_time', operator_id='$operator_id'";
							$execut = imw_query($ins);
							
							$this->update_opt_order_faciliy($order_id,$order_detail_id,$patient_id,$item_id,1,$loc_id);
							$this->optical_deduct_item_qty($order_id,$order_detail_id,$patient_id,$item_id,$lot_no,1,$loc_id);
							
							$up_qry="update in_order set loc_id='$loc_id',total_qty=total_qty+1,total_price=total_price+$retail_price,grand_total=grand_total+$retail_price,modified_date='$entered_date',modified_time='$entered_time',modified_by='$operator_id',order_status='dispensed' where id='$order_id'";
							imw_query($up_qry);
						}
						$tot_qty_arr[]=1;
						$tot_price_arr[]=$retail_price;
					}
				}
			}
			
			if($order_id>0){
				$tot_qty_sum=array_sum($tot_qty_arr);
				$tot_price_sum=array_sum($tot_price_arr);
				/*$up_qry="update in_order set total_qty='$tot_qty_sum',total_price='$tot_price_sum',modified_date='$entered_date',modified_time='$entered_time',modified_by='$operator_id',order_status='dispensed' where id='$order_id'";
				imw_query($up_qry);*/
				imw_query("update chart_procedures set order_id='$order_id' where id='$chart_proc_id'");
				
			}
		}
	}
	}
}

function update_opt_order_faciliy($order_id,$order_detail_id,$patient_id,$item_id,$total_qty,$loc_id)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$login_facility=$_SESSION['login_facility'];
	$loc_id = $loc_id;
	//$facility_id_qty = get_opt_facility_ids($item_id,$total_qty);
	$sel_ex_order_fac = imw_query("select id from in_order_fac where order_id='$order_id' and order_det_id='$order_detail_id' and patient_id='$patient_id' and item_id='$item_id' and del_status='0'");
	if(imw_num_rows($sel_ex_order_fac) > 0)
	{
		while($get_ex_fac = imw_fetch_array($sel_ex_order_fac))
		{
			$update_ex_fac = imw_query("update in_order_fac set qty='0', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='".$get_ex_fac['id']."'");
		}
	}
	
	$sel_order_fac = imw_query("select id from in_order_fac where order_id='$order_id' and order_det_id='$order_detail_id' and patient_id='$patient_id' and item_id='$item_id' and facility_id='$login_facility' and del_status='0' LIMIT 1");
	if(imw_num_rows($sel_order_fac) > 0)
	{
		$order_fac_row = imw_fetch_array($sel_order_fac);
		$act = "update";
		$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$order_fac_row['id']."'";
	}
	else
	{
		$act = "insert into";
		$qryWhere=", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
	}
	
	$query = imw_query("$act in_order_fac set order_id='$order_id', order_det_id='$order_detail_id', patient_id='$patient_id', item_id='$item_id', facility_id='$login_facility',loc_id='$loc_id', qty='$total_qty' $qryWhere");
}

function optical_deduct_item_qty($ord_id,$ord_det_id,$pat_id,$item_id,$lot_no,$tot_qty,$loc_id)
{
	$operator_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("H:i:s");
	//get default location id
							
	/*$select_fac = imw_query("select loc.id as locid from in_order_fac as ord_fac 
								left join facility as fac on fac.id=ord_fac.facility_id 
								left join in_location as loc on loc.pos=fac.fac_prac_code 
								where ord_fac.del_status='0' 
								and ord_fac.order_id='$ord_id' 
								and ord_fac.order_det_id='$ord_det_id' 
								and ord_fac.patient_id='$pat_id' 
								and ord_fac.item_id='$item_id' order by ord_fac.id asc limit 1");
	$data_fac=imw_fetch_object($select_fac);
	$df_ord_fac_id = $data_fac->locid;*/
	$df_ord_fac_id = $loc_id;
	$select_fac.close;
	if($df_ord_fac_id)
	{
		//check is default location record exist in loc_total table
		if(imw_num_rows(imw_query("select * from in_item_loc_total where item_id='$item_id' and loc_id=$df_ord_fac_id"))==0)
		imw_query("insert into in_item_loc_total set item_id='$item_id', loc_id=$df_ord_fac_id");
	}
	
	$sel_qty_fac = imw_query("select * from in_item_lot_total where item_id='$item_id' and lot_no='$lot_no' and stock>0");
	while($data=imw_fetch_object($sel_qty_fac))
	{
		$stockArr[$data->loc_id][$data->id]=$data->stock;
	}
	$sel_qty_fac.close;
	
	$restQty=$tot_qty;
	//check stock for default facility/location
	if(array_sum($stockArr[$df_ord_fac_id])>=1)
	{
		foreach($stockArr[$df_ord_fac_id] as $id=>$stock)
		{
			if($stock>=$restQty)
			{
				$deduct_arr[$df_ord_fac_id][$id]=$restQty;
				$restQty=0;	
			}
			else
			{
				$deduct_arr[$df_ord_fac_id][$id]=$stock;
				$restQty=$restQty-$stock;
			}
			if($restQty==0)break;
		}
	}
		
	unset($stockArr[$df_ord_fac_id]);//clear default facility/location record
	
	//check stock for other facility/location
	if($restQty>0)
	{
		foreach($stockArr as $loc_id=>$subArr)
		{
			foreach($subArr as $id=>$stock)
			{
				if($stock>=$restQty)
				{
					$deduct_arr[$loc_id][$id]=$restQty;
					$restQty=0;	
				}
				else
				{
					$deduct_arr[$loc_id][$id]=$stock;
					$restQty=$restQty-$stock;
				}
				if($restQty==0)break;	
			}
		}
	}
	//check rest qty
	if($restQty>0)$deduct_arr[$df_ord_fac_id]+=$restQty;
	
	//deduct qty
	foreach($deduct_arr as $loc_id=>$subArray)
	{
		foreach($subArray as $id=>$qty)
		{
			if($qty)imw_query("update in_item_loc_total set stock=(stock-$qty) where item_id='$item_id' and loc_id=$loc_id ");
			imw_query("update in_item_lot_total set stock=stock-$qty where id=$id");
		}
	}
	
	$sel_item_qry = imw_query("select retail_price, id, qty_on_hand from in_item where id='$item_id'");
	$get_item_qry = imw_fetch_array($sel_item_qry);
	$new_qty = $get_item_qry['qty_on_hand']-$tot_qty;
	$new_amt=0;
	if($new_qty>0)
	{
		$new_amt = $get_item_qry['retail_price']*$new_qty;
	}
	
	$deduct_qty = imw_query("update in_item set qty_on_hand='$new_qty', amount='$new_amt', modified_date='$date', modified_time='$time', modified_by='$operator_id' where id='$item_id'");
}

function getPtLExamInfo(){

	$pid=$this->pid;
	$fid=$this->fid;
	
	if(!empty($fid)){
		$strFid = "  AND formid='".$fid."' ";		
	}
	
	$tmp = "";
	$qry = "select operator_id, created_date as createdDate
		  from patient_last_examined where patient_id = '$pid' ".$strFid."
		  order by patient_last_examined_id desc limit 0,1";
	$row = sqlQuery($qry);
	if($row != false){
		$operator_id = $row["operator_id"];
		$createdDate = wv_formatDate($row['createdDate'], $syr=0, $tm=3, $op="show");  //date_format(created_date,'".getSqlDateFormat('','y')." %h:%i %p')
	}
	
	if(!empty($operator_id)){
		$qry = "select concat(substr(fname from 1 for 1),'',
			  substr(lname from 1 for 1)) as name from users where id = '$operator_id'";
		$row = sqlQuery($qry);
		if($row != false){
			$phyDetail = $row["name"];
		}
		$tmp = " ".$createdDate." ".$phyDetail;	
	}
	return $tmp;
}

	function isGenHealthDone(){
		$pid = $this->pid;
		$retFlag=0;		
		$sql = array();
		$sql[0] = "select title from lists where pid='".$pid."' and type in ('1','3','4','5','6','7') ";
		$sql[1] = "select id from immunizations  where patient_id='".$pid."' ";
		$sql[2] = "select social_id from social_history where patient_id='".$pid."' ";
		$sql[3] = "select general_id from general_medicine  where patient_id ='".$pid."' ";

		for($i=0;$i<4;$i++){
			$row = sqlQuery($sql[$i]);
			if($row != false){
				$retFlag=1;
				break;
			}
		}
		
		//Check reviewed --
		if($retFlag != 1){			
			//include_once(dirname(__FILE__)."/../common/functions_ptInfo.php");
			$tmp = $this->getPtLExamInfo();			
			if(!empty($tmp)){
				$retFlag=1;
			}			
		}
		//Check reviewed --

		return $retFlag;
	}
	
	function isMedHxDone(){
		$pid = $this->pid;
		$retMedCond = $retROS=$retPFSH=$retFHX=$retSocialHx=0;
		$sql = "select 
				any_conditions_you, any_conditions_relative, any_conditions_you_n, any_conditions_relative1_n,
				chk_annual_colorectal_cancer_screenings,chk_receiving_annual_mammogram, chk_received_flu_vaccine,
				chk_received_pneumococcal_vaccine, chk_high_risk_for_cardiac, nutrition_counseling, chk_fall_risk_assd,

				negChkBx, cbk_master_ROS, 
				review_const, review_const_others, review_head, review_head_others,
				review_resp,review_resp_others, review_card, review_card_others, review_gastro, review_gastro_others, review_genit,
				review_genit_others, review_aller, review_aller_others, review_neuro, review_neuro_others, review_sys
				from general_medicine  where patient_id ='".$pid."' ";
		$row = sqlQuery($sql);
		
		if($row!=false){
			
			//patient
			if(!empty($row["any_conditions_you"]) || !empty($row["any_conditions_you_n"]) || 
				!empty($row["chk_annual_colorectal_cancer_screenings"]) || !empty($row["chk_receiving_annual_mammogram"]) || 
				!empty($row["chk_received_flu_vaccine"]) || !empty($row["chk_received_pneumococcal_vaccine"]) || !empty($row["chk_high_risk_for_cardiac"]) || 
				!empty($row["nutrition_counseling"]) || !empty($row["chk_fall_risk_assd"])  
				){
				$retMedCond =1;
			}
			
			//relative
			if(!empty($row["any_conditions_relative"]) || !empty($row["any_conditions_relative1_n"])){
				$retFHX=1;
			}
			
			
			if(!empty($row["cbk_master_ROS"])){ 
				$retROS=10;
			}else{
				$retROS=0;
				if(!empty($row["review_const"]) || !empty($row["review_const_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"1")!==false)){
					$retROS+=1;
				}
				if(!empty($row["review_head"]) || !empty($row["review_head_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"2")!==false)){
					$retROS+=1;
				}
				if(!empty($row["review_resp"]) || !empty($row["review_resp_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"3")!==false)){
					$retROS+=1;
				}
				if( !empty($row["review_card"]) || !empty($row["review_card_others"])  || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"4")!==false)){
					$retROS+=1;
				}
				if(!empty($row["review_gastro"]) || !empty($row["review_gastro_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"5")!==false)){
					$retROS+=1;
				}
				if(!empty($row["review_genit"]) || !empty($row["review_genit_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"6")!==false)){
					$retROS+=1;
				}
				if(!empty($row["review_aller"]) || !empty($row["review_aller_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"7")!==false)){
					$retROS+=1;
				}					
				if(!empty($row["review_neuro"]) || !empty($row["review_neuro_others"]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"8")!==false)){
					$retROS+=1;
				}
				
				//Review_sys
				$review_sys = trim($row["review_sys"]);
				if(!empty($row["review_sys"])){					
					$ar_review_sys = json_decode($review_sys, true);
					$ar_tmp = array("9"=>'review_intgmntr',	"10"=>'review_psychiatry', "11"=>'review_blood_lymph', "12"=>'review_musculoskeletal',
									"13"=>'review_endocrine', "14"=>'review_eye');
					foreach($ar_tmp as $k => $v){
						$vother = $v."_others";
						if(!empty($ar_review_sys[$v]) || !empty($ar_review_sys[$vother]) || (!empty($row["negChkBx"]) && strpos($row["negChkBx"],"".$k)!==false)){
							$retROS+=1;
						}
					}
				}
			}			
			/*
			if(!empty($row["negChkBx"]) || !empty($row["cbk_master_ROS"]) ||  ||  ||
				 ||  ||
				 ||  || 
				){
				$retROS=1;
			}
			*/	
		}
		
		//social
		$sql = "select smoke_perday,smoke_counseling, source_of_smoke, number_of_years_with_smoke, family_smoke,smoke_description,
			smoking_status, smoking_status_id, cessation_counselling_option, smoke_years_months, intervention_not_performed_status, intervention_reason_option,
			med_order_not_performed_status, med_order_reason_option from social_history where patient_id='".$pid."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			if(!empty($row["smoke_perday"]) || !empty($row["smoke_counseling"]) || !empty($row["source_of_smoke"]) || !empty($row["number_of_years_with_smoke"]) || !empty($row["family_smoke"]) || !empty($row["smoke_description"]) ||
				!empty($row["smoking_status"]) || !empty($row["smoking_status_id"]) || !empty($row["cessation_counselling_option"]) || !empty($row["smoke_years_months"]) || !empty($row["intervention_not_performed_status"]) || !empty($row["intervention_reason_option"]) ||
				!empty($row["med_order_not_performed_status"]) || !empty($row["med_order_reason_option"]) ){
				$retSocialHx=1;
			}
		}		
		
		//PFSH
		if($retSocialHx==1&&$retFHX==1&&$retMedCond==1){
			$retPFSH=1;
		}		
		
		return array($retMedCond, $retROS, $retPFSH);	
	}
	
	function getOcularEyeInfo_v2(){
		//OCular
		$arrPtChroCond = array();
		$sql = "select any_conditions_you,any_conditions_others_you, chronicDesc, chronicRelative, OtherDesc FROM ocular WHERE patient_id='".$this->pid."' ";
		$row= sqlQuery($sql);
		if($row != false){
			$any_conds_u = $row["any_conditions_you"];
			$any_conds_other_u = $row["any_conditions_others_you"];
			$chronicDesc = $row["chronicDesc"];
			$chronicRelative = $row["chronicRelative"];
			$otherDesc = !empty($row["OtherDesc"]) ? $row["OtherDesc"] : "";
			
			//desc
			$strSep="~!!~~";
			$strSep2=":*:";
			$strDesc = $chronicDesc;				
			$arrDesc = array();
			$arrRelative = array();
			
			if(!empty($strDesc)){
				$arrDescTmp = explode($strSep, $strDesc);
				if(count($arrDescTmp) > 0){
					foreach($arrDescTmp as $key => $val){
						$arrTmp = explode($strSep2,$val);
						$arrDesc[$arrTmp[0]] = $arrTmp[1];							
					}
				}				
			}	
			
			//Relative
			if( !empty($chronicRelative) ){
				$arrRelTmp = explode($strSep, $chronicRelative);
				if( count($arrRelTmp) > 0 ){
					foreach( $arrRelTmp as $key => $val ){
						$arrTmp = explode($strSep2, $val);
						$arrRelative[$arrTmp[0]] = $arrTmp[1];	
					}
				}
			}	
			
			//chronic	
			$arrChroCond = array("Dry Eyes","Macula Degeneration","Glaucoma","Retinal Detachment","Cataracts" );
			$any_conditions_you_arr=explode(" ",trim(str_replace(","," ",$any_conds_u)));
			
			if( count($any_conditions_you_arr) > 0 ){					
				$arrPtChroCond = array();	
				foreach($any_conditions_you_arr as $keyTmp => $valTmp){
					if(!empty($arrChroCond[$valTmp-1])){
						$tmp = "";
						$tmp .= $arrChroCond[$valTmp-1];
						$tmp.= (!empty($arrRelative[$valTmp])) ? " (".$arrRelative[$valTmp].")" : "";
						$tmp.= (!empty($arrDesc[$valTmp])) ? " ".$arrDesc[$valTmp]."" : "";	
						$tmp = trim($tmp);
						$tmp = str_replace("~|~","",$tmp);	
						$arrPtChroCond[]= $tmp;
					}
				}
			}
			
			if(!empty($any_conds_other_u)){
				$tmp = "";
				$tmp .= $otherDesc;
				$tmp .= (!empty($arrRelative["other"])) ? " (".$arrRelative["other"].") " : "";
				$tmp .= $arrDesc["other"];
				$tmp = trim($tmp);
				$tmp = str_replace("~|~","",$tmp);
				$arrPtChroCond[]= $tmp;
			}
		}
		return $arrPtChroCond;
	}
	
	function getOcularEyeInfo($strCcHx="0Empty0", $chronicProbs_prv=""){
		
		$eyeProbs="";
		$chronicProbs = "";
		$ar_chronicProbs = array();
		$floatersYesNo = "No";$flashYesNo = "No";
		
		//
		$chronicProbs_prv = trim($chronicProbs_prv);
		if(!empty($chronicProbs_prv)){
			$chronicProbs_prv = htmlspecialchars_decode($chronicProbs_prv);
			$ar_chronicProbs_prv = json_decode($chronicProbs_prv, true);
		}
		
		//Ocular
		$sql = "select any_conditions_you, chronicDesc, any_conditions_others_you, OtherDesc, eye_problems,eye_problems_other 
				from ocular where patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		if($row != false){

			//desc
			$strSep="~!!~~";
			$strSep2=":*:";
			$strDesc = $row["chronicDesc"];
			$strDesc = $this->get_set_pat_rel_values_retrive($strDesc,"pat");
			$strDesc = html_entity_decode($strDesc);
			$strDesc = filter_var($strDesc, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$strDesc = html_entity_decode($strDesc);
			$strDesc = filter_var($strDesc, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			
			$arrChronicDesc=array();
			if(!empty($strDesc)){
				$arrDescTmp = explode($strSep, $strDesc);
				if(count($arrDescTmp) > 0){
					foreach($arrDescTmp as $key => $val){
						$arrTmp = explode($strSep2,$val);
						$arrChronicDesc[$arrTmp[0]] = $arrTmp[1];
					}
				}
			}		

			$strAnyConditionsYou = $row["any_conditions_you"];
			$strAnyConditionsYou = $this->get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat");

			$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$strAnyConditionsYou)));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr);$epr++){
				if(in_array("1", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[1])) ? " - ".$arrChronicDesc[1] : "";
					$sTmp2 ="Dry Eyes";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["1"] = $sTmpC;
					
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["1"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){						
							$strCcHx.=$sTmpC;
						}	
					}
					
					//$elem_glucoma = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["1"]);					
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
				
				if(in_array("2", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[2])) ? " - ".$arrChronicDesc[2] : "";
					$sTmp2 ="Macular Degeneration";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["2"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["2"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}
					}
					//$elem_macDeg = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["2"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
				
				if(in_array("3", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[3])) ? " - ".$arrChronicDesc[3] : "";
					$sTmp2 ="Glaucoma";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["3"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["3"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}	
					}
					//$elem_glucoma = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["3"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
				
				
				if(in_array("4", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[4])) ? " - ".$arrChronicDesc[4] : "";
					$sTmp2 ="Retinal Detachment";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["4"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["4"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}	
					}
					//$elem_rDetach = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["4"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
				
				
				
				if(in_array("5", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[5])) ? " - ".$arrChronicDesc[5] : "";
					$sTmp2 ="Cataracts";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["5"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["5"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}	
					}
					//$elem_cataracts = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["5"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
				
				
				if(in_array("6", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[6])) ? " - ".$arrChronicDesc[6] : "";
					$sTmp2 ="Keratoconus";
					$sTmpC ="\n".$sTmp2.$sTmp;
					$ar_chronicProbs["6"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["6"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}	
					}
					//$elem_cataracts = "1";
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["6"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}

			//}		
			
			
			$strOtherDesc = "";		
			if(!empty($arrChronicDesc["other"])){
				
				$strOtherDesc = $arrChronicDesc["other"];
				$sTmp2 =trim("".$strOtherDesc);
				$sTmp2 = wv_str_replace_html_chars($sTmp2);
				$sTmpC ="\n".$sTmp2;
				$ar_chronicProbs["other"] = $sTmpC;
				if($strCcHx=="0Empty0"){				
					$chronicProbs.=$sTmpC;
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["other"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
					}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
						$strCcHx.=$sTmpC;
					}	
				}
				
			}else{		
				$strOtherDesc = $row["OtherDesc"];
				$strOtherDesc = $this->get_set_pat_rel_values_retrive($strOtherDesc,"pat");
				$strOtherDesc = html_entity_decode($strOtherDesc);	
				$strOtherDesc = filter_var($strOtherDesc, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
				$strOtherDesc = html_entity_decode($strOtherDesc);	
				$strOtherDesc = filter_var($strOtherDesc, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
				if(($row["any_conditions_others_you"] == "1") && !empty($strOtherDesc)){
					$sTmp = (!empty($arrChronicDesc[$arrTmp["other"]])) ? " - ".$arrChronicDesc[$arrTmp["other"]] : "";
					$sTmp2 =trim("".$strOtherDesc.$sTmp);				
					$sTmp2 = wv_str_replace_html_chars($sTmp2);
					$sTmpC ="\n".$sTmp2;
					$ar_chronicProbs["other"] = $sTmpC;
					if($strCcHx=="0Empty0"){
						$chronicProbs.=$sTmpC;
					}else{
						$sTmpPr = trim($ar_chronicProbs_prv["other"]);
						if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
							$strCcHx = str_replace($sTmpPr,$sTmpC,$strCcHx);	
						}else if(wv_str_compare_wo_space($strCcHx,$sTmpC)===false){
							$strCcHx.=$sTmpC;
						}	
					}				
				}else{
					$sTmpPr = trim($ar_chronicProbs_prv["other"]);
					if(!empty($sTmpPr) && wv_str_compare_wo_space($strCcHx,$sTmpPr)!==false){
						$strCcHx = str_replace($sTmpPr,"",$strCcHx);
					}
				}
			}			
			
			$eye_problems_arr=explode(" ",trim(str_replace(","," ",$row["eye_problems"])));
			//for($epr=0;$epr<=sizeof($eye_problems_arr);$epr++){
				if(in_array("1",$eye_problems_arr)){
					//$eyeProbs.="\nBlurred";
					$eyeProbs.= "\nBlurred or Poor Vision";
				}
				if(in_array("2",$eye_problems_arr)){
					$eyeProbs.="\nPoor night vision";
				}
				if(in_array("3",$eye_problems_arr)){
					$eyeProbs.="\nGritty Sensation";
				}
				if(in_array("4",$eye_problems_arr)){
					$eyeProbs.="\nTrouble Reading Signs";
				}
				if(in_array("5",$eye_problems_arr)){
					$eyeProbs.="\nGlare From Lights";
				}
				if(in_array("6",$eye_problems_arr)){
					$eyeProbs.="\nTearing";
				}
				if(in_array("7",$eye_problems_arr)){
					$eyeProbs.="\nPoor Depth Perception";
				}
				if(in_array("8",$eye_problems_arr)){
					$eyeProbs.="\nHalos Around Lights";
				}
				if(in_array("9",$eye_problems_arr)){
					$eyeProbs.="\nItching/Burning";
				}
				if(in_array("10",$eye_problems_arr)){
					$eyeProbs.="\nTrouble Identifying Colors";
				}
				if(in_array("11",$eye_problems_arr)){
					$eyeProbs.="\nSpots/Floaters";
					$floatersYesNo = "Yes";
				}
				if(in_array("12",$eye_problems_arr)){
					$eyeProbs.="\nEye Pain";
				}
				if(in_array("13",$eye_problems_arr)){
					$eyeProbs.="\nDouble Vision";
				}
				if(in_array("14",$eye_problems_arr)){
					$eyeProbs.="\nSee Light Flashes";
					$flashYesNo = "Yes";
				}
				if(in_array("15",$eye_problems_arr)){
					$eyeProbs.="\nRed eyes";
				}
				if(!empty($row["eye_problems_other"])){
					$eyeProbs.="\n".$row["eye_problems_other"];
				}
			//}
			/*
			// Glasses Or Lens
			$you_wear = $row["you_wear"];
			if($you_wear == 1){
				//$rvs.="\nGlasses";
				//$elem_glasses = "1";
			}else if($you_wear == 2){
				//$rvs.="\nCL";
				//$elem_cl = "1";
			}else if($you_wear == 3){
				//$elem_glasses = "1";
				//$elem_cl = "1";
			}*/
		}
		
		//remove \n		
		$strCcHx = preg_replace('/(\n\s?)+/', "\n", $strCcHx);
		
		//
		$str_chronicProbs = json_encode($ar_chronicProbs);
		$str_chronicProbs = htmlspecialchars($str_chronicProbs); 
		
		if($strCcHx=="0Empty0"){
			return array($eyeProbs,$chronicProbs,$floatersYesNo,$flashYesNo,$str_chronicProbs);
		}else{
			return array($strCcHx,$str_chronicProbs); 
		}
	}
	
	function get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= ""){
		$dbValue 	= trim($dbValue);		
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);
		if($methodFor == "pat"){
			//echo '<br>dbv='.$dbValue;
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtPat;
				//echo '<br>'.$valueToShow;
			}
			else{
				$valueToShow = $dbValue;
			}
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtRel;
			}
			else{				
				$valueToShow = "";
			}
		}
		
		if($valueToShow) { $valueToShow = $hifenOptional.$valueToShow; }//FOR FACESHEET PDF
		
		return $valueToShow;
	}
	
	function getGenMedInfo($flagDiab=false){
		$delimiter = '~|~';
		$rvs = "";
		//$ptInfoDiaDesc="";
		//General Medicine
		$sql = "select any_conditions_you,any_conditions_relative,desc_u,desc_r,diabetes_values from general_medicine where patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$any_conditions_u1_arr1=explode(" ",trim(str_replace(","," ",$row["any_conditions_you"])));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr1);$epr++){

				//Diabeties
				if(in_array("3",$any_conditions_u1_arr1)){
					$strDiabetesIdTxtPat =  $this->get_set_pat_rel_values_retrive($row["diabetes_values"],'pat',$delimiter);				
					//$rvs.="Diabetes ".date("m-Y")." ".$strDiabetesIdTxtPat; //Dec10,2011::Diabetes – Why does the Current Month and Year date come up (please remove the date as this is incorrect the Pt Is not Diabetic this month)
					$rvs.="Diabetes ".$strDiabetesIdTxtPat;
					//$rvs.="\nDiabetes ";
					//$ptInfoDiaDesc="Diabetes";
					$strDiabetesTxtPat = "";
					$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_u"],"pat");
					$ptInfoDiaDesc=(!empty($strDiabetesTxtPat))? trim($ptInfoDiaDesc." ".stripslashes($strDiabetesTxtPat)) : "";
					if(!empty($ptInfoDiaDesc)) $rvs = $rvs." ".$ptInfoDiaDesc;
					$rvs = ($flagDiab) ? "".$ptInfoDiaDesc : $rvs;
				}

			//}
			/*//if(empty($ptInfoDiaDesc)){
				$any_conditions_r1_arr1=explode(" ",trim(str_replace(","," ",$row["any_conditions_relative"])));
				//Diabeties
				if(in_array("3",$any_conditions_r1_arr1)){
					$rvs .="\nDiabetes ";
					$rvs .= (!empty($row["desc_r"])) ? $ptInfoDiaDesc." ".$row["desc_r"] : $ptInfoDiaDesc;
				}
			//}*/
		}
		return $rvs;
	}
	
	function getPtGenHealthInfo(){
		$pid = $this->pid;
		$retVal = "";
		if(!empty($pid)){
			$retVal = array();			
			
			$sql = "select any_conditions_you, chk_annual_colorectal_cancer_screenings, chk_receiving_annual_mammogram, chk_received_flu_vaccine, chk_high_risk_for_cardiac, 
						sub_conditions_you, any_conditions_others_both, any_conditions_others, any_conditions_others, diabetes_values, chk_under_control, desc_r, relDescHighBp, 
						relDescStrokeProb, relDescHeartProb, relDescLungProb, relDescThyroidProb, relDescArthritisProb, relDescUlcersProb, relDescCancerProb, relDescLDL, 
						ghRelDescOthers, desc_u, desc_high_bp, desc_arthrities, desc_lung_problem, desc_stroke, desc_thyroid_problems, desc_ulcers, desc_cancer, desc_heart_problem,
						desc_LDL, any_conditions_others, genMedComments, any_conditions_others_both, review_const, review_head, review_resp, review_card, review_gastro, review_genit,
						review_aller, review_neuro, negChkBx, review_const_others, review_head_others, review_resp_others, review_card_others, review_gastro_others	, review_genit_others,
						review_aller_others, review_neuro_others, review_sys	
						from general_medicine where patient_id='".$pid."' ";
			$row = sqlQuery($sql);
			if($row != false){
				//Any Condition
				$arrPtAnyCond = array();
				$arrAnyCond = array("High Blood Pressure","Heart Problem","Diabetes","Lung Problems","Stroke","Thyroid Problems","Arthritis", "Ulcers", "", "", "", "", "LDL", "Cancer" );
				//Patient
				$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_you"])));		
				
				//Relative
				//$any_conditions_ralative1_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_relative"])));				
				//$arrTmp = array("You"=>$any_conditions_u1_arr,"Relatives"=>$any_conditions_ralative1_arr);
				
				$arrTmp = array("You"=>$any_conditions_u1_arr);
				foreach( $arrTmp as $key => $val ){
					$tmp = $val;				
					if( count($tmp) > 0 ){					
						$arrPtAnyCond[$key]=array();	
						foreach($tmp as $keyTmp => $valTmp){
							if(!empty($arrAnyCond[$valTmp-1])){
								
								//if($arrAnyCond[$valTmp-1] == "Diabetes"){
								//	$elem_desc_u = !empty($row["desc_u"]) ? " - ".$row["desc_u"] : "" ;
								//	$arrPtAnyCond[$key][]= "<font color=\"red\">".$arrAnyCond[$valTmp-1].$elem_desc_u."</font>";
								//}else{
									$arrPtAnyCond[$key][]=$arrAnyCond[$valTmp-1];
								///}
							}
						}
					}
				}
				
							
				if($row["chk_annual_colorectal_cancer_screenings"]==1){
				  $strAnnual .= "Annual colorectal cancer screenings,";
				}
				
				if($row["chk_receiving_annual_mammogram"]==1){
				  $strAnnual .= "Receiving annual mammogram,";
				}
				
				if($row["chk_received_flu_vaccine"]==1){
				  $strAnnual .= "Received flu vaccine,";
				}
				
				if($row["chk_high_risk_for_cardiac"]==1){
				  $strAnnual .= "High-risk for cardiac events on aspirin prophylaxis";
				}
				$retVal["str_annaual"] = $strAnnual;
				//Sub Conditions
				$arrSubConditions=array();
				$arrSbConText=array("7.1"=>"RA","7.2"=>"OA");
				$elem_subCondition_pat_val = $this->get_set_pat_rel_values_retrive($row["sub_conditions_you"],'pat',"~|~");
				
				$arr_sub_condition_you = explode(",", $elem_subCondition_pat_val);
				$lenSCds = count($arr_sub_condition_you);
				for($i=0;$i<$lenSCds;$i++){
					$arrSubConditions["Arthritis"][]=$arrSbConText[$arr_sub_condition_you[$i]];
				}
				
				$retVal["SubCond"]=$arrSubConditions;
				
				//Other Condition 				
				$any_conditions_others_both_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_others_both"])));
				$strOthersTxtPat = $this->get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");	
				$otherCondition = $strOthersTxtPat;
				if(!empty($otherCondition)){					
					foreach( $any_conditions_others_both_arr as $key => $val ){						
						if($val == "1"){
							$arrPtAnyCond["You"][]= $otherCondition;
							$retVal["patient_other"]= $row["any_conditions_others"];
						}
						if($val == "2")
						{
							$arrPtAnyCond["Relatives"][]= $otherCondition;
						}
					}
				}
				
				$retVal["AnyCond"]= $arrPtAnyCond;
				$strDiabetesIdTxtPat =  $this->get_set_pat_rel_values_retrive($row["diabetes_values"],'pat',"~|~"); 
				$retVal["diabetes_values"]= $strDiabetesIdTxtPat;
				$arrChkUnderControl = explode(',',$row["chk_under_control"]);
				$retVal["chkUnderControl"]= $arrChkUnderControl;
				$retVal["desc_r"]= $row["desc_r"];
				$retVal["relDescHighBp"]= $row["relDescHighBp"];
				$retVal["relDescStrokeProb"]= $row["relDescStrokeProb"];
				$retVal["relDescHeartProb"]= $row["relDescHeartProb"];
				$retVal["relDescLungProb"]= $row["relDescLungProb"];
				$retVal["relDescThyroidProb"]= $row["relDescThyroidProb"];
				$retVal["relDescArthritisProb"]= $row["relDescArthritisProb"];
				$retVal["relDescUlcersProb"]= $row["relDescUlcersProb"];
				$retVal["relDescCancerProb"]= $row["relDescCancerProb"];
				$retVal["relDescLDL"]= $row["relDescLDL"];
				$retVal["ghRelDescOthers"]= $row["ghRelDescOthers"];
				$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_u"],"pat","~|~");
				$retVal["desc_u"]= $strDiabetesTxtPat;
				$strHighBPTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_high_bp"],"pat","~|~");
				$retVal["desc_high_bp"]= $strHighBPTxtPat;
				$strArthritiesTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_arthrities"],"pat","~|~");
				$retVal["desc_arthrities"]= $strArthritiesTxtPat;
				$strLungProblemTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_lung_problem"],"pat","~|~");
				$retVal["desc_lung_problem"]= $strLungProblemTxtPat;
				$strStrokeTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_stroke"],"pat","~|~");
				$retVal["desc_stroke"]= $strStrokeTxtPat;
				$strThyroidProbTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_thyroid_problems"],"pat","~|~");
				$retVal["desc_thyroid_problems"]= $strThyroidProbTxtPat;
				$strUclearTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_ulcers"],"pat","~|~");
				$retVal["desc_ulcers"]= $strUclearTxtPat;
				$strCancerTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_cancer"],"pat","~|~");
				$retVal["desc_cancer"]= $strCancerTxtPat;
				$strHeartProbTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_heart_problem"],"pat","~|~");
				$retVal["desc_heart_problem"]= $strHeartProbTxtPat;
				$strLDLTxtPat = $this->get_set_pat_rel_values_retrive($row["desc_LDL"],"pat","~|~");
				$retVal["desc_LDL"]= $strLDLTxtPat;
				$strOthersTxtPat = $this->get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");
				$retVal["any_conditions_others"]= $strOthersTxtPat;
				$retVal["genMedComments"]= $row["genMedComments"]; 
				$retVal["Other_case"]= $row["any_conditions_others_both"]; 
				//Review Of System				
				$review_const_arr=explode(" ",trim(str_replace(","," ",$row["review_const"])));
				$review_head_arr=explode(" ",trim(str_replace(","," ",$row["review_head"])));
				$review_resp_arr=explode(" ",trim(str_replace(","," ",$row["review_resp"])));
				$review_card_arr=explode(" ",trim(str_replace(","," ",$row["review_card"])));
				$review_gastro_arr=explode(" ",trim(str_replace(","," ",$row["review_gastro"])));
				$review_genit_arr=explode(" ",trim(str_replace(","," ",$row["review_genit"])));
				$review_aller_arr=explode(" ",trim(str_replace(","," ",$row["review_aller"])));
				$review_neuro_arr=explode(" ",trim(str_replace(","," ",$row["review_neuro"])));
				$negChkBxArr = explode(',',$row["negChkBx"]);
				
				$arrROS = array("Constitutional" => array("arr"=>$review_const_arr,"arrNames"=>array("Fever","Weight Loss","Rash", "Skin Disease", "Fatigue"), "Other"=>$row["review_const_others"]),
						        "Ear, Nose, Mouth & Throat" => array("arr"=>$review_head_arr,"arrNames"=>array("Sinus Infection", "Post Nasal Drips", "Runny Nose","Dry Mouth","Deafness"), "Other"=>$row["review_head_others"]),
							"Respiratory" => array("arr"=>$review_resp_arr,"arrNames"=>array("Cough","Bronchitis","Shortness of Breath","Asthma","Emphysema","COPD","TB"), "Other"=>$row["review_resp_others"]),
							"Cardiovascular" => array("arr"=>$review_card_arr,"arrNames"=>array("Chest Pain","Congestive Heart Failure","Irregular Heart beat","Shortness of Breath","High Blood Pressure", "Low Blood Pressure", "Pacemaker/defibrillator"), "Other"=>$row["review_card_others"]),
							"Gastrointestinal" => array("arr"=>$review_gastro_arr,"arrNames"=>array("Vomiting","Ulcers","Diarrhea","Bloody Stools","Hepatitis","Jaundice","Constipation"), "Other"=>$row["review_gastro_others"]),
							"Genitourinary" => array("arr"=>$review_genit_arr,"arrNames"=>array("Genital Ulcers","Discharge","Kidney Stones","Blood in Urine"), "Other"=>$row["review_genit_others"]),
							"Allergic/Immunologic" => array("arr"=>$review_aller_arr,"arrNames"=>array("Seasonal Allergies","Hay Fever"), "Other"=>$row["review_aller_others"]),
							"Neurological" => array("arr"=>$review_neuro_arr,"arrNames"=>array("Headache","Migraines","Paralysis Fever","Joint Ache","Seizures","Numbness","Faints","Stroke","Multiple Sclerosis","Alzheimer's Disease","Parkinson's Disease","Dementia"), "Other"=>$row["review_neuro_others"]) ,
							"negChkBx" => array("arr"=>$negChkBxArr,"arrNames"=>array("Constitutional","Ear, Nose, Mouth & Throat","Respiratory","Cardiovascular","Gastrointestinal","Genitourinary","Allergic/Immunologic","Neurological","Integumentary","Psychiatry","Hemotologic/Lymphatic","Musculoskeletal","Endocrine", "Eyes")) 
							);
				//ros --
				$review_sys = $row["review_sys"];
				if(!empty($review_sys)){
					$ar_review_sys = json_decode($review_sys, true);
					$ar_tmp = array('Integumentary'=>array('review_intgmntr', array("Rashes", "Wounds", "Breast Lumps","Eczema","Dermatitis")),	
								'Psychiatry'=>array('review_psychiatry', array("Depression", "Anxiety", "Paranoia", "Sleep Patterns","Mental and/or emotional factors", "Alzheimer's Disease", "Parkinson's disease","Memory Loss")), 
								'Hemotologic/Lymphatic'=>array('review_blood_lymph', array("Anemia", "Blood Transfusions", "Excessive Bleeding", "Purpura", "Infection")),
								'Musculoskeletal'=>array('review_musculoskeletal', array("Pain", "Joint Ache", "Stiffness", "Swelling","Paralysis Fever")),
								'Endocrine'=>array('review_endocrine', array("Mood Swings", "Constipation", "Polydipsia","Hypothyroidism","Hyperthyroidism")),
								'Eyes'=>array('review_eye', array("Vision loss", "Eye pain", "Double vision", "Headache")));
					foreach($ar_tmp as $k => $arv){
						$v = $arv[0];
						$tmpar = array();
						if(isset($ar_review_sys[$v])){
							$artmp = explode(" ",trim(str_replace(","," ",$ar_review_sys[$v])));
							$tmpar["arr"] = $artmp;
							$tmpar["arrNames"] = $arv[1];
						}
						//
						$vother = $v."_others";
						if(isset($ar_review_sys[$vother])){				
							$tmpar["Other"] = $ar_review_sys[$vother];
						}
						
						if(count($tmpar)){
							$arrROS[$k] = $tmpar;
						}
					}			
				}

				$arrPtROS=array();
				foreach($arrROS as $key => $val){
					$tmp = $val["arr"];
					$tmpName = $val["arrNames"];
					$otherTmp = $val["Other"];
					
					$arrPtROS[$key]=array();
					if( count($tmp) > 0 ){
						foreach($tmp as $keyTmp => $valTmp){
							if(!empty($tmpName[$valTmp-1])){
								$arrPtROS[$key][]=$tmpName[$valTmp-1];
							}
						}
					}
					
					if(!empty($otherTmp)){
						$arrPtROS[$key][]=$otherTmp;
					}
				}
				
				ksort($arrPtROS);
				
				$retVal["ROS"]= $arrPtROS;
			}
		}
		return (empty($retVal)) ? false: $retVal;
	}
	
	//get daibetic info
	public function getDiabMedInfo($flagDiab=false){
		$pid = $this->pid;
		$delimiter = '~|~';
		$rvs = "";		
		//General Medicine
		$sql = "select any_conditions_you,any_conditions_relative,desc_u,desc_r,diabetes_values from general_medicine where patient_id='".$pid."' ";
		$res = sqlQuery($sql);
		if($res != false){
			$any_conditions_u1_arr1=explode(" ",trim(str_replace(","," ",$res["any_conditions_you"])));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr1);$epr++){				
			//Diabeties
			if(in_array("3",$any_conditions_u1_arr1)){
				//$rvs.="\nDiet ".date("m-Y");
				$strDiabetesIdTxtPat =  $this->get_set_pat_rel_values_retrive($res["diabetes_values"],'pat',$delimiter);				
				$rvs.="Yes ".date("m-Y")." ".$strDiabetesIdTxtPat;
				//$rvs.="\nDiabetes ";				
				//$ptInfoDiaDesc="Diabetes";
				$strDiabetesTxtPat = "";
				$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($res["desc_u"],"pat");										
				$ptInfoDiaDesc=(!empty($strDiabetesTxtPat))? $ptInfoDiaDesc." ".stripslashes($strDiabetesTxtPat) : "";
				$rvs = ($flagDiab) ? "\n".$ptInfoDiaDesc : $rvs."\n".$ptInfoDiaDesc;				
			}else {
				$rvs = "No";
			}
		}
		$rvs = nl2br($rvs);
		
		$allArr = array($rvs);
		return $allArr;
	}
	
	//get ocu nm w/ eye
	function getOcuMedP($str){
		$str=$str." ";
		$ar=array("OU", "OD", "OS", "PO");
		$ret="";
		foreach($ar as $key => $val){		
			if(empty($ret)){
				$indx = strpos($str, " ".$val." ");
				if($indx!==false){		
					$ret=substr($str,0,$indx);				
					$ret.=" (".$val.") ";
					$ret=trim($ret);
				}	
			}
		}
		return $ret;
	}
	
	function getCommonNoMedicalHistory($moduleName){
		$pid = $this->pid;
		$returnVal="";
		if(!empty($moduleName)){	
			$selectQuery="select common_id, no_value,comments from commonNoMedicalHistory where patient_id='".$pid."' and module_name='".$moduleName."'";
			$resultQuery=imw_query($selectQuery)or die(imw_error());		
			if($resultQuery){
				$numRows=imw_num_rows($resultQuery);
				 if($numRows>0){
					$reslutRow=imw_fetch_array($resultQuery);
					if(trim($reslutRow["no_value"])!="")
					{
						$returnVal="checked";//will check the check box//
					}
				}
			}
		}
		return $returnVal;
	}
	
	function getAllergies($sel=" * ", $retbool=0, $st="")
	{
		$patientId= $this->pid;
		if($retbool==0){
			$sql = "select ".$sel." from lists where pid='".$patientId."' and type in(3,7) ";
			if(!empty($st)) $sql.=" AND allergy_status = '".$st."' ";
			$rez = sqlStatement($sql);
			return $rez;
		}else{
			$ret="0";
			$sql = "select count(title) as num from lists
					where pid='".$patientId."'
					and type in(3,7)
					and trim(title) != '' AND UCASE(title)!='NKA' AND UCASE(title)!='NKDA'
					and allergy_status != 'Deleted' AND allergy_status != 'Suspended'  ";
			$row = sqlQuery($sql);			
			if($row != false && $row["num"]>0){
				$ret="1";
			}
			
			//get class
			if($retbool==2){
				$allergy = $ret;
				$checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
				if($checkAllergy == "checked"){
					$allergy = "2";	  // means NKA checkbox is checked
				}
				
				if($allergy=="0"){
					$ret = "NoData";
				}
				if($allergy=="1"){
					$ret="Allergic";
				}
				if($allergy=="2"){
					$ret="NKAllergy";
				}
			}			
			
			return $ret;
		}
	}
	
	//Get Allergies
	function getAllergies_v2(){
		$strAllergies = "";
		$strAllergyReaction = "";
		$rez = $this->getAllergies("title, comments",0,"Active");
		for($i=1;$row = sqlFetchArray($rez);$i++)
		{
			if(!empty($row["title"]))
			{
				$strAllergies .= $row["title"]."";
				$strAllergies .= (!empty($row["comments"])) ? " - ".$row["comments"] : "";
				$strAllergies .="<br>";
			}
		}
		return $strAllergies;
	}
	
	function setPtMedHxReviewed( $formId ){
		$sql = "UPDATE chart_master_table SET update_date = '".wv_dt('now')."', ptMedHxReviewed = '1' WHERE id = '".$formId."' ";
		$row = sqlQuery($sql);
		return 0;
	}
	
	function setPtLExam($mId=""){
		$cls_notifications = new core_notifications();
		//global $cls_notifications;
		$operator_id = (!empty($_SESSION['res_fellow_sess'])) ? $_SESSION['res_fellow_sess'] : $_SESSION['authId'];
		$patient_id = $this->pid; //$_SESSION['patient'];
		$section = 'complete';
		$now = date('Y-m-d H:i:s');
		$medhx_formid = $this->fid; //$_SESSION['form_id'];
		//if(empty($medhx_formid)){$medhx_formid = $_SESSION['finalize_id'];}
		$qrySelPatLastExamined = "select patient_last_examined_id,section_name from patient_last_examined
							where patient_id = '".$patient_id."' and operator_id = '".$operator_id."' and save_or_review = '1'";
		$rsSelPatLastExamined = imw_query($qrySelPatLastExamined);
		if($rsSelPatLastExamined){
			if(imw_num_rows($rsSelPatLastExamined) > 0){
				$qryInsertPatLastExamined = "insert into patient_last_examined set patient_id = '".$patient_id."',operator_id = '".$operator_id."',section_name = 'complete',created_date = '".$now."',status = '0',save_or_review = '2',formid='".$medhx_formid."'";
				$rsInsertPatLastExamined = imw_query($qryInsertPatLastExamined);
				$patLastExaminedInsertId = imw_insert_id();
				while($rowSelPatLastExamined = imw_fetch_array($rsSelPatLastExamined)){
					$masterPatLastExamId = $rowSelPatLastExamined['patient_last_examined_id'];
					$sectionName = $rowSelPatLastExamined['section_name'];
					$qryUpdatePatLastExamined = "update patient_last_examined set created_date = '".$now."',save_or_review = '2',section_name = '".$sectionName."', section_complete = '1',section_complete_id = '".$patLastExaminedInsertId."',formid='".$medhx_formid."' where patient_last_examined_id = '".$masterPatLastExamId."'";
					$rsUpdatePatLastExamined = imw_query($qryUpdatePatLastExamined);
				}			
			}else{
				$sql = "insert into patient_last_examined set patient_id = '".$patient_id."',operator_id = '".$operator_id."',section_name = '".$section."',created_date = '".$now."',status = '0',save_or_review = '2',formid='".$medhx_formid."'";
				imw_query($sql);
			}
			imw_free_result($rsSelPatLastExamined);
		}
		$tmp = $this->getPtLExamInfo();
		//Set Chart notes Pt Med reviewed
		if( isset($mId) && !empty($mId) ){
			$a = $this->setPtMedHxReviewed( $mId );
		}
		$cls_notifications->update_medHx_status();//updating iconbar status.
		$cls_notifications->update_sxicon_status();//updating iconbar status.
		$cls_notifications->update_vitalSign_status();//updating iconbar status.
		return $tmp;
	}
	
	function get_medHx_RevwdBy(){
		$ret = "";
		$ar_uni = array();
		$qry = "select patient_last_examined_id, operator_id, section_name,
			date_format(created_date,'".get_sql_date_format()."') as createdDate, time_format(created_date,'%h:%i %p') as createdTime,
			section_complete_id
		from patient_last_examined 
		where patient_id = '".$this->pid."' AND formid='".$this->fid."'  and (save_or_review = '2') 
		order by created_date desc"; // 
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);
		$inx=0;
		while($row = imw_fetch_assoc($sql))
		{	
			$section_name = trim($row["section_name"]);
			//if($section_name == "complete"){
				$patientLastExaminedId = $row['patient_last_examined_id'];
				$operator_id = $row['operator_id'];
				$date_time = $row['createdDate']."  ".$row["createdTime"];
				$sectionCompleteId=$row["section_complete_id"];
				$proName = "";
				if(!empty($operator_id) ){ //&& !in_array($operator_id, $ar_uni)
					$ousr = new User($operator_id);
					$proName = $ousr->getName(3);
					$patLastExaminedIdForComp="";
					if(($sectionCompleteId == 0 && $section_name != "complete"))
					{
						$patLastExaminedIdForComp=$patientLastExaminedId.",";
					}
					elseif($section_name == "complete")
					{	
						$query = "SELECT patient_last_examined_id FROM patient_last_examined WHERE patient_id = '".$this->pid."' and (save_or_review = '2') and section_complete_id = '".$patientLastExaminedId."' ORDER BY created_date Desc "; //
						//echo "<br>".$query;
						$rez = sqlStatement($query);
						for($i=1; $row1 = sqlFetchArray($rez); $i++){
							if(!empty($row1["patient_last_examined_id"])){								
								$patLastExaminedIdForComp.= $row1["patient_last_examined_id"].",";
							}
						}
					}
					
					if((($sectionCompleteId == 0 && $section_name != "complete"))||($section_name == "complete")){//
						$patLastExaminedIdForComp = substr(trim($patLastExaminedIdForComp), 0, -1);
						//if(!empty($patLastExaminedIdForComp)){
							$on_click = " onClick='show_review_detail(this)' ";
						//}
						$inx++;
						$ret .= "<tr><td>".$inx.".</td><td><a href=\"#\" class='text-nowrap' ".$on_click." data-lstid='".$patLastExaminedIdForComp."' data-scnm='".$section_name."' data-opid='".$operator_id."' data-dtm='".$date_time."' >".$proName."</a></td><td> ".$date_time."</td></tr>";
						$ar_uni[] = $operator_id;
					}
				}
			//}
		}
		if(empty($ret)){$ret="<tr><td>No record found!</td></tr>";}
		if(!empty($ret)){
			$ret = "<div id=\"dvMedRvwBy\" class=\"panel panel-primary\"  onMouseOver='top.showMedReview(1)' onMouseOut='top.showMedReview(0)'>
					<div class=\"panel-heading\">Medical Hx. reviewed by:</div>
					<div class=\"panel-body\"><table class='table table-striped table-bordered'>".$ret."</table></div>
				</div>";
		}
		
		echo $ret;
	}
}
?>