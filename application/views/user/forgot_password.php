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
        <div class="forgot-text">
            <strong>Forgot Password?</strong>
            Enter your email below to receive your new password
        </div>
        <?php
		$attributes = array('class' => '', 'id' => 'forgotpwd-form');
		echo form_open('signin/forgot_password', $attributes);
		?>
        <ul>
          <li>
            <label>Email</label>
            <input type="text" autocomplete="off" placeholder="" name="email" id="email" />
          </li>
          <li>
            <input type="submit" name="btn_save" id="btn_save" value="Reset Password" />
          </li>
          <li>
          	<span class="have-an-account-text"><a href="<?php echo site_url(); ?>">Back</a></span>
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
	// Set Autocomplete Off
	$("#forgotpwd-form").attr('autocomplete', 'off');
	
	// Setup form validation on the #register-form element
	$("#forgotpwd-form").validate({
	
		// Specify the validation rules
		rules: {
			email: {
				required: true,
				email: true
			}
		},
		
		// Specify the validation error messages
		messages: {
			email: "Please enter a valid email address"
		},
		
		submitHandler: function(form) {
			form.submit();
		}
	});
	
	$('.callout-danger').delay(3000).hide('700');
    $('.callout-success').delay(3000).hide('700');
});
</script>
