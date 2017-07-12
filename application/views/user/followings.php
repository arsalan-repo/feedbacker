<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion profile-page-wrapper"> <span class="edit-profile-popup-overlay">fas</span>
    <div class="profile-page">
      <div class="profile-image-block">
		  <div class="container">
		   <?php 
		   if(isset($user_info['photo'])) {
			echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_info['photo'].'" alt="" />';
		  } else {
			echo '<img src="'.ASSETS_URL . 'images/user-avatar-big.png" alt="" />';
		  }
		  ?>
		  <h3><?php echo $user_info['name']; ?></h3>
		  <h4><?php echo $this->common->getCountries($user_info['country']); ?></h4>
		  <span class="edit-profile-btn"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo $this->lang->line('edit_profile'); ?></span>
		  <div class="edit-feedback-btn-block"> 
		   <a href="<?php echo site_url('user/profile'); ?>" class="normal-btn" title="Feedbacks"><?php echo $this->lang->line('feedbacks'); ?></a> 
		   <a href="javascript:void(0)" class="blue-btn" title="Followings"><?php echo $this->lang->line('followings'); ?></a>
		 </div>
	   </div>
	 </div>
      <div class="profile-listing-block">
        <div class="container">
          <ul>
          <?php if (!empty($followings)) { ?>
          <!-- Loop Starts Here -->
          <?php foreach($followings as $row) { ?>
            <li>
              <div class="profile-listing">
                <div class="profile-listing-img-thumb-block">
                  <div class="profile-listing-img-thumb">
                  	<?php
					if(isset($row['user_avatar'])) {
						echo '<img src="'.$row['user_avatar'].'" alt="" />';
					} else {
						echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
					}
					?>
                  </div>
                  <span class="listing-post-profile-name"><?php echo $row['name']; ?></span> 
                  <span class="listing-post-profile-time"><?php echo $row['time']; ?></span> 
                </div>
                <div class="listing-post-name-block"> 
                	<span class="listing-post-name"><?php echo $row['title']; ?></span> 
                    <span class="listing-post-followers"><?php echo $row['followers']; ?> Followers</span> 
                </div>
                <div class="listing-post-img">
                <?php
                	if(!empty($row['feedback_img'])) {
						echo '<img src="'.$row['feedback_img'].'" alt="" />';
					} else {
                		echo '<img src="'.base_url().'assets/images/feedback-placeholder-img.jpg" alt="" />';
                    } ?>    
                </div>
                <p class="user-feedbacks"><?php echo $row['feedback']; ?></p>
                <div class="post-listing-follow-btn"> 
                	<span class="back-arrow">
                    	<img src="<?php echo base_url().'assets/images/reply-arrow.png'; ?>" alt="" />
                    </span> 
                    <span class="follow-btn-default">
                    	<?php if ($row['is_followed']) { ?>
                        	<?php echo $this->lang->line('unfollow'); ?>
                        <?php } else { ?>    
                            <?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i>
                        <?php } ?>
                    </span>
                    <span class="wishlist">
                    	<i class="fa fa-heart-o" aria-hidden="true" <?php $row['is_liked'] ?  'style="color: #f32836;"' : '' ?>></i> 
						<?php echo $row['likes']; ?>
                    </span>
                 </div>
              </div>
            </li>
          <?php } ?>
          <!-- Loop Ends Here -->
          <?php } else { ?>
          <?php echo $no_record_found; ?>
          <?php } ?>  
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="login-form edit-profile-form">
    <div class="login-form-block">
      <div class="login-form-fields">
        <h3><?php echo $this->lang->line('edit_profile'); ?> <span class="close-edit-popup"><img src="<?php echo base_url().'assets/images/close-icon.png'; ?>" alt="" /></span></h3>
        <div class="login-form-block-edit-profile">
        <div class="edit-profile-popup-pic"><img src="<?php echo base_url().'assets/images/profile-img.png'; ?>" alt=""></div>
        <div class="fileUpload update-pic-btn">
          <span><?php echo $this->lang->line('upload_picture'); ?></span>
          <input type="file" class="upload" />
        </div>
        <ul>
          <li>
            <label><?php echo $this->lang->line('name'); ?></label>
            <input placeholder="Jessica" name="name" id="name" type="text">
          </li>
          <li class="gender">
            <label><?php echo $this->lang->line('gender'); ?></label>
				<div class="radio-block">
				  <span><input type="radio" name="gender" value="male"> <?php echo $this->lang->line('male'); ?></span>
				  <span><input type="radio" name="gender" value="female"> <?php echo $this->lang->line('female'); ?></span>
				  <span><input type="radio" name="gender" value="other"> <?php echo $this->lang->line('other'); ?></span>
				</div>
              </li>
              <li>
                <label><?php echo $this->lang->line('birth_date'); ?></label>
                <input name="textfield" placeholder="DD/MMM/YYYY" id="datepicker" type="text">

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
                <input name="button" id="button" value="<?php echo $this->lang->line('save'); ?>" type="submit">
              </li>
            </ul>
          </div>
      </div>
    </div>
  </div>
<!-- /.content-wrapper -->
