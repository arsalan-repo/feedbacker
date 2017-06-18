<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<header>
  <div class="container">
    <div class="logo"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url().'assets/images/white-logo.png'; ?>" alt="" /></a></div>
    <div class="header-right">
      <div class="header-search">
      <?php echo form_open('post/search'); ?>
        <input type="text" name="textfield" placeholder="Type in to search" id="textfield" />
        <button type="submit"></button>
      <?php echo form_close(); ?>
      </div>
      <div class="header-create-post"><a href="<?php echo site_url('post/create'); ?>">Create Post</a></div>
      <div class="header-notification">
	  	<span class="notification-count"></span>
		<a href="<?php echo site_url('user/notifications'); ?>"><img src="<?php echo base_url().'assets/images/notification-icon.png'; ?>" alt="" /></a>
	  </div>
      <div class="header-flag"> <img src="<?php echo base_url().'assets/images/english-flag.png'; ?>" alt="" /> <i class="fa fa-caret-down" aria-hidden="true"></i> </div>
      <?php $user_info = $this->session->userdata['mec_user']; ?>
      <div class="header-profile">
      	<span class="profile-icon">
        	<?php
        	if(isset($user_info['photo'])) {
				echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_info['photo'].'" alt="" />';
            } else {
				echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
            }
			?>
        </span>
        <span class="profile-text"><?php echo $user_info['name']; ?></span>
        <i class="fa fa-caret-down" aria-hidden="true"></i>
        <ul>
        	<li><a href="<?php echo site_url('user/profile'); ?>">Profile</a></li> 
          	<li><a href="<?php echo site_url('user/settings'); ?>">Settings</a></li>
          	<li><a href="<?php echo site_url('user/logout'); ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>
