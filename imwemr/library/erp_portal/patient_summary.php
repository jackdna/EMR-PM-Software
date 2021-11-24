<?php
ini_set("memory_limit","3072M");

include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class Patient_summary extends ERP_portal_core
{
	
    public function __construct($arr=array())
    {
        parent::__construct($arr);
    }
    
	/*
	 * CLINICAL SUMMARY API HIT MAIN FILE WORK STARTS HERE
	 * PATIENT CLINICAL SUMMARY CCDA CREATED AND SEND TO imwemr PORTAL
	 * DATA MAPPED WITH imwemr PORTAL
	 * addUpdatePatientSummary CALLED IN /class/work_view/ChartNoteSaver.php 
	 * CALLED API: http://api.mveportal.com/Help/Api/POST-api-Hl7CDAs
	 */	
    public function addUpdatePatientSummary($ptSummArr = array(), $formId = 0)
	{
	    if (count($ptSummArr) > 0) 
		{
            $result = $this->CURL($ptSummArr, 'api/Hl7CDAs', 'POST');
            if ($result) 
			{
                $qry = "UPDATE chart_master_table SET erp_chart_id='".$result['id']."' WHERE id=".$formId." ";
                imw_query($qry);
            }
        }
    }
	
	
	public function mergePatients($data = array())
	{
	    if (count($data) > 0) 
		{
            $result = $this->CURL($data, 'api/MergePatients', 'POST');
        }
    }
}