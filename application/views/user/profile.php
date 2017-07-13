<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion profile-page-wrapper"> <span class="edit-profile-popup-overlay">fas</span>
	<div class="profile-page">
		<div class="profile-image-block">
			<div class="container">
				<?php 
				if(isset($user_data['photo'])) {
				echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_data['photo'].'" alt="" />';
				} else {
				echo '<img src="'.ASSETS_URL . 'images/user-avatar-big.png" alt="" />';
				}
				?>
				<h3><?php echo $user_data['name']; ?></h3>
				<h4><?php echo $this->common->getCountries($user_data['country']); ?></h4>
				<span class="edit-profile-btn"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo $this->lang->line('edit_profile'); ?></span>
				<div class="edit-feedback-btn-block"> 
					<a href="javascript:void(0)" id="show-feedbacks" class="blue-btn" title="Feedbacks"><?php echo $this->lang->line('feedbacks'); ?></a> 
					<a href="javascript:void(0)" id="show-followings" class="normal-btn" title="Followings"><?php echo $this->lang->line('followings'); ?></a>
				</div>
			</div>
		</div>
		<div class="profile-listing-block">
			<!-- Load by Ajax -->
		</div>
	</div>
</div>
<div class="login-form edit-profile-form">
  <div class="login-form-block">
    <div class="login-form-fields">
      <h3><?php echo $this->lang->line('edit_profile'); ?> <span class="close-edit-popup"><img src="<?php echo base_url().'assets/images/close-icon.png'; ?>" alt="" /></span></h3>
      <?php
      $attributes = array('class' => '', 'id' => 'edit-profile-form');
      echo form_open('user/update_profile', $attributes);
      ?>
      <div class="login-form-block-edit-profile">
        <div class="edit-profile-popup-pic">
		<?php 
		if(isset($user_data['photo'])) {
			echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_data['photo'].'" alt="" />';
		} else {
			echo '<img src="'.ASSETS_URL . 'images/user-avatar-big.png" alt="" />';
		} ?>
		</div>
        <div class="fileUpload update-pic-btn">
          <span><?php echo $this->lang->line('upload_picture'); ?></span>
          <input type="file" name="photo" id="photo" class="upload" />
        </div>
        <ul>
          <li>
            <label><?php echo $this->lang->line('name'); ?></label>
            <input type="text" name="name" id="name" value="<?php echo $user_data['name']; ?>" />
          </li>
          <li class="gender">
			<label><?php echo $this->lang->line('gender'); ?></label>
			<div class="radio-block">
			  <span><input type="radio" name="gender" value="Male" <?php if($user_data['gender'] == 'Male') echo ' checked="checked"'; ?> /> <?php echo $this->lang->line('male'); ?></span>
			  <span><input type="radio" name="gender" value="Female" <?php if($user_data['gender'] == 'Female') echo ' checked="checked"'; ?> /> <?php echo $this->lang->line('female'); ?></span>
			  <span><input type="radio" name="gender" value="Other" <?php if($user_data['gender'] == 'Other') echo ' checked="checked"'; ?> /> <?php echo $this->lang->line('other'); ?></span>
			</div>
		  </li>
		  <li>
			<label><?php echo $this->lang->line('birth_date'); ?></label>
			<input type="text" name="dob" id="datepicker" placeholder="" value="<?php echo $user_data['dob']; ?>" />

		  </li>
		  <li class="country-select">
			<label><?php echo $this->lang->line('country'); ?></label>
			<!-- <input name="textfield" placeholder="Jordan" id="textfield" type="text"> -->
			<select name="countries1" id="countries1">
			  <option value="1">US</option>

			  <option value="2">JO</option>
			  <option value="3">IN</option>                  

			</select>
		  </li>
		  <li>
		  	<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_data['id']; ?>" />
			<input type="submit" name="btn_save" id="btn_save" value="<?php echo $this->lang->line('save'); ?>" />
		  </li>
		</ul>
      </div>
	</div>
  </div>
</div>
<!-- /.content-wrapper -->
<script type="text/javascript">
$(document).ready(function() {
	var element = $('.profile-listing-block');
	var user_id = $('#edit-profile-form').find('#user_id').val();		
	
	$.ajax({
		type:'POST',
		url: '<?php echo site_url('user/feedbacks'); ?>',
		data:{user_id:user_id}
	}).done(function(data){
//		console.log(data);
		element.html(data);
	});
	
	$('#show-feedbacks').click(function(e) {
		e.preventDefault();
		
		$.ajax({
			type:'POST',
			url: '<?php echo site_url('user/feedbacks'); ?>',
			data:{user_id:user_id}
		}).done(function(data){
	//		console.log(data);
			element.html(data);
			$('#show-followings').removeClass("blue-btn").addClass("normal-btn");
			$('#show-feedbacks').removeClass("normal-btn").addClass("blue-btn");
		});
	});
	
	$('#show-followings').click(function(e) {
		e.preventDefault();
		
		$.ajax({
			type:'POST',
			url: '<?php echo site_url('user/followings'); ?>',
			data:{user_id:user_id}
		}).done(function(data){
	//		console.log(data);
			element.html(data);
			$('#show-feedbacks').removeClass("blue-btn").addClass("normal-btn");
			$('#show-followings').removeClass("normal-btn").addClass("blue-btn");
		});
	});
});
</script>
