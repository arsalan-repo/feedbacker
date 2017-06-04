<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Welcome to Feedbacker</title>
<link href="<?php echo base_url().'assets/css/font-awesome.min.css'; ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url().'assets/css/style.css'; ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url().'assets/css/responsive.css'; ?>" rel="stylesheet" type="text/css" />
<!-- jQuery 1.12.1 -->
<script src="<?php echo base_url().'assets/js/jquery-1.12.1.min.js';?>"></script>
<!-- jQuery Validate -->
<script src="<?php echo base_url().'assets/js/jquery.validate.min.js';?>"></script>
</head>

<body>
<div class="login-page"> 
	<span class="login-img">
    	<img src="<?php echo base_url().'assets/images/login-img.png'; ?>" alt="" />
    </span> 
    <span class="login-bg">
    	<img src="<?php echo base_url().'assets/images/form-bg.png'; ?>" />
    </span>
	<div class="login-form">
    <div class="login-form-block">
      <div class="login-form-fields">
        <div class="logo">
        	<a href="<?php echo site_url(); ?>">
            	<img src="<?php echo base_url().'assets/images/logo.png'; ?>" alt="" />
            </a>
        </div>
        <?php
		$attributes = array('class' => '', 'id' => 'signup-form');
		echo form_open('signup/submit', $attributes);
		?>
          <ul>
            <li>
              <label>Name</label>
              <input type="text" placeholder="" name="name" id="name" />
            </li>
            <label>Email</label>
            <input type="text" placeholder="" name="email" id="email" />
            </li>
            <li>
              <label>Password</label>
              <input type="password" name="password" placeholder="" id="password" />
            </li>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="" id="confirm_password" />
            </li>
            <li>
              <input type="submit" name="signup" id="signup" value="Sign Up" />
            </li>
            <li> <span class="have-an-account-text">Have an account? <a href="<?php echo site_url(); ?>">Sign In</a></span> </li>
          </ul>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<!-- jQuery Form Validation code -->
<script type="application/javascript">
// When the browser is ready...
$(function() {
	// Setup form validation on the #register-form element
	$("#signup-form").validate({
	
		// Specify the validation rules
		rules: {
			name: "required",
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				minlength: 5
			},
			confirm_password: {
				equalTo: "#password"
			}
		},
		
		// Specify the validation error messages
		messages: {
			name: "Please enter your name",
			email: "Please enter a valid email address",
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			confirm_password: "Enter Confirm Password Same as Password"
		},
		
		submitHandler: function(form) {
			form.submit();
		}
	});
	
	$('.callout-danger').delay(3000).hide('700');
    $('.callout-success').delay(3000).hide('700');
});
</script>
