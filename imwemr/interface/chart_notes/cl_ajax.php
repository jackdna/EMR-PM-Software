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
include_once("../../config/globals.php");
include_once("../../library/classes/common_function.php");
$mode= $_REQUEST['mode'];
$num = $_REQUEST['num'];
$clws_id = $_REQUEST['clws_id'];
$current_clws_id = $_REQUEST['current_clws_id'];
$arrCLData=array();
$finalFound='0';
$updated = 0;

if($mode == "copycomments" && $clws_id > 0){
	$commentArray = array();
	$sheetcount = 0;
	$sheetcount = $_REQUEST['sheetcount'];
	$clCommentsQuery = "select clc.id as comment_id, clc.cl_sheet_id as sheet_id, clc.comment as comment_desc from cl_comments clc left join contactlensmaster clm on clc.cl_sheet_id=clm.clws_id where clm.patient_id='".$_SESSION['patient']."' and clc.cl_sheet_id='".$clws_id."' and clc.delete_status='0' order by clc.id desc";
	$clCommentsResult = imw_query($clCommentsQuery) or die(imw_error()." - ".$clCommentsQuery);
	$newCommentDiv = "";
	$oldCommentDiv = "";
	while($clRow = imw_fetch_assoc($clCommentsResult)){
		$commentDesc = $clRow['comment_desc'];
		$commentId = $clRow['comment_id'];
		$newCommentDiv .= "<div id='div_0' class='row comments_text_div' style='width:610px;height:auto;'>";
		$newCommentDiv .= "<div class='col-xs-11' style='height:auto;'>";
		$newCommentDiv .= '<textarea id="cltextarea_0" name="comment_new_column'.$sheetcount.'[]" class="cltextarea" style="width: 100%; display: inline; resize: none; height: 58px;">'.$commentDesc.'</textarea>';
		$newCommentDiv .= "</div>";
		$newCommentDiv .= "<div class='col-xs-1 figure'>";
		$newCommentDiv .= "<figure id='commentplus' class='comment_figure' style='display:inline;'>";
		$newCommentDiv .= "<span class='glyphicon glyphicon-remove cl_comment_image' onClick=\"deleteCLComment(0, this);\" title='Delete comment'></span>";
		$newCommentDiv .= "</figure>";
		$newCommentDiv .= "</div>";
		$newCommentDiv .= "</div>";

		/* $commentArray[$sheetcount] = $newCommentDiv;

		$oldCommentDiv .= "<div id='div_0' class='row comments_text_div' style='width:610px;height:auto;'>";
		$oldCommentDiv .= "<div class='col-xs-11' style='height:auto;'>";
		$oldCommentDiv .= '<textarea id="cltextarea_'.$commentId.'" name="comment_update_'.$clws_id.'_'.$commentId.'" class="cltextarea" style="width: 100%; display: inline; resize: none; height: 58px;">'.$commentDesc.'</textarea>';
		$oldCommentDiv .= "</div>";
		$oldCommentDiv .= "<div class='col-xs-1 figure'>";
		$oldCommentDiv .= "<figure id='commentplus' class='comment_figure' style='display:inline;'>";
		$oldCommentDiv .= "<span class='glyphicon glyphicon-remove cl_comment_image' onClick=\"deleteCLComment(0, this);\" title='Delete comment'></span>";
		$oldCommentDiv .= "</figure>";
		$oldCommentDiv .= "</div>";
		$oldCommentDiv .= "</div>";*/
		$commentArray[$clws_id] = $oldCommentDiv;
		$commentArray["new"] = $newCommentDiv;
	}
	echo json_encode($commentArray);
	exit(0);
}

/* if($mode == "parentsheetcomments" && $clws_id > 0){
	$clCommentsQuery = "select clc.id as comment_id, clc.cl_sheet_id as sheet_id, clc.comment as comment_desc from cl_comments clc left join contactlensmaster clm on clc.cl_sheet_id=clm.clws_id where clm.patient_id='".$_SESSION['patient']."' and clc.cl_sheet_id='".$clws_id."' and clc.delete_status='0' order by clc.id desc";
	$clCommentsResult = imw_query($clCommentsQuery) or die(imw_error()." - ".$clCommentsQuery);
	$newCommentDiv = "";
	while($clRow = imw_fetch_assoc($clCommentsResult)){
		$commentDesc = $clRow['comment_desc'];
		$commentId = $clRow['comment_id'];
		$newCommentDiv .= "<div id='div_0' class='row comments_text_div' style='width:610px;height:auto;'>";
		$newCommentDiv .= "<div class='col-xs-11' style='height:auto;'>";
		$newCommentDiv .= '<textarea id="cltextarea_'.$commentId.'" name="comment_update_'.$clws_id.'_'.$commentId.'" class="cltextarea" style="width: 100%; display: inline; resize: none; height: 58px;">'.$commentDesc.'</textarea>';
		$newCommentDiv .= "</div>";
		$newCommentDiv .= "<div class='col-xs-1 figure'>";
		$newCommentDiv .= "<figure id='commentplus' class='comment_figure' style='display:inline;'>";
		$newCommentDiv .= "<span class='glyphicon glyphicon-remove cl_comment_image' onClick=\"deleteCLComment(".$commentId.", this);\" title='Delete comment'></span>";
		$newCommentDiv .= "</figure>";
		$newCommentDiv .= "</div>";
		$newCommentDiv .= "</div>";
	}
	echo $newCommentDiv;
	exit(0);
} */

if($mode == "delete" || $mode == "undelete"){
    if($mode == 'undelete'){
        $updQry = "Update contactlensmaster SET del_status='0' WHERE clws_id IN(".$clws_id.")";
        file_put_contents('charting.txt', $updQry);
        $rs = imw_query($updQry);
        if($rs)
        {
            $updated=1;
        }
    }else{
        $updQry = "Update contactlensmaster SET del_status='1' WHERE clws_id IN(".$clws_id.")";
        file_put_contents('charting.txt', $updQry);
        $rs = imw_query($updQry);
        if($rs)
        {
            $updated=1;
        }
    }
    echo $updated;
}

if($mode=='getFinal'){
	$qry="Select clws_id FROM contactlensmaster WHERE patient_id='".$_SESSION['patient']."' AND clws_type LIKE '%Final%' ORDER BY clws_id DESC LIMIT 0,1";
	$rs=imw_query($qry);
	if(imw_num_rows($rs)>0){
		$res=imw_fetch_array($rs);
		$clws_id=$res['clws_id'];
		$finalFound='1';
	}else{
		$clws_id='';
		echo json_encode(array('finalFound'=>$finalFound));			
	}
	unset($rs);
}


		
if((empty($clws_id)==false && empty($num)==false) || ($mode=='getFinal' && $clws_id>0)){
    $qry="Select cl.*, DATE_FORMAT(dos, '".get_sql_date_format()."') as 'dos', DATE_FORMAT(cl.clws_savedatetime , '".get_sql_date_format()."') AS worksheetdate, cl_det.* 
	FROM contactlensmaster cl 
	LEFT JOIN contactlensworksheet_det cl_det ON cl_det.clws_id = cl.clws_id 
	WHERE cl.patient_id='".$_SESSION['patient']."'";
	if(empty($clws_id)==false){
		if($clws_id!='all'){
			$qry.=" AND cl.clws_id='".$clws_id."'";
		}
	}else if(empty($_POST['oldSheets'])==true){
			$qry.=" AND cl.clws_id='".$oldClwsId."'";
	}		
	$qry.=" ORDER BY cl.clws_id ASC, cl_det.clEye, cl_det.id ASC";

	$rs = imw_query($qry);
	$i=$s=$old_clws_id=0;
	$arrTempOldArray=array();
	while($res =imw_fetch_assoc($rs)){ 
		if($res['clEye']!='OU'){
			$clws_id=$res['clws_id'];
			$clEye=$res['clEye'];
			$eyeL=strtolower($clEye);
			$eye1Cap=ucfirst($eyeL);
			
			if($old_clws_id>0 && $old_clws_id!=$clws_id){ $s=$s+1; }
			if($arrTempOldArray[$clws_id][$clEye]){
				$i=$i+1;
			}else{
				$i=0;
			}
		
			if($clEye=='OD'){
				$arrCLData[$s][$clEye][$i]['OD_ID']=$res['id'];
			}else if($clEye=='OS'){
				$arrCLData[$s][$clEye][$i]['OS_ID']=$res['id'];
			}
		
			$arrCLData[$s][$clEye][$i]['clType'.$clEye]=$res['clType'];
			$arrCLData[$s][$clEye][$i]['clEye'.$clEye]=$res['clEye'];
		
			$arrCLData[$s][$clEye][$i]['clws_id']=$res['clws_id'];
			$arrCLData[$s][$clEye][$i]['id']=$res['id'];
		
			if(!$arrCLWS_IDs[$clws_id]){
				$dos=$res['dos'];
				$arrCLData[$s][$clEye][$i]['dos']=$dos;
				$arrCLData[$s][$clEye][$i]['clws_savedatetime']=$res['clws_savedatetime'];
				$arrCLData[$s][$clEye][$i]['clws_type']=$res['clws_type'];
				$arrCLData[$s][$clEye][$i]['clws_trial_number']=$res['clws_trial_number'];
				$arrCLData[$s][$clEye][$i]['currentWorksheetid']=$res['currentWorksheetid'];
				$arrCLData[$s][$clEye][$i]['AverageWearTime']=$res['AverageWearTime'];
				$arrCLData[$s][$clEye][$i]['Solutions']=$res['Solutions'];
				$arrCLData[$s][$clEye][$i]['Age']=$res['Age'];
				$arrCLData[$s][$clEye][$i]['DisposableSchedule']=$res['DisposableSchedule'];
				$arrCLData[$s][$clEye][$i]['form_id']=$res['form_id'];
				$arrCLData[$s][$clEye][$i]['del_status']=$res['del_status'];
				$arrCLData[$s][$clEye][$i]['charges_id']=$res['charges_id'];
				$arrCLData[$s][$clEye][$i]['worksheetdate']=$res['worksheetdate'];
				$arrCLData[$s][$clEye][$i]['cl_comment']=$res['cl_comment'];
				$arrCLData[$s][$clEye][$i]['cpt_evaluation_fit_refit']=$res['cpt_evaluation_fit_refit'];
				$arrCLData[$s][$clEye][$i]['usage_val']=$res['usage_val'];
				$arrCLData[$s][$clEye][$i]['allaround']=$res['allaround'];
				$arrCLData[$s][$clEye][$i]['wear_scheduler']=$res['wear_scheduler'];
				$arrCLData[$s][$clEye][$i]['replenishment']=$res['replenishment'];
				$arrCLData[$s][$clEye][$i]['disinfecting']=$res['disinfecting'];
				$arrCLWS_IDs[$clws_id]=$clws_id;
				$formId=$res['form_id'];
				$arrFormIds[$res['form_id']]=$formId;
				//GETTING OU VALUES SAVED IN FIRST ROW OF THIS SHEET
				//if($mode!='copyfrom'){
					$arrCLData[$s]['OU'][$i]['SclNvaOU']=$res['SclNvaOU'];
					$arrCLData[$s]['OU'][$i]['SclDvaOU']=$res['SclDvaOU'];
				//}
				$arrSheetInfo[$clws_id]['form_id']=$formId;
				$arrSheetInfo[$clws_id]['dos']=$dos;
			}
			
			if($res['clType']=='scl'){
				$arrCLData[$s][$clEye][$i]['SclBcurve'.$clEye]=$res['SclBcurve'.$clEye];
				$arrCLData[$s][$clEye][$i]['SclDiameter'.$clEye]=$res['SclDiameter'.$clEye];
				$arrCLData[$s][$clEye][$i]['Sclsphere'.$clEye]=$res['Sclsphere'.$clEye];
				$arrCLData[$s][$clEye][$i]['SclCylinder'.$clEye]=$res['SclCylinder'.$clEye];
				$arrCLData[$s][$clEye][$i]['SclAdd'.$clEye]=$res['SclAdd'.$clEye];
				$arrCLData[$s][$clEye][$i]['Sclaxis'.$clEye]=$res['Sclaxis'.$clEye];
				$arrCLData[$s][$clEye][$i]['SclColor'.$clEye]=$res['SclColor'.$clEye];
				//if($mode!='copyfrom'){
					$arrCLData[$s][$clEye][$i]['SclDva'.$clEye]=$res['SclDva'.$clEye];
					$arrCLData[$s][$clEye][$i]['SclNva'.$clEye]=$res['SclNva'.$clEye];
				//}
				$arrCLData[$s][$clEye][$i]['SclType'.$clEye]=$res['SclType'.$clEye];
				$arrCLData[$s][$clEye][$i]['SclType'.$clEye.'_ID']=$res['SclType'.$clEye.'_ID'];
				
				//DRAWING
				//INSERT INTO SCAN DATABSE TABLE IF NOT EXIST THERE
				if($res['idoc_drawing_id']<=0 && $res['elem_SCL'.$eye1Cap.'DrawingPath']!=''){
					$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
					toll_image='imgCorneaCanvas',
					drawing_for='DrawCL',
					drawing_image_path='".$res['elem_SCL'.$eye1Cap.'DrawingPath']."',
					row_created_by='1',
					row_created_date_time='".date('Y-m-d H:i:s')."',
					patient_id='".$_SESSION['patient']."',
					patient_form_id='".$formId."',
					row_visit_dos='".$dos."'";
					imw_query($qry);
					$res['idoc_drawing_id']=imw_insert_id();
					
					$qry="Update contactlensworksheet_det SET idoc_drawing_id='".$res['idoc_drawing_id']."' WHERE id='".$res['id']."'";
					imw_query($qry);
				}
				//$arrCLData[$s][$clEye][$i]['elem_SCL'.$eye1Cap.'Drawing']=$res['elem_SCL'.$eye1Cap.'Drawing'];
				//$arrCLData[$s][$clEye][$i]['hdSCL'.$eye1Cap.'DrawingOriginal']=$res['hdSCL'.$eye1Cap.'DrawingOriginal'];
				//$arrCLData[$s][$clEye][$i]['elem_SCL'.$eye1Cap.'DrawingPath']=$res['elem_SCL'.$eye1Cap.'DrawingPath'];
				
			}else if($res['clType']=='rgp'){
				$arrCLData[$s][$clEye][$i]['RgpBC'.$clEye]=$res['RgpBC'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpDiameter'.$clEye]=$res['RgpDiameter'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpOZ'.$clEye]=$res['RgpOZ'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCT'.$clEye]=$res['RgpCT'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpPower'.$clEye]=$res['RgpPower'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCylinder'.$clEye]=$res['RgpCylinder'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpAxis'.$clEye]=$res['RgpAxis'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpColor'.$clEye]=$res['RgpColor'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpAdd'.$clEye]=$res['RgpAdd'.$clEye];
				//if($mode!='copyfrom'){
					$arrCLData[$s][$clEye][$i]['RgpDva'.$clEye]=$res['RgpDva'.$clEye];
					$arrCLData[$s][$clEye][$i]['RgpNva'.$clEye]=$res['RgpNva'.$clEye];
				//}
				$arrCLData[$s][$clEye][$i]['RgpType'.$clEye]=$res['RgpType'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpType'.$clEye.'_ID']=$res['RgpType'.$clEye.'_ID'];
			}else if($res['clType']=='cust_rgp'){
				$arrCLData[$s][$clEye][$i]['RgpCustomBC'.$clEye]=$res['RgpCustomBC'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomDiameter'.$clEye]=$res['RgpCustomDiameter'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomOZ'.$clEye]=$res['RgpCustomOZ'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomCT'.$clEye]=$res['RgpCustomCT'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomPower'.$clEye]=$res['RgpCustomPower'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustom2degree'.$clEye]=$res['RgpCustom2degree'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustom3degree'.$clEye]=$res['RgpCustom3degree'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomPCW'.$clEye]=$res['RgpCustomPCW'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomColor'.$clEye]=$res['RgpCustomColor'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomBlend'.$clEye]=$res['RgpCustomBlend'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomEdge'.$clEye]=$res['RgpCustomEdge'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomAdd'.$clEye]=$res['RgpCustomAdd'.$clEye];
				//if($mode!='copyfrom'){
					$arrCLData[$s][$clEye][$i]['RgpCustomDva'.$clEye]=$res['RgpCustomDva'.$clEye];
					$arrCLData[$s][$clEye][$i]['RgpCustomNva'.$clEye]=$res['RgpCustomNva'.$clEye];
				//}
				$arrCLData[$s][$clEye][$i]['RgpCustomType'.$clEye]=$res['RgpCustomType'.$clEye];
				$arrCLData[$s][$clEye][$i]['RgpCustomType'.$clEye.'_ID']=$res['RgpCustomType'.$clEye.'_ID'];
			}

			//DRAWING DATA
			//$arrCLData[$s][$clEye][$i]['idoc_drawing_id_'.$eyeL]=$res['idoc_drawing_id'];
			//$arrCLData[$s][$clEye][$i]['corneaSCL_od_desc']=$res['corneaSCL_od_desc'];
			//$arrCLData[$s][$clEye][$i]['corneaSCL_os_desc']=$res['corneaSCL_os_desc'];		
			
			//CHEKING IF DRAWING DATA EXIST OR NOT
			$arrCLData[$s][$clEye][$i]['hasDrawing_'.$eyeL]='no';
			if($res['idoc_drawing_id']>0){
				//$arrCLData[$s][$clEye][$i]['hasDrawing_'.$eyeL]='yes';
			}		
			
			$old_clws_id=$clws_id;
			$arrTempOldArray[$clws_id][$clEye]=$clEye;
		}
	}
	
	$arrCLEval=array();
	if(sizeof($arrCLWS_IDs)>0){
		$strCLWS_IDs=implode(',', $arrCLWS_IDs);
		$qry="Select * FROM contactlens_evaluations cl_eval WHERE clws_id IN(".$strCLWS_IDs.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$clwsId=$res['clws_id'];
			//SCL
			//if($mode!='copyfrom'){
				$arrCLEval[$clwsId]['CLSLCEvaluationSphereOD']=$res['CLSLCEvaluationSphereOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationCylinderOD']=$res['CLSLCEvaluationCylinderOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationAxisOD']=$res['CLSLCEvaluationAxisOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationDVAOD']=$res['CLSLCEvaluationDVAOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationSphereNVAOD']=$res['CLSLCEvaluationSphereNVAOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationCylinderNVAOD']=$res['CLSLCEvaluationCylinderNVAOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationAxisNVAOD']=$res['CLSLCEvaluationAxisNVAOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationNVAOD']=$res['CLSLCEvaluationNVAOD'];
				$arrCLEval[$clwsId]['CLSLCEvaluationSphereOS']=$res['CLSLCEvaluationSphereOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationCylinderOS']=$res['CLSLCEvaluationCylinderOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationAxisOS']=$res['CLSLCEvaluationAxisOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationDVAOS']=$res['CLSLCEvaluationDVAOS'];		
				$arrCLEval[$clwsId]['CLSLCEvaluationSphereNVAOS']=$res['CLSLCEvaluationSphereNVAOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationCylinderNVAOS']=$res['CLSLCEvaluationCylinderNVAOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationAxisNVAOS']=$res['CLSLCEvaluationAxisNVAOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationNVAOS']=$res['CLSLCEvaluationNVAOS'];
				$arrCLEval[$clwsId]['CLSLCEvaluationDVAOU']=$res['CLSLCEvaluationDVAOU'];
				$arrCLEval[$clwsId]['CLSLCEvaluationNVAOU']=$res['CLSLCEvaluationNVAOU'];
			//}
			$arrCLEval[$clwsId]['CLSLCEvaluationPositionOD']=$res['CLSLCEvaluationPositionOD'];
			$arrCLEval[$clwsId]['CLSLCEvaluationPositionOtherOD']=$res['CLSLCEvaluationPositionOtherOD'];
			$arrCLEval[$clwsId]['CLSLCEvaluationComfortOD']=$res['CLSLCEvaluationComfortOD'];
			$arrCLEval[$clwsId]['CLSLCEvaluationMovementOD']=$res['CLSLCEvaluationMovementOD'];
			$arrCLEval[$clwsId]['CLSLCEvaluationCondtionOD']=$res['CLSLCEvaluationCondtionOD'];
			$arrCLEval[$clwsId]['CLSLCEvaluationPositionOS']=$res['CLSLCEvaluationPositionOS'];
			$arrCLEval[$clwsId]['CLSLCEvaluationPositionOtherOS']=$res['CLSLCEvaluationPositionOtherOS'];		
			$arrCLEval[$clwsId]['CLSLCEvaluationComfortOS']=$res['CLSLCEvaluationComfortOS'];
			$arrCLEval[$clwsId]['CLSLCEvaluationMovementOS']=$res['CLSLCEvaluationMovementOS'];
			$arrCLEval[$clwsId]['CLSLCEvaluationCondtionOS']=$res['CLSLCEvaluationCondtionOS'];
	
			//RGP CUST-RGP																	
			//if($mode!='copyfrom'){
				$arrCLEval[$clwsId]['CLRGPEvaluationSphereOD']=$res['CLRGPEvaluationSphereOD'];			// DVA OD SPHERE
				$arrCLEval[$clwsId]['CLRGPEvaluationSphereOS']=$res['CLRGPEvaluationSphereOS'];			// DVA OS SPHERE
				$arrCLEval[$clwsId]['CLRGPEvaluationCylinderOD']=$res['CLRGPEvaluationCylinderOD'];		// DVA OD CYLINDER
				$arrCLEval[$clwsId]['CLRGPEvaluationCylinderOS']=$res['CLRGPEvaluationCylinderOS'];		// DVA OS CYLINDER
				$arrCLEval[$clwsId]['CLRGPEvaluationAxisOD']=$res['CLRGPEvaluationAxisOD'];				// DVA OD AXIS
				$arrCLEval[$clwsId]['CLRGPEvaluationAxisOS']=$res['CLRGPEvaluationAxisOS'];				// DVA OS AXIS
				$arrCLEval[$clwsId]['CLRGPEvaluationDVAOD']=$res['CLRGPEvaluationDVAOD'];				// DVA OD
				$arrCLEval[$clwsId]['CLRGPEvaluationDVAOS']=$res['CLRGPEvaluationDVAOS'];				// DVA OS


				$arrCLEval[$clwsId]['CLRGPEvaluationSphereNVAOD']=$res['CLRGPEvaluationSphereNVAOD'];				// NVA OD SPHERE
				$arrCLEval[$clwsId]['CLRGPEvaluationSphereNVAOS']=$res['CLRGPEvaluationSphereNVAOS'];				// NVA OS SPHERE
				$arrCLEval[$clwsId]['CLRGPEvaluationCylinderNVAOD']=$res['CLRGPEvaluationCylinderNVAOD'];			// NVA OD CYLINDER
				$arrCLEval[$clwsId]['CLRGPEvaluationCylinderNVAOS']=$res['CLRGPEvaluationCylinderNVAOS'];			// NVA OS CYLINDER
				$arrCLEval[$clwsId]['CLRGPEvaluationAxisNVAOD']=$res['CLRGPEvaluationAxisNVAOD'];					// NVA OD AXIS
				$arrCLEval[$clwsId]['CLRGPEvaluationAxisNVAOS']=$res['CLRGPEvaluationAxisNVAOS'];					// NVA OS AXIS
				$arrCLEval[$clwsId]['CLRGPEvaluationNVAOD']=$res['CLRGPEvaluationNVAOD'];							// NVA OD
				$arrCLEval[$clwsId]['CLRGPEvaluationNVAOS']=$res['CLRGPEvaluationNVAOS'];							// NVA OS


				//$arrCLEval[$clwsId]['CLRGPEvaluationSphereNVAOD']=$res['CLRGPEvaluationSphereNVAOD'];
				
				
				
				
				//$arrCLEval[$clwsId]['CLRGPEvaluationNVAOS']=$res['CLRGPEvaluationNVAOS'];
			//}
			$arrCLEval[$clwsId]['CLRGPEvaluationComfortOD']=$res['CLRGPEvaluationComfortOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationMovementOD']=$res['CLRGPEvaluationMovementOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOD']=$res['CLRGPEvaluationPosBeforeOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOtherOD']=$res['CLRGPEvaluationPosBeforeOtherOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOD']=$res['CLRGPEvaluationPosAfterOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOtherOD']=$res['CLRGPEvaluationPosAfterOtherOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationFluoresceinPatternOD']=$res['CLRGPEvaluationFluoresceinPatternOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationInvertedOD']=$res['CLRGPEvaluationInvertedOD'];
			$arrCLEval[$clwsId]['CLRGPEvaluationComfortOS']=$res['CLRGPEvaluationComfortOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationMovementOS']=$res['CLRGPEvaluationMovementOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOS']=$res['CLRGPEvaluationPosBeforeOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOtherOS']=$res['CLRGPEvaluationPosBeforeOtherOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOS']=$res['CLRGPEvaluationPosAfterOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOtherOS']=$res['CLRGPEvaluationPosAfterOtherOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationFluoresceinPatternOS']=$res['CLRGPEvaluationFluoresceinPatternOS'];
			$arrCLEval[$clwsId]['CLRGPEvaluationInvertedOS']=$res['CLRGPEvaluationInvertedOS'];
			
			$arrCLEval[$clwsId]['EvaluationRotationOD']=$res['EvaluationRotationOD'];
			$arrCLEval[$clwsId]['EvaluationRotationOS']=$res['EvaluationRotationOS'];
			//DRAWING
			//INSERT INTO SCAN DATABSE TABLE IF NOT EXIST THERE
			if($res['idoc_drawing_id_od']<=0 && $res['elem_conjunctivaOdDrawingPath']!=''){
				$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
				toll_image='imgCorneaCanvas',
				drawing_for='DrawCL',
				drawing_image_path='".$res['elem_conjunctivaOdDrawingPath']."',
				row_created_by='1',
				row_created_date_time='".date('Y-m-d H:i:s')."',
				patient_id='".$_SESSION['patient']."',
				patient_form_id='".$formId."',
				row_visit_dos='".$dos."'";
				imw_query($qry);
				$res['idoc_drawing_id_od']=imw_insert_id();
				
				$qry="Update contactlens_evaluations SET idoc_drawing_id_od='".$res['idoc_drawing_id_od']."' WHERE id='".$res['id']."'";
				imw_query($qry);
			}
			if($res['idoc_drawing_id_os']<=0 && $res['elem_conjunctivaOsDrawingPath']!=''){
				$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
				toll_image='imgCorneaCanvas',
				drawing_for='DrawCL',
				drawing_image_path='".$res['elem_conjunctivaOsDrawingPath']."',
				row_created_by='1',
				row_created_date_time='".date('Y-m-d H:i:s')."',
				patient_id='".$_SESSION['patient']."',
				patient_form_id='".$formId."',
				row_visit_dos='".$dos."'";
				imw_query($qry);
				$res['idoc_drawing_id_os']=imw_insert_id();
				
				$qry="Update contactlens_evaluations SET idoc_drawing_id_os='".$res['idoc_drawing_id_os']."' WHERE id='".$res['id']."'";
				imw_query($qry);
			}		
			//$arrCLEval[$clwsId]['cornea_od_desc']=$res['cornea_od_desc'];
			//$arrCLEval[$clwsId]['elem_conjunctivaOdDrawing']=$res['elem_conjunctivaOdDrawing'];
			//$arrCLEval[$clwsId]['hdConjunctivaOdDrawingOriginal']=$res['hdConjunctivaOdDrawingOriginal'];
			//$arrCLEval[$clwsId]['elem_conjunctivaOdDrawingPath']=$res['elem_conjunctivaOdDrawingPath'];
			//$arrCLEval[$clwsId]['cornea_os_desc']=$res['cornea_os_desc'];
			//$arrCLEval[$clwsId]['elem_conjunctivaOsDrawing']=$res['elem_conjunctivaOsDrawing'];
			//$arrCLEval[$clwsId]['hdConjunctivaOsDrawingOriginal']=$res['hdConjunctivaOsDrawingOriginal'];
			//$arrCLEval[$clwsId]['elem_conjunctivaOsDrawingPath']=$res['elem_conjunctivaOsDrawingPath'];
			
			//$arrCLEval[$clwsId]['idoc_drawing_id_od']=$res['idoc_drawing_id_od'];
			//$arrCLEval[$clwsId]['idoc_drawing_id_os']=$res['idoc_drawing_id_os'];
	
			//CHEKING IF DRAWING DATA EXIST OR NOT
			$arrCLEval[$clwsId]['hasDrawing_od']='no';
			$arrCLEval[$clwsId]['hasDrawing_os']='no';
			if($res['idoc_drawing_id_od']>0){
				//$arrCLEval[$clwsId]['hasDrawing_od']='yes';
			}
			if($res['idoc_drawing_id_os']>0){
				//$arrCLEval[$clwsId]['hasDrawing_os']='yes';
			}	
		}
		unset($rs);
	}
	$commentArray = array();
	$sheetcount = 0;
	$sheetcount = $_REQUEST['sheetcount'];
	$clCommentsQuery = "select clc.id as comment_id, clc.cl_sheet_id as sheet_id, clc.comment as comment_desc from cl_comments clc 
	left join contactlensmaster clm on clc.cl_sheet_id=clm.clws_id where clm.patient_id='".$_SESSION['patient']."' and clc.delete_status='0'";
	if($mode=='copyfrom'){
		$clCommentsQuery.= " and clc.cl_sheet_id='".$current_clws_id."'";
	}else{
		$clCommentsQuery.= " and clc.cl_sheet_id='".$clws_id."'";
	}
	$clCommentsQuery.= " order by clc.id desc";
	$clCommentsResult = imw_query($clCommentsQuery) or die(imw_error()." - ".$clCommentsQuery);
	$newCommentDiv = "";
	$oldCommentDiv = "";
	while($clRow = imw_fetch_assoc($clCommentsResult)){
		$commentDesc = $clRow['comment_desc'];
		$commentId = $clRow['comment_id'];
		$sheetId = $clRow['sheet_id'];
		$commentArray[$sheetId][$commentId][] = $commentDesc;
	}
	echo json_encode(array('arrCLData'=>$arrCLData, 'arrCLEval'=>$arrCLEval, 'finalFound'=>$finalFound, 'arrCLComments' => $commentArray));	
}


if($mode=='getDDOptions'){
	$copyFromSheets='<option value="">Select Sheet</option>';
	$oldWorksheets.= '
	<option value=""> - Select WorkSheet - </option>
	<option value="undeleted">Latest 15 Undeleted Worksheets</option>
	<option value="deleted">Latest 15 Deleted Worksheets</option>
	<option value="all">Latest 15 Worksheets</option>';
	
	$AllMenuesqry="SELECT currentWorksheetid,clws_trial_number,clws_id,DATE_FORMAT( `dos`,'".get_sql_date_format()."') as PreviousDOS, 
	DATE_FORMAT( `clws_savedatetime`,'".get_sql_date_format()."') as savedDate, clws_type, form_id, del_status  
	FROM contactlensmaster where patient_id='".$_SESSION["patient"]."' ORDER BY form_id DESC, clws_id DESC";

	$resAllMenues=imw_query($AllMenuesqry);
	if($resAllMenues){
		$strOptionsALLMenues="";
		$numRows=imw_num_rows($resAllMenues);
		if($numRows>0){
			$temPcurrentWorksheetid=0;
			while($resRowALL=imw_fetch_assoc($resAllMenues)){
				$colorStyle='';
				
				$LabelsTrial=$resRowALL["clws_type"];
				$clws_types_arr = explode(",", $resRowALL["clws_type"]);
				
				if(in_array('Current Trial', $clws_types_arr)){
					$LabelsTrial= str_replace('Current Trial', 'Current Trial #'.$resRowALL["clws_trial_number"], $LabelsTrial);
				}
				
				if($oldFormId!=$resRowALL['form_id']){
					$oldWorksheets.= '<optgroup label="Sheets of DOS '.$resRowALL['PreviousDOS'].'">Sheets of DOS '.$resRowALL['PreviousDOS'].'</optgroup>';
					$copyFromSheets.= '<optgroup label="Sheets of DOS '.$resRowALL['PreviousDOS'].'">Sheets of DOS '.$resRowALL['PreviousDOS'].'</optgroup>';
				}
				if($resRowALL['del_status']==1) { $colorStyle='style="color:#F00";'; }
				
				$oldWorksheets.= '<option value="'.$resRowALL["clws_id"].'" '.$colorStyle.' >'.$resRowALL["savedDate"].' ('.$LabelsTrial.')&nbsp;</option>';
				$copyFromSheets.= '<option value="'.$resRowALL["clws_id"].'" '.$colorStyle.' >'.$resRowALL["savedDate"].' ('.$LabelsTrial.')&nbsp;</option>';
				
				$oldFormId=$resRowALL['form_id'];
			}unset($resRowALL);
		}
	}

	
	echo json_encode(array('oldWorksheets'=>$oldWorksheets, 'copyFromSheets'=>$copyFromSheets, 'commentsArray'=>$commentArray));
}

?>