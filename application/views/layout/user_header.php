<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/plugins/images/favicon.png')?>">
    <title>User Panel | Setbat</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url('assets/backend/bootstrap/dist/css/bootstrap.min.css')?>" rel="stylesheet">
    <!-- summernotes CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/summernote/dist/summernote.css');?>" rel="stylesheet" />
    <!-- Select2 CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/custom-select/custom-select.css')?>" rel="stylesheet" type="text/css" />
    <!-- Datatables CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/datatables/jquery.dataTables.min.css')?>" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- Menu CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css')?>" rel="stylesheet">
    <!-- toast CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/toast-master/css/jquery.toast.css')?>" rel="stylesheet">
    <!-- morris CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/morrisjs/morris.css')?>" rel="stylesheet">
    <!-- animation CSS -->
    <link href="<?php echo base_url('assets/backend/css/animate.css')?>" rel="stylesheet">
    <!--alerts CSS -->
    <link href="<?php echo base_url('assets/plugins/bower_components/sweetalert/sweetalert.css')?>" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/backend/css/style.css')?>" rel="stylesheet">
    <!-- color CSS -->
    <link href="<?php echo base_url('assets/backend/css/colors/default.css')?>" id="theme" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/bower_components/dropify/dist/css/dropify.min.css')?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-datepicker/css/datepicker3.css');?> "/>

   <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-datepicker/css/datepicker.css');?> "/>
   <link rel="stylesheet" href="<?php echo base_url('assets/plugins/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css');?>"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
      <script>
    
    var base_url="<?php echo base_url();?>";

    </script>
</head>

<body>
    <?php  $id = $this->session->userdata('user_id'); ?>
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <a href="<?=base_url('uploads/setbat_v1.apk');?>" class="btn bt-danger text-white"><h3 class="text-white"><i class="fa fa-android"></i>&nbsp;Download App</h3></a>
                <ul class="nav navbar-top-links navbar-right pull-right">
                    
                    <!-- .Megamenu -->
                    <li id="mainBalance"><a href="javascript:void(0)">Balance: <?=$chips ? $chips->balanced_chips : 0;?></a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><span class="hidden-xs"></span><?=ucwords($this->Common_model->findfield('users','id',$id,'full_name'));?> <i class="icon-options-vertical"></i></a>
                        <ul class="dropdown-menu animated bounceInDown">
                          
                               
                                <li><a href="javascript:void(0)" data-target="#stakeModal" data-toggle="modal"> Edit Stake</a></li>
                                <li><a href="<?=base_url('User/accountInfo');?>"> Account Info</a></li>
                                <li><a href="<?=base_url('User/accountStatement');?>"> Account Statement</a></li>
                                <li><a href="<?=base_url('User/chipHistory');?>"> Chip History</a></li>
                                <li><a href="<?=base_url('User/profitLoss');?>"> Profit & Loss</a></li>
                                <li><a href="<?=base_url('User/betHistory');?>"> Bet History</a></li>
                                <li><a href="<?=base_url('User/changePassword');?>"> Change Password</a></li>
                                <li><a href="<?=base_url('Auth/logout');?>"> Logout</a></li>
                           
                        </ul>
                    </li>
                    <!-- /.Megamenu -->
                    
                    <!-- /.dropdown -->
                </ul>
           <center><h3 style="color: white;"><?php echo "User Panel | Setbat";?></h3></center> 
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <marquee class="bg-info text-white">
            <h3 class="text-white"><?=$heading?></h3>
        </marquee>

<!-- Modal -->
<div id="stakeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Stake</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="<?=base_url('User/editStake');?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_1">Chip Name 1 :</label>
                        <input type="text" name="chip_name_1" id="chip_name_1" class="form-control" value="<?php if($chipSetting->chip_name_1) echo $chipSetting->chip_name_1;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_1">Chip Value 1 :</label>
                        <input type="text" name="chip_value_1" id="chip_value_1" class="form-control" value="<?php if($chipSetting->chip_value_1) echo $chipSetting->chip_value_1;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_2">Chip Name 2 :</label>
                        <input type="text" name="chip_name_2" id="chip_name_2" class="form-control" value="<?php if($chipSetting->chip_name_2) echo $chipSetting->chip_name_2;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_2">Chip Value 2 :</label>
                        <input type="text" name="chip_value_2" id="chip_value_2" class="form-control" value="<?php if($chipSetting->chip_value_2) echo $chipSetting->chip_value_2;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_3">Chip Name 3 :</label>
                        <input type="text" name="chip_name_3" id="chip_name_3" class="form-control" value="<?php if($chipSetting->chip_name_3) echo $chipSetting->chip_name_3;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_3">Chip Value 3 :</label>
                        <input type="text" name="chip_value_3" id="chip_value_3" class="form-control" value="<?php if($chipSetting->chip_value_3) echo $chipSetting->chip_value_3;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_4">Chip Name 4 :</label>
                        <input type="text" name="chip_name_4" id="chip_name_4" class="form-control" value="<?php if($chipSetting->chip_name_4) echo $chipSetting->chip_name_4;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_4">Chip Value 4 :</label>
                        <input type="text" name="chip_value_4" id="chip_value_4" class="form-control" value="<?php if($chipSetting->chip_value_4) echo $chipSetting->chip_value_4;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_5">Chip Name 5 :</label>
                        <input type="text" name="chip_name_5" id="chip_name_5" class="form-control" value="<?php if($chipSetting->chip_name_5) echo $chipSetting->chip_name_5;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_5">Chip Value 5 :</label>
                        <input type="text" name="chip_value_5" id="chip_value_5" class="form-control" value="<?php if($chipSetting->chip_value_5) echo $chipSetting->chip_value_5;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_name_6">Chip Name 6 :</label>
                        <input type="text" name="chip_name_6" id="chip_name_6" class="form-control" value="<?php if($chipSetting->chip_name_6) echo $chipSetting->chip_name_6;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chip_value_6">Chip Value 6 :</label>
                        <input type="text" name="chip_value_6" id="chip_value_6" class="form-control" value="<?php if($chipSetting->chip_value_6) echo $chipSetting->chip_value_6;?>">
                    </div>
                </div>
            </div>
            <center><button type="submit" class="btn btn-primary">Update Chip Setting</button></center>
        </form>
      </div>
    </div>

  </div>
</div>