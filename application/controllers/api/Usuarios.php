<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Usuarios extends REST_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('Usuarios_model','UsuariosMDL');
    }

    /*
     * Essa função vai responder pela rota /api/usuarios sob o método GET
     */
    public function index_get()
    {
        // Lista os usuários
        $usuarios = $this->UsuariosMDL->GetAll('id, nome, email');

        // verifica se existem usuários e faz o retorno da requisição
        // usando os devidos cabeçalhos
        if ($usuarios) {
            $response['data'] = $usuarios;
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $this->response(null,REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /*
     * Essa função vai responder pela rota /api/usuarios sob o método POST
     */
    public function index_post()
    {
        // recupera os dados informado no formulário
        $usuario = $this->post();
        // processa o insert no banco de dados
        $insert = $this->UsuariosMDL->Insert($usuario);
        // define a mensagem do processamento
        $response['message'] = $insert['message'];

        // verifica o status do insert para retornar o cabeçalho corretamente
        // e a mensagem
        if ($insert['status']) {
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
