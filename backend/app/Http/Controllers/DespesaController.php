<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use Illuminate\Http\Request;

class DespesaController extends Controller
{
    public function index()
    {
        $despesas = Despesa::with('deputado')->paginate(15);
        return view('despesas.index', compact('despesas'));
    }

    public function show(Despesa $despesa)
    {
        return view('despesas.show', compact('despesa'));
    }
}
