<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_dentation = "select * from dentation order by `name`";
$rsNotes = imw_query($qry_dentation) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLDentAnesAdmin(obj){
	var  val = obj.innerHTML;
	top.frames[0].frames[0].frames[0].document.getElementById('selected_frame_name_id').value='';
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('local_anes_dentation_admin_id');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
}

/*
var tOutAdminLocalAnes; 
function closeAdminLocalAnes(){
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].document.getElementById('dentationLocalAnesDentationAdminDiv').style.display == "block"){
			top.frames[0].frames[0].document.getElementById('dentationLocalAnesDentationAdminDiv').style.display = "none";
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}

function closeAdminTimeLocalAnes(){
	tOutAdminLocalAnes = setTimeout("closeAdminLocalAnes()", 500);
}
function stopCloseAdminLocalAnes() {
	clearTimeout(tOutAdminLocalAnes);
}
*/
</script>
<!--<div id="dentationLocalAnesDentationDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('dentationLocalAnesDentationDiv');" class="class="col-md-5 col-lg-4 col-xs-5 col-sm-5"" style="position:absolute; background-color:#E0E0E0; width:350px; height:180px;display:none; z-index:3; overflow:hidden; border :solid 1px #DDD; ">

<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('dentationLocalAnesDentationDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="width:100%; overflow:hidden; overflow-y:auto; height:150px;"> -->
    
    

<div id="listContent_l" style="display:none;" class="listContent_l" >
	<ul class="list-group">
	<?php
		 $dent_seq=0;
		 $defaultLocalAnesDentArr = array();
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$dent_seq++;
			$defaultLocalAnesDentArr[] = $row_rsNotes;
			?>
				<li class="list-group-item" onClick="return getInnerHTMLDentAnesAdmin(document.getElementById('<?php echo 'dentation'.$dent_seq;?>'));"><a href="javascript:void(0)" id="<?php echo 'dentation'.$dent_seq;?>"> <?php echo stripslashes($row_rsNotes['name']); ?> </a></li>
                
                
                <!--<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="dentAnes_tr<?php echo $dent_seq; ?>" >
               		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLDentAnes('<?php echo stripslashes($row_rsNotes['name']); ?>');"><?php echo stripslashes($row_rsNotes['name']); ?></div>
       			</div>-->
                
                
            	
			<?php
		}
?>
	</ul>
</div>
<!--</div>-->