<?php

namespace App\Services;

use App\Models\Club;
use App\Models\ClubRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClubService
{
    public function createClub(User $user, string $name, string $description, string $icon): Club
    {
        $cost = 100000;

        if ($user->cash < $cost) {
            throw new \Exception('Fonds insuffisants pour créer un club');
        }

        if ($user->isInClub()) {
            throw new \Exception('Vous êtes déjà dans un club');
        }

        if (Club::where('name', $name)->exists()) {
            throw new \Exception('Ce nom de club existe déjà');
        }

        DB::transaction(function () use ($user, $name, $description, $icon, $cost) {
            $user->decrement('cash', $cost);

            $club = Club::create([
                'name' => $name,
                'description' => $description,
                'icon' => $icon,
                'leader_id' => $user->id,
            ]);

            $club->members()->attach($user->id, ['role' => 'leader']);
        });

        return Club::where('name', $name)->first();
    }

    public function sendJoinRequest(User $user, Club $club): void
    {
        if ($user->isInClub()) {
            throw new \Exception('Vous êtes déjà dans un club');
        }

        if ($club->requests()->where('user_id', $user->id)->where('status', 'pending')->exists()) {
            throw new \Exception('Vous avez déjà envoyé une demande à ce club');
        }

        if (!$club->canAcceptNewMember()) {
            throw new \Exception('Ce club est complet');
        }

        DB::transaction(function () use ($user, $club) {
            $club->requests()->where('user_id', $user->id)->delete();

            ClubRequest::create([
                'club_id' => $club->id,
                'user_id' => $user->id,
                'status' => 'pending'
            ]);
        });
    }

    public function acceptRequest(ClubRequest $request): void
    {
        if ($request->status !== 'pending') {
            throw new \Exception('Cette demande a déjà été traitée');
        }

        if (!$request->club->canAcceptNewMember()) {
            throw new \Exception('Le club est complet');
        }

        DB::transaction(function () use ($request) {
            $request->update(['status' => 'accepted']);
            $request->club->members()->attach($request->user_id, ['role' => 'member']);
            $request->club->updateTotalCp();
        });
    }

    public function rejectRequest(ClubRequest $request): void
    {
        if ($request->status !== 'pending') {
            throw new \Exception('Cette demande a déjà été traitée');
        }

        $request->update(['status' => 'rejected']);
    }

    public function removeMember(Club $club, User $member): void
    {
        if ($member->id === $club->leader_id) {
            throw new \Exception('Le chef ne peut pas être expulsé');
        }

        if (!$club->members()->where('user_id', $member->id)->exists()) {
            throw new \Exception('Ce membre n\'appartient pas à ce club');
        }

        DB::transaction(function () use ($club, $member) {
            $club->members()->detach($member->id);
            $club->updateTotalCp();
        });
    }

    public function leaveClub(User $user): void
    {
        if (!$user->isInClub()) {
            throw new \Exception('Vous n\'êtes dans aucun club');
        }

        $club = $user->club()->first();

        if ($user->id === $club->leader_id) {
            throw new \Exception('Le chef ne peut pas quitter le club directement. Utilisez transferLeadership ou destroyClub');
        }

        DB::transaction(function () use ($club, $user) {
            $club->members()->detach($user->id);
            $club->updateTotalCp();
        });
    }

    public function transferLeadership(Club $club, User $newLeader): void
    {
        if ($club->leader_id !== Auth::user()->id) {
            throw new \Exception('Seul le chef peut transférer le leadership');
        }

        if ($newLeader->id === $club->leader_id) {
            throw new \Exception('Le nouveau chef ne peut pas être le chef actuel');
        }

        if (!$club->members()->where('user_id', $newLeader->id)->exists()) {
            throw new \Exception('Le nouveau chef doit être membre du club');
        }

        $oldLeaderId = $club->leader_id;

        DB::transaction(function () use ($club, $newLeader, $oldLeaderId) {
            $club->update(['leader_id' => $newLeader->id]);

            $club->members()->updateExistingPivot($newLeader->id, ['role' => 'leader']);
            $club->members()->updateExistingPivot($oldLeaderId, ['role' => 'member']);

            $club->updateTotalCp();
        });
    }

    public function destroyClub(Club $club): void
    {
        if ($club->leader_id !== Auth::user()->id) {
            throw new \Exception('Seul le chef peut détruire le club');
        }

        if ($club->members()->count() > 1) {
            throw new \Exception('Le club ne peut être détruit que s\'il ne contient que le chef');
        }

        DB::transaction(function () use ($club) {
            $club->members()->detach();
            $club->requests()->delete();
            $club->delete();
        });
    }
}
