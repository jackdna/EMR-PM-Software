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

?><?php
include '../../config/globals.php';
$scan_doc_id = $_SESSION['scan_doc_id'];
$quickScan = (isset($_REQUEST['qs']) && $_REQUEST['qs']) ? true : false;
$table = '<page backtop="0" backbottom="0">';
$patient_id = $_SESSION['patient'];
//$path = "uploaddir/PatientId_".$patient_id."/uploaddir/"; //'//192.168.0.3/documents/test';
$path = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH')."/PatientId_".$patient_id.'/tmp_scan/'; 
$ab =  opendir($path);
$arrFiles = array();
$page_size = "A4";
$tld = $_REQUEST['tld'];
while(($filename = readdir($ab)) !== false){
	$arrFiles[] = $filename;
}
$b=0;
natsort($arrFiles);
foreach($arrFiles as $filename){
	$path1 = pathinfo($filename);
	if($path1['extension'] == 'jpg')
	{
		$b++;
		$arrImageSize = getimagesize($path.'/'.$filename);
		//pre($arrImageSize);exit;
		$scnimageWidth = "width=\"100%\" height=\"100%\"";
		//$img_width = (isset($_REQUEST['folder_id'])) ? "750" : "990";
		$img_width = 750;

		$width = $arrImageSize[0]; $height = $arrImageSize[1];
		$targetW = $tld == 'L' ? 1080 : 757;
		$targetH = $tld == 'L' ? 740 : 1070;

		$targetW = $width < $targetW ? $width : $targetW;
		$targetH = $height < $targetH ? $height : $targetH;
		
		if( $width > $height || ($height <= $targetH && $width > $targetW) ) {
			$percentage = ($targetW/$width);
		}
		elseif($height > $width || ($width <= $targetW && $height > $targetH) ) {
			$percentage = ($targetH/$height);
		}
		$width = round($width * $percentage);
		$height = round($height * $percentage); 
	
		$width = $width > $targetW ? $targetW : $width;
		$height = $height > $targetH ? $targetH : $height;
		
		$scnimageWidth = "width=\"".$width."\" height=\"".$height."\"  ";
		
		if($b>1) {
			$table .='</page><page>';		
		}
		
		//$table .='<img src="'.$path.'\\'.$filename.'"  '.$scnimageWidth.' align="left" style="text-align:left;padding:1px">';
		$table .='<img src="'.$path.$filename.'"  '.$scnimageWidth.' align="left" style="text-align:left;padding:1px">';
	}
}
	$table .='
</page>
';
$htmlFileName = 'pdffile_'.$_SESSION['authId'];
$file = fopen($path.$htmlFileName.'.html','w');
$data = fputs($file,$table);
fclose($file);
$folder_id=$_REQUEST['folder_id'];
?>
<form name="frm" action="index.php">
    <input type="hidden" name="page" value="5">
    <input type="hidden" name="comments" value="<?php echo $_REQUEST['comments'];?>">
    <input type="hidden" name="task_physician_id" value="<?php echo $_REQUEST['task_physician_id'];?>">
    <input type="hidden" name="font_size" value="10">
    <input type="hidden" name="page_size" value="<?php echo $page_size;?>">
    <input type="hidden" name="htmlFileName" value="<?php echo $htmlFileName;?>">
    <input type="hidden" name="tld" value="<?php echo $tld;?>">
    <input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id']; ?>"  />
    <input type="hidden" name="edit_id" value="<?php echo $_REQUEST['edit_id']; ?>"  />
    <?php if( $quickScan ) { ?>
        <input type="hidden" name="qs" value="1" />
    <?php }?>
</form>
<script language="javascript">
	document.frm.submit();
</script>