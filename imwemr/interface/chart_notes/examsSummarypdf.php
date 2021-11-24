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
Purpose: This file is used for printing exams summary. Now not in use.
Access Type : Include file
*/

//========Function By Ram To Include IDOC images in Printing Section========
$updir = data_path();
function getIDOCdrawingsBySection($sectionName,$primaryId){
	global $objImageManipulation;
	$returnStrImagePath="";
	$IDOCdrawingQuery="SELECT red_pixel, drawing_image_path FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for='".$sectionName."' and drawing_for_master_id='".$primaryId."' ";
	
	$IDOCdrawingQueryRes=imw_query($IDOCdrawingQuery);
	if($IDOCdrawingQueryRes){
		//echo "IN1";
		$IDOCdrawingQueryNumRow=imw_num_rows($IDOCdrawingQueryRes);
		if($IDOCdrawingQueryNumRow>0){
			//echo "IN2";
			$resDrawingArray=imw_fetch_array($IDOCdrawingQueryRes);
			if(empty($resDrawingArray["red_pixel"]) == false||!empty($resDrawingArray["drawing_image_path"])){
				
				$lamDrwingImagePath=$resDrawingArray["drawing_image_path"];
				$lamDrwingImage = realpath(dirname(__FILE__)."/../main/uploaddir".$lamDrwingImagePath);
				
				if(file_exists($lamDrwingImage)){
				$lamDrwingImage_b=str_replace(".png", "_b.png", $lamDrwingImage);
				if(file_exists($lamDrwingImage_b)){ $lamDrwingImage=$lamDrwingImage_b; }
				}
				
				//echo $lamDrwingImage;
				if(file_exists($lamDrwingImage)){				
					//echo "IN";
					$objImageManipulation->load($lamDrwingImage);
					$arrPathInfo = pathinfo($lamDrwingImage);
					$strFilenameJPG = $arrPathInfo["dirname"]."/".$arrPathInfo["filename"].".jpg";
					$objImageManipulation->save($strFilenameJPG);
					$returnStrImagePath = $strFilenameJPG;			
				}
			}			
		} 
	}

	//echo $returnStrImagePath;
	//exit();
	return $returnStrImagePath;
}


function isAppletModified($sign,$bag="0")
{
	$sign = trim($sign);

		if(empty($sign)){
			$ret = false;
		}else if(!empty($sign) && ((strpos($sign, "/") !== false) ||
			(strpos($sign, "-") === false) || (strpos($sign, ":") === false) || (strpos($sign, ";") === false)) ){
			// for new applet image string, return true
			$ret = true;
		}else{
			$signLength = strlen($sign);
			$bag = ($bag == "0") ? "0-:;" : $bag;
			$ret = false;
			for($i=0;$i<$signLength;$i++)
			{
				$signChar =  substr($sign,$i,1);

				$pos = strpos($bag, $signChar);
				if($pos === false)
				{
					//return true;
					$ret = true;
					break;
				}
			}
		}
		return $ret;
}

function getAppletImageOcular($id,$tblName,$idFieldName,$pixelFieldName,$imgPath,$alt='Image Here',$saveImg="0", $width="0",$hgt="0")
{
	//echo "imgGd.php?id=$id&tbl=$tblName&pixelField=$pixelFieldName&idField=$idFieldName&imgName=$imgPath";
	if($saveImg == "1" || $saveImg == "3"){
	
		$id=$id;
		$tblName=$tblName;
		$idFieldName=$idFieldName;
		$pixelFieldName = $pixelFieldName;
		$imgPath = $imgPath;
		$alt =$alt;
		$saveImg=$saveImg;
		include($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/imgGd.php');	
		return true;
	}else{
	
		if($width!="0"){
			$strWidth = "width='".$width."'";
		}
		
		if($hgt!="0"){
			$strHgt = "height='".$hgt."'";
		}
		
		return "<img src=\"".$GLOBALS['rootdir']."/patient_info/complete_pt_rec/imgGd.php?id=$id&tbl=$tblName&pixelField=$pixelFieldName&idField=$idFieldName&imgName=$imgPath&saveImg=$saveImg\" alt=\"$alt\" ".$strWidth." ".$strHgt." />";
	}
}

function SaveAndShowNewAppletImageOcular($base64String){
	$updir=substr(data_path(), 0, -1);
	//$strFileSave=realpath(dirname(__FILE__)."/html2pdfprint/");
	$strFileSave=data_path();	
	$srt=rand(1,9000000).".jpg";
	$strFileSave2=$strFileSave."/".$srt;	
	$fp = fopen($strFileSave2,"w+");
	fputs($fp, base64_decode($base64String));
	fclose($fp);	
	return $srt;
}
//========Function By Ram To Include IDOC images in Printing Section========

$sql = "SELECT ".
		 "chart_pupil.isPositive AS flagPosPupil, chart_pupil.wnl AS flagWnlPupil, chart_pupil.examinedNoChange AS chPupil, ".
		 "chart_pupil.sumOdPupil, chart_pupil.sumOsPupil, chart_pupil.pupil_id, chart_pupil.descPupil,chart_pupil.wnlPupilOd,chart_pupil.wnlPupilOs,".
		 "chart_eom.isPositive AS flagPosEom, chart_eom.wnl AS flagWnlEom, chart_eom.examined_no_change  AS chEom, ".
		 "chart_eom.sumEom,chart_eom.eomDrawing,chart_eom.eomDrwNear,chart_eom.eom_id, chart_eom.descEom,chart_eom.wnl,".
		 "chart_external_exam.isPositive AS flagPosEe, chart_external_exam.wnl AS flagWnlEe, chart_external_exam.examined_no_change  AS chEe, ".
		 "chart_external_exam.ee_drawing, chart_external_exam.external_exam_summary, chart_external_exam.sumOsEE, chart_external_exam.ee_id, chart_external_exam.descExternal,chart_external_exam.wnlEeOd,chart_external_exam.wnlEeOs,".
		 "chart_iop.isPositive AS flagPosIop, chart_iop.wnl AS flagWnlIop, chart_iop.examined_no_change AS chIop, ".
		 "chart_iop.sumOdIop, chart_iop.sumOsIop, chart_iop.gonio_od_drawing,chart_iop.gonio_od_desc,chart_iop.gonio_os_desc, chart_iop.iop_id,chart_iop.desc_ig,chart_iop.wnlIopOd,chart_iop.wnlIopOs, ".
		 "chart_gonio.wnlOd AS wnlGonioOd, chart_gonio.wnlOs AS wnlGonioOs, chart_gonio.desc_ig, chart_gonio.gonio_od_summary, chart_gonio.gonio_os_summary, ".
		 "chart_lids.id, chart_lids.posLids, chart_lids.wnlLids, chart_lids.ncLids, chart_lids.lid_conjunctiva_summary, chart_lids.sumLidsOs, chart_lids.wnlLidsOd,  chart_lids.wnlLidsOs, chart_lids.ncLids_od, chart_lids.ncLids_os,". 
		 "chart_lesion.id, chart_lesion.posLesion, chart_lesion.wnlLesion, chart_lesion.ncLesion, chart_lesion.lesion_summary, chart_lesion.sumLesionOs, chart_lesion.wnlLesionOd, chart_lesion.wnlLesionOs, chart_lesion.ncLesion_od, chart_lesion.ncLesion_os,".
		 "chart_lid_pos.id, chart_lid_pos.posLidPos, chart_lid_pos.wnlLidPos, chart_lid_pos.ncLidPos, chart_lid_pos.lid_deformity_position_summary, chart_lid_pos.sumLidPosOs, chart_lid_pos.wnlLidPosOd, chart_lid_pos.wnlLidPosOs, chart_lid_pos.ncLidPos_od, chart_lid_pos.ncLidPos_os,".
		 "chart_lac_sys.id, chart_lac_sys.ncLacSys, chart_lac_sys.posLacSys, chart_lac_sys.wnlLacSys, chart_lac_sys.lacrimal_system_summary, chart_lac_sys.sumLacOs, chart_lac_sys.wnlLacSysOd, chart_lac_sys.wnlLacSysOs, chart_lac_sys.ncLacSys_od, chart_lac_sys.ncLacSys_os,".		 
		 "chart_drawings.id as drawingId, chart_drawings.posDraw AS posDrawLa, chart_drawings.wnlDraw AS wnlDrawLa, chart_drawings.ncDraw, chart_drawings.exm_drawing, chart_drawings.drw_od_txt, chart_drawings.drw_os_txt, chart_drawings.ncDraw_od, chart_drawings.ncDraw_os, chart_drawings.idoc_drawing_id AS idoc_drawing_id_rv, ".
		 "chart_optic.isPositive AS flagPosOptic, chart_optic.wnl AS flagWnlOptic, chart_optic.examined_no_change AS chOptic, ".
		 "chart_optic.od_text, chart_optic.os_text,chart_optic.cdr_od_summary,chart_optic.cdr_os_summary, chart_optic.optic_nerve_od_summary, chart_optic.optic_nerve_os_summary,chart_optic.wnlOpticOd,chart_optic.wnlOpticOs,chart_optic.wnl_value_Optic, chart_optic.optic_id, ".
	     "chart_retinal_exam.wnlRetinal, chart_retinal_exam.posRetinal, chart_retinal_exam.ncRetinal, chart_retinal_exam.wnlRetinalOd, 
		 chart_retinal_exam.wnlRetinalOs, chart_retinal_exam.wnl_value_RetinalExam, chart_retinal_exam.periNotExamined, 
		 chart_retinal_exam.retinal_od_summary, chart_retinal_exam.retinal_os_summary, chart_retinal_exam.wnl_value_RetinalExam,".
		 "chart_vitreous.wnlVitreous, chart_vitreous.posVitreous, chart_vitreous.ncVitreous, chart_vitreous.wnlVitreousOd,
		 chart_vitreous.wnlVitreousOs, chart_vitreous.vitreous_od_summary, chart_vitreous.vitreous_os_summary,
		 chart_vitreous.wnl_value_Vitreous,".	
		 "chart_blood_vessels.wnlBV, chart_blood_vessels.posBV, chart_blood_vessels.ncBV, chart_blood_vessels.wnlBVOd,
		 chart_blood_vessels.wnlBVOs, chart_blood_vessels.wnl_value_BV, chart_blood_vessels.blood_vessels_od_summary, 
		 chart_blood_vessels.blood_vessels_os_summary,".
		 "chart_macula.wnlMacula, chart_macula.posMacula, chart_macula.ncMacula, chart_macula.wnlMaculaOd,
		 chart_macula.wnlMaculaOs, chart_macula.wnl_value_Macula, chart_macula.macula_od_summary,
		 chart_macula.macula_os_summary, ".
		 "chart_periphery.wnlPeri, chart_periphery.posPeri, chart_periphery.ncPeri, chart_periphery.wnlPeriOd,
		 chart_periphery.wnlPeriOs, chart_periphery.wnl_value_Peri, chart_periphery.periphery_od_summary,
		 chart_periphery.periphery_os_summary,".
		 "CONCAT(chart_retinal_exam.statusElem,	chart_vitreous.statusElem, chart_blood_vessels.statusElem, chart_macula.statusElem, chart_periphery.statusElem, chart_drawings.statusElem) AS chart_rvStatusElem,".
		"chart_conjunctiva.posConj, chart_conjunctiva.wnlConj, chart_conjunctiva.ncConj, chart_conjunctiva.conjunctiva_od_summary, chart_conjunctiva.conjunctiva_os_summary, chart_conjunctiva.wnlConjOd, chart_conjunctiva.wnlConjOs, 
		 chart_cornea.posCorn, chart_cornea.wnlCorn, chart_cornea.ncCorn, chart_cornea.cornea_od_summary, chart_cornea.cornea_os_summary, chart_cornea.wnlCornOd, chart_cornea.wnlCornOs, 
		 chart_ant_chamber.posAnt, chart_ant_chamber.wnlAnt, chart_ant_chamber.ncAnt, chart_ant_chamber.anf_chamber_od_summary, chart_ant_chamber.anf_chamber_os_summary, chart_ant_chamber.wnlAntOd, chart_ant_chamber.wnlAntOs, 
		 chart_iris.posIris, chart_iris.wnlIris, chart_iris.ncIris, chart_iris.iris_pupil_od_summary, chart_iris.iris_pupil_os_summary, chart_iris.wnlIrisOd, chart_iris.wnlIrisOs, 
		 chart_lens.posLens, chart_lens.wnlLens, chart_lens.ncLens, chart_lens.lens_od_summary, chart_lens.lens_os_summary, chart_lens.wnlLensOd, chart_lens.wnlLensOs,".
		 "amsler_grid.amsler_od, amsler_grid.amsler_os, amsler_grid.id AS AmslerId, ".
		 "chart_cvf.drawOd, chart_cvf.drawOs, chart_cvf.cvf_id, ".
		 "chart_diplopia.drawing, chart_diplopia.dip_id ".
		 "FROM chart_master_table ".
		 "LEFT JOIN chart_pupil ON chart_pupil.formId = chart_master_table.id ".
		 "LEFT JOIN chart_eom ON chart_eom.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_external_exam ON chart_external_exam.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_iop ON chart_iop.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_lids ON chart_lids.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_lesion ON chart_lesion.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_lid_pos ON chart_lid_pos.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_lac_sys ON chart_lac_sys.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_drawings ON chart_drawings.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_gonio ON chart_gonio.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_optic ON chart_optic.form_id = chart_master_table.id ".		
		 "LEFT JOIN chart_retinal_exam ON chart_retinal_exam.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_vitreous ON chart_vitreous.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_blood_vessels ON chart_blood_vessels.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_macula ON chart_macula.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_periphery ON chart_periphery.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_conjunctiva ON chart_conjunctiva.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_cornea ON chart_cornea.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_ant_chamber ON chart_ant_chamber.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_iris ON chart_iris.form_id = chart_master_table.id ".
		 "LEFT JOIN chart_lens ON chart_lens.form_id = chart_master_table.id ".
		 "LEFT JOIN amsler_grid ON amsler_grid.form_id = chart_master_table.id ".		 
		 "LEFT JOIN chart_cvf ON chart_cvf.formId = chart_master_table.id ".
		 "LEFT JOIN chart_diplopia ON chart_diplopia.formId = chart_master_table.id ".		 
		 "WHERE chart_master_table.id = '".$form_id."'";
//print  $sql;
//exit();
$sqlQry = imw_query($sql);

if(imw_num_rows($sqlQry)>0){ 
	$sqlRow = imw_fetch_array($sqlQry);
//print_r($sqlRow);
extract($sqlRow);
//print $la_drawing ;
	//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For NSE Merged Data---------
	$rv_id_arc = $sle_id_arc = $la_id_arc = "";
	if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
		if(empty($rv_id)){
			$sql = "SELECT  rv_id, 
					isPositive AS flagPosRv, wnl AS flagWnlRv, examined_no_change AS chRv, wnlVitreous, wnlMacula, wnlPeri, wnlBV, wnlDraw AS wnlDrawRv, wnlRetinal, posVitreous, posMacula, posPeri, posBV, posDraw AS posDrawRv, posRetinal, ncVitreous, ncMacula, ncPeri, ncBV, ncDrawRv, ncRetinal, od_drawing, os_drawing, idoc_drawing_id AS idoc_drawing_id_rv, 
					vitreous_od_summary, macula_od_summary, periphery_od_summary, blood_vessels_od_summary, retinal_od_summary, 
					vitreous_os_summary, macula_os_summary, periphery_os_summary, blood_vessels_os_summary, retinal_os_summary, 
					descRv, wnlVitreousOd, wnlVitreousOs, wnlMaculaOd, wnlMaculaOs, wnlPeriOd, wnlPeriOs, wnlBVOd, wnlBVOs, wnlRetinalOd, wnlRetinalOs, wnl_value_RetinalExam, periNotExamined, wnl_value_Vitreous, wnl_value_Macula, wnl_value_Peri, wnl_value_BV, wnl_value_RetinalExam, statusElem as chart_rvStatusElem 
					FROM chart_rv_archive 
					WHERE form_id='".$form_id."'";
			$sqlQry=imw_query($sql);
			$sqlRow = imw_fetch_array($sqlQry);
			if($sqlRow != false){ 
				extract($sqlRow); 
				$rv_id_arc = "_archive";
			}
		}
		
		if(empty($sle_id)){
			$sql = "SELECT  sle_id,
				    isPositive AS flagPosSle, wnl AS flagWnlSle, examined_no_change AS chSle, posConj, posCorn, posAnt, posIris, posLens, posDraw AS posDrawSle, wnlConj, wnlCorn, wnlAnt, wnlIris, wnlLens, wnlDraw AS wnlDrawSle, ncConj, ncCorn, ncAnt, ncIris, ncLens, ncDrawSle, conjunctiva_od_drawing, conjunctiva_os_drawing, lens_os_drawing, lens_od_drawing, conjunctiva_od_summary, cornea_od_summary, anf_chamber_od_summary, iris_pupil_od_summary, lens_od_summary, conjunctiva_os_summary, cornea_os_summary, anf_chamber_os_summary, iris_pupil_os_summary, lens_os_summary, descSle, wnlConjOd, wnlConjOs , wnlCornOd, wnlCornOs, wnlAntOd, wnlAntOs , wnlIrisOd , wnlIrisOs, wnlLensOd, wnlLensOs 
					FROM chart_slit_lamp_exam_archive 
					WHERE form_id='".$form_id."'";
			$sqlQry=imw_query($sql);
			$sqlRow = imw_fetch_array($sqlQry);
			if($sqlRow != false){ 
				extract($sqlRow); 
				$sle_id_arc = "_archive";
			}
		}
	}
	//-------- End ----------------------------------------------------------------------------------
	 if(($pupilPrint == true) && (($sumOdPupil) || ($sumOsPupil) || ($wnlPupilOd) || ($wnlPupilOs))){
	 //if($pupilPrint == true) {
		?>

		
				<!--<table style="width:740px;" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">-->
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width:740px;" colspan="3" valign="middle" class="text_10b tb_heading" ><b>Pupil</b></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm">&nbsp;</td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php osLable();?></td>
						
					</tr>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
						<?php 
						for($a=0;$a<=strlen($sumOdPupil);$a++){
							$findChar = substr($sumOdPupil,$a,1);
							if($findChar=='.'){
								$newsumOdPupil.=(substr($sumOdPupil,$a,1));
								$newsumOdPupil.='<br>';
							}
							else{
								$newsumOdPupil.=(substr($sumOdPupil,$a,1));
							}							
						}
						
						for($a=0;$a<=strlen($sumOsPupil);$a++){
							$findChar = substr($sumOsPupil,$a,1);
							if($findChar=='.'){
								$newsumOsPupil.=(substr($sumOsPupil,$a,1));
								$newsumOsPupil.='<br>';
							}
							else{
								$newsumOsPupil.=(substr($sumOsPupil,$a,1));
							}	
						}						
						?>
						<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($newsumOdPupil && $newsumOdPupil!='PERRLA') echo $newsumOdPupil; elseif($wnlPupilOd){echo 'PERRLA, Negative APD';} ?></td>
						<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($newsumOsPupil && $newsumOsPupil!='PERRLA') echo $newsumOsPupil; elseif($wnlPupilOs){echo ' PERRLA, Negative APD';} ?></td>						
					</tr>
				<!-- </table> -->
				<?php if($descPupil){
				?>
				<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
					<tr>
						<td class="text_9 bdrbtm" width="75">Comments :</td>
						<td class="text_9 bdrbtm" align="left" colspan="2"><?php if($descPupil) echo $descPupil; else echo '&nbsp;'; ?></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm"width="75">&nbsp;</td>
						<td class="text_9 bdrbtm" align="left" valign="top" ></td>
					</tr>
				<!-- </table> -->
				<?php }
				?>
			</table>		
		<?php
					
	} 
	if(($eomPrint == true) && ($sumEom || $flagWnlEom)){
	//if($eomPrint == true){
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width:740px;" valign="middle" colspan="2" class="text_10b tb_heading"><b>EOM</b></td>
					</tr>
					<tr>
						<td style="width:50px;" class="text_9 bdrbtm" align="left" valign="top" >&nbsp;</td>
						<td style="width:650px;" class="text_9 bdrbtm" align="left" valign="top" ><?php if($sumEom && $sumEom!='WNL') {echo $sumEom;} elseif($flagWnlEom==1) {echo 'WNL';} ?></td>
					</tr>
				<?php
				if($objDrw->isAppletModified($eomDrawing)){?>
							<tr>
								<td style="width:50px;">&nbsp;</td>
								<td style="width:650px;" align="center">							
									<table style="width:650px;" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">
                                    	<tr>
                                        	<td style="width:325px;" align="center">
												<?php 								
                                                    $imageEOMNAme=drawOnImageImage($eomDrawing);
                                                    $imageEOMNAme = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$imageEOMNAme);
                                                    if(file_exists($imageEOMNAme)){
                                                        echo('<img src="'.$imageEOMNAme.'" width="300" height="100"/>');
                                                    }
                                                    $ChartNoteImagesString[]=$imageEOMNAme;			
                                                ?>
											</td>
                                            <td align="center" style="width:325px;">
												<?php 
                                                    if($objDrw->isAppletModified($eomDrwNear)){?>
                                                        <?php 								
                                                            $imageEOMNear=drawOnImageImage($eomDrwNear);
                                                            $imageEOMNear=realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$imageEOMNear);
                                                            if(file_exists($imageEOMNear)){
                                                            echo('<img src="'.$imageEOMNear.'" width="300" height="100"/>');
                                                            }
                                                            $ChartNoteImagesString[]=$imageEOMNear;			
                                                        ?>
                                                        <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>    
                                	</table>
                                </td>
							</tr>
					<?php					
				}
				if($descEom){
				?>
				<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
					<tr>
						<td style="width:50px;">Comments :</td>
						<td style="width:650px;" class="text_9" align="left" colspan="2"><?php if($descEom) echo $descEom; else echo '&nbsp;'; ?></td>
					</tr>
					<tr>
						<td style="width:50px;">&nbsp;</td>
						<td style="width:650px;"class="text_9" align="left" valign="top" colspan="2" ></td>
					</tr>
					
				<!-- </table> -->
				<?php 					
				} ?>				
			</table>
			<?php 					
	}
	if(($externalPrint == true) && (($external_exam_summary) || ($sumOsEE) || ($wnlEeOd) || ($wnlEeOs))){
	//if($externalPrint == true){
		if(!$descEom){
			//echo $html = '<table style="width:740px;" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">';
		}
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td style="width:740px;" colspan="3" valign="middle" class="text_10b tb_heading" ><b>External</b></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm">&nbsp;</td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php osLable();?></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="70">&nbsp;</td>
						<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($external_exam_summary && $external_exam_summary!='WNL'){echo $external_exam_summary;} elseif($wnlEeOd == 1){echo 'WNL';} ?></td>
						<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumOsEE && $sumOsEE!='WNL'){echo $sumOsEE;} elseif($wnlEeOs == 1){echo 'WNL';}  ?></td>
					</tr>
				<!-- </table> -->
					<?php
					if($objDrw->isAppletModified($ee_drawing)){
						//External Exam
						$idEE = $ee_id;
						$tableEE = 'chart_external_exam';
						$idNameEE = 'ee_id';
						$pixelEeDrawing = 'ee_drawing';
						$imageEE = realpath(dirname(__FILE__).'/../../images/pic_face.jpg');
						$altEE = 'EE'; 			
						?>
					 <!-- <table style="width:740px;" border="0" align="center"> -->
						<tr>
							<td class="text_9" align="left" valign="top" width="50">&nbsp;</td>
						 	<td  colspan="2"><?php getAppletImageOcular($idEE,$tableEE,$idNameEE,$pixelEeDrawing,$imageEE,$altEE,"1");
						 	$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
							if(file_exists($gdFilename)){
								echo('<img src="'.$gdFilename.'" height="100" width="100" align="center"/>');								 
							}
							$ChartNoteImagesString[]=$gdFilename;					 
 							?>
							</td>
						</tr>
					<!-- </table> -->
					<?php }
					
				if($descExternal ){
				?>
				<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50">Comments :</td>
						<td  colspan="2" class="text_9 bdrbtm" align="left" ><?php if($descExternal ) echo $descExternal; else echo '&nbsp;'; ?></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
						<td  colspan="2" class="text_9 bdrbtm" align="left" valign="top" ></td>
					</tr>				
				<?php }
				?>
			</table>
		<?php
	} if($l_and_a==true){
	if(
		(($lid_conjunctiva_summary) || ($sumLidsOs) || ($wnlLidsOd)>0)
		||
		(($lesion_summary) || ($sumLesionOs) || ($wnlLesionOd)>0)
		||
		(($lid_deformity_position_summary) || ($sumLidPosOs) || ($wnlLidPosOd)>0)
		||
		(($lacrimal_system_summary) || ($sumLacOs) || ($wnlLacSysOd)>0)
		||
		($objDrw->isAppletModified($la_drawing))
		){
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width:740px;" colspan="3" valign="middle" class="text_10b tb_heading"><b>L &amp; A </b></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50"></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php osLable();?></td>
					</tr>				
					<?php
					if(($lid_conjunctiva_summary) || ($sumLidsOs) || ($wnlLidsOd) > 0){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Lids</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lid_conjunctiva_summary) echo $lid_conjunctiva_summary; elseif($wnlLidsOd == 1){echo 'WNL';} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumLidsOs ) echo $sumLidsOs; elseif($wnlLidsOs == 1){echo 'WNL';} ?></td>
						</tr>
						<!-- <tr>
							<td class="text_9" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr> -->
						<?php
					}
					if(($lesion_summary) || ($sumLesionOs) || ($wnlLesionOd) > 0){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Lesion</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lesion_summary) echo $lesion_summary; elseif($wnlLesionOd == 1){echo 'WNL';} ?></td>
							<td class="text_9 bdrbtm"  align="left" valign="top" width="300"><?php if($sumLesionOs ) echo $sumLesionOs; elseif($wnlLesionOs == 1){echo 'WNL';} ?></td>
						</tr>
						<!-- <tr>
							<td class="text_9" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr> -->
						<?php
					}					
					if(($lid_deformity_position_summary) || ($sumLidPosOs ) || ($wnlLidPosOd) > 0){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="90"><b>Lid&nbsp;Position</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lid_deformity_position_summary) echo $lid_deformity_position_summary; elseif($wnlLidPosOd == 1){echo 'WNL';} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumLidPosOs ) echo $sumLidPosOs; elseif($wnlLidPosOs == 1){echo 'WNL';} ?></td>
						</tr>
						<!-- <tr>
							<td class="text_9" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr> -->
						<?php
					}					
					if(($lacrimal_system_summary) || ($sumLacOs ) || ($wnlLacSysOd) > 0){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" nowrap valign="top" width="50"><b>Lacrimal System </b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lacrimal_system_summary ) echo $lacrimal_system_summary; elseif($wnlLacSysOd == 1){echo 'WNL';} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumLacOs ) echo $sumLacOs; elseif($wnlLacSysOs == 1){echo 'WNL';} ?></td>
						</tr>
						<?php
					}
					if($descLa){
					?>
					<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">Comments :</td>
							<td colspan="2" class="text_9 bdrbtm" align="left"><?php if($descLa) echo $descLa; else echo '&nbsp;'; ?></td>
						</tr>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td colspan="2" class="text_9 bdrbtm" align="left" valign="top" ></td>
						</tr>
					<!-- </table> -->
					<?php
					} 					
					if($objDrw->isAppletModified($la_drawing)){
						?>
						<?php
						if (strpos($la_drawing,"/") !== false ){
							$laImg=SaveAndShowNewAppletImageOcular($la_drawing);
							$ChartNoteImagesString[]=$laImg;							
							?>							
							<tr>
								<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
								<td class="text_9 bdrbtm"  colspan="2" align="center" valign="top" >
								
								<?php									
									$laImg = $updir.$laImg;
									if(file_exists($laImg)){									
									$laImg = realpath($laImg);
									//$laImg = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$laImg);
									?>
								<img src="<?php echo($laImg);?>" height="200" width="563"/>
								<?php } ?>
								</td>
							</tr>						
						<?php
							}else{
						?>					
						
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td class="text_9 bdrbtm" colspan="2" align="center" valign="top" >
								<?php
									if($objDrw->isAppletModified($la_drawing)){
										$tableLA = 'chart_drawings';
										$idNameLA = 'id';
										$pixelLaDrawing = 'exm_drawing';
										$imageLA = realpath(dirname(__FILE__).'/../../images/La.jpg');
										$altLA = 'LA'; 
										getAppletImageOcular($idLa,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,"1");
										$gdFilename = realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
										echo('<img src="'.$gdFilename.'" height="164" width="370" />');
									}					
								?>
							</td>
						</tr>
						
						<?php
						}
					}
					?>	
					<?php	
					if($la_od_txt<>"" || $la_os_txt<>""){?>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php echo($la_od_txt);?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php echo($la_os_txt);?></td>
					</tr>	
					<?php }
					?>
										
				</table>
				
		<?php
		}
		} 
		if(($IOPPrint == true) && ((($sumOdIop) || ($sumOsIop )) || (($gonio_od_summary ) || ($gonio_os_summary )))){
		//if($IOPPrint == true){
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td style="width:740px;" colspan="3" valign="middle" class="text_10b tb_heading" ><b>IOP/Gonio</b></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm">&nbsp;</td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php osLable();?></td>
					</tr>
					<?php
					if($sumOdIop || $sumOsIop){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>IOP</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumOdIop ) echo str_replace("<br> ","<br/>",$sumOdIop); else echo '&nbsp;'; ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($sumOsIop) echo str_replace("<br> ","<br/>",$sumOsIop); else echo '&nbsp;'; ?></td>
						</tr>
						<?php
					}
					if($gonio_od_summary || $gonio_os_summary || $wnlGonioOd || $wnlGonioOs){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Gonio</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($gonio_od_summary ) echo $gonio_od_summary; elseif($wnlGonioOd == 1){echo 'WNL';} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($gonio_os_summary ) echo $gonio_os_summary; elseif($wnlGonioOs == 1){echo 'WNL';} ?></td>
						</tr>
						<?php
					}
					
					if (strpos($gonio_od_drawing,"/") !== false ){
						$GonioIMg=SaveAndShowNewAppletImageOcular($gonio_od_drawing);
						$GonioIMg = $updir.$GonioIMg;
						$ChartNoteImagesString[]=$GonioIMg;
						?>						
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Drawing</b></td>
							<td class="text_9 bdrbtm" colspan="2" align="center" valign="top">
							<?php if(file_exists($GonioIMg)){ ?>
							<img src="<?php echo($GonioIMg);?>" height="120" width="200"/>
							<?php } ?>
							</td>
						</tr>						
					<?php
						}if($gonio_od_desc!="" || $gonio_os_desc!=""){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($gonio_od_desc ) echo $gonio_od_desc; else echo '&nbsp;'; ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($gonio_os_desc) echo $gonio_os_desc; else echo '&nbsp;'; ?></td>
						</tr>
						<?php
					}
										
					if($desc_ig){
					?>
					<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">Comments :</td>
							<td colspan="2" class="text_9 bdrbtm" align="left"><?php if($desc_ig) echo $desc_ig; else echo '&nbsp;'; ?></td>
						</tr>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td colspan="2" class="text_9 bdrbtm" align="left" valign="top" ></td>
						</tr>
					<!-- </table> -->
					<?php
					}
					?> 
				</table> 
			
		<?php
	}

if($SLEPrint == true){
		 /*if(
		(($conjunctiva_od_summary && $conjunctiva_od_summary!='Clear') || ($conjunctiva_os_summary && $conjunctiva_os_summary!='Clear')) 
		|| 
		(($cornea_od_summary && $cornea_od_summary!='Clear') || ($cornea_os_summary && $cornea_os_summary!='Clear')) 
		|| 
		(($anf_chamber_od_summary && $anf_chamber_od_summary!='Deep and quiet') || ($anf_chamber_os_summary && $anf_chamber_os_summary!='Deep and quiet')) 
		|| 
		(($iris_pupil_od_summary && $iris_pupil_od_summary!='WNL') || ($iris_pupil_os_summary && $iris_pupil_os_summary!='WNL')) 
		|| 
		(($lens_od_summary && $lens_od_summary!='Clear') || ($lens_os_summary && $lens_os_summary!='Clear')) 
		|| 
		($objDrw->isAppletModified($conjunctiva_od_drawing) || $objDrw->isAppletModified($conjunctiva_os_drawing)) 
		|| 
		($objDrw->isAppletModified($lens_od_drawing) || $objDrw->isAppletModified($lens_os_drawing))){ */
		/*if(
		(($conjunctiva_od_summary) || ($conjunctiva_os_summary )) || (($wnlConjOd) || ($wnlConjOs))
		(($cornea_od_summary) || ($cornea_os_summary))  || (($wnlCornOd) || ($wnlCornOs))
		(($anf_chamber_od_summary) || ($anf_chamber_os_summary))  || (($wnlAntOd) || ($wnlAntOs))
		(($iris_pupil_od_summary) || ($iris_pupil_os_summary))  || (($wnlIrisOd) || ($wnlIrisOs))
		(($lens_od_summary) || ($lens_os_summary))  || (($wnlLensOd) || ($wnlLensOs))

		){*/
		
		//Fix for conjuctiva wnl issue---
		if(empty($wnlConjOd) && empty($wnlConjOs) && !empty($wnlConj) && empty($posConj)){
				$wnlConjOd = $wnlConjOs = "1";
		}
		//Fix for conjuctiva wnl issue---
		
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" > 
					<tr>
						<td style="width:740px;"  valign="middle" colspan="3" class="text_10b tb_heading" bgcolor="#c0c0c0"><b>Slit Lamp Exam</b></td>
					</tr>
				<!-- </table>
				<table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
					<tr>
						<td class="text_9 bdrbtm" width="50">&nbsp;</td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="325" align="left"><?php osLable();?></td>
					</tr>
					<?php
					if((($conjunctiva_od_summary) || ($conjunctiva_os_summary )) || (($wnlConjOd) || ($wnlConjOs))){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Conjunctiva&nbsp;</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($conjunctiva_od_summary){echo $conjunctiva_od_summary;}elseif($wnlConjOd == 1){echo "Clear";} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($conjunctiva_os_summary){echo $conjunctiva_os_summary;} elseif($wnlConjOs ==1){echo "Clear";}  ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="15px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($cornea_od_summary) || ($cornea_os_summary) || ($wnlCornOd) || ($wnlCornOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Cornea</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($cornea_od_summary){echo $cornea_od_summary;} elseif($wnlCornOd == 1){echo "Clear";} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($cornea_os_summary){echo $cornea_os_summary;} elseif($wnlCornOs == 1){echo "Clear";} ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="15px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($anf_chamber_od_summary) || ($anf_chamber_os_summary) || ($wnlAntOd) || ($wnlAntOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Chamber</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($anf_chamber_od_summary){echo $anf_chamber_od_summary;}elseif($wnlAntOd  == 1){echo "Deep and quiet";} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($anf_chamber_os_summary){echo $anf_chamber_os_summary;}elseif($wnlAntOs  == 1){echo "Deep and quiet";} ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="15px">&nbsp;</td>
						</tr>
						<?php
					}
					
					if(($iris_pupil_od_summary) || ($iris_pupil_os_summary) || ($wnlIrisOd) || ($wnlIrisOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="80"><b>Iris&nbsp;&amp;&nbsp;Pupil</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($iris_pupil_od_summary){echo $iris_pupil_od_summary;} elseif($wnlIrisOd == 1){echo "WNL";} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($iris_pupil_os_summary){echo $iris_pupil_os_summary;} elseif($wnlIrisOs == 1){echo "WNL";} ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="15px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($lens_od_summary) || ($lens_os_summary) || ($wnlLensOd) || ($wnlLensOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Lens</b></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lens_od_summary){echo $lens_od_summary;} elseif($wnlLensOd == 1){echo "Clear";} ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="300"><?php if($lens_os_summary){echo $lens_os_summary;} elseif($wnlLensOs == 1){echo "Clear";} ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="15px">&nbsp;</td>
						</tr>
						<?php
					}
					if (strpos($conjunctiva_od_drawing,"/") !== false ){
						$conjuncImg=SaveAndShowNewAppletImageOcular($conjunctiva_od_drawing);
						$conjuncImg=$updir.$conjuncImg;
						$ChartNoteImagesString[]=$conjuncImg;
						?>
						<tr>
							<td width="50" align="left" valign="top" class="text_9 bdrbtm"><strong>Drawing</strong></td>
							<td class="text_9 bdrbtm" colspan="2" align="center" valign="top" >
							<?php if(file_exists($conjuncImg)){ ?>
							<img src="<?php echo($conjuncImg);?>" height="120" width="220"/>
							<?php } ?>
							</td>
						</tr>
						<!-- <tr>
							<td class="text_9" colspan="3" align="center" valign="top"><img	src='<?php echo($conjuncImg);?>' height="140" width="459"/></td>
						</tr>
						<tr>
							<td class="text_9" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr> -->
					<?php
					 } else if($objDrw->isAppletModified($conjunctiva_od_drawing) || $objDrw->isAppletModified($conjunctiva_os_drawing)){

					?>
						<tr>
							<td width="50" align="left" valign="top" class="text_9"><strong>Drawing</strong></td>
							<td class="text_9" align="center" valign="top" width="300">
								<?php
									if($objDrw->isAppletModified($conjunctiva_od_drawing)){
										//Sle
										$idSle = $sle_id;
										$tableSle = 'chart_drawings';//'chart_slit_lamp_exam'.$sle_id_arc;
										$idNameSle = 'id';
										$pixelSleConjunctivaOd = 'exm_drawing';
										$imageSle = realpath(dirname(__FILE__).'/../../images/pic_con_od.jpg');
										$altSle = 'SLE'; 						
										getAppletImageOcular($idSle,$tableSle,$idNameSle,$pixelSleConjunctivaOd,$imageSle,$altSle,"1");
										$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
										if(file_exists($gdFilename)){ 
										echo('<img src="'.$gdFilename.'" height="140" width="220"/>');	
										 } 
										$ChartNoteImagesString[]=$gdFilename;					 									
										}					
								?>
							</td>
							<td class="text_9" align="center" valign="top" width="300">
								<?php
									if($objDrw->isAppletModified($conjunctiva_os_drawing)){
										$idSle = $sle_id ;
										$tableSle = 'chart_drawings';//'chart_slit_lamp_exam'.$sle_id_arc;
										$idNameSle = 'id';
										$pixelSleConjunctivaOs = 'exm_drawing';
										$imageSle = realpath(dirname(__FILE__).'/../../images/pic_con_os.jpg');
										$altSle = 'SLE'; 
										getAppletImageOcular($idSle,$tableSle,$idNameSle,$pixelSleConjunctivaOs,$imageSle,$altSle,"1");
										$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
										if(file_exists($gdFilename)){
											echo('<img src="'.$gdFilename.'" height="140" width="220" />');
										}
										$ChartNoteImagesString[]=$gdFilename;					 
									}
								?>
							</td>
						</tr>
						<?php
					}
					if($objDrw->isAppletModified($lens_od_drawing) || $objDrw->isAppletModified($lens_os_drawing)){
						?>
						<tr>
							<td>&nbsp;</td>
							<td class="text_9" align="center" valign="top" width="300">
								<?php
									if($objDrw->isAppletModified($lens_od_drawing)){
										$idSle = $idSle;
										$tableSle = 'chart_drawings';//'chart_slit_lamp_exam'.$sle_id_arc;
										$idNameSle = 'id';
										$pixelSleLensOd = 'exm_drawing';
										$imageSle = realpath(dirname(__FILE__).'/../../images/sleLensOd.jpg');
										$altSle = 'SLE'; 
										getAppletImageOcular($idSle,$tableSle,$idNameSle,$pixelSleLensOd,$imageSle,$altSle,"1");
										$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
										if(file_exists($gdFilename)){
										echo('<img src="'.$gdFilename.'" height="105" width="150"/>');
										}
										$ChartNoteImagesString[]=$gdFilename;					 
									}					
								?>
							</td>
							<td class="text_9" align="center" valign="top" width="300">
								<?php
									if($objDrw->isAppletModified($lens_os_drawing)){
										$idSle = $idSle;
										$tableSle = 'chart_drawings';//'chart_slit_lamp_exam'.$sle_id_arc;
										$idNameSle = 'id';
										$pixelSleLensOs = 'exm_drawing';
										$imageSle = realpath(dirname(__FILE__).'/../../images/sleLensOs.jpg');
										$altSle = 'SLE'; 
										getAppletImageOcular($idSle,$tableSle,$idNameSle,$pixelSleLensOs,$imageSle,$altSle,"1");
										$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
										if(file_exists($gdFilename)){
										echo('<img src="'.$gdFilename.'" height="105" width="150"/>');
										}
										$ChartNoteImagesString[]=$gdFilename;					 
									}
								?>
							</td>
						</tr>
						<?php
					}
					if($descSle){
					?>
					<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
						<tr>
							<td class="text_9" align="left" valign="top" width="50">Comments :</td>
							<td colspan="2" class="text_9" align="left"><?php if($descSle) echo $descSle; else echo '&nbsp;'; ?></td>
						</tr>
						<tr>
							<td class="text_9" align="left" valign="top" width="50">&nbsp;</td>
							<td colspan="2" class="text_9" align="left" valign="top" ></td>
						</tr>
					<!-- </table> -->
					<?php
					} ?>
				</table>
			<?php
		 //} 
	 }  
	if((($od_text || $os_text || $cdr_od_summary || $cdr_os_summary) || ($optic_nerve_od_summary || $optic_nerve_os_summary || $wnl_value_Optic) || ($vitreous_od_summary || $vitreous_os_summary) || ($macula_od_summary || $macula_os_summary) || ($periphery_od_summary || $periphery_os_summary) || ($blood_vessels_od_summary || $blood_vessels_od_summary) || ($od_drawing || $os_drawing) || ($wnlVitreousOd || $wnlVitreousOs) || ($wnlMaculaOd || $wnlMaculaOs) || ($wnlPeriOd || $wnlPeriOs) || ($wnlBVOd || $wnlBVOs)  || ($retinal_od_summary || $retinal_os_summary))){
		?>
		
				<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td style="width:740px;" colspan="3" valign="middle" class="text_10b tb_heading"><b>Fundus Exam<!-- Retina &amp; Vitreous --></b></td>
					</tr>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
						<td class="text_9 bdrbtm" width="300" align="left"><?php odLable();?></td>
						<td class="text_9 bdrbtm" width="300" align="left"><?php osLable();?></td>
					</tr>
					<?php
					$od_text_val = $os_text_val = "";
					if($od_text || $os_text || $cdr_od_summary || $cdr_os_summary){
						$od_text_val = ($od_text ? $od_text : $cdr_od_summary);
						$os_text_val = ($os_text ? $os_text : $cdr_os_summary);
						?>
						<tr>
							<td class="text_9" align="left" valign="top" width="50"><b>C:D</b></td>
							<td class="text_9" align="left" valign="top" width="300"><?php if($od_text_val) echo $od_text_val; else echo '&nbsp;'; ?></td>
							<td class="text_9" align="left" valign="top" width="300"><?php if($os_text_val) echo $os_text_val; else echo '&nbsp;'; ?></td>
						</tr>
						<?php
					}
					if($optic_nerve_od_summary || $optic_nerve_os_summary || $wnlOpticOd || $wnlOpticOs){
						?>
						<tr>
							<td class="text_9" align="left" valign="top" width="50"><b>Opt.Nev</b></td>
							<td class="text_9" align="left" valign="top" width="300"><?php if($optic_nerve_od_summary) {echo $optic_nerve_od_summary; }elseif($wnlOpticOd==1){ echo $wnl_value_Optic; }else {echo '&nbsp;';} ?></td>
							<td class="text_9" align="left" valign="top" width="300"><?php if($optic_nerve_os_summary) {echo $optic_nerve_os_summary; }elseif($wnlOpticOs==1){ echo $wnl_value_Optic; }else {echo '&nbsp;';} ?></td>
						</tr>
						<?php
					}
					
					
					if(($vitreous_od_summary || $vitreous_os_summary) || ($wnlVitreousOd || $wnlVitreousOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Vitreous</b></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($vitreous_od_summary) echo $vitreous_od_summary; elseif($wnlVitreousOd==1)echo 'Clear';?></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($vitreous_os_summary) echo $vitreous_os_summary; elseif($wnlVitreousOs==1)echo 'Clear'; ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($retinal_od_summary || $retinal_os_summary) || ($wnlRetinalOd || $wnlRetinalOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Retinal Exam</b></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($retinal_od_summary) echo $retinal_od_summary; elseif($wnlRetinalOd==1)echo $wnl_value_RetinalExam;?></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($retinal_os_summary) echo $retinal_os_summary; elseif($wnlRetinalOs==1)echo $wnl_value_RetinalExam; ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($macula_od_summary || $macula_os_summary) || ($wnlMaculaOd || $wnlMaculaOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Macula</b></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($macula_od_summary) echo $macula_od_summary; elseif($wnlMaculaOd==1) echo 'Macula Benign'; ?></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($macula_os_summary) echo $macula_os_summary; elseif($wnlMaculaOs==1) echo 'Macula Benign'; ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($periphery_od_summary || $periphery_os_summary) || ($wnlPeriOd || $wnlPeriOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Periphery</b></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($periphery_od_summary) echo $periphery_od_summary; elseif($wnlPeriOd==1) echo 'WNL'; ?></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php  if($periphery_os_summary) echo $periphery_os_summary; elseif($wnlPeriOs==1) echo 'WNL';  ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
						<?php
					}
					if(($blood_vessels_od_summary || $blood_vessels_os_summary) || ($wnlBVOd || $wnlBVOs)){
						?>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50"><b>Blood Vessels</b></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($blood_vessels_od_summary) echo $blood_vessels_od_summary; elseif($wnlBVOd==1) echo 'Blood Vessels Benign'; ?></td>
							<td class="text_9 bdrbtm" width="300" align="left"><?php if($blood_vessels_os_summary) echo $blood_vessels_os_summary; elseif($wnlBVOs==1) echo 'Blood Vessels Benign'; ?></td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9 bdrbtm" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
						<?php
					}
					
					$rv_drawingImageExist=false;					
					
					if(!empty($idoc_drawing_id_rv) && $rv_drawingImageExist==false){
						$rv_idDrwingImage =$objCpr->getIDOCdrawingsBySection($sectionName='Fundus_Exam',$primaryId=$rv_id);
						//exit("<br/>cc2--".$rv_idDrwingImage);
						if($rv_idDrwingImage!="" && file_exists($rv_idDrwingImage)  && is_file($rv_idDrwingImage) )
						{
							$rv_idDrwingImage=str_replace("\\","/",$rv_idDrwingImage);		
							$rv_drawingImageExist=true;
							?>
							<tr>
								<td style="width:740px;" colspan="3" >
									<img src="<?php echo($rv_idDrwingImage);?>" class="imgdraw" height="200" width="343"  />
								</td>
							</tr>			
					<?php 	
						}
						
					}
					
					if($rv_drawingImageExist!=true && ($objDrw->isAppletModified($od_drawing) || $objDrw->isAppletModified($os_drawing))){
					if (strpos($od_drawing,"/") !== false ){
						$MMlaImg=SaveAndShowNewAppletImageOcular($od_drawing);
						$MMlaImg= $updir.$MMlaImg;
						$ChartNoteImagesString[]=$MMlaImg;
						?>						
						<tr>
							<td style="width:740px;" class="text_9" colspan="3" align="center" valign="top" >
							<?php if(file_exists($MMlaImg)){ ?>
							<img src="<?php echo($MMlaImg);?>" height="200" width="343"/>
							<?php } ?>
							</td>
						</tr>
						<tr>
							<td style="width:740px;" class="text_9" align="left" valign="top" colspan="3" height="5px">&nbsp;</td>
						</tr>
					<?php }
						else{?>
						<tr>
							<td width="50" class="text_9" valign="top"><b>Drawing</b></td>
							<td align="center" width="300">
							<?php
							if($objDrw->isAppletModified($od_drawing,"2550-:;")){
								if($drawingId){ $rv_id = $drawingId;}
								$tableRv = 'chart_drawings';//'chart_rv'.$rv_id_arc;
								$idNameRv = 'id';
								$pixelRvOd = 'exm_drawing';
								$imageRv = realpath(dirname(__FILE__).'/../../images/LeftEyeOpticNerve.jpg');
								$altRv = 'RV'; 
								getAppletImageOcular($rv_id,$tableRv,$idNameRv,$pixelRvOd,$imageRv,$altRv,"1");
								$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
								if(file_exists($gdFilename)){
								echo('<img src="'.$gdFilename.'" height="100" width="133" />');
								}
								$ChartNoteImagesString[]=$gdFilename;					 
							}
							?>
							</td>
							<td align="center" width="300">
							<?php
							if($objDrw->isAppletModified($os_drawing,"2550-:;")){
								if($drawingId){ $rv_id = $drawingId;}
								$tableRv = 'chart_drawings';//'chart_rv'.$rv_id_arc;
								$idNameRv = 'id';
								$pixelRvOs = 'exm_drawing';
								$imageRv = realpath(dirname(__FILE__).'/../../images/RightEyeOpticNerve.jpg');
								$altRv = 'RV'; 
								getAppletImageOcular($rv_id,$tableRv,$idNameRv,$pixelRvOs,$imageRv,$altRv,"1");
								
								$gdFilename = realpath(dirname(__FILE__).'/../main/html2pdfprint/'.$gdFilename);
								
								if(file_exists($gdFilename)){
								echo('<img src="'.$gdFilename.'" height="100" width="133" />');
								}
								$ChartNoteImagesString[]=$gdFilename;					 
							}
						}//end of else
							?>
							</td>
						</tr>
						<?php
					}
					if($descRv){
					?>
					<!-- <table style="width:740px;" border="0" cellspacing="0" cellpadding="0" > -->
						<tr>
							<td class="text_9" align="left" valign="top" width="50">Comments :</td>
							<td colspan="2" class="text_9" align="left"><?php if($descRv) echo $descRv; else echo '&nbsp;'; ?></td>
						</tr>
						<tr>
							<td class="text_9" align="left" valign="top" width="50">&nbsp;</td>
							<td colspan="2" class="text_9" align="left" valign="top" ></td>
						</tr>
					<!-- </table> -->
					<?php
					} 
					?>
				</table>
			
		<?php
	}
	if($assessmentPlanPrint == true){
		

		$sqlAssessQry = imw_query("SELECT * FROM chart_assessment_plans WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ");
		if(imw_num_rows($sqlAssessQry)>0){
			$sqlAssessRows = imw_fetch_array($sqlAssessQry);	
			extract($sqlAssessRows);	
			//echo '<pre>';
			//print_r($sqlAssessRows);
			//die;
				$idPlan = $id;
				$strpixls = $sign_coords;		
				$ta_plan_notes_4 = $assess_plan;
				$txt_assessment_4 = $assessment_4;
				$strXml = stripslashes($assess_plan);
				//--- Set Assess Plan Resolve NE Value FROM xml//
				$oChartApXml = new ChartApXml($patient_id,$form_id);

				$arrApVals = $oChartApXml->getVal_Str($strXml);
				$arrAp = $arrApVals["data"]["ap"];
				//print_r($arrAp);
				$lenAssess = count($arrAp);
				
				$sqlAssessPtQry = "SELECT * FROM chart_pt_assessment_plans WHERE id_chart_ap = '".$idPlan."' AND delete_by = '0' AND not_examin = '0' ";
				$resAssessPtQry = imw_query($sqlAssessPtQry);
				$numRowPtAssess = imw_num_rows($resAssessPtQry);
				if($numRowPtAssess>0 || $lenAssess>0){
				?>
                    <table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
                            <tr>
                                <td style="width:740px;" colspan="4" valign="middle" class="text_10b tb_heading" ><b>Assessment & Plans</b></td>
                            </tr>
                
					<?php
                    
                    $no_change_PtAssess = 0;
                    if($numRowPtAssess>0){
                        while($sqlAssessPtRows = imw_fetch_array($resAssessPtQry)) {
                            $no_change_PtAssess++;
                        ?>
                          <!--  <tr>
                                <td style="width:740px;" colspan="4" class="text_9" align="left" valign="top">&nbsp;</td>
                            </tr> -->
                            <tr>
                                <td style="width:20px;" class="text_9 bdrbtm bdrright" align="left" valign="top" width="20"><?php echo $no_change_PtAssess; ?></td>
                                <td style="width:230px;" class="text_9 bdrbtm bdrright"  align="left" valign="top"><?php if($sqlAssessPtRows["assessment"]) echo nl2br($sqlAssessPtRows["assessment"]); else echo '&nbsp;'; ?></td>
								<td style="width:40px;" class="text_9 bdrbtm bdrright"  align="left" valign="top"><?php if($sqlAssessPtRows["eye"]) echo nl2br($sqlAssessPtRows["eye"]); else echo '&nbsp;'; ?></td>
                                <td style="width:400px;" class="text_9 bdrbtm " align="left" valign="top"><?php if($sqlAssessPtRows["plan"]) echo nl2br(wordwrap($sqlAssessPtRows["plan"],55,"\n",1)); else echo '&nbsp;'; ?></td>
                            </tr>
                        <?php		
                        }
                    }elseif($lenAssess>0){
						for($i=0;$i<$lenAssess;$i++){
							$j=$i+1;	
							 $elem_assessment = "assessment_".$j;
							 $$elem_assessment = $arrAp[$i]["assessment"];
							$elem_plan = "plan_notes_".$j;
							$$elem_plan = $arrAp[$i]["plan"];
							$elem_resolve = "elem_resolve".$j;
							 $$elem_resolve = $arrAp[$i]["resolve"];
							$no_change_Assess = "no_change_Assess".$j;
							 $$no_change_Assess = $arrAp[$i]["ne"];
							if($$no_change_Assess) $$no_change_Assess = '<b>NE '.$j.'</b>';
								if($$elem_resolve){
									if($$no_change_Assess){
										$$no_change_Assess.= ', <b>RES '.$j.'</b>';
									}else{
										$$no_change_Assess = '<b>RES '.$j.'</b>';
										}
								}
								if(!$$no_change_Assess){
									$$no_change_Assess = '<b>'.$j.'</b>';
								}
								//die($$elem_plan);
							if(strpos(trim($$no_change_Assess),"NE") === false){	
								if($$elem_assessment || $$elem_plan ){
									?>	
									<!--	<tr>
											<td style="width:740px;" colspan="4" class="text_9 bdrbtm" align="left" valign="top">&nbsp;</td>
										</tr> -->
										<tr>
											<td style="width:20px;" class="text_9 bdrbtm bdrright" align="left" valign="top" width="20"><?php echo $$no_change_Assess; ?></td>
											<td style="width:230px;" class="text_9 bdrbtm bdrright"  align="left" valign="top"><?php if($$elem_assessment) echo nl2br($$elem_assessment); else echo '&nbsp;'; ?></td>
											<td style="width:40px;" class="text_9 bdrbtm bdrright"  align="left" valign="top">&nbsp;</td>
                                            <td style="width:400px;" class="text_9 bdrbtm bdrright" align="left" valign="top"><?php if($$elem_plan) echo nl2br(wordwrap($$elem_plan,55,"\n",1)); else echo '&nbsp;'; ?></td>
										</tr>
									<?php
								}
							}
						}
					} 
					?>
					</table>
			<?php
				}
            //--- Set Assess Plan Resolve NE Value FROM xml//
			?>
			<!--</table>-->
			<?php
			}
		}
////////////////////End Assesment And Plans/////////////////////////	 

		if($follow_up_numeric_value){			
			$follow_up_numeric_value = str_replace(array("13","14"), array("PRN","PMD"),$follow_up_numeric_value);
		}elseif($followup){
			$arrFollowup = $objFu->fu_getXmlValsArr($followup);
			//echo '<pre>';
			//echo count($arrFollowup[1]);
			//print_r($arrFollowup[1]);
			//die;
			$FUDatta = "";
			for($a=0;$a<count($arrFollowup[1]);$a++){
				if($a==0){$FUDatta .= "<table class='border' style='width:740px;' cellpadding='0' cellspacing='0'>";}
				 
				$FUDatta .= "<tr>";
				if($a==0){
					$FUDatta.="<td width='30' ><b>F/U </b></td>";
				}
				else{
					$FUDatta.="<td >&nbsp;</td>";
				}
				$number= $arrFollowup[1][$a]['number'];				
				$FUDatta.="<td >".$number."</td>";
	
				$time= $arrFollowup[1][$a]['time'];
				$FUDatta.='<td >'.$time.'</td>';
				
				$visit_type= $arrFollowup[1][$a]['visit_type'];
				$FUDatta.='<td >'.$visit_type.'</td>';
				
				$FUDatta .= "</tr>";
			}
			if(!empty($FUDatta))$FUDatta.="</table>";
			
		}
		
			?>							
				
			<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
				<?php if($follow_up_numeric_value || $FUDatta){  ?>
				<tr>
					<td class="text_10b tb_heading bdrbtm" style="width:740px;" valign="middle"  ><b>Follow Up</b></td>
				</tr>			
				<?php if($follow_up_numeric_value){
				?>					
					<tr>
						
						<td class="text_9 bdrbtm" align="left"  valign="top"><?php if($follow_up_numeric_value) echo 'F/U '.$follow_up_numeric_value.' - '.$follow_up.' - '.$followUpVistType;  ?></td>
					</tr>
				<?php
				}elseif($followup){
					echo "<tr>
					<td class=\"text_9 \" align=\"left\"  valign=\"top\">".
					$FUDatta.
					"</td>
					</tr>";					
				}
				}
				 //cosigner signature
				 if($objDrw->isAppletModified($sign_coordsCosigner)){
				 ?>		<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" >&nbsp;</td>
						</tr>
						<tr>
							<td align="left" class="text_9 bdrbtm" valign="top">
							<?php
								if($objDrw->isAppletModified($sign_coordsCosigner)){ 
									//Plan
									$idPlan = $idPlan;
									$tablePlan = 'chart_assessment_plans';
									$idNamePlan = 'id';
									$pixelPlan = 'sign_coordsCosigner';
									$imagePlan = dirname(__FILE__).'/../../images/white.jpg';
									$altPlan = 'Cosigner Sign'; 
									if(getAppletImageOcular($idPlan,"chart_assessment_plans","id","sign_coordsCosigner","","","1")){
										
										$gdFilename = realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
										
										if(file_exists($gdFilename)){													
										echo "<img src=\"".$gdFilename."\" height=\"41\" width=\"225\"/>";
											$ChartNoteImagesString[]=$gdFilename;
										}
									}																		
								 } 
							?>
							</td>										
						</tr>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" ><strong>Cosigner Signature:</strong></td>
						</tr>
						<tr>
							<td class="text_9 bdrbtm" >
								<b><?php echo (showDoctorName($cosigner_id)<>"")?"Cosigner: ".showDoctorName($cosigner_id):""; ?></b>
							</td>
						</tr>
					
						<?php
						}
					//echo"sssf";die();	
		?>
					
				<?php
				//if($retina == 1 || $neuro_ophth == 1 || $id_precation == 1 || $rd_precation == 1 || $lid_scrubs_oint == 1 || $doctor_name != ''){
					if($retina) $retina = 'Retina';									
					if($neuro_ophth){
						if($retina) 
							$retina.=', Neuro Ophth';
						else
							$retina = 'Neuro Ophth';
					}
					if($id_precation){
						if($retina) $retina.=', ID Precation';
						else $retina.='ID Precation';
					}
					if($rd_precation){ 										
						if($retina) $retina.=', RD Precation';
						else $retina.='RD Precation';
					}
					if($lid_scrubs_oint){
						if($retina) $retina.=', Lid Scrubs & Oint';
						else $retina.='Lid Scrubs & Oint';
					}
					
					if($continue_meds){
						if($retina) $retina.=', Continue Meds';
						else $retina.='Continue Meds';
					}
					
					if($doctor_name != ''){
						$doctor_name = '<b>Refer for consult: </b>'.$doctor_name;
					}
					?>
					<tr>
						<?php 
						//echo substr($plan_notes, 0, 9);exit;
							if(substr($plan_notes, 0, 9) == "Comments:"){
								$plan_notes = substr($plan_notes,9);						
								if($plan_notes!=""){
									$plan_notes = "Comments:<b>".$plan_notes."</b>";
								}
							}
							else{
								if($plan_notes!=""){
									$plan_notes = "Comments:<b>".$plan_notes."</b>";
								}
							}
						?>
						<td class="text_9 bdrbtm" align="left" valign="top"> <?php if($retina)echo $retina ?></td>
					</tr>	
					<?php if($plan_notes){ ?>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top">
							<?php echo $plan_notes;  ?>
						</td>
					</tr>	
					<?php }
					if($doctor_name){
					?>
					<tr>
						<td class="text_9 bdrbtm" align="left" valign="top">
							<?php echo $doctor_name;  ?>
						</td>
					</tr>	
					<?php }
						$sqlgetScr = imw_query("SELECT CONCAT_WS(',',fname,lname) as ScribedBy FROM users WHERE id = '".$scribedBy."'");
						if(imw_num_rows($sqlgetScr)>0){
							$sqlgetScrRows = imw_fetch_assoc($sqlgetScr);	
							extract($sqlgetScrRows);	
					?>
					<tr>
						<td class="text_9 bdrbtm"  align="left" valign="top">
							<?php echo "Scribed By:". $ScribedBy; ?>	
						</td>
					</tr>
						<?php } ?>
				</table>								
					<?php
				//}
			//}
			
	}	
	
	
	if($summaryOptionSelected==true){
	
	//do not include
	}else{		
	
	//include(dirname(__FILE__).'/amsler_print_pdf.php');
	include(dirname(__FILE__)."/optha_pdf_print.php");
	include(dirname(__FILE__)."/leftForms_pdf_print.php");
	
	}

if($objDrw->isAppletModified($sign_coords)){
								?>
    <table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
        <tr>
            <td class="text_9" align="left" valign="top" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" class="text_9" colspan="3" valign="top">
            
            <?php
                
                if($objDrw->isAppletModified($strpixls)){ 
                    //Plan
                    $idPlan = $idPlan;
                    $tablePlan = 'chart_assessment_plans';
                    $idNamePlan = 'id';
                    $pixelPlan = 'sign_coords';
                    $imagePlan = dirname(__FILE__).'/../../images/white.jpg';
                    $altPlan = 'Doctor Sign'; 
                    if(getAppletImageOcular($idPlan,"chart_assessment_plans","id","sign_coords","","","1")){
                        
                        $gdFilename = realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
                                    
                        if(file_exists($gdFilename)){													
                        echo "<strong>Signature:</strong>
                        <img src=\"".$gdFilename."\" height=\"46\" width=\"225\"/>";
                        $ChartNoteImagesString[]=$gdFilename;
                        }

                    }
                    
                    /*
                    echo getAppletImageOcular($idPlan,$tablePlan,$idNamePlan,$pixelPlan,$imagePlan,$altPlan);
                    */
                 } 
            ?>
            </td>
            
        </tr>
        <tr>
            <td>
                <b><?php echo (showDoctorName($doctorId)<>"")?"Physician: ".showDoctorName($doctorId):""; ?></b>
            </td>
        </tr>
    </table>
    <?php
}
if(!empty($chartOcularSignPath) && file_exists(trim(substr(data_path(), 0, -1).$chartOcularSignPath))){
   ?>
    <table style="width:740px;" class="border" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:740px;" valign="middle" class="text_10b tb_heading" ><b>Signature</b></td>
        </tr>
        <tr>
            <td class="text_9" align="left" valign="top" ><img src="<?php echo trim(substr(data_path(), 0, -1).$chartOcularSignPath);?>" alt="sign" style="width:225px; height:60px;" ></td>
        </tr>
        <tr>
            <td class="text_9" align="left" valign="top" ><?php echo (showDoctorName($chartOcularProId)<>"")?"<b>Signer:</b> ".showDoctorName($chartOcularProId):""; ?></td>
        </tr>
        <tr>
            <td class="text_9" align="left" valign="top" ><?php echo (showDoctorName($chartOcularProId)<>"")?"<b>Signed on:</b> ".$chartOcularSignDateTime:""; ?></td>
        </tr>
   </table>
   <?php 
}		
		?>