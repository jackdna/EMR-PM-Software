<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$pro_id 		= $_REQUEST['pro_id'];
$tblName		=	base64_decode($_REQUEST['t']);
$keyField		=	base64_decode($_REQUEST['k']);

if($_REQUEST['pro_id']<>"" && $_REQUEST['sub']=='yes'){
	$elem_consentIdArr = $_REQUEST['elem_consentIdList'];
	if($elem_consentIdArr) {
		$elem_consentIdArrImplode = implode(',',$elem_consentIdArr);
	}
	
	$updat="Update ".$tblName." Set consentTemplateId= '".$elem_consentIdArrImplode."' where ".$keyField." = '".$pro_id."'";
	$updat_qry = imw_query($updat) or die(imw_error());		
		
	echo "<script>window.close();</script >";
}
else if($_REQUEST['pro_id'] == "" && $_REQUEST['sub']=='yes')
{
	$elem_consentIdArr = $_REQUEST['elem_consentIdList'];
	if($elem_consentIdArr) {
		$elem_consentIdArrImplode = implode(',',$elem_consentIdArr);
	}	
	
	echo "<script>window.opener.document.getElementById('consentTemplateId').value = "."'$elem_consentIdArrImplode'".";</script>";
	echo "<script>window.close();</script>";
	
}


?>
<!DOCTYPE html>
<html>
<head>
<title>Consent Template</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?>
<script type="text/javascript">
	window.focus();
	function sel(valu){
		var val=valu;
		var obj = document.getElementsByName('elem_consentIdList[]');
		for(var i = 0;i<obj.length;i++){
			if(val=='Select All'){
				obj[i].checked='true';
			}else if(val=='Unselect All'){
				obj[i].checked='';
			}
		}
	}
</script>

 <script type="text/javascript" src="../js/jquery-1.11.3.js"></script>
		
		<script>
			$(window).load(function() 
			{
				$(".loader").fadeOut(1000).hide(1000); 
				bodySize();
			});
			$(window).resize(function()
			{
				bodySize();
			});
			
			var bodySize = function()
			{
				var HH	=	$(".header").height();
				var FH	=	$(".footer").height();
				var DH	=	$(window).height();
				var BH	=	DH - ( HH + FH ) -70;
				//alert('HEader'  + HH + '\n Footer -  ' + FH + '\n Document - ' + DH + '\nBody' + BH);
				
				$(".body").css({'min-height':BH+'px', 'max-height':BH+'px' })
			
			}
			
			//$(window).resize(function(){ size = [1034,630]; window.resizeTo(size[0],size[1]); });
		</script>
</head>
<body>
 <!-- Loader -->
		<div class="loader">
			<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
		</div>
		<!-- Loader-->
        
        <div class="box box-sizing">
        <div class="dialog box-sizing">
            <div class="content box-sizing">
                <div class="header box-sizing text-left ">
                    Consent Template
                </div>
                <div class="body">
                	<form action="#" method="post" name="pro" class="alignCenter">
                    <input type="hidden" value="<?php echo $pro_id; ?>" name="pro_id">
                    <input type="hidden" value="<?php echo $_REQUEST['t']; ?>" name="t">
                    <input type="hidden" value="<?php echo $_REQUEST['k']; ?>" name="k">
                    <input type="hidden" value="yes" name="sub">
                    
                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                        <?php
                        //print_r($consentTemplateId);
                        $consentFormTemplates = $objManageData->getArrayRecords('consent_forms_template','','','consent_name','ASC');
						$seq="";
                        foreach($consentFormTemplates as $templates){
                            if($_REQUEST['pro_id']<>'') {
                                $selectProcedureQry = "Select * From ".$tblName." where ".$keyField." = '".$_REQUEST['pro_id']."' ";
								$selectProcedureRes = imw_query($selectProcedureQry) or die(imw_error());
                                $selectProcedureRow = imw_fetch_array($selectProcedureRes);
                                $consentTemplateId = $selectProcedureRow['consentTemplateId'];
                            }
                            $deletedConsentFormDisplay =	true;
                            $consentTemplateIdExplode	=	array();
                            if($consentTemplateId) { $consentTemplateIdExplode = explode(',',$consentTemplateId);  }
                            if(!in_array($templates->consent_id,$consentTemplateIdExplode) && $templates->consent_delete_status=='true') { $deletedConsentFormDisplay=false;}
                            if($deletedConsentFormDisplay == true) 
							{
                            	++$seq;
                            ?>
                            		<tr style="height:20px;background-color:<?php if(($seq%2)!=0) echo '#FFFFFF';?>;">
                                    	<td class="text_10" style="width:10px;">
                                        	<input type="checkbox"  name="elem_consentIdList[]" value="<?php echo $templates->consent_id; ?>" <?php if(in_array($templates->consent_id,$consentTemplateIdExplode)) { echo "checked"; } ?>>
                                      	</td>
                                        <td style="padding-left:10px;">
                                        	<?php echo stripslashes($templates->consent_name); ?>
                                      	</td>
                                 	</tr>
                          	<?php
							}
                        }
                        ?>
                  </table>
                            
                    </form>
                </div>
                <div class="footer text-center">
                <a class="btn btn-primary" href="javascript:void(0)" onClick="sel('Select All');" id="selectAll"><b class="fa fa-check" ></b>&nbsp;Select All</a>
				<a class="btn btn-primary" href="javascript:void(0)" onClick="sel('Unselect All');" id="unselectAll"><b class="fa fa-close" ></b>&nbsp;Unselect All</a>
				<a class="btn btn-success" href="javascript:void(0)" onClick="document.pro.submit();" id="saveButton"><b class="fa fa-floppy-o" ></b>&nbsp;Save</a>
                
                </div>
            </div>
        </div>
        </div>
                

</body>
</html>
