<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 namespace App\Http\Controllers;

use App\User;
use App\PostOpPhysician;
use App\PostOpPhysicianMedications;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PostOpPhysicianController extends Controller {

    public function PostOpPhysician_data(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $data = [];
        $status = 0;
        $savedResponse = [];
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $scoringDetails = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                
                $data = $this->postop_data($pConfirmId);
                
                $status = 1;
                $message = 'Post Op Physician Orders';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                     ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PostOpPhysician_save(Request $request) {

        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patientId = $request->json()->get('patientId') ? $request->json()->get('patientId') : $request->input('patientId');
        $patientId = (int)$patientId;

        $data = [];
        $status = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                
                $arr = [];
                $arr['patient_confirmation_id'] = $pConfirmId;
                $postOpPhysOrderId =  $request->json()->get('postOpPhysOrderId')?$request->input('postOpPhysOrderId'):"";

                $med_data = $request->json()->get('med_data') ?$request->input('med_data'):"";
                
                $arr['patientAssessed'] = $request->json()->get('patientAssessed') ? $request->input('patientAssessed'):"";
                $arr['vitalSignStable'] = $request->json()->get('vitalSignStable') ?$request->input('vitalSignStable'):"";
                $arr['postOpEvalDone'] = $request->json()->get('postOpEvalDone') ? $request->input('postOpEvalDone'):"";
                $arr['postOpInstructionMethodWritten'] = $request->json()->get('postOpInstructionMethodWritten') ? $request->input('postOpInstructionMethodWritten'):"";
                $arr['postOpInstructionMethodVerbal'] = $request->json()->get('postOpInstructionMethodVerbal') ? $request->input('postOpInstructionMethodVerbal'):"";
                $arr['postOpPhyTime'] = $request->json()->get('postOpPhyTime') ? $request->input('postOpPhyTime'):"";
                if( $arr['postOpPhyTime'] ) {
                    $arr['postOpPhyTime'] = $this->setTmFormat($arr['postOpPhyTime']);
                } 
                $arr['patientAccompaniedSafely'] = $request->json()->get('patientAccompaniedSafely') ? $request->input('patientAccompaniedSafely'):"";

                $arr['notedByNurse'] = $request->json()->get('notedByNurse') ? $request->input('notedByNurse'):"";
                //$arr['surgeonId'] = $request->json()->get('surgeonId') ?? $request->input('surgeonId');
                //$arr['nurseId'] = $request->json()->get('nurseId') ?? $request->input('nurseId');
                $arr['comment'] = $request->json()->get('comment') ? $request->input('comment'):"";
                $arr['relivednurse'] = $request->json()->get('relivednurse') ? $request->input('relivednurse'):"";

                $arr['signSurgeon1Status'] = $request->json()->get('signSurgeon1Status') ? $request->input('signSurgeon1Status'):"";
                $arr['signNurse1Status'] = $request->json()->get('signNurse1Status') ? $request->input('signNurse1Status'):"";
                $arr['signNurseStatus'] = $request->json()->get('signNurseStatus') ? $request->input('signNurseStatus'):"";

                if( $arr['signSurgeon1Status'] == 'Yes' ) {
                    $usrArr = User::where('usersId',$userId)->first(['lname','fname','mname']);
                    $arr['signSurgeon1Id'] = (int)$userId;
                    $arr['signSurgeon1FirstName'] = $usrArr->fname;
                    $arr['signSurgeon1MiddleName'] = $usrArr['mname'];
                    $arr['signSurgeon1LastName'] = $usrArr['lname'];
                    $arr['signSurgeon1Status'] = 'Yes';
                    $arr['signSurgeon1DateTime'] = $request->json()->get('signSurgeon1DateTime') ? $request->input('signSurgeon1DateTime'):"";
                    if( $arr['signSurgeon1DateTime'] ) {
                        $arr['signSurgeon1DateTime'] = date('Y-m-d H:i:s', strtotime($arr['signSurgeon1DateTime']));
                    }
                } else  $arr['signSurgeon1Id'] = 0;

                if( $arr['signNurse1Status'] == 'Yes' ) {
                    $usrArr = User::where('usersId',$userId)->first(['lname','fname','mname']);
                    $arr['signNurse1Id'] = (int)$userId;
                    $arr['signNurse1FirstName'] = $usrArr->fname;
                    $arr['signNurse1MiddleName'] = $usrArr['mname'];
                    $arr['signNurse1LastName'] = $usrArr['lname'];
                    $arr['signNurse1Status'] = 'Yes';
                    $arr['signNurse1DateTime'] = $request->json()->get('signNurse1DateTime') ? $request->input('signNurse1DateTime'):"";
                    if( $arr['signNurse1DateTime'] ) {
                        $arr['signNurse1DateTime'] = date('Y-m-d H:i:s', strtotime($arr['signNurse1DateTime']));
                    }
                } else  $arr['signNurse1Id'] = 0;

                if( $arr['signNurseStatus'] == 'Yes' ) {
                    $usrArr = User::where('usersId',$userId)->first(['lname','fname','mname']);
                    $arr['signNurseId'] = (int)$userId;
                    $arr['signNurseFirstName'] = $usrArr->fname;
                    $arr['signNurseMiddleName'] = $usrArr['mname'];
                    $arr['signNurseLastName'] = $usrArr['lname'];
                    $arr['signNurseStatus'] = 'Yes';
                    $arr['signNurseDateTime'] = $request->json()->get('signNurseDateTime') ? $request->input('signNurseDateTime'):"";
                    if( $arr['signNurseDateTime'] ) {
                        $arr['signNurseDateTime'] = date('Y-m-d H:i:s', strtotime($arr['signNurseDateTime']));
                    }
                }else  $arr['signNurseId'] = 0;

                //'signNurseId','signNurse1Id','signSurgeon1Id'
                $postOpData = PostOpPhysician::where('patient_confirmation_id',$pConfirmId)->first(['form_status','version_num','version_date_time']);
                
                $version_num = $postOpData->version_num;
                if( !$postOpData->version_num )
                {
                    $version_date_time = $postOpData->version_date_time;
                    if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
                    {
                        $version_date_time	=	date('Y-m-d H:i:s');
                    }
                            
                    if($postOpData->form_status == 'completed' || $postOpData->form_status == 'not completed'){
                        $version_num = 1;
                    }else{
                        $version_num	=	3;
                    }
                    
                    $arr['version_num']	= $version_num;
                    $arr['version_date_time'] = $version_date_time;
                }

                $postOpDropExist = $this->manage_med_data($pConfirmId,$med_data,$version_num);

                $arr['form_status'] = 'completed';
                if(( $arr['patientAssessed'] !='Yes' || $arr['vitalSignStable'] !='Yes' ) 
                    ||( $arr['postOpEvalDone'] !='Yes') 
                    ||( $arr['postOpInstructionMethodWritten'] !='Yes' ) 
                    ||( $arr['postOpInstructionMethodVerbal'] !='Yes') 
                    ||( $arr['patientAccompaniedSafely'] !='Yes') 
                    ||( !$arr['signNurseId'])
                    ||( !$arr['signNurse1Id'] && $version_num >2) 
                    ||( !$arr['signSurgeon1Id'] )
                    ||( $postOpDropExist !='Yes')
                    ||( !$arr['notedByNurse'] && $version_num >2)
                    
                ){
                    $arr['form_status'] = 'not completed';
                }
                $data =  $postOpDropExist;

                // UPDATE PATIENT STATUS DISCHARGED
                DB::table('patientconfirmation')->where('patientConfirmationId',$pConfirmId)->update(['patientStatus' => 'Discharged']);
                if( $postOpPhysOrderId ) {
                    PostOpPhysician::where('postOpPhysicianOrdersId',$postOpPhysOrderId)->update($arr);
                }
                else {
                    PostOpPhysician::insert($arr);
                }

                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patientId;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'post_op_physician_order_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
    
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'post_op_physician_order_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = DB::table('chartnotes_change_audit_tbl')->where($conditionArr)->get();
                if($chkAuditStatus) {
                    //MAKE AUDIT STATUS MODIFIED
                    $arrayStatusRecord['status'] = 'modified';
                }else {
                    //MAKE AUDIT STATUS CREATED
                    $arrayStatusRecord['status'] = 'created';
                }
                DB::table('chartnotes_change_audit_tbl')->insert($arrayStatusRecord);												
                //CODE END TO SET AUDIT STATUS AFTER SAVE

                $status=1;
                $message=" Saved Successfully !";
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    private function postop_data($pConfirmId = '') {
        $data = [];
        $pConfirmId = (int)$pConfirmId;
        if( $pConfirmId ) {
            
            $data = $this->getExtractRecord('postopphysicianorders', 'patient_confirmation_id', $pConfirmId);

            if(!$data->version_num && ($data->form_status == 'completed' || $data->form_status == 'not completed')) 
                $data->version_num = 1; 
            elseif(!$data->version_num && ($data->form_status <> 'completed' && $data->form_status <> 'not completed')) 
                $data->version_num = 3;

                $data = $this->unset_keys($data);
                $data->local_anes_sig = $this->local_anes_sig($pConfirmId);

                if( $data->form_status <> 'completed' && $data->form_status <> 'not completed' ) {
                    $data->med_data = $this->template_data($pConfirmId);
                }
                else {
                    $data->med_data = $this->postop_patient_med_data($pConfirmId, $data->form_status);
                }
            

           $data->drop_down_med = $this->load_med_dropdown();
           $data->drop_down_rnurse = $this->load_rnurse_dropdown();
        }
        return $data;
    }

    private function unset_keys($data = []) {
        $unset_keys = ['postOpPhysicianOrdersTime', 'prefilMedicationStatus', 'prefilAdditionalStatus', 'surgeonSign', 'nurseSign', 
                        'patientToTakeHome', 'patientToTakeHomeOther','ascId', 'progressNotes', 'patient_confirmation_id', 'eposted', 'physician_order_date_time'];
        
        if( is_object($data) ) {
            foreach($unset_keys as $key)
                if( isset($data->{$key}) ) 
                    unset($data->{$key});
        }
        return $data;
    }

    private function postop_patient_med_data($pConfirmId = '', $form_status = '') {
        $pConfirmId = (int)$pConfirmId;
        $data = [];
        if( $pConfirmId ) {
            $condArr = [];
            $condArr['confirmation_id'] = $pConfirmId ;
            $condArr['chartName'] = 'post_op_physician_order_form';
            $pOrderData = $this->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_name','ASC');
            
            if( $pOrderData ){
                foreach($pOrderData as $pOrderRow) {
                    $time = ($pOrderRow->physician_order_date_time <> '00:00:00') ? $this->getTmFormat($pOrderRow->physician_order_date_time) : '' ;
                    $med_type = "";
                    if( $pOrderRow->physician_order_type ) {
                        $med_type = $pOrderRow->physician_order_type=="order" ? "order" : "medication";   
                    }
                    else if($form_status == "" || ($form_status != "" && $pOrderRow->physician_order_name == "") ) {
                        $med_type = "medication";
                    }
                    $data[] = ['recordId'=>$pOrderRow->recordId, 'physician_order_name'=>$pOrderRow->physician_order_name,'physician_order_time'=>$time,'physician_order_type' => $med_type];
                }
			}
        }
        return $data;
    }

    private function local_anes_sig($pConfirmId = ''){
        $pConfirmId = (int)$pConfirmId;
        $data = [];
        if( $pConfirmId ) {
            $qry = "SELECT signAnesthesia3Id,signAnesthesia3FirstName,signAnesthesia3MiddleName,signAnesthesia3LastName,signAnesthesia3Status,signAnesthesia3DateTime, date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat FROM localanesthesiarecord WHERE confirmation_id = '".$pConfirmId."'";
            return DB::selectone($qry);

        }
        return $data;
    }

    private function template_data($pConfirmId = '') {
        $pConfirmId = (int)$pConfirmId;
        $data = [];
        if( $pConfirmId ) {
            // get patient confirmation details
            $ptConfirmData = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
            $pri_proc_id = (int)$ptConfirmData->patient_primary_procedure_id; 
            $sec_proc_id = (int)$ptConfirmData->patient_secondary_procedure_id; 
            $ter_proc_id = (int)$ptConfirmData->patient_tertiary_procedure_id;
            $surgeonId = (int)$ptConfirmData->surgeonId;

            // get surgeon profile
            $profile = $this->load_surgeon_profile($surgeonId,$pri_proc_id,'a.postOpDrop');
            if( !$profile ) {
                // load procedure profile if surgeon profile not found;
                $profile = $this->load_procedure_profile($pri_proc_id, $sec_proc_id, $ter_proc_id, 'postOpDrop');
            } 
            $patientToTakeHome = $profile->postOpDrop;
            $seperator = ',';
            if( !$patientToTakeHome ) {
                $patientToTakeHome = $this->getDefault('postopdrops','name',"@@");
                $seperator = '@@';
            }

            $data = $this->process_admin_med_data($patientToTakeHome,$seperator);
        }
        return $data;
    }

    private function load_surgeon_profile($surgeon_id,$pri_proc_id, $fields = '*') {
        $surgeon_id = (int)$surgeon_id;
        $pri_proc_id = (int)$pri_proc_id;
        $data = [];
        
        if( $surgeon_id && $pri_proc_id ) {

            $fields = trim($fields); $fields = $fields ?$fields:'*';
            $qry = "SELECT ".$fields." FROM surgeonprofile a, surgeonprofileprocedure b WHERE a.surgeonId = ".$surgeon_id." AND b.procedureId = '".$pri_proc_id."' AND a.surgeonProfileId = b.profileId AND a.del_status = '' ";
            $data = DB::selectone($qry);
        } 
        return $data;
    }

    private function load_procedure_profile($pri_proc_id, $sec_proc_id = '', $ter_proc_id = '', $fields = '*') {
        $pri_proc_id = (int)$pri_proc_id;
        $sec_proc_id = (int)$sec_proc_id;
        $ter_proc_id = (int)$ter_proc_id;
        $data = [];
        
        if( $pri_proc_id ) {
            $proceduresArr = [$pri_proc_id,$sec_proc_id,$ter_proc_id];
            $fields = trim($fields); 
            $fields = $fields?$fields:'*';	
            foreach($proceduresArr as $procedureId)
            {
                if($procedureId)
                {	
                    $qry = "Select ".$fields." From procedureprofile Where procedureId = '".$procedureId."' and save_status <> '0' AND save_date <> '0000-00-00 00:00:00'  ";
                    $response = DB::selectone($qry);
                    if( $response ) {
                        $data = $response;    
                        break; 
                    }
                }
            } 
        }
        return $data;
    }

    private function process_admin_med_data($patientToTakeHome,$seperator = ',') {
        
        $seperator = $seperator ?$seperator: ',';
        $data = [] ;
        if( $patientToTakeHome ) {
            $pOrderData	= explode($seperator,$patientToTakeHome);
            foreach($pOrderData as $pOrderRow)
            {
                $data[] = ['physician_order_name'=>$pOrderRow, 'physician_order_time'=>'','physician_order_type' => 'medication'];
            }

        }
        return $data;
    }

    private function load_med_dropdown () {

        return DB::table('patient2takehome')
                ->orderBy('name','Asc')
                ->where('deleted', '0')->pluck('name');

    }

    private function load_rnurse_dropdown(){
        return DB::table('users')
                ->select(DB::raw("usersId, TRIM(CONCAT(lname,', ',fname,' ',mname)) as full_name") )
                ->orderBy('lname','Asc')
                ->where('user_type', 'Nurse')->get();
    }

    private function manage_med_data($pConfirmId,$med_data,$version_num) {

        //return ( is_array($med_data) && count($med_data) > 0 ) ? 'TRUE' : 'FALSE';

        $is_exists =  '';
        if ( is_array($med_data) && count($med_data) > 0 ) {
            $phyOrdersAdded = '';
            foreach($med_data as $med) {
                
                $recordId = (int)$med['recordId'];
                $orderName	=	trim($med['physician_order_name']);
                $orderTime	=	$med['physician_order_time'];
                $orderType	=	trim($med['physician_order_type']);

                if( $orderName ) $is_exists = 'Yes';

                if( $orderName )
                {
                    if($orderTime)
                    {
                        $orderTime = $this->setTmFormat($orderTime);
                    }
                    else 
                    {
                        $orderTime			= '00:00:00';
                        if($version_num <  2) {
                            $is_exists = "";	
                        }
                    }
                    
                    $dataArray	=	array();
                    $dataArray['confirmation_id'] = $pConfirmId ;
                    $dataArray['chartName']= 'post_op_physician_order_form';
                    $dataArray['physician_order_name'] = $orderName;
                    if($version_num <  2) {
                        $dataArray['physician_order_time'] = $orderTime;
                    }
                    $dataArray['physician_order_type'] = $orderType;
                    
                    if($recordId)
                    {
                        if(trim($orderName) && trim($orderType)=="medication") {
                            unset($chkMedArr);
                            $chkMedArr['recordId'] = $recordId;
                            $chkMedArr['physician_order_type'] = trim($orderType); 
                            $chkMedRecords = PostOpPhysicianMedications::where($dataArray)->get();
                            if(count($chkMedRecords) <= 0) {
                                $phyOrdersAdded = "yes";	
                            }
                        }
                        
                        PostOpPhysicianMedications::where('recordId',$recordId)->update($dataArray);
                    }
                    else
                    {
                        $chkRecords	= PostOpPhysicianMedications::where($dataArray)->get();
                        if( count($chkRecords) <= 0 ) 
                        {
                            $dataArray['physician_order_location'] = 'post_op_physician_orders';
                            $dataArray['physician_order_date_time'] = date("Y-m-d H:i:s");
                            
                            PostOpPhysicianMedications::insert($dataArray);	
                            if( $orderType == "medication" ) {
                                $phyOrdersAdded = "yes";
                            }
                        }
                    }
                }
                else
                {
                    if($recordId)
                    {
                        PostOpPhysicianMedications::delete($recordId);	
                    }
                }
            }

            //START CODE - IF ANY NEW PHY-ORDER ADDED AND FLAG OF POST-NURSING CHART IS ALREADY GREEN THEN MARK THIS FLAG AS RED FOR NURSE.
            if($phyOrdersAdded == "yes") {//IF ANY NEW PHY-ORDER ADDED THEN
                DB::table('postopnursingrecord')->where(['confirmation_id'=>$pConfirmId,'form_status'=>'completed'])->update(['form_status' => 'not completed' ]);
            }
            //END CODE - IF ANY NEW PHY-ORDER ADDED AND FLAG OF POST-NURSING CHART IS ALREADY GREEN THEN MARK THIS FLAG AS RED FOR NURSE.

        }
        
        return $is_exists;
    }
    

}
