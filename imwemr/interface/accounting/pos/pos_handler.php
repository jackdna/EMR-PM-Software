<?php
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../../config/globals.php");

if(isset($_REQUEST['method']) && $_REQUEST['method']!='') {

    switch($_REQUEST['method']) {
        case 'set_user_device' :
            $operator_id=$_SESSION['authId'];
            $tsys_device_id=trim($_POST['tsys_device_id']);
            $login_facility=$_SESSION['login_facility'];
            
            set_pos_cookie($tsys_device_id,$operator_id,$login_facility);

            break;
        case 'get_default_device' :
            $operator_id=$_SESSION['authId'];
            $login_facility=$_SESSION['login_facility'];
            get_default_device($operator_id,$login_facility,$phpHTTPProtocol);

            break;
        case 'save_pos_log' :
            $operator_id=$_SESSION['authId'];
            $tsys_device_id=trim($_POST['tsys_device_id']);
            $login_facility=$_SESSION['login_facility'];
            
            savePosTransationLog($tsys_device_id,$operator_id,$login_facility);

            break;
        case 'refNum':
            $data=array();
            $data['tsys_patient_id']=$_POST['tsys_patient_id'];
            $data['posMachine']=$_POST['posMachine'];

            $operator_id=$_SESSION['authId'];
            $tsys_device_id=trim($_POST['tsys_device_id']);
            $login_facility=$_SESSION['login_facility'];
            
            set_pos_cookie($tsys_device_id,$operator_id,$login_facility);
            
            createReferenceNumber($data);
            break;
        case 'ordNum':
            $data=array();
            $data['tsys_patient_id']=$_POST['tsys_patient_id'];
            $data['tsys_trans_mode']=$_POST['tsys_trans_mode'];
            $data['referenceNumber']=$_POST['referenceNumber'];
         
            createOrderNumber($data);
            break;
        case 'partial_void_id':
            $data=array();
            $data['referenceNumber']=$_POST['referenceNumber'];
            $data['laneId']=$_POST['laneId'];

            getpartial_void_id($data);
            break;
        case 'possale':
            $data=array();
            $data['scheduID']=$_POST['scheduID'];
            $data['encounter_id']=$_POST['encounter_id'];
            $data['transactionAmount']=$_POST['totalAmt'];
            $data['structuredResponse']=$_POST['structuredResponse'];
            $data['laneId']=$_POST['laneId'];
            $data['referenceNumber']=$_POST['referenceNumber'];
            $data['tsys_payment_type_log_id']=$_POST['tsys_payment_type_log_id'];
            
            save_pos_trans_response($data);
            break;
        
    }
}


function savePosTransationLog($tsys_device_id,$operator_id,$login_facility) {
    $data=array();
    $transaction_amount=($_POST['totalAmt']/100);
    $data['transactionAmount']=$transaction_amount;
    $data['laneId']=$_POST['laneId'];
    $data['scheduID']=$_POST['scheduID'];
    $data['encounter_id']=$_POST['encounter_id'];
    $data['transactionType']=$_POST['transactionType'];
    $data['transactionNumber']=(isset($_POST['transactionNumber']) && $_POST['transactionNumber']!='')?$_POST['transactionNumber']:'';
    $data['referenceNumber']=$_POST['referenceNumber'];
    $data['tsysOrderNumber']=(isset($_POST['tsysOrderNumber']) && is_array($_POST['tsysOrderNumber']) && $_POST['tsysOrderNumber']!='')?json_encode($_POST['tsysOrderNumber']):'';
    $data['device_id']=$_POST['tsys_device_id'];
    $data['device_url']=$_POST['posUrl'];
    $data['final_hex_string']=$_POST['final_b64'];
    $data['operator_id']=$operator_id;
    $data['merchant_id']=0;
    $data['facility_id']=$login_facility;
    
    $devices_sql="Select tsys_merchant.id as merchant_id, tsys_device_details.id as device_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND tsys_device_details.id='".$tsys_device_id."' 
              AND merchant_status=0 
              ";
    $resp = imw_query($devices_sql);
    if ($resp && imw_num_rows($resp) > 0) {
        $row = imw_fetch_assoc($resp);
        $data['merchant_id']=$row['merchant_id'];
    }
    
    $updatesql='UPDATE tsys_payment_type_log SET transactionAmount="'.$data['transactionAmount'].'", laneId="'.$data['laneId'].'", scheduID="'.$data['scheduID'].'" 
                , encounter_id="'.$data['encounter_id'].'", transactionType="'.$data['transactionType'].'", transactionNumber="'.$data['transactionNumber'].'"
                , tsysOrderNumber="'.$data['tsysOrderNumber'].'", device_id="'.$data['device_id'].'", device_url="'.$data['device_url'].'", final_hex_string="'.$data['final_hex_string'].'"
                , operator_id="'.$data['operator_id'].'", merchant_id="'.$data['merchant_id'].'", facility_id="'.$data['facility_id'].'"
                WHERE refrenceNumber="'.$data['referenceNumber'].'" ';
    imw_query($updatesql);
    echo imw_affected_rows();
}


function set_pos_cookie($tsys_device_id,$operator_id,$login_facility) {
    $cookieName="imedicwareposdevice_".$operator_id;
            
    if(isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName]!='') {
        $poscookie=json_decode($_COOKIE[$cookieName],true);
        if(empty($poscookie)==false && is_array($poscookie)){
            if($poscookie['login_facility']==$login_facility) {
                if($poscookie['user_id']==$operator_id) {
                    if($poscookie['device_id']==$tsys_device_id) {
                        if($poscookie['expire_time']<=time()) {
                            unset($_COOKIE[$cookieName]);
                        }
                    }
                    if($poscookie['device_id']!=$tsys_device_id) {
                        unset($_COOKIE[$cookieName]);
                    }
                }
            }
        }
    }
    
    $value=array();
    $value['device_id']=$tsys_device_id;
    $value['user_id']=$operator_id;
    $value['login_facility']=$login_facility;

    $expire_time=time()+3600*24;
    $value['expire_time']=$expire_time;
    $posvalue= json_encode($value);
    if(!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName]=='') {
        $res = setcookie($cookieName, $posvalue, $expire_time, '/');
    }

}

function getpartial_void_id($data) {
    $laneId=$data['laneId'];
    switch($laneId) {
        case '10000003':
            $update_table='patient_pre_payment';
            break;
        case '10000001':
        case '10000002':
            $update_table='check_in_out_payment';
            break;
        case '10000004':
            $update_table='patient_charges_detail_payment_info';
            break;
    }
    if(empty($data)==false) {
        $added_on_time = date('Y-m-d H:i:s');
        $sql='Select id from '.$update_table.' where log_referenceNumber="'.$data['referenceNumber'].'" ';
        $rs=imw_query($sql);
        $refRow=imw_fetch_assoc($rs);
        $ref_void_id=$refRow['id'];

        $params=array();
        $params=array();
        //$params['referenceNumber']=$referenceNumber;
        $params['ref_void_id']=$ref_void_id;
        echo json_encode($params);
        die;
    }
    
}

function createReferenceNumber($data) {
    $referenceNumber=false;

    if(empty($data)==false) {
        $added_on_time = date('Y-m-d H:i:s');
        $sql='INSERT INTO tsys_payment_type_log (patient_id,posMachine,added_on) VALUES ("' . $data['tsys_patient_id'] . '", "' . $data['posMachine'] . '", "' . $added_on_time . '") ';
        imw_query($sql);
        $refInsert_id=imw_insert_id();
        
        $referenceNumber=$data['tsys_patient_id'].'*'.$refInsert_id;
        if($refInsert_id && $referenceNumber) {
            $updatesql='UPDATE tsys_payment_type_log SET refrenceNumber="'.$referenceNumber.'" WHERE id="'.$refInsert_id.'" ';
            imw_query($updatesql);
        }
        $params=array();
        $params['referenceNumber']=$referenceNumber;
        $params['refInsert_id']=$refInsert_id;
        echo json_encode($params);
        die;
    }
}

function createOrderNumber($data) {
    $orderNumber=false;
    if(empty($data)==false) {
        $referenceNumber=$data['referenceNumber'];
        if($referenceNumber) {
            $orderNumber=$data['tsys_trans_mode'].$referenceNumber;
            $updatesql='UPDATE tsys_payment_type_log SET orderNumber="'.$orderNumber.'" WHERE refrenceNumber="'.$referenceNumber.'" ';
            imw_query($updatesql);
        }
        $params=array();
        $params['orderNumber']=$orderNumber;
        echo json_encode($params);
        die;
    }
}


function save_pos_trans_response($response = array(), $imw_trans_id=0) {
    $scheduID=$response['scheduID'];
    $encounter_id=$response['encounter_id'];
    $laneId=$response['laneId'];
    $tsys_void_id=$response['tsys_void_id'];
    $referenceNumber=$response['referenceNumber'];
    $tsys_payment_type_log_id=$response['tsys_payment_type_log_id'];
    
    $result=$response['structuredResponse'];
    
    $patient_id = $_SESSION['patient'];
    $operator_id = $_SESSION['authId'];
    
    if (empty($response) == false) {
        $return['status'] = $result['status'];
        $return['ResponseCode'] = $result['ResponseCode'];
        $return['ResponseMessage'] = $result['ResponseMessage'];
        $return['TransactionType'] = $result['TransactionType'];
        $transactionNumber=$response['transactionNumber'];
        if($result['ResponseCode'] == 100003 || $result['ResponseCode'] == 100002 || (isset($result['TraceInformation'][0]) && $result['TraceInformation'][0]=='') || (isset($result['TraceInformation']['TransactionNumber']) && $result['TraceInformation']['TransactionNumber']=='') ) {
            $data = array();
            $data['ResponseMessage'] = $return['ResponseMessage'];
            echo json_encode($data);
            die;
        }
        $transactionAmount=$result['AmountInformation'][0];
        $transactionAmount=$transactionAmount/100;
        $return['transactionAmount'] = $transactionAmount;
        
        $return['HostInformation'] = addslashes(json_encode($result['HostInformation'], JSON_FORCE_OBJECT));
        $return['AmountInformation'] = addslashes(json_encode($result['AmountInformation'], JSON_FORCE_OBJECT));
        $return['AccountInformation'] = addslashes(json_encode($result['AccountInformation'], JSON_FORCE_OBJECT));
        $return['TraceInformation'] = addslashes(json_encode($result['TraceInformation'], JSON_FORCE_OBJECT));
        $return['AVSinformation'] = addslashes(json_encode($result['AVSinformation'], JSON_FORCE_OBJECT));
        $return['CommercialInformation'] = addslashes(json_encode($result['CommercialInformation'], JSON_FORCE_OBJECT));
        $return['motoEcommerce'] = addslashes(json_encode($result['motoEcommerce'], JSON_FORCE_OBJECT));
        $return['AdditionalInformation'] = addslashes(json_encode($result['AdditionalInformation'], JSON_FORCE_OBJECT));
        
        $return['TransactionNumber'] = $result['TraceInformation'][0];
        $return['Account'] = $result['AccountInformation'][0];
        $return['ExpireDate'] = $result['AccountInformation'][2];
        $return['CardType'] = $result['AccountInformation'][6];
        
        $return['HostResponseCode'] = $result['HostInformation'][0];
        $return['HostResponseMessage'] = $result['HostInformation'][1];
        $return['AuthCode'] = $result['HostInformation'][2];
        $return['HostReferenceNumber'] = $result['HostInformation'][3];
        $return['TraceNumber'] = $result['HostInformation'][4];
        $return['BatchNumber'] = $result['HostInformation'][5];
        
        $return['TimeStamp'] = $result['TraceInformation'][2];
        
        $return['MOTOType'] = '';
        $return['MOTOOrderNumber'] = '';
        if(isset($result['motoEcommerce'][0]) && isset($result['motoEcommerce'][3]) && $result['motoEcommerce'][0]!='' && $result['motoEcommerce'][3]!='' ) {
            $return['MOTOType'] = $result['motoEcommerce'][0];
            $return['MOTOOrderNumber'] = $result['motoEcommerce'][3];
        }

        $token = '';
        if(isset($result['AdditionalInformation'][0]) && $result['AdditionalInformation'][0]!='' && strpos($result['AdditionalInformation'][0],'TOKEN=')!==false ) {
            $return['token'] = $result['AdditionalInformation'][0];
            $token=str_replace('TOKEN=','',$return['token']); //TOKEN=Gm6JbuM3H4Uu5439
        }
        
        $response_time = date('Y-m-d H:i:s');
        $return['added_on'] = $response_time;

        $sql = 'INSERT INTO tsys_possale_transaction (patient_id,operator_id,scheduID,encounter_id,laneId,transactionAmount,
                HostInformation,AmountInformation,AccountInformation,TraceInformation,
                AVSinformation,CommercialInformation,motoEcommerce,AdditionalInformation,
                TransactionNumber,Account,ExpireDate,CardType,HostResponseCode,
                HostResponseMessage,AuthCode,HostReferenceNumber,TraceNumber,BatchNumber,
                TimeStamp,added_on,status,ResponseCode,ResponseMessage,TransactionType,log_referenceNumber,tsys_payment_type_log_id,
                motoType,motoOrderNumber,token)
        VALUES ("' . $patient_id . '", "' . $operator_id . '","' . $scheduID . '","' . $encounter_id . '","' . $laneId . '", "' . $return['transactionAmount'] . '", 
                 "' . $return['HostInformation'] . '", "' . $return['AmountInformation'] . '", "' . $return['AccountInformation'] . '", "' . $return['TraceInformation'] . '",
                "' . $return['AVSinformation'] . '", "' . $return['CommercialInformation'] . '", "' . $return['motoEcommerce'] . '", "' . $return['AdditionalInformation'] . '",
                "' . $return['TransactionNumber'] . '", "' . $return['Account'] . '", "' . $return['ExpireDate'] . '", "' . $return['CardType'] . '", "' . $return['HostResponseCode'] . '",
                "' . $return['HostResponseMessage'] . '", "' . $return['AuthCode'] . '", "' . $return['HostReferenceNumber'] . '", "' . $return['TraceNumber'] . '", "' . $return['BatchNumber'] . '",
                "' . $return['TimeStamp'] . '", "' . $return['added_on'] . '", "' . $return['status'] . '", "' . $return['ResponseCode'] . '", "' . $return['ResponseMessage'] . '",
                "' . $return['TransactionType'] . '","' . $referenceNumber . '","' . $tsys_payment_type_log_id . '",
                "' . $return['MOTOType'] . '", "' . $return['MOTOOrderNumber'] . '", "' . $token . '")';

        $status=imw_query($sql);
        $posInsertId = imw_insert_id();
        if ($posInsertId) {
            
            $sql1status=false;
            if($transactionNumber) {
                $sql1='update tsys_possale_transaction set TransactionType="'.$return['TransactionType'].'" where TransactionNumber="'.$transactionNumber.'" ' ;
                $sql1status=imw_query($sql1);
            }
            switch ($return['CardType']) {
                case '01':
                    $CardType = 'Visa';
                    break;
                case '02':
                    $CardType = 'MC';
                    break;
                case '03':
                    $CardType = 'AX';
                    break;
                case '04':
                    $CardType = 'Dis';
                    break;
                case '99':
                    $CardType = 'Other';
                    break;
                default:
                    $CardType = 'Other';
                    break;
            }
            $TransactionType=trim($return['TransactionType']);
            $transactionID=$return['TransactionNumber'];
            $transactionAmount=$return['transactionAmount'];
            $TimeStamp=date('m-d-Y h:i A', strtotime($return['added_on']));
            switch($laneId) {
                case '10000003':
                    $update_table='patient_pre_payment';
                    break;
                case '10000001':
                case '10000002':
                    $update_table='check_in_out_payment';
                    break;
                case '10000004':
                    $update_table='patient_charges_detail_payment_info';
                    break;
            }
            $voidType=array("16","17","18","19","20","21","22");
            $btn_html='';
            $message='';
            $sale_html .= '<tr>
                <td>' . $TimeStamp . '</td>
                <td>' . $transactionID . '</td>
                <td>' . $patient_id . '</td>
                <td>$' . $transactionAmount . '</td>
                <td>' . $return['Account'] . '</td>
                <td>' . $return['ExpireDate'] . '</td>
                <td>' . $CardType . '</td>
                <td>' . $return['ResponseMessage'] . '</td>';
                if($TransactionType=="01") {
                    $message='Transaction done successfully.';
                    if($return['HostResponseMessage']=='Partially Approved')
                        $message='Transaction '.$return['HostResponseMessage'].'.';
                    $sale_html.='<td class="chngbtn_'.$transactionID.'"><button type="button" class="btn btn-primary" id="returnbtn_'.$transactionID.'" onclick="return_transaction(\''.$transactionID.'\',\''.$transactionAmount.'\',\'02\',this)">Return</button></td>';
                    $sale_html.='<td class="chngbtn_'.$transactionID.'"><button type="button" class="btn btn-primary" id="voidbtn_'.$transactionID.'" onclick="void_transaction(\''.$transactionID.'\',\'16\',this)">Void</button></td>';
                } else if($TransactionType=="02") {
                    if($tsys_void_id) {
                        //$log_referenceNumber=$patient_id.'*'.$transactionNumber;
                        $void_sql='update '.$update_table.' set tsys_status="RETURNED" where id="'.$tsys_void_id.'" ' ;
                        $voidstatus=imw_query($void_sql);
                    }
                    $message='Return successful.';
                    $sale_html.='<td>Returned</td>';
                    $btn_html.='<td>Returned</td>';
                } else if(in_array($TransactionType,$voidType)) {
                    if($tsys_void_id) {
                        //$log_referenceNumber=$patient_id.'*'.$transactionNumber;
                        $void_sql='update '.$update_table.' set tsys_status="VOIDED" where id="'.$tsys_void_id.'" ' ;
                        $voidstatus=imw_query($void_sql);
                    }
                    $message='Void done.';
                    $sale_html.='<td>Voided</td>';
                    $btn_html.='<td>Voided</td>';
                }
            $sale_html.='</tr>';
            
            $card_details_str=$CardType.'~~'.$return['Account'].'~~'.$return['ExpireDate'];

            $data = array();
            $data['TransactionNumber'] = $return['TransactionNumber'];
            $data['message'] = $message;
            $data['sale_html'] = $sale_html;
            $data['card_details_str'] = $card_details_str;
            $data['transactionNumber'] = '';
            if($transactionNumber && $sql1status) {
                $data['transactionNumber'] = $transactionNumber;
                $data['btn_html'] = $btn_html;
            }
            
            echo json_encode($data);
            die;
        }
    }
}



function get_default_device($operator_id,$login_facility,$phpHTTPProtocol) {
    $cookieName="imedicwareposdevice_".$operator_id;
    if(isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName]!='') {
        $poscookie=json_decode($_COOKIE[$cookieName],true);
        if(empty($poscookie)==false){
            if($poscookie['login_facility']==$login_facility) {
                if($poscookie['user_id']==$operator_id) {
                    if($poscookie['expire_time']<=time()) {
                        unset($_COOKIE[$cookieName]);
                    } else {
                        $defaultDevice=$poscookie['device_id'];
                    }
                }
            }
        }
    }
    
    $devicesArr=array();
    $devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
                  JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
                  WHERE device_status=0 
                  AND tsys_device_details.facility_id='".$login_facility."' 
                  AND merchant_status=0 
                  ";
    $resp = imw_query($devices_sql);
    $counter=0;
    $device_url='';
    if ($resp && imw_num_rows($resp) > 0) {
        while ($row = imw_fetch_assoc($resp)) {
            $counter++;
            $ipAddress=$row['ipAddress'];
            $port=$row['port'];
            $selected='';
            if( (!$defaultDevice || $defaultDevice=='no_pos_device') && $counter==1) {
                $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
            } else {
                if($row['d_id']==$defaultDevice){
                    $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;   
                }
            }
        }
        echo $device_url;       
        die;
    }
}

?>