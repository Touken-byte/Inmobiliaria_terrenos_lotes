<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Promocion;
use App\Models\Terreno;
use App\Models\Alquiler;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromocionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get promotions for Terrenos/Lotes owned by the seller
        $terrenoIds = Terreno::where('usuario_id', $user->id)->pluck('id');
        $alquilerIds = Alquiler::where('user_id', $user->id)->pluck('id');

        $promociones = Promocion::where(function ($query) use ($terrenoIds) {
            $query->where('promotable_type', Terreno::class)
                  ->whereIn('promotable_id', $terrenoIds);
        })->orWhere(function ($query) use ($alquilerIds) {
            $query->where('promotable_type', Alquiler::class)
                  ->whereIn('promotable_id', $alquilerIds);
        })->orderBy('created_at', 'desc')->get();

        return view('vendedor.promociones.index', compact('promociones'));
    }

    public function create()
    {
        $user = Auth::user();

        // Approved Terrenos/Lotes owned by seller
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->where('estado', 'aprobado')
            ->get();

        // Approved Rentals owned by seller
        $alquileres = Alquiler::where('user_id', $user->id)
            ->where('estado_aprobacion', 'aprobado')
            ->get();

        return view('vendedor.promociones.create', compact('terrenos', 'alquileres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'descuento_porcentaje' => 'required|numeric|min:1|max:99',
            'propiedad_tipo_id' => 'required|string', // Format: "ModelClass:ID"
        ], [
            'titulo.required' => 'El título de la promoción es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descuento_porcentaje.required' => 'El porcentaje de descuento es obligatorio.',
            'descuento_porcentaje.min' => 'El descuento debe ser de al menos 1%.',
            'descuento_porcentaje.max' => 'El descuento no puede superar el 99%.',
            'propiedad_tipo_id.required' => 'Debe seleccionar una propiedad.',
        ]);

        $user = Auth::user();
        $parts = explode(':', $request->propiedad_tipo_id);
        if (count($parts) !== 2) {
            return back()->withErrors(['propiedad_tipo_id' => 'Selección de propiedad inválida.']);
        }

        $type = $parts[0];
        $id = $parts[1];

        if ($type === 'Terreno') {
            $propType = Terreno::class;
            $propiedad = Terreno::where('id', $id)->where('usuario_id', $user->id)->first();
        } elseif ($type === 'Alquiler') {
            $propType = Alquiler::class;
            $propiedad = Alquiler::where('id', $id)->where('user_id', $user->id)->first();
        } else {
            return back()->withErrors(['propiedad_tipo_id' => 'Tipo de propiedad no válido.']);
        }

        if (!$propiedad) {
            return back()->withErrors(['propiedad_tipo_id' => 'No se encontró la propiedad seleccionada o no te pertenece.']);
        }

        // Check if there is already a pending or approved promotion for this property
        $exists = Promocion::where('promotable_type', $propType)
            ->where('promotable_id', $id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['propiedad_tipo_id' => 'Esta propiedad ya tiene una promoción activa o en revisión.']);
        }

        $promocion = Promocion::create([
            'promotable_type' => $propType,
            'promotable_id' => $id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'descuento_porcentaje' => $request->descuento_porcentaje,
            'estado' => 'pendiente',
        ]);

        // Registrar en Auditoría (OBS-A04)
        $tipoStr = $type === 'Terreno' ? ($propiedad->tipo === 'lote' ? 'lote' : 'terreno') : 'alquiler';
        $propiedadNombre = $propiedad->nombre ?? $propiedad->titulo ?? 'Propiedad';
        Auditoria::registrar(
            'postular_promocion',
            $type === 'Terreno' ? 'terreno' : 'alquiler',
            $propiedad->id,
            "El vendedor {$user->nombre} postuló una promoción del {$promocion->descuento_porcentaje}% para el {$tipoStr} '{$propiedadNombre}'",
            $user->id
        );

        return redirect()->route('vendedor.promociones.index')
            ->with('success', '✅ Promoción postulada con éxito. Está en espera de moderación.');
    }
}
