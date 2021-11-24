<?php
include_once 'dss_core.php';
class Dss_medical_hx extends Dss_core
{
	public function __construct()
	{
        parent::__construct();
	}

    /******Get Users List :: Provision Users within a DEVELOPMENT VistA/Cache Server ****/
	public function GetPatientProblemList($params = array())
	{
        if(empty($params))
                throw new Exception( 'Empty array supplied to get patient problem list.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_GetPatientProblemList');
		return $result;
	}
    
	public function GetProblemDetails($params = array())
	{
        if( empty($params) )
                throw new Exception( 'Empty array supplied to get patient problem list.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_GetProblemDetails');
		return $result;
	}
    
    //upload Added problem to vista
    public function updatePatientProblemList($params = array())
	{
        if( empty($params) )
                throw new Exception( 'Empty array supplied to add patient problem.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_UpdatePatientProblemList');
		return $result;
	}
    
    //upload Edited problem to vista
    public function editPatientProblemList($params = array())
	{
        if( empty($params) )
                throw new Exception( 'Empty array supplied to update patient problem.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_EditPatientProblemList');
		return $result;
	}
    
    public function deletePatientProblemList($params = array())
	{
        if( empty($params) )
                throw new Exception( 'Empty array supplied to delete patient problem.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_DeleteProblem');
		return $result;
	}
    
	/**
	 * Get Patient Allergies
	 * @param: patientId
     * @return: list of allergies array (allergyId, allergent, severity, symptoms)
	 */
	public function getPatientAllergies($patientId)
	{
		if(empty($patientId))
			throw new Exception('Empty value supplied.');

		$args = array(
			"patientId" => $patientId
		);
		$result = $this->CURL($args, 'DSIHTE/CPRS_GetPatientAllergyList');
		return $result;
	}

	/**
	 * Save patient allergiees
	 */
	public function savePatientAllergies($args = array())
	{
		if(empty($args))
			throw new Exception('Empty value supplied.');
		$result = $this->CURL($args, 'DSIHTE/CPRS_SavePatientAllergy');
		return $result;
	}
    
	/**
	 * Search allergies in DSS
     * /DSIHTE/CPRS_SearchForVistAAllergies
	 */
	public function searchForVistAAllergies($args = array())
	{
		if(empty($args))
			throw new Exception('Empty array supplied to get allergies in DSS.');
		$result = $this->CURL($args, 'DSIHTE/CPRS_SearchForVistAAllergies');
		return $result;
	}

    /**
	 * Mark Allergy in Error
	 * @param: patient, allergy
	 * @return: code, tiuDocumentIen
	 */
	public function cancelPatientAllergy($params = array())
	{
		if(empty($params))
			throw new Exception("Paitent DFN and Allergy IEN are not allowed to be empty.");

		$result = $this->CURL($params, 'DSIHTE/CPRS_AllergyMarkEnteredError');
		return $result;
	}

    /**
     * Get remaining patient medicin list
     * @param: patientDFN
     * @return: medicationId, drugName, stopDate, route, schedule, refills, remaining, orderNumber
     */
    public function getRemainingPatientMedList($patientDFN)
    {
        if(empty($patientDFN))
            throw new Exception('Patient id is empty null.');
        $result = $this->CURL(array("patientDFN"=>$patientDFN), 'DSIHTE/CPRS_GetPatientMedList');
        return $result;
	}
	
	/**
     * Get patient medication list
     * @param: patientDFN, startDate, endDate
     * @return: orderIfn, drugIfn, drugNameDose, dosingRuleText, startDate, stopDate, provider, status, field_8 (comment)
     */
    public function getPatientMedList($patientDFN, $startDate, $endDate)
    {
        if(empty($patientDFN) || empty($startDate) || empty($endDate))
			throw new Exception("Patient ID, start/end dates are not allowed to be empty.");

		$params = array(
			"patientDFN" => $patientDFN,
			"startDate" => $startDate,
			"endDate" => $endDate
		);
        $result = $this->CURL($params, 'DSIHTE/MED_GetPatientMedList');
        return $result;
    }

    /**
	 * Cancel Medication Order
	 * @param: orderIEN, providerIEN, locationIEN, electronicSignature, reasonForCanceling
	 * @return: 
	 */
	public function cancelMedicationOrder($params)
	{
		if(empty($params))
			throw new Exception("Order, Provider, Location, User Electronic Signature, and reason for cancelation are not allowed to be empty.");

		$result = $this->CURL($params, 'DSIHTE/MED_CancelMedicationOrder');
		return $result;
	}

    /**
     * Search medication by string
     * @return: list of medications array (medIen, medName, medType)
     */
    public function searchMed($search)
    {
        if(empty($search))
            throw new Exception('Search string is empty.');
        $result = $this->CURL(array("search"=>$search), 'DSIHTE/MED_MedicationSearch');
        return $result;
    }    

    /**
     * Get patient med. service connected status.
     */
    public function getMedServiceConnectedStatus($patient)
    {
    	if(empty($patient))
    		throw new Exception("Patient not allowed to be empty.");
		$result = $this->CURL(array("patientIEN"=>$patient),'DSIHTE/MED_ServiceConnectedStatus');
		return $result;
    }

    /**
     * Get patient med. service connected prompt data.
     */
    public function getMedServiceConnectedPrompt($params)
    {
		// {
		//   "patientDFN": "741",
		//   "orderList": ["19141","19142"]
		// }
    	if(empty($params))
    		throw new Exception("Patient, APPT. Date and Location not allowed to be empty.");
		$result = $this->CURL($params,'DSIHTE/MED_ServiceConnectedPrompts');
		return $result;
    }

    /**
     * Get patient service connected status.
     */
    public function getServiceConnectedStatus($patient)
    {
    	if(empty($patient))
    		throw new Exception("Patient not allowed to be empty.");
		$result = $this->CURL(array("patientDFN"=>$patient),'DSIHTE/PCE_ServiceConnectedText');
		return $result;
    }

    /**
     * Get patient service connected prompt data.
     */
    public function getServiceConnectedPrompt($params)
    {
    	if(empty($params))
    		throw new Exception("Patient, APPT. Date and Location not allowed to be empty.");
		$result = $this->CURL($params,'DSIHTE/PCE_ServiceConnectedPrompt');
		return $result;
    }

	public function GetPatientSurgeryList($patient_id)
	{
        if ( $patient_id == '' )
			throw new Exception( 'Blank Patient Id Supplied.' );
		
		$params = array();
        $params['patient'] = $patient_id;
        
		$result = $this->CURL($params,'DSIHTE/CPRS_GetPatientSurgeryList');
		return $result;
	}
    
    /**
     * Get Lab Samples
     */
    public function LabGetAllSamples()
    {
    	$params = array();
		$result = $this->CURL($params,'DSIHTE/CPRS_LabGetAllSamples');
		return $result;
    }
    
    /**
     * Get Lab default Dialog depending on location
     */
    public function LabGetDialogDefaults($params = array())
    {
        if( empty($params) )
            throw new Exception( 'Empty array supplied to Get Lab default Dialog.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_LabGetDialogDefaults');
		return $result;
    }

    //Orders/Labs
    public function LabGetOrderableLabs($params = array())
	{
        if( empty($params) )
            throw new Exception( 'Empty array supplied to Get Orderable Labs.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_LabGetOrderableLabs');
		return $result;
	}
    
    public function LabSave($params = array())
	{
        if( empty($params) )
            throw new Exception( 'Empty array supplied to Lab Save.' );
        
		$result = $this->CURL($params,'DSIHTE/CPRS_LabSave');
		return $result;
	}
    
	
	
}

