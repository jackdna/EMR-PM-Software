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

require_once('../admin_header.php'); 

//Get Physicians 
$phyArr = $lensesArr = $phpJsArr = array();

$chkQry = imw_query('SELECT id,username,fname,lname,mname FROM users where user_type = 1 AND delete_status = 0 ');
if($chkQry && imw_num_rows($chkQry) > 0){
	while($rowFetch = imw_fetch_assoc($chkQry)){
		$phyId = (isset($rowFetch['id']) && empty($rowFetch['id']) == false) ? $rowFetch['id'] : '';
		$phyUser = (isset($rowFetch['username']) && empty($rowFetch['username']) == false) ? $rowFetch['username'] : '';
		$phyName = core_name_format($rowFetch['lname'], $rowFetch['fname'], $rowFetch['mname']);
		
		if(empty($phyId) == false && empty($phyUser) == false) $phyArr[$phyId] = array('userName' => $phyUser, 'Name' => $phyName);
	}
}

//Get All lenses
$getLensesListStr = "SELECT iol_type_id,lenses_iol_type,lenses_category FROM lenses_iol_type ORDER BY lenses_category";
$getLensesListQry = imw_query($getLensesListStr);
while ($getLensesListRow = imw_fetch_array($getLensesListQry)) {
	$iol_type_id = $getLensesListRow['iol_type_id'];
	$lenses_iol_type = $getLensesListRow['lenses_iol_type'];
	$lensesArr[$iol_type_id] = array('lensType' => $lenses_iol_type, 'category' => $getLensesListRow['lenses_category']);
}

$phpJsArr['phyArr'] = $phyArr;
$phpJsArr['lenArr'] = $lensesArr;

$jsArr = json_encode($phpJsArr);

// PHP variables for jQuery
$js_vars =  '
<script>
	var jsArr = '.$jsArr.';
</script>';
echo $js_vars;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>imwemr</title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    </head>    
    <body class="">
		<div class="container-fluid">
			<div class="whtbox">
				<div class="table-responsive provtab">
					<table class="table table-bordered table-hover adminnw">
						<thead>
							<tr>
								<th style="width:5%!important" class="text-left">No.</th>
								<th>Physician Name</th>
								<th>Physician Username</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$rowOpt = '';
								if(count($phyArr) > 0){
									$counter = 1;
									foreach($phyArr as $key => &$val){
										$phyUser = (isset($val['userName']) && empty($val['userName']) == false) ? $val['userName'] : '';
										$phyName = (isset($val['Name']) && empty($val['Name']) == false) ? $val['Name'] : '';
										
										if(empty($phyUser)) continue;
										
										$rowOpt .= '
											<tr onClick="getLenses(this);" data-id="'.$key.'" data-name="'.$phyName.'" data-user="'.$phyUser.'">
												<td><span>'.$counter.'</span></td>
												<td><span>'.$phyName.'</span></td>
												<td><span>'.$phyUser.'</span></td>
											</tr>';
										$counter++;	
									}
								}else{
									$rowOpt = '<tr><td colspan="3">No Physicians Found</td></tr>';
								}
								echo $rowOpt;
							?>
						</tbody>	
					</table>
				</div>
			</div>
		</div>
		
		<div id="showLenses" class="modal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Modal Header</h4>
					</div>
					<div class="modal-body" style="height:450px;overflow-y:auto;overflow-x:hidden">
						<div class="row">
							<input type="hidden" id="phyID" value="" name="phyID">
							<table class="table table-bordered table-hover adminnw">
								<thead>
									<tr>
										<th>Define</th>
										<th>Lens Category</th>
										<th>IOL Type</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$strOpt = '';
										if(count($lensesArr) > 0){
											foreach($lensesArr as $key => &$val){
												$strOpt .= '
													<tr>
														<td>
															<div class="checkbox">
																<input id="typeId'.$key.'" type="checkbox" class="chkSel" name="iolIdChkBx[]" data-value="'.$key.'" value="'.$key.'" autocomplete="off">
																<label for="typeId'.$key.'">&nbsp;</label>
															</div>
														</td>
														<td>'.$val['category'].'</td>
														<td>'.$val['lensType'].'</td>
													</tr>
												';
											}
										}else{
											$strOpt = '<tr><td colspan="3">No Lens Found</td></tr>';
										}
										echo $strOpt;
									?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="module_buttons" class="modal-footer">
						<div class="row">
							<div class="col-sm-12 text-center">
								<button type="button" class="btn btn-success" onClick="saveLenses();">Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
    </body>
	<script>
		//Setting table height
		function set_container_height(){
			var header_position = $('#first_toolbar',top.document).position();
			var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight());
			
			//If pagination is there on the page i.e --> .pgn_prnt class
			if($('.pgn_prnt:first').length){
				window_height = window_height - $('.pgn_prnt').outerHeight();
				$('.pgn_prnt').css('overflow-x','hidden');
			}else{
				window_height = parseInt(window_height + 30);
			}
			
			$('.whtbox').css({
				'height':window_height,
				'max-height':window_height,
				'overflow-x':'hidden',
				'overflowY':'auto'
			});
		} 
		
		function getLenses(obj){
			var dataArr = $(obj).data();
			var id = dataArr.id;
			
			if(id && typeof(id) !== 'undefined'){
				$.ajax({
					url:'ajax.php',
					type:'POST',
					dataType:'JSON',
					data:{id:id, username:dataArr.user, name:dataArr.name,task:'getLenses'},
					beforeSend:function(){
						top.show_loading_image('show');
					},
					success:function(response){
						var modal = $('#showLenses');
						modal.find('.modal-content .modal-title').text(dataArr.name+' ('+dataArr.user+')');
						
						if(response.length){
							$.each(response,function(id,val){
								modal.find('.modal-content .modal-body input[type=checkbox][data-value="'+val+'"]').prop('checked', true);
							});
						}
						
						modal.find('#phyID').val(id);
						modal.modal('show');
						
						modal.on('hide.bs.modal', function(){
							modal.find('.modal-content .modal-body input[type=checkbox]').prop('checked', false);
						});
						
					},
					complete:function(){
						top.show_loading_image('hide');
					}
				});
			}else{
				top.fAlert('Invalid values. Please reload the page.');
				return false;
			}
		}
		
		function saveLenses(){
			var selArr = new Array;
			var phyID = $('#showLenses').find('#phyID').val();
			
			$('#showLenses input[type=checkbox]:checked').each(function(id,elem){
				var value = $(elem).val();
				selArr.push(value);
			});
			
			//if(selArr.length){
				if(phyID && typeof(phyID) !== 'undefined'){
					$.ajax({
						url:'ajax.php',
						type:'POST',
						data:{phyId:phyID, selId:selArr,task:'saveLenses'},
						beforeSend:function(){
							top.show_loading_image('show');
						},
						success:function(response){
							if(response === 0){
								top.fAlert('Unable to parse values. Please try again');
								
							}else{
								top.alert_notification_show('Record updated successfully');
							}
							$('#showLenses').modal('hide');
							return false;
						},
						complete:function(){
							top.show_loading_image('hide');
						}
					});
				}else{
					top.fAlert('Invalid values. Please try again');
					$('#showLenses').modal('hide');
					return false;
				}
			/* }else{
				top.alert_notification_show('No Lens selected. Please select atleast one lens.');
				return false;
			} */
		}
		
		$(function(){
			$('.chkSel').on('change', function(){
				var chkLength = $('.chkSel:checked').length;
				if(chkLength > 4){
					top.fAlert("Only four types can be selected.");
					$(this).prop('checked', false);
				}
			});
		});
		
		$(document).ready(function () {
            set_header_title('User IOL Lenses');
			set_container_height();
        });
		
		$(window).resize(function(){
			set_container_height();
		});
		
        top.show_loading_image('hide');
    </script>

</html>
