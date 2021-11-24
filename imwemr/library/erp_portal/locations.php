<?php
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class Locations extends ERP_portal_core
{
	
	public function __construct()
	{
        parent::__construct();
	}

    /*
     * Add/Update Locations at portal 
     * POST api/Locations
     */
    public function addUpdateLocation($params=array()) 
    {
        
        $r = $this->getLocation( array('externalId'=>$params['externalId']) );

        if( is_array($params) )
        {
            // static parameters as in imwemr these fields are not supported
            $params['sundayClosed'] =  $r['sundayClosed'] ?? true;
            $params['sundayFrom'] =  $r['sundayFrom'] ?? "00:00:00";
            $params['sundayTo'] = $r['sundayTo'] ?? "00:00:00";
            $params['mondayClosed'] = $r['mondayClosed'] ?? false;
            $params['mondayFrom'] = $r['mondayFrom'] ?? "09:00:00";
            $params['mondayTo'] = $r['mondayTo'] ?? "17:00:00";
            $params['tuesdayClosed'] = $r['tuesdayClosed'] ?? false;
            $params['tuesdayFrom'] = $r['tuesdayFrom'] ?? "09:00:00";
            $params['tuesdayTo'] =  $r['tuesdayTo'] ?? "17:00:00";
            $params['wednesdayClosed'] = $r['wednesdayClosed'] ?? false;
            $params['wednesdayFrom'] = $r['wednesdayFrom'] ?? "09:00:00";
            $params['wednesdayTo'] = $r['wednesdayTo'] ?? "17:00:00";
            $params['thursdayClosed'] = $r['thursdayClosed'] ?? false;
            $params['thursdayFrom'] = $r['thursdayFrom'] ?? "09:00:00";
            $params['thursdayTo'] = $r['thursdayTo'] ?? "17:00:00";
            $params['fridayClosed'] = $r['fridayClosed'] ?? false;
            $params['fridayFrom'] = $r['fridayFrom'] ?? "09:00:00";
            $params['fridayTo'] = $r['fridayTo'] ?? "17:00:00";
            $params['saturdayClosed'] = $r['saturdayClosed'] ?? true;
            $params['saturdayFrom'] = $r['saturdayFrom'] ?? "00:00:00";
            $params['saturdayTo'] = $r['saturdayTo'] ?? "00:00:00";
            $params['communicationSendingFromHour'] = $r['communicationSendingFromHour'] ?? "09:00:00";
            $params['communicationSendingToHour'] = $r['communicationSendingToHour'] ?? "17:00:00";
            $params['linkedInUrl'] = $r['linkedInUrl'] ?? "";
            $params['facebookUrl'] = $r['facebookUrl'] ?? "";
            $params['googlePlusUrl'] = $r['googlePlusUrl'] ?? "";
            $params['yahooUrl'] = $r['yahooUrl'] ?? "";
            $params['timeZoneId'] = $r['timeZoneId'] ?? "Eastern Standard Time";
            $params['requestFormLayoutAfterOnlineAppointment'] = $r['requestFormLayoutAfterOnlineAppointment'] ?? true;
        }

        $result = $this->CURL($params,'api/Locations', 'POST');
        
        return $result;
    }
    
    /*
     * Delete Locations at portal
     * DELETE api/Locations?externalId={externalId}
     */
    public function deleteLocation($externalId) 
    {
        $externalId=(int)$externalId;
        $result = false;
        if( $externalId )
        {
            $params=array();
            $result = $this->CURL($params, 'api/Locations?externalId='.$externalId, 'DELETE');
        }
        return $result;
    }
    
    /*
     * get location details from portal
     * GET api/Locations?externalId={externalId}
     * search locations via (externalId/name/active/city/state)(page/itemsperpage) 
     *  GET api/locations/search?externalId={externalId}&name={name}&active={active}&city={city}&state={state}&page={page}&itemsPerPage={itemsPerPage}
     */
    public function getLocation($request=array()) 
    {
        $externalId = $request['externalId'] ?? '';
        $externalId = (int)$externalId;
       
        $payload = http_build_query($request);
        $endPoint = $externalId ? 'api/Locations?externalId='.$externalId : 'api/locations/search?'. $payload;
        
        $params=array();
        $result = $this->CURL($params, $endPoint, 'GET');
        
		return $result;
    }
}

