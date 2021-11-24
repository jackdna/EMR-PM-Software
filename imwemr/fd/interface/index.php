<?php
/*
 * File: index.php
 * Coded in PHP7
 * Purpose: Tabs information
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
require_once(dirname(__FILE__)."/../library/header.php");
require_once(dirname(__FILE__)."/../library/classes/function.php");
require_once(dirname(__FILE__)."/../library/classes/login_functions.php");

?>
<div class="col-md-12 col-sm-9 col-xs-12 col-lg-12 padding_left_sm bg_white">
    <Div class="tab-content">
    	<div class="clearfix green_tape visible-sm"></div>
        <Div class="inner_middle_wrap">
             <Div class="tab-pane active fade in" id="birds_eye_tab">
                 <?php 
				 	if($show_tab=="birds_eye_tab"){
						require_once(dirname(__FILE__)."/birds_eye.php"); 
					}else if($show_tab=="charges_tab"){
						require_once(dirname(__FILE__)."/charges_report.php"); 
					}else if($show_tab=="payments_tab"){
						require_once(dirname(__FILE__)."/payments_report.php"); 
					}else if($show_tab=="sch_tab"){
						require_once(dirname(__FILE__)."/sch_report.php"); 
					}else if($show_tab=="ledger_tab"){
						require_once(dirname(__FILE__)."/ledger_report.php"); 
					}else if($show_tab=="physicians_tab"){
						require_once(dirname(__FILE__)."/phy_report.php"); 
					}else if($show_tab=="referring_tab"){
						require_once(dirname(__FILE__)."/ref_phy_report.php"); 
					}
					else if($show_tab=="trends_tab"){
						require_once(dirname(__FILE__)."/trends.php"); 
					}
				 ?>
             </Div>
        </Div>  <!----- INNER MIDDLE WRAP --> 
    </Div>	 <!------------ TAb-cOntent ------------------>                       
    
</div>
<?php require_once(dirname(__FILE__)."/../library/footer.php");?>