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

//--- PATIENT SPECIFIC DUR WINDOW ----

include_once("../../../config/globals.php");

//--- GET ERX URL ------
$query = "select EmdeonUrl from copay_policies";
$sql = imw_query($query);
$row = imw_fetch_assoc($sql);
$EmdeonUrl = $row['EmdeonUrl'];

//--- GET PROVIDER LOGIN USERNAME AND PASSWORD ----
$userId = $_SESSION['authId'];
$query = "select eRx_user_name, erx_password from users where id = '".$userId."'";
$sql = imw_query($query);
$userRes = imw_fetch_assoc($sql);
$eRx_user_name = $userRes['eRx_user_name'];
$erx_password = $userRes['erx_password'];
$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);

if(trim($EmdeonUrl) != '' and trim($eRx_user_name) != '' and trim($erx_password) != '')
{
	//--- GET PATIENT DETAILS ---
	$patientId = $_SESSION['patient'];
	$query = "select * from patient_data where id = '". $patientId ."'";
	$sql = imw_query($query);
	$row = imw_fetch_assoc($sql);
	$id = $row['id'];
	$fname = $row['fname'];
	$lname = $row['lname'];
	list($year,$mon,$day) = preg_split('/-/',$row['DOB']);
	$patient_dob = $mon.'/'.$day.'/'.$year;
	$erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/person/PersonRxHistory.jsp&actionCommand=apiRxHistory&P_ACT=$id&P_LNM=$lname&P_FNM=$fname&P_DOB=$patient_dob";
	header("location: $erx_url");
}
?>