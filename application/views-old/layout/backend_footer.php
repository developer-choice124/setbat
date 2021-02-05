            <footer class="footer text-center"> 2019 &copy; Designed & Developed by <a href="http://setbat.com" target="_blank">Setbat</a> </footer>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
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
    <!--Counter js -->
    <script src="<?php echo base_url('assets/plugins/bower_components/waypoints/lib/jquery.waypoints.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/counterup/jquery.counterup.min.js')?>"></script>
    <!--Morris JavaScript -->
    <script src="<?php echo base_url('assets/plugins/bower_components/raphael/raphael-min.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/morrisjs/morris.js')?>"></script>
    <!-- Sweet-Alert  -->
    <script src="<?php echo base_url('assets/plugins/bower_components/sweetalert/sweetalert.min.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js')?>"></script>
    <!-- Custom Theme JavaScript -->
    <script src="<?php echo base_url('assets/backend/js/custom.min.js')?>"></script>
    <script src="<?php echo base_url('assets/backend/js/dashboard1.js')?>"></script>
    <!-- Sparkline chart JavaScript -->
    <script src="<?php echo base_url('assets/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/jquery-sparkline/jquery.charts-sparkline.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/toast-master/js/jquery.toast.js')?>"></script>
    <!-- Datatable JS -->
    <script src="<?php echo base_url('assets/plugins/bower_components/datatables/jquery.dataTables.min.js')?>"></script>
    
     <!-- Custom Theme JavaScript -->
    <script src="<?php echo base_url('assets/backend/js/cbpFWTabs.js')?>"></script>
    <script src="<?php echo base_url('assets/backend/js/jasny-bootstrap.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/custom-select/custom-select.min.js')?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/summernote/dist/summernote.min.js');?>"></script>
    <script src="<?php echo base_url('assets/plugins/bower_components/dropify/dist/js/dropify.min.js')?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

         <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.js');?>"></script>
    <script type="text/javascript">
    (function() {

        [].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
            new CBPFWTabs(el);
        });

    })();
    </script>
    <script>
    $('#allusers').DataTable({});
    $('#admins').DataTable({});
    $('#supermasters').DataTable({});
    $('#masters').DataTable({});
    $('#users').DataTable({});
    </script>
    <script>
        // For select 2

           $(document).ready(function() {
    $('.select2').select2();
});
            $('.selectpicker').selectpicker();
    </script>
    <!--Style Switcher -->
    <script src="<?php echo base_url('assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js')?>"></script>
    <script src="<?php echo base_url('assets/backend/js/basic.js')?>"></script>
</body>
</html>