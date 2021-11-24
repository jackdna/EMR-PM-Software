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
require_once("../admin_header.php");

//--- get recent selected patient ------
$auth_id = $_SESSION['authId'];
if($_POST['seq']>0){
	for($i=0;$i<=$_POST['seq'];$i++){
		$vital_id_edit=$_POST['edit_id'.$i];
		$status_id=$_POST['status'.$i];
		if($_POST['switch']=='english'){
			$vital_sign_unit1=htmlentities(addslashes($_POST['english_'.$i]));
			$lower_limit=htmlentities(addslashes($_POST['lower_limit'.$i]));
			$upper_limit=htmlentities(addslashes($_POST['upper_limit'.$i]));
			
			if($vital_id_edit == "6" || $vital_id_edit == "7" || $vital_id_edit == "8"){				
				$up_vital=imw_query("update  vital_sign_limits set 
									vital_sign_unit='$vital_sign_unit1',
									vital_sign_unit_sec='$vital_sign_unit_sec',
									lower_limit='$lower_limit',upper_limit='$upper_limit',
									modified_on=now(),modified_by='$auth_id',status='$status_id',
									vital_sign_unit_english = '$vital_sign_unit1',									
									lower_limit_english = '$lower_limit',
									upper_limit_english = '$upper_limit'									
									where id='$vital_id_edit'");
			}
			else{
				$up_vital=imw_query("update  vital_sign_limits set 
									vital_sign_unit='$vital_sign_unit1',
									vital_sign_unit_sec='$vital_sign_unit_sec',
									lower_limit='$lower_limit',upper_limit='$upper_limit',
									modified_on=now(),modified_by='$auth_id',status='$status_id',
									vital_sign_unit_english = '$vital_sign_unit1'									
									where id='$vital_id_edit'");
			}						
		}
		else{
			$vital_sign_unit1=htmlentities(addslashes($_POST['metric_'.$i]));
			$lower_limit=htmlentities(addslashes($_POST['lower_limit'.$i]));
			$upper_limit=htmlentities(addslashes($_POST['upper_limit'.$i]));
			
			if($vital_id_edit == "6" || $vital_id_edit == "7" || $vital_id_edit == "8"){				
				$up_vital=imw_query("update  vital_sign_limits set 
										vital_sign_unit='$vital_sign_unit1',
										vital_sign_unit_sec='$vital_sign_unit_sec',
										lower_limit='$lower_limit',upper_limit='$upper_limit',
										modified_on=now(),modified_by='$auth_id',status='$status_id',
										vital_sign_unit_metric = '$vital_sign_unit1',									
										lower_limit_metric  = '$lower_limit',
										upper_limit_metric = '$upper_limit'									
										where id='$vital_id_edit'");
			}
			else{
				$up_vital=imw_query("update  vital_sign_limits set 
						vital_sign_unit='$vital_sign_unit1',
						vital_sign_unit_sec='$vital_sign_unit_sec',
						lower_limit='$lower_limit',upper_limit='$upper_limit',
						modified_on=now(),modified_by='$auth_id',status='$status_id',
						vital_sign_unit_metric = '$vital_sign_unit1'
						where id='$vital_id_edit'");
			}						
		}
	}
	header("Location: index.php?msg=1");	
}

$sel_met2=imw_query("select vital_sign_unit from vital_sign_limits where vital_sign='Weight'");
$row_met2=imw_fetch_array($sel_met2);
if($row_met2['vital_sign_unit']=='kg'){
	$lod_unit='metric';
}else{
	$lod_unit='english';
}
?>
<script type="text/javascript">
	var ar = [["VS_save","Save","top.fmain.frm_submit();"]];
	top.btn_show("ADMN",ar);	
	function show_unit(val){
		if(val=='english'){
			for(var i=1;i<=11;i++){
				if(document.getElementById('english_'+i)) {
					document.getElementById('english_'+i).style.display='';
					document.getElementById('metric_'+i).style.display='none';
					if(i == 6 || i == 7 || i == 8){
						document.getElementById('lower_limit'+i).value = document.getElementById('hidEnglishLower_limit'+i).value;
						document.getElementById('upper_limit'+i).value = document.getElementById('hidEnglishUpper_limit'+i).value;
					}
				}	
			
			}
		}else if(val=='metric'){
			for(var k=1;k<=11;k++){
				if(document.getElementById('metric_'+k)) {
					document.getElementById('metric_'+k).style.display='';
					document.getElementById('english_'+k).style.display='none';						
					if(k == 6 || k == 7 || k == 8) {						
						document.getElementById('lower_limit'+k).value = document.getElementById('hidMetricLower_limit'+k).value;
						document.getElementById('upper_limit'+k).value = document.getElementById('hidMetricUpper_limit'+k).value;
					}
				}
			}
		}
	}
	function frm_submit(){
		document.frm.submit();
	}
	
	function show2(){
		if (!document.all&&!document.getElementById)
		return
	}
</script>
<body onLoad="show_unit('<?php echo $lod_unit;?>');">
<div class="container-fliud">
	<div class="whtbox">
		<form action="index.php" name="frm" method="post">
			<div class="row">
				<div class="col-sm-12">
					<div class="row head">
						<div class="col-sm-3">
							<span>Switch All Vital Sign</span>
						</div>
						<div class="col-sm-9 content_box">
							<div class="radio radio-inline">
								<input type="radio" id="english" value="english" name="switch" onClick="show_unit('english');" <?php if($lod_unit=='english'){ echo "checked";}?> />
								<label for="english">English</label>
							</div>
							<div class="radio radio-inline" style="vertical-align:bottom">
								<input type="radio" id="metric" value="metric" name="switch" onClick="show_unit('metric');" <?php if($lod_unit=='metric'){ echo "checked";}?> />
								<label for="metric">Metric</label>
							</div>
						</div>	
					</div>
				</div>

				<div class="col-sm-12">
					<div class="table-responsive respotable tblBg">
						<table class="table table-bordered adminnw">
							<thead>
								<tr>
									<th>&nbsp;S.No.</th> 
									<th>Vital Sign Name</th>
									<th>Lower Limit</th>
									<th>Upper Limit</th>
									<th>Unit</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$sel_tab_qry1="select * from vital_sign_limits order by id";
								$run_tab1=imw_query($sel_tab_qry1);
								$sn=0;
								$seq=imw_num_rows($run_tab1);

								while($row_tab1=imw_fetch_array($run_tab1)){
									$id = "";
									$id=$row_tab1['id'];
									$vital_sign=$row_tab1['vital_sign'];
									$vital_sign_unit=$row_tab1['vital_sign_unit'];
									$vital_sign_unit_sec=$row_tab1['vital_sign_unit_sec'];
									$lower_limit=$row_tab1['lower_limit'];
									$upper_limit=$row_tab1['upper_limit'];
									$modified_on=$row_tab1['modified_on'];
									$modified_by=$row_tab1['modified_by'];
									$status=$row_tab1['status'];
									$display_order=$row_tab1['display_order'];
								
								$sel_tab_qry2="select lname,fname from users where id='$modified_by'";
								$run_tab2=imw_query($sel_tab_qry2);
								$row_tab2=imw_fetch_array($run_tab2);
								$operater_name=$row_tab2['lname'].', '.$row_tab2['fname'];
								$sn++;	
								if($sn % 2 == 0) { $bgClr = 'alt3'; } else{ $bgClr = '';}
								$arrPain = array("","Mild","Moderate","Severe");
								for($counter = 0; $counter <= 10; $counter++){
									array_push($arrPain,"Scale ".$counter);
								}				
								?>
								<tr>
									<td class="text-center" style="width:30px;"><?php echo $sn; ?>
									<input type="hidden" value="<?php echo $seq;?>" name="seq" id="seq<?php echo $sn;?>">
									<input type="hidden" name="edit_id<?php echo $sn;?>" id="edit_id<?php echo $sn;?>" value="<?php echo $id;?>">
									</td> 
									<td><?php echo $vital_sign; ?></td>
									<td>
										<?php
										if($vital_sign == "Pain"){								
										?>
											<select name="lower_limit<?php echo $sn; ?>" id="lower_limit<?php echo $sn; ?>" class="selectpicker" data-width="100%">
											<?php																		
												foreach ($arrPain as $key => $s) {	
													$value_print = ucfirst($s);																		
													echo "<option value='".$key."'";
													if ($key == $lower_limit){
														echo " selected";
														echo ">".$value_print."</option>\n";
													}else{
														echo ">".$value_print."</option>\n";
													}																				
												}																				
											?>
											</select>
										<?php
										}			
										else if($vital_sign == "Temperature"){								
										?>
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_english']; ?>" name="hidEnglishLower_limit<?php echo $sn; ?>" id="hidEnglishLower_limit<?php echo $sn; ?>">
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_metric']; ?>" name="hidMetricLower_limit<?php echo $sn; ?>" id="hidMetricLower_limit<?php echo $sn; ?>">
											<input class="form-control" type="text" value="<?php echo $lower_limit; ?>" name="lower_limit<?php echo $sn; ?>" id="lower_limit<?php echo $sn; ?>">
										<?php
										}			
										else if($vital_sign == "Height"){								
										?>
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_english']; ?>" name="hidEnglishLower_limit<?php echo $sn; ?>" id="hidEnglishLower_limit<?php echo $sn; ?>">
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_metric']; ?>" name="hidMetricLower_limit<?php echo $sn; ?>" id="hidMetricLower_limit<?php echo $sn; ?>">
											<input class="form-control" type="text" value="<?php echo $lower_limit; ?>" name="lower_limit<?php echo $sn; ?>" id="lower_limit<?php echo $sn; ?>">
										<?php
										}			
										else if($vital_sign == "Weight"){								
										?>
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_english']; ?>" name="hidEnglishLower_limit<?php echo $sn; ?>" id="hidEnglishLower_limit<?php echo $sn; ?>">
											<input type="hidden" value="<?php echo $row_tab1['lower_limit_metric']; ?>" name="hidMetricLower_limit<?php echo $sn; ?>" id="hidMetricLower_limit<?php echo $sn; ?>">
											<input class="form-control" type="text" value="<?php echo $lower_limit; ?>" name="lower_limit<?php echo $sn; ?>" id="lower_limit<?php echo $sn; ?>">
										<?php
										}			
										else{
										?>
												<input class="form-control" type="text" value="<?php echo $lower_limit; ?>" name="lower_limit<?php echo $sn; ?>" id="lower_limit<?php echo $sn; ?>">
											<?php
										}		
									?>				
									</td>
									<td>
										<?php
											if($vital_sign == "Pain"){								
												?>
												<select name="upper_limit<?php echo $sn; ?>" id="upper_limit<?php echo $sn; ?>" class="selectpicker" data-width="100%">
												<?php																		
													foreach ($arrPain as $key => $s) {	
														$value_print = ucfirst($s);																		
														echo "<option value='".$key."'";
														if ($key == $upper_limit){
															echo " selected";
															echo ">".$value_print."</option>\n";
														}else{
															echo ">".$value_print."</option>\n";
														}																				
													}																				
												?>
												</select>
											<?php
											}		
											else if($vital_sign == "Temperature"){								
												?>
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_english']; ?>" name="hidEnglishUpper_limit<?php echo $sn; ?>" id="hidEnglishUpper_limit<?php echo $sn; ?>">
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_metric']; ?>" name="hidMetricUpper_limit<?php echo $sn; ?>" id="hidMetricUpper_limit<?php echo $sn; ?>">
													<input class="form-control" type="text" value="<?php echo $upper_limit; ?>" name="upper_limit<?php echo $sn; ?>" id="upper_limit<?php echo $sn; ?>">
												<?php
											}	
											else if($vital_sign == "Height"){								
												?>
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_english']; ?>" name="hidEnglishUpper_limit<?php echo $sn; ?>" id="hidEnglishUpper_limit<?php echo $sn; ?>">
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_metric']; ?>" name="hidMetricUpper_limit<?php echo $sn; ?>" id="hidMetricUpper_limit<?php echo $sn; ?>">
													<input class="form-control" type="text" value="<?php echo $upper_limit; ?>" name="upper_limit<?php echo $sn; ?>" id="upper_limit<?php echo $sn; ?>">
												<?php
											}	
											else if($vital_sign == "Weight"){								
												?>
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_english']; ?>" name="hidEnglishUpper_limit<?php echo $sn; ?>" id="hidEnglishUpper_limit<?php echo $sn; ?>">
													<input type="hidden" value="<?php echo $row_tab1['upper_limit_metric']; ?>" name="hidMetricUpper_limit<?php echo $sn; ?>" id="hidMetricUpper_limit<?php echo $sn; ?>">
													<input class="form-control" type="text" value="<?php echo $upper_limit; ?>" name="upper_limit<?php echo $sn; ?>" id="upper_limit<?php echo $sn; ?>">
												<?php
											}				
											else{
												?>
													<input class="form-control" type="text" value="<?php echo $upper_limit; ?>" name="upper_limit<?php echo $sn; ?>" id="upper_limit<?php echo $sn; ?>">
												<?php
											}		
										?>		
									</td>
									<?php 
										$sel_met=imw_query("select * from vital_sign_units where type='metric' and vital_sign_id='$id'");
										$row_met=imw_fetch_array($sel_met);
									?>
									<td id="metric_<?php echo $sn; ?>">
										<?php echo $row_met['name'];?>
									<input type="hidden" value="<?php echo $row_met['name'];?>" name="metric_<?php echo $sn; ?>">
									</td>
									
									
									<?php 
										$sel_met1=imw_query("select * from vital_sign_units where type='english' and vital_sign_id ='$id'");
										$row_met1=imw_fetch_array($sel_met1);
									?>
									<td id="english_<?php echo $sn; ?>">
										<?php echo $row_met1['name'];?>
									<input type="hidden" value="<?php echo $row_met1['name'];?>" name="english_<?php echo $sn; ?>">	
									</td>
									
									<td id="sts_<?php echo $sn; ?>">
										<select name="status<?php echo $sn; ?>" class="selectpicker" data-width="100%">
											<option value="1" <?php if($status=='1'){echo "selected";} ?>>on</option>
											<option value="0" <?php if($status=='0'){echo "selected";} ?>>off</option>
										</select>
									</td>
								</tr>
								<?php 
								}
								if($sn==0)
								{?>
								<tr>
									<td colspan="6" class="warning text-center">No Record Found</td>
								</tr>
								<?php 
								}?>
							</tbody>
						</table>
					</div>
				</div>		
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	var msg = '<?php echo $_GET["msg"]; ?>';
	if(msg!=""){
		top.alert_notification_show('Records saved Successfully.');
	}
	
	show2();
	set_header_title('VS');
	show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>