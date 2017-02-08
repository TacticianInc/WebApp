    
	<div class="row">
<?php
if (!isset($success_text) || strlen($success_text) > 0) {
?>
		<div class="col-xs-12 col-md-6">
			<h1 class="top">Success</h1>
			<p>
<?php
echo $success_text;
?>
			</p>
		</div>
<?php
}else{
?>
        <div class="col-xs-12 col-md-6">
            <h1 class="top">Forgot</h1>
            <p>
            	To reset your password, just complete the form and a new password will be sent to you.
            </p>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default" style="margin-top:20px;">
            	<div class="panel-heading">Reset Password</div>
  				<div class="panel-body">
<?php
if (isset($error) && strlen($error) > 0) {
?>
	<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
<?php
}
?>
    				<form class="form-horizontal" method="post" action="<?php echo site_url("account/forgot"); ?>">
    					<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		Enter the email address you used to register.
					    	</div>
					  	</div>
						<div class="form-group">
							<label for="email" class="col-sm-2 control-label">Email</label>
					    	<div class="col-sm-10">
					    		<input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<div class="col-sm-3">
									<img src="data:image/png;base64,<?php echo $captcha;?>" style="width:90px;height:90px">
								</div>
								<div class="col-sm-9">
									<p>Enter the name you see on the image:</p>
									<input type="text" class="form-control" name="captcha" id="captcha" placeholder="" required>
								</div>
							</div>
						</div>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		<button type="submit" id="sbtbutton" class="btn btn-default" disabled>Reset Password</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
        </div>
<?php
}
?>
    </div>