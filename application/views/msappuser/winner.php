<link href="<?php echo base_url('assets/plugins/bower_components/sweetalert/sweetalert.css')?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
<script src="<?php echo base_url('assets/plugins/bower_components/sweetalert/sweetalert.min.js')?>"></script>
<script src="<?php echo base_url('assets/plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js')?>"></script>
<style type="text/css">
  .headings{
    background: #2c5ca9 !important;
    color: #fff;
  }
  .headings th {
    color: #fff;
    font-weight: normal !important;
  }
  .loader {
      display: none;
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(255,255,255,0.8) url(<?= base_url('assets/plugins/images/loader.gif'); ?>) top center no-repeat;
      z-index: 1000;
  }
  .overlay {
    position: absolute;
    display: block;
    background-color: rgba(255, 229, 0, 0.26);
    color: #ca0505;
    z-index: 2;
    vertical-align: middle;
    height: 100%;
    width: 90%;
    text-align: left;
  }
</style>
<div class="container-fluid no-gutters in-play-ss">
  <div class="row">
    <div class="col-md-12"> 
      <!-- Nav tabs -->
      <div class="card">
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" href="<?=base_url('Winner/match?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Match Odds</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('MsAppUser/unmatchedBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Unmatched</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('MsAppUser/matchedBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Matched</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('MsAppUser/fancyBets?market_id='.$match->market_id.'&match_id='.$match->event_id);?>">Fancies</a>
          </li>
        </ul>
        <div class="card-header bg-primary text-white">
          &nbsp; <?=$match->event_name;?>
          <span class="float-right"><?=date('d M-y H:i A', strtotime($match->event_date));?></span>
        </div>
        <!-- Tab panes -->
        <div class="card-body" style="padding: 0 !important;">
          <div id="scoreReload"></div>
          <div id="betMessage"></div>
          <div class="row">
            <div class="col-6 border">
              
            </div>
            <div class="col-3 text-white text-center font-weight-bold border lagai-ss">
              <center>BACK<br />(Lagai)</center>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border khai-ss">
              <center>LAY<br />(Khai)</center>
            </div>
          </div>
          <div id="matchOdd">
          <?php 
          
            $runners = $odds[0]['teams'];
            //echo json_encode($runners);die;
            $teams = json_decode($match->teams);
            $teamIds = array();
            foreach ($teams as $tm) {
              $tm = (object) $tm;
              $teamIds[] = $tm->SelectionId;
            }
            $bprice = 0; $bsize = 0; $lprice = 0; $lsize = 0;
            foreach ($odds as $rk => $r):
              $r = (object) $r;
            $bprice = $r->BackPrice1 ? $r->BackPrice1 : 0;
            $bsize = $r->BackSize1 ? $r->BackSize1 : 0;
            $lprice = $r->LayPrice1 ? $r->LayPrice1: 0;
            $lsize = $r->LaySize1 ? $r->LaySize1 : 0;

          ?>
            <div class="row">
              <div class="col-6 border">
                <span class="font-weight-bold pl-1 clearfix align-ss"><?=$r->RunnerName;?></span>
                <span id="<?=$r->SelectionId;?>" 
                  
                  class="pl-1 font-weight-bold  align-ss"></span>
              </div>
              <div class="col-3 text-center border" id='<?="$r->SelectionId'_backParentdiv'";?>' style="background: #ffffea;">
                <div 
                data-others = "<?php echo json_encode($teamIds); ?>" id='<?="$r->SelectionId'_backdiv'";?>' onclick="showBackBetDiv('<?=$r->SelectionId;?>','<?=$r->RunnerName;?>','<?=$rk;?>','back','matched','<?=$bprice;?>','<?=$bsize;?>')">
                  <span id='<?="$r->SelectionId'_backodd'";?>'>
                    <center><b><?=$bprice;?></b><br/><?=$bsize;?></center>
                  </span>
                </div>
              </div>
              <div class="col-3 text-center border" id='<?="$r->SelectionId'_layParentdiv'";?>' style="background: #ffffea;">
                <div data-others = "<?php echo json_encode($teamIds); ?>" id='<?="$r->SelectionId'_laydiv'";?>' onclick="showLayBetDiv('<?=$r->SelectionId;?>','<?=$r->RunnerName;?>','<?=$rk;?>','lay','matched','<?=$lprice;?>','<?=$lsize;?>')">
                  <span id='<?="$r->SelectionId'_layodd'";?>'>
                    <center><b><?=$lprice;?></b><br/><?=$lsize;?></center>
                  </span>
                </div>
              </div>
            </div>  
          <?php endforeach;?>
          </div>
        </div>
      </div>
      <div class="card border">
        <div class="loader"></div>
        <!-- Back Selection block Start here -->
        <div class="card-body pt-1 d-none" id="backBetDiv" style="background: #b6e8ff;">
          <div class="row">
            <div class="col-6">
              Back (Bet For)<br/>
              <span class="font-weight-bold" id="backBetFor">Bangladesh</span>
            </div>
            <div class="col-3">
              Profit<br/>
              <span class="font-weight-bold text-success" id="backBetProfit">235</span>
            </div>
            <div class="col-3">
              Loss<br/>
              <span class="font-weight-bold text-danger" id="backBetLoss">500</span>
            </div>
          </div>
          <div class="row pb-2">
            <div class="col-6 text-center">
              <b>Odd</b>
              <input type="number" name="backOddValue" id="backOddValue" class="form-control" value="0" readonly="readonly">
            </div>
            <div class="col-6 text-center">
              <b>Stake</b>
              <input type="number" name="backStakeValue" id="backStakeValue" class="form-control" value="0" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="calculateProfitLossBack(this.value)" onkeyup="calculateProfitLossBack(this.value)" onkeydown="calculateProfitLossBack(this.value)">
            </div>
          </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_1);?>)"><?=round($cuser->chip_value_1);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_2);?>)"><?=round($cuser->chip_value_2);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_3);?>)"><?=round($cuser->chip_value_3);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_4);?>)"><?=round($cuser->chip_value_4);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_5);?>)"><?=round($cuser->chip_value_5);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(<?=round($cuser->chip_value_6);?>)"><?=round($cuser->chip_value_6);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossBack(0)">Clear</button>
          <div class="clearfix pb-2"></div>
          <button type="button" id="placeBackBetButton" class="btn btn-success btn-sm" onclick="checkLimitBeforBetPlace()">
            Place Bet
          </button>
          <button type="button" id="clearBackBetButton" class="btn btn-danger btn-sm" onclick="clearAllSelection()">
            Clear All Selection
          </button>
        </div>
        <!-- Back Selection block End here -->
        <!-- Lay Selection block Start here -->
        <div class="card-body pt-1 d-none" id="layBetDiv" style="background: #ffbacc;">
          <div class="row">
            <div class="col-6">
              Lay (Bet For)<br/>
              <span class="font-weight-bold" id="layBetFor">Bangladesh</span>
            </div>
            <div class="col-3">
              Profit<br/>
              <span class="font-weight-bold text-success" id="layBetProfit">235</span>
            </div>
            <div class="col-3">
              Loss<br/>
              <span class="font-weight-bold text-danger" id="layBetLoss">500</span>
            </div>
          </div>
          <div class="row pb-2">
            <div class="col-6 text-center">
              <b>Odd</b>
              <input type="number" name="layOddValue" id="layOddValue" class="form-control" value="0" readonly>
            </div>
            <div class="col-6 text-center">
              <b>Stake</b>
              <input type="number" name="layStakeValue" id="layStakeValue" class="form-control" value="0" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="calculateProfitLossLay(this.value)" onkeyup="calculateProfitLossLay(this.value)" onkeydown="calculateProfitLossLay(this.value)">
            </div>
          </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_1);?>)"><?=round($cuser->chip_value_1);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_2);?>)"><?=round($cuser->chip_value_2);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_3);?>)"><?=round($cuser->chip_value_3);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_4);?>)"><?=round($cuser->chip_value_4);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_5);?>)"><?=round($cuser->chip_value_5);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(<?=round($cuser->chip_value_6);?>)"><?=round($cuser->chip_value_6);?></button>
            <button type="button" class="btn btn-primary btn-sm" onclick="calculateProfitLossLay(0)">Clear</button>
          <div class="clearfix pb-2"></div>
          <button type="button" id="placeLayBetButton" class="btn btn-success btn-sm" onclick="checkLimitBeforBetPlace()">
            Place Bet
          </button>
          <button type="button" id="clearLayBetButton" class="btn btn-danger btn-sm" onclick="clearAllSelection()">
            Clear All Selection
          </button>
        </div>
        <!-- Lay Selection block End here -->
      </div>
      <!-- Match Fancy  -->
      <div class="card border">
        <div class="card-body" style="padding: 0 !important;margin-top: 8px;">
          <div class="row">
            <div class="col-6 border heading-ss">
              <span class="text-danger font-weight-bold">Fancy(Session)</span>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border khai-ss heading-ss">
              <center>NO</center>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border lagai-ss heading-ss">
              <center>YES</center>
            </div>
          </div>
          <div id="matchFancy" style="max-height: 400px;overflow-y: scroll;">
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  var odd = 0;
  var line = 0;
  var teamId = '<?=$teams[0]->id;?>';
  var teamName = '<?=$teams[0]->name;?>';
  var profit = 0;
  var loss = 0;
  var amount = 0;
  var matchId = '<?=$match->event_id;?>';
  var marketId = '<?=$match->market_id;?>';
  var matchName = '<?=$match->event_name;?>';
  var market = '<?= $match->mtype; ?>';
  var backLay = 'back';
  var betType = 'matched';
  var step = 0;
  $(document).ready(function () {
    callAsync();
    callFancy();
    scoreReload();
  });
  function callAsync() {
    $.ajax({
        url: "<?php echo site_url('Winner/callAsync?market_id=') ?>" + marketId + "&match_id=" + matchId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            $("#matchOdd").html(data.oddData);
            calculateProfitLoss();
            setTimeout( callAsync, 1200);
        }
    });
  }

  function scoreReload() {
    $.ajax({
        url: "<?php echo site_url('Winner/scoreReload?market_id=') ?>" + marketId + "&match_id=" + matchId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            $("#scoreReload").html(data.score);
            setTimeout( scoreReload, 5000);
        }
    });
  }

  function callFancy() {
    $.ajax({
        url: "<?php echo site_url('Winner/callFancy?market_id=') ?>" + marketId + "&match_id=" + matchId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            $("#matchFancy").html(data.fancyData);
            setTimeout( callFancy, 700);
        }
    });
  }

  function calculateProfitLoss() {
    $.ajax({
        url: "<?php echo site_url('Winner/calculateTeamPLByMarketId?market_id=') ?>" + marketId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            //console.log(data);
            var plData = data.plData;
            for (var i = 0; i < plData.length; i++) {
              if(backLay == 'back') {
                if(i == step) {
                  plData[i].pl = parseInt(plData[i].pl) + parseInt(profit);
                } else {
                  plData[i].pl = parseInt(plData[i].pl) - parseInt(loss);
                }
              } else {
                if(i == step) {
                  plData[i].pl = parseInt(plData[i].pl) - parseInt(loss);
                } else {
                  plData[i].pl = parseInt(plData[i].pl) + parseInt(profit);
                }
              }
            }
            for (var j = 0; j < plData.length; j++) {
              plData[j].pl >= 0 ? $("#"+plData[j].id).addClass("text-success") : $("#"+plData[j].id).addClass("text-danger");
              plData[j].pl >= 0 ? $("#"+plData[j].id).removeClass("text-danger") : $("#"+plData[j].id).removeClass("text-success");
              $("#"+plData[j].id).text(Math.abs(plData[j].pl));
            }
        }
    });
  }

  function clearAllSelection() {
    $("#layBetDiv").addClass("d-none");
    $("#backBetDiv").addClass("d-none");
    calculateProfitLossBack(0);
    $("#backStakeValue").val(0);
    calculateProfitLossLay(0);
    $("#layStakeValue").val(0);
  }

  function showBackBetDiv(tId,tName,position,backOrLay,bet_type,bodd,bline) {
    $("#backBetDiv").removeClass("d-none");
    $("#layBetDiv").addClass("d-none");
    var ele = $("#"+tId + "_backdiv");
    var allids = $(ele).attr("data-others");
    if(allids) {
      var arr = JSON.parse(allids);
      for(i = 0; i < arr.length; i++) {
        if(arr[i] == tId) {
          arr.splice(i,1);
        }
      }
      team1 = tId;
      team2 = arr[0];
      team3 = arr[1];
    }
    
    $("#backOddValue").val(bodd);
    $("#backBetFor").text(tName);
    teamId = tId;
    teamName = tName;
    step = position;
    odd = bodd;
    line = bline;
    backLay = backOrLay;
    betType = bet_type;
    amount = $("#backStakeValue").val();
    calculateProfitLossBack(amount);
    // setTimeout(clearAllSelection, 4000);
  }
  function showLayBetDiv(tId,tName,position,backOrLay,bet_type,lodd,lline) {
    $("#layBetDiv").removeClass("d-none");
    $("#backBetDiv").addClass("d-none");
    var ele = $("#"+tId + "_laydiv");
    var allids = $(ele).attr("data-others");
    if(allids) {
      var arr = JSON.parse(allids);
      for(i = 0; i < arr.length; i++) {
        if(arr[i] == tId) {
          arr.splice(i,1);
        }
      }
      team1 = tId;
      team2 = arr[0];
      team3 = arr[1];
    }
    
    $("#layOddValue").val(lodd);
    $("#layBetFor").text(tName);
    teamId = tId;
    teamName = tName;
    step = position;
    odd = lodd;
    line = lline;
    backLay = backOrLay;
    betType = bet_type;
    amount = $("#layStakeValue").val();
    calculateProfitLossLay(amount);
    // setTimeout(clearAllSelection, 4000);
  }

  function calculateProfitLossBack(stake) {
    //alert(parseFloat(odd));
    if(betType == 'fancy') {
      profit = parseFloat((stake*line) / 100);
    } else {
      profit = parseFloat((stake*odd) - stake);
    }
    profit = profit.toFixed(0);
    loss = parseFloat(stake);
    loss = loss.toFixed(0);
    setTimeout(function(){ $("#backBetLoss").text(loss); }, 300);
    setTimeout(function(){ $("#backBetProfit").text(profit); }, 300);
    amount = parseFloat(stake);
    $("#backStakeValue").val(stake);
    calculateProfitLoss();
  }

  function calculateProfitLossLay(stake) {
    //alert(parseFloat(odd));
    profit = parseFloat(stake);
    profit = profit.toFixed(0);
    if(betType == 'fancy') {
      loss = parseFloat((stake*line)/100);
    } else {
      loss = parseFloat((stake*odd) - stake);
    }
    loss = loss.toFixed(0);
    setTimeout(function(){ $("#layBetLoss").text(loss); }, 300);
    setTimeout(function(){ $("#layBetProfit").text(profit); }, 300);
    amount = parseFloat(stake);
    $("#layStakeValue").val(stake);
    calculateProfitLoss();
  }

  function checkLimitBeforBetPlace() {
    if (amount >= 500 && amount <= 200000) {
        $('.loader').show();
        setTimeout(function () { placeBet(); }, 5000);
    } else {
        swal('bet stake should be greater than 500 & less than 200000');
    }
  }

  function placeBet() {
    $.ajax({
      url: "<?php echo site_url('Winner/placeBet') ?>",
      type: "POST",

      data: {
          match_id: matchId,
          market_id: marketId,
          match_name: matchName,
          team: teamName,
          team_id: teamId,
          market: market,
          back_lay: backLay,
          odd: odd,
          stake: amount,
          profit: profit,
          loss: loss,
          bet_type: betType,
          line: line,
      },
      success: function (response)
      {
          console.log(response);
          var data = JSON.parse(response);
          var msg = '<div class="alert '+data.class+' alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+data.message+'.</div>';
          $("#betMessage").html(msg);
          $("#finalBal").text(data.bal);
          $('.loader').hide();
          clearAllSelection();
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          
      }
    });
  }

  
</script>