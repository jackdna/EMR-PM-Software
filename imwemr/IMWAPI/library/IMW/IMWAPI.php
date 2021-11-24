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

	    if( $this->response->code() === 200 )
	    {
		    $responseData = array('Success'=>1, 'result'=>$response);
	    }
	    else
	    {
		    $responseData = array('Success'=>0, 'Error'=>$responseBody);
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
	
	/*
	 * This function is used to trigger appropriate saving function on the basis of data present int he request.
	 * It will validate the request based on the Primary column Id for field/endpoint saving profile.
	 * Created to be used in demographic Reoutes
	*/
	/* public function saveData( $parameters )
	{
		$endpoint = $this->request->pathname();
		$endpoint = substr($endpoint, strrpos($endpoint, '/')+1);
		
		$paramArray = (Array)$parameters;
		$primarykey = array_pop($paramArray);
		$primarykey = $primarykey->col_pri_id->parameter;
		
		if($this->request->__isset($primarykey) && $this->request->__get($primarykey) != '')
		{
			$this->service->validateParam($primarykey, 'Please provide valid '.$primarykey.'.')->isInt()->notNull()->{'is'.$endpoint}($this);
			
			$this->saveField($parameters);
		}
		else
		{
			$this->addField($parameters);
		}
	} */	
	
	public function addField( $parameters ){
		$counter = 0;
		$htmlStr = '';
		$addData = $qryData = $saveArr = array();
		
		$queryStr = '';
		foreach ( $parameters as $key => $data )
		{
			
			if ( $this->request->__isset($key) === true && trim($this->request->__get($key)) != '' )
			{
				$fieldName = $fieldVal = '';
				
				/*Set Referenced Values*/
				foreach ( $data as $keyTemp => &$value )
				{
					if ( is_object($value) === true )
					{
						if ( isset($value->parameter) === true )
						{
							$primaryKey = $value->parameter;
							$value = $this->request->__get($primaryKey);
						}
					}
				}
				
				$this->replaceValues( $data, $key );
				
				//These fields are used for reference only not for data insertion 
				if(isset($data->notsave) && $data->notsave == true){
					$fieldName = $key;	
					$fieldVal = $data->new_val;	
					if(isset($data->insertCase) && $data->insertCase === true) $queryStr .= ', `'.$data->col_name.'` = "'.$data->new_val.'"';
					
				}else{
					$addData[$key] = $data->new_val;
					
					if(!isset($addData['Description'])) $addData['Description'] = str_ireplace('updated', 'added', $data->title_msg);
					if(!isset($addData['tblName'])) $addData['tblName'] = $data->tb_name;
					if(!isset($addData['ptColName'])) $addData['ptColName'] = $data->patientIdColName;
					
					//Creating Query
					$queryStr .= ', `'.$data->col_name.'` = "'.$data->new_val.'"';
				}
				$saveArr[$key] = $data->new_val;
				if(empty($fieldName) == false && empty($fieldVal) == false ) $saveArr[$fieldName] = $fieldVal;
				
				$counter++;
			}
		}
		if( $counter == 0)
		{
			throw new Exception('Please provide a value to update.');
		}
		
		//Setting values for new additions
		$title = $addData['Description'];
		$htmlStr = $this->getHtml($addData);
		
		$sql = 'INSERT INTO `iportal_req_changes` SET `pt_id` = '.$this->request->__get('patientId');
		if(empty($queryStr) == false) $qryData['new_val'] = str_ireplace(array('iportal_req_changes', 'pt_id'), array($addData['tblName'], $addData['ptColName']), $sql).$queryStr;
		if(empty($htmlStr) == false) $qryData['new_val_lbl'] = addslashes($htmlStr);
		
		$qryData['tb_name'] = $addData['tblName'];
		$qryData['new_val_arr'] = serialize($saveArr);
		$qryData['old_val'] = '';
		$qryData['old_val_lbl'] = '';
		$qryData['col_pri_id'] = 0;
		$qryData['title_msg'] = $title;
		$qryData['action'] = 'insert';
		$qryData['operator_action'] = 0;
		$qryData['operator_id'] = 0;
		$qryData['del_status'] = 0;
		$qryData['is_approved'] = 0;
		
		if(count($qryData) > 0){
			foreach($qryData as $key => &$val){
				$sql .= ', `'.$key.'` = \''.$val.'\'';
			}
		}
		
		$resp = $this->app->dbh->imw_query($sql);
				
		if ( !$resp )
		{
			throw new Exception('Unable to save the Request.');
		}
		
	}
	
	//Creates HTML for the new fields to be shown in iDoc iPortal pop-up
	public function getHtml($arrData = array()){
		if(count($arrData) == 0) return false;
		
		//Title Row
		$headerRow = '';
		if(isset($arrData['Description']) && empty($arrData['Description']) == false){
			$headerRow = '<tr><td colspan="2">'.$arrData['Description'].'</td></tr>';
		}
		
		//Data Rows
		$strRow = '';
		if(count($arrData) > 0){
			foreach($arrData as $key => &$val){
				if($key == 'tblName' || $key == 'ptColName' || $key == 'Description') continue;
				$strRow .= '<tr><td><strong>'.$key.'</strong></td><td>'.$val.'</td></tr>';
			}
		}
		
		$finalHtml = '';
		if(empty($headerRow) == false && empty($strRow) == false){
			$finalHtml = '<table>'.$headerRow.$strRow.'</table>';
		}
		
		return $finalHtml;
	}
	
	//To Update fields in database
    public function saveField( $parameters ){
	
		/*Processed Fields Counter*/
		$fieldCounter = 0;
		
		foreach ( $parameters as $key => $data )
		{
			if ( $this->request->__isset($key) === true && trim($this->request->__get($key)) != '' )
			{
				$fieldCounter++;
				if(isset($data->notsave) && $data->notsave == true) continue;
				
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
    
    private function replaceValues( $data, $key, $callFrom = 0 )
    {
	
		/* Relation Array  */
		$arrRelationship = array();
		$chkRelation = $this->app->dbh->imw_query('select id,relation from patient_relations where del_status = 0');
		if($chkRelation && $this->app->dbh->imw_num_rows($chkRelation) > 0){
			while($row = $this->app->dbh->imw_fetch_assoc($chkRelation)){
				$arrRelationship[$row['id']] = $row['relation'];
			}
		}
		
		$arrMarital = array(1 => 'Divorced', 2 => 'Domestic Partner', 3 => 'Married', 4 => 'Single', 5 => 'Separated', 6 => 'Widowed');
		$medTypeArr = array(1 => 'Systemic', 4 => 'Ocular', 5 => 'Systemic' , 6 => 'Ocular');
		$arrTitle = array(1 => 'Mr.', 2 => 'Mrs.', 3 => 'Ms.', 4 => 'Dr.');
		
		$severityArr = array(0 => 'Fatal', 1 => 'Mild', 2 => 'Mild to moderate', 3 => 'Moderate', 4 => 'Moderate to severe', 5 => 'Severe');
		
		
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
				switch($sexVal){
					case 1:
						$data->new_val = 'Male';
					break;
					
					case 2:
						$data->new_val = 'Female';
					break;
					
					default:
						$data->new_val = '';
				}
				break;
				case 'status':

				$statusVal = (int)$this->request->__get($key);
				$data->new_val = strtolower( $arrMarital[$statusVal] );
				
				$data->old_val = strtolower( $arrMarital[$data->old_val] );

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
				
				case 'emergencyRelationship':
					$statusVal = (int)$this->request->__get($key);
					$data->new_val = strtolower( $arrRelationship[$statusVal] );
					if(is_int($data->old_val) === true) $data->old_val = strtolower( $arrRelationship[$data->old_val] );
				break;
			}
			break;
			
			case 'resp_party':
				switch( $data->col_name ){
					case 'relation':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrRelationship[$statusVal];
					break;
					
					case 'title':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrTitle[$statusVal];
					break;	
					
					case 'marital':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrMarital[$statusVal] ;
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
				$insArr = array(0 => 'primary', 1 => 'secondary');
				$caseArr = array(0 => 'medical', 1 => 'vision');
				switch($data->col_name){
					case 'provider':
						$data->new_val = $this->request->__get($key);
						$data->new_val_lbl = $this->getInsurance($data, $this->request->__get($key));
						$data->old_val = $data->old_val;
						$data->old_val_lbl = $this->getInsurance($data, $data->old_val);
					break;
					
					case 'subscriber_relationship':
						$statusVal = (int)$this->request->__get($key);
						$data->new_val = $arrRelationship[$statusVal];
					break;
					
					case 'type':
						$data->new_val = $insArr[$this->request->__get($key)];
					break;
					
					case 'case_type':
						$data->new_val = $caseArr[$this->request->__get($key)];
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
					
					case 'severity':
						$data->new_val = $severityArr[$this->request->__get($key)];
						if(is_int($data->old_val)) $data->old_val = $severityArr[$data->old_val];
						$data->old_val_lbl = $severityArr[$data->old_val];
					break;
					
					case 'med_route':
						$chkPhy = $this->app->dbh->imw_query('SELECT route_name as Route from route_codes WHERE id = '.$data->new_val.'');
						if($this->app->dbh->imw_num_rows($chkPhy) > 0){
							$rowPhy = $this->app->dbh->imw_fetch_assoc($chkPhy);
							$data->new_val = $rowPhy['Route'];
						}
					break;
					
				}
			break;
		}
    }
	
	//Returns Insurance formatted data
	private function getInsurance( $data = array(), $key = '' )
    {
		if(count($data) == 0 || empty($key) || is_object($key)) return ;	
		
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