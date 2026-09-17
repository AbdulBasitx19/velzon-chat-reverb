<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//CORRECTED: Private chat channel authorization
Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    // Yeh function check karta hai ke kya current user is channel ko access kar sakta hai
    // Check karo ke current user ya toh userId1 hai ya userId2
     // Agar haan, toh is channel ko access kar sakta hai

    // Logic: Current user ya toh sender hai ya receiver
    // Agar user ID 5 hai, aur channel hai chat.5, toh user 5 is channel ko access kar sakta hai
    // Ya agar user ID 3 hai, aur wo user 5 ko message bhej raha hai, toh bhi access kar sakta hai
    
    $currentUserId = (int) $user->id;
    $id1 = (int) $userId1;
    $id2 = (int) $userId2;
    
    return ($currentUserId === $id1 || $currentUserId === $id2);
});


    
   
    
   