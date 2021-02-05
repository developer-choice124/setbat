<script type="text/javascript">
    var match;
    var fancies;
    var bets;
    var scores;
    var pNl;
    var team;
    var team1Id = '<?= $t1id; ?>';
    var team2Id = '<?= $t2id; ?>';
    var team1pl = 0;
    var team1color;
    var team2pl = 0;
    var team2color;
    var matchId = '<?= $match->event_id; ?>';
    var marketId = '<?= $match->market_id; ?>'
    var ctype = 'back';
    var id = 1;
    var profit = 0;
    var loss = 0;
    var stake;
    var price;
    var curChips = '<?= $chips->current_chips ? $chips->current_chips : 0; ?>';
    var maxchips = '<?= $chips->balanced_chips ? $chips->balanced_chips : 0; ?>';
    var oddFancy;
    var finalTotal = 0;
    var selectedPrice;
    var currentPrice;
    var selectedKey;
    var selectedType;
    var team1;
    var team2;
    var changed = 'no';
    maxchips = parseInt(maxchips);
    var previousBalance = 0;
    var maxLimit = 0;
    var maxUse = 0;
    var updateMain;
    var actualLoss = '<?= $chips->balanced_chips ? $chips->balanced_chips : 0; ?>';
    $(document).ready(function () {
        team1 = "#team0";
        team2 = "#team1";
        selectedPrice = 0;
        currentPrice = 0;
        selectedKey = 0;
        selectedType = 'back';
      //  $("input[name='odd']").TouchSpin();
      //  $("input[name='stake']").TouchSpin();
        showPL();
        callAsync();
        //setInterval("callAsync()", 3000);
        //
//        match = setInterval("matchReload()", 2000);
//        fancies = setInterval("fancyReload()", 3000);
//        scores = setInterval("scoreReload()", 4000);
        bets = setInterval("betReload()", 30000);
//        updateMain = setInterval("updateMainBalance()", 5000);
        profitNLoss();
    });
    //#######################################
    function callAsync() {
        var mid = "<?= $match->market_id; ?>";
        var fid = id;
        $.ajax({
            url: "<?php echo site_url('MsUser/callAsync?market_id=') ?>" + marketId + "&selectedKey=" + selectedKey + "&selectedType=" + selectedType + '&fancy_id=' + id,
            type: "POST",
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                //console.log(JSON.stringify(data));
//                "result_match_reload" => $result_match_reload,
//            "result_fancy_reload" => $result_fancy_reload,
//            "result_score_reload" => $result_score_reload,
//            "result_bet_reload" => $result_bet_reload,
//            "result_main_balance" => $result_main_balance,

                uiMainBalance(data.result_main_balance);
                uiMatchReload(data.result_match_reload);
                uifancyReload(data.result_fancy_reload);
                uiscoreReload(data.result_score_reload);
                setTimeout( callAsync, 1000 );
                //uibetReload(data.result_bet_reload);
            }

        });
    }
    //#######################################

    function profitNLoss() {
        $.ajax({
            url: "<?php echo site_url('MsUser/profitNLoss?market_id=') ?>" + marketId + "&team1=" + team1Id + "&team2=" + team2Id,
            type: "POST",
            success: function (data)
            {
                var obj = JSON.parse(data);
                team1color = obj.team1status;
                team2color = obj.team2status;
                team1pl = parseInt(obj.team1pl);
                team1pl = team1pl.toFixed(0);
                team2pl = parseInt(obj.team2pl);
                team2pl = team2pl.toFixed(0);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }


    function closeBackLay() {
        $("#backWell").hide();
        $("#layWell").hide();
        profit = 0;
        loss = 0;
        $(team1).html("0");
        $(team2).html("0");
    }

    function clearBackLay() {
        $("#backStake").val(0);
        $("#layStake").val(0);
        profit = 0;
        loss = 0;
        $(team1).html("0");
        $(team2).html("0");
    }

    function getBackLay(type, price, team, teamId, teamKey, betType, line) {
        id = teamKey;
        ctype = type;
        oddFancy = betType;
        if (type == 'back')
        {
            stake = $("#backStake").val();
            $("#backWell").show();
            $("#layWell").hide();
            $("#backBetTeam").text(team);
            $("#backTeamId").val(teamId);
            if (betType == 'fancy') {
                $("#backOdd").val(line);
                $("#back_line").val(price);
                selectedPrice = line;
                selectedKey = teamKey;
                selectedType = type;
            } else {
                $("#backOdd").val(price);
                $("#back_line").val(line);
                selectedPrice = price;
                selectedKey = teamKey;
                selectedType = type;
            }

            $("#backTeam").val(team);
            $("#backBet_type").val(betType);
            profitLossBack(stake, changed);
            checkMax();
        } else {
            stake = $("#layStake").val();
            $("#layWell").show();
            $("#backWell").hide();
            $("#layBetTeam").text(team);
            $("#layTeamId").val(teamId);
            if (betType == 'fancy') {
                $("#layOdd").val(line);
                $("#lay_line").val(price);
                selectedPrice = line;
                selectedKey = teamKey;
                selectedType = type;
            } else {
                $("#layOdd").val(price);
                $("#lay_line").val(line);
                selectedPrice = price;
                selectedKey = teamKey;
                selectedType = type;
            }

            $("#layTeam").val(team);
            $("#layBet_type").val(betType);
            profitLossLay(stake, changed);
            checkMax();
        }
        showPL();
    }

    function stakeAdd(stake, type) {
        if (type == 'back') {
            $("#backStake").val(stake);
            profitLossBack(stake);
        } else {
            $("#layStake").val(stake);
            profitLossLay(stake, changed);
        }
    }

    function checkFancyMaxUse() {

    }

    function profitLossLayFancy(stake) {
        price = $("#lay_line").val();
        //stake = $("#layStake").val();
        var total = (price * stake);
        finalTotal = total / 100;
        profit = parseInt(stake);
        profit = profit.toFixed(0);
        loss = parseInt(total / 100);
        loss = loss.toFixed(0);
        var fteam = $("#layTeam").val();
        var fodd = $("#layOdd").val();
        galiLimit(fteam, fodd, 'lay');
        if (actualLoss < loss) {
            swal('loss chips can not be greater than balanced_chips');
            $("#layStake").val(0);
        } else {
            $("#layStake").val(stake);
            $("#layBetProfit").text(profit);
            $("#layBetLoss").text(loss);
            $("#layProfit").val(profit);
            $("#layLoss").val(loss);
        }
        console.log("----");
        console.log(actualLoss);
        console.log(loss);
        //showPL();
    }

    function galiLimit(fteam, fodd, type) {
        $.ajax({
            url: "<?php echo site_url('MsUser/checkFancyGali?market_id=') ?>" + marketId + "&team=" + fteam + "&odd=" + fodd + "&type=" + type,
            type: "POST",
            success: function (fpbal)
            {
                actualLoss = parseInt(fpbal);
            }
        });
    }

    function profitLossBackFancy(stake) {
        price = $("#back_line").val();

        //stake = $("#backStake").val();
        var total = (price * stake);
        finalTotal = total / 100;
        profit = total / 100;
        profit = profit.toFixed(0);
        loss = parseInt(stake);
        loss = loss.toFixed(0);
        var fteam = $("#backTeam").val();
        var fodd = $("#backOdd").val();
        galiLimit(fteam, fodd, 'back');
        if (actualLoss < loss) {
            swal('loss chips can not be greater than balanced_chips');
            $("#backStake").val(0);
        } else {
            $("#backStake").val(stake);
            $("#backBetProfit").text(profit);
            $("#backBetLoss").text(loss);
            $("#backProfit").val(profit);
            $("#backLoss").val(loss);
        }
        console.log("----");
        console.log(actualLoss);
        console.log(loss);
        //showPL();
    }

    function profitLossLay(stake, change) {

        if (change) {
            changed = change;
        }
        if (oddFancy == 'fancy') {
            profitLossLayFancy(stake);
        } else {
            price = $("#layOdd").val();
            //stake = $("#layStake").val();
            var total = (price * stake);
            finalTotal = total;
            profit = parseInt(stake);
            profit = profit.toFixed(0);
            loss = parseInt(total - stake);
            loss = loss.toFixed(0);
            $("#layStake").val(stake);
            $("#layBetProfit").text(profit);
            $("#layBetLoss").text(loss);
            $("#layProfit").val(profit);
            $("#layLoss").val(loss);
            $("#layChanged").val(changed);
            profitNLoss();
            showPL();
            checkMax();
        }
    }
    function profitLossBack(stake, change) {

        if (change) {
            changed = change;
        }
        if (oddFancy == 'fancy') {
            profitLossBackFancy(stake);
        } else {
            price = $("#backOdd").val();
            //stake = $("#backStake").val();
            var total = (price * stake);
            finalTotal = total;
            profit = total - stake;
            profit = profit.toFixed(0);
            loss = parseInt(stake);
            loss = loss.toFixed(0);
            $("#backStake").val(stake);
            $("#backBetProfit").text(profit);
            $("#backBetLoss").text(loss);
            $("#backProfit").val(profit);
            $("#backLoss").val(loss);
            $("#backChanged").val(changed);
            profitNLoss();
            showPL();
            checkMax();
        }
    }

    function checkPreviousBalance() {
        $.ajax({
            url: "<?php echo site_url('MsUser/checkUserMaxLimit/') ?>" + marketId,
            type: "POST",
            success: function (pbal)
            {
                previousBalance = parseInt(pbal);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
        // alert("hi");
        // previousBalance = 50000;
    }

    function checkMax() {
        checkPreviousBalance();
        if (ctype == 'back')
        {
            if (id == 1) {
                var totalTeam1 = parseInt(team1pl) - parseInt(loss);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) + parseInt(profit);
                totalTeam2 = totalTeam2.toFixed(0);
                var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
                var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;

            } else {
                var totalTeam1 = parseInt(team1pl) + parseInt(profit);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) - parseInt(loss);
                totalTeam2 = totalTeam2.toFixed(0);
                var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
                var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;

            }
            t1loss = parseInt(t1loss);
            t2loss = parseInt(t2loss);
            if (t1loss < 0 && t2loss < 0) {
                var finalLoss = t1loss > t2loss ? t1loss : t2loss;
            } else if (t1loss < 0 && t2loss >= 0) {
                var finalLoss = t1loss;
            } else if (t1loss >= 0 && t2loss < 0) {
                var finalLoss = t2loss;
            }
            maxLimit = parseInt(finalLoss);
        } else {
            if (id == 1) {
                var totalTeam1 = parseInt(team1pl) + parseInt(profit);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) - parseInt(loss);
                totalTeam2 = totalTeam2.toFixed(0);
                var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
                var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
            } else {
                var totalTeam1 = parseInt(team1pl) - parseInt(loss);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) + parseInt(profit);
                totalTeam2 = totalTeam2.toFixed(0);
                var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
                var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
            }
            t1loss = parseInt(t1loss);
            t2loss = parseInt(t2loss);
            if (t1loss < 0 && t2loss < 0) {
                var finalLoss = t1loss > t2loss ? t1loss : t2loss;
            } else if (t1loss < 0 && t2loss >= 0) {
                var finalLoss = t1loss;
            } else if (t1loss >= 0 && t2loss < 0) {
                var finalLoss = t2loss;
            }
            maxLimit = parseInt(finalLoss);
        }
        maxLimit = Math.abs(maxLimit);
        curChips = parseInt(curChips);
        maxUse = previousBalance - maxLimit;
        console.log(maxUse);
        console.log(curChips);
        console.log(previousBalance);
        console.log(maxLimit);
        if (maxUse < 0) {
            $("#layStake").val(0);
            $("#backStake").val(0);
            swal("max stake should not be greater than balanced_chips");
        }
    }

    function showPL() {
        var team1 = "#team0";
        var team2 = "#team1";
        if (ctype == 'back')
        {
            if (id == 1) {
                // if(team1color == 'p') {
                //   var pp = team1pl - loss;
                //   pp = Math.abs(pp);
                //   $(team1).html("<span class='text-danger'>"+loss+"</span>");
                // } else {
                //   $(team1).html("<span class='text-danger'>"+loss+"</span>");
                // }
                var totalTeam1 = parseInt(team1pl) - parseInt(loss);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) + parseInt(profit);
                totalTeam2 = totalTeam2.toFixed(0);
                var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
                var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
                $(team1).html("<span class='" + class1 + "'>" + totalTeam1 + "</span>");
                $(team2).html("<span class='" + class2 + "'>" + totalTeam2 + "</span>");
            } else {
                var totalTeam1 = parseInt(team1pl) + parseInt(profit);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) - parseInt(loss);
                totalTeam2 = totalTeam2.toFixed(0);
                var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
                var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
                $(team2).html("<span class='" + class2 + "'>" + totalTeam2 + "</span>");
                $(team1).html("<span class='" + class1 + "'>" + totalTeam1 + "</span>");
            }
        } else {
            if (id == 1) {
                var totalTeam1 = parseInt(team1pl) + parseInt(profit);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) - parseInt(loss);
                totalTeam2 = totalTeam2.toFixed(0);
                var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
                var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
                $(team2).html("<span class='" + class2 + "'>" + totalTeam2 + "</span>");
                $(team1).html("<span class='" + class1 + "'>" + totalTeam1 + "</span>");
            } else {
                var totalTeam1 = parseInt(team1pl) - parseInt(loss);
                totalTeam1 = totalTeam1.toFixed(0);
                var totalTeam2 = parseInt(team2pl) + parseInt(profit);
                totalTeam2 = totalTeam2.toFixed(0);
                var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
                var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
                $(team1).html("<span class='" + class1 + "'>" + totalTeam1 + "</span>");
                $(team2).html("<span class='" + class2 + "'>" + totalTeam2 + "</span>");
            }
        }
    }

    function uiMainBalance(data) {
        $("#mainBalance").html(data.msg);
        maxchips = data.main;
        maxchips = parseInt(maxchips);
    }
    function updateMainBalance() {
        $.ajax({
            url: "<?php echo site_url('MsUser/updateMainBalance'); ?>",
            type: "POST",
            success: function (data)
            {
                var obj = JSON.parse(data);
                $("#mainBalance").html(obj.msg);
                maxchips = obj.main;
                maxchips = parseInt(maxchips);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }

    function matchReload() {
        var mid = "<?= $match->market_id; ?>";
        $.ajax({
            url: "<?php echo site_url('MsUser/matchReload?market_id=') ?>" + mid + "&selectedKey=" + selectedKey + "&selectedType=" + selectedType,
            type: "POST",
            success: function (data)
            {

                //console.log(data);
                var obj = JSON.parse(data);
                $("#singleMatchTable").html(obj.mdata);
                currentPrice = obj.currentPrice;
                currentPrice = parseInt(currentPrice);
                showPL();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }
    function uiMatchReload(data) {
        $("#singleMatchTable").html(data.mdata);
        currentPrice = data.currentPrice;
        currentPrice = parseInt(currentPrice);
        showPL();
    }
    function uifancyReload(data) {

        $("#fancyTable").html(data.fancy);
        var showed = data.show;
        if (oddFancy == 'fancy') {
            if (showed == 'no') {
                //closeBackLay();
            }
        }
    }

    function fancyReload() {
        var mid = "<?= $match->market_id; ?>";
        var fid = id;
        $.ajax({
            url: "<?php echo site_url('MsUser/fancyReload?market_id=') ?>" + mid + "&fancy_id=" + id,
            type: "POST",
            success: function (msg)
            {
                var obj = JSON.parse(msg);
                $("#fancyTable").html(obj.fancy);
                var showed = obj.show;
                if (oddFancy == 'fancy') {
                    if (showed == 'no') {
                        //closeBackLay();
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }
    function uiscoreReload(data) {
        $("#scoreTable").html(data);
    }

    function scoreReload() {
        var mid = "<?= $match->market_id; ?>";
        $.ajax({
            url: "<?php echo site_url('MsUser/scoreReload?market_id=') ?>" + mid,
            type: "POST",
            success: function (msg)
            {
                $("#scoreTable").html(msg);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }

    function placeBetLay() {
        var odd = $("#layOdd").val();
        if (odd) {
            var maxStake = $("#layStake").val();
            maxStake = parseInt(maxStake).toFixed(0);
            if (maxStake >= 100 && maxStake <= 500000) {
                $('.loader').show();
                setTimeout(function () {
                    layBetRequest();
                }, 3000);
            } else {
                swal('bet stake should be greater than 100 & less than 500000');
            }
        } else {
            swal('odds can not be empty');
        }
    }

    function layBetRequest() {
        var match_id = $("#layMatchId").val();
        var match_name = $("#layMatchName").val();
        var team = $("#layTeam").val();
        var team_id = $("#layTeamId").val();
        var market = $("#layMarket").val();
        var back_lay = $("#layType").val();
        var odd = $("#layOdd").val();
        var stake = $("#layStake").val();
        var profit = $("#layProfit").val();
        var loss = $("#layLoss").val();
        var bet_type = $("#layBet_type").val();
        var line = $("#lay_line").val();
        var change = $("#layChanged").val();
        $.ajax({
            url: "<?php echo site_url('MsUser/placeBet') ?>",
            type: "POST",
            data: {
                match_id: match_id,
                market_id: marketId,
                match_name: match_name,
                team: team,
                team_id: team_id,
                market: market,
                back_lay: back_lay,
                odd: odd,
                stake: stake,
                profit: profit,
                loss: loss,
                bet_type: bet_type,
                line: line,
                change: change
            },
            success: function (response)
            {
                //console.log(response);
                $('.loader').hide();
                var obj = JSON.parse(response);
                msgg = obj.message;
                $("#placeMessage").text(msgg);
                $("#alerttopright").fadeToggle(350);
                betReload();
                closeBackLay();
                clearBackLay();
                profitNLoss();
                showPL();
                //updateMainBalance();
                //location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }

    function placeBetBack() {
        var odd = $("#backOdd").val();
        //console.log(finalTotal);
        if (odd) {
            var maxStake = $("#backStake").val();
            maxStake = parseInt(maxStake).toFixed(0);
            if (maxStake >= 100 && maxStake <= 500000) {
                $('.loader').show();
                setTimeout(function () {
                    backBetRequest();
                }, 3000);
            } else {
                swal('bet stake should be greater than 100 & less than 500000');
            }
        } else {
            swal('odds can not be empty');
        }
    }

    function backBetRequest() {
        var match_id = $("#backMatchId").val();
        var match_name = $("#backMatchName").val();
        var team = $("#backTeam").val();
        var team_id = $("#backTeamId").val();
        var market = $("#backMarket").val();
        var back_lay = $("#backType").val();
        var odd = $("#backOdd").val();
        var stake = $("#backStake").val();
        var profit = $("#backProfit").val();
        var loss = $("#backLoss").val();
        var bet_type = $("#backBet_type").val();
        var line = $("#back_line").val();
        var change = $("#backChanged").val();
        $.ajax({
            url: "<?php echo site_url('MsUser/placeBet') ?>",
            type: "POST",
            data: {
                market_id: marketId,
                match_id: match_id,
                match_name: match_name,
                team: team,
                team_id: team_id,
                market: market,
                back_lay: back_lay,
                odd: odd,
                stake: stake,
                profit: profit,
                loss: loss,
                bet_type: bet_type,
                line: line,
                change: change
            },
            success: function (response)
            {
                //console.log(response);
                $('.loader').hide();
                var obj = JSON.parse(response);
                msgg = obj.message;
                $("#placeMessage").text(msgg);
                $("#alerttopright").fadeToggle(350);
                betReload();
                closeBackLay();
                clearBackLay()
                profitNLoss();
                showPL();
                //updateMainBalance();
                //location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }

    function betReload() {
        var mid = "<?= $match->market_id; ?>";
        $.ajax({
            url: "<?php echo site_url('MsUser/msGetBetReload/') ?>" + mid,
            type: "POST",
            success: function (data)
            {
                $("#betReload").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }
    function uibetReload(data) {
        $("#betReload").html(data);
    }

    function deleteUnmatched(bid) {
        $.ajax({
            url: "<?php echo site_url('MsUser/deleteUnmatched?bet_id=') ?>" + bid,
            type: "POST",
            success: function (data)
            {
                setTimeout(function () {
                    betReload();
                }, 1000);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
        });
    }
    $(".myadmin-alert-top-right").click(function (event) {
        $(this).fadeToggle(350);
        return false;
    });
</script>