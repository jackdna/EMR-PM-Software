<body class="body_c">
<table width="100%" border="0">
	<tr>
		<td align="center">
			<?php
				include($_REQUEST['file_loc']);
			?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" title="Print " value="Print " class="btn btn-success" id="print_timely_report" onMouseOver="button_over('print_timely_report')" onMouseOut="button_over('print_timely_report', '')" onClick="javascript:window.print()">
		</td>
	</tr>
</table>
</body>
</html>
	