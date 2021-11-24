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
$patient_id = $_SESSION['patient'];
$operatorName = $_SESSION['authUser'];
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/Fu.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");

if($a_p_dos!=""){
	$whr_dos="and cmt.date_of_service='$a_p_dos'";
}
$query="SELECT * FROM `chart_assessment_plans` as cap LEFT JOIN chart_master_table cmt ON cmt.id = cap.form_id LEFT JOIN chart_left_cc_history clch ON clch.form_id = cap.form_id WHERE cap.patient_id = '$patient_id' $whr_dos ORDER BY cmt.date_of_service DESC,cmt.id DESC LIMIT 0,1 ";
$sqlAssessQry =imw_query($query);
if(imw_num_rows($sqlAssessQry)==0 && $a_p_dos!=""){
	$query="SELECT * FROM `chart_assessment_plans` as cap LEFT JOIN chart_master_table cmt ON cmt.id = cap.form_id LEFT JOIN chart_left_cc_history clch ON clch.form_id = cap.form_id WHERE cap.patient_id = '$patient_id'  ORDER BY cmt.date_of_service DESC,cmt.id DESC LIMIT 0,1 ";
	$sqlAssessQry =imw_query($query);
}
if(imw_num_rows($sqlAssessQry)>0){
	$assessmentPlanPrint=true;
	$brd='border:1px solid; border-color:#A6C9DB;';
}else{
	$assessmentPlanPrint=false;
}
if($html_ob!=""){
	ob_start();
}
?>
<table cellpadding="0" cellspacing="1">
<?php 

	if($assessmentPlanPrint == true){           
		$sqlAssessRows = imw_fetch_assoc($sqlAssessQry);	
		extract($sqlAssessRows);
		$dat_exp_ap=explode('-',$date_of_service);
		$dat_final_ap=$dat_exp_ap['1'].'-'.$dat_exp_ap['2'].'-'.$dat_exp_ap['0'];
		?>
		<?php 		
			//New fields for assessment and plan				
			//Set Assess Plan Resolve NE Value FROM xml ---
			$strXml = stripslashes($sqlAssessRows["assess_plan"]);
			$oChartApXml = new ChartAP($patient_id,$form_id);
			//$arrApVals = $oChartApXml->getVal_Str($strXml);
			$arrApVals = $oChartApXml->getVal();
			$arrAp = $arrApVals["data"]["ap"];
			
			$lenAssess = count($arrAp);
			for($i=0;$i<$lenAssess;$i++){
				$j=$i+1;	
				$elem_assessment = "txt_assessment_".$j;
				$$elem_assessment = $arrAp[$i]["assessment"];
				$elem_plan = "ta_plan_notes_".$j;
				$$elem_plan = $arrAp[$i]["plan"];
				$elem_resolve = "check_plan_resolve_".$j;
				$$elem_resolve = $arrAp[$i]["resolve"];
				$no_change_Assess = "no_change_".$j;
				$$no_change_Assess = $arrAp[$i]["ne"];
			}
			//Set Assess Plan Resolve NE Value FROM xml ---

			?>		
			
			<tr>
				<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">
						<tr height="25px">
							<td colspan="3" valign="middle" class="text_10b" ><b>Assessment & Plans</b></td>
						</tr>
						<?php

						if($lenAssess>0){
							for($j=1,$i=0;$i<$lenAssess;$i++,$j++){
								$tmpNC = "no_change_".$j;
								$tmpPR = "check_plan_resolve_".$j;
								
								if($$tmpNC) {
									$$tmpNC = "<b>NE $j- </b>Ne Assessment & Plan $j";
								}
								
								if($$tmpPR){
									if($$tmpNC) {
										$$tmpNC = ", <b>RES $j- </b>Ne Assessment & Plan $j";
									}else{
										$$tmpNC = "<b>RES $j- </b>Ne Assessment & Plan $j";
									}
								}
						?>
							<tr>
								<td class="text_9" colspan="3" align="left" valign="top"><?php echo (!empty($$tmpNC)) ? $$tmpNC : ""; ?></td>
							</tr>	
						<?php	
							}
						}
						
						if($lenAssess>0){
							for($j=1,$i=0;$i<$lenAssess;$i++,$j++){
								$tmpAssess = "txt_assessment_".$j;
								$tmpPlan = "ta_plan_notes_".$j;
								if($$tmpAssess || $$tmpPlan){
									?>	
									<tr>
										<td class="text_9" align="left" valign="top" width="20"><b><?PHP echo $j."."; ?></b></td>
										<td class="text_9"  align="left" valign="top"><?php if($$tmpAssess) echo nl2br($$tmpAssess); else echo '&nbsp;'; ?></td>
										<td class="text_9"  align="left" valign="top"><?php if($$tmpPlan) echo nl2br($$tmpPlan); else echo '&nbsp;'; ?></td>
									</tr>
									<?php
								}
							}
						}
						?>
					</table>

				</td>
			</tr>
			<tr>
				<td><table width="100%" border="0" cellspacing="0" cellpadding="0" >
							<tr height="25px">
								<td valign="middle" class="text_10b" ><b>Follow Up</b></td><!--bgcolor="#c0c0c0"-->
							</tr>								
							<tr>
								<?php									
									$str_follow_up = "";									
									if(!empty($followup)){
										
										$fu_obj = new Fu($patient_id,$form_id);
										list($len_arrFu,$arrFu) = $fu_obj->fu_getXmlValsArr($followup);
										if(count($arrFu) > 0){
											foreach($arrFu as $val){
												
												$pronm ="";
												if(!empty($val["provider"])){
													if($val["provider"]=="Tech Only"){
														$pronm =$val["provider"];
													}else{
														$pronm =getUserFirstName($val["provider"],1);
													}
												}
												
												$str_follow_up .= trim($val["number"]." ".$val["time"]." ".$val["visit_type"]." ".$pronm)."<br>";
											}
										}
									}else if($follow_up_numeric_value) {
										$str_follow_up = $follow_up_numeric_value.' - '.$follow_up."-".$followUpVistType ; 
									}else{
										$str_follow_up = "&nbsp;";
									}
								?>	
									<td class="text_9" align="left" colspan="3" valign="top"><?php echo $str_follow_up; ?></td>
							</tr>
								<?php
								if($retina == 1 || $neuro_ophth == 1 || $id_precation == 1 || $rd_precation == 1 || $lid_scrubs_oint == 1 || $doctor_name != ''){
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
									if($doctor_name != ''){
										$doctor_name = '<b>Refer for consult: </b>'.$doctor_name;
									}
										if($plan_notes<>""){
											$retina=$retina."<br>Comments:".$plan_notes;
										}
									?>
									<tr>
										
										<td class="text_9" colspan="3" align="left" valign="top"><?php echo $retina.'<br>'.$doctor_name; ?></td>
									</tr>	
								<?php } ?>
					</table>				
				
				</td>
			</tr>
<?php 
	
}
?>	
</table>
<?php
if($html_ob!=""){
	$ass_div_data=ob_get_contents();
	ob_end_clean();
	ob_start();
		show_modal('AssesmentDiv','Assessment & Plans ('.$dat_final_ap.')',$ass_div_data,'','','modal-lg');
		$ass_div_modal=ob_get_contents();
	ob_end_clean();
	$ret_data['ass_plan_rg']=$ass_div_modal;
}
?>
