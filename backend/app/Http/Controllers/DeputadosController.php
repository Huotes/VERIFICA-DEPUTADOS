<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deputados;

class DeputadosController extends Controller
{
    public function index()
    {
        $deputados = Deputados::orderBy('nome')->limit(30)->get();
        return view('deputados.index', compact('deputados'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $deputados = Deputados::orderBy('nome')->offset($offset)->limit(30)->get();

        return view('components.deputados-list', compact('deputados'));
    }
}
