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
File: imp_port_listener.php
Purpose: This file provides processing for dicom file in port listener.
Access Type : Include file
*/
?>
<?php
set_time_limit(0);
chdir(dirname(__FILE__));
$ignoreAuth = true;
require(dirname(__FILE__).'/dicom_link.php');
//require_once(dirname(__FILE__).'/class_dicom.php');
//require_once(dirname(__FILE__).'/class_dicom.php');
//require_once(dirname(__FILE__).'/dicom_db.php');


if(isset($_GET["st"]) && !empty($_GET["st"])){

//Delete DCM files
//dir to store images
$store_dir = DICOM_PRACTICE_DIR."/received_images";

$arr_more=array();
$dir = $store_dir;
$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
foreach($files as $file) {
$tmp_more=$file->getRealPath();
chmod($tmp_more,0777);
    if ($file->isDir()){	
        if(!rmdir($tmp_more)){  $arr_more[]=$tmp_more;  }
    } else {
        unlink($file->getRealPath());
    }
}

//
//print_r($arr_more);
//echo "<br/>";
//print_r($files);


echo "<br/>Process done ! ";
}else{
	
	echo "<br/><h3>Are you sure? This process will delete all DCM files forever.</h3>";
	echo "<br/><a href=\"del_dcm_files.php?st=1\">Click to delete all files</a>";

}

?>
