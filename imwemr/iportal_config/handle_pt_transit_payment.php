<?php
$ignoreAuth=true;
include_once("../config/globals.php");
/* error_reporting(E_ALL);
ini_set('display_errors', -1); */
include_once("../library/classes/payments/class.transitAPI.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	die("[Error]:401 Unauthorized Access ");
}

$returnVal = false;
try{
    $portalParms=array();
    $portalParms['patient_id']=$_REQUEST['patient_id'];
    $portalParms['operator_id']=$_REQUEST['providerID'];
    $portalParms['facilityID']=$_REQUEST['facilityID'];
    $portalParms['tsys_device_id']=$_REQUEST['tsys_device_id'];
    
    $laneID='20000001';
	$transitObj = new TransitAPI($laneID, $portalParms['tsys_device_id'],$portalParms);
        
    if($_REQUEST['manifest']=='manifest'){
        $returnVal=$transitObj->encryptManifest();
    } else {
        $referenceNumber=$transitObj->createReferenceNumber($_REQUEST['patient_id']);
        if($transitObj){
            $transaction_amount = $_REQUEST['amount'];
            
            $card_data=array();
            $card_data['referenceNumber']=$referenceNumber;
            $card_data['card_source']=$card_source='INTERNET';
            $card_data['cardNumber']=base64_decode($_REQUEST['cardNumber']);
            $card_data['expirationDate']= str_replace('/', '', base64_decode($_POST['expirationDate']));
            $card_data['pos_cvv2']= base64_decode($_REQUEST['cvv']);
            if(isset($_REQUEST['pos_tsep_token']) && $_REQUEST['pos_tsep_token']!='') {
                //$card_data['card_source']=$card_source='INTERNET';
                $card_data['cardNumber']=$_REQUEST['pos_tsep_token'];
                $card_data['expirationDate']= str_replace('/', '', $_POST['expirationDate']);
                $card_data['pos_cvv2']= $_REQUEST['cvv'];
            }
                        
            $otherParams=array();
            $otherParams['referenceNumber']= $referenceNumber;
            $otherParams['cardDataSource']=$card_source;
            $otherParams['laneID']=$laneID;
            $otherParams['pos_cvv2']=$card_data['pos_cvv2'];
            $otherParams['creditCardtype']=$_REQUEST['cc_type'];
            $otherParams['patient_name']=$_REQUEST['patient_name'];
            
            //$returnVal = $transitObj->saleTransaction($_REQUEST,$transaction_amount, $currency_code = 'USD', $card_source, $params);
            $returnVal = $transitObj->saleTransaction($card_data, $transaction_amount, $currency_code="USD", $card_source, $otherParams);
        }
    }
} catch(Exception $e){ }

echo json_encode($returnVal);
die;


function createReferenceNumber($ptId) {
	$referenceNumber=false;
	if(empty($ptId)==false) {
        $added_on_time = date('Y-m-d H:i:s');
        $sql='INSERT INTO tsys_payment_type_log (patient_id,posMachine,added_on) VALUES ("' . $ptId . '", "ABSENT", "' . $added_on_time . '") ';
        imw_query($sql);
        $refInsert_id=imw_insert_id();
        $referenceNumber=$ptId.'*'.$refInsert_id;
        if($refInsert_id && $referenceNumber) {
            $updatesql='UPDATE tsys_payment_type_log SET refrenceNumber="'.$referenceNumber.'" WHERE id="'.$refInsert_id.'" ';
            imw_query($updatesql);
        }
    }
	
	return $referenceNumber;
}

?>