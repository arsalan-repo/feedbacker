<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
<?php
	$attributes = array('class' => '', 'id' => 'create-post-form');
	echo form_open('post/create', $attributes);
	?>
    <div class="container creatae-post-content">
      <h2>Create Post</h2>
      
        <label>Write About?</label>
        <input type="text" name="title" id="title" placeholder="" />
        <label>Location</label>
        <input type="text" name="location" placeholder="" />
        <label>Your Feedback</label>
        <input type="text" name="feedback_cont" id="feedback_cont" placeholder="" />
      <div class="post-btn-block">
        <div class="camera-map-icon">
        	<img src="<?php echo base_url().'assets/images/camera-icon.png'; ?>" alt="" />
            <img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" />
        </div>
      	<span class="post-btn">Post</span>
      </div>
    </div>
    <?php echo form_close(); ?>
</div>
<!-- /.content-wrapper -->
<!-- jQuery Form Validation code -->
<script type="application/javascript">
// When the browser is ready...
$(function() {
	// Set Autocomplete Off
	$("#create-post-form").attr('autocomplete', 'off');
	
	// Setup form validation on the #register-form element
	$(".post-btn").click(function() {
		$("#create-post-form").submit();
	});
	
	$("#create-post-form").validate({
		// Specify the validation rules
		rules: {
			title: {
				required: true
			},
			feedback_cont: {
				required: true
			}
		},
		
		// Specify the validation error messages
		messages: {
			title: "Please enter a title",
			feedback_cont: "Please enter a feedback"
		},
		
		submitHandler: function(form) {
			form.submit();
		}
	});
	
	$('.callout-danger').delay(3000).hide('700');
    $('.callout-success').delay(3000).hide('700');
});
</script>
