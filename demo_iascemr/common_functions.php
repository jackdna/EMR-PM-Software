<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
////////////////////////////////////////////////////////////////////////////////////////////
// Get Applet Image 
///////////////////////////////////////////////////////////////////////////////////////////

function getAppletImage($id,$tblName,$idFieldName,$pixelFieldName,$imgPath,$alt='Image Here',$tmp_imgname)
{
	
	
	echo "imgGd.php?id=$id&tbl=$tblName&pixelField=$pixelFieldName&idField=$idFieldName&imgName=$imgPath";
	return "<img src=\"imgGd.php?id=$id&tbl=$tblName&pixelField=$pixelFieldName&idField=$idFieldName&imgName=$imgPath&tmp_imgname=$tmp_imgname\" alt=\"$alt\">";
   //drawOnImage($appletData,$imgName,"tess.jpg");
}

function getAppletImageFromPixels($pixels)
{	
	return "<img src=\"imgGd_pixels.php?pixels=$pixels\" alt=\"$alt\">";
}
	
?>