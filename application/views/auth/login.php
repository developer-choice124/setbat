
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="title" content="Admin Login | SetBat">
<meta name="description" content="">
<meta name="author" content="">
<title>GREYEXCH</title>
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/plugins/images/favicon.png')?>">

<!-- Bootstrap Core CSS -->
<link href="<?php echo base_url('assets/backend/bootstrap/dist/css/bootstrap.min.css')?>" rel="stylesheet">
<!-- animation CSS -->
<link href="<?php echo base_url('assets/backend/css/animate.css')?>" rel="stylesheet">
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/backend/css/style.css')?>" rel="stylesheet">
<!-- color CSS -->
<link href="<?php echo base_url('assets/backend/css/colors/default.css')?>" id="theme"  rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

</head>
<body>
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
  <div class="login-box">
    <div class="black-box">
      <form class="form-horizontal form-material" id="loginform" action="<?php base_url('auth/login')?>" method="POST">
        <center><h3 class="box-title m-b-20" style="font-size: 22px;"> <?php echo "GREYEXCH";?></h3></center>
        <!--h3 class="box-title m-b-20"> <?php echo lang('login_heading');?></h3-->
        <h4><div id="infoMessage"><?php echo $message;?></div></h4>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="required" placeholder="Username" name="identity">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="required" placeholder="<?php echo lang('password');?>" name="password">
          </div>
        </div>
        <!--div class="form-group">
          <div class="col-md-12">
            <div class="checkbox checkbox-primary pull-left p-t-0">
              <input id="checkbox-signup" type="checkbox" name="password" value="1">
              <label for="checkbox-signup">  <?php echo lang('login_remember_label');?> </label>
            </div>
            <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> <?php echo lang('login_forgot_password');?></a> </div>
        </div-->
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-darkblue btn-lg btn-block text-uppercase text-white waves-effect waves-light" type="submit"><?php echo lang('login_heading');?> <i class="fa fa-sign-in"></i></button>
          </div>
        </div>
        <!--div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
            <div class="social"><a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip"  title="Login with Facebook"> <i aria-hidden="true" class="fa fa-facebook"></i> </a> <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip"  title="Login with Google"> <i aria-hidden="true" class="fa fa-google-plus"></i> </a> </div>
          </div>
        </div-->
        <!--div class="form-group m-b-0">
          <div class="col-sm-12 text-center">
            <p> <?php echo lang('index_create_user_link');?> <a href="register.html" class="text-primary m-l-5"><b>Sign Up</b></a></p>
          </div>
        </div-->
      </form>
      <form class="form-horizontal" id="recoverform" action="<?php base_url('auth/forgot_password')?>" method="POST">
        <div class="form-group ">
          <div class="col-xs-12">
            <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
          </div>
        </div>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="Email">
          </div>
        </div>
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
<!-- jQuery -->
<script src="<?php echo base_url('assets/plugins/bower_components/jquery/dist/jquery.min.js')?>"></script>
<!-- Bootstrap Core JavaScript -->
<script src="<?php echo base_url('assets/backend/bootstrap/dist/js/bootstrap.min.js')?>"></script>
<!-- Menu Plugin JavaScript -->
<script src="<?php echo base_url('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js')?>"></script>

<!--slimscroll JavaScript -->
<script src="<?php echo base_url('assets/backend/js/jquery.slimscroll.js')?>"></script>
<!--Wave Effects -->
<script src="<?php echo base_url('assets/backend/js/waves.js')?>"></script>
<!-- Custom Theme JavaScript -->
<script src="<?php echo base_url('assets/backend/js/custom.min.js')?>"></script>
<!--Style Switcher -->
<script src="<?php echo base_url('assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js')?>"></script>
</body>
</html>