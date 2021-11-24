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
//error_reporting(1);
include_once(dirname(__FILE__)."/../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';
$echo="";
?>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<?php 

//TO SAVE THE EXTERNAL VA - FORM DATA IS COMING FROM /CHART_NOTES/VIEW/VISION_POP_UP.PHP =>PARAMETER popExtMR
if(!empty($_POST['extdos']) && !empty($_POST['entered_by_provider']))
{	
	$extDos= $providerName= "";
	if(!empty($_POST['extdos'])) $extDos = FormatDate_insert($_POST['extdos']); 
	if(!empty($_POST['entered_by_provider'])) $providerName = addslashes($_POST['entered_by_provider']);
	
	$insrtQry = "INSERT INTO `chart_vis_ext_mr` SET patient_id='".$_SESSION['patient']."', ext_dos='".$extDos."', ext_mr_od_s='".$_POST['ext_mr_od_s']."', ext_mr_od_c='".$_POST['ext_mr_od_c']."', ext_mr_od_a='".$_POST['ext_mr_od_a']."', ext_mr_od_txt1='".$_POST['ext_mr_od_txt1']."', ext_mr_od_add='".$_POST['ext_mr_od_add']."', ext_mr_od_txt2='".$_POST['ext_mr_od_txt2']."', ext_mr_od_gl_ph='".$_POST['ext_mr_od_gl_ph']."', ext_mr_od_gl_ph_txt='".$_POST['ext_mr_od_gl_ph_txt']."', ext_mr_od_p='".$_POST['ext_mr_od_p']."', ext_mr_od_sel_1='".$_POST['ext_mr_od_sel_1']."', ext_mr_od_slash='".$_POST['ext_mr_od_slash']."', 	ext_mr_od_prism='".$_POST['ext_mr_od_prism']."', ext_mr_os_s='".$_POST['ext_mr_os_s']."', ext_mr_os_c='".$_POST['ext_mr_os_c']."', ext_mr_os_a='".$_POST['ext_mr_os_a']."', ext_mr_os_txt1='".$_POST['ext_mr_os_txt1']."', ext_mr_os_add='".$_POST['ext_mr_os_add']."', ext_mr_os_txt2='".$_POST['ext_mr_os_txt2']."', ext_mr_os_gl_ph='".$_POST['ext_mr_os_gl_ph']."', ext_mr_os_gl_ph_txt='".$_POST['ext_mr_os_gl_ph_txt']."', ext_mr_os_p='".$_POST['ext_mr_os_p']."', ext_mr_os_sel_1='".$_POST['ext_mr_os_sel_1']."', ext_mr_os_slash='".$_POST['ext_mr_os_slash']."', ext_mr_os_prism='".$_POST['ext_mr_os_prism']."', ext_mr_desc='".$_POST['ext_mr_desc']."', ext_mr_prism_desc='".$_POST['ext_mr_prism_desc']."', entered_date_time='".date('Y-m-d H:i:s')."', prescribed_by='".$_POST['entered_by_provider']."' ";
	//echo $insrtQry; die;
	$exeQry	  = imw_query($insrtQry);
	$affectedCount = imw_insert_id();

	if(!empty($affectedCount))
	{	
		$echo = "alert('Record saved successfully!');";	
	}
}
echo "<script>".$echo." window.location.href='patient_refractive_sheet.php';</script>";	
?>