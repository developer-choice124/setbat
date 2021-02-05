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
                     Profit & Loss
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
                                <th class="">S.No. </th>
                                <th class="">Event Name </th>
                                <th class="">Market </th>
                                <th class="">P_L </th>
                                <th class="">Commission </th>
                                <th class="">Created On </th>
                                <th class="">Action </th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($profitLosses as $p):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$p['match_name'];?></td>
                                    <td><?=$p['market'];?></td>
                                    <td><?php if($p['profit'] > 0) echo '<span class="text-success">'.$p['profit'].'</span>'; else echo '<span class="text-danger">'.$p['loss'].'</span>';?></td>
                                    <td><span class="text-success"><?=$p['user_commission'];?></span></td>
                                    <td><?=date('Y-M-d H:i:sa',strtotime($p['created_at']));?></td>
                                    <td><a href="<?=base_url('Admin/bet?bet_id='.$p['bet_id']);?>">show bet</a></td>
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