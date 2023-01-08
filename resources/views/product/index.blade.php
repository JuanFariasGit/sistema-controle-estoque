@extends('layouts.app')
@section('title', 'Produtos')

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
        <h1>PRODUTOS</h1>
        <a href="{{ route('product.add') }}"><button class="btn btn-sm btn-primary">
                <i class="fas fa-plus-circle fa-lg"></i>
            </button></a>
    </div>
    <table id="products" class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <th>Código</th>
                <th>Foto</th>
                <th>Nome</th>
                <th>Capacidade</th>
                <th>Estoque atual</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $row)
            @can('user-product', $row)
            <tr>
                <td>{{ $row->code }}</td>
                <td>
                    <img width="150" src="{{ asset('imagens/produtos/' . $row->photo) }}">
                </td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->capacity }}</td>
                <td>{{ $row->current_stock }}</td>
                <td class="d-flex align-items-center justify-content-center">
                    <a class="nav-link" href="{{ route('product.edit', $row->id) }}">
                        <button class="btn btn-sm btn-primary mx-1">
                            <i class="far fa-edit fa-lg"></i>
                        </button>
                    </a>
                    <button 
                        id="row_{{ $row->id }}" 
                        class="btn btn-sm btn-danger mx-1" 
                        onclick="deleteProductModal('{{ $row->id }}', '{{ $row->name }}')"
                    >
                        <i class="far fa-trash-alt fa-lg"></i>
                    </button>
                    <a class="nav-link" href="{{ route('product.download-photo', $row->id) }}">
                        <button class="btn btn-sm btn-primary mx-1">
                            <i class="fas fa-download"></i>
                        </button>
                    </a>
                </td>
            </tr>
            @endcan
            @endforeach
        </tbody>
    </table>
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
            "targets": [1, 5],
            "orderable": false
        }],
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
        $.ajax({
            "method": "POST",
            "url": "{{ route('product.del') }}",
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
</script>
@endsection