<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller {

    /**
     * Retrieve the user details.
     * 
     * @return Response
     */
    function login(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        $deviceId = $request->input('deviceId');
        $devicetoken = $request->input('devicetoken');
        $imei = $request->input('imei');
        $devicetype = $request->input('devicetype');
        $facility = $request->input('facility');
        $message = "Invalid login";
        $data = array();

        if (trim($username) == "" || trim($password) == "") {
            
        } else {
             $database=getenv('DB_DATABASE');
            $users = DB::select("select usersId,userTitle,fname,lname,initial,address,address2,phone,concat(fname,' ',lname) as fullname,locked,user_privileges,admin_privileges"
                            . " hippaReviewedStatus,admin_privileges,hippaReviewedStatus,user_type,session_timeout from ".$database.".users where loginName='" . $username . "' and user_password=password('" . $password . "')");
            $userDetails = [];
            $token = $this->getTokens(5);
            if ($users) {
                $fac = DB::select("select fac_name,fac_id,fac_idoc_link_id from facility_tbl order by fac_name");
                $userDetails['Facility'] = $fac;
                $userDetails['User'] = $users;
                foreach ($users as $user) {
                    $locked = $user->locked;
                    $usersId = $user->usersId;
                    $privileges = $user->user_privileges;
                    $admin_privileges = $user->admin_privileges;
                    $hippaReviewedStatus = $user->hippaReviewedStatus;
                    $loginUserType = $user->user_type;
                    $loginUserSessionTimeout = $user->session_timeout;
                    $loginUserSessionTimeout = ($loginUserSessionTimeout ) ? $loginUserSessionTimeout : (30 * 60);
                    $_SESSION['loginUserName'] = $username;
                    $_SESSION['loginUserId'] = $usersId;
                    $_SESSION['userPrivileges'] = $privileges;
                    $_SESSION['admin_privileges'] = $admin_privileges;
                    $_SESSION['loginUserType'] = $loginUserType;
                    $_SESSION['loginUserSessionTimeout'] = $loginUserSessionTimeout;
                    $_SESSION['session_last_update'] = time();
                    $_SESSION['facility'] = $facility;
                    //start get facility name from facility_tbl table
                    if ($facility <> "") {
                        $getFacilityDetails = DB::select("select fac_name,fac_idoc_link_id from facility_tbl where fac_id='" . $facility . "'");
                        if ($getFacilityDetails) {
                            $_SESSION['loginUserFacilityName'] = $getFacilityDetails->fac_name;
                            $_SESSION['iasc_facility_id'] = $getFacilityDetails->fac_idoc_link_id;
                        }
                    }
                }

                $this->UserLog($users[0]->usersId, $token);
                $this->UserDevice($users[0]->usersId, $devicetype, $deviceId, $devicetoken, $imei);
                $last_loggedIn = DB::select("SELECT createdOn FROM `user_log` where user_id='" . $usersId . "' having createdOn<now() order by createdOn desc limit 1");
                if (!ini_get('date.timezone')) {
                    date_default_timezone_set("America/New_York");
                }
                $last_loggedInDate = $last_loggedIn[0]->createdOn;
                $userDetails['last_loggedIn'] = date("m/d/Y h:i A", strtotime($last_loggedInDate));
                $message = " LoggedIn Successfully !";
                // Set the response and exit
                return response()->json(['status' => 1,
                            'message' => $message, 'data' => $userDetails, 'token' => $token]); // OK (200) being the HTTP response code
            } else {
                // Set the response and exit
                return response()->json([
                            'status' => 0,
                            'message' => $message,
                            'data' => $data,
                ]); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }

    public function generateUserSession(Request $request, $token) {
        $res = DB::select("SELECT user_id FROM user_log where user_token='" . $token . "' and active=1 limit 1");
        if ($res) {
            $database=getenv('DB_DATABASE');
            $users = DB::select("select usersId,userTitle,fname,lname,initial,address,address2,phone,concat(fname,' ',lname) as fullname,locked,user_privileges,admin_privileges"
                            . " hippaReviewedStatus,admin_privileges,hippaReviewedStatus,user_type,session_timeout from ".$database.".users where usersId='" . $res[0]->user_id . "'");
            $userDetails = [];
            if ($users) {
                $fac = DB::select("select fac_name,fac_id,fac_idoc_link_id from facility_tbl order by fac_name");
                $userDetails['Facility'] = $fac;
                $userDetails['User'] = $users;
                foreach ($users as $user) {
                    $locked = $user->locked;
                    $usersId = $user->usersId;
                    $privileges = $user->user_privileges;
                    $admin_privileges = $user->admin_privileges;
                    $hippaReviewedStatus = $user->hippaReviewedStatus;
                    $loginUserType = $user->user_type;
                    $loginUserSessionTimeout = $user->session_timeout;
                    $loginUserSessionTimeout = ($loginUserSessionTimeout ) ? $loginUserSessionTimeout : (30 * 60);
                    $_SESSION['loginUserName'] = $username;
                    $_SESSION['loginUserId'] = $usersId;
                    $_SESSION['userPrivileges'] = $privileges;
                    $_SESSION['admin_privileges'] = $admin_privileges;
                    $_SESSION['loginUserType'] = $loginUserType;
                    $_SESSION['loginUserSessionTimeout'] = $loginUserSessionTimeout;
                    $_SESSION['session_last_update'] = time();
                    $_SESSION['facility'] = $facility;
                    //start get facility name from facility_tbl table
                    if ($facility <> "") {
                        $getFacilityDetails = DB::select("select fac_name,fac_idoc_link_id from facility_tbl where fac_id='" . $facility . "'");
                        if ($getFacilityDetails) {
                            $_SESSION['loginUserFacilityName'] = $getFacilityDetails->fac_name;
                            $_SESSION['iasc_facility_id'] = $getFacilityDetails->fac_idoc_link_id;
                        }
                    }
                }
            }
        }
    }

    private function UserLog($usersId, $token) {
        DB::select("insert into user_log set user_id='" . $usersId . "',user_token='" . $token . "',active=1");
    }

    private function UserDevice($usersId, $devicetype, $deviceId = NULL, $devicetoken = NULL, $imei = NULL) {
        $res = DB::select("select * from device where user_id='" . $usersId . "' and devicetype='" . $devicetype . "'");
        if ($res) {
            // $this->db->query("insert into device set active=1,user_id='".$usersId."',deviceId='".$deviceId."',devicetoken='".$devicetoken."',imei='".$imei."',devicetype='".$devicetype."'");
        } else {
            // $this->db->query("update device set active=0 where user_id='".$usersId."' and devicetype='".$devicetype."'");
            DB::select("insert into device set active=1,user_id='" . $usersId . "',deviceId='" . $deviceId . "',devicetoken='" . $devicetoken . "',imei='" . $imei . "',devicetype='" . $devicetype . "' ");
        }
    }

    /* app token webservice */

    public function getTokens($length) {

        return md5(time());
    }

    /* app token webservice */

    public function getPasswordTokens($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

}
