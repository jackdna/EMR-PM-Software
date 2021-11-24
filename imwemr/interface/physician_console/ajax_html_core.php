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

switch($task){
    case 'expand_notifications':
        $returnData = array();
        //If call is for single section
        $reqSection = (isset($_REQUEST['reqSection']) && empty($_REQUEST['reqSection']) == false) ? $_REQUEST['reqSection'] : '';
        
        //Default Tabs array for Bubble
        $bubbleArray = $msgConsoleObj->bubbleTabs;
        if(empty($reqSection) == false && isset($bubbleArray[$reqSection])){
            $tmpArr[$reqSection] = $bubbleArray[$reqSection];
            $bubbleArray = $tmpArr;
        }    
        
        if($bubbleArray && is_array($bubbleArray) && count($bubbleArray) > 0){
            foreach($bubbleArray as $taskFunc => $taskVal){
                if(method_exists($msgConsoleObj, $taskFunc) && $taskVal['enable'] == true){
                    $result = array();
                    $result =  call_user_func_array(array($msgConsoleObj, $taskFunc), $taskVal['params']);
                    if($taskFunc == 'get_tests_tasks'){
						//Fetching Results
						$authId = isset($_SESSION['authId']) ? $_SESSION['authId'] : '';
						$chkQry = imw_query('SELECT 
							usr.fname,
							usr.mname,
							usr.lname,
							pd.fname as pt_fname,
							pd.mname as pt_mname,
							pd.lname as pt_lname,
							pay.commentId,
							pay.encComments,
							pay.task_done, 
							pay.task_assign_by, 
							pay.patient_id,
							DATE(pay.task_assign_date) as task_date,
							DATE_FORMAT(pay.task_assign_date,\''.get_sql_date_format().' %h:%i %p\') as task_date_time
						FROM paymentscomment pay 
						LEFT JOIN users usr ON (usr.id = pay.task_assign_by)
						LEFT JOIN patient_data pd ON (pd.id = pay.patient_id)
						WHERE 
							usr.delete_status = 0 AND
							pay.task_assign_for IN ('.$authId.') AND 
							pay.task_done = 1 AND 
							pay.task_assign = 2 
					  ');
						if($chkQry && imw_num_rows($chkQry) > 0){

							while($rowFetch = imw_fetch_assoc($chkQry)){
								unset($subArr);
								$phyName = core_name_format($rowFetch['lname'], $rowFetch['fname'], $rowFetch['mname']);
								$ptName = core_name_format($rowFetch['pt_lname'], $rowFetch['pt_fname'], $rowFetch['pt_mname']);
								 $subArr['comment']=$rowFetch['encComments'];
								 $subArr['phyname']=$phyName;
								 $subArr['ptname']=$ptName.' - '.$rowFetch['patient_id'];
								 $subArr['task_id']=$rowFetch['commentId'];
								 $subArr['task_done']=$rowFetch['task_done'];
								 $subArr['task_date']=$rowFetch['task_date'];
								 $subArr['task_date_time']=$rowFetch['task_date_time'];
								 $result[]=$subArr;
							}

						}
					}
                    if($result && is_array($result) && count($result) > 0){
                        if($taskFunc == 'get_messages_reminders'){
                            $result = $result[0];
                            if(is_array($result) && count($result) > 0){
                                foreach($result as $key => $obj){
                                    if(isset($obj['message_sender_id']) && empty($obj['message_sender_id']) == false){
                                        $phyDetails = $msgConsoleObj->get_username_by_id(array($obj['message_sender_id']));
                                        if(is_array($phyDetails) && count($phyDetails) > 0) $phyDetails = $phyDetails[$obj['message_sender_id']]['full'];
                                        if(empty($phyDetails) == false && isset($obj['message_sender_name'])) $obj['message_sender_name'] = $phyDetails;
                                        $result[$key] = $obj;
                                    }
                                }
                            }    
                        }
                        //pre($result);
                        $returnData[$taskFunc] = array_filter($result);
                    }
                    else $returnData[$taskFunc] = $result;
                }
            }
        }
        
        echo json_encode($returnData);
    break;
    
    //Performs tasks requested from Bubble
    case 'performTask':
        $returnData = array();
        
        $paramArr = (isset($_REQUEST['params']) && empty($_REQUEST['params']) == false && is_array($_REQUEST['params'])) ? $_REQUEST['params'] : '';
        
        $action = (isset($paramArr['action']) && empty($paramArr['action']) == false) ? $paramArr['action'] : '';
        $section = (isset($paramArr['section']) && empty($paramArr['section']) == false) ? $paramArr['section'] : '';
        
        if(empty($section) == false && empty($action) == false){
            switch($section){
                //Messages Block
                case 'get_messages_reminders':
                    switch($action){
                        //Mark as read
                        case 'markRead':
                            $msgId = (isset($paramArr['msgid']) && empty($paramArr['msgid']) == false) ? $paramArr['msgid'] : '';
                            if(empty($msgId) == false){
                                $chkQry = imw_query("UPDATE user_messages SET message_read_status = 1 WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'");
                                
                                if($chkQry) $returnData['success'] = true;
                            }
                        break;
                        
                        //Mark as done
                        case 'markDone':
                            $msgId = (isset($paramArr['msgid']) && empty($paramArr['msgid']) == false) ? $paramArr['msgid'] : '';
                            $validate = false;
                            
                            if(empty($msgId) == false){
                                $chkQry = imw_query("SELECT msg_id,msg_type,message_sender_id, message_subject,patientId FROM user_messages WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'");
                                
                                if($chkQry && imw_num_rows($chkQry) > 0){
                                    $rowFetch = imw_fetch_assoc($chkQry);
                                    
                                    $sqlQry = "UPDATE user_messages SET message_read_status=IF(message_to = '".$_SESSION['authId']."',1,IF(message_read_status=0,0,1)), message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$_SESSION['authId']."' WHERE msg_id='".$row['msg_id']."'";
                                    
                                    if($rowFetch['msg_id'] == 0 || $rowFetch['msg_type'] == 0){
                                        $sqlQry = "UPDATE user_messages SET message_read_status=1, message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$_SESSION['authId']."' WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'";
                                    }
                                    
                                    $recSql = imw_query($sqlQry);
                                    if($recSql){
                                        $arr_users = $msgConsoleObj->get_username_by_id();
                                        
                                        $sendTo = $rowFetch['message_sender_id'];
                    					$sender = $_SESSION['authId'];
                    					$patientId = $rowFetch['patientId'];
                                        
                                        $senderName = $arr_users[$sender]['full'];
                    					$sendToName = $arr_users[$sendTo]['full'];
                                        
                                        $msgText = 'This task is marked as DONE by '.$senderName;
                                        
                                        $initialMsgId = $msgId;
                                        $originalText = '';
                                       
                                        $getQ2 = imw_query("SELECT message_sender_id,message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate FROM user_messages 
                    							  WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$initialMsgId' LIMIT 0,1");
                    					if($getQ2 && imw_num_rows($getQ2) > 0){
                                            $rs2 = imw_fetch_assoc($getQ2);
                                            $originalText = $rs2['message_text'];
                    						$sentDate = $rs2['msgDate'];
                    						$originalSubject = $rs2['message_subject'];
                    						$originalTextPrefix = '
                                                ----ORIGINAL MESSAGE----
                                                From: '.$sendToName.'
                                                To: '.$senderName.'
                                                Sent: '.$sentDate.'
                                                Subject: '.$originalSubject.'
                                            ';
                                            
                                            $originalText = $originalTextPrefix.$originalText;
                                        }
                                        
                                        $msgText .= $originalText;
                    					$subject = 'DONE: '.$originalSubject;
                                        
                                        $query = "INSERT INTO user_messages SET
                    					  message_subject='".imw_real_escape_string($subject)."', 
                    					  message_to = '$sendTo',
                    					  message_text = '".imw_real_escape_string($msgText)."',
                    					  message_sender_id = '".$sender."',
                    					  message_status=0,
                    					  message_read_status=0,
                    					  user_messages=0,
                    					  message_urgent=0,
                    					  msg_completed_by=0,
                    					  patientId = '$patientId',
                    					  Pt_Communication='0',
                    					  sent_to_groups='$sendToName',
                    					  review_by=0,
                    					  replied_id='$msgId',
                    					  done_alert='1',
                    					  del_status=0,
                    					  message_send_date='".date('Y-m-d H:i:s')."'";
                    					if(substr($rowFetch['message_subject'],0,5)!='DONE:'){
                    						$result = imw_query($query);
                                            if($result){
                                                $validate = true;    
                                            }
                    					}
                                    }
                                    $validate = true;    
                                }
                            }
                            
                            if($validate == true) $returnData['success'] = true;
                        break;
                        
                        //Quick Reply
                        case 'quickReply':
                            $validate = false;
                            $arr_users = $msgConsoleObj->get_username_by_id();
                            
                            $msgTxt = (isset($paramArr['msgtext']) && empty($paramArr['msgtext']) == false) ? $paramArr['msgtext'] : '';
                            
                            if(stristr($paramArr['subject'],'Re:')){$_REQUEST['subject'] = trim(substr($paramArr['subject'],3));}
                            $msgSubject = (isset($paramArr['subject']) && empty($_REQUEST['subject']) == false) ? 'RE: '.$paramArr['subject'] : '';
                            
                            $patientId = (isset($paramArr['patientid']) && empty($paramArr['patientid']) == false) ? $paramArr['patientid'] : '';
                            $msgId = (isset($paramArr['msgid']) && empty($paramArr['msgid']) == false) ? $paramArr['msgid'] : '';
                            
                            $senderId = (isset($paramArr['senderid']) && empty($paramArr['senderid']) == false) ? $paramArr['senderid'] : '';
                            $senderName = $arr_users[$senderId]['full'];
                            
                            //Getting Original Message Details
                            $originalMessage = '';
                            $ckMsgSql = imw_query("SELECT message_sender_id,message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate 
                    		FROM user_messages WHERE message_to = '".$_SESSION['authId']."' AND user_message_id = '$msgId' LIMIT 0,1");
                            if($ckMsgSql && imw_num_rows($ckMsgSql) > 0){
                                $rowFetch = imw_fetch_assoc($ckMsgSql);
                                
                                $originalMsg = $rowFetch['message_text'];
                    			$orignalSentData = $rowFetch['msgDate'];
                    			$originalSubject = $rowFetch['message_subject'];
                    			$originalSenderName = $arr_users[$_SESSION['authId']]['full'];
                                
                                //Prefixing Old Message with new message
                                $prefixTxt = '
                                    ----ORIGINAL MESSAGE----
                                    From: '.$senderName.'
                                    To: '.$originalSenderName.'
                                    Sent: '.$orignalSentData.'
                                    Subject: '.$originalSubject.'
                                ';
                                
                                $originalMessage = $prefixTxt.$originalMsg;
                                $msgSubject = 'RE: '.$originalSubject;      //Replace subject with new message attached subject
                            }
                            if(empty($originalMessage) == false) $msgTxt .= $originalMessage;
                            
                            //Saving New Message to DB
                            $insertSql = imw_query("INSERT INTO user_messages SET
                                message_subject = '".imw_real_escape_string($msgSubject)."', 
                                message_to = '$senderId',
                                message_text = '".imw_real_escape_string($msgTxt)."',
                                message_sender_id = '".$msgConsoleObj->operator_id."',
                                message_status = 0,
                                message_read_status = 0,
                                user_messages = 0,
                                message_urgent = 0,
                                msg_completed_by = 0,
                                patientId = '$patientId',
                                Pt_Communication = 0,
                                sent_to_groups='$senderName',
                                review_by = 0,
                                replied_id='$msgId',
                                del_status = 0,
                                message_send_date='".date('Y-m-d H:i:s')."'");
                            
                            if($insertSql){
                                //Mark this message as read
                                $chkQry = imw_query("UPDATE user_messages SET message_read_status = 1 WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'");
                                if($chkQry) $validate = true;
                            }
                            
                            //if Patient ID is not empty - send reply to PVC also
                            if(empty($patientId) == false && $validate == true){
                                $insertPtRply ="INSERT INTO user_messages SET
                                    message_subject='".imw_real_escape_string($msgSubject)."', 
                                    message_to = '$senderId',
                                    message_text = '".imw_real_escape_string($msgTxt)."',
                                    message_sender_id = '".$msgConsoleObj->operator_id."',
                                    message_status = 0,
                                    message_read_status = 0,
                                    user_messages = 0,
                                    message_urgent = 0,
                                    msg_completed_by = 0,
                                    patientId = '$patientId',
                                    Pt_Communication = 1,
                                    sent_to_groups = '$senderName',
                                    review_by = 0,
                                    replied_id = '$msgId',
                                    del_status = 0,
                                    message_send_date = '".date('Y-m-d H:i:s')."'
                                ";
                                
                                if($insertPtRply) $validate = true;
                            }
                            
                            if($validate == true) $returnData['success'] = true;
                        break;
                        
                        //Returns Patient Data array
                        case 'getptdetails':
                            $returnData = array();
                            $patientId = (isset($paramArr['patient']) && empty($paramArr['patient']) == false) ? $paramArr['patient'] : '';
                            
                            if(empty($patientId) == false){
                                $fieldArr = array('id', 'lname','mname','fname','sex','street','street2', 'city', 'state', 'postal_code', 'zip_ext', 'phone_home', 'phone_biz', 'phone_cell',  'p_imagename', 'email', "date_format(DOB,'".get_sql_date_format()."') as DOB");
                                $patientData = $msgConsoleObj->get_patient_more_info($patientId, implode(', ', $fieldArr));
                                
                                //Appointment Details
                                $pt_appt = $msgConsoleObj->get_pt_appt($patientData['id']);
                                if($pt_appt && is_array($pt_appt) && $patientData) $patientData['ptAppt'] = $pt_appt;
                                
                                if($patientData && empty($patientData) == false && is_array($patientData)){
                                    $patientData['dataPath'] = rtrim(data_path(1), '/');
                                    $returnData = $patientData;
                                }    
                            }
                        break;
                    }
                break;
            }
        }
        if(count($returnData) == 0) $returnData['error'] = 'Unable to perform task. Please try again';
        echo json_encode($returnData);
    break;
}
exit;
?>