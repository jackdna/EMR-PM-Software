<?php
/*
The MIT License (MIT)
Distribute, Modify and Contribute under MIT License
Use this software under MIT License

File: main_functions.php
Coded in PHP7
Purpose: This file provides various functions used in work view.
Access Type : Include file
*/

// order list
function orlist_getOrdersByName($nm, $id, $ordertype){
	$str="";
	$sql = "select name, id,snowmed, dosage, qty, sig from order_details where delete_status = '0' AND o_type ='".$ordertype."' AND name='".$nm."'  order by name"; //AND id!='".$id."'
	$rez = imw_query($sql);
	$i=0;
	while( $row = imw_fetch_assoc($rez) )
	{
		$sno_med=$dosage=$qty=$sig="";
		if($row["snowmed"]){$sno_med="<span style=\"font-size:9px;\"> (SNOMED CT: ".$row["snowmed"].")</span>";}
		if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
		if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
		if(!empty($row["sig"])){  $sig = " ".$row["sig"]."";  }
		$nm2=str_replace(" ","_", $nm);
		$str.= "<input type=\"checkbox\" id=\"elem_inorder_relorder".$nm2.$i."\" value=\"".$row["id"]."\">";
		$str.="<label for=\"elem_inorder_relorder".$nm2.$i."\">".$row["name"]." ".$sno_med."".$dosage."".$qty."".$sig."</label><br/>";
		$i++;
	}
	return $str;
}


if( !function_exists(scnDocExistFun)) {
	function scnDocReadChkFun($patient_id,$section_name,$session_provider_id) {
		$tblNme = 'scan_doc_tbl';
		global $dbase;
		if($section_name=='scan') {
			$scnImgSrc = $GLOBALS['webroot'].'/library/images/scanner.png';

		}else if($section_name=='tests') {
			$scnImgSrc = $GLOBALS['webroot'].'/images/icons_eye_green.png';
			$tblNme = 'scans';
		}
		if($patient_id) {
			//$qryScnId="SELECT scan_doc_id from ".constant("IMEDIC_SCAN_DB").".".$tblNme." WHERE patient_id='".$patient_id."'";
			$qryScnId="SELECT DISTINCT(scan_doc_id) FROM provider_view_log_tbl WHERE patient_id='".$patient_id."' AND section_name='".$section_name."'";
			if($section_name=='scan') {
				$qryScnId = "SELECT DISTINCT(pvlt.scan_doc_id) FROM ".$dbase.".provider_view_log_tbl pvlt, ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt, ".constant("IMEDIC_SCAN_DB").".folder_categories fc
									WHERE pvlt.scan_doc_id=sdt.scan_doc_id
									AND pvlt.patient_id='".$patient_id."'
									AND pvlt.section_name='".$section_name."'
									AND fc.folder_categories_id=sdt.folder_categories_id
									AND fc.alertPhysician='1'";
			}
			$resScnId=imw_query($qryScnId);
			$scnIdNumRow = imw_num_rows($resScnId);
			$andScanDocIdQry = '';
			if($scnIdNumRow >0) {
				$qryChkAnyDocRead="SELECT DISTINCT(scan_doc_id) FROM provider_view_log_tbl WHERE patient_id='".$patient_id."' AND section_name='".$section_name."' AND provider_id='".$session_provider_id."'";
				if($section_name=='scan') {
					$qryChkAnyDocRead = "SELECT DISTINCT(pvlt.scan_doc_id) FROM ".$dbase.".provider_view_log_tbl pvlt, ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt, ".constant("IMEDIC_SCAN_DB").".folder_categories fc
										WHERE pvlt.scan_doc_id=sdt.scan_doc_id
										AND pvlt.patient_id='".$patient_id."'
										AND pvlt.section_name='".$section_name."'
										AND pvlt.provider_id='".$session_provider_id."'
										AND fc.folder_categories_id=sdt.folder_categories_id
										AND fc.alertPhysician='1'";
				}
				$resChkAnyDocRead=imw_query($qryChkAnyDocRead);

				$ChkAnyDocReadNumRow = imw_num_rows($resChkAnyDocRead);
				if($ChkAnyDocReadNumRow >0 && $ChkAnyDocReadNumRow!=$scnIdNumRow) {
					if($section_name=='scan') {
						$scnImgSrc = $GLOBALS['webroot'].'/interface/chart_notes/images/scanDcs_active.png';
					}else if($section_name=='tests') {
						$scnImgSrc = $GLOBALS['webroot'].'/images/icons_eye_orange.png';
					}
				}else if($ChkAnyDocReadNumRow ==0) {
					if($section_name=='scan') {
						$scnImgSrc = $GLOBALS['webroot'].'/interface/chart_notes/images/scanDcs_active.png';
					}else if($section_name=='tests') {
						$scnImgSrc = $GLOBALS['webroot'].'/images/icons_eye_orange.png';
					}
				}
			}
		}
		return $scnImgSrc;
}
}


if( !function_exists(scnDocExistFun)) {
	function scnDocExistFun($dBaseName,$patient_id,$catId='') {//$dBaseName = constant("IMEDIC_SCAN_DB")
		$ChkAnyDocExistsNumRow=0;
		$andCatIdQry='';
		if($catId) { $andCatIdQry = " AND sdt.folder_categories_id='".$catId."' "; }
		if($patient_id) {
			$qryChkAnyDocExists="SELECT sdt.scan_doc_id FROM ".$dBaseName.".scan_doc_tbl sdt, ".$dBaseName.".folder_categories fc
								 WHERE sdt.folder_categories_id = fc.folder_categories_id
								 AND  sdt.patient_id = '".$patient_id."'
								 AND  fc.alertPhysician = '1'".$andCatIdQry;

			//$qryChkAnyDocExists="SELECT * from ".$dBaseName.".scan_doc_tbl WHERE patient_id='".$patient_id."'".$andCatIdQry;
			$resChkAnyDocExists=imw_query($qryChkAnyDocExists);
			$ChkAnyDocExistsNumRow = imw_num_rows($resChkAnyDocExists);
		}
		return $ChkAnyDocExistsNumRow;

	}
}
