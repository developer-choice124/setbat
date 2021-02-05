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
  .back {
    background-color: #b5e0ff !important;
  }
  .lay {
    background-color: #ffbfcd !important;
  }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     BET History
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="display nowrap  table-border" cellspacing="0" width="100%">
                            <thead>
                                <tr class="headings">
                                  <th class="">S.No. </th>
                                  <th>User</th>
                                  <th class="">Description </th>
                                  <th class="">Selection </th>
                                  <th class="">Type </th>
                                  <th class="">Odds </th>
                                  <th class="">Stack </th>
                                  <th class="">Date </th>
                                  <th class="">Profit </th>
                                  <th class="">Loss </th>
                                  <th class="">Liability </th>
                                  <th class="">Bet type</th>
                                  <th class="">Status </th>
                                  <th class="">IP </th>
                                  <th class="">ID </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($bets as $b):?>
                                  <tr class="<?=$b['back_lay'] == 'back' ? 'back' : 'lay' ;?>">
                                    <td><?=$i++;?></td>
                                    <td><?=$b['username'];?></td>
                                    <td><?=$b['match_name'];?></td>
                                    <td><?=$b['team'];?></td>
                                    <td><?=$b['back_lay'];?></td>
                                    <td><?=$b['odd'];?></td>
                                    <td><?=$b['stake'];?></td>
                                    <td><?=date('Y-M-d H:i:s a',strtotime($b['created_at']));?></td>
                                    <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-success">'.$b['profit'].'</span>';?></td>
                                    <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$b['loss'].'</span>';?></td>
                                    <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$b['loss'].'</span>';?></td>
                                    <td><?=$b['bet_type'];?></td>
                                    <td><?=$b['status'];?></td>
                                    <td><?=$b['ip'];?></td>
                                    <td><?=$b['id'];?></td>
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