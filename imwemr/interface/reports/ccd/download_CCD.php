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
FILE : download_CCD.php
PURPOSE : Downloding CCD report.
ACCESS TYPE : Indirect
*/
include("../../../config/globals.php");
$rqfileName = "";
$rqfileName = base64_decode($_REQUEST['fileName']);
$download_name = "";
if(empty($rqfileName) == false && isset($_REQUEST['fileName']) && trim($rqfileName) != "liaka_ccd.xml"){
	$filename = $rqfileName;
	if(isset($_REQUEST['ccdPatName']) && $_REQUEST['ccdPatName'] != "" && $_REQUEST['fileType'] == "xml"){		
		$download_name = $_REQUEST['ccdPatName']."_CCD.xml";
	}
	elseif(isset($_REQUEST['ccdPatName']) && $_REQUEST['ccdPatName'] != "" && $_REQUEST['fileType'] == "txt"){		
		$download_name = $_REQUEST['ccdPatName']."_SHA2.txt";
	}
	else{
		$download_name = $rqfileName;
	}
}
else{
	$filename = "liaka_ccd.xml";
	$download_name = "liaka_ccd.xml";
}
$content_type = "text/xml";
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("Cache-Control: private",false);
header("Content-Description: File Transfer");

header("Content-Type: ".$content_type."; charset=utf-8");
//die();
header("Content-disposition:attachment; filename=\"".$download_name."\"");

header("Content-Length: ".@filesize($filename));
//echo filesize($filename);
@readfile($filename) or die("File not found.");
exit;
?>
