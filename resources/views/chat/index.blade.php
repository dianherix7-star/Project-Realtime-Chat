<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ChatApp</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 h-screen flex overflow-hidden text-white">
<aside class="w-72 bg-gray-800 flex flex-col border-r border-gray-700">
    <div class="p-4 border-b border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-lg">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-green-400">● Online</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-gray-400 hover:text-red-400 text-xs">Keluar</button>
        </form>
    </div>
    <div class="p-3 border-b border-gray-700">
        <button type="button" onclick="document.getElementById('modal-create').classList.remove('hidden')" class="w-full bg-indigo-600 hover:bg-indigo-700 py-2 rounded-lg text-sm font-medium transition">+ Buat Room Baru</button>
    </div>
    <div class="flex-1 overflow-y-auto p-2">
        <p class="text-xs text-gray-500 uppercase px-2 mb-2">Ruang Chat</p>
        @forelse($rooms as $room)
        @php
        $displayName = $room->type === 'private'
            ? ($room->users->where('id', '!=', auth()->id())->first()?->name ?? $room->name)
            : $room->name;
        @endphp
        <button type="button" class="room-btn w-full text-left px-3 py-3 rounded-lg hover:bg-gray-700 transition mb-1"
            id="room-btn-{{ $room->id }}"
            data-room-id="{{ $room->id }}"
            data-room-name="{{ $displayName }}"
            data-room-type="{{ $room->type }}"
            onclick="openRoom(this)">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full {{ $room->type === 'group' ? 'bg-purple-600' : 'bg-blue-600' }} flex items-center justify-center text-xs">
                    {{ $room->type === 'group' ? '👥' : '💬' }}
                </div>
                <div>
                    <p class="text-sm font-medium">{{ $displayName }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $room->type }}</p>
                </div>
            </div>
        </button>
        @empty
        <p class="text-center text-gray-500 text-sm py-4">Belum ada room. Buat room baru untuk mulai chat.</p>
        @endforelse
    </div>
    <div class="p-3 border-t border-gray-700">
        <p class="text-xs text-gray-500 uppercase mb-2">Semua User</p>
        <div class="space-y-1 max-h-36 overflow-y-auto">
            @foreach($users as $user)
            <div class="flex items-center gap-2 px-2 py-1">
                <span class="w-2 h-2 rounded-full {{ $user->is_online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                <span class="text-sm text-gray-300">{{ $user->name }}</span>
                <span class="text-xs ml-auto {{ $user->is_online ? 'text-green-400' : 'text-gray-500' }}">{{ $user->is_online ? 'Online' : 'Offline' }}</span>
            </div>
            @endforeach
        </div>
    </div>
</aside>
<main class="flex-1 flex flex-col">
    <div id="chat-placeholder" class="flex-1 flex items-center justify-center flex-col gap-4">
        <div class="text-6xl">💬</div>
        <h2 class="text-2xl font-bold text-gray-300">Pilih Room untuk Mulai Chat</h2>
    </div>
    <div id="chat-area" class="hidden flex-1 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-lg" id="room-icon">💬</div>
            <div>
                <h2 class="font-bold" id="room-title">Room</h2>
                <p class="text-xs text-gray-400" id="room-type-label"></p>
            </div>
        </div>
        <div id="messages" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-900"></div>
        <div class="p-4 bg-gray-800 border-t border-gray-700">
            <div class="flex gap-3">
                <input type="text" id="message-input" placeholder="Ketik pesan..." class="flex-1 bg-gray-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onkeydown="if(event.key==='Enter') sendMessage()">
                <button type="button" onclick="sendMessage()" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-xl font-medium transition">Kirim</button>
            </div>
        </div>
    </div>
</main>
<div id="modal-create" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold mb-4">Buat Room Baru</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-400 text-sm mb-1">Nama Room</label>
                <input type="text" id="new-room-name" class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Tipe</label>
                <select id="new-room-type" class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="group">Group Chat</option>
                    <option value="private">Private Chat</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Tambahkan Member</label>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($users as $user)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700 px-3 py-2 rounded-lg">
                        <input type="checkbox" value="{{ $user->id }}" class="member-check w-4 h-4">
                        <span>{{ $user->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="createRoom()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 py-3 rounded-lg font-medium transition">Buat Room</button>
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 py-3 rounded-lg font-medium transition">Batal</button>
            </div>
        </div>
    </div>
</div>
<script>
const currentUserId = {{ auth()->id() }};
let currentRoomId = null;
let lastMessageId = 0;
let pollTimer = null;

function openRoom(btn) {
    currentRoomId = btn.dataset.roomId;
    document.getElementById('chat-placeholder').classList.add('hidden');
    document.getElementById('chat-area').classList.remove('hidden');
    document.getElementById('room-title').textContent = btn.dataset.roomName;
    document.getElementById('room-type-label').textContent = btn.dataset.roomType === 'group' ? '👥 Group Chat' : '💬 Private Chat';
    document.getElementById('room-icon').textContent = btn.dataset.roomType === 'group' ? '👥' : '💬';
    document.querySelectorAll('.room-btn').forEach(b => b.classList.remove('bg-gray-700'));
    btn.classList.add('bg-gray-700');
    lastMessageId = 0;
    loadMessages(true);
    startPolling();
}

function startPolling() {
    stopPolling();
    pollTimer = setInterval(() => loadMessages(false), 3000);
}

function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

async function loadMessages(initial) {
    if (!currentRoomId) return;
    const url = initial
        ? '/chat/rooms/' + currentRoomId + '/messages'
        : '/chat/rooms/' + currentRoomId + '/messages?after_id=' + lastMessageId;
    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const messages = await res.json();
        if (initial) {
            document.getElementById('messages').innerHTML = '';
            messages.forEach(msg => { appendMessage(msg); lastMessageId = Math.max(lastMessageId, msg.id); });
            scrollToBottom();
        } else if (messages.length > 0) {
            messages.forEach(msg => { appendMessage(msg); lastMessageId = Math.max(lastMessageId, msg.id); });
            scrollToBottom();
        }
    } catch (e) { console.error(e); }
}

async function sendMessage() {
    const input = document.getElementById('message-input');
    const body = input.value.trim();
    if (!body || !currentRoomId) return;
    input.value = '';
    input.disabled = true;
    try {
        const res = await fetch('/chat/rooms/' + currentRoomId + '/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ body }),
        });
        if (!res.ok) { alert('Gagal mengirim pesan.'); input.value = body; return; }
        const msg = await res.json();
        appendMessage(msg);
        lastMessageId = Math.max(lastMessageId, msg.id);
        scrollToBottom();
    } catch (e) {
        alert('Gagal mengirim pesan.');
        input.value = body;
    } finally {
        input.disabled = false;
        input.focus();
    }
}

function appendMessage(msg) {
    if (document.querySelector('[data-msg-id="' + msg.id + '"]')) return;
    const isMine = msg.user_id === currentUserId;
    const el = document.createElement('div');
    el.className = 'flex ' + (isMine ? 'justify-end' : 'justify-start');
    el.dataset.msgId = msg.id;
    el.innerHTML = '<div class="max-w-xs lg:max-w-md">' +
        (!isMine ? '<p class="text-xs text-gray-400 mb-1 ml-1">' + escapeHtml(msg.user_name) + '</p>' : '') +
        '<div class="' + (isMine ? 'bg-indigo-600' : 'bg-gray-700') + ' px-4 py-2 rounded-2xl"><p class="text-sm">' + escapeHtml(msg.body) + '</p></div>' +
        '<p class="text-xs text-gray-500 mt-1 ' + (isMine ? 'text-right' : 'text-left') + ' mx-1">' +
        new Date(msg.created_at).toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) + '</p></div>';
    document.getElementById('messages').appendChild(el);
}

async function createRoom() {
    const name = document.getElementById('new-room-name').value.trim();
    const type = document.getElementById('new-room-type').value;
    const userIds = Array.from(document.querySelectorAll('.member-check:checked')).map(c => parseInt(c.value));
    if (!name || userIds.length === 0) { alert('Isi nama room dan pilih minimal 1 member!'); return; }
    const res = await fetch('/chat/rooms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ name, type, user_ids: userIds }),
    });
    if (res.ok) location.reload();
    else alert('Gagal membuat room.');
}

function scrollToBottom() {
    const m = document.getElementById('messages');
    m.scrollTop = m.scrollHeight;
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(text));
    return d.innerHTML;
}

window.addEventListener('beforeunload', stopPolling);
</script>
</body>
</html>
