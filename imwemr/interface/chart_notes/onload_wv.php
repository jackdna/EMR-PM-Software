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
File: onload_wv.php
Purpose: This file provides data in work view main summary sheet on load.
Access Type : Direct
*/
?>
<?php
//include(dirname(__FILE__)."/minfy_inc.php");
//onload_wv.php
require_once(dirname(__FILE__).'/../../config/globals.php');

//
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//

//--
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

//--

##
header('Content-Type: text/html; charset=utf-8');
##

//Start Page--

class Loader extends Patient{
	public $pid, $fid;

	public function __construct($pid, $fid){
		parent::__construct($pid);
		$this->fid = $fid;
		$this->pid = $pid;
	}

	public function getTestsSummery(){
		//Only Doctor can interpret tests--

		$ouser=new User();
		$is_doc = $ouser->getUType(1);
		$showInterpretBtn=0;
		if(!empty($is_doc)){
			$flgUserAdmnSignExist = $ouser->getSign($flgCheck="1");
			if(!empty($flgUserAdmnSignExist)){
				$showInterpretBtn=1;
			}
		}


		$echo="";
		//$echo .= "VF - GL";
		$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os, examDate, vf_gl_id, sign_path, phyName FROM vf_gl WHERE formId = '".$this->fid."' AND patientId = '".$this->pid."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, vf_gl_id DESC  ";
		$rez = sqlStatement($sql);
		$tst_ln = imw_num_rows($rez);
		$row =false;
		//
		$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os, examDate, vf_gl_id, sign_path, phyName FROM vf_gl WHERE patientId = '".$this->pid."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, vf_gl_id DESC  ";
		$rez2 = sqlStatement($sql);
		$tst_ln2 = imw_num_rows($rez2);

		if($tst_ln<=0){
			$rez=$rez2;
			$tst_ln = $tst_ln2;
		}
		if($tst_ln>0){
			$row = sqlFetchArray($rez);
		}
		//
		//$showPriorSyn=" style=\"visibility:hidden;\" ";
		$showPriorSyn= " hidden "; //" style=\"display:none;\" ";
		if($tst_ln2>1){$showPriorSyn="";}
		$sign_path=$phyName="";
		if($row!=false){
			$descOd = trim($row["descOd"]);
			$descOs = trim($row["descOs"]);

			//$descOd = trim($row["synthesis_od"]);
			//$descOs = trim($row["synthesis_os"]);
			$dateofExam=wv_formatDate($row["examDate"]);
			$vf_gl_id=$row["vf_gl_id"];
			$sign_path=$row["sign_path"];
			$phyName=$row["phyName"];

		}

		if($tst_ln>0){
		if(!empty($descOd) || !empty($descOs)){

			//make bold labels;
			$arC=array("Reliability :", "MD :","MD:", "PSD :","VFI :", "VFI:", "Details:", "Nasal Step:", "Arcuate:", "Hemifield:", "Paracentral:", "Into Fixation:", "Central Island:", "Stage:","Reliab:","Interpret:","Comments:","Artifact:","Hemianopsis:", "Quadranopsia:","Congruity:","Nonspecific:" );
			$arR=array("<b>Reliability :</b>", "<b>MD :</b>","<b>MD:</b>", "<b>PSD :</b>","<b>VFI :</b>", "<b>VFI:</b>", "<b>Details:</b>", "<b>Nasal Step:</b>", "<b>Arcuate:</b>", "<b>Hemifield:</b>","<b>Paracentral:</b>", "<b>Into Fixation:</b>", "<b>Central Island:</b>", "<b>Stage:</b>","<b>Reliab:</b>","<b>Interpret:</b>","<b>Comments:</b>","<b>Artifact:</b>","<b>Hemianopsis:</b>", "<b>Quadranopsia:</b>","<b>Congruity:</b>","<b>Nonspecific:</b>" );
			$descOd=str_replace($arC,$arR,$descOd);
			$descOs=str_replace($arC,$arR,$descOs);
		}
			$rowspan=1;
			$enm_var = $enm = "VF-GL";

			// if already interpret then no show btn
			$showInterpretBtn_style="";
			if((!empty($sign_path) && !empty($phyName))||empty($showInterpretBtn)){	$showInterpretBtn_style=" style=\"display:none;\" "; }

			$str = "";
			$str .="<div id=\"dv_vf_gl\" class=\"esum\">".
					"<table class=\"table table-bordered table-hover table-striped\">";

			$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc\">";
			//$strDesc .="<div></div>";//Used to hgt cell
			$strDesc .="</td>";
			//$strDesc="";//stopped

			$exm_header = "<div class=\"einfo\">".
						"<div class=\"hdr \">".
						"<label onclick=\"openPW('".$enm_var."','".$vf_gl_id."')\">".$enm."</label>".
						"<label class=\"pull-right\">".$dateofExam."</label>";
			$exm_header .= "</div>";
			$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GL_tsttab\" value=\"Test Tab\" onclick=\"go_test_tab()\" class=\"btn testtabbut\" >";
			$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GLPrvSyn\" value=\"Prv Synthesis\" onclick=\"showPrvSynthesis('VF-GL','".$vf_gl_id."')\" class=\"btn testtabbut ".$showPriorSyn."\"  > ";
			$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GLRwd\" value=\"Interpret\" onclick=\"tests_interpret('VF-GL','".$vf_gl_id."')\" class=\"btn testtabbut\" ".$showInterpretBtn_style." >";
			$exm_header .= "</div>";

			$str .= "<tr>";
			$str .= "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
			$str .= "<td class=\"sumod bgWhite tablnk\">".nl2br($descOd)."</td>";
			$str .= "<td class=\"sumos bgWhite tablnk\">".nl2br($descOs)."</td>";
			$str .= "".$strDesc;
			$str .= "</tr>";

			$str .= "</table>";
			$str .= "</div>";
			$echo.=$str;
		}

		$descOd=$descOs="";

		//$echo .= "OCT - RNFL";
		$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os,examDate,oct_rnfl_id, sign_path, phyName FROM oct_rnfl WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, oct_rnfl_id DESC ";
		$rez = sqlStatement($sql);
		$tst_ln = imw_num_rows($rez);
		$row =false;
		//
		$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os,examDate,oct_rnfl_id, sign_path, phyName FROM oct_rnfl WHERE patient_id = '".$this->pid."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, oct_rnfl_id DESC  ";
		$rez2 = sqlStatement($sql);
		$tst_ln2 = imw_num_rows($rez2);
		if($tst_ln<=0){
			$rez=$rez2;
			$tst_ln = $tst_ln2;
		}
		if($tst_ln>0){
			$row = sqlFetchArray($rez);
		}
		//
		//$showPriorSyn=" style=\"visibility:hidden;\" ";
		$showPriorSyn= " hidden "; //" style=\"display:none;\" ";
		if($tst_ln2>1){$showPriorSyn="";}
		$sign_path=$phyName="";
		if($row!=false){
			$descOd = trim($row["descOd"]);
			$descOs = trim($row["descOs"]);

			//$descOd = trim($row["synthesis_od"]);
			//$descOs = trim($row["synthesis_os"]);
			$dateofExam=wv_formatDate($row["examDate"]);
			$oct_rnfl_id=$row["oct_rnfl_id"];
			$sign_path=$row["sign_path"];
			$phyName=$row["phyName"];

		}

		if($tst_ln>0){
		if(!empty($descOd) || !empty($descOs)){

			//make bold labels;
			$arC=array("Reliability :","Signal Strength", "Quality :", "Details :", "Disc area :", "Disc size :", "RNFL :", "Disc edema :", "Overall :", "Superior :", "Inferior :", "Temporal :", "Symmetric :", "Comments :", "Vertical C:D :","Nasal :","GCC :","GPA :", "Synthesis :");
			$arR=array("<b>Reliability :</b>", "<b>Signal Strength</b>", "<b>Quality :</b>", "<b>Details :</b>", "<b>Disc area :</b>", "<b>Disc size :</b>", "<b>RNFL :</b>", "<b>Disc edema :</b>", "<b>Overall :</b>", "<b>Superior :</b>", "<b>Inferior :</b>", "<b>Temporal:</b>", "<b>Symmetric :</b>", "<b>Comments:</b>","<b>Vertical C:D :</b>","<b>Nasal :</b>","<b>GCC :</b>","<b>GPA :</b>", "<b>Synthesis :</b>" );
			$descOd=str_replace($arC,$arR,$descOd);
			$descOs=str_replace($arC,$arR,$descOs);
		}
			$rowspan=1;
			$enm_var = $enm = "OCT - RNFL";

			// if already interpret then no show btn
			$showInterpretBtn_style="";
			if((!empty($sign_path) && !empty($phyName))||empty($showInterpretBtn)){	$showInterpretBtn_style=" style=\"display:none;\" "; }

			$str = "";
			$str .="<div id=\"dv_oct_rnfl\" class=\"esum\">".
					"<table class=\"table table-bordered table-hover table-striped\">";

			$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc\">";
			//$strDesc .="<div></div>";//Used to hgt cell
			$strDesc .="</td>";
			//$strDesc="";//stopped

			$exm_header = "<div class=\"einfo\">".
						"<div class=\"hdr\">".
						"<label onclick=\"openPW('".$enm_var."','".$oct_rnfl_id."')\">".$enm."</label>".
						"<label class=\"pull-right\">".$dateofExam."</label>";
			$exm_header .= "</div>";
			$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFL_tsttab\" value=\"Test Tab\" onclick=\"go_test_tab()\" class=\"btn testtabbut\" >";
			$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFLPrvSyn\" value=\"Prv Synthesis\" onclick=\"showPrvSynthesis('OCT-RNFL','".$oct_rnfl_id."')\" class=\"btn testtabbut ".$showPriorSyn."\"  > ";
			$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFLRwd\" value=\"Interpret\" onclick=\"tests_interpret('OCT-RNFL','".$oct_rnfl_id."')\" class=\"btn testtabbut\" ".$showInterpretBtn_style." >";
			$exm_header .= "</div>";

			$str .= "<tr>";
			$str .= "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
			$str .= "<td class=\"sumod bgWhite tablnk\">".nl2br($descOd)."</td>";
			$str .= "<td class=\"sumos bgWhite tablnk\">".nl2br($descOs)."</td>";
			$str .= "".$strDesc;
			$str .= "</tr>";

			$str .= "</table>";
			$str .= "</div>";
			$echo.=$str;
		}

		return $echo;


	}

	function getMrDoneDate(){
		$lastPtMR=$formIdMR=$chartStMR=$relNumMR=$curyrMR="";
		$cdt = date("Y-m-d");
		$sql = "SELECT
				c1.id AS form_id, c1.finalize, c1.releaseNumber,
				c2.status_elements,
				c3.exam_date AS dateVisMR,c3.ex_number,
				c4.sph AS sph_r,c4.cyl AS cyl_r,c4.axs AS axs_r,c4.ad AS ad_r,
				c5.sph AS sph_l,c5.cyl AS cyl_l,c5.axs AS axs_l,c5.ad AS ad_l
				FROM chart_master_table c1
				INNER JOIN chart_vis_master c2 ON c2.form_id = c1.id
				INNER JOIN chart_pc_mr c3 ON c3.id_chart_vis_master = c2.id AND c3.ex_type='MR'
				LEFT JOIN chart_pc_mr_values c4 ON c4.chart_pc_mr_id = c3.id AND c4.site='OD'
				LEFT JOIN chart_pc_mr_values c5 ON c5.chart_pc_mr_id = c3.id AND c5.site='OS'
				WHERE c1.patient_id='".$this->pid."'
				AND c1.delete_status='0'
				AND c1.purge_status='0'
				ORDER BY c1.date_of_service DESC, c1.id DESC
		";

		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez); $i++){

			$se = $row["status_elements"];
			$ad_r = trim($row["ad_r"]);
			if($ad_r=="20/"){$ad_r="";}
			$ad_l = trim($row["ad_l"]);
			if($ad_l=="20/"){$ad_l="";}

			$ex_num = $row["ex_number"];

			$s_othr=$s_sfx="";
			if($ex_num>1){
				$s_othr="Other";
				if($ex_num>2){
					$s_sfx="_".$ex_num;
				}
			}

			//|| (empty($se) && (!empty($row["sph_r"]) || !empty($row["sph_l"]) || !empty($row["cyl_r"]) || !empty($row["cyl_l"]) || !empty($row["axs_r"]) || !empty($row["axs_l"]) || !empty($ad_r) || !empty($ad_l)))
			if(  (!empty($se) &&
				 (
					(
						(!empty($row["sph_r"]) && (strpos($se, "elem_visMr".$s_othr."OdS".$s_sfx."=1") !== false)) || (!empty($row["cyl_r"]) && (strpos($se, "elem_visMr".$s_othr."OdC".$s_sfx."=1") !== false)) || (!empty($row["axs_r"]) && (strpos($se, "elem_visMr".$s_othr."OdA".$s_sfx."=1") !== false)) || (!empty($ad_r) && (strpos($se, "elem_visMr".$s_othr."OdAdd".$s_sfx."=1") !== false)) ||
						(!empty($row["sph_l"]) && (strpos($se, "elem_visMr".$s_othr."OsS".$s_sfx."=1") !== false)) || (!empty($row["cyl_l"]) && (strpos($se, "elem_visMr".$s_othr."OsC".$s_sfx."=1") !== false)) || (!empty($row["axs_l"]) && (strpos($se, "elem_visMr".$s_othr."OsA".$s_sfx."=1") !== false)) || (!empty($ad_l) && (strpos($se, "elem_visMr".$s_othr."OsAdd".$s_sfx."=1") !== false))
					)
				 )
				)
			){
				if(!empty($row["dateVisMR"]) && ( preg_replace("/[^0-9]/","",$row["dateVisMR"]) != "00000000" )){
					$lastPtMR = "MR (".wv_formatDate($row["dateVisMR"],1).")";
					$formIdMR = $row["form_id"];
					$chartStMR = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumMR = $row["releaseNumber"];
					$curyrMR = isDt12mOld($cdt, $row["dateVisMR"])?"0":"1";
					//(strpos($row["dateVisMR1"],$cyr) !== false) ? "1" : "0";
					break;
				}
			}
		}
		return array($lastPtMR, $formIdMR, $chartStMR, $relNumMR, $curyrMR);
	}

	function getLastDoneExamsInfo(){
		$lastPtCee=$lastPtGonio=$lastPtVF=$lastPtHRT = $lastPtMR = $lastPtDilation = "";
		$formIdCee=$formIdGonio=$formIdVF=$formIdHRT=$formIdMR = $formIdDilation = "";
		$cyr = "-".date("y");
		$cdt = date("Y-m-d");
		//global $oUtifun,$oCLSDrawingData;

		//CEE
		$sql = "SELECT ".
			//"chart_left_cc_history.date_of_service as dateCee, ".
			 //"DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dateCee1, ".
			 "chart_master_table.date_of_service as dateCee, ".
			 //"chart_master_table.date_of_service, '".getSqlDateFormat('','y')."') as dateCee1, ".
			 "chart_master_table.id AS form_id, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.ptVisit, ".
			 "chart_master_table.releaseNumber ".
			 "FROM chart_master_table ".
			 //"INNER JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.patient_id='".$this->pid."'  ".
			 "AND chart_master_table.delete_status='0' ".
			 "AND chart_master_table.purge_status='0' ".
			 "AND chart_master_table.ptVisit LIKE '%CEE%' ".
			 //"ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
			 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,DATE_FORMAT(chart_master_table.create_dt,'%Y-%m-%d')) DESC, chart_master_table.id DESC ".
			 "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
			 "LIMIT 0, 1";
		$row = sqlQuery($sql);
		if($row != false){
			if(!empty($row["dateCee"]) && (preg_replace("/[^0-9]/",'',$row["dateCee"]) != "00000000" )){
				$lastPtCee =  "CEE (".wv_formatDate($row["dateCee"],1).")";
				$formIdCee = $row["form_id"];
				$chartStCee = ($row["finalize"] == "1") ? "Final" : "Active";
				$relNumCee = $row["releaseNumber"];
				$curyrCee = isDt12mOld($cdt, $row["dateCee"])?"0":"1";
			}
		}

		//Gonio
		$sql = "SELECT ".
			 //"DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dosGonio, chart_left_cc_history.date_of_service, ".
			// "DATE_FORMAT(chart_master_table.date_of_service, '".getSqlDateFormat('','y')."') as dosGonio, ".
			 " chart_master_table.date_of_service, ".
			 //"DATE_FORMAT(chart_gonio.examDateGonio, '".getSqlDateFormat('','y')."') as dateGonio1, ".
			 "chart_gonio.examDateGonio, ".
			 //"DATE_FORMAT(c3.row_created_by, '%m-%d-%y') as dateDraw1, c3.row_created_by as dateDraw, ".
			 "chart_master_table.id AS form_id, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.releaseNumber ".
			 "FROM chart_master_table ".
			 //"LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "INNER JOIN chart_gonio ON chart_gonio.form_id = chart_master_table.id AND chart_gonio.purged='0'  ".
			 //"LEFT JOIN ".constant("IMEDIC_SCAN_DB").".idoc_drawing c3 ON c3.id = chart_gonio.idoc_drawing_id   ".
			 "WHERE chart_master_table.patient_id='".$this->pid."'  ".
			 "AND chart_master_table.delete_status='0' ".
			 "AND chart_master_table.purge_status='0' ".
			 //"AND (chart_gonio.gonio_od_summary != '' OR chart_gonio.gonio_od_summary != '' OR chart_gonio.idoc_drawing_id!=''  ) ".
			 //"AND chart_gonio.statusElem LIKE '%=1%'  ".
			 "AND (((chart_gonio.gonio_od_summary != '' OR chart_gonio.gonio_od_summary != '') AND (chart_gonio.statusElem LIKE '%Iop_Od=1%' OR chart_gonio.statusElem LIKE '%Iop_Os=1%') )	OR
				(chart_gonio.idoc_drawing_id!='' AND  (chart_gonio.statusElem LIKE '%Iop3_Od=1%' OR chart_gonio.statusElem LIKE '%Iop3_Os=1%')) ) ".
			 //"ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
			 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,DATE_FORMAT(chart_master_table.create_dt,'%Y-%m-%d')) DESC, chart_master_table.id DESC ".
			 "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
			 "LIMIT 0, 1";
		$row = sqlQuery($sql);
		if($row != false){
			$lastPtGonio = "";
			if( !empty($row["date_of_service"]) && ( $row["date_of_service"] != "0000-00-00" )){
				$lastPtGonio = "Gonio (".wv_formatDate($row["date_of_service"],1).")";
				$curyrGonio = isDt12mOld($cdt, $row["date_of_service"])?"0":"1";
				//(strpos($row["dosGonio"],$cyr) !== false) ? "1" : "0";
			}
			else if(!empty($row["examDateGonio"]) && ( preg_replace("/[^0-9]/","",$row["examDateGonio"]) != "00000000" )){
				$lastPtGonio = "Gonio (".wv_formatDate($row["examDateGonio"],1).")";
				$curyrGonio = isDt12mOld($cdt, $row["examDateGonio"])?"0":"1";
				//(strpos($row["dateGonio1"],$cyr) !== false) ? "1" : "0";
			}/*else if( !empty($row["dateDraw1"]) && ( $row["dateDraw1"] != "00-00-00" )){
				$lastPtGonio = "Gonio (".$row["dateDraw1"].")";
				$curyrGonio = $oUtifun->isDt12mOld($cdt, $row["dateDraw"])?"0":"1";
				//(strpos($row["dosGonio"],$cyr) !== false) ? "1" : "0";
			}*/

			if(!empty($lastPtGonio)){
				$formIdGonio = $row["form_id"];
				$chartStGonio = ($row["finalize"] == "1") ? "Final" : "Active";
				$relNumGonio = $row["releaseNumber"];
			}
		}

		//VF
		/*
		$sql = "SELECT ".
			 "DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dosVF, ".
			 "DATE_FORMAT(vf.examDate, '%m-%y') as dateVF, ".
			 "chart_master_table.id AS form_id, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.releaseNumber ".
			 "FROM chart_master_table ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "INNER JOIN vf ON vf.formId = chart_master_table.id ".
			 "WHERE chart_master_table.patient_id='".$patient_id."'  ".
			 "ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
			 "LIMIT 0, 1";

		$row = sqlQuery($sql);
		if($row != false){
			$lastPtVF = (!empty($row["dateVF"]) && ( $row["dateVF"] != "00-00" )) ? "VF (".$row["dateVF"].")" : "";
			$formIdVF = $row["form_id"];
			$chartStVF = ($row["finalize"] == "1") ? "Final" : "Active";
			$relNumVF = $row["releaseNumber"];
		}
		*/
		/*
		//HRT/OCT
		$sql = "SELECT ".
			 "DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dosNfa, ".
			 "DATE_FORMAT(nfa.examDate, '%m-%y') as dateNfa, ".
			 "chart_master_table.id AS form_id, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.releaseNumber ".
			 "FROM chart_master_table ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "INNER JOIN nfa ON nfa.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.patient_id='".$patient_id."'  ".
			 "ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
			 "LIMIT 0, 1";
		$row = sqlQuery($sql);
		if($row != false){
			$lastPtHRT = (!empty($row["dateNfa"]) && ( $row["dateNfa"] != "00-00" )) ? "HRT/OCT (".$row["dateNfa"].")" : "";
			$formIdHRT = $row["form_id"];
			$chartStHRT = ($row["finalize"] == "1") ? "Final" : "Active";
			$relNumHRT = $row["releaseNumber"];
		}
		*/

		//All Tests
		$oTests = new TestInfo();
		$arrTstNmShow = $oTests->get_tests_names_show();
		$flg_OCT=0;
		$arrTest = array("VF","VF-GL","HRT","OCTR","OCTON","OCTAS","OCT","OCT-RNFL","GDX","Pachy","IVFA","ICG","Fundus",
						"External/Anterior","Topography","Cell Count","A/Scan","IOL Master",
						"B-Scan","Labs","Other","TemplateTests","CustomTests");
		for($i=0;$i<23;$i++){
			$dtn = $tn = $arrTest[$i];
			$tmpName=$phrs="";
			$other_test_where_part=$other_test_join_part=$grpby_phrase="";

			if($dtn == "OCT"){
				$tn =$dtn="OCT";
				$tmpName = ", scanLaserOct";
				$arrName = array("ON","R","AS");
				$phrs =" AND scanLaserOct = '' ";

				if($flg_OCT==1){
					continue;
				}

			}else	if($dtn == "OCTR"){
				$tn =$dtn="OCT";
				$tmpName = ", scanLaserOct";
				$arrName = array("ON","R","AS");
				$phrs = " AND scanLaserOct = '2' ";
			}else if($dtn == "OCTON"){
				$tn =$dtn="OCT";
				$tmpName = ", scanLaserOct";
				$arrName = array("ON","R","AS");
				$phrs = " AND scanLaserOct = '1' ";
			}else if($dtn == "OCTAS"){
				$tn =$dtn="OCT";
				$tmpName = ", scanLaserOct";
				$arrName = array("ON","R","AS");
				$phrs = " AND scanLaserOct = '3' ";
			}else if($dtn == "External/Anterior"){
				$dtn = "Ext.";
				$tmpName = ", fundusDiscPhoto";
				$arrName =array("ES","ASP");
			}else if($dtn == "Topography"){
				$dtn = "Topo.";
			}else if($dtn == "Fundus"){
				$tmpName = ", fundusDiscPhoto";
				$arrName =array("DP","MP","RP");
			}else if($dtn == "Labs"){
				$tmpName = ", test_labs";
			}else if($dtn == "Other"){
				$tmpName = ", test_other";
				$other_test_where_part = " AND test_template_id=0 ";
			}else if($dtn == "TemplateTests"){
				$tmpName = ", tn.temp_name";
				$other_test_where_part = " AND test_template_id>0 ";
				$other_test_join_part = " JOIN tests_name tn ON(tn.id=test_other.test_template_id) ";
				//$grpby_phrase=" GROUP BY test_template_id ";
			}else if($dtn == "CustomTests"){
				$tmpName = ", tn.temp_name";
				$other_test_where_part = " AND test_template_id>0 ";
				$other_test_join_part = " JOIN tests_name tn ON(tn.id=test_custom_patient.test_template_id) ";
				//$grpby_phrase=" GROUP BY test_template_id ";
			}

			if(!empty($tmpName))$tmpName.=" AS testName ";

			//
			$arrF = $oTests->getTestFormFields($tn,1);

			$sql = "SELECT ".$arrF["keyId"].", ".
					//"DATE_FORMAT(".$arrF["eDt"].", '".getSqlDateFormat('','y')."') as dateT, 	".
					$arrF["eDt"]." AS dtchk ".
					$tmpName.
					"FROM ".$arrF["tbl"].$other_test_join_part."
					WHERE ".$arrF["ptId"]." = '".$this->pid."' ".
					$phrs.
					" AND ".$arrF["tbl"].".del_status='0' AND purged='0'".$other_test_where_part."".
					" ".$grpby_phrase."
					ORDER BY ".$arrF["eDt"]." DESC, ".$arrF["keyId"]." DESC ";

			if($dtn != "TemplateTests" && $dtn != "CustomTests"){
				$sql .= "LIMIT 0,1";
			}

			$dtn_org=$dtn;
			$arr_uni_Template_test=array();
			//$row = sqlQuery($sql);
			$rez = sqlStatement($sql);
			for($ii=1;$row=sqlFetchArray($rez);$ii++){
				if($row != false){
					if(!empty($row["dtchk"]) && (preg_replace("/[^0-9]/","",$row["dtchk"]) != "00000000")){
						$t_dtn = $arrTstNmShow[$arrF["tbl"]];
						if(!empty($t_dtn)){ $dtn = $t_dtn;  }
						if(!empty($tmpName)&&!empty($row["testName"])) {
							if($dtn_org == "Other"||$dtn_org == "Labs"||$dtn_org == "TemplateTests" || $dtn_org == "CustomTests"){
								$dtn = trim($row["testName"]);
								//Template test --
								if($dtn_org == "TemplateTests" || $dtn_org == "CustomTests"){
									if((is_array($arr_uni_Template_test[$dtn_org]) && !in_array($dtn, $arr_uni_Template_test[$dtn_org])) || !isset($arr_uni_Template_test[$dtn_org])){
										$arr_uni_Template_test[$dtn_org][]=$dtn;
									}else{
										continue; //multiple test template then show only one
									}
								}
								//Template test --

							}else{
								$dtn = $dtn ." - ". $arrName[$row["testName"]-1];
							}
						}

						$lastPt = $dtn." (".wv_formatDate($row["dtchk"],1).")";
						$tId = $row[$arrF["keyId"]];
						$chkdt = $row["dtchk"];

						if(!empty($lastPt) && !empty($tId)){
							$curyrtmp = isDt12mOld($cdt, $chkdt)?"0":"1";
							$atestDn[]=array($lastPt,$tId,$curyrtmp,$tn);

							//Check OCT
							if($tn=="OCT" && $flg_OCT==0){
								$flg_OCT=1;
							}
							//--
						}
					}
				}
			}//
		}

		//MR
		list($lastPtMR, $formIdMR, $chartStMR, $relNumMR, $curyrMR) = $this->getMrDoneDate();

		//Dilation
		list($lastPtDilation,$formIdDilation,$chartStDilation,$relNumDilation,$curyrDilation)=$this->getLastDoneDilation();

		//Opthamoscopy & Ext.Opthamoscopy
		// 17/05/12 : Dr. Silverman did the drawing and it did bill, however the date on the A&P bar for extended ophthalmoscopy did not update.
		$lastPtOptha=$lastPtExtOptha="";
		$sql= "SELECT ".
			"c1.id AS form_id, c1.finalize, c1.releaseNumber,  ".
			"c2.ophtha_id, c2.ophtha_os, c2.ophtha_od, c2.exam_date AS date_optha_full, ".
			//"DATE_FORMAT(c2.exam_date,'".getSqlDateFormat('','y')."') AS exmdate_op, ".
			"c3.id as rv_id, c3.exm_drawing as od_drawing, c3.idoc_drawing_id, c3.exam_date AS date_fundusExm_full, ".
			//"DATE_FORMAT(c3.exam_date,'".getSqlDateFormat('','y')."') AS exmdate_rv, ".
			"c5.pheny10, c5.pheny25, c5.mydiacyl1, c5.mydiacyl5, ".
			"c5.tropicanide, c5.cyclogel, c5.dilated_other,c5.dilation, ".
			"c5.noDilation, c5.unableDilation, ".
			"c5.exam_date as dateDilation ".
			"FROM chart_master_table c1 ".
			"LEFT JOIN ophtha c2 ON c2.form_id=c1.id ".
			"LEFT JOIN chart_drawings c3 ON c3.form_id=c1.id  AND c3.purged='0' AND c3.exam_name = 'FundusExam'  ".
			//"LEFT JOIN chart_left_cc_history c4 ON c4.form_id = c1.id ".
			"LEFT JOIN chart_dialation c5 ON c5.form_id = c1.id AND c5.purged='0'  ".
			"WHERE c1.patient_id='".$this->pid."'  ".
			"AND c1.delete_status='0' ".
			"AND c1.purge_status='0' ".
			//"ORDER BY IFNULL(c4.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) ASC, c1.id ASC ";
			"ORDER BY c1.date_of_service ASC, c1.id ASC ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){

			if($lastPtOptha==""){

				//(09-01-2013):Initial Ophthalmoscopy comes on the Yellow bar even when Dilation is not Done
				if((!empty($row["dilation"]) || !empty($row["pheny10"]) || !empty($row["pheny25"]) ||
				!empty($row["mydiacyl1"]) || !empty($row["mydiacyl5"]) ||
				!empty($row["tropicanide"]) || !empty($row["cyclogel"]) ||
				!empty($row["dilated_other"]) ||
				!empty($row["noDilation"]) || !empty($row["unableDilation"]) ) &&
				(!empty($row["dateDilation"]) && $row["dateDilation"]!="0000-00-00")){

				if(!empty($row["ophtha_id"]) && ((!empty($row["ophtha_od"]) && $row["ophtha_od"]!="0-0-0:;") || (!empty($row["ophtha_os"])&& $row["ophtha_os"]!="0-0-0:;")) && preg_replace('/[^0-9]/','',$row["date_optha_full"]) != "00000000" ){
					$lastPtOptha = "Ext.Oph Ini (".wv_formatDate($row["date_optha_full"],1).")";
					$formIdOptha = $row["form_id"];
					$chartStOptha = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumOptha = $row["releaseNumber"];
					$curyrOptha = isDt12mOld($cdt, $row["date_optha_full"])?"0":"1";
					$realExamOptha="Ophthalmoscopy";
				}else	if(!empty($row["rv_id"]) && (!empty($row["od_drawing"])||!empty($row["idoc_drawing_id"])) && preg_replace('/[^0-9]/','',$row["date_fundusExm_full"])!="00000000"){

					$dtDraw_show = wv_formatDate($row["date_fundusExm_full"],1);
					$dtDraw = $row["date_fundusExm_full"];

					if(!empty($row["idoc_drawing_id"])){
						$oCLSDrawingData = new CLSDrawingData();
						$dtDraw = $oCLSDrawingData->isExamDrawingExits($this->pid,$row["form_id"],$row["rv_id"],"Fundus_Exam","date");
						if(!empty($dtDraw)){
							$dtDraw_show = wv_formatDate($dtDraw,1);
						}
					}

					$lastPtOptha = "Ext.Oph Ini (".$dtDraw_show.")";
					$formIdOptha = $row["form_id"];
					$chartStOptha = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumOptha = $row["releaseNumber"];
					$curyrOptha = isDt12mOld($cdt, $dtDraw)?"0":"1";
					$realExamOptha="Fundus Exam Drawing";
				}

				}//End Check dilation

			}else {

				//AK(26jan12)  Check Dilation:The Extended Ophthalmoscopy should not come up as the patient was not dilated .
				if((!empty($row["dilation"]) || !empty($row["pheny10"]) || !empty($row["pheny25"]) ||
				!empty($row["mydiacyl1"]) || !empty($row["mydiacyl5"]) ||
				!empty($row["tropicanide"]) || !empty($row["cyclogel"]) ||
				!empty($row["dilated_other"]) ||
				!empty($row["noDilation"]) || !empty($row["unableDilation"]) ) &&
				(!empty($row["dateDilation"]) && $row["dateDilation"]!="0000-00-00")){

				if(!empty($row["ophtha_id"]) && ((!empty($row["ophtha_od"]) && $row["ophtha_od"]!="0-0-0:;") || (!empty($row["ophtha_os"])&& $row["ophtha_os"]!="0-0-0:;")) && preg_replace('/[^0-9]/','',$row["date_optha_full"]) != "00000000" ){
					$lastPtExtOptha = "Ext.Oph Sub (".wv_formatDate($row["date_optha_full"],1).")";
					$formIdExtOptha = $row["form_id"];
					$chartStExtOptha = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumExtOptha = $row["releaseNumber"];
					$curyrExtOptha = isDt12mOld($cdt, $row["date_optha_full"])?"0":"1";
					$realExamExtOptha="Ophthalmoscopy";
				}

				if(!empty($row["rv_id"]) && (!empty($row["od_drawing"])||!empty($row["idoc_drawing_id"])) && preg_replace('/[^0-9]/','',$row["date_fundusExm_full"])!="00000000"){

					$dtDraw_show = wv_formatDate($row["date_fundusExm_full"],1);
					$dtDraw = $row["date_fundusExm_full"];

					if(!empty($row["idoc_drawing_id"])){
						$oCLSDrawingData = new CLSDrawingData();
						$dtDraw = $oCLSDrawingData->isExamDrawingExits($this->pid,$row["form_id"],$row["rv_id"],"Fundus_Exam","date");
						if(!empty($dtDraw)){
							$dtDraw_show = wv_formatDate($dtDraw,1);
						}
					}

					$lastPtExtOptha = "Ext.Oph Sub (".$dtDraw_show.")";
					$formIdExtOptha = $row["form_id"];
					$chartStExtOptha = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumExtOptha = $row["releaseNumber"];
					$curyrExtOptha = isDt12mOld($cdt, $dtDraw)?"0":"1";
					$realExamExtOptha="Fundus Exam Drawing";
				}

				}//End Check dilation
			}
		}

		//echo "<br>";
		//print_r(array($lastPtMR,$formIdMR, $chartStMR, $relNumMR));
		//echo "<br>";

		return array("CEE"=>array($lastPtCee, $formIdCee, $chartStCee, $relNumCee,$curyrCee ),
				  "Gonio"=>array($lastPtGonio,$formIdGonio, $chartStGonio, $relNumGonio,$curyrGonio ),
				  "MR"=>array($lastPtMR,$formIdMR, $chartStMR, $relNumMR,$curyrMR ),
				  "Dilation"=>array($lastPtDilation,$formIdDilation,$chartStDilation,$relNumDilation,$curyrDilation ),
				  "Ophtha"=>array($lastPtOptha,$formIdOptha,$chartStOptha,$relNumOptha,$curyrOptha,$realExamOptha),
				  "E.Ophtha"=>array($lastPtExtOptha,$formIdExtOptha,$chartStExtOptha,$relNumExtOptha,$curyrExtOptha,$realExamExtOptha),
				  "Tests" =>$atestDn
				  );
	}


	function getListExamDone(){
		$patient_id = $this->pid;
		// Last Done Exams info at Assessment Title
		//list($arrlastPtCee,$arrlastPtGonio,$arrlastPtMR,$arrlastPtDilation,$arrTestDn) = getLastDoneExamsInfo($patient_id);
		$arrLastDn = $this->getLastDoneExamsInfo();

		$str="";
		$str.="<ul>";

			//<li>Gonio (02-11)</li>
			//All Exams --

				$arrLastDnExamNm = array("CEE","Gonio","MR","Dilation","Ophtha","E.Ophtha");
				foreach($arrLastDnExamNm as $key => $val){

					if( (count($arrLastDn[$val]) > 0) && !empty($arrLastDn[$val][0]) ){

						$arrlastPt = $arrLastDn[$val];
						$lastPt = $arrlastPt[0];
						$lastFId = $arrlastPt[1];
						$lastchartSt = $arrlastPt[2];
						$lastrelNum = $arrlastPt[3];
						$paramNm = $val;
						if(strpos($lastPt,"CEE") !== false) {$paramNm = "Cee";}
						else if($val=="Ophtha"|| $val=="E.Ophtha"){ $paramNm = $arrlastPt[5];}
						$tmp_clr = (!empty($arrlastPt[4])) ? "ltyro" : "gtyro";

						$link_click="";
						if($val != "CEE" && $val != "MR"){
							$link_click = " onclick=\"top.fmain.showFinalize( '".$paramNm."', '".$lastFId."', '".$lastchartSt."', '".$lastrelNum."')\" ";
						}

						$str.= "<li ".$link_click."	class=\"".$tmp_clr."\" >".
							$lastPt.
							"</li>" ;
					}
				}

			//All Exams --

			//All Tests --
			if((is_array($arrLastDn["Tests"]) && count($arrLastDn["Tests"]) > 0)){
				foreach($arrLastDn["Tests"] as $key => $val){
					//$tn = $key;
					$lastPtT = $val[0];
					$lastTId = $val[1];
					$tn = $val[3];

					//Check empty
					if(empty($lastPtT)){
						continue;
					}

					//Color
					$tmp_clr = (!empty($val[2])) ? "ltyro" : "gtyro";

					$link_click = " onclick=\"top.fmain.showFinalize('".$tn."', '', '', '',0,'".$lastTId."')\" ";

					$str.= "<li ".$link_click." class=\"".$tmp_clr."\" >".$lastPtT."</li>" ;

				}
			}
			//All Tests --


		$str.="</ul>";
		return $str;

	}

	function onlwv_GetExamSummary($enm,$post){
		$data_html="";


		switch($enm){
			case "Pupil":
			case "pupil":
				$oload = new PupilLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "EOM":
			case "eom":
				$oload = new EOMLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "External":
			case "external":
				$oload = new ExternalExamLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);

			break;
			case "L&A":
			case "la":
				$oload = new LALoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);

			break;
			case "IOP/Gonio":
			case "iop_gon":
				$oload = new IopGonioLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "SLE":
			case "sle":
				$oload = new SLELoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "Fundus Exam":
			case "fundus_exam":
				$oload = new FundusExamLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);

			break;
			case "Refractive Surgery":
			case "ref_surg":
				$oload = new RefSurgLoader($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "CVF":
			case "cvf":
				$oload = new CVF($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "Amsler Grid":
			case "amsgrid":
				$oload = new AmslerGrid($this->pid,$this->fid);
				$data_html = $oload->getWorkViewSummery($post);
			break;
			case "vf_oct_gl":
				$data_html = $this->getTestsSummery($post);
			break;

			case "dv_cpctrl":
				$oload = new Vision($this->pid,$this->fid);
				$data_html = $oload->getICP(1);
			break;

			case "dv_stereopsis":
				$oload = new Vision($this->pid,$this->fid);
				$data_html = $oload->getStereo(1);
			break;

			case "dv_w4dot":
				$oload = new Vision($this->pid,$this->fid);
				$data_html = $oload->getW4Dot(1);
			break;

		}
		return $data_html;

	}//End func

	function getWvExamIDs($allexm){
		//Pupil,EOM,External,L&A,IOP/Gonio,SLE,Fundus Exam,Refractive Surgery,
		//Conjunctiva,Cornea,Ant. Chamber,Iris & Pupil,Lens,DrawSLE,
		//Opt. Nev,Vitreous,DrawFundus,Retinal Exam,
		//VF/OCT - GL,

		$arr=array();
		$arr_AllExam = explode(",",$allexm);
		if(count($arr_AllExam)>0){
			foreach($arr_AllExam as $key => $val){
				$tmp="";
				if($val=="L&A"){
					$tmp.="la";
				}else if($val=="IOP/Gonio"){
					$tmp.="iop_gon";
				}elseif($val=="Fundus Exam"||$val=="Opt. Nev"||$val=="Vitreous"||$val=="DrawFundus"||$val=="Retinal Exam"){
					$tmp.="fundus_exam";
				}elseif($val=="Conjunctiva"||$val=="Cornea"||$val=="Ant. Chamber"||$val=="Iris & Pupil"||$val=="Lens"||$val=="DrawSLE"){
					$tmp.="sle";
				}elseif($val=="Refractive Surgery"){
					$tmp.="ref_surg";
				}elseif($val=="VF/OCT - GL"){
					$tmp.="vf_oct_gl";
				}else{
					$tmp.=strtolower($val)."";
				}
				//
				if(!empty($tmp) && !in_array($tmp, $arr)){
					$arr[]=$tmp;
				}
			}
		}
		return $arr;
	}

	function get_carry_frwd_id(){
		$cryfwd_form_id = 0;
		$sql = "SELECT cryfwd_form_id FROM chart_master_table where patient_id='".$this->pid."' AND id='".$this->fid."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$cryfwd_form_id = $row["cryfwd_form_id"];
		}
		return $cryfwd_form_id;
	}

	function main(){
		global $finalize_flag, $cryfwd_form_id;
		//uncompress
		if(isset($_GET["artemp"]) && !empty($_GET["artemp"])){ 	$_GET["artemp"] = json_decode(gzuncompress(base64_decode($_GET["artemp"]))); }
		if(isset($_GET["allexm"]) && !empty($_GET["allexm"])){
			if(!isset($_GET["encode"]) || empty($_GET["encode"])){ 	$_GET["allexm"] = gzuncompress(base64_decode($_GET["allexm"]));  }
		}

		//DOS Based chart note
		$cryfwd_form_id = $this->get_carry_frwd_id();

		//--
		switch($_REQUEST["elem_action"]){

			case "GetExamSummary":

				//include(dirname(__FILE__)."/onload_wv_exams.php");
				$echo = "";
				if(isset($_GET["allexm"])){

					$arr=array();
					if(!empty($_GET["allexm"])){
						//$enm = $post["enm"];
						//$oneeye = $post["oe"];

						$allexm = $_GET["allexm"];
						//$allexm = "pupil,eom,external,la,sle,fundus_exam,ref_surg,vf_oct_gl,";//for testing

						$arr_AllExam = $this->getWvExamIDs($allexm);
						if(count($arr_AllExam)>0){
							foreach($arr_AllExam as $key => $val){
								$enm =trim($val);
								if(!empty($enm)){
									if($enm=="GetICP"){
										//$arr["icp"] =  getICP();
									}else if($enm=="GetStereo"){
										//$arr["stereo"] =  getStereo();
									}else if($enm=="GetW4Dot"){
										//$arr["dip_dot"] =  getW4Dot();
									}else{

										$arr[$enm] =  $this->onlwv_GetExamSummary($enm,$_GET);
									}
								}
							}
						}
						/*
						//getExamDrawings
						$arr["drawingpane"] =  onlwv_GetExamDrawings($_GET);
						if($arr["drawingpane"]=="No Drawing exits."){ $arr["drawingpane"]="";  }
						*/
					}//end if

					//Test History
					$arr["imp_ex_done"] =  $this->getListExamDone();

					//Superbill
					$oload = new SuperBillLoader($this->pid,$this->fid);
					$arr["superbill"] = $oload->getWorkViewSuperbill();

					$echo = json_encode($arr);

				}else if(isset($_GET["enm"])&&!empty($_GET["enm"])){
					$arr=array();
					$enm=$_GET["enm"];
					if($enm!="drawingpane"){
						$arr_AllExam = $this->getWvExamIDs($enm);
						$enm = $arr_AllExam[0];
						$arr[$enm] =  $this->onlwv_GetExamSummary($enm,$_GET);
						//$arr[$enm] =  onlwv_GetExamSummary($enm,$_GET);
						//Test History
						$arr["imp_ex_done"] =  $this->getListExamDone();
					}
					/*
					if($_GET["enm"]!="Pupil" && $_GET["enm"]!="amsgrid" && $_GET["enm"]!="CVF"){
						//getExamDrawings
						$arr["drawingpane"] =  onlwv_GetExamDrawings($_GET);
					}
					*/
					$echo = json_encode($arr);
				}

				$z_ob_flush_content=$echo;
				include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");

			break;

			case "loadRVS":
				//print_r($_REQUEST);
				$oCcHx = new CcHx($this->pid,$this->fid);
				$data = $oCcHx->getRVSSummary();
				echo $data;
			break;


			case "GetSuperBill":
				//Superbill
					$oload = new SuperBillLoader($this->pid,$this->fid);
					echo $oload->getWorkViewSuperbill();
				//require(dirname(__FILE__)."/common/Poe.php");
				//include(dirname(__FILE__)."/super_bill.php");
			break;

			case "PupilExam":
				$oload = new PupilLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "EOM":
				$oload = new EOMLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "External":
				$oload = new ExternalExamLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "LA":
				$oload = new LALoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "IOP_Gonio":
				$oload = new IopGonioLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "SLE":
				$oload = new SLELoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "FundusExam":
				$oload = new FundusExamLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;

			case "RefractiveSurgery":
				$oload = new RefSurgLoader($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;
			case "GetGenHealth":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				$var = general_health_div($this->pid);
				echo $var;
			break;
			case "GetMedHx":
				global $datahtm_ocu_meds;
				$oMedHx =  new MedHx($this->pid);
				$oMedHx->setFormId($this->fid);
				$oMedHx->processOcuMeds($finalize_flag);
				echo $datahtm_ocu_meds;
			break;
			case "Procedures":
				$oload = new Procedures($this->pid,$this->fid);
				$oload->load_exam($finalize_flag);
			break;
			case "ScarAR":
				$oload = new Uploader($this->pid,$this->fid);
				$oload->scan_ar($finalize_flag);
			break;
			case "Drawingpane":
				$oPtDrawings = new PtDrawings($this->pid,$this->fid, $finalize_flag);
				$oPtDrawings->loadDrawings();
			break;
			case "Amendments":
				$oAmendments = new ChartAmendment($this->pid,$this->fid);
				$oAmendments->loadAmendments();
			break;
			case "Consult_letters":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->load_consult();
			break;
			case "fetchPhyFaxDetails":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				echo $oConLtr->fetchPhyFaxDetails();
			break;
			case "Pt_consult_letters":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->load_pt_consult();
			break;
			case "Pdf_consult_letters":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->load_pdf();
			break;

			case "physician_notes":
				$oPhyPtNote = new PhyPtNotes($this->pid);
				$oPhyPtNote->load_phy_pt_notes();
			break;
			case "Operative_notes":
				$oOpNote = new OperativeNote($this->pid,$this->fid);
				$oOpNote->load_pt_op_notes();
			break;

			case "CVF":
				$oCVF = new CVF($this->pid,$this->fid);
				$oCVF->getCVFSection($finalize_flag);
			break;

			case "AmslerGrid":
				$oAg = new AmslerGrid($this->pid,$this->fid);
				$oAg->getAGSection($finalize_flag);
			break;

			case "GetListExamDone":
				echo $this->getListExamDone();
			break;

			case "DrawSUC": //Scan upload Camera
				$oPtDrw = new PtDrawings($this->pid,$this->fid,$finalize_flag);
				echo $oPtDrw->getDrawSUC();
			break;

			case "get_scan_upload_preview":
				$oPtDrw = new PtDrawings($this->pid,$this->fid,$finalize_flag);
				echo $oPtDrw->get_scan_upload_preview();
			break;

			case "Check_Direct":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$phyId = (isset($_REQUEST['phyId']) && empty($_REQUEST['phyId']) == false) ? $_REQUEST['phyId'] : '';
				$checkLogic = (isset($_REQUEST['checkLogic']) && empty($_REQUEST['checkLogic']) == false) ? $_REQUEST['checkLogic'] : '';

				$phyArr = array();
				if(empty($phyId) == false){
					$idArr = explode(',', $phyId);
					foreach($idArr as $phy_id){
						if(empty($phy_id) == false && is_numeric($phy_id)) $phyArr[] = $phy_id;
					}
				}


				$returnStatus = false;
				$status = $oConLtr->checkDirectAddress($phyArr, $checkLogic);
				if(empty($status) == false && $status === true){
					$returnStatus = true;
				}

				echo json_encode($returnStatus);
			break;
			/**
			case "GetPtInfo":
				$arr=array();
				$patient_id = $_SESSION["patient"];
				$fid=$_GET["fid"];

				$arr["CLInfo"] = getCLinfo($patient_id);
				//$arr["PhyInfo"] = getPtPhyinfo($patient_id);
				$arr["PhyInfo"] = getMultiPhy($patient_id);
				$arr["patientImg"] = getPatientImage($patient_id,$GLOBALS['incdir']."/main/uploaddir");
				$arr["AD"] = getADInfo($patient_id);

				//GetFutureAppointments
				$arr["FAppts"] = getFutureAppointments("1");

				//GetListExamDone
				$arr["ListExDone"] = getListExamDone();

				//Legend Div
				$arr["PhyLegend"] = getPhyLegend($fid);

				if($_GET["flgGenHealth"]==1){
					ob_start();
					include_once(dirname(__FILE__)."/genHealthDivList.php");
					$arr["GenHealth"] = ob_get_clean();
				}

				//get recalls
				$arr["SchRecalls"] = getSchRecalls();

				echo json_encode($arr);

			break;

			case "GetFutureAppointments":
				$data = getFutureAppointments($_REQUEST["noLabel"]);
				echo $data;
			break;




			*/

			default:
			break;
		}
		//--

	}

}//End Class

//

//Check if patient is empty and redirect it patient search
wv_check_session();

//$patient_id
$patient_id = $_SESSION["patient"];
$form_id = 0;	//
$finalize_flag = 0;

//$form_id
if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
	$form_id = $_SESSION["finalize_id"];
	$finalize_flag = 1;

}else if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
	$form_id = $_SESSION["form_id"];
	$finalize_flag = 0;
}

//Loader
$oLoader = new Loader($patient_id, $form_id);
$oLoader->main();


?>
