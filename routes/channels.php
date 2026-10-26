<?php

use Illuminate\Support\Facades\Broadcast;

// Authorizes a user for a private chat channel.
// This single definition handles both combined IDs (e.g., '1_6') and 
// single user IDs (e.g., '6' for notifications/fallback).
Broadcast::channel('chat.{identifier}', function ($user, $identifier) {

    \Illuminate\Support\Facades\Log::info('Broadcast auth check', [
        'user_id' => $user->id,
        'identifier' => $identifier
    ]);

    // --- Case 1: Combined IDs (e.g., "5_6") ---
    if (str_contains($identifier, '_')) {

        // Explode combined ID into user IDs
        [$user1, $user2] = explode('_', $identifier);

        // Check if the current user is one of the participants
        $isUser1 = (int) $user->id === (int) $user1;
        $isUser2 = (int) $user->id === (int) $user2;

        return $isUser1 || $isUser2;
    }

    // --- Case 2: Single User ID (e.g., "6") ---
    // This is used for general notifications intended for a single user.
    return (int) $user->id === (int) $identifier;
});

// The previous duplicate definition for 'chat.{userId}' is now removed 
// as it caused the conflict and confusion.