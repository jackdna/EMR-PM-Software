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

require_once("../../../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
$ohpi = new APPolicies();
$oAdmn = new Admn();

$task	= isset($_REQUEST['aptask']) ? trim($_REQUEST['aptask']) : '';
switch($task){
	case "show_list":
		$ohpi->get_appolicies();
	break;
	
	case "save_update":
		$ohpi->save();
	break;
	
	case "get_edit_val":
		$ohpi->get_record_inf();
	break;
	
	case "get_fu":
		$ohpi->get_fu_htm();
	break;
	
	case "delete":
		$ohpi->op_delete();
	break;
	
	case "getSettings":
	case "saveSettings":
		$o = ($task=="saveSettings") ? "save" : "";
		$oAdmn->apPolicySettings($o);
	break;	
	
}
?>
