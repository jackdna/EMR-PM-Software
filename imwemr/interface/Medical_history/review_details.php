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
include_once("../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
$patient_id = $_SESSION['patient'];
$cls_review = new CLSReviewMedHx();
$library_path = $GLOBALS['webroot'].'/library';

$masterId = $_REQUEST['masterId'];
$secName = $_REQUEST['secName'];
$opId = $_REQUEST['opId'];
$dateTime = $_REQUEST['dateTime'];
$arrMEDHX = array();
$arrMEDHX [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "None");
$arrMEDHX [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Glasses");
$arrMEDHX [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Contact Lenses");
$arrMEDHX [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Glasses And Contact Lenses");										
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Blurred or Poor Vision :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Poor Night Vision :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Gritty Sensation :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Trouble Reading Signs :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Glare From Lights :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Tearing :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Poor Depth Perception :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Halos Around Lights :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Itching or Burning :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Trouble Identifying Colors :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "See Spots or Floaters :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Eye Pain :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Double Vision :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "See Light Flashes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '15',"Filed_Label_Og_Val"=> "Redness or Bloodshot :&#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "eye_problem_other_check","Filed_Label_Val"=> 'yes',"Filed_Label_Og_Val"=> "Eye Problems Others :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "eye_problem_other_check","Filed_Label_Val"=> 'no',"Filed_Label_Og_Val"=> "Eye Problems Others :&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Keratoconus :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Keratoconus :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Keratoconus :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "You other :");

$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Keratoconus :");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");

$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Keratoconus :");
$arrMEDHX [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");

$arrMEDHX [] = array("Filed_Label"=> "any_conditions_other_u","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_other_u","Filed_Label_Val"=> '',"Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_other_r","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_any_conditions_other_r","Filed_Label_Val"=> '',"Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "this_blood_sugar_fasting","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fasting :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "this_blood_sugar_fasting","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Fasting :&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You High Blood Pressure :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Heart Problem :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "You Arthritis :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Lung Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Stroke :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Thyroid Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Diabetes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "You Ulcers :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "You LDL :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "You Cancer :&#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You High Blood Pressure :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Heart Problem :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "You Arthritis :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Lung Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Stroke :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Thyroid Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Diabetes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "You Ulcers :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "You LDL :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "You Cancer :&#x2713;");


$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Relative LDL :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Relative Cancer :&#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Relative LDL :&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Relative Cancer :&#x2713;");


$arrMEDHX [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Other &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "any_conditions_others_rel","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Other &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Weight Loss &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Rash &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Skin Disease &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Fatigue &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Sinus Infection &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Post Nasal Drips &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Runny Nose &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Dry Mouth &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Deafness &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Cough &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Bronchitis &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Shortness of Breath &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Asthma &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Emphysema &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "COPD &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "TB &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Chest Pain &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Congestive Heart Failure &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Irregular Heart beat &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Shortness of Breath &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "High Blood Pressure &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Low Blood Pressure &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Pacemaker/defibrillator &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vomiting &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Ulcers &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diarrhea &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Bloody Stools &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hepatitis &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Jaundice &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Constipation &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Genital Ulcers &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Discharge &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Kidney Stones &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Blood in Urine &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Seasonal Allergies &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Hay Fever &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Headache &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Migraines &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paralysis Fever &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Joint Ache &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Seizures &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Numbness &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Faints &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Stroke &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Multiple Sclerosis &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Alzheimer's Disease &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Parkinson's Disease &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Dementia &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Rashes &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Wounds &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Breast Lumps &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Eczema &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Dermatitis &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Depression &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Anxiety &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paranoia &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Sleep Patterns &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Mental and/or emotional factors &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Alzheimer's Disease &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Parkinson's disease &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Memory Loss &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Anemia &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Blood Transfusions &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Excessive Bleeding &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Purpura &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Infection &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Pain &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Joint Ache &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Stiffness &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Swelling &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Paralysis Fever &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Mood Swings &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Constipation &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Polydipsia &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Hypothyroidism &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hyperthyroidism &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vision loss &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Eye pain &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Double vision &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Headache &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Negative for constitutional &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Negative for ear, nose, mouth & throat &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Negative for respiratory &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Negative for cardiovascular &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Negative for gastrointenstinal &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Negative for genitourinary &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Negative for allergic/immunologic &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Negative for neurological &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Negative for integumentary &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Negative for psychiatry &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Negative for hemotologic/lymphatic &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Negative for musculoskeletal &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Negative for endocrine &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Negative for eyes &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "cbkMasterROS", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "cbkMasterROS", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Negative for constitutional & integumentary &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Negative for head/neck &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Negative for respiratory &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Negative for cardiovascular &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Negative for gastrointenstinal &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Negative for genitourinary &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Negative for allergic/immunologic & blood/lymphatic &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Negative for neurological psychiatry & musculoskeletal &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "elem_subCondition_u1","Filed_Label_Val"=> '7.1',"Filed_Label_Og_Val"=> "RA &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "elem_subCondition_u1","Filed_Label_Val"=> '7.2',"Filed_Label_Og_Val"=> "OA &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "rel_elem_subCondition_u1","Filed_Label_Val"=> '7.1',"Filed_Label_Og_Val"=> "RA &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "rel_elem_subCondition_u1","Filed_Label_Val"=> '7.2',"Filed_Label_Og_Val"=> "OA &#x2713;");


$arrMEDHX [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Yes");
$arrMEDHX [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "No");

$arrMEDHX [] = array("Filed_Label"=> "md_occular","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Ocular &#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "md_occular","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Ocular &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "sg_occular","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Ocular &#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "sg_occular","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Ocular &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATDrugName',"Filed_Label_Og_Val"=> "Drug");
$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATIngredient',"Filed_Label_Og_Val"=> "Ingredient");
$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATAllergenGroup',"Filed_Label_Og_Val"=> "Allergen");

$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Under Control : High Blood Pressure &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '2', "Filed_Label_Og_Val"=> "Under Control : Heart Problem &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '3', "Filed_Label_Og_Val"=> "Under Control : Arthrities &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '4', "Filed_Label_Og_Val"=> "Under Control : Lung Problems &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '5', "Filed_Label_Og_Val"=> "Under Control : Stroke &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '6', "Filed_Label_Og_Val"=> "Under Control : Thyroid Problems &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '7', "Filed_Label_Og_Val"=> "Under Control : Diabetes &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '8', "Filed_Label_Og_Val"=> "Under Control : LDL &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '9', "Filed_Label_Og_Val"=> "Under Control : Ulcers &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '10', "Filed_Label_Og_Val"=> "Under Control : Others &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '11', "Filed_Label_Og_Val"=> "Under Control : Cancer  &#x2713;");


$arrMEDHX [] = array("Filed_Label"=> "chk_annual_colorectal_cancer_screenings", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_annual_colorectal_cancer_screenings", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "chk_receiving_annual_mammogram", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_receiving_annual_mammogram", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "chk_received_flu_vaccine", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_received_flu_vaccine", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "chk_high_risk_for_cardiac", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "chk_high_risk_for_cardiac", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "immunization_child", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Child Imm. &#x2713;");
$arrMEDHX [] = array("Filed_Label"=> "immunization_child", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Child Imm. &#x2717;");

$arrMEDHX [] = array("Filed_Label"=> "offered_cessation_counseling", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Offered Cessation Counselling &#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "offered_cessation_counseling", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Offered Cessation Counselling &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "radio_family_smoke", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "No");
$arrMEDHX [] = array("Filed_Label"=> "radio_family_smoke", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Yes");

$arrMEDHX [] = array("Filed_Label"=> "cbkMasterPtCon", "Filed_Label_Val"=> 'no', "Filed_Label_Og_Val"=> "No");
$arrMEDHX [] = array("Filed_Label"=> "cbkMasterPtCon", "Filed_Label_Val"=> 'yes', "Filed_Label_Og_Val"=> "Yes");

$arrMEDHX [] = array("Filed_Label"=> "cbkMasterFamCon", "Filed_Label_Val"=> 'no', "Filed_Label_Og_Val"=> "No");
$arrMEDHX [] = array("Filed_Label"=> "cbkMasterFamCon", "Filed_Label_Val"=> 'yes', "Filed_Label_Og_Val"=> "Yes");

?>
<!DOCTYPE html>
<html>
<head>
	<!-- Bootstrap -->
  <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
  <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css" media="all">
</head>
<body >
<script type="text/javascript">
		self.focus();
		window.onload =function()
		{
			var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
			window.resizeTo(parWidth,670);
			var t = 10;
			var l = parseInt(((screen.availWidth - window.outerWidth) / 2) + 200 )
			window.moveTo(l,t);
		}
		function printWindow()
		{
			//window.print();
			var parWidth = document.body.clientWidth;
			var dispSetting="toolbar=no,location=no,directories=yes,menubar=no,"; 
				dispSetting+="scrollbars=yes,width="+parWidth+", height=500, left=20, top=100"; 
			var rewiewmedHxData = document.getElementById("rewiewmedHxData").innerHTML; 	
			var headers = ""; //document.getElementsByTagName('head')[0].innerHTML;
			var docprint=window.open("","",dispSetting); 
			docprint.document.open(); 
			docprint.document.write('<html><head>'+headers); 
			docprint.document.write('</head><body bgcolor="#FFFFFF" style="margin-top:0; margin-left:0;margin-right:0;" onLoad="self.print()"><center>');          
			docprint.document.write(rewiewmedHxData);          
			docprint.document.write('</center></body></html>');	
			docprint.document.close(); 
			docprint.focus();
		}
		
		window.onbeforeprint = function(){
			document.getElementById("print").style.display="none";
		}
		window.onafterprint = function(){
			document.getElementById("print").style.display="block";
		}       
	</script>	
<div class="panel panel-primary" id="rewiewmedHxData">
  <div class="panel-heading">
  	Medical Hx Reviewed Detail For Section <span><u><?php echo ucfirst($secName); ?></u></span>
    <span class="pull-right">
    	<?php
				$query = "select concat(lname,', ',fname) as name, mname from users where id = '".$opId."'";
				$sql = imw_query($query);
				if($sql)
				{
					if(imw_num_rows($sql))
					{
						$row = imw_fetch_array($sql);
						$proName = trim(ucwords($row['name'].' '.$row['mname']));
					}
					imw_free_result($sql);
				}
			?>
      Reviewed By <?php echo ucfirst($proName); ?> on <?php echo ucfirst($dateTime); ?>	
    </span>	
  </div>
  
  <div class="clearfix"></div>
  
  <div class="panel-body popup-panel-body">
<?php
	$masterId = "'".str_replace(",","','",$masterId)."'";	
	$masterId = trim($masterId);
	$arrUserId = array();
	
	$qryUser = "SELECT id, fname, lname FROM users ORDER BY id";
	$resUser = imw_query($qryUser);
	if($resUser)
	{
		while($arrUser = imw_fetch_array($resUser))
		{
			$arrUserId[$arrUser["id"]]["fname"] = $arrUser["fname"];
			$arrUserId[$arrUser["id"]]["lname"] = $arrUser["lname"];
		}
	}
	
	$op_masterId = $masterId;
	$qry = "SELECT patient_id,section_name FROM patient_last_examined WHERE patient_last_examined_id = ".$masterId."";
	$res = imw_query($qry);
	$row = imw_fetch_assoc($res);
	if($row['patient_id']!="" && $row['section_name']!="")
	{
		$qry = "SELECT GROUP_CONCAT(patient_last_examined_id) AS masterIds FROM patient_last_examined 
									WHERE patient_id = '".$row['patient_id']."'
												AND section_name = '".$row['section_name']."'
												GROUP BY section_name ";
		$res = imw_query($qry);
		$row1 = imw_fetch_assoc($res);
		$masterId = $row1['masterIds'];
	}
	
 	$qryGetReviewMedHX = "select ple.section_name as secName, 
																plec.field_text as fieldText, 
																plec.field_name as fieldName, 
																DATE_FORMAT(plec.date_time,'".get_sql_date_format()." %h:%i %p') modiDateTime,
																plec.old_value as oldValue, 
																plec.new_value as newValue,
																plec.action as perFormAction,
																plec.Depend_Select as dependSelect,
																plec.Depend_Table as dependTable,
																plec.section_table_primary_key,
																plec.field_name,
																plec.date_time,
																plec.id,
																plec.old_value,
																plec.new_value,
																plec.Depend_Search as dependSearch, 
																ple.operator_id,
																plec.section_table_name ,
																plec.field_text,
																plec.action,
																plec.master_pat_last_exam_id
																from patient_last_examined ple
																inner join patient_last_examined_child plec on plec.master_pat_last_exam_id = ple.patient_last_examined_id 						
																where plec.master_pat_last_exam_id in (".$masterId.")
																order by plec.date_time asc,plec.id desc";
	$rsGetReviewMedHX1 =  $rsGetReviewMedHX = imw_query($qryGetReviewMedHX);
	$reGetReviewMedHXCount = imw_num_rows($rsGetReviewMedHX) ; 
	if( $reGetReviewMedHXCount > 0)
	{
	?>

	<table class="table table-bordered table-hover table-striped scroll release-table">	
      <thead class="header">
        
          <?php if($secName == "Medications" || $secName == "Sx/Procedure" || $secName == "Allergies"){ ?>
          <?php 
								$str = '';
								switch($secName)
								{
									case "Medications":
										$ocuW = "50";
										$medW = "100";
										$dosW = "50";
										$siteW = "50";
										$sigW = "100";
										$compW = "80";
										$beginW = "80";
										$endW = "80";
										$commW = "100";
										$ordW = "150";
										$statusW = "50";
										$colspan = "11";
									   $str .= '
										 	<th width="'.$ocuW.'">&nbsp;</th> 
											<th width="'.$ocuW.'">Ocular</th> 
											<th width="'.$medW.'">Medication</th>
											<th width="'.$dosW.'">Dosage</th>
											<th width="'.$siteW.'">Site</th>
											<th width="'.$sigW.'">Sig</th>
											<th width="'.$compW.'">Comliant</th>
											<th width="'.$beginW.'">Begin Date</th>
											<th width="'.$endW.'">End Date</th>
											<th width="'.$commW.'">Comments</th>
											<th width="'.$statusW.'">Status</th>';
									break;
									case "Sx/Procedure":
										$ocuW = "50";
										$medW = "250";
										$siteW = "50";
										$beginW = "80";
										$commW = "150";
										$ordW = "150";
										$colspan = "7";
										$str .= '<th width="'.$ocuW.'">&nbsp;</th> 
										<td width="'.$ocuW.'">Ocular</th> 
										<th width="'.$medW.'">Sx Procedure</th>
										<th width="'.$siteW.'">Site</th>
										<th width="'.$beginW.'">Begin Date</th>
										<th width="'.$ordW.'">Physician</th>
										<th width="'.$commW.'">Comments</th>';
									break;
									case "Allergies":
										$ocuW = "50";
										$medW = "250";
										$beginW = "80";
										$commW = "300";
										$statusW = "50";
										$drugW = "80";
										$colspan = "6";
										$str .= '<td width="'.$ocuW.'">&nbsp;</th> 
										<th width="'.$drugW.'">Drug</th> 
										<th width="'.$medW.'">Name</th>
										<th width="'.$beginW.'">Begin Date</th>
										<th width="'.$commW.'">Comments</th>
										<th width="'.$statusW.'">Status</th>';
									break;
								}
						?>
            <tr class="grythead">
              <th class="col-xs-1 text-center" valign="top" rowspan="2">S.No.</th>
              <th class="col-xs-1 text-center" valign="top" rowspan="2">Operator</th>
              <th class="col-xs-2 text-center" valign="top" rowspan="2">Date Time</th>
              <th class="col-xs-8 text-center" valign="top" colspan="<?php echo $colspan; ?>">values</th>
            </tr>
            <tr class="grythead">
             	<?php 
								echo $str;
							?>
            </tr>	
         	<?php } else { ?> 
          	<tr class="grythead">  
              <th class="col-xs-1">S.No.</th>
              <th class="col-xs-1">Operator</th>
              <th class="col-xs-2">Date Time</th>
              <th class="col-xs-2">Field name</th>
              <th class="col-xs-2">Original Value</th>
              <th class="col-xs-2">Modified Value</th>
              <th class="col-xs-2">Section Name</th>
            </tr>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
      	<?php
					$sn=1;
					if($secName == "Medications" || $secName == "Sx/Procedure" || $secName == "Allergies")
					{
						
						$arrMaintainSecName = array();
						$arrMain = array();
						$count =  1;
						while($rowGetReviewMedHX = imw_fetch_assoc($rsGetReviewMedHX))
						{	
							$arrOldEntry = $arrNewEntry = array();
							$primary_id = $rowGetReviewMedHX['section_table_primary_key'];
							$match_key = '';
						
							$field_name = preg_replace('/[0-9]/','',$rowGetReviewMedHX['field_name']);
						
							$arrOldEntry['primary_id'] =  $primary_id;
							$arrOldEntry['date_time'] =  $rowGetReviewMedHX['date_time'];
							$arrOldEntry['section_table_name'] =  $rowGetReviewMedHX['section_table_name'];
							$arrOldEntry['master_pat_last_exam_id'] =  $rowGetReviewMedHX['master_pat_last_exam_id'];
							$operaterName = stripslashes(html_entity_decode(core_name_format($arrUserId[$rowGetReviewMedHX['operator_id']]['lname'],$arrUserId[$rowGetReviewMedHX['operator_id']]['fname'])));
							$modiDateTime = stripslashes(html_entity_decode($rowGetReviewMedHX['modiDateTime']));
							
							$arrNewEntry['primary_id'] =  $primary_id;
							$arrNewEntry['date_time'] =  $rowGetReviewMedHX['date_time'];
							$arrNewEntry['section_table_name'] =  $rowGetReviewMedHX['section_table_name'];
							$arrNewEntry['field_text'] =  $rowGetReviewMedHX['field_text'];
							$arrNewEntry['action'] =  $rowGetReviewMedHX['action'];
							$arrNewEntry['master_pat_last_exam_id'] =  $rowGetReviewMedHX['master_pat_last_exam_id'];
							$match_key = get_last_matching_record($primary_id, $rowGetReviewMedHX['date_time'], $arrMain);
							
							if($match_key === "")
							{
								$arrOldEntry[$field_name] = $cls_review->isDate($rowGetReviewMedHX['old_value']);
								$arrNewEntry[$field_name] = $cls_review->isDate($rowGetReviewMedHX['new_value']);
								$match_arr = get_last_matching_record($primary_id, '', $arrMain,'array');
								if(is_array($match_arr) && count($match_arr)>0){
									foreach($match_arr as $key=>$val){
										if(!isset($arrOldEntry[$key])){
											$arrOldEntry[$key] = $cls_review->isDate($val);
										}
										if(!isset($arrNewEntry[$key])){
											$arrNewEntry[$key] = $cls_review->isDate($val);
										}
									}
								}
								$arrEntry['old'] = $arrOldEntry;
								$arrEntry['new'] = $arrNewEntry;
								$arrEntry['operaterName'] = $operaterName;
								$arrEntry['modiDateTime'] = $modiDateTime;
								
								$arrMain[] = $arrEntry;
							}
							else
							{
								$arrMain[$match_key]['old'][$field_name] = $cls_review->isDate($rowGetReviewMedHX['old_value']);
								$arrMain[$match_key]['new'][$field_name] = $cls_review->isDate($rowGetReviewMedHX['new_value']);
								$arrEntry['operaterName'] = $operaterName;
								$arrEntry['modiDateTime'] = $modiDateTime;
								$arrEntry['section_table_name'] = $rowGetReviewMedHX['section_table_name'];
								$arrEntry['field_text'] =  $rowGetReviewMedHX['field_text'];
							}
						}
						
						$str = '';
						$arrMain = array_reverse($arrMain);
						$counter = 0;
						foreach($arrMain as $key=>$arr)
						{
							if(trim($op_masterId,"'") != $arr['new']['master_pat_last_exam_id']){
								continue;
							}
							
							// *** getting old med value ***
							$nobgClr = "nobgClr";
							$old_med_data = '';
							switch($secName)
							{
								case "Medications":
									if($arr['old']['section_table_name'] == "lists" && $arr['old']['md_medication']!='')
									{
										$med_type =($arr['old']['med_type'] == 4)? 1:'';
										$old_med_data .= '<tr>';
										$old_med_data .= '<td width="'.$ocuW.'">Old</td>';
										$old_med_data .= '<td width="'.$ocuW.'">'.(isset($arr['old']['med_type'])?$med_type:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$medW.'">'.(isset($arr['old']['md_medication'])?$arr['old']['md_medication']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$dosW.'">'.(isset($arr['old']['md_dosage'])?$arr['old']['md_dosage']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$siteW.'">'.(isset($arr['old']['md_occular'])?$arr['old']['md_occular']:'&nbsp;').'</td >';
										$old_med_data .= '<td width="'.$sigW.'">'.(isset($arr['old']['md_sig'])?$arr['old']['md_sig']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$compW.'">'.(isset($arr['old']['compliant'])?$arr['old']['compliant']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$beginW.'">'.(isset($arr['old']['md_begindate'])?get_date_format($arr['old']['md_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$endW.'">'.(isset($arr['old']['md_enddate'])?get_date_format($arr['old']['md_enddate'],'mm-dd-yyyy'):'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$commW.'">'.(isset($arr['old']['md_comments'])?$arr['old']['md_comments']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$statusW.'">'.(isset($arr['old']['cbMedicationStatus'])?$arr['old']['cbMedicationStatus']:'&nbsp;').'</td>';
										$old_med_data .= '</tr>';
									}
								break;
								case "Sx/Procedure":
									if($arr['old']['section_table_name'] == "lists" && $arr['old']['sx_title_text']!='')
									{
										$sg_occular =($arr['old']['sg_occular'] == 6)? 1:'';
										$old_med_data .= '<tr>';
										$old_med_data .= '<td width="'.$ocuW.'">Old</td>';
										$old_med_data .= '<td width="'.$ocuW.'">'.(isset($arr['old']['sg_occular'])?$sg_occular:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$medW.'">'.(isset($arr['old']['sx_title_text'])?$arr['old']['sx_title_text']:'&nbsp;').'</td>';
										switch($arr['old']['sx_site'])
										{
											case "3":
												$sx_site = "OU";
											break;
											case "2":
												$sx_site = "OD";
											break;
											case "1":
												$sx_site = "OS";
											break;
											default:
												$sx_site = '';
										}
										$old_med_data .= '<td width="'.$siteW.'">'.(isset($sx_site)?$sx_site:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$beginW.'">'.(isset($arr['old']['sg_begindate'])?get_date_format($arr['old']['sg_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td >';
										$old_med_data .= '<td width="'.$ordW.'">'.(isset($arr['old']['sg_referredby'])?$arr['old']['sg_referredby']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$commW.'">'.(isset($arr['old']['sg_comments'])?$arr['old']['sg_comments']:'&nbsp;').'</td>';
										$old_med_data .= '</tr>';
									}
								break;
								case "Allergies":
									if($arr['old']['section_table_name'] == "lists" && $arr['old']['ag_title']!='')
									{ 
										switch($arr['new']['ag_occular_drug'])
										{
											case "fdbATIngredient":
												$ag_occular_drug = 'Ingredient';
											break;
											case "fdbATAllergenGroup":
												$ag_occular_drug = 'Allergen';
											break;
											case "fdbATDrugName":
											default:
												$ag_occular_drug = 'Drug';
											break;
										}
										$old_med_data .= '<tr>';
										$old_med_data .= '<td width="'.$ocuW.'">Old</td>';
										$old_med_data .= '<td width="'.$drugW.'">'.(isset($arr['old']['ag_occular_drug'])?$ag_occular_drug:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$medW.'">'.(isset($arr['old']['ag_title'])?$arr['old']['ag_title']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$beginW.'">'.(isset($arr['old']['ag_begindate'])?get_date_format($arr['old']['ag_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td >';
										$old_med_data .= '<td width="'.$commW.'">'.(isset($arr['old']['ag_comments'])?$arr['old']['ag_comments']:'&nbsp;').'</td>';
										$old_med_data .= '<td width="'.$statusW.'">'.(isset($arr['old']['ag_status'])?$arr['old']['ag_status']:'&nbsp;').'</td>';
										$old_med_data .= '</tr>';	
									}
								break;
							}
							// *** End getting old med value ***
							
							$counter++;
							$rowspan = ($old_med_data ? 'rowspan="2"' : '');
							$str .= '<tr>';
							$str .= '<td '.$rowspan.'>'.($counter).'</td>';
							$str .= '<td '.$rowspan.'>'.$arr['operaterName'].'</td>';
							$str .= '<td '.$rowspan.'>'.$arr['modiDateTime'].'</td>';
							
							switch($secName)
							{
								case "Medications":
									if($arr['new']['section_table_name'] == "lists" && $arr['new']['action'] !="delete")
									{
										$med_type =($arr['new']['med_type'] == 4)? 1:'';
										$str .= '<td width="'.$ocuW.'">New</td>';
										
										$str .= '<td width="'.$ocuW.'">'.(isset($arr['new']['med_type'])?$med_type:'&nbsp;').'</td>';
										$str .= '<td width="'.$medW.'">'.(isset($arr['new']['md_medication'])?$arr['new']['md_medication']:'&nbsp;').'</td>';
										$str .= '<td width="'.$dosW.'">'.(isset($arr['new']['md_dosage'])?$arr['new']['md_dosage']:'&nbsp;').'</td>';
										$str .= '<td width="'.$siteW.'">'.(isset($arr['new']['md_occular'])?$arr['new']['md_occular']:'&nbsp;').'</td >';
										$str .= '<td width="'.$sigW.'">'.(isset($arr['new']['md_sig'])?$arr['new']['md_sig']:'&nbsp;').'</td>';
										$str .= '<td width="'.$compW.'">'.(isset($arr['new']['compliant'])?$arr['new']['compliant']:'&nbsp;').'</td>';
										$str .= '<td width="'.$beginW.'">'.(isset($arr['new']['md_begindate'])?get_date_format($arr['new']['md_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td>';
										$str .= '<td width="'.$endW.'">'.(isset($arr['new']['md_enddate'])?get_date_format($arr['new']['md_enddate'],'mm-dd-yyyy'):'&nbsp;').'</td>';
										$str .= '<td width="'.$commW.'">'.(isset($arr['new']['md_comments'])?$arr['new']['md_comments']:'&nbsp;').'</td>';
										$str .= '<td width="'.$statusW.'">'.(isset($arr['new']['cbMedicationStatus'])?$arr['new']['cbMedicationStatus']:'&nbsp;').'</td>';
										
									}
									else if($arr['new']['action'] != "delete")
									{
										$str .= '<td colspan="11">'.$arr['new']['field_text'].'</td>';
									}
									else
									{
										$str .= '<td width="'.$ocuW.'">New</td>';
										$str .= '<td colspan="10" class="text-danger">Deleted</td>';
									}
									break;
									case "Sx/Procedure":
										if($arr['new']['section_table_name'] == "lists" && $arr['new']['action'] !="delete")
										{
											$sg_occular =($arr['new']['sg_occular'] == 6)? 1:'';
											
											$str .= '<td width="'.$ocuW.'">New</td>';
											$str .= '<td width="'.$ocuW.'">'.(isset($arr['new']['sg_occular'])?$sg_occular:'&nbsp;').'</td>';
											$str .= '<td width="'.$medW.'">'.(isset($arr['new']['sx_title_text'])?$arr['new']['sx_title_text']:'&nbsp;').'</td>';
											switch($arr['new']['sx_site'])
											{
												case "3":
													$sx_site = "OU";
												break;
												case "2":
													$sx_site = "OD";
												break;
												case "1":
													$sx_site = "OS";
												break;
											}
											$str .= '<td width="'.$siteW.'">'.(isset($sx_site)?$sx_site:'&nbsp;').'</td>';
											$str .= '<td width="'.$beginW.'">'.(isset($arr['new']['sg_begindate'])?get_date_format($arr['new']['sg_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td >';
											$str .= '<td width="'.$ordW.'">'.(isset($arr['new']['sg_referredby'])?$arr['new']['sg_referredby']:'&nbsp;').'</td>';
											$str .= '<td width="'.$commW.'">'.(isset($arr['new']['sg_comments'])?$arr['new']['sg_comments']:'&nbsp;').'</td>';
										}
										else if($arr['new']['action'] != "delete")
										{
											$str .= '<td colspan="11">'.$arr['new']['field_text'].'</td>';
										}
										else
										{
											$str .= '<td width="'.$ocuW.'">New </td>';
											$str .= '<td colspan="5" class="text-danger">Deleted</td>';
										}
									break;
									case "Allergies":
										
										if($arr['new']['section_table_name'] == "lists" && $arr['new']['action'] !="delete")
										{
											$str .= '<td width="'.$ocuW.'">New</td>';
											switch($arr['new']['ag_occular_drug'])
											{
												case "fdbATIngredient":
													$ag_occular_drug = 'Ingredient';
												break;
												case "fdbATAllergenGroup":
													$ag_occular_drug = 'Allergen';
												break;
												case "fdbATDrugName":
												default:
													$ag_occular_drug = 'Drug';
												break;
											}
											$str .= '<td width="'.$drugW.'">'.(isset($arr['new']['ag_occular_drug'])?$ag_occular_drug:'&nbsp;').'</td>';
											$str .= '<td width="'.$medW.'">'.(isset($arr['new']['ag_title'])?$arr['new']['ag_title']:'&nbsp;').'</td>';
											$str .= '<td width="'.$beginW.'">'.(isset($arr['new']['ag_begindate'])?get_date_format($arr['new']['ag_begindate'],'mm-dd-yyyy'):'&nbsp;').'</td >';
											$str .= '<td width="'.$commW.'">'.(isset($arr['new']['ag_comments'])?$arr['new']['ag_comments']:'&nbsp;').'</td>';
											$str .= '<td width="'.$statusW.'">'.(isset($arr['new']['ag_status'])?$arr['new']['ag_status']:'&nbsp;').'</td>';
										}
										else if($arr['new']['action'] != "delete")
										{
											$str .= '<td colspan="11">'.$arr['new']['field_text'].'</td>';
										}
										else
										{
											$str .= '<td width="'.$ocuW.'">New</td>';
											$str .= '<td colspan="10" class="text-danger">Deleted</td>';
										}
									break;
							}
							
							$str .= '</tr>';
							
							$str .= $old_med_data;
							
							
						}
						echo $str;
						
					}
					else
					{
						while($rowGetReviewMedHX = imw_fetch_array($rsGetReviewMedHX1))
						{
							$sectionName = stripslashes(html_entity_decode($rowGetReviewMedHX['secName']));
							$fieldText = stripslashes(html_entity_decode($rowGetReviewMedHX['fieldText']));
							$operaterName = stripslashes(html_entity_decode(core_name_format($arrUserId[$rowGetReviewMedHX['operator_id']]['lname'],$arrUserId[$rowGetReviewMedHX['operator_id']]['fname'])));
							$modiDateTime = stripslashes(html_entity_decode($rowGetReviewMedHX['modiDateTime']));
						
							if(preg_match ("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $rowGetReviewMedHX['oldValue'])){
								$Old_Value = get_date_format($rowGetReviewMedHX['oldValue']);
							}else if(preg_match ("^[0-9]{2}-[0-9]{2}-[0-9]{4}$", $rowGetReviewMedHX['oldValue'])){
								$Old_Value = get_date_format($rowGetReviewMedHX['oldValue'],'mm-dd-yyyy');
							}else
								$Old_Value = stripslashes(html_entity_decode($rowGetReviewMedHX['oldValue']));
							
							if(preg_match ("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $rowGetReviewMedHX['newValue'])){
								$New_Value = get_date_format($rowGetReviewMedHX['newValue']);
							}else if(preg_match ("^[0-9]{2}-[0-9]{2}-[0-9]{4}$", $rowGetReviewMedHX['newValue'])){
								$New_Value = get_date_format($rowGetReviewMedHX['newValue'],'mm-dd-yyyy');
							}else{
								$New_Value = stripslashes(html_entity_decode($rowGetReviewMedHX['newValue']));
							}
							$fieldName = stripslashes(html_entity_decode($rowGetReviewMedHX['fieldName']));
							$perFormAction = $rowGetReviewMedHX['perFormAction'];
							$Depend_Select = $rowGetReviewMedHX['dependSelect'];
							$Depend_Table = $rowGetReviewMedHX['dependTable'];
							$Depend_Search = $rowGetReviewMedHX['dependSearch'];
						
							if($Depend_Select && $Depend_Table && $Depend_Search)
							{
								$getOrignalValOLD = $Depend_Select." from ".$Depend_Table." where ".$Depend_Search." in (".$Old_Value.")";
								$rsGetOrignalValOLD = imw_query($getOrignalValOLD);
								if($rsGetOrignalValOLD)
								{
									if(imw_num_rows($rsGetOrignalValOLD))
									{
										$arrOldVal = array();
										while($row = imw_fetch_array($rsGetOrignalValOLD))
										{
											$arrOldVal[] = $row[0];
										}
										$Old_Value = implode("<br>",$arrOldVal);
									}
								}
								$getOrignalValNEW = $Depend_Select." from ".$Depend_Table." where ".$Depend_Search." in (".$New_Value.")";
								$rsGetOrignalValNEW = imw_query($getOrignalValNEW);
								if($rsGetOrignalValNEW)
								{
									if(imw_num_rows($rsGetOrignalValNEW))
									{
										$arrNewVal = array();	
										while($row = imw_fetch_array($rsGetOrignalValNEW))
										{
											$arrNewVal[] = $row[0];
										}
										$New_Value = implode("<br>",$arrNewVal);
									}
								}
								
							}
						
							$arrFLD = explode("_",$fieldName);
							$orignalFieldLabel = $fieldName;
						
							switch ($arrFLD[0])
							{
								case "md":								
									$noNumeric = preg_replace('#\d+#', '', $arrFLD[1]);
									$Field_Label = $arrFLD[0].'_'.$noNumeric;
								break;	
								case "sg":								
									$noNumeric = preg_replace('#\d+#', '', $arrFLD[1]);
									$Field_Label = $arrFLD[0].'_'.$noNumeric;
								break;	
								case "ag":								
									$noNumeric = preg_replace('#\d+#', '', $arrFLD[2]);							
									$Field_Label = $arrFLD[0].'_'.$arrFLD[1].'_'.$noNumeric;
								break;	
								case "imchk":	//Medical History -> Immunizations
									$noNumeric = preg_replace('#\d+#', '', $arrFLD[2]);
									$Field_Label = $arrFLD[0].'_'.$arrFLD[1].'_'.$noNumeric;
								break;
								case "immunization":	//Medical History -> Immunizations						
									$noNumeric = preg_replace('#\d+#', '', $arrFLD[1]);
									$Field_Label = $arrFLD[0].'_'.$noNumeric;
								break;
								default:			
									$Field_Label = $orignalFieldLabel;									
							}				
							
							switch ($Field_Label)
							{
								case "u_wear":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
								break;
								case "eye_problem":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
								break;
								case "eye_problem_other_check":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
								break;
								case "any_conditions_u":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];																		
								break;								
								case "any_conditions_other_u":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];																		
								break;
								case "rel_any_conditions_other_r":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];																		
								break;
								
								case "rel_any_conditions_relative":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];																		
								break;									
								case "elem_chronicDesc":												
									$orignalValue = $cls_review->getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];															
								break;							
								case "rel_elem_chronicDesc":												
									$orignalValue = $cls_review->getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];															
								break;							
								case "elem_chronicRelative":																								
									$orignalValue = $cls_review->getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];										
								break;
								case "this_blood_sugar_fasting":																								
									$orignalValue = $cls_review->getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];										
								break;						
								
								case "any_conditions_u1":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "any_conditions_u1_n":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "any_conditions_relative1":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "any_conditions_relative1_n":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								
								case "elem_subCondition_u1":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "rel_elem_subCondition_u1":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								
								case "cbkMasterPtCon":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "cbkMasterFamCon":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								
								case "chk_under_control":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "chk_annual_colorectal_cancer_screenings":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "chk_receiving_annual_mammogram":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "chk_received_flu_vaccine":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "chk_high_risk_for_cardiac":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "any_conditions_others_both":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "any_conditions_others_rel":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "review_eye":
								case "review_endocrine":
								case "review_musculoskeletal":
								case "review_blood_lymph":
								case "review_psychiatry":
								case "review_intgmntr":	
								case "review_const":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "review_head":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "review_resp":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								
								case "review_card":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "review_gastro":																														
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];
								break;
								case "review_genit":	
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];										
								break;
								
								case "cbkMasterROS":	
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];										
								break;
								
								case "negChkBx":	
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];										
								break;
								case "smoke":	
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];									
								break;
								case "review_aller":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
								break;									
								case "review_neuro":										
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
								break;	
								case "md_occular":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
								case "sg_occular":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
								case "ag_occular_drug":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
								case "immunization_child":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
								case "offered_cessation_counseling":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
								case "radio_family_smoke":																				
									$orignalValue = $cls_review->getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
									$orignalValue = explode("~~~~",$orignalValue);										
									$Old_Value = $orignalValue[0];
									$New_Value = $orignalValue[1];	
									$Field_Label = $orignalFieldLabel;
								break;
															
							}
							
							if(!in_array(ucwords($sectionName),$arrMaintainSecName))
							{	
								$arrMaintainSecName[] = ucwords($sectionName);
							}
						
							$operaterName =(trim($operaterName)!="" && trim($operaterName)!=",") ? ucwords($operaterName): "N/A" ;
						?>
						<tr>
            	<td><?php echo $sn; ?></td>
              <td><?php echo $operaterName;?></td>
              <td><?php echo $modiDateTime; ?></td>
              <td><?php echo ucwords($fieldText); ?></td>
              <td><?php echo $cls_review->isDate($Old_Value); ?></td>
              <?php 
								$strNewValue = "";
								if(trim($perFormAction) != "delete" && trim($New_Value) != ""){
									$strNewValue = $cls_review->isDate($New_Value);
								}
								elseif(trim($perFormAction) == "delete"){
									$strNewValue =  '<span class="text-danger">Deleted</span>';								
								}
								elseif((trim($perFormAction) == "Active") || (trim($perFormAction) == "InActive")){								
									$strNewValue = trim($perFormAction);
								}
							?>
              <td><?php echo $strNewValue; ?></td>
              <td class="text-center"><?php echo ucwords($sectionName); ?></td>
          	</tr>
            <?php 	
								$sn++;		
						}
					
						if(trim($secName) == "complete")
						{
							$arrMedHxSecName = array("Ocular Hx","General Health","Medications","Allergies","Sx/Procedure",
																				"Immunizations","Lab","Advance Directive");
							foreach($arrMedHxSecName as $val)
							{
								if(!in_array($val,$arrMaintainSecName))
								{							
				?>
        					<tr >
                  	<td class="text-center"><?php echo $sn; ?></td>
                    <td class="text-center"><?php echo ucwords($val); ?></td>
                    <td colspan="6">No change was observed </td>
                	</tr>
       	<?php 		
									$sn++;
								}
							}
						}
					}	
				?>
     	</tbody>
	</table>
  <?php
						
	}
	else
	{
		$arrMedHxSecName = array("Ocular Hx","General Health","Medications","Allergies","Sx/procedures",
															"Immunizations","Lab","Advance Directive");
		$sn = 1;
		foreach($arrMedHxSecName as $val)
		{
			$tr .= '<tr>
									<td class="text-center">'.$sn.'</td>
									<td class="text-center">'.ucwords($val).'</td>
									<td colspan="5">No change was observed </td>
								</tr>';
			$sn++;
		}
						
	?>	
			<table class="table table-bordered table-hover table-striped scroll release-table ">	
				<thead class="header">
					<tr class="grythead">
						<th class="col-xs-1">S.No.</th>
						<th class="col-xs-2">Section Name</th>
						<th class="col-xs-2">Field name</th>
						<th class="col-xs-2">Original Value</th>
						<th class="col-xs-2">Modified Value</th>
						<th class="col-xs-1">Operator</th>
						<th class="col-xs-2">Date Time</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $tr; ?>
				</tbody>
			</table>';
	<?php				
		
		
	}
	
	
	function get_last_matching_record($primary_id, $date_time, $arrMain, $return_type=''){
							$return_key = '';
							if($return_type == "array"){
								$arrMain = array_reverse($arrMain);
							}
							foreach($arrMain as $key=>$arr){
								if($arr['old']['primary_id'] == $primary_id){
									if(strtotime($arr['old']['date_time']) == strtotime($date_time)){
										$return_key = $key;
										return $return_key;
									}
									if($return_type == 'array')
									return $arr['new'];								
								}
							}
							return $return_key;
						}
	?>
	</div>
  <footer class="panel-footer">
  	<?php if($reGetReviewMedHXCount > 0 ) {?>
    	<input class="btn btn-primary" id="print" type="button" name="print" value="Print" onClick="printWindow();">
    <?php } ?>
    <button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
  </footer>
</div>  
</body>
</html>