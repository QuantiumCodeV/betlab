<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;

class DeclareOutcomeController extends Controller {
    public function pendingOutcomes() {
        $pageTitle = 'Pending Outcomes';
        $questions = Question::resultUndeclared()
            ->with([
                'options' => function ($bets) {
                    $bets->withCount('bets');
                },
                'game', 'game.teamOne', 'game.teamTwo',
            ])
            ->withCount('betDetails')
            ->whereHas('betDetails', function ($query) {
                $query->where('bet_details.status', 2);
            })
            ->searchable(['title'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        return view('admin.declare_outcomes.index', compact('pageTitle', 'questions'));
    }

    public function declaredOutcomes() {
        $pageTitle = 'Declared Outcomes';
        $questions = Question::resultDeclared()
            ->with([
                'options' => function ($bets) {
                    $bets->withCount('bets');
                },
                'game', 'game.teamOne', 'game.teamTwo', 'winOption:id,question_id,name',
            ])
            ->withCount('betDetails')
            ->whereHas('betDetails', function ($query) {
                $query->where('bet_details.status', '!=', 2);
            })
            ->searchable(['title'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.declare_outcomes.index', compact('pageTitle', 'questions'));
    }

    public function refund($id) {
        $question = Question::active()->resultUndeclared()
            ->with(['betDetails' => function ($query) {
                $query->pending()->with('bet.user');
            }])->find($id);

        if (!$question) {
            $notify[] = ['error', 'This selection is not refundable'];
            return back()->withNotify($notify);
        }

        $question->result = Status::DECLARED;
        $question->refund = Status::REFUND;
        $question->save();

        $betDetails = $question->betDetails;

        foreach ($betDetails as $detail) {
            $detail->status = Status::BET_REFUNDED;
            $detail->save();

            $bet = $detail->bet;
            if ($bet->type == Status::SINGLE_BET) {
                $bet->status          = Status::BET_REFUNDED;
                $bet->amount_returned = Status::YES;
                $bet->save();
            }
        }

        $notify[] = ['success', 'All bets for question : ' . $question->title . ' marked as refunded'];
        return back()->withNotify($notify);
    }

    public function winner($id) {

        $option = Option::availableForWinner()->with('question')->find($id);

        if (!$option) {
            $notify[] = ['error', 'Invalid option selected'];
            return back()->withNotify($notify);
        }

        $question = $option->question;

        if ($question && $question->status == Status::UNDECLARED) {
            $notify[] = ['error', 'Result already declared'];
            return back()->withNotify($notify);
        }

        $winnerBetDetails = $question->betDetails()->where('option_id', $option->id)->where('question_id', $question->id)->with('bet')->get();
        $loserBetDetails  = $question->betDetails()->where('option_id', '!=', $option->id)->where('question_id', $question->id)->with('bet')->get();

        $question->result        = Status::DECLARED;
        $question->win_option_id = $option->id;
        $question->save();

        $option->winner = Status::WINNER;
        $option->save();

        foreach ($loserBetDetails as $loserBetDetail) {
            $loserBetDetail->status = Status::BET_LOSE;
            $loserBetDetail->save();

            $loseBet                  = $loserBetDetail->bet;
            $loseBet->status          = Status::BET_LOSE;
            $loseBet->amount_returned = Status::YES;
            $loseBet->result_time     = now();
            $loseBet->save();
            $loseBet->bets()->update(['status' => Status::BET_LOSE]);
        }

        foreach ($winnerBetDetails as $betDetails) {
            $betDetails->status = Status::BET_WIN;
            $betDetails->save();

            $winBet = $betDetails->bet;

            if ($winBet->type == Status::MULTI_BET) {

                $totalMultiBet       = $winBet->bets()->count();
                $refundMultiBetCount = $winBet->bets()->where('status', Status::BET_REFUNDED)->count();
                $wonBets             = $winBet->bets()->where('status', Status::BET_WIN)->get();

                if ($totalMultiBet == $refundMultiBetCount + $wonBets->count()) {
                    $winAmount               = $this->winAmount($winBet, $wonBets);
                    $winBet->return_amount   = $winAmount;
                    $winBet->status          = Status::BET_WIN;
                    $winBet->amount_returned = Status::YES;
                    $winBet->result_time     = now();
                    $winBet->save();
                }
            } else {
                $winBet->status          = Status::BET_WIN;
                $winBet->amount_returned = Status::YES;
                $winBet->result_time     = now();
                $winBet->save();
            }
        }

        $notify[] = ['success', 'Outcome selected successfully'];
        return back()->withNotify($notify);
    }

    protected function winAmount($bet, $wonBets) {
        $totalOddsRate = 1;

        foreach ($wonBets as $betData) {
            $totalOddsRate *= $betData->odds;
        }

        $winAmount = getAmount($bet->stake_amount * $totalOddsRate, 8);
        return $winAmount;
    }
}
