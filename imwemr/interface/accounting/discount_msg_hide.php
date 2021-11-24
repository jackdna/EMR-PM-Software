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

include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
session_start();
$pat_id= $_REQUEST['pat_id'];
$enc_id=$_REQUEST['enc_id'];
if($enc_id>0){
	$_SESSION['cs_dis_enc'][$enc_id]="yes";
}else{
	$row=imw_query("select * from patient_charge_list where del_status='0' and totalBalance>0 and patient_id='$pat_id'");
	while($sel=imw_fetch_array($row)){
		$_SESSION['cs_dis_enc'][$sel['encounter_id']]="yes";
	}
}
$_SESSION['cs_dis_pat'][$pat_id]="yes";
?>