<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SedeController extends Controller
{
    public function elegirsede()
    {
        return Inertia::render('ElegirSedes');
    }

    public function index()
    {
        return Inertia::render('Admin/Sedes');
    }

    public function traer()
    {
        $sedes = Sede::all();
        return response()->json($sedes);
    }

    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'sed_nombre' => 'required|string|max:255',
            'sed_direccion' => 'required|string|max:255',
            'sed_ciudad' => 'required|string|max:255',
            'sed_telefono' => 'required|string|max:20',
            'sed_activo' => 'boolean',
        ]);

        $sede = Sede::create($validarDatos);
        return response()->json($sede, 201);
    }

    public function update(Request $request, Sede $sede)
    {
        $validarDatos = $request->validate([
            'sed_nombre' => 'required|string|max:255',
            'sed_direccion' => 'required|string|max:255',
            'sed_ciudad' => 'required|string|max:255',
            'sed_telefono' => 'required|string|max:20',
            'sed_activo' => 'nullable|boolean',
        ]);

        $sede->update($validarDatos);

        return response()->json(['message' => 'Sede actualizada correctamente', 'sede' => $sede]);
    }

    public function eliminar(Sede $sede)
    {
        $sede->delete();
        return response()->json(['message' => 'Sede eliminada correctamente', 200]);
    }

    public function desactivar(Sede $sede)
    {
        $sede->update(['sed_activo' => 0]);
        return response()->json(['message' => 'Sede desactivada correctamente'], 200);
    }

    public function activar(Sede $sede)
    {
        $sede->update(['sed_activo' => 1]);
        return response()->json(['message' => 'Sede activada correctamente'], 200);
    }
}
