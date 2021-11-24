<?php
// include_once 'dss_core.php';
require_once(dirname(__FILE__) . "/../../library/dss_api/dss_core.php");
class Dss_demographics extends Dss_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

    /******SEARCH PATIENT BY LAST NAME**** "patientDFN": "string",  "patientName": "string" *** */
	public function PT_SearchForVistApatient( $lname='' )
	{
		if ( $lname == '' )
			throw new Exception( 'Patient last name is Blank.' );

        $params = array();
        $params['patientName'] = $lname;

		$result = $this->CURL($params,'DSIHTE/PAT_SearchForVistApatient');
		return $result;
	}
    
    /******SEARCH PATIENT BY FULL NAME****/
	public function PT_TearApartPatientName( $patientName='' )
	{
		if ( $patientName == '' )
			throw new Exception( 'Patient name is Blank.' );

        $params = array();
        $params['patientName'] = $patientName;

		$result = $this->CURL($params,'DSIHTE/PAT_TearApartPatientName');
		return $result;
	}
    
    /******SEARCH PATIENT BY ID****/
	public function PT_GetPatientInfo( $patient_id='' )
	{
		if ( $patient_id == '' )
			throw new Exception( 'Supplied Patient Id is Blank.' );

        $params = array();
        $params['patient'] = $patient_id;

		$result = $this->CURL($params,'DSIHTE/PAT_GetPatientInfo');
		return $result;
	}
    
    /******SEARCH PATIENT Patient Demographics BY DSIHTE/PAT_GetDemographics****/
    /*****  You can either pass patient's DFN or SSN to identify the patient.  ******/
	public function PT_GetDemographics( $patient_id='' )
	{
		if ( $patient_id == '' )
			throw new Exception( 'Blank Patient Id Supplied.' );
		
		$params = array();
        $params['patient'] = $patient_id;

		$result = $this->CURL($params,'DSIHTE/PAT_GetDemographics');
		return $result;
	}
	
	
}

