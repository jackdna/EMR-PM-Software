<?php
/*
 * File: index.php
 * Coded in PHP7
 * Purpose: Login Screen
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 */
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../config/globals.php");
include_once(dirname(__FILE__)."/../fd/library/classes/login_functions.php");

$temp_username	= xss_rem($_POST["username"]);
$temp_pwd		= xss_rem($_POST["pwd"]);
$temp_act		= xss_rem($_POST["act"]);

$u = (isset($temp_username) && !empty($temp_username)) ? trim($temp_username) : '';
$p = (isset($temp_pwd) && !empty($temp_pwd)) ? trim($temp_pwd) : '';
$a = (isset($temp_act) && !empty($temp_act)) ? trim($temp_act) : '';
$hidd_multi_login = xss_rem($_POST["hidd_multi_login"]);
if($a=='logout'){
	app_logout();
	exit;
}
if(!empty($u) && !empty($p)){
	$return = check_wrong_attempts($u,$p,$hidd_multi_login);	
	
	if(!$return[1]){
		if($return[0]=="priv_fd"){
			$script = 'fancyAlert("Permission Denied, Please Contact Administrator.");';
		}else{
			$script = 'fancyAlert("Authentication Failed!");';
		}
	}else{
		list($status,$msg) = app_check_pass_expiry();
		/*$script = '<script type="text/javascript">alert("'.$msg.'");</script>';*/
	}
}
?>
<!DOCTYPE html>
<html><head>

<title>Financial Dashboard Login Page</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width= device-width, initial-scale=1,maximum-scale=1,zoom=1">
    
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/bootstrap-multiselect.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/messi/messi.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/common.css">
    
	<!-- SCript -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/messi/messi.js"></script>
    
    <script type="text/javascript">
		function valid_login(){
			var msg="";
			var obj_focus="";
			if($('#username').val() == ''){
				msg = 'Please Enter Username.<br>'; 
				obj_focus = $('#username');
			}
			if($('#pwd').val() ==''){
				msg+= 'Please Enter Password.'; 
				if(obj_focus==""){
					obj_focus = $('#pwd');
				}
			}
			if(msg!=""){
				fancyAlert(msg,'',obj_focus);
				return false;
			}else{
				return true;
				//$('#login_form').submit();
			}
		}
    	$(document).ready(function(e) {
			 <?php echo $script; ?>
        });
    </script>
   
</head>
<?php
if($status=="logged"){
?>
	<script>
		window.open('<?php echo $GLOBALS['webroot'];?>/fd/interface','Financial','scrollbars=0,resizable=1,status=1,toolbar=0,menubar=0,location=0');
		function closeIT(){
			!(window.ActiveXObject) && "ActiveXObject";
			function isIE11(){
				return !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
			}
			var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
			var ie11 = isIE11();
			if (ie7 || ie11){
				window.open("","_parent",""); 
				window.close(); 
			}else{
				this.focus();
				self.opener = this;
				self.close();
			}
		}
		if(self.name != "Financial"){
			closeIT();
		}
    </script>
<?php
}
?>
<body class="body_bg_jet" >
<Div class="main_main_wrapper">
<Div class="main_wrapper">
	<Div class="header_wrap">
    	<Div class="container-fluid">
          	<!--- CENTER PART -->
            <!--<Div class="centered_header text-center">
          		 <img class="" src="images/dashboard_white.png"> <img class="g_home" src="images/dashboard_green.png"> 
                 <Div class="clearfix"></Div>
                 <h4 class="rob"> FINANCIAL DASHBOARD </h4>
            </Div>-->	
            <div class="header_logo_2">
            	 <img class="" src="images/imedic_logo.svg"> 
            </div>
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
             <div class="row">
               	<div class="col-sm-7"> <Div class="left_header">
                	 <div class="custom_dash_label">
                     	<!--<img class="" src="../images/dashboard_white.png">-->
                    	<span class="single_header_label"> Financial Dashboard </span>
						
                    	
                     </div>
                    	
                </Div></div>	
                 <div class="col-sm-5 text-right"><img class="" src="images/company_logo.png"> </div>
                 </div>
            </Div>
            <!--- CENTER PART -->
        </Div>     
    </Div>	
    <Div class="middle_wrap login_bg_for_md"  style="background:#4d5359;">
    	<div  class="col-md-6 col-lg-4 col-md-offset-3 col-lg-offset-4 col-xs-12 col-sm-6 col-sm-offset-3">
        	<Div class="login_inner">
            	<form name="login_form" id="login_form" method="post" autocomplete="off" onSubmit="return valid_login();">
            		<div class="abs_member_head rob"> Login Form </div>	
                    <Div class="form_wrap">
                        <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-12" for="username"> Username </label>
                        <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                            <Div class="input-group">
                                <div class="input-group-addon btn"><span class="glyphicon glyphicon-user"></span></div>
                                <input class="form-control" id="username" name="username" type="text" />
                             </Div>                    			
                        </div>
                        <Div class="clearfix  margin_clear"></Div>
                        <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-12" for="pwd"> Password </label>
                        <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                            <Div class="input-group">
                                <div class="input-group-addon btn "><span class="glyphicon glyphicon-lock"></span></div>
                                <input class="form-control" id="pwd" name="pwd" type="password" autocomplete="off" />
                             </Div> 
                        </div>
                         <!--<Div class="clearfix  margin_clear"></Div>
                        <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                            <Div class="input-group">
                                <img style="margin:5px 10px;" src="<?php echo $GLOBALS['webroot'];?>/library/inc_captcha.php">
                                <input class="form-control" type="text" name="vericode" id="vericode" maxlength="6"/>
                             </Div> 
                        </div>-->
                    </div>
                  
                <Div class="clearfix  margin_clear"></Div>                
                <Div class="form_wrap text-center">
                	<input type="submit" name="loging_button" id="loging_button" class="rob_btn btn_custom btn_sign_out btn-lg" value="Submit">
                </Div>	
                </form>        	
            </Div>
        </div>
       	<Div class="form_wrap">
            <p class="rob color_alert">  Please Note: "Login with your own Credentials only."   </p>
        </Div>
    </Div>
</Div>
    <Div class="footer_wrap_2">
        <div class="container-fluid">
            <span class="footer_span">Under MIT License.
        </div>	
    </Div>
</Div>
</body>
</html>