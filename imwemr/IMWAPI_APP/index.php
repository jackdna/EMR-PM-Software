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
 * index.php
 * Access Type: Direct
 * Purpose: Support iDoc API Calls using Klein REST api router.
*/
set_time_limit (90);

require_once(dirname(__FILE__).'/commonRoutes.php');

/* List of Servers/Practise */
$router->respond(array('POST','GET'), '/getPractiseList', function($request, $response, $service, $app) use($router){

	// Static Array Of Practise //
	$result = array();
	$req = "SELECT * FROM practices_server where status = 1";
	$res = $app->dbh->imw_query($req);
	
	$i=0;
	while($resultServer = $app->dbh->imw_fetch_assoc($res)){

		$urlArr = parse_url($resultServer['practice_url']);
		$encodeScheme = sha1(md5($urlArr['scheme']));
		$encodeSlash = sha1(md5('/'));
		$result[$i]['ServerName'] = $resultServer['practice_name'];
		// Conerting Url To Secure Link //
		$str = sha1(md5('ImW1MedPrac1C'));
		$encodeStr = base64_encode(str_replace(array($urlArr['scheme'], '/'), array($encodeScheme, $encodeSlash), $resultServer['practice_url']));
		$result[$i]['ServerLink'] = $str.$encodeStr;
		$i++;
	}
	
	return json_encode($result);
	
});

// To Handle First Auth Of Patient //
$router->respond(array('POST','GET'), '/patientFirstAuth', function($request, $response, $service, $app) use($router){
	
	//$service->validateParam('lname', 'Please provide valid lastname')->notNull();
	//$service->validateParam('temp_key', 'Please provide temp key')->notNull();
	$p_fname = trim(addslashes($request->__get("fname")));
	$p_lname = trim(addslashes($request->__get("lname")));
	$p_dob = trim(addslashes($request->__get("dob")));
	$p_zip = trim(addslashes($request->__get("zip")));
	$p_gender = trim(addslashes($request->__get("gender")));
	$temp_key = trim(addslashes($request->__get('temp_key')));
	
	/*$req_sql = "SELECT id,temp_key_chk_val FROM patient_data WHERE fname='".$p_fname."' and lname = '".$p_lname."' and DOB = '".$p_dob."' and LEFT(TRIM(postal_code), 5)='".$p_zip."' and sex='".$p_gender."' and temp_key='".$temp_key."' LIMIT 1";*/
	
	$req_sql = "SELECT id,temp_key_chk_val FROM patient_data WHERE fname='".$p_fname."' and lname = '".$p_lname."' and DOB = '".$p_dob."' and postal_code ='".$p_zip."' and sex='".$p_gender."' and temp_key='".$temp_key."' LIMIT 1";
	
	$req_sql_obj = $app->dbh->imw_query($req_sql);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	$row = $app->dbh->imw_fetch_assoc($req_sql_obj);
	$result['p_id'] = (int)$row['id'];
	if($rowCount!=0){
		$result['patient_flag'] = true;
		$result['patient_exist'] = "exist";
	}
	else{
	 	$result['patient_flag'] = false;
		$result['patient_exist'] = "User does not exists with above filled information. Please check";
	}
	if($rowCount == 1 || is_array($row)){
		$p_id = $row["id"];
		$req = "SELECT id,username FROM patient_data WHERE id = '".$p_id."'";
		
		$res = $app->dbh->imw_query($req);
		$res_count = $app->dbh->imw_num_rows($res);
		$row_obj = $app->dbh->imw_fetch_assoc($res);
		
		if($row_obj['username'] != ""){
			$result['patient_user'] = true;
			//$result['user_name'] = $row_obj['username'];
			$result['patient_quote'] = "You have already registered";
		}	
		else{
			//$result['user_name'] = "";
			$result['patient_user'] = false;
			$result['patient_quote'] = "Not registered";
		}		
	}
	
	$post_result = $request->__get("resp");
	if($post_result == 1 ){
		$pr_p_fname = trim(addslashes($request->__get("pr_p_fname")));
		$pr_p_lname = trim(addslashes($request->__get("pr_p_lname")));
		$pr_dob = $request->__get("pr_p_dob");
		$pr_p_zip = trim(addslashes($request->__get("pr_p_zip")));
		$pr_p_gender = $request->__get("pr_p_gender");
		$pr_id = $request->__get("pr_id");
		
		$req_sql_1 = "SELECT id, patient_id,hippa_release_status,resp_username,resp_password FROM resp_party WHERE fname='".$pr_p_fname."' and lname = '".$pr_p_lname."' and DOB = '".$pr_dob."' and zip='".$pr_p_zip."' and sex='".$pr_p_gender."' and patient_id = '".$pr_id."' LIMIT 1";
		
		$res_obj_1 = $app->dbh->imw_query($req_sql_1);
		$count_obj_1 = $app->dbh->imw_num_rows($res_obj_1);
		$row_obj_1 = $app->dbh->imw_fetch_assoc($res_obj_1);
		unset($result['patient_flag']);
		unset($result['patient_user']);
		unset($result['patient_quote']);
		unset($result['patient_exist']);
		
		if($count_obj_1==0){
			$result['resp_user'] = true;
			$result['user_quote'] = "You are not authorized for this patient.";
		}
		else if($row_obj_1['hippa_release_status']==0){
			$result['resp_user'] = true;
			$result['user_quote'] = "You are not authorized for HIPPA release information.";
		}
		else if($row_obj_1['resp_username']!="" && $row_obj_1['resp_password']!=""){
			$result['resp_user'] = true;
			$result['user_quote'] = "already exist";	
		}
		else{
			$result['resp_user'] = false;
			$result['p_id'] = (int)$row_obj_1['patient_id'];
			$result['resp_id'] = (int)$row_obj_1['id'];
			
		}
	}
	return json_encode($result);
	//$router->skipNext();
});

/* List of Security Questions */

$router->respond(array('POST','GET'), '/listSecQues', function($request, $response, $service, $app) use($router){
	$result = array();
	$reqQry = "SELECT * FROM iportal_sec_questions WHERE del_status = 0";
	$req_sql_obj = $app->dbh->imw_query($reqQry);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	$i=0;
	while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
		$result[$i]['id'] = $row['id'];
		$result[$i]['ques'] = $row['name'];
		$i++;
	}
	return json_encode($result);
	//$response->append(json_encode($result));
	//$router->skipNext();
});

/* End of Security Questions */

// set user information
$router->respond(array('POST','GET'), '/setUserInfo', function($request, $response, $service, $app) use($router){

	$id = $request->__get('p_id');
	$userID = $request->__get('userID');
	$pass = $request->__get('password');
	$prefer_image = $request->__get('prefer_image');
	$sec_ques = $request->__get('sec_ques');
	$sec_ans = $request->__get('sec_ans');
	$priority = $request->__get('priority');
	
	$sec_ques = explode(',',$sec_ques);
	$sec_ans = explode(',',$sec_ans);
	$priority = explode(',',$priority);
	
	$qryUser="SELECT id from patient_data where username='".$userID."'";
	$req_sql_obj = $app->dbh->imw_query($qryUser);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	if($rowCount!=0){
		$result = 0;
		return json_encode($result);
	}
	else{
		$qryUser="UPDATE patient_data set 
										username='".$userID."',
										password = '".$pass."',
										preferred_image = '".$prefer_image."',
										enocde_status = 1 
										where id ='".$id."'";
		$up_qry = $app->dbh->imw_query($qryUser);
	
		$query = "SELECT id from patient_sec_questions where patient_id = '".$id."' ORDER BY que_priority ASC";
		$req_sql_obj = $app->dbh->imw_query($query);
		while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
			$main_id[] = $row; 
		}
		$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
		
		if($rowCount==0){
			
			foreach($sec_ques as $key => $value){
				$query = "INSERT INTO patient_sec_questions set patient_id = '".$id."',
																sec_question_id = '".$value."',
																sec_answer = '".$sec_ans[$key]."',
																que_priority = '".$priority[$key]."'";
				$res = $app->dbh->imw_query($query);
			}
			$result = 1;
			return json_encode($result);
															
		}
		else{
			foreach($sec_ques as $key => $value){
				$query = "UPDATE patient_sec_questions set patient_id = '".$id."',
																	sec_question_id = '".$value."',
																	sec_answer = '".$sec_ans[$key]."',
																	que_priority = '".$priority[$key]."'
																	where id = '".$main_id[$key]['id']."'";
				$res = $app->dbh->imw_query($query);
					
			}	
			$result = 1;
			return json_encode($result);
		}
			
	}	
});
// end of user information
	
// set resp. information
$router->respond(array('POST','GET'), '/setRespInfo', function($request, $response, $service, $app) use($router){

	$id = $request->__get('resp_id');
	$p_id = $request->__get('p_id');
	$userID = $request->__get('userID');
	$pass = $request->__get('password');
	$prefer_image = $request->__get('prefer_image');
	$sec_ques = $request->__get('sec_ques');
	$sec_ans = $request->__get('sec_ans');
	$priority = $request->__get('priority');
	
	$sec_ques = explode(',',$sec_ques);
	$sec_ans = explode(',',$sec_ans);
	$priority = explode(',',$priority);
	
	$qryUser="SELECT id from resp_party where username='".$userID."'";
	$req_sql_obj = $app->dbh->imw_query($qryUser);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	if($rowCount!=0){
		$result = 0;
		return json_encode($result);
	}
	else{
		$qryUser="UPDATE resp_party set 
										resp_username='".$userID."',
										resp_password = '".$pass."',
										preferred_image = '".$prefer_image."', 
										enocde_status = 1
										where id ='".$id."'";
		$up_qry = $app->dbh->imw_query($qryUser);
		
		$query = "SELECT id from patient_sec_questions where patient_id = '".$p_id."' ORDER BY que_priority ASC";
		
		$req_sql_obj = $app->dbh->imw_query($query);
		while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
			$main_id[] = $row; 
		}
		$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
		
		if($rowCount==0){
			
			foreach($sec_ques as $key => $value){
				$query = "INSERT INTO patient_sec_questions set patient_id = '".$p_id."',
																sec_question_id = '".$value."',
																sec_answer = '".$sec_ans[$key]."',
																que_priority = '".$priority[$key]."'";
				$res = $app->dbh->imw_query($query);
			}	
			$result = 1;
			return json_encode($result);
															
		}
		else{
				foreach($sec_ques as $key => $value){
					$query = "UPDATE patient_sec_questions set patient_id = '".$p_id."',
																	sec_question_id = '".$value."',
																	sec_answer = '".$sec_ans[$key]."',
																	que_priority = '".$priority[$key]."'
																	where id = '".$main_id[$key]['id']."'";
					$res = $app->dbh->imw_query($query);
				}	
				$result = 1;
				return json_encode($result);
			}
		}	
	});
	// end of resp. information
	
// Forgot password
$router->respond(array('POST','GET'), '/forgotPass', function($request, $response, $service, $app) use($router){
	
	$p_fname = trim(addslashes($request->__get("fname")));
	$p_lname = trim(addslashes($request->__get("lname")));
	$p_dob = trim($request->__get("dob"));
	$p_zip = trim(addslashes($request->__get("zip")));
	$p_gender = $request->__get("gender");
	
	/*$req_sql = "SELECT id FROM patient_data WHERE fname='".$p_fname."' and lname = '".$p_lname."' and DOB = '".$p_dob."' and LEFT(TRIM(postal_code), 5)='".$p_zip."' and sex='".$p_gender."'";*/
	
	$req_sql = "SELECT id, username FROM patient_data WHERE fname='".$p_fname."' and lname = '".$p_lname."' and DOB = '".$p_dob."' and postal_code = '".$p_zip."' and sex='".$p_gender."'";
	
	$req_sql_obj = $app->dbh->imw_query($req_sql);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	$data = $app->dbh->imw_fetch_assoc($req_sql_obj);
	
	if($rowCount!=0 && $data['username']!=""){
		$result['p_id'] =  $data['id'];
		$query = "SELECT id,sec_question_id from patient_sec_questions where patient_id = '".$data['id']."' AND que_priority = 1";
		$req_sql_obj = $app->dbh->imw_query($query);
		$ques_id = $app->dbh->imw_fetch_assoc($req_sql_obj);
		
		$qry = "SELECT id,name from iportal_sec_questions where id = '".$ques_id['sec_question_id']."'";
		$req_sql_obj = $app->dbh->imw_query($qry);
		$ques = $app->dbh->imw_fetch_assoc($req_sql_obj);
		$result['ques_id'] =  $ques_id['id'];
		$result['ques'] =  $ques['name'];
		$result['response'] = true;
	}
	else{
		$result['response'] = false;
	}
	return json_encode($result);
	//$response->append(json_encode($result));
	//$router->skipNext();
});
// end of Forgot password

// verify security ques
$router->respond(array('POST','GET'), '/verifySecQues', function($request, $response, $service, $app) use($router){
	
	$id = trim(addslashes($request->__get("id")));
	$ans = trim(addslashes($request->__get("ans")));
	$result = array();
	$query = "SELECT patient_id from patient_sec_questions where id = '".$id."' AND que_priority = 1 AND LOWER(sec_answer) = '".$ans."'";
		$req_sql_obj = $app->dbh->imw_query($query);
		$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
		if($rowCount!=0){
			$data = $app->dbh->imw_fetch_assoc($req_sql_obj);
			$result['p_id'] = $data['patient_id'];
			
			$qry = "SELECT username,preferred_image from patient_data where id = '".$data['patient_id']."'";
			$req_sql_obj = $app->dbh->imw_query($qry);
			while($rec = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				$result['username'] = $rec['username'];
				$result['preferred_image_id'] = $rec['preferred_image'];
			}
			// get a image from id
			$qry_1 = "SELECT name from  iportal_preferred_images where id = '".$result['preferred_image_id']."'";
			$req_sql_obj = $app->dbh->imw_query($qry_1);
			$image_data = $app->dbh->imw_fetch_assoc($req_sql_obj);
			$image = file_get_contents(data_path()."preferred_images/".$image_data['name']);
			$result['preferred_image'] = base64_encode($image);
			
			// get all security ques.
			$qry = "SELECT * from patient_sec_questions where patient_id = '".$data['patient_id']."'";
			$req_sql_obj = $app->dbh->imw_query($qry);
			$result['pt_sec'] = array();
			$i=0;
			while($record = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				
				$result['pt_sec'][$i]['id'] = $record['id'];
				$result['pt_sec'][$i]['sec_question_id'] = $record['sec_question_id'];
				$result['pt_sec'][$i]['sec_answer'] = $record['sec_answer'];
				$result['pt_sec'][$i]['que_priority'] = $record['que_priority'];
				$i++;
			}
			
			foreach($result['pt_sec'] as $key => $value){
			
				$reqQry = "SELECT name FROM iportal_sec_questions WHERE id = '".$value['sec_question_id']."'";
				$req_sql = $app->dbh->imw_query($reqQry);
				$data = $app->dbh->imw_fetch_assoc($req_sql);
				$result['pt_sec'][$key]['question'] = $data['name'];
			}
			
			$reqQry = "SELECT * FROM iportal_sec_questions WHERE del_status = 0 ORDER by id ASC";
			$req_sql_obj = $app->dbh->imw_query($reqQry);
			$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
			$result['ques'] = array();
			$i=0;
			while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				$result['ques'][$i]['id'] = $row['id'];
				$result['ques'][$i]['ques'] = $row['name'];
				$i++;
			}
			$reqQry = "SELECT * FROM  iportal_preferred_images ORDER by id ASC";
			$req_sql_obj = $app->dbh->imw_query($reqQry);
			$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
			$result['images'] = array();
			$i=0;
			while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				if(file_exists(data_path()."preferred_images/".$row['name'])){
					$result['images'][$i]['id'] = $row['id'];
					//$result['images'][$i]['image_name'] = $row['name'];
					$image_name = file_get_contents(data_path()."preferred_images/".$row['name']);
					$result['images'][$i]['image'] = base64_encode($image_name);
					$i++;
				}
			}
			$result['response'] = true;
		}
		else{
			$result['response'] = false;
		}
	return json_encode($result);
});
// end of verify ques

// set user information
$router->respond(array('POST','GET'), '/changeUserInfo', function($request, $response, $service, $app) use($router){
	$result = array();
	$id = $request->__get('p_id');
	$pass = $request->__get('password');
	$prefer_image = $request->__get('image_id');
	$sec_ques = $request->__get('que_id');
	$sec_ans = $request->__get('ans');
	
	$sec_ques = explode(',',$sec_ques);
	$sec_ans = explode(',',$sec_ans);
	
		$qryUser="UPDATE patient_data set 
										password = '".$pass."',
										preferred_image = '".$prefer_image."' 
										where id ='".$id."'";
		$up_qry = $app->dbh->imw_query($qryUser);
		if($up_qry && $app->dbh->imw_affected_rows() > 0){
			$query = "SELECT id from patient_sec_questions where patient_id = '".$id."' ORDER BY que_priority ASC";
			$req_sql_obj = $app->dbh->imw_query($query);
			while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				$main_id[] = $row; 
			}
			
			$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
			$i=1;
			foreach($sec_ques as $key => $value){
				$query = "UPDATE patient_sec_questions set patient_id = '".$id."',
														sec_question_id = '".$value."',
														sec_answer = '".$sec_ans[$key]."',
														que_priority = '".$i."'
														where id = '".$main_id[$key]['id']."'";
				$res = $app->dbh->imw_query($query);
				$i++;
			}		
		
			$result['response'] = true;
		}
		else{
			$result['response'] = false;
		}
	return json_encode($result);
});
// end of user information


// Forgot resp. password
$router->respond(array('POST','GET'), '/forgotRespPass', function($request, $response, $service, $app) use($router){
	$result = array();
	$p_fname = trim(addslashes($request->__get("fname")));
	$p_lname = trim(addslashes($request->__get("lname")));
	$p_dob = trim($request->__get("dob"));
	$p_zip = trim(addslashes($request->__get("zip")));
	$p_gender = $request->__get("gender");
	
	$req_sql = "SELECT id,patient_id,resp_username FROM resp_party WHERE fname='".$p_fname."' and lname = '".$p_lname."' and dob = '".$p_dob."' and LEFT(TRIM(zip), 5)='".$p_zip."' and sex='".$p_gender."'";
	
	$req_sql_obj = $app->dbh->imw_query($req_sql);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	$data = $app->dbh->imw_fetch_assoc($req_sql_obj);
	
	if($rowCount!=0 && $data['resp_username']!=""){
		$result['p_id'] =  $data['patient_id'];
		$result['resp_id'] = $data['id'];
		$query = "SELECT id,sec_question_id from patient_sec_questions where patient_id = '".$data['patient_id']."' AND que_priority = 1";
		$req_sql_obj = $app->dbh->imw_query($query);
		$ques_id = $app->dbh->imw_fetch_assoc($req_sql_obj);
		
		$qry = "SELECT id,name from iportal_sec_questions where id = '".$ques_id['sec_question_id']."'";
		$req_sql_obj = $app->dbh->imw_query($qry);
		$ques = $app->dbh->imw_fetch_assoc($req_sql_obj);
		$result['ques_id'] =  $ques_id['id'];
		$result['ques'] =  $ques['name'];
		$result['response'] = true;
	}
	else{
		$result['response'] = false;
	}
	return json_encode($result);
	//$response->append(json_encode($result));
	//$router->skipNext();
});
// end of Forgot resp. password


// verify resp security ques
$router->respond(array('POST','GET'), '/verifyRespSecQues', function($request, $response, $service, $app) use($router){
	
	$result = array();
	$id = trim(addslashes($request->__get("id")));
	$ans = trim(addslashes($request->__get("ans")));
	$resp_id = trim(addslashes($request->__get("resp_id")));
	
	$query = "SELECT patient_id from patient_sec_questions where id = '".$id."' AND que_priority = 1 AND LOWER(sec_answer) = '".$ans."'";
		$req_sql_obj = $app->dbh->imw_query($query);
		$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
		
		if($rowCount!=0 && $resp_id !=0 && !empty($resp_id)){
			$data = $app->dbh->imw_fetch_assoc($req_sql_obj);
			$result['p_id'] = $data['patient_id'];
			$qry = "SELECT id,resp_username,preferred_image from resp_party where id = '".$resp_id."'";
			$req_sql_obj = $app->dbh->imw_query($qry);
			while($rec = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				$result['resp_id'] = $rec['id'];
				$result['username'] = $rec['resp_username'];
				$result['preferred_image_id'] = $rec['preferred_image'];
			}
			// get a image from id
			$qry_1 = "SELECT name from  iportal_preferred_images where id = '".$result['preferred_image_id']."'";
			$req_sql_obj = $app->dbh->imw_query($qry_1);
			$image_data = $app->dbh->imw_fetch_assoc($req_sql_obj);
			$image = file_get_contents(data_path()."preferred_images/".$image_data['name']);
			$result['preferred_image'] = base64_encode($image);
			
			// get all security ques.
			$result['pt_sec'] = array();
			$qry = "SELECT * from patient_sec_questions where patient_id = '".$data['patient_id']."' ORDER BY que_priority ASC";
			$req_sql_obj = $app->dbh->imw_query($qry);
			$i=0;
			while($record = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				
				$result['pt_sec'][$i]['id'] = $record['id'];
				$result['pt_sec'][$i]['sec_question_id'] = $record['sec_question_id'];
				$result['pt_sec'][$i]['sec_answer'] = $record['sec_answer'];
				$result['pt_sec'][$i]['que_priority'] = $record['que_priority'];
				$i++;
			}
			
			foreach($result['pt_sec'] as $key => $value){
			
				$reqQry = "SELECT name FROM iportal_sec_questions WHERE id = '".$value['sec_question_id']."'";
				$req_sql = $app->dbh->imw_query($reqQry);
				$data = $app->dbh->imw_fetch_assoc($req_sql);
				$result['pt_sec'][$key]['question'] = $data['name'];
			}
			$result['ques'] = array();
			$reqQry = "SELECT * FROM iportal_sec_questions WHERE del_status = 0 ORDER by id ASC";
			$req_sql_obj = $app->dbh->imw_query($reqQry);
			$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
			$i=0;
			while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				$result['ques'][$i]['id'] = $row['id'];
				$result['ques'][$i]['ques'] = $row['name'];
				$i++;
			}
			$result['images'] = array();
			$reqQry = "SELECT * FROM iportal_preferred_images ORDER by id ASC";
			$req_sql_obj = $app->dbh->imw_query($reqQry);
			$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
			$i=0;
			while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
				if(file_exists(data_path()."preferred_images/".$row['name'])){
					$result['images'][$i]['id'] = $row['id'];
					//$result['images'][$i]['image_name'] = $row['name'];
					$image_name = file_get_contents(data_path()."preferred_images/".$row['name']);
					$result['images'][$i]['image'] = base64_encode($image_name);
					$i++;
				}
			}
			$result['response'] = true;
		}
		else{
			$result['response'] = false;
		}
		return json_encode($result);
});
// end of verify ques of resp

// change resp party information
$router->respond(array('POST','GET'), '/changeRespInfo', function($request, $response, $service, $app) use($router){

	$id = $request->__get('resp_id');
	$p_id = $request->__get('p_id');
	$pass = $request->__get('password');
	$prefer_image = $request->__get('image_id');
	$sec_ques = $request->__get('que_id');
	$sec_ans = $request->__get('ans');
	
	$sec_ques = explode(',',$sec_ques);
	$sec_ans = explode(',',$sec_ans);
	
		$qry_User = "UPDATE resp_party set resp_password = '".$pass."',
										   preferred_image = '".$prefer_image."',
										   enocde_status = 1
										   where id = '".$id."'";
		$request = $app->dbh->imw_query($qry_User);
		
		$query = "SELECT id from patient_sec_questions where patient_id = '".$p_id."' ORDER BY que_priority ASC";
		$req_sql_obj = $app->dbh->imw_query($query);
		while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
			$main_id[] = $row; 
		}
		
		$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
		
		foreach($sec_ques as $key => $value){
		$i=1;
			$query = "UPDATE patient_sec_questions set patient_id = '".$p_id."',
													sec_question_id = '".$value."',
													sec_answer = '".$sec_ans[$key]."',
													que_priority = '".$i."'
													where id = '".$main_id[$key]['id']."'";
			$res = $app->dbh->imw_query($query);
			$i++;
		}		
		if($res && $app->dbh->imw_affected_rows() > 0){
			$result['response'] = true;
		}
		else{
			$result['response'] = false;
		}
	return json_encode($result);
});
// end of resp party information


// list of preffered images

$router->respond(array('POST','GET'), '/listOfImages', function($request, $response, $service, $app) use($router){
	$result = array();
	$reqQry = "SELECT * FROM  iportal_preferred_images Order By id ASC";
	$req_sql_obj = $app->dbh->imw_query($reqQry);
	$rowCount = $app->dbh->imw_num_rows($req_sql_obj);
	$i=0;
	
	while($row = $app->dbh->imw_fetch_assoc($req_sql_obj)){
		if(file_exists(data_path()."preferred_images/".$row['name'])){
			$result[$i]['id'] = $row['id'];
			$result[$i]['image_name'] = $row['name'];
			$image = file_get_contents(data_path()."preferred_images/".$row['name']);
			$result[$i]['image'] = base64_encode($image);
			$i++;
		}
	}
	
	$return =  json_encode($result);
	return $return;
	
});

// end of list

// Change Password
$router->respond(array('POST','GET'), '/changePass', function($request, $response, $service, $app) use($router){
	
	$patientId = $request->__get("patientId");
	$oldPass = $request->__get("oldpass");
	$newPass = $request->__get("newpass");
	$return['PassStatus'] = false;
	$reqQry = "SELECT password FROM  patient_data where id = '".$patientId."'";
	$req_sql_obj = $app->dbh->imw_query($reqQry);
	$row = $app->dbh->imw_fetch_assoc($req_sql_obj);
	
	if($oldPass == $row['password']){
		$UpdateQry = "update patient_data set password = '".$newPass."' where id = '".$patientId."'";
		$req_sql_obj = $app->dbh->imw_query($UpdateQry);
		$return['PassStatus'] = true;
	}
	
	return json_encode($return);
	
});

// end Change Password

// To Handle Register New User //
$router->respond(array('POST','GET'), '/registerUser', function($request, $response, $service, $app) use($router){
	
	$finalResult['RegStatus'] = false;
	
	$user_fname = trim(addslashes($request->__get("fname")));
	$user_lname = trim(addslashes($request->__get("lname")));
	$user_email = trim(addslashes($request->__get("email")));
	$user_dob = $request->__get("dob");
	$user_gender = trim(addslashes($request->__get("gender")));
	$user_zip = trim(addslashes($request->__get("zip")));
	
	$user_homephone = trim(addslashes($request->__get("homephone")));
	$user_workphone = trim(addslashes($request->__get("workphone")));
	$user_workphoneExt = trim(addslashes($request->__get("workphoneExt")));
	$user_cellphone = trim(addslashes($request->__get("cellphone")));
	$user_city = trim(addslashes($request->__get("city")));
	$user_address = trim(addslashes($request->__get("address")));
	$user_state = trim(addslashes($request->__get("state")));
		
	if($user_fname!="" && $user_lname!="" && $user_email!="" && $user_dob !=""&& isset($user_gender)){
			
		$sql  = "SELECT COUNT(*) AS `count_0` FROM `iportal_register_patient` WHERE LOWER(`email`)='".strtolower($user_email)."' AND `approved` IN('0', '1')";
		$sql.= " UNION ALL ";	
		$sql.= "SELECT COUNT(*) AS `count_1` FROM `iportal_register_patient` WHERE LOWER(`fname`)='".strtolower($user_fname)."' AND LOWER(`lname`)='".strtolower($user_lname)."' AND `dob`='".$user_dob."' AND`sex`='".$user_gender."' AND `postal_code`='".$user_zip."' AND `approved` IN('0', '1')";
		$sql.= " UNION ALL ";	
		$sql.= "SELECT COUNT(*) AS `count_2` FROM `patient_data` WHERE LOWER(`fname`)='".strtolower($user_fname)."' AND LOWER(`lname`)='".strtolower($user_lname)."' AND `DOB`='".$user_dob."' AND`sex`='".$user_gender."' AND `postal_code`='".$user_zip."'";
		$resultSet = $app->dbh->imw_query($sql); 
		$result = $app->dbh->imw_fetch_assoc($resultSet);
		
		if($result['count_0']==0 && $result['count_1']==0 && $result['count_2']==0){
			$auth_token = md5($user_email.time());
			$sqlIportal = "INSERT INTO `iportal_register_patient`(`fname`, `lname`, `email`, `dob`, `sex`, `address`, `city`, `state`, `postal_code`, `phone_home`, `phone_cell`, `phone_biz`, `phone_biz_ext`, `auth_token`) VALUES('".$user_fname."', '".$user_lname."', '".$user_email."', '".$user_dob."', '".$user_gender."', '".$user_address."', '".$user_city."', '".$user_state."', '".$user_zip."', '".$user_homephone."', '".$user_cellphone."', '".$user_workphone."', '".$user_workphoneExt."', '".$auth_token."')";
			$finalResult['RegStatus'] = $app->dbh->imw_query($sqlIportal);
		}
	}
	$response->append(json_encode($finalResult));
	$router->skipNext();
	
});



/*Handle Token Generation*/
	$router->respond(array('POST','GET'), '/accessToken', function($request, $response, $service, $app) use($router){
	$hash = $app->passwordhash;
	$data = array();
	$service->validateParam('userId', 'Please provide valid username')->notNull();
	$service->validateParam('password', 'Please provide password')->notNull();
	
	
	$userId		= $app->dbh->imw_escape_string( $request->__get('userId') );
	$password	= $app->dbh->imw_escape_string( $request->__get('password') );
	$type = $request->__get('type');
	//$password	= sha1($password);
	
	// login for patient
		if($type == 'patient'){
			
			$sql = 'SELECT 
						pt_data.`id` as Pt_Id,
						pt_data.`fname` as Pt_Fname,
						pt_data.`mname` as Pt_Mname,
						pt_data.`lname` as Pt_Lname,
						pt_data.`street` as Pt_Street,
						pt_data.`street2` as Pt_Street2,
						pt_data.`phone_home` as Pt_Phone,
						pt_data.`email` as Pt_Email,
						pt_data.`p_imagename` as Pt_Image_Path,
						pt_data.`default_facility` as Pt_facility,
						IFNULL(users.`id`,"") as Phy_id,
						IFNULL(CONCAT(users.`lname`,", ",users.`fname`),"") as Phy_Name				
					FROM
						patient_data AS pt_data
						LEFT JOIN users on(pt_data.`providerID` = users.`id`)
					WHERE
						pt_data.`username`= "'.$userId.'"
					AND pt_data.`password`= "'.$password.'"';
			$resp = $app->dbh->imw_query($sql);
		
		
		}
	// login for Responsible Partty
		else{
				if($type == 'resp'){
				$sql = "select id,fname,lname,resp_password,patient_id from resp_party where resp_username = '".$userId."' AND resp_password = '".$password."'";
				$resp_party = $app->dbh->imw_query($sql); 
				
				}
			}
		
		$apiUserId = 0;
		
	// for patient
		if( $resp && $app->dbh->imw_num_rows($resp) === 1 )
		{
			
			$data = $app->dbh->imw_fetch_assoc($resp);
			$data['type'] = 'patient';
			$data['resp_id'] = '';
			$data['resp_fname'] = '';
			$data['resp_mname'] = '';
			
			$apiUserId = $data['Pt_Id'];
			$sql_Ins = "insert into patient_loginhistory set patient_id='".$apiUserId."',logindatetime=now()";
			$ins_resp = $app->dbh->imw_query($sql_Ins);
		}
	
	elseif($resp_party && $app->dbh->imw_num_rows($resp_party) === 1 ){
	
		$resp_data = $app->dbh->imw_fetch_assoc($resp_party);
		$patientId = $resp_data['patient_id'];
		$sql = 'SELECT 
					pt_data.`id` as Pt_Id,
					pt_data.`fname` as Pt_Fname,
					pt_data.`mname` as Pt_Mname,
					pt_data.`lname` as Pt_Lname,
					pt_data.`street` as Pt_Street,
					pt_data.`street2` as Pt_Street2,
					pt_data.`phone_home` as Pt_Phone,
					pt_data.`email` as Pt_Email,
					pt_data.`p_imagename` as Pt_Image_Path,
					pt_data.`default_facility` as Pt_facility,
					IFNULL(users.`id`,"") as Phy_id,
					IFNULL(CONCAT(users.`lname`,", ",users.`fname`),"") as Phy_Name				
				FROM
					patient_data AS pt_data
					LEFT JOIN users on(pt_data.`providerID` = users.`id`)
				WHERE
					pt_data.`id`= "'.$patientId.'"';
				
		$resp = $app->dbh->imw_query($sql);
		$data = $app->dbh->imw_fetch_assoc($resp);
		
		
		$data['resp_id'] = $resp_data['id'];
		$data['resp_fname'] = $resp_data['fname'];
		$data['resp_mname'] = $resp_data['lname'];
		$data['type'] = 'resp';
		
		
		$apiUserId = $resp_data['patient_id'];
		$respUserId = $resp_data['id'];
		
		$sql_Ins = "insert into patient_loginhistory set patient_id='".$apiUserId."',logindatetime=now(),pt_rp_id = '".$respUserId."'";
		$ins_resp = $app->dbh->imw_query($sql_Ins);
	}
	
	else
	{
		$response->code(403);
		$response->body('Invalid Credentials.');
		$router->skipNext();
	}
	
	$sql = 'SELECT 
					DATE_FORMAT(pt_lg.`logindatetime`, "%m-%d-%Y %H:%i:%s") as Pt_Login					
				FROM
					`patient_data` pt_data LEFT JOIN patient_loginhistory pt_lg on(pt_data.`id` = pt_lg.`patient_id`)
				WHERE
					pt_data.`id`= "'.$data["Pt_Id"].'"
				ORDER BY pt_lg.`logindatetime` desc LIMIT 1';
				
	$query = $app->dbh->imw_query($sql);
	$loginTime = $app->dbh->imw_fetch_assoc($query);
	
	$data['Pt_Login'] = $loginTime['Pt_Login'];
	
	
	/*Generate Token and return to the User*/
	$token = $request->ip().$request->__get('userId').time();
	$token = hash('sha256', $token);
	
	/*Log Token in DB*/
	
	$timeStamp = time();
	$createDateTime = date('Y-m-d H:i:s', $timeStamp);
	$expireDateTime = date('Y-m-d H:i:s', strtotime('+24 hours', $timeStamp) );

	$sql = 'INSERT INTO `fmh_iportal_api_token_log`
			SET
				`token`=\''.$token.'\',
				`user_id`='.$apiUserId.',
				`create_date_time`=\''.$createDateTime.'\',
				`expire_date_time`=\''.$expireDateTime.'\'';
	$resp = $app->dbh->imw_query($sql);
	
	if( $resp )
	{
		$tokenId = $app->dbh->imw_insert_id();
		$request->__set('TokenId', $tokenId);
	}
	//return json_encode($data);
	
	// Set patient Session
	
	if(isset($_SESSION)){
		
		$_SESSION['patient'] = $data['Pt_Id'];
		$session_app = $_REQUEST['SSID_DEV'];
		$session_app = md5($session_app.'uni@#imd=lab_ios_android3');
		$_SESSION['app_session'] = $session_app;
		$_SESSION['check_session'] =  session_id();
	}
	
	else{
			$data = array();
			
			$data['session'] = false;
			
			$data['Error'] = "Session Not Valid";
			
			echo json_encode($data);
			
			die();	
	}
	
	if($_SESSION['check_session'] !=  session_id()){
		
		$data = array();
			
		$data['session'] = false;
			
		$data['Error'] = "Session Not Valid";
			
		echo json_encode($data);
			
		die();	
	}
	
	/* Check patient id in session variable*/
	
	$returnArr = array();
	$returnArr = $data;
	
	$returnArr['Token'] = $token;
	//PT Image
	$image = file_get_contents(data_path().$data['Pt_Image_Path']);
	$returnArr['Pt_Image'] = base64_encode($image);
	$returnArr['cst'] = 'uni@#imd=lab_ios_android3';
	
	$response->append(json_encode($returnArr));
	$router->skipNext();
});

/*Verify Token*/
// HERE Array used for creating a api's without token generated.
$router->respond('*', function($request, $response, $service, $app) use($router) {
	$IgnoreArray = array('/getPractiseList','/patientFirstAuth','/registerUser','/listSecQues','/forgotPass','/verifySecQues','/forgotRespPass','/verifyRespSecQues','/listOfImages','/setUserInfo','/setRespInfo','/changeUserInfo','/changeRespInfo');
	$apiCall = $request->params();

	if(in_array($apiCall[0],$IgnoreArray))$router->skipNext();
	
	$service->validateParam('accessToken', 'No / Invalid access token provided.')->isAlnum()->notNull()->isLen(64, 64);
	
	$accessToken = $app->dbh->imw_escape_string( $request->__get('accessToken') );
	
	$patientId = $app->dbh->imw_escape_string( $request->__get('patientId') );
	
	$sql = "SELECT `id`, `expire_date_time`
			FROM
				`fmh_iportal_api_token_log`
			WHERE
				`token`='".$accessToken."' AND user_id = '".$patientId."'";
	$resp = $app->dbh->imw_query($sql);
	$tokenId = 0;
	
	if( $resp && $app->dbh->imw_num_rows($resp) === 1 )
	{
		$tokenData = $app->dbh->imw_fetch_assoc($resp);
		
		$tokenExpireDateTime = strtotime($tokenData['expire_date_time']);
		
		if( $tokenExpireDateTime < time() )
		{
			$response->append("Invalid Token.");
			$router->abort(401);
		}
		$tokenId = (int)$tokenData['id'];
		
	}
	else
	{
		$response->append('Token does not exists.');
		$router->abort(401);
	}
	
	$request->__set('TokenId', $tokenId);
});

/*Patient Search*/
$router->respond(array('POST','GET'), '/searchPatient', function($request, $response, $service, $app) use($router, $converToString){
		
	$service->validateParam('lname', 'Please provide valid lastname.')->isAlnum()->notNull();
	
	$firstName	= $app->dbh->imw_escape_string( $request->__get('fname') );
	$lastName	= $app->dbh->imw_escape_string( $request->__get('lname') );
	$dob		= $app->dbh->imw_escape_string( $request->__get('dob') );
	$maxRecords	= (int)$app->dbh->imw_escape_string( $request->__get('maxRecords' ) );
	
	$where = ' WHERE
				`lname` LIKE \'%'.$lastName.'%\'';
	
	if( $firstName !== '' )
		$where .= ' AND `fname` LIKE \'%'.$firstName.'%\'';
	if( $dob !== '' )
		$where .= ' AND `DOB` = \''.$dob.'\'';
	
	$limit = ' LIMIT '.( ($maxRecords > 0) ? $maxRecords : 100 );
	
	$sql = 'SELECT
				`id` AS \'ID\',
				`fname` AS \'FirstName\',
				`lname` AS \'LastName\',
				`mname` AS \'MiddleName\',
				`suffix` AS \'Suffix\',
				`DOB` AS \'DateOfBirth\',
				`ss` AS \'SSN\',
				`sex` AS \'Sex\',
				`phone_home` AS \'HomePhone\',
				`phone_biz` AS \'BusinessPhone\',
				`phone_cell` AS \'CellPhone\',
				`email` AS \'Email\'
			FROM
				`patient_data`
			'.$where.$limit;
	$resp = $app->dbh->imw_query($sql);
	
	$returnData = array();
	
	if( $resp && $app->dbh->imw_num_rows($resp) > 0 )
	{
		while( $row = $app->dbh->imw_fetch_assoc($resp) )
		{
			$row['SSN'] = filter_var($row['SSN'], FILTER_SANITIZE_NUMBER_INT);
			$row['DateOfBirth'] = filter_var($row['DateOfBirth'], FILTER_SANITIZE_NUMBER_INT);
			$row['HomePhone'] = filter_var($row['HomePhone'], FILTER_SANITIZE_NUMBER_INT);
			$row['BusinessPhone'] = filter_var($row['BusinessPhone'], FILTER_SANITIZE_NUMBER_INT);
			$row['CellPhone'] = filter_var($row['CellPhone'], FILTER_SANITIZE_NUMBER_INT);
			
			//$row['DateOfBirth'] = '0000-00-00';
			
			if( (double) preg_replace('/[^0-9]/', '', $row['DateOfBirth']) <= 0 ){
				$row['DateOfBirth'] = null;
			}
			
			/* Patient multi address */
			$pt_add_qry = "
				SELECT 
					street as Street1,
					street2 as Street2,
					city as City,
					state as State,
					postal_code as Zip
				FROM patient_multi_address 
				WHERE patient_id = ".$row['ID']." AND del_status = 0";
				
				$respAddres = $app->dbh->imw_query($pt_add_qry);
				
				if($respAddres && $app->dbh->imw_num_rows($respAddres) > 0){
					while($rowAddress = $app->dbh->imw_fetch_assoc($respAddres)){
						
						$row['Address'][] = $rowAddress;
					}
					
					$app->dbh->imw_free_result($respAddres);
					unset($respAddres);
				}
			
			array_push($returnData, $row);
		}
	}
	else
	{
		$returnData = array('status' => 'No match found.');
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Registration Status */
$router->respond(array('GET'), '/getRegistrationStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$patient_id = '';
	
	// Validating Values //
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patient_id	= $request->__get('patientId');
	if(empty($patient_id) == true)
	{
		$response->append('Invalid Patient ID.');
		$router->abort(400);
	}
	$qry = "SELECT 
				id as patientId,
				fname as FirstName,
				lname as LastName,
				dob as DOB,				
				fmh_pt_status as Status				
			FROM patient_data
			WHERE 
				id = ".$patient_id."";
	$res = $app->dbh->imw_query($qry);			
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No patients found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Update Pt. registration status */
$router->post('/updateRegistrationStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$status = $patientId = '';
	
	/* Validating Values */
	$service->validateParam('status', 'Please provide valid status.')->notNull()->isAlpha();
	$status	= filter_var($request->__get('status'), FILTER_SANITIZE_STRING);
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$sql_qry = "SELECT 
					fmh_pt_status,id 
				FROM patient_data
				WHERE
					id = ".$patientId."";
	
	$res = $app->dbh->imw_query($sql_qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		$row = $app->dbh->imw_fetch_assoc($res);
		$update_qry = 'UPDATE patient_data SET fmh_pt_status = \''.$status.'\' WHERE id = '.$row['id'].'';
		$up_qry = $app->dbh->imw_query($update_qry);
		if($up_qry){
			$returnData = array('status' => 'Status updated');
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No patients found.');
	}		
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});


/*Demographics Data*/
$router->with('/getDemographics', __DIR__ .'/demographicRoutes.php');


/*List modified Patients*/
$router->respond(array('POST','GET'), '/getPatientsModified', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	/* Validating Values */
	$service->validateParam('startDate', 'Please provide valid start date format.')->notNull()->isDate();
	$service->validateParam('endDate', 'Please provide valid end date format.')->notNull()->isDate();
	
	
	/* Retriving values */
	$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
	$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
	
	$sql = $app->dbh->imw_query("SELECT id,fname,lname FROM patient_data WHERE timestamp BETWEEN '$startDate' AND '$endDate' ORDER BY timestamp DESC");
	if($sql && $app->dbh->imw_num_rows($sql) > 0){
		while($row = $app->dbh->imw_fetch_assoc($sql)){
			$tmp_arr = array();
			$tmp_arr['PatientId'] = filter_var($row['id'], FILTER_SANITIZE_NUMBER_INT);
			$tmp_arr['FirstName'] = filter_var($row['fname'], FILTER_SANITIZE_STRING);
			$tmp_arr['LastName'] = filter_var($row['lname'], FILTER_SANITIZE_STRING);
			array_push($returnData, $tmp_arr);
		}
		$app->dbh->imw_free_result($sql);
	}else{
		$returnData = array('status' => 'No match found.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/*Providers List*/
$router->respond(array('POST','GET'), '/getProvidersList', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$qry = $app->dbh->imw_query("SELECT 
				id as ProviderId,
				fname as FirstName,
				mname as MiddleName,
				lname as LastName,
				user_npi as NPI				
			FROM users
			WHERE 
				user_type = 1 
			AND delete_status = 0
			AND superuser = 'no'
			ORDER BY lname ASC");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($qry);
	}else{
		$returnData = array('status' => 'No provider found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Procedures List */
$router->respond(array('POST','GET'), '/getProcedureList', function($request, $response, $service, $app) use($router, $converToString){
	
	$returnData = array();
	
	$qry = $app->dbh->imw_query("
			SELECT 
				proc as ProdecureName,
				acronym as ProdecureAlias			
			FROM slot_procedures
			WHERE 
				active_status = 'yes'
			ORDER BY proc ASC");
	
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			
			$row['ProdecureName'] = trim($row['ProdecureName']);
			if( empty($row['ProdecureName']) == false)
			{
				array_push($returnData, $row);
			}
		}
		$app->dbh->imw_free_result($qry);
	}
	else{
		$returnData = array('status' => 'No Procedure found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});


/* Logout */
$router->respond(array('POST','GET'), '/logout', function($request, $response, $service, $app) use($router){

	session_start();
	
	session_unset($_SESSION['patient']);
	session_unset($_SESSION['app_session']);
	session_unset($_SESSION['check_session']);
	
	session_destroy();
	
	//$_SESSION = array();
	$patientId	= $request->__get('patientId');
	$accessToken = $request->__get("accessToken");
	$returnData = array();
	
	$qryUser = "UPDATE fmh_iportal_api_token_log SET 
												`expire_date_time`= '".date('Y-m-d H:i:s')."' 
													WHERE
													`token`='".$accessToken."' 
													AND user_id = '".$patientId."'";
	$up_qry = $app->dbh->imw_query($qryUser);
	$responseData = array('Success'=>0, 'TokenStatus'=>false, 'session'=>false, 'Error'=>"Invalid Token.");	
	echo json_encode($responseData);
	die;
});

/* Appointment Status */
$router->respond(array('POST','GET'), '/getAppointmentsList', function($request, $response, $service, $app) use($router, $converToString){
	$patientId = $startDate = $endDate = $where = '';
	$valid_start_date = $valid_end_date = false;
	$returnData = array();
	
	/* Appointment status array */
	$appt_status_arr = array(
		0  => 'Restore, Reset, Created',
		1  => 'Reminder Done',
		2 => 'Chart Pulled', 
		3 => 'No Show', 
		4 => 'Arrived',
		5 => 'Arrived Late', 
		6 => 'Left without visit',
		7 => 'Insurance/Financial Issue', 
		8 => 'Billing Done', 
		9 => 'Vitals Done', 
		10 => 'In Exam Room',
		11 => 'Checked Out', 
		12 => 'Coding Done',
		13 => 'Check-in', 
		14 => 'In Waiting Room', 
		15 => 'With Technician', 
		16 => 'With Physician', 
		17 => 'Confirm', 
		18 => 'cancelled',
		21 => 'Patient-Cancel',  
		22 => 'Left-Message',  
		23 => 'Not-Confirm',
		100 => 'Waiting for Surgery(W/Sx)',
		101 => 'Scheduled For Surgery (S/Sx)',
		200 => 'Room Assigned',  
		201 => 'To-Do-Rescheduled',  
		202 => 'Reschedule',  
		203 => 'Deleted',  
		271 => 'First Available',
	);
	
	/* Validating Values */
	$patientId	= $request->__get('patientId');
	$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
	$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
	
	if(empty($patientId) == false){
		
		$service->validateParam('patientId', 'Please provide valid patient id.')->isInt()->isPatient($app);
		
		$patientId	= $request->__get('patientId');
		
		$where .= "sch_apt.sa_patient_id = '".$patientId."'";
		
	}
	
	if(empty($startDate) == false){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		
		if(empty($endDate) == true){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		}
	}
	
	if(empty($endDate) == false){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		
		if(empty($startDate) == true){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
			
		}
	}
	
	if(empty($endDate) == false && empty($startDate) == false){
		
		if(empty($where) == false){
			
			$where .= ' AND ';
			
		}
		
		$where .= "sch_apt.sa_app_start_date between '".$startDate."' and '".$endDate."'"; 
	}
	
	/* Retriving Values */
	$qry = "
		SELECT 
			sch_apt.id as appointmentId,
			sch_apt.sa_app_start_date as appointmentDate,
			TIME(sch_apt.sa_app_time) as appointmentTime,
			sch_apt.sa_patient_app_status_id as appointmentStatus,
			slt_proc.proc as procedureName,
			sch_apt.sa_doctor_id as physicianId,
			usrs.fname as physicianFirstName,
			usrs.lname as physicianLastName,
			fac.name as locationName,
			sch_apt.sa_patient_id as patientId,
			pt_data.fname as patientFirstName,
			pt_data.mname as patientMiddleName,
			pt_data.lname as patientLastName,
			DATE(pt_data.timestamp) as lastModifiedDate
		FROM
			schedule_appointments sch_apt
			LEFT JOIN slot_procedures slt_proc ON (sch_apt.procedureid = slt_proc.id)
			LEFT JOIN users usrs ON (sch_apt.sa_doctor_id = usrs.id)
			LEFT JOIN patient_data pt_data ON (sch_apt.sa_patient_id = pt_data.id)
			LEFT JOIN facility fac ON (sch_apt.sa_facility_id = fac.id)
		WHERE 
			sch_apt.sa_patient_app_status_id NOT IN (203,201,18,19,20) 
		AND 
			IF( sch_apt.sa_patient_app_status_id = 271, sch_apt.sa_patient_app_show =0, sch_apt.sa_patient_app_show <>2 ) 
		AND	".$where."
		ORDER BY sch_apt.sa_app_start_date DESC
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		
		while($row = $app->dbh->imw_fetch_assoc($res)){
			
			$row['appointmentStatus'] = $appt_status_arr[$row['appointmentStatus']];
			
			array_push($returnData, $row);
			
		}
		$app->dbh->imw_free_result($res);
	}else{
		
		$returnData = array('status' => 'No Appointments found.');
		
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Get Patient Message  */
$router->respond(array('POST','GET'), '/getPatientMessages', function($request, $response, $service, $app) use($router, $converToString){
	$where = '';$returnData = array();
	
	/* Get & Validate Values */
	$service->validateParam('senderType', 'Please provide valid sender type.')->notNull()->isInt();
	$sender_type = (int)$request->__get('senderType');
	
	$service->validateParam('patientId', 'Please provide valid patient id.')->notNull()->isInt()->isPatient($app);
	$patientId = (int)$request->__get('patientId');
	
	
	if( $request->__isset('physicianId') )
		$service->validateParam('physicianId', 'Please provide valid physician id.')->isInt()->notZero();
	
	$physicianId = (int)$request->__get('physicianId');
	
	
	$date = $request->__get('date');
	if(empty($date) == false){
		$service->validateParam('date', 'Please provide valid date.')->isDate();
		$where .= " AND DATE(msg_date_time) = '".$date."' ";
	}
	
	/* Retriving Values */
	switch($sender_type){
		case 1:		// Receiver --> Patient && Sender --> Physician/User
			$where .= "AND receiver_id = ".$patientId." AND sender_id != 0 ";
			if(empty($physicianId) == false){
				$where .= "AND sender_id = ".$physicianId."  ";
			}	
		break;
		
		case 2:		// Receiver --> Physician/User  && Sender --> Patient
			$where .= "AND sender_id = ".$patientId." ";
			if(empty($physicianId) == false){
				$where .= "AND receiver_id = ".$physicianId." ";
			}
		break;
	}
	
	$qry = "
		SELECT 
			pt_msg_id as messageId,
			sender_id as senderId,
			receiver_id as receiverId,
			communication_type as senderType,
			DATE(msg_date_time) as date,
			msg_subject as subject,
			msg_data as messageData
		FROM
			patient_messages
		WHERE 
			del_status_by_pt = 0 AND 
			communication_type = ".$sender_type." AND
			is_done = 0 ".$where."
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No Messages found.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Send Patient Messages */
$router->post('/sendPatientMessage', function($request, $response, $service, $app) use($router, $converToString){
	$db_sender_id = $db_receiver_id = '';$returnData = array();
	
	/* Get & Validate Values */
	$service->validateParam('patientId', 'Please provide valid patient id.')->notNull()->isInt()->isPatient($app);
	$patientId = (int)$request->__get('patientId');
	
	$service->validateParam('senderType', 'Please provide valid sender type.')->notNull()->isInt();
	$sender_type = (int)$request->__get('senderType');
	
	$service->validateParam('physicianId', 'Please provide valid physician id.')->notNull()->isInt()->notZero();
	$physicianId = (int)$request->__get('physicianId');
	
	$service->validateParam('subject', 'Please provide a subject for message')->notNull();
	$msgSubject = $request->__get('subject');
	
	$service->validateParam('data', 'Please provide content for message')->notNull();
	$msgData = $request->__get('data');
	
	/* Setting Values */
	switch($sender_type){
		case 1:		// Receiver --> Patient && Sender --> Physician/User
			$db_sender_id = $physicianId;
			$db_receiver_id = $patientId;	
		break;
		
		case 2:		// Receiver --> Physician/User  && Sender --> Patient
			$db_sender_id = $patientId;
			$db_receiver_id = $physicianId;
		break;
	}
	
	$qry = "
		INSERT INTO patient_messages SET
			`sender_id` = ".$db_sender_id.",
			`receiver_id` = ".$db_receiver_id.",
			`communication_type` = ".$sender_type.",
			`msg_subject` = '".$msgSubject."',
			`msg_data` = '".$msgData."',
			`delivery_date` = '".date('Y-m-d')."'
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_affected_rows() > 0){
		$insert_id = $app->dbh->imw_insert_id();
		$returnData = array('msgId' => $insert_id);
	}else{
		$returnData = array('status' => 'Unable to send message.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Get Visit DOS */
$router->respond(array('POST','GET'), '/getVisitDOS', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$returnData['dates'] = array();
	$patientId = $limit = '';
	
	/* Validating Values */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	/* Not implemented yet
	if( $request->__isset('maxRecords') ){
		$service->validateParam('maxRecords', 'Please provide valid limit')->notNull()->isInt();
		$limit = filter_var($request->__get('maxRecords'), FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(empty($limit) == false){
		$limit = ' LIMIT '.$limit;
	} */
	
	$sql_qry = "SELECT 
					date_of_service as DOS	
				FROM chart_master_table
				WHERE
					patient_id = ".$patientId." ORDER BY date_of_service DESC".$limit;			
	$res = $app->dbh->imw_query($sql_qry);
	//if($res && $app->dbh->imw_num_rows($res) > 0){
	$i=0;
		while($row = $app->dbh->imw_fetch_assoc($res)){
			//array_push($returnData,$row['DOS']);
			$returnData['dates'][$i]['date_of_service'] = $row['DOS'];
			$returnData['dates'][$i]['show_date'] = date('m-d-Y',strtotime($row['DOS']));
			$i++;
		}
		//$app->dbh->imw_free_result($res);
	//}else{
		//$returnData = array('status' => 'No DOS found.');
	//}		
	//array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});


/* Get Patient Information */
$router->respond(array('POST','GET'), '/getPatientInfo', function($request, $response, $service, $app) use($router, $converToString){
	
	$returnData = array();
	
	/* Validating Patient Id */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$sql = 'SELECT 
					pt_data.`id` as Pt_Id,
					pt_data.`fname` as Pt_Fname,
					pt_data.`mname` as Pt_Mname,
					pt_data.`lname` as Pt_Lname,
					pt_data.`street` as Pt_Street,
					pt_data.`street2` as Pt_Street2,
					pt_data.`phone_home` as Pt_Phone,
					pt_data.`email` as Pt_Email,
					pt_data.`sex` as Gender,
					DATE_FORMAT(pt_data.`DOB`, "%m-%d-%Y") as DOB,
					pt_data.`p_imagename` as Pt_Image_Path,
					pt_data.`default_facility` as Pt_facility,
					DATE_FORMAT(pt_lg.`logindatetime`, "%m-%d-%Y %H:%i:%s") as Pt_Login,
					users.`id` as Phy_id,
					CONCAT(users.`lname`,", ",users.`fname`) as Phy_Name				
				FROM
					`patient_data` pt_data LEFT JOIN patient_loginhistory pt_lg on(pt_data.`id` = pt_lg.`patient_id`)
					LEFT JOIN users on(pt_data.`providerID` = users.`id`)
				WHERE
					pt_data.`id`= "'.$patientId.'"
				ORDER BY pt_lg.`logindatetime` desc LIMIT 1';
				
	$resp = $app->dbh->imw_query($sql);
	$returnData = $app->dbh->imw_fetch_assoc($resp);
	
	$image_path = data_path().$returnData['Pt_Image_Path'];
	
	$image = base64_encode(file_get_contents($image_path));
	
	if(file_exists($image_path)){
	
		$returnData['Pt_Image'] = $image;
		
	} else {
	
		$returnData['Pt_Image'] = '';
	}
	
	return json_encode($returnData);
});


/* Get Provider/Physician Information */

$router->respond(array('POST','GET'), '/getProviderInfo', function($request, $response, $service, $app) use($router, $converToString){
	
	$returnData = array();
	
	/* Validating Patient Id */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$sql = 'SELECT 	
					users.`id` as id,
					users.`fname` as Fname,
					users.`mname` as Mname,
					users.`lname` as Lname,					
					IFNULL(users.`info`, "") AS About
								
				FROM
					`patient_data` pt_data LEFT JOIN patient_loginhistory pt_lg on(pt_data.`id` = pt_lg.`patient_id`)
					LEFT JOIN users on(pt_data.`providerID` = users.`id`)
				WHERE
					pt_data.`id`= "'.$patientId.'" AND users.locked = 0
				ORDER BY pt_lg.`logindatetime` desc LIMIT 1';
				
	$resp = $app->dbh->imw_query($sql);
	$returnData = $app->dbh->imw_fetch_assoc($resp);
	
	$returnData['Gender'] = '';
	$user_id = $returnData['id'];
	$image_path = 	data_path().'/UserId_'.$user_id.'/profile_img/Provider_'.$user_id.'.jpg';
	$image = base64_encode(file_get_contents(data_path().'/UserId_'.$user_id.'/profile_img/Provider_'.$user_id.'.jpg'));
	
	if(file_exists($image_path)){
		$returnData['Image'] = $image;
		//$user_img_photo = data_path().'/UserId_'.$user_id.'/profile_img/Provider_'.$user_id.'.jpg';
		
	} else {
	
		$returnData['Image'] = '';
	}
	
	return json_encode($returnData);
});

/* Care Team Members */
$router->respond(array('POST','GET'), '/getCareTeamMembers', function($request, $response, $service, $app) use($router, $converToString){
	$where = $startDate = $endDate = $dos = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		/* if($request->__isset('endDate') == false){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		} */
	}
	
	if($request->__isset('endDate') && trim($request->__get('endDate')) !== '' ){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		if($request->__isset('startDate') == false){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(date_of_service) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}	
	
	
	$dbh = $app->dbh;
	/* Get Providers IDS for the Chart note */
	$providerIds = array();
	$providersGroups = array();

	$sql = "SELECT `provIds` FROM `chart_master_table` WHERE `patient_id`='".$patientId."' AND `provIds`!='' ".$where;
	$resp = $dbh->imw_query($sql);
	if( $resp && $dbh->imw_num_rows($resp) > 0 )
	{
		while( $row = $dbh->imw_fetch_assoc($resp) )
		{
			$providerIds = array_merge($providerIds,  explode(',', $row['provIds']));
		}
		
		//$providerIds = array_map(array($this,'convertToInt'), $providerIds);
		
		$providerIds = array_map('trim', $providerIds);
		
		if(count($providerIds) > 0){
			$providerIds = array_filter($providerIds);	
			$providerIds = array_unique($providerIds);
		}
		
		if( is_array($providerIds) && count($providerIds) > 0 )
		{
			$sql = 'SELECT `user_group_id`, `id` FROM `users` WHERE `id` IN('.implode(',', $providerIds).')';
			$resp = $dbh->imw_query($sql);
			if( $resp && $dbh->imw_num_rows($resp)>0 )
			{
				while( $row = $dbh->imw_fetch_assoc($resp) )
				{
					$providersGroups[$row['id']] = (int)$row['user_group_id'];
				}
			}
		}
	}
	
	$sql_patient = "SELECT * FROM patient_data WHERE id = '".$patientId."' LIMIT 0,1";
	$result_patient = $dbh->imw_query($sql_patient);
	$row_patient 	= $dbh->imw_fetch_assoc($result_patient);
	$providerID = $row_patient['providerID'];
	
	$tempProviderID = false;
	$tempTechnicianID = false;
	
	if( count($providerIds) > 0)
	{
		foreach($providersGroups as $groupKey=>$groupVal)
		{
			if( $groupVal === 2 && $tempProviderID === false )
			{
				$tempProviderID = (int)$groupKey;
			}
			elseif( $groupVal === 5 && $tempTechnicianID === false )
			{
				$tempTechnicianID = (int)$groupKey;
			}
		}
		
		if( $tempProviderID !== false && $tempProviderID > 0)
		{
			$providerID = $tempProviderID;
		}
	}
	//Primiary Physician
	$qry_provider = "SELECT 
						id as ID,
						fname as FirstName,
						mname as MiddleName,
						lname as LastName,
						'Primary Physician' AS 'Role',
						default_facility as DefaultFacility
					FROM users WHERE id = '".$providerID."'";  // PRIMARY PHYSICIAN
	$res_provider = $dbh->imw_query($qry_provider);
	
	$tmp_arr = $return_arr = array();
	if($dbh->imw_num_rows($res_provider) > 0 && $res_provider){
		$row_provider = $dbh->imw_fetch_assoc($res_provider);
		$default_facility = (empty($row_provider['DefaultFacility']) == false) ? $row_provider['DefaultFacility'] : 1;
		
		if($default_facility > 0){
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where id = '".$default_facility."'";
		}
		else{
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'";
		}
		
		
		$res_facility = $dbh->imw_query($qry_facility);
		if($res_facility && $dbh->imw_num_rows($res_facility) > 0){
			$row_facility = $dbh->imw_fetch_assoc($res_facility);
		}else{
			$sql = $dbh->imw_query("select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'");
			if($sql && $dbh->imw_num_rows($sql) > 0){
				$row_facility = $dbh->imw_fetch_assoc($sql);
			}
		}
		
		$tmp_arr['ID'] = $row_provider['ID'];
		$tmp_arr['FirstName'] = $row_provider['FirstName'];
		$tmp_arr['MiddleName'] = $row_provider['MiddleName'];
		$tmp_arr['LastName'] = $row_provider['LastName'];
		$tmp_arr['Role'] = $row_provider['Role'];
		if(count($row_facility) > 0){
			$tmp_arr['Address'] = $row_facility;	
		}
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	}
	
	$qry_reff = "SELECT 
					physician_Reffer_id as ID,
					FirstName,
					MiddleName,
					LastName,
					NPI as NPI,
					'Primary Care Physician' AS 'Role',
					Address1 as Add_Address1,
					Address2 as Add_Address2,
					ZipCode as Add_ZipCode,
					City as Add_City,
					State as Add_State,
					physician_phone as Add_Phone,
					physician_fax as Add_Fax,
					physician_email as Add_Email,
					direct_email as Add_DirectEmail 
				FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_phy_id']."'"; // PCP PHYSICIAN
	$res_reff = $dbh->imw_query($qry_reff);
	$pcp_physician_arr = array();
	if($dbh->imw_num_rows($res_reff) > 0){
		while($row_reff = $dbh->imw_fetch_assoc($res_reff)){
			foreach($row_reff as $key => $val){
				if(strpos($key, 'Add_') !== false){
					$key = str_replace('Add_','',$key);
					$tmp_arr['Address'][$key] = $val;
				}else{
					$tmp_arr[$key] = $val;
				}
			}
		}
		array_filter($tmp_arr);
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	}
	
	//Nurse
	$qry_provider = "SELECT 
						id as ID,
						fname as FirstName,
						mname as MiddleName,
						lname as LastName,
						default_facility as DefaultFacility
					FROM users WHERE id = '".(($tempTechnicianID!==false)?$tempTechnicianID:$row_patient['assigned_nurse'])."'";  // PRIMARY PHYSICIAN
	$res_provider = $dbh->imw_query($qry_provider);
	
	if($dbh->imw_num_rows($res_provider) > 0 && $res_provider){
		$row_provider = $dbh->imw_fetch_assoc($res_provider);
		$default_facility = (empty($row_provider['DefaultFacility']) == false) ? $row_provider['DefaultFacility'] : 1;
		
		if($default_facility > 0){
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where id = '".$default_facility."'";
		}
		else{
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'";
		}
		
		
		$res_facility = $dbh->imw_query($qry_facility);
		if($res_facility && $dbh->imw_num_rows($res_facility) > 0){
			$row_facility = $dbh->imw_fetch_assoc($res_facility);
		}else{
			$sql = $dbh->imw_query("select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'");
			if($sql && $dbh->imw_num_rows($sql) > 0){
				$row_facility = $dbh->imw_fetch_assoc($sql);
			}
		}
		
		$tmp_arr['ID'] = $row_provider['ID'];
		$tmp_arr['FirstName'] = $row_provider['FirstName'];
		$tmp_arr['MiddleName'] = $row_provider['MiddleName'];
		$tmp_arr['LastName'] = $row_provider['LastName'];
		$tmp_arr['Role'] = 'Nurse';
		if(count($row_facility) > 0){
			$tmp_arr['Address'] = $row_facility;	
		}
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	}

	array_walk_recursive($return_arr, $converToString);
	return json_encode($return_arr);
});

// verify token for medical hx
$router->respond(array('POST','GET'), '/medicalHx', function($request, $response, $service, $app) use($router){
	
	$patientId = $request->__get("patientId");
	$accessToken = $request->__get("accessToken");
	$_SESSION['authId'] = 1;
	$link['link'] = $GLOBALS['php_server']."/IMWAPI_APP/medical_hx.php?patientId=".$patientId."&accessToken=".$accessToken;	
	
	return json_encode($link);
	
});

// verify token for medical hx


/*Clinical Data*/
$router->with('/getClinicalData', __DIR__ .'/clinicalRoutes.php');

/* Educational material , Statement , Patient Message */
$router->with('/getEducation', __DIR__ .'/educationRoutes.php');

/* Print Prescription PDF */
$router->with('/prescriptionPrint', __DIR__ .'/prescriptionPrintRoutes.php');

/* Generate CCDA for the requested patient */
$router->with('/getCCDA', __DIR__ .'/ccdaGenerate.php');

/* Book appointment for the requested patient */
$router->with('/patientAppointment', __DIR__ .'/manageAppointment.php');

/* To Print Consent Packages */
$router->with('/printConsent', __DIR__ .'/consentRoutes.php');

/* get direct messages for the requested patient */
$router->with('/getDirect', __DIR__ .'/directMessage.php');

$response = $router->dispatch();

?>