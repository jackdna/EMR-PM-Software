<?php
/*
 * File: footer.php
 * Coded in PHP7
 * Purpose: Footer information
 * Access Type: Include file
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 */
?>
                            </Div>
                        </Div>
                    </Div>
                </Div>
            </Div>
            <Div class="footer_wrap_2 text-center">
                <div class="container-fluid">
                  <!--  <span class="footer_span">MIT.-->
                  <!--- Commentted By Gaurav For Placing Button -->
 	                 <div class="footer_logo">
                     	 <img class="" src="../images/imedic_logo.svg" > 
                     </div>	
                     <div class="abs_footer_btn">
                 	        <div class="abs_footer_text">
                                <div>
									<?php 
                                        $qry=imw_query("select DATE_FORMAT(financial_dashboard, '%m-%d-%Y') as show_financial_dashboard from copay_policies");
                                        $row=imw_fetch_array($qry);
                                        $show_financial_dashboard=$row['show_financial_dashboard'];
                                    ?>
                                    Last Financial Date: <span id="show_financial_id"><?php echo $show_financial_dashboard;?></span>
                                </div>
                            </div>
                            <div id="page_buttons" class="page_btn">
                            </div>	
                    </div>	
                </div>	
            </Div>
            <iframe src="" name="ref_iframe" id="ref_iframe" style="height:0px; width:0px; visibility:hidden; position:absolute;top:0px ; left:0px;"></iframe>
		</Div>
        <script type="text/javascript">
			//show_loading_image('hide');
		</script>
	</body>
</html>