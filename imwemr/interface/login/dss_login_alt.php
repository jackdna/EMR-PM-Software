<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
$ignoreAuth = true;
require_once("../../config/globals.php");

$loginiDoc = false;
$errorMsg = '';

if(isset($_POST['signindss']) && isDssEnable()) {
    $req_duz = isset($_REQUEST['DUZ']) ? xss_rem($_REQUEST['DUZ']) : '';
    $req_patient = isset($_REQUEST['DFN']) ? xss_rem($_REQUEST['DFN']) : '';
    try {
        if(empty($req_duz)==false && $req_duz!="") {

            $params = array();
            $params['accessCode'] = isset($_POST['access_code']) ? xss_rem($_POST['access_code']) : '';
            $params['verifyCode'] = isset($_POST['verify_code']) ? xss_rem($_POST['verify_code']) : '';

            // Call DSS AUTH API to generate API Token
			include_once( $GLOBALS['srcdir'].'/dss_api/dss_core.php' );
            $objDss = new Dss_core($params);

            // Check DUZ after API authentication, return false if not matched.
            if($req_duz !== $_SESSION['dss_loginDUZ'])
                throw new Exception('Error: DUZ not matched with the API Response.');

            // Check user as per the DUZ in IMW
            $sqlCreds = "SELECT `id`, `password`, `username` FROM `users` WHERE `sso_identifier`='".$_SESSION['dss_loginDUZ']."'";
            $respCreds = imw_query( $sqlCreds );
			if( $respCreds && imw_num_rows($respCreds) > 0 )
            {
                $respCreds = imw_fetch_assoc($respCreds);
                $_POST['u_n'] = $respCreds['username'];
                $_POST['p_w'] = $respCreds['password'];
                
                $sqlfac = "SELECT `id` FROM `facility` WHERE `facility_type`='1' ";
                $sqlfacrs = imw_query( $sqlfac );
                if( $sqlfacrs && imw_num_rows($sqlfacrs) > 0 )
                {
                    $hqFacRow = imw_fetch_assoc($sqlfacrs);
                    $_POST['l_facility'] = $hqFacRow['id'];
                }

                $loginiDoc = true;
            } else {
                throw new Exception('Error: User not exists in iDoc as per given DUZ');
            }
        } else {
            throw new Exception('Error: DUZ is required to login');
        }
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    }



    if($loginiDoc==true && isset($req_patient) && empty($req_patient)==false && $req_patient!="") {
        // Load patient as per the given DFN
        try {
            require_once(dirname(__FILE__) . "/../../library/dss_api/dss_demographics.php");

            $patientDFN = $req_patient;
            $objDss = new Dss_demographics();
            $data = $objDss->PT_GetDemographics($patientDFN);

            if( is_null( $data ) )
                throw new Exception('Error: No record found for the patient DFN " '.$req_patient.' " on server.');

            if ((empty($data) == false && isset($data['resultCode']) && $data['resultCode'] == '-1' && $data['errorDescription'] != '') || (isset($data[0]['name']) && $data[0]['name'] == '-1,No match found for lookup value: ' . $req_patient)) {
                if($data[0]['name']){$data['errorDescription'] = $data[0]['name'];}
                throw new Exception('Error: ' . $data['errorDescription']);
            }
            
            $sqlPatient = 'INSERT INTO ';
            $otherFields = ', `created_by`=\''.imw_real_escape_string($_SESSION["authId"]).'\'';
            $whereCondition = '';

            // Check if patient with same patient DFN / MRN already exists in IMW Database
            $sqlPatientCheck = 'SELECT `id` FROM `patient_data` WHERE `External_MRN_5`=\''.$patientDFN.'\'';
            $respPatientCheck = imw_query( $sqlPatientCheck );

            if( $respPatientCheck && imw_num_rows( $respPatientCheck ) > 0 )
            {
                $pid = imw_fetch_assoc( $respPatientCheck );
                $pid = $pid['id'];

                $whereCondition = ' WHERE `id`='.$pid;
                $otherFields = '';

                $sqlPatient = 'UPDATE ';
            }

            // Patient Name
            $patient_name = explode(',', $data[0]['name']);

            if ( preg_match('/\s/',$patient_name[1]) ) {
                $patient_fm_name = explode(' ', $patient_name[1]);

                $patient_first_name = !empty($patient_fm_name) ? $patient_fm_name[0] : '';
                $patient_middle_name = !empty($patient_fm_name) ? $patient_fm_name[1] : '';
            } else {
                $patient_first_name = $patient_name[1];
                $patient_middle_name = '';
            }

            // Patient Gender
            $gender = '';
            if($data[0]['sex'] == 'M') $gender = 'Male';
            if($data[0]['sex'] == 'F') $gender = 'Female';

            // Patient DOB
            $dob = explode(';', $data[0]['dateOfBirth']);
            $dob = date( 'Y-m-d', strtotime($dob[1]) );

            // SSN
            $ssn = explode(';', $data[0]['ssn']);
            $ssn = $ssn[1];

            // Race / Ethnicity
            $raceData = explode(',',$data[0]['race']);
            $race = $raceData[0];
            $ethnicity = $raceData[1];

            // zipcode / zip_ext
            $zip = explode('-', $data[0]['zipCode']);
            $postal_code = $zip[0];
            $zip_ext = $zip[1];

            /*Add / Update Query*/
            $sqlPatient .= '`patient_data` SET
                            `External_MRN_5`=\''.imw_real_escape_string($patientDFN).'\',
                            `lname`=\''.imw_real_escape_string($patient_name[0]).'\',
                            `fname`=\''.imw_real_escape_string($patient_first_name).'\',
                            `mname`=\''.imw_real_escape_string($patient_middle_name).'\',
                            `sex`=\''.imw_real_escape_string($gender).'\',
                            `DOB`=\''.imw_real_escape_string($dob).'\',
                            `ss`=\''.imw_real_escape_string($ssn).'\',
                            `street`=\''.imw_real_escape_string($data[0]['street1']).'\',
                            `street2`=\''.imw_real_escape_string($data[0]['street2']).'\',
                            `city`=\''.imw_real_escape_string($data[0]['city']).'\',
                            `state`=\''.imw_real_escape_string($data[0]['state']).'\',
                            `postal_code`=\''.imw_real_escape_string($postal_code).'\',
                            `zip_ext`=\''.imw_real_escape_string($zip_ext).'\',
                            `phone_home`=\''.imw_real_escape_string(core_phone_format(getNumber($data[0]['homePhone']))).'\',
                            `phone_biz`=\''.imw_real_escape_string(core_phone_format(getNumber($data[0]['workPhone']))).'\',
                            `race`=\''.imw_real_escape_string($race).'\',
                            `otherEthnicity`=\''.imw_real_escape_string($ethnicity).'\',
                            `status`=\''.imw_real_escape_string($data[0]['maritalStatus']).'\',
                            `dod_patient`=\''.imw_real_escape_string($data[0]['dateOfDeath']).'\'
                            '.$otherFields.$whereCondition;
            // pre($sqlPatient);

            $resp = imw_query( $sqlPatient );

            if( $whereCondition === '' )
            {
                $pid  = imw_insert_id();
                imw_query("UPDATE `patient_data` SET `pid`='".$pid."' WHERE `id`='".$pid."'");
            }

            $_POST['idoc_pt_id'] = $pid;
            $_POST['External_MRN_5'] = $req_patient;
            $_POST['dss_login'] = true;
					
        } catch(Exception $e) {
            $errorMsg = $e->getMessage();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Login </title>
    <link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-32x32.png" sizes="32x32"> 
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/loginpage.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/fonts/font-awesome.css" rel="stylesheet">

 	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .so_message{
            width: 100%;
            height: 600px;
            overflow: auto;
            background-color: #FFFFFF;
            white-space: pre-wrap;
            padding: 0px 0px 0px 30px;
            margin: 15px 0;
        }
    </style>
</head>
  <body class=<?php echo ($loginiDoc == false)?"loginpage":''; ?>>
<?php if($loginiDoc == false) { ?>
	<div class="container-fluid">
		<div class="mainlogbx">
        	<div class="loginmid">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                            $apiurl='';
                            $sql = 'SELECT `url` FROM `dss_credentials` WHERE `id`=1';
                            $resp = imw_query($sql);
                            if($resp && imw_num_rows($resp)>0)
                            {
                                $credsData = imw_fetch_assoc($resp);
                                $apiurl = urldecode($credsData['url']);
                            }
                            try {
                                $request_headers = array();
                                $request_headers[] = 'Accept:application/json';
                                $request_headers[] = 'Content-Type:application/json';

                                $url = $apiurl.'auth/SOMESSAGE';

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* Return the response */
                                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                                curl_setopt($ch, CURLOPT_FAILONERROR, false);
                                curl_setopt($ch, CURLOPT_HEADER, false); /* Include header in Output/Response */
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

                                 // Execute Curl Request
                                $result = curl_exec($ch);
                                // Get data response code
                                $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                if( $response_code !== 200 )
                                    throw new Exception( 'API Error: Service Unavailable' );
                                curl_close($ch);
                            } catch (Exception $e) {
                                echo '<p class="so_message text-center">'.$e->getMessage().'</p>';
                            }
                        ?>
                        <div class="clearfix"></div>
                        <?php if($result){ echo '<pre class="so_message">'.$result.'</pre>'; } ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="loginaccount text-center">
                        <span class="loginicon"></span>
                        	<h2 style="font-size:36px;margin-bottom: 40px;">Login To Your Account </h2>
                        </div>
                    	<div class="clearfix"></div>
                        <div class="loginform" style="padding-bottom:40px;">
                           
                            <form id="dss_login_form" method="post" name="dss_login_form" autocomplete="off">
                                <div class="group">      
                                  <input type="password" id="access_code" name="access_code" required >
                                  <span class="highlight"></span>
                                  <span class="bar"></span>
                                  <label>Access Code</label>
                                </div>
                                  
                                <div class="group">      
                                  <input type="password" id="verify_code" name="verify_code" required >
                                  <span class="highlight"></span>
                                  <span class="bar"></span>
                                  <label>Verify Code</label>
                                </div>

                              	<div class="text-center">
								  <button type="submit" class="signinbut hvr-rectangle-out" name="signindss" >SIGN IN</button>
								  </div>
                            </form>
                            <div class="clearfix"></div>
                            <div class="lognote"><span>Please Note:</span> "Login with your own Credentials only."</div>
                            <div class="clearfix"></div>

                        </div>
                    </div>
                </div>
			</div>
            <div class="copytxt">
                Copyrights &copy; 2021 - <?php echo date('Y');?> yourcompanyname and year <?php echo constant('PRODUCT_VERSION');?> <?php echo constant('PRODUCT_VERSION_DATE');?><br>
                <a href="javascript:void(0);" onClick="legal_pop('login_legal.php?pg=legal-doc&doc=privacy&defaultProduct=<?php echo $ObjSecurity->default_product; ?>',200);">Our Privacy Statement</a> | 
                <a href="javascript:void(0);" onClick="legal_pop('login_legal.php?pg=legal-doc&doc=copyright&defaultProduct=<?php echo $ObjSecurity->default_product; ?>',700);">Copyright Notice</a> | 
                <a href="javascript:void(0);" onClick="legal_pop('login_legal.php?pg=legal-doc&doc=softLic&defaultProduct=<?php echo $ObjSecurity->default_product; ?>',700);">License</a>
            </div>
        </div>
	</div>
<?php } ?>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins -->
<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript">

function legal_pop(url, h){
	window.open(url, '', 'width=700, height=' + h + 'px, left=200, top=100, toolbar=0, location=0, statusbar=0, menubar=0');
}

var errorMsg = '<?php echo $errorMsg ?>';
if(errorMsg != "") {
	top.fAlert(errorMsg);
}

</script>
  </body>
</html>

<?php 
if($loginiDoc == true) {
	include_once( dirname(__FILE__).'/../login/index.php' );
}
?>