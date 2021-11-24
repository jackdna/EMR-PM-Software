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
	Purpose: Delete Groups
	Access Type: Direct
*/
include_once '../../../config/globals.php';

if(isset($_REQUEST['ajax_request']) && empty($_REQUEST['ajax_request']) === false){
	//For deleting the selected group
	if(isset($_REQUEST['group_del_reason']) && empty($_REQUEST['group_del_reason']) === false){
		$gro_id = explode(',',$_REQUEST['gro_id']);
		$del_reason = imw_real_escape_string($_REQUEST['group_del_reason']);
		$counter = 0;
		foreach($gro_id as $key => $val){
			$sql = "UPDATE `groups_new` SET `del_status`=1, `del_operator`='".$_SESSION['authId']."', `del_date_time`='".date("Y-m-d H:i:s",time())."', `del_reason`='".$del_reason."' 	WHERE `gro_id`='".$val."'";
			$qry = imw_query($sql);
			if($qry){
				$counter = ($counter + imw_affected_rows());
			}
		}
		
		if($counter == count($gro_id)){
			echo "success";
		}else{
			echo "error";
		}
	}
	
	//For getting group listing
	if(isset($_REQUEST['get_listing']) || $_REQUEST['get_listing'] == 'null' || $_REQUEST['get_listing'] == 'undefined'){
		$gro_id = $_REQUEST['get_listing'];
		$html = '';
		$qry = "select * from groups_new where `del_status`='0' order by name";
		$res = get_array_records_query($qry);
		$data = '';
		if(count($res) > 0 ){
			for($i=0;$i<count($res);$i++){
				$group_Address_1=$group_Address="";
				if (!empty($res[$i]['group_Address2']))  { $group_Address_1= ', '.$res[$i]['group_Address2']; };
				$zip = "";
				if($res[$i]['group_Zip']){
					$zip .= $res[$i]['group_Zip'];
				}
				if($res[$i]['zip_ext']){
					$zip .= "-".$res[$i]['zip_ext'];
				}
				 
				$group_Address = trim($res[$i]['group_Address1'] ).''.$group_Address_1.'<br>';
				$group_Address .= $zip.' '.$res[$i]['group_City'].', '.$res[$i]['group_State'];
				$email_length=strlen($res[$i]['group_Email']);
				if($email_length > 15)
				{
					$res[$i]['group_Email']=substr($res[$i]['group_Email'],0,28)."...";
				}
				if($res[$i]['group_Address1'] == '' || $res[$i]['group_Address1'] == '' || $res[$i]['group_Zip'] == ''){
					$group_Address = '';
				}
				
				$hover = (!empty($gro_id) && $res[$i]['gro_id']==$gro_id) ? 'hover' : '';
				$onClick = 'onClick="top.show_loading_image(\'show\');window.location=\'index.php?act=edit_group&gro_id='.$res[$i]['gro_id'].'\'"';
				$onClick_email = 'onClick="top.show_loading_image(\'show\');window.location=\'index.php?act=email_config&gro_id='.$res[$i]['gro_id'].'\'"';
				$onClick_del = 'onClick="return confirm_del(\''.$res[$i]['gro_id'].'\');"';
				$color = 'N/A';
				if($res[$i]['group_color'])
				{
					$color = '<div style="background-color:'.$res[$i]['group_color'].'; width:30px; height:10px;">&nbsp;</div>';	
				}
				$phone = core_phone_format($res[$i]['group_Telephone']);
				if($res[$i]['group_Telephone_ext']!=''){ $phone .= '<br>Ext. '.$res[$i]['group_Telephone_ext']; }
				
				$html	.=	'<tr id="g'.$res[$i]['gro_id'].'" class="pointer '.$hover.'" >';
				$html	.=	'<td><div class="checkbox"><input type="checkbox" name="id" class="chk_sel" id="chk_sel_'.$res[$i]['gro_id'].'" value="'.$res[$i]['gro_id'].'"><label for="chk_sel_'.$res[$i]['gro_id'].'"></label></div></td>';
				$html	.=	'<td '.$onClick.'>'.ucwords(stripslashes($res[$i]['name'])).'</td>';
				$html	.=	'<td '.$onClick.'>'.ucwords($res[$i]['group_NPI']).'</td>';
				$html	.=	'<td '.$onClick.' class="text-left">'.$color.'</td>';
				$html	.=	'<td '.$onClick.'>'.ucwords(stripcslashes($group_Address)).'</td>';
				$html	.=	'<td '.$onClick.'>'.htmlspecialchars($res[$i]['group_Email']).'</td>';
				$html	.=	'<td '.$onClick.'>'.$phone.'</td>';
				$html	.=	'<td '.$onClick.'>'.core_phone_format($res[$i]['group_Fax']).'</td>';
				$html	.=	'<td '.$onClick.'>'.ucwords($res[$i]['Contact_Name']).'</td>';
				$html	.=	'<td '.$onClick_email.' class="text-center pointer"  >';
				$html	.=	'<img src="'.$GLOBALS['webroot'].'/library/images/mail_icon.png" alt="Email">';
				$html	.=	'</td>';
				$html	.=	'</tr>';
			}
		}
		else{
			$html = '<tr><td colspan="10" class="text-center bg-info">no record found</td></tr>';
		}
		echo $html;
	}
	
	//Multiple NPI Requests
	if(isset($_REQUEST['npi_request']) && empty($_REQUEST['npi_request']) === false){
		$npi_dt = '';
		if(isset($_REQUEST['npi_mode']) && empty($_REQUEST['npi_mode']) === false){
			$npi_mode = $_REQUEST['npi_mode'];
			switch($npi_mode){
				case 'view':
					$arr_ins_type = explode(',',$_REQUEST['ins_grp_arr']);
					$npi_dt = '';
					$qry = "Select * from groups_npi WHERE group_id='".$_REQUEST['group_id']."' ORDER BY id";
					$rs = imw_query($qry);
					$i = 1;
					$rows=imw_num_rows($rs);
					while($res=imw_fetch_assoc($rs)){
						$ins_options=$default_sel='';
						//INS TYPES
						foreach($arr_ins_type as $key => $val){
							$sel='';
							if($res['ins_type']==$val)$sel='SELECTED';
							
							$ins_options.='<option value="'.$val.'" '.$sel.'>'.$val.'</option>';
						}
						
						if($res['default_val']=='1'){ $default_sel='CHECKED';$default_no=$i; }
						
						$npi_dt.='<tr id="npiRow'.$i.'"><td><input type="text" name="npi_name'.$i.'" id="npi_name'.$i.'" value="'.$res['npi'].'" class="form-control" />';
						$npi_dt.='<input type="hidden" name="id'.$i.'" id="id'.$i.'" value="'.$res['id'].'"/>';
						$npi_dt.='</td>';
						$npi_dt.='<td><select name="ins_type'.$i.'" id="ins_type'.$i.'" class="selectpicker" data-width="100%" data-size="5" data-container="#select_box">';
						$npi_dt.= $ins_options;
						$npi_dt.='</select></td>';
						$npi_dt.='<td class="text-center"><div class="radio"><input type="radio" name="default_npi" id="default_npi'.$i.'" value="1" '.$default_sel.' onClick="javascript:$(\'#default_npi_num\').val('.$i.');" /><label for="default_npi'.$i.'"></label></div></td>';
						
						if($rows==$i){
							$npi_dt.='<td class="pt10 text-center pointer" style="vertical-align:middle" onClick="addNPIRows('.$i.');"><img id="add_npi_row'.$i.'" title="Add Row" src="'.$GLOBALS['webroot'].'/library/images/add_icon.png" alt="Add More"  ></td>';
						}else{
							$npi_dt.='<td class="pt10 text-center pointer" style="vertical-align:middle" onClick="top.fancyConfirm(\'Are you sure you want to delete this record\',\'\',\'top.fmain.removeNPI('.$i.')\');"><img id="add_npi_row'.$i.'" title="Delete Row" src="'.$GLOBALS['webroot'].'/library/images/closerd.png" alt="Delete Row" ></td>';
						}
						$npi_dt.='</tr>';
						
						$saved_npi=$i;
						$i++;
					}	
					echo $npi_dt.'~'.$saved_npi.'~'.$default_no;
				break;
				
				case 'delete':
					$counter = 0;
					if($_REQUEST['id']>0){
						$qry= "Delete FROM groups_npi WHERE id='".$_REQUEST['id']."'";
						imw_query($qry);
						$counter = ($counter + imw_affected_rows());
					}
					$npi_dt = $counter;
					echo $npi_dt;
				break;
				
				case 'save':
					$arrForm = $_REQUEST;
					$default_npi_num = $arrForm['default_npi_num'];
					$totNPIRows = $arrForm['totNPIRows'];
					$group_id = $arrForm['group_id'];
					$defaultNPI='';
					
					for($i=1; $i<=$totNPIRows; $i++){
					
						if($arrForm['npi_name'.$i]!=''){
							$default=0;
							if($default_npi_num==$i){ $default=1; $defaultNPI=$arrForm['npi_name'.$i]; }
							
							$prefix="Insert INTO ";
							$where='';

							if($arrForm['id'.$i]>0){
								$prefix="Update ";
								$where=" WHERE id='".$arrForm['id'.$i]."'";
							}
							
							$qry= $prefix." groups_npi SET 
							group_id='".$group_id."',
							npi='".$arrForm['npi_name'.$i]."',
							ins_type='".$arrForm['ins_type'.$i]."',
							default_val='".$default."'
							".$where;
					
							imw_query($qry);
						}
					}	
					
					if(empty($defaultNPI)==false && $group_id > 0){
						$qry = "Update groups_new SET group_NPI='".$defaultNPI."' WHERE gro_id='".$group_id."'";
						$saved = imw_query($qry);
						if($saved){
							echo 'parent_npi_saved~'.$defaultNPI;
						}
					}
				break;
			}
		}
	}
	exit;
}
?>