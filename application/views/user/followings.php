<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

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
