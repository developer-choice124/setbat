<script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
<div class="content_wrapper">
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="page-heading">
            <div class="row d-flex align-items-center">
                <div class="col-12">
                    <div class="page-breadcrumb">
                        <h1>Dashboard</h1>
                    </div>
                </div>
                <div class="col-12  d-md-flex">
                    <div class="breadcrumb_nav">
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-home"></i>
                                <a class="parent-item" href="<?=base_url('MsAppUser/index');?>">Dashboard</a>
                                <i class="fa fa-angle-right"></i>
                            </li>
                            <li class="active">
                                In Play
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb_End -->

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
                                          <?php if($m['status'] == 1 || $m['status'] == true) { ?>
                                            <tr>
                                                <td><a href="<?=base_url('MsUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"><?=$m['event_name'];?></a>
                                                </td>
                                                <td><a href="<?=base_url('MsUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>">
                                                        <?php if ($m['status'] == 1 || $m['status'] == true) {echo 'In Play';}?>
                                                    </a>
                                                </td>
                                                <td><?=date('D d-M-Y H:i:sa', strtotime($m['start_date']));?></td>
                                                <td>
                                                    <?php foreach ($m['odds'] as $r): ?>
                                                    <a href="<?=base_url('MsUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"
                                                        class="btn btn-info" style="color: white;">
                                                        <?=$r['ex']['availableToBack'][0]['price'];?>
                                                    </a>
                                                    <a href="<?=base_url('MsUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']);?>"
                                                        class="btn btn-danger" style="color: white;">
                                                        <?=$r['ex']['availableToLay'][0]['price'];?>
                                                    </a>
                                                    <?php endforeach;?>
                                                </td>
                                            </tr>
                                          <?php } ?>
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
        url : "<?php echo site_url('MsAppUser/getInPlay')?>",
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