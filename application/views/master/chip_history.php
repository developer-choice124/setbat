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

                                <?php
                                    //print_r($history);
                                $total = 0;
                                for($i = count($history) -1; $i >= 0; $i--) {
                                    $total = $total + ($history[$i]['credits'] - $history[$i]['debits']);
                                    $history[$i]['total'] = $total;
                                }
                                 $i = 1; foreach ($history as $key => $h):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$h['bet_type']=='matched' ? $h['match_name'] : $h['team'];?>&nbsp;[ <?=$h['bet_type']=='matched' ? 'Match Odds' : 'Match Fancy';?>]&nbsp;Winner: <?=$h['winner'];?></td>
                                    <td><?=date('d-M-Y h:i:sa', strtotime($h['transaction_date']));?></td>
                                    <td><span class="text-success"><?=$h['credits']?$h['credits']:0;?></span></td>
                                    <td><span class="text-danger"><?=$h['debits']?$h['debits']:0?></span></td>
                                    <td>
                                        <?php $cl = $h['total'] >= 0 ? 'text-success' : 'text-danger';?>
                                        <span class="<?=$cl;?>"><?=$h['total'];?></span>
                                    </td>
                                    <td><?=$h['bid'];?></td>
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