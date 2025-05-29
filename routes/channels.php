<?php

use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conv = \App\Models\Conversation::find($id);
    return $conv && ($conv->user_one_id === $user->id || $conv->user_two_id === $user->id);
}); //listen to only conversation
