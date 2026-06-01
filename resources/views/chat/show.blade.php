@extends(auth()->user()->rol === 'comprador' ? 'layouts.comprador' : 'layouts.app')

@section('title', 'Negociación | TerrenoSur')

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
        --emerald:      #1dba7e;
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
    .chat-header-avatar { width: 45px; height: 45px; border-radius: 50%; background: var(--surface); border: 1px solid var(--rim); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--gold); }
    .chat-header-text h2 { margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 700; }
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

    .message { display: flex; flex-direction: column; max-width: 72%; position: relative; }
    .message.sent { align-self: flex-end; }
    .message.received { align-self: flex-start; }
    .message-row { display: flex; align-items: flex-end; gap: 0.75rem; }
    .message.received .message-row { align-items: flex-start; }
    .message.sent .message-row { justify-content: flex-end; }
    .message-avatar { width: 38px; height: 38px; flex-shrink: 0; border-radius: 50%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; color: var(--text-1); font-weight: 700; font-size: 0.95rem; }
    .message-content { display: flex; flex-direction: column; gap: 0.35rem; }

    .bubble {
        padding: 1rem 1.25rem; border-radius: 18px; font-size: 0.95rem; line-height: 1.5; position: relative; word-wrap: break-word;
    }
    .message.sent .bubble {
        background: #6366f1; color: white; border-radius: 18px 18px 4px 18px; box-shadow: 0 12px 25px rgba(99,102,241,0.18);
    }
    .message.received .bubble {
        background: rgba(255,255,255,0.08); color: var(--text-1); border: 1px solid rgba(255,255,255,0.12); border-radius: 18px 18px 18px 4px;
    }

    .msg-meta { font-size: 0.7rem; color: var(--text-3); margin-top: 0.3rem; display: flex; align-items: center; gap: 0.5rem; }
    .msg-meta .read-tick { font-weight: 700; }
    .message.sent .msg-meta { align-self: flex-end; color: var(--text-2); }
    .flash-banner { width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 0.95rem 1rem; margin-bottom: 0.75rem; border-radius: 0 0 16px 16px; font-weight: 600; }
    .flash-success { background: rgba(16,185,129,0.16); color: #059669; border: 1px solid rgba(16,185,129,0.25); }
    .flash-info { background: rgba(59,130,246,0.16); color: #2563eb; border: 1px solid rgba(59,130,246,0.25); }

    .chat-footer {
        padding: 1.25rem 2rem; background: var(--card); border-top: 1px solid var(--rim); flex-shrink: 0;
    }
    .chat-input-wrapper {
        display: flex; align-items: flex-end; gap: 1rem; background: var(--surface); border: 1px solid var(--rim); border-radius: 20px; padding: 0.5rem 1rem;
    }
    .chat-input {
        flex: 1; background: transparent; border: none; color: var(--text-1); padding: 0.8rem 0; font-family: 'Outfit', sans-serif; resize: none; max-height: 120px; overflow-y: auto; outline: none;
    }
    .chat-input::placeholder { color: var(--text-3); }
    .chat-input::-webkit-scrollbar { width: 4px; }
    .chat-input::-webkit-scrollbar-thumb { background: var(--rim); border-radius: 4px; }

    .chat-attach-btn {
        background: transparent; border: none; color: var(--gold); font-size: 1.4rem; cursor: pointer; padding: 0.5rem; transition: 0.2s;
        filter: drop-shadow(0 0 5px rgba(201,168,76,0.3));
    }
    .chat-attach-btn:hover { color: var(--text-1); transform: scale(1.1); }

    .chat-send-btn {
        background: var(--cobalt); color: white; border: none; width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .chat-send-btn:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 5px 15px rgba(61,126,245,0.4); }
    .chat-send-btn:active { transform: scale(0.9); }

    .file-preview {
        background: var(--card-raised); border: 1px solid var(--rim); border-top-left-radius: 15px; border-top-right-radius: 15px;
        padding: 0.8rem 1.2rem; display: none; align-items: center; justify-content: space-between; border-bottom: none;
        animation: slideUp 0.3s ease;
    }
    .file-preview.active { display: flex; }
    
    @keyframes slideUp {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .attachment-box {
        display: flex; align-items: center; gap: 0.8rem; background: rgba(255,255,255,0.05);
        padding: 0.8rem; border-radius: 12px; margin-top: 0.5rem; border: 1px solid rgba(255,255,255,0.1);
        transition: 0.2s;
    }
    .attachment-box:hover { background: rgba(255,255,255,0.1); }
    .attachment-box i { font-size: 1.5rem; color: var(--gold); }
    .attachment-box a { color: var(--text-1); text-decoration: none; font-size: 0.85rem; font-weight: 500; }
    .attachment-box a:hover { text-decoration: underline; color: var(--cobalt); }

</style>
@endpush

@section('content')
<div class="chat-container">
    @if(session('success') || session('info'))
        <div id="chatFlashBanner" class="flash-banner {{ session('success') ? 'flash-success' : 'flash-info' }}">
            <span>{{ session('success') ?? session('info') }}</span>
        </div>
    @endif
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="chat-header-avatar">
                @if(auth()->user()->id === $chat->vendedor_id)
                    <i class="fa-solid fa-user"></i>
                @else
                    <i class="fa-solid fa-building"></i>
                @endif
            </div>
            <div class="chat-header-text">
                <h2>{{ auth()->user()->id === $chat->vendedor_id ? $chat->lead->comprador->nombre : $chat->lead->vendedor->nombre }}</h2>
                <p>
                    @if($chat->lead->terreno_id)
                        Negociando Terreno en {{ Str::limit($chat->lead->terreno->ubicacion ?? 'N/D', 25) }}
                    @elseif($chat->lead->alquiler_id)
                        Negociando Alquiler en {{ Str::limit($chat->lead->alquiler->ubicacion ?? 'N/D', 25) }}
                    @endif
                    <span class="badge" style="background: rgba(29,186,126,0.15); color: var(--emerald); padding: 0.2rem 0.5rem; margin-left: 0.5rem; border-radius: 8px;">{{ ucfirst($chat->lead->estado) }}</span>
                </p>
            </div>
        </div>
        <div class="chat-header-actions">
            @if($chat->lead->terreno_id)
                <a href="{{ route('catalogo.detalle', $chat->lead->terreno_id) }}" target="_blank" class="chat-header-btn">
                    <i class="fa-solid fa-map"></i> Ver Terreno
                </a>
            @elseif($chat->lead->alquiler_id)
                <a href="{{ route('catalogo.detalle.alquiler', $chat->lead->alquiler_id) }}" target="_blank" class="chat-header-btn">
                    <i class="fa-solid fa-house"></i> Ver Alquiler
                </a>
            @endif
            <a href="{{ auth()->user()->rol === 'vendedor' ? route('vendedor.leads.index') : route('comprador.leads') }}" class="chat-header-btn">
                <i class="fa-solid fa-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="chat-body" id="chatBody">
        @foreach($mensajes as $msg)
            @php
                $isMe = $msg->user_id === auth()->id();
                $otherName = $msg->user_id === $chat->vendedor_id
                    ? ($chat->lead->vendedor->nombre ?? 'V')
                    : ($chat->lead->comprador->nombre ?? 'C');
                $otherInitial = strtoupper(substr($otherName, 0, 1));
            @endphp
            <div class="message {{ $isMe ? 'sent' : 'received' }}">
                <div class="message-row">
                    @if(!$isMe)
                        <div class="message-avatar">{{ $otherInitial }}</div>
                    @endif
                    <div class="message-content">
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
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                            @if($isMe)
                                <span class="read-tick" style="color: {{ $msg->leido ? 'var(--cobalt)' : 'var(--text-3)' }};">{{ $msg->leido ? '✓✓' : '✓' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($chat->estado !== 'cerrado')
    <style>
        .file-preview {
            background: #0f1830; border: 1px solid rgba(120,160,255,0.2); border-radius: 12px 12px 0 0;
            padding: 10px 15px; display: none; align-items: center; justify-content: space-between;
            border-bottom: none; animation: slideIn 0.3s ease;
        }
        .file-preview.active { display: flex; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .remove-file-btn {
            background: rgba(255,77,77,0.2); color: #ff4d4d; border: none; border-radius: 50%;
            width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .remove-file-btn:hover { background: #ff4d4d; color: white; }
    </style>

    <div class="chat-footer">
        <div id="filePreview" class="file-preview">
            <span style="color: #eef2fc; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-lines" style="color: #c9a84c;"></i>
                <span id="fileName"></span>
            </span>
            <button type="button" class="remove-file-btn" onclick="window.removeFile()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="chatForm" class="chat-input-wrapper" onsubmit="event.preventDefault(); window.enviarMensaje();" style="gap: 0.5rem; padding: 0.5rem;">
            <input type="file" id="fileInput" name="archivo" style="display: none" accept=".jpg,.jpeg,.png,.pdf">
            <button type="button" onclick="document.getElementById('fileInput').click()" 
                    style="background: var(--cobalt); color: white; border: none; border-radius: 10px; padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: 0.2s;">
                <i class="fa-solid fa-paperclip"></i> + SUBIR PDF / FOTO
            </button>
            <textarea id="messageInput" name="mensaje" class="chat-input" placeholder="Escribe un mensaje..." rows="1" style="color: white !important; min-width: 100px;"></textarea>
            <button type="button" id="sendBtn" class="chat-send-btn" onclick="window.enviarMensaje()">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
    @else
    <div class="chat-footer" style="text-align: center; color: var(--text-3);">
        <i class="fa-solid fa-lock" style="margin-right: 5px;"></i> Esta negociación ha sido cerrada.
    </div>
    @endif
</div>

<script>
(function() {
    if (window.chatIniciado) return;
    window.chatIniciado = true;

    var chatId = {{ $chat->id }};
    var userId = {{ auth()->id() }};

    window.enviarMensaje = function() {
        var messageInput = document.getElementById('messageInput');
        var fileInput = document.getElementById('fileInput');
        var form = document.getElementById('chatForm');
        
        if (!messageInput || !form) return;
        if (!messageInput.value.trim() && !fileInput.value) return;

        var formData = new FormData(form);
        var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        var msgBackup = messageInput.value;
        messageInput.value = '';
        messageInput.style.height = 'auto';
        
        if (typeof window.removeFile === 'function') window.removeFile();

        fetch(`/chat/${chatId}/mensaje`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if(data.status === 'success') {
                window.appendMessage(data.mensaje, true);
            } else {
                alert('Error: ' + (data.error || 'Desconocido'));
                messageInput.value = msgBackup;
            }
        })
        .catch(function(err) {
            console.error(err);
            messageInput.value = msgBackup;
        });
    };

    window.removeFile = function() {
        var fileInput = document.getElementById('fileInput');
        var filePreview = document.getElementById('filePreview');
        if (fileInput) fileInput.value = '';
        if (filePreview) filePreview.style.display = 'none';
    };

    var fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var filePreview = document.getElementById('filePreview');
            var fileName = document.getElementById('fileName');
            if (this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
                filePreview.style.display = 'flex';
            }
        });
    }

    window.appendMessage = function(msg, isMe) {
        var chatBody = document.getElementById('chatBody');
        if (!chatBody || !msg) return;

        var div = document.createElement('div');
        div.className = 'message ' + (isMe ? 'sent' : 'received');
        
        var attachmentHtml = msg.archivo ? 
            '<div class="attachment-box"><i class="fa-regular fa-file-pdf"></i><a href="/chat/archivo/' + msg.id + '" target="_blank">' + (msg.nombre_archivo || 'Archivo') + '</a></div>' : '';

        var date = new Date(msg.created_at);
        var time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        var readTick = isMe ? ' <span class="read-tick" style="color: ' + (msg.leido ? 'var(--cobalt)' : 'var(--text-3)') + ';">' + (msg.leido ? '✓✓' : '✓') + '</span>' : '';
        var avatarHtml = !isMe ? '<div class="message-avatar">V</div>' : '';

        div.innerHTML = '<div class="message-row">' +
            avatarHtml +
            '<div class="message-content">' +
                '<div class="bubble">' + (msg.mensaje ? msg.mensaje : '') + attachmentHtml + '</div>' +
                '<div class="msg-meta">' + time + readTick + '</div>' +
            '</div>' +
        '</div>';

        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight;
    };

    var lastId = {{ $mensajes->last() ? $mensajes->last()->id : 0 }};
    
    function poll() {
        fetch('/chat/' + chatId + '/nuevos?last_id=' + lastId, {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.mensajes && data.mensajes.length > 0) {
                data.mensajes.forEach(function(msg) {
                    if (msg.user_id !== userId) {
                        window.appendMessage(msg, false);
                    }
                    lastId = Math.max(lastId, msg.id);
                });
            }
        })
        .catch(function(e) { console.warn("Polling error", e); });
    }

    setInterval(poll, 3000);

    var input = document.getElementById('messageInput');
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                window.enviarMensaje();
            }
        });
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    var chatBody = document.getElementById('chatBody');
    if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;

    var flashBanner = document.getElementById('chatFlashBanner');
    if (flashBanner) {
        setTimeout(function() {
            flashBanner.style.opacity = '0';
            flashBanner.style.transition = 'opacity 0.4s ease';
            setTimeout(function() { flashBanner.remove(); }, 400);
        }, 3000);
    }
})();
</script>
@endsection
