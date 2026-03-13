<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterBarangController extends Controller
{
    // Legacy index, redirect or show info
    public function index()
    {
        return Inertia::render('Barang/MasterBarang/Index'); 
    }

    // Legacy store, redirect or show info
    public function store(Request $request)
    {
        return Inertia::render('Barang/MasterBarang/Create');
    }
} 