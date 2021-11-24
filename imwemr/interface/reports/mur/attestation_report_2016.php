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
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
$objMUR			= new MUR_Reports;

$provider 		= isset($_REQUEST['providers']) ? trim(strip_tags($_REQUEST['providers'])) : 0;
$ptIDs = $objMUR->get_denominator($provider);
$commaptIDs = implode(', ',$ptIDs);
if(!$commaptIDs || empty($commaptIDs)) {$commaptIDs = '0';}

$curr_EP_name_temp	= $objMUR->get_provider_ar($provider);
$curr_EP_name_arr	= explode(',',$curr_EP_name_temp[$provider]);
$curr_EP_name		= strtolower($curr_EP_name_arr[0]);

$arr_task = array('Invalid','Cal-Core/Menu','Cal- CMS', 'Analyze', 'Attestation', 'PQRI XML');
$taskNum = isset($_REQUEST['task']) ? intval($_REQUEST['task']) : 0;
$task = $arr_task[$taskNum];

$provider 		= isset($_REQUEST['providers']) ? trim(strip_tags($_REQUEST['providers'])) : 0;
$dtfrom 		= isset($_REQUEST['dtfrom']) ? trim(strip_tags($_REQUEST['dtfrom'])) : 0;
$dtupto 		= isset($_REQUEST['dtupto']) ? trim(strip_tags($_REQUEST['dtupto'])) : 0;
$facility_id 	= isset($_REQUEST['facility_id']) ? trim(strip_tags($_REQUEST['facility_id'])) : 0;
$mur_version 	= isset($_REQUEST['mur_version']) ? trim(strip_tags($_REQUEST['mur_version'])) : 0;

/*--GETTING OPERATOR INFO--*/
$temp_createdBy = $objMUR->get_provider_ar($_SESSION['authId']);
$createdBy = $temp_createdBy[$_SESSION['authId']];

/*--GETTING ALL INFO ABOUT SELECTED PROVIDER--*/
$arr_physician_details = $objMUR->getUserDetails($provider);
$arr_physician_details = $arr_physician_details[0];
$createdFor	= $arr_physician_details['pro_title'].' '.$arr_physician_details['fname'].' '.$arr_physician_details['lname'].' '.$arr_physician_details['pro_suffix'];
/*--GETTING PRACTICE (GROUP) INFO FOR SELECTED PHYSICIAN--*/
$gro_id = $arr_physician_details['default_group'];
$practice_name = $objMUR->getPhysicianGroupInfo($gro_id);

if(constant('MUR_PRACTICE_NAME') && constant('MUR_PRACTICE_NAME') != ''){
	$practice_name['name'] = constant('MUR_PRACTICE_NAME');
}

//first block have s1 to s10 keys.
//second block have have cms1 & cms2 keys.
//third block have cms31 to cms34 keys.
/******STAGE1-CORE*******************/
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

$NQF0018			= $objMUR->getNQF0018();
$arr_NQF0018 		= $objMUR->format_values($NQF0018);

$NQF0022			= $objMUR->getNQF0022('one');
$arr_NQF0022		= $objMUR->format_values($NQF0022);

$NQF0022b			= $objMUR->getNQF0022('two');
$arr_NQF0022b		= $objMUR->format_values($NQF0022b);
		
$NQF0421a			= $objMUR->getNQF0421a();
$arr_NQF0421 		= $objMUR->format_values($NQF0421a);

$NQF0421b			= $objMUR->getNQF0421b();
$arr_NQF0421b 		= $objMUR->format_values($NQF0421b);

$NQF0028			= $objMUR->getNQF0028(); 		
$arr_NQF0028		= $objMUR->format_values($NQF0028);

$RefLoop			= $objMUR->getRefLoop();
$arr_RefLoop		= $objMUR->format_values($RefLoop);

$NQF0052			= $objMUR->getNQF0052();
$arr_NQF0052		= $objMUR->format_values($NQF0052);

/******CMS- QUALITY MEASURES *************************/
$NQF0086			= $objMUR->getNQF0086();
$arr_NQF0086 		= $objMUR->format_values($NQF0086);

$NQF0088			= $objMUR->getNQF0088();
$arr_NQF0088 		= $objMUR->format_values($NQF0088);

$NQF0089			= $objMUR->getNQF0089();
$arr_NQF0089 		= $objMUR->format_values($NQF0089);

$NQF0055			= $objMUR->getNQF0055();
$arr_NQF0055 		= $objMUR->format_values($NQF0055);


$testElecTransfer='';
if(intval($arr_SummaryCareRec2['numerator'])>=1){$testElecTransfer='Yes';}else{$testElecTransfer='No';}
$aao_iris = (constant('AAO_IRIS_REG')!=false || trim(constant('AAO_IRIS_REG'))!='') ? trim(constant('AAO_IRIS_REG')):'No';
//making html for 1st block.


$html_block1 = '
<table style="width:98%;" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td align="left" height="40" vAlign="top"><img src="../images/logo_for_pdf.jpg" /></td>
		<td height="30px" align="center" style="font-size:14px;"><big><b>Meaningful Use Measure Report (Stage 2016)</b></big></td>
		<td align="right" vAlign="top"><img src="../images/mur_certnum.jpg" /></td>
	</tr>
	<tr>
		<td class="text_value" style="width:30%;" align="left">Physician Name : '.$createdFor.'</td>
		<td class="text_value" style="width:40%;" align="center">Practice Name : '.$practice_name['name'].'</td>
		<td class="text_value" style="width:30%;" align="right">MU Period : From '.$dtfrom.'&nbsp;To&nbsp;'.$dtupto.'</td>
	</tr>
	<tr>
		<td class="text_value" align="left">NPI# : '.$arr_physician_details['user_npi'].'</td>
		<td class="text_value" align="center">TIN# : '.$arr_physician_details['TaxId'].'</td>
		<td class="text_value" align="right">Printed on : '.date('m-d-Y').'</td>
	</tr>
	<tr>
		<td class="text_value" align="left">Taxonomy# : '.$arr_physician_details['TaxonomyId'].'</td>
		<td class="text_value" align="center">&nbsp;</td>
		<td class="text_value" align="right">Printed by : '.$createdBy.'</td>
	</tr>
	<tr>
		<td colspan=3><hr></td>
	</tr>
	<tr>
		<td colspan=3 style="padding-left:20px;">
			<ol>
				<li>The information submitted with respect to clinical quality measures was generated as output of an identified certified EHR technology (imwemr).</li>
				<li>The information submitted is accurate to the best of the knowledge and belief of the EP.</li>
				<li>The information submitted includes information on all patients to whom the clinical quality measure applies.</li>
			</ol>
		</td>
	</tr>
</table>
<table cellspacing="0" rules="rows" style="text-align:left;">
	  <tr>
			<td class="tb_heading" style="width:60px;" valign="top">&nbsp;</td>
			<td class="tb_heading" align="left" style="width:520px;"> &nbsp; Measures</td>				
			<td class="tb_heading" style="width:100px;">Stage I</td>
			<td class="tb_heading" style="width:100px;">Stage II</td>
			<td class="tb_heading" style="width:100px;">Denominator</td>
			<td class="tb_heading" style="width:100px;">Numerator</td>
			<td class="tb_heading" style="width:80px;">Exclusion</td>
	  </tr>
	  <tr>
		<td class="text_value botborder" colspan="2" style="width:580px;">Computerized provider order entry (CPOE)</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:80px;">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Medication</td>
		<td class="text_value botborder">'.$getCPOE_Med_I['percent'].'</td>
		<td class="text_value botborder">'.$getCPOE_Med['percent'].'</td>
		<td class="text_value botborder">'.$getCPOE_Med['denominator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Med['numerator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Med['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Imaging</td>
		<td class="text_value botborder">Optional</td>
		<td class="text_value botborder">'.$getCPOE_Img['percent'].'</td>
		<td class="text_value botborder">'.$getCPOE_Img['denominator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Img['numerator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Img['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Laboratory</td>
		<td class="text_value botborder">Optional</td>
		<td class="text_value botborder">'.$getCPOE_Lab['percent'].'</td>
		<td class="text_value botborder">'.$getCPOE_Lab['denominator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Lab['numerator'].'</td>
		<td class="text_value botborder">'.$getCPOE_Lab['exclusion'].'</td>
	  </tr>
	  <tr>
		<td class="text_value botborder" colspan="2" style="width:580px;">e-Prescribing (eRx)</td>
		<td class="text_value botborder" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_EPrescribe['percent'].'</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_EPrescribe['denominator'].'</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_EPrescribe['numerator'].'</td>
		<td class="text_value botborder" style="width:80px;">'.$arr_EPrescribe['exclusion'].'</td>
	  </tr>
	  <tr>
		<td class="text_value botborder" colspan="2" style="width:580px;">Clinical Decision Support</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder dataBGColor" style="width:80px;">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Clinical Decision Support</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">Yes</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Drug Drug and Drug Allergy Interactions</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">Yes</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Patient Electronic Access</td>
		<td class="dataBGColor text_value botborder">&nbsp;</td>
		<td class="dataBGColor text_value botborder">&nbsp;</td>
		<td class="dataBGColor text_value botborder">&nbsp;</td>
		<td class="dataBGColor text_value botborder">&nbsp;</td>
		<td class="dataBGColor text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Provide Patient Access to PHI</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['percent'].'</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['denominator'].'</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['numerator'].'</td>				
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Patients View Their PHI</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthView['percent'].'</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthView['denominator'].'</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthView['numerator'].'</td>				
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthView['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Patients use of secure electronic messaging</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_ptSecureMsg['percent'].'</td>
		<td class="text_value botborder">'.$arr_ptSecureMsg['denominator'].'</td>
		<td class="text_value botborder">'.$arr_ptSecureMsg['numerator'].'</td>				
		<td class="text_value botborder">'.$arr_ptSecureMsg['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Use certified EHR technology to identify patient-specific education resources and provide to patient,<br> if appropriate</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_EduResourceToPt['percent'].'</td>
		<td class="text_value botborder">'.$arr_EduResourceToPt['denominator'].'</td>
		<td class="text_value botborder">'.$arr_EduResourceToPt['numerator'].'</td>
		<td class="text_value botborder">'.$arr_EduResourceToPt['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Medication reconciliation</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_MedReconcil['percent'].'</td>
		<td class="text_value botborder">'.$arr_MedReconcil['denominator'].'</td>
		<td class="text_value botborder">'.$arr_MedReconcil['numerator'].'</td>
		<td class="text_value botborder">'.$arr_MedReconcil['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Summary of Care</td>
		<td class="botborder dataBGColor">&nbsp;</td>
		<td class="botborder dataBGColor">&nbsp;</td>
		<td class="botborder dataBGColor">&nbsp;</td>
		<td class="botborder dataBGColor">&nbsp;</td>
		<td class="botborder dataBGColor">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Provide Summary of Care Record to referring physician</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec1['percent'].'</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec1['denominator'].'</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec1['numerator'].'</td>				
		<td class="text_value botborder">'.$arr_SummaryCareRec1['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Provide Summary of Care Record Electronically to referring physician</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec2['percent'].'</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec2['denominator'].'</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec2['numerator'].'</td>				
		<td class="text_value botborder">'.$arr_SummaryCareRec2['exclusion'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Public Health Measures</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Capability to submit electronic data to immunization registries or systems</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">Exempt</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Capability to submit syndromic surveillance data to public health registries</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">Exempt</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">&nbsp; &nbsp; &#187; Participate in AAO IRIS Registry</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$aao_iris.'</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>   
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Protect electronic health information</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">Yes</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr></table>
	  <br><br><br>
	  <table cellspacing="0" rules="rows" style="text-align:left;">
	  ';

$html_block3 .= '
	  <tr>
		<th colspan="2" class="tb_subheading" align="left" style="width:680px;">CLINICAL QUALITY MEASURE</th>
		<th class="tb_subheading" align="left" style="width:100px;">Percentage</th>
		<th class="tb_subheading" align="left" style="width:100px;">Denominator</th>
		<th class="tb_subheading" align="left" style="width:100px;">Numerator</th>
		<th class="tb_subheading" align="left" style="width:80px;">Exclusion</th>
	  </tr>	  
	  <tr>
		<td valign="top" class="text_value botborder" align="left" style="width:80px;"><b>NQF 0018</b></td>
		<td align="left"  class="text_value botborder" style="width:500px;">Controlling High Blood Pressure</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_NQF0018['percent'].'</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_NQF0018['denominator'].'</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_NQF0018['numerator'].'</td>
		<td class="text_value botborder" style="width:80px;">'.$arr_NQF0018['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><b>NQF 0022</b></td>
		<td align="left"  class="text_value botborder">Use of High-Risk Medications in the Elderly</td>
		<td class="text_value dataBGColor botborder"></td>
		<td class="text_value dataBGColor botborder"></td>
		<td class="text_value dataBGColor botborder"></td>
		<td class="text_value dataBGColor botborder"></td>
	  </tr>
	  <tr>
		<td valign="top" align="left" class="text_value botborder">&nbsp;</td>
		<td align="left" class="text_value botborder">&nbsp; &nbsp; &#187; Measure I (1+)</td>
		<td class="text_value botborder">'.$arr_NQF0022['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" align="left" class="text_value botborder">&nbsp;</td>
		<td align="left" class="text_value botborder">&nbsp; &nbsp; &#187; Measure II (2+)</td>
		<td class="text_value botborder">'.$arr_NQF0022['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0022['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><b>NQF 0421</b></td>
		<td align="left" class="text_value botborder">Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up</td>
		<td class="text_value dataBGColor botborder "></td>
		<td class="text_value dataBGColor botborder"></td>
		<td class="text_value dataBGColor botborder"></td>
		<td class="text_value dataBGColor botborder"></td>
	  </tr>
	  <tr>
		<td valign="top" align="left" class="text_value botborder">&nbsp;</td>
		<td align="left" class="text_value botborder">&nbsp; &nbsp; &#187; BMI Screening &amp; Follow Up: 65+</td>
		<td class="text_value botborder">'.$arr_NQF0421['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" align="left" class="text_value botborder">&nbsp;</td>
		<td align="left" class="text_value botborder">&nbsp; &nbsp; &#187; BMI Screening &amp; Follow Up: 18-64</td>
		<td class="text_value botborder">'.$arr_NQF0421b['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421b['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421b['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0421b['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><b>CMS50v2</b></td>
		<td  align="left"  class="text_value botborder">Closing the Referral Loop: Receipt of specialist report</td>
		<td class="text_value botborder">'.$arr_RefLoop['percent'].'</td>
		<td class="text_value botborder">'.$arr_RefLoop['denominator'].'</td>
		<td class="text_value botborder">'.$arr_RefLoop['numerator'].'</td>
		<td class="text_value botborder">'.$arr_RefLoop['exclusion'].'</td>
	  </tr>
	   <tr>
		<td align="left" class="text_value botborder"><b>NQF 0028</b></td>
		<td align="left" class="text_value botborder">Prevent Care and Screening: Tobacco Use: Screening and Cessation Intervention</td>
		<td class="text_value botborder">'.$arr_NQF0028['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0028['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0028['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0028['exclusion'].'</td>
	  </tr>
	 <tr>
		<td valign="top" class="text_value botborder" align="left"><b>NQF 0052</b></td>
		<td  align="left"  class="text_value botborder">Use of Imaging Studies for Low Back Pain</td>
		<td class="text_value botborder">'.$arr_NQF0052['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0052['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0052['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0052['exclusion'].'</td>
	  </tr>';
			  
$html_block4 .= '			  
	  <tr><th colspan="6" class="tb_subheading" align="left">CLINICAL QUALITY MEASURE - OPTHALMOLOGY</th></tr>	  
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><strong>NQF 0086</strong></td>
		<td align="left" class="text_value botborder">Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</td>
		<td class="text_value botborder">'.$arr_NQF0086['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0086['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0086['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0086['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><b>NQF 0088</b></td>
		<td  align="left"  class="text_value botborder" style="width:550px">Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema &amp; Level of Severity of Retinopathy</td>
		<td class="text_value botborder">'.$arr_NQF0088['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0088['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0088['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0088['exclusion'].'</td>
	  </tr>
	  <tr>
		<td  align="left"  class="text_value botborder"><b>NQF 0089</b></td>
		<td  align="left"  class="text_value botborder" style="width:550px">Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</td>
		<td class="text_value botborder">'.$arr_NQF0089['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0089['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0089['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0089['exclusion'].'</td>
	  </tr>
	  <tr>
		<td valign="top" class="text_value botborder" align="left"><b>NQF 0055</b></td>
		<td  align="left"  class="text_value botborder">Diabetes: Eye Exam</td>
		<td class="text_value botborder">'.$arr_NQF0055['percent'].'</td>
		<td class="text_value botborder">'.$arr_NQF0055['denominator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0055['numerator'].'</td>
		<td class="text_value botborder">'.$arr_NQF0055['exclusion'].'</td>
	  </tr>
</table>
			  
';






$styles = '
<page backtop="5mm" backbottom="5mm">
<page_footer>

<table style="width: 100%;">
	<tr>
		<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
	</tr>
</table>
</page_footer>

<style>
.botborder{
	border-bottom:1px solid #cccccc;
}
td, .text_b_w, .text_lable, .text_value, .tb_subheading, .tb_heading, .tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
}
.text_b_w{
		
		font-weight:bold;
}
.text_lable{
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value, .text_value_sm{
		font-weight:100;
		background-color:#FFFFFF;
		height:15px;
	}
.text_value_sm{
		font-size:10px;
	}

.paddingTop{
	padding-top:5px;
}

.tb_subheading{
	font-weight:bold;
	padding:2px 0px 2px 2px;
	color:#000000;
	background-color:#dddddd;
}
.tb_heading{
	font-size:12px;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#333333;
	margin-top:10px;
}
.tb_headingHeader{
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.dataBGColor{
	background-color:#f2f2f2;
}
</style>';
//end of third block.

//echo '<br><br>';
//echo $html_block2;
//echo '<br><br>';
//echo $html_block3;
$strHTML = $styles.$html_block1.$html_block3.$html_block4.'<br><br><br><br><br>Physician ______________________________________<br>
	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.$arr_physician_details['pro_title'].' '.$arr_physician_details['fname'].' '.$arr_physician_details['lname'].' '.$arr_physician_details['pro_suffix'].')';
$strHTML .= '	   </page>';
//echo $strHTML;
//die();

	$file_pointer = write_html($strHTML,'attestation_report_trans2016.html');

	?>
	<html>
		<body>
        <form name="frm_print_mur" action="../../../library/html_to_pdf/createPdf.php" method="POST">
            <input type="hidden" name="onePage" value="false">
            <input type="hidden" name="op" value="l" >
            <input type="hidden" name="file_location" value="<?php echo $file_pointer;?>">
        </form>
			<script type="text/javascript">
			document.forms.frm_print_mur.submit();
			</script>
		</body>
	</html>