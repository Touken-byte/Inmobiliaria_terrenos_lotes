<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Lead;
use App\Models\Chat;
use App\Models\Mensaje;

class Usuario extends Authenticatable implements MustVerifyEmail
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
        'email_verified_at',
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
        'email_verified_at' => 'datetime',
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

    public function leadsComoComprador()
    {
        return $this->hasMany(Lead::class, 'comprador_id');
    }

    public function leadsComoVendedor()
    {
        return $this->hasMany(Lead::class, 'vendedor_id');
    }

    public function chatsComoComprador()
    {
        return $this->hasMany(Chat::class, 'comprador_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, $this->rol === 'vendedor' ? 'vendedor_id' : 'comprador_id');
    }

    /**
     * Devuelve el total de mensajes no leídos para el usuario.
     */
    public function unreadMessagesCount()
    {
        return Mensaje::whereHas('chat', function($query) {
                $query->where($this->rol === 'vendedor' ? 'vendedor_id' : 'comprador_id', $this->id);
            })
            ->where('user_id', '!=', $this->id)
            ->where('leido', false)
            ->count();
    }
    
    // Pegar DESPUÉS de chatsComoVendedor() y ANTES del último } de la clase

    public function favoritos()
    {
        return $this->hasMany(\App\Models\Favorito::class, 'usuario_id');
    }

    public function chatsComoVendedor()
    {
        return $this->hasMany(Chat::class, 'vendedor_id');
    }

    public function terrenos()
    {
        return $this->hasMany(\App\Models\Terreno::class, 'usuario_id');
    }

    public function alquileres()
    {
        return $this->hasMany(\App\Models\Alquiler::class, 'user_id');
    }
}
