<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Game;
use App\Models\Language;
use App\Models\League;
use App\Models\Option;
use App\Models\Page;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller {
    public function index($categorySlug = null, $leagueSlug = null) {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $gameType  = session('game_type', 'running');

        $games      = Game::active()->$gameType();
        $categories = Category::getGames($gameType);

        if ($categorySlug) {
            $activeCategory = $categories->where('slug', $categorySlug)->first();
        } else {
            $activeCategory = $categories->where('games_count', $categories->max('games_count'))->first();
        }

        $leagues      = [];
        $activeLeague = null;

        if ($leagueSlug) {
            $activeLeague = League::where('slug', $leagueSlug)->active()->whereHas('category', function ($q) {
                $q->active();
            })->firstOrFail();

            $activeCategory = $activeLeague->category;
        }

        if ($activeCategory && $activeCategory->leagues->count()) {
            $leagues = $this->filterByLeagues($activeCategory, $gameType);
            if (!$leagueSlug) {
                $activeLeague = $leagues->first();
            }
        }

        $games = $games->where('league_id', @$activeLeague->id)->with(['teamOne', 'teamTwo'])->with(['questions' => function ($q) {
            $q->active()
                ->resultUndeclared()->select('id', 'game_id', 'title', 'locked')
                ->withCount('betDetails')
                ->with('options', function ($option) {
                    $option->active();
                });
        }])->orderBy('id', 'desc')->get();

        return view('Template::home', compact('pageTitle', 'categories', 'leagues', 'games', 'activeCategory', 'activeLeague'));
    }

    public function gamesByLeague($slug) {
        return $this->index(leagueSlug: $slug);
    }
    public function gamesByCategory($slug) {
        return $this->index(categorySlug: $slug);
    }

    public function switchType($type) {
        $url = url()->previous() ?? '/';
        session()->put('game_type', $type == 'live' ? 'running' : 'upcoming');
        return redirect($url);
    }

    public function oddsType($type) {
        session()->put('odds_type', $type);
        return to_route('home');
    }

    public function markets($gameSlug) {
        $gameType = session()->get('game_type', 'running');
        $game     = Game::active()->$gameType()->where('slug', $gameSlug)->hasActiveCategory()->hasActiveLeague()
            ->with([
                'league',
                'questions'         => function ($question) {
                    $question->active();
                    if (request()->more) {
                        $question->limit(request()->more);
                    }
                    $question->orderBy('id', 'desc')->resultUndeclared();
                },
                'questions.options' => function ($option) {
                    $option->active();
                },
            ])->firstOrFail();

        $categories     = Category::getGames($gameType);
        $activeCategory = $game->league->category;
        $activeLeague   = $game->league;
        $leagues        = $this->filterByLeagues($activeCategory, $gameType);
        $pageTitle      = "$game->slug - odds";
        return view('Template::markets', compact('pageTitle', 'categories', 'leagues', 'game', 'activeCategory', 'activeLeague'));
    }

    public function getOdds($id) {
        $options = Option::query();
        if (session('game_type') == 'running') {
            $options->availableForBet();
        }
        $options = $options->where('question_id', $id)->with('question')->get();
        return view('Template::partials.odds_by_question', compact('options'));
    }

    private function filterByLeagues($activeCategory, $gameType) {
        $leagues = $activeCategory->leagues();
        $gameType .= 'Game';
        return $leagues->withCount("$gameType as game_count")->orderBy('game_count', 'desc')->active()->get();
    }

    public function pages($slug) {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function contact() {
        $pageTitle = "Contact Us";
        $user      = auth()->user();
        return view('Template::contact', compact('pageTitle', 'user'));
    }

    public function contactSubmit(Request $request) {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug) {
        $policy      = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle   = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function blog() {
        $pageTitle = "News and Updates";
        $blogs     = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::blog', compact('pageTitle', 'blogs'));
    }

    public function blogDetails($slug) {
        $blog        = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle   = 'Read Full News';
        $latestBlogs = Frontend::where('id', '!=', $blog->id)->where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(10)->get();
        $seoContents = $blog->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'pageTitle', 'seoContents', 'seoImage', 'latestBlogs'));
    }

    public function cookieAccept() {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy() {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null) {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance() {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

}
