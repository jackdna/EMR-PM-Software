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

require_once("../../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
$oExamXml = new ExamXml();	

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
switch($task){
	case 'load_exam':
		//print_r($_REQUEST);		
		$oExamXml->get_exam_info();		
	break;
	
	case 'save':
		$oExamXml->save_exam_extension();
		
		
	break;
	
	case 'get_findings':
		$oExamXml->get_exam_ext_findings();
	break;
	
	case 'edit':
		$oExamXml->edit_exam_extension();
	break;
	
	case 'delete':
		
	break;
}


?>
