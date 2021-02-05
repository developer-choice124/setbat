<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/plugins/images/favicon.png')?>">
    <title><?=$title;?></title>
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
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
                <ul class="nav navbar-top-links navbar-left hidden-xs">
                    <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>
                    <li class="dropdown">
                       
                        
                        <!-- /.dropdown-messages -->
                    </li>
                    <!-- /.dropdown -->
                    
                    <!-- /.dropdown -->
                   
                </ul>
                <ul class="nav navbar-top-links navbar-right pull-right">
                    <?php if($this->ion_auth->is_admin() || $this->ion_auth->is_superadmin()) {
                        
                    } else { ?>
                        <li><a href="javascript:void(0)">Balance: <?=$chips ? $chips->balanced_chips : 0;?></a></li>
                    <?php } ?>
                    
                     <li class="dropdown">
                        <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><span class="hidden-xs"></span> <i class="icon-options-vertical"></i></a>
                        <ul class="dropdown-menu animated bounceInDown">
                          <?php  $id = $this->session->userdata('user_id'); 
                          $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
                          ?>
                               
                                <li><a href="<?php echo base_url().($ug=='admin'?'Admin': ($ug == 'master' ? 'Master' : 'SuperMaster')).'/changePassword';?>"><i class="fa fa-lock"></i> Change Password</a></li>
                                <li><a href="<?php echo base_url('auth/logout')?>"><i class="fa fa-power-off"></i> Logout</a></li>
                           
                        </ul>
                    </li>
                    <!-- /.Megamenu -->
                    
                    <!-- /.dropdown -->
                </ul>
           <center><h3 style="color: white;"><?=$title;?></h3></center> 
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <marquee class="bg-info text-white">
            <h3 class="text-white"><?=$heading;?></h3>
        </marquee>