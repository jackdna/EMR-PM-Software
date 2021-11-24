<!-- Modal -->
<div id="agModal" class="modal fade" role="dialog" >
  <div class="modal-dialog">

<form id="frm_amsler_grid" name="frm_amsler_grid" >
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<div class="row form-inline ams_grid_hdr <?php echo $bggrey; ?>">
			<div class="col-sm-4">
				<h4 class="modal-title">Amsler Grid</h4>
			</div>
			<div class="col-sm-1 div_wnl">
				<span id="flagImg_ag" class="glyphicon <?php echo $flg_src." ".$flg_dis;?>" ></span>			
				<button type="button" class="btn btn-default btn-xs pull-right" onClick="checkwnl_amsler()">WNL</button>
			</div>
			<div class="col-sm-4 div_wnl">			
			<input type="checkbox" id="elem_noChange_amsler" name="elem_noChange_amsler" value="1" class="frcb" <?php echo ($elem_noChange_amsler == "1") ? "checked=\"checked\"" : "" ;?> ><label for="elem_noChange_amsler" class="frcb" >No Change</label>
			<input type="text" id="elem_examDate_amsler" name="elem_examDate_amsler" class="form-control date-pick" value="<?php echo $elem_examDate;?>"   >			
			</div>
		</div>        
      </div>
      <div class="modal-body">
	<!-- content -->

<div class="row <?php echo $bggrey; ?>">
	<div class="col-md-6">
		<div class="row">
			<div class="amsdrw-hdr center-block od" ><span class="flgWnl_2 hidden" id="flagWnlOd_ag" ></span> OD</div>
		</div>
		<div class="row">
			<div class="amsdrw-con center-block" >
			<!-- canvas -->
			<div class="amsdrw pull-left" onmouseout="ag_setWnlFlag();">
				<canvas id="app_ams_od" width="200" height="200" ></canvas>
				<input type="hidden" name="sig_dataapp_ams_od"  id="sig_dataapp_ams_od" />
				<input type="hidden" name="sig_imgapp_ams_od"  id="sig_imgapp_ams_od" value="<?php echo $sig_path_od;?>" />
			</div>	
			<!-- canvas -->
			<div class="cvfbtn pull-left">
				<div class=" glyphicon-color glyphicon-color-red" onClick="changeColor(255,0,0,'app_ams_od')" ></div>
				<div class=" glyphicon-color glyphicon-color-yellow" onClick="changeColor(255,255,0,'app_ams_od')"></div>					
				<div class=" glyphicon-color glyphicon-color-green" onClick="changeColor(0,128,0,'app_ams_od')"></div>
				<div class=" glyphicon-color glyphicon-color-maroon" onClick="changeColor(128,0,0,'app_ams_od')"></div>
				<div class=" glyphicon-color glyphicon-color-blue" onClick="changeColor(0,0,255,'app_ams_od')"></div>
				<div class=" glyphicon-color glyphicon-color-black" onClick="changeColor(0,0,0,'app_ams_od')"></div>
				<div class=" glyphicon-color glyphicon-color-grey" onClick="changeColor(128,128,128,'app_ams_od')"></div>
				<span class="glyphicon glyphicon-color glyphicon-erase" onClick="getClear('app_ams_od')"></span>					
			</div>
			</div>	
		</div>		
	</div>		
	<div class="col-md-6">
		<div class="row">
			<div class="amsdrw-hdr center-block os" ><span class="flgWnl_2 hidden" id="flagWnlOs_ag" ></span> OS</div>
		</div>
		<div class="row text-center">
			<div class="amsdrw-con center-block" >
			<!-- canvas -->
			<div class="amsdrw pull-left " onmouseout="ag_setWnlFlag();">
				<canvas id="app_ams_os" width="200" height="200" ></canvas>
				<input type="hidden" name="sig_dataapp_ams_os"  id="sig_dataapp_ams_os" />
				<input type="hidden" name="sig_imgapp_ams_os"  id="sig_imgapp_ams_os" value="<?php echo $sig_path_os;?>" />
			</div>	
			<!-- canvas -->
			<div class="cvfbtn pull-left">
				<div class=" glyphicon-color glyphicon-color-red" onClick="changeColor(255,0,0,'app_ams_os')" ></div>
				<div class=" glyphicon-color glyphicon-color-yellow" onClick="changeColor(255,255,0,'app_ams_os')"></div>					
				<div class=" glyphicon-color glyphicon-color-green" onClick="changeColor(0,128,0,'app_ams_os')"></div>
				<div class=" glyphicon-color glyphicon-color-maroon" onClick="changeColor(128,0,0,'app_ams_os')"></div>
				<div class=" glyphicon-color glyphicon-color-blue" onClick="changeColor(0,0,255,'app_ams_os')"></div>
				<div class=" glyphicon-color glyphicon-color-black" onClick="changeColor(0,0,0,'app_ams_os')"></div>
				<div class=" glyphicon-color glyphicon-color-grey" onClick="changeColor(128,128,128,'app_ams_os')"></div>
				<span class="glyphicon glyphicon-color glyphicon-erase" onClick="getClear('app_ams_os')"></span>					
			</div>
			</div>	
		</div>		
	</div>
</div>
<div class="row <?php echo $bggrey; ?>">	
	<div class="col-md-8 form-group">
		<label for="elem_notes">Note</label>
		<textarea class="form-control" name="elem_notes_ag" id="elem_notes_ag" rows="1"><?php echo $elem_notes;?></textarea>
	</div>
	
	<div class="col-md-4 form-group">
		<label for="elem_doctorName_show_amsler">Performed By</label>
		<input type="text" id="elem_doctorName_show_amsler" name="elem_doctorName_show_amsler" value="<?php echo $elem_doctorName_show_amsler;?>" class="form-control" readonly>
		<input type="hidden" id="elem_doctorName_amsler" name="elem_doctorName_amsler" value="<?php echo $elem_doctorName_amsler;?>" >
	</div>
</div>
<input type="hidden" id="elem_amslerOs" name="elem_amslerOs" value="<?php echo $elem_amslerOs;?>" >
<input type="hidden" id="elem_amslerOd" name="elem_amslerOd" value="<?php echo $elem_amslerOd;?>" >
<input type="hidden" id="hdAmslerOsOriginal" name="hdAmslerOsOriginal" value="<?php echo $elem_amslerOs;?>">
<input type="hidden" id="hdAmslerOdOriginal" name="hdAmslerOdOriginal" value="<?php echo $elem_amslerOd;?>">				
<input type="hidden" id="elem_doctorSign_amsler" name="elem_doctorSign_amsler" value="<?php echo $elem_doctorSign;?>" >
<input type="hidden" id="hdDoctorSignOriginal_amsler" name="hdDoctorSignOriginal_amsler" value="<?php echo $elem_doctorSign;?>">
<input type="hidden" id="hd_amsler_mode" name="hd_amsler_mode" value="<?php echo $amsler_mode;?>">
<input type="hidden" id="elem_amslerid" name="elem_amslerid" value="<?php echo $amsler_edid;?>">
<input type="hidden" id="wnl_flag_amsler" name="wnl_flag_amsler" value="<?php echo $wnl_flag; ?>" />
<input type="hidden" id="elem_wnlOd_amsler" name="elem_wnlOd_amsler" value="<?php echo $elem_wnlOd; ?>" />
<input type="hidden" id="elem_wnlOs_amsler" name="elem_wnlOs_amsler" value="<?php echo $elem_wnlOs; ?>" />

<!-- content -->
	</div>
      <div class="modal-footer text-center">
	<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?>
	<button type="button" class="btn btn-success done" onclick="funSave_ag()">Done</button>
	<button type="button" class="btn btn-success reset" onclick="funReset_ag()">Reset</button>
	<button type="button" class="btn btn-success reset" onclick="setPrevValues_ag()">Previous</button>
	<?php } ?>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

</form>	
  </div>
</div>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/simple_drawing.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/amsler_grid.js"></script>
