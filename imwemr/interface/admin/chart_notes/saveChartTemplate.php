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

require_once('../../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartTemp.php');

//saveComprehensizePlastic----

if(isset($_GET["saveComprehensizePlastic"])){
	
	$sql = "UPDATE chart_admin_settings SET plastic ='".$_GET["saveComprehensizePlastic"]."', vf_gl='".$_GET["saveVFOCT"]."'  WHERE id = '1' ";
	$row=sqlQuery($sql);

	exit();
}


//saveComprehensizePlastic----


$elem_template_id=$_POST["elem_template_id"];
$preObjBack=$_POST["preObjBack"];
$saveBtn=$_POST["saveBtn"];
$elem_templateName=$_POST["elem_templateName"];
$elem_ccda_cpt_code=$_POST["elem_ccda_cpt_code"];

$elem_objNote=$_POST["elem_objNote"];
$elem_vision=$_POST["elem_vision"];
$elem_visDistance=$_POST["elem_visDistance"];
$elem_visNear=$_POST["elem_visNear"];
$elem_visAr=$_POST["elem_visAr"];
$elem_visAk=$_POST["elem_visAk"];
$elem_visPc1=$_POST["elem_visPc1"];
$elem_visPc2=$_POST["elem_visPc2"];
$elem_visPc3=$_POST["elem_visPc3"];
$elem_visMr1=$_POST["elem_visMr1"];
$elem_visMr2=$_POST["elem_visMr2"];
$elem_visMr3=$_POST["elem_visMr3"];
$elem_visBat=$_POST["elem_visBat"];
$elem_visPam=$_POST["elem_visPam"];
$elem_visLasik=$_POST["elem_visLasik"];
$elem_cvf=$_POST["elem_cvf"];
$elem_amsler_grid=$_POST["elem_amsler_grid"];
$elem_icpClr=$_POST["elem_icpClr"];
$elem_stereo=$_POST["elem_stereo"];
$elem_diplopia=$_POST["elem_diplopia"];
$elem_w4dot=$_POST["elem_w4dot"];
$elem_retino=$_POST["elem_retino"];
$elem_exophth=$_POST["elem_exophth"];
$elem_cyc_ret=$_POST["elem_cyc_ret"];
$elem_pupil=$_POST["elem_pupil"];
$elem_eom=$_POST["elem_eom"];
$elem_external=$_POST["elem_external"];
$elem_La=$_POST["elem_La"];
$elem_plastic=$_POST["elem_plastic"];
$elem_iop=$_POST["elem_iop"];
$elem_delId=$_POST["elem_delId"];
$elem_comments=$_POST["elem_comments"];
$elem_refSurgery=$_POST["elem_refSurgery"];

$elem_refSurgery=$_POST["elem_refSurgery"];

$elem_conj = $_POST["elem_conj"];
$elem_corn= $_POST["elem_corn"];
$elem_antChm= $_POST["elem_antChm"];
$elem_IrisPupil= $_POST["elem_IrisPupil"];
$elem_lens= $_POST["elem_lens"];
$elem_drawSle= $_POST["elem_drawSle"];

$elem_opNrv= $_POST["elem_opNrv"];
$elem_macula= $_POST["elem_macula"];
$elem_vit= $_POST["elem_vit"];
$elem_peri= $_POST["elem_peri"];
$elem_bv= $_POST["elem_bv"];
$elem_drawRv= $_POST["elem_drawRv"];

$elem_vfOctGL = $_POST["elem_vfOctGL"];

//Techician --

$elem_objNote_tech=$_POST["elem_objNote_tech"];
$elem_vision_tech=$_POST["elem_vision_tech"];
$elem_visDistance_tech=$_POST["elem_visDistance_tech"];
$elem_visNear_tech=$_POST["elem_visNear_tech"];
$elem_visAr_tech=$_POST["elem_visAr_tech"];
$elem_visAk_tech=$_POST["elem_visAk_tech"];
$elem_visPc1_tech=$_POST["elem_visPc1_tech"];
$elem_visPc2_tech=$_POST["elem_visPc2_tech"];
$elem_visPc3_tech=$_POST["elem_visPc3_tech"];
$elem_visMr1_tech=$_POST["elem_visMr1_tech"];
$elem_visMr2_tech=$_POST["elem_visMr2_tech"];
$elem_visMr3_tech=$_POST["elem_visMr3_tech"];
$elem_visBat_tech=$_POST["elem_visBat_tech"];
$elem_visPam_tech=$_POST["elem_visPam_tech"];
$elem_visLasik_tech=$_POST["elem_visLasik_tech"];
$elem_cvf_tech=$_POST["elem_cvf_tech"];
$elem_amsler_grid_tech=$_POST["elem_amsler_grid_tech"];
$elem_icpClr_tech=$_POST["elem_icpClr_tech"];
$elem_stereo_tech=$_POST["elem_stereo_tech"];
$elem_diplopia_tech=$_POST["elem_diplopia_tech"];
$elem_w4dot_tech=$_POST["elem_w4dot_tech"];
$elem_retino_tech=$_POST["elem_retino_tech"];
$elem_exophth_tech=$_POST["elem_exophth_tech"];
$elem_cyc_ret_tech=$_POST["elem_cyc_ret_tech"];
$elem_pupil_tech=$_POST["elem_pupil_tech"];
$elem_eom_tech=$_POST["elem_eom_tech"];
$elem_external_tech=$_POST["elem_external_tech"];
$elem_La_tech=$_POST["elem_La_tech"];
$elem_plastic_tech=$_POST["elem_plastic_tech"];
$elem_iop_tech=$_POST["elem_iop_tech"];
$elem_comments_tech=$_POST["elem_comments_tech"];
$elem_refSurgery_tech=$_POST["elem_refSurgery_tech"];
$elem_conj_tech = $_POST["elem_conj_tech"];
$elem_corn_tech= $_POST["elem_corn_tech"];
$elem_antChm_tech= $_POST["elem_antChm_tech"];
$elem_IrisPupil_tech= $_POST["elem_IrisPupil_tech"];
$elem_lens_tech= $_POST["elem_lens_tech"];
$elem_drawSle_tech= $_POST["elem_drawSle_tech"];
$elem_opNrv_tech= $_POST["elem_opNrv_tech"];
$elem_macula_tech= $_POST["elem_macula_tech"];
$elem_vit_tech= $_POST["elem_vit_tech"];
$elem_peri_tech= $_POST["elem_peri_tech"];
$elem_bv_tech= $_POST["elem_bv_tech"];
$elem_drawRv_tech= $_POST["elem_drawRv_tech"];
$elem_vfOctGL_tech = $_POST["elem_vfOctGL_tech"];

//Techician --

//Obj
$oChartTemp = new ChartTemp();

if(isset($elem_delId) && !empty($elem_delId)){
	
	$tmp = $oChartTemp->deleteTemp($elem_delId);
	//QString
	$qstr = "eid=".$elem_delId;
}else{

	//Make comma seperated string
	$str = "";
	$arr = array($elem_objNote,$elem_vision,$elem_visDistance,$elem_visNear,
					$elem_visAr,$elem_visAk,
					$elem_visPc1,$elem_visPc2,$elem_visPc3,
					$elem_visMr1,$elem_visMr2,$elem_visMr3,
					$elem_visBat,$elem_visPam,$elem_visLasik,$elem_cvf, $elem_amsler_grid,
					$elem_icpClr,$elem_stereo,$elem_diplopia,
					$elem_w4dot,$elem_retino,$elem_exophth,$elem_comments,$elem_cyc_ret,
					$elem_pupil,$elem_eom,$elem_external,
					$elem_La,$elem_iop,$elem_sle,$elem_fundus,$elem_refSurgery,
					$elem_conj,$elem_corn,$elem_antChm,$elem_IrisPupil,$elem_lens,$elem_drawSle,
					$elem_opNrv,$elem_vit,$elem_drawRv,$elem_macula, $elem_peri, $elem_bv,$elem_reti,$elem_visContLens,$elem_plastic, $elem_vfOctGL
				);
	foreach($arr as $key => $val){
		if((!empty($val) && ($val != "0"))){
			$str .= !empty($str) ? "," : "";
			$str .=  $val;
		}
	}
	//
	
	//Make comma seperated string - technician
	$str_tech = "";
	$arr_tech = array($elem_objNote_tech,$elem_vision_tech,$elem_visDistance_tech,$elem_visNear_tech,
					$elem_visAr_tech,$elem_visAk_tech,
					$elem_visPc1_tech,$elem_visPc2_tech,$elem_visPc3_tech,
					$elem_visMr1_tech,$elem_visMr2_tech,$elem_visMr3_tech,
					$elem_visBat_tech,$elem_visPam_tech,$elem_visLasik_tech,$elem_cvf_tech, $elem_amsler_grid_tech,
					$elem_icpClr_tech,$elem_stereo_tech,$elem_diplopia_tech,
					$elem_w4dot_tech,$elem_retino_tech,$elem_exophth_tech,$elem_comments_tech,$elem_cyc_ret_tech,
					$elem_pupil_tech,$elem_eom_tech,$elem_external_tech,
					$elem_La_tech,$elem_iop_tech,$elem_sle_tech,$elem_fundus_tech,$elem_refSurgery_tech,
					$elem_conj_tech,$elem_corn_tech,$elem_antChm_tech,$elem_IrisPupil_tech,$elem_lens_tech,$elem_drawSle_tech,
					$elem_opNrv_tech,$elem_vit_tech,$elem_drawRv_tech,$elem_macula_tech, $elem_peri_tech, $elem_bv_tech,$elem_reti_tech,$elem_visContLens_tech,$elem_plastic_tech, $elem_vfOctGL_tech
				);
	foreach($arr_tech as $key => $val){
		if((!empty($val) && ($val != "0"))){
			$str_tech .= !empty($str_tech) ? "," : "";
			$str_tech .=  $val;
		}
	}
	//
	
	
	//echo $str_tech;
	//exit();

	// Check Template Name for Duplication b4 insertion
	if(empty($elem_template_id)){	
		$tmp = $oChartTemp->getIdFromName($elem_templateName);	
		if($tmp != false){
			$elem_template_id = $tmp;
		}
	}

	//Make Array
	$arrSave = array("temp_name"=>$elem_templateName,"temp_fields"=>$str,"ccda_cpt_code"=>$elem_ccda_cpt_code,"id"=>$elem_template_id,"temp_fields_tech"=>$str_tech); 

	//Save Query
	if(!empty($elem_template_id)){
		//Update
		$sid = $oChartTemp->update($arrSave);

	}else{
		//Insert
		$sid = $oChartTemp->insert($arrSave);
	}
	//QString
	$qstr = "sid=".$sid;

}
//Redirect
header("Location: chart_template.php?".$qstr);
exit();
?>