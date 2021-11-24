<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<!--<link href="../css/style_surgery.css" TYPE="text/css" rel="stylesheet">-->
<!--<link rel="stylesheet" href="../css/form.css" type="text/css" />-->
<!--<link rel="stylesheet" href="../css/theme.css" type="text/css" />-->
<link rel="stylesheet" href="../css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="../css/simpletree.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="../css/ion.calendar.css" />



<script type="text/javascript" src="../js/jquery-1.11.3.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.js"></script>
<script type="text/javascript" src="../js/alert_file.js"></script>		
<script type="text/javascript" src="../js/wufoo.js"></script>
<script type="text/javascript" src="../js/jsFunction.js"></script>
<script type="text/javascript" src="../js/cur_timedate.js"></script>
<script type="text/javascript" src="../js/simpletreemenu.js"></script>
<script type="text/javascript" src="../js/jscript.js"></script>
<script type="text/javascript" src="../js/epost.js"></script>
<script type="text/javascript" src="../js/vitalSignGrid.js"></script>


<script type="text/javascript" src="../js/moment.js"></script>
<script type="text/javascript" src="../js/ion.calendar.js"></script>
<script type="text/javascript" src="../js/overflow.js"></script>

<script type="text/javascript" src="../admin/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../admin/ckeditor/samples/js/sample.js"></script>
<script type="text/javascript" src="../js/bootstrap-select.js"></script>
<script>
	$(document).ready(function(){
		$('.selectpicker').on( 'change', function (e) {
		// take jquery val()s array for multiple
		//store the selected value
		var $selectedOption = $(this).find(':selected');
		var attending = $selectedOption.data('attending');
		var checkAttendings = [];
		$selectedOption.each(function(index){
			checkAttendings.push($(this).data('attending'));
		});
		if(typeof attending == 'undefined' || attending === '') return; 
		// take jquery vals() array for multiple
		var value = checkAttendings || [];
		
		// take the existing old data or create new
		var old = $(this).data('old') || [];
		//alert('OLD IS '+JSON.stringify(old));
		// take the old order or create a new
		var order = $(this).data('order') || [];
		//alert('ORDER'+JSON.stringify(order));
		// find the new items
		var newone = value.filter(function (val) {
			return old.indexOf(val) == -1;
		});
		//alert('NEW One'+JSON.stringify(newone));
		// find missing items
		var missing = old.filter(function (val) {
			return value.indexOf(val) == -1;
		});
		// console.log(missing,newone)
		// remove missing items from order array and add new ones to it
		$.each(missing, function (i, miss) {
			order.splice(order.indexOf(miss), 1);
		})
		$.each(newone, function (i, thing) {
			order.push(thing);
		})
	
		// save the order and old in data()
		$(this).data('old', value).data('order', order);
		
		if(JSON.stringify(order) == JSON.stringify([1,2]) || JSON.stringify(order) == JSON.stringify([0,2]))
		{
			attending = 2;
		}
		
		
		//if current selected array equal to [0,2] that means user trying to check TBD
		if(JSON.stringify(order) == JSON.stringify([1,0]) || JSON.stringify(order) == JSON.stringify([2,0]) )
		{
			attending = 0;
		}
	
		//if current selected array equal to [0,1]  or [2,1] that means user trying to check Attending date
		if(JSON.stringify(order) == JSON.stringify([0,1]) || JSON.stringify(order) == JSON.stringify([2,1])){
			attending = 1;
		}
		//alert(attending);
		console.log(order, 'attending :' + attending);
	
		if(attending == 1)
		{
	
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-success');
			var tbd = $(this).find('[data-attending="2"]');
			var notAttending = $(this).find('[data-attending="0"]');
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
	
			if(notAttending.is(':selected'))
			{
				notAttending.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}
		 
		/*if(attending == 2)
		{
	
			$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-default');
	
			var coming = $(this).find('[data-attending="1"]');
			var not = $(this).find('[data-attending="0"]');
	
			if(coming.is(':selected'))
			{
				console.log('comming');
				coming.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(not.is(':selected'))
			{
				console.log('not comming');
				not.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}*/
		
		if(attending == 0 )
		{
			var screening_date_id 	= 0;
			
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-danger');
	
			var yesComing	=	$(this).find('[data-attending="1"]');
			var tbd 		=	$(this).find('[data-attending="2"]');
			
			if(yesComing.is(':selected'))
			{
				yesComing.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
			   
				$(this).selectpicker('refresh');
			}
			
			order.reverse();
			console.log(order, 'attending :'+attending);
		}
		
		if(typeof attending == 'undefined' || attending == '' )
		{	
			$(this).find('[data-attending="0"]').prop('selected',true);	
			$(this).selectpicker('refresh');
		}
		//order.length = 0;
	});
	});		
</script>
<?PHP
	//include_once ("../autoSessionTimeout.php");
?>


<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
	.textColor{
		color:#666666;
		font-family:Arial, Helvetica, sans-serif;
	}
	/* All Pagination Styles */
	.text_10b_purpule{
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:12px;
		color:#9900CC;
		font-weight:bold;
		text-align:left;
		padding-left:5px;
		text-decoration:none;
	}	
	.pagenation_alpha{
		overflow:auto; width:100%; margin:0px; text-align:center; vertical-align:middle; 
	}
	.pagenation_alpha a{
		display:inline-block; margin:2px 2px; height:30px; padding-top:2px; width:29px; text-align:center;text-decoration:none; color:#FFFFFF; background:transparent url(../images/page_alpha_bg.png) no-repeat; font-size:18px; font-weight:bold;
	}
	.pagenation_alpha a:hover, .pagenation_alpha a:active, .pagenation_alpha a.activealpha{
		color:#333333; height:30px; background:transparent url(../images/page_alpha_hover.png) no-repeat;
	}
	
	a.num{display : inline-block; margin:2px 2px; height:30px; padding-top:2px; width:50px; text-align:center; color:#FFFFFF; background:transparent url(../images/page_alpha_09.png) no-repeat;  font-size:18px; font-weight:bold;}
	a.num:active, a.num:hover{color:#333333; background:transparent url(../images/page_alpha_09_hover.png) no-repeat;}
	.tab_bg{background-image:url(../images/bg_tab.jpg);background-repeat:repeat-x; height:30px;  }
</style>
