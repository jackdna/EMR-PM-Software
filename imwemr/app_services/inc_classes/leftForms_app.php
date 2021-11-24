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
File: leftForms_pdf_print.php
Purpose: This file provides tests data for Android and IOS.
Access Type : Include file
*/
?>
<?php
include("/../../interface/printing/patient_info.inc.php");  // added to use getTestImages($testtid,$sectionImageFrom,$patient_id) function

function pdfVersion2($filename)
{ 
	$fp = @fopen($filename, 'rb');
 
	if (!$fp) {

		return 0;
	}
 
	
	fseek($fp, 0);
 
	
	preg_match('/\d\.\d/',fread($fp,20),$match);
 
	fclose($fp);
 
	if (isset($match[0])) {
		return $match[0];
	} else {
		return 0;
	}
}
function checknMakeImagesHTML($arrPDFs){
	$str_alternative_images='';
	foreach($arrPDFs as $pdf_file){
		if(file_exists($pdf_file)){
			
			$version= pdfVersion2($pdf_file);
			if($version>1.4){
				$jpg_name= substr($pdf_file, 0, -4).'.jpg';
	
				if(file_exists($jpg_name)){
					$scnfileSize = getimagesize($jpg_name);
					if($scnfileSize[0]>700 ){
						$scnimageWidth2 = ManageData::imageResize($scnfileSize[0],$scnfileSize[1],700);
						$scnMultiImage= '<tr><td style="width:700px;text-align:center" ><img style="cursor:pointer" src="'.$jpg_name.'" alt="patient Image" '.$scnimageWidth2.'></td></tr>';
					}
					else{
						$scnMultiImage= '<tr><td style="width:700px;text-align:center"><img style="cursor:pointer" src="'.$jpg_name.'" alt="patient Image"></td></tr>';
					}
					
					$arr_alternative_images[]= $scnMultiImage;
				}
			}
		}
	}

	if(sizeof($arr_alternative_images)>0){
		$str_alternative_images=implode(',', $arr_alternative_images);
	}
	return $str_alternative_images;
}

//---- new funtion by Aqib for android And IOS ----//
//---- test_summary_app in tests.php is calling this function ----//
function print_cellcount_app($req){	

	if(!empty($req)){
		$sql = "SELECT * FROM test_cellcnt WHERE test_cellcnt_id =$req";	
	}
	
	$row = imw_query($sql);
	
	if(imw_num_rows($row)>0){
		
		$result=imw_fetch_assoc($row);
		
		$descod = stripslashes($result["descOd"]);
		$result["descOd"] = $descod;
		
		$descOs = stripslashes($result["descOs"]);
		$result["descOs"] = $descOs;
		
		$techComments = stripslashes($result["techComments"]);
		$result["techComments"] = $techComments;
		
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['test_cellcnt_id'],$sectionImageFrom="CellCount",$result['patientId']);

		//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
		$arr_alternative_images=array();
		$str_alternative_images='';
		if($result['test_cellcnt_id']!=""){
			$arrPDFs = getAllTestPdf($result['test_cellcnt_id']);
			$str_alternative_images= checknMakeImagesHTML($arrPDFs);
			$imagesHtml.=$str_alternative_images;
		}	
					//------------------------------------
										
		if($imagesHtml!=""){
			$result['imagesHtml']=$imagesHtml;
		} 
					
		
		return $result;
		
	}
}
// end of print_cellcount() function//



//---- new funtion by Aqib for android And IOS ----//
//---- test_summary_app in tests.php is calling this function ----//
function print_vf_app($req){
	if(!empty($req)){
		$sqlVFFormQry = imw_query("SELECT * FROM vf WHERE vf_id =$req");
	}
	
	if(imw_num_rows($sqlVFFormQry)>0){
		$result = imw_fetch_assoc($sqlVFFormQry);
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		
		$imagesHtml=getTestImages($result['vf_id'],$sectionImageFrom="VF",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['vf_id']!=""){
						$arrPDFs = getAllTestPdf($result['vf_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
					
		
		
		return $result;
	}
}

// end  of print_vf_app //


//---- new funtion by Aqib for android And IOS ----//
//---- test_summary_app in tests.php is calling this function ----//
function print_vf_gl_fun_app($req){
	if(!empty($req)){
		$sqlVFFormQry = imw_query("SELECT * FROM vf_gl WHERE vf_gl_id =$req");
	}
	if(imw_num_rows($sqlVFFormQry)>0){
		$result = imw_fetch_assoc($sqlVFFormQry);
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['vf_gl_id'],$sectionImageFrom="VF-GL",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['vf_gl_id']!=""){
						$arrPDFs = getAllTestPdf($result['vf_gl_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
					
		
		return $result;
	}
	
}



// end of print_vf_gl_fun_app() function//



//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//
function print_ivfa_app($req){

	if(!empty($req)){
		$sqlIVFA = "SELECT * FROM ivfa WHERE vf_id=$req";
	}
	$rowIVFA = imw_query($sqlIVFA);
	if(imw_num_rows($rowIVFA)>0){
		$result=imw_fetch_assoc($rowIVFA);
		
		if($result['ivfa_od'] == "1") $result['ivfa_od'] = 'OU';
		else if($result['ivfa_od'] == "2") $result['ivfa_od'] = 'OD';
		else if($result['ivfa_od'] == "3") $result['ivfa_od'] = 'OS';
		
		
		
		$performedBy=$result['performed_by'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performed_by'] =$performedBy;
		
		$physicianName=$result['phy'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phy'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['vf_id'],$sectionImageFrom="IVFA",$result['patient_id']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['vf_id']!=""){
						$arrPDFs = getAllTestPdf($result['vf_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
		
		return $result;
	}

}
// end of function print_ivfa_app() //

//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//
function print_icg_app($req)
{
	if(!empty($req))
	{
		$sqlICG = "SELECT * FROM icg WHERE icg_id = $req";
	}
	$rowICG = imw_query($sqlICG);
	if(imw_num_rows($rowICG)>0){
		$result=imw_fetch_assoc($rowICG);
		
		if($result['icg_od'] == "1") $result['icg_od'] = 'OU';
		else if($result['icg_od'] == "2") $result['icg_od'] = 'OD';
		else if($result['icg_od'] == "3") $result['icg_od'] = 'OS';		
		
		
		$performedBy=$result['performed_by'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performed_by'] =$performedBy;
		
		$physicianName=$result['phy'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phy'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['icg_id'],$sectionImageFrom="ICG",$result['patient_id']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['icg_id']!=""){
						$arrPDFs = getAllTestPdf($result['icg_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
		
		return $result;
	}
}
// end of function print_icg_app() //


//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//
function print_disc_app($req){
	if(!empty($req)){
		$sql = "SELECT * FROM disc WHERE disc_id =$req";					
	}
	$rowDISC = imw_query($sql);
	if(imw_num_rows($rowDISC)>0){
		$result=imw_fetch_assoc($rowDISC);
		
		if($result['fundusDiscPhoto'] == 1){
			 $result['fundusDiscPhoto']='Disc Photo'; 
		}
		if($result['fundusDiscPhoto'] == 2){
			 $result['fundusDiscPhoto']='Macula Photo'; 
		}
		if($result['fundusDiscPhoto'] == 3){
			$result['fundusDiscPhoto']='Retina Photo'; 
		}
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['disc_id'],$sectionImageFrom="Disc",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['disc_id']!=""){
						$arrPDFs = getAllTestPdf($result['disc_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
		
		return $result;
		
	}
}
// end of function print_disc_app()//


//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//
function print_external_app($req){

	if(!empty($req)){
		$sql = "SELECT * FROM disc_external WHERE disc_id =$req";		
	}
	$rowexternal = imw_query($sql);
	if(imw_num_rows($rowexternal)>0){
		$result=imw_fetch_assoc($rowexternal);
		
		if($result['fundusDiscPhoto'] == 1){
			$result['fundusDiscPhoto']='ES (External)'; 
		}
		if($result['fundusDiscPhoto'] == 2){
			$result['fundusDiscPhoto']='ASP (Anterior Segment Photos)'; 
		}		
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['disc_id'],$sectionImageFrom="discExternal",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['disc_id']!=""){
						$arrPDFs = getAllTestPdf($result['disc_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
		
		return $result;
	}
	
}

// end of function print_external_app() //


function print_topo_app($req){

	if(!empty($req)){
		$sql = "SELECT * FROM topography WHERE topo_id =$req";		
	}
	$rowtopography = imw_query($sql);
	if(imw_num_rows($rowtopography)>0){
		$result=imw_fetch_assoc($rowtopography);
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['topo_id'],$sectionImageFrom="Topogrphy",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['topo_id']!=""){
						$arrPDFs = getAllTestPdf($result['topo_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
					
		
		return $result;
	}
}
// end of function print_topo_app() function //


//---- new funtion by Aqib for android And IOS ----//
//---- test_summary_app in tests.php is calling this function ----//
function print_bscan_app($req){

	if(!empty($req)){
		
		$sql = "SELECT * FROM test_bscan WHERE test_bscan_id =$req";		
	}
	$row = imw_query($sql);
	if(imw_num_rows($row)>0){
	
		$result=imw_fetch_assoc($row);
		
		$performedBy=$result['performedBy'];
			$performedByFname = print_getData('fname', 'users', 'id', $performedBy);			
			$performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		$performedBy= $performedByFname." ".$performedByLname;
		$result['performedBy'] =$performedBy;
		
		$physicianName=$result['phyName'];
			$physicianFname = print_getData('fname', 'users', 'id', $physicianName);			
			$physicianLname = print_getData('lname', 'users', 'id', $physicianName);
		$physicianName= $physicianFname." ".$physicianLname;
		$result['phyName'] =$physicianName;
	
		$ordrbyName=$result['ordrby'];
			$ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);			
			$ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
		$ordrbyName= $ordrByFname." ".$ordrByLname;
		$result['ordrby'] =$ordrbyName;
		
		$imagesHtml=getTestImages($result['test_bscan_id'],$sectionImageFrom="Bscan",$result['patientId']);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($result['test_bscan_id']!=""){
						$arrPDFs = getAllTestPdf($result['test_bscan_id']);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						$result['imagesHtml']=$imagesHtml;
					} 
					
		
		
		return $result;
		
	}
}




//================= FUNCTION TO GET K HEADING NAMES
function print_getKHeadingName($ID){		
	$getKreadingIdStr = "SELECT * FROM kheadingnames WHERE kheadingId = '$ID'";
	$getKreadingIdQry = imw_query($getKreadingIdStr);
	$getKreadingIdRow = imw_fetch_array($getKreadingIdQry);
		$kReadingHeadingName = 'K['.$getKreadingIdRow['kheadingName'].']';
	return $kReadingHeadingName;
}
//================= FUNCTION TO GET K HEADING NAMES

//================= FUNCTION TO GET LENSE TYPE
function print_getLenseName($lenseID){
	$getLenseTypeStr = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
	$getLenseTypeQry = imw_query($getLenseTypeStr);
	$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	return $lenses_iol_type;
}
//================= FUNCTION TO GET LENSE TYPE

//================= FUNCTION TO GET LENSES FORMULA HEADING NAME
function print_getFormulaHeadName($id){
	$getFormulaheadingsStr = "SELECT * FROM formulaheadings WHERE formula_id = '$id'";
	$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
	$getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry);
	$formula_heading_name = $getFormulaheadingsRow['formula_heading_name'];
	return $formula_heading_name;
}
//================= FUNCTION TO GET LENSES FORMULA HEADING NAME

function print_getData($fieldReq, $tableName, $idField, $val){
	$getDetailsStr = "SELECT $fieldReq FROM $tableName WHERE $idField = '$val'";
	$getDetailsQry = imw_query($getDetailsStr);
	$getDetailsRow = imw_fetch_array($getDetailsQry);
	return $getDetailsRow[$fieldReq];
}

//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//

function print_ascan_app($req){

	if(!empty($req)){
		$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE surgical_id =$req";	
	}
	
	$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
	$rowsCount = imw_num_rows($getSurgicalRecordQry);
	if($rowsCount>0){
		$getSurgicalRecordRows = imw_fetch_assoc($getSurgicalRecordQry);
		
		
			$performedByOD = $getSurgicalRecordRows['performedByOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $performedByOD);			
				$performedByODLname = print_getData('lname', 'users', 'id', $performedByOD);
			$performedByOD = $performedByODFname." ".$performedByODLname;
			$getSurgicalRecordRows['performedByOD']=$performedByOD;
			
			
			
			$autoSelectOD = $getSurgicalRecordRows['autoSelectOD'];
				$autoSelectOD = print_getKHeadingName($autoSelectOD);
			$getSurgicalRecordRows['autoSelectOD']=$autoSelectOD;
			
			$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
				$iolMasterSelectOD = print_getKHeadingName($iolMasterSelectOD);
			$getSurgicalRecordRows['iolMasterSelectOD']=$iolMasterSelectOD;
			
			$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
				$topographerSelectOD = print_getKHeadingName($topographerSelectOD);
			$getSurgicalRecordRows['topographerSelectOD']=$topographerSelectOD;
			
			$provider_idOD = $getSurgicalRecordRows['performedByPhyOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $provider_idOD);
				$performedByODLname = print_getData('lname', 'users', 'id', $provider_idOD);
			$provider_idOD = $performedByODFname." ".$performedByODLname;
			$getSurgicalRecordRows['performedByPhyOD']=$provider_idOD;
			
			$powerIolOD = $getSurgicalRecordRows['powerIolOD'];
				$powerIolOD = print_getFormulaHeadName($powerIolOD);
			$getSurgicalRecordRows['powerIolOD']=$powerIolOD;
			
			$holladayOD = $getSurgicalRecordRows['holladayOD'];
				$holladayOD = print_getFormulaHeadName($holladayOD);
			$getSurgicalRecordRows['holladayOD']=$holladayOD;
			
			$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
				$srk_tOD = print_getFormulaHeadName($srk_tOD);
			$getSurgicalRecordRows['srk_tOD']=$srk_tOD;
			
			$hofferOD = $getSurgicalRecordRows['hofferOD'];
				$hofferOD = print_getFormulaHeadName($hofferOD);
			$getSurgicalRecordRows['hofferOD']=$hofferOD;
			
			$iol1OD = $getSurgicalRecordRows['iol1OD'];
				$iol1OD = print_getLenseName($iol1OD);
			$getSurgicalRecordRows['iol1OD']=$iol1OD;
			
			$iol2OD = $getSurgicalRecordRows['iol2OD'];
				$iol2OD = print_getLenseName($iol2OD);
			$getSurgicalRecordRows['iol2OD']=$iol2OD;
			
			$iol3OD = $getSurgicalRecordRows['iol3OD'];
				$iol3OD = print_getLenseName($iol3OD);
			$getSurgicalRecordRows['iol3OD']=$iol3OD;
			
			$iol4OD = $getSurgicalRecordRows['iol4OD'];
				$iol4OD = print_getLenseName($iol4OD);
			$getSurgicalRecordRows['iol4OD']=$iol4OD;
			
			$selecedIOLsOD = $getSurgicalRecordRows['selecedIOLsOD'];
				$selecedIOLsOD = print_getLenseName($selecedIOLsOD);
			$getSurgicalRecordRows['selecedIOLsOD']=$selecedIOLsOD;
			
			$lengthTypeOD = $getSurgicalRecordRows['lengthTypeOD'];
			if($lengthTypeOD == 'percent') $lengthTypeOD = '%';
				$getSurgicalRecordRows['lengthTypeOD']=$lengthTypeOD;
			
			$provider_idOS = $getSurgicalRecordRows['performedByOS'];
				$performedByOSFname = print_getData('fname', 'users', 'id', $provider_idOS);
				$performedByOSLname = print_getData('lname', 'users', 'id', $provider_idOS);
				$performedByOS = $performedByOSFname." ".$performedByOSLname;
			$getSurgicalRecordRows['performedByOS']=$provider_idOS;
			
			
			
			$autoSelectOS = $getSurgicalRecordRows['autoSelectOS'];
				$autoSelectOS = print_getKHeadingName($autoSelectOS);
			$getSurgicalRecordRows['autoSelectOS']=$autoSelectOS;
			
			$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
				$iolMasterSelectOS = print_getKHeadingName($iolMasterSelectOS);
			$getSurgicalRecordRows['iolMasterSelectOS']=$iolMasterSelectOS;
			
			$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
				$topographerSelectOS = print_getKHeadingName($topographerSelectOS);
			$getSurgicalRecordRows['topographerSelectOS']=$topographerSelectOS;
			
			$performedIolOS = $getSurgicalRecordRows['performedIolOS'];		
				$performedByPhyOSFname = print_getData('fname', 'users', 'id', $performedIolOS);
				$performedByPhyOSLname = print_getData('lname', 'users', 'id', $performedIolOS);
				$performedIolOS = $performedByPhyOSFname." ".$performedByPhyOSLname;
			$getSurgicalRecordRows['performedIolOS']=$performedIolOS;
			
			$powerIolOS = $getSurgicalRecordRows['powerIolOS'];
				$powerIolOS = print_getFormulaHeadName($powerIolOS);
			$getSurgicalRecordRows['powerIolOS']=$powerIolOS;
			
			$holladayOS = $getSurgicalRecordRows['holladayOS'];
				$holladayOS = print_getFormulaHeadName($holladayOS);
			$getSurgicalRecordRows['holladayOS']=$holladayOS;
				
			$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
				$srk_tOS = print_getFormulaHeadName($srk_tOS);
			$getSurgicalRecordRows['srk_tOS']=$srk_tOS;
			
			$hofferOS = $getSurgicalRecordRows['hofferOS'];
				$hofferOS = print_getFormulaHeadName($hofferOS);
			$getSurgicalRecordRows['hofferOS']=$hofferOS;
				
			$iol1OS = $getSurgicalRecordRows['iol1OS'];
				$iol1OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol1OS);	
			$getSurgicalRecordRows['iol1OS']=$iol1OS;
			
			$iol2OS = $getSurgicalRecordRows['iol2OS'];
				$iol2OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol2OS);
			$getSurgicalRecordRows['iol2OS']=$iol2OS;
			
			$iol3OS = $getSurgicalRecordRows['iol3OS'];
				$iol3OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol3OS);
			$getSurgicalRecordRows['iol3OS']=$iol3OS;
			
			$iol4OS = $getSurgicalRecordRows['iol4OS'];
				$iol4OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol4OS);
			$getSurgicalRecordRows['iol4OS']=$iol4OS;
			
			$selecedIOLsOS = $getSurgicalRecordRows['selecedIOLsOS'];
				$selecedIOLsOS = print_getLenseName($selecedIOLsOS);
			$getSurgicalRecordRows['selecedIOLsOS']=$selecedIOLsOS;
			
			$lengthOS = $getSurgicalRecordRows['lengthOS'];
				$lengthTypeOS = $getSurgicalRecordRows['lengthTypeOS'];		
				if($lengthTypeOS == 'percent') $lengthTypeOS = '%';
			$getSurgicalRecordRows['lengthOS']=	$lengthOS;
			
			
			
		return $getSurgicalRecordRows;
		}
}

//---- end of function ----//


//---- new function by Aqib for IOS And Android app----//
//---- test_summary_app in tests.php is calling this function ----//

function print_iol_master_app($req){

	if(!empty($req)){
		$getSurgicalRecordStr = "SELECT * FROM iol_master_tbl WHERE iol_master_id = '$req'";
	}
	
	$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
	$rowsCount = imw_num_rows($getSurgicalRecordQry);
	if($rowsCount>0){
		$getSurgicalRecordRows = imw_fetch_assoc($getSurgicalRecordQry);
		
		
			$performedByOD = $getSurgicalRecordRows['performedByOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $performedByOD);			
				$performedByODLname = print_getData('lname', 'users', 'id', $performedByOD);
			$performedByOD = $performedByODFname." ".$performedByODLname;
			$getSurgicalRecordRows['performedByOD']=$performedByOD;
			
			
			
			$autoSelectOD = $getSurgicalRecordRows['autoSelectOD'];
				$autoSelectOD = print_getKHeadingName($autoSelectOD);
			$getSurgicalRecordRows['autoSelectOD']=$autoSelectOD;
			
			$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
				$iolMasterSelectOD = print_getKHeadingName($iolMasterSelectOD);
			$getSurgicalRecordRows['iolMasterSelectOD']=$iolMasterSelectOD;
			
			$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
				$topographerSelectOD = print_getKHeadingName($topographerSelectOD);
			$getSurgicalRecordRows['topographerSelectOD']=$topographerSelectOD;
			
			$provider_idOD = $getSurgicalRecordRows['performedByPhyOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $provider_idOD);
				$performedByODLname = print_getData('lname', 'users', 'id', $provider_idOD);
			$provider_idOD = $performedByODFname." ".$performedByODLname;
			$getSurgicalRecordRows['performedByPhyOD']=$provider_idOD;
			
			$powerIolOD = $getSurgicalRecordRows['powerIolOD'];
				$powerIolOD = print_getFormulaHeadName($powerIolOD);
			$getSurgicalRecordRows['powerIolOD']=$powerIolOD;
			
			$holladayOD = $getSurgicalRecordRows['holladayOD'];
				$holladayOD = print_getFormulaHeadName($holladayOD);
			$getSurgicalRecordRows['holladayOD']=$holladayOD;
			
			$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
				$srk_tOD = print_getFormulaHeadName($srk_tOD);
			$getSurgicalRecordRows['srk_tOD']=$srk_tOD;
			
			$hofferOD = $getSurgicalRecordRows['hofferOD'];
				$hofferOD = print_getFormulaHeadName($hofferOD);
			$getSurgicalRecordRows['hofferOD']=$hofferOD;
			
			$iol1OD = $getSurgicalRecordRows['iol1OD'];
				$iol1OD = print_getLenseName($iol1OD);
			$getSurgicalRecordRows['iol1OD']=$iol1OD;
			
			$iol2OD = $getSurgicalRecordRows['iol2OD'];
				$iol2OD = print_getLenseName($iol2OD);
			$getSurgicalRecordRows['iol2OD']=$iol2OD;
			
			$iol3OD = $getSurgicalRecordRows['iol3OD'];
				$iol3OD = print_getLenseName($iol3OD);
			$getSurgicalRecordRows['iol3OD']=$iol3OD;
			
			$iol4OD = $getSurgicalRecordRows['iol4OD'];
				$iol4OD = print_getLenseName($iol4OD);
			$getSurgicalRecordRows['iol4OD']=$iol4OD;
			
			$selecedIOLsOD = $getSurgicalRecordRows['selecedIOLsOD'];
				$selecedIOLsOD = print_getLenseName($selecedIOLsOD);
			$getSurgicalRecordRows['selecedIOLsOD']=$selecedIOLsOD;
			
			$lengthTypeOD = $getSurgicalRecordRows['lengthTypeOD'];
			if($lengthTypeOD == 'percent') $lengthTypeOD = '%';
				$getSurgicalRecordRows['lengthTypeOD']=$lengthTypeOD;
			
			$provider_idOS = $getSurgicalRecordRows['performedByOS'];
				$performedByOSFname = print_getData('fname', 'users', 'id', $provider_idOS);
				$performedByOSLname = print_getData('lname', 'users', 'id', $provider_idOS);
				$performedByOS = $performedByOSFname." ".$performedByOSLname;
			$getSurgicalRecordRows['performedByOS']=$provider_idOS;
			
			
			
			$autoSelectOS = $getSurgicalRecordRows['autoSelectOS'];
				$autoSelectOS = print_getKHeadingName($autoSelectOS);
			$getSurgicalRecordRows['autoSelectOS']=$autoSelectOS;
			
			$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
				$iolMasterSelectOS = print_getKHeadingName($iolMasterSelectOS);
			$getSurgicalRecordRows['iolMasterSelectOS']=$iolMasterSelectOS;
			
			$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
				$topographerSelectOS = print_getKHeadingName($topographerSelectOS);
			$getSurgicalRecordRows['topographerSelectOS']=$topographerSelectOS;
			
			$performedIolOS = $getSurgicalRecordRows['performedIolOS'];		
				$performedByPhyOSFname = print_getData('fname', 'users', 'id', $performedIolOS);
				$performedByPhyOSLname = print_getData('lname', 'users', 'id', $performedIolOS);
				$performedIolOS = $performedByPhyOSFname." ".$performedByPhyOSLname;
			$getSurgicalRecordRows['performedIolOS']=$performedIolOS;
			
			$powerIolOS = $getSurgicalRecordRows['powerIolOS'];
				$powerIolOS = print_getFormulaHeadName($powerIolOS);
			$getSurgicalRecordRows['powerIolOS']=$powerIolOS;
			
			$holladayOS = $getSurgicalRecordRows['holladayOS'];
				$holladayOS = print_getFormulaHeadName($holladayOS);
			$getSurgicalRecordRows['holladayOS']=$holladayOS;
				
			$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
				$srk_tOS = print_getFormulaHeadName($srk_tOS);
			$getSurgicalRecordRows['srk_tOS']=$srk_tOS;
			
			$hofferOS = $getSurgicalRecordRows['hofferOS'];
				$hofferOS = print_getFormulaHeadName($hofferOS);
			$getSurgicalRecordRows['hofferOS']=$hofferOS;
				
			$iol1OS = $getSurgicalRecordRows['iol1OS'];
				$iol1OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol1OS);	
			$getSurgicalRecordRows['iol1OS']=$iol1OS;
			
			$iol2OS = $getSurgicalRecordRows['iol2OS'];
				$iol2OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol2OS);
			$getSurgicalRecordRows['iol2OS']=$iol2OS;
			
			$iol3OS = $getSurgicalRecordRows['iol3OS'];
				$iol3OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol3OS);
			$getSurgicalRecordRows['iol3OS']=$iol3OS;
			
			$iol4OS = $getSurgicalRecordRows['iol4OS'];
				$iol4OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol4OS);
			$getSurgicalRecordRows['iol4OS']=$iol4OS;
			
			$selecedIOLsOS = $getSurgicalRecordRows['selecedIOLsOS'];
				$selecedIOLsOS = print_getLenseName($selecedIOLsOS);
			$getSurgicalRecordRows['selecedIOLsOS']=$selecedIOLsOS;
			
			$lengthOS = $getSurgicalRecordRows['lengthOS'];
				$lengthTypeOS = $getSurgicalRecordRows['lengthTypeOS'];		
				if($lengthTypeOS == 'percent') $lengthTypeOS = '%';
			$getSurgicalRecordRows['lengthOS']=	$lengthOS;
			
			
			
		return $getSurgicalRecordRows;
		}
}

// end of function print_iol_master_app() //




//TEST Other
function print_testother_app($req){  
 if(!empty($req)){
  $sql = "SELECT * FROM test_other WHERE test_other_id = '$req'"; 
 }
 $rowCellCount= imw_query($sql);
 if(imw_num_rows($rowCellCount)>0){
  
  $result = imw_fetch_assoc($rowCellCount);
  
  
  
   
  $result["descOd"] = stripslashes($result["descOd"]);
  $result["descOs"] = stripslashes($result["descOs"]);
  $result["inter_pret_od"] = stripslashes($result["inter_pret_od"]);
  $result["inter_pret_os"] = stripslashes($result["inter_pret_os"]);
  
  $result["techComments"] = stripslashes($result["techComments"]);
  
  
  
   $ordrbyName=$result['ordrby'];
   $ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);   
   $ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
   $ordrbyName= $ordrByFname." ".$ordrByLname;
   $result['ordrby'] =$ordrbyName;
  
   $performedBy=$result['performedBy'];
   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
   $performedBy= $performedByFname." ".$performedByLname;
   $result['performedBy'] =$performedBy;
  
   $physicianName=$result['phyName'];
   $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
   $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
   $physicianName= $physicianFname." ".$physicianLname;
   $result['phyName'] =$physicianName;
 
  $imagesHtml=getTestImages($result['test_other_id'],$sectionImageFrom="TestOther",$result['patientId']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['test_other_id']!=""){
      $arrPDFs = getAllTestPdf($result['test_other_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
     } 
     
  
    return $result;
 }
 }
 
// end of function print_testother_app() //



//new function of Laboratory for app calling from tests.php

function print_lab_app($req){  
 if(!empty($req)){
  $sql = "SELECT * FROM test_labs WHERE test_labs_id = '$req'"; 
 }
 $rowCellCount= imw_query($sql);
 if(imw_num_rows($rowCellCount)>0){
  
  $result = imw_fetch_assoc($rowCellCount);
  
  
  
   
  $result["descOd"] = stripslashes($result["descOd"]);
  $result["descOs"] = stripslashes($result["descOs"]);
  $result["inter_pret_od"] = stripslashes($result["inter_pret_od"]);
  $result["inter_pret_os"] = stripslashes($result["inter_pret_os"]);
  
  $result["techComments"] = stripslashes($result["techComments"]);
  
  
  
   $ordrbyName=$result['ordrby'];
   $ordrByFname = print_getData('fname', 'users', 'id', $ordrbyName);   
   $ordrByLname = print_getData('lname', 'users', 'id', $ordrbyName);
   $ordrbyName= $ordrByFname." ".$ordrByLname;
   $result['ordrby'] =$ordrbyName;
  
   $performedBy=$result['performedBy'];
   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
   $performedBy= $performedByFname." ".$performedByLname;
   $result['performedBy'] =$performedBy;
  
   $physicianName=$result['phyName'];
   $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
   $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
   $physicianName= $physicianFname." ".$physicianLname;
   $result['phyName'] =$physicianName;
 
  $imagesHtml=getTestImages($result['test_labs_id'],$sectionImageFrom="TestLabs",$result['patientId']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['test_labs_id']!=""){
      $arrPDFs = getAllTestPdf($result['test_labs_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
     } 
     
  
    return $result;
 }
}
 
// end of function print_lab_app() //


// new function of hrt for app  calling from tests.php// 
function print_hrt_app($req){
 if(!empty($req)){
  $sql = "SELECT * FROM nfa WHERE nfa_id = '$req'"; 
 }
 $rowResTemp = imw_query($sql);
 if($rowResTemp){
  $result = imw_fetch_assoc($rowResTemp);
  $performedBy=$result['performBy'];
   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
   $performedBy= $performedByFname." ".$performedByLname;
   $result['performBy'] =$performedBy;
  
   $physicianName=$result['phyName'];
   $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
   $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
   $physicianName= $physicianFname." ".$physicianLname;
   $result['phyName'] =$physicianName;
 
  $imagesHtml=getTestImages($result['nfa_id'],$sectionImageFrom="HRT",$result['patient_id']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['nfa_id']!=""){
      $arrPDFs = getAllTestPdf($result['nfa_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
     } 
  return $result;
 
  }
}
  // end or HRT New //
  
  
  
// new function of gdx for app calling from tests.php //
function print_gdx_app($req){
 if(!empty($req)){
  $sql = "SELECT * FROM test_gdx WHERE gdx_id = '$req'"; 
 }
 $rowOct = imw_query($sql);
 if($rowOct){
    $result = imw_fetch_assoc($rowOct);
 
   $performedBy=$result['performBy'];
   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
   $performedBy= $performedByFname." ".$performedByLname;
   $result['performBy'] =$performedBy;
  
   $physicianName=$result['phyName'];
   $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
   $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
   $physicianName= $physicianFname." ".$physicianLname;
   $result['phyName'] =$physicianName;
 
   $imagesHtml=getTestImages($result['gdx_id'],$sectionImageFrom="GDX",$result['patient_id']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['gdx_id']!=""){
      $arrPDFs = getAllTestPdf($result['gdx_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
     } 
     return $result;
 
  }
}
  // end function print_gdx_app //



//new function of pachy for app //

function print_pachy_app($req){
 
 if(!empty($req)){
  $sqlPachy = "SELECT * FROM pachy WHERE  pachy_id = '$req'";  
 
 
 
	 $resPachy = imw_query($sqlPachy);
	 if(imw_num_rows($resPachy)>0){
		 $result = imw_fetch_assoc($resPachy);
		 
		   $performedBy=$result['performedBy'];
		   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
		   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		   $performedBy= $performedByFname." ".$performedByLname;
		   $result['performedBy'] =$performedBy; 
		   
		   $performedBy=$result['phyName'];
		   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
		   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
		   $performedBy = $performedByFname." ".$performedByLname;
		   $result['phyName'] = $performedBy;
		   $imagesHtml=getTestImages($result['pachy_id'],$sectionImageFrom="Pacchy",$result['patientId']);
		
		  //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
			 $arr_alternative_images=array();
			 $str_alternative_images='';
			 if($result['pachy_id']!=""){
			  $arrPDFs = getAllTestPdf($result['pachy_id']);
			  $str_alternative_images= checknMakeImagesHTML($arrPDFs);
			  $imagesHtml.=$str_alternative_images;
			 } 
			 //------------------------------------
				  
			 if($imagesHtml!=""){
			  $result['imagesHtml']=$imagesHtml;
			 } 
		  return $result; 
  	}
  }
}
  // end of pachy//

 // new function of OCT for app calling from tests.php //
 function print_oct_app($req){
  if(!empty($req)){
   $sql = "SELECT * FROM oct WHERE oct_id = '$req'"; 
  }
   $rowOct = imw_query($sql);
  if($rowOct){
 $result = imw_fetch_assoc($rowOct);
 
            if($result['scanLaserOct']=="3"){
       $result['scanLaserOct'] = "Anterior Segment";
      }else if($result['scanLaserOct']=="2"){
       $result['scanLaserOct'] = "Retina";
      }else if($result['scanLaserOct']=="1"){
       $result['scanLaserOct'] = "Optic Nerve";
      }
      if($result['performBy'] || $result['ptUndersatnding']){
  $performedBy=$result['performBy'];
    $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
    $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
    $performedBy= $performedByFname." ".$performedByLname;
    $result['performBy'] =$performedBy; }
   
    $physicianName=$result['phyName'];
    $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
    $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
    $physicianName= $physicianFname." ".$physicianLname;
    $result['phyName'] =$physicianName;
  
  $imagesHtml=getTestImages($result['oct_id'],$sectionImageFrom="OCT",$result['patient_id']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['oct_id']!=""){
      $arrPDFs = getAllTestPdf($result['oct_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
     } 
     
   return $result;
   }
}
   // end of oct//  

// new function of oct_rnfl_app for app calling from tests.php //  
function print_oct_rnfl_app($req){
 
 if(!empty($req)){
  $sql = "SELECT * FROM oct_rnfl WHERE oct_rnfl_id = '$req'"; 
 }
 $rowOct = imw_query($sql);
 if($rowOct){
   $result=imw_fetch_assoc($rowOct);
   
   if($result['scanLaserOct_rnfl']=="3"){
      $result['scanLaserOct_rnfl'] = "Anterior Segment";
      }else if($result['scanLaserOct_rnfl'] =="2"){
       $result['scanLaserOct_rnfl'] = "Retina";
     }else if($result['scanLaserOct_rnfl'] =="1"){
       $result['scanLaserOct_rnfl'] = "Optic Nerve";
      }
 $performedBy=$result['performBy'];
   $performedByFname = print_getData('fname', 'users', 'id', $performedBy);   
   $performedByLname = print_getData('lname', 'users', 'id', $performedBy);
   $performedBy= $performedByFname." ".$performedByLname;
   $result['performBy'] =$performedBy;
  
   $physicianName=$result['phyName'];
   $physicianFname = print_getData('fname', 'users', 'id', $physicianName);   
   $physicianLname = print_getData('lname', 'users', 'id', $physicianName);
   $physicianName= $physicianFname." ".$physicianLname;
   $result['phyName'] =$physicianName;
 
  $imagesHtml=getTestImages($result['oct_rnfl_id'],$sectionImageFrom="OCT-RNFL",$result['patient_id']);

     //GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
     $arr_alternative_images=array();
     $str_alternative_images='';
     if($result['oct_rnfl_id']!=""){
      $arrPDFs = getAllTestPdf($result['oct_rnfl_id']);
      $str_alternative_images= checknMakeImagesHTML($arrPDFs);
      $imagesHtml.=$str_alternative_images;
     } 
     //------------------------------------
          
     if($imagesHtml!=""){
      $result['imagesHtml']=$imagesHtml;
   }
   return $result;
  }
}
  
  
  
  //end fuction oct_rnfl_app

?>