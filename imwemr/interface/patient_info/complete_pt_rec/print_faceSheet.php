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
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
ob_start();
//echo "<newpage>";
include_once('../../../library/classes/work_view/wv_functions.php');
require_once("../../../library/classes/audit_common_function.php");

function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}


if($cpr->patient_id == ""){
?>
	<script>
		alert('Please select Patient to Proceed');
		window.close();
	</script>
<?php
}
######array for medical histoty##############
$arrMEDHX = array();
$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "None");
$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Glasses");
$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Contact Lenses");
$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Glasses And Contact Lenses");										
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Blurred or Poor Vision");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Poor Night Vision");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Gritty Sensation");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Trouble Reading Signs");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Glare From Lights");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Tearing");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Poor Depth Perception");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Halos Around Lights");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Itching or Burning");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Trouble Identifying Colors");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "See Spots or Floaters");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Eye Pain");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Double Vision");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "See Light Flashes");
$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '15',"Filed_Label_Og_Val"=> "Redness or Bloodshot");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "You other :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");

$arrMEDHXGenralHealth = array();

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "High Blood Pressure");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Heart Problem");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Arthritis");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Lung Problems");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Stroke");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Thyroid Problems");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diabetes");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Ulcers");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Other");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Other");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Weight Loss");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Rash");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Skin Disease");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Fatigue");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Sinus Infection");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Post Nasal Drips");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Runny Nose");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Dry Mouth");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Deafness");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Cough");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Bronchitis");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Shortness of Breath");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Asthma");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Emphysema");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "COPD");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "TB");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Chest Pain");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Congestive Heart Failure");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Irregular Heart beat");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Shortness of Breath");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "High Blood Pressure");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Low Blood Pressure");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Pacemaker/defibrillator");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vomiting");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Ulcers");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diarrhea");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Bloody Stools");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hepatitis");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Jaundice");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Constipation");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Genital Ulcers");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Discharge");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Kidney Stones");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Blood in Urine");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Seasonal Allergies");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Hay Fever");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Headache");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Migraines");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paralysis Fever");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Joint Ache");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Seizures");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Numbness");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Faints");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Stroke");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Multiple Sclerosis");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Alzheimer's Disease");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Parkinson's Disease");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Dementia");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Rashes");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Wounds");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Breast Lumps");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Eczema");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Dermatitis");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Depression");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Anxiety");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paranoia");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Sleep Patterns");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Mental and/or emotional factors");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Alzheimer's Disease");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Parkinson's disease");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Memory Loss");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Anemia");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Blood Transfusions");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Excessive Bleeding");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Purpura");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Infection");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Pain");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Joint Ache");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Stiffness");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Swelling");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Paralysis Fever");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Mood Swings");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Constipation");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Polydipsia");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Hypothyroidism");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hyperthyroidism");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vision loss");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Eye pain");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Double vision");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Headache");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Negative for constitutional");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Negative for ear, nose, mouth & throat");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Negative for respiratory");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Negative for cardiovascular");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Negative for gastrointenstinal");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Negative for genitourinary");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Negative for allergic/immunologic");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Negative for neurological");

$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Negative for integumentary");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Negative for psychiatry");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Negative for hemotologic/lymphatic");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Negative for musculoskeletal");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Negative for endocrine");
$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Negative for eyes");

$arrMEDHXSocial = array();
$arrMEDHXSocial [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Yes");
$arrMEDHXSocial [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "No");


#################################################
######Audit For Patient Export PHI###############
$plPHI = 0;
$plPHI = (int) $_SESSION['AUDIT_POLICIES']['PHI_Export'];

if($plPHI == 1){
	if($_SESSION['PHI_Audit']=="Noo"){
		$_SESSION['PHI_Audit']="Yess";
		$arrAuditTrailPHI = array();
		$opreaterId = $_SESSION['authId'];															 
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);			
		$arrAuditTrailPHI [] = 
					array(
							"Pk_Id"=> $pid,						
							"Table_Name"=> "patient_data",
							"Action"=> "phi_export",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"pid"=> $pid,
							"Category"=> "chart_notes-patient_information",
							"Category_Desc"=> "face_sheet",	
							"Old_Value"=> $pid,
							"Depend_Select"=> "select CONCAT(CONCAT_WS(',',fname,lname),'(',id,')') as patientName" ,
							"Depend_Table"=> "patient_data" ,
							"Depend_Search"=> "id",
							"New_Value"=> $_SESSION['form_id']																																										
						);																		
						//echo '<pre>';
						//print_r($arrAuditTrailPHI);
						//die;
		$table = array("audit_policies");
		$error = array($phiError);
		$mergedArray = merging_array($table,$error);		
		auditTrail($arrAuditTrailPHI,$mergedArray,0,0,0);	
	}				
}	
##############################################
?>
<table cellpadding='0' cellspacing='0'  rules="none" width='100%'>
	<tr>
		<td align="left" colspan="4" height="1" style="border-bottom:1px solid #012778;!important;height:1px;" >&nbsp;</td>
	</tr>
	<tr>
		<td align="left" colspan="4" height="2" ></td>
	</tr>
	<tr>
		<td align="left" colspan="4" style="font-size:14px;color:#012778;font-weight:bold;" ><b><?php echo strtoupper('Patient Demographic'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left" valign="top">
		<td class="text_10b" width="180" valign="bottom" height="5px"><b><?php echo trim($pt_data['title'].' '.ucwords($pt_data['patientName'])); ?></b></td>
		<td class="text_10b" width="180" valign="<?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? "bottom" : "top"; ?>" height="<?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? "5px" : "14px"; ?>" >&nbsp;<b><?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? $pt_data['date_of_birth']."(".$pt_data['age'].")" : underLine(35); ?></b></td>
		<td class="text_10b" width="180" valign="bottom" height="5px"><b><?php echo $pt_data['id']; ?></b></td>		
		<td class="text_10b" width="180" valign="<?php echo (ucwords(trim($pt_data['status']))) ? "bottom" : "top"; ?>" height="<?php echo (ucwords(trim($pt_data['status']))) ? "5px" : "14px"; ?>" >&nbsp;<b><?php echo (ucwords(trim($pt_data['status']))) ? ucwords($pt_data['status']) : underLine(35); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180" valign="top" ><i>Patient Name</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top" ><i>DOB</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top" ><i>ID</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top" ><i>Marital</i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	?>	
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? $pt_data['pt_ss'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['phyName'] != "")) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['phyName']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['phyName']) != "") ? $pt_data['phyName'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['facilityPracCode']) != "") ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['facilityPracCode']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['facilityPracCode']) != "") ? $pt_data['facilityPracCode'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? $pt_data['created_date'] : underLine(35); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180" valign="top"><i>Social Security#</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top"><i>Physician</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top"><i>Facility</i></td>
		<td class="text_10" style="color:#444444" width="180" valign="top"><i>Registration date</i></td>
	</tr>
	<tr><td height="7"></td></tr>			
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['sex'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['sex'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['sex'])) ? $pt_data['sex'] : underline(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['reffPhyName'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['reffPhyName'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['reffPhyName'])) ? $pt_data['reffPhyName'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['driving_licence'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['driving_licence'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['driving_licence'])) ? $pt_data['driving_licence'] : underLine(35) ; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['createByName'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['createByName'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['createByName'])) ? ucwords($pt_data['createByName']) : underLine(35) ;  ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Sex</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Referring Dr.</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>DL #</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Created By</i></td>			
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	$street = trim(ucwords($pt_data['street']));
	if(trim(ucwords($pt_data['street'])) != '' && trim(ucwords($pt_data['street2'])) != ''){
		$street .= '<br>';
	}
	if(trim(ucwords($pt_data['street2']))){
		$street .= trim(ucwords($pt_data['street2']));
	}
	
	?>
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo (trim($street)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($street)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($street)) ? $street : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['cityAddress']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['cityAddress']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['cityAddress']))) ? trim(ucwords($pt_data['cityAddress'])) : underLine(35); ?></b></td>
		<td class="text_10b" style="width:190px; vertical-align:bottom;" height="<?php echo ($pt_data['phone_home'] != "000-000-0000" && trim($pt_data['phone_home']) != "") ? "5px" : "14px"; ?>">&nbsp;<b><?php echo ($pt_data['phone_home'] != "000-000-0000" && trim($pt_data['phone_home']) != "") ? core_phone_format($pt_data['phone_home']) : underLine(35); ?></b></td>
		<td class="text_10b"  width="180" height="<?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? core_phone_format($pt_data['phone_biz']) : underLine(35); ?></b></td>
	</tr>
	
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Street</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>City, State Zip</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Home Phone#</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Work Phone#</i></td>
	</tr>		
	<tr><td height="7"></td></tr>
	<?php
	?>
</table>
<?php
//--- Emergency Contact Details Check ------
?>
<table width="100%" rules="none" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="3"><b><?php echo strtoupper('Emergency Contact Information'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['contact_relationship']) != "") ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['contact_relationship']) != "") ? "bottom," : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['contact_relationship'])) ? $pt_data['contact_relationship'] : underLine(35);  ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['phone_contact']) != "" && $pt_data['phone_contact'] != "000-000-0000") ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['phone_contact']) != "" && $pt_data['phone_contact'] != "000-000-0000") ? "bottom," : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['phone_contact']) && $pt_data['phone_contact'] != "000-000-0000" ) ? core_phone_format($pt_data['phone_contact']) : underLine(35); ?></b></td>
		
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Emergency Contact</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Emergency Phone</i></td>
		
	</tr>
	<tr height="10"><td></td></tr>
</table>
<?php
	$resp_name = $pt_data['res_party_detail'][0]['lname'].', ';
	$resp_name .= $pt_data['res_party_detail'][0]['fname'].' ';
	$resp_name .= $pt_data['res_party_detail'][0]['mname'];
	if(trim($resp_name) == ","){
		$resp_name = "";
	}	
	$dob = $pt_data['res_party_detail'][0]['dob'];
	$dateOfBirth = get_date_format($dob);
	list($resp_y,$resp_m,$resp_d) = explode('-',$dob);
	$resp_age ="";
	if($resp_y!=""){
	$resp_age = get_age($pt_data['res_party_detail'][0]['dob']); //date('Y') - $resp_y;
	}
	$city_address = $pt_data['res_party_detail'][0]['city'].', ';
	$city_address .= $pt_data['res_party_detail'][0]['state'].' ';
	$city_address .= $pt_data['res_party_detail'][0]['zip'];
	if(trim($city_address) == ","){
		$city_address = "";
	}
	?>
<table width="100%" rules="none" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">	
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" width="220" colspan="4"><b><?php echo strtoupper('Responsibility Party'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($resp_name))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($resp_name))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($resp_name))) ? trim(ucwords($resp_name)) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? trim(ucwords($pt_data['res_party_detail'][0]['relation'])) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($dateOfBirth != "--") ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($dateOfBirth != "--" && $dateOfBirth !="") ? $dateOfBirth.' ('.$resp_age.') ' : underline(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['sex']))) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['sex']))) ? trim(ucwords($pt_data['res_party_detail'][0]['sex'])) : underLine(35); ?></b></td>
	</tr>

	<tr align="left">
		<td class="text_10" style="color:#444444" width="180">&nbsp;<i>Name</i></td>
		<td class="text_10"style="color:#444444" width="180">&nbsp;<i>Relation</i></td>
		<td class="text_10" width="180" style="color:#444444">&nbsp;<i>DOB</i></td>
		<td class="text_10"style="color:#444444" width="180">&nbsp;<i>Sex</i></td>
	</tr>
	<tr><td height="7"></td></tr>	
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo ($pt_data['res_party_detail'][0]['ss']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($pt_data['res_party_detail'][0]['ss']) ? $pt_data['res_party_detail'][0]['ss'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($pt_data['res_party_detail'][0]['licence']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($pt_data['res_party_detail'][0]['licence']) ? $pt_data['res_party_detail'][0]['licence'] : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? trim(ucwords($pt_data['res_party_detail'][0]['marital'])) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? trim(core_phone_format($pt_data['res_party_detail'][0]['work_ph'])) : underLine(35); ?></b></td>
	</tr>
	
	<tr align="left">
		<td class="text_10" width="180" style="color:#444444"><i>Social Security#</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>DL #</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>Marital</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>Work Phone#</i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo "5px";} else{ echo "14px";} ?>" valign="<?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo $pt_data['res_party_detail'][0]['address']." ".$pt_data['res_party_detail'][0]['address2'];} else{ echo underLine(35);} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($city_address) ? "5px" : "14px"; ?>" valign="<?php echo ($city_address) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($city_address) ? $city_address : underLine(35) ; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? core_phone_format(trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? core_phone_format(trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) : underLine(35); ?></b></td>
	</tr>
	
	<tr align="left">
		<td class="text_10" width="180" style="color:#444444"><i>Street</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>City, State Zip</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>Home Phone#</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>Mobile#</i></td>
	</tr>
	<tr><td height="7"></td></tr>
</table>
		<?php
//}
//--- get Occupation Details if patient fill in demographics --------
//for($f=0;$f<count($emp_details);$f++){
	if($emp_details[0]['city'])
		$emp_adderss .= $emp_details[0]['city'];
	if($emp_details[0]['state'])
		$emp_adderss .= ', '.$emp_details[0]['state'];
	if($emp_details[0]['postal_code'])
		$emp_adderss .= ' '.$emp_details[0]['postal_code'];
?>
<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="4"><b><?php echo strtoupper('Patient Occupation, Race & Language'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	//if(trim($pt_data['occupation']) != '' || trim($emp_details[0]['name']) != ''){
	?>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['occupation']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['occupation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['occupation']))) ? trim(ucwords($pt_data['occupation'])) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo "5px";}else {echo "14px";} ?>" valign="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo "bottom";}else {echo "top";} ?>">&nbsp;<b><?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {echo underLine(35);} ?></b></td>		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : underLine(35); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444" width="180"><i>Occupation</i></td>
		<td class="text_10"  style="color:#444444" width="180"><i>Employer Name</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Employer Address</i></td>		
		<td class="text_10" style="color:#444444" width="180"><i>City, State Zip</i></td>
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<?php
	//}
	//--- Check That All Fields Are Not Blank -------
	//if(trim($emp_details[0]['street']) != '' || trim($emp_details[0]['street2']) != '' || trim($emp_adderss) != ''){
	?>
	<tr align="left">		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['hipaa_mail']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['hipaa_mail']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php if(trim($pt_data['hipaa_mail'])==1){ echo("Yes");}else if(trim($pt_data['hipaa_mail'])==0) { echo("No");}else{echo(underLine(35));} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['hipaa_voice']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['hipaa_voice']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php if(trim($pt_data['hipaa_voice'])==1){ echo("Yes");}else if(trim($pt_data['hipaa_voice'])==0) { echo("No");}else{echo(underLine(35));} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['genericval1']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['genericval1']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['genericval1']))) ? trim(ucwords($pt_data['genericval1'])) : underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['genericval2']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['genericval2']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['genericval2']))) ? trim(ucwords($pt_data['genericval2'])) : underLine(35); ?></b></td>
	</tr>
	<tr align="left">		
		<td class="text_10" style="color:#444444" width="180"><i>Allow Mail</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Allow Voice Msg</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>User defined 1</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>User defined 2</i></td>
	</tr>
	<?php
	//} 
	?>	
	<tr><td height="7" colspan="4"></td></tr>
	<?php
	//if($pt_data['genericval2'] != '' || $pt_data['language'] != '' || $pt_data['ethnoracial'] != '' || $pt_data['interpretter'] != ''){
	?>
	<tr align="left">		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['language']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['language']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['language']))) ? trim(ucwords($pt_data['language'])) : underLine(35); ?></b></td>
<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!="" || $pt_data['ethnicity']!="") ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!=""  || $pt_data['ethnicity']!="") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!="" || $pt_data['ethnicity']!="") ? trim(ucwords($pt_data['ethnoracial'])).$pt_data['race'].$pt_data['otherRace'].'/'.$pt_data['ethnicity'].$pt_data['otherEthnicity']: underLine(35); ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['interpretter']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['interpretter']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['interpretter']))) ? trim(ucwords($pt_data['interpretter'])) : underLine(35); ?></b></td>

	</tr>
	<tr align="left">		
		<td class="text_10" style="color:#444444" width="180"><i>Language</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Race / Ethnicity</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Interpreter</i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php	
	//}
	?>
</table>
<?php
//}
//---  Patient custom field Data --------
$getCustomField = "select cf.control_lable as adminControlLable,cf.control_type as adminControltype,cf.default_value as adminDefaultvalue,	
					pcf.patient_control_value as patientControlVal from custom_fields cf 
					LEFT JOIN patient_custom_field pcf on 
					(cf.id = pcf.admin_control_id and pcf.patient_id = '$pid') 
					where cf.module = 'Patient_Info' 
					and cf.sub_module ='Demographics' 
					and cf.status = '0' order by cf.id ";

$rsCustomField = imw_query($getCustomField);
if(imw_num_rows($rsCustomField)>0){	
	$counter= 1;
	$controlText = "";
	$controlLabel = "";
	$process = 0;
	while($row = imw_fetch_assoc($rsCustomField)){
		$writeData = "";
		if($process == 0){
			$process = 1;
		?>					
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">
				<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
					<td align="left"><b><?php echo strtoupper('Miscellaneous'); ?></b></td>
				</tr>
				<tr><td height="7"></td></tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">							
			<?php 
		}	
		if($counter == 1){				
			$controlText .= "<tr class=\"bgColor\">";
			$controlLabel .= "<tr>";
		}
		if($row['adminControltype'] == "checkbox"){
			$controlType = "checkbox";								
			if($row['patientCbkControlVal']){
				if($row['patientCbkControlVal'] == "checked"){
					$checked = "checked";
				}									
			}	
			elseif($row['adminCbkDefaultSelect'] == 1){
				$checked = "checked";
			}	
					
			if($row['patientControlVal'] != ""){
				if($row['adminDefaultvalue']){
					$cbkValue = $row['adminDefaultvalue'];
				}
				else{
					$cbkValue = $row['patientControlVal'];
				}
			}
			elseif($row['adminDefaultvalue'] != ""){
				$cbkValue = $row['adminDefaultvalue'];
			}		
			elseif($checked == "checked"){
				$cbkValue = "checked";
			}
			else{
				$cbkValue = "checked";
			}	
					
			$cbkTextBox = "<input type=\"checkbox\" class=\"input_text_10\" value='".$cbkValue."' name='".$row['adminControlName']."' $checked/>";
			if($row['adminDefaultvalue']){
				$cbkTextBoxLabel = $row['adminControlLable']."(".$row['adminDefaultvalue'].")";
			}
			else{
				$cbkTextBoxLabel = $row['adminControlLable'];
			}
		}
		elseif($row['adminControltype'] == "text"){
			$controlType = "text";
			$cbkTextBox = $row['patientControlVal'];
			$cbkTextBoxLabel = $row['adminControlLable'];
		}
		
		$hightMisc = $topBottom = "";
		if($cbkTextBox){
			$writeData = $cbkTextBox;
			$hightMisc = "5px";
			$topBottom = "bottom";
		}
		else{
			$writeData = underLine(35);
			$hightMisc = "14px";
			$topBottom = "top";
		}
		$controlText .= "<td height=\"$hightMisc\" valign=\"$topBottom\">&nbsp;".$writeData."</td>";
		$controlLabel .= "<td class=\"text_10\" style=\"color:#444444\"><i>".$cbkTextBoxLabel."</i></td>";															
		$counter++;
		if($counter == 5){
			echo $controlText .= "</tr><tr><td height=\"7\"></td></tr>";
			echo $controlLabel .= "</tr><tr><td height=\"7\"></td></tr>";
			$controlText = "";
			$controlLabel = "";
			$counter = 1;
		}					
	}
	if($process == 1){
		if(($counter > 1 || $counter == 1) && $counter < 5){
			//echo $controlText .= "</tr>";
			//echo $controlLabel .= "</tr>";													
		}
	}
	?>
	<tr><td></td></tr>
	</table>
	<?php
}
		
//---  Patient demographics history --------
if($detail == 1){
	$previousRecordQry ="SELECT * FROM patient_previous_data WHERE patient_id = '".$pid."' ORDER BY save_date_time DESC";
	$previousRecordRes = imw_query($previousRecordQry);
	if(imw_num_rows($previousRecordRes)>0){
	?>
	<table width="100%" rules="none" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">
		<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
			<td align="left" colspan="8"><b><?php echo strtoupper('Patient demographics history'); ?></b></td>
		</tr>
		<tr><td height="7"></td></tr>
	</table>
	<table width="100%" cellpadding="5" cellspacing="3" border="0">	
		<tr valign="middle" class="text_10">
			<td align="left"><i><b>Title</b></i></td>
			<td align="left"><i><b>First Name</b></i></td>
			<td align="left"><i><b>Middle</b></i></td>
			<td align="left"><i><b>Last Name</b></i></td>
			<td align="left"><i><b>Suffix</b></i></td>
			<td align="left"><i><b>Address</b></i></td>
			<td align="left"><i><b>Home Phone#</b></i></td>
			<td align="left"><i><b>Mobile Phone#</b></i></td>		
		</tr>
		<?php 
		$prevTname=$prevFname=$prevMname=$prevLname=$prevsufix=$prevAddr=$prevPhone=$prevCell='';
		while($previousRecordRow = imw_fetch_array($previousRecordRes)) {
		$prevStreet2		=	'';
		$prevCityStateZip	=	'';
		
		if($previousRecordRow['prev_title']) {
			$prevTname	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_title']).'</span></span><br>';
		}
		if($previousRecordRow['prev_fname']) {
			$prevFname	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_fname']).'</span></span><br>';
		}
		if($previousRecordRow['prev_mname']) {
			$prevMname	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prevMname']).'</span></span><br>';
		}
		if($previousRecordRow['prev_lname']) {
			$prevLname	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_lname']).'</span></span><br>';
		}
		if($previousRecordRow['prev_suffix']) {
			$prevsufix	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_suffix']).'</span></span><br>';
		}
		
		//START CODE TO GET PREVIOUS FULL ADDRESS
		$prevFullAddr = getFullAddress(trim($previousRecordRow['prev_street']),trim($previousRecordRow['prev_street2']),trim($previousRecordRow['prev_city']),trim($previousRecordRow['prev_state']),trim($previousRecordRow['prev_postal_code']));
		if($prevFullAddr) {
			$prevAddr	.=	'<span class="strikeStyl"><span>'.$prevFullAddr.'</span></span><br>';
		}
		//END CODE TO GET PREVIOUS FULL ADDRESS
		
		if($previousRecordRow['prev_phone_home']) {
			$prevPhone	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_phone_home']).'</span></span><br>';
		}
		if($previousRecordRow['prev_phone_cell']) {
			$prevCell	.=	'<span class="strikeStyl"><span>'.stripslashes($previousRecordRow['prev_phone_cell']).'</span></span><br>';
		}
		}
		?>
			<tr class="text_10">	
				<td valign="top" ><?php echo $prevTname;?></td>
				<td valign="top" ><?php echo $prevFname;?></td>
				<td valign="top" ><?php echo $prevMname;?></td>
				<td valign="top" ><?php echo $prevLname;?></td>
				<td valign="top" ><?php echo $prevsufix;?></td>
				<td valign="top"><?php echo $prevAddr;?></td>
				<td valign="top"><?php echo $prevPhone;?></td>
				<td valign="top"><?php echo $prevCell;?></td>
			</tr>
			<tr height="7" class="5"><td></td></tr>
		
		<tr height="7" class="5"><td></td></tr>
	</table>
	<?php
	}
}
//---  Insurance Details --------
if($face_sheet_scan == 1 || $insurance_scan == 1){
	$scan_card_var = true;
}
else{
	$scan_card_var = false;
}
$insProcess = 0;
$qry = imw_query("select * from insurance_case where patient_id = '".$pid."' and case_status = 'Open' order by ins_case_type");
while($row = imw_fetch_array($qry)){
	$caseDetail[] = $row;
}	
for($r=0;$r<count($caseDetail);$r++){		
	//if(count($caseDetail)>0){
	$ins_caseid = "";
	$ins_caseid = $caseDetail[$r]['ins_caseid'];
	$start_date = substr($caseDetail[$r]['start_date'],0,strpos($caseDetail[$r]['start_date'],' '));
	$end_date = substr($caseDetail[$r]['end_date'],0,strpos($caseDetail[$r]['end_date'],' '));
	$openDate = get_date_format($start_date);
	if($end_date != '0000-00-00'){
		$end_Date = get_date_format($end_date);
	}
	//--- Get Insurance Case Type -------
	$ins_case_type = $caseDetail[$r]['ins_case_type'];
	$qry = imw_query("select case_name from insurance_case_types where case_id = '".$ins_case_type."'");
	while($row = imw_fetch_array($qry)){
		$caseType[] = $row;
	}
	//-- Get Responsible Party Name -----
	$resp_name = $pt_data['res_party_detail'][0]['lname'].', ';
	$resp_name .= $pt_data['res_party_detail'][0]['fname'].' ';
	$resp_name .= $pt_data['res_party_detail'][0]['mname'];	
	if(trim($resp_name) == ','){
		$resp_name = 'Self';
	}	
	$insType = array('primary','Secondary','Tertiary');
	$ins = 0;
	foreach($insType as $val){
	    $insPriDetails = array();
		$currDate=date('Y-m-d H:i:s');
		$qry = imw_query("select * from insurance_data where pid='".$pid."' and type='".$val."' and actInsComp='1' and ins_caseid='".$ins_caseid."' and provider > 0 and effective_date <= '".$currDate."' 
				and (expiration_date = '0000-00-00 00:00:00' or expiration_date > '".$currDate."')");
		while($row = imw_fetch_array($qry)){
			$insPriDetails[] = $row;
		}
		if(((count($insPriDetails)>0) && ($val == 'Tertiary')) || (($val == 'primary') || ($val == 'Secondary'))){
			$priInsHeading = $val." insurance carrier details";
			$scan_card = '';
			if($insPriDetails[0]['scan_card'] != '' || $insPriDetails[0]['scan_card2'] != ''){
				$scan_card = '<span class="text_10b" >Ins. Card Scanned</span>';
				//--- Scan Card Print Or Not ------
				if($scan_card_var){					
					if($insPriDetails[0]['scan_card']){
						$scan_card_arr[$val]['scan_card'] = $insPriDetails[0]['scan_card'];
					}
					if($insPriDetails[0]['scan_card2']){
						$scan_card_arr[$val]['scan_card2'] = $insPriDetails[0]['scan_card2'];
					}
				}
			}										
			if($ins == 0){		
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b>Insurance</b></td>
				</tr>		
				<tr><td height="7"></td></tr>				
				<tr> 	
					<td class="text_10b" align="left" width="180" height="<?php echo ($caseDetail[$r]['ins_caseid']) ? "5px" : "14px"; ?>" valign="<?php echo ($caseDetail[$r]['ins_caseid']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($caseDetail[$r]['ins_caseid']) ? $caseDetail[$r]['ins_caseid'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($caseType[0]['case_name']) ? "5px" : "14px"; ?>" valign="<?php echo ($caseType[0]['case_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($caseType[0]['case_name']) ? $caseType[0]['case_name'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($resp_name) ? "5px" : "14px"; ?>" valign="<?php echo ($resp_name) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($resp_name) ? $resp_name : underline(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo (($openDate) ? "5px" : "14px") or (($end_Date) ? "5px" : "14px"); ?>" valign="<?php echo (($openDate) ? "bottom" : "top") or (($end_Date) ? "bottom" : "top"); ?>">&nbsp;<b><?php echo (($openDate) ? $openDate : underLine(20)) .' / '.(($end_Date) ? $end_Date : underLine(20)); ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Case#</i></td>
					<td class="text_10" style="color:#444444"><i>Ins. Case Type</i></td>
					<td class="text_10" style="color:#444444"><i>Responsible Party</i></td>
					<td class="text_10" style="color:#444444"><i>Case Open/End Date</i></td>
				</tr>
			</table>
			<?php
			}
			$ins++;
			?>									
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">			
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b><?php echo strtoupper(trim($priInsHeading)).' '.strtoupper(trim($scan_card)); ?></b></td>
				</tr>	
				<tr><td height="7"></td></tr>		
			<?php
				$provider = $insPriDetails[0]['provider'];
				$qry = imw_query("select name from insurance_companies where id = '".$provider."'");
				$insDetails = imw_fetch_array($qry);
				$insProviderName = "";
				$insProviderName = ucwords(strtolower($insDetails['name']));			
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($insProviderName) ? "5px" : "14px"; ?>" valign="<?php echo ($insProviderName) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insProviderName) ? $insProviderName : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['policy_number']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['policy_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['policy_number']) ? $insPriDetails[0]['policy_number'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['group_number']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['group_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['group_number']) ? $insPriDetails[0]['group_number'] : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['plan_name']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['plan_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['plan_name']) ? $insPriDetails[0]['plan_name'] : underLine(35); ?></b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Carrier</i></td>
					<td class="text_10" style="color:#444444"><i>Policy#</i></td>
					<td class="text_10" style="color:#444444"><i>Group#</i></td>
					<td class="text_10" style="color:#444444"><i>Plan Name</i></td>
				</tr>		
				<tr><td height="7"></td></tr>
			<?php
				$actDate = substr($insPriDetails[0]['effective_date'],0,strpos($insPriDetails[0]['effective_date'],' '));
				$activeDate = '';
				if($actDate != '0000-00-00')
					$activeDate = get_date_format($actDate);
				$expDate = substr($insPriDetails[0]['expiration_date'],0,strpos($insPriDetails[0]['expiration_date'],' '));
				$expireDate = '';
				if($expDate != '0000-00-00')
					$expireDate = get_date_format($expDate);
				$copay = '$'.number_format($insPriDetails[0]['copay'],2);
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($activeDate != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($activeDate != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($activeDate != "--") ? $activeDate : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($pt_data["ss"] != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data["ss"] != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data["ss"] != "--") ? $pt_data["ss"] : underLine(35);//echo $expireDate; ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($copay != "$0.00") ? "5px" : "14px"; ?>" valign="<?php echo ($copay != "$0.00") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($copay != "$0.00") ? $copay : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['referal_required']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['referal_required']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['referal_required']) ? $insPriDetails[0]['referal_required'] : underLine(35); ?></b></td>
				</tr>
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Activation Date</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
					<td class="text_10" style="color:#444444"><i>Copay</i></td>
					<td class="text_10" style="color:#444444"><i>Referral Required</i></td>
				</tr>	
				<tr><td height="7"></td></tr>
			<?php
				$subscriberName = $insPriDetails[0]['subscriber_lname'].', ';
				$subscriberName .= $insPriDetails[0]['subscriber_fname'].' ';
				$subscriberName .= $insPriDetails[0]['subscriber_mname'];
				if(trim($subscriberName) == ","){
					$subscriberName = "";
				}
				
				if($insPriDetails[0]['subscriber_DOB'] != '0000-00-00'){
					$subscriber_DOB = get_date_format($insPriDetails[0]['subscriber_DOB']);
				}	
				else{			
					$subscriber_DOB = '';
				}	
								
				$strToShowRelation = $insPriDetails[0]['subscriber_relationship'];
				if(strtolower($insPriDetails[0]['subscriber_relationship']) == "doughter"){
					$strToShowRelation = "Daughter";
				}
			?>
				<tr align="left"> 
					<td class="text_10b" width="180" height="<?php echo (ucwords($strToShowRelation)) ? "5px" : "14px"; ?>" valign="<?php echo (ucwords($strToShowRelation)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (ucwords($strToShowRelation)) ? ucwords($strToShowRelation) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($subscriberName)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($subscriberName)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($subscriberName)) ? trim($subscriberName) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($subscriber_DOB != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($subscriber_DOB != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($subscriber_DOB != "--") ? $subscriber_DOB : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['subscriber_ss']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['subscriber_ss']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (($insPriDetails[0]['subscriber_ss']) ? $insPriDetails[0]['subscriber_ss'] : underLine(35)); ?></b></td>
				</tr> 
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Subscriber Relation</i></td>
					<td class="text_10" style="color:#444444"><i>Subscriber Name</i></td>
					<td class="text_10" style="color:#444444"><i>DOB</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
				</tr>	
				<tr><td height="7"></td></tr>
			<?php
				$subscriberAddress = $insPriDetails[0]['subscriber_city'].', ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_state'].' ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_postal_code'];
				if(trim($subscriberAddress) == ","){
					$subscriberAddress = "";
				}
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['subscriber_street']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['subscriber_street']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['subscriber_street']) ? $insPriDetails[0]['subscriber_street'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($subscriberAddress)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($subscriberAddress)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($subscriberAddress)) ? trim($subscriberAddress) : underLine(35); ?></b></td>
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>		
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Street</i></td>
					<td class="text_10" style="color:#444444"><i>City, State Zip</i></td>
					<td class="text_10" style="color:#444444"><i>Payment Authorized</i></td>
					<td class="text_10" style="color:#444444"><i>Signature on File</i></td>
				</tr>
			</table>				
			<?php
			if($insPriDetails[0]['referal_required'] == 'Yes'){
				$id = $insPriDetails[0]['id'];
				$qry = imw_query("select * from patient_reff where ins_data_id = $id
						and (no_of_reffs > 0 or now() between effective_date and end_date)");
				while($row = imw_fetch_array($qry)){
					$reffDetails[] = $row;
				}
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">					
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b><?php echo strtoupper('Referral Required '); ?></b></td>
				</tr>	
				<tr><td height="7"></td></tr>	
			<?php
				if($reffDetails[0]['reff_date'] != '0000-00-00')
					$reff_date = get_date_format($reffDetails[0]['reff_date']);
				else
					$reff_date = '';
				$reff_phy_id = $reffDetails[0]['reff_phy_id'];
				//--- get Reffering Physician Name --------
				$qry = imw_query("select concat(LastName,', ',FirstName) as name,MiddleName from
						refferphysician where physician_Reffer_id = $reff_phy_id");
				while($row = imw_fetch_array($qry)){
					$reffPhyDetails[] = $row;
				}
				$pt_data['reffPhyName'] = $reffPhyDetails['name'].' ';
				$pt_data['reffPhyName'] .= $reffPhyDetails['MiddleName'];
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo (trim($pt_data['reffPhyName'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['reffPhyName'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['reffPhyName'])) ? trim($pt_data['reffPhyName']) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($reffDetails[0]['reffral_no'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($reffDetails[0]['reffral_no'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($reffDetails[0]['reffral_no'])) ? trim($reffDetails[0]['reffral_no']) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($reff_date) ? "5px" : "14px"; ?>" valign="<?php echo ($reff_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($reff_date) ? $reff_date : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo "5px";} else{ echo "14px";} ?>" valign="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo $reffDetails[0]['no_of_reffs'] + $reffDetails[0]['reff_used'];} else{ echo underLine(35);} ?></b></td>
				</tr>
			
				<tr>
					<td class="text_10" style="color:#444444" width="180"><i>Referring Dr.</i></td>
					<td class="text_10" style="color:#444444" width="180"><i>Ref #</i></td>
					<td class="text_10" style="color:#444444" width="180"><i>Ref Date</i></td>
					<td class="text_10" style="color:#444444" width="180"><i># of Visits</i></td>
				</tr>		
				<tr><td height="7"></td></tr>
			<?php
				if($reffDetails[0]['effective_date'] != '0000-00-00'){
					$effective_date = get_date_format($reffDetails[0]['effective_date']);
				}	
				else{
					$effective_date = '';
				}	
				if($reffDetails[0]['end_date'] != '0000-00-00'){
					$end_date = get_date_format($reffDetails[0]['end_date']);
				}	
				else{
					$end_date = '';
				}	
			?>
				<tr align="left"> 	
					<td class="text_10b" width="220" height="<?php echo ($effective_date) ? "5px" : "14px"; ?>" valign="<?php echo ($effective_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($effective_date) ? $effective_date : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($end_date)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($end_date)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($end_date)) ? trim($end_date) : underLine(35); ?></b></td>
					<td class="text_10b" colspan="2" height="<?php echo ($reffDetails[0]['note']) ? "5px" : "14px"; ?>" valign="<?php echo ($reffDetails[0]['note']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($reffDetails[0]['note']) ? $reffDetails[0]['note'] : underLine(35); ?></b></td>		
				</tr>
			
				<tr>
					<td class="text_10" style="color:#444444"><i>Start Date</i></td>
					<td class="text_10" style="color:#444444"><i>End date</i></td>
					<td class="text_10" colspan="2" style="color:#444444"><i>Notes</i></td>
				</tr>
				<tr><td height="7"></td></tr>
			</table>
			<?php
			}
		}	
	}
//}
}
//---  Insurance Details End --------
//---  Show Scan Card Documents --------
$pageHeight = 900;
if(count($scan_card_arr)>0){
	//echo '<newpage>';
	foreach($scan_card_arr as $key => $ins_scan_arr){
		foreach($ins_scan_arr as $scan_key => $scan_val){
			$img_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
			//$img_real_path = realpath('../main/uploaddir'.$scan_val);
			$ImageSize = getimagesize($img_real_path);
			$imageSizeArr[] = $ImageSize[1];
			$pageCount ++;
		}
	}
}
$i=1;
if(count($scan_card_arr)>0){
	foreach($scan_card_arr as $key => $ins_scan_arr){
		?>			
		<table width="100%" border="<?php echo $border; ?>" rules="none" cellpadding="1" cellspacing="1">
		<?php
		if(count($ins_scan_arr) > 0){
		?>
			<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
				<td class="text_10" colspan="2"><b><?php echo strtoupper($key.' scan document'); ?></b></td>
			</tr>
		</table>
		<?php			
			foreach($ins_scan_arr as $scan_key => $scan_val){				
				$chk_img=explode("/",$scan_val);
				$chk_img_ext=explode(".",end($chk_img));
				if(end($chk_img_ext)=="pdf"){continue;}
				$img_path = '../../data/'.constant('PRACTICE_PATH').$scan_val;
				$img_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
				
				//$copy_dir_path = explode('/',$img_real_path);
				//array_pop($copy_dir_path);
				//$copy_dir_path = join("/",$copy_dir_path).'/html2pdfprint/';
				
				
				if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){
					$img_name = substr($scan_val,strrpos($scan_val,'/')+1);
					$patient_img[$key.'-'.$scan_key] = $img_name;
					//copy($img_real_path,$copy_dir_path.$img_name);
					//$img_path = $img_name;
					$priImageSize = getimagesize($img_real_path);
					//echo '<pre>';
					//print_r($priImageSize); die;
					$newSize = '';
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						//$newSize = ManageData::imageResize(680,400,710);						
						$newSize = $cpr->imageResize(680,400,710);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
						$priImageSize[0] = 710;
					}		
					elseif($priImageSize[0] > 700){
						//width > 700
						//$newSize = ManageData::imageResize($priImageSize[0],$priImageSize[1],840);						
						$newSize = $cpr->imageResize($priImageSize[0],$priImageSize[1],700);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
						$priImageSize[1] =700;
						
					}					
					elseif($priImageSize[1] > 840){
						//hight > 700
						//$newSize = ManageData::imageResize($priImageSize[0],$priImageSize[1],840);						
						$newSize = $cpr->imageResize($priImageSize[0],$priImageSize[1],800);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
						$priImageSize[1] = 800;
						
					}								
					else{					
						$newSize = $priImageSize[3];
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
						//echo $image_view; die;
					}							
					if($priImageSize[1] > 800 ){					
						//echo '</newpage><newpage>';												
					}								
					?>						
					<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">
						<tr>
							<td class="text_10"><b><?php echo $img_name; ?></b></td>
						</tr>
						<tr>
							<td class="text_10b"><i><?php echo $scan_key; ?></i></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><?php echo $image_view; ?></td>
						</tr>
					</table>
					<?php
					$pageHeight = $pageHeight - $priImageSize[1];	
					if($pageHeight < $imageSizeArr[$i-1] && $i < $pageCount ){
						//echo '</newpage><newpage>';	
						$pageHeight = 900;
					}					
					$i++;				
				}
			}
		}		
	}
}
//echo '</newpage><newpage>';		
$medQry = "select * from ocular where patient_id=$pid";
$medSql = imw_query($medQry);
$result = imw_fetch_assoc($medSql);

//echo '<pre>';
//print_r($result);
//die;
foreach ((array)$result as $key => $value) {	
	switch ($key):
		case "you_wear":														
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$youWearValue = $orignalValue[0];														
		break;
		case "last_exam_date":														
			$lastExamDateValue = get_date_format($value);
		break;		
		case "eye_problems":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$eyeProblemValue = $orignalValue[0];	
			
		break;	
		case "any_conditions_you":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyConditionYouValue = $orignalValue[0];																	
			
		break;		
		case "any_conditions_relative":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyConditionRelativeValue = $orignalValue[0];
			break;									
		case "chronicDesc":												
			$orignalValue = $cpr->getOrignalValWt2Sep($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$chronicDescValue = $orignalValue[0];															
		break;
		case "chronicRelative":																								
			$orignalValue = $cpr->getOrignalValWt2Sep($value,$value,$arrMEDHX,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$chronicRelativeValue = $orignalValue[0];									
		break;	
		case "eye_problems_other":																																			
			$eyeProbOtherValue = wv_formatDate($value);									
		break;		
		case "OtherDesc":																																				
			$otherDescValue = wv_formatDate($value);									
		break;		
				
	endswitch;
}	

$qryGetGenHealth = "select * from general_medicine where patient_id=$pid";
$rsGetGenHealth = imw_query($qryGetGenHealth);
$arrRsGetGenHealth = imw_fetch_assoc($rsGetGenHealth);
//echo '<pre>';
//print_r($arrRsGetGenHealth);
//die;
foreach ((array)$arrRsGetGenHealth as $key => $value) {	
	switch ($key):	
		case "med_doctor":																							
			$medDoctorValueGH = $value;
			break;		
		case "any_conditions_you":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyConditionYouValueGH = $orignalValue[0];	
		break;		
		case "any_conditions_relative":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyConditionRelativeValueGH = $orignalValue[0];
			break;
		case "any_conditions_others_both":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyConditionOtherBothValueGH = $orignalValue[0];
			break;
		case "any_conditions_others":																					
			$anyConditionOtherValueGH = wv_formatDate($value);
			break;
		case "review_sys":							
			$review_sys = $value;
			if(!empty($review_sys)){
				$ar_review_sys = json_decode($review_sys, true);
				$ar_tmp = array('review_intgmntr',	'review_psychiatry', 'review_blood_lymph',
							'review_musculoskeletal','review_endocrine','review_eye');
				foreach($ar_tmp as $k => $v){
					$value = $ar_review_sys[$v];
					if(!empty($value)){
						$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$v);										
						$orignalValue = explode("~~~~",$orignalValue);
						$vgh = $v."GH";	
						$$vgh = "".$orignalValue[0];
					}
					$vother = $v."_others";
					if(!empty($ar_review_sys[$vother])){
						$vothr = $v."OtherGH";
						$$vothr = $ar_review_sys[$vother];
					}
				}
			}
			break;	
		case "review_const":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$anyReviewConstValueGH = $orignalValue[0];
			break;
		case "review_const_others":										
			$reviewConstOtherValueGH = wv_formatDate($value);
			break;
		case "review_head":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewHeadValueGH = $orignalValue[0];
			break;
		case "review_head_others":										
			$reviewHeadOtherValueGH = wv_formatDate($value);
			break;
		case "review_resp":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewRespValueGH = $orignalValue[0];
			break;	
		case "review_resp_others":										
			$reviewRespOtherValueGH = wv_formatDate($value);
			break;	
		case "review_card":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewCardValueGH = $orignalValue[0];
			break;	
		case "review_card_others":										
			$reviewCardOtherValueGH = wv_formatDate($value);
			break;	
		case "review_gastro":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewGastroValueGH = $orignalValue[0];
			break;	
		case "review_gastro_others":										
			$reviewGastroOtherValueGH = wv_formatDate($value);
			break;
		case "review_genit":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewGenitValueGH = $orignalValue[0];
			break;	
		case "review_genit_others":										
			$reviewGenitOtherValueGH = wv_formatDate($value);
			break;		
		case "review_aller":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewAllerValueGH = $orignalValue[0];
			break;	
		case "review_aller_others":										
			$reviewAllerOtherValueGH = wv_formatDate($value);
			break;	
		case "review_neuro":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$reviewNeuroValueGH = $orignalValue[0];
			break;	
		case "review_neuro_others":										
			$reviewNeuroOtherValueGH = wv_formatDate($value);
			break;
		case "desc_u":					
			$descValueGH = wv_formatDate($value);
			break;
		case "desc_r":					
			$descRValueGH = wv_formatDate($value);
			break;		
		case "genMedComments":										
			$genMedCommentsValueGH = wv_formatDate($value);
			break;
		case "relDescHighBp":
			
			$relDescHighBpValueGH =	($value) ? "Relative High Blood Pressure :" : "";											
			$relDescHighBpValueGH .= $value;
			break;
		case "relDescHeartProb":				
			$relDescHeartProbValueGH = ($value) ? "Relative Heart Problem :" : "";																			
			$relDescHeartProbValueGH .= $value;
			break;	
		case "relDescLungProb":	
			$relDescLungProbValueGH = ($value) ? "Relative Lung Problems :" : "";																					
			$relDescLungProbValueGH .= $value;
			break;						
		case "relDescStrokeProb":	
			$relDescStrokeProbValueGH = ($value) ? "Relative Stroke :" : "";																				
			$relDescStrokeProbValueGH .= $value;
			break;	
		case "relDescThyroidProb":
			$relDescThyroidProbValueGH = ($value) ? "Relative Thyroid Problems :" : "";																						
			$relDescThyroidProbValueGH .= $value;
			break;	
		case "relDescArthritisProb":		
			$relDescArthritisProbValueGH = ($value) ? "Relative Arthritis :" : "";																				
			$relDescArthritisProbValueGH .= $value;
			break;	
		case "relDescUlcersProb":	
			$relDescUlcersProbValueGH = ($value) ? "Relative Ulcers :" : "";																				
			$relDescUlcersProbValueGH .= $value;
			break;
		case "ghRelDescOthers":																					
			$ghRelDescOthersValueGH = $value;
			break;	
		case "negChkBx":										
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$negChkBxValueGH = $orignalValue[0];
			break;														
	endswitch;
}	

//Get Medication
$getMedication = "select * from lists where pid='".$pid."' and (type='1' or type='4') order by id";
$rsGetMedication = imw_query($getMedication);
//$arrRsGetMedication = imw_fetch_assoc($rsGetMedication);

//Get Allergies
$getAllergies = "select * from lists where pid='".$pid."' and type in(3,7) order by id";
$rsGetAllergies = imw_query($getAllergies);
//$arrRsGetAllergies = imw_fetch_assoc($rsGetAllergies);
	
//Get Surgeries
$getSurgeries = "select * from lists where pid='".$pid."' and (type='5' or type='6') order by id";
$rsGetSurgeries = imw_query($getSurgeries);
//$arrRsGetSurgeries = imw_fetch_assoc($rsGetSurgeries);

//Get Social History
$getSocialHistory = "select * from social_history where patient_id=$pid";
$rsGetSocialHistory = imw_query($getSocialHistory);
$arrRsrsGetSocialHistory = imw_fetch_assoc($rsGetSocialHistory);

foreach ((array)$arrRsrsGetSocialHistory as $key => $value) {	
	switch ($key):	
		case "smoke":																							
			$orignalValue = $cpr->getOrignalValComa($value,$value,$arrMEDHXSocial,$key);										
			$orignalValue = explode("~~~~",$orignalValue);										
			$smokeValueSOCIAL = $orignalValue[0];
			break;
		case "smoke_perday":																																			
			$smokePerDayValueSOCIAL = $value;
			break;	
		case "list_drugs":																																			
			$smokeListDurgValueSOCIAL = $value;
			break;
		case "alcohal":																																			
			$alcohalValueSOCIAL = $value;
			break;	
		case "otherSocial":																																			
			$otherSocialValueSOCIAL = $value;
		break;			
	endswitch;
}			
if($dont_print_medical==0){
?>
<table width="100%" cellpadding="2" cellspacing="0" border="<?php echo $border; ?>" rules="none">	
	<tr valign="middle" height="25px" bgcolor="#c0c0c0">
		<td colspan="4" class="text_10" >
			<b>Medical History</b>
		</td>		
	</tr>
</table>
<table width="100%" cellpadding="0" border="0" cellspacing="0">
	<tr>
		<td colspan="4" class="text_10b">
			<b>Ocular</b>
		</td>				
	</tr>	
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td class="text_10" colspan="4" valign="top">
			<i>Eye History :-</i>
		</td>
	</tr>	
	<tr>
		<td width="13%"  valign="top" class="text_10">
			<i>Do you wear:</i>
		</td>
		<td width="29%"  valign="top" class="text_10">
			<?php echo $youWearValue; ?>
		</td>
		<td width="17%"  valign="top" class="text_10">
			<i>Last eye exam date:</i>
		</td>
		<td width="41%" valign="top" class="text_10">
			<?php echo $lastExamDateValue; ?>
		</td>
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td class="text_10" valign="top">
			<?php if($eyeProblemValue){?>
			<i>Eye problems :-</i>
			<?php }?>
		</td>	
		<td  class="text_10" valign="top">
			<?php echo $eyeProblemValue; ?>
		</td>	
		<td  class="text_10" valign="top">
			<?php if($eyeProbOtherValue){?>
			<i>Other eye problems :-</i>
			<?php }?>
		</td>		
		<td class="text_10" valign="top">
			<?php echo $eyeProbOtherValue; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td class="text_10" colspan="4" valign="top">
		<?php if($chronicDescValue || $chronicRelativeValue){?>
			Any condition you or blood relative :-
		<?php }?>	
		</td>			
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td colspan="2" class="text_10"  valign="top">
		<?php if($chronicDescValue){?>
			<i>Any condition you :-</i>
		<?php }?>
		</td>		
		<td colspan="2" class="text_10"  valign="top">
		<?php if($chronicRelativeValue){?>
			<i>Any condition blood relative :-</i>
		<?php }?>	
		</td>		
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td colspan="2" class="text_10">			
			<?php echo $chronicDescValue; ?>			
		</td>	
		<td colspan="2" class="text_10" align="left" valign="top">
			<?php echo $chronicRelativeValue; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
</table>
<!-- <table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td  class="text_10"  valign="top">
		<?php 
		$otherDescValue = trim($otherDescValue);
		if(!empty($otherDescValue)){?>
			<i>Other any condition you or blood relative :-</i>
		<?php }		
		?>		
		</td>		
	</tr>
	
	<tr>		
		<td class="text_10">		
			<?php echo $otherDescValue; ?>
		</td>			
	</tr>
</table> -->
<?php  if(
			(!empty($medDoctorValueGH)) || 	
			(!empty($genMedCommentsValueGH)) || 	
			(!empty($anyConditionYouValueGH)) || 
			(!empty($relDescHighBpValueGH)) || 
			(!empty($relDescHeartProbValueGH)) || 
			(!empty($relDescLungProbValueGH)) || 
			(!empty($relDescStrokeProb)) || 
			(!empty($relDescThyroidProb)) || 
			(!empty($relDescArthritisProb)) || 
			(!empty($relDescUlcersProbValueGH)) ||
			(!empty($descValueGH)) ||
			(!empty($descRValueGH)) ||
			(!empty($anyConditionOtherValueGH)) ||
			(!empty($ghRelDescOthersValueGH)) || 
			(!empty($anyReviewConstValueGH)) ||
			(!empty($reviewHeadValueGH)) ||
			(!empty($reviewConstOtherValueGH)) ||
			(!empty($reviewHeadOtherValueGH)) ||
			(!empty($reviewRespValueGH)) ||
			(!empty($reviewCardValueGH)) ||
			(!empty($reviewRespOtherValueGH)) ||
			(!empty($reviewCardOtherValueGH)) ||					
			(!empty($reviewGastroValueGH)) ||
			(!empty($reviewGenitValueGH)) ||
			(!empty($reviewGastroOtherValueGH)) ||
			(!empty($reviewGenitOtherValueGH)) ||					
			(!empty($reviewAllerValueGH)) ||
			(!empty($reviewNeuroValueGH)) ||
			(!empty($reviewAllerOtherValueGH)) ||
			(!empty($reviewNeuroOtherValueGH))
		){?>
<table width="100%" cellpadding="2" cellspacing="0">	
	<tr>
		<td colspan="4" class="text_10b">
			<b>General Health</b>
		</td>				
	</tr>	
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td width="18%" valign="top" class="text_10">
			<?php if($medDoctorValueGH){?>
			<i><b>Medical Doctor :-</b></i>
			<?php }?>		
		</td>
		<td class="text_10" colspan="3" valign="top">
			<?php echo $medDoctorValueGH; ?>
		</td>
	</tr>	
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td class="text_10" valign="top">
		<?php if($genMedCommentsValueGH){?>
			<i><b>Comments :-</b></i>
		<?php }?>		
		</td>
		<td class="text_10" colspan="3" valign="top">
			<?php echo $genMedCommentsValueGH; ?>
		</td>
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td class="text_10" colspan="4" valign="top">
		<?php 		
		if(	
			(!empty($anyConditionYouValueGH)) || 
			(!empty($relDescHighBpValueGH)) || 
			(!empty($relDescHeartProbValueGH)) || 
			(!empty($relDescLungProbValueGH)) || 
			(!empty($relDescStrokeProb)) || 
			(!empty($relDescThyroidProb)) || 
			(!empty($relDescArthritisProb)) || 
			(!empty($relDescUlcersProbValueGH)) ||
			(!empty($descValueGH)) ||
			(!empty($descRValueGH)) ||
			(!empty($anyConditionOtherValueGH)) ||
			(!empty($ghRelDescOthersValueGH)) || 
			(!empty($anyReviewConstValueGH)) ||
			(!empty($reviewHeadValueGH)) ||
			(!empty($reviewConstOtherValueGH)) ||
			(!empty($reviewHeadOtherValueGH)) ||
			(!empty($reviewRespValueGH)) ||
			(!empty($reviewCardValueGH)) ||
			(!empty($reviewRespOtherValueGH)) ||
			(!empty($reviewCardOtherValueGH)) ||					
			(!empty($reviewGastroValueGH)) ||
			(!empty($reviewGenitValueGH)) ||
			(!empty($reviewGastroOtherValueGH)) ||
			(!empty($reviewGenitOtherValueGH)) ||					
			(!empty($reviewAllerValueGH)) ||
			(!empty($reviewNeuroValueGH)) ||
			(!empty($reviewAllerOtherValueGH)) ||
			(!empty($reviewNeuroOtherValueGH))
		){?>
			Any condition you or blood relative :-
		<?php }?>	
		</td>			
	</tr>
	<tr><td height="7" colspan="4"></td></tr>	
	<tr>
		<td colspan="2" class="text_10"  valign="top">
		<?php if($anyConditionYouValueGH){?>
			<i><b>Any condition you :-</b></i>
		<?php }?>			
		</td>		
		<td width="59%" colspan="2"  valign="top" class="text_10">
		<?php if($relDescHighBpValueGH || $relDescHeartProbValueGH || $relDescLungProbValueGH || $relDescStrokeProb || $relDescThyroidProb || $relDescArthritisProb || $relDescUlcersProbValueGH){?>
			<i><b>Any condition blood relative :-</b></i>
		<?php }?>			
		</td>		
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td colspan="2" class="text_10">
			<?php echo $anyConditionYouValueGH; ?>
		</td>	
		<td colspan="2" class="text_10" align="left" valign="top">
			<?php echo $relDescHighBpValueGH.'<br>'.$relDescHeartProbValueGH.'<br>'.$relDescLungProbValueGH.'<br>'.$relDescStrokeProb.'<br>'.$relDescThyroidProb.'<br>'.$relDescArthritisProb.'<br>'.$relDescUlcersProbValueGH.'<br>'; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>
		<td colspan="2" class="text_10">
		<?php if($descValueGH){?>
			You Diabetes :
		<?php }?>			
		</td>	
		<td colspan="2" class="text_10">
		<?php if($descRValueGH){?>
			Relative Diabetes :
		<?php }?>		
		</td>			
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>		
		<td colspan="2" class="text_10" align="left" valign="top">
			<?php echo $descValueGH; ?>
		</td>	
		<td colspan="2" class="text_10" align="left" valign="top">
			<?php echo $descRValueGH; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>		
		<td colspan="4" class="text_10" align="left" valign="top">
		<?php if($anyConditionOtherValueGH){?>
			Any condition other
		<?php }?>			
		</td>			
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>				
		<td colspan="4" class="text_10" align="left" valign="top">
			<?php echo $anyConditionOtherValueGH; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>		
		<td colspan="4" class="text_10" align="left" valign="top">
		<?php if($ghRelDescOthersValueGH){?>
			Relative condition other
		<?php }?>				
		</td>			
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>				
		<td colspan="4" class="text_10" align="left" valign="top">
			<?php echo $ghRelDescOthersValueGH; ?>
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<tr>				
		<td colspan="4" class="text_10" align="left" valign="top">
		<?php if(
				(!empty($anyReviewConstValueGH)) ||
				(!empty($reviewHeadValueGH)) ||
				(!empty($reviewConstOtherValueGH)) ||
				(!empty($reviewHeadOtherValueGH)) ||
				(!empty($reviewRespValueGH)) ||
				(!empty($reviewCardValueGH)) ||
				(!empty($reviewRespOtherValueGH)) ||
				(!empty($reviewCardOtherValueGH)) ||					
				(!empty($reviewGastroValueGH)) ||
				(!empty($reviewGenitValueGH)) ||
				(!empty($reviewGastroOtherValueGH)) ||
				(!empty($reviewGenitOtherValueGH)) ||					
				(!empty($reviewAllerValueGH)) ||
				(!empty($reviewNeuroValueGH)) ||
				(!empty($reviewAllerOtherValueGH)) ||
				(!empty($reviewNeuroOtherValueGH)) ||
				(!empty($review_intgmntrGH)) ||
				(!empty($review_psychiatryGH)) ||
				(!empty($review_blood_lymphGH)) ||				
				(!empty($review_musculoskeletalGH)) ||
				(!empty($review_endocrineGH)) ||
				(!empty($review_eyeGH)) ||				
				(!empty($negChkBxValueGH))
					
				){?>
			<i>Review of Systems</i>
		<?php }?>	
		</td>	
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<?php
	
		//ROS --
		$ros_pos=array(  $reviewAllerValueGH, $reviewCardValueGH,
					$anyReviewConstValueGH, $reviewHeadValueGH,
					$review_endocrineGH, $review_eyeGH, 
					$reviewGastroValueGH, $reviewGenitValueGH,
					$review_blood_lymphGH, $review_intgmntrGH,
					$review_musculoskeletalGH,$reviewNeuroValueGH,
					$review_psychiatryGH,$reviewRespValueGH);
		$ros_pos_other=array( 
						$reviewAllerOtherValueGH,$reviewCardOtherValueGH,
						$reviewConstOtherValueGH,$reviewHeadOtherValueGH,
						$review_endocrineOtherGH,$review_eyeOtherGH,
						$reviewGastroOtherValueGH,$reviewGenitOtherValueGH,
						$review_blood_lymphOtherGH,$review_intgmntrOtherGH,
						$review_musculoskeletalOtherGH, $reviewNeuroOtherValueGH, 
						$review_psychiatryOtherGH,$reviewRespOtherValueGH);
		$cpr->get_med_ros($ros_pos, $ros_pos_other, $negChkBxValueGH, '2');
	
	?>	
	<tr><td height="7" colspan="7"></td></tr>
</table>
<?php 
}
if (imw_num_rows($rsGetMedication) > 0) {	
?>
<table width="100%" cellpadding="2" cellspacing="0">	
	<tr>
		<td width="100%" colspan="7" class="text_10b">
			<b>Medication</b>
		</td>				
	</tr>	
	<tr><td height="7" colspan="7"></td></tr>
	<tr>
		<td class="text_10" nowrap valign="top">
			<i><b>Ocular</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Medication</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Strength</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Prescribed By</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Begin Date</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>End Date</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Comments</b></i>
		</td>		
	</tr>	
	<tr><td height="7"></td></tr>
	
	<?php 	
	while($row = imw_fetch_assoc($rsGetMedication)){
	?>
		<tr>
		<?php 
		if($row["type"] == "1"){
			?>
			<td class="text_10" valign="top">
			
			</td>
			<?php	
		}	
		else if($row["type"] == "4"){
			?>
			<td class="text_10" valign="top">
				Ocular
			</td>
			<?php	
		}
			?>
			<td class="text_10" valign="top">
				<?php echo $row["title"]; ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo $row["destination"]; ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo $row["referredby"]; ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo get_date_format($row["begdate"]); ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo get_date_format($row["enddate"]); ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo wv_formatDate($row["comments"]); ?>
			</td>
		</tr>				
			<?php								
	}
	?>
	<tr><td height="7" colspan="7"></td></tr>
</table>
<?php
}

if (imw_num_rows($rsGetAllergies) > 0) {	
?>
<table width="100%" cellpadding="2" cellspacing="0">	
	<tr>
		<td width="100%" colspan="7" class="text_10b">
			<b>Allergies</b>
		</td>				
	</tr>	
	<tr><td height="7" colspan="7"></td></tr>
	<tr>
		<td class="text_10" nowrap valign="top">
			<i><b>Drug</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Name</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Begin Date</b></i>
		</td>
		
		<td class="text_10" nowrap valign="top">
			<i><b>Acute</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Chronic</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Reactions</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Comments</b></i>
		</td>		
	</tr>	
	<tr><td height="7"></td></tr>
	
	<?php 	
	while($row = imw_fetch_assoc($rsGetAllergies)){
	?>
		<tr>
		<?php 
		if($row["type"] == "7"){
			?>
			<td class="text_10" valign="top">
				Drug
			</td>
			<?php	
		}
			?>
			<td class="text_10" valign="top">
				<?php echo $row["title"]; ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo get_date_format($row["begdate"]); ?>
			</td>
			<td class="text_10" valign="top">
				<?php echo ucwords($row["acute"]); ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo ucwords($row["chronic"]); ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo ucwords($row["reactions"]); ?>
			</td>	
			<td class="text_10" valign="top">
				<?php echo ucwords($row["comments"]); ?>
			</td>		
		</tr>				
			<?php								
	}
	?>
	<tr><td height="7" colspan="7"></td></tr>
</table>
<?php
}

if (imw_num_rows($rsGetSurgeries) > 0) {	
?>
<table width="100%" cellpadding="2" cellspacing="0">	
	<tr>
		<td width="100%" colspan="7" class="text_10b">
			<b>Surgeries</b>
		</td>				
	</tr>	
	<tr><td height="7" colspan="7"></td></tr>
	<tr>
		<td class="text_10" nowrap valign="top">
			<i><b>Ocular</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Name</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Date of Surgery</b></i>
		</td>
		
		<td class="text_10" nowrap valign="top">
			<i><b>Physician</b></i>
		</td>
		<td class="text_10" nowrap valign="top">
			<i><b>Comments</b></i>
		</td>		
	</tr>	
	<tr><td height="7" colspan="5"></td></tr>
	
	<?php 		
	while($row = imw_fetch_assoc($rsGetSurgeries)){
	?>
		<tr>
		<?php 
		if($row["type"] == "6"){
			?>
			<td class="text_10" valign="top">
				Ocular
			</td>
			<?php	
		}else if($row["type"] == "5"){
			?>
			<td class="text_10" valign="top">
				
			</td>
			<?php	
		}
		
			?>
			<td class="text_10" valign="top">
				<?php echo $row["title"]; ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo get_date_format($row["begdate"]); ?>
			</td>
			<td class="text_10" valign="top">
				<?php echo ucwords($row["referredby"]); ?>
			</td>
			
			<td class="text_10" valign="top">
				<?php echo ucwords($row["comments"]); ?>
			</td>			
		</tr>				
			<?php								
	}
	?>
	<tr><td height="7" colspan="5"></td></tr>
</table>
<?php
}
if (imw_num_rows($rsGetSocialHistory) > 0) {	
	if($smokeValueSOCIAL || $smokePerDayValueSOCIAL || $smokeListDurgValueSOCIAL || $alcohalValueSOCIAL || $otherSocialValueSOCIAL){ 
?>
	<table width="100%" cellpadding="2" cellspacing="0" border="0">	
		<tr>
			<td  colspan="2" class="text_10b">
				<b>Social</b>
			</td>				
		</tr>	
		<?php if($smokeValueSOCIAL || $smokePerDayValueSOCIAL){ ?>
		<tr><td height="7" colspan="2"></td></tr>
		<tr>			
			<td width="11%"  class="text_10b">
				<?php echo $smokeValueSOCIAL; ?>
			</td>			
			<td width="89%"  class="text_10b">
				<?php echo $smokePerDayValueSOCIAL; ?>
			</td>				
		</tr>
		<tr>
			<td   class="text_10b">
			<?php if($smokeValueSOCIAL){ ?>
				<i><b>Smoke</b></i>
			<?php } ?>	
			</td>				
			<td   class="text_10b">
			<?php if($smokePerDayValueSOCIAL){ ?>
				<i><b>Per Day</b></i>
			<?php } ?>
			</td>					
		</tr>
		<?php } ?>
		<?php if($smokeListDurgValueSOCIAL){ ?>
		<tr><td height="7" colspan="2"></td></tr>
		<tr>
			<td  class="text_10b">
				<i><b>List any Drugs:</b></i>
			</td>	
			<td  class="text_10b">
				<?php echo $smokeListDurgValueSOCIAL; ?>
			</td>				
		</tr>
		<?php } ?>
		<?php if($alcohalValueSOCIAL){ ?>
		<tr><td height="7" colspan="2"></td></tr>
		<tr>
			<td   class="text_10b">
				<i><b>Alcohol:</b></i>
			</td>	
			<td   class="text_10b">
				<?php echo $alcohalValueSOCIAL; ?>
			</td>				
		</tr>
		<?php } ?>
		<?php if($otherSocialValueSOCIAL){ ?>
		<tr><td height="7" colspan="2"></td></tr>
		<tr>
			<td  class="text_10b">
				<i><b>Other:</b></i>
			</td>	
			<td  class="text_10b">
				<?php echo $otherSocialValueSOCIAL; ?>
			</td>				
		</tr>
		<?php } ?>
	</table>
	<?php
		}
	}
}//Dont Print Medical History
//echo '</newpage>';	
//die;
$patient_print_data = ob_get_contents();
ob_end_clean();
?>
