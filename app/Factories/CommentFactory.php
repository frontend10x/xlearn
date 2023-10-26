<?php

namespace App\Factories;

class CommentFactory
{
    public static function createComment($comment, $user, $replies)
    {
        $commentData = [
            'userId' => $user->id,
            'comId' => $comment->comId,
            'fullName' => $user->name,
            'userProfile' => 'URL_DEL_PERFIL', // Reemplaza con la URL real del perfil del usuario
            'text' => $comment->comment,
            'avatarUrl' => 'https://static-00.iconduck.com/assets.00/avatar-default-icon-248x256-bdv8pdld.png', // Reemplaza con la URL real del avatar del usuario
            'replies' => self::createReplies($replies),
        ];

        return $commentData;
    }

    public static function createReplies($replies)
    {
        $replyData = [];

        foreach ($replies as $reply) {
            $replyData[] = [
                'userId' => $reply->user->id,
                'comId' => $reply->comId,
                'userProfile' => 'URL_DEL_PERFIL', // Reemplaza con la URL real del perfil del usuario
                'fullName' => $reply->user->name,
                'avatarUrl' => 'https://icons.iconarchive.com/icons/papirus-team/papirus-status/512/avatar-default-icon.png', // Reemplaza con la URL real del avatar del usuario
                'text' => $reply->comment,
            ];
        }

        return $replyData;
    }
}