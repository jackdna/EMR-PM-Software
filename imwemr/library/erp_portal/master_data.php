<?php
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");
set_time_limit(0);
class Master_data extends ERP_portal_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

   
    /*
     * Creates and maintains of a list of patient races in Eye Reach Patients.
     * POST api/PatientRaces
     */
    public function sync_races($params=array()) 
    {
        $result = $this->CURL($params,'api/PatientRaces', 'POST');
        
		return $result;
    }
    
    
    /*
     * Creates and maintains of a list of patient medication in Eye Reach Patients.
     * POST api/Medications
     */
    public function sync_medication_master($params=array())
    {
        $result = $this->CURL($params,'api/Medications', 'POST');
        
        return $result;
    }
    
    
    /*
     * Creates and maintains of a list of routes in Eye Reach Patients.
     * POST api/Medications
     */
    public function sync_route_master($params=array())
    {
        $result = $this->CURL($params,'api/MedicationRoutes', 'POST');
        
        return $result;
    }
    
    /*
     * Delete of a list of patient medication in Eye Reach Patients.
     * POST api/Medications
     */
    public function delete_medication_master($params=array(),$externalId) 
    {
        $result = $this->CURL($params, 'api/Medications?externalId='.$externalId, 'DELETE');
        return $result;
    }

    /*

     * Creates and maintains of a list of allergy severity.

     * POST api/AllergySeverities

     */

    public function sync_severity($params=array())
    {
        $result = $this->CURL($params,'api/AllergySeverities', 'POST');
        return $result;
    }
    
    /*
     * Creates and maintains of a list of patient ethnicities in Eye Reach Patients.
     * POST api/PatientEthnicities
     */
    public function sync_ethnicity($params=array()) 
    {
        $result = $this->CURL($params, 'api/PatientEthnicities', 'POST');
        
		return $result;
    }

    /*
     * Creates and maintains of a list of patient allergy in Eye Reach Patients.
     * POST api/Allergies
     */
    public function sync_allergy($params=array()){
        $result = $this->CURL($params, 'api/Allergies', 'POST');
        
		return $result;
    }

    public function sync_allergy_delete($params=array(),$externalId) 
    {
        $result = $this->CURL($params, 'api/Allergies?externalId='.$externalId, 'DELETE');
        
		return $result;
    }

    
    /*
     * Creates and maintains of a list of marital statuses in Eye Reach Patients.
     * POST api/PatientMaritalStatuses
     */
    public function sync_marital_status($params=array()) 
    {
        $result = $this->CURL($params, 'api/PatientMaritalStatuses', 'POST');
        
		return $result;
    }
    
    
    /*
     * Create a new patient sex or update an existing patient sex in the Eye Reach Patients list.
     * POST api/PatientSexes
     */
    public function sync_sexes($params=array()) 
    {
        $result = $this->CURL($params, 'api/PatientSexes', 'POST');
        
		return $result;
    }
    
        
    /*
     * 
     * 
     */
    public function sync_language($params=array()) 
    {
        return false;
    }
    
    
    /*
     * Create a new patient relation or update an existing patient relation in the Eye Reach Patients list.
     * POST api/PatientRelationships
     */
    public function sync_pt_relations($params=array()) 
    {
        $result = $this->CURL($params, 'api/PatientRelationships', 'POST');
        
		return $result;
    }
    
    public function sync_pt_relations_delete($params=array(),$externalId) 
    {
        $result = $this->CURL($params, 'api/PatientRelationships?externalId='.$externalId, 'DELETE');
        
		return $result;
    }

    /*

     * Creates and maintains of a list of surgery.

     * POST api/api/Surgeries

     */
    public function sync_surgery($params=array())
    {
        $result = $this->CURL($params,'api/Surgeries', 'POST');
        return $result;
    }

}

