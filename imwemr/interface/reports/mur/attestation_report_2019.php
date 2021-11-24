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

//$arr_task = array('Invalid','Cal-Core/Menu','Cal- CMS', 'Analyze', 'Attestation', 'PQRI XML');
//$taskNum = isset($_REQUEST['task']) ? intval($_REQUEST['task']) : 0;

//$task = $arr_task[$taskNum];

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

/****GETTING TIN BASED ON NON-TIN BASED FACILITIES******/
$TINfacilities = $objMUR->get_tin_options();
$practice_name = '';

if($TINfacilities && !empty($facility_id)){//GET HQ and then linked group
	$temp_facs = explode(',',$facility_id);
	foreach($temp_facs as $fac_id){
		if(isset($TINfacilities[$fac_id])){
			$temp_facs_rs = $TINfacilities[$fac_id];
			$practice_name .= $temp_facs_rs['name'].', TIN#: '.$temp_facs_rs['fac_tin'].'<br>';
		}
	}
}else if(constant('MUR_PRACTICE_NAME') && constant('MUR_PRACTICE_NAME') != ''){
	$practice_name = constant('MUR_PRACTICE_NAME');
}else{
	$HQFacRs = get_facility_details();
	$default_group = $HQFacRs['default_group'];
	$temp_groups_rs = $objMUR->getPhysicianGroupInfo($default_group);
	$temp_group_rs = $temp_groups_rs[0];
	$practice_name = $temp_group_rs['name'];
}


/**********MEASURE RESULTS***************************/
$arr_EPrescribe 				= $objMUR->format_values($objMUR->getEPrescribe($commaptIDs),50,'noPt');
$arr_EPrescribe['points'] 		= $objMUR->pointsMeasureWise('eRx',$objMUR->int_percent);

$arr_SummaryCareRec2			= $objMUR->format_values($objMUR->SendSummaryCareRec($commaptIDs,'m2'),10,'nored');
$arr_SummaryCareRec2['points'] 	= $objMUR->pointsMeasureWise('SummaryCareRec2',$objMUR->int_percent);

$arr_MedReconcil			= $objMUR->format_values($objMUR->getMedReconcil_2018($commaptIDs),50);
$arr_MedReconcil['points'] = $objMUR->pointsMeasureWise('MedRecon',$objMUR->int_percent);

$arr_TimelyPtElectHlthInfo	= $objMUR->format_values($objMUR->getTimelyPtElectHlthInfo($commaptIDs),50); //pt.electronic access, measure1
$arr_TimelyPtElectHlthInfo['points'] = $objMUR->pointsMeasureWise('PtViewAccess',$objMUR->int_percent);


$str_pirr = 'No';
if($_POST['pirr']=='on'){
	$str_pirr = 'YES';
}

$str_ecr = 'No';
if($_POST['ecr']=='on'){
	$str_ecr = 'YES';
}

$str_phrr = 'No';
if($_POST['phrr']=='on'){
	$str_phrr = 'YES';
}

$str_bssr = 'No';
if($_POST['bssr']=='on'){
	$str_bssr = 'YES';
}

$str_biris = 'No';
if($_POST['biris']=='on'){
	$str_biris = 'YES';
}

$str_bsra = 'No';
if($_POST['bsra']=='on'){
	$str_bsra = 'YES';
}


$html_block1 = '
<table style="width:1020px;" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td align="left" height="40" vAlign="top"><img src="../images/logo_for_pdf.jpg" /></td>
		<td height="30px" align="center" style="font-size:14px;"><big><b>2019 MIPS PI</b></big></td>
		<td align="right" vAlign="top"><img src="../images/mur_certnum.jpg" /></td>
	</tr>
	<tr>
		<td class="text_value" style="width:30%;" align="left" valign="top">';
		foreach($arr_physician_details as $user_rs){
			$createdFor	= $user_rs['pro_title'].' '.$user_rs['fname'].' '.$user_rs['lname'].' '.$user_rs['pro_suffix'];
			$html_block1 .= ''.trim($createdFor).', NPI#: '.$user_rs['user_npi'].'<!--, Taxonomy#: '.$user_rs['TaxonomyId'].'--><br>';
		}

$html_block1 .= '
		</td>
		<td class="text_value" style="width:40%;" align="center" valign="top">
			'.$practice_name.'
		</td>
		<td class="text_value" style="width:30%;" align="right" valign="top">
			MU Period : From '.$dtfrom.'&nbsp;To&nbsp;'.$dtupto.'<br>
			Printed on : '.date('m-d-Y').'<br>
			Printed by : '.$createdBy.'
		</td>
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
			<td class="tb_heading" align="left" style="width:620px;"> &nbsp; Measures</td>				
			<td class="tb_heading" style="width:100px;">&nbsp;</td>
			<td class="tb_heading" style="width:100px;">Numerator</td>
			<td class="tb_heading" style="width:100px;">Denominator</td>
			<td class="tb_heading" style="width:80px;">Percentage</td>
	  </tr>
	  <tr>
		<td class="text_value botborder" colspan="2" style="width:680px;">e-Prescribing</td>
		<td class="text_value botborder" style="width:100px;">&nbsp;</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_EPrescribe['numerator'].'</td>
		<td class="text_value botborder" style="width:100px;">'.$arr_EPrescribe['denominator'].'</td>
		<td class="text_value botborder" style="width:80px;">'.$arr_EPrescribe['percent'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Support Electronic Referral Loops by Sending Health Information</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec2['numerator'].'</td>
		<td class="text_value botborder">'.$arr_SummaryCareRec2['denominator'].'</td>				
		<td class="text_value botborder">'.$arr_SummaryCareRec2['percent'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Support Electronic Referral Loops by Receiving and Incorporating Health Information</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_MedReconcil['numerator'].'</td>
		<td class="text_value botborder">'.$arr_MedReconcil['denominator'].'</td>				
		<td class="text_value botborder">'.$arr_MedReconcil['percent'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Provide Patients Electronic Access to their Health Information</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['numerator'].'</td>
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['denominator'].'</td>				
		<td class="text_value botborder">'.$arr_TimelyPtElectHlthInfo['percent'].'</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Immunization Registry Reporting</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_pirr.'</td>
		<td class="text_value botborder">&nbsp;</td>				
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Electronic Case Reporting</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_ecr.'</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Public Health Registry Reporting</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_phrr.'</td>
		<td class="text_value botborder">&nbsp;</td>				
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Syndromic Surveillance Reporting</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_bssr.'</td>
		<td class="text_value botborder">&nbsp;</td>				
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Clinical Data Registry Reporting (IRIS)</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_biris.'</td>
		<td class="text_value botborder">&nbsp;</td>				
		<td class="text_value botborder">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="left" class="text_value botborder" colspan="2">Security Risk Analysis</td>
		<td class="text_value botborder">&nbsp;</td>
		<td class="text_value botborder">'.$str_bsra.'</td>
		<td class="text_value botborder">&nbsp;</td>				
		<td class="text_value botborder">&nbsp;</td>
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
$strHTML = $styles.$html_block1;//.'<br><br><br><br><br>Physician ______________________________________<br>
//	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.$arr_physician_details['pro_title'].' '.$arr_physician_details['fname'].' '.$arr_physician_details['lname'].' '.$arr_physician_details['pro_suffix'].')';
$strHTML .= '	   </page>';
//echo $strHTML;
//die();

	
	$file_pointer = write_html($strHTML,'attestation_report_trans2018.html');
	/*$fp = fopen('../new_html2pdf/attestation_report.html','w');
	$intBytes = fputs($fp,$strHTML);
	fclose($fp);
	*/
	$page_style='l';
	?>
	<html>
		<body>
        <form name="frm_print_mur" action="../../../library/html_to_pdf/createPdf.php" method="POST">
            <input type="hidden" name="onePage" value="false">
            <input type="hidden" name="op" value="<?php echo $page_style;?>" >
            <input type="hidden" name="file_location" value="<?php echo $file_pointer;?>">
        </form>
			<script type="text/javascript">
			document.forms.frm_print_mur.submit();
				/*var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;

				window.location.href = '../../../library/html_to_pdf/createPdf.php?op=<?php echo $page_style;?>&file_location=<?php echo $file_pointer;?>';
				window.resizeTo(parWidth,parHeight);
//				parent.show_img('none');*/
			</script>
		</body>
	</html>