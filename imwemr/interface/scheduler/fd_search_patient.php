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
?>
<div class="clearfix"></div>
<div class="physiciaara">
    <ul>
		<!-- search controls -->
        <li class="physihd">
        	<span class="hd_resolution">Patient</span>
        	<span class="low_resolution">Pt</span>
        </li>
        <li>
            <div class="form-inline physform" id="homeDropDownSCH">
            <!--
            <input type="text" class="form-control" id="exampleInputName2" placeholder="Jane Doe">
              <select class="form-control minimal">
              <option>August 2016</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
            </select>
              <button type="submit" class="searchbut"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
  
  -->
  			
            <?php echo $patientSearch = core_get_patient_search_controls($_SESSION['authId'],"","txt_patient_app_name","findBy","../common/core_search_functions.php","hd_patient_id", $from = "scheduler", "<button type=\"button\" class=\"searchbut\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span></button>", "", "schSearchBox"); ?>
            </div>
        	<div id="getImage"></div>
        </li>
		<!-- scan tool -->
        <li style="display:none" id="fd_scan_links">
        	<!-- web cam -->
        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/camera.png" alt="Web Cam" title="Web Cam" onClick="fd_scan_patient_image()" class="link_cursor" /> 
			<!-- scan tool -->
            <img src="<?php echo $GLOBALS['webroot'];?>/library/images/scan.png" alt="Scan Driving License" title="Scan Driving License" onClick="scan_licence()"/>
       </li>
       <li>
       		<!-- Eligibility Icon -->
			<?php 
            if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
            ?>
            
            <div class="fl" id="div_fd_el_links" style="display:inline-block;"></div>
            <div class="fl" style="cursor:pointer;"><div class="fl" id="rte_info"></div>
                <div class="fl div_popup border white" onmouseout="hide_rte_div();"  style="display:none;background:white;z-index:2500;position:absolute; margin-top:20px;" id="rte_information"></div>
            </div>
            <?php 
            }
            ?>
        </li>
       <li> 
            <!-- collection flag -->
            <div class="fl" style="display:none;" id="collection_flag_space">
            	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/flag_red_collection.png">
            </div>
	   </li>
       <li>
       	<!-- todo flag -->
		<div class="fl" style="display:none;" id="todo_flag_space">
        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/yellow_flagn.gif"  style="cursor:pointer;" onClick="javascript:load_last_app();" alt="To Do" title="To Do">
        </div>
       </li>
       
    </ul>
	<div class="phclose" id="getImageCross" style="display:none"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/closebut.png" onclick="top.clean_patient_session('scheduler');" class="link_cursor"/></div>
</div>
<div class="clearfix"></div>
<?php 
    $dyn_id = "fd_base_controls";
    include("fd_controls.php");
    ?>
<div id="frontdesk2"></div>

<div id="frontdesk">
<!--default div to give height -->
	<div style="height:<?php echo $_SESSION["wn_height"] - 605;?>px;overflow:hidden;margin-left:1px;"></div>
</div>

<!--this modal is being used to show diff detail dynamically from sc script-->
<!--modal wrapper class is being used to control modal design-->
<div class="common_modal_wrapper">
 <!-- Modal -->
<div id="frontdesk_mdl" class="modal fade" role="dialog">
    <div class="modal-dialog modal_90">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
    			<button id="print_app" style="display:none" class="btn  btn-default" onclick="openPrintWindow();"><span class="glyphicon glyphicon-print"></span> Print</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
<!--modal wrapper class end here -->
        
<div id="frontdesk3" style="display:none; margin-top:3px; vertical-align:baseline;">
    <button id="add_recall" class="btn ptprobbut" onMouseOver="button_over_ins('add_recall')" onMouseOut="button_over_ins('add_recall','')" onclick="descrip();">Add Recall</button>&nbsp;
    <button id="close" class="btn ptprobbut"  onMouseOver="button_over_ins('close')" onMouseOut="button_over_ins('close','')" onclick="close_patient_appoitment();">Close</button>
</div>