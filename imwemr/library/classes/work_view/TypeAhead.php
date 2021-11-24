<?php
class TypeAhead{
	private $term;
	private $lastTerm;
	public $glb_stg_cd_ar;
	
	public function __construct(){
		$this->term = "";
		$this->lastTerm = "";
		$this->glb_stg_cd_ar=array();
	}
	
	function getTypeAheadLastTerm($term){
		$lastterm="";
		$arr = explode(" ", $term);
		$ln=	count($arr);
		if($ln > 1){
			$lastterm="".$arr[$ln-1];
			
			$ln = strlen($lastterm);		
			if($ln<2){ 	if(!empty($arr[$ln-2]) && !empty($arr[$ln-1])){$lastterm="".$arr[$ln-2]." ".$arr[$ln-1];} }
		}	
		
		return $lastterm;
	}
	
	function getFUVisit(){
		$arrFuVist = array();		
		$lastTerm = $this->term; //!empty($lastTerm) ? $lastTerm : $term ;	
		$wh = $_REQUEST["wh"];
		if(strpos($wh, "elem_followUpNumber")!==false){
			$arrFuVist = array('1','2','3','4','5','6','7','8','9','10','Today','Calendar','PRN','PMD','-');
		}else{
			$oAdmn = new Admn();
			$tmp = $oAdmn->getFuOptions(0,1,$lastTerm);	
			if(count($tmp) > 0){
				$arrFuVist = array_merge($arrFuVist,$tmp);
			}
		}
		echo json_encode($arrFuVist);
		exit();
	}
	
	function get_refPhy(){
		$ret_tmp = array();
		$term = $this->term;
		$lastTerm = $this->lastTerm;
		if(!empty($lastTerm)&&1==2){ $lastTermPhrase = " OR LastName like '".$lastTerm."%' ";   }else{ $lastTermPhrase=""; }
		
		$arterm = explode(",", $term);
		$term = $arterm[0];
		if(isset($arterm[1]) && trim($arterm[1])!=""){
			$lastTermPhrase = " OR FirstName like '".$arterm[1]."%' ";
		}
		
		//RE: Transition of Care / Refer To: Stop Logged in user based on NPI
		$usr = new User();
		$usinf = $usr->get_user_info();
		$npi = (is_array($usinf) && !empty($usinf[5])) ? trim($usinf[5]) : "" ;
		
		//--
		$addrs_field_p="";	
		if($_GET["w"]=="6"){
			$addrs_field_p = " Address1,Address2,ZipCode,
					City,State,	physician_phone, "; 
		}
		//--	
		
		$sql = "select physician_Reffer_id,Title,FirstName,MiddleName,LastName,	".$addrs_field_p.							
					"physician_fax, NPI from refferphysician  where delete_status = 0  
						and (LastName like '".$term."%' ".$lastTermPhrase.")
						and primary_id = 0 
						order by LastName asc, FirstName asc  ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			//RE: Transition of Care / Refer To: Stop Logged in user based on NPI
			if(!empty($npi) && trim($row["NPI"]) == $npi){ continue; }
			
			//$ret[] = $row[""];
			$name="";
			if($GLOBALS["REF_PHY_FORMAT"] != 'Boston'||$GLOBALS["REF_PHY_FORMAT"]==""){						
				if($row["LastName"] != ''){$name .= $row["LastName"].', ';}						
				if($row["FirstName"] != ''){$name .= $row["FirstName"].' ';}
				if($row["MiddleName"] != ''){$name .= $row["MiddleName"];}
			}else{					
				$name = $row["LastName"].", ".$row["FirstName"]." ";
				if($row["MiddleName"]!=''){ $name .= $row["MiddleName"]." "; }						
				$name .= $row["Title"];
			}
			
			if(!empty($name)){
				$inarr = array("label"=>$name,"refid"=>$row["physician_Reffer_id"]);//,"fax"=>$row["physician_fax"]
				if($addrs_field_p!=""){
					$strAdd ="";
					if(!empty($row["Address1"])){ if(!empty($strAdd)){$strAdd.=", ";} $strAdd .= $row["Address1"];}
					if(!empty($row["Address2"])){ if(!empty($strAdd)){$strAdd.=", ";} $strAdd .= $row["Address2"];}
					if(!empty($row["City"])){ if(!empty($strAdd)){$strAdd.=", ";} $strAdd .= $row["City"];}
					if(!empty($row["State"])){ if(!empty($strAdd)){$strAdd.=", ";} $strAdd .= $row["State"];}
					if(!empty($row["ZipCode"])){ if(!empty($strAdd)){$strAdd.=", ";} $strAdd .= $row["ZipCode"];}
					$inarr["address"] = $strAdd;							
				}
				$ret_tmp[] = $inarr;
			}
		}
		
		
		/*
		$xml = $GLOBALS['fileroot']."/xml/refphy/".$fnm.".xml";
		
		if(file_exists($xml)){
			$refphydata = simplexml_load_file($xml);	
			foreach($refphydata->refPhyInfo as $refphy){
					
				$refphy->refphyId;	
					
					
			}					
		}
		*/
		
		/*
		$w = $_GET["w"]; 
		if($w == "2"){					
			
		}else if($w == "3"){					
			
		}else if($w == "4"){					
			
		}else{					
			
		}
		*/
		
		//echo json_encode($ret);
		//exit();	
		return $ret_tmp;
	}
	
	//get type ahead
	function getTypeAheadStr_ocuMeds($providerId,$srch="",$srch2="")
	{
		$phrase="";
		if(!empty($srch)){	
			if(!empty($srch2)&&1==2){ $lastTermPhrase = " OR medicine_name like '".$srch2."%' ";   }else{ $lastTermPhrase=""; }		
			$phrase = " AND ( medicine_name LIKE '".$srch."%' ".$lastTermPhrase." ) ";
		}
		
		$retStr = array();
		$sql = "SELECT distinct(medicine_name) FROM medicine_data ".
			   "WHERE (provider_id = '".$providerId."' || provider_id = 0) AND ocular = 1 AND del_status = '0' ".$phrase.
			   "ORDER BY provider_id DESC , medicine_name ";
			   
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$retStr[] = "".removeLineBreaks(str_replace(array("'","\n","\r"),array("\'","\\n","\\r"),$row["medicine_name"]))."";
		}
		return $retStr;
		//return !empty($retStr) ? substr($retStr,0,-1) : "";
	}	
	
	function icd10_getLSSDesc($ar,$icd10){
		//global $diff_stage_code_arr;
		$diff_stage_code_arr = $this->glb_stg_cd_ar;
		$ret_str="";
		$lat = $ar["lat"];
		$sever = $ar["sever"];
		$stage = $ar["stage"];
		$no_bilateral = $ar["no_bilateral"];	
		
		if($lat>0){ 
			$query2 = "SELECT title, code  FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0";
			$res2	= imw_query($query2);		
			while ($rs2 = imw_fetch_assoc($res2)){
				if($no_bilateral==1 && $rs2['title']=="Both Eyes"){
				}else{				
					if(!empty($ar["clat1"]) && $ar["clat1"] == $rs2["code"]){
						$ret_str .= $rs2['title']." "; 
					}
				}
			}
		}
		if($sever>0){
			if(count($diff_stage_code_arr[$icd10])>0){
				foreach($diff_stage_code_arr[$icd10] as $key => $val){

					$val_exp=explode(' - ',$val);
					if(!empty($ar["clat2"]) &&  trim($ar["clat2"]) == trim($val_exp[1])){
						$ret_str .= $val_exp[0]." "; 
					}
				}
			}else{ 
				$query2 = "SELECT title, code FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
				$res2	= imw_query($query2);		
				while ($rs2 = imw_fetch_assoc($res2)){	
					if(!empty($ar["clat2"]) &&  $ar["clat2"] == $rs2["code"]){
						$ret_str .= $rs2['title']." "; 
					}
				}
			}
		}
		if($stage>0){ 
			$query2 = "SELECT title, code FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
			$res2	= imw_query($query2);			
			while ($rs2 = imw_fetch_assoc($res2)){	
				if(!empty($ar["clat2"]) &&  $ar["clat2"] == $rs2["code"]){
					$ret_str .= $rs2['title']." "; 
				}
			}
		}
		$ret_str=trim($ret_str);
		return $ret_str;	
	}
	
	function icd10_isFullDxCode($q, $ar_cm_dx=array()){
		//global $ar_cm_dx;
		//if(strpos($q,"-")!==false){return $q;}
		$icd10_t=$icd10_desc="";
		$icd10_t_code_nolss=0;
		
		$sql = "SELECT id,laterality,staging,severity,icd10,icd10_desc, no_bilateral FROM icd10_data WHERE (icd10 LIKE '$q') AND deleted=0  ";
		$rez = sqlStatement($sql);
		$ln = imw_num_rows($rez);
		if($ln<=0){		
			$q1=substr($q,0,-1)."-";
			$q2=substr($q,0,-2)."--";
			$q3=substr($q,0,-3)."-x-";
			
			//--	
			//if(stripos($q, "H54.41")!==false||stripos($q, "H54.51")!==false){
				$q4=substr($q,0,-2)."-a";
			//}	
			//--
			
			$sql = "SELECT id,laterality,staging,severity,icd10,icd10_desc, no_bilateral FROM icd10_data WHERE (icd10 LIKE '$q' OR icd10 LIKE '$q1' OR icd10 LIKE '$q2' or LOWER(icd10) LIKE '".strtolower($q3)."' or LOWER(icd10) LIKE '".strtolower($q4)."') AND deleted=0  $icd_dos_whr";
			$row = sqlQuery($sql);
		}else{
			$row = sqlFetchArray($rez);	
		}
		
		if($row!=false){
		
			if((!empty($row["laterality"]) && $row["laterality"]!="NA") || !empty($row["staging"]) || !empty($row["severity"]) ){
				$icd10_t = $row["icd10"]; //is full code with lss
			}else{
				$icd10_t_code_nolss=1;
			}
			
			//Exceptions
		
			//if($row["icd10"]=="H54.0X--"){
			if(in_array($row["icd10"], $ar_cm_dx)){
				$icd10_t = $row["icd10"];
				$icd10_t_code_nolss=0;
			}
			
			//
			if(!empty($row["icd10_desc"])){
				$icd10_desc = $row["icd10_desc"];
				
				//insert LSS values
				$ar=array();
				$ar["lat"]	= intval($row['laterality']);
				$ar["stage"]	= intval($row['staging']);
				$ar["sever"]	= intval($row['severity']);
				$ar["no_bilateral"]	= $row['no_bilateral'];
				
				if(strtoupper($q1)==strtoupper($icd10_t)){
					$ar["clat1"] = substr($q, -1);	
				}else if(strtoupper($q2)==strtoupper($icd10_t)){
					$clat = substr($q, -2);
					$ar["clat1"] = substr($clat, 0,1);
					$ar["clat2"] = substr($clat, 1,1);
					
				}else if(strtoupper($q3)==strtoupper($icd10_t)){
					$clat = substr($q, -3);
					$ar["clat1"] = substr($clat, 0,1);
					$ar["clat2"] = substr($clat, -1);
				}
				
				$str_lss_desc = $this->icd10_getLSSDesc($ar,$row["icd10"]);
				if(!empty($str_lss_desc)){ $icd10_desc=$icd10_desc."; ".$str_lss_desc;  }
			}
		}
		
		return array($q,$icd10_t,$icd10_t_code_nolss,$icd10_desc);
	}
	
	function getIC10Data(){	
		$diff_stage_code_arr['H35.31--']=array('Early - 1','Intermediate - 2','Adv atrophic w/o subfoveal Involvement - 3','Adv atrophic w subfoveal Involvement - 4');
		$diff_stage_code_arr['H35.32--']=array('Active neovascularization - 1','Inactive neovascularization - 2','Inactive scar - 3');
		$diff_stage_code_arr['H34.81--']=array('w/ macular edema - 0','w/ret neovascularization - 1','stable - 2');
		$diff_stage_code_arr['H34.83--']=array('w/ macular edema - 0','w/ret neovascularization - 1','stable - 2');
		//
		$this->glb_stg_cd_ar = $diff_stage_code_arr;
		
		//Blindness--
		$ar_cm_types=array("1"=>array("Right eye category 3 - 3", "Right eye category 4 - 4", "Right eye category 5 - 5"),
							"2"=>array("Left eye category 3 - 3", "Left eye category 4 - 4", "Left eye category 5 - 5"),
							"3"=>array("Right eye category 1 - 1", "Right eye category 2 - 2"),
							"4"=>array("Left eye category 1 - 1", "Left eye category 2 - 2"));
		$ar_icd_10_cm = array("H54.0X--"=>array( "0"=>$ar_cm_types["1"], "1"=>$ar_cm_types["2"] ), 
						"H54.11--"=>array( "0"=>$ar_cm_types["1"], "1"=>$ar_cm_types["4"] ), 
						"H54.12--"=>array( "0"=>$ar_cm_types["3"], "1"=>$ar_cm_types["2"] ), 
						"H54.2X--"=>array( "0"=>$ar_cm_types["3"], "1"=>$ar_cm_types["4"] ), 
						"H54.41-A"=>array( "0"=>$ar_cm_types["1"], "1"=>array() ), 
						"H54.42A-"=>array( "1"=>$ar_cm_types["2"], "0"=>array() ), 
						"H54.51-A"=>array( "0"=>$ar_cm_types["3"], "1"=>array() ), 
						"H54.52A-"=>array( "1"=>$ar_cm_types["4"], "0"=>array() ));
		$ar_cm_dx = array_keys($ar_icd_10_cm);				
		//Blindness--
		
		
		
		$icd_dos_whr="";
		if(strpos($_REQUEST['chart_dos'],'-')==true){
			$chart_dos=wv_formatDate($_REQUEST['chart_dos'],0,0,'insert');
			if($chart_dos>='2016-10-01'){
				$icd_dos_whr=" and status!=1";
			}else{
				$icd_dos_whr=" and status!=2";
			}
		}
		
		$getLaterality = $getStageSever = false;
		$q = strtolower($_GET["term"]);


		//check icd10 dxcode if it is full code, then show Laterality etc options : in workview+superbill--
		if(!empty($_GET["show_pop"])){ 
			// if multiple
			$q_multi="";
			if(!empty($q) && strpos($q,",")!==false){
				$q_multi=$q;
				$arr_q=explode(",",$q);
				$tmp_inx = count($arr_q);
				$q=trim($arr_q[$tmp_inx-1]);//last word
			}

			list($q_tmp, $qdb_tmp,$dx_no_lss_tmp,$qdesc_tmp) = $this->icd10_isFullDxCode($q, $ar_cm_dx);
			if(!empty($qdb_tmp)){ $q = $qdb_tmp; }
		}
		//end--

		$q_arr = explode('>>',$q);
		if(strpos($q,'.')){
			$LatPos = strpos($q,'-');
			$SevPos = strrpos($q,'-');
		}else{
			$LatPos = 0;
			$SevPos = 0;
		}
		$flg_LitSev=0;
		
		if(strlen($q)==8 && (stripos($q, "E08.")!==false || stripos($q, "E09.")!==false || stripos($q, "E10.")!==false || stripos($q, "E11.")!==false || stripos($q, "E13.")!==false)){
			if((stripos($q, "E10.35")!==false || stripos($q, "E11.35")!==false)){
				if(strpos($q,'.')){
					$LatPos = strrpos($q,'-');
					$SevPos = strpos($q,'-');
				}else{
					$LatPos = 0;
					$SevPos = 0;
				}
				if($SevPos>0 && $SevPos<7){
					$getStageSever = true;
				}else if($LatPos>6){
					$getLaterality = true;
				}
			}else{
				$getLaterality = true;
			}
		}else if($LatPos>0 && $LatPos<7){
			$getLaterality = true;
		}else if($SevPos>6){
			$getStageSever = true;
		}//var_dump($getLaterality);var_dump($getStageSever);
		
		if(count($q_arr) > 1 && $getLaterality==false && $getStageSever==false){
			$qc = count($q_arr) - 2;
			$q = $q_arr[$qc];
			$q = "SELECT id FROM icd10_data WHERE icd10_desc LIKE '$q' AND deleted=0 $icd_dos_whr";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				$id = '';
				while($rs = imw_fetch_assoc($res)){
					 $id .= $rs['id'].',';
				}
				$id = substr($id,0,-1);
				$query = imw_query("SELECT icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10 FROM icd10_data WHERE parent_id IN ($id) AND deleted=0 $icd_dos_whr ORDER BY icd10_desc");
				$results = array();
				while ($row = imw_fetch_assoc($query)){
					if($row['icd10']==''){
						$results[] = $row['icd10_desc'].'>>';
					}else{
						$results[] = $row['icd10_desc'].$row['code'];
					}
				}		
			}			
		}else if($getLaterality==true || $getStageSever==true){
			if(!empty($_GET["show_pop"])){
				$flg_LitSev=1;
				$icd10_dxcode_db="";
				$results = array();
				$query	= "SELECT laterality,staging,severity,icd10,no_bilateral  FROM icd10_data WHERE icd10 = '$q' AND deleted=0 $icd_dos_whr LIMIT 0,1";
				$res	= imw_query($query);
				$rs	= imw_fetch_assoc($res);
				$lat	= intval($rs['laterality']);
				$stage	= intval($rs['staging']);
				$sever	= intval($rs['severity']);
				$icd10_dxcode_db = $rs['icd10'];
				$no_bilateral	= $rs['no_bilateral'];
				$results[0]=$results[1]=$results[2]=array();
				
				if(in_array($icd10_dxcode_db, $ar_cm_dx)){
					$results[0] = $ar_icd_10_cm[$icd10_dxcode_db][0];
					$results[1] = $ar_icd_10_cm[$icd10_dxcode_db][1];
				}else{
				
				if($lat>0){ 
					$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0";
					$res2	= imw_query($query2);
					
					while ($rs2 = imw_fetch_assoc($res2)){
						if($no_bilateral==1 && $rs2['name']=="Both Eyes - 3"){
						}else{
							$results[0][] = $rs2['name']; 
						}
					}
				}
				if($sever>0){ 
					if(count($diff_stage_code_arr[$q])>0){
						foreach($diff_stage_code_arr[$q] as $val){
							$results[1][] = $val; 
						}
					}else{
						$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
						$res2	= imw_query($query2);
						
						while ($rs2 = imw_fetch_assoc($res2)){	
							$results[1][] = $rs2['name']; 
						}
					}
				}
				if($stage>0){ 
					$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
					$res2	= imw_query($query2);			
					while ($rs2 = imw_fetch_assoc($res2)){	
						$results[2][] = $rs2['name']; 
					}
				}
				/*
				if($lat<=0 && $sever<=0 && $stage<=0){ 
				
					$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE deleted=0 AND under>0";
					//echo $query2;die;
					$res2	= mysql_query($query2);
					
					while ($rs2 = mysql_fetch_assoc($res2)){	
						$results[0][] = $rs2['name']; 
					}
					
				}
				*/
				}
			}else{

				if($getLaterality==true){
					$query	= "SELECT laterality,staging,severity,no_bilateral FROM icd10_data WHERE icd10 = '$q' AND deleted=0 $icd_dos_whr LIMIT 0,1";
					$res	= imw_query($query);
				}	
				if($getStageSever==true){
					$or_q="";
					if(strlen($q)==8 && substr_count($q,'-')==1){
						$or_q=" or icd10='".$q."'";
					}
					/*--Replace here Laterality character which is before 7th position--*/
					if(strtolower($q[6])!='x' && strtolower($q)!='e11.351-' && strtolower($q)!='h54.52a-' && strtolower($q)!='h54.42a-') $q[6]='-';
					$query	= "SELECT staging,severity FROM icd10_data WHERE (icd10 = '$q' $or_q) AND deleted=0 $icd_dos_whr LIMIT 0,1";
					$res	= imw_query($query);
					if($res && imw_num_rows($res)==0){
						if(strtolower($q[6])=='x'){
							$q[5]='-';
						}
						
						$query	= "SELECT staging,severity FROM icd10_data WHERE icd10 = '$q' AND deleted=0 $icd_dos_whr LIMIT 0,1";
						$res	= imw_query($query);	
					}
				}
				if($res){
					$rs		= imw_fetch_assoc($res);
					$stop_both_qry="";
					if($getLaterality==true){
						$lat	= intval($rs['laterality']);
						if($rs['no_bilateral']>0){
							$stop_both_qry=" and code!='3'";
						}
					}else if($getStageSever==true){
						$stage	= intval($rs['staging']);
						$sever	= intval($rs['severity']);
					}
					//echo $lat.'-'.$sever.'-'.$stage;die;
					if($lat>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0 $stop_both_qry";
					else if($sever>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
					else if($stage>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
					else $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE deleted=0 AND under>0";
					//echo $query2;die;
					$res2	= imw_query($query2);
					$results = array();
					if(count($diff_stage_code_arr[$q])>0 && $sever>0){
						foreach($diff_stage_code_arr[$q] as $val){
							$results[] = $val;
						}
					}else{
						if($sever=='3' && (stripos($q, "E10.35")!==false || stripos($q, "E11.35")!==false)){
							$results[] = "PDR With ME - 1";
							$results[] = "withTRD macula - 2";
							$results[] = "with TRD no macula - 3";
							$results[] = "with TRD and RD - 4";
							$results[] = "with stable PDR - 5";
							$results[] = "PDR without ME - 9";
						}else if(stripos($q, "H54.")!==false){
							foreach($ar_icd_10_cm as $ar_icd_10_cm_key=>$ar_icd_10_cm_val){
								if(stripos($q, $ar_icd_10_cm_key)!==false){
									if($getStageSever==true){
										foreach($ar_icd_10_cm[$ar_icd_10_cm_key][1] as $ar_icd_10_cm_key2=>$ar_icd_10_cm_val2){
											$results[$ar_icd_10_cm_key2] = $ar_icd_10_cm_val2;
										}
									}else{
										foreach($ar_icd_10_cm[$ar_icd_10_cm_key][0] as $ar_icd_10_cm_key2=>$ar_icd_10_cm_val2){
											$results[$ar_icd_10_cm_key2] = $ar_icd_10_cm_val2;
										}
									}
								}
							}	
						}else{
							while ($rs2 = imw_fetch_assoc($res2)){	
								$results[] = $rs2['name']; 
							}
						}
					}
				}
			}


		}else{	
			
			//$qq = "SELECT icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select distinct(parent_id) from icd10_data where deleted=0) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (icd9_desc LIKE '$q%') OR (icd10_desc LIKE '$q%')) AND deleted=0 ORDER BY parents";
			$qq = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0 $icd_dos_whr) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (TRIM(icd9_desc) LIKE '$q%') OR (TRIM(icd10_desc) LIKE '$q%')) AND deleted=0 AND parent_id=0 $icd_dos_whr group by icd10_desc ORDER BY parents DESC, TRIM(icd10_desc) asc";
			$query = imw_query($qq);
			$results = array();	
			while ($row = imw_fetch_assoc($query)){
				$tmp_res_icd10="";
				if($row['icd10']==''){
					$tmp_res_icd10 = $row['icd10_desc'].'>>';
				}else{
					$tmp_res_icd10 = $row['icd10_desc'].$row['code'];
				}
				$tmp_res_icd10 = trim($tmp_res_icd10);
				if(!empty($tmp_res_icd10) && !in_array($tmp_res_icd10, $results)){ //
					$results[] = array("label"=>$tmp_res_icd10, "value"=>$tmp_res_icd10, "id"=>$row['mainId']);
				}
			}	
			
			//-- ICD 10 checked description for every keyword in description : WV
			if($_REQUEST['callFrom']=="wv"){
			$qq = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0 $icd_dos_whr) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (icd9_desc LIKE '%$q%') OR (icd10_desc LIKE '%$q%')) AND deleted=0 AND parent_id=0 $icd_dos_whr group by icd10_desc ORDER BY parents DESC,icd10_desc asc, icd9_desc asc";
			$query = imw_query($qq);	
			while ($row = imw_fetch_assoc($query)){
				$tmp_res_icd10="";
				if($row['icd10']==''){
					$tmp_res_icd10 = $row['icd10_desc'].'>>';
				}else{
					$tmp_res_icd10 = $row['icd10_desc'].$row['code'];
				}
				$tmp_res_icd10 = trim($tmp_res_icd10);
				if(!empty($tmp_res_icd10) && !in_array($tmp_res_icd10, $results)){ //
					$results[] = array("label"=>$tmp_res_icd10, "value"=>$tmp_res_icd10, "id"=>$row['mainId']);
				}
			}
			}
			//--		
		}

		//
		//$results[]=strpos($_REQUEST['chart_dos'],'-').'----'.$icd_dos_whr.'-'.$_REQUEST['chart_dos'];
		if(!isset($_REQUEST['callFrom']) || $_REQUEST['callFrom']!="wv"){
			if(!empty($_GET["show_pop"])){
				if($dx_no_lss_tmp==1){$results=array();$flg_LitSev=1;}
					echo json_encode(array("results"=>$results,"flg_LitSev"=>$flg_LitSev,"icd10_dxdb"=>$icd10_dxcode_db,"srchd_code"=>$q_tmp,"icd10_dxdesc"=>$qdesc_tmp));
			} else{
				echo json_encode($results);
			}
		}else{
			return array("results"=>$results,"flg_LitSev"=>$flg_LitSev,"icd10_dxdb"=>$icd10_dxcode_db,"srchd_code"=>$q_tmp,"icd10_dxdesc"=>$qdesc_tmp);
		}	
	}
	
	function get_dx_data(){
		$results=array();
		$q = strtolower($_GET["term"]);
		$icd = $_GET["ICD10"];
		// if multiple
		$q_multi="";
		if(!empty($q) && strpos($q,",")!==false){
			$q_multi=$q;
			$arr_q=explode(",",$q);
			$tmp_inx = count($arr_q);
			$q=trim($arr_q[$tmp_inx-1]);//last word
		}
		if(!empty($q)){
		if($icd == 9){
			$qq = "SELECT diag_description, d_prac_code, dx_code,
					CONCAT(diag_description,' [',d_prac_code,', ',dx_code,']') AS label,
					d_prac_code as value
					FROM diagnosis_code_tbl dct
				    WHERE (diag_description LIKE '%".$q."%' OR
						d_prac_code LIKE '".$q."%' OR
						dx_code LIKE '".$q."%' )
						ORDER BY d_prac_code, dx_code, diag_description ";
		}else{
		//
		$icd_dos_whr=" and status!=1";
		//
		$qq = "SELECT id as mainId,icd10_desc, CONCAT(icd10_desc,' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS label,icd10 as value, (select count(id) from icd10_data where parent_id = mainId and deleted=0 $icd_dos_whr) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (icd9_desc LIKE '%$q%') OR (icd10_desc LIKE '%$q%')) AND deleted=0 AND parent_id=0 $icd_dos_whr group by icd10_desc ORDER BY parents DESC,icd10_desc asc, icd9_desc asc";
		}
		$res = imw_query($qq);
		while($rs = imw_fetch_assoc($res)){
			$results[] = array("label"=>$rs['label'], "value"=>$rs['value'], "id"=>$rs['mainId']);
		}
		}
		echo json_encode($results);		
	}
	
	
	//Get Typeahead Dx Codes
	function getTH_dxdropdown_new($tmp="", $srch2="",$flag=""){
		$ar=array();
		if(!empty($tmp)){
			if(!empty($srch2)){ 
				$lastTermPhrase = " OR diag_description LIKE '".$srch2."%' OR
						d_prac_code LIKE '".$srch2."%' OR
						dx_code LIKE '".$srch2."%' ";   
			}else{ $lastTermPhrase=""; }
			
			if($flag == "Assessment"){
				$assesment = " , console_to_do c ";
				$assesmentWhere = " AND diagnosis_code_tbl.diag_description  != c.assessment AND diagnosis_code_tbl.dx_code != c.dxcode  ";
			}else{
				$assesment = '';
				$assesmentWhere = '';
			}
			//org--
			$sql = "SELECT diag_description, d_prac_code, dx_code 
					FROM diagnosis_code_tbl dct
				    WHERE (diag_description LIKE '".$tmp."%' OR
						d_prac_code LIKE '".$tmp."%' OR
						dx_code LIKE '".$tmp."%' ".$lastTermPhrase." )
						ORDER BY d_prac_code, dx_code, diag_description ";
			
			//$ar[] = $sql;	return $ar;  
			$rez = sqlStatement($sql);		
			for($i=0;$row=sqlFetchArray($rez);$i++){
				//*
				$row["d_prac_code"]=trim($row["d_prac_code"]);
				$row["diag_description"] = trim($row["diag_description"]);
				$row["dx_code"] = trim($row["dx_code"]);
				
				//*
				if(!empty($row["d_prac_code"]) && (stripos($row["d_prac_code"],$tmp)!==false||(!empty($srch2) && stripos($row["d_prac_code"],$srch2)!==false)) && !in_array($row["d_prac_code"],$ar) ){	$ar[] = $row["d_prac_code"];	}	//			
				if(!empty($row["diag_description"]) && (stripos($row["diag_description"],$tmp)!==false ||(!empty($srch2) && stripos($row["diag_description"],$srch2)!==false))  && !in_array($row["diag_description"],$ar)){				$ar[] = $row["diag_description"]." \t\r(".$row["dx_code"].")";	}	//
				if(!empty($row["dx_code"]) && (stripos($row["dx_code"],$tmp)!==false ||(!empty($srch2) && stripos($row["dx_code"],$srch2)!==false) )  && !in_array($row["dx_code"],$ar)){			$ar[] = $row["dx_code"]; } 		//
				
				//*/				
			}
			//-
			//add : typeahead should check every keyword in desc--
			if(!empty($srch2)){ 
				$lastTermPhrase = " OR diag_description LIKE '%".$srch2."%' OR
						d_prac_code LIKE '".$srch2."%' OR
						dx_code LIKE '".$srch2."%' ";
			}
			$sql = "SELECT diag_description, d_prac_code, dx_code 
					FROM diagnosis_code_tbl dct
				    WHERE (diag_description LIKE '%".$tmp."%' OR
						d_prac_code LIKE '".$tmp."%' OR
						dx_code LIKE '".$tmp."%' ".$lastTermPhrase." )
						ORDER BY d_prac_code, dx_code, diag_description ";
			$rez = sqlStatement($sql);		
			for($i=0;$row=sqlFetchArray($rez);$i++){
				//*
				$row["d_prac_code"]=trim($row["d_prac_code"]);
				$row["diag_description"] = trim($row["diag_description"]);
				$row["dx_code"] = trim($row["dx_code"]);
				
				//*
				if(!empty($row["d_prac_code"]) && (stripos($row["d_prac_code"],$tmp)!==false||(!empty($srch2) && stripos($row["d_prac_code"],$srch2)!==false)) && !in_array($row["d_prac_code"],$ar) ){	$ar[] = $row["d_prac_code"];	}	//			
				if(!empty($row["diag_description"]) && (stripos($row["diag_description"],$tmp)!==false ||(!empty($srch2) && stripos($row["diag_description"],$srch2)!==false))  && !in_array($row["diag_description"],$ar)){				$ar[] = $row["diag_description"]." \t\r(".$row["dx_code"].")";	}	//
				if(!empty($row["dx_code"]) && (stripos($row["dx_code"],$tmp)!==false ||(!empty($srch2) && stripos($row["dx_code"],$srch2)!==false) )  && !in_array($row["dx_code"],$ar)){			$ar[] = $row["dx_code"]; } 		//
				
				//*/				
			}
			//add--

		}

		return $ar;
	}

	
	function get_assess_mr_opts($mode){
		
		$term=$this->term; 
		$lastTerm=$this->lastTerm;
		$ret = array();
	
		// ICD 10 Code
		//assessment
		$oAdmn = new Admn();
		$strSeting = $oAdmn->getAPPolicySettings();
		$flgNoDx = ($mode=="MR") ? "1" : "0" ;
		
		//
		//1.  Personal A&P should be on Top and then the community.  Currently it randomly shows green and red but mostly red i.e. community shows in the beginning of the list.				
		$oUsrAp=new UserAp();
		$arrAsPhy = $oUsrAp->getAssessmentAndPolicies_physician("Assessment", $term, $lastTerm,$_REQUEST['ICD10'], $flgNoDx, $_REQUEST['chart_dos']);
		if(count($arrAsPhy)>0){
			array_walk($arrAsPhy,"ar_suffix","phy");				
			$ret = array_merge($ret,$arrAsPhy);	
			
		}
		
		if(strpos($strSeting, "Enable Community A&P in Work View")!==false){//
			
			$flgDynamicAP = 0;
			/*
			if(strpos($strSeting, "Enable Dynamic A&P in Work View")!==false){
				$flgDynamicAP = 1;
			}
			*/
			
			$arrAsCommu = $oUsrAp->getAssessmentAndPolicies_community("Assessment", $term, $lastTerm,$_REQUEST['ICD10'], $flgDynamicAP,$flgNoDx,$_REQUEST['chart_dos']);
			if(count($arrAsCommu)>0){
				array_walk($arrAsCommu,"ar_suffix", "commu");
				$ret = array_merge($ret,$arrAsCommu);
			}
		}
		
		
		/*
		7.       Dynamic SMART A&Ps don't work as they should. It constantly creates A&Ps which show up in the chart. They should JUST go to the physician console's dynamic section! Such that the Physician can later decide to click and edit the Dynamic A&P and make it their personal A&P.  It should never go to A&P in Work View of Patient.  No site is using dynamic A&Ps because of the way it works now.
		*/
		/*
		if(strpos($strSeting, "Enable Dynamic A&P in Work View")!==false){//
			$arrAsDyn = getAssessmentAndPolicies_dynamic("Assessment", $term, $lastTerm,$_REQUEST['ICD10'], $_REQUEST['chart_dos']);
			if(count($arrAsDyn)>0){
				array_walk($arrAsDyn,"suffix_dyn");
				$ret = array_merge($ret,$arrAsDyn);
			}
		}*/
		if($mode=="assessment"){
			$str_json_dxcode="";$flg_die="";
			if($_REQUEST['ICD10'] == 1){
				
				//Diabetese then first two will be diabetese type 1 or 2
				if(preg_match('/dia?b?e?t?e?s?/i',$term)&&strlen($term)<9){
					$ret = array_merge(array("Diabetes Type 1","Diabetes Type 2"),$ret);
				}
				
				//Dm then first two will be dm type 1 or 2
				if((preg_match('/dm?/i',$term)&&strlen($term)<3)||$term=="dm 1"||$term=="dm 2"){
					$ret = array_merge(array("DM Type 1","DM Type 2"),$ret);
				}
				
				//$_GET["term"] = 'ab';
				$arr_res = $this->getIC10Data();
				$results = $arr_res["results"];
				$flg_LitSev = $arr_res["flg_LitSev"];
				$icd10_dxdb = $arr_res["icd10_dxdb"];
				$srchd_code = $arr_res["srchd_code"];
				$icd10_dxdesc = $arr_res["icd10_dxdesc"];			
				
				//include_once("../../common/getICD10data.php");	
				$ret = array_merge($ret,$results);	
				if($_REQUEST['type_mode'] == "dxcode"){
					$str_json_dxcode= json_encode($results);
					$flg_die="1";	
				}
			}else{
				$flg_LitSev=0;//imp for typeahead
				//DxCodes
				$arrDx = $this->getTH_dxdropdown_new($term, $lastTerm, "Assessment");
				if(count($arrDx)>0){
					$ret = array_merge($ret,$arrDx);
					array_walk($ret,"ar_suffix","dx");						
				}
			}
		}

		return array("ret"=>$ret, "str_json_dxcode"=>$str_json_dxcode, "flg_die"=>$flg_die, 
					"results"=>$results,"flg_LitSev"=>$flg_LitSev,"icd10_dxdb"=>$icd10_dxdb,"srchd_code"=>$srchd_code,"icd10_dxdesc"=>$icd10_dxdesc);
		
	}	
	
	function cptdropdown(){
		//*
		$ar= $ar2= array();
		$tmp = $_GET["term"];
		if(!empty($tmp)){
			
			for($a=0; $a<2; $a++){
				
				$find_more = ($a==1) ? "%" : "";			
				$sql = "SELECT cpt_desc, cpt_prac_code, cpt4_code ".
					  "FROM cpt_fee_tbl ".			
					  "WHERE (REPLACE(cpt_desc,'\r\n','') LIKE '".$find_more.$tmp."%' OR
							REPLACE(cpt_prac_code,'\r\n','') LIKE '".$find_more.$tmp."%' OR
							REPLACE(cpt4_code,'\r\n','') LIKE '".$find_more.$tmp."%' ) ".
					  "AND delete_status = '0' ".
					  "AND UPPER(status) != 'INACTIVE' ".
					  "ORDER BY cpt_prac_code, cpt4_code, cpt_desc ";
					  
				//$ar[] = $sql;	  
				$rez = sqlStatement($sql);		
				for($i=0;$row=sqlFetchArray($rez);$i++){
					//*
					$row["cpt_prac_code"]=trim($row["cpt_prac_code"]);
					$row["cpt_desc"] = trim($row["cpt_desc"]);
					$row["cpt4_code"] = trim($row["cpt4_code"]);
					
					if(!empty($row["cpt_prac_code"]) && stripos($row["cpt_prac_code"],$tmp)!==false && !in_array($row["cpt_prac_code"],$ar) ){	$ar[] = $row["cpt_prac_code"];	}	//			
					if(!empty($row["cpt_desc"]) && stripos($row["cpt_desc"],$tmp)!==false  && !in_array($row["cpt_desc"],$ar)){				$ar[] = $row["cpt_desc"];	}	//
					
					if(!empty($row["cpt4_code"]) && stripos($row["cpt4_code"],$tmp)!==false ){			
						//&& !in_array($row["cpt4_code"],$ar)
						$tmp_lbl = trim($row["cpt4_code"]." - ".$row["cpt_prac_code"]);
						if(($row["cpt4_code"]!=$row["cpt_prac_code"]) &&  !in_array($tmp_lbl,$ar2) ){
							$ar[] = array("value"=>$row["cpt_prac_code"], "label"=>$tmp_lbl); 
							$ar2[] = $tmp_lbl;	
						}
						
					} //$row["cpt4_code"]; }//array("value"=>$row["cpt_prac_code"], "label"=>$row["cpt4_code"]." - ".$row["cpt_prac_code"]);  		//
					//*/
				}

			}
			
		}
		//*/
		
		echo json_encode($ar);
	
	}
	
	function moddropdown(){
		//*
		$ar=array();
		$tmp = $_GET["term"];
		if(!empty($tmp)){
			
			for($a=0; $a<2; $a++){
				$find_more = ($a==1) ? "%" : "";
				$sql = "SELECT mod_description, mod_prac_code, modifier_code ".
					  "FROM modifiers_tbl  ".			
					  "WHERE (REPLACE(mod_description,'\r\n','') LIKE '".$find_more.$tmp."%' OR
							REPLACE(mod_prac_code,'\r\n','') LIKE '".$find_more.$tmp."%' OR
							REPLACE(modifier_code,'\r\n','') LIKE '".$find_more.$tmp."%' )".					
					  "AND delete_status = '0' ".
					  "AND UPPER(status) != 'INACTIVE' ".
					  "ORDER BY mod_prac_code, modifier_code, mod_description ";
				//$ar[] = $sql;	  			
				//*
				$rez = sqlStatement($sql);		
				for($i=0;$row=sqlFetchArray($rez);$i++){
					//*
					$row["mod_prac_code"]=trim($row["mod_prac_code"]);
					$row["mod_description"] = trim($row["mod_description"]);
					$row["modifier_code"] = trim($row["modifier_code"]);
					
					if(!empty($row["mod_prac_code"]) && stripos($row["mod_prac_code"],$tmp)!==false && !in_array($row["mod_prac_code"],$ar) ){	$ar[] = $row["mod_prac_code"];	}	//			
					if(!empty($row["mod_description"]) && stripos($row["mod_description"],$tmp)!==false  && !in_array($row["mod_description"],$ar)){				$ar[] = $row["mod_description"];	}	//
					if(!empty($row["modifier_code"]) && stripos($row["modifier_code"],$tmp)!==false  && !in_array($row["modifier_code"],$ar)){			$ar[] = $row["modifier_code"]; } 		//
					//*/				
				}
				//*/

			}
			
		}
		//*/
		
		echo json_encode($ar);
	}
	
	//Get Typeahead Dx Codes
	function getTH_dxdropdown($tmp="", $srch2="",$flag=""){
		$ar=array();
		if(!empty($tmp)){
			
			/* //Performance issue
			if(!empty($srch2)){ 
				$lastTermPhrase = " OR REPLACE(diag_description,'\r\n','') LIKE '".$srch2."%' OR
						REPLACE(d_prac_code,'\r\n','') LIKE '".$srch2."%' OR
						REPLACE(dx_code,'\r\n','') LIKE '".$srch2."%' ";   
			}else{ $lastTermPhrase=""; }

			$sql = "SELECT diag_description, d_prac_code, dx_code ".
				  "FROM diagnosis_code_tbl  ".			
				  "WHERE (REPLACE(diag_description,'\r\n','') LIKE '".$tmp."%' OR
						REPLACE(d_prac_code,'\r\n','') LIKE '".$tmp."%' OR
						REPLACE(dx_code,'\r\n','') LIKE '".$tmp."%' ".$lastTermPhrase." )".					
				  //"AND delete_status = '0' ".
				  "ORDER BY d_prac_code, dx_code, diag_description ";
			*/
			
			if(!empty($srch2)){ 
				$lastTermPhrase = " OR diag_description LIKE '".$srch2."%' OR
						d_prac_code LIKE '".$srch2."%' OR
						dx_code LIKE '".$srch2."%' ";   
			}else{ $lastTermPhrase=""; }
			
			if($flag == "Assessment"){
				$assesment = " , console_to_do c ";
				$assesmentWhere = " AND diagnosis_code_tbl.diag_description  != c.assessment AND diagnosis_code_tbl.dx_code != c.dxcode  ";
			}else{
				$assesment = '';
				$assesmentWhere = '';
			}
			$sql = "SELECT diag_description, d_prac_code, dx_code ".
				  "FROM diagnosis_code_tbl  ".
				  $assesment.			
				  "WHERE (diag_description LIKE '".$tmp."%' OR
						d_prac_code LIKE '".$tmp."%' OR
						dx_code LIKE '".$tmp."%' ".$lastTermPhrase." )".
						$assesmentWhere	.				
				  //"AND delete_status = '0' ".
				  "ORDER BY d_prac_code, dx_code, diag_description ";
			
			//$ar[] = $sql;	return $ar;  
			$rez = sqlStatement($sql);		
			for($i=0;$row=sqlFetchArray($rez);$i++){
				//*
				$row["d_prac_code"]=trim($row["d_prac_code"]);
				$row["diag_description"] = trim($row["diag_description"]);
				$row["dx_code"] = trim($row["dx_code"]);
				
				//*
				if(!empty($row["d_prac_code"]) && (stripos($row["d_prac_code"],$tmp)!==false||(!empty($srch2) && stripos($row["d_prac_code"],$srch2)!==false)) && !in_array($row["d_prac_code"],$ar) ){	$ar[] = $row["d_prac_code"];	}	//			
				if(!empty($row["diag_description"]) && (stripos($row["diag_description"],$tmp)!==false ||(!empty($srch2) && stripos($row["diag_description"],$srch2)!==false))  && !in_array($row["diag_description"],$ar)){				$ar[] = $row["diag_description"];	}	//
				if(!empty($row["dx_code"]) && (stripos($row["dx_code"],$tmp)!==false ||(!empty($srch2) && stripos($row["dx_code"],$srch2)!==false) )  && !in_array($row["dx_code"],$ar)){			$ar[] = $row["dx_code"]; } 		//
				
				//*/				
			}			
		}

		return $ar;
	}
	
	function dxdropdown(){
		$tmp = $_GET["term"];
		$ar = getTH_dxdropdown($tmp);			
		echo json_encode($ar);
	}

	function getOrderData(){
		$arr=array();
		$tmp = $_GET["term"];
		$sql = "SELECT distinct(medicine_name), id FROM medicine_data ".
			   "WHERE del_status = '0' AND medicine_name LIKE '$tmp%' ".
			   "ORDER BY medicine_name ";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$arr[] = array("value"=>$row["medicine_name"], "id"=>$row["id"]);			
		}
		echo json_encode($arr);
	}
	
	function getProviders(){
		$ar=array();
	
		$tmp = $_GET["term"];
		$sql = "SELECT fname, lname, mname, id, pro_title from users 
				WHERE (fname LIKE '".$tmp."%' OR mname LIKE '".$tmp."%' OR  lname LIKE '".$tmp."%')  
				AND delete_status!='1' 
				AND locked!='1'  
				ORDER BY fname, mname, lname ";
		$rez=sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			
			$userName="";
			$userName = $row["pro_title"]." ".$row["fname"]." ".substr($row["mname"],0,1)." ".$row["lname"];//." - ".$row["id"]
			$userName = trim($userName);
			if(!empty($userName)){
				$ar[] = $userName;
			}
		}	
		
		echo json_encode($ar);
		
	}
	
	function escape_like_chars($s){
		return str_replace(array('_','%'), array('\_','\%'), $s);
	}
	
	//enable/disable typeahead
	function en_dis_TA(){
		if($_GET["flg"] == "1"){
			$_SESSION["disable_typeahead"] = 1;
			$flg=0;			
		}else{
			$_SESSION["disable_typeahead"] = NULL;
			unset($_SESSION["disable_typeahead"]);
			$flg=1;
		}
		echo $flg;
	} 
    
    /*     get referral code for CMS50v8 for C1 measure    */
    function referralCode($code) {
        $ar=array();
        $sql = "SELECT `Code`, `Description` FROM `cqm_v8_valueset` "
                . "WHERE `Value_Set_Name`='Referral' "
                . "AND `CMS_ID`='CMS50v8' "
                . "AND `Value_Set_OID` = '2.16.840.1.113883.3.464.1003.101.12.1046' "
                . "AND ( `Code` like '".$code."%' OR `Description` like '".$code."%' )";
        $resp = imw_query($sql);

        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                $ar[]=$row['Description'].' -('.$row['Code'].')';
            }
        }
        
        echo json_encode($ar);
    }
	
	function main(){
		//elem_formAction=TypeAhead
		$mode = $_GET["mode"];
		if($mode=="en_dis_TA"){$this->en_dis_TA();exit();}
		$term = $_GET["term"];		
		$term = $this->escape_like_chars($term);
		$this->term = $term;
		$providerId = $_SESSION['authId'];
		 
		$exmnm = $_GET["exmnm"];
		if($exmnm == "Pupil" || $exmnm == "EOM" || 
				$exmnm == "Gonio" || $exmnm == "SLE" || $exmnm == "Fundus"){
			
		}else{$exmnm="";}	
		
		//skip initial characters like -,1.,a.,A.			
		$ptrn = "/^(\-|((\d+|\w)\.))/i";
		$term = preg_replace($ptrn,"",$term);
		$term = trim($term);			
		
		$ret = array();
		
		if(!empty($term)){
			
			//get Providers
			if($mode=="getproviders"){
				$this->getProviders();
				exit();
			}
			
			//getOrders
			if($mode=="getorder"){
				$this->getOrderData();
				exit();
			}
		
			//getICD10Data
			if($mode=="getICD10Data"){
				$this->getIC10Data();
				exit();
			}
			
			if($mode=="get_dx_data"){
				$this->get_dx_data();
				exit();
			}
			
			if($mode=="cptdropdown"){
				$this->cptdropdown();
				exit();
			}
			if($mode=="moddropdown"){
				$this->moddropdown();
				exit();
			}
			if($mode=="dxdropdown"){
				$this->dxdropdown();
				exit();
			}		
			if($mode=="referralCode"){
				$this->referralCode($term);
				exit();
			}		
			
		
			$lastTerm = $this->getTypeAheadLastTerm($term);
			$lastTerm = $this->escape_like_chars($lastTerm);
			$this->lastTerm = $lastTerm;
			//Fu Visit
			if($mode=="FUVisit"){
				$this->getFUVisit();	
			}
			
			//Multi phy ref
			if($mode=="refPhy"){
				$ar_tmp = $this->get_refPhy();
				$ret = array_merge($ret, $ar_tmp);
			}
			
			if($mode=="OcuMed"){
				// Get Type Ahead Ocu. Meds
				$arrOM = $this->getTypeAheadStr_ocuMeds($providerId,$term, $lastTerm);				
				if(count($arrOM)>0){
					$ret = array_merge($ret,$arrOM);					
				}
			}
			
			$tmp_dis_phrs = ($mode=="assessment" && isset($GLOBALS["DISABLE_PHRASE_ASSESS"]) && !empty($GLOBALS["DISABLE_PHRASE_ASSESS"])) ? true : false;
			//typeahead
			if($mode!="refPhy" && $mode!="OcuMed" && !$tmp_dis_phrs && empty($_SESSION["disable_typeahead"])){
				$oUserAp = new UserAp();
				$tmp_ret = $oUserAp->console_get_smart_phrases($term, $lastTerm, $exmnm, $providerId);
				if(count($tmp_ret)>0){
					$ret = array_merge($ret,$tmp_ret);
				}
			}
			
			if($mode=="assessment" || $mode=="MR"){
				$tmp_ret = $this->get_assess_mr_opts($mode);
				$results = $tmp_ret["results"];
				$flg_LitSev = $tmp_ret["flg_LitSev"];
				$icd10_dxdb = $tmp_ret["icd10_dxdb"];
				$srchd_code = $tmp_ret["srchd_code"];
				$icd10_dxdesc = $tmp_ret["icd10_dxdesc"];
				$ret_more = $tmp_ret["ret"];
				$str_json_dxcode = $tmp_ret["str_json_dxcode"];
				$flg_die = $tmp_ret["flg_die"];
				
				if(!empty($flg_die)){					
					echo $str_json_dxcode; //echo json
					die();					
				}else{
					$ret = array_merge($ret, $ret_more);
				}
			}
			
			
		}//
		//$ret=array("test 1~~1", "test 11~~1","test 2~~2", "test 22~~2");
		if(!empty($_GET["show_pop"])){
			echo json_encode(array("results"=>$ret,"flg_LitSev"=>$flg_LitSev,"icd10_dxdb"=>$icd10_dxdb,"srchd_code"=>$srchd_code,"icd10_dxdesc"=>$icd10_dxdesc));				
		}else{
			echo json_encode($ret);
		}
	}	
}

?>