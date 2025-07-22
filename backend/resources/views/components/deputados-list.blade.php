@foreach ($deputados as $deputado)
    <div class="border p-4 rounded shadow bg-white">
        <h2 class="text-lg font-bold">{{ $deputado->nome }}</h2>
        <p class="text-sm text-gray-700">{{ $deputado->partido }} - {{ $deputado->uf }}</p>
    </div>
@endforeach
