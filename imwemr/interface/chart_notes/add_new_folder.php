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
?>
<?php
/*
File: add_new_folder.php
Purpose: This file is used to create new folders in upload Image section in workview
Access Type : Direct
*/
?>
<?php 
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/folder_function.php");
$library_path = $GLOBALS['webroot'].'/library';
$pid = $_SESSION['patient'];
$redirectPth = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs";
$button="Add New";
$title="Add New Record";
$disp="none";
$parent_cat1=$_REQUEST['folder_id'];
$cat_name=$_REQUEST['cat_name'];
$parent_cat=$_REQUEST['parent_cat'];
$status=$_REQUEST['status'];
if($_REQUEST['sbmit']<>"")	{
	if($_REQUEST['editid']=="")	{
		$mod_qry="select * from ".constant("IMEDIC_SCAN_DB").".folder_categories  where folder_name='$cat_name' and ((patient_id='$pid' and parent_id='$parent_cat') or (patient_id='0' and parent_id='0'))";
		$mod_res=imw_query($mod_qry);
		if(imw_num_rows($mod_res)>0)	{
			$msg="Folder Already Exists";
			$disp="block";
		}else	{
			$ins_qry="insert into ".constant("IMEDIC_SCAN_DB").".folder_categories set
			folder_name='$cat_name',
			parent_id='$parent_cat',
			folder_status='$status',
			patient_id='$pid',
			created_by = '".$_SESSION['authId']."', 
			date_created = '".date('Y-m-d H:i:s')."', 
			modified_section = 'CNscanDocs'";
			imw_query($ins_qry);
			$id=imw_insert_id();
		//	folder_update2($id,$pid); 
			//header("location:folder_category.php?view=all&cat_id=$parent_cat1&elem_refreshNavi=1");
			?>
            	<script>
					var cat_id = '<?php echo $_REQUEST["cat_id"];?>';	
					var redirectPth = '<?php echo $redirectPth;?>';	
					if(!cat_id) {
						cat_id = '<?php echo $_REQUEST["folder_id"];?>';	
					}
					top.frames['fmain'].location.href = redirectPth+"&cat_id="+cat_id;
				</script>
            <?php
			
			exit();
		}
	}else	{
	}
	//echo $dia_Qery;
}
if($_REQUEST['folder_id']<>"")	{
	
		if($_REQUEST['parent_cat']!= 0 || $parent_cat1!=0)
		{
			$subCatChecked = 'checked';
			$parentDisabled = 'enabled';
		}
			else
		{
			$parentDisabled='disabled';
			
		}
}
?>

<html>
	<head>
	<title>Folder Managment</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<script>
function activateParent()
{
	if(document.folder_form.subCat.checked == true)
		document.folder_form.parent_cat.disabled = false;
	else
		document.folder_form.parent_cat.disabled = true;
}
function check_form()	{
	
	if(document.folder_form.cat_name.value=="")	{
		alert("Please enter folder name");
		document.folder_form.cat_name.focus();
		return false
	}
	top.show_loading_image("show",""," Loading...");
	return true;
}

function hideBtn(){
	if(top.document.getElementById("btSaveComment")){
		top.document.getElementById("btSaveComment").style.display = "none";
	}
	if(top.document.getElementById("btBackFolderCat")){
		top.document.getElementById("btBackFolderCat").style.display = "none";
	}
	if(top.document.getElementById("btAddNew")){
		top.document.getElementById("btAddNew").style.display = "none";
	}
	if(top.document.getElementById("scnDocmntBtn")){
		top.document.getElementById("scnDocmntBtn").style.display = "none";
	}
	if(top.document.getElementById("upldDocmntBtn")){
		top.document.getElementById("upldDocmntBtn").style.display = "none";
	}
}

top.show_loading_image("hide");
$(document).ready(function(e) {
    $("select.selectpicker").selectpicker();
});
hideBtn();
</script>

</head>
<body topmargin='0'  rightmargin='0' leftmargin='2' bottommargin='0' marginwidth='2' marginheight='0' >
<form name="folder_form" method="post" action="add_new_folder.php" onSubmit="return check_form();">
    <input type="hidden" name="editid" value="<?php echo $_REQUEST['editid'];?>">
    <input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id'];?>">

    <div class="col-xs-12 bg-white" >
        <div class="col-xs-12" >&nbsp;</div>
        <?php 
        if($_REQUEST['folder_id']=="")	{
        ?>
            <script>
                window.onload=function()	{
                    document.folder_form.parent_cat.disabled=true;
                    document.folder_form.subCat.checked =false;
                }	
            </script>
        <?php 
        }
        $query = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE parent_id=0 AND folder_status ='active' and  (patient_id='$pid' || patient_id='0') order by folder_categories_id";
        $resultSet = imw_query($query) or die(imw_error()); 
        if(imw_num_rows($resultSet))
        {
            $mainArr = array();
            $tempArr = array();
            while($row = imw_fetch_assoc($resultSet))
            {
                $level = 0;
                $categoryID = $row['folder_categories_id'];
                $categoryName = $row['folder_name'];
                $parentID = $row['parent_id'];
                $mainArr[$categoryID] = '&gt;&gt;'.$categoryName;
                $tempArr = getChild1($categoryID, $level);
                $mainArr = mergeArr($mainArr,$tempArr);
            }
    
            $catArr = $mainArr;
    
        }
     ?>
        <div class="col-xs-12" >
            <div class="col-xs-4" >&nbsp;</div>
            <div class="col-xs-2" ><b><?php echo $title;?></b></div>
            <div class="col-xs-6" >&nbsp;</div> 
        </div>
        
        <div class="col-xs-12" >
            <div class="col-xs-4" ></div>
            <div class="col-xs-2" ><b><?php echo $msg;?></b></div>
            <div class="col-xs-6" >&nbsp;</div> 
        </div>
        
        <div class="col-xs-12" >
            <div class="col-xs-2" >&nbsp;</div>
            <div class="col-xs-2" ><b>Folder Name</b></div>
            <div class="col-xs-2" ><input name="cat_name" type="text" class="form-control" value="<?php echo $cat_name1;?>" size="40" /></div>
             
        </div>
        <div class="col-xs-12" >
            <div class="col-xs-2" >&nbsp;</div>
            <div class="col-xs-2" ><label for="subCat"><b>Is Subfolder</b></label></div>
            <div class="col-xs-2" >
                <div class="checkbox" style="margin-top:2px;">
                    <input type="checkbox" name="subCat" id="subCat"  value=1 onClick="return activateParent()" <?php echo $subCatChecked;?>>
                    <label for="subCat"></label>
                </div>
            </div>
        </div>
        <div class="col-xs-12" >
            <div class="col-xs-2" >&nbsp;</div>
            <div class="col-xs-2" ><b>Parent Folder</b></div>
            <div class="col-xs-2" >
                <select name="parent_cat" class="selectpicker " <?php echo $parentDisabled?> >
                       <?php
                            foreach($catArr as $key => $val){
                                if($key == $parent_cat1) {
                                    echo "<option value=$key selected><b>$val</b></option>";
                                }else {
                                    echo "<option value=$key><b>$val</b></option>";
                                }
                            }
                    ?>
              </select>
            </div>
        </div>
    
        <div class="col-xs-12" >
            <div class="col-xs-2" >&nbsp;</div>
            <div class="col-xs-2" ><b>Status</b></div>
            <div class="col-xs-2" style="margin-top:5px;">
                <select name="status" class="selectpicker"  >
                    <option value="active"<?php if($status1='active') echo 'selected';?>>Active</option>
                    <option value="inactive"<?php if($status1=='inactive') echo 'selected';?>>Inactive</option>
                  </select>
            </div>
        </div>
        <div class="col-xs-12" >&nbsp;</div>
        <div class="col-xs-12" >
            <div class="col-xs-4" >&nbsp;</div>
            <div class="col-xs-1" ><input type="submit" class="btn btn-success" value="<?php echo $button;?>" name="sbmit" ></div>
            <div class="col-xs-1" ><input type="button" class="btn btn-success" value="Go Back" name="go_back" onClick="top.show_loading_image('show','',' Loading...');top.fmain.ifrm_FolderContent.location.href='folder_category.php?folder_id=<?php echo $folder_id;?>';" ></div>
            <div class="col-xs-6" >&nbsp;</div> 
        </div>
        
    </div>
</form>
<div class="col-xs-12" >&nbsp;</div>
</body>
</html>
