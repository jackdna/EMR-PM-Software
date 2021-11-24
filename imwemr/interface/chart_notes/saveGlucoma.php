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

File: saveGlucoma.php
Purpose: This file provides Save process in Glucoma.
Access Type : Direct
*/

	require_once('../../config/globals.php');
	extract($_REQUEST);
	//Check patient session and closing popup if no patient in session
	$window_popup_mode = true;
	require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

	$library_path = $GLOBALS['webroot'].'/library';
	include_once $GLOBALS['srcdir']."/classes/common_function.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/ChartCorrectionValues.php";
	include_once $GLOBALS['srcdir']."/classes/SaveFile.php";

	$pid = $_SESSION['patient'];
	$auth_id = $_SESSION['authId'];

	$glaucoma_obj = New ChartGlucoma($pid);
	
	// function Log Elements
	function getLogElements($str)
	{
		$arrRet = array();
		if(!empty($str))
		{
			$valueSap = "!#!";
			$fieldSap = "~*~";
			$arrFields = array();
			$arrFields = explode($fieldSap,$str);
			$len = count($arrFields);			
			for($i=0;$i<$len;$i++)
			{
				$strField = $arrFields[$i];  
				$arrValues = explode($valueSap,$strField);
				$name = $arrValues[0];
				$val =  $arrValues[1];
				$chk_dis_med = strrpos($name,"_dis_");
				$chk_dis_med = false;
				$indx = strrpos($name,"_");		
				if($indx !== false && $chk_dis_med !== false){
					$id = substr($name,$chk_dis_med+1);					
					$name = substr($name,0,$chk_dis_med);
				}else{
					$id = substr($name,$indx+1);					
					$name = substr($name,0,$indx);
				}
				$arrRet[$id][$name] = $val;		
			}		
		}
		return $arrRet;	
	}
	
	function  saveGlucomaGrid_old($arr,$elem_glucomaId,$type)
	{
		$arrIds = array();
		$arrCurIds = array();
		$sql = "SELECT id FROM glucoma_grid 
				WHERE glucomaId='".$elem_glucomaId."' 
				AND typeGrid ='".$type."' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$arrIds[$i] = $row["id"];			
		}
		
		$sql_med=imw_query("select medicine_name from medicine_data where glucoma='1' AND del_status = '0'");
		while($row_med=imw_fetch_array($sql_med)){
			$glucoma_med_arr[]=$row_med["medicine_name"];
		}
			
		if(count($arr) > 0)
		{
			foreach($arr as $key => $val)
			{	
				$allergy_status="";	
				if(strrpos($key,"dis_")!== false){
					$allergy_status="Discontinue";
				}else{
					$allergy_status="Active";
				}
				
				if($type == "Medication"){
					$medication = addslashes($val["elem_medication"]);
				}else if($type == "Systemic"){
					$medication = addslashes($val["elem_medication"]);
				}else if($type == "Surgery"){
					$medication = addslashes($val["elem_surgery"]);
				}
				$dateStart = set_date_YY_format($val["elem_dateStart"]);
				$dateStop = set_date_YY_format($val["elem_dateStopped"]);
				$reason = addslashes($val["elem_reasonDiscontinued"]);
				$gridId = trim($val["elem_grid"]);
				$site="";
				if(strtolower(trim($val["elem_site"]))=='os'){
					$site=1;
				}
				if(strtolower(trim($val["elem_site"]))=='od'){
					$site=2;
				}
				if(strtolower(trim($val["elem_site"]))=='ou'){
					$site=3;
				}
				if(strtolower(trim($val["elem_site"]))=='po'){
					$site=4;
				}
				$sig=$val["elem_sig"];
				$glucomaId = $elem_glucomaId; 
				$list_id=$val["elem_listId"];
				$med_type="";
				if($type == "Medication" && in_array($medication,$glucoma_med_arr)){
					$med_type="glucoma";
					
				}
				if(!empty($medication))
				{
					if(!empty($gridId))
					{	$list_id = "";
						$sql  = "SELECT id FROM LISTS WHERE title = '".$medication."' AND pid = '".$_SESSION['patient']."' AND del_allergy_status = 0";
						$res = imw_query($sql);
						if(imw_num_rows($res)>0){
							$row = imw_fetch_assoc($res);
							$list_id = $row['id'];
						}
						if($type == "Medication")
						$list_type = 4;
						if($type == "Surgery")
						$list_type = 6;
						if($list_type == 4 || $list_type == 6){
							if($list_id ==""){
							$sql = "INSERT INTO lists
									(
									id, date, title, begdate, enddate, med_comments, type, sites, sig, allergy_status, pid, compliant )
									VALUES
									(
									NULL,'".date('Y-m-d H:i:s')."' ,
									'".$medication."','".$dateStart."',
									'".$dateStop."','".$reason."',
									'".$list_type."','".$site."','".$sig."',
									'".$allergy_status."','".$_SESSION['patient']."','2'
									) 
									";
							$list_id = sqlInsert($sql);
							}
							else{
							imw_query("UPDATE lists SET
									begdate ='".$dateStart."', enddate = '".$dateStop."', med_comments = '".$reason."',
									type = '".$list_type."', sites = '".$site."', sig = '".$sig."', allergy_status = '".$allergy_status."',  compliant =2
									WHERE id = '".$list_id."'
									");
							}
						}
						//Update
						$sql = "UPDATE glucoma_grid
								SET
								glucomaId = '".$glucomaId."',
								medication = '".$medication."', dateStart='".$dateStart."',
								dateStop='".$dateStop."', reason='".$reason."',
								typeGrid='".$type."',
								site='".$site."',
								sig='".$sig."',
								allergy_status='".$allergy_status."',
								med_type='".$med_type."',
								list_id='".$list_id."'
								WHERE id='".$gridId."' ";
						$sql = sqlQuery($sql);
						$arrCurIds[] = $gridId;				
					}
					else
					{
						//Insert
						$sql  = "SELECT id FROM LISTS WHERE title = '".$medication."' AND pid = '".$_SESSION['patient']."' AND del_allergy_status = 0";
						$res = imw_query($sql);
						if(imw_num_rows($res)>0){
							$row = imw_fetch_assoc($res);
							$list_id = $row['id'];
						}
						if($type == "Medication")$list_type = 4;
						if($type == "Surgery")$list_type = 6;
					   if($list_type == 4 || $list_type == 6){
							if($list_id ==""){
									$sql = "INSERT INTO lists
										(
										id, date, title, begdate, enddate, med_comments, type, sites, sig, allergy_status, pid, compliant)
										VALUES
										(
										NULL,'".date('Y-m-d H:i:s')."' ,
										'".$medication."','".$dateStart."',
										'".$dateStop."','".$reason."',
										'".$list_type."','".$site."','".$sig."',
										'".$allergy_status."','".$_SESSION['patient']."','2'
										) 
										";
								$list_id = sqlInsert($sql);
							  }
							else{
								imw_query("UPDATE lists SET
									begdate ='".$dateStart."', enddate = '".$dateStop."', med_comments = '".$reason."',
									type = '".$list_type."', sites = '".$site."', sig = '".$sig."', allergy_status = '".$allergy_status."',  compliant =2
									WHERE id = '".$list_id."'
									");
							 }
						}
						$sql = "INSERT INTO glucoma_grid
								(
								id, glucomaId,
								medication, dateStart,
								dateStop, reason,
								typeGrid,site,sig,allergy_status,med_type,list_id	
								)
								VALUES
								(
								NULL,'".$glucomaId."',
								'".$medication."','".$dateStart."',
								'".$dateStop."','".$reason."',
								'".$type."','".$site."','".$sig."',
								'".$allergy_status."','".$med_type."','".$list_id."'
								) 
								";
						$insertId = sqlInsert($sql);						
						$arrCurIds[] = $insertId; 				
					}					
				}		
			}
		}//die();
		$len = count($arrIds);
		for($i=0;$i<$len;$i++)
		{
			if(array_search($arrIds[$i],$arrCurIds) === false)
			{
				$sql = "DELETE FROM glucoma_grid WHERE id ='".$arrIds[$i]."' ";
				$row = sqlQuery($sql);				
			}			
		}
	}
	function set_date_YY_format($elem_dateStart){
		list($mm,$dd,$yy)=explode("-",$elem_dateStart);
		if(strlen($yy)==2){$yy="20".$yy;}
		if(!checkdate($mm,$dd,$yy)){
			$elem_dateStart="";
		}
		if($elem_dateStart){
			$yy_year="";
			$cur_year=date("y")+5;
			$arr_elem_dateStart=explode("-",$elem_dateStart);
			$yy_year=end($arr_elem_dateStart);
			$st_year="20";
			if($yy_year>=$cur_year){
				$st_year="19";
			}
			if(strlen($yy_year)==2){
				$arr_elem_dateStart[2]=$st_year.$yy_year;
				$elem_dateStart=implode("-",$arr_elem_dateStart);	
			}
		}
		return $elem_dateStart;	
	}	
	function  saveGlucomaGrid($arr,$elem_glucomaId,$type)
	{
		
		if(count($arr) > 0)
		{
			foreach($arr as $key => $val)
			{	
				if($val["elem_dis_glaucoma"]){
					$allergy_status="Discontinue";
				}else{
					$allergy_status="Active";
				}
				
				if($type == "Medication"){
					$medication = addslashes($val["elem_medication"]);
				}else if($type == "Surgery"){
					$medication = addslashes($val["elem_surgery"]);
				}
				
				if($val["elem_dateStart"]){
					$val["elem_dateStart"]=set_date_YY_format($val["elem_dateStart"]);
				}
				if($val["elem_dateStopped"]){
					$val["elem_dateStopped"]=set_date_YY_format($val["elem_dateStopped"]);	
				}
				
				$dateStart = getDateFormatDB($val["elem_dateStart"]);
				$dateStop = getDateFormatDB($val["elem_dateStopped"]);
				$reason = addslashes($val["elem_reasonDiscontinued"]);
				$site="";
				if(strtolower(trim($val["elem_site"]))=='os'){
					$site=1;
				}
				if(strtolower(trim($val["elem_site"]))=='od'){
					$site=2;
				}
				if(strtolower(trim($val["elem_site"]))=='ou'){
					$site=3;
				}
				if(strtolower(trim($val["elem_site"]))=='po'){
					$site=4;
				}
				$sig=$val["elem_sig"];
				$list_id=$val["elem_listId"];
				$med_type="";
				if($type == "Medication"){
					$list_type = 4;
					$med_comments = "med_comments";
				}
				if($type == "Surgery"){
					$list_type = 6;
					$med_comments = "comments";
				}
				if(!empty($medication) && strrpos($key,"dis_")=== false)
				{
					if(!empty($list_id)){
						$sql  = "SELECT id FROM LISTS WHERE id = '".$list_id."'";
						$res = imw_query($sql);
						if(imw_num_rows($res)>0){
							$sql = "UPDATE lists SET
									title = '".$medication."',
									begdate ='".$dateStart."',enddate = '".$dateStop."',$med_comments = '".$reason."',
									type = '".$list_type."', sites = '".$site."', sig = '".$sig."',compliant =2,
									allergy_status = '".$allergy_status."'
									WHERE id = '".$list_id."'
									";
							imw_query($sql);
							}
					}else{
						$sql = "INSERT INTO lists
									(
									id, date, title, begdate,enddate, $med_comments, type, sites, sig,pid, compliant, allergy_status )
									VALUES
									(
									NULL,'".date('Y-m-d H:i:s')."' ,
									'".$medication."','".$dateStart."',
									'".$dateStop."',
									'".$reason."',
									'".$list_type."','".$site."','".$sig."',
									'".$_SESSION['patient']."','2','".$allergy_status."'
									) 
									";
						$list_id = sqlInsert($sql);
						
					}
				}
			}		
		}//die();
	}
	//
	function insertGlucomaReading($arr)
	{
		global $glaucoma_obj;
		if(count($arr) > 0)
		{
			$elem_date = getDateFormatDB($arr["elem_date"]);
			$elem_taOd = $arr["elem_taOd"];
			$elem_taOs = $arr["elem_taOs"];
			//$elem_tpOd = $arr["elem_tpOd"];
			//$elem_tpOs = $arr["elem_tpOs"];
			$elem_txOd = $arr["elem_txOd"];
			$elem_txOs = $arr["elem_txOs"];
			
			$elem_vfOdSummary = $arr["elem_vfOdSummary"];
			$elem_vfOsSummary = $arr["elem_vfOsSummary"];
			$elem_nfaOd = $arr["elem_nfaOd"];
			$elem_nfaOs = $arr["elem_nfaOs"];
			$elem_cdOd = $arr["elem_cdOd"];
			$elem_cdOs = $arr["elem_cdOs"];
			$elem_gonioOdSummary = $arr["elem_gonioOdSummary"];
			$elem_gonioOsSummary = $arr["elem_gonioOsSummary"];
			$elem_medication = $arr["elem_medication"];
			$elem_patientId = $arr["elem_patientId"];
			$elem_formId = $arr["elem_formId"];		
			$elem_pachyOdReads = $arr["elem_pachyOdReads"];
			$elem_pachyOdAvg = $arr["elem_pachyOdAvg"];
			$elem_pachyOdCorr = $arr["elem_pachyOdCorr"];
			$elem_pachyOsReads = $arr["elem_pachyOsReads"];
			$elem_pachyOsAvg = $arr["elem_pachyOsAvg"];
			$elem_pachyOsCorr = $arr["elem_pachyOsCorr"];
			$elem_diskPhotoOd = $arr["elem_diskPhotoOd"];
			$elem_diskPhotoOs = $arr["elem_diskPhotoOs"];			
			$elem_vfOd = addslashes($arr["elem_vfOd"]);
			$elem_vfOs = addslashes($arr["elem_vfOs"]);
			$elem_scanOd = addslashes($arr["elem_scanOd"]);
			$elem_scanOs = addslashes($arr["elem_scanOs"]);
			$elem_diskFundus = $arr["elem_diskFundus"];
			$treatmentChange = $arr["treatmentChange"];
			$elem_timeReading = date("h:i A");
			$elem_diagnosisDate = getDateFormatDB($arr["elem_diagnosisDate"]);
			$elem_highTaOdDate = getDateFormatDB($arr["elem_highTaOdDate"]);
			$elem_highTaOsDate = getDateFormatDB($arr["elem_highTaOsDate"]);
			//$elem_highTpOdDate = wv_formatDate($arr["elem_highTpOdDate"]);
			//$elem_highTpOsDate = wv_formatDate($arr["elem_highTpOsDate"]);
			$elem_highTxOdDate = getDateFormatDB($arr["elem_highTxOdDate"]);
			$elem_highTxOsDate = getDateFormatDB($arr["elem_highTxOsDate"]);
			
			$elem_vfDate = getDateFormatDB($arr["elem_vfDate"]);
			$elem_nfaDate = getDateFormatDB($arr["elem_nfaDate"]);
			$elem_gonioDate = getDateFormatDB($arr["elem_gonioDate"]);
			$elem_pachyDate = getDateFormatDB($arr["elem_pachyDate"]);
			$elem_diskPhotoDate = getDateFormatDB($arr["elem_diskPhotoDate"]);
			$elem_cdDate = getDateFormatDB($arr["elem_cdDate"]);	
			$elem_cee = $arr["elem_cee"];
			$elem_ceeDate = getDateFormatDB($arr["elem_ceeDate"]);
			$elem_ceeNotes = $arr["elem_ceeNotes"];
			$elem_timeReadingMil = $glaucoma_obj->chTimeFormat($elem_timeReading);
			
			/// INSERT
			$sql = "INSERT INTO 
					glaucoma_past_readings
					(id, dateReading, timeReading, taOd, taOs, tpOd, tpOs, txOd, txOs,
					vfOdSummary, vfOsSummary, nfaOdSummary, nfaOsSummary, cdOd, cdOs, 
					gonioOdSummary, gonioOsSummary,	medication,					
					patientId,formId,
					pachyOdReads,pachyOdAvg,pachyOdCorr,
					pachyOsReads,pachyOsAvg,pachyOsCorr,
					diskPhotoOd,diskPhotoOs,
					vfOd,vfOs,scanOd,scanOs,
					diskFundus,treatmentChange,
					diagnosisDate,highTaOdDate,
					highTaOsDate,highTpOdDate,
					highTpOsDate,highTxOdDate,
					highTxOsDate,vfDate,
					nfaDate,gonioDate,
					pachyDate,diskPhotoDate,
					cdDate,cee,ceeDate,ceeNotes,
					time_read_mil
					)
					VALUES
					(NULL, '".$elem_date."', '".$elem_timeReading."', '".$elem_taOd."', '".$elem_taOs."', '".$elem_tpOd."', '".$elem_tpOs."', '".$elem_txOd."', '".$elem_txOs."',
					'".$elem_vfOdSummary."', '".$elem_vfOsSummary."', '".$elem_nfaOd."', '".$elem_nfaOs."', '".$elem_cdOd."', '".$elem_cdOs."', 
					'".$elem_gonioOdSummary."', '".$elem_gonioOsSummary."',	'".$elem_medication."',					
					'".$elem_patientId."', '".$elem_formId."',
					'".$elem_pachyOdReads."','".$elem_pachyOdAvg."','".$elem_pachyOdCorr."',
					'".$elem_pachyOsReads."','".$elem_pachyOsAvg."','".$elem_pachyOsCorr."',
					'".$elem_diskPhotoOd."','".$elem_diskPhotoOs."',
				   	'".$elem_vfOd."','".$elem_vfOs."','".$elem_scanOd."','".$elem_scanOs."', 
					'".$elem_diskFundus."','".$treatmentChange."', 
					'".$elem_diagnosisDate."','".$elem_highTaOdDate."', 
					'".$elem_highTaOsDate."','".$elem_highTpOdDate."',
					'".$elem_highTpOsDate."','".$elem_highTxOdDate."',
					'".$elem_highTxOsDate."','".$elem_vfDate."', 
					'".$elem_nfaDate."','".$elem_gonioDate."', 
					'".$elem_pachyDate."','".$elem_diskPhotoDate."', 
					'".$elem_cdDate."','".$elem_cee."','".$elem_ceeDate."','".$elem_ceeNotes."',
					'".$elem_timeReadingMil."'
					)
				   ";									
			$insertIdGlucoma = sqlInsert($sql);
			return $insertIdGlucoma;
		}
	}	
	
	//Main Glucoma
	$elem_patientId = $_POST["elem_patientId"];
	$elem_glucomaId = $_POST["elem_glucomaId"];
	$elem_dateActivation = wv_formatDate($_POST["elem_dateActivation"]);
	$elem_activate = $_POST["elem_activate"];
	$elem_dateDiagnosis = $_POST["elem_dateDiagnosis"];
	$dxDescription = $_POST["diagnosis_description"];
	$elem_stagingCode_od = $_POST["staging_code_od"];
	$elem_stagingCode_os = $_POST["staging_code_os"];	
	$elem_dateHighTmaxOd = get_date_format($_POST["elem_dateHighTmaxOd"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTmaxOdOd = $_POST["elem_highTmaxOdOd"];
	$elem_dateHighTmaxOs = get_date_format($_POST["elem_dateHighTmaxOs"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTmaxOsOs = $_POST["elem_highTmaxOsOs"];
	
	$elem_diagnosisOd = imw_real_escape_string($_POST["elem_diagnosisOd"]);
	$elem_diagnosisOs = imw_real_escape_string($_POST["elem_diagnosisOs"]);
	$elem_dateHighTaOd = get_date_format($_POST["elem_dateHighTaOd"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTaOdOd = imw_real_escape_string($_POST["elem_highTaOdOd"]);
	$elem_highTaOdOs = imw_real_escape_string($_POST["elem_highTaOdOs"]);
	$elem_dateHighTaOs = get_date_format($_POST["elem_dateHighTaOs"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTaOsOd = imw_real_escape_string($_POST["elem_highTaOsOd"]);
	$elem_highTaOsOs = imw_real_escape_string($_POST["elem_highTaOsOs"]);
	
	$elem_dateHighTxOd = get_date_format($_POST["elem_dateHighTxOd"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTxOdOd = imw_real_escape_string($_POST["elem_highTxOdOd"]);
	$elem_highTxOdOs = imw_real_escape_string($_POST["elem_highTxOdOs"]);
	$elem_dateHighTxOs = get_date_format($_POST["elem_dateHighTxOs"],inter_date_format(),'mm-dd-yyyy');
	$elem_highTxOsOd = imw_real_escape_string($_POST["elem_highTxOsOd"]);
	$elem_highTxOsOs = imw_real_escape_string($_POST["elem_highTxOsOs"]);	
	
	$elem_dateVf = get_date_format($_POST["elem_dateVf"],inter_date_format(),'mm-dd-yyyy');
	$elem_vfOdSummary = ($_POST["elem_vfOdSummary"] == "Other") 
						? imw_real_escape_string($_POST["elem_vfOdSummaryOther"]) : imw_real_escape_string($_POST["elem_vfOdSummary"]);
	$elem_vfOsSummary = ($_POST["elem_vfOsSummary"] == "Other") 
						? imw_real_escape_string($_POST["elem_vfOsSummaryOther"]) : imw_real_escape_string($_POST["elem_vfOsSummary"]);
	$elem_vfOd = imw_real_escape_string($_POST["elem_vfOd"]);
	$elem_vfOs = imw_real_escape_string($_POST["elem_vfOs"]);
	$elem_nfaOd = imw_real_escape_string($_POST["elem_nfaOd"]);
	$elem_nfaOs = imw_real_escape_string($_POST["elem_nfaOs"]);
	$elem_dateNfa = get_date_format($_POST["elem_dateNfa"],inter_date_format(),'mm-dd-yyyy');
	$elem_nfaOdSummary = ($_POST["elem_nfaOdSummary"] == "Other") 
						? imw_real_escape_string($_POST["elem_nfaOdSummaryOther"]) : imw_real_escape_string($_POST["elem_nfaOdSummary"]);
	$elem_nfaOsSummary = ($_POST["elem_nfaOsSummary"] == "Other") 
						? imw_real_escape_string($_POST["elem_nfaOsSummaryOther"]) : imw_real_escape_string($_POST["elem_nfaOsSummary"]);
	$elem_dateGonio = get_date_format($_POST["elem_dateGonio"],inter_date_format(),'mm-dd-yyyy');
	$elem_gonioOd = imw_real_escape_string($_POST["elem_gonioOd"]);
	$elem_gonioOs = imw_real_escape_string($_POST["elem_gonioOs"]);
	$elem_datePachy = get_date_format($_POST["elem_datePachy"],inter_date_format(),'mm-dd-yyyy');
	$elem_pachyOdReads = imw_real_escape_string($_POST["elem_pachyOdReads"]);
	$elem_pachyOdAvg = imw_real_escape_string($_POST["elem_pachyOdAvg"]);
	$elem_pachyOdCorr = imw_real_escape_string($_POST["elem_pachyOdCorr"]);
	$elem_pachyOsReads = imw_real_escape_string($_POST["elem_pachyOsReads"]);
	$elem_pachyOsAvg = imw_real_escape_string($_POST["elem_pachyOsAvg"]);
	$elem_pachyOsCorr = imw_real_escape_string($_POST["elem_pachyOsCorr"]);
	$elem_dateDiskPhoto = get_date_format($_POST["elem_dateDiskPhoto"],inter_date_format(),'mm-dd-yyyy');
	$elem_diskPhotoOd = imw_real_escape_string($_POST["elem_diskPhotoOd"]);
	$elem_diskPhotoOs = imw_real_escape_string($_POST["elem_diskPhotoOs"]);
	$elem_dateCd = get_date_format($_POST["elem_dateCd"],inter_date_format(),'mm-dd-yyyy');
	$elem_cdOd = imw_real_escape_string($_POST["elem_cdOd"]);
	$elem_cdOs = imw_real_escape_string($_POST["elem_cdOs"]);
	$elem_cdOdSummary = imw_real_escape_string($_POST["elem_cdOdSummary"]);
	$elem_cdOsSummary = imw_real_escape_string($_POST["elem_cdOsSummary"]);
	$elem_riskFactors = imw_real_escape_string($_POST["elem_riskFactors"]);
	$elem_warnings = imw_real_escape_string($_POST["elem_warnings"]);
	$elem_cdAppOd = imw_real_escape_string($_POST["elem_cdAppOd"]);
	$elem_cdAppOs = imw_real_escape_string($_POST["elem_cdAppOs"]);
	$elem_notes = (trim($_POST["elem_notes"]) == "Notes:") ? "" : imw_real_escape_string($_POST["elem_notes"]);
        $elem_dateCee = wv_formatDate($_POST["elem_dateCee"]);
        $elem_cee = imw_real_escape_string($_POST["elem_cee"]);
        $elem_print = imw_real_escape_string($_POST["elem_print"]);
	$elem_ceeNotes = imw_real_escape_string($_POST["elem_ceeNotes"]);
	$elem_initialChanged = imw_real_escape_string($_POST["elem_initialChanged"]);
	$elem_mediGridChanged = imw_real_escape_string($_POST["elem_mediGridChanged"]);
	$elem_surgeryChanged = imw_real_escape_string($_POST["elem_surgeryChanged"]);
	$elem_logChanged = imw_real_escape_string($_POST["elem_logChanged"]);
	$elem_addNewChanged = imw_real_escape_string($_POST["elem_addNewChanged"]);
	//
	$elem_trgtIopOd = imw_real_escape_string($_POST["elem_trgtIopOd"]);
	$elem_trgtIopOs = imw_real_escape_string($_POST["elem_trgtIopOs"]);
	//
	
	//--
	
	//osavefile
	$oSaveFile = new SaveFile($elem_patientId);
	
	$imgcd_app_od=$imgcd_app_os="";			
	if(isset($_POST["sig_datacd_app_od"]) && !empty($_POST["sig_datacd_app_od"])){
		$imgcd_app_od = $oSaveFile->mkHx2Img($_POST["sig_datacd_app_od"],"GFS","OD");
	}
	
	if(isset($_POST["sig_datacd_app_os"]) && !empty($_POST["sig_datacd_app_os"])){
		$imgcd_app_os = $oSaveFile->mkHx2Img($_POST["sig_datacd_app_os"],"GFS","OS");
	}

	
	if(!empty($elem_initialChanged) || !empty($elem_mediGridChanged) || !empty($elem_systemicGrid) || !empty($elem_surgeryChanged))
	{
	
		if(isset($elem_glucomaId) && !empty($elem_glucomaId))
		{
			//UPDATE
			$sql = "UPDATE glucoma_main 
					SET				
					dateActivation='".$elem_dateActivation."',				
					activate='".$elem_activate."',
					patientId='".$elem_patientId."',
					dateDiagnosis='".$elem_dateDiagnosis."',
					diagnosis_description='".$dxDescription."',
					diagnosisOd='".$elem_diagnosisOd."',
					diagnosisOs='".$elem_diagnosisOs."',
					staging_code_od='".$elem_stagingCode_od."',
					staging_code_os='".$elem_stagingCode_os."',					
					elem_dateHighTmaxOd='".$elem_dateHighTmaxOd."',
					elem_highTmaxOdOd='".$elem_highTmaxOdOd."',
					elem_dateHighTmaxOs='".$elem_dateHighTmaxOs."',
					elem_highTmaxOsOs='".$elem_highTmaxOsOs."',
					dateHighTaOd='".$elem_dateHighTaOd."',
					highTaOdOd='".$elem_highTaOdOd."',
					highTaOdOs='".$elem_highTaOdOs."',
					dateHighTaOs='".$elem_dateHighTaOs."',
					highTaOsOd='".$elem_highTaOsOd."',
					highTaOsOs='".$elem_highTaOsOs."',
					dateHighTpOd='".$elem_dateHighTpOd."',
					highTpOdOd='".$elem_highTpOdOd."',
					highTpOdOs='".$elem_highTpOdOs."',
					dateHighTpOs='".$elem_dateHighTpOs."',
					highTpOsOd='".$elem_highTpOsOd."',
					highTpOsOs='".$elem_highTpOsOs."',
					dateHighTxOd='".$elem_dateHighTxOd."',
					highTxOdOd='".$elem_highTxOdOd."',
					highTxOdOs='".$elem_highTxOdOs."',
					dateHighTxOs='".$elem_dateHighTxOs."',
					highTxOsOd='".$elem_highTxOsOd."',
					highTxOsOs='".$elem_highTxOsOs."',
					dateVf='".$elem_dateVf."',
					vfOdSummary='".$elem_vfOdSummary."',
					vfOsSummary='".$elem_vfOsSummary."',
					dateNfa='".$elem_dateNfa."',
					nfaOdSummary='".$elem_nfaOdSummary."',
					nfaOsSummary='".$elem_nfaOsSummary."',
					dateGonio='".$elem_dateGonio."',
					gonioOd='".$elem_gonioOd."',
					gonioOs='".$elem_gonioOs."',
					datePachy='".$elem_datePachy."',
					pachyOdReads='".$elem_pachyOdReads."',
					pachyOdAvg='".$elem_pachyOdAvg."',
					pachyOdCorr='".$elem_pachyOdCorr."',
					pachyOsReads='".$elem_pachyOsReads."',
					pachyOsAvg='".$elem_pachyOsAvg."',
					pachyOsCorr='".$elem_pachyOsCorr."',
					dateDiskPhoto='".$elem_dateDiskPhoto."',
					diskPhotoOd='".$elem_diskPhotoOd."',
					diskPhotoOs='".$elem_diskPhotoOs."',
					dateCd='".$elem_dateCd."',
					cdOd='".$elem_cdOd."',
					cdOs='".$elem_cdOs."',
					cdOdSummary='".$elem_cdOdSummary."',
					cdOsSummary='".$elem_cdOsSummary."',
					riskFactors='".$elem_riskFactors."',
					warnings='".$elem_warnings."',
					cdAppOd='".$elem_cdAppOd."',
					cdAppOs='".$elem_cdAppOs."',
					notes='".$elem_notes."',
					cee='".$elem_cee."',
					ceeDate='".$elem_dateCee."',
					ceeNotes='".$elem_ceeNotes."',
					iopTrgtOd='".$elem_trgtIopOd."',
					iopTrgtOs='".$elem_trgtIopOs."',
					imgcd_app_od='".$imgcd_app_od."',
					imgcd_app_os='".$imgcd_app_os."'
				WHERE glucomaId= '".$elem_glucomaId."'
						";		
			//echo $sql;
			//exit();
			$res = sqlQuery($sql);
			
		}
		else
		{
			//INSERT
			$sql = "INSERT INTO glucoma_main 
					(
					glucomaId, dateActivation,activate,patientId,
					dateDiagnosis,diagnosis_description,diagnosisOd,diagnosisOs,
					staging_code_od,staging_code_os,
					elem_dateHighTmaxOd,elem_highTmaxOdOd,elem_dateHighTmaxOs,elem_highTmaxOsOs,
					dateHighTaOd,highTaOdOd,highTaOdOs,
					dateHighTaOs,highTaOsOd,highTaOsOs,
					dateHighTpOd,highTpOdOd,highTpOdOs,
					dateHighTpOs,highTpOsOd,highTpOsOs,
					dateHighTxOd,highTxOdOd,highTxOdOs,
					dateHighTxOs,highTxOsOd,highTxOsOs,
					dateVf,	vfOdSummary,vfOsSummary,
					dateNfa,nfaOdSummary,nfaOsSummary,
					dateGonio,gonioOd,gonioOs,
					datePachy,pachyOdReads,	pachyOdAvg,
					pachyOdCorr,pachyOsReads,pachyOsAvg,
					pachyOsCorr,dateDiskPhoto,diskPhotoOd,
					diskPhotoOs,dateCd,	cdOd,
					cdOs,cdOdSummary,cdOsSummary,riskFactors,warnings,
					cdAppOd,cdAppOs,notes,cee,ceeDate,ceeNotes,
					iopTrgtOd,iopTrgtOs,imgcd_app_od,imgcd_app_os
					)
					VALUES
					(
					NULL,'".$elem_dateActivation."','".$elem_activate."','".$elem_patientId."',
					'".$elem_dateDiagnosis."','".$dxDescription."','".$elem_diagnosisOd."','".$elem_diagnosisOs."',
					'".$elem_stagingCode_od."','".$elem_stagingCode_os."',
					'".$elem_dateHighTmaxOd."','".$elem_highTmaxOdOd."','".$elem_dateHighTmaxOs."','".$elem_highTmaxOsOs."',					
					'".$elem_dateHighTaOd."','".$elem_highTaOdOd."','".$elem_highTaOdOs."',
					'".$elem_dateHighTaOs."','".$elem_highTaOsOd."','".$elem_highTaOsOs."',
					'".$elem_dateHighTpOd."','".$elem_highTpOdOd."','".$elem_highTpOdOs."',
					'".$elem_dateHighTpOs."','".$elem_highTpOsOd."','".$elem_highTpOsOs."',
					'".$elem_dateHighTxOd."','".$elem_highTxOdOd."','".$elem_highTxOdOs."',
					'".$elem_dateHighTxOs."','".$elem_highTxOsOd."','".$elem_highTxOsOs."',
					'".$elem_dateVf."','".$elem_vfOdSummary."','".$elem_vfOsSummary."',
					'".$elem_dateNfa."','".$elem_nfaOdSummary."','".$elem_nfaOsSummary."',
					'".$elem_dateGonio."','".$elem_gonioOd."','".$elem_gonioOs."',
					'".$elem_datePachy."','".$elem_pachyOdReads."','".$elem_pachyOdAvg."',
					'".$elem_pachyOdCorr."','".$elem_pachyOsReads."','".$elem_pachyOsAvg."',
					'".$elem_pachyOsCorr."','".$elem_dateDiskPhoto."','".$elem_diskPhotoOd."',
					'".$elem_diskPhotoOs."','".$elem_dateCd."','".$elem_cdOd."',
					'".$elem_cdOs."','".$elem_cdOdSummary."','".$elem_cdOsSummary."','".$elem_riskFactors."','".$elem_warnings."',
					'".$elem_cdAppOd."','".$elem_cdAppOs."','".$elem_notes."',
					'".$elem_cee."','".$elem_dateCee."','".$elem_ceeNotes."',
					'".$elem_trgtIopOd."','".$elem_trgtIopOs."','".$imgcd_app_od."','".$imgcd_app_os."'
					)";
			$elem_glucomaId = sqlInsert($sql);		
		}
	
		//if Deactivate
		if($elem_activate == "0")
		{
			$arr["elem_date"] = $elem_dateActivation;
			$arr["elem_taOd"] = $elem_highTaOdOd;
			$arr["elem_taOs"] = $elem_highTaOsOs;
			$arr["elem_txOd"] = $elem_highTxOdOd;
			$arr["elem_txOs"] = $elem_highTxOsOs;			
			$arr["elem_vfOdSummary"] = $elem_vfOdSummary;
			$arr["elem_vfOsSummary"] = $elem_vfOsSummary;
			$arr["elem_nfaOd"] = $elem_nfaOd;
			$arr["elem_nfaOs"] = $elem_nfaOs;
			$arr["elem_cdOd"] = $elem_cdOd;
			$arr["elem_cdOs"] = $elem_cdOs;
			$arr["elem_gonioOdSummary"] = $elem_gonioOd;
			$arr["elem_gonioOsSummary"] = $elem_gonioOs;
			$arr["elem_medication"] = "Activate Date:".$elem_dateActivation." ".$elem_notes;		
			$arr["elem_patientId"] = $elem_patientId;
			$arr["elem_formId"] = 0;		
			$arr["elem_pachyOdReads"] = $elem_pachyOdReads;
			$arr["elem_pachyOdAvg"] = $elem_pachyOdAvg;
			$arr["elem_pachyOdCorr"] = $elem_pachyOdCorr;
			$arr["elem_pachyOsReads"] = $elem_pachyOsReads;
			$arr["elem_pachyOsAvg"] = $elem_pachyOsAvg;
			$arr["elem_pachyOsCorr"] = $elem_pachyOsCorr;
			$arr["elem_diskPhotoOd"] = $elem_diskPhotoOd;
			$arr["elem_diskPhotoOs"] = $elem_diskPhotoOs;		
			$arr["elem_vfOd"] = "";
			$arr["elem_vfOs"] = "";
			$arr["elem_scanOd"] = "";
			$arr["elem_scanOs"] = "";
			$arr["elem_diskFundus"] = (($elem_diskPhotoOd == "Done") || ($elem_diskPhotoOs == "Done")) ? "Disc" : "Fundus";
			$arr["treatmentChange"] = "";		
			$arr["elem_diagnosisDate"] = $elem_diagnosisOd;
			$arr["elem_highTaOdDate"] = $elem_dateHighTaOd;
			$arr["elem_highTaOsDate"] = $elem_dateHighTaOs;
			$arr["elem_highTxOdDate"] = $elem_dateHighTxOd;
			$arr["elem_highTxOsDate"] = $elem_dateHighTxOs;
			$arr["elem_vfDate"] = $elem_dateVf;
			$arr["elem_nfaDate"] = $elem_dateNfa;
			$arr["elem_gonioDate"] = $elem_dateGonio;
			$arr["elem_pachyDate"] = $elem_datePachy;
			$arr["elem_diskPhotoDate"] = $elem_dateDiskPhoto;
			$arr["elem_cdDate"] = $elem_dateCd;		
			$arr["elem_cee"] = $elem_cee;        
			$arr["elem_ceeDate"] = $elem_dateCee;
			$arr["elem_ceeNotes"] = $elem_ceeNotes;
			//Insert Glucoma
			insertGlucomaReading($arr);				
		}	
	
		//Medication
		$arrMedicationGrid = getLogElements($_POST["elem_medicationGrid"]);	
		$len = count($arrMedicationGrid);
		if($len > 0)
		{
			saveGlucomaGrid($arrMedicationGrid,$elem_glucomaId,"Medication");				
		}
		//Systemic
		$arrSystemicGrid = getLogElements($_POST["elem_systemicGrid"]);	
		$len = count($arrSystemicGrid);
		if($len > 0)
		{
			saveGlucomaGrid($arrSystemicGrid,$elem_glucomaId,"Systemic");				
		}
		//Surgery
		$arrSurgeryGrid = getLogElements($_POST["elem_surgeryGrid"]);	
		$len = count($arrSurgeryGrid);
		if($len > 0)
		{	
			saveGlucomaGrid($arrSurgeryGrid,$elem_glucomaId,"Surgery");		
		}		
		//Set All glucoma_past_reading->rec_status to 1
		$sql = "UPDATE glaucoma_past_readings SET rec_status = '1' WHERE patientId = '".$elem_patientId."'";
		$row = sqlQuery($sql);		
	}
	
	//Edit Log
	if(!empty($elem_logChanged)){
		//echo $_POST["elem_log"];
		$arrEditLog = getLogElements($_POST["elem_log"]);	
		$len = count($arrEditLog);
		if($len > 0){
			foreach($arrEditLog as $key => $val){			
					
					$id = $val["elem_id"];
					$tc = imw_real_escape_string($val["elem_treatmentChange"]);
					$medi = imw_real_escape_string($val["elem_medication"]);	
					$show_data = imw_real_escape_string($val["elem_show_data"]);	
					$highlight_data = imw_real_escape_string($val["elem_highlight_data"]);							
					$sql = "UPDATE glaucoma_past_readings SET 
							medication = '".$medi."',
							treatmentChange = '".$tc."',
							show_data = '".$show_data."',
							highlight_data = '".$highlight_data."'
							WHERE id='".$id."'	
							";
					$res = sqlQuery($sql);					
			}
		}	
	}
	
	// ADD New Form
	$elem_Add_addNew = $_POST["elem_Add_addNew"];	
	if(($elem_Add_addNew == "1") && !empty($elem_addNewChanged))
	{
		$elem_date = get_date_format($_POST["elem_Add_date"],inter_date_format(),'mm-dd-yyyy');
		$elem_taOd = imw_real_escape_string($_POST["elem_Add_taOd"]);
		$elem_taOs = imw_real_escape_string($_POST["elem_Add_taOs"]);
		$elem_vfOdSummary = imw_real_escape_string($_POST["elem_Add_vfOdSummary"]);
		$elem_vfOsSummary = imw_real_escape_string($_POST["elem_Add_vfOsSummary"]);
		$elem_gonioOdSummary = imw_real_escape_string($_POST["elem_Add_gonioOd"]);
		$elem_gonioOsSummary = imw_real_escape_string($_POST["elem_Add_gonioOs"]);
		$elem_cdOd = imw_real_escape_string($_POST["elem_Add_cdOd"]);
		$elem_cdOs = imw_real_escape_string($_POST["elem_Add_cdOs"]);
		$elem_medication = ($_POST["elem_Add_medication"] == trim("Medication & Recommendation:")) 
							? "" : imw_real_escape_string($_POST["elem_Add_medication"]);
		$elem_timeReading = $_POST["elem_Add_time"];
		$elem_show_data = $_POST["elem_Add_show_data"];
		$elem_highlight_data = $_POST["elem_highlight_data"];
		$elem_timeReadingMil = $glaucoma_obj->chTimeFormat($elem_timeReading);
		//$elem_Add_timeStamp = (!empty($_POST["elem_Add_timeStamp"])) ? $_POST["elem_Add_timeStamp"] : time();
		$elem_tpOd = imw_real_escape_string($_POST["elem_Add_tpOd"]);
		$elem_tpOs = imw_real_escape_string($_POST["elem_Add_tpOs"]);
		$elem_txOd = imw_real_escape_string($_POST["elem_Add_txOd"]);
		$elem_txOs = imw_real_escape_string($_POST["elem_Add_txOs"]);
		$elem_nfaOd = imw_real_escape_string($_POST["elem_Add_nfaOdSummary"]);
		$elem_nfaOs = imw_real_escape_string($_POST["elem_Add_nfaOsSummary"]);
		$elem_tc = imw_real_escape_string($_POST["elem_Add_tc"]);
		$elem_diskPhotoOd = imw_real_escape_string($_POST["elem_Add_diskFundusOd"]);
		$elem_diskPhotoOs = imw_real_escape_string($_POST["elem_Add_diskFundusOs"]);
		$elem_diskFundus = (($elem_diskPhotoOd == "Done") || ($elem_diskPhotoOs == "Done")) ? "Disc" : "";
		$elem_cee = imw_real_escape_string($_POST["elem_Add_cee"]); 
		$elem_id = $_POST["elem_Add_id"];
		
		$elem_Add_va_od_summary = $_POST["elem_Add_va_od_summary"];
		$elem_Add_va_os_summary = $_POST["elem_Add_va_os_summary"];
		$elem_Add_glucoma_med = $_POST["elem_Add_glucoma_med"];
		$elem_Add_glucoma_med_allergies = $_POST["elem_Add_glucoma_med_allergies"];
		$elem_Add_gonio_od_summary = $_POST["elem_Add_gonio_od_summary"];
		$elem_Add_gonio_os_summary = $_POST["elem_Add_gonio_os_summary"];
		$elem_Add_sle_od_summary = $_POST["elem_Add_sle_od_summary"];
		$elem_Add_sle_os_summary = $_POST["elem_Add_sle_os_summary"];
		$elem_Add_fundus_od_cd_ratio = $_POST["elem_Add_fundus_od_cd_ratio"];
		$elem_Add_fundus_os_cd_ratio = $_POST["elem_Add_fundus_os_cd_ratio"];
		$elem_Add_fundus_od_summary = $_POST["elem_Add_fundus_od_summary"];
		$elem_Add_fundus_os_summary = $_POST["elem_Add_fundus_os_summary"];
		$elem_Add_test_data = $_POST["elem_Add_test_data"];
		$elem_Add_assessment = $_POST["elem_Add_assessment"];
		$elem_Add_plan = $_POST["elem_Add_plan"];
		$elem_Add_ocular_med = $_POST["elem_Add_ocular_med"];
		
		if(!empty($elem_taOd)) {
			$elem_highTaOdDate = $elem_date;
		}	
			
		if(!empty($elem_taOs)){
			$elem_highTaOsDate = $elem_date;
		}
		
		if(!empty($elem_txOd)){
			$elem_highTxOdDate = $elem_date;
		}
		
		if(!empty($elem_txOs)){
			$elem_highTxOsDate = $elem_date;
		}
		
		if(!empty($elem_gonioOdSummary) || !empty($elem_gonioOsSummary)){
			$elem_gonioDate = $elem_date;
		}
				
		if(!empty($elem_vfOdSummary) || !empty($elem_vfOsSummary)){
			$elem_vfDate = $elem_date;
		}
		
		if(!empty($elem_nfaOd) || !empty($elem_nfaOd)){
			$elem_nfaDate = $elem_date;
		}		
		
		if(!empty($elem_cdOd) || !empty($elem_cdOs)){
			$elem_cdDate = $elem_date;
		}
		
		if($elem_diskFundus == "Disc"){
			$elem_diskPhotoDate = $elem_date;
		}
		
		if(!empty($elem_cee)){
		$elem_ceeDate = $elem_date;  
		} 
		
		$elem_pachyOdReads_empty = "";						
		$elem_pachyOdAvg_empty = "";
		$elem_pachyOdCorr_empty = "";
		$elem_pachyOsReads_empty = "";						
		$elem_pachyOsAvg_empty = "";
		$elem_pachyOsCorr_empty = "";
		$elem_pachyDate_empty = "";
		
		if(empty($elem_id))
		{
		    /// INSERT
		    $sql = "INSERT INTO 
				    glaucoma_past_readings
				    (id, dateReading, timeReading, taOd, taOs, tpOd, tpOs, txOd, txOs,
				    vfOdSummary, vfOsSummary, nfaOdSummary, nfaOsSummary, cdOd, cdOs, 
				    gonioOdSummary, gonioOsSummary,	medication,				
				    patientId,formId,
				    pachyOdReads,pachyOdAvg,pachyOdCorr,
				    pachyOsReads,pachyOsAvg,pachyOsCorr,
				    diskPhotoOd,diskPhotoOs,
				    vfOd,vfOs,scanOd,scanOs,
				    diskFundus, treatmentChange,
				    diagnosisDate,highTaOdDate,
				    highTaOsDate,
				    highTpOdDate, highTpOsDate,
				    highTxOdDate, highTxOsDate,
				    vfDate,
				    nfaDate,gonioDate,
				    pachyDate,diskPhotoDate,
				    cdDate,cee,ceeDate,
					time_read_mil,
					va_od_summary,va_os_summary,glucoma_med,glucoma_med_allergies,gonio_od_summary,
					gonio_os_summary,sle_od_summary,sle_os_summary,fundus_od_cd_ratio,fundus_os_cd_ratio,fundus_od_summary,
					fundus_os_summary,assessment,plan,ocular_med
				    )
				    VALUES
				    (NULL, '".$elem_date."', '".$elem_timeReading."', '".$elem_taOd."', '".$elem_taOs."', '".$elem_tpOd."', '".$elem_tpOs."', '".$elem_txOd."', '".$elem_txOs."',
				    '".$elem_vfOdSummary."', '".$elem_vfOsSummary."', '".$elem_nfaOd."', '".$elem_nfaOs."', '".$elem_cdOd."', '".$elem_cdOs."', 
				    '".$elem_gonioOdSummary."', '".$elem_gonioOsSummary."',	'".$elem_medication."',				
				    '".$elem_patientId."', '".$elem_formId."',
				    '".$elem_pachyOdReads_empty."','".$elem_pachyOdAvg_empty."','".$elem_pachyOdCorr_empty."',
				    '".$elem_pachyOsReads_empty."','".$elem_pachyOsAvg_empty."','".$elem_pachyOsCorr_empty."',
				    '".$elem_diskPhotoOd."','".$elem_diskPhotoOs."', 
				    '".$elem_vfOd."','".$elem_vfOs."','".$elem_scanOd."','".$elem_scanOs."', 
				    '".$elem_diskFundus."','".$elem_tc."' ,
				    '".$elem_diagnosisDate."','".$elem_highTaOdDate."', 
				    '".$elem_highTaOsDate."','".$elem_highTpOdDate."',
				    '".$elem_highTpOsDate."','".$elem_highTxOdDate."',
				    '".$elem_highTxOsDate."','".$elem_vfDate."', 
				    '".$elem_nfaDate."','".$elem_gonioDate."', 
				    '".$elem_pachyDate_empty."','".$elem_diskPhotoDate."', 
				    '".$elem_cdDate."','".$elem_cee."','".$elem_ceeDate."',
					'".$elem_timeReadingMil."',
					'".$elem_Add_va_od_summary."','".$elem_Add_va_os_summary."','".$elem_Add_glucoma_med."','".$elem_Add_glucoma_med_allergies."','".$elem_Add_gonio_od_summary."',
					'".$elem_Add_gonio_os_summary."','".$elem_Add_sle_od_summary."','".$elem_Add_sle_os_summary."','".$elem_Add_fundus_od_cd_ratio."','".$elem_Add_fundus_os_cd_ratio."',
					'".$elem_Add_fundus_od_summary."','".$elem_Add_fundus_os_summary."','".$elem_Add_assessment."','".$elem_Add_plan."','".$elem_Add_ocular_med."'
				    )
			       ";		
		    $insertIdGlucoma = sqlInsert($sql);
			
			$elem_Add_test_data_exp=explode(',',$elem_Add_test_data);
			for($f=0;$f<=count($elem_Add_test_data_exp);$f++){
				if(trim($elem_Add_test_data_exp[$f])!=""){
					$test_type=strtolower(trim($elem_Add_test_data_exp[$f]));
					if($test_type!=""){
						imw_query("insert into glaucoma_past_test set test_type='$test_type',glaucoma_past_id='$insertIdGlucoma'");  
					}
				}
			}
		}
		else
		{
		$sql = "UPDATE glaucoma_past_readings 
                    SET
                    dateReading = '".$elem_date."', 
                    timeReading='".$elem_timeReading."', 
                    taOd='".$elem_taOd."', 
                    taOs='".$elem_taOs."', 
                    tpOd='".$elem_tpOd."', 
                    tpOs='".$elem_tpOs."',
		   			txOd='".$elem_txOd."', 
                    txOs='".$elem_txOs."',
                    vfOdSummary='".$elem_vfOdSummary."', 
                    vfOsSummary='".$elem_vfOsSummary."', 
                    nfaOdSummary='".$elem_nfaOd."', 
                    nfaOsSummary='".$elem_nfaOs."',
                    cdOd='".$elem_cdOd."', 
                    cdOs='".$elem_cdOs."', 
                    gonioOdSummary='".$elem_gonioOdSummary."', 
                    gonioOsSummary='".$elem_gonioOsSummary."',    
                    medication='".$elem_medication."',                
                    patientId='".$elem_patientId."',                    
                    diskPhotoOd='".$elem_diskPhotoOd."',
                    diskPhotoOs='".$elem_diskPhotoOs."',                    
                    treatmentChange='".$elem_tc."',
                    diagnosisDate='".$elem_diagnosisDate."',
                    highTaOdDate='".$elem_highTaOdDate."',
                    highTaOsDate='".$elem_highTaOsDate."',
                    highTpOdDate='".$elem_highTpOdDate."',
                    highTpOsDate='".$elem_highTpOsDate."',
		    highTxOdDate='".$elem_highTxOdDate."',
                    highTxOsDate='".$elem_highTxOsDate."',
                    vfDate='".$elem_vfDate."',
                    nfaDate='".$elem_nfaDate."',
                    gonioDate='".$elem_gonioDate."',                    
                    diskPhotoDate='".$elem_diskPhotoDate."',
                    cdDate='".$elem_cdDate."',
                    cee='".$elem_cee."',
                    ceeDate='".$elem_ceeDate."',
		    time_read_mil='".$elem_timeReadingMil."'
                    WHERE id = '".$elem_id."' 
                    ";
		$res = sqlQuery($sql);       
		}
     
	}	
	
	//Syncronise with iop trgts
	$curformId = $glaucoma_obj->isChartOpened($elem_patientId);		
	if($curformId == false){$curformId =0;}
	if(!empty($elem_trgtIopOd) || !empty($elem_trgtIopOs)){
		if($curformId != false){
			//check
			$cQry = "select sumOdIop, sumOsIop FROM chart_iop WHERE form_id='".$curformId."' ";
			$row = sqlQuery($cQry);			
			
			if($row == false){	// Insert
				
			}else{
				$sumOdIop = $row["sumOdIop"];
				$sumOsIop = $row["sumOsIop"];
				
				$sumOdIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOd,$sumOdIop);
				$sumOsIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOs,$sumOsIop);
				
				//Update
				$sql = "UPDATE chart_iop SET trgtOd='".$elem_trgtIopOd."', trgtOs='".$elem_trgtIopOs."',
						sumOdIop='".imw_real_escape_string($sumOdIop)."', 
						sumOsIop='".imw_real_escape_string($sumOsIop)."' WHERE form_id='".$curformId."' ";
				$res = sqlQuery($sql);
			}
			
			//VF
			//Update VF
			$sql = "UPDATE vf SET iopTrgtOd='".$elem_trgtIopOd."', iopTrgtOs='".$elem_trgtIopOs."' WHERE formId = '".$curformId."'  ";
			$res = sqlQuery($sql);
			
			//NFA
			$sql = "UPDATE nfa SET iopTrgtOd='".$elem_trgtIopOd."', iopTrgtOs='".$elem_trgtIopOs."' WHERE form_id = '".$curformId."'  ";
			$res = sqlQuery($sql);
			
		}else{
			$curformId = 0;
		}
		//Save IOP Def Vals
		$glaucoma_obj->saveIopTrgt($elem_trgtIopOd,$elem_trgtIopOs,$elem_patientId,$curformId);	
	}else{//if empty targt values
		$glaucoma_obj->remIopTrgtDefVal($elem_trgtIopOd,$elem_trgtIopOs,$elem_patientId,$curformId);
	}

	//Update formId pachy
	if($curformId != false && ($elem_pachyOdReads!=""||$elem_pachyOsReads!="")){
		$arr = array("elem_formId" => $curformId, "elem_od_readings" => $elem_pachyOdReads,
					"elem_od_average" => $elem_pachyOdAvg, "elem_od_correction_value" => $elem_pachyOdCorr,
					"elem_os_readings" => $elem_pachyOsReads, "elem_os_average" => $elem_pachyOsAvg,
					"elem_os_correction_value" => $elem_pachyOsCorr, "patientid" => $elem_patientId,
					"elem_cor_date" => $elem_datePachy
					);
		$oChartCorrectionValues = new ChartCorrectionValues($elem_patientId, $curformId);			
		$oChartCorrectionValues->saveCorrectionValues($arr);
		
		//check if Pachy is made and syncronize correction values
		//check
		$sql = "SELECT * FROM pachy WHERE formId='".$curformId."' AND patientId='".$elem_patientId."' ";
		$row = sqlQuery($sql);
		if($row != false){
			//Update
			$sql = "UPDATE pachy SET ".
				 "pachy_od_readings = '".$elem_pachyOdReads."', ".
				 "pachy_od_average='".$elem_pachyOdAvg."', ".
				 "pachy_od_correction_value = '".$elem_pachyOdCorr."', ".
				 "pachy_os_readings = '".$elem_pachyOsReads."', ".
				 "pachy_os_average = '".$elem_pachyOsAvg."', ".
				 "pachy_os_correction_value = '".$elem_pachyOsCorr."' ".
				 "WHERE formId = '".$curformId."' AND patientId='".$elem_patientId."' ";
			$row = sqlQuery($sql);
		}

		//Sync Ascan
		//check if A/Scan is made and syncronize correction values
		//check
		$sql = "SELECT surgical_id FROM surgical_tbl WHERE form_id='".$curformId."' AND patient_id='".$elem_patientId."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$pachymetryValOD = !empty($elem_pachyOdAvg) ? $elem_pachyOdAvg : $elem_pachyOdReads ;
			$pachymetryValOS = !empty($elem_pachyOsAvg) ? $elem_pachyOsAvg : $elem_pachyOsReads ;
			
			//Update
			$sql = "UPDATE surgical_tbl SET ".
				 "pachymetryValOD = '".$pachymetryValOD."', ".
				 "pachymetryCorrecOD = '".$elem_pachyOdCorr."', ".
				 "pachymetryValOS = '".$pachymetryValOS."', ".
				 "pachymetryCorrecOS = '".$elem_pachyOsCorr."' ".
				 "WHERE form_id = '".$curformId."' AND patient_id='".$elem_patientId."' ";
			$row = sqlQuery($sql);
		}
		
		//Sync IOL_Master
		//check if IOL_Master is made and syncronize correction values
		//check
		$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE form_id='".$curformId."' AND patient_id='".$elem_patientId."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$pachymetryValOD = !empty($elem_pachyOdAvg) ? $elem_pachyOdAvg : $elem_pachyOdReads ;
			$pachymetryValOS = !empty($elem_pachyOsAvg) ? $elem_pachyOsAvg : $elem_pachyOsReads ;
			
			//Update
			$sql = "UPDATE iol_master_tbl SET ".
				 "pachymetryValOD = '".$pachymetryValOD."', ".
				 "pachymetryCorrecOD = '".$elem_pachyOdCorr."', ".
				 "pachymetryValOS = '".$pachymetryValOS."', ".
				 "pachymetryCorrecOS = '".$elem_pachyOsCorr."' ".
				 "WHERE form_id = '".$curformId."' AND patient_id='".$elem_patientId."' ";
			$row = sqlQuery($sql);
		}		

	}	
	
	$loadFile="chart_glucoma.php?";
	if($elem_initialChanged == "1"){
		$loadFile .= "op=i";
	}
	
	if($elem_print == "1")
	{
		//$loadFile="chart_glucoma_print.php?op=i";
		echo "<script>";
		echo "var features = \"width=855,height=700,menubar=no,top=0,left=0,resizable=yes,scrollbars=yes\";";	
		echo "window.open(\"chart_glucoma_print.php\",\"docGlucomaPrint\",features);";	
		echo "self.close();";
		echo "</script>";
		//exit();
		$flgStopExec = 1;
	}
    	
	if(!isset($flgStopExec)){
		if( $_POST["elem_saveClose"] == "1"){
		echo "<script>";
		echo "window.close();";
		echo "</script>";
		//exit();
		$flgStopExec = 1;
		}
	}
	
	//header
	if(!isset($flgStopExec)){
		//header("Location:".$loadFile);	
		echo "<script>";
		echo "window.location.replace('".$loadFile."');";
		echo "</script>";
		//exit();
	}
?>