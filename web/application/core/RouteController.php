<?php

class RouteController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        if (!isset($this->session->all_userdata()['Username'])) {
            redirect('Login');
        }
    }
 }