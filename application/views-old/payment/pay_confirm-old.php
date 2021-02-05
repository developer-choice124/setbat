<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$title;?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=base_url();?>front_assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="<?=base_url();?>front_assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?=base_url();?>front_assets/vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template -->
    <link href="<?=base_url();?>front_assets/css/landing-page.min.css" rel="stylesheet">

  </head>

  <body>

    <!-- Navigation -->
    <nav class="navbar navbar-light bg-light static-top">
      <div class="container">
        <a class="navbar-brand" href="#">Astrology Tv</a>
      </div>
    </nav>

    <section>
      <div class="container">
        <center><h1>Purchase Confirmation Page</h1></center>
        <div class="row">
          <div class="col-2">
            
          </div>
          <div class="col-8">
            <form action="<?= $payu['action']; ?>/_payment" method="post" id="payuForm" name="payuForm">
                    <input type="hidden" name="key" value="<?= $payu['mkey'] ?>" />
                    <input type="hidden" name="hash" value="<?= $payu['hash'] ?>"/>
                    <input type="hidden" name="txnid" value="<?= $payu['tid'] ?>" />
                    <div class="form-group">
                        <label class="control-label">Total Payable Amount(in Rs.)</label>
                        <input class="form-control" name="amount" value="<?= $payu['amount'] ?>"  readonly/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Your Name</label>
                        <input class="form-control" name="firstname" id="firstname" value="<?= $payu['name'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input class="form-control" name="email" id="email" value="<?= $payu['mailid'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Phone</label>
                        <input class="form-control" name="phone" value="<?= $payu['phoneno'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label class="control-label"> Booking Info</label>
                        <textarea class="form-control" name="productinfo" readonly><?= $payu['pinfo'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <input name="surl" value="<?= $payu['sucess'] ?>" size="64" type="hidden" />
                        <input name="furl" value="<?= $payu['failure'] ?>" size="64" type="hidden" />                             
                        <input type="hidden" name="service_provider" value="" size="64" /> 
                        <input name="curl" value="<?= $payu['cancel'] ?> " type="hidden" />
                    </div>
                    <div class="form-group text-center">
                      <input type="submit" value="Pay Now" class="btn btn-danger rounded-0" />
                    </div>
                </form> 
          </div>
          <div class="col-2">
            
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-light">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 h-100 text-center text-lg-left my-auto">
            <ul class="list-inline mb-2">
              <li class="list-inline-item">
                <a href="#">About</a>
              </li>
              <li class="list-inline-item">&sdot;</li>
              <li class="list-inline-item">
                <a href="#">Contact</a>
              </li>
              <li class="list-inline-item">&sdot;</li>
              <li class="list-inline-item">
                <a href="#">Terms of Use</a>
              </li>
              <li class="list-inline-item">&sdot;</li>
              <li class="list-inline-item">
                <a href="#">Privacy Policy</a>
              </li>
            </ul>
            <p class="text-muted small mb-4 mb-lg-0">&copy; Astrology Tv App 2018. All Rights Reserved.</p>
          </div>
          <div class="col-lg-6 h-100 text-center text-lg-right my-auto">
            <ul class="list-inline mb-0">
              <li class="list-inline-item mr-3">
                <a href="#">
                  <i class="fab fa-facebook fa-2x fa-fw"></i>
                </a>
              </li>
              <li class="list-inline-item mr-3">
                <a href="#">
                  <i class="fab fa-twitter-square fa-2x fa-fw"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fab fa-instagram fa-2x fa-fw"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="<?=base_url();?>front_assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?=base_url();?>front_assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>

</html>