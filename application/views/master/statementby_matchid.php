<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js') ?>"></script>
<style type="text/css">
  .headings {
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
          Account Statement
          <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
        </div>
        <div class="panel-body">
          <div><?php if ($this->session->flashdata('message')) {
                  echo $this->session->flashdata('message');
                } ?>
          </div>
          <div class="table-responsive" id="divtlimit">
            <table id="allusers" class="table table-condensed">
              <thead>
                <tr class="headings" role="row">
                  <th>S.No. </th>
                  <th>Date </th>
                  <th>Description </th>
                  <th>P_L </th>
                  <th>Commission </th>
                  <th>Action </th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1;
                $oc = 0;
                $od = 0;
                $aoc = 0;
                $afc = 0;
                $fc = 0;
                $fd = 0;
                $olinks;

                foreach ($statements as $s) :
                  if ($s['commission'] == "yes" && $s['bet_type'] == "") {
                    $aoc += ($s['credits'] - $s['debits']);
                  }
                  if ($s['commission'] == "yes" && $s['bet_type'] == "fancy") {
                    $afc += ($s['credits'] - $s['debits']);
                  }
                  if ($s['bet_type'] == 'matched') {
                    $oc += $s['credits'];
                    $od += $s['debits'];
                  } elseif ($s['bet_type'] == 'fancy') {
                    $fc += $s['credits'];
                    $fd += $s['debits'];
                  }
                endforeach;
                $opl = $oc - $od;
                $ocl = $opl >= 0 ? 'text-success' : 'text-danger';
                $fpl = $fc - $fd;
                $fcl = $fpl >= 0 ? 'text-success' : 'text-danger';
                ?>
                <tr>
                  <td>1</td>
                  <td><?= date('d-M-Y H:i:sa', strtotime($statements[0]['transaction_date'])); ?></td>
                  <td><?= $statements[0]['description'] . '&nbsp;(Match Odds)'; ?></td>
                  <td><span class="<?= $ocl ?>"><?= $opl; ?></span></td>
                  <td>
                    <?php if ($aoc >= 0) { ?>
                      <span style="color: green; font-weight: bold;"><?= $aoc; ?></span>
                    <?php } else { ?>
                      <span style="color: red; font-weight: bold;"><?= $aoc; ?></span>
                    <?php } ?>
                  </td>
                  <td><a href="<?= base_url('Master/oddFancyByMatchId?match_id=' . $statements[0]['match_id'] . '&user_id=' . $statements[0]['user_id'] . '&type=matched'); ?>">Show Bets</a></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td><?= date('d-M-Y H:i:sa', strtotime($statements[0]['transaction_date'])); ?></td>
                  <td><?= $statements[0]['description'] . '&nbsp;(Match Fancy)'; ?></td>
                  <td><span class="<?= $fcl ?>"><?= $fpl; ?></span></td>
                  <td>
                    <?php if ($afc >= 0) { ?>
                      <span style="color: green; font-weight: bold;"><?= $afc; ?></span>
                    <?php } else { ?>
                      <span style="color: red; font-weight: bold;"><?= $afc; ?></span>
                    <?php } ?>
                  </td>
                  <td><a href="<?= base_url('Master/oddFancyByMatchId?match_id=' . $statements[0]['match_id'] . '&user_id=' . $statements[0]['user_id'] . '&type=fancy'); ?>">Show Fancy</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>