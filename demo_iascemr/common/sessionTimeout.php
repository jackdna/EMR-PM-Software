<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
session_start(); 
$do = isset($_REQUEST['do']) ? intval($_REQUEST['do']) : 0;
if($do==1){
	$ignoreAuth=true;
}
else if($do==2){
	$ignoreAuth=false;
}

if($do==2) {
	$_SESSION["session_last_update"] = time();
	die();
}
$sess_timeout = isset($_REQUEST['timeout_in']) ? intval($_REQUEST['timeout_in']) : 0;

$margin = $sess_timeout > 60 ? 61 : 31;
$sess_timeout_warning_time = $sess_timeout - $margin;
$recall_in = isset($_REQUEST['recall_in']) ? intval($_REQUEST['recall_in']) : 30000;

$elapsed = (time() - $_SESSION["session_last_update"]);
$new_timeout = $sess_timeout - $elapsed;
$warning_time = $recall_in/1000;
$new_interval = ($new_timeout - $margin) * 1000;
$show_seconds = $margin-1;
echo '{js}';

//echo 'elapsed='; echo $elapsed; echo ';';
//echo 'new_timeout="'; echo $new_timeout; echo '";';
//echo 'warning_time='; echo $warning_time; echo ';';
//echo 'new_interval='; echo $new_interval; echo ';';
//echo 'alert("Elapsed = '.$elapsed.'\nNew TimeOut = '.$new_timeout.'\nWarning Time = '.$warning_time.'\nNew Interval = '.$new_interval.'\nRecall In = '.$recall_in.'");';

if($elapsed >= $warning_time)
{
	echo 'clearTimeout(top.auto_sess_timer);';
	echo 'top.log_sec_timer = setInterval(top.fun_sec_timer,1000);';
	echo 'top.showDialog("Session Timeout Warning","<b>You will be logged out in <span id=\'logout_seconds\' style=\'color:#f00\'>'.$show_seconds.'</span> Seconds.</b><br /><br />","Keep Session Active","javascript:top.do_sess_alive();","Logout Now","javascript:top.goLogout();","dialogBoxScreen");';
	echo 'clearInterval(top.log_sec_timer);';
	echo 'top.log_sec_timer = setInterval(top.fun_sec_timer,1000);';
}
else{
	
	if($new_interval < 0) $new_interval = 0;
//	echo 'alert("in else. New Interval = '.$new_interval.'");';
	echo ' top.ajax_sess_timer = setTimeout(function(){
					clearTimeout(top.auto_sess_timer);
					clearTimeout(top.ajax_sess_timer);
					top.sessionCheck("'.$sess_timeout.'","'.$recall_in.'","callBack","auto_sess_timeout_span");
				}, '.$new_interval.');';
	/*echo 'clearTimeout(top.auto_sess_timer);';
	echo 'function ajax_warn_sess_timeout(){
					top.sessionCheck("'.$sess_timeout.'","'.$recall_in.'","callBack","auto_sess_timeout_span");
			
		}';*/
}
?>