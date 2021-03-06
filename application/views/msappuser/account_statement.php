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
</style>
<div class="content_wrapper no-gutters">
    <div class="container-fluid no-gutters">

        <!-- Section -->
        <section class="chart_section">
            <div class="row">
                <div class="col-12">
                    <div class="full_chart card mb-4">
                        <div class="chart_header">
                            <div class="chart_headibg">
                                <h3>Account Statement</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg">
                                <table id="datatable" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                    <thead>
                                        <tr class="headings" role="row">
                                          <th>S.No. </th>
                                          <th>Date </th>
                                          <th>Description </th>
                                          <th>Credit </th>
                                          <th>Debit </th>
                                          <th>Balance </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i =1; foreach($statements as $s):?>
                                          <?php if($s['type']== 'bet') {
                                            $link = '<a href="'.base_url('MsAppUser/statementByMatchId?match_id='.$s['match_id'].'&user_id='.$s['user_id']).'">'.$s['description'].'</a>';
                                          } else {
                                            $link = $s['description'];
                                          }
                                          ?>
                                          <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=date('d-M-Y H:i:sa',strtotime($s['transaction_date']));?></td>
                                            <td><?=$link;?></td>
                                            <td><span style="color: green; font-weight: bold;"><?=$s['credits'];?></span></td>
                                            <td><span style="color: red; font-weight: bold;"><?=$s['debits'];?></span></td>
                                            <td><b><?=$s['balance'];?></b></td>
                                          </tr>
                                        <?php endforeach;?>
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