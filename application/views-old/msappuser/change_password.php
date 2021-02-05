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
                                <h3>Change Password</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <form method="post" action="<?=base_url('User/updatePassword?user_id='.$this->session->userdata('user_id'));?>">
                              <div class="row">
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label for="old">Old Password</label>
                                      <input type="password" name="old" id="old" class="form-control" placeholder="Please enter your old password">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label for="new">New Password</label>
                                      <input type="text" name="new" id="new" class="form-control" placeholder="Please enter your new password">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label for="new">Confirm New Password</label>
                                      <input type="text" name="new_confirm" id="new-confirm" class="form-control" placeholder="Please confirm your new password">
                                  </div>
                                </div>
                              </div>
                              <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            
        </section>
        <!-- Section_End -->

    </div>
</div>