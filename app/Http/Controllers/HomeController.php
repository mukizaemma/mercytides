<?php

namespace App\Http\Controllers;
use App\Models\News;

use App\Models\Team;
// use Google\Recaptcha\Recaptcha;
use App\Models\About;
use App\Models\Event;
use App\Models\Image;
use App\Models\Slide;
use App\Models\Donate;
use App\Models\Impact;
use App\Models\Member;
use App\Models\Country;
use App\Models\Gallery;
use App\Models\Message;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Activity;
use ReCaptcha\ReCaptcha;
use App\Models\Testimony;
use App\Models\Volunteer;
use App\Mail\ReplyMessage;
use App\Models\Background;
use App\Models\OrderRequest;
use App\Models\PartnershipInquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Sponsorship;
use App\Models\Projectimage;
use App\Support\PageHeaderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function redirects(){
        $role = Auth::user()->role;
        if($role ==1){
            return redirect()->route('dashboard');
        }
        else{


            $programs = Program::oldest()->get();
            $about = Background::firstOrEmpty();
            $mission = About::firstOrEmpty();
            $news = News::latest()->paginate(2);
            $homeGallery = DB::table('galleries')->latest()->get();
            $events = DB::table('events')->latest()->get();
            $slides = DB::table('slides')->latest()->get();
            $testimonials = DB::table('testimonies')->latest()->get();
            $staff = DB::table('teams')->orderby('id','asc')->where('display','Yes')->get();

            return view('frontend.home', [
                'programs' =>$programs,
                'homeGallery' =>$homeGallery,
                'events' =>$events,
                'slides' =>$slides,
                'testimonials' =>$testimonials,
                'staff' =>$staff,
                'about' =>$about,
                'mission' => $mission,
                'news' => $news,
            ]);
        }
    }

    public function index(){
        $background = Background::latest()->get();
        $programs = Activity::oldest()->get();
        $about = Background::firstOrEmpty();
        $mission = About::firstOrEmpty();
        $homeGallery = Gallery::latest()->get();
        $slides = Slide::oldest()->get();
        $testimonials = Testimony::latest()->paginate(3);
        $mothers = $this->publishedSponsorshipProfiles('young_mother', 4, 'oldest');
        $partners = Partner::latest()->get();
        $staff = Team::latest()->get();

        $today = Carbon::today()->toDateString();

        $event = Event::where('status', 'Active')
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc') // earliest upcoming
            ->first();

        $setting = Setting::firstOrEmpty();
        $homeProducts = collect();
        if (($setting->show_products_publicly ?? false) && Schema::hasTable('products')) {
            $homeProducts = Product::query()
                ->active()
                ->with('category')
                ->latest()
                ->take(3)
                ->get();
        }

        $impacts = collect();
        if (Schema::hasTable('impacts')) {
            $impactQuery = Impact::query()->latest();
            if (Schema::hasColumn('impacts', 'status')) {
                $impactQuery->where('status', 'Active');
            }
            $impacts = $impactQuery->take(6)->get();
        }

        $latestNews = collect();
        if (Schema::hasTable('news')) {
            $newsQuery = News::query()->latest();
            if (Schema::hasColumn('news', 'published_at')) {
                $newsQuery->whereNotNull('published_at');
            }
            $latestNews = $newsQuery->take(3)->get();
        }

        return view('frontend.home', [
            'background' =>$background,
            'programs' =>$programs,
            'homeGallery' =>$homeGallery,
            'event' =>$event,
            'slides' =>$slides,
            'testimonials' =>$testimonials,
            'mothers' => $mothers,
            'partners' =>$partners,
            'staff' =>$staff,
            'about' =>$about,
            'mission' =>$mission,
            'homeProducts' => $homeProducts,
            'impacts' => $impacts,
            'latestNews' => $latestNews,
        ]);
    }

    public function backgroundDetails(){

        $programs = Program::latest()->get();
        $partners = Partner::oldest()->get();
        $staff = Team::oldest()->get();
        $about = Background::firstOrEmpty();
        $mission = About::firstOrEmpty();
        $testimonials = DB::table('testimonies')->paginate(3);
        return view('frontend.about',['about'=>$about,'mission'=>$mission,'testimonials' =>$testimonials,'programs'=>$programs, 'partners'=>$partners, 'staff'=>$staff]);
    }
    public function team(){
        $programs = Program::latest()->get();
        $team = Team::where('category','Administration')->oldest()->get();
        $advisors = Team::where('category','Advisors')->oldest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.team',['team'=>$team,'programs'=>$programs,'about'=>$about,'advisors'=>$advisors]);
    }
    public function testimonials(){
        $programs = Program::latest()->get();
        $testimonials = Testimony:: latest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.testimonials',['testimonials'=>$testimonials,'programs'=>$programs, 'about'=>$about]);
    }

    public function mothersGallery()
    {
        // Single mother source: sponsorship young_mother profiles.
        return redirect()->route('sponsorship.youngMother', [], 301);
    }

    public function motherProfile($slug)
    {
        $profile = Sponsorship::query()
            ->published()
            ->ofType('young_mother')
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->first();

        if ($profile) {
            return redirect()->route('sponsorshipProfile', ['slug' => $profile->slug ?: $profile->id], 301);
        }

        // Fallback: any published sponsorship slug, then the mothers listing.
        $any = Sponsorship::query()
            ->published()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->first();

        if ($any) {
            return redirect()->route('sponsorshipProfile', ['slug' => $any->slug ?: $any->id], 301);
        }

        return redirect()->route('sponsorship.youngMother', [], 301);
    }

    public function sponsorshipCategory(string $type)
    {
        $types = \App\Support\MercyTidesContent::sponsorshipTypes();
        if (! array_key_exists($type, $types)) {
            abort(404);
        }

        $meta = $types[$type];
        $about = Background::firstOrEmpty();
        $profiles = $this->publishedSponsorshipProfiles($type);

        return view('frontend.sponsorship-type', compact('meta', 'profiles', 'type', 'about'));
    }

    public function sponsorChild()
    {
        return $this->sponsorshipCategory('child');
    }

    public function sponsorshipHub()
    {
        $about = Background::firstOrEmpty();

        return view('frontend.sponsorship-hub', compact('about'));
    }

    public function sponsorYoungMother()
    {
        return $this->sponsorshipCategory('young_mother');
    }

    public function sponsorFamily()
    {
        return $this->sponsorshipCategory('family');
    }

    public function sponsorshipProfile(string $slug)
    {
        $about = Background::firstOrEmpty();
        $profile = Sponsorship::query()
            ->published()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->firstOrFail();

        $types = \App\Support\MercyTidesContent::sponsorshipTypes();
        $typeMeta = $types[$profile->type] ?? $types['child'];

        $relatedProfiles = Sponsorship::query()
            ->published()
            ->ofType($profile->type)
            ->where('id', '!=', $profile->id)
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.sponsorship-profile', compact('about', 'profile', 'typeMeta', 'relatedProfiles'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Sponsorship>
     */
    protected function publishedSponsorshipProfiles(string $type, ?int $limit = null, string $order = 'latest')
    {
        if (! Schema::hasTable('sponsorships')) {
            return collect();
        }

        $query = Sponsorship::query()
            ->published()
            ->ofType($type);

        if ($order === 'oldest') {
            $query->oldest('id');
        } else {
            $query->latest();
        }

        if ($limit !== null) {
            $query->take($limit);
        }

        return $query->get();
    }

    public function testimony($id){
        $testimony = Testimony::findOrFail($id);
        $programs = Program:: latest()->get();
        $about = Background::firstOrEmpty();
        $testimonials = Testimony::where('id', '!=', $testimony->id)->paginate(6);
        return view('frontend.testimony',['testimony'=>$testimony, 'programs'=>$programs,'testimonials'=>$testimonials,'about'=>$about]);
    }
    public function showPrograms(){
        $programs = Program::query()->oldest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.programs',['programs'=>$programs, 'about'=>$about]);
    }
    public function singleProgram($slug){
        $program = Program::with('activities')->where('slug',$slug)->firstOrFail();
        $programs = Program::where('id' ,'!=',$program->id)->oldest()->get();
        $about = Background::firstOrEmpty();
        $gallery = Gallery::latest()->get();
        $news = News::latest()->paginate(9);
        return view('frontend.activities',['program'=>$program, 'programs'=>$programs, 'about'=>$about, 'gallery'=>$gallery,'news'=>$news]);
    }

    public function project($slug){
        $eager = ['images', 'program'];
        if (Schema::hasTable('programimages')) {
            $eager[] = 'program.images';
        }

        $activity = Activity::with($eager)->where('slug', $slug)->first();
        if (!$activity) {
            $program = Program::with('activities')->where('slug', $slug)->firstOrFail();
            $programs = Program::where('id', '!=', $program->id)->oldest()->get();
            $about = Background::firstOrEmpty();
            $gallery = Gallery::latest()->get();
            $news = News::latest()->paginate(9);

            return view('frontend.activities', [
                'program' => $program,
                'programs' => $programs,
                'about' => $about,
                'gallery' => $gallery,
                'news' => $news,
            ]);
        }

        $images = $activity->images;

        $programGallery = collect();
        if (Schema::hasTable('programimages') && $activity->program) {
            $programGallery = $activity->program->images()->latest()->get();
        }

        $relatedActivities = collect();
        if ($activity->program_id) {
            $relatedActivities = Activity::query()
                ->where('program_id', $activity->program_id)
                ->where('id', '!=', $activity->id)
                ->oldest()
                ->get();
        }

        $about = Background::firstOrEmpty();
        $news = News::latest()->paginate(9);

        return view('frontend.activity', [
            'activity' => $activity,
            'relatedActivities' => $relatedActivities,
            'about' => $about,
            'images' => $images,
            'programGallery' => $programGallery,
            'news' => $news,
        ]);
    }
    public function campaigns(){
        $programs = Program::oldest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.campaigns',['about'=>$about,'programs'=>$programs]);
    }
    public function campaign($slug){
        $about = Background::firstOrEmpty();
        $programs = Program::oldest()->get();
        $testimonials = DB::table('testimonies')->paginate(6);
        return view('frontend.campaign',['about'=>$about, 'testimonials'=>$testimonials,'programs'=>$programs]);
    }

    public function upcomingEvents(){
        $events = Event::where('status','Active')->latest()->get();
        return view('frontend.events',['events'=>$events]);
    }
    
    public function event($slug){
        $event = Event::where('slug', $slug)->firstOrFail();
        return view('frontend.event',['event'=>$event]);
    }
    public function posts(){
        $news = News::latest()->paginate(20);
        $programs = Program::latest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.blogs',['news'=>$news,'programs'=>$programs, 'about'=>$about]);
    }

    public function postSingle($slug){
        $blogs = News::latest()->get();
        $blog = News::where('slug',$slug)->firstOrFail();
        $images = $blog->blogimages ?? collect();
        $relatedBlogs = News::where('id','!=',$blog->id)->latest()->take(9);
        $programs = Program::latest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.blog',['blog'=>$blog,'blogs'=>$blogs,'relatedBlogs'=>$relatedBlogs,
        'programs'=>$programs,'about'=>$about,'images'=>$images]);
    }

    public function gallery()
    {
        $gallery = Image::query()
            ->latest()
            ->paginate(16);

        return view('frontend.gallery', [
            'gallery' => $gallery,
        ]);
    }


    public function contacts(){
        $contact = Setting::firstOrEmpty();
        $programs = Program::latest()->get();
        $about = Background::firstOrEmpty();
        return view('frontend.contact',['programs'=>$programs,'contact'=>$contact, 'about'=>$about]);
    }


    public function sendMessage(Request $request){

        $validatedData = $request->validate([
            'names' => 'required|max:255',
            'email' => 'required|max:255',
            'message' => 'required'
        ]);
        $blog = Message::firstOrCreate(
            [
                'names' => $request->input('names'),
                'email' => $request->input('email'),
                'message' => $request->input('message'),
            ]
        );
        return redirect()->back()->with('success', 'Your Message has been well submitted. We will get back to you soon');
    }

    public function webMessages(){

        $messages = Message::all();
        return view('admin.dashboard', ['messages'=>$messages]);
    }

    public function messageReply($id){

        $data = Message::find($id);
        return view('admin.emails.messageReply',['data'=>$data]);
    }

    public function sendReply(Request $request)
    {
        $data = [
            'email' => $request->email,
            'reply' => $request->reply,
        ];
        Mail::to($request->email)->send(new ReplyMessage($data));
        return redirect()->back()->with('success', 'Reply sent successfully');
    }


    public function members(){
        return redirect()->route('applyForSupport');
    }

    public function applyForSupport()
    {
        $about = Background::firstOrEmpty();

        return view('frontend.apply-for-support', compact('about'));
    }

    public function storeSupportApplication(Request $request)
    {
        $validated = $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'district' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'max:30'],
            'child_info' => ['nullable', 'string', 'max:2000'],
            'challenge' => ['required', 'string', 'max:5000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $application = new Member();
        $application->fill([
            'names' => $validated['names'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'district' => $validated['district'] ?? null,
            'address' => $validated['address'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'child_info' => $validated['child_info'] ?? null,
            'challenge' => $validated['challenge'],
            'membership' => 'Support Application',
            'status' => 'Pending',
            'dateJoined' => now()->toDateString(),
        ]);

        if ($request->hasFile('document')) {
            $application->document = $request->file('document')->store('images/applications', 'public');
        }

        $application->save();

        return redirect()->route('applyForSupport')->with('success', 'Thank you. Your application has been received. Our team will contact you soon.');
    }

    public function sponsorMother()
    {
        return redirect()->route('sponsorship.youngMother', [], 301);
    }
    public function volunteer()
    {
        $programs = Program::query()->oldest()->get(['id', 'title']);

        return view('frontend.volunteer', compact('programs'));
    }

    public function storeVolunteer(Request $request)
    {
        if (! Schema::hasTable('volunteers')) {
            return back()
                ->withInput()
                ->withErrors(['form' => 'Volunteer applications are not available at the moment. Please contact us directly.']);
        }

        $validated = $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:120'],
            'aboutYou' => ['nullable', 'string', 'max:5000'],
            'career' => ['nullable', 'string', 'max:5000'],
            'howToServe' => ['required', 'string', 'max:5000'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'website' => ['nullable', 'max:0'],
            'started_at' => ['nullable', 'integer'],
        ]);

        $startedAt = (int) ($request->input('started_at') ?? 0);
        if ($startedAt > 0 && (time() - $startedAt) < 3) {
            return back()
                ->withInput()
                ->withErrors(['form' => 'Form submitted too quickly. Please review your details and try again.']);
        }

        $aboutYou = trim((string) ($validated['aboutYou'] ?? ''));
        if (! empty($validated['phone'])) {
            $aboutYou = trim('Phone: ' . $validated['phone'] . ($aboutYou !== '' ? "\n\n" . $aboutYou : ''));
        }

        $slugBase = \Illuminate\Support\Str::slug($validated['names']);
        $slug = $slugBase . '-' . now()->format('YmdHis');

        Volunteer::create([
            'names' => $validated['names'],
            'slug' => $slug,
            'email' => $validated['email'],
            'address' => $validated['address'] ?? '',
            'country' => $validated['country'],
            'aboutYou' => $aboutYou !== '' ? $aboutYou : null,
            'career' => $validated['career'] ?? null,
            'howToServe' => $validated['howToServe'],
            'program_id' => $validated['program_id'] ?? null,
            'status' => 'Pending',
        ]);

        return redirect()
            ->route('volunteer')
            ->with('success', 'Thank you for offering to volunteer. Our team will contact you soon.');
    }
    public function donate(Request $request)
    {
        $countries = Country::all();
        $children = Schema::hasTable('sponsorships')
            ? Sponsorship::where('status', 'Not Sponsored')->get()
            : collect();

        $sponsorId = $request->filled('sponsor') ? (int) $request->input('sponsor') : null;
        $sponsorProfile = ($sponsorId && Schema::hasTable('sponsorships'))
            ? Sponsorship::query()->find($sponsorId)
            : null;
        $headerImage = PageHeaderImage::forDonate($sponsorId);
        $setting = Setting::firstOrEmpty();
        $donationPaymentMethods = \App\Support\DonationPaymentMethods::enabled($setting);
        $donationPaymentConfig = \App\Support\DonationPaymentMethods::frontendConfig($setting);

        return view('frontend.donate', [
            'countries' => $countries,
            'children' => $children,
            'headerImage' => $headerImage,
            'sponsorProfile' => $sponsorProfile,
            'donationPaymentMethods' => $donationPaymentMethods,
            'donationPaymentConfig' => $donationPaymentConfig,
            'setting' => $setting,
        ]);
    }

    public function saveDonation(Request $request){
        $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'period' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:120'],
            'program_id' => ['nullable', 'integer'],
            'message' => ['nullable', 'string', 'max:2000'],
            'sponsorship_id' => ['nullable', 'integer'],
        ]);

        $data = new Donate();
        $data->names = $request->names;
        $data->email = $request->email;
        $data->amount = $request->amount;
        $data->program_id = $request->program_id;
        $data->period = $request->period;
        $data->country = $request->country;
        $data->message = $request->message;
        $data->sponsorship_id = $request->sponsorship_id ?? $request->query('sponsor');

        $stored = $data->save();

        if ($stored) {
            return redirect()->back()->with('success', 'Thank you for your generous gift. Our team will follow up with payment details shortly.');
        }

        return redirect()->back()->with('error', 'We could not save your donation. Please try again.');
    }

    public function saveMember(Request $request){
        $data = new Member();
        $data->names = $request->names;
        $data ->phone = $request->phone;
        $data ->address = $request->address;
        $data ->gender = $request->gender;
        $data ->martual = $request->martual;
        $data ->membership = $request->membership;
        $data ->dateJoined = $request->dateJoined;

        $stored = $data->save();

        if($stored){
            return redirect()->back()->with('success', 'Thank you for your membership. We will get back to you soon for more details');
        }

    }


    public function programDetail($id){
        $data = Program::find($id);
        return view('frontend.programDetails',['data'=>$data]);
    }

    public function setting(){
        $data = Setting::first();
        if($data===null)
        {
            $data = new Setting();
            $data->title = 'Company Name';
            $data->save();
            $data = Setting::first();
        }

        return view('admin.settings', ['data'=>$data]);
    }



    public function saveSetting(Request $request){
        $data = Setting::firstOrEmpty();
        $data->company = $request->input('company');
        $data->address = $request->input('address');
        $data->phone = $request->input('phone');
        $data->phone1 = $request->input('phone1');
        $data->email = $request->input('email');
        $data->keywords = $request->input('keywords');
        $data->facebook = $request->input('facebook');
        $data->instagram = $request->input('instagram');
        $data->youtube = $request->input('youtube');

        // Theme options (safe if migration hasn't run yet)
        if (Schema::hasColumn('settings', 'primary_color')) {
            $data->primary_color = $request->input('primary_color') ?: '#3386B5';
        }
        if (Schema::hasColumn('settings', 'secondary_color')) {
            $data->secondary_color = $request->input('secondary_color') ?: '#00205B';
        }
        if (Schema::hasColumn('settings', 'neutral_color')) {
            $data->neutral_color = $request->input('neutral_color') ?: '#58A9C9';
        }
        if (Schema::hasColumn('settings', 'font_family')) {
            $data->font_family = $request->input('font_family') ?: 'Poppins';
        }
        if (Schema::hasColumn('settings', 'show_products_publicly')) {
            $data->show_products_publicly = $request->boolean('show_products_publicly');
        }
        if (Schema::hasColumn('settings', 'page_header_caption')) {
            $data->page_header_caption = $request->input('page_header_caption');
        }
        if (Schema::hasColumn('settings', 'google_map_embed_code')) {
            $data->google_map_embed_code = $request->input('google_map_embed_code');
        }
        if (Schema::hasColumn('settings', 'show_contact_map')) {
            $data->show_contact_map = $request->boolean('show_contact_map');
        }
        if (Schema::hasColumn('settings', 'recaptcha_site_key')) {
            $data->recaptcha_site_key = $request->input('recaptcha_site_key');
        }
        if (Schema::hasColumn('settings', 'recaptcha_secret_key')) {
            $data->recaptcha_secret_key = $request->input('recaptcha_secret_key');
        }
        if (Schema::hasColumn('settings', 'donation_payment_methods')) {
            $methodsInput = (array) $request->input('donation_methods', []);
            $data->donation_payment_methods = \App\Support\DonationPaymentMethods::fromAdminInput($methodsInput);
        }
        if (Schema::hasColumn('settings', 'donation_gateway_enabled')) {
            $data->donation_gateway_enabled = $request->boolean('donation_gateway_enabled');
        }
        if (Schema::hasColumn('settings', 'donation_stripe_publishable_key')) {
            $data->donation_stripe_publishable_key = $request->input('donation_stripe_publishable_key');
        }
        if (Schema::hasColumn('settings', 'donation_stripe_secret_key') && $request->filled('donation_stripe_secret_key')) {
            $data->donation_stripe_secret_key = $request->input('donation_stripe_secret_key');
        }
        if (Schema::hasColumn('settings', 'donation_default_currency')) {
            $data->donation_default_currency = strtoupper(trim((string) $request->input('donation_default_currency', 'USD'))) ?: 'USD';
        }
        if (Schema::hasColumn('settings', 'donation_gateway_notice')) {
            $data->donation_gateway_notice = $request->input('donation_gateway_notice');
        }

        if ($request->hasFile('logo') && request('logo') != '') {
            $data->logo = $this->storeOptimizedImageBasename(
                $request->file('logo'),
                'images',
                'public',
                ['preset' => 'logo']
            );
        }

        if (Schema::hasColumn('settings', 'page_header_image') && $request->hasFile('page_header_image') && request('page_header_image') != '') {
            $data->page_header_image = $this->storeOptimizedImageBasename(
                $request->file('page_header_image'),
                'images',
                'public',
                ['preset' => 'hero']
            );
        }

        // Allow password change only for this specific admin account
        if ((Auth::user()->email ?? null) === 'admin@iremetech.com' && $request->filled('new_password')) {
            $request->validate([
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = Auth::user();
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
        }

        $data->save();

        return redirect()->back()->with('success', 'Setting has been updated successfully');
    }

    public function about(){
        $data = About::first();
        if($data===null)
        {
            $data = new About();
            $data->vision = 'Alleviate poverty among single-teen mothers in Rutsiro District by providing tailoring trainings';
            $data->save();
            $data = About::first();
        }

        $background = Background::firstOrEmpty();

        return view('admin.about', ['data'=>$data, 'background' => $background]);
    }

    public function saveAbout(Request $request, $id){
        $data = About::firstOrEmpty();
        $data->mission = $request->input('mission');
        $data->vision = $request->input('vision');
        $data->values = $request->input('values');
        if (Schema::hasColumn('abouts', 'core_values_list') && $request->has('core_values_list')) {
            $data->core_values_list = $request->input('core_values_list');
        }


        if ($request->hasFile('backImage') && request('backImage') != '') {
            $data->backImage = $this->storeOptimizedImageBasename(
                $request->file('backImage'),
                'images',
                'public',
                ['preset' => 'hero']
            );
        }

        $data->save();

        return redirect()->back()->with('success', 'Setting has been updated successfully');
    }

    public function logoutUser(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function registerUser(){
        return view('frontend.registerUser');
    }

    public function loginUser(){
        return view('frontend.loginUser');
    }

    public function deleteDonation($id){
        $data = Donation::find($id);
        $data->delete($id);
        return redirect()->back()->with('warning','Donation has been deleted!');
    }

    public function ourMission(){
        $about = Background::firstOrEmpty();
        $mission = About::firstOrEmpty();
        return view('frontend.our-mission', compact('about', 'mission'));
    }

    public function ourApproach(){
        $about = Background::firstOrEmpty();
        $mission = About::firstOrEmpty();
        return view('frontend.our-approach', compact('about', 'mission'));
    }

    public function ourModel(){
        $about = Background::firstOrEmpty();
        return view('frontend.our-model', compact('about'));
    }

    public function ourFactory(){
        $about = Background::firstOrEmpty();
        $factoryGallery = Gallery::query()->latest()->take(9)->get();
        return view('frontend.our-factory', compact('about', 'factoryGallery'));
    }

    public function ourServices(){
        $about = Background::firstOrEmpty();
        $services = Service::query()->active()->orderBy('sort_order')->orderBy('title')->get();
        return view('frontend.our-services', compact('about', 'services'));
    }

    public function serviceShow($slug){
        $about = Background::firstOrEmpty();
        $service = Service::query()->active()->where('slug', $slug)->firstOrFail();
        return view('frontend.service-single', compact('about', 'service'));
    }

    public function ourProducts(Request $request){
        $about = Background::firstOrEmpty();
        $setting = Setting::firstOrEmpty();
        $categories = ProductCategory::query()->active()->orderBy('sort_order')->orderBy('name')->get();

        $products = collect();
        if ($setting->show_products_publicly ?? false) {
            $query = Product::query()->active()->with('category');

            if ($request->filled('category')) {
                $query->where('product_category_id', (int) $request->input('category'));
            }

            if ($request->filled('q')) {
                $term = trim((string) $request->input('q'));
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%')
                        ->orWhere('color', 'like', '%' . $term . '%');
                });
            }

            $products = $query->orderBy('sort_order')->orderBy('title')->get();
        }

        return view('frontend.our-products', compact('about', 'products', 'categories', 'setting'));
    }

    public function productShow($slug){
        $about = Background::firstOrEmpty();
        $setting = Setting::firstOrEmpty();
        if (!($setting->show_products_publicly ?? false)) {
            abort(404);
        }
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with(['category', 'images'])
            ->firstOrFail();

        return view('frontend.product-detail', compact('about', 'product', 'setting'));
    }

    public function requestOrder(Request $request)
    {
        $about = Background::firstOrEmpty();
        $product = null;
        if ($request->filled('product')) {
            $product = Product::query()
                ->active()
                ->where('slug', (string) $request->input('product'))
                ->first();
        }

        return view('frontend.request-order', compact('about', 'product'));
    }

    public function storeOrderRequest(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'email' => ['required', 'email', 'max:255'],
            'product_description' => ['required', 'string', 'max:20000'],
            'product_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $productId = null;
        $productReference = null;
        if (! empty($validated['product_slug'])) {
            $p = Product::query()->active()->where('slug', $validated['product_slug'])->first();
            if ($p) {
                $productId = $p->id;
                $productReference = $p->title;
            }
        }

        OrderRequest::create([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'product_description' => $validated['product_description'],
            'product_id' => $productId,
            'product_reference' => $productReference,
        ]);

        return redirect()
            ->route('requestOrder')
            ->with('success', 'Thank you. We have received your request and will contact you to discuss quantities and timelines.');
    }

    public function getInvolved()
    {
        $about = Background::firstOrEmpty();
        $introHtml = \App\Support\MercyTidesContent::field(
            Schema::hasColumn('backgrounds', 'get_involved_intro') ? ($about->get_involved_intro ?? null) : null,
            \App\Support\MercyTidesContent::getInvolvedWhy()
        );

        $impactStats = collect([
            ['label' => 'Families impacted', 'value' => $about->families_impacted ?? null],
            ['label' => 'Jobs created', 'value' => $about->jobs_created ?? null],
            ['label' => 'Training hours', 'value' => $about->training_hours ?? null],
        ])->filter(fn ($item) => trim((string) ($item['value'] ?? '')) !== '');

        return view('frontend.get-involved', compact('about', 'introHtml', 'impactStats'));
    }

    public function storePartnershipInquiry(Request $request)
    {
        $ipKey = 'partner-inquiry:ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            return back()
                ->withInput()
                ->withErrors(['form' => 'Too many attempts. Please wait a few minutes and try again.']);
        }

        RateLimiter::hit($ipKey, 10 * 60);

        $allowed = [
            'training',
            'equipment',
            'fundraising',
            'volunteering',
            'sales_ambassador',
            'wholesale',
            'corporate',
            'other',
        ];

        $validated = $request->validate([
            'organization' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'email' => ['required', 'email', 'max:255'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'in:' . implode(',', $allowed)],
            'message' => ['nullable', 'string', 'max:20000'],
            'website' => ['nullable', 'max:0'], // honeypot: must stay empty
            'started_at' => ['nullable', 'integer'],
        ]);

        $startedAt = (int) ($request->input('started_at') ?? 0);
        if ($startedAt > 0 && (time() - $startedAt) < 3) {
            return back()
                ->withInput()
                ->withErrors(['form' => 'Form submitted too quickly. Please review your details and try again.']);
        }

        $spamPattern = '/https?:\/\/|www\./i';
        foreach (['organization', 'full_name', 'message'] as $field) {
            $value = (string) ($validated[$field] ?? '');
            if ($value !== '' && preg_match($spamPattern, $value)) {
                return back()
                    ->withInput()
                    ->withErrors([$field => 'Please remove links from this field.']);
            }
        }

        if (empty($request->input('interests')) && ! $request->filled('message')) {
            return back()
                ->withInput()
                ->withErrors(['interests' => 'Select at least one area of interest or write a message.']);
        }

        $labels = [
            'training' => 'Skills development & training',
            'equipment' => 'Equipment or materials',
            'fundraising' => 'Fundraising or sponsorship',
            'volunteering' => 'Volunteering',
            'sales_ambassador' => 'Sales & ambassador programmes',
            'wholesale' => 'Wholesale / bulk orders',
            'corporate' => 'Corporate or institutional partnership',
            'other' => 'Other',
        ];

        $raw = (array) $request->input('interests', []);
        $picked = array_values(array_intersect($allowed, $raw));
        $summaryParts = [];
        foreach ($picked as $key) {
            $summaryParts[] = $labels[$key] ?? $key;
        }
        $interestsText = $summaryParts !== [] ? implode(', ', $summaryParts) : null;

        PartnershipInquiry::create([
            'organization' => $validated['organization'] ?? null,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'interests' => $interestsText,
            'message' => $validated['message'] ?? null,
        ]);

        return redirect()->route('getInvolved')->with('success', 'Thank you for reaching out. Our team will respond shortly.');
    }

    public function impactPage(){
        $about = Background::firstOrEmpty();
        $initiatives = Activity::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', 'Active');
            })
            ->latest()
            ->take(9)
            ->get();
        $impacts = Impact::query()->where('status', 'Active')->latest()->get();
        $testimonials = Testimony::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', 'Publish')
                    ->orWhere('status', 'Active');
            })
            ->latest()
            ->take(8)
            ->get();
        $mothers = $this->publishedSponsorshipProfiles('young_mother', 4, 'oldest');
        return view('frontend.impact', compact('about', 'initiatives', 'impacts', 'testimonials', 'mothers'));
    }

    public function handoverPage()
    {
        $about = Background::firstOrEmpty();

        return view('frontend.handover', compact('about'));
    }


}
