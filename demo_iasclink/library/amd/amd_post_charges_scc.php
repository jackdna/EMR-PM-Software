<?php

/**
 * Only for Surgical Care Center
 * List of medicare payers that need the institutional changes to populate on a HCFA in AdvanceMD
 */
$insurance_company = array(
    'WPS J8 Medicare Part B',
    'IU Health Plans',
    'IU Medicare Choice',
    'IU Medicare Select/Select Plus',
    'Aetna Medicare',
    'Allwell Medicare'
);
$insurance_company = strtolower(implode('\',\'',$insurance_company));

$qry = "select id from insurance_data where patient_id = (select patient_id from patient_data_tbl where amd_patient_id = '".$patientId."') AND type = 'primary' and actInsComp='1' AND LOWER(ins_provider) IN ('".$insurance_company."')  order by id";

$result = imw_query($qry);

if( imw_num_rows($result) > 0 ) {

    $this->parameters = array( 'ppmdmsg' => array(
        "usercontext" => $this->userContext,
        "@nocookie" => '1',
        "@action" => "updvisitwithquickcharges",
        "@class" => "onlinechargeslips",
        "@msgtime" => date('m/d/Y h:i:s A'),
        "@patientid" => (string)$patientId,
        "@approval" => "1",
        "@respparty" => $respParty,
        "@insorder" => (string)$insOrder,
        "@acceptassign" => "1",
        "visit" => array(
            "@id" => $callVisitId,
            "@date" => $visitDate,
            "@force" => "1",
            "@profile" => $providerIds[$key],
            "@facility" => $facId,
            "@note" => $chargeNote,
            "@insorder" => (string)$insOrder,
            "chargelist" => array(
                    "charge"=>$chargeList
                )
            )
        )
    );

    $result = self::CURL($this->appURL, $this->parameters);

    /*Log Query Response*/
    $logData =  $key."\n".$visitId.' - '.$callVisitId."\n".$visitDate."\n".json_encode($this->parameters)."\n\n".$result."\n";
    $logData .= "=======================================================\n";
    file_put_contents(dirname(__FILE__).'/data/'.$log_file_name, $logData, FILE_APPEND);

    $result = json_decode($result);

    $sqlLog = '';
    if(isset($result->PPMDResults->Error->Fault))
    {
        $_SESSION['amd_charge_error'][strtoupper($key)] = $result->PPMDResults->Error->Fault->detail->description;

        $sqlLog = "INSERT INTO `amd_charges_log` SET
            `pt_id`='".$patientId."',
            `amd_visit_id`='".$callVisitId."',
            `status`='0',
            `reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
            `date_posted`='".date('Y-m-d H:i:s')."',
            `m_amd_visit_id`='".$visitId."',
            `type`='4'";
    }
    else
    {
        $chargeid = $result->PPMDResults->Results->{'@chargevalue'};
        $sqlLog = "INSERT INTO `amd_charges_log` SET
            `pt_id`='".$patientId."',
            `amd_visit_id`='".$callVisitId."',
            `status`='1',
            `reason`='".$chargeid."',
            `date_posted`='".date('Y-m-d H:i:s')."',
            `m_amd_visit_id`='".$visitId."',
            `type`='4'";
    }
    if( $sqlLog != '' )
        imw_query($sqlLog);

} else {

    /*Currently this block is not being used*/
    $this->parameters = array( 
        'ppmdmsg' => array(
        "usercontext" => $this->userContext,
        "@nocookie" => '1',
        "@action" => "savecharges",
        "@class" => "onlinechargeslips",
        "@msgtime" => date('m/d/Y h:i:s A'),
        "@patientid" => (string)$patientId,
        "@approval" => "1",
        "@respparty" => $respParty,
        "visit" => array(
            "@id" => $callVisitId,
            "@date" => $visitDate,
            "@force" => "1",
            "@profile" => $providerIds[$key],
            "@facility" => $facId,
            "chargelist" => array(
                    "charge"=>$chargeList
                )
            )
        )
    );

    $result = self::CURL($this->appURL, $this->parameters);

    /*Log Query Response*/
    $logData =  $key."\n".$visitId.' - '.$callVisitId."\n".$visitDate."\n".json_encode($this->parameters)."\n\n".$result."\n";
    $logData .= "=======================================================\n";
    file_put_contents(dirname(__FILE__).'/data/'.$log_file_name, $logData, FILE_APPEND);
    $result = json_decode($result);

    $sqlLog = '';
    if(isset($result->PPMDResults->Error->Fault))
    {
        $_SESSION['amd_charge_error'][strtoupper($key)] = 	$result->PPMDResults->Error->Fault->detail->description;

        $sqlLog = "INSERT INTO `amd_charges_log` SET
            `pt_id`='".$patientId."',
            `amd_visit_id`='".$callVisitId."',
            `status`='0',
            `reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
            `date_posted`='".date('Y-m-d H:i:s')."',
            `m_amd_visit_id`='".$visitId."',
            `type`='".$log_entry_type."'";
    }
    else
    {
        $chargeid = $result->PPMDResults->Results->{'@chargevalue'};
        $sqlLog = "INSERT INTO `amd_charges_log` SET
            `pt_id`='".$patientId."',
            `amd_visit_id`='".$callVisitId."',
            `status`='1',
            `reason`='".$chargeid."',
            `date_posted`='".date('Y-m-d H:i:s')."',
            `m_amd_visit_id`='".$visitId."',
            `type`='".$log_entry_type."'";
    }
    if( $sqlLog != '' )
        imw_query($sqlLog);

}
?>