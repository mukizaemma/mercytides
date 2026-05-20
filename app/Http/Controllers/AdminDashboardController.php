<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\Event;
use App\Models\Member;
use App\Models\Message;
use App\Models\PartnershipInquiry;
use App\Models\Program;
use App\Models\Sponsorship;
use App\Models\FormSubmission;
use App\Models\Volunteer;
use App\Services\FormSubmissionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'beneficiaries' => Schema::hasTable('members')
                ? Member::query()->where('membership', 'Support Application')->count()
                : 0,
            'pending_applications' => Schema::hasTable('members')
                ? Member::query()
                    ->where('membership', 'Support Application')
                    ->where('status', 'Pending')
                    ->count()
                : 0,
            'donations_month' => Schema::hasTable('donates')
                ? Donate::query()
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->count()
                : 0,
            'active_programs' => Schema::hasTable('programs') ? Program::query()->count() : 0,
            'unsponsored_mothers' => Schema::hasTable('sponsorships')
                ? Sponsorship::query()->where('status', 'Not Sponsored')->count()
                : 0,
            'messages' => Schema::hasTable('messages') ? Message::query()->count() : 0,
            'volunteers' => Schema::hasTable('volunteers') ? Volunteer::query()->count() : 0,
            'partnership_inquiries' => Schema::hasTable('partnership_inquiries')
                ? PartnershipInquiry::query()->count()
                : 0,
            'form_submissions' => Schema::hasTable('form_submissions')
                ? FormSubmission::query()->count()
                : 0,
        ];

        $formSubmissionStats = Schema::hasTable('form_submissions')
            ? FormSubmissionService::aggregateStats()
            : ['total' => 0, 'by_channel' => ['whatsapp' => 0, 'email' => 0], 'by_form' => []];

        $upcomingEvents = Schema::hasTable('events')
            ? Event::query()
                ->where('status', 'Active')
                ->where('date', '>=', Carbon::today()->toDateString())
                ->orderBy('date')
                ->take(5)
                ->get()
            : collect();

        $recentApplications = Schema::hasTable('members')
            ? Member::query()
                ->where('membership', 'Support Application')
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $recentDonations = Schema::hasTable('donates')
            ? Donate::query()->latest()->take(5)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'upcomingEvents', 'recentApplications', 'recentDonations', 'formSubmissionStats'));
    }
}
