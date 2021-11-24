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

$bl_fake_pass = false;
$password = $GLOBALS["query_string"]["AdminPass"];
if(!empty($password)){
	$password_chk = hashPassword($password);
	$pass_qry = imw_query("select access_pri from users where password = '".$password_chk."' and delete_status='0'");
	if(imw_num_rows($pass_qry) > 0){
		$pass_arr = imw_fetch_assoc($pass_qry);
		$arr_privileges = unserialize(html_entity_decode(trim($pass_arr["access_pri"])));
		if($arr_privileges["priv_Sch_Override"] == 1){
			//good - grant access
		}else{
			$bl_fake_pass = true;
		}
	}else{
		$bl_fake_pass = true;
	}
}
if($bl_fake_pass == true){
	echo "revoke_access";
}else{
	echo "grant_access";
}
?>