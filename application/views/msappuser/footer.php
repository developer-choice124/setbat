</div>
<!-- Content_right_End -->
<!-- Footer -->
<footer class="footer ptb-20">
    <div class="row">
        <div class="col-12 text-center">
            <div class="copy_right">
                <p>
                    2020 © SetBat | Powered By
                    <a href="#">BetFair</a>
                </p>
            </div>

        </div>
    </div>
</footer>
<!-- Footer_End -->
</div>
<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/popper.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.2.5/jquery.bootstrap-touchspin.min.js"></script>
<script src="<?= base_url(); ?>app_assets/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>app_assets/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="<?= base_url(); ?>app_assets/js/custom.js" type="text/javascript"></script>
<?php if ($cuser->first_login == "yes") { ?>
    <script>
        $(document).ready(function() {
            $('#passwordModal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });
    </script>
<?php } ?>
<script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
    $("input[name='backStakeValue']").TouchSpin({
        min: 0,
        max: 1000000,
        step: 0.1,
        decimals: 2,
        boostat: 5,
        maxboostedstep: 10
    });
    $("input[name='layStakeValue']").TouchSpin({
        min: 0,
        max: 1000000,
        step: 0.1,
        decimals: 2,
        boostat: 10,
        maxboostedstep: 10
    });
</script>

</body>


</html>