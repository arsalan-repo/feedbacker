<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo "<pre>";
//print_r($feedback);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion post-details-wrapper">
    <div class="container">
      <div class="post-detail-left">
        <div class="profile-listing">
          <div class="listing-post-name-block">
          	<span class="listing-post-name"><?php echo $feedback['title']; ?></span> 
            <span class="listing-post-followers"><?php echo $feedback['followers']; ?> Follower(s)</span> </div>
          <div class="profile-listing-img-thumb-block">
            <div class="profile-listing-img-thumb">
            	<?php
				if(isset($feedback['user_avatar'])) {
					echo '<img src="'.$feedback['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </div>
            <span class="listing-post-profile-name"><?php echo $feedback['name']; ?></span> <span class="listing-post-profile-time"><?php echo $feedback['time']; ?></span> </div>
          	<?php if($feedback['feedback_img'] != "") { ?>
            <div class="listing-post-img">
				<img src="<?php echo $feedback['feedback_img']; ?>" alt="" />
            </div>
			<?php } ?>
          <p><?php echo $feedback['feedback']; ?></p>
          <div class="post-listing-follow-btn">
          	<span class="back-arrow">
            	<img src="<?php echo base_url().'assets/images/reply-arrow.png'; ?>" alt="" />
            </span> 
            <?php if($feedback['is_followed'] == "") { ?>
            	<span class="follow-btn fill">Follow <i class="fa fa-plus" aria-hidden="true"></i></span>	
            <?php } else { ?>
            	<span class="follow-btn fill">Unfollow</span>
			<?php } ?>
            <?php if($feedback['is_liked'] == "") { ?>
                <span class="wishlist empty">
                    <i class="fa fa-heart-o" aria-hidden="true"></i> <?php echo $feedback['likes']; ?>
                </span>
			<?php } else { ?>
                <span class="wishlist">
                    <i class="fa fa-heart" aria-hidden="true"></i> <?php echo $feedback['likes']; ?>
                </span>
            <?php } ?>
          </div>
          <div class="post-detail-comments-block">
          	<?php if(!empty($feedback['replies'])) { ?>
            <h3>Comments</h3>
            <?php foreach($feedback['replies'] as $row) { ?>
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
              <span class="listing-post-profile-name"><?php echo $row['name']; ?></span> <span class="listing-post-profile-time"><?php echo $row['time']; ?></span> </div>
            <div class="comment-description">
              <p><?php echo $row['feedback']; ?></p>
            </div>
            <?php } ?>
            <?php } ?>
            <div class="post-detail-comment-form">
              <h2>Write a comment</h2>
              <form id="form1" name="form1" method="post" action="">
                <label>Comment</label>
                <input type="text" name="textfield1" placeholder="Write comment here" />
                <input type="text" name="textfield1" placeholder="Location" />
              </form>
              <div class="post-btn-block">
                <div class="camera-map-icon"> 
				<div class="camera-icon-block">
					<span>Choose File</span>
					<input name="Select File" type="file" />
				</div>
				<?php /*?><img src="<?php echo base_url().'assets/images/camera-icon.png'; ?>" alt="" /> <?php */?>
				
				<img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" /> </div>
                <span class="post-btn">Post</span> </div>
            </div>
          </div>
        </div>
      </div>
      <div class="post-detail-rgt">
      <?php if(count($others) > 0) { ?>
		  <?php foreach($others as $row) { ?>
            <div class="profile-listing">
              <div class="listing-post-name-block"> <span class="listing-post-name"><?php echo $row['title']; ?></span> <span class="listing-post-followers"><?php echo $row['followers']; ?> Follower(s)</span> </div>
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
                <span class="listing-post-profile-name"><?php echo $row['name']; ?></span> <span class="listing-post-profile-time"><?php echo $row['time']; ?></span> </div>
			<?php if($row['feedback_img'] != "") { ?>
              <div class="listing-post-img">
              	<img src="<?php echo $row['feedback_img']; ?>" alt="" />
              </div>
            <?php } ?>
              <p><?php echo $row['feedback']; ?></p>
              <div class="post-listing-follow-btn"> 
              	<span class="back-arrow">
                	<img src="<?php echo base_url().'assets/images/reply-arrow.png'; ?>" alt="" />
                </span> 
                <?php if($row['is_followed'] == "") { ?>
            	<span class="follow-btn fill">Follow <i class="fa fa-plus" aria-hidden="true"></i></span>	
				<?php } else { ?>
                    <span class="follow-btn fill">Unfollow</span>
                <?php } ?>
                <?php if($row['is_liked'] == "") { ?>
                    <span class="wishlist empty">
                        <i class="fa fa-heart-o" aria-hidden="true"></i> <?php echo $row['likes']; ?>
                    </span>
                <?php } else { ?>
                    <span class="wishlist">
                        <i class="fa fa-heart" aria-hidden="true"></i> <?php echo $row['likes']; ?>
                    </span>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
      <?php } ?>
      </div>
    </div>
</div>
<!-- /.content-wrapper -->
