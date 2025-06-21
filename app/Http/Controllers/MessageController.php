<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NewMessageEvent;
use App\Models\Conversation;

class MessageController extends Controller
{
    //
    public function AllUsers()
    {
        $users = User::where('id', '!=', Auth::id())->paginate(12); // 12 users per page
        return view('try.dashboard', compact('users'));
    }

    public function AllUsersAPI()
    {
        $users = User::where('id', '!=', Auth::id())->paginate(12); // 12 users per page
        return response()->json(['users' => $users]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $userquery = $request->search;
        $users = User::where('name', 'like', '%' . $userquery . '%')->where('id', '!=', Auth::id())->paginate(12); // 12 users per page
        return response()->json(['users' => $users]);
    }

    public function send(Request $request) {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string|max:1000',
        ]);

        $check = Conversation::find($request->conversation_id);

        if(!$check) {
            return response()->json(['error' => 'Conversation not found.'], 404);

        }

        if($check->user_one_id != Auth::user()->id && $check->user_two_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not a participant in this conversation.'], 403);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::user()->id,
            'body' => $request->body,
        ]);

        broadcast(new NewMessageEvent($message))->toOthers(); //uses the conversation
        return response()->json($message->load('sender'));
    }

    public function initiateConversation($user_ID) {
         // Prevent user from messaging themselves
        if (Auth::id() == $user_ID) {
            return response()->json(['error' => 'Cannot start a conversation with yourself.'], 422);
        }

        // Check if a conversation already exists between these users (in any order)
        $conversation = Conversation::where(function ($query) use ($user_ID) {
                $query->where('user_one_id', Auth::id())
                    ->where('user_two_id', $user_ID);
            })
            ->orWhere(function ($query) use ($user_ID) {
                $query->where('user_one_id', $user_ID)
                    ->where('user_two_id', Auth::id());
            })
            ->first();

        // If it exists, return the existing conversation
        if ($conversation) {
            //use redirect instead if not ajax
            return redirect("/conversation/{$conversation->id}");
        }

        // Otherwise, create a new one
        $newConversation = Conversation::create([
            'user_one_id' => Auth::id(),
            'user_two_id' => $user_ID,
        ]);

        //use redirect instead if not ajax
        return redirect("/conversation/{$newConversation->id}");
    }

    public function fetchConversation($id) {
        $conversation = Conversation::with([
            'userOne:id,name,email', // Only load needed fields
            'userTwo:id,name,email'   // Only load needed fields
        ])->findOrFail($id);

        if(Auth::user()->id === $conversation->user_one_id){
            $userNow = $conversation->user_one_id;
        } else if(Auth::user()->id === $conversation->user_two_id) {
            $userNow = $conversation->user_two_id;
        } else {
            return response()->json([
                'error' => 'true'
            ]);
        }

        $messages = $conversation->messages()->oldest()->paginate(10);
        $lastPage = $messages->lastPage();
        $currentPage = request()->get('page', 1);
        return view('try.trysendmessage', [
            'current_user' => $userNow,
            'conversation' => $conversation,
            'messages' => $messages,
            'lastPage' => $lastPage,
            'currentPage' => $currentPage,
            'participants' => [
                'user_one' => $conversation->userOne,
                'user_two' => $conversation->userTwo
            ]
        ]);
    }

    public function viewConversation($id) {

    }

    public function fetchAllConversations() {
        $conversations = Conversation::with(['userOne', 'userTwo', 'messages'])
            ->where('user_one_id', Auth::id())
            ->orWhere('user_two_id', Auth::id())
            ->get();

        $results = $conversations->map(function ($conversation) {
            return [
                'other_user' => $conversation->otherUser(Auth::id()),
                'conversation' => $conversation->messages->last(),
            ];
        });

        return response()->json(['conversations' => $results]);
    }


}
