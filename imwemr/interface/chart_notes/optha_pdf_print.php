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
//require_once(getcwd()."/../chart_notes/common/saveFile.php");

//error_reporting(E_ALL);
//include_once(dirname(__FILE__).'/SaveFile.php');

$objectSaveFile=new SaveFile;		

$sql = "SELECT * FROM ophtha ".		   
		   "WHERE patient_id = '".$patient_id."'  AND form_id = '$form_id' ";	

	$row = sqlQuery($sql);
		
	if(($row == false))
	{	
		
		// No Record
			
	}
	else
	{	
				
		$facility_id = $row["facility_id"];
		$facility_name = getFacilityName($facility_id);			
		$exam_date = FormatDate_show($row["exam_date"]);			
		$app_os = $row["ophtha_os"];
		$app_od = $row["ophtha_od"];
		$doctor_name = $row["doctor_name"];
		$doctor_sign = $row["doctor_sign"];	
		$notes=$row["notes"];
		$idOptha = $row["ophtha_id"];
	}	
				

		
	
if($idOptha != "")
{ 			
?>
<table style="width:100%;" class="paddingTop" border="0" cellspacing="0" cellpadding="0">
			<tr >
				<td  valign="middle" class="tb_heading" colspan="3" style="width: 100%;" >Ophthamoscopy</td>
			</tr>
	
	
<?php
if(isAppletModified($app_os) || isAppletModified($app_od))
{ 
?>

	<tr>
		<td class="text_lable" style="width: 45%;" align="center"><?php odLable();?></td>
		<td style="width:10%;">&nbsp;</td>
		<td class="text_lable" style="width: 45%;"><?php osLable();?></td>
	</tr>
	<tr>
		<td align="right" style="width: 45%;">
		<?php	
		$tableOptha = 'ophtha';
		$idNameOptha = 'ophtha_id';
		$pixelOpthaOd = 'ophtha_od';
		$imageOptha = realpath(dirname(__FILE__).'/../../images/ophtha.jpg');
		$altOptha = 'OD'; 
		getAppletImage($idOptha,$tableOptha,$idNameOptha,$pixelOpthaOd,$imageOptha,$altOptha,"1");
		$gdFilenamePath =realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
		$gdFilenameThumbPath = "../common/new_html2pdf/".$gdFilename;
		$gdFilename=$objectSaveFile->createThumbs($gdFilenamePath,$gdFilenamePath,$thumbWidth="342",$thumbHeight="342","jpg");
		 echo('<img src="'.$gdFilenamePath.'"/>');
		$ChartNoteImagesString[]=$gdFilename;
		?>	 													
		</td>
		<td style="width:10%;">&nbsp;</td>
		<td  align="left" style="width: 45%;">				
		<?php
		if(isAppletModified($app_os) || isAppletModified($app_od)){
		$tableOptha = 'ophtha';
		$idNameOptha = 'ophtha_id';
		$pixelOpthaOs = 'ophtha_os';
		$imageOptha = realpath(dirname(__FILE__).'/../../images/ophtha.jpg');
		$altOptha = 'OS'; 
		getAppletImage($idOptha,$tableOptha,$idNameOptha,$pixelOpthaOs,$imageOptha,$altOptha,"1");
		$gdFilenamePath =realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
		$gdFilenameThumbPath = "../common/new_html2pdf/".$gdFilename;
		$gdFilename=$objectSaveFile->createThumbs($gdFilenamePath,$gdFilenamePath,$thumbWidth="342",$thumbHeight="342","jpg");
		echo('<img src="'.$gdFilenamePath.'"/>');
		$ChartNoteImagesString[]=$gdFilename;
		/*$gdFilename = realpath(dirname(__FILE__)."/../main/html2pdfprint/".$gdFilename);
		echo('<img src="'.$gdFilename.'" height="200" width="200" />');
		$ChartNoteImagesString[]=$gdFilename;*/					 
		}
		?>		
		</td>
	</tr>

<?php
	}		
?>						
<?php  
if($notes<>"") { 
?>

<tr>

	<td class="text_value" colspan="3" style="width:100%;"><span class="text_lable">Notes:&nbsp;</span><?php echo($notes);?></td>
</tr>

<?php }	?>


<?php

	if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"]) && (1==2))
	{
?>
		<tr>
		<td class="text_lable" colspan="3" style="width:100%;"><div class="text_lable">Finalized Signature:</div>
	<?php
		//Doctor Sign
		$idPlan = $_SESSION["finalize_id"];
		$tablePlan = 'chart_assessment_plans';
		$idNamePlan = 'form_id';
		$pixelPlan = 'sign_coords';
		$imagePlan = realpath(dirname(__FILE__).'/../../images/white.jpg');
		$altPlan = 'Doctor Sign'; 
		echo getAppletImage($idPlan,$tablePlan,$idNamePlan,$pixelPlan,$imagePlan,$altPlan);
	?>											
	</td>
	</tr>

<?php
	}
?>													
<tr>
	<td class="text_value" colspan="3" style="width:100%;">		
		<span class="text_lable">Performed By:&nbsp;</span><?php echo (showDoctorName($doctor_name));?>	
	</td>
</tr>
</table>
<?php 
}

?>				
