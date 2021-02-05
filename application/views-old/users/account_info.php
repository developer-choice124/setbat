<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<style type="text/css">
  .headings{
    background: #2c5ca9 !important;
    color: #fff;
  }
  .headings th {
    color: #fff;
    font-weight: normal !important;
  }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-2">
            <ul class="list-group">
              <li class="list-group-item"><a href="#"><i class="fa fa-heart"></i> &nbsp;Favorite</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/inPlay');?>"><i class="fa fa-play"></i> &nbsp;In Play</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-bar-chart"></i> &nbsp;My Market</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/matches');?>"><i class="fa fa-child"></i> &nbsp;Cricket</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-futbol-o"></i> &nbsp;Soccer</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-globe"> </i> &nbsp;Tennis</a></li>
            </ul>
        </div>
        <div class="col-sm-10">
            <div class="panel panel-info">
                <div class="panel-heading">
                     Account Info
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="table table-condensed" >
                            <thead>
                                <tr class="headings">
                                    <th>S. No</th>
                                    <th>Chips</th>
                                    <th>Free Chips</th>
                                    <th>Liability</th>
                                    <th>Wallet</th>
                                    <th>Up</th>
                                    <th>Down</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td>1</td>
                                  <td><?=$info->balanced_chips;?></td>
                                  <td><?=$info->free_chips;?></td>
                                  <td><?php if($info->balanced_chips < $info->spent_chips) echo ($info->spent_chips - $info->balanced_chips); else echo 0.00;?></td>
                                  <td><?=$info->balanced_chips;?></td>
                                  <td><?=$up->up;?></td>
                                  <td><?=$down->down;?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getVideos(cid) {
       $.ajax({
            url : "<?php echo site_url('Admin/getVideos')?>/"+cid,
            type: "POST",
            //dataType: "JSON",
            success: function(data)
            {
               $("#videos").html(data);
               $('#vidtable').DataTable( {
                    responsive: true
                });
            },
              error: function (jqXHR, textStatus, errorThrown)
         {
            alert("error");
         }
        });
    }
</script>