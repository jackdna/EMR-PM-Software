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
require_once(dirname(__FILE__).'/../../config/globals.php');
$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : $_SESSION['authId'];
$user_details = getUserDetails($user_id,'*');

?>
<script type="text/javascript">
var user_fname	= '<?php echo $user_details['fname'];?>';
var user_lname	= '<?php echo $user_details['lname'];?>';
var user_uname	= '<?php echo $user_details['username'];?>';
var frm			= top.document.forms.cp;

function check_valid_cp(){
	xp = frm.xp;		xpv = xp.value;		xpv = xpv.trim();
	pn = frm.pnew;		pnv = pn.value;		pnv = pnv.trim();	pn_l = pnv.toLowerCase();
	cp = frm.cpnew;		cpv = cp.value;		cpv = cpv.trim();
	msg = '';
	focus_obj = false;
	
	var str = pnv;
	var letterExists =false;
	var charExists = false;
	var numericExists = false;
	for(var i=0; i<pnv.length; i++){
		var strChar = str.charAt(i);
		if( strChar.length === 1 && strChar.match(/[a-z]/i) ) {
			letterExists = "true";		
		}else	if(isNaN(strChar)){
			charExists = true;
		}else{
			numericExists = true;
		}
	}
	
		 if(xpv == ''){msg = 'Please enter current password.';	focus_obj=xp;}
	else if(pnv == ''){msg = 'Please enter new password.';	focus_obj=pn;}
	else if((pn_l.indexOf(user_fname.toLowerCase()) >= 0) || (pn_l.indexOf(user_lname.toLowerCase()) >= 0) || (pn_l.indexOf(user_uname.toLowerCase()) >= 0)){msg = 'Password must not contain First Name, Last Name and Login ID.';	focus_obj=pn;}
	//else if(!top.hasNumbers(pnv)){msg = 'New Password must contain atleast a numeric character.'; focus_obj=pn;}
	else if(pnv.length<8){msg = 'Password must be atleast 8 characters long.'; focus_obj=pn;}
	else if(letterExists == false || charExists==false || numericExists==false){msg = 'New Password must contain alphabet, numeric and special characters.  .'; focus_obj=pn;}
	
	else if(cpv != pnv){msg = 'Confirm Password must be same as New Password.';	focus_obj=cp;}
	if(msg != ''){
		top.fAlert(msg,'Change Password',focus_obj);
		return false;
	}else{
		if(top.HASH_METHOD=='MD5'){
			xp.value=top.md5(xpv);			pn.value=top.md5(pnv);			cp.value=top.md5(cpv);
		}else{
			xp.value=top.Sha256.hash(xpv);	pn.value=top.Sha256.hash(pnv);	cp.value=top.Sha256.hash(cpv);
		}
		top.process_cp_form();
	}
}

</script>
<form name="cp" id="cp" autocomplete="off" onsubmit="return valid_cp();">
<input type="hidden" name="resetby" value="<?php echo $resetBy; ?>">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" />
<div class="row">
	<div class="col-sm-12">
    <table class="table">
    	<tr>
        	<th>Existing Password</th>
            <td><input type="password" class="form-control" name="xp" id="xp" value="" /></td>
        </tr>
    	<tr>
        	<th>New Password</th>
            <td><input type="password" class="form-control" name="pnew" id="pnew" value="" /></td>
        </tr>
    	<tr>
        	<th>Confirm Password</th>
            <td><input type="password" class="form-control" name="cpnew" id="cpnew" value="" /></td>
        </tr>
    </table>
    
    </div>
</div>
</form>
<script type="text/javascript">
function validPWD2Change(){
	
}
</script>