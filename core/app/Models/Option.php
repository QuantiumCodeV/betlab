<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Option extends Model {
    use GlobalStatus;

    public function question() {
        return $this->belongsTo(Question::class);
    }
    public function bets() {
        return $this->hasMany(BetDetail::class);
    }

    public function scopeLocked($query) {
        return $query->where('locked', Status::OPTION_LOCKED);
    }

    public function scopeUnLocked($query) {
        return $query->where('locked', Status::OPTION_UNLOCKED);
    }

    public function scopeAvailableForBet($query) {
        return $query->active()->unLocked()->whereHas('question', function ($question) {
            $question->active()->unLocked()->resultUndeclared()
                ->whereHas('game', function ($game) {
                    $game->active()->running()->hasActiveCategory()->hasActiveLeague();
                });
        });
    }
    public function scopeAvailableForWinner($query) {
        return $query->where('status', Status::ENABLE)->where('winner', 0);
    }
}
