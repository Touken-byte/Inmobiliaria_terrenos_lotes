<?php

namespace App\Console\Commands;

use App\Models\TerrenoImagen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixImagePaths extends Command
{
    protected $signature = 'fix:image-paths';
    protected $description = 'Corregir rutas de imágenes para que usen storage link';

    public function handle()
    {
        $imagenes = TerrenoImagen::all();
        foreach ($imagenes as $img) {
            $original = $img->ruta_archivo;
            if (!str_starts_with($original, '/storage/') && !str_starts_with($original, 'storage/')) {
                // Si la ruta es algo como "terrenos/archivo.jpg", la convertimos
                if (!str_contains($original, '/storage/')) {
                    $newPath = '/storage/' . ltrim($original, '/');
                    $img->ruta_archivo = $newPath;
                    $img->save();
                    $this->info("Corregido: $original -> $newPath");
                }
            }
        }
        $this->info("Rutas corregidas.");
    }
}