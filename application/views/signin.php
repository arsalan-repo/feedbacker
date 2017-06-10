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
<span class="login-img"><img src="<?php echo base_url().'assets/images/login-img.png'; ?>" alt="" /></span> <span class="login-bg"><img src="<?php echo base_url().'assets/images/form-bg.png'; ?>" /></span>
  <div class="login-form">
    <div class="login-form-block">
    <?php if ($this->session->flashdata('success')) { ?>
    <div class="callout callout-success">
        <p><?php echo $this->session->flashdata('success'); ?></p>
    </div>
	<?php } ?>
    <?php if ($this->session->flashdata('error')) { ?>  
        <div class="callout callout-danger">
            <p><?php echo $this->session->flashdata('error'); ?></p>
        </div>
    <?php } ?>
      <div class="login-form-fields">
        <div class="logo">
        	<a href="<?php echo site_url(); ?>">
	            <img src="<?php echo base_url().'assets/images/logo.png'; ?>" alt="" />
            </a>
        </div>
        <?php
		$attributes = array('class' => '', 'id' => 'signin-form');
		// $hidden = array('username' => 'Joe', 'member_id' => '234');
		echo form_open('signin/auth', $attributes);
		// echo form_open('login/auth', '', $hidden);
		?>
        <ul>
          <li>
            <label>Email</label>
            <input type="text" placeholder="" name="email" id="email" />
          </li>
          <li>
            <label>Password</label>
            <input type="password" name="password" placeholder="" id="password" />
          </li>
          <li>
            <input type="submit" name="button" id="button" value="Login" />
          </li>
          <li> 
          	<span class="forgot-text">Forgot Password?</span> 
            <span class="signup-text">
            	<a href="<?php echo site_url('signup'); ?>">New User? Sign Up</a>
            </span>
            <div class="login-with"><span>or login with</span></div>
            <div class="login-social-icons"> <span class="facebook-icon"><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></span> <span class="twitter-icon"><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></span> </div>
          </li>
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
	$("#signin-form").validate({
	
		// Specify the validation rules
		rules: {
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				minlength: 5
			}
		},
		
		// Specify the validation error messages
		messages: {
			email: "Please enter a valid email address",
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			}
		},
		
		submitHandler: function(form) {
			form.submit();
		}
	});
	
	$('.callout-danger').delay(3000).hide('700');
    $('.callout-success').delay(3000).hide('700');
});
</script>
