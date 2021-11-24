<?php
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class AccountSettings extends ERP_portal_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

    /*
     * Add/Update Account Settings at portal 
     * POST api/AccountSettings
     */
    public function addUpdateSettings($params=array()) 
    {
        $result = $this->CURL($params,'api/AccountSettings', 'POST');
        
        return $result;
    }


    /*
    *
    * get Account Settings from portal
    *
    */
    public function getAccountSettings() 
    {
        $params = array();
        $result = $this->CURL($params, 'api/AccountSettings', 'GET');
        
        return $result;
    }
}

