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
File: imm_data_arrays.php
Purpose: Immunization arrays are defined
Access Type: Include 
*/
/*------MASTER DATA DEFINITION ARRAYS----------*/
/*      USED FOR DATA VALUE MAPPING            */
/*---------------------------------------------*/

//IMMUNIZATION REGISTRY STATUS MAPPING ARRAY
$imm_reg_status		= array(
						"A"=>"Active",
						"I"=>"Inactive-Unspecified",
						"L"=>"Inactive-Lost to follow-up (cannot contact)",
						"M"=>"Inactive-Moved or gone elsewhere (transferred)",
						"P"=>"Inactive Permanently inactive",
						"U"=>"Unknown"
						);


//HL7 DEFINED TABLE 0322 - COMPLETION STATUS
$imm_completion_status 	= array(
						"CP" => "Complete",
						"RE" => "Refused",
						"NA" => "Not Administered",
						"PA" => "Partially Administered"
						);
						

//RACE DATA MAPPING ARRAY
$race_data_array 	= array(
						'American Indian or Alaska Native' => "1002-5",
						'Asian' => "2028-9",
						'Black or African American' => "2054-5",
						'Native Hawaiian or Other Pacific Islander' => "2076-8",
						'White' => "2106-3",
						'Other Race' => "2131-1"
						);


//ETHNICITY DATA MAPPING ARRAY
$ethnicity_data_array = array(
						  'African Americans' => "N",
						  'American Indians' => "N",
						  'Chinese' => "N",
						  'European Americans' => "N",
						  'Jewish' => "N",
						  'Hispanic or Latino' => "2135-2",
						  'Not Hispanic or Latino'=> '2186-5',
						  "Unknown" => "U",
						  'Other' => "U"
						  );
						  
						  
//PUBLICITY DATA MAPPING ARRAY
$publicity_code_array = array(
						  "01"=>"No reminder/recall",
						  "02"=>"Reminder/recall - any method",
						  "03"=>"Reminder/recall - no calls",
						  "04"=>"Reminder only - any method",
						  "05"=>"Reminder only - no calls",
						  "06"=>"Recall only - any method",
						  "07"=>"Recall only - no calls",
						  "08"=>"Reminder/recall - to provider",
						  "09"=>"Reminder to provider",
						  "10"=>"Only reminder to provider no recall",
						  "11"=>"Recall to provider",
						  "12"=>"Only recall to provider no reminder"
						  );


//HL70063 (RELATIONSHIP DATA MAPPING ARRAY)
$relationship_codes	= array(
						'ASC'=>'Associate',
						'BRO'=>'Brother',
						'CGV'=>'Care Giver',
						'CHD'=>'Child',
						'DEP'=>'Handicapped Dependent',
						'DOM'=>'Life Partner',
						'EMC'=>'Emergency Contact',
						'EME'=>'Employee',
						'EMR'=>'Employer',
						'EXF'=>'Extended Family',
						'FCH'=>'Foster Child',
						'FMN'=>'Form completed by (Name)-Manufacturer',
						'FND'=>'Friend',
						'FOT'=>'Form completed by (Name)-Other',
						'FPP'=>'Form completed by (Name)-Patient/Parent',
						'FTH'=>'Father',
						'FVP'=>'Form completed by (Name)-Vaccine provider',
						'GCH'=>'Grandchild',
						'GRD'=>'Guardian',
						'GRP'=>'Grandparent',
						'MGR'=>'Manager',
						'MTH'=>'Mother',
						'NCH'=>'Natural child',
						'NON'=>'None',
						'OAD'=>'Other adult',
						'OTH'=>'Other',
						'OWN'=>'Owner',
						'PAR'=>'Parent',
						'SCH'=>'Stepchild',
						'SEL'=>'Self',
						'SIB'=>'Sibling',
						'SIS'=>'Sister',
						'SPO'=>'Spouse',
						'TRA'=>'Trainer',
						'UNK'=>'Unknown',
						'VAB'=>'Vaccine administered by (Name)',
						'WRD'=>'Ward of court'
						);


//HL70162 (ROUTESET DATA MAPPING ARRAY)
$routeset_codes	= array(
					'ID'=>'Intradermal',
					'IM'=>'Intramuscular',
					'IV'=>'Intravenous',
					'NS'=>'Nasal',
					'OTH'=>'Other/Miscellaneous',
					'PO'=>'Oral',
					'SC'=>'Subcutaneous',
					'TD'=>'Transdermal'
					);

//ROUTESET NCI CODES MAPPING ARRAY
$routeset_nci_codes = array(
						'ID'=>'C38238',
						'IM'=>'C28161',
						'IV'=>'C38276',
						'NS'=>'C38284',
						'OTH'=>'Other/Miscellaneous',
						'PO'=>'C38288',
						'SC'=>'C38299',
						'TD'=>'C38305'
						);
						
						
						
//HL70163 (BODY SITE CODESET, DATA MAPPING ARRAY)
$bodysite_codes	= array(
					'LA'=>'Left Upper Arm',
					'LD'=>'Left Deltoid',
					'LG'=>'Left Gluteous Medius',
					'LLFA'=>'Left Lower Forearm',
					'LT'=>'Left Thigh',
					'LVL'=>'Left Vastus Lateralis',
					'RA'=>'Right Upper Arm',
					'RD'=>'Right Deltoid',
					'RG'=>'Right Gluteous Medius',
					'RLFA'=>'Right Lower Forearm',
					'RT'=>'Right Thigh',
					'RVL'=>'Right Vastus Lateralis'
					);


//HL70064 (FINANCIAL CLASS (VFC) CODESET DATA MAPPING ARRAY)
$vfc_codes	= array('V01'=>'Not VFC eligible',
					'V02'=>'VFC eligible-Medicaid/Medicaid Managed Care',
					'V03'=>'VFC eligible- Uninsured',
					'V04'=>'VFC eligible- Americal Indian/Alaskan Native',
					'V05'=>'VFC eligible-Federally Qualified Health Center Patient (under-insured)',
					'V06'=>'VFC eligible- State specific eligibility (e.g. S-CHIP plan)',
					'V07'=>'VFC eligibility- Local-specific eligibility',
					'V08'=>'Not VFC eligible-Under-insured'
					);

function get_MVX_code_Vals($name){
	if(trim($name)==''){return false;}
	$xml_file = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/MVXxml.xml";
	$xml=simplexml_load_file($xml_file);
	foreach($xml->MVXInfo as $MVX_companies){
		if(strtolower(trim($name))==strtolower(trim($MVX_companies->ManufacturerName))){
				return trim($MVX_companies->MVX_CODE);			
		}else{
			$compNameArr = explode(', ',$MVX_companies->ManufacturerName);	
			if(strtolower(trim($name))==strtolower(trim($compNameArr['0']))){
				return trim($MVX_companies->MVX_CODE);
			}
		}
	}
	return false;
}

// function getRespParty($pt_id){
// 	$q = "SELECT * from resp_party WHERE patient_id = '".$pt_id."'";
// 	$r = imw_query($q);
// 	if($r && imw_num_rows($r)==1){
// 		$rs = imw_fetch_assoc($r);
// 		return $rs;
// 	}
// 	return false;
// }

function getCDC_codeset($type='NIP001'){
	$q = "SELECT * from dd_cdc_nip001";
	if($type=='NIP002'){$q = "SELECT * from dd_cdc_nip002";}
	$r = imw_query($q);
	if($r && imw_num_rows($r)>0){
		$rs = array();
		while($rs1 = imw_fetch_assoc($r)){
			$rs[] = $rs1;
		}
		return $rs;
	}
	return false;
}

?>