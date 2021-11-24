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
?>
<?php
/*
File: dicom_globals.php
Purpose: This file is used for setting global variables for DICOM.
Access Type : Include file
*/
?>
<?php

//----- Server Specific ----

//path for dcmtk
define('TOOLKIT_DIR', 'G:/Newfolder/dicom/dcmtk-3.6.0-win32-i386/d-3.6.0-win32-i386/bin');

//path of dicom folder in imwemr
define('IMEDIC_DICOM', 'G:/xampp/htdocs/iMedicWareR8-Dev/interface/chart_notes/dicom');

//dicom IP for receiver
define('DICOM_IP', "127.0.0.1");

//dicom AE Title for receiver
define('LISTENER_SLEEP_TIME', 10);

//convert tools
define('TCONVRT', 'G:/Newfolder/ffmpeg');
//define('TCONVRT', '');

//PHP HOME
define('PHP_HOME', "G:/xampp/php");

//DICOM DB PATH
//define('DICOM_DB_PATH', IMEDIC_DICOM."\wl_data");
define('DICOM_DB_PATH', "G:/xampp/htdocs/www/wl-data");

//----- Facility Specific ----

//path of folder where files are coming from dicom machine
define('DCM_DIR', 'C:\Imedic\apache\htdocs\directoryHandler');

//dicom port for receiver
define('DICOM_PORT', 11317);

//dicom AE Title for receiver
define('DICOM_AE', "imw1");

//dicom AE TITLE for folderlistener
define('DICOM_AE_FOLDERLISTENER', "FOLDERLISTENER");

//DICOM WORK LIST PORT
define('DICOM_WL_PORT', 12340);

//DICOM IS WORKING  ON THIS SERVER
define('DICOM_IS_WORKING', 1);

//dicom AE Title for receiver
define('DICOM_AE_WLM', "imw");

//dicom log  put '1' to enable it
//define('DICOM_LOG', "1");
define('DICOM_LOG', "");

//USE MRN  not Patient ID : insert field name of mrn
//define('DICOM_USE_MRN', "External_MRN_2");
define('DICOM_USE_MRN', "");

//dicom AE To set in worklist file; Default is DICOM_AE_WLM
define('DICOM_AE_WLM_DB', "OPT");

//dicom modality type to set in worklist file; Default is OPT
define('DICOM_MODALITY', "RGOCT1");

//Set Practice url:: shoreeye
$_SERVER['REQUEST_URI'] = "shoreeye";

//dicom modality type to set in worklist file; Default is OPT
define('DICOM_PRACTICE_DIR', dirname(__FILE__));

?>