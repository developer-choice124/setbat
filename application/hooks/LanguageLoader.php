<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
        $siteLang = $ci->session->userdata('site_lang');
        if ($siteLang) {
            $ci->lang->load('all_message',$siteLang);
            $ci->lang->load('auth',$siteLang);
            $ci->lang->load('ion_auth',$siteLang);
        }
    }
}