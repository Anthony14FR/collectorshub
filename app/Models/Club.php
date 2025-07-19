<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'leader_id',
        'total_cp'
    ];

    protected $casts = [
        'total_cp' => 'integer'
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ClubRequest::class);
    }

    public function pendingRequests(): HasMany
    {
        return $this->hasMany(ClubRequest::class)->where('status', 'pending');
    }

    public function getMembersCount(): int
    {
        return $this->members()->count();
    }

    public function canAcceptNewMember(): bool
    {
        return $this->getMembersCount() < 30;
    }

    public function updateTotalCp(): void
    {
        $totalCp = $this->members()->with(['pokedex' => function ($query) {
            $query->where('is_in_team', true);
        }])->get()->sum(function ($member) {
            return $member->pokedex->sum('cp');
        });

        $this->total_cp = $totalCp;
        $this->save();
    }


}
