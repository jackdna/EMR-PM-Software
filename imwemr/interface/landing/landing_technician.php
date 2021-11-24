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

//this file being include to get top 5 messages
require_once (dirname(__FILE__).'/../../library/classes/msgConsole.php');
$msgConsoleObj = new msgConsole();
$landTechObj = new landing_technician();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>

    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/landing_page.css" rel="stylesheet">
    <!--this style sheet being used for form styles like checkboxes-->
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.carousel.css">
    <!--
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.theme.default.min.css">
    -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<div class="container-fluid pt10 techcol">
	<div class="row">
		<div class="col-sm-6">
			<div class="whitebox">
				<div class="boxheader">
					<h2 data-toggle="collapse" data-target="#phyList" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/mail_icon.png" alt=""/></figure>Practice Physicians</h2>
					<div class="hdoption">
                    	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Move" />
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Reload" /> -->
                    </div>
				</div>
				<div class="clearfix"></div>
				<div id="phyList" class="scroll-content mCustomScrollbar tablcont collaspe in">
					<?php echo $landTechObj->get_main_att_phys();?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="col-sm-6">
            <div class="whitebox">
                <div class="boxheader">
                	<h2 data-toggle="collapse" data-target="#load_messages" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/mail_icon.png" alt=""/></figure>Top 5 Messages </h2>
	                <div class="hdoption">
                    	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('load_messages')" />
                    </div>
                </div>
            	<div class="clearfix"></div>
            	<div id="load_messages" class="scroll-content mCustomScrollbar tablcont collaspe in">
            	<?php
				$landTechObj->top_five_messages('messages');
				?>
	            </div>
				<div class="clearfix"></div>
            </div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="whitebox">
        <div class="boxheader">
        	<h2 data-toggle="collapse" data-target="#ciPatientList" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/checkin.png" alt=""/></figure>Checked In Patient </h2>
			<div class="hdoption">
            	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Move" />
                --><img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('ciPatientList');" />
            </div>
		</div>
		<div class="clearfix"></div>
		<div id="ciPatientList" class="scroll-content mCustomScrollbar tablcont collaspe in">
		     <?php $landTechObj->checked_patient();?>
        </div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
    <div class="whitebox">
        <div class="boxheader">
        <h2 data-toggle="collapse" data-target="#ready4Doc" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/checkin.png" alt=""/></figure>
        Ready For Doctor</h2>
        <div class="hdoption">
        <!--<div class="hdoption"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('ready4Doc');" /></div>

        </div>
    	<div class="clearfix"></div>
        <div id="ready4Doc" class="scroll-content mCustomScrollbar tablcont collaspe in">
              <?php $landTechObj->ready4doctor();?>
        </div>
    	<div class="clearfix"></div>
    </div>
	<div class="clearfix"></div>
    <div class="whitebox">
        <div class="boxheader">
            <h2 data-toggle="collapse" data-target="#todoList" class="link_cursor" aria-expanded="false" aria-controls="collapseExample"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/todo.png" alt=""/></figure>Todo List</h2>
        	<div class="hdoption">
            	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />
                --><img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('todoList');" />
            </div>
        </div>
    	<div class="clearfix"></div>
        <div id="todoList" class="scroll-content mCustomScrollbar tablcont collaspe in">
       <?php $landTechObj->to_do_list(); ?>

        </div>
    	<div class="clearfix"></div>
    </div>
	<div class="clearfix"></div>
	<?php if( get_refill_direct_users() && isERPPortalEnabled() ) {
		include_once('medication_refill_req.php');
	} ?>
</div>
<div class="clearfix"></div>
<?php require_once('common_landing_bottom.php');?>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/chart_js.js"></script>
<script>
//pT portal --
var oPUF=[];
var erp_api_patient_portal = '<?php echo isERPPortalEnabled() ? "1" : "0"; ?>';

	$(document).ready(function(){
		//show options for role change
		if(top.logged_user_type=="3" || top.logged_user_type=="13"){
			var a = '<?php echo (!isset($_SESSION["user_role"]) || empty($_SESSION["user_role"])) ? $_SESSION["logged_user_type"] : $_SESSION["user_role"];?>';
			top.$("#li_rc input[name=el_usr_role]").each(function(){ this.checked = (this.value==a) ? true : false; });
			top.$("#li_rc").show();
		}
    iportal_load_app_reqs();
	});
    if(typeof(top.btn_show)!='undefined'){top.btn_show('DEF');}
</script>
  </body>
</html>
