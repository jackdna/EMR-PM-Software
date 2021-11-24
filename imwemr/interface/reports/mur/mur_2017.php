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
  FILE : mur_2017.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
$objMUR			= new MUR_Reports;
$ptIDs = $objMUR->get_denominator($provider);
$commaptIDs = implode(', ',$ptIDs);
if(!$commaptIDs || empty($commaptIDs)) {$commaptIDs = '0';}

$curr_EP_name_temp	= $objMUR->get_provider_ar($provider);
$curr_EP_name_arr	= explode(',',$curr_EP_name_temp[$provider]);
$curr_EP_name		= strtolower($curr_EP_name_arr[0]);
?>
<form name="frm_mur_report" id="frm_mur_report" action="mur/attestation_report_2017.php" method="post" target="_blank">
<input type="hidden" name="providers" id="providers" value="<?php echo $provider;?>">
<input type="hidden" name="facility_id" id="facility_id" value="<?php echo $facility_id;?>">
<input type="hidden" name="dtfrom" id="dtfrom" value="<?php echo $dtfrom;?>">
<input type="hidden" name="dtupto" id="dtupto" value="<?php echo $dtupto;?>">
<input type="hidden" name="mur_version" id="mur_version" value="<?php echo $mur_version;?>">
<input type="hidden" name="task" id="task" value="<?php echo $objMUR->taskNum;?>">
<?php
if($objMUR->taskNum==1 || $objMUR->taskNum==3 || $objMUR->taskNum==4){
	$arr_EPrescribe 				= $objMUR->format_values($objMUR->getEPrescribe($commaptIDs),50,'noPt');
	$arr_EPrescribe['points'] 		= $objMUR->pointsMeasureWise('eRx',$objMUR->int_percent);
	
	$arr_SummaryCareRec2			= $objMUR->format_values($objMUR->SendSummaryCareRec2017($commaptIDs,'m2'),10,'nored');
	$arr_SummaryCareRec2['points'] 	= $objMUR->pointsMeasureWise('SummaryCareRec2',$objMUR->int_percent);
	
	$arr_TimelyPtElectHlthInfo	= $objMUR->format_values($objMUR->getTimelyPtElectHlthInfo($commaptIDs),50); //pt.electronic access, measure1
	$arr_TimelyPtElectHlthInfo['points'] = $objMUR->pointsMeasureWise('PtViewAccess',$objMUR->int_percent);

	$arr_MedReconcil			= $objMUR->format_values($objMUR->getMedReconcil($commaptIDs),50);
	$arr_MedReconcil['points'] = $objMUR->pointsMeasureWise('MedRecon',$objMUR->int_percent);

	$arr_TimelyPtElectHlthView	= $objMUR->format_values($objMUR->getTimelyPtInfoViewed($commaptIDs),5); //pt.electronic access, measure2
	$arr_TimelyPtElectHlthView['points'] = $objMUR->pointsMeasureWise('PtViewed',$objMUR->int_percent);
	
	$arr_EduResourceToPt		= $objMUR->format_values($objMUR->getEduResourceToPt($commaptIDs),10);
	$arr_EduResourceToPt['points'] = $objMUR->pointsMeasureWise('ptEduResource',$objMUR->int_percent);

	$arr_ptSecureMsg			= $objMUR->format_values($objMUR->getPatientSecureMessaging($commaptIDs),0);
	$arr_ptSecureMsg['points'] = $objMUR->pointsMeasureWise('SecureMess',$objMUR->int_percent);
	
	$total_performance_points = 0;
	?>   
    <table class="table table-bordered table-hover text-center">
      <thead class="bg-primary">
      <tr>
        <th width="25">&nbsp;</th>
        <th width="auto">&nbsp; Measure</th>
        <th width="95">Numerator</th>
        <th width="100">Denominator</th>
        <th width="90">Percentage</th>
        <!--<th width="90">Points</th>-->
      </tr>
      </thead>
      <tbody>
<?php if($objMUR->taskNum==3){echo '<tr><th>MEASURE</th><th style="font-size:9px;">Actual/(Required)</th><th style="font-size:9px;" title="Percentage of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th><th class="bg4" align="center" style="font-size:9px;" title="Number of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th></tr>';}?>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Base e-Prescribing (eRx)</td>
        <td id="td_num_erx"><?php echo $arr_EPrescribe['numerator'];?></td>
        <td><?php echo $arr_EPrescribe['denominator'];?></td>
        <td><?php echo $arr_EPrescribe['percent'];?></td>
        <!--<td><?php echo $arr_EPrescribe['points'];?></td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Base  Performance - Provide Summary of Care Record Electronically (HIE)</td>
        <td id="td_num_summofcare"><?php echo $arr_SummaryCareRec2['numerator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['denominator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['percent'];?></td>
        <!--<td><?php echo $arr_SummaryCareRec2['points']; $total_performance_points += intval($arr_SummaryCareRec2['points']);?></td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Base  Performance – Provide Patient Access to their PHI</td>
        <td id="td_num_accessphi"><?php echo $arr_TimelyPtElectHlthInfo['numerator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['denominator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['percent'];?></td>
        <!--<td><?php echo $arr_TimelyPtElectHlthInfo['points'];?></td>-->
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="bsra" name="bsra" class="point_checkbox" onclick="update_basic_points()" /><label for="bsra"></label></div></td>
        <td class="text-left measure_name">Base Security Risk Analysis</td>
        <td id="td_bsra">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <!--<td>NA</td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Performance Medication Reconciliation</td>
        <td id="td_num_medrecon"><?php echo $arr_MedReconcil['numerator'];?></td>
        <td><?php echo $arr_MedReconcil['denominator'];?></td>
        <td><?php echo $arr_MedReconcil['percent'];?></td>
        <!--<td><?php echo $arr_MedReconcil['points']; $total_performance_points +=intval($arr_MedReconcil['points']); ?></td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Performance  Patients View their Personal Health Information</td>
        <td id="td_num_viewphi"><?php echo $arr_TimelyPtElectHlthView['numerator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthView['denominator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthView['percent'];?></td>
        <!--<td><?php echo $arr_TimelyPtElectHlthView['points']; $total_performance_points += intval($arr_TimelyPtElectHlthView['points']);?></td>-->
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="pirr" name="pirr" class="point_checkbox" /><label for="pirr"></label></div></td>
        <td class="text-left measure_name">Performance Immunization Registry Reporting</td>
        <td id="td_pirr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <!--<td>NA</td>-->
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="bssr" name="bssr" class="point_checkbox" onclick="update_bonus_points()" /><label for="bssr"></label></div></td>
        <td class="text-left measure_name">Bonus Syndromic Surveillance Reporting</td>
        <td id="td_bssr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <!--<td>NA</td>-->
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="biris" name="biris" class="point_checkbox" onclick="update_bonus_points()" /><label for="biris"></label></div></td>
        <td class="text-left measure_name">Bonus IRIS Reporting</td>
        <td id="td_biris">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <!--<td>NA</td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Performance Patient Specific Education</td>
        <td id="td_num_ptedu"><?php echo $arr_EduResourceToPt['numerator'];?></td>
        <td><?php echo $arr_EduResourceToPt['denominator'];?></td>
        <td><?php echo $arr_EduResourceToPt['percent'];?></td>
        <!--<td><?php echo $arr_EduResourceToPt['points']; $total_performance_points += intval($arr_EduResourceToPt['points']);?></td>-->
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Performance Secure Messaging</td>
        <td id="td_num_secumsg"><?php echo $arr_ptSecureMsg['numerator'];?></td>
        <td><?php echo $arr_ptSecureMsg['denominator'];?></td>
        <td><?php echo $arr_ptSecureMsg['percent'];?></td>
        <!--<td><?php echo $arr_ptSecureMsg['points']; $total_performance_points += intval($arr_ptSecureMsg['points']);?></td>-->
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="iatuc" name="iatuc" class="point_checkbox" onclick="update_bonus_points()" /><label for="iatuc"></label></div></td>
        <td class="text-left measure_name">Bonus Report Improvement Activities That Utilized CEHRT</td>
        <td id="td_iatuc">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <!--<td>NA</td>-->
      </tr>
      <!--<tr>
        <td class="text-right" colspan="5">BASIC SCORE (50 POINTS MAX)</td>
        <td id="td_basic_socre">0</td>
      </tr>
      <tr>
        <td class="text-right" colspan="5">PERFORMANCE SCORE (80 POINTS MAX)</td>
        <td id="td_performance_score">0</td>
      </tr>
      <tr>
        <td class="text-right" colspan="5">BONUS POINTS (15 POINTS MAXIMUM)</td>
        <td id="td_bonus_points">0</td>
      </tr>
      <tr>
        <td class="text-right" colspan="5">TOTAL ACI SCORE (MAXIMUM 155)</td>
        <td id="td_aci_score">0</td>
      </tr>
      <tr>
        <td class="text-right" colspan="5">CONTRIBUTION TO TOTAL MIPS SCORE (25 MAXIMUM)</td>
        <td id="td_mips_score">0</td>
      </tr>-->
      </tbody>
    </table>
    
<?php
}
?>
</form>