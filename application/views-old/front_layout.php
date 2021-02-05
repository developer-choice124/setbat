<?php

  $this->load->view('layout/frontend_header', $this->session->userdata);
  $this->load->view($page);
  $this->load->view('layout/frontend_footer');
  
?>
