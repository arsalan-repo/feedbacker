<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container">
      <div class="search-result-page"> <img src="<?php echo base_url().'assets/images/serch-result-img.png'; ?>" alt="" />
        <h3><?php echo $this->lang->line('no_results'); ?></h3>
        <p>“boeing” not found <br />
          <?php echo $this->lang->line('tap_to_create'); ?></p>
        <span class="normal-btn"><?php echo $this->lang->line('create'); ?></span> </div>
    </div>
</div>
<!-- /.content-wrapper -->
