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

//--------------	IF NOT FINALIZED	---------------//
function getVisionCompareValues($afterFormID){
	global $vis_idC,$vis_mr_none_givenC,$vis_mr_od_sC,$vis_mr_od_cC,$vis_mr_od_aC,$visionODC,$glareODC;
	global $vis_ak_od_kC,$vis_ak_od_slashC,$vis_ak_od_xC,$vis_mr_os_sC,$vis_mr_os_cC,$vis_mr_os_aC;
	global $visionOSC,$glareOSC,$vis_ak_os_kC,$vis_ak_os_slashC,$vis_ak_os_xC,$provider_idODC,$provider_idOSC, $patient_id;
	
	$sql = "SELECT 
			c4.id as form_id, 
			c1.mr_none_given, c1.provider_id, c0.id as vis_id, 
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, 
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l,
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l,
			c5.k_od, c5.slash_od, c5.x_od, c5.k_os, c5.slash_os, c5.x_os, c5.k_type 
			FROM chart_master_table c4
			LEFT JOIN chart_vis_master c0 ON c4.id = c0.form_id
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id AND c1.ex_type='MR' AND c1.delete_by='0'	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			LEFT JOIN chart_ak c5 ON c5.id_chart_vis_master = c0.id
			WHERE  c4.patient_id = '".$patient_id."'   
			AND c4.delete_status='0' 
			AND (
				(mr_none_given='None Given' OR LOCATE(c1.ex_number, c1.mr_none_given) > 0)
				AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.txt_1!='' OR c2.txt_2!='' OR
					c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.txt_1!='' OR c3.txt_2!=''
					)
			)
			AND c4.id != '".$afterFormID."'	
			Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number DESC
			LIMIT 1;  ";
	
	$row = sqlQuery($sql);
	if($row!=false){
		
		$visionFormIdC = $row['form_id'];
		$vis_idC = $row['vis_id'];
		$vis_mr_none_givenC = $row['mr_none_given'];
		//======================= OD
		
		$vis_mr_od_sC = $row['sph_r'];
		$vis_mr_od_cC = $row['cyl_r'];
		$vis_mr_od_aC = $row['axs_r'];
		$visionODC = $row['txt_1_r'];
		$glareODC = $row['txt_2_r'];		
		
		//======================= OS
		$vis_mr_os_sC = $row['sph_l'];
		$vis_mr_os_cC = $row['cyl_l'];
		$vis_mr_os_aC = $row['axs_l'];
		$visionOSC = $row['txt_1_l'];
		$glareOSC = $row['txt_2_l'];
		
		$vis_ak_od_kC = $row['k_od'];
		$vis_ak_od_slashC = $row['slash_od'];
		$vis_ak_od_xC = $row['x_od'];
		
		$vis_ak_os_kC = $row['k_os'];
		$vis_ak_os_slashC = $row['slash_os'];
		$vis_ak_os_xC = $row['x_os'];
		$vis_ktypeC = $row["k_type"];
		
		$provider_idODC = $row['provider_id'];
		$provider_idOSC = $row['provider_id'];		
	}
	
}

function get_vision_values($patient_id, $finalize){
	global $visionFormId, $vis_id, $vis_mr_none_given, 
$vis_mr_od_s, $vis_mr_od_c, $vis_mr_od_a, $visionOD, $glareOD, 
$vis_ak_od_k, $vis_ak_od_slash, $vis_ak_od_x,

$vis_mr_os_s, $vis_mr_os_c, $vis_mr_os_a, $visionOS, $glareOS, 
$vis_ak_os_k, $vis_ak_os_slash, $vis_ak_os_x;
	
	$sql = "SELECT 
			c4.id as form_id, 
			c1.mr_none_given, c0.id as vis_id, 
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, 
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l,
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l,
			c5.k_od, c5.slash_od, c5.x_od, c5.k_os, c5.slash_os, c5.x_os, c5.k_type 
			FROM chart_master_table c4
			LEFT JOIN chart_vis_master c0 ON c4.id = c0.form_id
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			LEFT JOIN chart_ak c5 ON c5.id_chart_vis_master = c0.id
			WHERE  c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.delete_by='0'  
			AND c4.delete_status='0' 
			AND (
				(mr_none_given='None Given' OR LOCATE(c1.ex_number, c1.mr_none_given) > 0)
				AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.txt_1!='' OR c2.txt_2!='' OR
					c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.txt_1!='' OR c3.txt_2!=''
					)
			) 	
			Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number DESC
			LIMIT 1;  ";
	
	$row = sqlQuery($sql);
	if($row!=false){
		
		$visionFormId = $row['form_id'];
		$vis_id = $row['vis_id'];
		$vis_mr_none_given = $row['mr_none_given'];
		//======================= OD
		
		$vis_mr_od_s = $row['sph_r'];
		$vis_mr_od_c = $row['cyl_r'];
		$vis_mr_od_a = $row['axs_r'];
		$visionOD = $row['txt_1_r'];
		$glareOD = $row['txt_2_r'];		
		
		//======================= OS
		$vis_mr_os_s = $row['sph_l'];
		$vis_mr_os_c = $row['cyl_l'];
		$vis_mr_os_a = $row['axs_l'];
		$visionOS = $row['txt_1_l'];
		$glareOS = $row['txt_2_l'];
		
		$vis_ak_od_k = $row['k_od'];
		$vis_ak_od_slash = $row['slash_od'];
		$vis_ak_od_x = $row['x_od'];
		
		$vis_ak_os_k = $row['k_os'];
		$vis_ak_os_slash = $row['slash_os'];
		$vis_ak_os_x = $row['x_os'];
		$vis_ktype = $row["k_type"];
		
		//Comapre Value ( vision )
		if($finalize!=true){
			getVisionCompareValues($visionFormId);
		}
	}
	
	//--	
}

function get_ak_values($patient_id, $form_id){
	global $vis_mr_od_sC, $vis_mr_od_cC, $vis_mr_od_aC, $visionODC, 
$glareODC, $vis_ak_od_kC, $vis_ak_od_xC, $vis_ak_od_slashC, 
$vis_mr_os_sC, $vis_mr_os_cC, $vis_mr_os_aC, $visionOSC, 
$glareOSC, $vis_ak_os_kC, $vis_ak_os_xC, $vis_ak_os_slashC;

	if(!empty($form_id)){
		$getAKValuesCompStr = "SELECT 
								c4.sph as sph_r, c4.cyl as cyl_r, c4.axs as axs_r, 
								c4.txt_1 as txt_1_r, c4.txt_2 as txt_2_r, 
								c5.sph as sph_l, c5.cyl as cyl_l, c5.axs as axs_l,
								c5.txt_1 as txt_1_l, c5.txt_2 as txt_2_l,
								c2.k_od, c2.slash_od, c2.x_od, c2.k_os, c2.slash_os, c2.x_os, c2.k_type 
		
								FROM chart_vis_master c1
								INNER JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
								LEFT JOIN chart_pc_mr c3 ON c3.id_chart_vis_master = c1.id	
								LEFT JOIN chart_pc_mr_values c4 ON c3.id = c4.chart_pc_mr_id AND c4.site='OD'
								LEFT JOIN chart_pc_mr_values c5 ON c3.id = c5.chart_pc_mr_id AND c5.site='OS'	
								WHERE c1.patient_id = '$patient_id'
								AND	(((k_od != '') AND (x_od != '')) OR ((k_os != '') AND (x_os != '')))
								AND c1.form_id = '$form_id'
								ORDER BY c1.form_id DESC LIMIT 0, 1";
		$getAKValuesCompQry = imw_query($getAKValuesCompStr);
		$countAKCompRows = imw_num_rows($getAKValuesCompQry);
	}else{
		$countAKCompRows = 0;
	}	
	
	if($countAKCompRows<=0){
		$getAKValuesCompStr = "SELECT 
								c4.sph as sph_r, c4.cyl as cyl_r, c4.axs as axs_r, 
								c4.txt_1 as txt_1_r, c4.txt_2 as txt_2_r, 
								c5.sph as sph_l, c5.cyl as cyl_l, c5.axs as axs_l,
								c5.txt_1 as txt_1_l, c5.txt_2 as txt_2_l,
								c2.k_od, c2.slash_od, c2.x_od, c2.k_os, c2.slash_os, c2.x_os, c2.k_type 
								
								FROM chart_vis_master c1
								INNER JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
								LEFT JOIN chart_pc_mr c3 ON c3.id_chart_vis_master = c1.id	
								LEFT JOIN chart_pc_mr_values c4 ON c3.id = c4.chart_pc_mr_id AND c4.site='OD'
								LEFT JOIN chart_pc_mr_values c5 ON c3.id = c5.chart_pc_mr_id AND c5.site='OS'
								WHERE c1.patient_id = '$patient_id'
								AND	(((k_od != '') AND (x_od != '')) OR ((k_os != '') AND (x_os != '')))
								ORDER BY c1.form_id DESC LIMIT 0, 1";
		$getAKValuesCompQry = imw_query($getAKValuesCompStr);
	}
	$getAKValuesCompRows = imw_fetch_array($getAKValuesCompQry);
	//	OD
	$vis_mr_od_sC = $getAKValuesCompRows['sph_r'];
	$vis_mr_od_cC = $getAKValuesCompRows['cyl_r'];
	$vis_mr_od_aC = $getAKValuesCompRows['axs_r'];
	$visionODC = $getAKValuesCompRows['txt_1_r'];
	$glareODC = $getAKValuesCompRows['txt_2_r'];
	$vis_ak_od_kC = $getAKValuesCompRows['k_od'];
	$vis_ak_od_xC = $getAKValuesCompRows['x_od'];
	$vis_ak_od_slashC = $getAKValuesCompRows['slash_od'];
	//	OS
	$vis_mr_os_sC = $getAKValuesCompRows['sph_l'];
	$vis_mr_os_cC = $getAKValuesCompRows['cyl_l'];
	$vis_mr_os_aC = $getAKValuesCompRows['axs_l'];
	$visionOSC = $getAKValuesCompRows['txt_1_l'];
	$glareOSC = $getAKValuesCompRows['txt_2_l'];
	$vis_ak_os_kC = $getAKValuesCompRows['k_os'];
	$vis_ak_os_xC = $getAKValuesCompRows['x_os'];
	$vis_ak_os_slashC = $getAKValuesCompRows['slash_os'];

}

function getVfNfaCompareValues($afterFormID){
	global $vfNfaFormIdC,$pachymetryValODC,$pachymetryCorrecODC,$pachymetryValOSC,$pachymetryCorrecOSC;
	$getPachyReadingStr = "SELECT
						   vf_nfa.pachy_od_readings,
						   vf_nfa.pachy_od_average,
						   vf_nfa.pachy_od_correction_value,
						   vf_nfa.pachy_os_readings,
						   vf_nfa.pachy_os_average,
						   vf_nfa.pachy_os_correction_value,
						   chart_master_table.id
						   FROM chart_master_table
						   INNER JOIN vf_nfa ON vf_nfa.form_id = chart_master_table.id
						   WHERE chart_master_table.patient_id = '$patient_id'
						   AND chart_master_table.record_validity = '1'
						   AND ((vf_nfa.pachy_od_readings != '') OR (vf_nfa.pachy_od_correction_value != '') OR
					   			(vf_nfa.pachy_os_readings != '') OR (vf_nfa.pachy_os_correction_value != '') OR
								(vf_nfa.pachy_od_average != '') OR (vf_nfa.pachy_os_average != '') )
						   AND chart_master_table.id != '".$afterFormID."'
						   ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC
						   LIMIT 0,1
						   ";

	$row = sqlQuery($getPachyReadingStr);
	if($row != false)
	{
		$vfNfaFormIdC = $row["id"];
		$pachymetryValODC = (!empty($row['pachy_od_average'])) ? $row['pachy_od_average'] : $row['pachy_od_readings'];
		$pachymetryCorrecODC = $row['pachy_od_correction_value'];

		$pachymetryValOSC = (!empty($row['pachy_os_average'])) ? $row['pachy_os_average'] : $row['pachy_os_readings'];
		$pachymetryCorrecOSC = $row['pachy_os_correction_value'];

	}
}

//--------------	FUNCTION TO GET K HEADING NAMES ID	---------------//
function getKheadingId($str){
	$newStr = $str;
	//$str = substr($str, 2);
	//$str = strrev($str);
	//$str = substr($str, 1);
	$str = trim($str);
	//$str = strtolower($str);	
	
	if(strpos($str, "K[")!==false){$str = str_replace('K[',"",$str);}
	if(strpos($str, "]")!==false){$str = str_replace(']',"",$str);}
	
	$getKreadingNameStr = "SELECT * FROM kheadingnames WHERE kheadingName = '".imw_real_escape_string($str)."' ORDER BY kheadingId ";
	
	$getKreadingNameQry = imw_query($getKreadingNameStr);
	$getKNameCount = imw_num_rows($getKreadingNameQry);
	if($getKNameCount>0){
		$getKreadingNameRows = imw_fetch_array($getKreadingNameQry);
		$kReadingHeadingID = $getKreadingNameRows['kheadingId'];
	}else{
		if(!empty($str)){
		$insertKreadingNameStr = "INSERT INTO kheadingnames SET kheadingName = '".imw_real_escape_string($str)."' ";
		$insertKreadingNameQry = imw_query($insertKreadingNameStr);
		$kReadingHeadingID = imw_insert_id();
		}
	}
	return $kReadingHeadingID;
}

//--------------	FUNCTION TO GET LENSES FORMULA HEADING NAME	---------------//
function getFormulaHeadName($id){
	$getFormulaheadingsStr = "SELECT * FROM formulaheadings WHERE formula_id = '$id'";
	$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
	$getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry);
	$formula_heading_name = $getFormulaheadingsRow['formula_heading_name'];
	return $formula_heading_name;
}

//--------------	FUNCTION TO GET LENSES FORMULA HEADING	---------------//
function getFormulaHeadingId($str){
	$getFormulaheadingsIdStr = "SELECT * FROM formulaheadings WHERE formula_heading_name = '$str'";
	$getFormulaheadingsIdQry = imw_query($getFormulaheadingsIdStr);
	$countFormulaRows = imw_num_rows($getFormulaheadingsIdQry);
	if($countFormulaRows>0){
		$getFormulaheadingsIdRows = imw_fetch_array($getFormulaheadingsIdQry);
		$formulaHeadingId = $getFormulaheadingsIdRows['formula_id'];
	}else{
		$insertFormulaheadingsStr = "INSERT INTO  formulaheadings SET formula_heading_name = '$str'";
		$insertFormulaheadingsQry = imw_query($insertFormulaheadingsStr);
		$formulaHeadingId = imw_insert_id();
	}
	return $formulaHeadingId;
}

//--------------	FUNCTION TO GET K HEADING NAMES	---------------//
function getKHeadingName($ID){
	$getKreadingIdStr = "SELECT * FROM kheadingnames WHERE kheadingId = '$ID'";
	$getKreadingIdQry = imw_query($getKreadingIdStr);
	$getKreadingIdRow = imw_fetch_array($getKreadingIdQry);		
		if(strpos($getKreadingIdRow['kheadingName'], "K[")===false){$getKreadingIdRow['kheadingName'] = 'K['.$getKreadingIdRow['kheadingName'];}
		if(strpos($getKreadingIdRow['kheadingName'], "]")===false){$getKreadingIdRow['kheadingName'] = $getKreadingIdRow['kheadingName']."]";}		
		$kReadingHeadingName = $getKreadingIdRow['kheadingName'];		
	return $kReadingHeadingName;
}

//================= FUNCTION TO GET LENSE ID
function getLenseId($lenseType){
	$getLenseIdStr = "SELECT * FROM lenses_iol_type WHERE lenses_iol_type = '$lenseType'";
	$getLenseIdQry = imw_query($getLenseIdStr);
	$getLenseIdRow = imw_fetch_array($getLenseIdQry);
	$iol_type_id = $getLenseIdRow['iol_type_id'];
	return $iol_type_id;
}

//================= FUNCTION TO GET LENSE TYPE
function getLenseName($lenseID){
	$getLenseTypeStr = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
	$getLenseTypeQry = imw_query($getLenseTypeStr);
	$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	return $lenses_iol_type;
}

//================= LENSES DEFINED TO PROVIDER OD
function getLenses($provider_id){
	$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
								lenses_iol_type b
								WHERE a.physician_id = '$provider_id'
								AND a.iol_type_id = b.iol_type_id";
	$getLensesForProviderQry = imw_query($getLensesForProviderStr);
	if((imw_num_rows($getLensesForProviderQry)>0)){
		while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
			$iol_type_id = $getLensesForProviderRows['iol_type_id'];
			$providersLenses = $getLensesForProviderRows['lenses_iol_type'];
			$lensesProviderArray[$iol_type_id] = $providersLenses;
		}
	}
	return $lensesProviderArray;
}


function test_isCNValChanged($arr_tmp, $patient_id, $form_id,$str_all_val){
	$flg=0;
	$ar_nw_vl=array();
	$str_db_val="";	
	$res_temp_1 = getCN_MRVals($patient_id, $form_id);
	if($res_temp_1==false){ $res_temp_1= getCN_MRVals($patient_id, 0,1); }//active
	if($res_temp_1==false){ $res_temp_1= getCN_MRVals($patient_id, 0); }//previous	
	if($res_temp_1==false){ $res_temp_1=array(); }
	
	$res_temp_2 = getCN_AKVals($patient_id, $form_id);	
	if($res_temp_2==false){ $res_temp_2=getCN_AKVals($patient_id, 0,1); }//active
	if($res_temp_2==false){ $res_temp_2=getCN_AKVals($patient_id, 0); }//previous	
	if($res_temp_2==false){ $res_temp_2=array(); }	
	
	$arr_temp = array_merge($res_temp_1, $res_temp_2);
	
	//without formId
	$ar_nw_vl_nofid=array();
	$str_db_val_nofid="";	
	$res_temp_1_nofid = getCN_MRVals($patient_id, 0,1);//active
	if($res_temp_1_nofid==false){	$res_temp_1_nofid = getCN_MRVals($patient_id, 0);}//previous	
	if($res_temp_1_nofid==false){ $res_temp_1_nofid=array(); }
	
	$res_temp_2_nofid = getCN_AKVals($patient_id, 0,1);//active
	if($res_temp_2_nofid==false){ $res_temp_2_nofid = getCN_AKVals($patient_id, 0); }//previous	
	if($res_temp_2_nofid==false){ $res_temp_2_nofid=array(); }
	
	$arr_temp_nofid = array_merge($res_temp_1_nofid, $res_temp_2_nofid);	
	
	$len_temp = count($arr_temp);
	$len_temp_nofid = count($arr_temp_nofid);
	
	
	//if(count($arr_temp)>0){
		foreach($arr_tmp as $key => $arval){		
			$val = $arval[0];
			$fld_nm = $arval[1];
			if($len_temp>0 && isset($arr_temp[$key])){
				
				//if($arr_temp[$key] != $val){$flg=1;}
				$str_db_val.=$arr_temp[$key]."~"; 
				if($fld_nm!=""){
					$ar_nw_vl[$fld_nm] = $arr_temp[$key];
					if($key == "vis_ak_od_x"||$key == "vis_ak_os_x"){
						$tm_eye = (strpos($key,"od")!==false) ? "OD" : "OS";
						$ar_nw_vl["k2Auto2".$tm_eye] = $arr_temp[$key];
					}					
				}
			}else{
				$str_db_val.="~"; 
			}

			//nofid
			//echo "<br>".$key;	
			if($len_temp_nofid>0 && isset($arr_temp_nofid[$key])){			
				//echo "IN";
				$str_db_val_nofid.=$arr_temp_nofid[$key]."~"; 
				//echo "<br/>".$str_db_val_nofid;
				if($fld_nm!=""){
					$ar_nw_vl_nofid[$fld_nm] = $arr_temp_nofid[$key];
					if($key == "vis_ak_od_x"||$key == "vis_ak_os_x"){
						$tm_eye = (strpos($key,"od")!==false) ? "OD" : "OS";
						$ar_nw_vl_nofid["k2Auto2".$tm_eye] = $arr_temp_nofid[$key];
					}					
				}
			}else{
				$str_db_val_nofid.="~"; 
			}
		}
	//}
	
	//check
	$str_db_val = trim($str_db_val,"~");	
	$str_all_val = trim($str_all_val,"~");
	$str_db_val_nofid = trim($str_db_val_nofid,"~");
	
	/*
	echo $str_db_val;
	echo "<br/>11<br/>";
	echo $str_all_val;
	echo "<br/>22<br/>";
	echo $str_db_val_nofid;
	exit();
	//*/
	if($str_db_val!=$str_all_val&&$str_db_val_nofid!=$str_all_val){	$flg=1;}	
	
	/*
	print_r($arr_tmp);
	echo "<br/><br/>";
	print_r($arr_temp);
	
	echo $flg;
	exit();
	*/
	
	return array($flg, $ar_nw_vl, $str_db_val);
}

/*Ascan + iol_master*/
function getCN_MRVals($patient_id, $formid=0, $flgActive=""){
	$phrs = "";
	if(!empty($flgActive)){ $phrs .= " AND c4.finalize = '0' ";  }
	if(!empty($formid)){	$phrs .= " AND c0.form_id = '$formid' "; }	
	
							
	$getMRValuesStr = "SELECT 
			c4.id as form_id, 
			c1.mr_none_given, c1.provider_id, c0.id as vis_id, c0.status_elements,
			c1.ex_number,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, 
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l,
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l
			
			FROM chart_master_table c4
			LEFT JOIN chart_vis_master c0 ON c4.id = c0.form_id
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			
			WHERE  c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.delete_by='0'  ".$phrs."
			AND c4.delete_status='0' 
			AND (
				 (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.txt_1!='' OR c2.txt_2!='' OR
					c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.txt_1!='' OR c3.txt_2!='')
			) 	
			Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number DESC
			LIMIT 1;  ";							
	
	$getMRValuesQry = imw_query($getMRValuesStr);
	$getNewChartNoteRow = imw_fetch_array($getMRValuesQry);
	$arRet=false;
	if($getNewChartNoteRow!=false){
		$statusElements = $getNewChartNoteRow["status_elements"];
		$ex_number = $getNewChartNoteRow["ex_number"];
		
		$indx1=$indx2="";
		if($ex_number>1){
			$indx1="Other";
			if($ex_number>2){
				$indx2="_".$ex_number;
			}
		}
		
		if(
			(strpos($statusElements,"elem_providerId".$indx1.$indx2."=1") !== false && !empty($getNewChartNoteRow["provider_id"])) &&			
			(!empty($getNewChartNoteRow["sph_r"]) ||
			 !empty($getNewChartNoteRow["cyl_r"]) ||
			 !empty($getNewChartNoteRow["axs_r"]) ||
			 
			 !empty($getNewChartNoteRow["sph_l"]) ||
			 !empty($getNewChartNoteRow["cyl_l"]) ||
			 !empty($getNewChartNoteRow["axs_l"]) 
			 
			)				
		){
			$arRet["vis_mr_od_s"]=$getNewChartNoteRow["sph_r"];
			$arRet["vis_mr_od_c"]=$getNewChartNoteRow["cyl_r"];
			$arRet["vis_mr_od_a"]=$getNewChartNoteRow["axs_r"];
			$arRet["vis_mr_od_txt_1"]=$getNewChartNoteRow["txt_1_r"];
			$arRet["vis_mr_od_txt_2"]=$getNewChartNoteRow["txt_2_r"];			
			$arRet["vis_mr_os_s"]=$getNewChartNoteRow["sph_l"];
			$arRet["vis_mr_os_c"]=$getNewChartNoteRow["cyl_l"];
			$arRet["vis_mr_os_a"]=$getNewChartNoteRow["axs_l"];
			$arRet["vis_mr_os_txt_1"]=$getNewChartNoteRow["txt_1_l"];
			$arRet["vis_mr_os_txt_2"]=$getNewChartNoteRow["txt_2_l"];
		}		
	}
	
	return $arRet;
}

function getCN_AKVals($patient_id, $formid=0, $flgActive=""){
	$phrs = "";
	if(!empty($flgActive)){ $phrs .= " AND c1.finalize = '0' ";  }
	if(!empty($formid)){	$phrs .= " AND c1.form_id = '$formid' "; }	

	$getAKValuesStr = "SELECT c3.k_od AS vis_ak_od_k, c3.slash_od AS vis_ak_od_slash, c3.x_od AS vis_ak_od_x, c3.k_os AS vis_ak_os_k, 
							c3.slash_os AS vis_ak_os_slash, c3.x_os AS vis_ak_os_x, c3.k_type AS vis_ktype 
							FROM chart_master_table c1
							LEFT JOIN chart_vis_master c2 ON c1.id = c2.form_id
							INNER JOIN chart_ak c3 ON c3.id_chart_vis_master = c2.id
							WHERE c1.patient_id = '$patient_id' ".$phrs."
							AND	(((k_od != '') AND (x_od != '')) OR ((k_os != '') AND (x_os != '')))
							ORDER BY c1.date_of_service DESC, c1.id DESC LIMIT 0, 1";
	$getAKValuesQry = imw_query($getAKValuesStr);
	$getNewChartNoteRow = imw_fetch_array($getAKValuesQry);
	return $getNewChartNoteRow;
}

function test_check20slash($str){
	$str=trim($str);
	$str = (!empty($str) && $str!="20/")?$str:"";
	return $str;
}

/*Ascan + iol_master*/
function test_getCN_StrVals($patient_id){
	//Ak
	$getNewChartNoteRow = getCN_AKVals($patient_id);					
	
	//	OD					
	$vis_ak_od_k = $getNewChartNoteRow['vis_ak_od_k'];
	$vis_ak_od_x = $getNewChartNoteRow['vis_ak_od_x'];
	$vis_ak_od_slash = $getNewChartNoteRow['vis_ak_od_slash'];
	//	OS					
	$vis_ak_os_k = $getNewChartNoteRow['vis_ak_os_k'];
	$vis_ak_os_x = $getNewChartNoteRow['vis_ak_os_x'];
	$vis_ak_os_slash = $getNewChartNoteRow['vis_ak_os_slash'];
	$vis_ktype = $getNewChartNoteRow['vis_ktype'];					
	
	//MR
	$getNewChartNoteRow = getCN_MRVals($patient_id);					
	//	OD
	$vis_mr_od_s = $getNewChartNoteRow['vis_mr_od_s'];
	$vis_mr_od_c = $getNewChartNoteRow['vis_mr_od_c'];
	$vis_mr_od_a = $getNewChartNoteRow['vis_mr_od_a'];
	$mrCOD = $getNewChartNoteRow['mrCOD'];
	$visionOD = $getNewChartNoteRow['vis_mr_od_txt_1'];
	$glareOD = $getNewChartNoteRow['vis_mr_od_txt_2'];
	//	OS
	$vis_mr_os_s = $getNewChartNoteRow['vis_mr_os_s'];
	$vis_mr_os_c = $getNewChartNoteRow['vis_mr_os_c'];
	$vis_mr_os_a = $getNewChartNoteRow['vis_mr_os_a'];
	$visionOS = $getNewChartNoteRow['vis_mr_os_txt_1'];
	$glareOS = $getNewChartNoteRow['vis_mr_os_txt_2'];					
	
	//Save for CN values
	$str_cn_vals=$vis_mr_od_s."~".$vis_mr_od_c."~".$vis_mr_od_a."~".$visionOD."~".$glareOD."~".$vis_ak_od_k."~".$vis_ak_od_x."~".$vis_ak_od_slash."~".
				$vis_mr_os_s."~".$vis_mr_os_c."~".$vis_mr_os_a."~".$visionOS."~".$glareOS."~".$vis_ak_os_k."~".$vis_ak_os_x."~".$vis_ak_os_slash; 
	return $str_cn_vals;
}

function getMenuValue($str){
	$retStr = "";
	$arrCheck = array("Normal","Border Line", "PS", "Increase Abnormal", "Decrease Abnormal", "No Change Abnormal", "Abnormal");
	if(!empty($str)){
		foreach($arrCheck as $key => $val){
			if(strpos($str,$val) !== false){
				$retStr = $val;
				break;
			}
		}
			}
	return $retStr;
}

function getIntialTop($patient_id)
{
	$highTaOd = $highTaOs = $highTpOd = $highTpOs = $highTxOd = $highTxOs = NULL;
	$arrInitialTop = array();
	$sql = "SELECT 
		  glaucoma_past_readings.*, 
		  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
		  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
		  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth
		  FROM glaucoma_past_readings 
		  WHERE patientId ='".$patient_id."' 				
		  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, id DESC
		 ";
	
	$rez = sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++)
	{
		// High Ta Od
		if(($row["taOd"] > $highTaOd) || (($highTaOd == NULL) && !empty($row["taOd"])))
		{
			$date = get_date_format($row["highTaOdDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTaOd"] = array("id" => $row["id"],"od" => $row["taOd"], 
											  "os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTaOd = $row["taOd"];
		}
		// High Ta Os
		if(($row["taOs"] > $highTaOs) || (($highTaOs == NULL) && !empty($row["taOs"])))
		{
			$date = get_date_format($row["highTaOsDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTaOs"] = array("id" => $row["id"],"od" => $row["taOd"], 
												"os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTaOs = $row["taOs"];
		}
		// High Tp Od 
		if(($row["tpOd"] > $highTpOd) || (($highTpOd == NULL) && !empty($row["tpOd"])))
		{
			$date = get_date_format($row["highTpOdDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTpOd"] = array("id" => $row["id"],"od" => $row["tpOd"], 
												"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTpOd = $row["tpOd"];
		}
		// High Tp Os
		if(($row["tpOs"] > $highTpOs) || (($highTpOs == NULL) && !empty($row["tpOs"])))
		{
			$date = get_date_format($row["highTpOsDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTpOs"] = array("id" => $row["id"],"od" => $row["tpOd"], 
												"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTpOs = $row["tpOs"];
		}
		// High Tx Od 
		if(($row["txOd"] > $highTxOd) || (($highTxOd == NULL) && !empty($row["txOd"])))
		{
			$date = get_date_format($row["highTxOdDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTxOd"] = array("id" => $row["id"],"od" => $row["txOd"], 
												"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTxOd = $row["txOd"];
		}
		// High Tx Os
		if(($row["txOs"] > $highTxOs) || (($highTxOs == NULL) && !empty($row["txOs"])))
		{
			$date = get_date_format($row["highTxOsDate"],'mm-dd-yyyy');
			$arrInitialTop["HighTxOs"] = array("id" => $row["id"],"od" => $row["txOd"], 
												"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
			$highTxOs = $row["txOs"];
		}
		
		
		// VF
		if(!empty($row["vfOdSummary"]) || !empty($row["vfOsSummary"]))
		{
			$date = get_date_format($row["vfDate"],'mm-dd-yyyy');
			$arrInitialTop["VF"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
											"od" => getMenuValue($row["vfOdSummary"]), "os" => getMenuValue($row["vfOsSummary"]));
		}			
		// NFA
		if(!empty($row["nfaOdSummary"]) || !empty($row["nfaOsSummary"]))
		{
			$date = get_date_format($row["nfaDate"],'mm-dd-yyyy');	
			$arrInitialTop["NFA"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											"od" => getMenuValue($row["nfaOdSummary"]), "os" => getMenuValue($row["nfaOsSummary"]));
		}

		// Gonio
		if((!empty($row["gonioOdSummary"]) && ($row["gonioOdSummary"] != "Empty")) || 
			(!empty($row["gonioOsSummary"]) && ($row["gonioOsSummary"] != "Empty")))
		{				
			$date = get_date_format($row["gonioDate"],'mm-dd-yyyy');	
			$arrInitialTop["Gonio"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											  "od" => $row["gonioOdSummary"], "os" => $row["gonioOsSummary"]);
		}		
		//Pachy			
		$tmpOd = trim($row["pachyOdCorr"]);
		$tmpOs = trim($row["pachyOsCorr"]);			
		if(!empty($tmpOd) || !empty($tmpOs) || !empty($row["pachyOdReads"]) || !empty($row["pachyOsReads"]))
		{
			$date = get_date_format($row["pachyDate"],'mm-dd-yyyy');	
			$arrInitialTop["Pachy"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											  "od" => array("Read" => $row["pachyOdReads"], "Avg" => $row["pachyOdAvg"], "Corr" => $row["pachyOdCorr"]), "os" => array("Read" => $row["pachyOsReads"], "Avg" => $row["pachyOsAvg"], "Corr" => $row["pachyOsCorr"]));				
		}
		//Disk Photo
		if(($row["diskPhotoOd"] == "Done") || ($row["diskPhotoOs"] == "Done"))
		{
			$date = get_date_format($row["diskPhotoDate"],'mm-dd-yyyy');	
			$arrInitialTop["Disk"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											  "od" =>$row["diskPhotoOd"] , "os" =>$row["diskPhotoOs"] );				
		}
		//CD
		if(!empty($row["cdOd"]) || !empty($row["cdOs"]))
		{
			$date = get_date_format($row["cdDate"],'mm-dd-yyyy');				
			$arrInitialTop["CD"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											  "od" =>$row["cdOd"] , "os" =>$row["cdOs"] );				
		}
		//CEE
		if(!empty($row["cee"]))
		{
			$date = get_date_format($row["ceeDate"],'mm-dd-yyyy');            
			$arrInitialTop["CEE"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
											  "cee" => $row["cee"],"notes"=>$row["ceeNotes"]); 				
		}           						
	}
	return $arrInitialTop;		
}

function valuesNewRecordsCorrectionValues($patient_id,$sel=" * ",$LF="0")
{
	$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
	$qry = "SELECT ".$sel." FROM chart_master_table ".
		  "INNER JOIN chart_correction_values ON chart_master_table.id = chart_correction_values.form_id ".
		  "WHERE chart_master_table.patient_id = '".$patient_id."' ".
		  "AND chart_master_table.record_validity = '1' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
		  $LF.
		  "ORDER BY chart_master_table.create_dt DESC, chart_master_table.id DESC ".
		  "LIMIT 0,1 ";
	$row = sqlQuery($qry);
	return $row;
}


function setDefPachyVals($formId=0){
	
	$elem_pachy_od_readings=$elem_pachy_od_average=$elem_pachy_od_correction_value="";
	$elem_pachy_os_readings=$elem_pachy_os_average=$elem_pachy_os_correction_value="";

	if(!empty($formId)){
		//
		$sql="SELECT reading_od,avg_od,cor_val_od,reading_os,avg_os,cor_val_os,cor_date
			  FROM chart_correction_values WHERE patient_id='".$_SESSION['patient']."' AND form_id='".$formId."'  ";
		$row=sqlQuery($sql);
		if($row!=false){
			$elem_pachy_od_readings=$row["reading_od"];
			$elem_pachy_od_average=$row["avg_od"];
			$elem_pachy_od_correction_value=trim($row["cor_val_od"]);
			$elem_pachy_os_readings=$row["reading_os"];
			$elem_pachy_os_average=$row["avg_os"];
			$elem_pachy_os_correction_value=trim($row["cor_val_os"]);
			if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["cor_date"]); 
		}		

		//Pachy
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){			
			$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
					pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
					FROM pachy WHERE formId='".$formId."' AND patientId='".$_SESSION['patient']."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["pachy_od_readings"];
				$elem_pachy_od_average=$row["pachy_od_average"];
				$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);

				$elem_pachy_os_readings=$row["pachy_os_readings"];
				$elem_pachy_os_average=$row["pachy_os_average"];
				$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["examDate"]); 
			}
			//
		}

		//A/scan
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
					pachymetryValOS,pachymetryCorrecOS,examDate
					FROM surgical_tbl WHERE form_id='".$formId."' AND patient_id='".$_SESSION['patient']."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["pachymetryValOD"];
				//$elem_pachy_od_average=$row["pachy_od_average"];
				$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);

				$elem_pachy_os_readings=$row["pachymetryValOS"];
				//$elem_pachy_os_average=$row["pachy_os_average"];
				$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["examDate"]); 
			}
		}
	}

	//if Empty , Get Values from glucoma main
	if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
		
		$elem_activate = "1";

		$sql = "SELECT
				glucomaId,activate,
				datePachy,
				pachyOdReads,pachyOdAvg,pachyOdCorr,
				pachyOsReads,pachyOsAvg,pachyOsCorr,activate
			  FROM glucoma_main 
			  WHERE patientId = '".$_SESSION["patient"]."' 
			  AND activate = '1' ";
		
		$row=sqlQuery($sql);
		if($row != false && !empty($row["datePachy"]) && $row["datePachy"] != "0000-00-00" && ($row["pachyOdReads"]!=""||$row["pachyOsReads"]!="")){
				$elem_pachy_od_readings = $row["pachyOdReads"];
				$elem_pachy_od_average = $row["pachyOdAvg"];
				$elem_pachy_od_correction_value = $row["pachyOdCorr"];
				$elem_pachy_os_readings = $row["pachyOsReads"];
				$elem_pachy_os_average = $row["pachyOsAvg"];
				$elem_pachy_os_correction_value = $row["pachyOsCorr"];
				$elem_cor_date =  getDateFormatDB($row["datePachy"],'mm-dd-yyyy');
				$elem_activate = $row["activate"];
				$elem_glucomaId = $row["glucomaId"];
		}	
		
		if($elem_activate!=-1){
			$arrInitialTop = getIntialTop($_SESSION["patient"]);
			$lenInitialTop = count($arrInitialTop);
			
			if((empty($elem_pachy_od_readings) && empty($elem_pachy_od_correction_value) &&
				empty($elem_pachy_os_readings) && empty($elem_pachy_os_correction_value)
				) || empty($elem_glucomaId) || empty($arrInitialTop["Pachy"][0]["new"]))
            {			
			
				if(isset($arrInitialTop["Pachy"][0])){
					$elem_cor_date = $arrInitialTop["Pachy"][0]["date"];
					$elem_pachy_od_readings = $arrInitialTop["Pachy"][0]["od"]["Read"];
					$elem_pachy_od_average = $arrInitialTop["Pachy"][0]["od"]["Avg"];
					$elem_pachy_od_correction_value = $arrInitialTop["Pachy"][0]["od"]["Corr"];
					$elem_pachy_os_readings = $arrInitialTop["Pachy"][0]["os"]["Read"];
					$elem_pachy_os_average = $arrInitialTop["Pachy"][0]["os"]["Avg"];
					$elem_pachy_os_correction_value = $arrInitialTop["Pachy"][0]["os"]["Corr"];
				}
			}
		}
	}

	//if Empty , Get Values from previous visit
	if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
		
		$row=valuesNewRecordsCorrectionValues($_SESSION["patient"]);
		if($row != false){
			$elem_pachy_od_readings = $row["reading_od"];
			$elem_pachy_od_average = $row["avg_od"];
			$elem_pachy_od_correction_value = $row["cor_val_od"];
			$elem_pachy_os_readings = $row["reading_os"];
			$elem_pachy_os_average = $row["avg_os"];
			$elem_pachy_os_correction_value = $row["cor_val_os"];
			if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["cor_date"]);
		}

	}
	
	//Pachy
	if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
		
		$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
				pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
				FROM pachy 
				WHERE patientId='".$_SESSION['patient']."' 
				AND (pachy_od_correction_value!='' OR pachy_os_correction_value!='')
				ORDER BY examDate DESC, pachy_id DESC
				LIMIT 0,1
				";
		$row=sqlQuery($sql);
		if($row!=false){
			$elem_pachy_od_readings=$row["pachy_od_readings"];
			$elem_pachy_od_average=$row["pachy_od_average"];
			$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);
			$elem_pachy_os_readings=$row["pachy_os_readings"];
			$elem_pachy_os_average=$row["pachy_os_average"];
			$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
			if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["examDate"]);
		}
	}
	
	//A/Scan
	if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
		$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
				pachymetryValOS,pachymetryCorrecOS,examDate
				FROM surgical_tbl WHERE patient_id='".$_SESSION['patient']."' 
				AND (pachymetryCorrecOD!='' OR pachymetryCorrecOS!='')
				ORDER BY examDate DESC, surgical_id DESC
				LIMIT 0,1
				";
		$row=sqlQuery($sql);
		if($row!=false){
			$elem_pachy_od_readings=$row["pachymetryValOD"];
			$elem_pachy_od_average="";
			$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);
			$elem_pachy_os_readings=$row["pachymetryValOS"];
			$elem_pachy_os_average="";
			$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
			if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($row["examDate"]);
		}
	}
	
	//pt past diag
	if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
		$sql = "SELECT pachy FROM chart_ptPastDiagnosis WHERE patient_id='".$_SESSION['patient']."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$pachy = unserialize($row["pachy"]);
			
			$elem_pachy_od_readings=$pachy["od_readings"];
			$elem_pachy_od_average=$pachy["od_average"];
			$elem_pachy_od_correction_value=trim($pachy["od_correction_value"]);
			$elem_pachy_os_readings=$pachy["os_readings"];
			$elem_pachy_os_average=$pachy["os_average"];
			$elem_pachy_os_correction_value=trim($pachy["os_correction_value"]);
			if(!empty($pachy["cor_date"]) && ($pachy["cor_date"] != "0000-00-00")) $elem_cor_date = getDateFormatDB($pachy["cor_date"]);
			
		}
	}

	return array($elem_pachy_od_readings,$elem_pachy_od_average,$elem_pachy_od_correction_value,
				 $elem_pachy_os_readings,$elem_pachy_os_average,$elem_pachy_os_correction_value,$elem_cor_date);
}

?>