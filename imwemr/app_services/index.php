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
/*$_REQUEST['reqModule'] = "pag";
$_REQUEST['pag_service'] = "medical_history, active_problem_list";
$_REQUEST['patient'] = '100024';*/
	$ignoreAuth = true;

	// Accessing the Global file
	require_once "../config/globals.php";
	
	$library_path = $GLOBALS['webroot'].'/library';
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

	// Database access class
	require_once "inc_classes/db.php";
	include_once($GLOBALS['fileroot'].'/library/html_to_pdf/html2pdf.class.php');
	
	
	// creating the database object	
	//Global variable $gbl_imw_connect
	$gbl_imw_connect = $sqlconf;
	global $gbl_imw_connect;
	
	$sqlArr = array();
	$sqlArr['host'] = (isset($sqlconf['host']) && empty($sqlconf['host']) == false) ? $sqlconf['host'] : '';
	$sqlArr['port'] = (isset($sqlconf['port']) && empty($sqlconf['port']) == false) ? $sqlconf['port'] : '';
	$sqlArr['login'] = (isset($sqlconf['login']) && empty($sqlconf['login']) == false) ? $sqlconf['login'] : '';
	$sqlArr['pass'] = (isset($sqlconf['pass']) && empty($sqlconf['pass']) == false) ? $sqlconf['pass'] : '';
	$sqlArr['dbName'] = (isset($sqlconf['idoc_db_name']) && empty($sqlconf['idoc_db_name']) == false) ? $sqlconf['idoc_db_name'] : '';
	
	array_walk($sqlArr,"trim");
	$db_obj = new app_db($sqlArr);
	
	
	//file_put_contents('text.txt', print_r($_REQUEST, true).'--------------------------------------------------------------------------------\r\n\r\n', FILE_APPEND | LOCK_EX);
	//file_put_contents('text.txt', print_r($_FILES, true).'--------------------------------------------------------------------------------\r\n\r\n', FILE_APPEND | LOCK_EX);
	
	//Common Functions
	function attachHttp2file($file){
		if(!empty($file)){
			$tmp_http = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'];
			$uppth = $GLOBALS['rootdir']."/main/uploaddir";
			$file = $GLOBALS['php_server']."/data/".constant('PRACTICE_PATH').$file;
		}
		return $file;
	}
	
	function extractDate($str){
		$srch = "<~ED~>";
		$dt = "";
		$indx = strpos($str,$srch);
		if($indx !== false){
			$dt = str_replace($srch,"",substr($str,$indx));
			$str = substr($str,0,$indx);
		}
		return array(trim($str),trim($dt));
	}
	
	function logged_in_facility_info($loggedinFacId){
		$loggedinFacInfo="";
		$qry = imw_query("SELECT name, street, city, state, postal_code, zip_ext FROM `facility` WHERE id='".$loggedinFacId."'");
		if(imw_num_rows($qry)>0){
			$qryRes 		=  imw_fetch_assoc($qry);
			$facName		=  $qryRes['name'];
			$facStreet  	=  $qryRes['street'];
			$facCity 		=  $qryRes['city'];
			$facState		=  $qryRes['state'];
			$facPostal_code =  $qryRes['postal_code'];
			$facZip_ext 	=  $qryRes['zip_ext'];
		}
		$loggedinFacInfo = array($facName,$facStreet,$facCity,$facState,$facPostal_code,$facZip_ext);
		return $loggedinFacInfo;
	}
	
	function uniqueName($pat_id)
	{
		$id=$pat_id.time().rand();
		return $name=$id;
	}
	
	function Delete($path)
	{
		if (is_dir($path) === true)
		{
			$files = array_diff(scandir($path), array('.', '..'));

			foreach ($files as $file)
			{
				if (is_dir($path. '/' . $file) === false)
				{
					Delete(realpath($path) . '/' . $file);
				}
			}

			return rmdir($path);
		}

		else if (is_file($path) === true)
		{
			return unlink($path);
		}

		return false;
	}
	
	function inputBox($typ,$id,$val)
	{
		$val=($val)?$val:'  ';
			
		if($typ=='{TEXTBOX_MEDIUM}')
		{
			//$strRt='<div style="height:20px; width:200px; min-width:200px; border:#999 1px solid; display:inline-block" id="'.$id.'">'.$val.'</div>';	
			$strRt='<input class="manageUserInput" type="text" name="'.$id.'" id="'.$id.'" value="'.$val.'" size="60" maxlength="'.$size.'" autocomplete="off"><span id="'.$id.'_span" style="display:none"></span>';
		}elseif($typ=='{TEXTBOX_SMALL}')
		{
			//$strRt='<div style="height:20px; width:200px; min-width:200px; border:#999 1px solid; display:inline-block" id="'.$id.'">'.$val.'</div>';	
			$strRt='<input class="manageUserInput" type="text" name="'.$id.'" id="'.$id.'" value="'.$val.'" size="30" maxlength="'.$size.'" autocomplete="off"><span id="'.$id.'_span" style="display:none"></span>';
		}
		elseif($typ=='{TEXTBOX_XSMALL}')
		{
			//$strRt='<div style="height:20px; width:200px; min-width:200px; border:#999 1px solid; display:inline-block" id="'.$id.'">'.$val.'</div>';	
			$strRt='<input class="manageUserInput" type="text" name="'.$id.'" id="'.$id.'" value="'.$val.'" size="1" maxlength="'.$size.'" autocomplete="off"><span id="'.$id.'_span" style="display:none"></span>';
		}
		else
		{
			//$strRt='<div style="height:70px; width:300px; min-width:300px; border:#999 1px solid; display:inline-block" id="'.$id.'">'.$val.'</div>';
			$strRt='<textarea class="manageUserInput" rows="2" cols="90" name="'.$id.'" id="'.$id.'">'.$val.'</textarea><span id="'.$id.'_span" style="display:none"></span>';	
		}	
		return $strRt;
	}

	$addition_path="/admin_consent";
	if($_REQUEST['op_id']) $op_id=$_REQUEST['op_id'];
	$filePath = data_path()."app_services/signature/tmp/".$op_id.$addition_path;
	if (is_dir($filePath)) {
		delete($filePath);
	}//clear dir for that operator
	mkdir($filePath, 0777);
	
	function createBlankImg($pat_id,$op_id,$addition_path)
	{
		if(empty($addition_path)) $addition_path="/admin_consent";
		//global $webServerRootDirectoryName;
		//global $web_RootDirectoryName,$web_root;
		if($pat_id){
			$id=uniqueName($pat_id);
			$fileName=$id.'.jpeg';
			//$filePath = data_path()."app_services/signature/tmp/".$op_id.$addition_path.'/';
			$filePath =data_path(1)."PatientId_".$pat_id."/sign/";
			$x=304;
			$y=85;
			// Create a blank image and add some text
			$im = imagecreatetruecolor($x, $y);
			$white = imagecolorallocate($im, 255, 255, 255);
			imagefilledrectangle($im, 0, 0, $x, $y, $white);
			// Set the content type header - in this case image/jpeg
			//header('Content-Type: image/jpeg');
			// Output the image
			imagejpeg($im, $filePath.$fileName);
			// Free up memory
			imagedestroy($im);	
			//return file name and path
		}
               $fullpath[]= data_path(1)."PatientId_".$pat_id."/sign/".$fileName;
		
		//$fullpath[]=data_path(1)."app_services/signature/tmp/".$op_id."/admin_consent/".$fileName;
		$fullpath[]=$id;
		//$fullpath=$filePath.$fileName;
		return $fullpath;
	}
	
	$reqModule = trim($_REQUEST["reqModule"]);	
	$responseArray = array();
	switch($reqModule)
	{
		case "login":			
			$idocUser = trim($_REQUEST["idocUser"]);
			$passwordMd = trim($_REQUEST["passwordMd"]);
			$passwordSh = trim($_REQUEST["passwordSh"]);
			$passwordMdUser = array($idocUser, $passwordMd);
			$passwordShUser = array($idocUser, $passwordSh);
			$result_json = array();
			$fac_arr_temp ="";
			$loginSuccess = 0;			

			if($idocUser == "" || ($passwordMd == "" && $passwordSh == ""))
			{
				$result_json["loginResult"] = "failure";
				$result_json["failureReason"] = "Username and password can not be blank";
				$response_data = json_encode($result_json);				
				die($response_data);
			}
			
			$login_auth_qry = "SELECT id, sch_facilities, user_group_id FROM users WHERE username = ? and password = ?   LIMIT 1";
			
			/* Checking with SHA1 */
			$db_obj->run_query($login_auth_qry, $passwordShUser);
			$auth_result = $db_obj->get_qry_result();
			if(count($auth_result) == 1)
			{
				$user_data = $auth_result[0];
				$loginSuccess = 1;
			}
			else
			{
				/* Checking with MD5 */
				$db_obj->run_query($login_auth_qry, $passwordMdUser);
				$auth_result = $db_obj->get_qry_result();							
				//var_dump($auth_result);
				if(count($auth_result) == 1)
				{
					$user_data = $auth_result[0];
					$loginSuccess = 1;					
				}
				else
				{
					$result_json["loginResult"] = "failure";
					$result_json["failureReason"] = "Not an Authorized User";
					$response_data = json_encode($result_json);
					die($response_data);
				}
			}
			
			if($loginSuccess == 1)
			{
				$result_json["loginResult"] = "success";
				$result_json["loginUserId"] = (int) $user_data["id"];
				// Getting the user type
				$query = imw_query("select name from user_groups where id = '".$user_data["user_group_id"]."'");
				$result = imw_fetch_assoc($query);
				
				$last_character = substr($result['name'], -1 );
				if($last_character == 's'){
					$result['name'] =  substr( $result['name'],0, -1 );
				}
				$qry = imw_query("select color from user_type where user_type_name = '".$result['name']."'");
				$res_color = imw_fetch_assoc($qry);
				$color = explode(',',$res_color['color']);
				$result_json['UserType'] = $result['name'];
				
				foreach($color as $key => $value){
					if($value == ""){
						$result_json["col_name_0"] = '';
						$result_json["col_name_1"] = '';	
					}
					else{
					
						$result_json["col_name_$key"] = $value;
						
					}
				} 
				
				// Getting the facility data
				$fac_arr = array();
				$facilities_id_arr = explode(';', $user_data["sch_facilities"]);
				foreach($facilities_id_arr  as $key=>$val){
					if(trim($val) == "")unset($facilities_id_arr[$key]);
				}
				$facilities_id_str = implode(',',$facilities_id_arr);
				$fac_qry = "SELECT id, name FROM facility WHERE id IN(".$facilities_id_str.")";
				$db_obj->run_query($fac_qry);
				$fac_qry_obj = $db_obj->get_qry_result();
				if(count($fac_qry_obj)>0)
				{
					if($_REQUEST['app']=='android')
					{
						foreach($fac_qry_obj as $fac_data_row)
						{
							$sub_array['id']=$fac_data_row["id"];
							$sub_array['name']=$fac_data_row["name"];
							$fac_arr[]=$sub_array;
							$fac_arr_temp.=   $fac_data_row["id"].',';
						}
					}
					else
					{
						foreach($fac_qry_obj as $fac_data_row)
						{	
							$fac_arr[$fac_data_row["id"]] = $fac_data_row["name"];
							$fac_arr_temp.=   $fac_data_row["id"].',';
								
						}
					}
				}
				$result_json['facilityData'] = $fac_arr;
				$result_json['facilityData_key'] = $fac_arr_temp;
				// Getting the login provider name
				
				$user_qry = "SELECT fname, lname, mname FROM users WHERE id = ?";
				$db_obj->run_query($user_qry, array($result_json["loginUserId"]));
				$user_qry_obj = $db_obj->get_qry_result();				
				if(count($user_qry_obj) > 0)
				{
					$user_data = $user_qry_obj[0];	
					$user_name = core_name_format($user_data["lname"], $user_data["fname"], $user_data["mname"]);
					$result_json['loginDoctorName'] = $user_name;
				}
				if($_REQUEST['pre'])pre($result_json);
				
				/*Set flag in session to Allow access to images and pdf*/
				$_SESSION['allowAppAccess'] = true;
				
				echo json_encode($result_json);	
			}
		break;
		case "scheduler":			
			require_once "inc_classes/sch_service.php";
			
			$sch_service_obj = new sch_service($db_obj);
			
			$sch_service = trim($_REQUEST["sch_service"]);
			if($sch_service != "")
			{
				if(method_exists($sch_service_obj, $sch_service))
				{
					call_user_func(array($sch_service_obj, $sch_service));
				}
				else
				{
					echo "NO SCHEDULER SERVICE EXISTS";	
				}				
			}
			else
			{
				echo "NO SCHEDULER SERVICE SPECIFIED";	
			}
						
		break;
		case "pag":
			include_once("pag_handler.php");
		break;
		case "patient_messages":
			include_once("patient_messages_handler.php");
		break;
		case "phy_messages":
			include_once("phy_messages_handler.php");
		break;
		case "direct_messages":
			include_once("direct_messages_handler.php");
		break;
		case "pending_charts":
			include_once("pending_charts_handler.php");
		break;
		case "pending_task_tests":
			include_once("pending_task_tests_handler.php");
		break;
		case "pending_erx":
			include_once("pending_erx_handler.php");
		break;
		case "imedic_monitor":
			include_once("imedic_monitor_handler.php");
		break;
		case "chart_notes":
			include_once("chart_notes_handler.php");
		break;
		case "medical_hx":
			include_once("medical_hx_handler.php");
		break;
		case "tests":
			include_once("tests_handler.php");
		break;
		case "physician_console":
			include_once("physician_console.php");
		break;
		case "consent_forms":
			include_once("consent_forms.php");
		break;
		case "consult_letters":
			include_once("consult_letters.php");
		break;
		case "work_view":
		include_once("work_view.php");
		die;
		default:
			echo "NOT AUTHORIZED FOR THE SERVICE";
		break;			
	}
?>