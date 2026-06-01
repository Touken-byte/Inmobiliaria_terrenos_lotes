<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Models\Chat::find($chatId);
    
    if (!$chat) {
        return false;
    }

    // Permitir acceso solo si es comprador, vendedor, o admin
    return (int) $user->id === (int) $chat->comprador_id ||
           (int) $user->id === (int) $chat->vendedor_id ||
           $user->rol === 'admin';
});
