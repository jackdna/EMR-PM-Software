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
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
$OBJCommonFunction = new CLSCommonFunction;
$cap_date=$_REQUEST['cap_date'];
$cap_opt=$_REQUEST['cap_opt'];
$cap_main_id=$_REQUEST['cap_main_id'];
ob_start();
$left_margin=10;
$operator_id=$_SESSION['authId'];
?>
<style>
	.tb_heading{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#FFFFFF;
		background-color:#4684ab;
		border:1px solid #FFFFFF ;
	}
	.text_b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#CCC;
		border:1px solid #FFFFFF ;
	}
	.text_10{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#CCC;
		border:1px solid #FFFFFF ;
	}
	.font_14b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#FFFFFF;
	}
</style>
<?php
	$phy_id_arr=array();
	$sel_prov=imw_query("select id,lname,fname from users order by lname,fname asc");
	while($fet_prov=imw_fetch_array($sel_prov)){
		$phy_id_arr[]=$fet_prov['id'];
		$phy_id_name[$fet_prov['id']]=$fet_prov['lname'].', '.$fet_prov['fname'];
		$phy_id_als_name[$fet_prov['id']]=substr($fet_prov['fname'],0,1).substr($fet_prov['lname'],0,1);
	}
?>

<page backtop="14mm" backbottom="7mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>		
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        	<tr>
            	<td width="<?php echo $left_margin; ?>">&nbsp;</td>
                <td class="tb_heading" style="height:25px;">&nbsp;&nbsp;Capitation Hx</td>
                <td class="tb_heading" colspan="4" style="text-align:center;">
                    Batch Date : <?php echo $_REQUEST['cap_date']; ?>
                </td>
                <td class="tb_heading" colspan="3" style="text-align:center;">
                    Printed By <?php echo $phy_id_als_name[$operator_id]; ?> on <?php echo date("m-d-Y h:m A"); ?>
                </td>
            </tr>
			<tr>
				<td width="<?php echo $left_margin; ?>">&nbsp;</td>
				<td class="tb_heading" style="width:270px; text-align:left;">Patient Name - ID</td>
                <td class="tb_heading" style="width:100px; text-align:center;">Encounter Id</td>
                <td class="tb_heading" style="width:100px; text-align:center;">DOS</td>
                <td class="tb_heading" style="width:100px; text-align:center;">DOC</td>
                <td class="tb_heading" style="width:100px; text-align:center;">Charges</td>
                <td class="tb_heading" style="width:100px; text-align:center;">Balance</td>
                <td class="tb_heading" style="width:100px; text-align:center;">Copay</td>
                <td class="tb_heading" style="width:100px; text-align:center; padding-right:20px;">Pending Copay</td>
			</tr>
		</table>
	</page_header>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<?php
			$cap_opt=$_REQUEST['cap_opt'];
			$cap_date=$_REQUEST['cap_date'];
			$strQry = "select cap_batch.*,patient_data.fname,patient_data.mname,patient_data.lname from cap_batch join patient_data on patient_data.id=cap_batch.patient_id where cap_main_id='$cap_main_id' group by cap_batch.id order by patient_data.lname asc,patient_data.fname asc";
			$den_qry=imw_query($strQry);
			$tot_sum=0;
			if(imw_num_rows($den_qry)>0){
				while($den_fet=imw_fetch_array($den_qry)){
				?>						
				<tr>
                	<td width="<?php echo $left_margin;?>">&nbsp;</td>
					<td  class="text_10" style="width:270px;  text-align:left;">
						<?php
						 $patientName = ucwords(trim($den_fet['lname'].", ".$den_fet['fname']." ".$den_fet['mname']));
						 echo $patientName .' - '.$den_fet['patient_id']; 
						 $tot_pat_arr[$den_fet['patient_id']]=$patientName;
						?>
					</td>
					<td  class="text_10" style="width:100px; text-align:left;">
						<?php
							echo $den_fet['encounter_id'];
						?>
					</td>
					<td  class="text_10" style="width:100px; text-align:center;">
						<?php
							$dat_exp_show_dos = explode("-", $den_fet['dos']);
							$shw_dat_dos = date('m-d-Y',mktime(0,0,0,$dat_exp_show_dos[1],$dat_exp_show_dos[2],$dat_exp_show_dos[0]));
							echo $shw_dat_dos;
						?>
					</td>
					<td  class="text_10" style="width:100px; text-align:center;">
						<?php
							if($den_fet['doc']!="0000-00-00"){
								$dat_exp_show_doc = explode("-", $den_fet['doc']);
								$shw_dat_doc = date('m-d-Y',mktime(0,0,0,$dat_exp_show_doc[1],$dat_exp_show_doc[2],$dat_exp_show_doc[0]));
								echo $shw_dat_doc;
							}
						?>
					</td>
					<td  class="text_10" style="width:100px; text-align:right;">
						<?php echo numberFormat($den_fet['charges'],2,'yes'); $tot_amt_arr[]=$den_fet['charges']; ?>
					</td>
					<td  class="text_10" style="width:100px; text-align:right;">
						<?php echo numberFormat($den_fet['balance'],2,'yes'); $tot_bal_arr[]=$den_fet['balance']; ?>
					</td>
					<td  class="text_10" style="width:100px; text-align:right;">
						<?php echo numberFormat($den_fet['copay'],2,'yes'); $tot_copay_arr[]=$den_fet['copay']; ?>
					</td> 
					<td  class="text_10" style="width:100px; text-align:right; padding-right:20px;">
						<?php echo numberFormat($den_fet['pending_copay'],2,'yes'); $tot_pend_copay_arr[]=$den_fet['pending_copay']; ?>
					</td> 
				</tr>
				<?php
				}
				?>
				<?php
			}
			if(imw_num_rows($den_qry)==0){
				?>
				<tr><td colspan="7" style="text-align:center;" class="text_b"><b>No Record Found</b></td></tr>
			<?php }else{
			?>
				<tr>
                	<td width="<?php echo $left_margin;?>">&nbsp;</td>
					<td  class="text_b" style="text-align:left;">
						Total Patient : <?php echo count($tot_pat_arr); ?>
					</td>
					<td  class="text_b" style="text-align:left;">&nbsp;</td>
					<td  class="text_b" style="text-align:center;">&nbsp;</td>
					<td  class="text_b" style="text-align:center;">&nbsp;</td>
					<td  class="text_b" style="text-align:right;">
						<?php echo numberFormat(array_sum($tot_amt_arr),2,'yes'); ?>
					</td>
					<td  class="text_b" style="text-align:right;">
						<?php echo numberFormat(array_sum($tot_bal_arr),2,'yes'); ?>
					</td>
					<td  class="text_b" style="text-align:right;">
						<?php echo numberFormat(array_sum($tot_copay_arr),2,'yes'); ?>
					</td>
					<td  class="text_b" style="text-align:right; padding-right:20px;">
						<?php echo numberFormat(array_sum($tot_pend_copay_arr),2,'yes'); ?>
					</td> 
				</tr>
			 <?php
			}
			?> 
    	</table>
</page>
<?php
$file_content = ob_get_contents();
ob_end_clean();

if($file_content){
		$file_location = write_html($file_content);
	?>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
	<script type="text/javascript">
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		top.html_to_pdf('<?php echo $file_location; ?>','l');
		window.close();
	</script>
<?php
}
?>
