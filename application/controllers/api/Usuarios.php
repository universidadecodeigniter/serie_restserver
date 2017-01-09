<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Usuarios extends REST_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('Usuarios_model','UsuariosMDL');

        // Configuração para os limites de requisições (por hora)
        $this->methods['index_get']['limit'] = 10;
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

        // verifica se a foto foi selecionada e faz o processamento
        if (isset($_FILES['avatar'])) {
            $upload = $this->UploadImage('avatar');

            // se ocorreu algum erro no upload, retorna a mensagem de erro
            // em caso de sucesso, armazena o path na variável $usuario
            if ($upload['error']) {
                $response['message'] = $upload['error'];
                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $usuario['avatar'] = $upload['upload_data']['file_name'];
            }
        }

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

    /**
     * Executa o upload da imagem
     * @param string $input_name nome do campo "file" no formulário
     * @return array
     */
    private function UploadImage($input_name)
    {
        // Carrega a biblioteca de upload
        $this->load->library('upload');

        // Define o path do diretório onde a imagem será armazenada
        $path = './uploads/';
        // Verifica se o o path é um diretórios
        // caso não seja, então cria dando permissão de escrita
        if (!is_dir($path)) {
            mkdir($path, 0777, $recursive = true);
        }

        // Configurações para o upload da imagem
        // Diretório para gravar a imagem
        $configUpload['upload_path']   = $path;
        // Tipos de imagem permitidos
        $configUpload['allowed_types'] = 'jpg|jpeg|png';
        // Usar nome de arquivo aleatório, ignorando o nome original do arquivo
        $configUpload['encrypt_name']  = TRUE;

        // Aplica as configurações e inicializa a biblioteca
        $this->upload->initialize($configUpload);

        // Verifica se o upload foi efetuado ou não
        // Em caso de erro retorna a mensagem de erro
        // Em caso de sucesso retorna a mensagem de sucesso
        if ( !$this->upload->do_upload($input_name))
        {
            // Recupera as mensagens de erro e envia o usuário para a home
            $data = array('error' => $this->upload->display_errors());
        }
        else
        {
            // Recupera os dados da imagem enviada
            $data = array('error' => null, 'upload_data' => $this->upload->data());
        }

        return $data;
    }
}
