<script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
<div class="content_wrapper no-gutters">
    <div class="container-fluid no-gutters">
        
        <!-- Section -->
        <section class="chart_section">

            


            <div class="row">
                <div class="col-12">
                    <div class="full_chart card mb-4">
                        <div class="chart_header">
                            <div class="chart_headibg">
                                <h3>In Play Matches</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg" id="matchTable">
                                <table id="" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                    <tbody>
                                        <?php $i = 1;foreach ($matches as $mkey => $m) {?>
                                          
                                            <tr>
                                                <td><a href="<?=base_url('Winner/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"><?=$m['event_name'];?></a>
                                                </td>
                                                <td><a href="<?=base_url('Winner/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>">
                                                        <?php if($m['odds'][0]['inplay'] == 1 || $m['odds'][0]['inplay'] == true || $m['odds'][0]['inPlay'] == 1 || $m['odds'][0]['inPlay'] == true) {echo 'In Play';}?>
                                                    </a>
                                                </td>
                                                <td><?=date('D d-M-Y H:i:sa', strtotime($m['start_date']));?></td>
                                                <td>
                                                    <?php foreach($m['odds'][0]['teams'] as $r):?>
                                                    <a href="<?=base_url('Winner/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"
                                                        class="btn btn-primary" style="color: white;">
                                                        <?=$r->back['price'];?>
                                                    </a>
                                                    <a href="<?=base_url('Winner/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"
                                                        class="btn btn-danger" style="color: white;">
                                                        <?=$r->lay['price'];?>
                                                    </a>
                                                    <?php endforeach;?>
                                                </td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </section>
        <!-- Section_End -->

    </div>
</div>

<script type="text/javascript">
    var matches;
    $(document).ready(function(){
      getInPlay();
    });

    function getInPlay() {
      $.ajax({
        url : "<?php echo site_url('MsAppUser/getCrickets')?>",
        type: "POST",
        success: function(data)
        {
          $("#matchTable").html(data);
          setTimeout( getInPlay, 1000 );
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
</script>