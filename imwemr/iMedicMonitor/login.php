<?php
//require_once("../library/classes/class.security.php");
//$ObjSecurity = new security();
$date_time = date('l M d, Y');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login to imwMonitor</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="imagetoolbar" content="no" />
		<link rel="stylesheet" type="text/css" href="../library/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="../library/css/loginpage.css" />
		<link rel="stylesheet" type="text/css" href="../library/css/common.css" />
		

	</head>
	<body class="loginpage">
		<div class="container-fluid">
			<div class="mainlogbx">
				<div class="loginmid">
					<div class="row">
						<div class="col-sm-6 loginwelcome">
							<div class="row logoara">
								<div class="col-lg-6 col-md-6  col-sm-12"><img src="../library/images/logo.png" alt=""/></div>
								<div class="col-lg-6 col-md-6  col-sm-12"><p class="logindate"><?php echo $date_time; ?> <span id="tick2"></span></p>
								<div class="clearfix text-right">
							<a href="#"  target="_blank"><img src="../library/images/company_logo.png" alt="Company"></a>	
                           
                           </div>
								
								
								</div>
							</div>

							<div class="loginlogo">
<!--								<div class="image_con"><img src="<?php //echo $ObjSecurity->getHQFacLogo(); ?>" alt=""/></div>-->
								<h1>Welcome to imwMonitor</h1>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="loginaccount text-center"><img src="../library/images/loginicon.png" alt=""/>
								<h2>Login To Your Account</h2>
							</div>
							<div class="clearfix"></div>
							<div class="loginform">
								<form method="post" action="index.php" name="login" onsubmit="javascript:return valid_login();" autocomplete="off">
									<div class="group">
										<input type="text" id="u_n" name="u_n" required value="" />
										<span class="highlight"></span>
										<span class="bar"></span>
										<label>Username</label>
									</div>

									<div class="group">
										<input type="password" id="p_w" name="p_w" required value="" autocomplete="off" />
										<span class="highlight"></span>
										<span class="bar"></span>
										<label>Password</label>
									</div>

									<div class="group hide">
										<br>
<!--										<img style="margin:5px 10px;" src="../library/inc_captcha.php" align="left">-->
										<span class="hint">Please enter the "Verification Code" as shown in the image.</span>
										<label>Verification Code</label>
									</div>
									<div class="clearfix text-center"><button type="submit" class="signinbut hvr-rectangle-out">Login</button></div>
								</form>
							</div>
							<div class="clearfix"></div>
							<div class="lognote"><span>Please Note:</span> "Login with your own Credentials only."</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<div class="copytxt">
					<?php /* Copyrights &copy; 2021 - <?php echo date("Y"); ?>  &reg; All rights reserved. R6 <?php echo date("Y"); */ ?>
					Copyrights &copy; yourcompany name with year<br>
					<!--					<a href="javascript:void(0);" onClick="legal_pop('../interface/core/login.index.php?pg=legal-doc&doc=privacy',200);">Our Privacy Statement</a> | 
										<a href="javascript:void(0);" onClick="legal_pop('../interface/core/login.index.php?pg=legal-doc&doc=copyright',650);">Copyright Notice</a> | 
										<a href="javascript:void(0);" onClick="legal_pop('../interface/core/login.index.php?pg=legal-doc&doc=softLic',650);">License</a>-->
				</div>
			</div>
		</div>


		<script language="javascript" src="../library/js/jquery.min.1.12.4.js"></script>
		<script language="javascript" src="../library/js/bootstrap.min.js"></script>
		<script language="javascript" src="../library/js/common.js"></script>
		<?php if(constant('HASH_METHOD')=="MD5"){?>
			<script type="text/javascript" src="../library/js/md5.js"></script>
		<?php }else{?>
			<script type="text/javascript" src="../library/js/js_crypto_sha256.js"></script>
		<?php }?>
		<script language="javascript">
			var init_actions = true;
			var d = document.forms;
			function valid_login(){
				var msg = ''; 
				var callback = ''; 
				var p = d.login.p_w.value; 
				var len = p.length;

				if(d.login.u_n.value == ''){
					msg = 'Please Enter Username.'; 
					callback = 'd.login.u_n.focus()';
				}else if(len < 1){
					msg = 'Please Enter Password.'; 
					callback = 'd.login.p_w.focus()';
				}

				if(msg != ''){
					fancyAlert(msg, "imwemr", callback); 
					msg = '';
					return false;
				}
			}

			function log_height() {
				top_logo_hgt = $('#top_logo_div').height();
				inHeight = pageHeight() - top_logo_hgt - 190;
				dgi("logintbl").style.height = inHeight + "px";
			}

			//---auditing app-close
			var do_not_do_audit = "0";
			var do_audit = '1';
			window.onbeforeunload = function () {
				if (do_audit == '1' && do_not_do_audit == "0") {
					//alert("/../interface/main/close_audit.php");
					//return false;
					//$.ajax({ url: "/../interface/main/close_audit.php", success: function(resp){}});


				}
			}


			function legal_pop(url, h) {
				window.open(url, '', 'width=700, height=' + h + 'px, left=200, top=100, toolbar=0, location=0, statusbar=0, menubar=0');
			}

			//Author: RM						 
			//Purpose: this function getting and setting Client PC name in cookie
			function getPCName() {
				try {
					var name = 'clientPCName';
					var chkCookie = document.cookie.indexOf(name + "=");
					if (chkCookie == -1) {
						//var net = new ActiveXObject("wscript.network");
						var net = new ActiveXObject("WScript.Network");
						var value = net.ComputerName;
						var date = new Date();
						date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 1000));
						var expires = "; expires=" + date.toGMTString();
						document.cookie = name + "=" + value + expires + "; path=/";
					}
				} catch (err) {
					//Handle errors here
				}
			}

			window.onload = function () {
				show_clock();
				document.login.u_n.focus();
				if (init_actions == true) {
					//log_height();
					
					init_actions = false;
				}
			}

		</script>
		<script type="text/javascript">
			<?php if ($_REQUEST['login_status'] == "1") { ?>
				msg = 'Authentication Failed.';
				callback = 'd.login.u_n.focus()';
				fancyAlert(msg, "imwemr", callback);
				msg = '';
			<?php } ?>
		</script>
	</body>
</html>