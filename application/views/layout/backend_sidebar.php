<?php $id = $this->session->userdata('user_id'); ?>
<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <div class="user-profile">
            <div class="dropdown user-pro-body">
                <div><img src="<?= base_url('assets/plugins/images/white.svg') ?>" height="48" alt="betcric"></div>
                <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ucwords(strtolower($this->Common_model->findfield('users', 'id', $id, 'full_name'))); ?></a>

            </div>
        </div>
        <?php if($this->ion_auth->is_superadmin()) { ?>
            <ul class="nav" id="side-menu">
                <li><a href="<?= base_url('SuperAdmin/index') ?>" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li><a href="<?= base_url('SuperAdmin/series') ?>" class="waves-effect"><i class="fa fa-list" data-icon="F"></i> <span class="hide-menu">Series</span></a></li>
                <li>
                    <a href="#" class="waves-effect"><img src="<?=base_url('assets/uploads/images/cricket.png')?>" height="16px"> <span class="hide-menu">Cricket<span class="fa arrow"></span><span class="label label-rouded label-danger pull-right"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="<?= base_url('SuperAdmin/allCricket') ?>">All Matches</a></li>
                        <li><a href="<?= base_url('SuperAdmin/runningCricket') ?>">Running Match</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="waves-effect"><i class="fa fa-users"></i> <span class="hide-menu">All Users<span class="fa arrow"></span><span class="label label-rouded label-danger pull-right"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="<?= base_url('SuperAdmin/admins') ?>">Admins</a></li>
                        <li><a href="<?= base_url('SuperAdmin/supermasters') ?>">SuperMasters</a></li>
                        <li><a href="<?= base_url('SuperAdmin/masters') ?>">Masters</a></li>
                        <li><a href="<?= base_url('SuperAdmin/users') ?>">Users</a></li>
                        <li><a href="<?= base_url('SuperAdmin/deletedUsers') ?>">Deleted Users</a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url('SuperAdmin/accountInfo') ?>" class="waves-effect"><i class="fa fa-info-circle" data-icon="F"></i> <span class="hide-menu">Account Info</span></a></li>
                <li><a href="<?= base_url('SuperAdmin/accountStatement') ?>" class="waves-effect"><i class="fa fa-list-alt" data-icon="F"></i> <span class="hide-menu">Account Statement</span></a></li>
                <li><a href="<?= base_url('SuperAdmin/chipSummary?user_id='.$this->session->userdata('user_id')); ?>" class="waves-effect"><i class="fa fa-newspaper-o" data-icon="F"></i> <span class="hide-menu">Chip Summary</span></a></li>
                <!-- <li><a href="<?php //echo base_url('SuperAdmin/chipHistory') ?>" class="waves-effect"><i class="fa fa-history" data-icon="F"></i> <span class="hide-menu">Chip History</span></a></li> -->
                <li><a href="<?= base_url('SuperAdmin/profitLoss') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Profit & Loss</span></a></li>
                <li><a href="<?= base_url('SuperAdmin/betHistory') ?>" class="waves-effect"><i class="fa fa-list" data-icon="F"></i> <span class="hide-menu">Bet History</span></a></li>
                <li><a href="<?= base_url('SuperAdmin/panelTitle') ?>" class="waves-effect"><i class="fa fa-tags" data-icon="F"></i> <span class="hide-menu">Panel Heading</span></a></li>
            </ul>
        <?php } else if($this->ion_auth->is_admin()) { ?>
            <ul class="nav" id="side-menu">
                <li><a href="<?= base_url('Admin/index') ?>" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li>
                    <a href="#" class="waves-effect"><img src="<?=base_url('assets/uploads/images/cricket.png')?>" height="16px"> <span class="hide-menu">Cricket<span class="fa arrow"></span><span class="label label-rouded label-danger pull-right"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="<?= base_url('Admin/allCricket') ?>">All Matches</a></li>
                        <li><a href="<?= base_url('Admin/runningCricket') ?>">Running Match</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="waves-effect"><i class="fa fa-users"></i> <span class="hide-menu">All Users<span class="fa arrow"></span><span class="label label-rouded label-danger pull-right"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="<?= base_url('Admin/supermasters') ?>">SuperMasters</a></li>
                        <li><a href="<?= base_url('Admin/masters') ?>">Masters</a></li>
                        <li><a href="<?= base_url('Admin/users') ?>">Users</a></li>
                        <li><a href="<?= base_url('Admin/deletedUsers') ?>">Deleted Users</a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url('Admin/accountInfo') ?>" class="waves-effect"><i class="fa fa-info-circle" data-icon="F"></i> <span class="hide-menu">Account Info</span></a></li>
                <li><a href="<?= base_url('Admin/accountStatement') ?>" class="waves-effect"><i class="fa fa-list-alt" data-icon="F"></i> <span class="hide-menu">Account Statement</span></a></li>
                <li><a href="<?= base_url('Admin/chipSummary?user_id='.$this->session->userdata('user_id')); ?>" class="waves-effect"><i class="fa fa-newspaper-o" data-icon="F"></i> <span class="hide-menu">Chip Summary</span></a></li>
                <!-- <li><a href="<?php //echo base_url('Admin/chipHistory') ?>" class="waves-effect"><i class="fa fa-history" data-icon="F"></i> <span class="hide-menu">Chip History</span></a></li> -->
                <li><a href="<?= base_url('Admin/profitLoss') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Profit & Loss</span></a></li>
                <li><a href="<?= base_url('Admin/betHistory') ?>" class="waves-effect"><i class="fa fa-list" data-icon="F"></i> <span class="hide-menu">Bet History</span></a></li>
                <li><a href="<?= base_url('Admin/panelTitle') ?>" class="waves-effect"><i class="fa fa-tags" data-icon="F"></i> <span class="hide-menu">Panel Heading</span></a></li>
            </ul>
        <?php } else if($this->ion_auth->is_supermaster()) { ?>
            <ul class="nav" id="side-menu">
                <li><a href="<?= base_url('SuperMaster/index') ?>" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li>
                    <a href="#" class="waves-effect"><i class="fa fa-users"></i> <span class="hide-menu">All Users<span class="fa arrow"></span><span class="label label-rouded label-danger pull-right"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="<?= base_url('SuperMaster/masters') ?>">Masters</a></li>
                        <li><a href="<?= base_url('SuperMaster/users') ?>">Users</a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url('SuperMaster/runningCricket') ?>" class="waves-effect"><img src="<?=base_url('assets/uploads/images/cricket.png')?>" height="16px"> <span class="hide-menu">Cricket</span></a></li>
                <li><a href="<?= base_url('SuperMaster/accountInfo') ?>" class="waves-effect"><i class="fa fa-info-circle" data-icon="F"></i> <span class="hide-menu">Account Info</span></a></li>
                <li><a href="<?= base_url('SuperMaster/accountStatement') ?>" class="waves-effect"><i class="fa fa-list-alt" data-icon="F"></i> <span class="hide-menu">Account Statement</span></a></li>
                <li><a href="<?= base_url('SuperMaster/chipHistory') ?>" class="waves-effect"><i class="fa fa-history" data-icon="F"></i> <span class="hide-menu">Chip History</span></a></li>
                <li><a href="<?= base_url('SuperMaster/chipSummary?user_id='.$this->session->userdata('user_id')); ?>" class="waves-effect"><i class="fa fa-newspaper-o" data-icon="F"></i> <span class="hide-menu">Chip Summary</span></a></li>
                <li><a href="<?= base_url('SuperMaster/profitLoss') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Profit & Loss</span></a></li>
                <li><a href="<?= base_url('SuperMaster/betHistory') ?>" class="waves-effect"><i class="fa fa-list" data-icon="F"></i> <span class="hide-menu">Bet History</span></a></li>
            </ul>
        <?php } else if($this->ion_auth->is_master()) { ?>
            <ul class="nav" id="side-menu">
                <li><a href="<?= base_url('Master/index') ?>" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li><a href="<?= base_url('Master/allusers') ?>" class="waves-effect"><i class="fa fa-users" data-icon="F"></i> <span class="hide-menu">All Users</span></a></li>
                <li><a href="<?= base_url('Master/runningCricket') ?>" class="waves-effect"><img src="<?=base_url('assets/uploads/images/cricket.png')?>" height="16px"> <span class="hide-menu">Cricket</span></a></li>
                <li><a href="<?= base_url('Master/accountInfo') ?>" class="waves-effect"><i class="fa fa-info-circle" data-icon="F"></i> <span class="hide-menu">Account Info</span></a></li>
                <li><a href="<?= base_url('Master/accountStatement') ?>" class="waves-effect"><i class="fa fa-list-alt" data-icon="F"></i> <span class="hide-menu">Account Statement</span></a></li>
                <li><a href="<?= base_url('Master/chipHistory') ?>" class="waves-effect"><i class="fa fa-history" data-icon="F"></i> <span class="hide-menu">Chip History</span></a></li>
                <li><a href="<?= base_url('Master/chipSummary?user_id='.$this->session->userdata('user_id')); ?>" class="waves-effect"><i class="fa fa-newspaper-o" data-icon="F"></i> <span class="hide-menu">Chip Summary</span></a></li>
                <li><a href="<?= base_url('Master/profitLoss') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Profit & Loss</span></a></li>
                <li><a href="<?= base_url('Master/betHistory') ?>" class="waves-effect"><i class="fa fa-list" data-icon="F"></i> <span class="hide-menu">Bet History</span></a></li>
            </ul>
        <?php } else if($this->ion_auth->is_user()) { ?>
            <ul class="nav" id="side-menu">
                <li><a href="<?= base_url('Master/index') ?>" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li><a href="<?= base_url('Master/allusers') ?>" class="waves-effect"><i class="fa fa-users" data-icon="F"></i> <span class="hide-menu">All Users</span></a></li>
                <li><a href="<?= base_url('Master/runningCricket') ?>" class="waves-effect"><img src="<?=base_url('assets/uploads/images/cricket.png')?>" height="16px"> <span class="hide-menu">Cricket</span></a></li>
                <li><a href="<?= base_url('Master/accountInfo') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Account Info</span></a></li>
                <li><a href="<?= base_url('Master/accountStatement') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Account Statement</span></a></li>
                <li><a href="<?= base_url('Master/chipHistory') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Chip History</span></a></li>
                <li><a href="<?= base_url('Master/chipSummary?user_id='.$this->session->userdata('user_id')); ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Chip Summary</span></a></li>
                <li><a href="<?= base_url('Master/profitLoss') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Profit & Loss</span></a></li>
                <li><a href="<?= base_url('Master/betHistory') ?>" class="waves-effect"><i class="fa fa-money" data-icon="F"></i> <span class="hide-menu">Bet History</span></a></li>
            </ul>
        <?php } else { ?>
            
        <?php } ?>
    </div>
</div>
<!-- Left navbar-header end -->
<!-- Page Content -->
<div id="page-wrapper">