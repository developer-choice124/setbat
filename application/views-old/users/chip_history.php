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
                     Chip History
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
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Balance</th>
                                    <th>Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($history as $key => $h):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$h['match_name'].'&nbsp;['.$h['market'].']&nbsp;Winner: '.$h['winner_team'];?></td>
                                    <td><?=date('d-M-Y h:i:sa', strtotime($h['created_at']));?></td>
                                    <td><span class="text-success"><?=$h['profit']?$h['profit']:0;?></span></td>
                                    <td><span class="text-danger"><?=$h['loss']?$h['loss']:0?></span></td>
                                    <td></td>
                                    <td><?=ucwords($h['id']);?></td>
                                  </tr>
                                <?php endforeach;?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>