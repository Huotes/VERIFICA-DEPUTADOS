@extends('layouts.app')

@section('title', 'Lista de Deputados')

@section('content')
<h1>Deputados</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Partido</th>
            <th>UF</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Detalhes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($deputados as $deputado)
            <tr>
                <td>{{ $deputado->nome }}</td>
                <td>{{ $deputado->partido }}</td>
                <td>{{ $deputado->uf }}</td>
                <td>{{ $deputado->email }}</td>
                <td>{{ $deputado->telefone }}</td>
                <td><a href="{{ route('deputados.show', $deputado) }}" class="btn btn-sm btn-primary">Ver</a></td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $deputados->links() }}

@endsection
