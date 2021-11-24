<?php
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class Doctors extends ERP_portal_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

   
    /*
     * Add/Update doctors (physicians) at portal 
     * POST api/Doctors
     */
    public function addUpdateDoctor($params=array()) 
    {
        //$params=array();
        $result = $this->CURL($params,'api/Doctors', 'POST');
        
		return $result;
    }
    
    /*
     * Delete user at portal
     * DELETE api/Doctors?externalId={externalId}
     */
    public function deleteDoctor($request=array()) 
    {
        $externalId=$request['externalId'];

        $params=array();
        $result = $this->CURL($params, 'api/Doctors?externalId='.$externalId, 'DELETE');
        
		return $result;
    }
    
    /*
     * get doctor details from portal
     * GET api/Doctors?externalId={externalId}
     */
    public function getDoctor($request=array()) 
    {
        $externalId=$request['externalId'];
        
        $params=array();
        $result = $this->CURL($params, 'api/Doctors?externalId='.$externalId, 'GET');
        
		return $result;
    }
    
    /*
     * search Doctor via (username/firstname/lastname/active)(page/itemsperpage) 
     * GET api/doctors/search?fullName={fullName}&active={active}&page={page}&itemsPerPage={itemsPerPage}
     */
    public function searchDoctors($request=array()) 
    {
        $payload = http_build_query($request);
        
        $params=array();
        $result = $this->CURL($params, 'api/doctors/search?'.$payload, 'GET');
        
		return $result;
    }
    
    
}

