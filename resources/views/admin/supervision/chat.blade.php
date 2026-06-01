@extends('layouts.app')

@section('title', 'Auditoría de Chat | Admin')

@push('styles')
<style>
    :root {
        --void:         #050810;
        --deep:         #080d1a;
        --surface:      #0c1326;
        --card:         #0f1830;
        --rim:          rgba(120,160,255,0.10);
        --rim-gold:     rgba(201,168,76,0.22);
        --gold:         #c9a84c;
        --cobalt:       #3d7ef5;
        --text-1:       #eef2fc;
        --text-2:       #8fa3cc;
        --text-3:       #3d5480;
    }

    body { background: var(--void); color: var(--text-1); font-family: 'Outfit', sans-serif; overflow: hidden; }

    .chat-container {
        display: flex; flex-direction: column; height: calc(100vh - 80px); max-width: 1200px; margin: 0 auto; background: var(--deep); border-radius: 20px 20px 0 0; border: 1px solid var(--rim); border-bottom: none; margin-top: 10px; overflow: hidden;
    }

    .chat-header {
        padding: 1.25rem 2rem; background: var(--card); border-bottom: 1px solid var(--rim); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    }

    .chat-header-info { display: flex; align-items: center; gap: 1rem; }
    .chat-header-avatar { width: 45px; height: 45px; border-radius: 50%; background: rgba(201,168,76,0.15); border: 1px solid rgba(201,168,76,0.3); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--gold); }
    .chat-header-text h2 { margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 700; color: var(--gold); }
    .chat-header-text p { margin: 0; font-size: 0.8rem; color: var(--text-2); }

    .chat-header-actions { display: flex; gap: 0.75rem; }
    .chat-header-btn {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; text-decoration: none; border: 1px solid var(--rim); background: var(--surface); color: var(--text-2); transition: 0.2s;
    }
    .chat-header-btn:hover { background: var(--rim); color: var(--text-1); }

    .chat-body {
        flex: 1; padding: 2rem; overflow-y: auto; background: var(--deep); display: flex; flex-direction: column; gap: 1rem;
    }
    .chat-body::-webkit-scrollbar { width: 6px; }
    .chat-body::-webkit-scrollbar-thumb { background: rgba(120,160,255,0.2); border-radius: 10px; }

    .message { display: flex; flex-direction: column; max-width: 70%; position: relative; }
    .message.comprador { align-self: flex-start; }
    .message.vendedor { align-self: flex-end; }

    .bubble {
        padding: 1rem 1.25rem; border-radius: 18px; font-size: 0.95rem; line-height: 1.5; position: relative; word-wrap: break-word;
    }
    .message.vendedor .bubble {
        background: linear-gradient(135deg, var(--cobalt) 0%, #1a4eb8 100%); color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 15px rgba(61,126,245,0.2);
    }
    .message.comprador .bubble {
        background: var(--card); color: var(--text-1); border: 1px solid var(--rim); border-bottom-left-radius: 4px;
    }

    .msg-meta { font-size: 0.7rem; color: var(--text-3); margin-top: 0.3rem; display: flex; align-items: center; gap: 0.5rem; }
    .message.vendedor .msg-meta { align-self: flex-end; }

    .chat-footer {
        padding: 1.25rem 2rem; background: var(--card); border-top: 1px solid var(--rim); flex-shrink: 0; text-align: center; color: var(--text-3); font-size: 0.85rem; font-weight: 500;
    }

    .attachment-box {
        background: rgba(0,0,0,0.2); border-radius: 8px; padding: 0.75rem; margin-top: 0.5rem; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem;
    }
    .attachment-box i { font-size: 1.5rem; color: var(--gold); }
    .attachment-box a { color: var(--text-1); text-decoration: none; word-break: break-all; }
    .attachment-box a:hover { text-decoration: underline; color: var(--cobalt); }
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="chat-header-avatar">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div class="chat-header-text">
                <h2>Modo Auditoría</h2>
                <p>
                    Comprador: <strong>{{ $chat->lead->comprador->nombre }}</strong> | Vendedor: <strong>{{ $chat->lead->vendedor->nombre }}</strong>
                </p>
            </div>
        </div>
        <div class="chat-header-actions">
            <a href="{{ route('admin.supervision.leads') }}" class="chat-header-btn">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="chat-body" id="chatBody">
        @foreach($mensajes as $msg)
            @php $role = $msg->user_id === $chat->comprador_id ? 'comprador' : 'vendedor'; @endphp
            <div class="message {{ $role }}">
                <div class="msg-meta" style="margin-bottom: 0.3rem; margin-top: 0; font-weight: 600;">
                    {{ $msg->usuario->nombre }} ({{ ucfirst($role) }})
                </div>
                <div class="bubble">
                    @if($msg->mensaje)
                        {{ $msg->mensaje }}
                    @endif
                    
                    @if($msg->archivo)
                        <div class="attachment-box">
                            <i class="fa-regular fa-file-pdf"></i>
                            <a href="{{ route('chat.archivo', $msg->id) }}" target="_blank">{{ $msg->nombre_archivo }}</a>
                        </div>
                    @endif
                </div>
                <div class="msg-meta">
                    {{ $msg->created_at->format('d/m/Y H:i') }}
                    @if($msg->leido)
                        <i class="fa-solid fa-check-double" style="color: var(--cobalt)"></i>
                    @else
                        <i class="fa-solid fa-check" style="color: var(--text-3)"></i>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="chat-footer">
        <i class="fa-solid fa-shield-halved" style="margin-right: 5px; color: var(--gold);"></i> Está viendo esta conversación en modo de solo lectura (Auditoría Admin).
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])
<script>
    const chatId = {{ $chat->id }};
    const chatBody = document.getElementById('chatBody');

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    scrollToBottom();

    // Echo WebSocket Listener para Admin (solo lectura)
    window.onload = function() {
        if(window.Echo) {
            window.Echo.private(`chat.${chatId}`)
                .listen('MessageSent', (e) => {
                    const msg = e.mensaje;
                    const isVendedor = msg.user_id === {{ $chat->vendedor_id }};
                    const role = isVendedor ? 'vendedor' : 'comprador';
                    
                    const div = document.createElement('div');
                    div.className = `message ${role}`;
                    
                    let attachmentHtml = '';
                    if (msg.archivo) {
                        attachmentHtml = `
                            <div class="attachment-box">
                                <i class="fa-regular fa-file-pdf"></i>
                                <a href="/chat/archivo/${msg.id}" target="_blank">${msg.nombre_archivo}</a>
                            </div>
                        `;
                    }

                    const date = new Date(msg.created_at);
                    const time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                    div.innerHTML = `
                        <div class="msg-meta" style="margin-bottom: 0.3rem; margin-top: 0; font-weight: 600;">
                            ${msg.usuario.nombre} (${role.charAt(0).toUpperCase() + role.slice(1)})
                        </div>
                        <div class="bubble">
                            ${msg.mensaje ? msg.mensaje : ''}
                            ${attachmentHtml}
                        </div>
                        <div class="msg-meta">
                            ${time}
                            <i class="fa-solid fa-check" style="color: var(--text-3)"></i>
                        </div>
                    `;

                    chatBody.appendChild(div);
                    scrollToBottom();
                });
        }
    };
</script>
@endpush
