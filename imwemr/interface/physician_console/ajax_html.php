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
set_time_limit(600);

$callFrom = (isset($_REQUEST['from']) && trim($_REQUEST['from'])!='') ? trim($_REQUEST['from']) : 'core';
if($callFrom == 'core'){$ignoreAuth = true;}
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.cls_notifications.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');

$task = (isset($_REQUEST['task']) && trim($_REQUEST['task'])!='') ? trim($_REQUEST['task']) : 'expand_notifications';
$task = trim($task);
$msgConsoleObj = new msgConsole();
$msgConsoleObj->callFrom = $callFrom;
$cls_notifications = new core_notifications();

if($callFrom=='core'){
	include_once("ajax_html_core.php");
}else if($callFrom=='console'){
	include_once("ajax_html_console.php");
}
?>