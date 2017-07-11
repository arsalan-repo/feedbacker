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
          <h2><span>Trends</span><!-- Change--></h2>
          <?php foreach($trends as $row) {
              echo '<h3><a href="'.site_url('post/title').'/'.$row['title_id'].'">'.$row['title'].'</a></h3>';
              echo '<p>'.$this->common->limitText($row['feedback_cont'], 20).'</p>';
          } ?>
        </div>
      </div>
      <div class="middle-content">
        <div class="middle-content-block" id="post-data">
          <?php 
		  if (!empty($feedbacks)) {
		  	$this->load->view('user/feedbacks', $feedbacks);
          } else {
          	echo $no_record_found;
          } ?>
        </div>
      </div>
      <div class="right-content">
        <h3>What to Follow <!--<a href="#">View All</a>--></h3>
		<?php if (!empty($to_follow)) { ?>
			<?php foreach($to_follow as $row) { ?>
			<div class="who-follow-block">
				<span>
					<?php
					if (isset($row['user_avatar'])) {
						echo '<img src="'.$row['user_avatar'].'" alt="" />';
					} else {
						echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
					}
					?>
				</span>
				<div class="who-follow-text">
					<span><?php echo $row['title']; ?></span> <?php echo $row['name']; ?>
				</div>
				<div class="who-follow-add" id="follow-btn-<?php echo $row['feedback_id']; ?>"> Follow <i class="fa fa-plus" aria-hidden="true"></i></div>
				<input type="hidden" id="title_id" value="<?php echo $row['title_id']; ?>" />
				<input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
			</div>
			<?php } ?>
		<?php } else { ?>
			<div class="no-record">No records found</div>
		<?php } ?>
      </div>
    </div>
    <div class="ajax-load text-center" style="display:none">
        <p><img src="<?php echo ASSETS_URL . 'images/loader.gif'; ?>">Loading</p>
    </div>
    
    <script type="text/javascript">
        var page = 1;
        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                loadMoreData(page);
            }
        });
    
        function loadMoreData(page){
          $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    beforeSend: function()
                    {
                        $('.ajax-load').show();
                    }
                })
                .done(function(data)
                {
                    if(data == " "){
                        $('.ajax-load').html("No more records found");
                        return;
                    }
                    $('.ajax-load').hide();
                    $("#post-data").append(data);
                })
                .fail(function(jqXHR, ajaxOptions, thrownError)
                {
                      alert('server not responding...');
                });
        }
		
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
    </script>
</div>
<!-- /.content-wrapper -->
