<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // carregamos o helper que irá fazer o gerenciamento da criptografia da senha
        $this->load->helper('passw_service');
    }

    /*
     * Método que irá listar todos os usuários
     * recebe como parâmetro os campos a serem retornados
     */
    public function GetAll($fields = null){
        $this->db->select($fields)
        ->from('usuarios')
        ->order_by('nome','ASC');

        return $this->db->get()->result_array();
    }

    /*
     * Método que irá fazer a validação dos dados e processar o insert na tabela
     * recebe como parâmetro o array com os dados vindos do formulário
     */
    function Insert($dados) {
        if (!isset($dados)) {
            $response['status'] = false;
            $response['message'] = "Dados não informados.";
        } else {
            // setamos os dados que devem ser validados
            $this->form_validation->set_data($dados);
            // definimos as regras de validação
            $this->form_validation->set_rules('nome','Nome','required|min_length[2]|trim');
            $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[usuarios.email]|trim');
            $this->form_validation->set_rules('senha','Senha','required|min_length[6]|trim');
            $this->form_validation->set_rules('biografia','Biografia','trim');

            // executamos a validação e verificamos o seu retorno
            // caso haja algum erro de validação, define no array $response
            // o status e as mensagens de erro
            if ($this->form_validation->run() === false) {
                $response['status'] = false;
                $response['message'] = validation_errors();
            } else {
                // criptografamos a senha
                $dados['senha'] = EncryptPassw($dados['senha']);
                //executamos o insert
                $status = $this->db->insert('usuarios', $dados);
                // verificamos o status do insert
                if ($status) {
                    $response['status'] = true;
                    $response['message'] = "Usuário inserido com sucesso.";
                } else {
                    $response['status'] = false;
                    $response['message'] = $this->db->error_message();
                }
            }
        }
        // retornamos as informações sobre o insert
        return $response;
    }

}
