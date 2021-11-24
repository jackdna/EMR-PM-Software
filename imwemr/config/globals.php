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

 
 Purpose: global settings file.
 Access Type: Indirect Access.
*/
date_default_timezone_set('America/New_York');
define("HASH_METHOD","SHA2"); //it can be SHA1 or MD5

error_reporting(0);
ini_set("display_errors",0);

//This is the idle logout function:
$GLOBALS['session_timeout'] = 1800;		//default value
ini_set("session.gc_maxlifetime", "21600");

//Turn off PHP compatibility warnings
ini_set("session.bug_compat_warn","off");

global $query_string;
if(!isset($query_string) || !is_array($query_string)){
	$query_string = array();
	$query_string = array_merge($_GET, $_POST, $_FILES); //get overwrites post	
}

//Practice settings
/*$dyn_path = $_SERVER['REQUEST_URI'];
$dyn_path = trim($dyn_path,"/");
$dyn_path = explode("/",$dyn_path);
$dyn_path = $dyn_path[0];*/
$dyn_host = $_SERVER['HTTP_HOST'];
$dyn_path = preg_replace('/(.*)\..*\..*/', '$1', $dyn_host);
define("PRACTICE_PATH", strtolower($dyn_path));

$RootDirectoryName = "imwemr";
$web_RootDirectoryName = '';//"imwemr-Dev"; //imwemr
$webServerRootDirectoryName = "/var/www/html/";// ::C:/zProject2/XAMP/xampp1.5.1/xampp/htdocs/

$web_root = $web_RootDirectoryName;
$webserver_root = $webServerRootDirectoryName . $RootDirectoryName;

//for r3.1 folder compatibility - added by amit
global $strR3_1Path;
$strR3_1Path = $web_root.'/field_settings/';

//root directory, relative to the webserver root:
$GLOBALS['rootdir'] = "$web_root/interface";
//absolute path to the source code include and headers file directory (Full path):
$GLOBALS['srcdir'] = "$webserver_root/library";
//absolute path to the location of interface root directory for use with include statements:
$GLOBALS['fileroot'] = "$webserver_root";
//absolute path to the location of interface root directory for use with include statements:
$include_root = "$webserver_root/interface";
$GLOBALS['webroot'] = $web_root;
$GLOBALS['incdir'] = $include_root;
//location of the login screen file
$GLOBALS['login_screen'] = "$rootdir/login_screen.php";

//SESSION SETTINGS
define('GLOBAL_IMW_IDOC_SESSION_NAME',"demoemr".$web_RootDirectoryName);
session_name(constant('GLOBAL_IMW_IDOC_SESSION_NAME'));
session_start();

//this is the theme definition for a beige theme:
$top_bg_line = ' bgcolor="#94d6e7" ';
$GLOBALS['style']['BGCOLOR2'] = "#94d6e7";
$bottom_bg_line = ' background="'.$rootdir.'/pic/aquabg.gif" ';

$bgColor_main = ' bgcolor="#F5F5F5" ';
$backImage = " background = '".$web_root."/images/bg.jpg'";

$login_filler_line = ' bgcolor="#f7f0d5" ';
$login_body_line = ' background="'.$rootdir.'/pic/aquabg.gif" ';
$title_bg_line = ' bgcolor="#33669B" ';
$nav_bg_line = ' bgcolor="#94d6e7" ';
$prac_back = ' bgcolor = "#efefef" ';
$css_header = "$rootdir/themes/style_sky_blue.css";
$css_header_tech = "$rootdir/themes/style_sky_tech.css";

$css_patient = "$rootdir/themes/style_patient.css";
$clock = "$rootdir/main/clock.php";
$logocode="<img src='$rootdir/pic/logo_sky.gif'>";
$linepic = "$rootdir/pic/repeat_vline9.gif";
$table_bg = ' bgcolor="#cccccc" ';
$GLOBALS['style']['BGCOLOR1'] = "#cccccc";
$GLOBALS['style']['TEXTCOLOR11'] = "#222222";
$GLOBALS['style']['HIGHLIGHTCOLOR'] = "#dddddd";
$GLOBALS['style']['BOTTOM_BG_LINE'] = $bottom_bg_line;

// the height in pixels of the Logo bar at the top of the login page.
$GLOBALS['logoBarHeight'] = 110;
// the height in pixels of the Navigation bar
$GLOBALS['navBarHeight'] = 70;
// the height in pixels of the Title bar
$GLOBALS['titleBarHeight'] = 20;

$srcdir = $GLOBALS['srcdir'];
$login_screen = $GLOBALS['login_screen'];
$GLOBALS['css_header'] = $css_header;
$GLOBALS['css_patient'] = $css_patient;
$GLOBALS['backpic'] = $backpic;

//validate is we heading to right practice folder
if(file_exists($webserver_root.'/config/'.PRACTICE_PATH.'/config_'.PRACTICE_PATH.'.php'))
{
	include($webserver_root.'/config/'.PRACTICE_PATH.'/config_'.PRACTICE_PATH.'.php');
}else
{
	//send user on default error page
	header("location:".$GLOBALS['webroot']."/interface/login/login_error.php");	
}

// This is a URL of server Where this application is installed (without trailing slash).Test
$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;

//include loops
if (!isset($ignoreAuth)) {
	include_once($srcdir."/classes/auth.php");
}else{
	//DB Connections and related Function
	include_once($srcdir."/classes/db.php");
	//include_once($srcdir."/classes/sql.php");
}

$encounter = $_SESSION['encounter'];

if (!empty($_GET['pid']) && empty($_SESSION['pid'])) {
	$_SESSION['pid'] = $_GET['pid'];
}
elseif (!empty($_POST['pid']) && empty($_SESSION['pid'])) {
	$_SESSION['pid'] = $_POST['pid'];
}
$pid = $_SESSION['pid'];
$userauthorized = $_SESSION['userauthorized'];
$groupname = $_SESSION['authProvider'];


//required for normal operation because of recent changes in PHP:
$ps = strpos($_SERVER['REQUEST_URI'],"mysqladmin");
if ($ps === false) {
	extract($_GET);
	extract($_POST);
}

define('PRODUCT_VERSION', 'IMWEMR'); //SP4 P3
//define('PRODUCT_VERSION_DATE', '06/30/2022');
define('PRODUCT_VERSION_DATE', 'January 01, 2022 - V1.00'); //April 24, 2022
?>
