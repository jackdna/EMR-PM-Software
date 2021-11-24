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
require_once("config/globals.php");
require_once("library/classes/class.security.php");
$ObjSecurity	= new security();

switch($_GET["pg"]){
	case "app-logout":
		$ObjSecurity->app_logout('logout');
		break;
	case "load-landing-page":
		header("location:".$GLOBALS['webroot']."/interface/core/index.php");
		break;
	//on session timeout option controler
	case "app-session-timeout":
		$ObjSecurity->app_session_timeout();
		break;
	//post login checks before welcome page
	case "app-welcome-checks":
		$ObjSecurity->app_welcome_checks($_REQUEST['redMedMod'], $_REQUEST['remote_fac'], $_REQUEST['redOptMod']);
		break;
	//app main login page
	case "app-login-page":
	default:
		header("location:".$GLOBALS['webroot']."/interface/login/index.php");
		break;
}
?>