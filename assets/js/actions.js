$(document).ready(function() {

    /* Configurações para o datatables */
    var el_datatable = $('#DataTableUsuarios').DataTable({
        "language":{
            "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Portuguese-Brasil.json"
        },
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": $('#DataTableUsuarios').data('url'),
            "type": "GET"
        },
        "columns": [
            { "data": "id" },
            { "data": "nome" },
            { "data": "email" }
        ],
        select: true
    });

    /* Abre a modal para edição dos dados do usuário */
    $('#DataTableUsuarios').on( 'click', 'tbody tr', function () {
        if ( el_datatable.row( this, { selected: true } ).any() ) {
            var usuario_data = el_datatable.row( this, { selected: true } ).data();
            ShowModalEdit(usuario_data.id);
        }
    });

    /* action para o botão que atualiza a tabela com os dados */
    $("#btn-atualiza-datatable").click(function(){
        el_datatable.ajax.reload();
        return false;
    });

    /* action para o botão que dispara a requisição para a API, enviando os
    dados do formulário */
    $('#btn-submit-form').click(function(){
        var url_action = $('#formUsuario').attr('action');
        var form_data = $('#formUsuario').serialize();
        var form_method = $('#formUsuario').attr('method');

        $.ajax({
            url: url_action,
            data: form_data,
            dataType: 'json',
            method: form_method,
            beforeSend: function(){
                $('#btn-submit-form').prop('disabled',true);
            },
            success: function(response){
                $('#formUsuarioModal').modal('hide');
                MessageDialog('Informação',response.message);
                el_datatable.ajax.reload();
                $('#btn-submit-form').prop('disabled',false);
                ResetForm(form_method);
                return false;
            },
            error: function(response){
                MessageDialog('Atenção',response.responseJSON.message);
                $('#btn-submit-form').prop('disabled',false);
                return false;
            }
        });
        return false;
    });

    /* Faz a remoção do usuário selecionado */
    $('#btn-remover-usuario').click(function(){
        var confirmacao = confirm('Deseja realmente excluir esse usuário?');
        if(confirmacao){
            var api_url = location.protocol + "//" + location.host + "/unici_restserver/api/usuarios/"+$(this).data("id_usuario");

            $.ajax({
                url: api_url,
                type: 'DELETE',
                dataType: 'json',
                success: function (response) {
                    $('#formUsuarioModal').modal('hide');
                    MessageDialog('Informação',response.message);
                    $('#btn-remover-usuario').addClass('hidden');
                    $('#btn-remover-usuario').removeData("id_usuario");
                    el_datatable.ajax.reload();
                    return false;
                },
                error: function(response){
                    MessageDialog('Atenção',response.responseJSON.message);
                    return false;
                }
            });
        }
        return false;
    });

});

/* Função criada para poder exibir as mensagens de sucesso ou erro */
function MessageDialog(title,message){
    var dialog = bootbox.dialog({
        title: title,
        message: message,
        backdrop: true
    });
    dialog.init(function(){
        setTimeout(function(){
            dialog.modal('hide');
        }, 5000);
    });
}

// Função criada para manipular o formulário, inserindo os dados do usuário para edição
function ShowModalEdit(usuario_id){

    if(usuario_id === undefined) {
        MessageDialog('Atenção','O usuário não foi selecionado.');
        return false;
    }

    var api_url = location.protocol + "//" + location.host + "/unici_restserver/api/usuarios/"+usuario_id;
    $.ajax({
        url: api_url,
        dataType: 'json',
        method: "GET",
        success: function(response){
            console.log(response.data[0].id);
            $('#formUsuario').attr('method','PUT');
            $('#formUsuario').attr('action',api_url);
            $('#formUsuario #nome').val(response.data[0].nome);
            $('#formUsuario #email').val(response.data[0].email);
            $('#formUsuario #email').attr('readonly', true);
            $('#formUsuario #senha').attr('required', false);
            $('#formUsuario #biografia').val(response.data[0].biografia);
            $('#formUsuarioModal #myModalLabel').text('Editar Usuário');
            $('#btn-remover-usuario').removeClass('hidden');
            $('#btn-remover-usuario').data("id_usuario",response.data[0].id);
            $('#formUsuarioModal').modal('show');
            return false;
        },
        error: function(response){
            MessageDialog('Atenção',response.responseJSON.message);
            return false;
        }
    });
}

// Função que irá executar o reset do formulário, voltando os seus campos e configurações para o estado inicial
function ResetForm(form_method){
    if (form_method == "PUT") {
        var api_url = location.protocol + "//" + location.host + "/unici_restserver/api/usuarios";
        $('#formUsuario').attr('method','POST');
        $('#formUsuario').attr('action',api_url);
        $('#formUsuario #email').attr('readonly', false);
        $('#formUsuario #senha').attr('required', true);
        $('#formUsuario')[0].reset();
        $('#formUsuarioModal #myModalLabel').text('Novo Usuário');
        $('#btn-remover-usuario').addClass('hidden');
    } else {
        $('#formUsuario')[0].reset();
    }
}
