<?php
//
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $this->class_var['page_title'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS["webroot"];?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS["webroot"];?>/library/css/bootstrap.css" rel="stylesheet">

<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/jquery.min.1.12.4.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS["webroot"];?>/library/js/common.js"></script>
<script language="javascript">
    function proceed(mode){
        do_audit = 'no';
        document.frm_sla.sla_mode.value = mode;
        document.frm_sla.submit();
    }
    
</script>
<style type="text/css">
    .sla td {line-height:1.5;}
    .sla th{text-align:left;}
    .sla td,th{padding-bottom:5px;}
    .sla th[colspan="2"]{font-size:16px;}
    .sla td, th{vertical-align:top;}
</style>
</head>
	<body scroll="auto" <?php if(APP_DEBUG_MODE!=1){?>oncontextmenu="return false;"<?php }?>>
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="put_me_center_screen"><br>
            <div class="panel-group margin-top-lg margin-top-md margin-top-sm margin-top-xs">
                <div class="panel panel-default">
                  <div class="panel-heading"><h3><?php echo $this->class_var['page_title'];?></h3></div>
                  <div class="panel-body">
                    <div  class="container" style="height:<?php echo $_SESSION['wn_height']-350;?>px; overflow-x:hidden; overflow:auto;" id="sladiv">
                    
                    <table width="90%" align="center" cellpadding="0" cellspacing="0" border="0" class="sla">
                    <tr><th colspan="2">1. General</th></tr>
                    <tr><th width="30px">1.1</th><td width="auto">lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>1.2</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>1.3</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><td colspan="2" height="20px"></td></tr>
                    <tr><th colspan="2">2. Use of Content</th></tr>
                    <tr><th>2.1</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>2.2</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>2.3</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>2.4</th><td><b>lorem ipusum some more lines goes here.</b></td></tr>
                    
                    <tr><td colspan="2" height="20px"></td></tr>
                    <tr><th colspan="2">3. Indemnity</th></tr>
                    <tr><th>3.1</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><td colspan="2" height="20px"></td></tr>
                    <tr><th colspan="2">4. DISCLAIMER OF WARRANTIES; LIMITATION OF LIABILITY</th></tr>
                    <tr><th>4.1</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>4.2</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>4.3</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><th>4.4</th><td>lorem ipusum some more lines goes here.</td></tr>
                    
                    <tr><td colspan="2" height="20px"></td></tr>
                    <tr><th colspan="2">5. Miscellaneous</th></tr>
                    <tr><th>5.1</th><td>lorem ipusum some more lines goes here.</td></tr>
                    </table>
                    
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="text-center">
                        <button class="btn btn-success" title="Please read the complete Agreement" id="ok" disabled onClick="javascript:proceed(1);">Agree</button>
                        &nbsp; &nbsp; &nbsp; 
                        <button class="btn btn-danger" id="cancel" onClick="javascript:proceed(0);">Disagree</button>
                    </div>
                    <form name="frm_sla" id="frm_sla" action="" method="get">
                        <input type="hidden" id="pg" name="pg" value="app-welcome-checks" />
                        <input type="hidden" id="sla_mode" name="sla_mode" value="" />
                        <input type="hidden" id="wn_height" name="wn_height" value="<?php echo $_SESSION['wn_height'];?>}" />
                    </form>             
                  </div>
                </div>
            </div>
            </div>
        </div>
    </div>

	<script language="javascript">
        $('#sladiv').scroll(function(){if(isScrolledV('sladiv')){$('#ok').prop({"disabled":'','title':'Click to Accept License Agreement'});}});
    </script>
	</body>
</html>