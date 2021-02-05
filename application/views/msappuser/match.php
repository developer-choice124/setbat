<link href="<?php echo base_url('assets/plugins/bower_components/sweetalert/sweetalert.css')?>" rel="stylesheet" type="text/css"><script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
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
</style>
<div class="container-fluid no-gutters">
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
          <i class="fa fa-star-o"></i>&nbsp; <?=$match->event_name;?>
        </div>
        <div class="card-header bg-white">
          <span class="text-success">In-play</span>&nbsp;Match Odds
          <span class="float-right"><?=date('D Y-M-d H:i:s A');?></span>
        </div>
        <!-- Tab panes -->
        <div class="card-body" style="padding: 0 !important;">
          <div id="betMessage"></div>
          <div class="row">
            <div class="col-6 border">
              <span class="text-danger font-weight-bold">Min Stake:&nbsp;200<br/>Max Stake:&nbsp;500000</span>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border" style="background: #2c5ca9;">
              <center>Lagai</center>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border" style="background: #EF8279;">
              <center>Khai</center>
            </div>
          </div>
          <div id="matchOdd">
          <?php 
            $runners = $odds['runners'];
            //echo json_encode($runners);die;
            $teams = json_decode($match->teams);
            $teamIds = array();
            foreach ($teams as $tm) {
              $teamIds[] = $tm->id;
            }
            $bprice = 0; $bsize = 0; $lprice = 0; $lsize = 0;
            foreach ($runners as $rk => $r):
            $bprice = $r['ex']['availableToBack'][0]['price'];
            $bsize = $r['ex']['availableToBack'][0]['size'];
            $lprice = $r['ex']['availableToLay'][0]['price'];
            $lsize = $r['ex']['availableToLay'][0]['size'];

          ?>
            <div class="row">
              <div class="col-6 border">
                <span class="font-weight-bold pl-1 clearfix"><?=$teams[$rk]->name;?></span>
                <span id="<?=$teams[$rk]->id.'_pl';?>" 
                  
                  class="pl-1 font-weight-bold"></span>
              </div>
              <div class="col-3 text-center border" id="<?=$teams[$rk]->id.'_backParentdiv';?>" style="background: #ffffea;">
                <div 
                data-others = "<?php echo json_encode($teamIds); ?>" id="<?=$teams[$rk]->id.'_backdiv';?>" onclick="showBackBetDiv('<?=$teams[$rk]->id;?>','<?=$teams[$rk]->name;?>','<?=$rk;?>','back','matched','<?=$bprice;?>','<?=$bsize;?>')">
                  <span id="<?=$teams[$rk]->id.'_backodd';?>">
                    <center><b><?=$bprice;?></b><br/><?=$bsize;?></center>
                  </span>
                </div>
              </div>
              <div class="col-3 text-center border" id="<?=$teams[$rk]->id.'_layParentdiv';?>" style="background: #ffffea;">
                <div data-others = "<?php echo json_encode($teamIds); ?>" id="<?=$teams[$rk]->id.'_laydiv';?>" onclick="showLayBetDiv('<?=$teams[$rk]->id;?>','<?=$teams[$rk]->name;?>','<?=$rk;?>','lay','matched','<?=$lprice;?>','<?=$lsize;?>')">
                  <span id="<?=$teams[$rk]->id.'_layodd';?>">
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
      <div class="card border">
        <div class="card-body" style="padding: 0 !important;">
          <div class="row">
            <div class="col-6 border">
              <span class="text-danger font-weight-bold">Session</span>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border" style="background: #EF8279;">
              <center>No</center>
            </div>
            <div class="col-3 text-white text-center font-weight-bold border" style="background: #2c5ca9;">
              <center>Yes</center>
            </div>
          </div>
          <div id="matchFancy">
          <?php
            $did = array();
            foreach ($dfancy as $dkey => $d) {
                $did[] = $d['fancy_id'];
            }
            if(!empty($fancy['session'])) {
              $fancies = $fancy['session'];
              foreach ($fancies as $fkey => $f) {
                if (in_array($f['SelectionId'], $did)) { ?>
                  <?php 
                    $lprice = $f['LayPrice1'];
                    $lsize = $f['LaySize1'];
                    $bprice = $f['BackPrice1'];
                    $bsize = $f['BackSize1'];
                  ?>
                  <div class="row">
                    <div class="col-6 border pt-2"><?= $f['RunnerName']; ?></div>
                    <div class="col-3 text-center border" style="background: #ffffea;" onclick="showLayBetDiv('<?= $f['SelectionId'] ?>','<?= $f['RunnerName']; ?>','<?=$fkey;?>','lay','fancy','<?=$lprice;?>','<?=$lsize;?>')">
                      <b><?= $f['LayPrice1']; ?></b><br/><?= $f['LaySize1']; ?></div>
                    <div class="col-3 text-center border" style="background: #ffffea;" 
                      onclick="showBackBetDiv('<?= $f['SelectionId'] ?>','<?= $f['RunnerName']; ?>','<?=$fkey;?>','back','fancy','<?=$bprice;?>','<?=$bsize;?>')"
                      ><b><?= $f['BackPrice1']; ?></b><br/><?= $f['BackSize1']; ?></div>
                  </div>
            <?php  
                }
              }
            }
            
          ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  var team1 = 0;
  var team2 = 0;
  var team3 = 0;
  var team1pl = 0;
  var team2pl = 0;
  var team3pl = 0;
  var odd = 0;
  var line = 0;
  var teamId = 0;
  var teamName = '';
  var profit = 0;
  var loss = 0;
  var amount = 0;
  var matchId = '<?=$match->event_id;?>';
  var marketId = '<?=$match->market_id;?>';
  var matchName = '<?=$match->event_name;?>';
  var market = '<?= $match->mtype; ?>';
  var backLay = 'back';
  var betType = 'matched';
  $(document).ready(function () {
    callAsync();
  });
  function callAsync() {
    $.ajax({
        url: "<?php echo site_url('MsAppUser/callAsync?market_id=') ?>" + marketId + "&match_id=" + matchId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            $("#matchOdd").html(data.oddData);
            $("#matchFancy").html(data.fancyData);
            calculateProfitLoss();
            setTimeout( callAsync, 1200);
        }
    });
  }

  function calculateProfitLoss() {
    $.ajax({
        url: "<?php echo site_url('MsAppUser/calculateProfitLoss?market_id=') ?>" + marketId,
        type: "POST",
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            //console.log(data);
            var current = {};
            var j = 0;
            var ele = data;
            for(i = 0; i < data.length; i++) {
              if(data[i].id == teamId) {
                current = data[i];
                j = i;
              }
            }
            if(Object.keys(current).length > 0){
              if(j == 0) {
                if(backLay == 'back') {
                  team1pl = parseInt(data[0].pl) + parseInt(profit);
                  team2pl = parseInt(data[1].pl) - parseInt(loss);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) - parseInt(loss);
                  }
                  
                } else {
                  team1pl = parseInt(data[0].pl) - parseInt(loss);
                  team2pl = parseInt(data[1].pl) + parseInt(profit);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) + parseInt(profit);
                  }
                  
                }
              } else if(j == 1) {
                if(backLay == 'back') {
                  team1pl = parseInt(data[0].pl) - parseInt(loss);
                  team2pl = parseInt(data[1].pl) + parseInt(profit);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) - parseInt(loss);
                  }
                  
                } else {
                  team1pl = parseInt(data[0].pl) + parseInt(profit);
                  team2pl = parseInt(data[1].pl) - parseInt(loss);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) + parseInt(profit);
                  }
                  
                }
              } else if(j == 2) {
                if(backLay == 'back') {
                  team1pl = parseInt(data[0].pl) - parseInt(loss);
                  team2pl = parseInt(data[1].pl) - parseInt(loss);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) + parseInt(profit);
                  }
                  
                } else {
                  team1pl = parseInt(data[0].pl) + parseInt(profit);
                  team2pl = parseInt(data[1].pl) + parseInt(profit);
                  if(data.length > 2)
                  {
                    team3pl = parseInt(data[2].pl) - parseInt(loss);
                  }
                  
                }
              } else {

              }
            } else {
              if(data) {
                team1pl = data[0].pl;
                team2pl = data[1].pl;
                if(data.length > 2)
                {
                  team3pl = data[2].pl;
                }
              }
            }
            team1pl >= 0 ? $("#"+data[0].id+"_pl").addClass("text-success") : $("#"+data[0].id+"_pl").addClass("text-danger");
            team1pl >= 0 ? $("#"+data[0].id+"_pl").removeClass("text-danger") : $("#"+data[0].id+"_pl").removeClass("text-success");
            $("#"+data[0].id+"_pl").text(Math.abs(team1pl));
            team2pl >= 0 ? $("#"+data[1].id+"_pl").addClass("text-success") : $("#"+data[1].id+"_pl").addClass("text-danger");
            team2pl >= 0 ? $("#"+data[1].id+"_pl").removeClass("text-danger") : $("#"+data[1].id+"_pl").removeClass("text-success");
            $("#"+data[1].id+"_pl").text(Math.abs(team2pl));
            if(data.length > 2)
            {
              team3pl >= 0 ? $("#"+data[2].id+"_pl").addClass("text-success") : $("#"+data[2].id+"_pl").addClass("text-danger");
              team3pl >= 0 ? $("#"+data[2].id+"_pl").removeClass("text-danger") : $("#"+data[2].id+"_pl").removeClass("text-success");
              $("#"+data[2].id+"_pl").text(Math.abs(team3pl));
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
    odd = bodd;
    line = bline;
    backLay = backOrLay;
    betType = bet_type;
    amount = $("#backStakeValue").val();
    calculateProfitLossBack(amount);
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
    odd = lodd;
    line = lline;
    backLay = backOrLay;
    betType = bet_type;
    amount = $("#layStakeValue").val();
    calculateProfitLossLay(amount);
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
    if (amount >= 500 && amount <= 500000) {
        $('.loader').show();
        setTimeout(function () { placeBet(); }, 2000);
    } else {
        swal('bet stake should be greater than 500 & less than 500000');
    }
  }

  function placeBet() {
    $.ajax({
      url: "<?php echo site_url('MsAppUser/placeBet') ?>",
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