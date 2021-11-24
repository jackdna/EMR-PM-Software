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
/**
 * File: updoxDirect.php
 * Purpose: Handle communication with UPDOX API, UPDOX API Integration
 * Access Type: Include 
 **/
//echo dirname(__FILE__).'/updoxFax.php';
//die;
//echo dirname(__FILE__).'../library/updox/updoxFax.php';
//die;
require_once( dirname(__FILE__).'/../library/updox/updoxFax.php' );
class updoxDirect extends updoxFax{
	
	public $arrMail = array();
	public $save_directory = "";
	
	/*Constructor to fetch Account credentials from DB*/
	public function __construct(){
		
		/*Invoke Parent's Constructor*/
		parent::__construct();
		
		$sql = "SELECT updox_user_id FROM users where id='".$_REQUEST['phy_id']."'";
		$resp = imw_query($sql);
		$result_data = imw_fetch_assoc($resp);
		
		
		//$this->auth['userId'] = $resp['updox_user_id'];
		
		$this->auth['userId'] = $result_data['updox_user_id'];
		
		$this->set_attach_dir_path();
		//print_r($this->auth);
	}
	
	
	/**
	 * Validate direct address
	 * @to = Email address
	 * @subject = Email subject
	 * @msg = Pain text email message
	 * @attachments = Email attachments
	 * */
	public function validateMail()
	{
		$data = array('auth'=>$this->auth);
		$data['recipient'] = $this->arrMail['to_email'];
		$data['includeLoA'] = '';
		
		$resp = $this->call('DirectAddressValidate', $data);
		
		if($resp['data']->validDirectAddress === false)
		{
			$resp="Invalid Direct Address";
		}else{
			$resp="";
		}
		return $resp;
	}
	
	
	/**
	 * Get user folder
	 * */
	
	function set_attach_dir_path(){
		$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
		
		$upload_dir = $dir_path."/users";
		if(!is_dir($upload_dir)){
			mkdir($upload_dir,0700);
		}
		$uDir = $upload_dir."/UserId_".$phy_id;
		if(!is_dir($uDir)){
			mkdir($uDir,0700);
		}
		$uDirMailAttach = $upload_dir."/UserId_".$phy_id."/mails";
		if(!is_dir($uDirMailAttach)){
			mkdir($uDirMailAttach,0700);
		}
		$this->save_directory = $uDirMailAttach."/";
	}
	
	
	/**
	 * Get direct message
	 * @msgId = Updox message Id to fetch message data
	 * */
	public function getMessage($msgId,$UserId)
	{
		$this->auth['userId'] = $UserId;
		$data = array('auth'=>$this->auth);
		
		$data['messageId'] = $msgId;
		
		$resp = $this->call('MessageRetrieveWithAttachmentID', $data);
		return $resp;
	}
	
	/**
	 * Get direct message
	 * @attachId = Updox attached Id to fetch message attached data
	 * */
	public function getMessageAttachment($attachId,$UserId)
	{
		$this->auth['userId'] = $UserId;
		$data = array('auth'=>$this->auth);
		
		$data['attachmentId'] = $attachId;
		
		$resp = $this->call('MessageAttachRetrieve', $data);
		return $resp;
	}
	
	/**
	 * Set True to mark the message as read, false to mark the message as unread.
	 * @msgId = Updox message Id to set message read/unread
	 * */
	public function setMessageMark($msgId,$UserId)
	{
		$this->auth['userId'] = $UserId;
		$data = array('auth'=>$this->auth);
		
		$data['messageId'] = $msgId;
		$data['read'] = true;
		
		$resp = $this->call('MessageMark', $data);
		return $resp;
	}
	
	
	/**
	 * Send direct message
	 * @to = Email address
	 * @subject = Email subject
	 * @msg = Pain text email message
	 * @attachments = Email attachments
	 * */
	public function sendMail($to_email,$subject,$body)
	{
		$data = array('auth'=>$this->auth);
		$data['to'] = $to_email;
		$data['subject'] = $subject;
		$data['textMessage'] = $body;
		
		foreach($this->arrMail['attachment'] as $att_key=>$att_val){
			if($this->arrMail['attachment'][$att_key]['file_name']!=""){
				$data['attachments'][$att_key]['name']=str_replace('.zip','',$this->arrMail['attachment'][$att_key]['file_name']);
				$data['attachments'][$att_key]['fileName']=$this->arrMail['attachment'][$att_key]['file_name'];
				$data['attachments'][$att_key]['mimeType']=$this->arrMail['attachment'][$att_key]['mime'];
				$data['attachments'][$att_key]['content']=base64_encode(file_get_contents("../../cda/temp/".$this->arrMail['attachment'][$att_key]['file_name']));
			}
		}
		
		/*$sql = 'SELECT id,fname,lname,mname,DOB,sex,email FROM patient_data where id="'.$this->arrMail['PatientId'].'"';
		$pat_data = unserialize(mysql_query_i($sql));
		$data['patientDemographics']['patientId'] = $pat_data['id'];
		$data['patientDemographics']['firstName'] = $pat_data['fname'];
		$data['patientDemographics']['middleName'] = $pat_data['mname'];
		$data['patientDemographics']['lastName'] = $pat_data['lname'];
		$data['patientDemographics']['dateOfBirth'] = $pat_data['DOB'];
		$data['patientDemographics']['gender'] = substr($pat_data['sex'],0,1);
		$data['patientDemographics']['emailAddress'] = $pat_data['email'];
		$resp = $this->call('PatientDirectMessageSend', $data);*/
		$resp = $this->call('DirectSimpleSend', $data);
		return $resp;
	}
	
}
