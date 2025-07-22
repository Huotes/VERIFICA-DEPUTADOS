<?php

namespace App\Http\Controllers;

use App\Models\Deputado;
use Illuminate\Http\Request;

class DeputadoController extends Controller
{
    public function index()
    {
        $deputados = Deputado::paginate(15);
        return view('deputados.index', compact('deputados'));
    }

    public function show(Deputado $deputado)
    {
        $deputado->load('despesas');
        return view('deputados.show', compact('deputado'));
    }
}
