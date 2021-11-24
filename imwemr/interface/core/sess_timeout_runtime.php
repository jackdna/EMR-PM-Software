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

$do = isset($_REQUEST['do']) ? intval($_REQUEST['do']) : 0;
if($do==1){
	$ignoreAuth=true;
}
require_once(dirname(__FILE__).'/../../config/globals.php');
if($do==2) die();

$sess_timeout = isset($_REQUEST['timeout_in']) ? intval($_REQUEST['timeout_in']) : 0;

$margin = $sess_timeout > 60 ? 61 : 31;
$sess_timeout_warning_time = $sess_timeout - $margin;
$recall_in = isset($_REQUEST['recall_in']) ? intval($_REQUEST['recall_in']) : 30000;

$elapsed = (time() - $_SESSION["last_update"]);
$new_timeout = $sess_timeout - $elapsed;
$warning_time = $recall_in/1000;
$new_interval = ($new_timeout - $margin) * 1000;
$show_seconds = $margin-1;
$debugg =  'do='.$do.', elapsed='.$elapsed.', time='.time().', Last update='.$_SESSION["last_update"];
//echo 'new_timeout="'; echo $new_timeout; echo '";';
//echo 'warning_time='; echo $warning_time; echo ';';
//echo 'new_interval='; echo $new_interval; echo ';';
//echo 'alert("Elapsed = '.$elapsed.'\nNew TimeOut = '.$new_timeout.'\nWarning Time = '.$warning_time.'\nNew Interval = '.$new_interval.'\nRecall In = '.$recall_in.'");';

if($elapsed >= $warning_time){
	/*
	echo '<script type="text/javascript">';
//	echo 'alert("in if");';
	echo 'clearInterval(auto_sess_timer);';
	echo 'dialogBox("Session TimeOut Warning","<span style=\'font-size:14px;\'><b>You will be logged out in <span id=\'logout_seconds\' style=\'color:#f00\'>'.$show_seconds.'</span> Seconds.</b></span>","Keep Session Active","Logout Now","do_sess_alive();","window.location=\'login.index.php?pg=app-logout\'",false,true,false,false,500,"",true,"","",false,dgi(\'dialog_span\'));';
	echo 'log_sec_timer = setInterval(fun_sec_timer,1000);';
	echo '</script>';
	*/
	$return = array();
	$return['respType'] 	= 'if';
	$return['showSeconds']	= $show_seconds;
	$return['debug'] = $debugg;
	echo json_encode($return);
}
else{
	if($new_interval < 0) $new_interval = 0;
	/*
	echo '<script type="text/javascript">';
	echo 'alert("in else. New Interval = '.$new_interval.'");';
	echo 'var ajax_sess_timer = setInterval(ajax_warn_sess_timeout, '.$new_interval.');';
	echo 'function ajax_warn_sess_timeout(){clearInterval(ajax_sess_timer);
			//fajax("../chart_notes/sess_timeout_runtime.php",Array("do=1","timeout_in="+'.$sess_timeout.',"recall_in="+'.$recall_in.'),"auto_sess_timeout_span");
			$.ajax({
				url:top.JS_WEB_ROOT_PATH+\'/interface/core/sess_timeout_runtime.php?do==1&timeout_in='.$sess_timeout.'&recall_in='.$recall_in.'\',
				dataType: "script",
				complete:function(resp){
					eval(resp);
					//$(\'#auto_sess_timeout_span\').html(resp);
				}
			});
		}';
		echo '</script>';
	*/
	$return = array();
	$return['respType'] 	= 'else';
	$return['timeoutInVal']	= $sess_timeout;
	$return['recallIn']		= $recall_in;
	$return['recallFun']	= $recall_in-($elapsed*1000);
	$return['debug'] = $debugg;
	echo json_encode($return);
}
?>