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
if( isset($_SESSION['REQUIRES_AUTH']) && $_SESSION['REQUIRES_AUTH'] === true )
{
	header('Access-Control-Expose-Headers: REQUIRES_AUTH');
	header('REQUIRES_AUTH:1');
	unset($_SESSION['REQUIRES_AUTH']);
}

require_once("../../library/classes/class.security.php");
require_once("../../library/classes/work_view/ChartPtLock.php");

$ObjSecurity	= new security('login');
$date_time=date('l M d, Y');
$_SESSION["PRACTICE_PATH"]			= constant('PRACTICE_PATH');
function audit_app_start(){
	$arrAuditTrail = array();
	$plSt = array();
	$qryGetAuditPolicies = "SELECT `policy_status` FROM `audit_policies` WHERE `policy_id` = '1' order by `policy_id`";
	$rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
	if($rsGetAuditPolicies){
		if(imw_num_rows($rsGetAuditPolicies) > 0){
			while($row = imw_fetch_array($rsGetAuditPolicies)){
				$plSt[] = $row['policy_status'];
			}
		}
	}

	if($plSt[0] == 1){
		if(!isset($_SESSION['Login_Page_Audit'])){
			$_SESSION['Login_Page_Audit'] = "Noo";
		} 	
		if($_SESSION['Login_Page_Audit'] == "Noo"){
			$_SESSION['Login_Page_Audit'] = "Yes";
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);			
			$arrAuditTrail [] = 
				array(						
						"Table_Name"=>"users",												
						"Action"=> "app_start",												
						"IP"=> $ip,
						"MAC_Address"=> get_mac_add(),
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"Category"=> "login_module",
						"Category_Desc"=> "login"						
					);
	//				echo '<pre>'; print_r($arrAuditTrail);													
			auditTrail($arrAuditTrail,$mergedArray);	
		}								 
	}
}//end of audit_app_start.
audit_app_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login to <?php echo $ObjSecurity->default_product;?></title>
    <link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-32x32.png" sizes="32x32"> 
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/loginpage.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/fonts/font-awesome.css" rel="stylesheet">
    <?php if($ObjSecurity->default_product == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="loginpage">
	<div class="container-fluid">
		<div class="mainlogbx">
        	<div class="loginmid">
                <div class="row">
                    <div class="col-sm-6 loginwelcome">
                        <div class="row logoara">
                            <div class="col-lg-6 col-md-6  col-sm-12"><img src="../../library/images/logo.png" alt=""/></div>
                            <div class="col-lg-6 col-md-6  col-sm-12"><p class="logindate"><?php echo $date_time; ?> <span id="tick2"></span></p>
								<div class="clearfix text-right">
							<a href="#"  target="_blank"><img src="../../library/images/company_logo.png" alt="ECL"></a>	
                           
                           </div>
                            
                            </div>
                        </div>
                    
                        <div class="loginlogo">
                        	<div class="image_con"><img src="<?php echo $ObjSecurity->getHQFacLogo(); ?>" alt=""/></div>
                            <h1>Welcome to the Clinic</h1></div>
						<div class="one_mve_logo">
                            <div class="col-lg-6 col-md-6  col-sm-12">
                            	<a href="http://www.somedomain.com/"  target="_blank">
			                		<?php
										$cur_date = date('Y-m-d');
										if($cur_date <= '2019-05-02') {
											echo '<img src="../../library/images/somelogo.png" alt="">';
										} else if($cur_date <= '2019-07-10') {
											echo '<img src="../../library/images/anotherlogo.png" alt="">';
										} else if($cur_date <= '2019-08-02') {
											echo '<img src="../../library/images/onemorelogo.png" alt="">';
										}
									?>	
                            	</a>

                            </div>
                            <div class="col-lg-6 col-md-6  col-sm-12"></div>
                        </div>						
                    </div>
                    <div class="col-sm-6">
                        <div class="loginaccount text-center">
                        <span class="loginicon"></span>
                        	<h2>Login To Your Account</h2>
                        </div>
                    	<div class="clearfix"></div>
                        <div class="loginform">
                            <form id="login_form" method="post" action="" name="login" onsubmit="return valid_login();" autocomplete="on">
                                <input type="hidden" id="cc_t" name="cc_t">
                                <div class="group">      
                                    <input type="text" id="u_n" name="u_n" required onblur="getfacility_options(this);">
                                  <span class="highlight"></span>
                                  <span class="bar"></span>
                                  <label>User Name</label>
                                </div>
                                  
                                <div class="group">      
                                  <input type="password" id="p_w" name="p_w" required>
                                  <span class="highlight"></span>
                                  <span class="bar"></span>
                                  <label>Password</label>
                                </div>
                                
                                <div class="facility">
                                    <span>Facility</span>
                                    <span class="plain-select"><select class="inp" name="l_facility" id="l_facility"><?php echo $ObjSecurity->getFacilityOptions(); ?></select></span>
                              	</div>
                              	<div class="clearfix text-center"><button type="submit" class="signinbut hvr-rectangle-out">SIGN IN</button></div>
                            </form>
                        </div>
                    	<div class="clearfix"></div>
                    	<div class="lognote"><span>Please Note:</span> "Login with your own Credentials only."</div>
                    	<div class="clearfix"></div>
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

	<div class="clearfix"></div>
	<div class='common_modal_wrapper'>
		<div id="captchaModal" class="modal fade">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
						<h4 id="bsHeader" class="text-center lead">Just to prove you're <kbd>Human</kbd>...</h4>	
					</div>
					<div id="captchaOutput" class="modal-body"></div>
					<div class="modal-footer hide" id="bsFooter">
						<button class="btn btn-small btn-success" data-dismiss="modal" aria-hidden="true">You can close this now</button>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="../../library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap-captcha.js"></script>
<script type="text/javascript" src="../../library/js/mobile-touch.js"></script>  <!-- To enable drag and drop events on touch screens -->

<!--<script type="text/javascript" src="../../library/js/index.js"></script>-->

<script type="text/javascript" src="../../library/js/common.js"></script>
<?php if(constant('HASH_METHOD')=="MD5"){?>
<script type="text/javascript" src="../../library/js/md5.js"></script>
<?php }else{?>
<script type="text/javascript" src="../../library/js/js_crypto_sha256.js"></script>
<?php }?>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript">
<?php 
if($_SESSION["CAPTCHACOUNT"] >= constant("DEFAULT_CAPTCHA_COUNT")){?>get_captcha_block();<?php }?>
var init_actions = true;
var d = document.forms; 
var JS_WEB_ROOT_PATH='<?php echo $GLOBALS['webroot'];?>';

function valid_login(){
	var msg = ''; 
	var callback = ''; 
	var p = d.login.p_w.value; 
	var len = p.length;

	if(d.login.u_n.value == ''){
		msg = 'Please Enter Username.'; 
		callback = dgi('u_n');
	}else if(len < 1){
		msg = 'Please Enter Password.'; 
		callback = d.login.p_w;
	}else if(d.login.l_facility.value==''){
		msg = 'Please Select Facility.'; 
		callback = d.login.l_facility;
	}				
	if(msg != ''){
		top.fAlert(msg,'',callback); 
		msg = '';
		return false;
	}
	<?php if(constant('HASH_METHOD')=="MD5"){?>
	hashed_str=md5(p); 
	<?php }else{?>
	hashed_str=Sha256.hash(p);
	<?php }?>
	if(hashed_str.length!=32 && hashed_str.length!=64){
		top.fAlert('Password encryption failed. Security exception. Can\'t proceed.');
		return false;	
	}
	//check_human();
	d.login.p_w.value=hashed_str;
	d.login.cc_t.value=dgi("tick2").innerHTML;
}

function legal_pop(url, h){
	window.open(url, '', 'width=700, height=' + h + 'px, left=200, top=100, toolbar=0, location=0, statusbar=0, menubar=0');
}

function get_captcha_block(){
	// Init. Captcha block
	$('#captchaOutput').bootstrapCaptcha({
		iconSize: '3x',
		onDrop: function(results){
			if(Boolean(results.valid) === true && Boolean(results.mouseUsed) === true){
				$('#bsHeader').text('Yep, you\'re human');
				$('#bsCaptchaTarget').slideUp();
				setTimeout(function(){$('#bootstrapCaptchaDiv').html('<p class="text-center lead">Plz wait</p><p class="text-center"><img src="../../library/images/loading_image.gif" width="30px" height="auto"></p>');},1500);
				$('#captchaOutput').slideDown('slow');
				$('#login_form').append('<input id="captchaVerfyVal" type="hidden" name="vericode" value="1">');
				setTimeout(function() { document.forms.login.submit(); },2000);
			}else{
				return false;
			}
		}
	});
			
	// Showing captcha block on submit	
	$('#login_form').on('submit',function(e){
		if($('#captchaModal').length > 0){		// If user tries to remove the captcha block
			$('#captchaModal').modal({
				backdrop: 'static',
				keyboard: false
			});
			$('#captchaModal').modal('show');
		}else{
			fAlert('Unauthorize Access. Page reloading');
			setTimeout(function() { window.location.reload(); },500);
		}
		return false;
	});
}

//Author: RM						 
//Purpose: this function getting and setting Client PC name in cookie
function getPCName(){		
	try{
		var name = 'clientPCName';
		var chkCookie = document.cookie.indexOf(name + "=");				
		if (chkCookie==-1){
			//var net = new ActiveXObject("wscript.network");
			var net = new ActiveXObject("WScript.Network");									
			var value = net.ComputerName;
			var date = new Date();
			date.setTime(date.getTime()+(1*24*60*60*1000));				
			var expires = "; expires="+date.toGMTString();
			document.cookie = name+"="+value+expires+"; path=/";					
		}	
	}
	catch(err){
			//Handle errors here
	}				
}

window.onload = function(){
	show_clock();
	document.login.u_n.focus();
	if(init_actions == true){
		<?php if(constant('ACTIVE_X_CLIENT_INFO')=="YES"){?>
			getPCName();
		<?php }?>
		init_actions = false;
	}
}

<?php if($ObjSecurity->class_var['show_alert'] != false && $ObjSecurity->class_var['show_alert'] != ''){?>
	fancyAlert("<?php echo $ObjSecurity->class_var['show_alert'];?>","","document.forms.login.u_n.focus()");
<?php }?>
<?php 
if( isset($_SESSION['OFFICE_HOURS']) && $_SESSION['OFFICE_HOURS'] === true ){
	header('Access-Control-Expose-Headers: OFFICE_HOURS');
	header('OFFICE_HOURS:1');
	unset($_SESSION['OFFICE_HOURS']);?>
    fancyAlert("You are not allowed to login in Off Hours.");
<?php } ?>
    
//IM-6581:- Default location
function getfacility_options(elem) {
    var us_name=$(elem).val() || '';
    if(us_name!='' && us_name!='undefined') {
        var form_data={us_name:us_name};
        $.ajax({
            url:top.JS_WEB_ROOT_PATH + '/interface/core/ajax_handler.php?task=get_defaultfac_options',
            data:form_data,
            type:'POST',
            dataType:'json',
            success:function(response){
                if(response){
                    $('#l_facility').html(response);
                }
            }
        });
    }
}
    
</script>
  </body>
</html>