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
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $this->class_var['page_title'];?></title>
    
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS["webroot"];?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS["webroot"];?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS["webroot"];?>/library/messi/messi.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container-fluid">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="put_me_center_screen">
        <div class="panel-group col-lg-12">
            <div class="panel panel-default">
              <form name="cp" method="post" action="" onSubmit="return checkdata();">
              <div class="panel-heading"><h3>Change Password</h3></div>
              <div class="panel-body">
              	<div class="alert alert-warning alert-dismissible fade in" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <?php echo $this->class_var['show_reason'];?>
                </div>
				<div class="container col-lg-12">
                    <div class="form-group">
                        <label for="pnew">New Password</label>
                        <input name="pnew" id="pnew" type="password" class="form-control" value="">
                    </div>
                    
                    <div class="form-group">
                        <label for="cpnew">Confirm Password</label>
                        <input name="cpnew" id="cpnew" type="password"  class="form-control" value="">
                    </div>   
				</div>
              </div>
              <div class="panel-footer text-center">
                <div class="form-group">
                    <input type="hidden" id="wn_height" name="wn_height" value="<?php echo $_SESSION['wn_height'];?>" />
                    <input type="hidden" name="getlink" value="pass_process">
                    <input type="hidden" name="resetby" value="<?php echo $resetBy;?>">
                    <input type="submit" value="Change" name="save" id="save" class="btn btn-primary" />
                    <?php if($this->class_var['mandatory']=='no'){?>
                    <input type="button" value="Cancel" name="cancel" id="cancel" class="btn btn-warning" onclick="javascript:window.location.href='index.php?pg=load-landing-page';" />
                    <?php }?>
                </div>          
              </div>
              </form>
            </div>
    	</div>
        </div>
    </div>
</div>
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/common.js" language="javascript"></script>
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/messi/messi.js"></script>
<?php if(constant('HASH_METHOD')=="MD5"){?>
<script language="javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/md5.js"></script>
<?php }else{?>
<script language="javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/js_crypto_sha256.js"></script>
<?php }?>
<script type="text/javascript">

d= new Object; 
d=document.forms;
function checkdata(){ 
	var userFname = '<?php echo $fname;?>';
	var userLname = '<?php echo $lname;?>';
	var loginName = '<?php echo $username;?>';

	var msg=""; var charExists = false; var numericExists = false; var specialCharExists = false; 
	pwd = d.cp.pnew.value; var len = pwd.length;
	if(len < 8){
		msg = "<b>Please Enter New Password.</b><br>Required Length 8 or more characters.";
		callback='d.cp.pnew.focus()';
		//d.pro_pass_new.focus();
	}else if(len >= 8){
		for(i=0; i<len; i++){
			var strChar = pwd.charAt(i);
			if(isLetter(strChar)){var charExists = "true";}
			else if(isNaN(strChar)){var specialCharExists = "true";}
			else{var numericExists = "true";}
		}					
		if((charExists != "true") || (numericExists != "true") || (specialCharExists != "true") ){
			msg = "Must contain alphabet, numeric and special characters.";
			callback='d.cp.pnew.focus()';
		//	d.pro_pass_new.focus();
		}else if(pwd == userFname || pwd == userLname || pwd == loginName){
			msg = "Password can not have user First Name or Last Name or User Login id.";
			callback='d.cp.pnew.focus()';
		//	d.pro_pass_new.focus();
		}
	}
	if(msg == '' && d.cp.cpnew.value != pwd){
		msg = "Confirm Password must be as same as New Password."
		callback='d.cp.cpnew.focus()';
	}
				
	if(msg != ''){fancyAlert(msg,'Change Password',callback);msg='';return false;}
	else{
			<?php if(constant('HASH_METHOD')=="MD5"){?>
			hash_str=md5(d.cp.pnew.value);
			<?php }else{?>
			hash_str=Sha256.hash(d.cp.pnew.value);
			<?php }?>
			d.cp.pnew.value = hash_str;
			d.cp.cpnew.value = hash_str;
			if(hash_str.length!=32 && hash_str.length!=64){
				fancyAlert('Password encryption failed. Security exception. Can\'t proceed.');
				return false;	
			}
		}
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}
function RemainingDaysAlert(days){
	if(typeof(days)=="string"){
		//dialogBox("imwemr", "<b>Your Password will expire in "+days+" Day(s)!</b><br>Would you like to change it now?", "Yes","No","closeDialog()","?pg=load-landing-page','_self');",false,true,true,"","","",true,"","",false);
	}
}
$(function() {
	//$('.password').pstrength();
});

window.onload = function(){
	document.forms.cp.pnew.focus();
}
if ('<?php echo $this->class_var['show_alert'];?>'!="no"){
	RemainingDaysAlert('<?php echo $this->class_var['show_alert'];?>');
}
if ('<?php echo $this->class_var['show_msg'];?>'!=""){
	var call_back ="document.forms.cp.pnew.focus()";
	if ('<?php echo $this->class_var['show_msg'];?>'=="Password has been changed successfully."){
		call_back ="window.location.href='<?php echo $GLOBALS['webroot'];?>'";
	}
	fancyAlert("<?php echo $this->class_var['show_msg'];?>","",call_back);
}
	</script>
</html>