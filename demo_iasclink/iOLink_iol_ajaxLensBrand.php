<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
$manufacture = $_REQUEST['manufacture'];
$manufacture = str_ireplace('~','&',$manufacture);
$manLensQry = "SELECT mlb.name as lensName, mlc.name as catName 
				FROM manufacturer_lens_brand mlb,manufacturer_lens_category mlc 
				WHERE mlc.name='".addslashes($manufacture)."' 
				AND mlc.name!='' 
				AND mlc.manufacturerLensCategoryId= mlb.catId
				ORDER BY mlb.name";
$manLensRes = imw_query($manLensQry) or die(imw_error());
?>
<select name="lensBrand"  id="lensBrand" class="text_10" style=" width:130px;border:1px; <?php echo $IOL_BackColor;?>  " >
            <option value="">Select</option>
	<?php
    if(imw_num_rows($manLensRes)>0) {
        while($manLensRow = imw_fetch_array($manLensRes)) {
			$lensName = $manLensRow['lensName'];?>
            <option value="<?php echo $lensName;?>"><?php echo $lensName;?></option>
    <?php			
        }
    }?> 
</select>
