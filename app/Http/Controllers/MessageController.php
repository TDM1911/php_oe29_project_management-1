<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Room;
use App\Events\SendMessage;
use App\Events\DeleteMessage;
use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MessageRequest $request, Room $room)
    {
        $message = Message::create([
            'content' => $request->content,
            'user_id' => auth()->user()->id,
            'room_id' => $room->id,
        ]);
        broadcast(new SendMessage($message->load(['user','room'])))->toOthers();

        return $message->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        $messages = $room->messages()->with(['user','room'])->orderBy('created_at', 'asc')->get();

        return ['messages' => $messages];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        broadcast(new DeleteMessage($message->load(['user','room'])))->toOthers();
        $message->delete();

        return true;
    }
}
