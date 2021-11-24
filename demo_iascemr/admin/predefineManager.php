<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$elem_category = $_REQUEST['contentsOf'];

$predefineArray = array('Medications', 'Allergies', 'Evaluation', 'Health Questionnaire', 
						'List of food & drinks', 'Pre-operative comments', 'Site', 
						'Procedures','Procedures Category', 'Diagnosis ICD9', 'Diagnosis ICD10', 'Nourishment Kind', 
						'Recovery comments', 'Physician Orders', 
						'Post-Op Drops', 'Operating room nurses notes', 
						'Nurse Post-Op Checklist', 'Post-Op evaluation', 'Surgical Pack','IOL Manufacturer',
						'Manufacturer Lens Brand','Model','Supplies Used',
						'Laser Chief Complaint','Laser Hx. of Present Illness',
						'Laser Past Medical Hx','Laser SLE','Laser Fundus Exam','Laser Mental State',
						'Laser Post Op Orders','Laser Progress Note',
						'Laser Spot Duration','Laser Spot Size','Laser Power/Wattage','Laser Shots',
						'Laser Total Energy','Laser Degree of Opening','Laser Exposure','Laser Count',
						'Quality Measures','Zip Codes','Practice Name','Chart Unlock','Specialty','Modifiers',
						'Pre-Op Nurse Category','Pre-Op Nurse', 'Intra Op Post Op Orders','Procedures Group',
						'Supply Categories','Dentition','History and Physical','Mac/Regional Questions','Complications'
						);
asort($predefineArray);
?>
<!DOCTYPE html>
<html>
<head>
<title>Pre-define Manager</title>
<?php include("adminLinkfile.php");?>
<script type="text/javascript">
function changeFrameSrc(label, s, total, content){
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';	
	top.frames[0].document.getElementById('addNew').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('optionsButton').style.display = 'none';
	top.frames[0].document.getElementById('importSupplies').style.display = 'none';
	
	top.frames[0].frames[0].document.getElementById('labelTr').innerHTML = content;
	top.frames[0].frames[0].document.getElementById('predefine-list').style.display = 'none';
	/*for(var i=0; i<total; i++){
		if(document.getElementById('td'+i)){
			document.getElementById('link'+i).className = "black";
			document.getElementById("td"+i).style.background = "#BCD2B0";
		}
	}
	
	document.getElementById('link'+s).className = "white";
	document.getElementById("td"+s).style.background = "#003300";
	*/
	//alert(top.frames[0].frames[0].frames[0].name);
	//top.frames[0].frames[0].frames[0].src = 'predefineFrmForm.php?contentOf='+content;
	top.frames[0].frames[0].frames[0].location.href = 'predefineFrmForm.php?contentOf='+content;
	if(label=='Chart Unlock') {
		top.frames[0].document.getElementById('saveButton').style.display = 'none';
		top.frames[0].document.getElementById('addNew').style.display = 'none';
	}
	if(label=='Supplies Used') {
		top.frames[0].document.getElementById('importSupplies').style.display = 'inline-block';
	}
	
	if(top.frames[0].frames[0].frames[0].location.href != 'predefineFrmForm.php')
	{
		$('#predefine_Drop').show();
	}
}
	$(window).load(function()
	{
		var LDL	=	function()
		{
			var H	=parent.top.$("#div_middle").height() - top.frames[0].$("#div_innr_btn").outerHeight()-top.frames[0].frames[0].$(".subtracting-head").height()-10;
			$("iframe").attr('height', H +'px');
		}
		LDL();
		
		$(window).resize(function(e) {
           LDL();
        });
	});


</script>

</head>
<body style="margin:0px;">
<div class="margin_bottom_mid_adjustment scheduler_margins_head">
    <div class="container-fluid padding_0">
        <div class="inner_surg_middle">
              <div style="" id="" class="all_content1_slider all_admin_content_agree">	         
                   <div class="subtracting-head"> 
                         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                            <span id="labelTr">
                                Pre-Define
                            </span>
                          </div>
                   </div>
                   <div class="dropdown predefine_Drop" id="predefine_Drop" style="display:none">
                      <Div class="abs_pre_hover">
                        <a href="javascript:void(0)" data-target="#predefine-dropdown" class="btn btn-info btn-md dropdown-toggle" data-toggle="dropdown"> Pre-Define <Span class="fa fa-gears"></Span> </a>
                      </Div>
                      <div class="dropdown-menu" id="predefine-dropdown">
                      <Div class="container-fluid">
                      <ul class="predefine-ul-hover list-unstyled">
                            <?php
						foreach($predefineArray as $key => $preDefineLabel){
							$totalTabs = count($predefineArray);
							++$counter_pre;
							?>
							<li class="col-md-4 col-sm-4 col-xs-4 col-lg-2"> <a id="link<?php echo $key; ?>" href="javascript:changeFrameSrc('<?php echo $preDefineLabel; ?>', '<?php echo $key; ?>', '<?php echo $totalTabs; ?>', '<?php echo $preDefineLabel; ?>')" data-hover="<?php echo $preDefineLabel; ?>" class="btn btn-primary" onClick="hh_hide();"><?php echo $preDefineLabel; ?></a></li>
							<?php
							if($counter_pre==6)
							{
								echo'</ul>
                                        <Div class="clearfix margin_adjustment_only"></Div>
                                        <ul class="predefine-ul-hover list-unstyled" style="">';$counter_pre=0;	
							}
						}
						?></ul>
                      </Div>
                      </div>
                      
                  </div>
                              
                   <div class="open" id="predefine-list">
                        <Div class="container-fluid">
                            <ul class="predefine-ul-hover list-unstyled" style="">
                            <?php
						foreach($predefineArray as $key => $preDefineLabel){
							$totalTabs = count($predefineArray);
							++$counter;
							?>
							<li class="col-md-4 col-sm-4 col-xs-4 col-lg-2"> <a id="link<?php echo $key; ?>sub" href="javascript:changeFrameSrc('<?php echo $preDefineLabel; ?>', '<?php echo $key; ?>', '<?php echo $totalTabs; ?>', '<?php echo $preDefineLabel; ?>')" data-hover="<?php echo $preDefineLabel; ?>" class="btn btn-primary" onClick="hh_hide()"><?php echo $preDefineLabel; ?></a></li>
							<?php
							if($counter==6)
							{
								echo'</ul>
                                        <Div class="clearfix margin_adjustment_only"></Div>
                                        <ul class="predefine-ul-hover list-unstyled" style="">';$counter=0;	
							}
						}
						?></ul>
                         
                        </Div>
                      </div>
                  
                  <script type="text/javascript">
				  function hh_hide()
					{
						$('#predefine-dropdown').hide(); 
					}
					 $(document).ready(function(){
							$(".dropdown.predefine_Drop").hover(            
								function() {
									$('#predefine-dropdown', this).not('.in .dropdown-menu').stop(true,true).slideDown("fast");
									$(this).toggleClass('open'); 
								},
								function() {
									$('#predefine-dropdown', this).not('.in .dropdown-menu').stop(true,true).slideUp("fast");
									$(this).toggleClass('open');       
								}
							);
						});

					</script>
                         
                      
              <iframe name="predefineFormFrm" id="predefineFormFrm" style="width:100%;" seamless frameborder="0" src="predefineFrmForm.php"></iframe>                  
              </div>  
              <Div class="push"></Div>
              <!-- NEcessary PUSH     -->
        </div>
    </div>   
</div>

</body>
</html>