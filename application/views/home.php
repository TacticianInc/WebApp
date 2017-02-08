    
	<div class="row logo">
        <div class="col-xs-12 col-md-6">
            <h1 class="top">Tactician</h1>
            <p>
            	Tactician Investigative Report Writing Software is a comprehensive report writing software system that gives the user a beginning to end platform to document an engagement and resulting investigation from the predication and engagement stage through the dissemination phase and deliverable.
            </p>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default" style="margin-top:20px;">
            	<div class="panel-heading">Sign In</div>
  				<div class="panel-body">
<?php
if (isset($error) && strlen($error) > 0) {
?>
	<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
<?php
}
?>
    				<form class="form-horizontal" method="post" action="<?php echo site_url(""); ?>">
						<div class="form-group">
							<label for="email" class="col-sm-2 control-label">Email</label>
					    	<div class="col-sm-10">
					    		<input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="password" class="col-sm-2 control-label">Password</label>
					    	<div class="col-sm-10">
					      		<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
					    	</div>
					  	</div>
<?php
if (isset($captcha) && strlen($captcha) > 0) {
?>
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
<?php
}
?>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		<a href="<?php echo site_url("account/forgot"); ?>" title="Forgot Password">Forgot Password</a>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		<button type="submit" id="sbtbutton" class="btn btn-default" disabled>Sign in</button>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		By clicking Sign In, you agree to our <a href="<?php echo site_url("legal"); ?>">Terms</a>.
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
        </div>
    </div>