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
require_once( dirname(__FILE__).'/updoxFax.php' );
class updoxDirect extends updoxFax{

	public $arrMail = array();
	public $save_directory = "";
	public $direct_updox_user = '';

	/*Constructor to fetch Account credentials from DB*/
	public function __construct($directUserId = ''){

		/*Invoke Parent's Constructor*/
		parent::__construct();

		$this->direct_updox_user = $_SESSION['authId'];
		if(empty($directUserId) == false) $this->direct_updox_user = $directUserId;

		$sql = 'SELECT updox_user_id FROM users where id="'.$this->direct_updox_user.'"';
		$resp = imw_query($sql) or die($sql.imw_error());
		$data = imw_fetch_assoc($resp);
		$this->auth['userId'] = $data['updox_user_id'];

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
		$uDir = $upload_dir."/UserId_".$this->direct_updox_user;
		if(!is_dir($uDir)){
			mkdir($uDir,0700);
		}
		$uDirMailAttach = $upload_dir."/UserId_".$this->direct_updox_user."/mails";
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
	public function sendMail()
	{
		$data = array('auth'=>$this->auth);
		$data['to'] = $this->arrMail['to_email'];
		$data['subject'] = $this->arrMail['subject'];
		$data['textMessage'] = $this->arrMail['body'];
		foreach($this->arrMail['attachment'] as $att_key=>$att_val){
			if($this->arrMail['attachment'][$att_key]['file_name']!=""){

				$tmp_content = "";
				if(file_exists($this->save_directory.$this->arrMail['attachment'][$att_key]['file_name'])){
					$tmp_content = $this->save_directory.$this->arrMail['attachment'][$att_key]['file_name'];
				}else if(!empty($this->arrMail['attachment'][$att_key]['complete_path']) && file_exists($this->arrMail['attachment'][$att_key]['complete_path'])){
					$tmp_content = $this->arrMail['attachment'][$att_key]['complete_path'];
				}

				if(!empty($tmp_content)){
						$tmp_content = base64_encode(file_get_contents($tmp_content));
				}

				$data['attachments'][$att_key]['name']=str_replace('.zip','',$this->arrMail['attachment'][$att_key]['file_name']);
				$data['attachments'][$att_key]['fileName']=$this->arrMail['attachment'][$att_key]['file_name'];
				$data['attachments'][$att_key]['mimeType']=$this->arrMail['attachment'][$att_key]['mime'];
				$data['attachments'][$att_key]['content']=$tmp_content;
			}
		}
		$resp = $this->call('DirectSimpleSend', $data);

		/**
		 * Log Direct Message in Status Update Queue
		 */
		if(
			defined('UPDOX_USE_WEBHOOKS') &&
			UPDOX_USE_WEBHOOKS === false &&
			$resp['status'] != 'failed' &&
			$resp['data']->messageId > 0
		)
		{
			$directStatusData = [
				'userId' => $this->auth['userId'],
				'messageId' => $resp['data']->messageId
			];
			$directStatusData = json_encode($directStatusData);

			/**
			 * Create the "updoxPendingStatusUpdate" data directory, if it does not exists already
			 */
			$queueDir = data_path().'updoxPendingStatusUpdate';
			if( !is_dir($queueDir) )
			{
				mkdir( $queueDir, 0700, true );
				chown( $queueDir, 'apache' );
			}

			file_put_contents($queueDir.'/'.$resp['data']->messageId.'.json', $directStatusData);
		}

		return $resp;
	}


	/*
	 * Check User Account Status for ClientAccess Tool
	*/
	public function pingAccountUser( $userId = false)
	{
		if( $userId !== false )
		{
			$this->auth['userId'] = $userId;
		}

		$data = array('auth'=>$this->auth);

		$resp = $this->call( 'pingWithAuth', $data, true );
		return $resp;
	}

	/*
	 * Check Email status for ClientAccess Tool
	*/
	public function validateDirect( $userId = false, $emailId = false)
	{
		if( $userId !== false )
		{
			$this->auth['userId'] = $userId;
		}

		$data = array('auth'=>$this->auth);


		$data['recipient'] = $emailId;
		$data['includeLoA'] = '';

		$resp = $this->call('DirectAddressValidate', $data, true);

		return $resp;
	}

	/**
	 * List All direct messages for the Practice
	 * This function will be used only then updox would not be able to push data to iMW through webhooks
	 * This would work based on pull mechanism.
	 * This function will list the unread direct messages pending int the Inbox for all the updox direct enabled users of the Practice
	 */
	public function listPendingInboundDirect()
	{
		/**
		 * Authentical data
		 * Rquired by Updox
		 */
		$data = array('auth'=>$this->auth);

		$data['countUnreadOnly'] = true;
		$data['location'] = 'I';
		$data['messageTypes'] = ['email'];

		$resp = $this->call('MessageFetchForAccount', $data);
		return $resp;
	}
}
