<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<header>
  <div class="container">
    <div class="logo"><a href="index.html"><img src="<?php echo base_url().'assets/images/white-logo.png'; ?>" alt="" /></a></div>
    <div class="header-right">
      <div class="header-search">
        <input type="text" name="textfield" placeholder="Type in to search" id="textfield" />
        <button type="button"></button>
      </div>
      <div class="header-create-post">Create Post</div>
      <div class="header-notification"><span class="notification-count"></span><img src="<?php echo base_url().'assets/images/notification-icon.png'; ?>" alt="" /></div>
      <div class="header-flag"> <img src="<?php echo base_url().'assets/images/english-flag.png'; ?>" alt="" /> <i class="fa fa-caret-down" aria-hidden="true"></i> </div>
      <div class="header-profile"> <span class="profile-icon"><img src="<?php echo base_url().'assets/images/user-pic.png'; ?>" alt="" /></span> <span class="profile-text">Jassica  Doe</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
        <ul>
          <li><a href="#">Settings</a></li>
          <li><a href="<?php echo site_url('dashboard/logout'); ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>
