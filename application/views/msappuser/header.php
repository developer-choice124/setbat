<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grey Exchange</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('app_assets/images/favicon.png'); ?>">
    <!-- google font -->
    <link href="<?= base_url(); ?>app_assets/fonts.googleapis.com/css6079.css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>app_assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url(); ?>app_assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url(); ?>app_assets/css/ionicons.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url(); ?>app_assets/css/simple-line-icons.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url(); ?>app_assets/css/jquery.mCustomScrollbar.css" rel="stylesheet">
    <link href="<?= base_url(); ?>app_assets/css/weather-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.2.5/jquery.bootstrap-touchspin.min.css">
    <!--bs4 data table-->
    <link href="<?= base_url(); ?>app_assets/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <link href="<?= base_url(); ?>app_assets/css/style.css" rel="stylesheet">
    <link href="<?= base_url(); ?>app_assets/css/responsive.css" rel="stylesheet">
</head>

<body>
    <div id="loader_wrpper">
        <div class="loader_style"></div>
    </div>

    <div class="wrapper">
        <!-- header -->
        <header class="main-header">
            <div class="container_header">
                <div class="logo d-flex align-items-center">
                    <div class="icon_menu full_menu">
                        <a href="#" class="menu-toggler sidebar-toggler"></a>
                    </div>
					<span style="margin-left: 50px;color: #2c5ca9 !important;">GREYEXCH</span>
                </div>
                <div class="right_detail">
                    <div class="row d-flex align-items-center min-h pos-md-r">
                        <div class="col-3 search_col ">

                        </div>
                        <div class=" col-9 d-flex justify-content-end">
                            <div class="right_bar_top d-flex align-items-center">
                                <b id="mainBalance"><span class="text-primary">Balance: &nbsp;</span><span id="finalBal"><?= $cuser->balanced_chips; ?></span></b>&nbsp;
                                <!-- Dropdown_User -->
                                <div class="dropdown dropdown-inbox">
                                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true"> <i class="fa fa-user"></i> </a>
                                    <ul class="dropdown-menu dropdown-menu-default">
                                        <li><a href="<?= base_url('uploads/setbat_v1.apk'); ?>"> <i class="fa fa-android"></i> Download Apk</a></li>
                                        <li><a href="javascript:void(0)" data-target="#stakeModal" data-toggle="modal"> <i class="fa fa-money"></i> Edit Stake</a></li>
                                        <li><a href="<?= base_url('MsAppUser/accountInfo'); ?>"> <i class="fa fa-info-circle"></i> Account Info</a></li>
                                        <li><a href="<?= base_url('MsAppUser/accountStatement'); ?>"> <i class="fa fa-file-text"></i> Account Statement</a></li>
                                        <li><a href="<?= base_url('MsAppUser/chipHistory'); ?>"> <i class="fa fa-history"></i> Chip History</a></li>
                                        <li><a href="<?= base_url('MsAppUser/profitLoss'); ?>"> <i class="fa fa-usd"></i> Profit & Loss</a></li>
                                        <li><a href="<?= base_url('MsAppUser/betHistory'); ?>"> <i class="fa fa-server"></i> Bet History</a></li>
                                        <li><a href="<?= base_url('MsAppUser/changePassword'); ?>"> <i class="fa fa-key"></i> Change Password</a></li>
                                        <li><a href="<?= base_url('MsAuth/logout'); ?>"> <i class="fa fa-sign-out"></i> Logout</a></li>
                                    </ul>
                                </div>
                                <!-- Dropdown_User_End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </header>
        <!-- header_End -->
        <!-- Content_right -->
        <div class="container_full">
            <!-- Modal -->
            <div id="stakeModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel5">Edit Stake</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="<?= base_url('MsAppUser/editStake'); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_1">Chip Name 1 :</label>
                                            <input type="text" name="chip_name_1" id="chip_name_1" class="form-control" value="<?php if ($cuser->chip_name_1) echo $cuser->chip_name_1; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_1">Chip Value 1 :</label>
                                            <input type="text" name="chip_value_1" id="chip_value_1" class="form-control" value="<?php if ($cuser->chip_value_1) echo $cuser->chip_value_1; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_2">Chip Name 2 :</label>
                                            <input type="text" name="chip_name_2" id="chip_name_2" class="form-control" value="<?php if ($cuser->chip_name_2) echo $cuser->chip_name_2; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_2">Chip Value 2 :</label>
                                            <input type="text" name="chip_value_2" id="chip_value_2" class="form-control" value="<?php if ($cuser->chip_value_2) echo $cuser->chip_value_2; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_3">Chip Name 3 :</label>
                                            <input type="text" name="chip_name_3" id="chip_name_3" class="form-control" value="<?php if ($cuser->chip_name_3) echo $cuser->chip_name_3; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_3">Chip Value 3 :</label>
                                            <input type="text" name="chip_value_3" id="chip_value_3" class="form-control" value="<?php if ($cuser->chip_value_3) echo $cuser->chip_value_3; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_4">Chip Name 4 :</label>
                                            <input type="text" name="chip_name_4" id="chip_name_4" class="form-control" value="<?php if ($cuser->chip_name_4) echo $cuser->chip_name_4; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_4">Chip Value 4 :</label>
                                            <input type="text" name="chip_value_4" id="chip_value_4" class="form-control" value="<?php if ($cuser->chip_value_4) echo $cuser->chip_value_4; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_5">Chip Name 5 :</label>
                                            <input type="text" name="chip_name_5" id="chip_name_5" class="form-control" value="<?php if ($cuser->chip_name_5) echo $cuser->chip_name_5; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_5">Chip Value 5 :</label>
                                            <input type="text" name="chip_value_5" id="chip_value_5" class="form-control" value="<?php if ($cuser->chip_value_5) echo $cuser->chip_value_5; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_name_6">Chip Name 6 :</label>
                                            <input type="text" name="chip_name_6" id="chip_name_6" class="form-control" value="<?php if ($cuser->chip_name_6) echo $cuser->chip_name_6; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="chip_value_6">Chip Value 6 :</label>
                                            <input type="text" name="chip_value_6" id="chip_value_6" class="form-control" value="<?php if ($cuser->chip_value_6) echo $cuser->chip_value_6; ?>">
                                        </div>
                                    </div>
                                </div>
                                <center><button type="submit" class="btn btn-primary">Update Chip Setting</button></center>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- change password modal -->
            <div id="passwordModal" class="modal show" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Password</h5>
                        </div>
                        <div class="modal-body">
                            <p>Please chnage your password before continue.</p>
                            <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
                            <form method="post" action="<?= base_url('MsAppUser/updateModalPassword?user_id=' . $this->session->userdata('user_id')); ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="old">Old Password</label>
                                            <input type="password" name="old" id="old" class="form-control" placeholder="Please enter your old password">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="new">New Password</label>
                                            <input type="text" name="new" id="new" class="form-control" placeholder="Please enter your new password">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="new">Confirm New Password</label>
                                            <input type="text" name="new_confirm" id="new-confirm" class="form-control" placeholder="Please confirm your new password">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>