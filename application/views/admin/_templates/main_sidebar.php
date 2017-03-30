<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                 <?php /*?><img src="<?php echo base_url($frameworks_dir . '/adminlte/img/logo.jpg')?>" class="" alt="MEC"> <?php */?>
            </div>
        </div>

        <ul class="sidebar-menu">
            <!--<li class="header">MAIN NAVIGATION</li>-->

            <!-- Start Dashboard -->
            <li <?php if ($this->uri->segment(2) == 'dashboard' || $this->uri->segment(1) == '') { ?> class="active treeview" <?php } else { ?> class="treeview"   <?php } ?> >
                <a href="<?php echo base_url('admin/dashboard'); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span> 
                </a>
            </li>
            <!-- End Dashboard -->
            <!--Start Users-->
            <li <?php if ($this->uri->segment(2) == 'users') { ?> class="active treeview" <?php } else { ?> class="treeview"   <?php } ?>>
                <a href="<?php echo base_url('admin/users'); ?>">    
                    <i class="fa fa-users"></i><span>Users</span>
                </a>
            </li>
            <!--End Users-->
            <!--Start Feedbacks-->
            <li <?php if ($this->uri->segment(2) == 'feedbacks') { ?> class="active treeview" <?php } else { ?> class="treeview"   <?php } ?>>
                <a href="<?php echo base_url('admin/feedbacks'); ?>">    
                    <i class="fa fa-commenting"></i><span>Feedbacks</span>
                </a>
            </li>
            <!--End Feedbacks-->
            <!--Start Settings -->
            <li <?php if ($this->uri->segment(2) == 'settings') { ?> class="active treeview" <?php } else { ?> class="treeview"   <?php } ?>>
                <a href="<?php echo base_url('admin/settings'); ?>">    
                    <i class="fa fa-cog"></i><span>Settings</span>
                </a>
            </li>
            <!--End Setting-->
            <!--Start Change Password-->
            <li <?php if ($this->uri->segment(2) == 'change_password') { ?> class="active treeview" <?php } else { ?> class="treeview"   <?php } ?> >
               <a href="<?php echo base_url('admin/dashboard/change_password'); ?>">
                   <i class="fa fa-lock"></i> <span>Change Password</span>
               </a>
            </li>
            <!--End Change Password-->

            <!--End of my code-->

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>