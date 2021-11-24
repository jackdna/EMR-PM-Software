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
?>
<?php
/*
File: chart_glucoma_print.php
Purpose: This file is used for Glucoma Flow Sheet Printing.
Access Type : Direct
*/

include_once("../../config/globals.php");

include_once($GLOBALS['fileroot'].'/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir']."/classes/complete_pt_record.class.php");
include_once($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/print_functions_class.php");

$pid = $_SESSION['patient'];
$call_from = 'wv';
if(isset($_REQUEST['call_from']) && trim($_REQUEST['call_from']) != ''){
	$call_from = $_REQUEST['call_from'];
}
$cpr = New CmnFunc($pid); 
$library_path = $GLOBALS['webroot'].'/library';
$insideGlucoma = true;
include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/chart_glucoma_print_inc.php");
?>