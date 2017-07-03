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
      <span class="edit-profile-btn"><i class="fa fa-pencil" aria-hidden="true"></i>Edit profile</span>
      <div class="edit-feedback-btn-block"> 
       <a href="#" class="blue-btn" title="Feedbacks">Feedbacks</a> 
       <a class="normal-btn" href="<?php echo site_url('user/followings'); ?>" title="Followings">Followings</a>
     </div>
   </div>
 </div>
 <div class="profile-listing-block">
  <div class="container">
    <ul>
      <?php if (!empty($feedbacks)) { ?>
      <!-- Loop Starts Here -->
      <?php foreach($feedbacks as $row) { ?>
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
         Unfollow
         <?php } else { ?>    
         Follow <i class="fa fa-plus" aria-hidden="true"></i>
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
      <h3>Edit Profile <span class="close-edit-popup"><img src="<?php echo base_url().'assets/images/close-icon.png'; ?>" alt="" /></span></h3>
      <?php
      $attributes = array('class' => '', 'id' => 'edit-profile-form');
      echo form_open('user/update_profile', $attributes);
      ?>
      <div class="login-form-block-edit-profile">
        <div class="edit-profile-popup-pic"><img src="<?php echo base_url().'assets/images/profile-img.png'; ?>" alt=""></div>
        <div class="fileUpload update-pic-btn">
          <span>Update new picture</span>
          <input type="file" class="upload" />
        </div>
        <ul>
          <li>
            <label>Name</label>
            <input placeholder="Jessica" name="name" id="name" type="text">
          </li>
          <li class="gender">
            <label>Gender</label>
            <div class="radio-block">
              <span><input type="radio" name="gender" value="male"> Male</span>
              <span><input type="radio" name="gender" value="female"> Female</span>
              <span><input type="radio" name="gender" value="other"> Other</span>
              <div>
              </li>
              <li>
                <label>Date of Birth</label>
                <input name="textfield" placeholder="DD/MMM/YYYY" id="datepicker" type="text">

              </li>
              <li class="country-select">
                <label>Country</label>
                <!-- <input name="textfield" placeholder="Jordan" id="textfield" type="text"> -->
                <select name="countries1" id="countries1">
                  <option value="1">1</option>

                  <option value="2">2</option>
                  <option value="3">3</option>                  

                </select>
              </li>
              <li>
                <input name="button" id="button" value="Save" type="submit">
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>
    <!-- /.content-wrapper -->
