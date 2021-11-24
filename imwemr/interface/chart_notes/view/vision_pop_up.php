<?php
$popNameModal=$popName."Modal";
if($popName=="popDistance"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
		<!-- Distance -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<!--Panel Heading-->
				<div class="row">	
					<div class="col-sm-4">
					<label>Distance</label>	
					</div>
					<div class="col-sm-8">				
					<div class="input-group">						
						<input id="elem_visSnellan_input" type="text" class="form-control" name="elem_visSnellan_input" placeholder="..">
						<?php echo $menu_visSnellan ; ?>
					</div>				
					</div>	
				</div>				
			</div>
			<div class="panel-body">
				<!--Panel Content-->
				<table class="table borderless">
					<tr>
						<td colspan="4">
							<select id="disBlockAType"  class="form-control minimal " >
								<option value=""></option>
								<option value="SC" >SC</option>
								<option value="CC" >CC</option>
								<option value="CL-S" >CL-S</option>
								<option value="GPCL" >GPCL</option>
							</select>
						</td>
						<td></td>
						<td colspan="4">
							<select id="disBlockBType"  class="form-control minimal " >
								<option value=""></option>
								<option value="CC" >CC</option>
								<option value="SC" >SC</option>
								<option value="PH" >PH</option>
								<option value="GL" >GL</option>								
								<option value="CL-S" >CL-S</option>
								<option value="GPCL" >GPCL</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class=" text-center">
							<label class="od">OD</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="os">OS</label>	
						</td>
						<td class=" text-center">
							<label class="ou">OU</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="od">OD</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="os">OS</label>	
						</td>
						<td class=" text-center">
							<label class="ou">OU</label>	
						</td>	
					</tr>
					<tr>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOdTxt1_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOsTxt1_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOuTxt1_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOdTxt2_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOsTxt2_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visDisOuTxt2_text_input">
						</td>
					</tr>
					<tr>
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visDisOdTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td ><a class="btn btn-link bl">BL</a></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOsTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOuTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOdTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td ><a class="btn btn-link bl">BL</a></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOsTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOuTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>	
					</tr>
					<tr>
						<td class="">
							<textarea name="disOldBlockAOd" id="disOldBlockAOd" rows="1" class="form-control"></textarea>
						</td>
						<td></td>
						<td class="">
							<textarea name="disOldBlockAOs" id="disOldBlockAOs" rows="1" class="form-control"></textarea>
						</td>
						<td class="">
							<textarea name="disOldBlockAOu" id="disOldBlockAOu" rows="1" class="form-control"></textarea>
						</td>
						<td></td>
						<td class="">
							<textarea name="disOldBlockBOd" id="disOldBlockBOd" rows="1" class="form-control"></textarea>
						</td>
						<td></td>
						<td class="">
							<textarea name="disOldBlockBOs" id="disOldBlockBOs" rows="1" class="form-control"></textarea>
						</td>
						<td class="">
							<textarea name="disOldBlockBOu" id="disOldBlockBOu" rows="1" class="form-control"></textarea>
						</td>	
					</tr>
				</table>
				<!--Panel Content-->
			</div>
		</div>
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>

<?php
}
if($popName=="popNear"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
		<!-- Near -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<!--Panel Heading-->
				<div class="row">	
					<div class="col-sm-4">
					<label>Near</label>	
					</div>
					<div class="col-sm-8">				
					<div class="input-group">						
						<input id="elem_visSnellan_near_input" type="text" class="form-control" name="elem_visSnellan_near_input" placeholder="..">
						<?php echo $menu_visSnellan_near ; ?>
					</div>				
					</div>	
				</div>				
			</div>
			<div class="panel-body">
				<!--Panel Content-->
				<table class="table borderless">
				<tr>
					<td colspan="4">
						<select id="nearBlockAType"  class="form-control minimal " >
							<option value="" ></option>
							<option value="SC">SC</option>                                    
							<option value="CC">CC</option>
							<option value="CL-S">CL-S</option>
							<option value="GPCL">GPCL</option>
							<option value="MV">MV</option>
						</select>
					</td>
					<td></td>
					<td colspan="4">
						<select id="nearBlockBType"  class="form-control minimal " >
							<option value="" ></option>
							<option value="CC">CC</option>
							<option value="SC">SC</option>                                    
							<option value="GL">GL</option>
							<option value="CL-S">CL-S</option>
							<option value="GPCL">GPCL</option>
							<option value="MV">MV</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class=" text-center">
						<label class="od">OD</label>	
					</td>
					<td></td>
					<td class=" text-center">
						<label class="os">OS</label>	
					</td>
					<td class=" text-center">
						<label class="ou">OU</label>	
					</td>
					<td></td>
					<td class=" text-center">
						<label class="od">OD</label>	
					</td>
					<td></td>
					<td class=" text-center">
						<label class="os">OS</label>	
					</td>
					<td class=" text-center">
						<label class="ou">OU</label>	
					</td>	
				</tr>
				<tr>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOdTxt1_text_input">
					</td>
					<td></td>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOsTxt1_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOuTxt1_text_input">
					</td>
					<td></td>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOdTxt2_text_input">
					</td>
					<td></td>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOsTxt2_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visNearOuTxt2_text_input">
					</td>
				</tr>
				<tr>
					<td class="">
						<select class="form-control" multiple  data-size="25"  id="elem_visNearOdTxt1_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>
					<td ><a class="btn btn-link bl">BL</a></td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visNearOsTxt1_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visNearOuTxt1_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>
					<td></td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visNearOdTxt2_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>
					<td ><a class="btn btn-link bl">BL</a></td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visNearOsTxt2_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visNearOuTxt2_input" >
							<option value="">None</option>
							<?php echo $arrAcuitiesNearOptions;?>
						</select>
					</td>	
				</tr>
				<tr>
					<td class="">
						<textarea name="nearOldBlockAOd" id="nearOldBlockAOd" rows="1" class="form-control"></textarea>
					</td>
					<td></td>
					<td class="">
						<textarea name="nearOldBlockAOs" id="nearOldBlockAOs" rows="1" class="form-control"></textarea>
					</td>
					<td class="">
						<textarea name="nearOldBlockAOu" id="nearOldBlockAOu" rows="1" class="form-control"></textarea>
					</td>
					<td></td>
					<td class="">
						<textarea name="nearOldBlockBOd" id="nearOldBlockBOd" rows="1" class="form-control"></textarea>
					</td>
					<td></td>
					<td class="">
						<textarea name="nearOldBlockBOs" id="nearOldBlockBOs" rows="1" class="form-control"></textarea>
					</td>
					<td class="">
						<textarea name="nearOldBlockBOu" id="nearOldBlockBOu" rows="1" class="form-control"></textarea>
					</td>	
				</tr>
				</table>
			</div>
		</div>
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popAdAcuity"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
			<!-- Additional Acuity -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<!--Panel Heading-->
					<div class="row">	
						<div class="col-sm-4">
						<label>Additional Acuity</label>	
						</div>
						<div class="col-sm-8">
						</div>	
					</div>				
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<div class="row">
						<div class="col-sm-2 text-center"></div>
						<div class="col-sm-3 text-center">
							<label class="od">OD</label>	
						</div>
						<div class="col-sm-1 text-center"></div>
						<div class="col-sm-3 text-center">
							<label class="os">OS</label>	
						</div>
						<div class="col-sm-3 text-center">
							<label class="ou">OU</label>	
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2">
							<input type="text" class="form-control" id="elem_visDisOuSel3_text_input">
						</div>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="elem_visDisOdTxt3_text_input">
						</div>
						<div class="col-sm-1 text-center"></div>
						<!--<div class="col-sm-2">
							<input type="text" class="form-control" id="elem_visDisOsSel3_text_input">
						</div>-->
						<div class="col-sm-3">
							<input type="text" class="form-control" id="elem_visDisOsTxt3_text_input">
						</div>
						<!--<div class="col-sm-1">
							<input type="text" class="form-control" id="elem_visDisOuSel3_text_input">
						</div>-->
						<div class="col-sm-3">
							<input type="text" class="form-control" id="elem_visDisOuTxt3_text_input">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2">
							<select class="form-control" multiple  data-size="25"  id="elem_visDisOuSel3_input" >
								<option value=""></option>
								<option value="PH" >PH</option>
								<option value="GL" >GL</option>
								<option value="SC" >SC</option>
								<option value="CC" >CC</option>
							</select>
						</div>						
						<div class="col-sm-3">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOdTxt3_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</div>
						<div class="col-sm-1 text-center"><a class="btn btn-link bl">BL</a></div>
						<!--<div class="col-sm-2">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOsSel3_input" >
								<option value=""></option>
								<option value="PH" >PH</option>
								<option value="GL" >GL</option>
								<option value="SC" >SC</option>
								<option value="CC" >CC</option>
							</select>
						</div>-->
						<div class="col-sm-3">
							<select class="form-control" multiple  data-size="25"  id="elem_visDisOsTxt3_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</div>
						<!--<div class="col-sm-1">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOuSel3_input" >
								<option value=""></option>
								<option value="PH" >PH</option>
								<option value="GL" >GL</option>
								<option value="SC" >SC</option>
								<option value="CC" >CC</option>
							</select>
						</div>-->
						<div class="col-sm-3">
							<select class="form-control" multiple data-size="25"  id="elem_visDisOuTxt3_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</div>
					</div>
					<!--
					<div class="row">
						<div class="col-sm-4">
							<textarea name="acuityOld" id="acuityOld" rows="1" class="form-control"></textarea>
						</div>
						<div class="col-sm-4">
							<textarea name="acuityOldOs" id="acuityOldOs" rows="1" class="form-control"></textarea>
						</div>
						<div class="col-sm-4">
							<textarea name="acuityOldOu" id="acuityOldOu" rows="1" class="form-control"></textarea>
						</div>
					</div>
					-->
				</div>
			</div>
			<!-- Additional Acuity -->
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popK"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
			<!-- K Values -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<!--Panel Heading-->
					<div class="row">	
						<div class="col-sm-4">
						<label>K Values</label>	
						</div>
						<div class="col-sm-8">
						</div>	
					</div>				
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<div class="row">
						<div class="col-sm-6 text-center od">
							OD
						</div>
						<div class="col-sm-5"></div>
						<div class="col-sm-6 text-center os">
							OS
						</div>
					</div>
					<div class="row">
						<div class="col-sm-5">
							<div class="row">
								<div class="col-sm-4"><input type="text" name="elem_visAkOdK_input" id="elem_visAkOdK_input" class="form-control" /></div>
								<div class="col-sm-1">/</div>
								<div class="col-sm-3">
									<input type="text" name="elem_visAkOdSlash_input" id="elem_visAkOdSlash_input" class="form-control" />
								</div>
								<div class="col-sm-1">X</div>
								<div class="col-sm-3">
									<input type="text"  name="elem_visAkOdX_input" id="elem_visAkOdX_input" class="form-control" />							
								</div>
							</div>
						</div>
						<div class="col-sm-2 text-center" id="bl_kvalues" ><a class="btn btn-link bl">BL</a></div>
						<div class="col-sm-5">
							<div class="row">
								<div class="col-sm-4"><input type="text" name="elem_visAkOsK_input" id="elem_visAkOsK_input" class="form-control" /></div>
								<div class="col-sm-1">/</div>
								<div class="col-sm-3">
									<input type="text" name="elem_visAkOsSlash_input" id="elem_visAkOsSlash_input" class="form-control" />
								</div>
								<div class="col-sm-1">X</div>
								<div class="col-sm-3">							
									<input type="text"  name="elem_visAkOsX_input" id="elem_visAkOsX_input" class="form-control" />							
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</div>			
			<!-- K Values -->
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popAR"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
			<!-- AR -->
			<div class="panel panel-default">
			<div class="panel-heading">
				<!--Panel Heading-->
				<div class="row">	
					<div class="col-sm-4">
					<label>AR</label>	
					</div>
					<div class="col-sm-8 text-right">				
						<a href="javascript:void(0);" onclick="showTranspose_visionpopup('Ar');" class="btn-info btn-sm">+/- Cyl</a>			
					</div>	
				</div>				
			</div>
			<div class="panel-body">
				<!--Panel Content-->
				<table class="table borderless">	
				<tr>
					<td class=" text-center" colspan="4">
						<label class="od">OD</label>	
					</td>
					<td class=" text-center">
							
					</td>
					<td class=" text-center" colspan="4">
						<label class="os">OS</label>	
					</td>
				</tr>
				<tr>
					<td class=" text-center">
						<label class="od">S</label>	
					</td>
					<td class=" text-center">
						<label class="os">C</label>	
					</td>
					<td class=" text-center">
						<label class="od">A</label>	
					</td>
					<td class=" text-center">
						<label class="os">Conf</label>	
					</td>
					<td class=" text-center"></td>
					<td class=" text-center">
						<label class="od">S</label>	
					</td>
					<td class=" text-center">
						<label class="os">C</label>	
					</td>
					<td class=" text-center">
						<label class="od">A</label>	
					</td>
					<td class=" text-center">
						<!--<label class="os">Conf</label>-->	
					</td>
				</tr>
				
				<tr>
					
					<td class="">
						<input type="text" class="form-control" id="elem_visArOdS_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOdC_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOdA_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOdSel1_text_input">
					</td>	
					<td class=" text-center"></td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOsS_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOsC_text_input">
					</td>
					<td class="">
						<input type="text" class="form-control" id="elem_visArOsA_text_input">
					</td>
					<td class="">
						<!--<input type="text" class="form-control" id="elem_visArOsSel1_text_input">-->
					</td>
					
				</tr>
				
				<tr>
					
					<td class="">
						<select class="form-control" multiple  data-size="25"  id="elem_visArOdS_input" >
							<option value="">None</option>
							<?php echo $sphereOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOdC_input" >
							<option value="">None</option>
							<?php echo $cylinderOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOdA_input" >
							<option value="">None</option>
							<?php echo $axisOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOdSel1_input" >
							<option value="">None</option>
							<option value="High">High</option>
							<option value="Med">Med</option>
							<option value="Low">Low</option>
						</select>
					</td>
					<td class=" text-center"><label ><a class="btn btn-link bl">BL</a></label></td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOsS_input" >
							<option value="">None</option>
							<?php echo $sphereOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOsC_input" >
							<option value="">None</option>
							<?php echo $cylinderOptions;?>
						</select>
					</td>
					<td class="">
						<select class="form-control" multiple data-size="25"  id="elem_visArOsA_input" >
							<option value="">None</option>
							<?php echo $axisOptions;?>
						</select>
					</td>
					<td class="">
						<!--<select class="form-control" multiple data-size="25"  id="elem_visArOsSel1_input" >
							<option value="">None</option>
							<option value="High">High</option>
							<option value="Med">Med</option>
							<option value="Low">Low</option>
						</select>-->
					</td>
						
				</tr>
				</table>
			</div>
		</div>
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popCycAR"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">				
			<!-- Cycloplegic AR -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<!--Panel Heading-->
					<div class="row">	
						<div class="col-sm-4">
						<label>Cycloplegic AR</label>	
						</div>
						<div class="col-sm-8 text-right">				
							<a href="javascript:void(0);" onclick="showTranspose_visionpopup('CycAr');" class="btn-info btn-sm">+/- Cyl</a>
						</div>	
					</div>				
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<table class="table borderless">	
					<tr>
						<td class=" text-center" colspan="4">
							<label class="od">OD</label>	
						</td>
						<td class=" text-center">
							<label ></label>	
						</td>
						<td class=" text-center" colspan="4">
							<label class="os">OS</label>	
						</td>						
					</tr>
					<tr>
						
						<td class=" text-center">
							<label >S</label>	
						</td>
						<td class=" text-center">
							<label >C</label>	
						</td>
						<td class=" text-center">
							<label >A</label>	
						</td>
						<td class=" text-center">
							<label >Conf</label>	
						</td>
						<td class=" text-center"></td>
						<td class=" text-center">
							<label >S</label>	
						</td>
						<td class=" text-center">
							<label >C</label>	
						</td>
						<td class=" text-center">
							<label >A</label>	
						</td>
						<td class=" text-center">
							<!--<label >Conf</label>-->	
						</td>
						
					</tr>
					<tr>
						
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOdS_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOdC_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOdA_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOdSel1_text_input">
						</td>
						<td class=" text-center"></td>	
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOsS_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOsC_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visCycArOsA_text_input">
						</td>
						<td class="">
							<!--<input type="text" class="form-control" id="elem_visCycArOsSel1_text_input">-->
						</td>
						
					</tr>
					<tr>
						
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visCycArOdS_input" >
								<option value="">None</option>
								<?php echo $sphereOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOdC_input" >
								<option value="">None</option>
								<?php echo $cylinderOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOdA_input" >
								<option value="">None</option>
								<?php echo $axisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOdSel1_input" >
								<option value="">None</option>
								<option value="High">High</option>
								<option value="Med">Med</option>
								<option value="Low">Low</option>
							</select>
						</td>
						<td class=" text-center"><label ><a class="btn btn-link bl">BL</a></label></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOsS_input" >
								<option value="">None</option>
								<?php echo $sphereOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOsC_input" >
								<option value="">None</option>
								<?php echo $cylinderOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visCycArOsA_input" >
								<option value="">None</option>
								<?php echo $axisOptions;?>
							</select>
						</td>
						<td class="">
							<!--<select class="form-control" multiple data-size="25"  id="elem_visCycArOsSel1_input" >
								<option value="">None</option>
								<option value="High">High</option>
								<option value="Med">Med</option>
								<option value="Low">Low</option>
							</select>-->
						</td>
						
					</tr>
					</table>	
				</div>
			</div>
			<!-- cyclo AR -->
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popBAT"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
			<!-- BAT  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<!--Panel Heading-->
					<div class="row">	
						<div class="col-sm-4">
						<label>BAT</label>	
						</div>
						<div class="col-sm-8">
							
						</div>	
					</div>				
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<table class="table borderless">
					<tr>
						<td colspan="4" class=" text-center">
							<label class="od">OD</label>	
						</td>
						<td></td>
						<td colspan="4" class=" text-center">
							<label class="os">OS</label>	
						</td>
						<td colspan="4" class=" text-center">
							<label class="ou">OU</label>	
						</td>
					</tr>
					<tr>
						<td class=" text-center">
							<label class="od">NL</label>	
						</td>
						<td class=" text-center">
							<label class="os">L</label>	
						</td>
						<td class=" text-center">
							<label class="ou">M</label>	
						</td>
						<td class=" text-center">
							<label class="od">H</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="od">NL</label>	
						</td>
						<td class=" text-center">
							<label class="os">L</label>	
						</td>
						<td class=" text-center">
							<label class="ou">M</label>	
						</td>
						<td class=" text-center">
							<label class="od">H</label>	
						</td>
						
						<td class=" text-center">
							<label class="od">NL</label>	
						</td>
						<td class=" text-center">
							<label class="os">L</label>	
						</td>
						<td class=" text-center">
							<label class="ou">M</label>	
						</td>
						<td class=" text-center">
							<label class="od">H</label>	
						</td>							
					</tr>
					<tr>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatNlOd_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatLowOd_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatMedOd_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatHighOd_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatNlOs_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatLowOs_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatMedOs_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatHighOs_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatNlOu_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatLowOu_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatMedOu_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visBatHighOu_text_input">
						</td>
					</tr>
					<tr>
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visBatNlOd_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatLowOd_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatMedOd_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatHighOd_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td><a class="btn btn-link bl">BL</a></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatNlOs_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatLowOs_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visBatMedOs_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatHighOs_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatNlOu_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatLowOu_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatMedOu_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visBatHighOu_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>	
					</tr>
					</table>	
				</div>
			</div>					
			<!-- BAT  -->
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if($popName=="popPAM"){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		<div class="row " >
		<div class="col-sm-12">
			<!-- PAM  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<!--Panel Heading-->
					<div class="row">	
						<div class="col-sm-4">
						<label>PAM</label>	
						</div>
						<div class="col-sm-8">
							<div class="input-group">						
								<input id="elem_visPam_input" type="text" class="form-control" name="elem_visPam_input" placeholder="..">
								<?php echo $menu_visSnellan_pam ; ?>
							</div>
						</div>	
					</div>				
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<table class="table borderless">
					<tr>	
						<td colspan="4">
						<label>P<sub>SC</sub></label>	
						</td>
						<td colspan="8">
							<select name="elem_visPamOdSel2_input" id="elem_visPamOdSel2_input" class="form-control">
								<option value="" ></option>
								<option value="CC" >CC</option>
								<option value="CL-S">CL-S</option>
								<option value="GPCL">GPCL</option>
							</select>
						</td>	
					</tr>
					<tr>
						<td class=" text-center">
							<label class="od">OD</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="os">OS</label>	
						</td>
						<td class=" text-center">
							<label class="ou">OU</label>	
						</td>
						<td class=" text-center">
							<label class="od">OD</label>	
						</td>
						<td></td>
						<td class=" text-center">
							<label class="os">OS</label>	
						</td>
						<td class=" text-center">
							<label class="ou">OU</label>	
						</td>	
					</tr>
					<tr>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOdTxt1_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOsTxt1_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOuTxt1_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOdTxt2_text_input">
						</td>
						<td></td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOsTxt2_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPamOuTxt2_text_input">
						</td>
					</tr>
					
					<tr>
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visPamOdTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td><a class="btn btn-link bl">BL</a></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPamOsTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPamOuTxt1_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPamOdTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td><a class="btn btn-link bl">BL</a></td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPamOsTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPamOuTxt2_input" >
								<option value="">None</option>
								<?php echo $arrAcuitiesMrDisOptions;?>
							</select>
						</td>	
					</tr>					
					
					<tr>
						<td class="">
							<textarea name="elem_visPam_OldBlockAOd" id="elem_visPam_OldBlockAOd" rows="1" class="form-control"></textarea>
						</td>
						<td></td>
						<td class="">
							<textarea name="elem_visPam_OldBlockAOs" id="elem_visPam_OldBlockAOs" rows="1" class="form-control"></textarea>
						</td>
						<td class="">
							<textarea name="elem_visPam_OldBlockAOu" id="elem_visPam_OldBlockAOu" rows="1" class="form-control"></textarea>
						</td>
						<td class="">
							<textarea name="elem_visPam_OldBlockBOd" id="elem_visPam_OldBlockBOd" rows="1" class="form-control"></textarea>
						</td>
						<td></td>
						<td class="">
							<textarea name="elem_visPam_OldBlockBOs" id="elem_visPam_OldBlockBOs" rows="1" class="form-control"></textarea>
						</td>
						<td class="">
							<textarea name="elem_visPam_OldBlockBOu" id="elem_visPam_OldBlockBOu" rows="1" class="form-control"></textarea>
						</td>
					</tr>
					</table>	
				</div>
			</div>					
			<!-- PAM  -->
		</div>
		</div>
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if(strpos($popName,"popPC")!==false){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">		
			
		<?php
		$i = str_replace("popPC","",$popName);
		$i = trim($i);
		if(!empty($i)){
			$findx = ($i>1) ? $i: "";
			$cls_hidden = ($i>1) ? "hidden" : "";
			
		?>

		<!-- content -->
		<div id="row_pc<?php echo $i; ?>" >
		<div class="row " >
			<div class="col-sm-6">
				<!-- PC -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<!--Panel Heading-->
						<div class="row">	
							<div class="col-sm-2">
							<label>PC <?php echo $i; ?></label>	
							</div>
							<div class="col-sm-4 text-right">
								<select class="form-control" name="ctrlpcType<?php echo $i; ?>" id="ctrlpcType<?php echo $i; ?>">
								<option value="">None</option>
								<option value="distance">Distance</option>
								<option value="near">Near</option>
								<option value="bifocal">BiFocal</option>
								<option value="trifocal">TriFocal</option>
								<option value="progressive">Progressive</option>
								<option value="computer">Computer</option>
								<option value="sun glasses">Sun Glasses</option>
								</select>
							</div>	
							<div class="col-sm-4 text-right">				
								<select class="form-control" id="el_copyfrm_pc<?php echo $i; ?>" onchange="vis_copy_from(this)" >
									<option value="">Copy From</option>
									<?php										
										foreach($ar_copy_frm as $key => $val){
											if(strpos($val,"pc")!==false && $val=="pc".$i){}
											else{
												echo "<option value=\"".$val."\">".strtoupper($val)."</option>";
											}
										}
									?>									
								</select>
							</div>
							<div class="col-sm-2 text-right">		
								<a href="javascript:void(0);" onclick="showTranspose_visionpopup('Pc<?php echo $findx; ?>');" class=" btn-info btn-sm">+/- Cyl</a>	
							</div>
						</div>				
					</div>
					<div class="panel-body">
						<!--Panel Content-->
						<table class="table borderless">
						<tr>
							<td class=" text-center" colspan="5">
								<label class="od">OD</label>	
							</td>
							<td class=" text-center">									
							</td>
							<td class=" text-center" colspan="5">
								<label class="os">OS</label>	
							</td>
						</tr>
						<tr>
							<td class=" text-center">
								<label >S</label>	
							</td>
							<td class=" text-center">
								<label >C</label>	
							</td>
							<td class=" text-center">
								<label >A</label>	
							</td>
							<td class=" text-center">
								<label >Add</label>	
							</td>
							<td class=" text-center">
								<label >Vision</label>	
							</td>
							<td class=" text-center">									
							</td>
							<td class=" text-center">
								<label >S</label>	
							</td>
							<td class=" text-center">
								<label >C</label>	
							</td>
							<td class=" text-center">
								<label >A</label>	
							</td>
							<td class=" text-center">
								<label >Add</label>	
							</td>
							<td class=" text-center">
								<label >Vision</label>	
							</td>		
							
						</tr>
						<tr>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdS<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdC<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdA<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdAdd<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdSel1<?php echo $findx; ?>_text_input">
							</td>
							<td class=" text-center">
								
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsS<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsC<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsA<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsAdd<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsSel1<?php echo $findx; ?>_text_input">
							</td>							
						</tr>
						<tr>
							<td class="">
								<select class="form-control" multiple  data-size="25"  id="elem_visPcOdS<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $sphereOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdC<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $cylinderOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdA<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $axisOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdAdd<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $addOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdSel1<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<option value="SV">SV</option>
									<option value="BF">BF</option>
									<option value="Progs">Progs</option>
									<option value="TRF">TRF</option>
								</select>
							</td>
							<td class=" text-center">		
								<a class="btn btn-link bl">BL</a>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsS<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $sphereOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsC<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $cylinderOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsA<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $axisOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsAdd<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $addOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsSel1<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<option value="SV">SV</option>
									<option value="BF">BF</option>
									<option value="Progs">Progs</option>
									<option value="TRF">TRF</option>
								</select>
							</td>
						</tr>
						</table>
						<!--Panel Content-->
					</div>
				</div>	
				<!-- PC -->
			</div>
			<div class="col-sm-6">
				<!-- Prism -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<!--Panel Heading-->
						<div class="row">	
							<div class="col-sm-2">
							<label>PRISM</label>	
							</div>
							<div class="col-sm-4 text-right">
							</div>	
							<div class="col-sm-4 text-right">										
							</div>
							<div class="col-sm-2 text-right">
							</div>
						</div>				
					</div>
				</div>
				<div class="panel-body">
					<!--Panel Content-->
					<table class="table borderless">
					<tr>
						<td class=" text-center" colspan="5">
							<label class="od">OD</label>	
						</td>
						<td class=" text-center">
								
						</td>
						<td class=" text-center" colspan="5">
							<label class="os">OS</label>	
						</td>
					</tr>
					<tr>
						<td class=" text-center">
							<label >P1</label>	
						</td>
						<td class=" text-center">
							<label >P2</label>	
						</td>
						<td class=" text-center">
							<label >/</label>	
						</td>
						<td class=" text-center">
							<label >P1</label>	
						</td>
						<td class=" text-center">
							<label >P2</label>	
						</td>
						<td class=" text-center">
								
						</td>
						<td class=" text-center">
							<label >P1</label>	
						</td>
						<td class=" text-center">
							<label >P2</label>	
						</td>
						<td class=" text-center">
							<label >/</label>	
						</td>
						<td class=" text-center">
							<label >P1</label>	
						</td>
						<td class=" text-center">
							<label >P2</label>	
						</td>						
					</tr>
					
					<tr>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOdP<?php echo $findx; ?>_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOdSel2<?php echo $findx; ?>_text_input">
						</td>
						<td class=" text-center">
							/
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOdSlash<?php echo $findx; ?>_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOdPrism<?php echo $findx; ?>_text_input">
						</td>
						<td class=" text-center">
								
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOsP<?php echo $findx; ?>_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOsSel2<?php echo $findx; ?>_text_input">
						</td>
						<td class=" text-center">
							/
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOsSlash<?php echo $findx; ?>_text_input">
						</td>
						<td class="">
							<input type="text" class="form-control" id="elem_visPcOsPrism<?php echo $findx; ?>_text_input">
						</td>						
					</tr>
					
					<tr>
						<td class="">
							<select class="form-control" multiple  data-size="25"  id="elem_visPcOdP<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<?php echo $prismOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOdSel2<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<option value="BI">BI</option>
								<option value="BO">BO</option>
							</select>
						</td>
						<td class="">
									
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOdSlash<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<?php echo $prismOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOdPrism<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<option value="BD">BD</option>
								<option value="BU">BU</option>
							</select>
						</td>
						<td class=" text-center">
							<a class="btn btn-link bl">BL</a>	
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOsP<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<?php echo $prismOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOsSel2<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<option value="BI">BI</option>
								<option value="BO">BO</option>
							</select>
						</td>
						<td class="">
									
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOsSlash<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<?php echo $prismOptions;?>
							</select>
						</td>
						<td class="">
							<select class="form-control" multiple data-size="25"  id="elem_visPcOsPrism<?php echo $findx; ?>_input" >
								<option value="">None</option>
								<option value="BD">BD</option>
								<option value="BU">BU</option>
							</select>
						</td>
					</tr>
					</table>
					<!--Panel Content-->
				</div>
				<!-- Prism -->
			</div>
		</div>		
		<!-- content -->
		
		<!-- content -->
		<div class="row " >
			<div class="col-sm-6">
				<!-- Over Refraction -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<!--Panel Heading-->
						<div class="row">	
							<div class="col-sm-2">
							<label>Over Refraction</label>	
							</div>
							<div class="col-sm-4 text-right">						
							</div>	
							<div class="col-sm-4 text-right">
							</div>
							<div class="col-sm-2 text-right">						
							</div>
						</div>				
					</div>
					<div class="panel-body">
						<!--Panel Content-->
						<table class="table borderless">
						<tr>
							<td class=" text-center" colspan="4">
								<label class="od">OD</label>	
							</td>
							<td class=" text-center">
									
							</td>
							<td class=" text-center" colspan="4">
								<label class="os">OS</label>	
							</td>
						</tr>
						<tr>
							<td class=" text-center">
								<label >S</label>	
							</td>
							<td class=" text-center">
								<label >C</label>	
							</td>									
							<td class=" text-center">
								<label >A</label>	
							</td>									
							<td class=" text-center">
								<label >Vision</label>	
							</td>
							<td class=" text-center">
									
							</td>
							<td class=" text-center">
								<label >S</label>	
							</td>
							<td class=" text-center">
								<label >C</label>	
							</td>									
							<td class=" text-center">
								<label >A</label>	
							</td>									
							<td class=" text-center">
								<label >Vision</label>	
							</td>
						</tr>
						<tr>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdOverrefS<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdOverrefC<?php echo $findx; ?>_text_input">
							</td>									
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdOverrefA<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOdOverrefV<?php echo $findx; ?>_text_input">
							</td>
							<td class="col-sm-2 text-center">
									
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsOverrefS<?php echo $findx; ?>_text_input">
							</td>
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsOverrefC<?php echo $findx; ?>_text_input">
							</td>									
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsOverrefA<?php echo $findx; ?>_text_input">
							</td>								
							<td class="">
								<input type="text" class="form-control" id="elem_visPcOsOverrefV<?php echo $findx; ?>_text_input">
							</td>							
							
						</tr>
						<tr>
							<td class="">
								<select class="form-control" multiple  data-size="25"  id="elem_visPcOdOverrefS<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $sphereOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdOverrefC<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $cylinderOptions;?>
								</select>
							</td>									
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdOverrefA<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $axisOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOdOverrefV<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $arrAcuitiesMrDisOptions;?>
								</select>
							</td>
							<td class="col-sm-2 text-center">
								<a class="btn btn-link bl">BL</a>	
							</td>
							<td class="">
								<select class="form-control" multiple  data-size="25"  id="elem_visPcOsOverrefS<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $sphereOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsOverrefC<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $cylinderOptions;?>
								</select>
							</td>									
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsOverrefA<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $axisOptions;?>
								</select>
							</td>
							<td class="">
								<select class="form-control" multiple data-size="25"  id="elem_visPcOsOverrefV<?php echo $findx; ?>_input" >
									<option value="">None</option>
									<?php echo $arrAcuitiesMrDisOptions;?>
								</select>
							</td>
							
						</tr>
						</table>
						<!--Panel Content-->
					</div>
				</div>	
				<!-- Over Refraction -->
			</div>			
		</div>		
		</div>
		<!-- content -->
		
		<?php
		}
		?>
		
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
if(strpos($popName,"popMR")!==false){
?>
<!-- Modal -->
<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		
		<!--MR-->
			<?php
			//for($i=1;$i<=3;$i++){
			$i = str_replace("popMR","",$popName);
			$i = trim($i);
			if(!empty($i)){
				$other="";
				$findx = "";								
				if($i>1){ $other="Other"; }
				if($i>2){ $findx="_".$i; }			
				
			?>
			
			<!-- content -->		
			<div id="row_mr<?php echo $i; ?>" >
				<div class="row " >
					<div class="col-sm-12">
						<!-- MR -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label class="mr_pop_lbl"><?php echo $mr_pop_lbl; ?></label></strong>	
									</div>
									<div class="col-sm-4 text-right">						
									</div>	
									<div class="col-sm-2 text-right">				
										<select class="form-control" id="el_copyfrm_mr<?php echo $i; ?>" onchange="vis_copy_from(this)" >
										<option value="">Copy From</option>
										<?php										
											foreach($ar_copy_frm as $key => $val){
												if(strpos($val,"mr")!==false && $val=="mr".$i){}
												else{
													echo "<option value=\"".$val."\">".strtoupper($val)."</option>";
												}
											}
										?>									
										</select>			
									</div>
									<div class="col-sm-2 text-right">						
									</div>
									<div class="col-sm-2 text-right">
										<a href="javascript:void(0);" onclick="showTranspose_visionpopup('Mr<?php echo $i; ?>');" class=" btn-info btn-sm">+/- Cyl</a>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="6" class="text-center od">OD</th>
										<th></th>
										<th colspan="6" class="text-center os">OS</th>
										<th></th>
										<th class="text-center ou">OU</th>
									</tr>
									<tr>
										<th class="text-center">S</th>
										<th class="text-center">C</th>
										<th class="text-center">A</th>
										<th class="text-center">VA<sub>D</sub></th>
										<th class="text-center">ADD</th>
										<th class="text-center">VA<sub>N</sub></th>
										
										<th class="text-center"></th>
										
										<th class="text-center">S</th>
										<th class="text-center">C</th>
										<th class="text-center">A</th>
										<th class="text-center">VA<sub>D</sub></th>
										<th class="text-center">ADD</th>
										<th class="text-center">VA<sub>N</sub></th>
										
										<th></th>
										<th class="text-center">VA<sub>D</sub></th>
									</tr>
									
									<tr>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdS<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdC<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdA<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt1<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdAdd<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt2<?php echo $findx; ?>_text_input"></td>
										
										<td></td>
										
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsS<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsC<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsA<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt1<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsAdd<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt2<?php echo $findx; ?>_text_input"></td>
										
										<td></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OuTxt1<?php echo $findx; ?>_text_input"></td>
									</tr>
									
									<tr>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdS<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $sphereOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OdC<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $cylinderOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OdA<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $axisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdAdd<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $addOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt2<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesNearOptions;?>
										</select></td>
										
										<td><a class="btn btn-link bl">BL</a></td>
										
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsS<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $sphereOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OsC<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $cylinderOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OsA<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $axisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsAdd<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $addOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt2<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesNearOptions;?>
										</select></td>
										
										<td></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OuTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
									</tr>
									
									<tr>
										<td colspan="6"><input type="text" class="form-control" id="mroldod1" ></td>
										<td></td>
										<td colspan="6"><input type="text" class="form-control" id="mroldos1" ></td>
										<td></td>
										<td><input type="text" class="form-control" id="mroldou1" ></td>
									</tr>								
								</table>
							</div>
						</div>					
						<!-- MR -->
					</div>				
				</div>
				<div class="row">
					<div class="col-sm-6">
						<!-- MR Prism -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label>PRISM </label></strong>	
									</div>
									<div class="col-sm-4 text-right">						
									</div>	
									<div class="col-sm-4 text-right">
									</div>
									<div class="col-sm-2 text-right">						
									</div>
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="5" class="text-center od">OD</th>
										<th></th>
										<th colspan="5" class="text-center os">OS</th>									
									</tr>
									
									<tr>
										<th colspan="2" class="text-center">P1</th>
										<th></th>
										<th colspan="2" class="text-center">P2</th>
										<th></th>
										<th colspan="2" class="text-center">P1</th>
										<th></th>
										<th colspan="2" class="text-center">P2</th>
									</tr>
									
									<tr>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdP<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdSel1<?php echo $findx; ?>_text_input"></td>
										<td>/</td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdSlash<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdPrism<?php echo $findx; ?>_text_input"></td>
										<td></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsP<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsSel1<?php echo $findx; ?>_text_input"></td>
										<td>/</td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsSlash<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsPrism<?php echo $findx; ?>_text_input"></td>
									</tr>
									
									<tr>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdP<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BI">BI</option>
											<option value="BO">BO</option>
										</select></td>
										<td>/</td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSlash<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdPrism<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BD">BD</option>
											<option value="BU">BU</option>
										</select></td>
										<td><a class="btn btn-link bl">BL</a></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsP<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BI">BI</option>
											<option value="BO">BO</option>
										</select></td>
										<td>/</td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSlash<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsPrism<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BD">BD</option>
											<option value="BU">BU</option>
										</select></td>
									</tr>
									
									<tr>
										<td colspan="5" class="text-center">
											<input type="text" name="prismoldod1" id="prismoldod1" class="form-control">
										</td>
										<td></td>
										<td colspan="5" class="text-center">
											<input type="text" name="prismoldos1" id="prismoldos1" class="form-control">
										</td>									
									</tr>
									
								</table>
							</div>
						</div>					
						<!-- MR Prism -->
					</div>
					<div class="col-sm-4">
						<!-- GL/PH -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label>GL/PH </label></strong>	
									</div>
									<div class="col-sm-4 text-right">
									</div>	
									<div class="col-sm-4 text-right">
									</div>
									<div class="col-sm-2 text-right">
									</div>
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="2" class="text-center od">OD</th>
										<th></th>
										<th colspan="2" class="text-center os">OS</th>
									</tr>
									<tr>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2Vision<?php echo $findx; ?>_text_input"></td>
										<td ></td>
										<td ><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2Vision<?php echo $findx; ?>_text_input"></td>
									</tr>	
									<tr>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<option value="PH">PH</option>
											<option value="GL">GL</option>
										</select></td>
										<td><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2Vision<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><a class="btn btn-link bl">BL</a></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<option value="PH">PH</option>
											<option value="GL">GL</option>
										</select></td>
										<td><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2Vision<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
									</tr>								
								</table>						
							</div>
						</div>
					</div>
				</div>
			</div>		
			<!-- content -->
			
			<?php
			}
			?>
		<!--MR-->
		
	</div>
	<div class="modal-footer">	
	<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>
	<button type="button" class="btn btn-success" onclick="shiftToParent();">Done</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
      </div>
    </div>

</div>
</div>
<?php
}
//PRS EXTERNAL VA WORK ADDED - USE IN PRS ICON IN ICON BAR BY "ADD EXTERNAL VA" BUTTON 
if(strpos($popName,"popExtMR")!==false){
?>
<script type="text/javascript" >
$(document).ready(function () {
$("#extdos").datetimepicker({timepicker: false, format: window.opener.top.jquery_date_format, maxDate: new Date(), autoclose: true, scrollInput: false});
$('.input').selectpicker();
});

function chkVal(){
	if($('#extdos').val()==''){
		alert('Please enter DOS');
		$('#extdos').focus();
		return false;
	}
	if($('#entered_by_provider').val()==''){
		alert('Please enter Physician Name');
		$('#entered_by_provider').focus();
		return false;
	}

	var tmpVal = new Array();

	$('#externalMR table').find('input[type=text]').each(function(id, elem){
		var inputVal = $(elem).val();
		if(inputVal) tmpVal.push(inputVal);
	});

	if(tmpVal.length == 0){
		alert(' Please select atleast one value to continue ');
		return false;
	}

	$('form#externalMR').submit();
	$("#submit_ext_va").attr("disabled", true);
}

</script>
<!-- Modal -->
<form id="externalMR" action="save_prs_external_mr.php" method="post">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

<div id="<?php echo $popNameModal;?>" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg ">
  
  <!-- Modal content-->
    <div class="modal-content">	
	<div class="modal-body">
		
		<!--External MR-->
			<?php
			//for($i=1;$i<=3;$i++){
			$i = str_replace("popExtMR","",$popName);
			$i = trim($i);
			if(!empty($i)){
				$other="";
				$findx = "";								
				if($i>1){ $other="Other"; }
				if($i>2){ $findx="_".$i; }			
				
			?>
			
			<!-- content -->		
			<div id="row_ext_mr<?php echo $i; ?>" >
				<div class="row " >
					<div class="col-sm-12">
						<!--External MR -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label class="mr_pop_lbl">External MR</label></strong>	
									</div>
									<div class="col-sm-1 text-right">				
										<strong><label class="mr_pop_lbl">DOS</label></strong>	
									</div>
									<div class="col-sm-2 text-right">			
										<input type="text" id="extdos" name="extdos" class="form-control datepicker glyphicon glyphicon-calendar" value="" onchange="chk_mr_given(this)" readonly autocomplete="off" style="background-color: rgb(255, 255, 255);z-index:99999!important;">									
									</div>
									<div class="col-sm-1 text-right">				
										<strong><label class="mr_pop_lbl">Physician Name:</label></strong>	
									</div>
									<div class="col-sm-2 text-right">				
										<input type="text" id="entered_by_provider" name="entered_by_provider" class="form-control">		
									</div>
									<div class="col-sm-2 text-right">
									</div>
									<div class="col-sm-4 text-right">						
									</div>	
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="6" class="text-center od">OD</th>
										<th></th>
										<th colspan="6" class="text-center os">OS</th>
										<th></th>
										<th class="text-center ou">OU</th>
									</tr>
									<tr>
										<th class="text-center">S</th>
										<th class="text-center">C</th>
										<th class="text-center">A</th>
										<th class="text-center">VA<sub>D</sub></th>
										<th class="text-center">ADD</th>
										<th class="text-center">VA<sub>N</sub></th>
										
										<th class="text-center"></th>
										
										<th class="text-center">S</th>
										<th class="text-center">C</th>
										<th class="text-center">A</th>
										<th class="text-center">VA<sub>D</sub></th>
										<th class="text-center">ADD</th>
										<th class="text-center">VA<sub>N</sub></th>
										
										<th></th>
										<th class="text-center">VA<sub>D</sub></th>
									</tr>
									
									<tr>
										<td><input type="text" class="form-control" name="ext_mr_od_s"  id="elem_visMr<?php echo $other; ?>OdS<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_c" id="elem_visMr<?php echo $other; ?>OdC<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_a"  id="elem_visMr<?php echo $other; ?>OdA<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_txt1" id="elem_visMr<?php echo $other; ?>OdTxt1<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_add" id="elem_visMr<?php echo $other; ?>OdAdd<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_txt2" id="elem_visMr<?php echo $other; ?>OdTxt2<?php echo $findx; ?>_text_input"></td>
										
										<td></td>
										
										<td><input type="text" class="form-control" name="ext_mr_os_s" id="elem_visMr<?php echo $other; ?>OsS<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_c" id="elem_visMr<?php echo $other; ?>OsC<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_a" id="elem_visMr<?php echo $other; ?>OsA<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_txt1" id="elem_visMr<?php echo $other; ?>OsTxt1<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_add" id="elem_visMr<?php echo $other; ?>OsAdd<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_txt2" id="elem_visMr<?php echo $other; ?>OsTxt2<?php echo $findx; ?>_text_input"></td>
										
										<td></td>
										<td><input type="text" class="form-control" name="ext_mr_ou_txt1" id="elem_visMr<?php echo $other; ?>OuTxt1<?php echo $findx; ?>_text_input"></td>
									</tr>
									<tr>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdS<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $sphereOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OdC<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $cylinderOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OdA<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $axisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdAdd<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $addOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OdTxt2<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesNearOptions;?>
										</select></td>
										
										<td><a class="btn btn-link bl">BL</a></td>
										
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsS<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $sphereOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OsC<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $cylinderOptions;?>
										</select></td>
										<td><select  class="form-control sm1" id="elem_visMr<?php echo $other; ?>OsA<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $axisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsAdd<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $addOptions;?>
										</select></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OsTxt2<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesNearOptions;?>
										</select></td>
										
										<td></td>
										<td><select  class="form-control" id="elem_visMr<?php echo $other; ?>OuTxt1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
									</tr>
									<tr>
										<td colspan="12"><input type="text" class="form-control" name="ext_mr_desc" id="mroldod1" ></td>
										<!--<td></td>
										<td colspan="6"><input type="text" class="form-control" id="mroldos1" ></td>
										<td></td>
										<td><input type="text" class="form-control" id="mroldou1" ></td>
									</tr>	-->							
								</table>
							</div>
						</div>					
						<!--External MR -->
					</div>				
				</div>
				<div class="row">
					<div class="col-sm-6">
						<!--External MR Prism -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label>PRISM </label></strong>	
									</div>
									<div class="col-sm-4 text-right">						
									</div>	
									<div class="col-sm-4 text-right">
									</div>
									<div class="col-sm-2 text-right">						
									</div>
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="5" class="text-center od">OD</th>
										<th></th>
										<th colspan="5" class="text-center os">OS</th>									
									</tr>
									
									<tr>
										<th colspan="2" class="text-center">P1</th>
										<th></th>
										<th colspan="2" class="text-center">P2</th>
										<th></th>
										<th colspan="2" class="text-center">P1</th>
										<th></th>
										<th colspan="2" class="text-center">P2</th>
									</tr>
									
									<tr>
										<td ><input type="text" class="form-control" name="ext_mr_od_p" id="elem_visMr<?php echo $other; ?>OdP<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" name="ext_mr_od_sel_1" id="elem_visMr<?php echo $other; ?>OdSel1<?php echo $findx; ?>_text_input"></td>
										<td>/</td>
										<td ><input type="text" class="form-control" name="ext_mr_od_slash" id="elem_visMr<?php echo $other; ?>OdSlash<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" name="ext_mr_od_prism" id="elem_visMr<?php echo $other; ?>OdPrism<?php echo $findx; ?>_text_input"></td>
										<td></td>
										<td ><input type="text" class="form-control" name="ext_mr_os_p" id="elem_visMr<?php echo $other; ?>OsP<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" name="ext_mr_os_sel_1" id="elem_visMr<?php echo $other; ?>OsSel1<?php echo $findx; ?>_text_input"></td>
										<td>/</td>
										<td ><input type="text" class="form-control" name="ext_mr_os_slash" id="elem_visMr<?php echo $other; ?>OsSlash<?php echo $findx; ?>_text_input"></td>
										<td ><input type="text" class="form-control" name="ext_mr_os_prism" id="elem_visMr<?php echo $other; ?>OsPrism<?php echo $findx; ?>_text_input"></td>
									</tr>
									
									<tr>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdP<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BI">BI</option>
											<option value="BO">BO</option>
										</select></td>
										<td>/</td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSlash<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdPrism<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BD">BD</option>
											<option value="BU">BU</option>
										</select></td>
										<td><a class="btn btn-link bl">BL</a></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsP<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel1<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BI">BI</option>
											<option value="BO">BO</option>
										</select></td>
										<td>/</td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSlash<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<?php echo $prismOptions;?>
										</select></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsPrism<?php echo $findx; ?>_input" multiple data-size="22.7">
											<option value="">None</option>
											<option value="BD">BD</option>
											<option value="BU">BU</option>
										</select></td>
									</tr>
									
									<tr>
										<td colspan="11" class="text-center">
											<input type="text" name="ext_mr_prism_desc" id="prismoldod1" class="form-control">
										</td>
										<!--<td></td>
										<td colspan="5" class="text-center">
											<input type="text" name="prismoldos1" id="prismoldos1" class="form-control">
										</td>-->						
									</tr>
								</table>
							</div>
						</div>					
						<!--External MR Prism -->
					</div>
					<div class="col-sm-4">
						<!-- GL/PH -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<!--Panel Heading-->
								<div class="row">	
									<div class="col-sm-2">
									<strong><label>GL/PH </label></strong>	
									</div>
									<div class="col-sm-4 text-right">
									</div>	
									<div class="col-sm-4 text-right">
									</div>
									<div class="col-sm-2 text-right">
									</div>
								</div>
							</div>
							<div class="panel-body">
								<!--Panel Content-->
								<table class="table">
									<tr>
										<th colspan="2" class="text-center od">OD</th>
										<th></th>
										<th colspan="2" class="text-center os">OS</th>
									</tr>
									<tr>
										<td ><input type="text" class="form-control" name="ext_mr_od_gl_ph" id="elem_visMr<?php echo $other; ?>OdSel2<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_od_gl_ph_txt" id="elem_visMr<?php echo $other; ?>OdSel2Vision<?php echo $findx; ?>_text_input"></td>
										<td ></td>
										<td ><input type="text" class="form-control" name="ext_mr_os_gl_ph" id="elem_visMr<?php echo $other; ?>OsSel2<?php echo $findx; ?>_text_input"></td>
										<td><input type="text" class="form-control" name="ext_mr_os_gl_ph_txt" id="elem_visMr<?php echo $other; ?>OsSel2Vision<?php echo $findx; ?>_text_input"></td>
									</tr>	
									<tr>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<option value="PH">PH</option>
											<option value="GL">GL</option>
										</select></td>
										<td><select class="form-control" id="elem_visMr<?php echo $other; ?>OdSel2Vision<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
										<td><a class="btn btn-link bl">BL</a></td>
										<td ><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<option value="PH">PH</option>
											<option value="GL">GL</option>
										</select></td>
										<td><select class="form-control" id="elem_visMr<?php echo $other; ?>OsSel2Vision<?php echo $findx; ?>_input" multiple data-size="24.2">
											<option value="">None</option>
											<?php echo $arrAcuitiesMrDisOptions;?>
										</select></td>
									</tr>								
								</table>						
							</div>
						</div>
					</div>
				</div>
			</div>		
			<!-- content -->
			
			<?php
			}
			?>
		<!--External MR-->
		
	</div>
	<div class="modal-footer">	
	<!--<button type="button" class="btn btn-success" onclick="no_change()">No Change</button>-->
		<button id="submit_ext_va" type="button" name="submit_ext_va" value="submit_ext_va" class="btn btn-success" onClick="chkVal()">Done</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnclose">Close</button>	
    </div>
    </div>
</div>
</div>
</form>
<?php
}
?>
<!--- End External MR --->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/vision_pop_up.js"></script>