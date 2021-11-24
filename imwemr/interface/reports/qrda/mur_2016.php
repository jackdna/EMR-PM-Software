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

$_REQUEST['task'] = '2';

include_once(dirname(__FILE__) . "/class.mur_reports.php");
$objMUR = new MUR_Reports;
$task = intval($objMUR->taskNum);

$ptIDs = $objMUR->get_denominator($provider);
$commaptIDs = implode(', ', $ptIDs);
if (!$commaptIDs || empty($commaptIDs)) {
    $commaptIDs = '0';
}
?>
<form name="frm_mur_report" action="">
    <input type="hidden" name="providers" id="providers" value="<?php echo $provider; ?>">
    <input type="hidden" name="dtfrom" id="dtfrom" value="<?php echo $dtfrom; ?>">
    <input type="hidden" name="dtupto" id="dtupto" value="<?php echo $dtupto; ?>">
    <input type="hidden" name="task" id="task" value="2">
<?php

    $objMUR->updateCQMpatients('empty');
    /*     * ****CMS- CLINICAL CORE************************ */
    
    $NQF0018 = $objMUR->getNQF0018();
    $arr_NQF0018 = $objMUR->format_values($NQF0018);
    $objMUR->updateCQMpatients('update', 'NQF0018', $NQF0018['ipop']);
	$arr_NQF0018_check = ($NQF0018['ipop'] > 0 && empty($NQF0018['ipop']) == false) ? '' : 'disabled';

    $NQF0022 = $objMUR->getNQF0022('one');
    $arr_NQF0022 = $objMUR->format_values($NQF0022);
    $objMUR->updateCQMpatients('update', 'NQF0022a', $NQF0022['ipop']);
	$arr_NQF0022_check = ($NQF0022['ipop'] > 0 && empty($NQF0022['ipop']) == false) ? '' : 'disabled';

    $NQF0022b = $objMUR->getNQF0022('two');
    $arr_NQF0022b = $objMUR->format_values($NQF0022b);
    $objMUR->updateCQMpatients('update', 'NQF0022b', $NQF0022b['ipop']);
	$arr_NQF0022b_check = ($NQF0022b['ipop'] > 0 && empty($NQF0022b['ipop']) == false) ? '' : 'disabled';
    
    $NQF0421a = $objMUR->getNQF0421a();
    $arr_NQF0421 = $objMUR->format_values($NQF0421a);
    $objMUR->updateCQMpatients('update', 'NQF0421a', $NQF0421a['ipop']);
	$arr_NQF0421_check = ($NQF0421a['ipop'] > 0 && empty($NQF0421a['ipop']) == false) ? '' : 'disabled';

    $NQF0421b = $objMUR->getNQF0421b();
    $arr_NQF0421b = $objMUR->format_values($NQF0421b);
    $objMUR->updateCQMpatients('update', 'NQF0421b', $NQF0421b['ipop']);
	$arr_NQF0421b_check = ($NQF0421b['ipop'] > 0 && empty($NQF0421b['ipop']) == false) ? '' : 'disabled';
    
    $NQF0028 = $objMUR->getNQF0028('one');
    $arr_NQF0028 = $objMUR->format_values($NQF0028);
    $objMUR->updateCQMpatients('update', 'NQF0028a', $NQF0028['ipop']);
	$arr_NQF0028_check = ($NQF0028['ipop'] > 0 && empty($NQF0028['ipop']) == false) ? '' : 'disabled';
    
    $NQF0028b = $objMUR->getNQF0028('two');
    $arr_NQF0028b = $objMUR->format_values($NQF0028b);
    $objMUR->updateCQMpatients('update', 'NQF0028b', $NQF0028b['ipop']);
	$arr_NQF0028b_check = ($NQF0028b['ipop'] > 0 && empty($NQF0028b['ipop']) == false) ? '' : 'disabled';
    
    $NQF0028c = $objMUR->getNQF0028('three');
    $arr_NQF0028c = $objMUR->format_values($NQF0028c);
    $objMUR->updateCQMpatients('update', 'NQF0028c', $NQF0028c['ipop']);
	$arr_NQF0028c_check = ($NQF0028c['ipop'] > 0 && empty($NQF0028c['ipop']) == false) ? '' : 'disabled';

//    $RefLoop = $objMUR->getRefLoop();
//    $arr_RefLoop = $objMUR->format_values($RefLoop);
//    $objMUR->updateCQMpatients('update', 'CMS50v2', $RefLoop['ipop']);

    $NQF0052 = $objMUR->getNQF0052();
    $arr_NQF0052 = $objMUR->format_values($NQF0052);
    $objMUR->updateCQMpatients('update', 'NQF0052', $NQF0052['ipop']);
    $arr_NQF0052_check = ($NQF0052['ipop'] > 0 && empty($NQF0052['ipop']) == false) ? '' : 'disabled';
	
    /*     * ****CMS- QUALITY MEASURES ************************ */
    $NQF0086 = $objMUR->getNQF0086();
    $arr_NQF0086 = $objMUR->format_values($NQF0086);
    $objMUR->updateCQMpatients('update', 'NQF0086', $NQF0086['ipop']);
	$arr_NQF0086_check = ($NQF0086['ipop'] > 0 && empty($NQF0086['ipop']) == false) ? '' : 'disabled';

    $NQF0088 = $objMUR->getNQF0088();
    $arr_NQF0088 = $objMUR->format_values($NQF0088);
    $objMUR->updateCQMpatients('update', 'NQF0088', $NQF0088['ipop']);
	$arr_NQF0088_check = ($NQF0088['ipop'] > 0 && empty($NQF0088['ipop']) == false) ? '' : 'disabled';

    $NQF0089 = $objMUR->getNQF0089();
    $arr_NQF0089 = $objMUR->format_values($NQF0089);
    $objMUR->updateCQMpatients('update', 'NQF0089', $NQF0089['ipop']);
	$arr_NQF0089_check = ($NQF0089['ipop'] > 0 && empty($NQF0089['ipop']) == false) ? '' : 'disabled';

    $NQF0055 = $objMUR->getNQF0055();
    $arr_NQF0055 = $objMUR->format_values($NQF0055);
    $objMUR->updateCQMpatients('update', 'NQF0055', $NQF0055['ipop']);
	$arr_NQF0055_check = ($NQF0055['ipop'] > 0 && empty($NQF0055['ipop']) == false) ? '' : 'disabled';
    
    
    /*New Measures 2017*/
    $NQF0565 = $objMUR->getNQF0565();
    $arr_NQF0565 = $objMUR->format_values($NQF0565);
    $objMUR->updateCQMpatients('update', 'NQF0565', $NQF0565['denominator']);
    $arr_NQF0565_check = ($NQF0565['ipop'] > 0 && empty($NQF0565['ipop']) == false) ? '' : 'disabled';
    
    $NQF0564 = $objMUR->getNQF0564();
    $arr_NQF0564 = $objMUR->format_values($NQF0564);
    $objMUR->updateCQMpatients('update', 'NQF0564', $NQF0564['ipop']);
    $arr_NQF0564_check = ($NQF0564['ipop'] > 0 && empty($NQF0564['ipop']) == false) ? '' : 'disabled';
    
    $NQF0419 = $objMUR->getNQF0419();
    $arr_NQF0419 = $objMUR->format_values($NQF0419);
    $objMUR->updateCQMpatients('update', 'NQF0419', $NQF0419['ipop']);
    $arr_NQF0419_check = ($NQF0419['ipop'] > 0 && empty($NQF0419['ipop']) == false) ? '' : 'disabled';
    
?>
    <table class="table table-bordered table-hover">
	<thead class="bg-primary">
	    <tr>
		<td width="50" style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="selectAllNQF" onClick="selectAllCQM()" />
			<label for="selectAllNQF" style="padding:0;"></label>
		    </div>
		</td>
		<td width="90" valign="top">&nbsp;</td>
		<td width="90" valign="top">&nbsp; CMS ID</td>
		<th width="auto" align="left"> &nbsp; Measure Name</th>
		<th width="120">Initial Population</th>
		<th width="100">Denominator</th>
		<th width="168">Denominator Exclusions</th>
		<th width="95">Numerator</th>				
		<th width="168">Denominator Exceptions</th>
	    </tr>
	</thead>
	<tbody>
	    <tr>
		<th class="bg-info" colspan="9">CLINICAL QUALITY MEASURE</th>
	    </tr>	  
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0018" class="nqfchkbx" value="NQF0018" <?php echo $arr_NQF0018_check; ?>/>
			<label for="nqf0018" style="padding:0;"></label>
		    </div>
		</td>
		<td valign="top" align="left"><b>NQF 0018</b></td>
		<td valign="top" align="left"><b>CMS165v6</b></td>
		<td align="left">Controlling High Blood Pressure</td>
		<!--<td><?php echo $arr_NQF0018['percent']; ?></td>-->
		<td><?php echo $arr_NQF0018['ipop']; ?></td>
		<td><?php echo $arr_NQF0018['denominator']; ?></td>
		<td><?php echo $arr_NQF0018['exclusion']; ?></td>
		<td><?php echo $arr_NQF0018['numerator']; ?></td>
		<td>None<?php // echo $arr_NQF0018['denominatorException']; ?></td>
	    </tr>
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0022" class="nqfchkbx" value="NQF0022" <?php echo $arr_NQF0022_check; ?>/>
			<label for="nqf0022" style="padding:0;"></label>
		    </div>
		</td>
		<td valign="top" align="left"><b>NQF 0022</b></td>
		<td valign="top" align="left"><b>CMS156v6</b></td>
		<td align="left">Use of High-Risk Medications in the Elderly</td>
		<td class="bg1">&nbsp;</td>
		<td class="bg1">&nbsp;</td>
		<td class="bg1">&nbsp;</td>
		<td class="bg1">&nbsp;</td>
	    </tr>
	    <tr>
		<td>&nbsp;</td>
		<td valign="top" align="left">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp; &nbsp; &#10157 Measure I (1+)</td>
		<td><?php echo $arr_NQF0022['ipop']; ?></td>
		<td><?php echo $arr_NQF0022['denominator']; ?></td>
		<td><?php echo $arr_NQF0022['exclusion']; ?></td>
		<td><?php echo $arr_NQF0022['numerator']; ?></td>
		<td>None<?php //echo $arr_NQF0022['denominatorException']; ?></td>
	    </tr>
	    <tr>
		<td>&nbsp;</td>
		<td valign="top" align="left">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp; &nbsp; &#10157 Measure II (2+)</td>
		<td><?php echo $arr_NQF0022b['ipop']; ?></td>
		<td><?php echo $arr_NQF0022b['denominator']; ?></td>
		<td><?php echo $arr_NQF0022b['exclusion']; ?></td>
		<td><?php echo $arr_NQF0022b['numerator']; ?></td>
		<td>None<?php //echo $arr_NQF0022b['denominatorException']; ?></td>
	    </tr>
  <!--          <tr>
	      <td valign="top" align="left"><b>NQF 0421</b></td>
	      <td align="left">Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up</a></td>
	      <td class="bg1">&nbsp;</td>
	      <td class="bg1">&nbsp;</td>
	      <td class="bg1">&nbsp;</td>
	      <td class="bg1">&nbsp;</td>
	    </tr>-->
  <!--          <tr>
	      <td valign="top" align="left">&nbsp;</td>
	      <td align="left">&nbsp; &nbsp; &#10157 BMI Screening &amp; Follow Up: 65+</td>
	      <td><?php echo $arr_NQF0421['percent']; ?></td>
	      <td><?php echo $arr_NQF0421['denominator']; ?></td>
	      <td><?php echo $arr_NQF0421['numerator']; ?></td>
	      <td><?php echo $arr_NQF0421['exclusion']; ?></td>
	    </tr>-->
  <!--          <tr>
	      <td valign="top" align="left">&nbsp;</td>
	      <td align="left">&nbsp; &nbsp; &#10157 BMI Screening &amp; Follow Up: 18-64</td>
	      <td><?php echo $arr_NQF0421b['percent']; ?></td>
	      <td><?php echo $arr_NQF0421b['denominator']; ?></td>
	      <td><?php echo $arr_NQF0421b['numerator']; ?></td>
	      <td><?php echo $arr_NQF0421b['exclusion']; ?></td>
	    </tr>-->
  <!--          <tr>
	      <td valign="top" align="left"><b>CMS50v2</b></td>
	      <td align="left">Closing the Referral Loop: Receipt of specialist report</td>
	      <td><?php echo $arr_RefLoop['percent']; ?></td>
	      <td><?php echo $arr_RefLoop['denominator']; ?></td>
	      <td><?php echo $arr_RefLoop['numerator']; ?></td>
	      <td><?php echo $arr_RefLoop['exclusion']; ?></td>
	    </tr>-->
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0028" class="nqfchkbx" value="NQF0028" <?php echo $arr_NQF0028_check; ?>/>
			<label for="nqf0028" style="padding:0;"></label>
		    </div>
		</td>
		<td align="left"><b>NQF 0028</b></td>
		<td align="left"><b>CMS138v6</b></td>
		<td align="left">Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	    </tr>
	    
	    <tr>
		<td>&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp; &nbsp; &#10157 Measure I (1+)</td>
		<td><?php echo $arr_NQF0028['ipop']; ?></td>
		<td><?php echo $arr_NQF0028['denominator']; ?></td>
		<td>None<?php //echo $arr_NQF0028['exclusion']; ?></td>
		<td><?php echo $arr_NQF0028['numerator']; ?></td>
		<td><?php echo $arr_NQF0028['denominatorException']; ?></td>
	    </tr>
	    
	    <tr>
		<td>&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp; &nbsp; &#10157 Measure II (2+)</td>
		<td><?php echo $arr_NQF0028b['ipop']; ?></td>
		<td><?php echo $arr_NQF0028b['denominator']; ?></td>
		<td>None<?php //echo $arr_NQF0028b['exclusion']; ?></td>
		<td><?php echo $arr_NQF0028b['numerator']; ?></td>
		<td><?php echo $arr_NQF0028b['denominatorException']; ?></td>
	    </tr>
	    
	    <tr>
		<td>&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp; &nbsp; &#10157 Measure III (3+)</td>
		<td><?php echo $arr_NQF0028c['ipop']; ?></td>
		<td><?php echo $arr_NQF0028c['denominator']; ?></td>
		<td>None<?php //echo $arr_NQF0028c['exclusion']; ?></td>
		<td><?php echo $arr_NQF0028c['numerator']; ?></td>
		<td><?php echo $arr_NQF0028c['denominatorException']; ?></td>
	    </tr>
	    
  <!--          <tr>
	      <td valign="top" align="left"><b>NQF 0052</b></td>
	      <td align="left">Use of Imaging Studies for Low Back Pain</td>
	      <td><?php echo $arr_NQF0052['percent']; ?></td>
	      <td><?php echo $arr_NQF0052['denominator']; ?></td>
	      <td><?php echo $arr_NQF0052['numerator']; ?></td>
	      <td><?php echo $arr_NQF0052['exclusion']; ?></td>
	    </tr>-->

	    <!--New Entries in 2017-->
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0565" class="nqfchkbx" value="NQF0565" <?php echo $arr_NQF0565_check; ?>/>
			<label for="nqf0565" style="padding:0;"></label>
		    </div>
		</td>
		<td align="left"><b>NQF 0565</b></td>
		<td align="left"><b>CMS133v6</b></td>
		<td align="left">Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery</td>
		<td><?php echo $arr_NQF0565['ipop']; ?></td>
		<td><?php echo $arr_NQF0565['denominator']; ?></td>
		<td><?php echo $arr_NQF0565['exclusion']; ?></td>
		<td><?php echo $arr_NQF0565['numerator']; ?></td>
		<td>None<?php //echo $arr_NQF0565['denominatorException']; ?></td>
	    </tr>

	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0564" class="nqfchkbx" value="NQF0564" <?php echo $arr_NQF0564_check; ?>/>
			<label for="nqf0564" style="padding:0;"></label>
		    </div>
		</td>
		<td align="left"><b>NQF 0564</b></td>
		<td align="left"><b>CMS132v6</b></td>
		<td align="left">Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures</td>
		<td><?php echo $arr_NQF0564['ipop']; ?></td>
		<td><?php echo $arr_NQF0564['denominator']; ?></td>
		<td><?php echo $arr_NQF0564['exclusion']; ?></td>
		<td><?php echo $arr_NQF0564['numerator']; ?></td>
		<td>None<?php //echo $arr_NQF0564['denominatorException']; ?></td>
	    </tr>

	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0419" class="nqfchkbx" value="NQF0419" <?php echo $arr_NQF0419_check; ?>/>
			<label for="nqf0419" style="padding:0;"></label>
		    </div>
		</td>
		<td align="left"><b>NQF 0419</b></td>
		<td align="left"><b>CMS68v7</b></td>
		<td align="left">Documentation of Current Medications in the Medical Record</td>
		<td><?php echo $arr_NQF0419['ipop'] ?></td>
		<td><?php echo $arr_NQF0419['denominator'] ?></td>
		<td>None<?php //echo $arr_NQF419['exclusion'] ?></td>
		<td><?php echo $arr_NQF0419['numerator'] ?></td>
		<td><?php echo $arr_NQF0419['denominatorException'] ?></td>
	    </tr>
	    <!--End New Entries in 2017--> 

	    <tr><th colspan="9" class="bg-info">CLINICAL QUALITY MEASURE - OPTHALMOLOGY</th></tr>	  
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0086" class="nqfchkbx" value="NQF0086" <?php echo $arr_NQF0086_check; ?>/>
			<label for="nqf0086" style="padding:0;"></label>
		    </div>
		</td>
		<td valign="top" align="left"><b>NQF 0086</b></td>
		<td valign="top" align="left"><b>CMS143v6</b></td>
		<td align="left">Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation</td>
		<!--<td><?php echo $arr_NQF0086['percent']; ?></td>-->
		<td><?php echo $arr_NQF0086['ipop']; ?></td>
		<td><?php echo $arr_NQF0086['denominator']; ?></td>
		<td>None<?php //echo $arr_NQF0086['exclusion']; ?></td>
		<td><?php echo $arr_NQF0086['numerator']; ?></td>
		<td><?php echo $arr_NQF0086['denominatorException']; ?></td>
	    </tr>
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0088" class="nqfchkbx" value="NQF0088" <?php echo $arr_NQF0088_check; ?>/>
			<label for="nqf0088" style="padding:0;"></label>
		    </div>
		</td>
		<td valign="top" align="left"><b>NQF 0088</b></td>
		<td valign="top" align="left"><b>CMS167v6</b></td>
		<td align="left">Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema and Level of Severity of Retinopathy</td>
		<!--<td><?php echo $arr_NQF0088['percent']; ?></td>-->
		<td><?php echo $arr_NQF0088['ipop']; ?></td>
		<td><?php echo $arr_NQF0088['denominator']; ?></td>
		<td>None<?php // echo $arr_NQF0088['exclusion']; ?></td>
		<td><?php echo $arr_NQF0088['numerator']; ?></td>
		<td><?php echo $arr_NQF0088['denominatorException']; ?></td>
	    </tr>
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0089" class="nqfchkbx" value="NQF0089" <?php echo $arr_NQF0089_check; ?>/>
			<label for="nqf0089" style="padding:0;"></label>
		    </div>
		</td>
		<td align="left"><b>NQF 0089</b></td>
		<td valign="top" align="left"><b>CMS142v6</b></td>
		<td align="left">Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</td>
		<!--<td><?php echo $arr_NQF0089['percent']; ?></td>-->
		<td><?php echo $arr_NQF0089['ipop']; ?></td>
		<td><?php echo $arr_NQF0089['denominator']; ?></td>
		<td>None<?php // echo $arr_NQF0089['exclusion']; ?></td>
		<td><?php echo $arr_NQF0089['numerator']; ?></td>
		<td><?php echo $arr_NQF0089['denominatorException']; ?></td>
	    </tr>
	    <tr>
		<td style="text-align: center;">
		    <div class="checkbox">
			<input type="checkbox" id="nqf0055" class="nqfchkbx" value="NQF0055" <?php echo $arr_NQF0055_check; ?>/>
			<label for="nqf0055" style="padding:0;"></label>
		    </div>
		</td>
		<td valign="top" align="left"><b>NQF 0055</b></td>
		<td valign="top" align="left"><b>CMS131v6</b></td>
		<td align="left">Diabetes: Eye Exam</td>
		<!--<td><?php echo $arr_NQF0055['percent']; ?></td>-->
		<td><?php echo $arr_NQF0055['ipop']; ?></td>
		<td><?php echo $arr_NQF0055['denominator']; ?></td>
		<td><?php echo $arr_NQF0055['exclusion']; ?></td>
		<td><?php echo $arr_NQF0055['numerator']; ?></td>
		<td>None<?php // echo $arr_NQF0055['denominatorException']; ?></td>
	    </tr>
	</tbody>
    </table>
</form>