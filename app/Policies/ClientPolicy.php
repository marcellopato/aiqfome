<?php

namespace App\Policies;

use App\Models\Client;

class ClientPolicy
{
    public function view(Client $authUser, Client $client)
    {
        return $authUser->hasRole('manager') || $authUser->id === $client->id;
    }

    public function update(Client $authUser, Client $client)
    {
        return $authUser->hasRole('manager') || $authUser->id === $client->id;
    }

    public function delete(Client $authUser, Client $client)
    {
        return $authUser->hasRole('manager') || $authUser->id === $client->id;
    }
} 