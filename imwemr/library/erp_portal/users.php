<?php
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class Users extends ERP_portal_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

    /*
     * VerifyUserUsername
     * GET api/VerifyUserUsername?username={username}
     */
    public function VerifyUserUsername($request=array()) 
    {
        $username=$request['username'];
        $externalId=$request['externalId'];
        
        $params=array();       
		$result = $this->CURL($params, 'api/VerifyUserUsername?username='.$username, 'GET');
        
		return $result;
    }
    
    /*
     * Add/Update user at portal 
     * POST api/Users
     */
    public function addUpdateUser($params=array()) 
    {
        //$params=array();
        $result = $this->CURL($params,'api/Users', 'POST');
        
		return $result;
    }
    
    /*
     * Delete user at portal
     * DELETE api/Users?externalId={externalId}
     */
    public function deleteUser($request=array()) 
    {
        $externalId=$request['externalId'];

        $params=array();
        $result = $this->CURL($params, 'api/Users?externalId='.$externalId, 'DELETE');
        
		return $result;
    }
    
    /*
     * get user details from portal
     * GET api/Users?externalId={externalId}
     */
    public function getUser($request=array()) 
    {
        $externalId=$request['externalId'];
        
        $params=array();
        $result = $this->CURL($params, 'api/Users?externalId='.$externalId, 'GET');
        
		return $result;
    }
    
    /*
     * search user via (username/firstname/lastname/active)(page/itemsperpage) 
     * GET api/users/search?active={active}&username={username}&firstName={firstName}&lastName={lastName}&page={page}&itemsPerPage={itemsPerPage}
     */
    public function searchUsers($request=array()) 
    {
        $payload = http_build_query($request);
        
        $params=array();
        $result = $this->CURL($params, 'api/users/search?'.$payload, 'GET');
        
		return $result;
    }
    
    
}

