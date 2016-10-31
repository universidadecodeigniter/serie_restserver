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

        // Recupera o ID diretamente da URL
        $id = (int) $this->uri->segment(3);
        // Valida o ID
        if ($id <= 0)
        {
            // Lista os usuários
            $usuarios = $this->UsuariosMDL->GetAll('id, nome, email');
        } else {
            // Lista os dados do usuário conforme o ID solicitado
            $usuarios = $this->UsuariosMDL->GetById($id);
        }

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

    /*
     * Essa função vai responder pela rota /api/usuarios sob o método POST
     */
    public function index_put()
    {
        // recupera os dados informado no formulário
        $usuario = $this->put();
        $usuario_id = $this->uri->segment(3);
        // processa o update no banco de dados
        $update = $this->UsuariosMDL->Update('id',$usuario_id, $usuario);
        // define a mensagem do processamento
        $response['message'] = $update['message'];

        // verifica o status do update para retornar o cabeçalho corretamente
        // e a mensagem
        if ($update['status']) {
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /*
     * Essa função vai responder pela rota /api/usuarios sob o método DELETE
     */
    public function index_delete()
    {
        // Recupera o ID diretamente da URL
        $id = (int) $this->uri->segment(3);
        // Valida o ID
        if ($id <= 0)
        {
            // Define a mensagem de retorno
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        // Executa a remoção do registro no banco de dados
        $delete = $this->UsuariosMDL->Delete('id',$id);

        // define a mensagem do processamento
        $response['message'] = $delete['message'];
        // verifica o status do insert para retornar o cabeçalho corretamente
        // e a mensagem
        if ($delete['status']) {
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
    }
}
