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
      <h2><?php echo $this->lang->line('create_post'); ?></h2>
      
        <label><?php echo $this->lang->line('write_about'); ?></label>
        <input type="text" name="title" id="title" placeholder="" />
        <label><?php echo $this->lang->line('location'); ?></label>
        <input type="text" name="location" id="location" placeholder="" />
        <label><?php echo $this->lang->line('your_feedback'); ?></label>
        <input type="text" name="feedback_cont" id="feedback_cont" placeholder="" />
      <div class="post-btn-block">
        <div class="camera-map-icon">
        	<div class="camera-icon-block">
                <span>Choose File</span>
                <input name="Select File" type="file" />
            </div>
            <img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" class="geo-map" alt="" />
        </div>
      	<span class="post-btn"><?php echo $this->lang->line('post'); ?></span>
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
				toastr.error('Error getting location. Try later!', 'Failure Alert', {timeOut: 5000});
            }
        }
    });
}
</script>
