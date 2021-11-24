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
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

$msgConsoleObj = new msgConsole();
$final_arr = $msgConsoleObj->get_tests_tasks('completed_tasks');
$result_data_arr = $final_arr['result_arr'];
$order_data_arr  = $final_arr['orders_arr'];
$users_arr = $final_arr['users_arr'];
	if(count($result_data_arr)>0 || count($order_data_arr)>0){
		$tableElem .= '<table  cellpadding="0" cellspacing="0" style="width:700px;" align="center" class="border">
							<thead>
								<tr>
									<th class="tb_heading alignLeft pdl5" style="width:700px;" colspan="6">Completed Tasks</th>
								</tr>
								<tr>									
									<td class="tb_subheading alignLeft pdl5 bdrright bdrbtm" style="width:100px;color:#000;">Date Assigned</td>
									<td class="tb_subheading alignLeft pdl5 bdrright bdrbtm" style="width:110px;color:#000;">Date Completed</td>
									<td class="tb_subheading alignLeft pdl5 bdrright bdrbtm" style="width:120px;color:#000;">Patient Name</td>
									<td class="tb_subheading alignLeft pdl5 bdrright bdrbtm" style="width:160px;color:#000;">Subject</td>
									<td class="tb_subheading alignLeft pdl5 bdrright bdrbtm" style="width:95px;color:#000;">Assigned By</td>	
									<td class="tb_subheading alignLeft pdl5 bdrbtm" style="width:95px;color:#000;">Completed By</td>
								</tr>
							</thead>
						 ';
		foreach($result_data_arr as $key => $val_arr)
			{
				$username = $users_arr[$val_arr['performedBy']];
				$TempPtNameVal = explode(' - ',$val_arr['patient_name']);
				$ptName = trim($val_arr['patient_name']);
				$ptId = trim($TempPtNameVal[1]);
			
				if($val_arr['tb_name']=='user_messages')
				{
					$subject_view = strtoupper($val_arr['message_subject']);					
				}
				else
				{
					$subject_view = strtoupper($val_arr['TableName']);					
				}
				
				if($val_arr['tb_name']!='user_messages'){
				$completed_by = $users_arr[$val_arr['completed_by']];
				$tableElem .= '<tr>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;">'.$val_arr['taskDate'].'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:110px;">'.$val_arr['cur_date'].'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:120px;">'.$ptName.'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:160px;">'.$subject_view.'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;">'.$username.'</td>
									<td class="text_value bdrbtm pdl5 ptp5" style="width:95px;">'.$completed_by.'</td>
								</tr>';
				}
				if($val_arr['tb_name']=='user_messages'){
					$tableElem_msg .= '<tr>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;">'.$val_arr['taskDate'].'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:110px;">'.$val_arr['cur_date'].'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:120px;">'.$patient_name.'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:160px;">'.$subject_view.'</td>
									<td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;">'.$username.'</td>
									<td class="text_value bdrbtm pdl5 ptp5" style="width:95px;">'.$val_arr['completed_by_name'].'</td>
								</tr>';
						{			  
						$tableElem_msg .= '<tr>
												<td colspan="6" class="text_value bdrbtm pdl5 ptp5" style="width:700px;">'.nl2br(html_entity_decode($val_arr['message_text'])).'</td>
											</tr>';
						}			
				}
				
			}
		$qry = "select * from order_sets order by createdy_on desc";
			$orderSetDetails = $msgConsoleObj->create_array_from_qry($qry);
			$orderSetArr = array();
			for($i=0;$i<count($orderSetDetails);$i++){
				$id = $orderSetDetails[$i]['id'];
				$orderSetArr[$id] = $orderSetDetails[$i];
			}
			
			$qry = "select * from order_details order by created_on desc";
			$ordersQryRes = $msgConsoleObj->create_array_from_qry($qry);
			$ordersDetailsArr = array();
			for($o=0;$o<count($ordersQryRes);$o++){
				$id = $ordersQryRes[$o]['id'];
				$ordersDetailsArr[$id] = $ordersQryRes[$o];
			}		
			
			$previous_primary_set_id = 0;
			$orderSetContentData = '';
			$ordersContentData = '';
			
			$ordersQryRes = $final_arr['orders_arr'];			
		  for($i=0,$q=1;$i<count($ordersQryRes);$i++){
			  $order_set_id = $ordersQryRes[$i]['order_set_id'];
			  $patient_name = $ordersQryRes[$i]['lname'].', ';
			  $patient_name .= $ordersQryRes[$i]['fname'].' ';
			  $patient_name .= $ordersQryRes[$i]['mname'];
			  $patient_name = trim(ucfirst($patient_name));
			  if($patient_name[0] == ','){
				  $patient_name = substr($patient_name,1);
			  }
			  $patient_name .= ' - '.$ordersQryRes[$i]['patient_id'];
			  //---  GET ALL ORDER SETS  ---------
			  if($order_set_id > 0){
				  $c_date = $ordersQryRes[$i]['c_date'];
				  $modified_date = $ordersQryRes[$i]['m_date'];
				  $order_id = $ordersQryRes[$i]['order_id'];
				  $main_id = $ordersQryRes[$i]['order_set_associate_details_id'];
				  $order_detail_arr = $ordersDetailsArr[$order_id];
				  $order_name = $order_detail_arr['name'];
				  $logged_provider_id = $ordersQryRes[$i]['logged_provider_id'];
				  $provider_name = $users_arr[$logged_provider_id];
				  $os_chk_val = 'order_set_associate_chart_notes_details-'.$main_id;
				  
				  $primary_set_id = $ordersQryRes[$i]['primary_set_id'];
				  if($primary_set_id != $previous_primary_set_id){
					  $order_set_arr = $orderSetArr[$order_set_id];
					  $orderset_name = $order_set_arr['orderset_name'];
					  $orderSetContentData .= '
						  <tr>
							  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:205px;border:1px solid red;" colspan="2">&nbsp;</td>
							  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:120px;border:1px solid red;"><b>Order Set</b></td>
							  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:160px;border:1px solid red;">'.$orderset_name.'</td>
							  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;border:1px solid red;">'.$provider_name.'</td>					
							  <td class="text_value bdrbtm pdl5 ptp5" style="width:95px;border:1px solid red;" >&nbsp;</td>					
						  </tr>';
					  $previous_primary_set_id = $primary_set_id;
				  }
				  $orderSetContentData .= '
					  <tr>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright"  style="width:95px;border:1px solid blue;">'.$c_date.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright"  style="width:110px;border:1px solid blue;">'.$modified_date.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright"  style="width:120px;border:1px solid blue;">'.$patient_name.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright"  style="width:160px;border:1px solid blue;">'.$order_name.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright"  style="width:95px;border:1px solid blue;" >&nbsp;</td>
						  <td class="text_value bdrbtm pdl5 ptp5"  style="width:95px;border:1px solid blue;" >&nbsp;</td>
					  </tr>';
			  }
			  else{
			  //---  GET ALL SINGLE ORDERS WITHOUT ORDER SETS ---------
				  $c_date = $ordersQryRes[$i]['c_date'];
				  $modified_date = $ordersQryRes[$i]['m_date'];
				  $order_id = $ordersQryRes[$i]['order_id'];
				  $main_id = $ordersQryRes[$i]['order_set_associate_details_id'];
				  $order_detail_arr = $ordersDetailsArr[$order_id];
				  $order_name = $order_detail_arr['name'];
				  $logged_provider_id = $ordersQryRes[$i]['logged_provider_id'];
				  $provider_name = $usernameArr[$logged_provider_id];
				  //---- ORDERS STATUS CHECK -----			
				  $status_option = '';
				  $provider_status = $ordersQryRes[$i]['orders_status'];
				  for($p=0;$p<count($opArr);$p++){
					  $sel = $p == $provider_status ? 'selected="selected"' : '';
					  $status_option .= '
						  				<option value="$p" $sel>$opArr[$p]</option>'
						  				;
				  }
				 
				  
				  $ordersContentData .=' 
					  <tr height="20" bgcolor="$bgcolor"  valign="top">
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;" valign="top">'.$c_date.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:110px;" valign="top">'.$modified_date.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:120px;" valign="top">'.$patient_name.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:160px;" valign="top">'.$order_name.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5 bdrright" style="width:95px;" valign="top">'.$provider_name.'</td>
						  <td class="text_value bdrbtm pdl5 ptp5" style="width:95px;">&nbsp;</td>
					  </tr>';
			  }
}
//-----SHOW COMPLETED MESSAGES------
if($tableElem_msg != ""){
	$tableElem .='<tr>
			<td class="tb_subheading bdrbtm ptp5" valign="top" colspan="6" style="width:700px;" >Messages</td>
		</tr>'.
		$tableElem_msg;
}
			
//--- SHOW COMPLETED ORDER SET DATA ----
if(trim($orderSetContentData) != ''){
	$tableElem .='
		<tr>
			<td class="tb_subheading bdrbtm ptp5" valign="top" colspan="6" style="width:700px;">Order Sets</td>
		</tr>'.
		$orderSetContentData;
}
//--- SHOW COMPLETED ORDERS DATA ----
if(trim($ordersContentData) != ''){
	$tableElem .='
		<tr>
			<td class="tb_subheading bdrbtm ptp5" valign="top" colspan="6">Orders</td>
		</tr>'.
		$ordersContentData;
}			
			$tableElem .= '</table>';	
		}
		else
		{
			$tableElem = '<table  cellpadding="0" cellspacing="0" style="width:700px;" align="center" class="border"><tr><td class="warning " style="width:700px;">No Record Available</td></tr> ';	
		}
	  $tableElem.='<style>
					  .ptp5{padding-top:5px;}
					  .tb_subheading{font-size:14px;font-weight:bold;color:#000000;background-color:#f3f3f3;}
					  .tb_heading{font-size:14px;font-weight:bold;color:#000;background-color:#CCC;padding:3px 0px 3px 0px;vertical-align:middle;}
					  .text_value{font-size:14px;	font-weight:100;background-color:#FFFFFF;}
					  .border{border:1px solid #C0C0C0;}
					  .bdrbtm{border-bottom:1px solid #C0C0C0;height:13px;vertical-align:top;padding-top:2px;padding-left:3px;}
					  .bdrtop{border-top:1px solid #C0C0C0;height:15px;vertical-align:top;}
					  .pdl5{padding-left:5px;}
					  .bdrright{border-right:1px solid #C0C0C0;}
					  .alignCenter{text-align:center;}
					  .alignLeft{text-align:left;}
					  .warning{color:#F00;font-size:14px;text-align:center;}
				  </style>';

$print_file_name = "completed_tasks_".$_SESSION["authId"];
$file_location = write_html($tableElem,$print_file_name.".html");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','p','',true,false);
</script>

