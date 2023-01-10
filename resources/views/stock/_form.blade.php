<div class="form-group col-md-6">
    <label for="date_time">Data e Hora:</label>
    <input class="form-control @error('date_time') is-invalid @enderror" type="datetime-local" id="date_time" name="date_time"
    value="{{ isset($movement->date_time) ? date('Y-m-d\TH:i', strtotime($movement->date_time)) : old('date_time') }}">
    @error('date_time')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label for="description">Descrição:</label>
    <input class="form-control @error('description') is-invalid @enderror" type="text" id="description" name="description"
    value="{{ $movement->description ?? old('description') }}">
    @error('description')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="type" id="entry" value="entry"
        {{ !isset($movement->type) || $movement->type == 'entry' || old('type') == 'entry' ? 'checked' : '' }}>
        <label class="form-check-label" for="entry">Entrada</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="type" id="exit" value="exit"
        {{ isset($movement->type) && $movement->type == 'exit' || old('type') == 'exit' ? 'checked' : '' }}>
        <label class="form-check-label" for="exit">Saída</label>
    </div>
</div>
<div class="form-group col-md-9">
    <h5>Produto(s)</h5>
    <hr>
    <div id="products" class="row justify-content-center">
        @if (isset($movement))
            @foreach($movement->products as $movementProduct)
            <div class="col-md-4">
                <label>Nome:</label>
                <select class="form-control" name="idProducts[]">
                    <option></option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $movementProduct->pivot->product_id == $product->id ? 'selected' : ''}}>
                            {{ $product->name }}
                        </option>
                        @endforeach       
                </select>
            </div>
            <div class="col-md-4">
                <label>Quantidade:</label>
                <input class="form-control" type="text" name="quantities[]" value="{{ isset($movementProduct->pivot->quantity) ? $movementProduct->pivot->quantity : '' }}">
            </div>
            <div class="col-md-4">
                <label>Valor unitário (R$):</label>
                <input class="form-control value" type="text" name="values[]" value="{{ isset($movementProduct->pivot->value) ? str_replace('.', ',', $movementProduct->pivot->value) : '' }}">
            </div>
            @endforeach
        @else 
            <div class="col-md-4">
                <label>Nome:</label>
                <select class="form-control" name="idProducts[]">
                    <option></option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                        </option>
                        @endforeach       
                </select>
            </div>
            <div class="col-md-4">
                <label>Quantidade:</label>
                <input class="form-control" type="text" name="quantities[]" value="{{ isset($product->pivot->quantity) ? $product->pivot->quantity : '' }}">
            </div>
            <div class="col-md-4">
                <label>Valor unitário (R$):</label>
                <input class="form-control value" type="text" name="values[]" value="{{ isset($product->pivot->value) ? str_replace('.', ',', $product->pivot->value) : '' }}">
            </div>
        @endif
    </div>
    <button type="button" class="btn btn-sm btn-primary my-3" onclick="addProduct()">
        <i class="fas fa-plus-circle fa-lg"></i>
    </button>
</div>
<button type="submit" class="btn btn-sm btn-primary">SALVAR</button>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>

<script>
    $('input.value').maskMoney({decimal:",", thousands:"."})

    function addProduct() {
        html = `<div class="col-md-4">
            <label>Nome:</label>
            <select class="form-control" name="idProducts[]">
                <option selected>...</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Quantidade:</label>
            <input class="form-control" type="text" name="quantities[]">
        </div>
        <div class="col-md-4">
            <label>Valor unitário (R$):</label>
            <input class="form-control value" type="text" name="values[]">
        </div>`

        $('#products').append(html)
        $('input.value').maskMoney({decimal:",", thousands:"."})
    }
</script>
@endsection
