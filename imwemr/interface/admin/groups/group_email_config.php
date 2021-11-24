<div id="email_config_form" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">x</button>
        <h4 class="modal-title" id="modal_title">Email Configuration</h4>
     	</div>
      <div class="modal-body" style="max-height:400px; overflow:hidden; overflow-y:auto;">
      	<form name="groups" method="post" style="margin:0px;">
					<input type="hidden" name="act" value="config_email" />
          <input type="hidden" name="gro_id" id="gro_id" value="<?php echo $templateArray['Other_Information']['GroupDetails'];?>" />
					<input type="hidden" name="txtSave" id="txtSave" value="1" />
          
          	<div class="row">
              <!-- Email -->
              <div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<label for="config_email">Email</label>
          			<input type="text" onBlur="changeClass(this);" name="config_email" id="config_email" class="form-control" value="<?php echo $templateArray['email_config']['config_email'];?>" >
				</div>
            	</div>
              
              <!-- Password -->
              <div class="col-xs-12 col-sm-6">
				<div class="form-group">
              	<label for="config_pwd">Password</label>
          			<input type="password" onBlur="changeClass(this);" name="config_pwd" id="config_pwd" class="form-control" value="<?php echo $templateArray['email_config']['config_pwd'];?>" >
            	</div>
            	</div>
              
              <div class="clearfix"></div>
              
              <!-- Host -->
              <div class="col-xs-6">
			  <div class="form-group">
              	<label for="config_host">Host</label>
          			<input type="text" onBlur="changeClass(this);" name="config_host" id="config_host" class="form-control" value="<?php echo $templateArray['email_config']['config_host'];?>" placeholder="mail.example.com;mail2.example.com">
            	</div>
            	</div>
              
              <!-- Port -->
              <div class="col-xs-6">
			  <div class="form-group">
              	<label for="config_port">Port (25,587)</label>
          			<input type="text" name="config_port" id="config_port" value="<?php echo $templateArray['email_config']['config_port'];?>" class="form-control">
            	</div>
            	</div>
              
              <div class="clearfix"></div>
              
              <!-- Template Header -->
              <div class="col-xs-12">
			  <div class="form-group">
              	<label for="config_port">Template Header</label>
          			<textarea rows="4" onBlur="changeClass(this);" name="config_header" class="form-control" ><?php echo $templateArray['email_config']['config_header'];?></textarea>
            	</div>
            	</div>
              
              
              <!-- Template Footer -->
              <div class="col-xs-12">
			  <div class="form-group">
              	<label for="config_port">Template Footer</label>
               	<textarea rows="4" onBlur="changeClass(this);" name="config_footer" class="form-control" ><?php echo $templateArray['email_config']['config_footer'];?></textarea>
            	</div>
            	</div>
           	
            </div>
  					 
				</form>	
      </div>
      
      <div class="modal-footer"></div>
      
    </div>
  </div>
</div>