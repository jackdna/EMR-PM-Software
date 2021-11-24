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

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $this->class_var['page_title'];?></title>
    
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS["webroot"];?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS["webroot"];?>/library/css/bootstrap.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container-fluid">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="put_me_center_screen"><br>
        <div class="panel-group margin-top-lg margin-top-md margin-top-sm margin-top-xs">
            <div class="panel panel-default">
              <div class="panel-heading"><h3><?php echo $this->class_var['page_title'];?></h3></div>
              <div class="panel-body">
				<div  class="container" style="height:<?php echo $_SESSION['wn_height']-350;?>px; overflow-x:hidden; overflow:auto;" id="sladiv">
                <?php echo $this->class_var['hippa_agreement_content'];?>
				</div>
              </div>
              <div class="panel-footer">
                <div class="text-center">
					<input type="button" id="ok" value="Accept" class="btn btn-success" onClick="javascript:proceed(1);">
					&nbsp; &nbsp; &nbsp; 
					<input type="button" id="cancel" value="Cancel" class="btn btn-danger" onClick="javascript:proceed(0);">
				</div>
              	<form name="frm_hippa" action="" method="get">
                    <input type="hidden" id="pg" name="pg" value="app-welcome-checks" />
                    <input type="hidden" id="hippa_mode" name="hippa_mode" value="" />
                    <input type="hidden" id="wn_height" name="wn_height" value="<?php echo $_SESSION['wn_height'];?>" />
                </form>              
              </div>
            </div>
    	</div>
        </div>
    </div>
</div>
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript">

function proceed(mode){
	do_audit = 'no';
	document.frm_hippa.hippa_mode.value = mode;
	document.frm_hippa.submit();
}


</script>
</html>