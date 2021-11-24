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
Purpose: Save, del & updates specilty records from database.
Access Type: Include
*/
require_once("../../../config/globals.php");
require_once($GLOBALS['srcdir']."/classes/admin/GroupPrevileges.php");
require_once($GLOBALS['srcdir']."/classes/work_view/User.php");
require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
$oGroupPrevileges = new GroupPrevileges();
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'gr_name';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "groups_prevlgs";
$pkId	= "id";
$chkFieldAlreadyExist = "name";
switch($task){
	case 'delete':
		$oGroupPrevileges->delete_gp($_POST['pkId']);
		break;
	case 'save_update':
		$oGroupPrevileges->save();
		break;
	case 'show_list':
		$oGroupPrevileges->show_list($so, $soAD);		
		break;
	case 'save_previleges':
		$oGroupPrevileges->save_previleges();
		break;
	case 'get_prvlgs':
		$oGroupPrevileges->get_previleges($_REQUEST['id']);
		break;
	case 'get_users_for_del':
		$oGroupPrevileges->get_users_of_grp($_POST['pkId']);
		break;
	case 'show_log':
		$oGroupPrevileges->show_log();
		break;
	default: 
}

?>