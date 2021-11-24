<!-- Modal -->
<div id="cvfModal" class="modal fade" role="dialog" >
  <div class="modal-dialog">

<form id="frmCvf" name="frmCvf" >
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<div class="row form-inline cvf_hdr <?php echo $bggrey; ?>">
			<div class="col-sm-4">
				<h4 class="modal-title">Confrontation Field</h4>
			</div>
			<div class="col-sm-4 div_wnl">
			<button type="button" class="btn btn-default btn-xs" onClick="setWnl_cvf()">WNL</button>
			
			<input type="checkbox" id="elem_noChange_cvf" name="elem_noChange_cvf" value="1" class="frcb" <?php echo ($elem_nochange_cvf == "1") ? "checked=\"checked\"" : "" ;?> ><label for="elem_noChange_cvf" class="frcb" >No Change</label>
			
			</div>
		</div>        
      </div>
      <div class="modal-body">
	<!-- content -->

<div class="row <?php echo $bggrey; ?>">
	<div class="col-md-6">
		<div class="row text-center od">
			OD
		</div>
		<div class="row">
			<div class="col-md-4">
				<div >
					<input type="checkbox" id="elem_fullOd" name="elem_fullOd" value="1" <?php echo ($elem_fullOd == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'full','Od')" class="frcb" ><label for="elem_fullOd" class="frcb">Full</label>
				</div>
				<div >
					<input type="checkbox" id="elem_constrictionOd" name="elem_constrictionOd" value="1" <?php echo ($elem_constrictionOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'const','Od')" class="frcb" ><label for="elem_constrictionOd" class="frcb">Constriction</label>
				</div>
				<div >
					<input type="checkbox" id="elem_sTempOd" name="elem_sTempOd" value="1" <?php echo ($elem_sTempOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'super','Od')" class="frcb"><label for="elem_sTempOd" class="frcb">Superotemporal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infTempOd" name="elem_infTempOd" value="1" <?php echo ($elem_infTempOd == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'infer','Od')" class="frcb" ><label for="elem_infTempOd" class="frcb">Inferotemporal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_sNasalOd" name="elem_sNasalOd" value="1" <?php echo ($elem_sNasalOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'supra','Od')" class="frcb" ><label for="elem_sNasalOd" class="frcb">Superonasal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infNasalOd" name="elem_infNasalOd" value="1" <?php echo ($elem_infNasalOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'nasal','Od')" class="frcb" ><label for="elem_infNasalOd" class="frcb">Inferonasal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_superHalfOd" name="elem_superHalfOd" value="1" <?php echo ($elem_superHalfOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'super','Od')" class="frcb" ><label for="elem_superHalfOd" class="frcb">Superior Half Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infHalfOd" name="elem_infHalfOd" value="1" <?php echo ($elem_infHalfOd  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'half','Od')" class="frcb" ><label for="elem_infHalfOd" class="frcb">Inferior Half Defect</label>
				</div>				
			</div>
			<div class="col-md-8">
				<!-- canvas -->
				<div class="cvfdrw pull-left"> 
					<canvas id="app_cvf_od_drawing" width="200" height="150" ></canvas>
					<input type="hidden" name="sig_dataapp_cvf_od_drawing"  id="sig_dataapp_cvf_od_drawing" />
					<input type="hidden" name="sig_imgapp_cvf_od_drawing"  id="sig_imgapp_cvf_od_drawing" value="<?php echo $sig_path_od;?>" />	
				</div>	
				<!-- canvas -->
				<div class="cvfbtn pull-left">
					<div class=" glyphicon-color glyphicon-color-red" onClick="changeColor(255,0,0,'app_cvf_od_drawing')" ></div>
					<div class=" glyphicon-color glyphicon-color-yellow" onClick="changeColor(255,255,0,'app_cvf_od_drawing')"></div>					
					<div class=" glyphicon-color glyphicon-color-green" onClick="changeColor(0,128,0,'app_cvf_od_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-maroon" onClick="changeColor(128,0,0,'app_cvf_od_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-blue" onClick="changeColor(0,0,255,'app_cvf_od_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-black" onClick="changeColor(0,0,0,'app_cvf_od_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-grey" onClick="changeColor(128,128,128,'app_cvf_od_drawing')"></div>
					<span class="glyphicon glyphicon-color glyphicon-erase" onClick="getClear('app_cvf_od_drawing')"></span>					
				</div>
			</div>			
		</div>
		<div class="row">
			<div class="col-md-4">
				Comments
			</div>
			<div class="col-md-8">
				<textarea class="form-control" name="elem_confrontAdOptionsOd" id="elem_confrontAdOptionsOd"><?php echo $elem_confrontAdOptionsOd;?></textarea>
			</div>
		</div>
	</div>
	<div class="col-md-1" > <a href="javascript:void(0);" <?php if(($finalize_flag != 1) || ($isReviewable)){?> class="btn btn-link" onClick="setBL_cvf()" <?php } ?> >Bilateral</a></div>	
	<div class="col-md-5">
		<div class="row text-center os">
			OS
		</div>
		<div class="row">
			<div class="col-md-4">
				<div >
					<input type="checkbox" id="elem_fullOs" name="elem_fullOs" value="1" <?php echo ($elem_fullOs == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'full','Os')" class="frcb" ><label for="elem_fullOs" class="frcb">Full</label>
				</div>
				<div >
					<input type="checkbox" id="elem_constrictionOs" name="elem_constrictionOs" value="1" <?php echo ($elem_constrictionOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'const','Os')" class="frcb" ><label for="elem_constrictionOs" class="frcb">Constriction</label>
				</div>
				<div >
					<input type="checkbox" id="elem_sTempOs" name="elem_sTempOs" value="1" <?php echo ($elem_sTempOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'super','Os')" class="frcb"><label for="elem_sTempOs" class="frcb">Superotemporal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infTempOs" name="elem_infTempOs" value="1" <?php echo ($elem_infTempOs == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'infer','Os')" class="frcb" ><label for="elem_infTempOs" class="frcb">Inferotemporal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_sNasalOs" name="elem_sNasalOs" value="1" <?php echo ($elem_sNasalOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'supra','Os')" class="frcb" ><label for="elem_sNasalOs" class="frcb">Superonasal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infNasalOs" name="elem_infNasalOs" value="1" <?php echo ($elem_infNasalOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'nasal','Os')" class="frcb" ><label for="elem_infNasalOs" class="frcb">Inferonasal Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_superHalfOs" name="elem_superHalfOs" value="1" <?php echo ($elem_superHalfOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'super','Os')" class="frcb" ><label for="elem_superHalfOs" class="frcb">Superior Half Defect</label>
				</div>
				<div >
					<input type="checkbox" id="elem_infHalfOs" name="elem_infHalfOs" value="1" <?php echo ($elem_infHalfOs  == "1") ? "checked=\"checked\"" : "";?> onClick="setImgText(this,'half','Os')" class="frcb" ><label for="elem_infHalfOs" class="frcb">Inferior Half Defect</label>
				</div>				
			</div>
			<div class="col-md-8">
				<!-- canvas -->
				<div class="cvfdrw pull-left">
					<canvas id="app_cvf_os_drawing" width="200" height="150" ></canvas>
					<input type="hidden" name="sig_dataapp_cvf_os_drawing"  id="sig_dataapp_cvf_os_drawing" />
					<input type="hidden" name="sig_imgapp_cvf_os_drawing"  id="sig_imgapp_cvf_os_drawing" value="<?php echo $sig_path_os;?>" />	
				</div>	
				<!-- canvas -->
				<div class="cvfbtn pull-left">
					<div class=" glyphicon-color glyphicon-color-red" onClick="changeColor(255,0,0,'app_cvf_os_drawing')" ></div>
					<div class=" glyphicon-color glyphicon-color-yellow" onClick="changeColor(255,255,0,'app_cvf_os_drawing')"></div>					
					<div class=" glyphicon-color glyphicon-color-green" onClick="changeColor(0,128,0,'app_cvf_os_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-maroon" onClick="changeColor(128,0,0,'app_cvf_os_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-blue" onClick="changeColor(0,0,255,'app_cvf_os_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-black" onClick="changeColor(0,0,0,'app_cvf_os_drawing')"></div>
					<div class=" glyphicon-color glyphicon-color-grey" onClick="changeColor(128,128,128,'app_cvf_os_drawing')"></div>
					<span class="glyphicon glyphicon-color glyphicon-erase" onClick="getClear('app_cvf_os_drawing')"></span>					
				</div>
			</div>			
		</div>
		<div class="row">
			<div class="col-md-4">
				Comments
			</div>
			<div class="col-md-8">
				<textarea class="form-control" name="elem_confrontAdOptionsOs" id="elem_confrontAdOptionsOs"><?php echo $elem_confrontAdOptionsOs;?></textarea>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="hd_cvf_mode" name="hd_cvf_mode" value="<?php echo $cvf_mode;?>">
<input type="hidden" id="elem_cvfId" name="elem_cvfId" value="<?php echo $cvf_edid;?>">
<input type="hidden" id="elem_wnl_cvf" name="elem_wnl_cvf" value="<?php echo $elem_wnl_cvf;?>">
<input type="hidden" id='wnlOSHiddden_cvf' name='wnlOSHiddden_cvf' value="<?php echo $wnlOSHiddden_cvf; ?>">
<input type="hidden" id='wnlODHiddden_cvf' name='wnlOSHiddden_cvf' value="<?php echo $wnlOSHiddden_cvf; ?>">
<input type="hidden" name="elem_cvfOdDrawing" value="<?php echo $elem_cvfOdDrawing; ?>" >
<input type="hidden" name="hdCvfOdDrawingOriginal" value="<?php echo $elem_cvfOdDrawing; ?>">
<input type="hidden" name="elem_cvfOsDrawing" value="<?php echo $elem_cvfOsDrawing; ?>" >
<input type="hidden" name="hdCvfOsDrawingOriginal" value="<?php echo $elem_cvfOsDrawing; ?>">
		<!-- content -->
	</div>
      <div class="modal-footer text-center">
	<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?> 
	<button type="button" class="btn btn-success done" onclick="funSave_cvf()">Done</button>
	<button type="button" class="btn btn-success reset" onclick="funReset_cvf()">Reset</button>
	<?php } ?>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

</form>	
  </div>
</div>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/simple_drawing.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/cvf.js"></script>


