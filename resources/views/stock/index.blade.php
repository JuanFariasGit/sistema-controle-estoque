@extends('layouts.app')
@section('title', 'Estoque')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link href="{{ asset('css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    @if (session('alert'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('alert') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="d-flex justify-content-between align-items-center">
        <h1>ESTOQUE MOVIMENTAÇÕES</h1>
        <a href="{{ route('stock.add') }}"><button class="btn btn-sm btn-primary">
                <i class="fas fa-plus-circle fa-lg"></i>
            </button></a>
    </div>
    <table id="movements" class="table table-striped table-bordered text-center"></table>
</div>
<div id="modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script>
    const table = $('#movements').DataTable({
        "responsive": true,
        "autoWidth": false,
        "columnDefs": [{
            "targets": [4],
            "orderable": false
        }],
        "ajax": {
            "method": "POST",
            "url": "{{ route('stock.index') }}",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            }
        },
        "columns": [{
                "title": "Data e Hora",
                "data": "date_time",
                "render": function(dateTime) {
                    return formatDateTime(dateTime)
                }
            },
            {
                "title": "Tipo",
                "data": "type",
                "render": function(type) {
                    if (type == 'entry') {
                        return 'Entrada'
                    }
                    return 'Saída'
                }
            },
            {
                "title": "Descrição",
                "data": "description"
            },
            {
                "title": "Total",
                "data": "total",
                "render": function(total) {
                    return formatTotal(total)
                }
            },
            {
                "title": "Ações",
                "data": function(data) {
                    let urlEdit = "{{ route('stock.edit', ':id') }}"

                    html = `<a href="${urlEdit.replace(':id', data.id)}"><button class="btn btn-sm btn-primary mx-1">
                    <i class="far fa-edit fa-lg"></i>
                    </button></a>`
                    html += `<button id="row_${data.id}" class="btn btn-sm btn-danger mx-1" onclick="deleteMovementModal('${data.id}', '${data.date_time}')">
                        <i class="far fa-trash-alt fa-lg"></i>
                    </button>`
                    return html
                }
            }
        ],
        "language": {
            "infoFiltered": "(filtrado do total de _MAX_ entradas)",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "zeroRecords": "Nenhum registro correspondente encontrado",
            "loadingRecords": "Carregando...",
            "lengthMenu": "Mostrar _MENU_ entrada(s)",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
            "search": "Procurar:",
            "paginate": {
                "next": "›",
                "previous": "‹"
            }
        },
    })

    const deleteMovementModal = (id, dateTime) => {
        $('#modal').modal('show')
        $('#modal').find('.modal-header').html(`<h5>Deseja realmente deletar a movimentação de data e hora <strong>${formatDateTime(dateTime)}</strong> ?</h5>`)
        $('#modal').find('.modal-footer').html(`<button class="btn btn-outline-secondary" onclick="closeModal()">Não</button> <button class="btn btn-primary" onclick="deleteMovement('${id}')">Sim</button>`)
    }

    const closeModal = () => {
        $('#modal').modal('hide')
        $('#modal').find('.modal-header').html('')
        $('#modal').find('.modal-footer').html('')
    }

    const deleteMovement = (id) => {
        let urlDel = "{{ route('stock.del', ':id') }}"

        $.ajax({
            "method": "DELETE",
            "url": `${urlDel.replace(':id', id)}`,
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": {
                "id": id
            },
            success: function() {
                table.row($(`#row_${id}`).parents('tr')).remove().draw(false);
                closeModal()
            }
        })
    }

    function formatDateTime(dateTime) {
        let dt = new Date(dateTime)
        return dt.toLocaleString('pt-Br', {
            dateStyle: "short",
            timeStyle: "short"
        })
    }

    function formatTotal(total) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(total);
    }
</script>
@endsection