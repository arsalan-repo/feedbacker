<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container">
      <div class="left-content">
        <div class="home-left-profile">
          <div class="home-left-section">
            <div class="home-left-profile-block"> 
            	<span class="home-left-profile-thumb">
					<?php 
                    if(isset($user_info['photo'])) {
                        echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_info['photo'].'" alt="" />';
                    } else {
                        echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
                    }
                    ?>
                </span> 
                <span class="home-left-profile-name"><?php echo $user_info['name']; ?></span> 
                <span class="home-left-profile-designation"><?php echo $this->common->getCountries($user_info['country']); ?></span> </div>
          </div>
        </div>
        <div class="home-left-text-block">
          <h2><span>Trends</span> Change</h2>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
          <h3>#TigorStyleback</h3>
          <p>It is a long established fact that a reader will be distracted by the readable content of a page when lo</p>
        </div>
      </div>
      <div class="middle-content">
        <div class="middle-content-block">
          <?php if (!empty($feedbacks)) { ?>
          <!-- Loop Starts Here -->
          <?php foreach($feedbacks as $row) { ?>
          <div class="post-profile-block"> <span class="post-right-arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
            <div class="post-img">
            	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </div>
            <div class="post-profile-content"> 
            	<span class="post-designation">
                	<a href="<?php echo site_url('post/detail').'/'.$row['id']; ?>"><?php echo $row['title']; ?></a>
                </span> 
            	<span class="post-name"><?php echo $row['name']; ?></span> 
            	<span class="post-address"><?php echo $row['location']; ?></span>
                <p><?php echo $row['feedback']; ?></p>
                <?php if (!empty($feedback_img)) { ?>
              	<div class="post-large-img"> 
                	<img src="<?php echo $feedback_img; ?>" alt="" />
              	</div>
                <?php } ?>
              	<div class="post-follow-block"> 
                	<span class="post-follow-back-arrow">
                    	<img src="<?php echo ASSETS_URL.'images/reply-arrow.png'; ?>" alt="" />
                    </span>
                    <span class="post-follow-text">
                    	<?php if ($row['is_followed']) { ?>
                        	Unfollow
                        <?php } else { ?>    
                            Follow <i class="fa fa-plus" aria-hidden="true"></i>
                        <?php } ?>
                    </span>
                    <span class="post-wishlist">
                    	<i class="fa fa-heart" aria-hidden="true" <?php $row['is_liked'] ?  'style="color: #f32836;"' : '' ?>></i> 
						<?php echo $row['likes']; ?>
                    </span>
                </div>
            </div>
          </div>
          <?php } ?>
          <!-- Loop Ends Here -->
          <div id="pagination">
            <ul class="tsc_pagination">
            	<!-- Show pagination links -->
            	<?php 
				foreach ($links as $link) {
            		echo "<li>". $link."</li>";
            	} 
				?>
            </ul>
          </div>
          <?php } else { ?>
          <?php echo $no_record_found; ?>
          <?php } ?>
        </div>
      </div>
      <div class="right-content">
        <h3>What to Follow <a href="#">View All</a></h3>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
        <div class="who-follow-block"> <span><img src="<?php echo ASSETS_URL.'images/follow-person-thumb.png'; ?>" alt="" /></span>
          <div class="who-follow-text"> <span>Sachin Tendulkar</span> @AmitabhBa...</div>
          <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i> </div>
        </div>
      </div>
    </div>
</div>
<!-- /.content-wrapper -->
