<?php
include('image.class.php');
echo "<body bgcolor='#cccccc'>";

echo "<p>draw 100 red pixels in random locations</p>";
$image = new image(80, 15);
for($i = 0; $i <= 99; $i++) {
	$x = rand(0, 79);
	$y = rand(0, 14);
	$image->drawPixel($x, $y, 'ff000000');
}
$image->saveImage('test_01.png');
echo "<img hspace='2' vspace='2' src='test_01.png' border='0'>";
echo "<hr />";

echo "<p>draw a red alpha transparent line 3 pixels thick</p>";
$image = new image(80, 15);
$image->drawLine(5, 5, 75, 5, 3, 'ff000050');
$image->saveImage('test_02.png');
echo "<img hspace='2' vspace='2' src='test_02.png' border='0'>";
echo "<hr />";

echo "<p>draw a red alpha transparent box with a 4 pixel border</p>";
$image = new image(50, 50);
$image->drawRectangle(5, 5, 45, 45, 4, 'ff000050');
$image->saveImage('test_03.png');
echo "<img hspace='2' vspace='2' src='test_03.png' border='0'>";
echo "<hr />";

echo "<p>draw a red alpha transparent filled box</p>";
$image = new image(50, 50);
$image->drawFilledRectangle(5, 5, 45, 45, 'ff000050');
$image->saveImage('test_04.png');
echo "<img hspace='2' vspace='2' src='test_04.png' border='0'>";
echo "<hr />";

echo "<p>draw a red alpha transparent filled blue bordered box with a border of 4 pixels</p>";
$image = new image(50, 50);
$image->drawFilledBorderedRectangle(5, 5, 45, 45, 4, '00007f50', 'ff000050');
$image->saveImage('test_05.png');
echo "<img hspace='2' vspace='2' src='test_05.png' border='0'>";
echo "<hr />";

echo "<p>draw a red alpha transparent filled blue bordered box.  The border will increase in size from 0 to 10 pixels pixels for the border and some white text in the box</p>";
for($i = 0; $i <= 10; $i++) {
	$image = new image(200, 50);
	$image->drawFilledBorderedRectangle(0, 0, 200, 50, $i, '00007f50', 'ff000050');
	$image->addFont('verdana.ttf');
	$image->drawText(4, 14, 0, 'verdana.ttf', 12, 'ffffff00', 'Some text');
	$image->saveImage("test_06_".$i.".png");
	echo "<img hspace='2' vspace='2' src='test_06_".$i.".png' border='0'></br >";
}
echo "<hr />";

echo "<p>draw a red and blue alpha transparent box that overlap.  This demonstrates the alpha blending of the images</p>";
$image = new image(50, 50);
$image->drawFilledRectangle(5, 5, 30, 30, 'ff000050');
$image->drawFilledRectangle(20, 20, 45, 45, '0000ff50');
$image->saveImage('test_07.png');
echo "<img hspace='2' vspace='2' src='test_07.png' border='0'>";
?>