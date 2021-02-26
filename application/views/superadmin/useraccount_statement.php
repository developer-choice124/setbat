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
                     Account Statement
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
                                <tr class="headings" role="row">
                                  <th>S.No. </th>
                                  <th>Date </th>
                                  <th>Description </th>
                                  <th>Credit </th>
                                  <th>Debit </th>
                                  <th>Balance </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i =1; foreach($statements as $s):?>
                                  <?php if($s['type']== 'bet') {
                                    $link = '<a href="'.base_url('SuperAdmin/statementByMatchId?match_id='.$s['match_id'].'&user_id='.$s['user_id']).'">'.$s['description'].'</a>';
                                    
                                  } else {
                                    $link = $s['description'];
                                  }
                                  ?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=date('d-M-Y H:i:sa',strtotime($s['transaction_date']));?></td>
                                    <td><?=$link;?></td>
                                    <td><span style="color: green; font-weight: bold;"><?=$s['credits'];?></span></td>
                                    <td><span style="color: red; font-weight: bold;"><?=$s['debits'];?></span></td>
                                    <td><b><?=$s['balance'];?></b></td>
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