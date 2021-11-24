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
Purpose: This file provide Glucoma Log Section in Glucoma Flow Sheet.
Access Type : Direct
*/

$ignoreAuth = true;

include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
include_once $GLOBALS['srcdir']."/classes/SaveFile.php";

$glaucoma_obj = New ChartGlucoma($patient);

//osavefile
$oSaveFile = new SaveFile($glaucoma_obj->pid);

	$glaucoma_obj->getChartNotesReading($patient);
	
	
	// Default Date of Activation
	$elem_dateActivation = get_date_format(date("Y-m-d"));
	$elem_activate = "-1";	
	$elem_vfOdSummary = $elem_vfOsSummary = $elem_nfaOdSummary = $elem_nfaOsSummary = "Empty";
	
	//elem_activate	
	if((isset($_POST["elem_activate"]) && !empty($_POST["elem_activate"]))){
		$elem_activate = $_POST["elem_activate"];   
	}
	else if(isset($_REQUEST["mode"]) && !empty($_REQUEST["mode"])){
		$elem_activate = $_REQUEST["mode"];   
	}
	
	
	// get Initial Top
	$lenInitialTop = count($arrInitialTop);
    
	//if Past Glucoma Record exists	
	$sql = "SELECT *
		  FROM glucoma_main 
		  WHERE patientId = '".$patient."' 
		  AND activate = '1' ";	
	
	$row = sqlQuery($sql); // get One Record
	if($row != false)
	{	
		
		// Record Exists					
		$elem_dateActivation = get_date_format($row["dateActivation"],'mm-dd-yyyy');
		$elem_glucomaId = $row["glucomaId"];
		$elem_activate = $row["activate"];
		$elem_dateDiagnosis = get_date_format($row["dateDiagnosis"],'mm-dd-yyyy');
		$dxDescription = $row["diagnosis_description"];			
		$elem_diagnosisOd = $row["diagnosisOd"];
		$elem_diagnosisOs = $row["diagnosisOs"];
		$elem_stagingCode_od = $row["staging_code_od"];
		$elem_stagingCode_os = $row["staging_code_os"];
		$elem_dateHighTaOd = get_date_format($row["dateHighTaOd"],'mm-dd-yyyy');
		$elem_highTaOdOd = $row["highTaOdOd"];
		$elem_highTaOdOs = $row["highTaOdOs"];
		$elem_dateHighTaOs = get_date_format($row["dateHighTaOs"],'mm-dd-yyyy');
		$elem_highTaOsOd = $row["highTaOsOd"];
		$elem_highTaOsOs = $row["highTaOsOs"];
		$row["elem_dateHighTmaxOd"];
		$elem_dateHighTmaxOd = get_date_format($row["elem_dateHighTmaxOd"],'mm-dd-yyyy');
		$elem_highTmaxOdOd = $row["elem_highTmaxOdOd"];
		$elem_dateHighTmaxOs = get_date_format($row["elem_dateHighTmaxOs"],'mm-dd-yyyy');
		$elem_highTmaxOsOs = $row["elem_highTmaxOsOs"];
		
		$elem_dateHighTxOd = get_date_format($row["dateHighTxOd"],'mm-dd-yyyy');
		$elem_highTxOdOd = $row["highTxOdOd"];
		$elem_highTxOdOs = $row["highTxOdOs"];
		$elem_dateHighTxOs = get_date_format($row["dateHighTxOs"],'mm-dd-yyyy');
		$elem_highTxOsOd = $row["highTxOsOd"];
		$elem_highTxOsOs = $row["highTxOsOs"];
		
		$elem_dateVf = get_date_format($row["dateVf"],'mm-dd-yyyy');
		$elem_vfOdSummary = $row["vfOdSummary"];
		$elem_vfOsSummary = $row["vfOsSummary"];
		$elem_dateNfa = get_date_format($row["dateNfa"],'mm-dd-yyyy');
		$elem_nfaOdSummary = $row["nfaOdSummary"];
		$elem_nfaOsSummary = $row["nfaOsSummary"];
		$elem_dateGonio = get_date_format($row["dateGonio"],'mm-dd-yyyy');
		$elem_gonioOd = $row["gonioOd"];
		$elem_gonioOs = $row["gonioOs"];
		$elem_datePachy = get_date_format($row["datePachy"],'mm-dd-yyyy');
		$elem_pachyOdReads = $row["pachyOdReads"];
		$elem_pachyOdAvg = $row["pachyOdAvg"];
		$elem_pachyOdCorr = $row["pachyOdCorr"];
		$elem_pachyOsReads = $row["pachyOsReads"];
		$elem_pachyOsAvg = $row["pachyOsAvg"];
		$elem_pachyOsCorr = $row["pachyOsCorr"];
		$elem_dateDiskPhoto = get_date_format($row["dateDiskPhoto"],'mm-dd-yyyy');
		$elem_diskPhotoOd = $row["diskPhotoOd"];
		$elem_diskPhotoOs = $row["diskPhotoOs"];
		$elem_dateCd = get_date_format($row["dateCd"],'mm-dd-yyyy');
		$elem_cdOd = $row["cdOd"];
		$elem_cdOs = $row["cdOs"];
		$elem_cdOdSummary = $row["cdOdSummary"];
		$elem_cdOsSummary = $row["cdOsSummary"];
		$elem_riskFactors = $row["riskFactors"];		
		$elem_warnings = $row["warnings"];
		$elem_cd_app_od = $row["cdAppOd"];
		$elem_cd_app_os = $row["cdAppOs"];		
		$elem_notes = $row["notes"];
		$elem_cee = $row["cee"];
		$elem_ceeNotes = $row["ceeNotes"];
		$elem_dateCee = get_date_format($row["ceeDate"],'mm-dd-yyyy'); 	
		$targetOd=$row["iopTrgtOd"];
		$targetOs=$row["iopTrgtOs"];

		$sig_imgcd_app_od = (!empty($row["imgcd_app_od"])) ? $oSaveFile->getFilePath($row["imgcd_app_od"],"w") : "" ;
		$sig_imgcd_app_os = (!empty($row["imgcd_app_os"])) ? $oSaveFile->getFilePath($row["imgcd_app_os"],"w") : "" ;

	}

	// if Acivate 
	if($elem_activate != "-1")
        {   	
			//Set High Values
			//Ta
			if(empty($elem_highTaOdOd) || ($elem_highTaOdOd < $arrInitialTop["HighTaOd"]["od"])){
				$elem_highTaOdOd =  $arrInitialTop["HighTaOd"]["od"];
				$elem_dateHighTaOd = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTaOd"]["date"]);
		    }
			
			if(empty($elem_highTaOdOs) || ($elem_highTaOdOs < $arrInitialTop["HighTaOd"]["os"])){
				$elem_highTaOdOs = $elem_highTaOdOs;
			}		
		    
			if(empty($elem_highTaOsOd) || ($elem_highTaOsOd < $arrInitialTop["HighTaOs"]["od"])){
				$elem_highTaOsOd = $arrInitialTop["HighTaOs"]["od"];				
			}

			if(empty($elem_highTaOsOs) || ($elem_highTaOsOs < $arrInitialTop["HighTaOs"]["os"])){
				$elem_highTaOsOs = $arrInitialTop["HighTaOs"]["os"];
				$elem_dateHighTaOs = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTaOs"]["date"]);
		    }
			
		    //Tx
			if(empty($elem_highTxOdOd) || 
				($elem_highTxOdOd < $arrInitialTop["HighTxOd"]["od"])){
				$elem_highTxOdOd = $arrInitialTop["HighTxOd"]["od"];
				$elem_dateHighTxOd = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTxOd"]["date"]);
			}

			if(empty($elem_highTxOdOs) || 
				($elem_highTxOdOs < $arrInitialTop["HighTxOd"]["os"])){
				$elem_highTxOdOs = $arrInitialTop["HighTxOd"]["os"];
			}
		    
			if(empty($elem_highTxOsOd) || 
				($elem_highTxOsOd < $arrInitialTop["HighTxOs"]["od"])){
				$elem_highTxOsOd = $arrInitialTop["HighTxOs"]["od"];
			}
			
			if(empty($elem_highTxOsOs) || 
				($elem_highTxOsOs < $arrInitialTop["HighTxOs"]["os"])){
				$elem_highTxOsOs = $arrInitialTop["HighTxOs"]["os"];
				$elem_dateHighTxOs = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTxOs"]["date"]);
			}
			
		   //Trgt 
		     $targetOd = $row["iopTrgtOd"];
		     $targetOs = $row["iopTrgtOs"];
	}

	
	//
	$arrDoneNotDone = array();
	$arrDoneNotDone[] = array("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$arrEmpty,"");
	$arrDoneNotDone[] = array("Done",$arrEmpty,"Done");
	$arrDoneNotDone[] = array("Not Done",$arrEmpty,"Not Done");
	
	//Menu Option    
	$arrMenuVfNfa = array();
	$arrTmp = array("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "Normal","Border Line", "PS", "Abnormal","Increase Abnormal","Decrease Abnormal","No Change Abnormal","Stable");
	foreach($arrTmp as $key => $var)
	{
		$varTmp =  ($var == "Empty") ? "" : $var;
		$arrMenuVfNfa[] = array($var,$arrEmpty,$varTmp);            
	}
	
	//Targets	
	$curformId = $glaucoma_obj->isChartOpened($patient);
	list($targetOdTmp,$targetOsTmp)= $glaucoma_obj->getIopTargets($patient);	
	if($curformId != false){
		//check
		$cQry = "select sumOdIop, sumOsIop FROM chart_iop WHERE form_id='".$curformId."' ";
		$row = sqlQuery($cQry);
		if($row != false){
			$targetOd = (!empty($targetOdTmp)) ? $targetOdTmp : $targetOd;
			$targetOs = (!empty($targetOsTmp)) ? $targetOsTmp : $targetOs;
		}
	}else{
		if(empty($targetOd) && empty($targetOs)){
			$targetOd = (!empty($targetOdTmp)) ? $targetOdTmp : $targetOd;
			$targetOs = (!empty($targetOsTmp)) ? $targetOsTmp : $targetOs;
		}
	}

	//Pachy ---	

	$tmpFid = ($curformId != false) ? $curformId : 0 ;
	$tmp = $glaucoma_obj->setDefPachyVals($tmpFid);
	if(count($tmp)>0){		
		$elem_pachyOdReads=$tmp[0];
		$elem_pachyOdAvg=$tmp[1];
		$elem_pachyOdCorr=$tmp[2];
		$elem_pachyOsReads=$tmp[3];
		$elem_pachyOsAvg=$tmp[4];
		$elem_pachyOsCorr=$tmp[5];
		$elem_datePachy=$tmp[6];
	}
	//Pachy ---

	$vf_gl_dos_arr = array();
	$vf_gl_txt_qry = "SELECT vf_gl_id, ptUnderstanding, examDate,reliabilityOd,reliabilityOs,normal_od,normal_os,nonspecific_od,nonspecific_os,nasal_step_od,nasal_step_os,arcuate_od,arcuate_os,hemifield_od,hemifield_os,paracentral_od,paracentral_os,into_fixation_od,into_fixation_os,central_island_od,central_island_os,Stable,worse,improve,interpretation,synthesis_od,synthesis_os 	 FROM vf_gl WHERE patientId = '".$patient."' order by vf_gl_id DESC limit 0,1";
	$vf_gl_txt_qry_obj = imw_query($vf_gl_txt_qry);
	
	$interpretation_arr = array();
	$interpretation_arr["stable"] = "S";
	$interpretation_arr["worse"] = "W";
	$interpretation_arr["improve"] = "I";	
	$interpretation_arr["not improve"] = "NI";
	$interpretation_arr["likely progression"] = "LW";
	$interpretation_arr["possible progression"] = "PW";		

	$vf_gl_txt_data = imw_fetch_assoc($vf_gl_txt_qry_obj);

	$vf_gl_dos_val = get_date_format($vf_gl_txt_data["examDate"]);
	$vf_gl_dos_arr['date'] = $vf_gl_dos_val;			
	$vf_gl_dos_arr['od'] = $vf_gl_txt_data['synthesis_od'];
	$vf_gl_dos_arr['os'] = $vf_gl_txt_data['synthesis_os'];
	
	$rnfl_gl_txt_qry = "SELECT oct_rnfl_id,examDate,ptUndersatnding,interpretation,rnfl_od,contour_overall_od,contour_superior_od,contour_inferior_od,contour_temporal_od,symmetric_od,rnfl_os,contour_overall_os,contour_superior_os,contour_inferior_os,contour_temporal_os,symmetric_os,synthesis_od,synthesis_os FROM oct_rnfl WHERE patient_id = '".$patient."' order by oct_rnfl_id DESC limit 0,1";
	$rnfl_gl_txt_qry_obj = imw_query($rnfl_gl_txt_qry);
	$rnfl_status_arr = array('nl'=>'NL','thin'=>'T','very thin'=>'VT');
	
	$rnfl_dos_arr = array();	
	$rnfl_gl_txt_data = imw_fetch_assoc($rnfl_gl_txt_qry_obj);
	
		$rnfl_dos_val = get_date_format($rnfl_gl_txt_data["examDate"]);	
		$rnfl_dos_arr['date'] = $rnfl_dos_val;
		$rnfl_dos_arr['od'] = $rnfl_gl_txt_data['synthesis_od'];
				
		$rnfl_dos_arr['os'] = $rnfl_gl_txt_data['synthesis_os'];
		
	/* DISC APPEARANCE */ 
	$disc_app_qry = "SELECT optic_id,date_format(exam_date,'%Y-%m-%d') as exam_date, od_text, os_text, cd_val_od,cd_val_os,optic_nerve_od_summary,optic_nerve_os_summary FROM chart_optic WHERE patient_id = '".$patient."' ORDER BY optic_id DESC limit 0,1";
	$disc_app_qry_obj = imw_query($disc_app_qry);
	$disc_app_data_arr = array();
	
	$disc_app_data = imw_fetch_assoc($disc_app_qry_obj);
	
	$disc_dos_val = get_date_format($disc_app_data["exam_date"]);
	$disc_app_data_arr["date"] = $disc_dos_val;
	if(trim($disc_app_data["cd_val_od"]) == "")
	{
		$disc_app_data_arr["cd_od"] = $disc_app_data["od_text"];	
	}
	else
	{
		$disc_app_data_arr["cd_od"] = $disc_app_data["cd_val_od"];	
	}
	
	if(trim($disc_app_data["cd_val_os"]) == "")
	{
		$disc_app_data_arr["cd_os"] = $disc_app_data["os_text"];
	}
	else
	{
		$disc_app_data_arr["cd_os"] = $disc_app_data["cd_val_os"];	
	}
	/* GONIO */
	$gonio_req_qry = "SELECT gonio_id, date_format(examDateGonio,'%Y-%m-%d') as examDateGonio, gonio_od_summary, gonio_os_summary FROM chart_gonio WHERE examDateGonio<>'' and examDateGonio<>'0000-00-00' and patient_id = '".$patient."' order by gonio_id DESC limit 0,1";
	$gonio_qry_obj = imw_query($gonio_req_qry);
	$gonio_dos_data_arr = array();
	$gonio_dos_data = imw_fetch_assoc($gonio_qry_obj);
	$gonio_dos_data_arr["date"] = get_date_format($gonio_dos_data["examDateGonio"]); 
	$gonio_dos_data_arr["od_summary"] = $gonio_dos_data["gonio_od_summary"]; 	
	$gonio_dos_data_arr["os_summary"] = $gonio_dos_data["gonio_os_summary"]; 			
	
	/* Pachy */
	$pachy_req_qry = "SELECT pachy_id,examDate,pachy_od_readings,pachy_od_average,pachy_od_correction_value,pachy_os_readings,pachy_os_average,pachy_os_correction_value FROM pachy WHERE patientId = '".$patient."' order by pachy_id DESC limit 0,1";	
	$pachy_req_qry_obj = imw_query($pachy_req_qry);
	$pachy_dos_data_arr = array();
	$pachy_inc = 0;
	$pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj);
	$pachy_dos_data_arr["date"] = get_date_format($pachy_dos_data["examDate"]);
	$pachy_dos_data_arr["od_readings"] = $pachy_dos_data["pachy_od_readings"];
	$pachy_dos_data_arr["os_readings"] = $pachy_dos_data["pachy_os_readings"];
	$pachy_dos_data_arr["od_correction"] = $pachy_dos_data["pachy_od_correction_value"];
	$pachy_dos_data_arr["os_correction"] = $pachy_dos_data["pachy_os_correction_value"];				
	$pachy_dos_data_arr["od_avg"] = $pachy_dos_data["pachy_od_average"];				
	$pachy_dos_data_arr["os_avg"] = $pachy_dos_data["pachy_os_average"];						
	
	
	$pachy_req_qry = "SELECT cor_id,cor_date,reading_od,reading_os,cor_val_od,cor_val_os,avg_od,avg_os FROM chart_correction_values WHERE patient_id = '".$patient."' and (reading_od!='' or reading_os!='') order by cor_id DESC limit 0,1";	
	$pachy_req_qry_obj = imw_query($pachy_req_qry);
	//$pachy_dos_data_arr = array();
	$pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj);
	
		$cor_date = get_date_format($pachy_dos_data["cor_date"]);
		if(trim($cor_date) != "")
		{
			$matched = 0;
			foreach($pachy_dos_data_arr as $pachy_val_arr)
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
				$pachy_dos_data_arr["date"] = get_date_format($pachy_dos_data["cor_date"]);
				$pachy_dos_data_arr["od_readings"] = $pachy_dos_data["reading_od"];
				$pachy_dos_data_arr["os_readings"] = $pachy_dos_data["reading_os"];
				$pachy_dos_data_arr["od_correction"] = $pachy_dos_data["cor_val_od"];
				$pachy_dos_data_arr["os_correction"] = $pachy_dos_data["cor_val_os"];				
				$pachy_dos_data_arr["od_avg"] = $pachy_dos_data["avg_od"];				
				$pachy_dos_data_arr["os_avg"] = $pachy_dos_data["avg_os"];	
				$pachy_inc++;			
			}
		}
		
		
		//------------------------------------ medication code ----------------------------------
		
		$show_type=4;
	function is_glaucoma_med($med){
		$sql = "SELECT count(*) AS counter FROM medicine_data WHERE glucoma='1' AND medicine_name= '".$med."' AND del_status = '0'";
		$res = imw_query($sql);
		$row = imw_fetch_assoc($res);
		if($row['counter']>0) return 1;
		else return 0;
	}
	$check_data="select * from lists where pid='".$patient."' 
				AND type='4' 
				AND allergy_status='Active' 
				AND (enddate >= '".date('Y-m-d')."' OR enddate = '0000-00-00' OR enddate = '' OR enddate IS NULL)
				ORDER BY allergy_status, begdate DESC";
	$resList =  imw_query($check_data);
	$tot_rec = imw_num_rows($resList);
	$j = 0;
	while($row = @imw_fetch_array($resList)){
			$tmp_med = $row["title"];
			$tmp_dtSt = (!empty($row["begdate"]) && ($row["begdate"] != '0000-00-00')) ? get_date_format($row["begdate"]) : "" ;
			$tmp_dtEd = (!empty($row["enddate"]) && ($row["enddate"] != '0000-00-00')) ? get_date_format($row["enddate"]) : "" ;
			$tmp_reason = $row["med_comments"];
			$tmp_sts = strtoupper($row["allergy_status"]);
			$tmp_compliant = $row["compliant"];
			$tmp_date = $row["date"];
			$tmp_site = $row["sites"];
			$tmp_sig = $row["sig"];
			$tmp_id = $row["id"];
			
			$elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($j+1);
			$$elem_listId = $tmp_id;	
			$elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($j+1);
			$$elem_siteId = $tmp_site;	
			$elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($j+1);
			$$elem_sigId = $tmp_sig;	
			$elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($j+1);
			$$elem_medicationId = $tmp_med;				
			$elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($j+1);
			$$elem_dateStartId = $tmp_dtSt;
			$elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($j+1);
			$$elem_dateStoppedId = $tmp_dtEd;
			$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($j+1);
			$$elem_reasonDiscontinuedId = $tmp_reason;				
			$elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($j+1);
			$$elem_statusId = $tmp_sts;		
			$elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($j+1);
			$$elem_compliantId = $tmp_compliant;
			$elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($j+1);
			$$elem_dateId = $tmp_date;		
			
			$arrSearch[] = strtolower($tmp_med);
			$arrTypeHead[$tmp_med] = "'".$tmp_med."'";
			$j++;
		}
		
	$check_data="SELECT li.* FROM lists li
					JOIN medicine_data md ON REPLACE(li.title,'* Ocular','') = md.medicine_name
					where pid='".$patient."' AND type='4' AND (allergy_status='Discontinue' OR allergy_status='Stop' OR (allergy_status='Active' AND enddate < '".date('Y-m-d')."' AND enddate != '0000-00-00' AND enddate != '' AND enddate IS NOT NULL))
					AND md.glucoma='1' 
					AND md.del_status = '0'
					ORDER BY allergy_status, begdate DESC";
	$resList =  imw_query($check_data);
	$tot_rec = imw_num_rows($resList);
	$ocular_count = $j;
	while($row = @imw_fetch_array($resList)){
		$tmp_med = $row["title"];
		
		$tmp_dtSt = (!empty($row["begdate"]) && ($row["begdate"] != '0000-00-00')) ? get_date_format($row["begdate"]) : "" ;
		$tmp_dtEd = (!empty($row["enddate"]) && ($row["enddate"] != '0000-00-00')) ? get_date_format($row["enddate"]) : "" ;
		$tmp_reason = $row["med_comments"];
		
		$tmp_sts = strtoupper($row["allergy_status"]);
		$tmp_compliant = $row["compliant"];
		$tmp_date = $row["date"];
		$tmp_site = $row["sites"];
		$tmp_sig = $row["sig"];
		$tmp_id = $row["id"];
		
		$elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($j+1);
		$$elem_listId = $tmp_id;	

				
		$elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($j+1);
		$$elem_siteId = $tmp_site;	
		$elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($j+1);
		$$elem_sigId = $tmp_sig;	
		$elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($j+1);
		$$elem_medicationId = $tmp_med;				
		$elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($j+1);
		$$elem_dateStartId = $tmp_dtSt;
		$elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($j+1);
		$$elem_dateStoppedId = $tmp_dtEd;
		$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($j+1);
		$$elem_reasonDiscontinuedId = $tmp_reason;				
		$elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($j+1);
		$$elem_statusId = $tmp_sts;		
		$elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($j+1);
		$$elem_compliantId = $tmp_compliant;
		$elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($j+1);
		$$elem_dateId = $tmp_date;		
		
		$arrSearch[] = strtolower($tmp_med);
		$arrTypeHead[$tmp_med] = "'".$tmp_med."'";
		
		$j++;
		
	}
	$dis_glaucoma_count = $j;	
	$total_meds = $j;	
	
	//-------------------------------------- surgical med -------------------------------------------
	
	$arrSearch = array();
	
	// Ocu Surgery
	$sql = "select id,title,type,
								if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
								if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
													if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
													if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
													date_format(begdate,'".get_sql_date_format()."')
													))))as begdate1
								 ,referredby,comments,sites from lists where pid='".$patient."' AND type='6' and allergy_status != 'Deleted' order by DATE_FORMAT(begdate1,'%Y-%m-%d') desc,id desc";
	$rez = sqlStatement($sql);
	for($j=0;$row=sqlFetchArray($rez);$j++)
	{
		$tmpSurgery = $row["title"];
		$tmpsite = $row["sites"];
		$tmpDtSt = (!empty($row["begdate1"]) && (preg_replace('/[^0-9]/','',$row["begdate1"]) != "00000000")) ? ($row["begdate1"]) : "" ;
		$tmpReason = $row["comments"];
		
		//if(!in_array(strtolower($tmpSurgery), $arrSearch)){
			$elem_listId = "elem_listId_".($j+1);
			$$elem_listId = $row["id"];
			$elem_surgeryId = "elem_surgery_".($i+1);
			$$elem_surgeryId = $tmpSurgery;
			$elem_siteId = "elem_site_".($i+1);
			$$elem_siteId = $tmpsite;
			$elem_dateStartId = "elem_dateStart_".($i+1);
			$$elem_dateStartId = $tmpDtSt;
			$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".($i+1);
			$$elem_reasonDiscontinuedId = $tmpReason;
			$arrSearch[] = strtolower($elem_surgeryId);
			$i++;
		//}	
	}	
	
	$countR = $i; // Total Records
?><!doctype html>
<html>
<head>
<title>Glaucoma Flow Sheet </title>
<style type="text/css">
.wrap_t_style2 table.t_scroll:first-child{border-left:none !important;}
.t_scroll:first-child { border-left:none !important}
.inner_content_gfs #no_height table tbody{height:auto !important;min-height:auto !important;}

.wrap_t_style table{text-align:left;-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;border:1px solid #333;}
.wrap_t_style  caption{-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px; border-top-left-radius: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;border-right:1px solid #333;border-left:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
.wrap_t_style  thead{padding:0px 0px;background:#f1f1f1;border-bottom:1px solid #333;}
.wrap_t_style  tbody{padding:0px 0;background:#fff;overflow-y:auto;max-height:100px !important;}
.wrap_t_style  tbody td{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style  tbody td:last-child{border-right:none !important;}
.wrap_t_style  thead th:last-child{border-right:none !important;}
.wrap_t_style  thead th{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}

.wrap_t_style2 table{text-align:left;-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;border:1px solid #333;}
.wrap_t_style2  caption{-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px; border-top-left-radius: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;border-right:1px solid #333;border-left:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
.wrap_t_style2  thead{padding:0px 0px;background:#f1f1f1;border-bottom:1px solid #333;}
.wrap_t_style2  tbody{padding:0px 0;background:#fff;}
.wrap_t_style2  tbody td{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style2  tbody td:last-child{border-right:none !important;}
.wrap_t_style2  thead th:last-child{border-right:none !important;}
.wrap_t_style2  thead th{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style2 tr{float:left;width:100%;}

.main_Wrap{width:100%;float:left;display:inline-block;min-height:768px;max-height:768px;overflow-y:auto;}
.ipad_wrap{margin:0;display:block;width:100%; float:none}
.inner_ipad_wrap{width:96%;float:left;display:inline-block;background:#fff;min-height:500px;border:2px solid #333;font-family:"Verdana";}
.head_w_gfs{float:left;width:100%;display:inline-block;padding:10px 0;  text-align: center;border-bottom:1px solid #333;font-size:18px;color:#006699;font-weight:normal;}
.bg_gfs_gray{ background:#f1f1f1;}
.content_gfs, .inner_content_gfs  , .inner_content_head , .inner_col{float:left;width:100%;display:inline-block;}
.content_gfs{padding:20px 0;border-bottom:1px solid #333;margin-bottom:10px;}
.inner_content_gfs , .inner_content_head{padding-left:15px; padding-right:15px;}
.inner_content_gfs{padding-top:10px;}
.inner_content_head{padding-top::10px; padding-bottom:10px;font-weight:normal; font-size:18px;}
.col-1{float:left;width:65%;display:inline-block;min-height:440px;}
.border-sty tbody{-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;}
.label_left{float:left;width:134px;display:inline-block;font-weight:normal;color:#333;font-weight:bold;font-size:15px;line-height:34px;font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;}
.col-2{float:left;width:33%;display:inline-block;min-height:440px;}
.right_content{float:left;width:75%;display:inline-block;}
.b_full , .right_inner21, .right_inner31 , .right_inner22, .right_inner32, .right_inner1 , .right_inner2 , .right_inner3{float:left;border:1px solid #333;  color:#333;font-weight:normal;min-height:48px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;padding:5px 2px;overflow:auto;max-height:48px;overflow-x:auto;color:#333;font-size:15px;font-family:"Verdana";word-wrap:break-word;}
.right_inner1{width:115px;margin-right:5px;}
.right_inner2, .right_inner3{width:152px;margin-right:5px;}
.right_inner21, .right_inner31{width:97px;margin-right:5px;}
.right_inner22, .right_inner32{width:44px;margin-right:5px;}
.min-h{min-height:90px !important;max-height:90px !important;}
.od_wrap{float:left;width:142px;margin-right:5px;color:#00992f ;margin: 10px 0 0 10px;font-weight:bold;}
.od_wrap.blue{color:#006699 !important;}
.inner_col{margin-top:6px;}
.inner_col:first-child{margin-top:0xp !important;}
.m_top_0{margin-top:0px !important;}
.b_full{width:441px;word-wrap:break-word;}
.inner_col-2{float:left;width:95%;display:inline-block;padding:8px 0 0 10px;    word-wrap: break-word;}
.bg_white{background:#fff !important;padding:4px 4px ; margin:8px 0px 3px 10px;width:207px !important;border:1px solid #333;}
.clearfix{clear:both;width:100%;float:left;}
.img_check{margin-right:5px;float:left;width:28px;}
.inner_col-2 label{float:left;display:inline-block;line-height:23px;}
.b_half_wrap{float:left;width:auto; margin-right:3px;  margin-left: 5px;word-wrap:break-word;}
.b_half{margin-left:10px;float:left;border:1px solid #333; ; color:#333;font-weight:normal;min-height:24px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;padding:5px 4px;   width: 43px;word-wrap:break-word;}
.wrap_table_1{margin:0 auto;width:95%;display:block;min-height:20px;}
.padding_0{padding:0px !important;}

.data_box {	background:#FFF;
	border:#CCC 1px solid
}
.data_box_pad {	background:#FFF;
	border:#CCC 1px solid;
	padding:0px 5px
}
</style>

</head>

<body>

<div style="" class="main_Wrap">
    	<div class="ipad_wrap">
        	<div class="inner_ipad_wrap bg_gfs_gray">
            	<div class="head_w_gfs" style="background:#fff;">
                	GFS
                </div>	
                <div class="content_gfs bg_gfs_gray">
                	<div class="inner_content_head" style="position:relative">
                    	<span style="border-bottom:1px solid #006699; color:#006699;padding-bottom:0px;"> Description </span>
                        <img src="../interface/chart_notes/graph_images/arrow_gray.png" style="width:10px; position:absolute;top:22px;left:55px;">
                    </div>
                	<div class="inner_content_gfs">
                  		<div class="col-1">
							<div class="inner_col m_top_0">                        	
                                <div class="label_left" style="visibility:hidden;">     	&nbsp;					 </div>	
                                <div class="right_content" style="max-height:30px;min-height:30px;overflow:hidden;">
                                    <div class="right_inner1" style="visibility:hidden;"></div>	
                                    <div class="od_wrap blue" style=" ">OD</div>	
                                    <div class="od_wrap" style="">OS </div>	
                                </div>
                            </div>
                            
                            <div class="inner_col m_top_0">                        	
                                <div class="label_left">     	Diagnosis   </div>	
                                <div class="right_content">
                                    <div class="right_inner1"><?php echo $dxDescription; ?></div>	
                                    <div class="right_inner2"><?php echo $elem_diagnosisOd; ?></div>	
                                    <div class="right_inner3"><?php echo $elem_diagnosisOs; ?></div>	
                                </div>
                            </div>
                            
                            <div class="inner_col">                        	
                                <div class="label_left">     	Staging Code					 </div>	
                                <div class="right_content">
                                    <div class="right_inner1" style="visibility:hidden;"></div>	
                                    <div class="right_inner2"><?php echo $elem_stagingCode_od;?></div>	
                                    <div class="right_inner3"><?php echo $elem_stagingCode_os;?></div>	
                                </div>
                            </div>
                            
                            <div class="inner_col">                        	
                                <div class="label_left"> <sup> T</sup>MAX </div>	
                                <div class="right_content">
                                    <div class="right_inner1"></div>	
                                    <div class="right_inner21"><?php echo $elem_dateHighTmaxOd;?></div>	
                                    <div class="right_inner22"><?php echo $elem_highTmaxOdOd; ?></div>	
                                  <div class="right_inner31"><?php echo $elem_dateHighTmaxOs; ?></div>	
                                    <div class="right_inner32"><?php echo $elem_highTmaxOsOs; ?></div>	
                                </div>
                            </div>
                            
                            <div class="inner_col">                        	
                                <div class="label_left">     	Pachy					 </div>	
                                <div class="right_content">
                                    <div class="right_inner1"><?php echo $pachy_dos_data_arr["date"];?></div>	
                                    <div class="right_inner21"><?php echo $elem_pachyOdReads;?></div>	
                                    <div class="right_inner22">
                                      <?php
                                //$elem_pachyOdCorr = (int) $elem_pachyOdCorr;
                                $pachy_od_calc = str_replace(',','',$elem_pachyOdReads);
                                $pachy_od_status = '';
                                if($pachy_od_calc != 0)
                                {
                                    if($pachy_od_calc < 555)
                                    {
                                        $pachy_od_status = 'Thin';	
                                    }
                                    else if($pachy_od_calc >= 555 && $pachy_od_calc <= 588)
                                    {
                                        $pachy_od_status = 'Average';								
                                    }
                                    else if($pachy_od_calc > 588)
                                    {
                                        $pachy_od_status = 'Thick';								
                                    }
                                }
                                echo $pachy_od_status;
                            ?>
                                    </div>	
                                  <div class="right_inner31"><?php echo $elem_pachyOsReads;?></div>	
                                    <div class="right_inner32">
                                      <?php
                                $pachy_os_calc = $elem_pachyOsReads;
                                $pachy_os_status = '';
                                if($pachy_os_calc != 0)
                                {
                                    if($pachy_os_calc < 555)
                                    {
                                        $pachy_os_status = 'Thin';	
                                    }
                                    else if($pachy_os_calc >= 555 && $pachy_os_calc <= 588)
                                    {
                                        $pachy_os_status = 'Average';								
                                    }
                                    else if($pachy_os_calc > 588)
                                    {
                                        $pachy_os_status = 'Thick';								
                                    }
                                }
                                echo $pachy_os_status;
                            ?>
                                    </div>	
                                </div>
                            </div>
                             <div class="inner_col">                        	
                                <div class="label_left">  VF </div>	
                                <div class="right_content">
                                    <div class="right_inner1"><?php echo $vf_gl_dos_arr['date'];?></div>	
                                    <div class="right_inner2"><?php echo $vf_gl_dos_arr['od'];?></div>	
                                    <div class="right_inner3"><?php echo $vf_gl_dos_arr['os'];?></div>	
                                </div>
                            </div>
                             <div class="inner_col">                        	
                                <div class="label_left">  OCT-RNFL </div>	
                                <div class="right_content">
                                    <div class="right_inner1 min-h"><?php echo $rnfl_dos_arr['date'];?></div>	
                                    <div class="right_inner2 min-h"><span style="height:80px; overflow:auto"><?php echo str_replace("\r\n","<br />",$rnfl_dos_arr['od']);?></span></div>	
                                    <div class="right_inner3 min-h"><span style="height:80px; overflow:auto"><?php echo str_replace("\r\n","<br />",$rnfl_dos_arr['os']);?></span></div>	
                                </div>
                            </div>
                             <div class="inner_col">                        	
                                <div class="label_left">  Gonio </div>	
                                <div class="right_content">
                                    <div class="right_inner1"><?php echo $gonio_dos_data_arr["date"];?></div>	
                                    <div class="right_inner2">
                                      <?php if($gonio_dos_data_arr["od_summary"]!='')echo'Done';else echo"Not Done";?>
                                    </div>	
                                    <div class="right_inner3">
                                      <?php if($gonio_dos_data_arr["os_summary"]!='')echo'Done';else echo"Not Done";?>
                                    </div>	
                                </div>
                            </div>
                             <div class="inner_col">                        	
                                <div class="label_left"> Disc Appearance </div>	
                                <div class="right_content">
                                    <div class="right_inner1"><?php echo $disc_app_data_arr["date"];?></div>	
                                    <div class="right_inner2"><?php echo $disc_app_data_arr["cd_od"];?></div>	
                                    <div class="right_inner3"><?php echo $disc_app_data_arr["cd_os"];?></div>	
                                </div>
                            </div>
                             <div class="inner_col">                        	
                                <div class="label_left"> Notes </div>	
                                <div class="right_content min-h b_full"><?php echo (!empty($elem_notes)) ? $elem_notes : "Notes:";?>
                                </div>
                            </div>
                        </div>	<!-- col-1 ends -->
               	 		<div class="col-2">
                        	<div class="inner_col m_top_0">                        	
                                <div class="od_wrap" style="color:#333 !important">    Risk factors</div>	
                            </div>
                            <div class="clearfix"></div>	
                            <div class="inner_col-2">
                            	<img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_riskFactors,"Family History") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label> Family HX </label>	
                            </div>
							<div class="inner_col-2 bg_white">
								                                    
                                    Race: <span style="width:130px; height:16px; overflow:auto">
                                    <?php
					$arrRace = array(
					"Race-American Indian or Alaska Native" => "American Indian or Alaska Native",
					"Race-Asian" => "Asian",
					"Race-Black or African American" => "Black or African American",
					"Race-Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
					"Race-Latin American" => "Latin American",
					"Race-White" => "White"
					);
					
					/* $raceOptMatch = "";				
					$displayRaceDropDown = ""; */
					if(strpos($elem_riskFactors,'Race-') === false)
					{
						$race_pt_qry = "SELECT race FROM patient_data WHERE id = '".$_SESSION['patient']."'";
						$race_pt_qry_obj = imw_query($race_pt_qry);
						$race_pt_qry_data = imw_fetch_assoc($race_pt_qry_obj);
						$elem_riskFactors .= $race_pt_qry_data["race"];
					}
					foreach($arrRace as $arrRaceKey => $arrRace_val)
					{
						$selOpt = "";
						if(strpos($elem_riskFactors,trim($arrRace_val)) !== false)
						{
							echo $arrRace_val.', ';
						}
						
					}
					
                	
				?>
                            </span> </div>
                            <div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_riskFactors,"Diabetes") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  Diabetes  </label>	
                            </div>
                       		<div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_riskFactors,"PXF") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  PXF </label>	
                            </div>
                            <div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_riskFactors,"Steroid Responder") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  Steroid Responder  </label>	
                            </div>
                            <div class="inner_col-2">
                              <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_riskFactors,"PDS") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  PDS  </label>	
                            </div>
                          <div class="inner_col m_top_0">                        	
                                <div class="od_wrap" style="color:#333 !important">  Warnings     </div>	
                            </div>
                            <div class="clearfix"></div><div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"Arrhythmia") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>Arrhythmia</div>
                            <div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"Asthma/COPD") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>Asthma/COPD</div>
                       		<div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"Bradycardia") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>Bradycardia</div>
                            <div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"CHF") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  CHF </label>	
                            </div>
                            <div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"Sulfa Allergy") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/> Sulfa Allergy</div>
                       		<div class="inner_col-2">
                                <img src="../interface/chart_notes/graph_images/<?php echo(strpos($elem_warnings,"Depression") !== false)? "checked.png" : "checked_empty.png" ;?>" class="img_check"/>  <label>  Depression </label>	
                            </div> 
                           <div class="inner_col m_top_0">                        	
                                <div class="od_wrap" style="color:#333 !important">  IOP Targets</div>	
                            </div>
                            <div class="clearfix"></div> 
							<div class="inner_col-2">
                               <label style="margin-right:10px;line-height:36px;font-weight:bold;">   Trgt </label>	
                               <div class="b_half_wrap">
                                <span style="float:left;color:#006699;font-size:18px;font-weight:bold;line-height:36px;"> OD </span> <div class="b_half"><?php echo $targetOd;?></div>
							  </div>
                              <div class="b_half_wrap">
                                <span style=" float:left;color:#00992f;font-size:18px;font-weight:bold;line-height:36px;"> OS </span> <div class="b_half"><?php echo $targetOs;?></div>
							  </div>
                            </div>
                         </div>	  		
          			 </div>
                </div> <!-- Content GFs -->
                
                 <div class="content_gfs">
                    <div class="inner_content_gfs padding_0">
						<div class="wrap_table_1 wrap_t_style">
						  <table style="width:100%; float:left;background:#fff; border-collapse:collapse;">	
                        	<caption style="text-align:center;padding:4px;color:#006699;font-size:18px ;font-weight:bold;background:#fff;">
                                	Medication Grid
                            </caption>   
                             <thead>
                                	<tr>
                                    	<th style="width:296px;"> Ocular Med  </th>
                                      	<th style="width:100px;"> Site   </th>
                                    	<th style="width:150px;"> Sig </th>
                                    	<th style="width:150px;"> Started </th>
                                    	<th style="width:auto;"> Comments </th>
                                    </tr>	
                              </thead>
                          </table>  
                          <div  style="max-height:150px; overflow-y:auto; float:left;width:100%;">
                              <Table style="width:100%; float:left;background:#fff; border-collapse:collapse;" class="border-sty">
                            	 <tbody>
                                 <?php
                                    $lm=8;		
                                    if($total_meds>$lm){
                                        $lm=$total_meds+1;
                                    }else{
                                        $lm=$lm;
                                    }
                                    if($_REQUEST['chk_allergy_status'] == "dis_")
                                        $readonly = " readonly ";
                                    else 
                                        $readonly = "";
                                    for($i=0;$i<$lm;$i++)
                                    {
                                        if($i >= $ocular_count && $i < $dis_glaucoma_count){
                                        $readonly = " readonly ";
                                        $dis_glaucoma = 1;
                                        }
                                        else{
                                        $readonly = "";
                                        $dis_glaucoma = 0;
                                        }
                                        $elem_dis_glaucoma = "elem_dis_glaucoma_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_list_id = $$elem_listId;
                                        $elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_medication = $$elem_medicationId;
                                        $elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_dateStart = $$elem_dateStartId;
                                        $elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_dateStopped = $$elem_dateStoppedId;
                                        $elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_reasonDiscontinued = $$elem_reasonDiscontinuedId;
                                        $elem_gridId = "elem_grid_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_grid = $$elem_gridId;
                                        $elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_status = $$elem_statusId;
                                        $elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_compliant = $$elem_compliantId;
                                        $elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_date = $$elem_dateId;
                                        
                                        $elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_site = $$elem_siteId;
                                        
                                        $elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($i+1);
                                        $elem_sig = $$elem_sigId;
                            
                                        $cssStyle = "color:#F00;";
                                        if(strtoupper($elem_status) == 'ACTIVE' || strtoupper($elem_status) == 'RENEW'){
                                            $cssStyle = "color:#390;"; //Green
                                        }
                                        
                                        $curDate = date('Y-m-d',strtotime($elem_date));
                                        if(strtotime($curDate) == strtotime(date('Y-m-d'))){
                                            $cssStyle = "color:#36F;"; //Blue
                                        }
                                        
                                        if($elem_compliant=='0' || (strtoupper($elem_status) != 'ACTIVE' && strtoupper($elem_status) != 'RENEW')){
                                            $cssStyle = "color:#F00;"; // Black
                                        }
                                        
                                        if(strtoupper($elem_status) == 'DISCONTINUE'){
                                            $trStyle= "background-color:#ff9853;";
                                        }
                                        if(is_glaucoma_med($elem_medication)){
                                            $trStyle = "background-color:#69D200";
                                        }
                                        if($elem_medication==""){
                                            $cssStyle = "color:#000000;";
                                        }
                                        $elem_site_val="";
                                        if($elem_site==1){
                                            $elem_site_val="OS";
                                        }
                                        if($elem_site==2){
                                            $elem_site_val="OD";
                                        }
                                        if($elem_site==3){
                                            $elem_site_val="OU";
                                        }
                                        if($elem_site==4){
                                            $elem_site_val="PO";
                                        }
                                        if($elem_medication)
                                        {
                                        echo "<tr style=\"border-bottom:1px solid #333;".$trStyle."\" >";
                                            echo "<td style=\"width:296px;\">".$elem_medication."</td>";
                                            echo "<td style=\"width:100px;\">".$elem_site_val."</td>";
                                            if($chk_allergy_status==""){	
                                                echo "<td style=\"width:150px;\">".$elem_sig."</td>";
                                                echo "<td style=\"width:150px;\">".$elem_dateStart."</td>";
                                            }else{
                                                echo "<td style=\"width:150px;\">".$elem_dateStopped."</td>";
                                            }
                                            echo "<td style=\"width:auto;\">".$elem_reasonDiscontinued."</td>";
                                            echo "</tr>";
                                        }
                                    }
                                ?>	
                                </tbody>
                            </Table>
                          </div>   <!-- overflow-scroll --> 
                        </div>	
                    </div>
                 </div>
                 
                 
                <div class="content_gfs ">
                    <div class="inner_content_gfs padding_0">
						<div class="wrap_table_1 wrap_t_style">
						  <table style="width:100%; float:left;background:#fff; border-collapse:collapse;">	
                        	<caption style="text-align:center;padding:4px;color:#006699;font-size:18px ;font-weight:bold;background:#fff;">
                                	Surgery
                            </caption>   
                             <thead>
                                	<tr>
                                    	<th style="width:296px;"> Ocular Med  </th>
                                      	<th style="width:100px;"> Site   </th>
                                    	<th style="width:300px;"> Date</th>
                                    	<th style="width:auto;"> Comments </th>
                                    </tr>	
                              </thead>
                          </table>  
                          <div  style="max-height:150px; overflow-y:auto; float:left;width:100%;">
                              <Table style="width:100%; float:left;background:#fff; border-collapse:collapse;" class="border-sty">
                            	 <tbody>
                                 <?php	
                                $lm=4;
                                $lm = ($countR > $lm)? $countR : $lm;
                                for($i=0;$i<$lm;$i++)
                                {
                                    $elem_listId = "elem_listId_".($i+1);
                                    $elem_list_id = $$elem_listId;
                                    $elem_surgeryId = "elem_surgery_".($i+1);
                                    $elem_surgery = $$elem_surgeryId;
                                    $elem_siteId = "elem_site_".($i+1);
                                    $elem_site_int = $$elem_siteId;
                                    $elem_dateStartId = "elem_dateStart_".($i+1);
                                    $elem_dateStart = $$elem_dateStartId;
                                    $elem_dateStoppedId = "elem_dateStopped_".($i+1);
                                    $elem_dateStopped = $$elem_dateStoppedId;
                                    $elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".($i+1);
                                    $elem_reasonDiscontinued = $$elem_reasonDiscontinuedId;
                                    $elem_gridId = "elem_grid_".($i+1);
                                    $elem_grid = $$elem_gridId;		
                                    $surg_sites="";
                                    if($elem_site_int==1){
                                        //OS
                                        $surg_sites="OS";
                                    }
                                    if($elem_site_int==2){
                                        //OD
                                        $surg_sites="OD";
                                    }
                                    if($elem_site_int==3){
                                        //OU
                                        $surg_sites="OU";
                                    }
                                    if($elem_site_int==4){
                                        //PO
                                        $surg_sites="PO";
                                    }
                                    if($elem_surgery)
                                    {
                            ?>	
                                	<tr style="border-bottom:1px solid #333;"> 
                                    	<td style="width:296px;"><?php echo $elem_surgery; ?></td>
                                      	<td style="width:100px;"><?php echo $surg_sites; ?>
                                        	
                                        </td>
                                     	<td style="width:300px;"><?php echo $elem_dateStart; ?></td>
                                    
                                    	<td style="width:auto;"><?php echo $elem_reasonDiscontinued; ?></td>
                                    </tr>
                                <?php }
							} ?>	
                                </tbody>
                            </Table>
                          </div>   <!-- overflow-scroll --> 
                        </div>	
                    </div>
                 </div>
             <?php 
	$surg_qry=imw_query("select title,begdate,sites from lists where pid='".$patient."' and type='6'");
	while($surg_row=imw_fetch_array($surg_qry)){
		$surg_arr[]=$surg_row;
	}
	$arrReturn = array();
	$gfs_qry = imw_query("SELECT 
						  c1.*,
						  c2.purge_status,		
						  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
						  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
						  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth			  			  
						  FROM glaucoma_past_readings c1
						  LEFT JOIN chart_master_table c2 ON c2.id = c1.formId
						  WHERE patientId ='".$patient."' 
						  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, c1.id DESC")or die(imw_error());
	$i=0;
	$rec_num=imw_num_rows($gfs_qry);
	while($record=imw_fetch_array($gfs_qry)){
		$i++;
		$logId=$record["id"];
		$log_ta_time=$record["ta_time"];
		$log_tp_time=$record["tp_time"];
		$log_tx_time=$record["tx_time"];
		$logTaOd = $record['taOd'];
		$logTaOs = $record['taOs'];
		$logTpOd = $record['tpOd'];
		$logTpOs = $record['tpOs'];
		$logTxOd = $record['txOd'];
		$logTxOs = $record['txOs'];
		$gonio_od_summary = $record['gonio_od_summary'];
		$gonio_os_summary = $record['gonio_os_summary'];
		$fundus_od_cd_ratio=$record["fundus_od_cd_ratio"];
		$fundus_os_cd_ratio=$record["fundus_os_cd_ratio"];
		$fundus_od_summary=$record["fundus_od_summary"];
		$fundus_os_summary=$record["fundus_os_summary"];
		
		
		
		if($record['taOd']>0 || $record['taOs']>0){
			$pressure_od=$record['taOd'];
			$pressure_os=$record['taOs'];
		}else if($logTxOd>0 || $logTxOs>0){ 
			$pressure_od=$record['tpOd'];
			$pressure_os=$record['tpOs'];
		}else{
			$pressure_od=$record['txOd'];
			$pressure_os=$record['txOs'];
		}
		
		$test_data_arr=array();
		$test_qry=imw_query("select test_type from glaucoma_past_test where glaucoma_past_id='$logId'");
		while($test_row=imw_fetch_array($test_qry)){
			$test_data_arr[]=$test_row['test_type'];
		}
		$test_data_exp=strtoupper(implode(", ",$test_data_arr));
					
		
		$gonio_od_summary_data="";
		$gonio_os_summary_data="";
		$sle_od_summary_data="";
		$sle_os_summary_data="";
		$fundus_od_summary_data="";
		$fundus_os_summary_data="";
		$fundus_od_cd_ratio_data="";
		$fundus_os_cd_ratio_data="";
		$log_va_od_summary_data="";
		$log_va_os_summary_data="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_iop_od_summary_data="";
		$log_iop_os_summary_data="";
		$fundus_od_cd_ratio_line="";
		
		if($gonio_od_summary!=""){
			$gonio_od_summary_data="<tr><td class='text_10'><font color='blue'>OD: </font>".$gonio_od_summary."</td></tr>";
		}
		if($gonio_os_summary!=""){
			$gonio_os_summary_data="<tr><td class='text_10'><font color='green'>OS: </font>".$gonio_os_summary."</td></tr>";
		}
		
		if($fundus_od_cd_ratio!=""){
			$fundus_od_cd_ratio_data="<tr><td class='text_10'><font color='blue'>OD </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_od_cd_ratio)."</td></tr>";
		}
		if($fundus_os_cd_ratio!=""){
			$fundus_os_cd_ratio_data="<tr><td class='text_10'><font color='Green'>OS </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_os_cd_ratio)."</td></tr>";
		}
		if(($fundus_od_cd_ratio!="" || $fundus_os_cd_ratio!="") && ($fundus_od_summary!="" || $fundus_os_summary!="")){
			$fundus_od_cd_ratio_line="<tr><td style='border-bottom:1px solid #CCE6FF; width:100%; padding-top:2px;'></td></tr>";
		}
		if($fundus_od_summary!=""){
			$fundus_od_summary_data="<tr><td class='text_10'><font color='blue'>OD: </font>".$fundus_od_summary."</td></tr>";
		}
		if($fundus_os_summary!=""){
			$fundus_os_summary_data="<tr><td class='text_10'><font color='Green'>OS: </font>".$fundus_os_summary."</td></tr>";
		}
		
		if($record['va_od_summary']!=""){
			$log_va_od_summary_col= "<span style=\"color:blue;font-weight:bold;font-size:18px;\"> OD </span> 
									".$record['va_od_summary'];
		}
		$log_va_od_summary_data=$log_va_od_summary_col."&nbsp;";
		
		if($record['va_os_summary']!=""){
			$log_va_os_summary_col= "<span style=\"color:Green;font-weight:bold;font-size:18px;\"> OS </span> 
									".$record['va_os_summary'];
		}
		$log_va_os_summary_data=$log_va_os_summary_col."&nbsp;";
		
		//if($logTaOd>0 && $logTaOs>0){
			$pressure_os=$pressure_od=$iop_od_summary_val_arr['ta']=$iop_os_summary_val_arr['ta']=$iop_od_summary_val_arr['tp']=$iop_os_summary_val_arr['tp']=$iop_od_summary_val_arr['tx']=$iop_os_summary_val_arr['tx']='';
		if($logTaOd>0 || $logTaOs>0){
			$pressure_od=$logTaOd;
			$pressure_os=$logTaOs;
			$iop_od_summary_val_arr['ta']=$logTaOd;
			$iop_os_summary_val_arr['ta']=$logTaOs;
		}else if($logTxOd>0 || $logTxOs>0){ //if($logTxOd>0 && $logTxOs>0){
			$pressure_od=$logTxOd;
			$pressure_os=$logTxOs;
		}else{
			$pressure_od=$logTpOd;
			$pressure_os=$logTpOs;
		}
		//if($logTxOd>0 && $logTxOs>0){
		if($logTxOd>0 || $logTxOs>0){
			$iop_od_summary_val_arr['tx']=$logTxOd;
			$iop_os_summary_val_arr['tx']=$logTxOs;
		}
		//if($logTpOd>0 && $logTpOs>0){
		if($logTpOd>0 || $logTpOs>0){
			$iop_od_summary_val_arr['tp']=$logTpOd;
			$iop_os_summary_val_arr['tp']=$logTpOs;
		}
		
		arsort($iop_od_summary_val_arr);
		arsort($iop_os_summary_val_arr);						
		$log_iop_od_summary=implode(',',$iop_od_summary_val_arr);
		$log_iop_os_summary=implode(',',$iop_os_summary_val_arr);	
		
		$iop_od_summary_val_arr['ta']=($iop_od_summary_val_arr['ta'])?$iop_od_summary_val_arr['ta']:'&nbsp;';
		$iop_od_summary_val_arr['tx']=($iop_od_summary_val_arr['tx'])?$iop_od_summary_val_arr['tx']:'&nbsp;';
		$iop_od_summary_val_arr['tp']=($iop_od_summary_val_arr['tp'])?$iop_od_summary_val_arr['tp']:'&nbsp;';
		
		$iop_os_summary_val_arr['ta']=($iop_os_summary_val_arr['ta'])?$iop_os_summary_val_arr['ta']:'&nbsp;';
		$iop_os_summary_val_arr['tx']=($iop_os_summary_val_arr['tx'])?$iop_os_summary_val_arr['tx']:'&nbsp;';
		$iop_os_summary_val_arr['tp']=($iop_os_summary_val_arr['tp'])?$iop_os_summary_val_arr['tp']:'&nbsp;';
		
		$log_iop_od_summary_col=$log_iop_od_summary_data='';
		if($log_iop_od_summary!="" || $log_iop_os_summary!=""){
			$log_iop_od_summary_col= "				
				<div style=\"float:left;display:inline-block;margin-left:5px;\">
					<table style=\"float:none;width:221px;\">
					<tbody style=\"float:left;width:100%;\">	
					<tr style=\"float:left;width:100%;display:inline-block;border-bottom:1px solid #333;\">
						<th style=\"width:45px;border-right:1px solid #333;\">&nbsp;&nbsp;   </th>
						<td style=\"width:45px;border-right:1px solid #333;\">T<sub>A</sub> </td>
						<td style=\"width:45px;\">T<sub>X</sub></td>
						<td style=\"width:45px;\">T<sub>P</sub>  </td>
					</tr>
					<tr style=\"float:left;width:100%;display:inline-block;border-bottom:1px solid #333;\">
						<th style=\"color:#006699;font-size:16px;width:60px;border-right:1px solid #333;\"> OD  </th>
						<td style=\"font-size:16px;width:60px;border-right:1px solid #333;\">".$iop_od_summary_val_arr['ta']." </td>
						<td style=\"width:60px;\">".$iop_od_summary_val_arr['tx']."</td>
						<td style=\"width:60px;\">".$iop_od_summary_val_arr['tp']."</td>
					</tr>
					 <tr style=\"float:left;width:100%;display:inline-block;\">
						<th style=\"color:#00992f;font-size:16px;width:60px;border-right:1px solid #333;\"> OS</th>
						<td style=\"font-size:16px;width:60px;border-right:1px solid #333;\">".$iop_os_summary_val_arr['ta']."</td>
						<td style=\"font-size:16px;width:60px;\">".$iop_os_summary_val_arr['tx']."</td>
						<td style=\"font-size:16px;width:60px;\">".$iop_os_summary_val_arr['tp']."</td>
					</tr>
					</tbody>
					</table>	
			   </div>
				";
		}else{
			$log_iop_od_summary_col = '';
		}
		
		$log_iop_od_summary_data=$log_iop_od_summary_col;
	
		$log_time_mil_arr=array();
		$log_iop_time_data="";
		if($log_ta_time!=""){
			if(strpos($log_ta_time,'AM')>0){
				$log_ta_time_mil=str_replace(':','',str_replace('AM','',$log_ta_time));
			}else{
				$log_ta_time=str_replace('PM',' PM',$log_ta_time);
				$log_ta_time_mil = date('Hi', strtotime($log_ta_time));
			}
			//$log_time_mil_arr[]=$log_ta_time_mil;
			$log_time_mil_arr[]=$log_ta_time;
		}
		if($log_tp_time!=""){
			if(strpos($log_tp_time,'AM')>0){
				$log_tp_time_mil=str_replace(':','',str_replace('AM','',$log_tp_time));
			}else{
				$log_tp_time=str_replace('PM',' PM',$log_tp_time);
				$log_tp_time_mil = date('Hi', strtotime($log_tp_time));
			}
			//$log_time_mil_arr[]=$log_tp_time_mil;
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
		
		$log_iop_time_data=str_replace('PM',' PM',str_replace('AM',' AM',$log_time_mil_arr[0]))."&nbsp;";
		
		//$row_date[$i][]=$record['dateReading'];
		$TrString.='<tr style="border-bottom:1px solid #333;font-size:14px;"><td style="">'.$record['dateReading'].'</td></tr>';//date
		
		$TrString.='<tr style="border-bottom:1px solid #333;"><td style="width:100%;word-wrap:break-word;display:inline-block;float:left;min-height:182px;max-height:182px;"> 
						<div style="float:left;display:inline-block;"> '.$log_va_od_summary_data.' '.$log_va_os_summary_data.'  
						'.$log_iop_time_data.' </div> '.$log_iop_od_summary_data.'</td></tr>';// od os reading
						
		$TrString.='<tr style="border-bottom:1px solid #333;font-size:14px;"> 
						<td style="width:100%;word-wrap:break-word;display:inline-block;float:left;">
						'.$record['ocular_med'].'&nbsp;</td></tr>';				//ocular med
		
		$TrString.='<tr style="font-size:14px;"> 
                         <td style="width:100%;word-wrap:break-word;display:inline-block;float:left;">
                         '.$record['medication'].' &nbsp;</td> </tr>';				//comments
		$row_data[$i]=$TrString;unset($TrString);

	}
	
	
	
	echo '<div class="content_gfs ">
			<div class="inner_content_gfs padding_0" style="position:relative;">
				<div class="inner_content_head" style="position:relative">
					<span style="border-bottom:1px solid #006699; color:#006699;padding-bottom:0px;"> Log </span>
					<img src="../interface/chart_notes/graph_images/arrow_gray.png" style="width:10px; position:absolute;top:22px;left:26px;">
				</div>
				<div class="wrap_table_1 wrap_t_style2 " id="" style="margin-top:40px;">
				   <table style="width:150px;float:left;display:table-cell;height:330px;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;z-index:9;position:relative;">
					 <thead style="line-height: 23.7px;float:left;">
							<tr style="width:100%;border-bottom:1px solid #333;float:left;">
								<th style="width:296px;">Date  </th>    
							</tr>	
							<tr style="width:100%;border-bottom:1px solid #333;float:left; line-height: 178px;">
								<th style="width:296px;"> Visual Acuity </th>    
							</tr>	
							<tr style="width:100%;border-bottom:1px solid #333;float:left;">
								<th style="width:296px;"> Ocular Med  </th>    
							</tr>	
							<tr style="float:left;width:100%;display:inline-block;">
								<th style="width:296px;"> Comment </th>    
							</tr>	
					 </thead>
				  </table>';
						  
	echo '<div style="float: left; max-width: 729px; min-width:729px;display:inline-block; overflow-x:scroll ;white-space:nowrap;
                          overflow-x:auto;margin-left:-1px;z-index:0;">';
	if(count($rec_num)>0){	
		//for($f=0;$f<11;$f++){
			//echo $field_data[0][$f];
			$skip_sur_data=array();
			
			for($k=0;$k<=$rec_num;$k++){
				if($row_data[$k])
				{
					echo '<Table style="float:none;display:table-cell;width:400px;word-wrap:break-word;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px; border-collapse:collapse;max-height:auto !important;" class="t_scroll">	
									<tbody style="height:100%;line-height: 27.6px;  float: left;width:100%;background:#fff;">';
					echo $row_data[$k];
					echo'</tbody> </Table> ';
				}
				$sur_data="";
				
				if($f==0){
				
					$chart_date_arr=explode('-',$row_date[$k][$f]);
					$chart_date_exp=$chart_date_arr[2].'-'.$chart_date_arr[0].'-'.$chart_date_arr[1];
					$chart_date_arr2=explode('-',$row_date[$k+1][$f]);
					$chart_date_exp2=$chart_date_arr2[2].'-'.$chart_date_arr2[0].'-'.$chart_date_arr2[1];
					for($j=0;$j<count($surg_arr);$j++){
						if($surg_arr[$j]['begdate']!="0000-00-00" && !in_array($j,$skip_sur_data)){
							if($surg_arr[$j]['begdate']>$chart_date_exp2 && $surg_arr[$j]['begdate']<=$chart_date_exp){
								$begdate_arr=explode('-',$surg_arr[$j]['begdate']);
								$begdate_exp=$begdate_arr[1].'-'.$begdate_arr[2].'-'.substr($begdate_arr[0],2);
								//$begdate_exp = get_date_format($surg_arr[$j]['begdate'],'','',2);
								if($surg_arr[$j]['sites']==1){
									//OS
									$surg_site="OS";
								}
								if($surg_arr[$j]['sites']==2){
									//OD
									$surg_site="OD";
								}
								if($surg_arr[$j]['sites']==3){
									//OU
									$surg_site="OU";
								}
								if($surg_arr[$j]['sites']==4){
									//PO
									$surg_site="PO";
								}
								
								$sur_data="yes";
								$skip_sur_data[]=$j;
							}
						}
						
					}
					
					if($sur_data!=""){
					
						echo '<div  style="display:table-cell;background:url(../interface/chart_notes/graph_images/surgery_img.png) repeat-y center center;width:18px;padding:10px;">  </div>';
					}
				}
			}
			 
//		}//log end here
	}
	
	echo "
	 </div> 
	<!-- overflow-scroll --> 
                        </div>
                    </div>
                 </div>";
		
?>


                
            </div>
        </div>
	</div>
    
   
</body>
</html>
