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
                <span class="home-left-profile-designation">
				<?php
				$getcountry = $this->common->select_data_by_id('users', 'id', $user_info['id'], 'country', '');
				echo $this->common->getCountries($getcountry[0]['country']); ?></span> </div>
          </div>
        </div>
        <div class="home-left-text-block">
          <h2><span><?php echo $this->lang->line('trends'); ?></span><!-- Change--></h2>
          <?php foreach($trends as $row) {
              echo '<h3><a href="'.site_url('post/title').'/'.$row['title_id'].'">'.$row['title'].'</a></h3>';
              echo '<p>'.$this->common->limitText($row['feedback_cont'], 20).'</p>';
          } ?>
        </div>
      </div>
      <div class="middle-content">
        <div class="middle-content-block">
          <?php if (!empty($results)) { ?>
          <!-- Loop Starts Here -->
          <?php foreach($results as $row) { ?>
          <div class="post-profile-block">
		   <div class="post-right-arrow">
		   <!--<i class="fa fa-angle-down" aria-hidden="true"></i>-->
		   <span class="post-followers-text">
		   <?php 
			if($user_info['language'] == 'ar') {
				echo $this->lang->line('followers')." ".$row['followers'];
			} else {
				echo $row['followers']." ".$this->lang->line('followers');
			} ?>
		   </span>
		   <span class="post-profile-time-text"><?php echo $row['time']; ?></span>
		   </div>
            <div class="post-img">
            <a href="<?php echo site_url('post/detail').'/'.$row['id']; ?>">
            	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </a>    
            </div>
            <div class="post-profile-content"> 
            	<span class="post-designation">
                	<a href="<?php echo site_url('post/title').'/'.$row['title_id']; ?>"><?php echo $row['title']; ?></a>
                </span> 
            	<span class="post-name"><?php echo $row['name']; ?></span> 
            	<span class="post-address"><?php echo $row['location']; ?></span>
                <p><?php echo $row['feedback']; ?></p>
                <?php if (!empty($feedback_img)) { ?>
              	<div class="post-large-img"> 
                	<img src="<?php echo $feedback_img; ?>" alt="" />
              	</div>
                <?php } ?>
              	<?php if(!isset($row['ads'])) { ?>
				<div class="post-follow-block"> 
					<span class="post-follow-back-arrow">
						<img src="<?php echo ASSETS_URL.'images/reply-arrow.png'; ?>" alt="" title="<?php echo $this->lang->line('reply'); ?>" />
					</span>
					<span class="follow-btn-default follow-btn-<?php echo $row['title_id']; ?>" id="follow-btn-<?php echo $row['id']; ?>">
						<?php if ($row['is_followed']) { ?>
							<?php echo $this->lang->line('unfollow'); ?>
						<?php } else { ?>    
							<?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i>
						<?php } ?>
					</span>
					<span class="post-wishlist" id="post-wishlist-<?php echo $row['id']; ?>">
						<?php if ($row['is_liked']) { ?>
							<i class="fa fa-heart" aria-hidden="true" title="<?php echo $this->lang->line('unlike'); ?>"></i> 
						<?php } else { ?>
							<i class="fa fa-heart-o" aria-hidden="true" title="<?php echo $this->lang->line('like'); ?>"></i>
						<?php } ?> 
						<?php echo $row['likes']; ?>
					</span>
					<input type="hidden" id="feedback_id" value="<?php echo $row['id']; ?>" />
					<input type="hidden" id="totl_likes" value="<?php echo $row['likes']; ?>" />			
					<input type="hidden" id="title_id" value="<?php echo $row['title_id']; ?>" />
					<input type="hidden" id="user_id" value="<?php echo $user_info['id']; ?>" />
				</div>
				<?php } ?>
				<div class="post-detail-comment-form">
				  <h2><?php echo $this->lang->line('write_comment'); ?></h2>
				  <form id="form-reply-post" name="form-reply-post" method="post" action="">
					<label><?php echo $this->lang->line('comment'); ?></label>
					<input type="text" name="feedback_cont" id="feedback_cont" placeholder="<?php echo $this->lang->line('comment_here'); ?>" />
					<input type="text" name="location" id="location" placeholder="<?php echo $this->lang->line('location'); ?>" />
				  </form>
				  <div class="post-btn-block">
					<div class="camera-map-icon"> 
					<div class="camera-icon-block">
						<span>Choose File</span>
						<input name="Select File" type="file" />
					</div>            
					<img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" /> </div>
					<span class="post-btn"><?php echo $this->lang->line('post'); ?></span> </div>
				</div>
            </div>
          </div>
          <?php } ?>
          <!-- Loop Ends Here -->
          <?php } else { ?>
          	<div class="search-result-page"> <img src="<?php echo base_url().'assets/images/serch-result-img.png'; ?>" alt="" />
				<h3><?php echo $this->lang->line('no_results'); ?></h3>
				<p>“<?php echo $qs; ?>” not found! <br />
				<?php echo $this->lang->line('tap_to_create'); ?></p>
				<span class="normal-btn"><?php echo $this->lang->line('create'); ?></span> 
			</div>
          <?php } ?>
        </div>
      </div>
      <div class="right-content">
        <h3><?php echo $this->lang->line('what_tofollow'); ?> <!--<a href="#">View All</a>--></h3>
        <?php foreach($to_follow as $row) { ?>
        <div class="who-follow-block">
        	<span>
            	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </span>
            <div class="who-follow-text">
            	<span><?php echo $row['title']; ?></span> <?php echo $row['name']; ?>
            </div>
            <div class="who-follow-add" id="who-follow-<?php echo $row['feedback_id']; ?>"> <?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i></div>
			<input type="hidden" id="title_id" value="<?php echo $row['title_id']; ?>" />
			<input type="hidden" id="user_id" value="<?php echo $user_info['id']; ?>" />
        </div>
        <?php } ?>
      </div>
    </div>
</div>
<!-- /.content-wrapper -->
<script type="application/javascript">
	$('.who-follow-add').off("click").on("click",function(e){
		e.preventDefault();
		
		var element = $(this).attr('id');
		var title_id = $(this).parent().find('#title_id').val();
		var user_id = $(this).parent().find('#user_id').val();		
	
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: '<?php echo site_url('title/follow'); ?>',
			data:{title_id:title_id, user_id:user_id}
		}).done(function(data){
			// console.log(data);
			if (data.is_followed == 1) {
				$('#'+element).parent().remove();
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			}
		});
	});
		
	$('.follow-btn-default').off("click").on("click",function(e){
		e.preventDefault();
		
		var title_id = $(this).parent().find('#title_id').val();
		var user_id = $(this).parent().find('#user_id').val();		
	
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: '<?php echo site_url('title/follow'); ?>',
			data:{title_id:title_id, user_id:user_id}
		}).done(function(data){
			// console.log(data);
			if (data.is_followed == 1) {
				$('.follow-btn-'+title_id).each(function() {
					$(this).html('Unfollow');
				});
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			}
			else
			{
				$('.follow-btn-'+title_id).each(function() {
					$(this).html('Follow <i class="fa fa-plus" aria-hidden="true"></i>');
				});
				toastr.warning(data.message, 'Success Alert', {timeOut: 5000});
			}
		});
	});
	
	$('.post-wishlist').off("click").on("click",function(e){
		e.preventDefault();
		
		var element = $(this).attr('id');
		var totl_likes = $(this).parent().find('#totl_likes').val();
		var feedback_id = $(this).parent().find('#feedback_id').val();
		var user_id = $(this).parent().find('#user_id').val();		
	
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: '<?php echo site_url('post/like'); ?>',
			data:{feedback_id:feedback_id, user_id:user_id, totl_likes:totl_likes}
		}).done(function(data){
			// console.log(data);
			if (data.is_liked == 1) {
				var totl = parseInt(data.likes) + 1;	
				$('#'+element).parent().find('#totl_likes').val(totl);
							
				$('#'+element).html('<i class="fa fa-heart" aria-hidden="true"></i><span class="total-likes"> '+totl+'</span>');
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			}
			else
			{
				var totl = parseInt(data.likes) - 1;
				$('#'+element).parent().find('#totl_likes').val(totl);
				
				$('#'+element).html('<i class="fa fa-heart-o" aria-hidden="true"></i><span class="total-likes"> '+totl+'</span>');
				toastr.warning(data.message, 'Success Alert', {timeOut: 5000});
			}
		});
	});
</script>
