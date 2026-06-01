<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $rooms = Auth::user()->chatRooms()->with('users')->get();
        $users = User::where('id', '!=', Auth::id())->get();

        return view('chat.index', compact('rooms', 'users'));
    }

    public function getMessages(Request $request, $roomId)
    {
        $room = ChatRoom::with('users')->findOrFail($roomId);

        if (!$this->userInRoom($room)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = $room->messages()->with('user')->latest();

        if ($request->has('after_id')) {
            $query->where('id', '>', (int) $request->after_id);
        } else {
            $query->take(50);
        }

        $messages = $query->get()->reverse()->values();

        return response()->json($messages->map(fn ($msg) => $this->formatMessage($msg)));
    }

    public function sendMessage(Request $request, $roomId)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $room = ChatRoom::with('users')->findOrFail($roomId);

        if (!$this->userInRoom($room)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'user_id'      => Auth::id(),
            'chat_room_id' => $roomId,
            'body'         => $request->body,
        ]);

        $message->load('user');

        // Dispatch event untuk broadcasting realtime
        // Dibungkus try-catch agar jika Reverb server tidak aktif,
        // pesan tetap berhasil dikirim dan disimpan
        try {
            MessageSent::dispatch($message);
        } catch (\Throwable $e) {
            \Log::warning('Broadcasting gagal (Reverb mungkin tidak aktif): ' . $e->getMessage());
        }

        return response()->json($this->formatMessage($message));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'required|in:private,group',
            'user_ids' => 'required|array|min:1',
        ]);

        $room = ChatRoom::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        $userIds = array_unique(array_merge($request->user_ids, [Auth::id()]));
        $room->users()->attach($userIds);

        return response()->json($room->load('users'));
    }

    public function openDirectChat($userId)
    {
        $targetUser = User::findOrFail($userId);
        $myId       = Auth::id();

        // Cari private room yang sudah ada antara dua user ini
        $existingRoom = ChatRoom::where('type', 'private')
            ->whereHas('users', fn ($q) => $q->where('users.id', $myId))
            ->whereHas('users', fn ($q) => $q->where('users.id', $targetUser->id))
            ->withCount('users')
            ->having('users_count', 2)
            ->first();

        if ($existingRoom) {
            $room = $existingRoom->load('users');
        } else {
            // Buat room baru
            $room = ChatRoom::create([
                'name' => 'Private: ' . Auth::user()->name . ' & ' . $targetUser->name,
                'type' => 'private',
            ]);
            $room->users()->attach([$myId, $targetUser->id]);
            $room->load('users');
        }

        $displayName = $targetUser->name;

        return response()->json([
            'id'           => $room->id,
            'name'         => $displayName,
            'type'         => 'private',
        ]);
    }

    private function userInRoom(ChatRoom $room): bool
    {
        return $room->users->contains('id', Auth::id());
    }

    private function formatMessage(Message $msg): array
    {
        return [
            'id'         => $msg->id,
            'body'       => $msg->body,
            'user_id'    => $msg->user_id,
            'user_name'  => $msg->user->name,
            'created_at' => $msg->created_at->toDateTimeString(),
        ];
    }
}
