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
require_once("../../../config/globals.php");
require_once('../../../library/classes/admin/class.providers.php'); 
$providers = new Providers;

$req = isset($_REQUEST['req']) ? $_REQUEST['req'] : 'listing';
switch($req){
	
	case  'lockunlock':
		$providers->providers_account_locked();
	break; 
	
	case  'resetPassword':
		echo $providers->reset_password_modal();
	break; 
	
	case  'resetPasswordSave':
		echo $providers->reset_password_save();
	break; 
	
	case  'password_encode':
		$password_get = $_REQUEST['get_password_text'];
		echo hashPassword($password_get);
	break;
	 
	case 'delete_selected':
		echo $providers->del_selected_provider();
	break;
	
	case 'directCred':
		echo $providers->direct_cred_modal();
	break;
	
	case 'directCredSave':
		echo $providers->direct_cred_save();
	break;
	
	case 'zeissCred':
		echo $providers->zeiss_cred_modal();
	break;
	
	case 'zeissCredSave':
		echo $providers->zeiss_cred_save();
	break;
	
	case 'touchworksCredSave':
		$callFrom = (isset($_REQUEST['callFrom']) && empty($_REQUEST['callFrom']) == false) ? $_REQUEST['callFrom'] : '';
		$provId = (isset($_REQUEST['provId']) && empty($_REQUEST['provId']) == false) ? $_REQUEST['provId'] : '';
		
		$paramArr = array();
		if(isset($_REQUEST['twUser']) && empty($_REQUEST['twUser']) == false) $paramArr['as_username'] = $_REQUEST['twUser'];
		/*if(isset($_REQUEST['twPass']) && empty($_REQUEST['twPass']) == false) $paramArr['as_password'] = $_REQUEST['twPass'];*/
		if(isset($_REQUEST['twEntryCode']) && empty($_REQUEST['twEntryCode']) == false) $paramArr['as_entry_code'] = $_REQUEST['twEntryCode'];
		if(isset($_REQUEST['imwUser']) && empty($_REQUEST['imwUser']) == false) $paramArr['imwUser'] = $_REQUEST['imwUser'];
		
		echo $providers->manage_as_cred($callFrom, $provId, $paramArr);
	break;
	
	case 'save':
		echo $return_status = $providers->providers_save();
	break;
	
    case 'resetToOldPriv':
		echo $providers->resetPrivilegesCheckbox();
	break;

	case 'dssUserInfoSave':
		$callFrom = (isset($_REQUEST['callFrom']) && empty($_REQUEST['callFrom']) == false) ? $_REQUEST['callFrom'] : '';
		$provId = (isset($_REQUEST['provId']) && empty($_REQUEST['provId']) == false) ? $_REQUEST['provId'] : '';
		
		$paramArr = array();
		if(isset($_REQUEST['electronicSignature']) && empty($_REQUEST['electronicSignature']) == false) $paramArr['electronicSignature'] = $_REQUEST['electronicSignature'];
		
		echo $providers->manage_dss_user_info($callFrom, $provId, $paramArr);
	break;
	
	case 'refill_direct':
		echo $providers->portal_refill_direct_modal();
	break;
	
	case 'savePortalRefillDirect':
		echo $providers->save_portal_refill_direct();
	break;

	case 'listing':
	default:
		echo $providers->providers_listing();
	break;		
}


?>