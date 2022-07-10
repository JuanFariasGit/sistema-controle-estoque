@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link href="{{ asset('css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center"> 
        <h1>PRODUTOS</h1>
        <a href="{{ route('product.add') }}"><button class="btn btn-sm btn-primary">
            <i class="fas fa-plus-circle fa-lg"></i>
        </button></a>
    </div>
    <table id="products" class="table table-striped table-bordered text-center"></table>
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
    const table = $('#products').DataTable({
        "responsive": true,
        "autoWidth": false,
        "columnDefs": [{
        "targets": [ 1, 5 ],
        "orderable": false 
        }],
        "ajax": {
            "method": "POST",
            "url": "{{ route('product.index') }}",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            }
        },
        "columns": [
            {
                "title": "Código", 
                "data":"code"
            },
            {
                "title": "Foto", 
                "data":"photo",
                "render": function(photo) {
                    return `<image height="100" src="{{ asset('imagens/produtos/${photo}') }}">`;
                }
            },
            {
                "title": "Nome", 
                "data":"name"
            },
            {
                "title": "Capacidade (ml)", 
                "data":"capacity"
            },
            {
                "title": "Estoque atual", 
                "data":"current_stock"
            },
            {
                "title": "Ações", 
                "data": function(data) {
                    let urlEdit = "{{ route('product.edit', ':id') }}"
                    let urlDownload = "{{ route('product.download-photo', ':id') }}"

                    html = `<a href="${urlEdit.replace(':id', data.id)}"><button class="btn btn-sm btn-primary mx-1">
                    <i class="far fa-edit fa-lg"></i>
                    </button></a>`
                    html += `<button id="row_${data.id}" class="btn btn-sm btn-danger mx-1" onclick="deleteProductModal('${data.id}', '${data.name}')">
                        <i class="far fa-trash-alt fa-lg"></i>
                    </button>`
                    html += `<a href="${urlDownload.replace(':id', data.id)}"><button class="btn btn-sm btn-primary mx-1">
                    <i class="fas fa-download"></i>
                    </button></a>`
                    return html
                }
            }
        ],
            "language": {
                "infoFiltered":   "(filtrado do total de _MAX_ entradas)",
                "infoEmpty":      "Mostrando 0 a 0 de 0 entradas",
                "zeroRecords": "Nenhum registro correspondente encontrado",
                "loadingRecords": "Carregando...",
                "lengthMenu": "Mostrar _MENU_ entrada(s)",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                "search": "Procurar:",
                "paginate": {
                "next":  "›",
                "previous": "‹"
            }
        },   
    })

    const deleteProductModal = (id, item) => {
        $('#modal').modal('show')
        $('#modal').find('.modal-header').html(`<h5><strong class="text-danger">ATENÇÃO!</strong> Todas as entradas e saídas que tem como produto <strong>${item}</strong> terá o valor total modificado. Deseja realmente deletar ?</h5>`)
        $('#modal').find('.modal-footer').html(`<button class="btn btn-outline-secondary" onclick="closeModal()">Não</button> <button class="btn btn-primary" onclick="deleteProduct('${id}')">Sim</button>`)
    }

    const closeModal = () => {
        $('#modal').modal('hide')
        $('#modal').find('.modal-header').html('')
        $('#modal').find('.modal-footer').html('')
    }

    const deleteProduct = (id) => {
        let urlDel = "{{ route('product.del', ':id') }}" 

        $.ajax ({
            "method": "DELETE",
            "url": `${urlDel.replace(':id', id)}`,
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": {"id": id},
            success: function() {
                table.row($(`#row_${id}`).parents('tr')).remove().draw(false);
                closeModal()
            }
        })
    }
</script>
@endsection
