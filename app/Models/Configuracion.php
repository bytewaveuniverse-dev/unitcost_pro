<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuracion extends Model
{
    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'descripcion',
        'valor',
        'user_id'
    ];

    /**
     * Relación: Una configuración pertenece a un Usuario.
     * Esto te permitirá hacer: $config->user->name
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
