<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
        'estado_verificacion',
        'telefono',
        'ultimo_login',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_registro' => 'datetime',
        'ultimo_login' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    /**
     * Indicamos que no usamos los campos created_at y updated_at automáticos,
     * ya que la tabla usa fecha_registro y se definió sin timestamps genéricos en la migración.
     */
    public $timestamps = false; // Como usamos fecha_registro y lo controla la DB, o lo agregamos manual

    // Relaciones
    public function documentosCi()
    {
        return $this->hasMany(DocumentoCi::class, 'usuario_id');
    }

    public function historialesComoUsuario()
    {
        return $this->hasMany(HistorialVerificacion::class, 'usuario_id');
    }

    public function historialesComoAdmin()
    {
        return $this->hasMany(HistorialVerificacion::class, 'admin_id');
    }
}
