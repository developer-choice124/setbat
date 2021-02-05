<script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
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
      background-color: #b5e0ff;
  }
  .lay {
      background-color: #ffbfcd;
  }
</style>
<div class="container-fluid no-gutters">
  <div class="row">
    <div class="col-md-12"> 
      <!-- Nav tabs -->
      <div class="card">
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('Winner/match?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Match Odds</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('MsAppUser/unmatchedBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Unmatched</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('MsAppUser/matchedBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Matched</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="<?=base_url('MsAppUser/fancyBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Fancies</a>
          </li>
        </ul>
        <div class="card-header bg-primary text-white">
          <i class="fa fa-star-o"></i>&nbsp; India Vs Bangladesh
        </div>
        <div class="card-header bg-white clearfix">
          <span class="text-success">Fancy Bets</span>
          <span class="float-right"><?=date('D Y-M-d H:i:s A');?></span>
        </div>
        <div class="card-body bg-white">
          <div class="student_table table-responsive-lg">
            <table id="" class="table table-bordered table-sm" cellspacing="0" width="100%">
              <tr class="headings">
                  <th class="">Runner </th>
                  <th class="">Type </th>
                  <th class="">Odds </th>
                  <th class="">Stack </th>
                  <th class="">Profit </th>
                  <th class="">Loss </th>
                  <th class="">IP </th>
                  <th class="">ID </th>
              </tr>
              <?php foreach ($fbets as $fb): ?>
                  <tr class="<?= $fb['back_lay'] == 'back' ? 'back' : 'lay'; ?>">
                      <td><?= $fb['team']; ?></td>
                      <td><?= $fb['back_lay']; ?></td>
                      <td><?= $fb['odd']; ?></td>
                      <td><?= $fb['stake']; ?></td>
                      <td><?= $fb['profit']; ?></td>
                      <td><?= $fb['loss']; ?></td>
                      <td><?= $fb['ip']; ?></td>
                      <td><?= $fb['id']; ?></td>
                  </tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var matchId = '<?=$match->event_id;?>';
  var marketId = '<?=$match->market_id;?>';
  $(document).ready(function () {
    callAsync();
  });
  function callAsync() {
    $.ajax({
        url: "<?php echo site_url('MsAppUser/unmatchReload?market_id=') ?>" + marketId + "&match_id=" + matchId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            setTimeout( callAsync, 1200);
        }
    });
  }
</script>