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
      <div class="panel panel-info">
        <div class="panel-heading">
             SuperMaster Chip Summary
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
                      <th class="text-white bg-success">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $pt = 0; foreach($plus as $p):?>
                      <?php  $pt += round($p['chips'],2);?>
                      <tr>
                        <td><?=$p['name'];?></td>
                        <td><?php if($p['username'] == 'Own' || $p['username'] == 'Parent A/C' || $p['username'] == 'Cash') {
                          echo $p['username'];
                        } else {
                          echo '<a href="'.base_url('SuperMaster/chipSummary?user_id='.$p['uid']).'">'.$p['username'].'</a>';
                        }
                        ?>
                        </td>
                        <td><?=round($p['chips'],2);?></td>
                        <td>
                          <?php if($p['username'] != 'Cash') { ?>
                            <a href="<?=base_url('SuperMaster/userBetHistory?user_id='.$p['uid']);?>" class="btn btn-primary btn-sm" style="color: white;">History</a>
                          <?php } ?>
                          <?php if($p['username'] == 'Own' || $p['username'] == 'Parent A/C'  || $p['chips'] == 0 || $p['username'] == 'Cash') { } else { ?>
                            <a href="javascript:void(0)" onclick="makeSettlement('<?=$p['uid'];?>','<?=round($p['chips'],2);?>','plus')" class="btn btn-warning btn-sm" style="color: white;" data-toggle="modal" data-target="#settlementModal">Settlement</a>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php endforeach;?>
                  </tbody>
                  <tfoot>
                    <tr class="footers">
                      <th colspan="2">Total</th>
                      <th><span class="text-success"><?=round($pt,2);?></span></th>
                      <th></th>
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
                      <th class="text-white bg-danger">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $lt = 0; foreach($minus as $m):?>
                      <?php $lt += round($m['chips'],2);?>
                      <tr>
                        <td><?=$m['name'];?></td>
                        <td><?php if($m['username'] == 'Own' || $m['username'] == 'Parent A/C' || $m['username'] == 'Cash') {
                          echo $m['username'];
                        } else {
                          echo '<a href="'.base_url('SuperMaster/chipSummary?user_id='.$m['uid']).'">'.$m['username'].'</a>';
                        }
                        ?>
                        </td>
                        <td><?=round($m['chips'],2);?></td>
                        <td>
                          <?php if($m['username'] != 'Cash') { ?>
                            <a href="<?=base_url('SuperMaster/userBetHistory?user_id='.$m['uid']);?>" class="btn btn-primary btn-sm" style="color: white;">History</a>
                          <?php } ?>
                          <?php if($m['username'] == 'Own' || $m['username'] == 'Parent A/C' || $m['chips'] == 0 || $m['username'] == 'Cash') { } else { ?>
                            <a href="javascript:void(0)" onclick="makeSettlement('<?=$m['uid'];?>','<?=round($m['chips'],2);?>','minus')" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#settlementModal" style="color: white;">Settlement</a>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php endforeach;?>
                  </tbody>
                  <tfoot>
                    <tr class="footers">
                      <th colspan="2">Total</th>
                      <th><span class="text-danger"><?=round($lt,2);?></span></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="settlementModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Settlement</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="<?=base_url('SuperMaster/chipSettlement');?>">
          <div class="row">
            <div class="form-group">
              <div class="col-md-3">
                <label for="chips">Chips</label>
                <input type="number" min="0" name="chips" id="chips" class="form-control" onchange="checkMaxChips(this.value)" onkeyup="checkMaxChips(this.value)" step="any">
                <input type="hidden" min="0" name="chipsd" id="chipsd" class="form-control">
                <input type="hidden" name="user_id" id="user_id">
                <input type="hidden" name="type" id="type">
                <input type="hidden" name="cuser_id" id="cuser_id" value="<?=$cuser;?>">
              </div>
              <div class="col-md-9">
                <label for="message">Message</label>
                <input type="text" name="message" id="message" class="form-control">
              </div>
            </div>
          </div>
          <br/>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-info">Save</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var max;
  });
  function makeSettlement(uid,chips,type) {
    max = parseFloat(chips);
    $("#chips").val(chips);
    $("#chipsd").val(chips);
    $("#user_id").val(uid);
    $("#type").val(type);
  }
  function checkMaxChips(chip) {
    chip = parseFloat(chip);
    if(chip > max) {
      alert("entered chips can not more than "+max);
      $("#chips").val(0);
    }
  }
</script>