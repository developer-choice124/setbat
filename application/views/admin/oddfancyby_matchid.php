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
  .footers {
    background: #ccc !important;
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
                     Account Statement
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <span style="font-size: 14px; font-weight: bold;">Plus Account</span>
                        <div class="table-responsive">
                          <table class="table table-striped table-border">
                            <thead class="bg-success">
                              <tr>
                                <th class="text-white bg-success">Name</th>
                                <th class="text-white bg-success">Account</th>
                                <th class="text-white bg-success">Chips</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $pt = 0; foreach($plus as $p):?>
                                <?php $pt += $p['chips'];?>
                                <tr>
                                  <td><?=$p['name'];?></td>
                                  <td><?php if($p['username'] == 'Own' || $p['username'] == 'Parent A/C') {
                                    echo $p['username'];
                                  } else {
                                    echo '<a href="'.base_url('Admin/oddFancyByMatchId?match_id='.$bets[0]['match_id'].'&user_id='.$p['uid'].'&type='.$type).'">'.$p['username'].'</a>';
                                  }
                                  ?>
                                  </td>
                                  <td><?=$p['chips'];?></td>
                                </tr>
                              <?php endforeach;?>
                            </tbody>
                            <tfoot>
                              <tr class="footers">
                                <th colspan="2">Total</th>
                                <th><?=$pt;?></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <span style="font-size: 14px; font-weight: bold;">Minus Account</span>
                        <div class="table-responsive">
                          <table class="table table-striped table-border">
                            <thead class="bg-danger">
                              <tr>
                                <th class="text-white bg-danger">Name</th>
                                <th class="text-white bg-danger">Account</th>
                                <th class="text-white bg-danger">Chips</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $lt = 0; foreach($minus as $m):?>
                                <?php $lt += $m['chips'];?>
                                <tr>
                                  <td><?=$m['name'];?></td>
                                  <td><?php if($m['username'] == 'Own' || $m['username'] == 'Parent A/C') {
                                    echo $m['username'];
                                  } else {
                                    echo '<a href="'.base_url('Admin/oddFancyByMatchId?match_id='.$bets[0]['match_id'].'&user_id='.$m['uid'].'&type='.$type).'">'.$m['username'].'</a>';
                                  }
                                  ?>
                                  </td>
                                  <td><?=$m['chips'];?></td>
                                </tr>
                              <?php endforeach;?>
                            </tbody>
                            <tfoot>
                              <tr class="footers">
                                <th colspan="2">Total</th>
                                <th><?=$lt;?></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                    <br/>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="table table-condensed" >
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
                              <th class="">Bet Id </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $i = 1; foreach($bets as $b):?>
                              <tr>
                                <td><?=$i++;?></td>
                                <td><?=$this->Common_model->findfield('users','id',$b['user_id'],'username');?></td>
                                <td><?=$b['match_name'];?></td>
                                <td><?=$b['team'];?></td>
                                <td><?=$b['back_lay'];?></td>
                                <td><?=$b['odd'];?></td>
                                <td><?=$b['stake'];?></td>
                                <td><?=date('Y-M-d H:i:s a',strtotime($b['created_at']));?></td>
                                <td><span class="text-success"><?=$b['profit'];?></span></td>
                                <td><span class="text-danger"><?=$b['loss'];?></span></td>
                                <td><?php if($b['status'] == 'pending'){ echo 'pending'; } else { 
                                      if($b['bet_result']=='win') {
                                        echo '<span class="text-success">0</span>';
                                      } else {
                                        echo '<span class="text-danger">'.$b['loss'].'</span>';
                                      }
                                  } ?> 
                                </td>
                                <td><?=ucwords($b['bet_type']);?></td>
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