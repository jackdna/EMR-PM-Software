<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++ discountinued file ++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

?><?php
/*
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$predefineArray = array('Medications', 'Allergies', 'Evaluation', 'Health Questionnaire', 
						'List of food & drinks', 'Pre-operative comments', 'Site', 
						'Procedures', 'Diagnosis ICD9', 'Diagnosis ICD10', 'Nourishment kind', 
						'Recovery comments', 'Physician Orders', 
						'Post-Op Drops', 'Operating room nurses notes', 
						'Post-Op evaluation', 'Surgical Pack','IOL Manufacturer','Manufacturer Lens Brand','Model','Supplies Used',
						'Laser Chief Complaint','Laser Hx. of Present Illness',
						'Laser Past Medical Hx','Laser SLE','Laser Fundus Exam','Laser Mental State',
						'Laser Progress Note',
						'Laser Spot Duration','Laser Spot Size','Laser Power/Wattage','Laser Shots',
						'Laser Total Energy','Laser Degree of Opening','Laser Exposure','Laser Count',
						'Quality Measures','Zip Codes','Practice Name','Chart Unlock','Specialty'
						);
asort($predefineArray);*/
?>
<!--
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Pre Defined Data List</title>
<?php include("adminLinkfile.php");?>
<style>
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
</style>
<script type="text/javascript">
function changeFrameSrc(label, s, total, content){
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';	
	top.frames[0].document.getElementById('addNew').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
	
	/*for(var i=0; i<total; i++){
		if(document.getElementById('td'+i)){
			document.getElementById('link'+i).className = "black";
			document.getElementById("td"+i).style.background = "#BCD2B0";
		}
	}
	
	document.getElementById('link'+s).className = "white";
	document.getElementById("td"+s).style.background = "#003300";
	*/
	top.frames[0].frames[0].frames[1].src = 'predefineFrmForm.php?contentOf='+content;
	top.frames[0].frames[0].document.getElementById('labelTr').style.display = 'none';
	if(label=='Chart Unlock') {
		top.frames[0].document.getElementById('saveButton').style.display = 'none';
		top.frames[0].document.getElementById('addNew').style.display = 'none';
	}
}

$(window).load(function()
{
	var LDL	=	function()
	{
		var topHeight=top.frames[0].frames[0].$("#preDefinedDataList_frame").innerHeight()-$(".subtracting-head").height();
		$("#predefine-tabs-wraper").css({'min-height':topHeight+'px', 'max-height':topHeight+'px' })
	}
	LDL();
	$(window).resize(function(e) {
	   LDL();
	});
});
</script>
</head>
<body style="margin:5px; 0px 0px 5px;" id="myBody">

<div class="margin_bottom_mid_adjustment scheduler_margins_head">
    <div class="container-fluid padding_0">
        <div class="inner_surg_middle">
              <div style="" id="" class="all_content1_slider all_admin_content_agree">	         
                   <div class="subtracting-head"> 
                         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                            <span>
                                Pre-Define
                            </span>
                          </div>
                   </div>
                          <Div class="wrap_inside_admin" style="overflow:auto" id="predefine-tabs-wraper">
                                <div class="predefine-tabs">
                                    <ul class="nav-justified nav">
                                    <?php
                                   /* foreach($predefineArray as $key => $preDefineLabel){
                                        $totalTabs = count($predefineArray);
                                        ++$counter;
                                        ?>
                                         <li id="td<?php echo $key; ?>">
                                         <a id="link<?php echo $key; ?>" href="javascript:changeFrameSrc('<?php echo $preDefineLabel; ?>', '<?php echo $key; ?>', '<?php echo $totalTabs; ?>', '<?php echo $preDefineLabel; ?>')" data-hover="<?php echo $preDefineLabel; ?>"><?php echo $preDefineLabel; ?></a>
                                         </li>
                                        <?php
                                        if($counter>3){
                                            $counter = 0;
                                            echo '</ul><ul class="nav-justified nav">';
                                        }
                                    }*/
                                    ?>
                                     </ul>
                                      
                                </div>
                         </Div>
              </div>  
              
              <Div class="push"></Div>
        </div>
    </div>   
</div>
</body>
</html>-->