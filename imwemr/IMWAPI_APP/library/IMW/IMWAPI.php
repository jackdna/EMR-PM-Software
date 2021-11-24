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
 * IMWAPI - Extension to Klein REST API framework
 */

namespace IMW;

use Klein\Klein;
use Klein\Request;
use Klein\AbstractResponse;
use Exception;


class IMWAPI extends Klein
{
    public function __construct() {
	
	// Instanciate defaults
	parent::__construct();
	
    }
	
    public function dispatch(
	Request $request = null,
	AbstractResponse $response = null,
	$send_response = false,
	$capture = self::DISPATCH_NO_CAPTURE
    ) {
	    $responseData = parent::dispatch($request, $response, $send_response, $capture);

	    $responseData = $this->response->body();
	    
	    /*Prepare Response Structure*/
	    $pathName = $this->request->pathname();

	    if( strtolower( substr($pathName, 1, 3) ) === 'get' )
		    $pathName = substr($pathName, 4);
	    else
		    $pathName = substr($pathName, 1);

	    $pathName = ucfirst($pathName);
	    $pathName = explode('/', $pathName);
	    $pathName = array_shift($pathName);

	    $responseBodyContnet = $this->response->body();
	    $responseBody = json_decode($responseBodyContnet, true);
	    if( $responseBody === null )
		    $responseBody = $responseBodyContnet;

	    $response = array($pathName=>$responseBody);
		
		// accessToken //
		
		
		$IgnoreArray = array('PractiseList','AccessToken','PatientFirstAuth','RegisterUser','ListSecQues','ForgotPass','VerifySecQues','ForgotRespPass','VerifyRespSecQues','ListOfImages','SetUserInfo','SetRespInfo','ChangeUserInfo','ChangeRespInfo');
		// accessToken //
		$key = array_keys($response);
		
		if(in_array($key[0],$IgnoreArray)){
			if( $this->response->code() === 200)
			{
				$responseData = array('Success'=>1, 'result'=>$response);
			}
			else
			{
				$responseData = array('Success'=>0, 'Error'=>$responseBody);
			}
		}
		else{
			$sql = "SELECT `id`, `expire_date_time`
			FROM
				`fmh_iportal_api_token_log`
			WHERE
				`token`='".$_REQUEST['accessToken']."' AND user_id = '".$_REQUEST['patientId']."'";
			$resp = $this->app->dbh->imw_query($sql);
			$tokenStatus = true;
			
			if( $resp && $this->app->dbh->imw_num_rows($resp) === 1 )
			{
				$tokenData = $this->app->dbh->imw_fetch_assoc($resp);
				
				$tokenExpireDateTime = strtotime($tokenData['expire_date_time']);
				
				if( $tokenExpireDateTime < time() )
				{
					$tokenStatus  = false;
					
				}
				
			}
			else
			{
				$tokenStatus  = false;
				
			}
			if( $this->response->code() === 200)
			{	
			
				if($_SESSION['check_session'] != session_id() || !isset($_REQUEST['version']) || empty($_REQUEST['version']) || $_REQUEST['version'] != 1 || !isset($_REQUEST['cst']) || $_REQUEST['cst'] != $_SESSION['app_session'] || !isset($_REQUEST['patientId']) || $_REQUEST['patientId'] != $_SESSION['patient']){
				
					$data = array();
				
					$data['session'] = false;
						
					$data['Error'] = "Invalid Request";
					
					session_unset($_SESSION['patient']);
					session_unset($_SESSION['app_session']);
					session_unset($_SESSION['check_session']);
					
					session_destroy();
							
					echo json_encode($data);
						
					die();	
				}
				
				$responseData = array('Success'=>1, 'TokenStatus'=>$tokenStatus, 'session'=>true, 'result'=>$response);
			}
			else
			{
				$responseData = array('Success'=>0, 'TokenStatus'=>$tokenStatus, 'session'=>false, 'Error'=>$responseBody);
			}
		}

	    if( !$this->request->__isset('responseFormat') || $this->request->__get('responseFormat') !== 'xml' ){
		    $this->response->json($responseData);
	    }
    }
    
    /*Get Old Value of a Field from Database*/
    public function oldValue( $data, $fieldName )
    {
		/*Query Old Values from DB*/
		$sql = 'SELECT `'.$fieldName.'` FROM `'.$data->tb_name.'` WHERE `'.$data->pri_col_name.'` = \''.$data->col_pri_id.'\'';
		$resp = $this->app->dbh->imw_query($sql);

		if( $this->app->dbh->imw_num_rows($resp) != 1 )
		{
			echo $sql;
			throw new Exception('Unable to locate Old Value.');
		}

		$resp = $this->app->dbh->imw_fetch_assoc($resp);
		return $resp[$fieldName];
    }
    
	/* Replacing Custom Variables with required one -- {REPLACE_*} => {Field || Parameter || Text} */
    public function replaceCustomValue($arrData = array()){
		
		//Single Value
		/*
			"replace_var": {
				"col_lbl": {"REPLACE_TYPE" : {"parameter":"PatientInsId"}}
			}
		
		*/
		
		//Multiple Values
		/*
			"replace_var": {
				"title_msg":
					[
						{
							"REPLACE_TYPE" : {"field" : "type"}
						},
						{
							"Replace_Sec" : {"text" : "Text Sample"}
						}
					]
			}
		*/
		
		
		if(count($arrData) == 0) return false;
		
		$replaceArr = array();
		if(isset($arrData->replace_var) && is_object($arrData->replace_var) === true){
			foreach($arrData->replace_var as $repKey => &$repVal){
				$replaceArr[$repKey] = array('replaceText' => array(), 'replaceVal' => array());
				
				//If multiple values are given
				if(is_array($repVal) === true){
					foreach($repVal as $obj){
						if(is_object($obj)){
							foreach($obj as  $repKeys => &$repValues){
								$tmp_arr = array();
								$replaceValue = '';
								if ( isset($repValues->field) === true )
								{
									$replaceValue = $this->oldValue( $arrData, $repValues->field );
								}
								elseif ( isset($repValues->parameter) === true )
								{
									$primaryKey = $repValues->parameter;
									
									if ( empty($primaryKey) === true )
									{
									throw new Exception('Primary Key column is not configured for: '.$keyTemp);
									}
									
									if ( $this->request->__isset($primaryKey) === false )
									{
									throw new Exception('Please provide identifier value: '.$primaryKey);
									}
									
									$replaceValue = $this->request->__get($primaryKey);
								}elseif ( isset($repValues->text) === true ){
									$replaceValue = $repValues->text;
								}
								
								array_push($replaceArr[$repKey]['replaceText'], '{'.$repKeys.'}');
								array_push($replaceArr[$repKey]['replaceVal'], $replaceValue);
							}		
						}
					}
				}else{		// If single value is given
					foreach($repVal as $key => &$val){
						$replaceValue = '';
						if ( isset($val->field) === true )
						{
							$replaceValue = $this->oldValue( $arrData, $val->field );
						}
						elseif ( isset($val->parameter) === true )
						{
							$primaryKey = $val->parameter;
							
							if ( empty($primaryKey) === true )
							{
							throw new Exception('Primary Key column is not configured for: '.$keyTemp);
							}
							
							if ( $this->request->__isset($primaryKey) === false )
							{
							throw new Exception('Please provide identifier value: '.$primaryKey);
							}
							
							$replaceValue = $this->request->__get($primaryKey);
						}else{
							$replaceValue = $val;
						}	

						array_push($replaceArr[$repKey]['replaceText'], '{'.$key.'}');
						array_push($replaceArr[$repKey]['replaceVal'], $replaceValue);
					}
				}
			}
		}
		
		return $replaceArr;
	}
	
    public function saveField( $parameters ){
	
		/*Processed Fields Counter*/
		$fieldCounter = 0;
		
		foreach ( $parameters as $key => $data )
		{
			if ( $this->request->__isset($key) === true && trim($this->request->__get($key)) != '' )
			{
				$fieldCounter++;
				
				/*Set Referenced Values*/
				foreach ( $data as $keyTemp => &$value )
				{
					if ( is_object($value) === true )
					{
						if ( isset($value->field) === true )
						{
							$value = $this->oldValue( $data, $value->field );
						}
						elseif ( isset($value->parameter) === true )
						{
							$primaryKey = $value->parameter;
							
							if ( empty($primaryKey) === true )
							{
							throw new Exception('Primary Key column is not configured for: '.$keyTemp);
							}
							
							if ( $this->request->__isset($primaryKey) === false )
							{
							throw new Exception('Please provide identifier value: '.$primaryKey);
							}
							
							$value = $this->request->__get($primaryKey);
						}
					}
				}
				
				$this->replaceValues( $data, $key );
				$data->reqDateTime = date('Y-m-d H:i:s');
				
				$replaceArr = $this->replaceCustomValue($data);
				unset($data->replace_var);
				
				/*Clear Values*/
				foreach ( $data as $key => &$value )
				{
					if ( is_object($value) === true || $value === true )
					{
						$value = '';
					}
					
					if(isset($replaceArr[$key])){
						$rplTxtArr = $replaceArr[$key]['replaceText'];
						$rplValArr = $replaceArr[$key]['replaceVal'];
						
						$value = str_replace($rplTxtArr, $rplValArr, $value);
						
						/* //If any unexpected {var} is still in the string
						$value = preg_replace('/\{.*\}/', '', $value);   */
					}
					
				}
				
				/*Inset Change Request*/
				$sql = 'INSERT INTO `iportal_req_changes` SET `pt_id` = '.$this->request->__get('patientId');
				
				/*Build query*/
				foreach ($data as $key => $value)
				{
					$sql .= ', `'.$key.'` = \''.$value.'\'';
				}
				
				$resp = $this->app->dbh->imw_query($sql);
				
				if ( !$resp )
				{
					throw new Exception('Unable to save the Request.');
				}
			}   
			
		}
		if( $fieldCounter == 0)
		{
			throw new Exception('Please provide a value to update.');
		}
    }
    
    private function replaceValues( $data, $key )
    {
	
		/* Relation Array  */
		$arrRelationship = array();
		$chkRelation = $this->app->dbh->imw_query('select id,relation from patient_relations where del_status = 0');
		if($chkRelation && $this->app->dbh->imw_num_rows($chkRelation) > 0){
			while($row = $this->app->dbh->imw_fetch_assoc($chkRelation)){
				$arrRelationship[$row['id']] = $row['relation'];
			}
		}
		
		$arrMarital = array(1 => 'Divorced', 2 => 'Domestic Partner', 3 => 'Married', 4 => 'Single', 5 => 'Separated', 6 => 'Windowed');
		$medTypeArr = array(1 => 'Systemic', 4 => 'Ocular', 5 => 'Systemic' , 6 => 'Ocular');
		$arrTitle = array(1 => 'Mr.', 2 => 'Mrs.', 3 => 'Ms.', 4 => 'Dr.');
		
		
		$data->new_val = $this->request->__get($key);
		
		switch( $data->tb_name )
		{
			case 'patient_data':
			switch( $data->col_name )
			{
				case 'DOB':

				list($yy, $mm, $dd) = explode( '-', $this->request->__get($key) );
				if( checkdate($mm, $dd, $yy) === true )
				{
					$data->new_val	    = $yy.'-'.$mm.'-'.$dd;
					$data->new_val_lbl  = $mm.'-'.$dd.'-'.$yy;
				}

				/*Change Format of Old Value*/
				list($yy, $mm, $dd) = explode( '-', $data->old_val );
				if( checkdate($mm, $dd, $yy) === true )
				{
					$data->old_val  = $mm.'-'.$dd.'-'.$yy;
				}
				else
				{
					$data->old_val = '';
				}

				break;
				case 'sex':

				$sexVal = (int)$this->request->__get($key);
				$data->new_val = ($sexVal === 1) ? 'Male' : ($sexVal === 2) ? 'Female' : '' ;

				break;
				case 'status':

				$statusVal = (int)$this->request->__get($key);
				$data->new_val = strtolower( $arrMarital[$statusVal] );

				break;
				case 'primary_care_phy_id':

				$physicianId	=   (int)$this->request->__get($key);
				$data->new_val	=   $physicianId;

				/*Get Referring Physician Details from DB*/
				$sql	=   "SELECT CONCAT(`LastName`, ', ', `FirstName`) AS 'phyLabel' FROM `refferphysician` WHERE `physician_Reffer_id`=".$physicianId;
				$resp	=   $this->app->dbh->imw_query($sql);
				$resp	=   $this->app->dbh->imw_fetch_assoc($resp);
				$data->new_val_lbl  =   $resp['phyLabel'];

				break;
				case 'preferr_contact':
				
				$data->new_val = (int)$data->new_val--;
				
				$preferrContactLabels = array('Preferr Contact - Home Phone', 'Preferr Contact - Business Phone', 'Preferr Contact - Mobile Phone');;
				
				$data->old_val_lbl = $preferrContactLabels[$data->old_val];
				$data->new_val_lbl = $preferrContactLabels[$data->new_val];
				
				break;
			}
			break;
			
			case 'resp_party':
				switch( $data->col_name ){
					case 'relation':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = strtolower( $arrRelationship[$statusVal] );
					break;
					
					case 'title':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = strtolower( $arrTitle[$statusVal] );
					break;	
					
					case 'marital':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = strtolower( $arrMarital[$statusVal] );
					break;		
				}
			
			break;
			
			case 'patient_family_info':
				switch( $data->col_name ){
					case 'patient_relation':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val =  $arrRelationship[$statusVal];
					break;
					
					case 'title':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrTitle[$statusVal];
					break;	
				}
			
			break;	
			
			case 'insurance_data':
				switch($data->col_name){
					case 'provider':
						$data->new_val = $this->getInsurance($data, $this->request->__get($key));
						$data->old_val = $this->getInsurance($data, $data->old_val);
					break;
					
					case 'subscriber_relationship':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrRelationship[$statusVal];
					break;
				}
				
			break;
			
			case 'lists':
				switch($data->col_name){
					case 'title':
						$titleId = (int)$this->request->__get($key);
						$titleVal = '';
					
						//Allergy
						if($this->request->__isset('AllergyId') && trim($this->request->__isset('AllergyId')) != ''){
							//Get Allergy name
							$chkQry = $this->app->dbh->imw_query('
								SELECT 
									`allergie_name` as Name
								FROM 
									allergies_data
								WHERE 
									`allergies_id` = '.$titleId		
							);	
							
							if($this->app->dbh->imw_num_rows($chkQry) > 0){
								$row = $this->app->dbh->imw_fetch_assoc($chkQry);
								$titleVal = $row['Name'];
							}
							
							$data->new_val = $titleVal;
						}elseif($this->request->__isset('MedicationId') && trim($this->request->__isset('MedicationId')) != ''){
							//Medication
							$chkQry = $this->app->dbh->imw_query('
								SELECT 
									`medicine_name` as Name
								FROM 
									medicine_data
								WHERE 
									`id` = '.$titleId		
							);	
							
							if($this->app->dbh->imw_num_rows($chkQry) > 0){
								$row = $this->app->dbh->imw_fetch_assoc($chkQry);
								$titleVal = $row['Name'];
							}
							
							$data->new_val = $titleVal;
						}elseif($this->request->__isset('SurgeryId') && trim($this->request->__isset('SurgeryId')) != ''){
							//Surgery
							$chkQry = $this->app->dbh->imw_query('
								SELECT 
									`title` as Name
								FROM 
									lists_admin
								WHERE 
									`id` = '.$titleId		
							);	
							
							if($this->app->dbh->imw_num_rows($chkQry) > 0){
								$row = $this->app->dbh->imw_fetch_assoc($chkQry);
								$titleVal = $row['Name'];
							}
							
							$data->new_val = $titleVal;
						}
						
						
					break;
					
					case 'sites':
						$arrSite = array(1 => 'OS', 2 => 'OD', 3 => 'OU', 4 => 'PO');
						
						$siteType = '';
						
						$chkQury = $this->app->dbh->imw_query('SELECT type from lists where id = '.$data->col_pri_id.' AND pid = '.$this->request->__get('patientId').'');
						if($this->app->dbh->imw_num_rows($chkQury) > 0){
							$row = $this->app->dbh->imw_fetch_assoc($chkQury);
							$siteType = $medTypeArr[$row['type']];
						}
						
						//If site is Ocular
						if(empty($siteType) == false && strtolower($siteType) == 'ocular' ){
						
							$data->new_val_lbl = $arrSite[$data->new_val];
							$data->old_val_lbl = $arrSite[$data->old_val];
						
						}else{
						
						//Unset if site is systemic or any other
							unset($data->new_val_lbl); 
							unset($data->old_val_lbl);
							unset($data->new_val); 
							unset($data->old_val);
						}
					break;
					
					case 'referredby':
						//Get Physician Name
						$chkPhy = $this->app->dbh->imw_query('SELECT FirstName, MiddleName, LastName FROM refferphysician WHERE FirstName != "" AND LastName != "" AND physician_Reffer_id = '.$data->new_val.'');
						if($this->app->dbh->imw_num_rows($chkPhy) > 0){
							$rowPhy = $this->app->dbh->imw_fetch_assoc($chkPhy);
							$data->new_val = $rowPhy['LastName'].', '.$rowPhy['FirstName'].' '.$rowPhy['MiddleName'];
						}
					break;
				}
			break;
		}
    }
	
	//Returns Insurance formatted data
	private function getInsurance( $data = array(), $key = '' )
    {
		if(count($data) == 0 || empty($key)) return ;	
		
		$insDetail = '';
		
		$arr_find = array("&","\n","\t");
		$arr_rplc = array("&amp;","","");
		
		$chkInsurance = $this->app->dbh->imw_query('SELECT id,name,contact_address,City,State,Zip,zip_ext FROM insurance_companies WHERE ins_del_status = 0 AND id = '.$key.'');
		
		if($this->app->dbh->imw_num_rows($chkInsurance) > 0){
			$row = $this->app->dbh->imw_fetch_assoc($chkInsurance);
			$insAddr = str_ireplace("\r\n","",$row['contact_address']);
			
			$pt_zip = $row['Zip'];
			$pt_id = $row['id'];
			if($row['zip_ext']){
				$pt_zip=$pt_zip."-".$row_ins_name->zip_ext;
			}
			
			// Setting Insurance Save Format
			$insDetail = htmlspecialchars($row['name'])." - ".(htmlspecialchars($insAddr))." - ".(htmlspecialchars($row['City']))." ".(htmlspecialchars($row['State']))." ".($pt_zip)." -".$row['id'];
			
			$insDetail = str_replace($arr_find,$arr_rplc,$insDetail);
		}
		
		return $insDetail;
	}
}