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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/Fu.php');
$msgConsoleObj = new msgConsole();
$fu = new Fu();

//pdf printing related files
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

$pageHt = '10mm';
$pdf_css = '<style>
			.text_b_w{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#FFFFFF;
				background-color:#4684ab;
			}
			.text_12{
				font-size:10px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.text_12b{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;font-weight:bold;
				background-color:#FFFFFF;
			}
			.text_10b{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				background-color:#FFFFFF;
			}
			.text_10{
				font-size:9px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.text_b_w_9{
				font-size:9px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#FFFFFF;
				background-color:#4684ab;
			}
			.textBold{ font-weight:bold;}
			.table_collapse{
				width:100%; padding:0px; border-collapse:collapse;
			}
			.cellBorder7 td{border:1px solid #eee;
				}
			.section_header{
				background:#CCDDE6 url(images/page_block_heading_sm.gif) repeat-x; border-bottom:2px solid #ffffff; padding:2px; 
				font-weight:bold; color:#000000; font-size:100%; font-family:Arial, Helvetica, sans-serif;
			}
}
		</style>
		<page backtop="'.$pageHt.'" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';

$task = $_REQUEST['task'];

switch($task)
{
	case 'ap_policies_phy':
		$name_arr = $msgConsoleObj->get_username_by_id(array(0=>$msgConsoleObj->operator_id));
		$physician_name = $name_arr[$msgConsoleObj->operator_id]['full']; 
		$final_data_arr = $msgConsoleObj->get_ap_policies();
		$result_data_arr = $final_data_arr[0];
		if(count($result_data_arr)>0)
		{
			$pdf_header = '<page_header>	
				<table  class="table_collapse cellBorder3" style="background-color:#FFF3E8;">
				<tr class="text_b_w"><td colspan="7" class="text_b_w">A &amp; P Policies For '.$physician_name.'</td></tr>
				<tr class="subheading text_b_w">
							<td width="80" align="left" class="text_b_w">Findings</td>
							<td width="120" align="left" class="text_b_w">Assessment</td>
							<td width="250" align="left" class="text_b_w">Plan</td>
							<td width="100" align="left" class="text_b_w">Order Set</td>
							<td width="100" align="left" class="text_b_w">Orders</td>
							<td width="80" align="left" class="text_b_w">CPT Code</td>
							
				</tr>
				</table>
				</page_header>';
            
				$pdf_data = '<table  class="table_collapse cellBorder3" >';
				$final_data_arr = $msgConsoleObj->get_ap_policies();
				$result_data_arr = $final_data_arr[0];
				foreach($result_data_arr as $key => $row)
				{
					$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
					$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
					$tmpCptcd = $row["strCptCd"];
					$order_set_id_arr = preg_split('/,/',$row['order_set_name']);
					$order_set_name_arr = array();
					for($o=0;$o<count($order_set_id_arr);$o++){
						$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
					}
					$order_set_name_str = join(', ',$order_set_name_arr);
					if(trim($order_set_name_str) == ''){
						$order_set_name_str = '&nbsp;';
					}
					$follow_up = "";
					if(!empty($row["xmlFU"])){
						list($len_arrFu,$arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
						if(count($arrFu) > 0){
							foreach($arrFu as $val){
								$tmp = trim($val["number"]." ".$val["time"]." ".$val["visit_type"]);
								if(!empty($tmp)){
									$follow_up .= "<br>".$tmp."";
								}
							}
						}
					}	
					if($row['to_do']=="yes") { $chk = 'checked="checked"'; $status="yes";}else{ $chk=''; $status="no";}
					$findings_view = (!empty($row['task'])) ? str_replace(",",",<br>",$row["task"]) : "&nbsp;" ;
					$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan'])."<b>".$follow_up."</b>" : "&nbsp;";
					$pdf_data.= '<tr class="link_cursor text_12" onclick="$(\'#console_form\').attr(\'src\',\'console_to_do.php?editid='.$row['to_do_id'].'\');">
										<!-- <td><input type="checkbox" name="todo" '.$chk.' value="'.$row['to_do_id'].'"></td> -->
										<td style="width:80px;vertical-align:top">'.$findings_view.'</td>
										<td style="width:120px;vertical-align:top"> '.$assess.'</td>
										<td style="width:250px;vertical-align:top">'.$follow_up_view.'</td>
										<td style="width:100px;vertical-align:top">'.$row['order_set_name'].'</td>
										<td style="width:100px;vertical-align:top">'.$row['order_name'].'</td>
										<td style="width:80px;vertical-align:top">'.$tmpCptcd.'</td>
										
									</tr><tr><td colspan="5" style="height:2px"></td></tr>';	
						
				}
			$pdf_data .= '</table></page>';
			
		}
	break;
	case 'ap_policies_community':
			
			$name_arr = $msgConsoleObj->get_username_by_id(array(0=>$msgConsoleObj->operator_id));
			$physician_name = $name_arr[$msgConsoleObj->operator_id]['full']; 
			$final_data_arr = $msgConsoleObj->get_ap_policies();
			$result_data_arr = $final_data_arr[1];
			if(count($result_data_arr)>0)
			{
				$pdf_header = '<page_header>	
				<table  class="table_collapse cellBorder3" style="background-color:#FFF3E8;">
				<tr class="text_b_w"><td colspan="7" class="text_b_w">A &amp; P Policies For Community</td></tr>
				<tr class="subheading text_b_w">
							<td width="80" align="left" class="text_b_w">Findings</td>
							<td width="130" align="left" class="text_b_w">Assessment</td>
							<td width="250" align="left" class="text_b_w">Plan</td>
							<td width="100" align="left" class="text_b_w">Order Set</td>
							<td width="100" align="left" class="text_b_w">Orders</td>
							<td width="80" align="left" class="text_b_w">CPT Code</td>
				</tr>
				</table>
				</page_header>';

				$pdf_data = '<table  class="table_collapse cellBorder3" >';
				foreach($result_data_arr as $key => $row)
					{
						$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
						$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
						$tmpCptcd = $row["strCptCd"];
				
						$order_set_id_arr = preg_split('/,/',$row['order_set_name']);
						$order_set_name_arr = array();
						for($o=0;$o<count($order_set_id_arr);$o++){
							$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
						}
						$order_set_name_str = join(', ',$order_set_name_arr);
						if(trim($order_set_name_str) == ''){
							$order_set_name_str = '&nbsp;';
						}
						$follow_up = "";
						if(!empty($row["xmlFU"])){
							list($len_arrFu,$arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
							if(count($arrFu) > 0){
								foreach($arrFu as $val){
									$tmp = trim($val["number"]." ".$val["time"]." ".$val["visit_type"]);
									if(!empty($tmp)){
										$follow_up .= "<br>".$tmp."";
									}
								}
							}
						}	
						if($row['to_do']=="yes") { $chk = 'checked="checked"'; $status="yes";}else{ $chk=''; $status="no";}
						$findings_view = (!empty($row['task'])) ? str_replace(",",",<br>",$row["task"]) : "&nbsp;" ;
						$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan'])."<b>".$follow_up."</b>" : "&nbsp;";
						
						$pdf_data .= '<tr class="link_cursor text_12">
											<td style="width:80px;vertical-align:top">'.$findings_view.'</td>
											<td style="width:130px;vertical-align:top">'.$assess.'</td>
											<td style="width:250px;vertical-align:top">'.$follow_up_view.'</td>
											<td style="width:100px;vertical-align:top">'.$row['order_set_name'].'</td>
											<td style="width:100px;vertical-align:top">'.$row['order_name'].'</td>
											<td style="width:80px;vertical-align:top">'.$tmpCptcd.'</td>
										</tr><tr><td colspan="5" style="height:2px"></td></tr>';						
					}
				$pdf_data.= '</table></page>';
		}
		break;
		case 'ap_policies_dynamic':
			
			$name_arr = $msgConsoleObj->get_username_by_id(array(0=>$msgConsoleObj->operator_id));
			$physician_name = $name_arr[$msgConsoleObj->operator_id]['full']; 
			$final_data_arr = $msgConsoleObj->get_ap_policies();
			$result_data_arr = $final_data_arr[2];
			if(count($result_data_arr)>0)
			{
				$pdf_header = '<page_header>	
				<table  class="table_collapse cellBorder3" style="background-color:#FFF3E8;">
				<tr class="text_b_w"><td colspan="7" class="text_b_w">A &amp; P Policies For Community</td></tr>
				<tr class="subheading text_b_w">
							<td width="80" align="left" class="text_b_w">Findings</td>
							<td width="130" align="left" class="text_b_w">Assessment</td>
							<td width="250" align="left" class="text_b_w">Plan</td>
							<td width="100" align="left" class="text_b_w">Order Set</td>
							<td width="100" align="left" class="text_b_w">Orders</td>
							<td width="80" align="left" class="text_b_w">CPT Code</td>
				</tr>
				</table>
				</page_header>';

				$pdf_data = '<table  class="table_collapse cellBorder3" >';
				foreach($result_data_arr as $key => $row)
					{
						$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
						$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
						$tmpCptcd = $row["strCptCd"];
				
						$order_set_id_arr = preg_split('/,/',$row['order_set_name']);
						$order_set_name_arr = array();
						for($o=0;$o<count($order_set_id_arr);$o++){
							$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
						}
						$order_set_name_str = join(', ',$order_set_name_arr);
						if(trim($order_set_name_str) == ''){
							$order_set_name_str = '&nbsp;';
						}
						$follow_up = "";
						if(!empty($row["xmlFU"])){
							list($len_arrFu,$arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
							if(count($arrFu) > 0){
								foreach($arrFu as $val){
									$tmp = trim($val["number"]." ".$val["time"]." ".$val["visit_type"]);
									if(!empty($tmp)){
										$follow_up .= "<br>".$tmp."";
									}
								}
							}
						}	
						if($row['to_do']=="yes") { $chk = 'checked="checked"'; $status="yes";}else{ $chk=''; $status="no";}
						$findings_view = (!empty($row['task'])) ? str_replace(",",",<br>",$row["task"]) : "&nbsp;" ;
						$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan'])."<b>".$follow_up."</b>" : "&nbsp;";
						
						$pdf_data .= '<tr class="link_cursor text_12">
											<td style="width:80px;vertical-align:top">'.$findings_view.'</td>
											<td style="width:130px;vertical-align:top">'.$assess.'</td>
											<td style="width:250px;vertical-align:top">'.$follow_up_view.'</td>
											<td style="width:100px;vertical-align:top">'.$row['order_set_name'].'</td>
											<td style="width:100px;vertical-align:top">'.$row['order_name'].'</td>
											<td style="width:80px;vertical-align:top">'.$tmpCptcd.'</td>
										</tr><tr><td colspan="5" style="height:2px"></td></tr>';						
					}
				$pdf_data.= '</table></page>';
		}
		break;
}
$pdf_write_date = 	$pdf_css.$pdf_header.$pdf_data;
$flName = 'phy_console'.$_SESSION['authId'].".html";
$file_location = write_html($pdf_write_date, $flName);

?>
<?php if(isset($flName)){?>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','p','',true,false);
</script>

<script>//window.open('../common/<?php echo $htmlFilePth;?>?font_size=10&page=4&htmlFileName=<?php echo $flName?>','Print_Data');</script>
<?php }?>
