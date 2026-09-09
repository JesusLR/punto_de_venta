<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'chat_id',
        'phone',
        'cliente_id',
        'step',
        'last_keyword',
        'context_data',
        'last_interaction_at'
    ];

    protected $casts = [
        'context_data' => 'array',
        'last_interaction_at' => 'datetime'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(WhatsAppMessage::class, 'conversation_id')->latest();
    }
}
