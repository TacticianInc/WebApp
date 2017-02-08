    
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
            
<?php
if (!isset($guest) || $guest == FALSE) {
?>
			<h1 class="top">Welcome</h1>
            <p>
            	Thank you for choosing Tactician, the best Investigative Report Writing Software. Please select your plan below:
            </p>
<?php
if (isset($plans) && count($plans) > 0) {

	if (!isset($plan_id) || $plan_id == 0) {
		$plan_id = 1;
	}

	foreach ($plans as $pln) {

		$selected = "";
		$html_id = str_replace(" ", "_", $pln['name']);
		$html_id = strtolower($html_id);

		if (intval($pln['id']) == $plan_id) {
			$selected = " checked";
		}
?>
	<div class="radio">
    	<label>
      		<input type="radio" id="<?php echo $html_id; ?>" name="rdoPlan" value="<?php echo $pln['id']; ?>"<?php echo $selected; ?>> <?php echo $pln['name']; ?> $<?php echo $pln['total']; ?>
    	</label>
  	</div>
<?php		
	}
}
?>

<?php
}else{
?>
			<h1 class="top">Welcome</h1>
            <p>
            	Thank you for accepting the invitation. Please complete the short form to get started.
            </p>
<?php
}
?>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default" style="margin-top:20px;">
            	<div class="panel-heading">Register</div>
  				<div class="panel-body">
<?php
if (isset($error) && strlen($error) > 0) {
?>
	<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
<?php
}
?>
    				<form class="form-horizontal" method="post" action="<?php echo $posturl; ?>">
    					<div class="form-group">
							<label for="name" class="col-sm-2 control-label">Name</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" placeholder="First and Last Name" required>
					    			<span id="nameres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="title" class="col-sm-2 control-label">Title</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="title" id="title" value="<?php echo $title; ?>" placeholder="Your Job Title" required>
					    			<span id="titleres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
						<div class="form-group">
							<label for="email" class="col-sm-2 control-label">Email</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email" required>
					    			<span id="emailres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
<?php
if (!isset($guest) || $guest == FALSE) {
?>
					  	<div class="form-group">
							<label for="company" class="col-sm-2 control-label">Agency</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="company" id="company" value="<?php echo $company; ?>" placeholder="Agency Name" required>
					    			<span id="cmpres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="street" class="col-sm-2 control-label">Street</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="street" id="street" value="<?php echo $street; ?>" placeholder="Street Address" required>
					    			<span id="streetres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="city" class="col-sm-2 control-label">City</label>
					    	<div class="col-sm-6">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="city" id="city" value="<?php echo $city; ?>" placeholder="City" required>
					    			<span id="cityres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					    	<label for="state" class="col-sm-2 control-label">State</label>
					    	<div class="col-sm-2">
					    		<input type="text" size="2" class="form-control" name="state" value="<?php echo $state; ?>" id="state" placeholder="State" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="zip" class="col-sm-2 control-label">Zipcode</label>
					    	<div class="col-sm-6">
					    			<input type="text" size="5" class="form-control" name="zip" value="<?php echo $zip; ?>" id="zip" placeholder="5 digit Zipcode" required>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="phone" class="col-sm-2 control-label">Phone</label>
					    	<div class="col-sm-6">
					    			<input type="text" size="5" class="form-control" name="phone" value="<?php echo $phone; ?>" id="phone" placeholder="Agency Telephone" required>
					    	</div>
					  	</div>
<?php
}
?>
					  	<hr>
					  	<div class="form-group">
					    	<label for="password" class="col-sm-2 control-label">Password</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					      			<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
					    			<span id="passres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="confirm" class="col-sm-2 control-label">Confirm</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					      			<input type="password" class="form-control" name="confirm" id="confirm" placeholder="Password Again" required>
					    			<span id="confres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	
<?php
if (!isset($guest) || $guest == FALSE) {
?>
						<hr>
					  	<div class="form-group">
					  		<div class="col-sm-offset-2 col-sm-10">
					  			<p>Credit Card Information</p>
					  		</div>
					  	</div>
					  	<div class="form-group">
							<label for="cardname" class="col-sm-2 control-label">Name</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="cardname" id="cardname" value="<?php echo $cc_name; ?>" placeholder="Name on Credit Card" required>
					    			<span id="ccnameres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
							<label for="cardnumber" class="col-sm-2 control-label">Number</label>
					    	<div class="col-sm-10">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="cardnumber" id="cardnumber" value="<?php echo $cc_num; ?>" placeholder="Credit Card Number" required>
					    			<span id="ccnumres" class="input-group-addon"></span>
					    		</div>
					    	</div>
					  	</div>
					  	<div class="form-group">
					  		<div class="col-sm-offset-2 col-sm-10">
					  			<input type="hidden" id="cc_type" value=" value="<?php echo $cc_type; ?>"">
					  			<span class="cc visa dk"></span>
					  			<span class="cc mc dk"></span>
					  			<span class="cc amex dk"></span>
					  			<span class="cc disc dk"></span>
					  		</div>
					  	</div>
					  	<div class="form-group">
							<label for="expyear expmonth csv" class="col-sm-2 control-label">Exp</label>
					    	<div class="col-sm-6">
					    		<div class="input-group">
					    			<input type="number" min="1" max="12" value="<?php echo $cc_month; ?>" class="form-control" name="expmonth" id="expmonth" placeholder="MM" required>
					    			<span class="input-group-addon" id="expres">/</span>
					    			<input type="number" min="<?php echo date("Y"); ?>" max="<?php echo date("Y")+20; ?>" value="<?php echo $cc_year; ?>" class="form-control" name="expyear" id="expyear" placeholder="YYYY" required>
					    		</div>
					    	</div>
					    	<div class="col-sm-4">
					    		<div class="input-group">
					    			<input type="text" class="form-control" name="cvv" id="cvv" value="<?php echo $cvv; ?>" placeholder="CVV" required>
					    			<span class="input-group-addon" id="cvvres"><span class="glyphicon glyphicon-credit-card" style="font-size:18px;" aria-hidden="true"></span></span>
					    		</div>
					    	</div>
					  	</div>
					  	<hr>
<?php
}
?>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
<?php
if ((strlen($name) > 0) && (strlen($title) > 0) && (strlen($email) > 0)) {
?>				    		
					      		<button type="submit" id="sbtbutton" class="btn btn-default">Register</button>
<?php
} else {
?>
								<button type="submit" id="sbtbutton" class="btn btn-default" disabled>Register</button>
<?php
}
?>
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