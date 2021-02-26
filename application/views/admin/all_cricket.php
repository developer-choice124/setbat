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
        <div class="col-sm-12">
            <div class="panel panel-darkblue">
                <div class="panel-heading">
                     All Matches
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
                                  <th class="">Event </th>
                                  <th class="">Date </th>
                                  <th class="">Competition </th>
                                  <th class="">Type </th>
                                  <!-- <th class="">Action </th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($crickets as $c):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$c['event_name'];?></td>
                                    <td><?=date('D d-M-Y H:i:sa',strtotime($c['start_date']));?></td>
                                    <td><?=$c['competition_name'];?></td>
                                    <td><?=$c['mtype'];?></td>
                                    <!-- <td><a href="<?=base_url('Admin/enableBetting?event_id='.$c['event_id']);?>">Enable Betting</a></td> -->
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