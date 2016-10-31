<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // carregamos o helper que irá fazer o gerenciamento da criptografia da senha
        $this->load->helper('passw_service');
    }

    /*
     * Método que irá listar todos os usuários
     * recebe como parâmetro os campos a serem retornados
     */
    public function GetAll($fields = '*')
    {
        $this->db->select($fields)
        ->from('usuarios')
        ->order_by('nome','ASC');

        return $this->db->get()->result_array();
    }

    /*
     * Método que irá listar todos os usuários
     * recebe como parâmetro os campos a serem retornados
     */
    public function GetById($id, $fields = '*')
    {
        if ($id <= 0) {
            return array();
        }

        $this->db->select($fields)
        ->from('usuarios')
        ->where('id',$id)
        ->order_by('nome','ASC');

        return $this->db->get()->result_array();
    }

    /*
     * Método que irá fazer a validação dos dados e processar o insert na tabela
     * recebe como parâmetro o array com os dados vindos do formulário
     */
    function Insert($dados)
    {
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

    /*
     * Método que irá fazer a validação dos dados e processar o update na tabela
     * recebe como parâmetro o array com os dados vindos do formulário
     */
    function Update($field, $value, $dados)
    {
        if (!isset($dados) || !isset($field) || !isset($dados)) {
            $response['status'] = false;
            $response['message'] = "Dados não informados.";
        } else {
            // setamos os dados que devem ser validados
            $this->form_validation->set_data($dados);
            // definimos as regras de validação
            $this->form_validation->set_rules('nome','Nome','required|min_length[2]|trim');
            $this->form_validation->set_rules('email','Email','required|valid_email|trim');
            $this->form_validation->set_rules('senha','Senha','min_length[6]|trim');
            $this->form_validation->set_rules('biografia','Biografia','trim');

            // executamos a validação e verificamos o seu retorno
            // caso haja algum erro de validação, define no array $response
            // o status e as mensagens de erro
            if ($this->form_validation->run() === false) {
                $response['status'] = false;
                $response['message'] = validation_errors();
            } else {
                if (isset($dados['senha'])) {
                    // criptografamos a senha
                    $dados['senha'] = EncryptPassw($dados['senha']);
                }

                //executamos o update
                $this->db->where($field, $value);
                $status = $this->db->update('usuarios', $dados);
                // verificamos o status do insert
                if ($status) {
                    $response['status'] = true;
                    $response['message'] = "Usuário atualizado com sucesso.";
                } else {
                    $response['status'] = false;
                    $response['message'] = $this->db->error_message();
                }
            }
        }
        // retornamos as informações sobre o update
        return $response;
    }


    /*
     * Método que irá fazer a remoção dos dados
     * Recebe como parâmetro o campo e o valor a serem usados na cláusula where
     */
    function Delete($field, $value)
    {

        if (is_null($field) || is_null($value)) {
            $response['status'] = false;
            $response['message'] = "Dados não informados.";
        } else {
            // definimos o campo que é o parâmetro para remoção
            $this->db->where($field, $value);
            // removemos o registro e armazenamos o status do procedimento
            $status =  $this->db->delete('usuarios');

            // verificamos o status do procedimento de remoção
            if ($status) {
                $response['status'] = true;
                $response['message'] = "Usuário removido com sucesso.";
            } else {
                $response['status'] = false;
                $response['message'] = $this->db->error_message();
            }
        }
        // retornamos as informações sobre o status do procedimento
        return $response;
    }

}
