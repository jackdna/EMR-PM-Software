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
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");

$prac_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
$basePath = $prac_path.'BatchFiles';

$directory = '';
$file_type = (isset($_REQUEST['file_type']) && trim($_REQUEST['file_type'])!=='')?strtolower( trim($_REQUEST['file_type']) ):'';
/*Emedeon User Id*/
$user_id = (isset($_REQUEST['userId']) && trim($_REQUEST['userId'])!=='')?trim($_REQUEST['userId']):'';

if($file_type === 'era' && $user_id!='')
	$directory = $basePath.'/others/get_era/'.$user_id;
elseif($file_type === 'report' && $user_id!=='')
	$directory = $basePath.'/others/get_report/'.$user_id;
	
$valid_query = array('list', 'del');
$query = (isset($_REQUEST['query']) && trim($_REQUEST['query'])!=='')?strtolower(trim($_REQUEST['query'])):'';

/*Used to delete file*/
$file_name = (isset($_REQUEST['file_name']) && trim($_REQUEST['file_name'])!=='')?trim($_REQUEST['file_name']):'';

/*Scan Directory and List Files*/
if($directory!=='' && is_dir($directory) && in_array($query, $valid_query)){
	
	if($query==='list'){
		$files = scandir($directory, 1);
		$files = array_filter($files, function($file){
			return ($file!== '.' && $file!=='..');
		});
		$returnData = array();
		$returnData['files'] = $files;
		$returnData['practice_path'] = constant('PRACTICE_PATH');
		print json_encode($returnData);
	}
	elseif($query==='del' && $file_name!==''){
		/*$file_name = $directory.'/'.$file_name;
		if( file_exists($file_name) )
			$resp = unlink($file_name);
		print ($resp)?'success':'fail';*/
		print 'success';
	}
}
?>