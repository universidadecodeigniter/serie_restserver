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
        ]
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
                $('#formUsuario')[0].reset();
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
