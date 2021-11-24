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
$task			= intval($objMUR->taskNum);

$ptIDs = $objMUR->get_denominator($provider);
$commaptIDs = implode(', ',$ptIDs);
if(!$commaptIDs || empty($commaptIDs)) {$commaptIDs = '0';}
?>
<form name="frm_mur_report" id="frm_mur_report" action="mur/attestation_report_2016.php" method="post" target="_blank">
<input type="hidden" name="providers" id="providers" value="<?php echo $provider;?>">
<input type="hidden" name="facility_id" id="facility_id" value="<?php echo $facility_id;?>">
<input type="hidden" name="dtfrom" id="dtfrom" value="<?php echo $dtfrom;?>">
<input type="hidden" name="dtupto" id="dtupto" value="<?php echo $dtupto;?>">
<input type="hidden" name="mur_version" id="mur_version" value="<?php echo $mur_version;?>">
<input type="hidden" name="task" id="task" value="<?php echo $task;?>">
<?php

if($task==1 || $task==3){
	//CORE (Cal-Core/Menu)
	$getCPOE_Med 				= $objMUR->format_values($objMUR->getCPOE('Meds',$commaptIDs),60,'noPt');
	$getCPOE_Med_I 				= $objMUR->format_values($objMUR->getCPOE('Meds',$commaptIDs),30,'noPt');
	$getCPOE_Img 				= $objMUR->format_values($objMUR->getCPOE('Imaging/Rad'),30,'noPt');
	$getCPOE_Lab 				= $objMUR->format_values($objMUR->getCPOE('Labs'),30,'noPt');
	$arr_EPrescribe 			= $objMUR->format_values($objMUR->getEPrescribe($commaptIDs),50,'noPt');
	$arr_TimelyPtElectHlthInfo	= $objMUR->format_values($objMUR->getTimelyPtElectHlthInfo($commaptIDs),50); //pt.electronic access, measure1
	$arr_TimelyPtElectHlthView	= $objMUR->format_values($objMUR->getTimelyPtInfoViewed($commaptIDs),5,'FAIL'); //pt.electronic access, measure2
	$arr_EduResourceToPt		= $objMUR->format_values($objMUR->getEduResourceToPt($commaptIDs),10);		
	$arr_MedReconcil			= $objMUR->format_values($objMUR->getMedReconcil($commaptIDs),50);
	$arr_SummaryCareRec1		= $objMUR->format_values($objMUR->getSummaryCareRec2016($commaptIDs,'m1'),50,'nored');
	$arr_SummaryCareRec2		= $objMUR->format_values($objMUR->getSummaryCareRec2016($commaptIDs,'m2'),10,'nored');
	$arr_ptSecureMsg			= $objMUR->format_values($objMUR->getPatientSecureMessaging($commaptIDs),0,'FAIL');
	?>
    <table class="table table-bordered table-hover">
      <thead class="bg-primary">
      <tr>
        <th width="auto"> &nbsp; Measure</th>
        <th width="100">Stage I</th>
        <th width="100">Stage II</th>
        <th width="100">Denominator</th>
        <th width="95">Numerator</th>
        <th width="90">Exclusion</th>
      </tr>
      </thead>
      <tbody>
<?php if($task==3){echo '<tr><th>MEASURE</th><th style="font-size:9px;">Actual/(Required)</th><th style="font-size:9px;" title="Percentage of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th><th class="bg4" align="center" style="font-size:9px;" title="Number of ptients you require more to achieve the measure threshold">Actual/(Required)</th><th class="bg4">&nbsp;</th></tr>';}?>
      <tr class="bg-info">
        <td>Computerized Provider Order Entry (CPOE)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Medication</td>
        <td><?php echo $getCPOE_Med_I['percent'];?></td>
        <td><?php echo $getCPOE_Med['percent'];?></td>
        <td><?php echo $getCPOE_Med['denominator'];?></td>
        <td><?php echo $getCPOE_Med['numerator'];?></td>
        <td><?php echo $getCPOE_Med['exclusion'];?></td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Imaging</td>
        <td>Optional</td>
        <td><?php echo $getCPOE_Img['percent'];?></td>
        <td><?php echo $getCPOE_Img['denominator'];?></td>
        <td><?php echo $getCPOE_Img['numerator'];?></td>
        <td><?php echo $getCPOE_Img['exclusion'];?></td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Laboratory</td>
        <td>Optional</td>
        <td><?php echo $getCPOE_Lab['percent'];?></td>
        <td><?php echo $getCPOE_Lab['denominator'];?></td>
        <td><?php echo $getCPOE_Lab['numerator'];?></td>
        <td><?php echo $getCPOE_Lab['exclusion'];?></td>
      </tr>
      <tr>
        <td>e-Prescribing (eRx)</td>
        <td></td>
        <td><?php echo $arr_EPrescribe['percent'];?></td>
        <td><?php echo $arr_EPrescribe['denominator'];?></td>
        <td><?php echo $arr_EPrescribe['numerator'];?></td>
        <td><?php echo $arr_EPrescribe['exclusion'];?></td>
      </tr>
      <tr class="bg-info">
        <td>Clinical Decision Support</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Clinical Decision Support</td>
        <td></td>
        <td>Yes</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Drug Drug and Drug Allergy Interactions</td>
        <td></td>
        <td>Yes</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr class="bg-info">
        <td>Patient Electronic Access</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Provide Patient Access to PHI</td>
        <td></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['percent'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['denominator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthInfo['numerator'];?></td>				
        <td><?php echo $arr_TimelyPtElectHlthInfo['exclusion'];?></td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Patients View Their PHI</td>
        <td></td>
        <td><?php echo $arr_TimelyPtElectHlthView['percent'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthView['denominator'];?></td>
        <td><?php echo $arr_TimelyPtElectHlthView['numerator'];?></td>				
        <td><?php echo $arr_TimelyPtElectHlthView['exclusion'];?></td>
      </tr>
      <tr>
        <td>Patients use of secure electronic messaging</td>
        <td></td>
        <td><?php echo $arr_ptSecureMsg['percent'];?></td>
        <td><?php echo $arr_ptSecureMsg['denominator'];?></td>
        <td><?php echo $arr_ptSecureMsg['numerator'];?></td>
        <td><?php echo $arr_ptSecureMsg['exclusion'];?></td>
      </tr>
      <tr>
        <td>Use certified EHR technology to identify patient-specific education resources and provide to patient, if appropriate</td>
        <td></td>
        <td><?php echo $arr_EduResourceToPt['percent'];?></td>
        <td><?php echo $arr_EduResourceToPt['denominator'];?></td>
        <td><?php echo $arr_EduResourceToPt['numerator'];?></td>
        <td><?php echo $arr_EduResourceToPt['exclusion'];?></td>
      </tr>
      <tr>
        <td>Medication reconciliation</td>
        <td></td>
        <td><?php echo $arr_MedReconcil['percent'];?></td>
        <td><?php echo $arr_MedReconcil['denominator'];?></td>
        <td><?php echo $arr_MedReconcil['numerator'];?></td>
        <td><?php echo $arr_MedReconcil['exclusion'];?></td>
      </tr>
      <tr class="bg-info">
        <td>Summary of Care</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Provide Summary of Care Record to referring physician</td>
        <td></td>
        <td><?php echo $arr_SummaryCareRec1['percent'];?></td>
        <td><?php echo $arr_SummaryCareRec1['denominator'];?></td>
        <td><?php echo $arr_SummaryCareRec1['numerator'];?></td>
        <td><?php echo $arr_SummaryCareRec1['exclusion'];?></td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Provide Summary of Care Record Electronically to referring physician</td>
        <td></td>
        <td><?php echo $arr_SummaryCareRec2['percent'];?></td>
        <td><?php echo $arr_SummaryCareRec2['denominator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['numerator'];?></td>
        <td><?php echo $arr_SummaryCareRec2['exclusion'];?></td>
      </tr>
      <tr class="bg-info">
        <td>Public Health Measures</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Capability to submit electronic data to immunization registries or systems</td>
        <td></td>
        <td>Exempt</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Capability to submit syndromic surveillance data to public health registries</td>
        <td>
        <td>Exempt</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp; &nbsp; &#10157 Participate in AAO registry</td>
        <td></td>
        <td><?php echo (constant('AAO_IRIS_REG')!=false || trim(constant('AAO_IRIS_REG'))!='') ? trim(constant('AAO_IRIS_REG')):'No';?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>Protect electronic health information</td>
        <td></td>
        <td>Yes</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      </tbody>
    </table>
<?php
}

//if($task==2 || $task==3){ //CQM or Analyze or Attestation
        $objMUR->updateCQMpatients('empty');
		/******CMS- CLINICAL CORE*************************/
        $NQF0018			= $objMUR->getNQF0018();
		$arr_NQF0018 		= $objMUR->format_values($NQF0018);					$objMUR->updateCQMpatients('update','NQF0018',$NQF0018['denominator']);

		$NQF0022			= $objMUR->getNQF0022('one');
		$arr_NQF0022		= $objMUR->format_values($NQF0022);					$objMUR->updateCQMpatients('update','NQF0022a',$NQF0022['denominator']);

		$NQF0022b			= $objMUR->getNQF0022('two');
		$arr_NQF0022b		= $objMUR->format_values($NQF0022b);				$objMUR->updateCQMpatients('update','NQF0022b',$NQF0022b['denominator']);
				
		$NQF0421a			= $objMUR->getNQF0421a();
		$arr_NQF0421 		= $objMUR->format_values($NQF0421a);				$objMUR->updateCQMpatients('update','NQF0421a',$NQF0421a['denominator']);
		
		$NQF0421b			= $objMUR->getNQF0421b();
		$arr_NQF0421b 		= $objMUR->format_values($NQF0421b);				$objMUR->updateCQMpatients('update','NQF0421b',$NQF0421b['denominator']);

		$NQF0028			= $objMUR->getNQF0028(); 		
		$arr_NQF0028		= $objMUR->format_values($NQF0028); 				$objMUR->updateCQMpatients('update','NQF0028',$NQF0028['denominator']);
		
		$RefLoop			= $objMUR->getRefLoop();
		$arr_RefLoop		= $objMUR->format_values($RefLoop);					$objMUR->updateCQMpatients('update','CMS50v2',$RefLoop['denominator']);
		
		$NQF0052			= $objMUR->getNQF0052();
		$arr_NQF0052		= $objMUR->format_values($NQF0052);					$objMUR->updateCQMpatients('update','NQF0052',$NQF0052['denominator']);
		/******CMS- QUALITY MEASURES *************************/
		$NQF0086			= $objMUR->getNQF0086();
		$arr_NQF0086 		= $objMUR->format_values($NQF0086);					$objMUR->updateCQMpatients('update','NQF0086',$NQF0086['denominator']);
		
		$NQF0088			= $objMUR->getNQF0088();
		$arr_NQF0088 		= $objMUR->format_values($NQF0088);					$objMUR->updateCQMpatients('update','NQF0088',$NQF0088['denominator']);
		
		$NQF0089			= $objMUR->getNQF0089();
		$arr_NQF0089 		= $objMUR->format_values($NQF0089);					$objMUR->updateCQMpatients('update','NQF0089',$NQF0089['denominator']);
		
		$NQF0055			= $objMUR->getNQF0055();
		$arr_NQF0055 		= $objMUR->format_values($NQF0055);					$objMUR->updateCQMpatients('update','NQF0055',$NQF0055['denominator']);
		?>
        <table class="table table-bordered table-hover">
          <thead class="bg-primary">
          <tr>
            <td width="90" valign="top">&nbsp;</td>
            <th width="auto" align="left"> &nbsp; Measure</th>
            <th width="100">Percentage</th>
            <th width="100">Denominator</th>
            <th width="95">Numerator</th>				
            <th width="90">Exclusion</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <th class="bg-info" colspan="6">CLINICAL QUALITY MEASURE</th>
          </tr>	  
          <tr>
            <td valign="top" align="left"><b>NQF 0018</b></td>
            <td align="left">Controlling High Blood Pressure</td>
            <td><?php echo $arr_NQF0018['percent'];?></td>
            <td><?php echo $arr_NQF0018['denominator'];?></td>
            <td><?php echo $arr_NQF0018['numerator'];?></td>
            <td><?php echo $arr_NQF0018['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><b>NQF 0022</b></td>
            <td align="left">Use of High-Risk Medications in the Elderly</td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" align="left">&nbsp;</td>
            <td align="left">&nbsp; &nbsp; &#10157 Measure I (1+)</td>
            <td><?php echo $arr_NQF0022['percent'];?></td>
            <td><?php echo $arr_NQF0022['denominator'];?></td>
            <td><?php echo $arr_NQF0022['numerator'];?></td>
            <td><?php echo $arr_NQF0022['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left">&nbsp;</td>
            <td align="left">&nbsp; &nbsp; &#10157 Measure II (2+)</td>
            <td><?php echo $arr_NQF0022b['percent'];?></td>
            <td><?php echo $arr_NQF0022b['denominator'];?></td>
            <td><?php echo $arr_NQF0022b['numerator'];?></td>
            <td><?php echo $arr_NQF0022b['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><b>NQF 0421</b></td>
            <td align="left">Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up</a></td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
            <td class="bg1">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" align="left">&nbsp;</td>
            <td align="left">&nbsp; &nbsp; &#10157 BMI Screening &amp; Follow Up: 65+</td>
            <td><?php echo $arr_NQF0421['percent'];?></td>
            <td><?php echo $arr_NQF0421['denominator'];?></td>
            <td><?php echo $arr_NQF0421['numerator'];?></td>
            <td><?php echo $arr_NQF0421['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left">&nbsp;</td>
            <td align="left">&nbsp; &nbsp; &#10157 BMI Screening &amp; Follow Up: 18-64</td>
            <td><?php echo $arr_NQF0421b['percent'];?></td>
            <td><?php echo $arr_NQF0421b['denominator'];?></td>
            <td><?php echo $arr_NQF0421b['numerator'];?></td>
            <td><?php echo $arr_NQF0421b['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><b>CMS50v2</b></td>
            <td align="left">Closing the Referral Loop: Receipt of specialist report</td>
            <td><?php echo $arr_RefLoop['percent'];?></td>
            <td><?php echo $arr_RefLoop['denominator'];?></td>
            <td><?php echo $arr_RefLoop['numerator'];?></td>
            <td><?php echo $arr_RefLoop['exclusion'];?></td>
          </tr>
          <tr>
            <td align="left"><b>NQF 0028</b></td>
            <td align="left">Prevent Care and Screening: Tobacco Use: Screening and Cessation Intervention</td>
            <td><?php echo $arr_NQF0028['percent'];?></td>
            <td><?php echo $arr_NQF0028['denominator'];?></td>
            <td><?php echo $arr_NQF0028['numerator'];?></td>
            <td><?php echo $arr_NQF0028['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><b>NQF 0052</b></td>
            <td align="left">Use of Imaging Studies for Low Back Pain</td>
            <td><?php echo $arr_NQF0052['percent'];?></td>
            <td><?php echo $arr_NQF0052['denominator'];?></td>
            <td><?php echo $arr_NQF0052['numerator'];?></td>
            <td><?php echo $arr_NQF0052['exclusion'];?></td>
          </tr>
          <tr><th colspan="6" class="bg-info">CLINICAL QUALITY MEASURE - OPTHALMOLOGY</th></tr>	  
          <tr>
            <td valign="top" align="left"><div style="height:25px;" class="fl"></div>
                <b>NQF 0086</b></td>
            <td align="left">Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</td>
            <td><?php echo $arr_NQF0086['percent'];?></td>
            <td><?php echo $arr_NQF0086['denominator'];?></td>
            <td><?php echo $arr_NQF0086['numerator'];?></td>
            <td><?php echo $arr_NQF0086['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><div style="height:25px;" class="fl"></div><b>NQF 0088</b></td>
           <td align="left">Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema and Level of Severity of Retinopathy</td>
            <td><?php echo $arr_NQF0088['percent'];?></td>
            <td><?php echo $arr_NQF0088['denominator'];?></td>
            <td><?php echo $arr_NQF0088['numerator'];?></td>
            <td><?php echo $arr_NQF0088['exclusion'];?></td>
          </tr>
          <tr>
            <td align="left"><div style="height:25px;" class="fl"></div>
                <b>NQF 0089</b></td>
            <td align="left">Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</td>
            <td><?php echo $arr_NQF0089['percent'];?></td>
            <td><?php echo $arr_NQF0089['denominator'];?></td>
            <td><?php echo $arr_NQF0089['numerator'];?></td>
            <td><?php echo $arr_NQF0089['exclusion'];?></td>
          </tr>
          <tr>
            <td valign="top" align="left"><div style="height:25px;" class="fl"></div>
                <b>NQF 0055</b></td>
            <td align="left">Diabetes: Eye Exam</a></td>
            <td><?php echo $arr_NQF0055['percent'];?></td>
            <td><?php echo $arr_NQF0055['denominator'];?></td>
            <td><?php echo $arr_NQF0055['numerator'];?></td>
            <td><?php echo $arr_NQF0055['exclusion'];?></td>
          </tr>
          </tbody>
        </table>
		<?php
	//}//end of else if
?>
		</form><br />

<?php


?>