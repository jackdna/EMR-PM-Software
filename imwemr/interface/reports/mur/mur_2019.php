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
  FILE : scheduler_new_report.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
$objMUR			= new MUR_Reports;
$objMUR->mur_version = $mur_version;
$ptIDs = $objMUR->get_denominator($provider);

$commaptIDs = implode(',',$ptIDs);
if(!$commaptIDs || empty($commaptIDs)) {$commaptIDs = '0';}

$curr_EP_name_temp	= $objMUR->get_provider_ar($provider);
$curr_EP_name_arr	= explode(',',$curr_EP_name_temp[$provider]);
$curr_EP_name		= strtolower($curr_EP_name_arr[0]);
?>
<form name="frm_mur_report" id="frm_mur_report" action="mur/attestation_report_2019.php" method="post" target="_blank">
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
	
	$arr_SummaryCareRec2			= $objMUR->format_values($objMUR->SendSummaryCareRec($commaptIDs,'m2'),10,'nored');
	$arr_SummaryCareRec2['points'] 	= $objMUR->pointsMeasureWise('SummaryCareRec2',$objMUR->int_percent);

	$arr_MedReconcil			= $objMUR->format_values($objMUR->getMedReconcil_2018($commaptIDs),50);
	$arr_MedReconcil['points'] = $objMUR->pointsMeasureWise('MedRecon',$objMUR->int_percent);

	$arr_TimelyPtElectHlthInfo	= $objMUR->format_values($objMUR->getTimelyPtElectHlthInfo($commaptIDs),50); //pt.electronic access, measure1
	$arr_TimelyPtElectHlthInfo['points'] = $objMUR->pointsMeasureWise('PtViewAccess',$objMUR->int_percent);

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
        <th width="120">Anti-Numerator</th>
      </tr>
      </thead>
      <tbody>
<?php if($objMUR->taskNum==3){echo '<tr><th>MEASURE</th><th style="font-size:9px;">Actual/(Required)</th><th style="font-size:9px;" title="Percentage of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th><th class="bg4" align="center" style="font-size:9px;" title="Number of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th></tr>';}?>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">e-Prescribing</td>
        <td id="td_num_erx"><?php echo $arr_EPrescribe['numerator'];?></td>
        <td><?php echo $arr_EPrescribe['denominator'];?></td>
        <td><?php echo $arr_EPrescribe['percent'];?></td>
        <td><?php echo $arr_EPrescribe['anti'];?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Support Electronic Referral Loops by Sending Health Information</td>
        <td id="td_num_summofcare"><?php echo $arr_SummaryCareRec2['numerator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['denominator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['percent'];?></td>
        <td><?php echo $arr_SummaryCareRec2['anti'];?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Support Electronic Referral Loops by Receiving and Incorporating Health Information</td>
        <td id="td_num_summofcare"><?php echo $arr_MedReconcil['numerator'];?></td>
        <td><?php echo $arr_MedReconcil['denominator'];?></td>
        <td><?php echo $arr_MedReconcil['percent'];?></td>
        <td><?php echo $arr_MedReconcil['anti'];?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="text-left measure_name">Provide Patients Electronic Access to their Health Information</td>
        <td id="td_num_accessphi"><?php echo $arr_TimelyPtElectHlthInfo['numerator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['denominator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['percent'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['anti'];?></td>
      </tr>
       <tr>
        <td><div class="checkbox"><input type="checkbox" id="pirr" name="pirr" class="point_checkbox" /><label for="pirr"></label></div></td>
        <td class="text-left measure_name">Immunization Registry Reporting</td>
        <td id="td_pirr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="ecr" name="ecr" class="point_checkbox" /><label for="ecr"></label></div></td>
        <td class="text-left measure_name">Electronic Case Reporting</td>
        <td id="td_ecr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="phrr" name="phrr" class="point_checkbox" /><label for="phrr"></label></div></td>
        <td class="text-left measure_name">Public Health Registry Reporting</td>
        <td id="td_phrr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="bssr" name="bssr" class="point_checkbox" onclick="update_bonus_points()" /><label for="bssr"></label></div></td>
        <td class="text-left measure_name">Syndromic Surveillance Reporting</td>
        <td id="td_bssr">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="biris" name="biris" class="point_checkbox" onclick="update_bonus_points()" /><label for="biris"></label></div></td>
        <td class="text-left measure_name">Clinical Data Registry Reporting (IRIS)</td>
        <td id="td_biris">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div class="checkbox"><input type="checkbox" id="bsra" name="bsra" class="point_checkbox" onclick="update_basic_points()" /><label for="bsra"></label></div></td>
        <td class="text-left measure_name">Security Risk Analysis</td>
        <td id="td_bsra">NO</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      </tbody>
    </table>
    
<?php
}
?>
</form>