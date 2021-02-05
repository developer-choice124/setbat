<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SetBat User Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('app_assets/images/favicon.png'); ?>">
    <!-- google font -->
    <link href="<?=base_url();?>app_assets/fonts.googleapis.com/css6079.css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
    <link href="<?=base_url();?>app_assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>app_assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>app_assets/css/ionicons.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>app_assets/css/simple-line-icons.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>app_assets/css/jquery.mCustomScrollbar.css" rel="stylesheet">
    <link href="<?=base_url();?>app_assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url();?>app_assets/css/responsive.css" rel="stylesheet">
</head>

<body class="bg_darck">

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-form">
                    <center><h4>Welcome To SetBat</h4></center>
                    <form action="<?=base_url('MsAuth/login');?>" method="POST">
                        <div id="loginMessage">
                            <?php if($this->session->flashdata('message')) {
                                echo $this->session->flashdata('message');
                            } ?>
                        </div>
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" name="identity" id="identity" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">Sign in</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?=base_url();?>app_assets/js/popper.min.js"></script>
    <script type="text/javascript" src="<?=base_url();?>app_assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script type="text/javascript" src="<?=base_url();?>app_assets/js/custom.js"></script>
</body>


</html>