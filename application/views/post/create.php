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
        <input type="text" name="location" id="location" placeholder="" />
        <label>Your Feedback</label>
        <input type="text" name="feedback_cont" id="feedback_cont" placeholder="" />
      <div class="post-btn-block">
        <div class="camera-map-icon">
        	<div class="camera-icon-block">
                <span>Choose File</span>
                <input name="Select File" type="file" />
            </div>
            <img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" class="geo-map" alt="" />
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

	$(".geo-map").click(function() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showLocation);
		} else { 
			$('#location').html('Geolocation is not supported by this browser.');
		}
	});
	
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

function showLocation(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
	
    $.ajax({
        type:'POST',
        url:'<?php echo site_url('post/get_location'); ?>',
        data:'latitude='+latitude+'&longitude='+longitude,
        success:function(response){
            if(response){
				var objJSON = JSON.parse(response);
            	$('#location').val(objJSON.location);
            }else{
                alert('Not Available');
            }
        }
    });
}
</script>
