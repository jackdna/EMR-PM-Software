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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/landing_page.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
#require_once($GLOBALS['fileroot'].'/library/classes/class.cls_notifications.php');

$landPhyObj = new landing_physician();
$landTechObj = new landing_technician();

$task = $_POST['task'];
$default = '<div class="alert alert-info">Data does not exists</div>';

switch( $task ){
	case 'load_messages':
		$landPhyObj->top_five_messages('messages');
	break;
	case 'load_direct':
		$landPhyObj->top_five_messages('direct');
	break;
	case 'patientSchedule':
		$landPhyObj->today_appts();
	break;	
	case 'appointmentSummary':
		echo $arrApptSummary=$landPhyObj->appt_summary();
	break;	
	case 'unfinalizedCharts':
		$landPhyObj->unfinalized_chart();
	break;
	case 'uninterpretedTests':
		$landPhyObj->un_int_test();
	break;
	case 'ciPatientList':
		$landTechObj->checked_patient();
	break;
	case 'ready4Doc':
		$landTechObj->ready4doctor();
	break;
	case 'todoList':
		$landTechObj->to_do_list();
	break;
	default:
		echo $default;
}

?>