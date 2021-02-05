            <footer class="footer text-center"> 2020 &copy; Powered by <a href="#" target="_blank">Betfair</a> </footer>
            <!-- Modal -->
            <div id="blockMatchModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Block Match</h4>
                </div>
                <div class="modal-body">
                        <form action="<?=base_url('Blockedevent/blocked_event');?>" method="POST">
                            <div class="row">
                                <input type="hidden" name="user_id" id="bloeckedUserevent">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <section>
                                        <label for="blockMatch">Select Match</label>
                                        <select name="block_event_id[]" class="form-control" id="blockMatch" multiple>
                                            <?php
                                                if(sizeof($matches) > 0){
                                                    foreach($matches as $matche): ?>
                                                        <option value="<?= $matche['event_id']; ?>"><?= $matche['event_name']; ?></option>
                                            <?php   endforeach; 
                                                } ?>
                                        </select>
                                        </section>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-darkblue">Submit</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        <h4 class="modal-title">Blocked Match List</h4>
                        <div>
                            <ul class="list-group" id="matchesList">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <!-- Modal -->
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
    
    <script type="text/javascript" src="<?= base_url('assets/backend/bootstrap/dist/js/bootstrap-multiselect.min.js'); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#blockMatch').multiselect();
        });
        function blockMatches(id, buttonid)
        {
            $('#blockMatch').multiselect("deselectAll", false).multiselect("refresh");
            var dataEvnt = $("#"+buttonid).attr("data-user-event");
            var datauserevent = JSON.parse(dataEvnt);
            var selectoption = '';
            if(datauserevent.length > 0){

                var event = JSON.parse(datauserevent[0].event_id);
                event.forEach((list) => {
    
                    $('#blockMatch').multiselect('select',list);
                });
            }
            
            $.ajax({
            url : "<?= site_url('Blockedevent/getblocked_event?user_id=')?>"+id,
            type: "POST",
            success: function(data)
            {
                $("#bloeckedUserevent").val(id); 

                $("#blockMatchModal").modal('show');

                //make blocked event list
                let blokedEventlist = JSON.parse(data);
                var html = '';
                if(blokedEventlist.length > 0){
                    
                    blokedEventlist = blokedEventlist[0];
                    let details = JSON.parse(blokedEventlist.details);
                    details.forEach((list) => {
                        html += `<li class="list-group-item">${list.event_name}</li>`;
                    });
                }

                $("#matchesList").empty();
                $("#matchesList").html(html);
            },
                error: function (jqXHR, textStatus, errorThrown)
            {
            alert("error");
            }
        });

        }
    </script>
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