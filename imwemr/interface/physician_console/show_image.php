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
  File: show_image.php
  Purpose: This file shows scanned, uploaded files.
  Access Type : Direct
 */
?>
<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/user_console.php');
require_once($GLOBALS['fileroot'] . '/library/classes/class.tests.php');

$userTests = new Tests;

$id = $_REQUEST['id'];
$ext = $_REQUEST['ext'];
$noZoom = $_REQUEST['noZoom'];
$scn_task = $_REQUEST['scn_task']; //from interface/physician_console/scan_task.php
$pid = $_GET["pid"];
$file_name = $_GET["file_name"];
$userauthorized = $_SESSION['authId'];
$nowDate = date('Y-m-d H:i:s');
if (!$pid) {
    $pid = $_SESSION['patient'];
}

function get_image_prop_new($image_name, $tw, $th) {
    $image_attributes = @getimagesize("$image_name");
    $ow = $image_attributes[0];
    $oh = $image_attributes[1];
//echo($ow."=$tw Ram W".$oh."Ram H".$th);

    if ($ow <= $tw && $oh <= $th) {
        $ret[0] = $ow;
        $ret[1] = $oh;
        return($ret);
    } else {
        $pc_width = $tw / $ow;
        $pc_height = $th / $oh;
        $pc_width = number_format($pc_width, 2);
        $pc_height = number_format($pc_height, 2);
//echo("Percentage Width=".$pc_width."and Perscentage height=".$pc_height);
        if ($pc_width < $pc_height) {
            $rd_image_width = number_format(($ow * $pc_width), 2);
            $rd_image_height = number_format(($oh * $pc_width), 2);
            $ret[0] = $rd_image_width;
            $ret[1] = $rd_image_height;
            return($ret);
        } else if ($pc_height < $pc_width) {
            $rd_image_width = number_format(($ow * $pc_height), 2);
            $rd_image_height = number_format(($oh * $pc_height), 2);
            $ret[0] = $rd_image_width;
            $ret[1] = $rd_image_height;
            return($ret);
        }
    }
}

/*
  $src1 = "../main/demoApplet/uploaddir/PatientId_".$pid."/uploadJPG/".$file_name;
  $g1 = get_image_prop_new($src1,700,600);
  $ow=$g1[0];
  $oh=$g1[1];
 */
if (!empty($id)) {
    $sql = "select file_path,folder_categories_id,task_physician_id from " . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl where scan_doc_id =$id";
    $res = imw_query($sql);
    $row = imw_fetch_array($res);
    $file_path = $row['file_path'];
    $folder_categories_id = $row['folder_categories_id'];
    $task_physician_id = $row['task_physician_id'];
}

if ($_SESSION['authId'] == $task_physician_id && $scn_task == 'review') {
    $updateTskAllocationQry = "UPDATE " . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl SET task_status='1', task_review_date='" . $nowDate . "' WHERE scan_doc_id ='" . $id . "'";
    $updateTskAllocationRes = imw_query($updateTskAllocationQry);
} else if ($_SESSION['authId'] == $task_physician_id) {
    $updateTskAllocationQry = "UPDATE " . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl SET task_status='2', task_review_date='" . $nowDate . "' WHERE scan_doc_id ='" . $id . "'";
    $updateTskAllocationRes = imw_query($updateTskAllocationQry);
}
//START CODE TO CREATE LOG OF SCAN
$scanProviderLog = $userTests->providerViewLogFun($id, $_SESSION['authId'], $_SESSION['patient'], 'scan');
//END CODE TO CREATE LOG OF SCAN
//START CODE TO CHANGE SCAN ICON ACCORDING TO CONDITION
$scnImgSrcActive = scnDocReadChkFun($_SESSION['patient'], 'scan', $_SESSION['authId']);
//START CODE TO CHANGE SCAN ICON ACCORDING TO CONDITION
//START
$ChkAnyDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"), $_SESSION['patient'], $folder_categories_id); //FUNCTION FROM common/scan_function.php
//echo $folder_categories_id;


if (!empty($file_path) && (strtolower($ext) == 'html' || strtolower($ext) == 'htm')) {
    echo '<script type="text/javascript">window.location.href=\'' . $GLOBALS['php_server'] . "/interface//main/uploaddir" . $file_path . '\';</script>';
    exit;
} else if (!empty($file_path) && (strtolower($ext) == 'rtf')) {
    echo '<script type="text/javascript">window.location.href=\'' . $web_root . "/library/RTF2HTML/index.php?file_root=pt_doc_root&to_format=html&file_src=" . urlencode($file_path) . '\';</script>';
    exit;
}
$chkUnReadCatQry = "SELECT DISTINCT(pvlt.scan_doc_id) FROM " . $dbase . ".provider_view_log_tbl pvlt," . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl sdt
					WHERE sdt.folder_categories_id='" . $folder_categories_id . "'
					AND pvlt.scan_doc_id=sdt.scan_doc_id
					AND pvlt.provider_id='" . $_SESSION['authId'] . "'
					AND pvlt.patient_id='" . $_SESSION['patient'] . "'
					AND pvlt.section_name='scan'
				";
$chkUnReadCatRes = imw_query($chkUnReadCatQry);
$chkUnReadCatNumRow = imw_num_rows($chkUnReadCatRes);

$scnCatUnReadImageSrc = $GLOBALS['webroot'] . '/images/sign.gif';
$scnCatUnReadImage = '<img src="' . $scnCatUnReadImageSrc . '" height="13" vspace="0" border="0" align="middle" title="Unread Folder">';
if ($ChkAnyDocExistsNumRow == $chkUnReadCatNumRow) {
    $scnCatUnReadImage = '';
}
//END
?>
<html>
    <head>
    <!--<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $css_patient; ?>" type="text/css">
    <link type="text/css"  href="css/style.css" rel="stylesheet">-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>imwemr</title>
        <script language="JavaScript1.2">
        </script>
    </head>

    <body class="body_c">


        <?php
        if (($ext == "pdf") || (!empty($file_name) && !empty($pid))) {
            $src = "";
            
            if (!empty($file_path)) {
                $src = $GLOBALS['rootdir'] . "../../data/" . PRACTICE_PATH . $file_path;
            } else if ($ext == "pdf") {
                $src = "common/pdfLoader.php?id=$id&ext=$ext";
            } else if (!empty($file_name) && !empty($pid)) {
                $src = "../main/demoApplet/uploaddir/PatientId_" . $pid . "/uploaddir/" . $file_name . ".pdf";
            }
            echo "<table width=\"100%\" height=\"100%\" border=\"0\" align=\"center\">";
            echo "<tr><td><iframe src=\"" . $src . "\" width=\"100%\" height=\"500px\" scrolling=\"no\"></iframe></td></tr>";
            echo "</table>";
        } else {
            ?>
            <table width="650" border="0" align="center">
                <?php /*if (!isset($noZoom) || empty($noZoom)) { ?>
                    <TR>
                        <TD align="center"><a href="#" target="_self"  onMouseOver="zoom(200, 150, 'logo', 'in')" onMouseOut="clearzoom()" class="text_9">Zoom In(+)</a> | <a href="#" target="_self"  onMouseOver="zoom(650, 550, 'logo', 'restore')" class="text_9">Normal</a> <!--| <a href="#" target="_self"  onMouseOver="zoom(200,150,'logo','out')" onMouseOut="clearzoom()" class="text_9">Zoom Out(-)</a>--><br><br><br></Td>
                    </TR>
                <?php } */ ?>

                <TR>
                    <TD align="center">
                        <?php
                        $src1 = dirname(__FILE__) . "/../../data" . $GLOBALS['webroot'] . $file_path;
                        $file_path_web = $GLOBALS['rootdir'] . "../../data/" . $GLOBALS['webroot'] . $file_path;
                        if ($ext == 'tif' && $GLOBALS['gl_browser_name'] != 'ie') {
                            $tifSize = getimagesize($src1);
                            $tifJPGpath = substr($src1, 0, -3) . 'jpg';
                            if (!file_exists($tifJPGpath)) {
                                exec('convert -density 300 -trim "' . $src1 . '" -strip -quality 100 -interlace line -colorspace RGB -resize ' . $tifSize[0] . ' "' . $tifJPGpath . '"', $output, $return_var);
                            }
                            $file_path_web = substr($file_path_web, 0, -3) . 'jpg';
                        }

                        if (file_exists($src1)) {
                            $g1 = get_image_prop_new($src1, 700, 600);
                            $ow = $g1[0];
                            $oh = $g1[1];
                            ?>
                            <img name="logo" src="<?php echo $file_path_web; ?>" width="<?php echo $ow; ?>" height="<?php echo $oh; ?>" onDblClick="openWindowlogoFull('<?php echo $file_path_web; ?>');" />
                            <?php
                        } else {
                            ?>	
                            Error: Image not found.
                            <?php
                        }
                        ?>
                    </TD>
                </TR>
                <?php /*if ((!isset($noZoom) || empty($noZoom)) && !isset($_GET['hide_close_btn'])) { ?>
                    <TR>
                        <TD align="center"><input type="button" value="close" class="text_10b" onClick="javascript:window.close();" />
                    </TR>
                <?php }*/ ?>	
            </table>
            <?php
        }
        ?>


    </body>
</html>
