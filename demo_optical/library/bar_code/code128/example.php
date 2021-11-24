<?php
#===========================================================================
#= Script : phpCode128
#= File   : example.php
#= Version: 0.1
#= Author : Mike Leigh
#= Email  : mike@mikeleigh.com
#= Website: http://www.mikeleigh.com/scripts/phpcode128/
#= Support: http://www.mikeleigh.com/forum
#===========================================================================
#= Copyright (c) 2006 Mike Leigh
#= You are free to use and modify this script as long as this header
#= section stays intact
#=
#= This file is part of phpCode128.
#=
#= phpFile is free software; you can redistribute it and/or modify
#= it under the terms of the GNU General Public License as published by
#= the Free Software Foundation; either version 2 of the License, or
#= (at your option) any later version.
#=
#= phpFile is distributed in the hope that it will be useful,
#= but WITHOUT ANY WARRANTY; without even the implied warranty of
#= MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#= GNU General Public License for more details.
#=
#= You should have received a copy of the GNU General Public License
#= along with DownloadCounter; if not, write to the Free Software
#= Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#===========================================================================
include('code128.class.php');

$barcode = new phpCode128('10003', 150, '', '');
$barcode->setBorderWidth(0);
$barcode->setBorderSpacing(0);
$barcode->setPixelWidth(1);
$barcode->setEanStyle(false);
$barcode->setShowText(false);
$barcode->setAutoAdjustFontSize(true);
$barcode->setTextSpacing(10);
$barcode->saveBarcode('barcode_images/1.png');

?>