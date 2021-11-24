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
$ohpi = new HPI();	

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
switch($task){
	case "hpi":
		$ohpi->get_hpi();
	break;
	case "subcats":
		$ohpi->get_subcats_opts();
	break;
	case "save":
		$ohpi->save();
	break;
	case "get_edit_val":
		$ohpi->get_record_inf();
	break;
}
?>
