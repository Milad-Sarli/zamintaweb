<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->admin_panel_access == 1;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->admin_panel_access == 1) {
            return true;
        }

        return $user->id === $ticket->user_id && $ticket->status !== 'closed';
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->admin_panel_access == 1;
    }
}
