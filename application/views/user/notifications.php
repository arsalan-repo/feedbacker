<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container creatae-post-content notification-page">
     <!-- <h2>Notifications</h2>-->
		  <div class="notification-block">
			 <div class="post-img">
					<?php
					if(isset($row['user_avatar'])) {
						echo '<img src="'.$row['user_avatar'].'" alt="" />';
					} else {
						echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
					}
					?>
				</div>
				<div class="notification-description"> 
					<span class="notification-title">Steve wrote about "feel burger"</span> 
					<span class="notification-sub-title">Nice location</span> 
					<span class="notification-time">a month</span> 
				</div>
			</div>
			
			
    </div>
</div>
<!-- /.content-wrapper -->
