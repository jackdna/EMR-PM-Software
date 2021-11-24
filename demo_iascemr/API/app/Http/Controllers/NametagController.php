<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NametagController extends Controller {

    public function nametagformElement(Request $request) {
        $userToken = $request->input('user_token');
        $dos = $request->input('dos');
        $showAllApptStatus = $request->input('showAllApptStatus');
        $userType = $request->input('userType');
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $series = [];
        $message = " unauthorized ";
        $validUser = $this->checkToken($userToken);
        if ($validUser) {
            $userTypeQry = "SELECT fname, mname, lname, 
			user_type,coordinator_type, practiceName FROM users
			WHERE usersId = '$loginUser'";
            $userTypeRes = imw_query($userTypeQry);
            $userTypeRows = imw_fetch_array($userTypeRes);
            $surgeonLoggedFirstName = trim(stripslashes($userTypeRows['fname']));
            $surgeonLoggedMiddleName = trim(stripslashes($userTypeRows['mname']));
            $surgeonLoggedLastName = trim(stripslashes($userTypeRows['lname']));
            $userType = $userTypeRows['user_type'];
            $coordinatorType = $userTypeRows['coordinator_type'];
            $practiceName = stripslashes($userTypeRows['practiceName']);
            $user_type = $userTypeRows['user_type'];
// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.

            $andCancelledQry = " AND  stub_tbl.patient_status!='Canceled' ";
            if ($userType == 'Coordinator' && $coordinatorType != 'Master') { //IF USER TYPE IS Coordinator AND HE IS NOT MASTER THEN SHOW RECORD RELATED TO HIS PRACTICENAME
                $stub_tbl_group_query = "select stub_tbl.*, DATE_FORMAT(stub_tbl.patient_dob,'%m/%d/%Y') as patient_dob_format 
							FROM stub_tbl,users  
							WHERE stub_tbl.dos = '" . $selected_date . "' 
							AND users.practiceName='" . addslashes($practiceName) . "' 
							AND users.practiceName!='' 
							AND users.fname=stub_tbl.surgeon_fname 
							AND users.lname=stub_tbl.surgeon_lname 
							" . $andCancelledQry . $fac_con . "
							ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time";
            } elseif ($userType == 'Surgeon') {
                $stub_tbl_group_query = "select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where stub_tbl.dos = '" . $selected_date . "' " . $andCancelledQry . " AND stub_tbl.surgeon_fname ='" . addslashes($surgeonLoggedFirstName) . "' AND stub_tbl.surgeon_lname = '" . addslashes($surgeonLoggedLastName) . "' " . $fac_con . " ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time";
            } else {
                $stub_tbl_group_query = "select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where stub_tbl.dos = '" . $selected_date . "' " . $andCancelledQry . $fac_con . " ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time";
            }
            $message = " status updated ";
            $status = 1;
            for ($i = 1; $i < 30; $i++) {
                if ($i > 5)
                    $i += 4;
                $series[] = $i;
            }
            $data['User'] = $validUser;
            $data['series'] = $series;
        }



        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
