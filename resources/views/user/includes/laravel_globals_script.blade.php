@php
    $user = auth()->user();
    $laravelGlobals = [
        'csrfToken' => csrf_token(),
        'authUserId' => $user->id,
        'authUserRole' => $user->hasNewRole('SUPER ADMIN') ? 'admin' : 'user',
        'authTimeZone' => $user->time_zone ?? 'UTC',
        'ipAddress' => env('IP_ADDRESS'),
        'socketPort' => env('SOCKET_PORT'),
        'storageUrl' => Storage::url(''),
        'assetUrls' => [
            'profileDummy' => asset('user_assets/images/profile_dummy.png'),
            'fileIcon' => asset('user_assets/images/file.png'),
            'groupDefaultImage' => asset('user_assets/images/group.jpg'),
        ],
        'userInfo' => [
            'firstName' => $user->first_name,
            'middleName' => $user->middle_name,
            'lastName' => $user->last_name,
            'profilePicture' => $user->profile_picture,
        ],
        'routes' => [
            'chatbotMessage' => route('chatbot.message'),
            'notificationList' => route('notification.list'),
            'notificationClear' => route('notification.clear'),
            'chatLoad' => route('chats.load'),
            'chatSend' => route('chats.send'),
            'chatList' => route('chats.chat-list'),
            'chatClear' => route('chats.clear'),
            'chatRemove' => route('chats.remove'),
            'chatSeen' => route('chats.seen'),
            'chatNotification' => route('chats.notification'),
            'notificationRead' => route('notification.read', ['type' => '__TYPE__', 'id' => '__ID__']),
            'teamChatLoad' => route('team-chats.load'),
            'teamChatSend' => route('team-chats.send'),
            'teamChatGroupList' => route('team-chats.group-list'),
            'teamChatGroupInfo' => route('team-chats.group-info'),
            'teamChatUpdateGroupImage' => route('team-chats.update-group-image'),
            'teamChatEditNameDes' => route('team-chats.edit-name-des'),
            'teamChatRemoveMember' => route('team-chats.remove-member'),
            'teamChatMakeAdmin' => route('team-chats.make-admin'),
            'teamChatExitFromGroup' => route('team-chats.exit-from-group'),
            'teamChatDeleteGroup' => route('team-chats.delete-group'),
            'teamChatRemoveChat' => route('team-chats.remove-chat'),
            'teamChatClearAllConversation' => route('team-chats.clear-all-conversation'),
            'teamChatSeen' => route('team-chats.seen'),
            'teamChatNotification' => route('team-chats.notification'),
            'mailInboxList' => route('mail.inbox-email-list'),
            'mailSentList' => route('mail.sent-email-list'),
            'mailStarList' => route('mail.star-email-list'),
            'mailTrashList' => route('mail.trash-email-list'),
        ],
    ];
@endphp
<script id="laravel-globals-data" type="application/json">@json($laravelGlobals)</script>
<script>
    window.Laravel = JSON.parse(document.getElementById('laravel-globals-data').textContent);
</script>
