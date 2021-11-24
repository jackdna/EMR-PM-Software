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
File: main.php
Purpose: Main interface of iMedicMonitor 
Access Type: Include File
*/
include_once("../globals.php");
include_once("common_functions.php");
$dd_pro_id		= isset($_REQUEST['dd_pro_id']) 		? trim($_REQUEST['dd_pro_id']) 		: 0;
$profiles = iMonProfiles();		/*--GET PROFILES--*/
$pro_id_val_array = array();
foreach($profiles as $field=>$val){
	$pro_id_val_array[$val['id']] = json_decode($val['profile_data']);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add/Edit Group Rooms</title>
<link rel="stylesheet" href="../css/screen_styles.css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.9.2.custom.min.js"></script>
<style type="text/css">
table.settings tr{height:25px;}
table.settings tr th{font-weight:normal; padding:2px 5px;}
div.statusmsg{text-align:center; text-shadow:#FF0; font-size:16px;display:none;}
.groupname input[type="text"]{width:200px;}
.groupname {cursor:pointer}
</style>
<script type="text/javascript">
var lastProfileValue='';
var js_profile_data_array = JSON.parse('<?php echo json_encode($pro_id_val_array);?>');
	
$(document).ready(function() {
	sto = $('#statusmsg');
	initDisp();
	<?php if($dd_pro_id>0){?>$('#sel_profiles').val('<?php echo $dd_pro_id;?>');<?php }?>
	loadSelectedProfile($('#sel_profiles').val())
	
	$('.groupname span').click(function (){
		curr_obj = $(this);
		var text = curr_obj.text();
		var id	 = curr_obj.parent().parent().attr('id').split('_')[1];
		var $this = curr_obj;
		var $input = $('<input type=text>');
		$input.prop('value', text);
		$input.appendTo($this.parent());
		$input.focus();
		$input.select();
		$this.hide();
		$input.focusout(function(){
			saveGroupName(id,$(this).val());
			$this.show();
			$input.remove();
			curr_obj.text($(this).val());
		});
		$input.keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				saveGroupName(id,$(this).val());
			}
		});
	});
});

function saveGroupName(id,val){
	curr_pro_id = $('#sel_profiles').val()
	ajaxURL = "ajax.php?task=AddEditGroup&groupText="+escape(val)+"&groupId="+id+"&pro_id="+curr_pro_id;
	if(val=='') ajaxURL = "ajax.php?task=deleteGroup&groupId="+id;//+"&pro_id="+curr_pro_id;
	if(val=='[+NEW GROUP]..') {return false;}
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		if(r=='false'){top.fAlert('Saving failed! Please try again.');}
		if(r=='duplicate'){top.fAlert('Duplicate value not allowed.');}
		window.location.href=window.location.href;
	  }
	});
}

function DeleteProfile(){
	curr_pro_id = $('#sel_profiles').val()
	ajaxURL = "ajax.php?task=DeleteProfile&pro_id="+curr_pro_id;
	$.ajax({
	  url: ajaxURL,
	  success: function(r){
		if(r=='false'){top.fAlert('Profile delete failed! Please try again.'); return;}
		window.location.href=window.location.href;
	  }
	});
}

function initDisp(){
	ih=$(window).innerHeight();
	dh=$('#div_profiles').height();
	sh=$('#statusbar').height();
	mbh = ih-(sh+dh+25);
	$('#main_body').css({'height':mbh+'px','overflow':'auto'});	
}

function resetCheckBoxes(){
	$('th input[type="checkbox"]').each(function(index, element) {
        $(this).attr('checked',false);
    });
}

function loadSelectedProfile(pro_id){
	resetCheckBoxes();
	if(js_profile_data_array){
		curr_profile = js_profile_data_array[pro_id];
		if(curr_profile){
			for(x in curr_profile){
			//	alert(x+' :: '+curr_profile[x]);
				group_row = $('table.settings tbody tr#group_'+x);
				room_arr = curr_profile[x];
				if(room_arr.length > 0){
					for(i=0;i<room_arr.length;i++){
						group_row.find('th input.room_'+room_arr[i]).attr('checked',true);
					}
				}
			}
		}
	}
}

function saveProfileName(returnType){
	if(typeof(returnType)=='undefined'){returnType='regular';}
	newproname	= $('#txt_profile_name').val();
	default_pro	= $('#default_profile').attr('checked') ? 1 : 0;
	if(newproname==''){
		top.fAlert('Please enter profile title.','iMedicMonitor',$('#txt_profile_name'));
		return;
	}
	ajaxURL = "ajax.php?task=savenewprofile&returnType="+returnType+"&newproname="+newproname+"&default_pro="+default_pro;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		if(r=='true'){
			if(returnType=='first'){top.window.location.href=top.window.location.href;}
		}else if(r=='duplicate'){
			top.fAlert('This profile name already exists. Please enter a unique name.','iMedicMonitor',$('#txt_profile_name'));
		}else if(returnType=='regular'){
			r_temp = r.split('@~@');
			if(r_temp.length==2){
				$("#sel_profiles option:last").before("<option value='"+r_temp[0]+"'>"+r_temp[1]+"</option>");
				$("#sel_profiles").val(r_temp[0]);
				UndoNewProfileForm();
				resetCheckBoxes();
			}
		}else{
			alert(r);
		}
		initDisp();
	  }
	});
}

function UndoNewProfileForm(){
	$('#div_new_profile').hide();
	$('#txt_profile_name').val('');
	$('#div_room_pro').fadeIn();
	$('#statusbar input[type="button"], #grid_data input').attr('disabled',false);	
}
function checkProfileVal(o){
	v= o.value;
	if(v=='newProfile'){
		$('#div_room_pro').hide();
		$('#div_new_profile').fadeIn('fast',null, function(){$('#txt_profile_name').focus();});
		$('#statusbar input[type="button"], #grid_data input').attr('disabled','disabled');
	}else{
		lastProfileValue=v;
		loadSelectedProfile(v);
	}
	initDisp();
}

function saveSettings(){
	var totalRooms=new Array();
	var totalGroups=new Array();
	var fullData=new Array();
	//GET ALL ROOM IDs
	$('table.settings tbody tr').each(function(index, element){
		if($(this).attr('id')){
			group_id_tmp	= $(this).attr('id').split('_');
			if(group_id_tmp[1] && group_id_tmp[1]!=''){
				curr_group_id = group_id_tmp[1];
				thisGroupRooms = new Array();
				$(this).find('th input[type="checkbox"]').each(function(index, element) {
                    if($(this).attr('class') && $(this).attr('checked')){
						room_id_tmp	= $(this).attr('class').split('_');
						if(jQuery.inArray(room_id_tmp[1],totalRooms)== '-1'){
							totalRooms[totalRooms.length]=room_id_tmp[1];
						}
						thisGroupRooms.push(room_id_tmp[1]);
					}
                });
				if(thisGroupRooms.length>0){
					fullData[curr_group_id] = thisGroupRooms;
					totalGroups[totalGroups.length]=curr_group_id;
				}
			} 
		}
    }); 
	//{"1":[1,2,3],"3":[50,51]}
	json_all_settings_arr = new Array();
	for(x in fullData){
		json_all_settings_arr[json_all_settings_arr.length] = '"'+x+'":['+fullData[x].join(',')+']';
	}
	json_all_settings = '{'+json_all_settings_arr.join(',')+'}';
	postVals 	= "task=saveSettings&profileName="+$('#sel_profiles').val()+"&allsettings="+json_all_settings+"&totalGroups="+totalGroups+"&totalRooms="+totalRooms;
	$.ajax({
		type: "POST",
		data: postVals,
		url : "ajax.php", 
		success:function(r){
		  	if(r==1) top.fAlert('Profile settings saved successfully.','iMedicMonitor','top.window.location.href=top.window.location.href;top.removeMessi();');
			else if(r==0){top.fAlert('Settings not saved! Contact support team if error persists.','iMedicMonitor');}
		 //	r = jQuery.parseJSON(r);
		}
	});
}
</script>
</head>
<body>
<!--<span class="helpIcon"></span>-->
<div class="alignCenter">
<?php
if($profiles){?>
	<div id="div_profiles">
        <div id="div_room_pro">
        	Room Profiles: <select name="sel_profiles" id="sel_profiles" style="width:200px;" onChange="checkProfileVal(this)"><?php
			foreach($profiles as $profile_id=>$profile_rs){?>
				<option value="<?php echo $profile_id;?>"><?php echo stripslashes($profile_rs['title']);?></option><?php
			}?>
			<option value="newProfile">--ADD NEW PROFILE--</option>
			</select>
            <span title="Delete This Profile" style="display:inline-block;" class="ml5 icon20 icon20_close" onClick="DeleteProfile()"></span>
        </div>
		<div class="hide" id="div_new_profile"><form name="frm_new_profile" onSubmit="saveProfileName(); return false;">
            Profile Name: <input id="txt_profile_name" type="text" style="width:200px;" maxlength="100">
            <label><input type="checkbox" name="default_profile" id="default_profile" value="1"> Default</label>
            <input type="button" class="btn_ok ml10" value="Save" onClick="saveProfileName()">
            <input type="button" class="btn_normal ml10" value="Cancel" onClick="UndoNewProfileForm();$('#sel_profiles').val(lastProfileValue);">
            </form>
        </div>
	</div>
    <div class="section" id="main_body" style="margin-top:10px; white-space:nowrap; overflow:auto;">
		<?php
        $q = "SELECT id, mac_address,room_no,fac_id FROM mac_room_desc WHERE room_no <> '' AND delete_status=0 ORDER BY room_no";
        $res = imw_query($q);
        $rooms = array();
        if($res && imw_num_rows($res)>0){
            while($rs = imw_fetch_assoc($res)){
                $rooms[$rs['id']] = $rs;
            }
        }
        
        $num_of_cols = count($rooms);
        $q1 = "SELECT id,group_name,group_order FROM imonitor_room_groups WHERE delete_status=0 ORDER BY group_order,id,group_name";
        $res1 = imw_query($q1);echo imw_error();
        if($res1 && imw_num_rows($res1)>=0){?><form name="grid_data" id="grid_data" onSubmit="return false;">
            <table class="table_collapse settings" style="min-width:100%;">
                <thead>
                <tr class="section_header">
                    <th style="min-width:210px;width:auto;">&nbsp;</th>
                    <?php foreach($rooms as $room_id=>$room_data_arr){
                    echo '<th style="min-width:60px;white-space:nowrap">'.$room_data_arr['room_no'].'</th>
                    ';
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php $altClass='';while($rs1 = imw_fetch_assoc($res1)){?>
                <tr<?php echo $altClass;?> id="group_<?php echo $rs1['id'];?>">
                    <th class="alignLeft groupname"><span><?php echo $rs1['group_name'];?></span></th>
                    <?php foreach($rooms as $room_id=>$room_data_arr){
                    echo '<th><input type="checkbox" class="room_'.$room_data_arr['id'].'"></th>
                    ';
                    }
                    ?>
                </tr>
                <?php if($altClass=='') $altClass=' class="altclr"'; else $altClass='';
                }
                ?>
                <tr<?php echo $altClass;?> id="group_new">
                	<th style="cursor:pointer; font-weight:bold;" class="alignLeft groupname"><span title="Click to Add new Group">[+NEW GROUP]..</span></th>
                    <?php foreach($rooms as $room_id=>$room_data_arr){
                    echo '<th><input disabled type="checkbox" class="room_'.$room_data_arr['id'].'"></th>
                    ';}
                    ?>
                </tr>            
                </tbody>
            </table></form>
        <?php	
        }
        
        ?>

    </div>
    <div class="alignCenter" id="statusbar" style="background-color:#A6C9DB; padding:5px">
    	<input type="button" class="btn_ok" value="SAVE" style="height:20px; margin-right:20px" onClick="saveSettings();">
        <input type="button" class="btn_normal ml10" value="CLOSE" onClick="top.removeMessi()">
    </div>
    
    
	<?php
}else{?>
	<h3 class="unBold">Hi!</h3>
	<h3 class="unBold">There is no profile created yet. Please start by creating a new profile for "Room View"</h3>
	<input type="button" class="btn_normal" id="btn_new_profile" value="Add New Profile" onClick="$(this).hide();$('#div_new_profile').fadeIn('slow',null,function(){$('#txt_profile_name').focus();});">
	<div class="hide" id="div_new_profile"><form name="frm_new_profile" onSubmit="saveProfileName('first'); return false;">
    	Profile Name: <input id="txt_profile_name" type="text" style="width:300px;" maxlength="100">
        <input type="submit" class="btn_ok ml10" value="Save"></form>
    </div>
<?php
}
?>
</div>


</body>
</html>