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
/*
File: get_pt_alert.php
Purpose: Get pt alerts
Access Type: Indirect
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
$patient_id = (isset($_REQUEST['pt_id']) && empty($_REQUEST['pt_id']) == false) ? $_REQUEST['pt_id'] : $_SESSION['patient'];
$query_ptalert = "SELECT alertId FROM alert_tbl WHERE patient_id='".$patient_id."' AND alert_showed NOT LIKE '%1%' AND is_deleted = '0' and status='1'";
$result_ptalert = imw_query($query_ptalert);
if(imw_num_rows($result_ptalert) > 0){
	echo imw_num_rows($result_ptalert);
}
?>