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
  .loader {
        display: none;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8) url(<?= base_url('assets/plugins/images/loader.gif'); ?>) top center no-repeat;
        z-index: 1000000;
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
                     Running Matches
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="loader"></div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="table table-condensed" >
                            <thead>
                                <tr class="headings">
                                  <th>S. No</th>
                                  <th class="">Event </th>
                                  <th class="">Date </th>
                                  <th class="">Competition </th>
                                  <th class="">Type </th>
                                  <th>Match Result</th>
                                  <th class="">Action </th>
                                  <th>Play/Pause</th>
                                  <th>Score Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($crickets as $c):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$c['event_name'];?></td>
                                    <td><?=date('D d-M-Y H:i:sa',strtotime($c['start_date']));?></td>
                                    <td><?=$c['competition_name'];?></td>
                                    <td><?=$c['mtype'];?></td>
                                    <td>
                                      <?php if($c['match_result'] == 'running'){ ?> 
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#resultModal" onclick="matchResult('<?=$c['id'];?>')">Playing</a>
                                        <?php }else{echo '<b>'.$c['match_result'].'</b>';}?>
                                    </td>
                                    <td><a href="<?=base_url('Admin/matchOdds?match_id='.$c['event_id'].'&market_id='.$c['market_id']);?>" data-toggle="tooltip" title="View Match Odds">Match Odds</a>&nbsp;&nbsp;<a href="<?=base_url('Admin/viewMatchFancy?match_id='.$c['event_id'].'&market_id='.$c['market_id']);?>" data-toggle="tooltip" title="View Fancy list">Fancy List</a></td>
                                    <td>
                                      <?=anchor("Admin/playPauseMatch?id=".$c['id'],$c['match_result'],array('onclick' => "return confirm('Do you want to change this match status')"))?>
                                    </td>
                                    <td><input type="text" name="cricbuzz_id" id="cricbuzz_id" onkeyup="saveCricbuzzId(this.value, '<?=$c['id'];?>')"></td>
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
<!-- Modal -->
<div id="resultModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Match Result</h4>
      </div>
      <div id="matchFormData">
        
      </div>
      
    </div>

  </div>
</div>
<script>
    function matchResult(id)
    {
      $.ajax({
        url : "<?php echo site_url('Admin/matchResult?id=')?>"+id,
        type: "POST",
        //dataType: "JSON",
        success: function(data)
        {
            $("#matchFormData").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert("error");
        }
      });
    }
    function resultDeclare()
    {
      swal({   
            title: "Are you sure?",   
            text: "Is this a correct selection!",   
            type: "warning",  
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, it is!",   
            closeOnConfirm: true 
        }, function(){
          var winner = $("#winner").val();
          var match_id = $("#match_id").val();
          var market_id = $("#market_id").val();
          $('.loader').show();
          $.ajax({
            url : "<?php echo site_url('Admin/resultDeclare')?>",
            type: "POST",
            data: {winner:winner, match_id:match_id,market_id:market_id},
            //dataType: "JSON",
            success: function(data)
            {
                //console.log(data);
                $("#resultModal").modal('hide');
                location.reload();
                //fancyStatus(fid,mid,mkey);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert("error");
            }
          });
        });
    }
    function saveCricbuzzId(cricbuzz_id, id) {
      $.ajax({
          url : "<?php echo site_url('Admin/saveCricbuzzId')?>",
          type: "GET",
          data: {id: id, cricbuzz_id: cricbuzz_id},
          dataType: "JSON",
          success: function(data)
          {
              console.log(data);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              //alert("error");
          }
        });
    }
</script>
