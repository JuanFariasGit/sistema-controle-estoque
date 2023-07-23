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
    <table id="movements" class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <th>Data e Hora</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php $types = ['entry' => 'Entrada', 'exit' => 'Sáida'] @endphp
            @foreach($movements as $movement)
            @can('user-movement', $movement)
            <tr>
                <td>{!! date('d/m/Y H:i', strtotime($movement->date_time)) !!}</td>
                <td>{{ $types[$movement->type] }}</td>
                <td>{{ $movement->description }}</td>
                <td>R$ {{ number_format($movement->total, 2, ',', '.') }}</td>
                <td>
                <button class="btn btn-sm btn-success mx-1" onclick="viewMovementModal('{{ $movement->id }}')"><i class="far fa-eye fa-lg"></i></button>
                <a href="{{ route('stock.edit', $movement->id) }}"><button class="btn btn-sm btn-primary mx-1">
                    <i class="far fa-edit fa-lg"></i>
                    </button>
                </a>
                <button id="row_{{ $movement->id }}" class="btn btn-sm btn-danger mx-1" onclick="deleteMovementModal('{{ $movement->id }}', '{{ $movement->date_time }}')">
                    <i class="far fa-trash-alt fa-lg"></i>
                </button>
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
<div id="modal-view" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
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
        $.ajax({
            "method": "POST",
            "url": "{{ route('stock.del') }}",
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

    function formatPrice(price) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(price);
    }

    function viewMovementModal(id) {
        let urlView = "{{ route('stock.view-moviment', ':id') }}"

        $.ajax({
            "method": "POST",
            "url": `${urlView.replace(':id', id)}`,
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            success: function(resp) {
                const types = {
                    "entry": "Entrada",
                    "exit": "Saída"
                }
                $('#modal-view').modal('show')
                movement_html = `
                <p><strong>Data e Hora:</strong> ${formatDateTime(resp.movement.date_time)}</p>
                <p><strong>Descrição:</strong> ${resp.movement.description}</p>
                <p><strong>Tipo:</strong> ${types[resp.movement.type]}</p>
                <p><strong>Total:</strong> ${formatPrice(resp.movement.total)}</p>
                `
                products_html = '<strong>Produtos</strong>'
                let i = 0
                const len = resp.products.length
                for (let p of resp.movement.products) {
                    products_html += `
                    <p>Código: ${p.code}</p>
                    <p>Nome: ${p.name}</p>
                    <p>Quatidade: ${p.pivot.quantity}</p>
                    <p>Valor: ${formatPrice(p.pivot.value)}</p>
                    <p>Subtotal: ${formatPrice(p.pivot.value * p.pivot.quantity)}</p>
                    `
                    i++
                    if (i < len) {
                        products_html += '<p>------------------</p>'
                    }
                }
                $('#modal-view').find('.modal-body').html(movement_html + products_html)
            }
        })
    }
</script>
@endsection
