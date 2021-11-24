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
<?php
/*
File: test_signature.php
Purpose: This file provide Signature section in tests.
Access Type : Include file
*/
?>
<?php
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once("../../library/classes/work_view/wv_functions.php");
?>
			<div class="col-sm-2 tstpanl tstlft">
            	<div>
                    <h2 class="link_cursor"<?php if(!$itsIndexPage){?> data-toggle="collapse" href="#saved_tests_container"<?php }?>>Patient Tests &amp; Labs</h2>
                    <div class="clearfix"></div>
                    <div id="saved_tests_container"<?php if(!$itsIndexPage){?> class="collapse"<?php }?>>
                        <?php
                        //----GET PATIENT SAVED TESTS FOR ACTIVE TESTS-----
                        $patient_tests			= $objTests->get_patient_saved_tests($patient_id);
                        foreach($patient_tests as $pt_test_rs){
                            ?>
                        <h3><?php echo $pt_test_rs['show_name'];?></h3>
                        <div class="clearfix"></div>
                        <div class="tstlst_tab">
                            <ul>
                                <?php foreach($pt_test_rs['test_rs'] as $test_rs){
                                $flag_color = $objTests->get_test_flag_status($test_rs,$pt_test_rs['test_name']);
                                $flag_title = $objTests->get_test_flag_title($flag_color);
                                
                                $list_test_name = $test_rs['dt'];
                                if(!empty($test_rs['purged'])){
                                    $purge_user_initials = getUserFirstName($test_rs['purged'],2);
                                    $list_test_name = '<span class="purged_css" title="Purged">'.$list_test_name.'</span> '.$purge_user_initials[1];
                                }
                                
                                
                                /****CHECKING IF PICTURE ICON NEED TO SHOW***/
                                $current_list_test_images = $objTests->get_test_images($patient_id,$pt_test_rs['test_table'],$test_rs['tId'],$pt_test_rs['test_type']);
                                ?>
                                <li><a class="link_cursor" onClick="loadTest('<?php echo $pt_test_rs['test_table'];?>','<?php echo $test_rs['tId'];?>','<?php echo $pt_test_rs['test_type'];?>','<?php echo $noP;?>');"><?php echo $list_test_name;?></a> <figure class="mlr10"<?php echo $flag_title;?>><span class="glyphicon glyphicon-flag <?php echo $flag_color;?>" aria-hidden="true"></span></figure><?php if($current_list_test_images){?> <figure class="mlr10 link_cursor" title="Images Available. Click to view SlideShow" onclick="test_image_slideshow('<?php echo $pt_test_rs['test_table'];?>','<?php echo $test_rs['tId'];?>','<?php echo $pt_test_rs['test_type'];?>','<?php echo $patient_id;?>')"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></figure> <?php }?></li>
                               <?php }?>
                            </ul>
                        </div>
                        <?php
                        }							
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php if(!$itsIndexPage && isset($test_scan_edit_id_scan) && $test_scan_edit_id_scan > 0){?>
                <div class="uplddoc">
                    <h2 class="link_cursor" data-toggle="collapse" href="#saved_tests_images">TEST Documents</h2>
                    <div class="clearfix"></div>
                    <div id="saved_tests_images">
						
                    </div>
                </div>
                <?php
                $STRPRINT="";
                $current_list_test_images_pdf = $objTests->get_test_images($patient_id,$this_test_properties['test_table'],$test_scan_edit_id_scan,$this_test_properties['test_type']);
                if($current_list_test_images_pdf) {
                    foreach($current_list_test_images_pdf as $scan_doc_rs){
                        if(strpos($scan_doc_rs['file_type'], 'image') === false) {
                            continue;
                        }
                        $imageFilePath	= data_path().$scan_doc_rs['file_path'];
                        //$imgDim = getimagesize($imageFilePath);
                        $new_width  = 900;
                        $new_height = 650;
                        list($width, $height) = getimagesize($imageFilePath);
                        
                        if($width > 900 || $height > 650) {
                            if ($width > $height) {
                              $image_height = floor(($height/$width)*$new_width);
                              $image_width  = $new_width;
                            } else {
                              $image_width  = floor(($width/$height)*$new_height);
                              $image_height = $new_height;
                            }

                            $width = $image_width;
                            $height = $image_height;
                        }
                        $STRPRINT .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">				
                                    <tr><td>
                                    <div style=\"text-align:center\"><img src=\"" . $imageFilePath . "\" height=\"".$height."\" width=\"".$width."\"  ></div>
                                 </td></tr></table>";
                        }
                }
                ?>
                <script type="text/javascript">
				function reloadTestImages(){
					load_this_test_images('<?php echo $patient_id;?>','<?php echo $this_test_properties['test_table'];?>','<?php echo $test_scan_edit_id_scan;?>','<?php echo $this_test_properties['test_type'];?>',true);
				}
				$(document).ready(function(e) {
                    setTimeout(function(){reloadTestImages();},'1000');
                });
				
                </script>
                <div class="clearfix"></div>
                <?php }?>
                <!--<div class="uplddoc">
                    <h2>More Documents</h2>
                    <div class="clearfix"></div>
                    <div class="plr10">
                        <div class="row">
                            <div class="col-sm-4"><div class="upldimg"><img src="../../library/images/pdfimg.png" alt=""/> <div class="upldvw"><a href=""><img src="../../library/images/lgvides.png" alt=""/></a></div></div></div>
                            <div class="col-sm-4"><div class="upldimg"><img src="../../library/images/pdfimg.png" alt=""/> <div class="upldvw"><a href=""><img src="../../library/images/lgvides.png" alt=""/></a></div></div></div>
                            <div class="col-sm-4"><div class="upldimg"><img src="../../library/images/pdfimg.png" alt=""/> <div class="upldvw"><a href=""><img src="../../library/images/lgvides.png" alt=""/></a></div></div></div>
                        </div>
                    </div>
                </div>-->
            </div>
