<?php
include_once 'dss_core.php';
class Dss_enc_visit_notes extends Dss_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

	public function tiu_TitleSearch( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed for TIU Title Search is blank.' );

		$result = $this->CURL($params,'DSIHTE/TIU_TitleSearch');
		return $result;
	}

	public function dssCreateTiuRecord( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed for Create TIU Record.' );

		$result = $this->CURL($params,'DSIHTE/TIU_CreateTIURecord');
		return $result;	
	}
    
	public function addCPTCodeToVisit( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to add CPT Code To Visit is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_AddCPTCodeToVisit');
		return $result;
	}
    
	public function addICDCodeToVisit( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to add ICD Code To Visit is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_AddICDCodeToVisit');
		return $result;
	}
    
	public function getVisitIFN( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to get Visit is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_GetVisitIFN');
		return $result[0];
	}
    
	public function createNewPCEVisit( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to create Visit is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_CreateNewPCEVisit');
		return $result[0];
	}
    
	public function PCE_ICDLexSearch( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to to get ICD10 codes or CPT codes is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_ICDLexSearch');
		return ( isset($result[0])?$result[0]:array() );
	}
    
	public function PCE_DefaultCPTModifiers( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to get Modifiers for CPT codes is blank.' );

		$result = $this->CURL($params,'DSIHTE/PCE_DefaultCPTModifiers');
		return $result;
	}
    
	
    //Consult save process starts here
    
	public function ConsultGetServiceSpecialtyList()
	{
		$params = array();
        $params['start']="o";
        $params['purpose']="0";
        $params['includes']="1";
        $params['consultIen']=" ";
		$result = $this->CURL($params,'DSIHTE/CPRS_ConsultGetServiceSpecialtyList');
		return $result;
	}
    
	public function ConsultGetDialogSelections()
	{
        $params = array();
        $params['type']="C";
    	$result = $this->CURL($params,'DSIHTE/CPRS_ConsultGetDialogSelections');
		return $result;
	}
    
    public function ConsultSave( $params = array() )
	{
		if ( empty($params) )
			throw new Exception( 'Array passed to save consult is empty.' );

		$result = $this->CURL($params,'DSIHTE/CPRS_ConsultSave');
		return $result;
	}

	/**
	 * Process ICD10, get lexien from db if available, otherwise send request to dss for the lexien as per given ICD10 Code.
	 */
    public function processICD10($icd10, $dos = '')
    {
        if ( empty($icd10) )
            throw new Exception( 'ICD10 code is not allowed as empty.' );

        $dos = (!empty($dos) && $dos != 0) ? $dos : $this->convertToFileman(date('Y-m-d'));

        $sql = "SELECT id, lexIen FROM dss_code_dictionary WHERE code = '".$icd10."' AND code_type = 'icd10'";
        $query = imw_query($sql);
        if(imw_num_rows($query) > 0) {
            $data = imw_fetch_assoc($query);
            if( empty($data['lexIen']) || $data['lexIen'] == 0 ) {
                return $this->updateDssCodes($icd10, $dos, 'update');
            } else {
                return $data['lexIen'];
            }
        } else {
        	return $this->updateDssCodes($icd10, $dos, 'insert');
        }
    }

    /**
     * Get lexien from dss as per given ICD10 Code, and update into DB if exists. Otherwise insert new record with lexien.
     */
    public function updateDssCodes($icd10, $dos, $action) 
    {
    	$postArr = array(
            'search' => $icd10,
            "application" => "10D",
            'maxRecords' => "1",
            'searchDate' => $dos,
        );
        $result = $this->PCE_ICDLexSearch($postArr);
        if(empty($result)==false && isset($result['lexIen']) && $result['lexIen']!='-1') {
            if($action == 'update') {
            	$id = '';
            	$sql = "SELECT id FROM dss_code_dictionary WHERE code = '".$icd10."' AND code_type = 'icd10'";
            	$query = imw_query($sql);
		        if(imw_num_rows($query) > 0) {
		        	$res = imw_fetch_assoc($query);
		        	$id = $res['id'];
		        }
                $updateLexien = "UPDATE dss_code_dictionary SET lexIen = '".$result['lexIen']."' WHERE code = '".$icd10."' AND id = ".$id;
                imw_query($updateLexien) or imw_error();
                return $result['lexIen'];
            } elseif ($action == 'insert') {
                $insertLexien = "INSERT INTO dss_code_dictionary SET code = '".$icd10."', code_type = 'icd10', status = 0, lexIen = '".$result['lexIen']."' ";
                imw_query($insertLexien) or imw_error();
                return $result['lexIen'];
            }
        } else {
        	$errorMsg = "DSS Exception: Active ICD10 code ".$icd10." not found";
        	if(isset($result['codeDescription']) && $result['codeDescription'] != '') {
        		$errorMsg = "DSS Exception: ".$result['codeDescription'];
        	}
        	throw new Exception( $errorMsg );
        }
    }

}

