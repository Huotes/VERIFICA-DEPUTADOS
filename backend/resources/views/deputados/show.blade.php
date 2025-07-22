@extends('layouts.app')

@section('title', 'Deputado: ' . $deputado->nome)

@section('content')
<h1>{{ $deputado->nome }}</h1>
<p><strong>Partido:</strong> {{ $deputado->partido }}</p>
<p><strong>UF:</strong> {{ $deputado->uf }}</p>
<p><strong>Email:</strong> {{ $deputado->email }}</p>
<p><strong>Telefone:</strong> {{ $deputado->telefone }}</p>
<img src="{{ $deputado->url_foto }}" alt="Foto de {{ $deputado->nome }}" class="img-thumbnail" width="150" />

<h3>Despesas</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Descrição</th>
        </tr>
    </thead>
    <tbody>
        @foreach($deputado->despesas as $despesa)
        <tr>
            <td>{{ $despesa->tipo }}</td>
            <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
            <td>{{ \Carbon\Carbon::parse($despesa->data)->format('d/m/Y') }}</td>
            <td>{{ $despesa->descricao }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('deputados.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
