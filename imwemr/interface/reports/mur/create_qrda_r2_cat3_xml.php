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
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
include(dirname(__FILE__)."/../../../library/classes/MUR_class.php");
//include(dirname(__FILE__)."/../../../library/classes/functions_cms_core_alternative_quality.php");
$library_path 	= $GLOBALS['webroot'].'/library';
$objMUR			= new MUR_Reports;
$objMURcls			= new MUR;

$provider = isset($_REQUEST['provider']) ? trim(strip_tags($_REQUEST['provider'])) : 0;
$dtfrom = isset($_REQUEST['dtfrom']) ? trim(strip_tags($_REQUEST['dtfrom'])) : 0;
$dtupto = isset($_REQUEST['dtupto']) ? trim(strip_tags($_REQUEST['dtupto'])) : 0;

//require_once("functions.php");
//$objDataManage = new ManageData;

$srh_patientId=$_REQUEST['patientId'];
if($_REQUEST['measure_cat']=="cat_3"){
	$srh_patientId="";
}
$count = 1;
$dtfrom=$_REQUEST['dtfrom'];
$dtupto=$_REQUEST['dtupto'];

$currDate = date("YmdHis");
//$dtfrom1=$objDataManage->__getDateFormat($dtfrom);
//$dtupto1=$objDataManage->__getDateFormat($dtupto);
$dtfrom1=get_date_format($dtfrom);
$dtupto1=get_date_format($dtupto);

$pro_id=$_REQUEST['provider'];

//$totalPtIDs = aged_get_denominator($pro_id,18);
$measure = $measures = array();

$measure['0055']['title'] = 'Diabetes: Eye Exam';
$measure['0055']['version_neutral'] = 'd90bdab4-b9d2-4329-9993-5c34e2c0dc66';
$measure['0055']['version_specific'] = '40280381-3D61-56A7-013E-62237D5D24E1';
$measure['0055']['version_no'] = '2';
$measure['0055']['MAT'] = '131';
$measure['0055']['NFQ_no'] = '0055';
$measure['0055']['IPP'] = '488362E0-49E3-4FD0-AC0F-F7BF5B7174E4';
$measure['0055']['DENOM'] = '5D79C027-3938-422E-9E61-7AE9A1ED4CCB';
$measure['0055']['NUMER'] = '0EBCBD3D-0671-4490-8DA7-A517EB491A75';
$measure['0055']['DENEX'] = '959F8819-402B-4834-BD69-12697CBE53FE';
$measure['0055']['data'] = $objMUR->getNQF0055();

$measures['0055'] = $measure['0055'];

$measure['0089']['title'] = 'Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care';
$measure['0089']['version_neutral'] = '53d6d7c3-43fb-4d24-8099-17e74c022c05';
$measure['0089']['version_specific'] = '40280381-3d61-56a7-013e-66b2ca294c47';
$measure['0089']['version_no'] = '2';
$measure['0089']['MAT'] = '142';
$measure['0089']['NFQ_no'] = '0089';
$measure['0089']['IPP'] = 'F914BFB9-0842-4208-9139-ADBB8A7AAAC7';
$measure['0089']['DENOM'] = 'B5E20C03-6A5F-4646-B71D-A754669A2F3C';
$measure['0089']['NUMER'] = 'B385664C-913A-4DF3-8505-52C1E70A4F52';
$measure['0089']['DENEXCEP'] = 'EAC18AF5-ECE0-4FD5-AB92-EB9AF119216E';
$measure['0089']['data'] = $objMUR->getNQF0089();

$measures['0089'] = $measure['0089'];

$measure['0088']['title'] = 'Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema and Level of Severity of Retinopathy';
$measure['0088']['version_neutral'] = '50164228-9d64-4efc-af67-da0547ff61f1';
$measure['0088']['version_specific'] = '40280381-3d61-56a7-013e-57fe7ed437d7';
$measure['0088']['version_no'] = '2';
$measure['0088']['MAT'] = '167';
$measure['0088']['NFQ_no'] = '0088';
$measure['0088']['IPP'] = 'FCF010C0-342A-4EE3-A497-ACF3FDFFBC9F';
$measure['0088']['DENOM'] = '43646797-5070-4A54-8CCF-DCDFF132762F';
$measure['0088']['NUMER'] = '1184C060-9580-44D0-9E0B-4846F4B94F1F';
$measure['0088']['DENEXCEP'] = '53967F51-7CA0-4B4D-B3F8-1FF5F7CB9FF3';
$measure['0088']['data'] = $objMUR->getNQF0088();

$measures['0088'] = $measure['0088'];

$measure['0086']['title'] = 'Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation';
$measure['0086']['version_neutral'] = 'db9d9f09-6b6a-4749-a8b2-8c1fdb018823';
$measure['0086']['version_specific'] = '40280381-3d61-56a7-013e-425ad8e9179c';
$measure['0086']['version_no'] = '2';
$measure['0086']['MAT'] = '143';
$measure['0086']['NFQ_no'] = '0086';
$measure['0086']['IPP'] = 'B213BF2E-302C-4E5B-AE68-603DA144CDE9';
$measure['0086']['DENOM'] = '24C39C36-E27F-431F-8DB3-839682D50B0C';
$measure['0086']['NUMER'] = 'C9DFC49E-317C-47CE-9C0D-6EAC9515704D';
$measure['0086']['DENEXCEP'] = '248BAB87-2D1C-41B9-92D7-032C9CACC470';
$measure['0086']['data'] = $objMUR->getNQF0086();

$measures['0086'] = $measure['0086'];

$measure['0052']['title'] = 'Use of Imaging Studies for Low Back Pain';
$measure['0052']['version_neutral'] = 'b6016b47-b65d-4be0-866f-1d397886ca89';
$measure['0052']['version_specific'] = '40280381-3d61-56a7-013e-62790fe42ca1';
$measure['0052']['version_no'] = '3';
$measure['0052']['MAT'] = '166';
$measure['0052']['NFQ_no'] = '0052';
$measure['0052']['IPP'] = '0830B88A-3DB1-42BC-AD83-5A5C8018094A';
$measure['0052']['DENOM'] = '27397FA7-B01B-4AE2-A494-C8B98276036A';
$measure['0052']['NUMER'] = 'A0558947-9954-4704-A3A3-B96577C456FC';
$measure['0052']['DENEX'] = '3DF163D2-5021-4C1B-B162-89E55A4880E9';
$measure['0052']['data'] = $objMUR->getNQF0052();

$measures['0052'] = $measure['0052'];

$measure['0018']['title'] = 'Controlling High Blood Pressure';
$measure['0018']['version_neutral'] = 'abdc37cc-bac6-4156-9b91-d1be2c8b7268';
$measure['0018']['version_specific'] = '40280381-3d61-56a7-013e-66bc02da4dee';
$measure['0018']['version_no'] = '2';
$measure['0018']['MAT'] = '165';
$measure['0018']['NFQ_no'] = '0018';
$measure['0018']['IPP'] = 'B936D6B5-151F-47FB-97D8-A5AB9AB00656';
$measure['0018']['DENOM'] = '2A53CBF0-510A-429D-814D-CBCA4DC0E2A8';
$measure['0018']['NUMER'] = '5EE417A9-32DB-452F-BAA0-74C5831A4457';
$measure['0018']['DENEX'] = '57F3D1D7-F9FA-437F-A2B9-406A1498E8C0';
$measure['0018']['data'] = $objMUR->getNQF0018();

$measures['0018'] = $measure['0018'];

$measure['0022']['title'] = 'Use of High-Risk Medications in the Elderly';
$measure['0022']['version_neutral'] = 'a3837ff8-1abc-4ba9-800e-fd4e7953adbd';
$measure['0022']['version_specific'] = '40280381-3d61-56a7-013e-65c9c3043e54';
$measure['0022']['version_no'] = '2';
$measure['0022']['MAT'] = '156';
$measure['0022']['NFQ_no'] = '0022';
$measure['0022']['IPP'] = 'EE59C907-09FA-47C7-94CF-BB03E2F18667';
$measure['0022']['DENOM'] = '30B2C98C-09C0-425E-94B0-98A224D8958D';
$measure['0022']['NUMER'] = 'F9870989-EC28-4523-934A-544F19C34ED1';
$measure['0022']['NUMER2'] = '4C09A356-D793-487E-BD27-1031D9BF35B7';
$measure['0022']['data'] = $objMUR->getNQF0022('two');
$measure['0022']['data2'] = $objMUR->getNQF0022('one');

$measures['0022'] = $measure['0022'];

$measure['0028']['title'] = 'Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention';
$measure['0028']['version_neutral'] = 'e35791df-5b25-41bb-b260-673337bc44a8';
$measure['0028']['version_specific'] = '40280381-3d61-56a7-013e-5cd94a4d64fa';
$measure['0028']['version_no'] = '2';
$measure['0028']['MAT'] = '138';
$measure['0028']['NFQ_no'] = '0028';
$measure['0028']['IPP'] = 'F130623A-0505-466B-A2B7-996AC6D9C05A';
$measure['0028']['DENOM'] = '4A7D841F-0C3A-43DB-8220-ED6B1A971AC9';
$measure['0028']['NUMER'] = 'FB081A01-4184-43CB-9ADE-0698C1B82082';
$measure['0028']['DENEXCEP'] = 'F8B92E13-AEC3-4B52-B381-E1BD98164F84';
$arr1 = $objMUR->getNQF0028();
//$arr2 = getNQF0028b();
//$measure['0028']['data'] = array_merge_recursive($arr1, $arr2);
$measure['0028']['data'] = $arr1;
//pre($measure['0028']['data']);
$measures['0028'] = $measure['0028'];

$measure['0421']['title'] = 'Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up';
$measure['0421']['version_neutral'] = '9a031bb8-3d9b-11e1-8634-00237d5bf174';
$measure['0421']['version_specific'] = '40280381-3e93-d1af-013e-d6e2b772150d';
$measure['0421']['version_no'] = '2';
$measure['0421']['MAT'] = '69';
$measure['0421']['NFQ_no'] = '0421';
$measure['0421']['IPP'] = '95D45B98-C6A1-42F4-A0F5-617DF414A2AE';
$measure['0421']['DENOM'] = '73F6ACC4-B252-42CA-8C3B-330EC37F1294';
$measure['0421']['NUMER'] = '875D8E62-44E5-4249-803D-1B36B57DC2DA';
$measure['0421']['DENEX'] = 'D98C4592-BDCE-4BDF-8466-2339A98AD00B';

$measure['0421']['IPP2'] = '3FA95131-7882-49C0-9830-2ACB82E914D8';
$measure['0421']['DENOM2'] = '47F98343-B180-4CD6-9622-1D913DE255B2';
$measure['0421']['NUMER2'] = '22AAB4CB-4452-4BA0-8A45-B340A318092D';
$measure['0421']['DENEX2'] = '6F3694A9-6AE2-4FED-923A-198489AB47B8';
$arr1 = $objMUR->getNQF0421b();
$arr2 = $objMUR->getNQF0421a();
//$measure['0421']['data'] = array_merge_recursive($arr1, $arr2);
$measure['0421']['data'] = $arr1;
$measure['0421']['data2'] = $arr2;
$measures['0421'] = $measure['0421'];



$measure['CMS50v2']['title'] = 'Closing the referral loop: receipt of specialist report';
$measure['CMS50v2']['version_neutral'] = 'f58fc0d6-edf5-416a-8d29-79afbfd24dea';
$measure['CMS50v2']['version_specific'] = '40280381-3d61-56a7-013e-7aa509de6258';
$measure['CMS50v2']['version_no'] = '2';
$measure['CMS50v2']['MAT'] = '50';
$measure['CMS50v2']['NFQ_no'] = '';
$measure['CMS50v2']['IPP'] = '6832D937-1A47-4C0B-A9D6-B29B66E92AA4';
$measure['CMS50v2']['DENOM'] = '6BDAD3A7-4EEB-4BAC-A2D6-DE51AA4DE608';
$measure['CMS50v2']['NUMER'] = '8A300BD2-3DB8-458D-B142-FE59B4711CC2';

$measure['CMS50v2']['data'] = $objMUR->getRefLoop();

$measures['CMS50v2'] = $measure['CMS50v2'];

//pre($measures);
//die();
/* BEGIN AUTHOR DATA */
	$qry_user = "select * from users where id = '".$_SESSION['authId']."'";
	$res_user = imw_query($qry_user);
	$row_user = imw_fetch_assoc($res_user);
	$XML_author_data = '<author>';
	$XML_author_data .= '<time value="'.$currDate.'"/>';
	$XML_author_data .= '<assignedAuthor>';
	if($row_user['user_npi'] != "")
		$XML_author_data .= '<id extension="'.$row_user['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
	else
		$XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';
	
	if($row_user['facility'] > 0){
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_user['facility']."'";
	}
	else{
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
	}
	$res_facility = imw_query($qry_facility);
	$row_facility = imw_fetch_assoc($res_facility);
	
	$XML_author_data .= '<addr use="WP">';
	if($row_facility['street'] != "")
		$XML_author_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
	if($row_facility['city'] != "")
		$XML_author_data .= '<city>'.$row_facility['city'].'</city>';
	if($row_facility['state'] != "")
		$XML_author_data .= '<state>'.$row_facility['state'].'</state>';
	if($row_facility['postal_code'] != "")
		$XML_author_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
	
	$XML_author_data .= '<country>US</country>';
	$XML_author_data .= '</addr>';
	
	if($row_facility['phone'] != "")
		$XML_author_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';

	$XML_author_data .= '<assignedAuthoringDevice>
			<manufacturerModelName>imwemr</manufacturerModelName>
			<softwareName>imwemr</softwareName>
		  </assignedAuthoringDevice>';
	$XML_author_data .= ' <representedOrganization>
						<name>imwemr</name>
					  </representedOrganization>';
	$XML_author_data .= '</assignedAuthor>';
	$XML_author_data .= '</author>';
/* END AUTHOR DATA   */

/* BEGIN CUSTODIAN (FACILITY) DATA */
	$facility = "";
	$qry = "select * from users where id = '".$pro_id."'";
	$res = imw_query($qry);
	$row = imw_fetch_assoc($res);
	$facility = $row['facility'];
	if($facility > 0){
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";
	}
	else{
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
	}
	$res_facility = imw_query($qry_facility);
	$row_facility = imw_fetch_assoc($res_facility);
	
	$XML_custodian_data = '<custodian>';
	$XML_custodian_data .= '<assignedCustodian>';
	$XML_custodian_data .= '<representedCustodianOrganization>';
	$XML_custodian_data .= '<id root="2.16.840.1.113883.19.5"/>';
	if($row_facility['name'] != "")
	$XML_custodian_data .= '<name>'.$row_facility['name'].'</name>';
	
	if($row_facility['phone'] != "")
		$XML_custodian_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
	else
		$XML_custodian_data .= '<telecom nullFlavor="NI"/>';
		
	$XML_custodian_data .= '<addr>';
	if($row_facility['street'] != "")
		$XML_custodian_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
	if($row_facility['city'] != "")
		$XML_custodian_data .= '<city>'.$row_facility['city'].'</city>';
	if($row_facility['state'] != "")
		$XML_custodian_data .= '<state>'.$row_facility['state'].'</state>';
	if($row_facility['postal_code'] != "")
		$XML_custodian_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
	$XML_custodian_data .= '<country>US</country>';
	$XML_custodian_data .= '</addr>';
	
	$XML_custodian_data .= '</representedCustodianOrganization>';
	$XML_custodian_data .= '</assignedCustodian>';
	$XML_custodian_data .= '</custodian>';
/* END CUSTODIAN (FACILITY) DATA */

/* BEGIN LEGAL AUTHENTICATOR */
	/*$XML_legal_authenticator_data = '<legalAuthenticator>
			<time value="'.$currDate.'"/>
			<signatureCode code="S"/>
			<assignedEntity>
			  <id root="bc01a5d1-3a34-4286-82cc-43eb04c972a7"/>
			  <addr>
				<streetAddressLine>202 Burlington Rd.</streetAddressLine>
				<city>Bedford</city>
				<state>MA</state>
				<postalCode>01730</postalCode>
				<country>US</country>
			  </addr>
			  <telecom use="WP" value="tel:(781)271-3000"/>
			  <assignedPerson>
				<name>
				   <given>Henry</given>
				   <family>Seven</family>
				</name>
			 </assignedPerson>
			  <representedOrganization>
				<id root="2.16.840.1.113883.19.5"/>
				<name>Cypress</name>
			  </representedOrganization>
			</assignedEntity>
		  </legalAuthenticator>';*/
	$XML_legal_authenticator_data ='<legalAuthenticator>
										<time value="'.$currDate.'"/>
										<signatureCode code="S"/>
										<assignedEntity>
											<id root="bc01a5d1-3a34-4286-82cc-43eb04c972a7"/>
											<representedOrganization>
												<!-- example root -->
												<id root="2.16.840.1.113883.19.5"/>
												<name>imwemr</name>
											</representedOrganization>
										</assignedEntity>
									</legalAuthenticator>';
/* END LEGAL AUTHENTICATOR*/

/* BEGIN CARE TEAM MEMBERS */
	$sql = "SELECT * FROM users WHERE id = '".$pro_id."'";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$XML_documentationof_data = 
	'<documentationOf typeCode="DOC">
    <serviceEvent classCode="PCPR"> <!-- care provision -->
      <effectiveTime>
        <low value="'.preg_replace("/-/","",$dtfrom1).'"/>
        <high value="'.preg_replace("/-/","",$dtupto1).'"/>
      </effectiveTime>
      <!-- You can include multiple performers, each with an NPI, TIN, CCN. -->
      <performer typeCode="PRF"> 
        <assignedEntity>
          <!-- This is the provider NPI -->
          <id root="2.16.840.1.113883.4.6" extension="'.$row['user_npi'].'" /> 
          <representedOrganization>
			<name>imwemr</name>
          </representedOrganization>
        </assignedEntity>
      </performer>
    </serviceEvent>
  </documentationOf>';
/* END CARE TEAM MEMBERS */


/* BEGIN REPORTING PARAMETER SECTION */
	$XML_reporting_para_section ='<component>
									<section>
									<!-- Reporting Parameters templateId -->
									<templateId root="2.16.840.1.113883.10.20.17.2.1"/>
									<!-- QRDA Category III Reporting Parameters templateId -->
									<templateId root="2.16.840.1.113883.10.20.27.2.2"/>
									<code code="55187-9" codeSystem="2.16.840.1.113883.6.1"/>
									<title>Reporting Parameters</title>
									<text>
									<list>
									<item>Reporting period: '.$dtfrom1.' - '.$dtupto1.'</item>
									</list>
									</text>
										<entry typeCode="DRIV">
										<!-- Reporting Parameters Act -->
										<act classCode="ACT" moodCode="EVN">
											<templateId root="2.16.840.1.113883.10.20.17.3.8"/>
											<code code="252116004" codeSystem="2.16.840.1.113883.6.96" displayName="Observation Parameters"/>
											<effectiveTime>
												<low value="'.str_replace('-','',$dtfrom1).'"/>
												<high value="'.str_replace('-','',$dtupto1).'"/>
											</effectiveTime>
										</act>
										</entry>
									</section>
								</component>';
/* END REPORTING PARAMETER SECTION */

/* BEGIN MEASURE SECTION */
	$XML_measure_section = '<component>
							<section>
							<!-- Implied template Measure Section templateId -->
							<templateId root="2.16.840.1.113883.10.20.24.2.2"/>
							<templateId root="2.16.840.1.113883.10.20.27.2.1"/>
							<code code="55186-1" codeSystem="2.16.840.1.113883.6.1"/>
							<title>Measure Section</title>
							<text>';
							
	foreach($measures as $measure)	{					
	$XML_measure_section .= '<table border = "1" width = "100%">
								<thead>
									<tr>
										<th>eMeasure Title</th>
										<th>Version neutral identifier</th>
										<th>eMeasure Version Number</th>
										<th>NQF eMeasure Number</th>
										<th>eMeasure Identifier (MAT)</th>
										<th>Version specific identifier</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>'.$measure['title'].'</td>
										<td>'.$measure['version_neutral'].'</td>
										<td>'.$measure['version_no'].'</td>
										<td>'.$measure['NFQ_no'].'</td>
										<td>'.$measure['MAT'].'</td>
										<td>'.$measure['version_specific'].'</td>
									</tr>
								</tbody>
							</table>';
	}
	$XML_measure_section .= '</text>';
	foreach($measures as $measure)	{
		
	$XML_measure_entry = '<entry>
								<!-- Entry for measure "'.$measure['NFQ_no'].'" -->
								<!-- Measure Reference and Results -->
								<organizer classCode="CLUSTER" moodCode="EVN">
									<!-- Measure Reference template -->
									<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
									<!-- Measure Reference and Results template -->
									<templateId root="2.16.840.1.113883.10.20.27.3.1"/>
									<statusCode code="completed"/>
										<reference typeCode="REFR">
											<externalDocument classCode="DOC" moodCode="EVN">
												<id root="'.$measure['version_specific'].'"/>
												<!-- This is the NQF Number, root is an NQF OID and for eMeasure Number and extension is the eMeasures NQF number -->';
						if($measure['NFQ_no'] != "")											
						$XML_measure_entry .= '<id root="2.16.840.1.113883.3.560.1" extension="'.$measure['NFQ_no'].'"/>';
	
						$XML_measure_entry .= '<!-- eMeasure Measure Authoring Tool Identifier -->
												<id root="2.16.840.1.113883.3.560.101.2" extension="'.$measure['MAT'].'"/>
												<code code="57024-2" displayName="Health Quality Measure Document" codeSystemName="LOINC" codeSystem="2.16.840.1.113883.6.1" />
												<!-- This is the title of the eMeasure -->
												<text>'.$measure['title'].'</text>
												<!-- setId is the eMeasure version neutral id -->
												<setId root="'.$measure['version_neutral'].'"/>
												<!-- This is the sequential eMeasure Version number -->
												<versionNumber value="'.$measure['version_no'].'"/>
											</externalDocument>
										</reference>';
	$arrGender = getTotalGender($measure['data']['denominator']);
	$XML_measure_entry .=    		    '<!-- SHOULD Reference the measure set it is a member of-->
										<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="488362E0-49E3-4FD0-AC0F-F7BF5B7174E4"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="IPP" codeSystem="2.16.840.1.113883.5.1063" displayName="initial patient population" codeSystemName="ObservationValue"/>
													<entryRelationship typeCode="SUBJ" inversionInd="true">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
															<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
															<value xsi:type="INT" value="'.count($measure['data']['denominator']).'"/>
															<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
															<referenceRange>
															<observationRange>
															<value xsi:type="INT" value="300"/>
															</observationRange>
															</referenceRange>
														</observation>
													</entryRelationship>
													<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGender['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
													<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGender['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
													<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
													 <entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
													<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>	
													<reference typeCode="REFR">
													<externalObservation classCode="OBS" moodCode="EVN">
													<id root="'.$measure['IPP'].'"/>
													</externalObservation>
													</reference>
												</observation>
												
										</component>';
		if(isset($measure['IPP2']) && $measure['IPP2'] != ""){									
	$arrGender = getTotalGender($measure['data2']['denominator']);
	$XML_measure_entry .=    		    '<!-- SHOULD Reference the measure set it is a member of-->
										<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="488362E0-49E3-4FD0-AC0F-F7BF5B7174E4"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="IPP" codeSystem="2.16.840.1.113883.5.1063" displayName="initial patient population" codeSystemName="ObservationValue"/>
													<entryRelationship typeCode="SUBJ" inversionInd="true">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
															<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
															<value xsi:type="INT" value="'.count($measure['data2']['denominator']).'"/>
															<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
															<referenceRange>
															<observationRange>
															<value xsi:type="INT" value="300"/>
															</observationRange>
															</referenceRange>
														</observation>
													</entryRelationship>
													<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGender['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
													<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGender['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
													<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
													 <entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
													<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>	
													<reference typeCode="REFR">
													<externalObservation classCode="OBS" moodCode="EVN">
													<id root="'.$measure['IPP2'].'"/>
													</externalObservation>
													</reference>
												</observation>
												
										</component>';
	}
		$arrGenderNumer = getTotalGender($measure['data']['numerator']);
		$XML_measure_entry .=    		'<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="0EBCBD3D-0671-4490-8DA7-A517EB491A75"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="NUMER" codeSystem="2.16.840.1.113883.5.1063" displayName="Numerator" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data']['numerator']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderNumer['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderNumer['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['NUMER'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
								
			if(isset($measure['NUMER2']) && $measure['NUMER2'] != ""){
				$arrGenderNumr2 = getTotalGender($measure['data2']['numerator']);		
											$XML_measure_entry .=    		'<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="0EBCBD3D-0671-4490-8DA7-A517EB491A75"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="NUMER" codeSystem="2.16.840.1.113883.5.1063" displayName="Numerator" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data2']['numerator']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderNumr2['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderNumr2['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data2']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data2']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data2']['numerator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['NUMER2'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
										}
		$arrGenderDemon = getTotalGender($measure['data']['denominator']);
		$XML_measure_entry .=    		'<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="5D79C027-3938-422E-9E61-7AE9A1ED4CCB"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="DENOM" codeSystem="2.16.840.1.113883.5.1063" displayName="Denominator" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data']['denominator']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDemon['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDemon['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['DENOM'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
		if(isset($measure['DENOM2']) && $measure['DENOM2'] != ""){
		$arrGenderDemon2 = getTotalGender($measure['data2']['denominator']);
		$XML_measure_entry .=    		'<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="5D79C027-3938-422E-9E61-7AE9A1ED4CCB"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="DENOM" codeSystem="2.16.840.1.113883.5.1063" displayName="Denominator" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data2']['denominator']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDemon2['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDemon2['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data2']['denominator']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['DENOM2'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
	}
		if(isset($measure['DENEX']) && $measure['DENEX'] != ""){								
		$arrGenderDenx = getTotalGender($measure['data']['exclusion']);
		$XML_measure_entry .=    		'
										<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="959F8819-402B-4834-BD69-12697CBE53FE"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="DENEX" codeSystem="2.16.840.1.113883.5.1063" displayName="Denominator Exclusions" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data']['exclusion']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDenx['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDenx['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['DENEX'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
		}
		if(isset($measure['DENEX2']) && $measure['DENEX2'] != ""){								
		$arrGenderDenx2 = getTotalGender($measure['data2']['exclusion']);
		$XML_measure_entry .=    		'
										<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="959F8819-402B-4834-BD69-12697CBE53FE"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type="CD" code="DENEX" codeSystem="2.16.840.1.113883.5.1063" displayName="Denominator Exclusions" codeSystemName="ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data2']['exclusion']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDenx2['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderDenx2['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data2']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data2']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data2']['exclusion']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['DENEX2'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
		}
		if(isset($measure['DENEXCEP']) && $measure['DENEXCEP'] != ""){								
		$arrGenderExclu = getTotalGender($measure['data']['exception']);
		$XML_measure_entry .=    		'
										<component>
											<observation classCode="OBS" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.27.3.5"/>
												<id root="959F8819-402B-4834-BD69-12697CBE53FE"/>
												<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4" displayName="Assertion" codeSystemName="ActCode"/>
												<statusCode code="completed"/>
												<value xsi:type = "CD" code = "DENEXCEP" codeSystem = "2.16.840.1.113883.5.1063" displayName = "Denominator Exceptions" codeSystemName = "ObservationValue"/>
												<entryRelationship typeCode="SUBJ" inversionInd="true">
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
														<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
														<value xsi:type="INT" value="'.count($measure['data']['exception']).'"/>
														<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
														<referenceRange>
														<observationRange>
														<value xsi:type="INT" value="300"/>
														</observationRange>
														</referenceRange>
													</observation>
												</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="F" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderExclu['female'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>
												<entryRelationship typeCode="COMP">
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.27.3.6"/>
															<code code="184100006" displayName="patient sex" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT"/>
															<statusCode code="completed"/>
															<value xsi:type="CD" code="M" codeSystem="2.16.840.1.113883.5.1" codeSystemName="AdministrativeGender"/>
															<entryRelationship typeCode="SUBJ" inversionInd="true">
																<observation classCode="OBS" moodCode="EVN">
																	<templateId root="2.16.840.1.113883.10.20.27.3.3"/>
																	<code code="MSRAGG" displayName="rate aggregation" codeSystem="2.16.840.1.113883.5.4" 	codeSystemName="ActCode"/>
																	<value xsi:type="INT" value="'.$arrGenderExclu['male'].'"/>
																	<methodCode code="COUNT" displayName="Count" codeSystem="2.16.840.1.113883.5.84" codeSystemName="ObservationMethod"/>
																</observation>
															</entryRelationship>
														</observation>	
													</entryRelationship>	
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.7"/>
																<code code = "364699009" displayName = "Ethnic Group" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "2186-5" displayName = "Not Hispanic or Latino" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalEthnicity($measure['data']['exception']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
													 </entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.27.3.8"/>
																<code code = "103579009" displayName = "Race" codeSystem = "2.16.840.1.113883.6.96" codeSystemName = "SNOMED CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "1002-5" displayName = "American Indian or Alaska Native" codeSystem = "2.16.840.1.113883.6.238" codeSystemName = "Race &amp; Ethnicity - CDC"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.getTotalRace($measure['data']['exception']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<entryRelationship typeCode = "COMP">
															<observation classCode = "OBS" moodCode = "EVN">
																<templateId root = "2.16.840.1.113883.10.20.24.3.55"/>
																<templateId root = "2.16.840.1.113883.10.20.27.3.9"/>
																<id nullFlavor = "NA"/>
																<code code = "48768-6" displayName = "Payment source" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "SNOMED-CT"/>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "349" codeSystem = "2.16.840.1.113883.3.221.5" codeSystemName = "Source of Payment Typology" displayName = "Other"/>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<observation classCode = "OBS" moodCode = "EVN">
																		<templateId root = "2.16.840.1.113883.10.20.27.3.3"/>
																		<code code = "MSRAGG" displayName = "rate aggregation" codeSystem = "2.16.840.1.113883.5.4" codeSystemName = "ActCode"/>
																		<value xsi:type = "INT" value = "'.count($measure['data']['exception']).'"/>
																		<methodCode code = "COUNT" displayName = "Count" codeSystem = "2.16.840.1.113883.5.84" codeSystemName = "ObservationMethod"/>
																	</observation>
																</entryRelationship>
															</observation>
														</entryRelationship>
												<reference typeCode="REFR">
												<externalObservation classCode="OBS" moodCode="EVN">
												<id root="'.$measure['DENEXCEP'].'"/>
												</externalObservation>
												</reference>
											</observation>
										</component>';
		}
			$XML_measure_entry .= '</organizer>
			</entry>';
			$XML_measure_section .= $XML_measure_entry;	
	}
	$XML_measure_section .= '</section>
		</component>';
/* END MEASURE SECTION*/

$dtfrom1_exp=explode('-',$dtfrom1);
$dtupto1_exp=explode('-',$dtupto1);
$rep_dtfrom1=date('j F Y',mktime(0,0,0,$dtfrom1_exp[1],$dtfrom1_exp[2],$dtfrom1_exp[0]));
$rep_dtto1=date('j F Y',mktime(0,0,0,$dtupto1_exp[1],$dtupto1_exp[2],$dtupto1_exp[0]));

		/* BEGIN XML BODY */
		$XML_cda_body = '<component>';
        $XML_cda_body .= '<structuredBody>';
		
		/*---BEGIN REPORTING PARAMETER SECTION------*/
		$XML_cda_body .=  $XML_reporting_para_section;
		/*---END REPORTING PARAMETER SECTION-----*/
		
		/*---BEGIN MEASURE SECTION------*/
		$XML_cda_body .=  $XML_measure_section;
		/*---END MEASURE SECTION------*/
		
		$XML_cda_body .= '</structuredBody>';
		$XML_cda_body .= '</component>';
		/* END XML BODY */

$xml = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= '<?xml-stylesheet type="text/xsl" href="cda.xsl"?>';
$xml .= '<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		 xmlns="urn:hl7-org:v3"
		 xmlns:voc="urn:hl7-org:v3/voc"
		 xmlns:sdtc="urn:hl7-org:sdtc">
		  <!-- QRDA Header -->
		  <realmCode code="US"/>
		  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>
		  
		  <!-- US Realm Header Template Id -->
		  <templateId root="2.16.840.1.113883.10.20.27.1.1"/>
		  <!-- This is the globally unique identifier for this QRDA document -->';
$xml .= '<id nullFlavor="NI"/>';
$xml .= '<!-- QRDA document type code -->
		  <code code="55184-6" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Quality Reporting Document Architecture Calculated Summary Report"/>
		  <title>QRDA Incidence Report</title>
		  <!-- This is the document creation time -->
		  <effectiveTime value="'.$currDate.'"/>
		  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>
		  <languageCode code="eng"/>
		  <!-- reported patient -->
			<recordTarget>
				<patientRole>
					<id nullFlavor="NA"/>
				</patientRole>
			</recordTarget>';
  
	$xml .= $XML_author_data;
	//if(strpos($_REQUEST['option'],'Demographics') == true || strpos($_REQUEST['option'],'Bulk') == true){
	$xml .= $XML_custodian_data;
	$xml .= $XML_legal_authenticator_data;
	$xml .= $XML_documentationof_data; // CARE TEAM MEMBERS
	
	$xml .= $XML_cda_body;
	$xml .= '</ClinicalDocument>';
	
	$XML_file_name = "qrda_r2_cat3_xml.xml";
	file_put_contents($XML_file_name,$xml);
	
	// IMPLEMENT ENCRYPTION KEY
	$AESPatientID = $AESPatientDOB = $AESPatientDOBMonth = $AESPatientDOBDay = $AESPatientFName = $AESPatientLName = "";
	$AESPatientID = $pid;
	$AESPatientDOB = $rowPatient['DOB'];
	$AESPatientFName = $rowPatient['fname'];
	$AESPatientLName = $rowPatient['lname'];


if($XML_file_name!=""){
	$zipfilename = $XML_file_name;
	
	header('Content-Type: application/file');
	header('Content-disposition: attachment; filename='.$zipfilename);
	header('Content-Length: ' . filesize($zipfilename));
	readfile($zipfilename);
}else{
	echo"<script>window.location.href='create_qrda_r2_temp.php?provider=$pro_id&dtfrom=$dtfrom&dtupto=$dtupto';</script>";
}

?>

<?php 
//  FUNCTIONS
function getTotalGender($arr){
	$arrReturn = array("male"=>0,"female"=>0);
	if(count($arr)>0){
		
	$strPatIDs = implode(",",$arr);
	
	$sql = "SELECT count(*) as counter FROM patient_data WHERE id IN (".$strPatIDs.") AND (sex = 'Male' or sex = 'male')";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$arrReturn['male'] = $row['counter'];
	
	$sql = "SELECT count(*) as counter FROM patient_data WHERE id IN (".$strPatIDs.") AND (sex = 'Female' or sex = 'female')";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$arrReturn['female'] = $row['counter'];
	}
	return $arrReturn;
}

function getTotalEthnicity($arr){
	$totalEthnicity = "0";
	if(count($arr)>0){
	$strPatIDs = implode(",",$arr);
	$sql = "SELECT count(*) as counter FROM patient_data WHERE id IN (".$strPatIDs.") AND ethnicity  = 'Not Hispanic or Latino'";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$totalEthnicity = $row['counter'];
	}
	return $totalEthnicity;
}

function getTotalRace($arr){
	$totalRace = "0";
	if(count($arr)>0){
	$strPatIDs = implode(",",$arr);
	$sql = "SELECT count(*) as counter FROM patient_data WHERE id IN (".$strPatIDs.") AND race  = 'American Indian or Alaska Native'";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$totalRace = $row['counter'];
	}
	return $totalRace;
}


/*function getTotalPaymentSource($arr){
	$totalRace = "0";
	if(count($arr)>0){
	$strPatIDs = implode(",",$arr);
	$sql = "SELECT count(*) as counter FROM patient_data WHERE id IN (".$strPatIDs.") AND race  = 'American Indian or Alaska Native'";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	$totalRace = $row['counter'];
	}
	return $totalRace;
}*/
?>