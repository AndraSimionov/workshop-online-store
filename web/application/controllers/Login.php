<?php

class Login extends CI_Controller
{
    function index()
    {

        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('Smartyci');
        $this->load->library('session');
        $this->load->library('form_validation');
        //$this->load->model('LoginModel');
        $smartyci = new Smartyci();
        $smartyci->setCompileCheck(false);

        $config =  [
            [
                'field'   => 'username',
                'label'   => 'Username',
                'rules'   => 'trim|required|min_length[5]'
            ],
            [
                'field'   => 'password',
                'label'   => 'Password',
                'rules'   => 'trim|required|min_length[5]'
            ]
        ];

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == FALSE && !isset($this->session->all_userdata()['Username'])) {
            $smartyci->display('LoginView.tpl');
        } else if ($this->form_validation->run() == TRUE && !isset($this->session->all_userdata()['Username'])) {

            $data = [
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password')
            ];

//            $result = $this->LoginModel->login($data);
            $result = TRUE;

            if ($result != TRUE) {
                $smartyci->assign('eroare', 'Invalid username or password');
                $smartyci->display('LoginError.tpl');
            } else {
                $username = $this->input->post('username');
//                $result = $this->LoginModel->getUserInfo($data);
//                $this->LoginModel->updateLastLogin($result['Id']);

                //  $this->session->set_userdata($result);
                $this->session->set_userdata(['Username' => $username]);

                $smartyci->display('LoginSuccess.tpl');

            }
        } else {
            $smartyci->display('LoginSuccess.tpl');

        }
    }
}


