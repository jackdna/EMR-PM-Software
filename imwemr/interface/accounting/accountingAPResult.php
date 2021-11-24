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
$title = "A&amp;P"; 
require_once('acc_header.php'); 
$showFooter=true;
######scheduler call specific settings start #####
if(trim($_REQUEST['scheduler_call']))
{
	$showFooter=false;
}
######scheduler call specific settings end here#####
//require_once('acc_header.php');  
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/Fu.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");

$patient_id = $_SESSION['patient'];
if($_REQUEST['ids']){
	$imp_ids="'";
	$imp_ids.=implode("','",$_REQUEST['ids']);
	$imp_ids.="'";
	$whr_dos="and cmt.date_of_service in($imp_ids)";
}
if($_REQUEST['dat_id']){
	$dat_id=$_REQUEST['dat_id'];
	$whr_dos="and cmt.date_of_service in('$dat_id')";
}
$query="SELECT *, cmt.date_of_service as DOS, cap.form_id as frm_id FROM `chart_assessment_plans` as cap 
		LEFT JOIN chart_master_table cmt ON cmt.id = cap.form_id 
		LEFT JOIN chart_left_cc_history clch ON clch.form_id = cap.form_id 
		WHERE cap.patient_id = '$patient_id' $whr_dos ORDER BY cmt.date_of_service DESC,cmt.id DESC";
$sqlAssessQry =imw_query($query) or die(imw_error()) ;
if(imw_num_rows($sqlAssessQry)>0){
	$assessmentPlanPrint=true;
}else{
	$assessmentPlanPrint=false;
}
//print  $patient_id  .$assessmentPlanPrint;
?>
<div class="table-responsive" style="height:365px; overflow:auto; width:100%;">
        <?php 
        if($assessmentPlanPrint == true){
            while($sqlAssessRows = imw_fetch_assoc($sqlAssessQry)){
                $dat_exp_ap=explode('-',$sqlAssessRows['DOS']);
                $dat_final_ap=$dat_exp_ap['1'].'-'.$dat_exp_ap['2'].'-'.$dat_exp_ap['0'];
        ?>
        <table class="table table-bordered table-hover table-striped">
            <tr class="purple_bar text-center"> 
                <td colspan="3">Assessment & Plans (<?php echo date(phpDateFormat(), strtotime($dat_exp_ap['2'].'-'.$dat_exp_ap['1'].'-'.$dat_exp_ap['0'])); ?>)
                </td>
            </tr>
        <?php 		
            //New fields for assessment and plan				
            //Set Assess Plan Resolve NE Value FROM xml ---
            $strXml = stripslashes($sqlAssessRows["assess_plan"]);
            $oChartApXml = new ChartAP($patient_id,$sqlAssessRows["frm_id"]);
            //$arrApVals = $oChartApXml->getVal_Str($strXml);
	    $arrApVals = $oChartApXml->getVal();
            $arrAp = $arrApVals["data"]["ap"];
            //print_r($arrAp);
            
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
            
            <tr class="grythead">
                <th colspan="3" style="text-align:left !important;">Assessment & Plans2</th>
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
                    if(!empty($$tmpNC)){
                ?>
                    <tr>
                        <td colspan="3"><?php echo (!empty($$tmpNC)) ? $$tmpNC : ""; ?></td>
                    </tr>	
                <?php
                    }
                }
            }
            
            if($lenAssess>0){
                for($j=1,$i=0;$i<$lenAssess;$i++,$j++){
                    $tmpAssess = "txt_assessment_".$j;
                    $tmpPlan = "ta_plan_notes_".$j;
                    if($$tmpAssess || $$tmpPlan){
            ?>
                        <tr>
                            <td style="width:5%;"><?php echo $j."."; ?></td>
                            <td style="width:40%;"><?php if($$tmpAssess) echo nl2br($$tmpAssess); else echo '&nbsp;'; ?></td>
                            <td><?php if($$tmpPlan) echo nl2br(preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,"#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$$tmpPlan)); else echo '&nbsp;'; ?></td>
                        </tr>
            <?php
                    }
                }
            }
            ?>
        </table>	
        <table class="table table-bordered table-hover table-striped">
            <tr class="grythead">
                <th style="text-align:left !important;" colspan="3">Follow Up</th>
            </tr>
            <?php									
            $str_follow_up = "";	
            $followup=$sqlAssessRows['followup'];								
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
            <tr>	
                <td colspan="3"><?php echo $str_follow_up; ?></td>
            </tr>
            <?php
	    $plan_notes = $sqlAssessRows['plan_notes'];
	    $commentsForPatient = $sqlAssessRows['commentsForPatient'];
            if($retina == 1 || $neuro_ophth == 1 || $id_precation == 1 || $rd_precation == 1 || $lid_scrubs_oint == 1 || $doctor_name != '' || !empty($plan_notes) || !empty($commentsForPatient)){
                if($retina) $retina = 'Retina';									
                if($neuro_ophth){
                    if($retina) $retina.=', Neuro Ophth';
                    else $retina = 'Neuro Ophth';
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
		    $artmp = explode("~||~", $plan_notes);	
		    $plan_notes = $artmp[0];	
                    $retina=$retina."<br>Comments:".$plan_notes;
		    if(!empty($artmp[1])){ $retina.= " Date: ".$artmp[1].""; }
                }else if(!empty($commentsForPatient)){
			$artmp = explode("~||~", $commentsForPatient);
			$commentsForPatient = $artmp[0];
			$retina=$retina."<br>Comments:".$commentsForPatient;
			if(!empty($artmp[1])){ $retina.= " Date: ".$artmp[1].""; }
		}
            ?>
                <tr>
                    <td colspan="3"><?php echo $retina.'<br>'.$doctor_name; ?></td>
                </tr>	
            <?php } ?>
        </table>	
        <?php 
        }			
    }
    ?>
    </div>
	<?php if($showFooter==true){?>
    <footer>
        <div class="text-center" id="module_buttons">
            <input type="button" id="back" class="btn btn-success" value="Back"  onClick="javascript:history.go(-1);">
            <input type="button" id="cancel" class="btn btn-danger" value="Cancel"  onClick="window.close();">
        </div>
    </footer>
    <?php }?>
</body>
</html>	