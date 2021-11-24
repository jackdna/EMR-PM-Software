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
$include_sch_class=true;
require_once(dirname(__FILE__).'/vital_functions.php');
/*Check availability of Vital FTP credentials*/
if( !defined('VITAL_IDOC_USER') || trim(constant('VITAL_IDOC_USER')) == '')
{
	//get user id using user name
	$q=imw_query("select id from users where user_name='".constant('VITAL_IDOC_USER')."' and delete_status=0 limit 1");
	if(imw_num_rows($q)<=0)
	{
		print 'Please provide correct and active iDoc user name for Vital Interaction.';
		exit;
	}
	$d=imw_fetch_object($q);
	define("VITAL_IDOC_USER_ID", $d->id);
}
FUNC_downloadFile($fileDir);
/*require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
$csv_content=file_get_contents('530_vital_interaction_appt_statuses_from_20190713000000_to_20190719235959.csv');
//scheduler object
$obj_scheduler = new appt_scheduler;
//write file on local system to read it as csv
				$local_csv_file=$fileDir.'/vital_updates.csv';
				//delete if any file exist
				unlink($local_csv_file);
				$fp = fopen($local_csv_file, 'w');
				fwrite($fp, $csv_content);
				fclose($fp);
updateStatus($local_csv_file, $obj_scheduler);*/
?>