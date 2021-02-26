<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<style type="text/css">
  .headings{
    background: #008efa !important;
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
            <div class="panel panel-dark">
                <div class="panel-heading">
                     Fancy List( <?=$match->event_name;?> )
                    <div class="pull-right"><a href="<?=base_url('SuperAdmin/runningCricket');?>">Running Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="display nowrap  table-border table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr class="headings">
                                  <th>S. No</th>
                                  <th class="">Name </th>
                                  <th>Event</th>
                                  <th class="">Type </th>
                                  <th class="">Action </th>
                                  <th class="">Declare</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <tr>
                                  <td><?=$i++;?></td>
                                  <td><?=$fancy->fancy_name;?> </td>
                                  <td><?=$fancy->event_name;?></td>
                                  <td>Fancy </td>
                                  <td><?=$fancy->status;?></td>
                                  <td>
                                    <?php if($fancy->status == 'settled') {
                                        echo 'settled';
                                      } else { ?>
                                        <a href="javascript:void(0)" onclick="fancySettle('<?=$fancy->id;?>','<?=$fancy->market_id;?>','<?=$fancy->fancy_id;?>','<?=$fancy->fancy_name;?>','<?="status0"?>')" data-toggle="modal" data-target="#fancyModal">Settle Fancy</a>
                                    <?php } ?>
                                  </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="fancyModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Fancy Result</h4>
      </div>
      <div class="modal-body" id="matchFormData">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label col-md-8" id="fancyName"></label>
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Fancy Score" id="fancyScore">
                <input type="hidden" name="market_id" id="marketId">
                <input type="hidden" name="fancy_id" id="fancyId">
                <input type="hidden" name="fancy_key" id="fancyKey">
                <input type="hidden" name="fdid" id="fdid">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <button type="button" class="btn btn-dark" onclick="declareFancy()">Declare</button>
            </div>
          </div>
        </div>
      </div>
      
    </div>

  </div>
</div>
<div id="alerttopright" class="mySuperAdmin-alert alert-success mySuperAdmin-alert-top-right">
  <a href="#" class="closed">&times;</a>
  Fancy status updated!
</div>
<script>
    $(".mySuperAdmin-alert-top-right").click(function(event) {
        $(this).fadeToggle(350);

        return false;
    });

    function fancySettle(fdid,mid,fid,fname,mkey) {
      $("#marketId").val(mid);
      $("#fancyId").val(fid);
      $("#fancyName").text(fname);
      $("#fancyKey").val(mkey);
      $("#fdid").val(fdid);
    }

    function declareFancy()
    {
      swal({   
            title: "Are you sure?",   
            text: "Is this a correct score!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, it is!",   
            closeOnConfirm: false 
        }, function(){
          var mid = $("#marketId").val();
          var fid = $("#fancyId").val();
          var fscore = $("#fancyScore").val();
          var mkey = $("#fancyKey").val();
          var fdid = $("#fdid").val();
          $.ajax({
            url : "<?php echo site_url('SuperAdmin/declareFancy?market_id=')?>"+mid+"&fancy_id="+fid+"&fancy_score="+fscore+"&fdid="+fdid,
            type: "POST",
            //dataType: "JSON",
            success: function(data)
            {
                $("#fancyModal").modal('hide');
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

    function matchResult(id)
    {
      $.ajax({
        url : "<?php echo site_url('SuperAdmin/matchResult?id=')?>"+id,
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
    function fancyStatus(fid,mid,mkey)
    {
      $.ajax({
        url : "<?php echo site_url('SuperAdmin/fancyStatus?market_id=')?>"+mid+"&fancy_id="+fid,
        type: "POST",
        //dataType: "JSON",
        success: function(msg)
        {
            var obj = JSON.parse(msg);
            $("#"+mkey).text(obj.status);
            $("#alerttopright").fadeToggle(350);
            setTimeout(function(){ location.reload(); }, 1500);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert("error");
        }
      });
    }
</script>