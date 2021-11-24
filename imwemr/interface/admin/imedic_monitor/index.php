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
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_imedic_monitor.js?<?php echo filemtime('../../../library/js/admin/admin_imedic_monitor.js');?>"></script>
<style>
.imedhead h2{ font-size:20px; color:#000; margin:10px 0px 10px 10px; border-bottom:1px solid #F0F0F0; padding-bottom:10px;}
</style>
<?php
/*******GETTING SAVED OR DEFAULT IMM SETTINGS*******/
$im_setting_res = imw_query("SELECT setting_name, practice_value FROM imonitor_settings");
$im_settings_arr = array();
if($im_setting_res){
	while($im_setting_rs = imw_fetch_assoc($im_setting_res)){
		$im_settings_arr[$im_setting_rs['setting_name']] = $im_setting_rs['practice_value'];
	}
}?><body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="status_text">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
     
	<div class="whtbox imedhead">
		<div class="table-responsive respotable" >
        	<h2>iMedicMonitor Settings</h2>
        	<div class="container-fluid">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="checkbox">
                            <input name="show_noshow_patients" id="show_noshow_patients" type="checkbox" value="1" onClick="save_imon_basic_settings(this);"<?php if(isset($im_settings_arr['show_noshow_patients']) && $im_settings_arr['show_noshow_patients']=='1'){echo 'checked';} ?>><label for="show_noshow_patients" title="This setting controls displaying ON/OFF listing of NO-SHOW appointments.">List "No Show" Appointments</label>
                        </div>
                    </div>
                    <div class="col-sm-3 form-inline">
                    	<select name="dilation_time" id="dilation_time" class="form-control minimal" onChange="save_imon_basic_settings(this);" style="width:150px;">
                        <?php $dilation_time_arr = array(5,10,15,20,30,45);
						foreach($dilation_time_arr as $v){
							$dilation_time_selected= '';
							if(isset($im_settings_arr['dilation_time']) && $im_settings_arr['dilation_time']==$v) $dilation_time_selected= ' selected';
							echo '<option value="'.$v.'"'.$dilation_time_selected.'>'.$v.' Minutes</option>';
						}						
						?>
                        </select>
                        <label for="chk_auto_refresh">Dilation Time</label>
                    </div>
                    <div class="col-sm-2">
                    	<div class="row"><div class="col-sm-12">
                        <div class="checkbox">
                            <input name="auto_refresh" id="auto_refresh" type="checkbox" value="1" onClick="save_imon_basic_settings(this);"<?php if(isset($im_settings_arr['show_noshow_patients']) && $im_settings_arr['auto_refresh']=='1'){echo 'checked'; $auto_refresh_disabled='';}else{$auto_refresh_disabled=' disabled';} ?> title="This setting controls ON/OFF for auto refreshing of new appointment data in iMedicMonitor."><label for="auto_refresh">Auto-Refresh</label>
                        </div>
                        </div></div>
                        <div class="row"><div class="col-sm-12">
                        <div class="checkbox">
                            <input name="refresh_in_background" id="refresh_in_background" type="checkbox" value="YES" onClick="save_imon_basic_settings(this);"<?php if(isset($im_settings_arr['show_noshow_patients']) && $im_settings_arr['refresh_in_background']=='YES'){echo 'checked';}?> title="This setting controls ON/OFF for data refresh on iMedicMonitor window if iMedicMonitor browser window minimized or not an active window."<?php echo $auto_refresh_disabled;?>><label for="refresh_in_background">Refresh in Background Also</label>
                        </div>
                        </div></div>
                    </div>
                    <div class="col-sm-3 form-inline">
                        <select name="refresh_interval" id="refresh_interval" class="form-control minimal" onChange="save_imon_basic_settings(this);" style="width:150px;"<?php echo $auto_refresh_disabled;?>>
                        <?php $refresh_interval_arr = array('10 Seconds'=>'10000','20 Seconds'=>'20000','30 Seconds'=>'30000','45 Seconds'=>'45000','1 Minute'=>'60000','2 Minute'=>'120000','5 Minute'=>'300000','10 Minute'=>'600000');
						foreach($refresh_interval_arr as $k=>$v){
							$refresh_interval_selected= '';
							if(isset($im_settings_arr['refresh_interval']) && $im_settings_arr['refresh_interval']==$v) $refresh_interval_selected= ' selected';
							echo '<option value="'.$v.'"'.$refresh_interval_selected.'>'.$k.'</option>';
						}
						?>
                        </select>
                        <label>Refresh Interval</label>
                    </div>
                    
                    
                </div>
            </div>
        </div>
        
        <div class="table-responsive respotable mt10" style="height:<?php echo ($_SESSION['wn_height']-450);?>px;">
        	<h2>Ready-For Settings</h2>
			<table class="table table-bordered adminnw tbl_fixed">
                <thead>
					<tr>
						<th style="width:2.4%"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th style="vertical-align:baseline;" onClick="LoadResultSet('','','','status_text',this);" class="link_cursor">Ready For Status Text<span></span></th>
						<th style="vertical-align:baseline;">
							<div class="row">
								<div class="col-sm-3">
									<label class="pull-left">Provider: &nbsp;&nbsp;</label>	
								</div>	
								<div class="col-sm-3 content_box">
									<select id="sel_provider_id" class="selectpicker" name="sel_provider_id" onChange="$('#provider_id').val(this.value);LoadResultSet();" data-size="5" data-width="100%" data-live-search="true"></select>	
								</div>	
							</div>							
						</th>
						<th style="vertical-align:baseline;">Status Color<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>  
	
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog"> 
		<!-- Modal content-->
			<div class="modal-content">
				<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData(); return false;">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="id" id="id" >
						<div class="row">
							<div class="col-md-2">
								<label><b>Ready For:</b>&nbsp;</label>
							</div>
							<div class="col-md-7">
								<input class="form-control" name="status_text" id="status_text" required type="text">
							</div>
							<div class="col-md-3">
								<div class="bfh-colorpicker" id="status_color" name="status_color" data-name="status_color" data-color=""></div>
								<input type="hidden" name="provider_id" id="provider_id" value="">
							</div>
						</div>
					</div>
					<div id="module_buttons" class="modal-footer ad_modal_footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>	
				</form>
			</div>
		</div>
	</div>
</body>
<?php 
	require_once('../admin_footer.php');
?>
       