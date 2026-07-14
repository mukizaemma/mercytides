<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav flex-column pt-2">
            <p class="admin-nav-section-title mb-0 mt-1">Overview</p>
            <x-admin.nav-link :href="route('dashboard')" icon="fa-tachometer-alt" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-admin.nav-link>

            <p class="admin-nav-section-title mb-0 mt-3">Website content</p>
            <x-admin.nav-link :href="route('slides')" icon="fa-images" :active="request()->routeIs(['slides', 'saveSlide', 'updateSlide', 'destroySlide'])">
                Home slides
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('pageHeaders.index')" icon="fa-heading" :active="request()->routeIs('pageHeaders.*')">
                Page headers
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('about')" icon="fa-bullseye" :active="request()->routeIs(['about', 'background', 'homePage'])">
                About &amp; homepage
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('programs')" icon="fa-list-alt" :active="request()->routeIs(['programs', 'editProgram', 'saveProgram', 'updateProgram', 'destroyProgram'])">
                Programs
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('getProjects')" icon="fa-project-diagram" :active="request()->routeIs(['getProjects', 'editProject', 'saveProject', 'updateProject'])">
                Program stories
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('impacts.index')" icon="fa-chart-line" :active="request()->routeIs(['impacts.index', 'saveImpact', 'updateImpact', 'destroyImpact'])">
                Impact metrics
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('sponsorship.index')" icon="fa-heart" :active="request()->routeIs(['sponsorship.index', 'saveSponsorship', 'updateSponsorship', 'destroySponsorship'])">
                Sponsorship profiles
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('events')" icon="fa-calendar" :active="request()->routeIs(['events', 'saveEvent', 'updateEvent'])">
                Events
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('blog.index')" icon="fa-newspaper" :active="request()->routeIs('blog.*')">
                News &amp; updates
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('getTestimonials')" icon="fa-quote-right" :active="request()->routeIs(['getTestimonials', 'saveTestimony', 'updateTestimony'])">
                Testimonials
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('staff')" icon="fa-users" :active="request()->routeIs(['staff', 'saveStaff', 'updateStaff'])">
                Leadership team
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('partner')" icon="fa-handshake" :active="request()->routeIs(['partner', 'savePartner', 'updatePartner'])">
                Partners
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('images')" icon="fa-image" :active="request()->routeIs(['images', 'saveGallery', 'updateGallery'])">
                Media gallery
            </x-admin.nav-link>

            <p class="admin-nav-section-title mb-0 mt-3">People &amp; giving</p>
            <x-admin.nav-link :href="route('formSubmissions.admin')" icon="fa-paper-plane" :active="request()->routeIs('formSubmissions.admin')">
                Form submissions
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('partnershipInquiries.index')" icon="fa-comments" :active="request()->routeIs('partnershipInquiries.index')">
                Partnership inquiries
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('AllMembers')" icon="fa-user-friends" :active="request()->routeIs('AllMembers')">
                Support applications
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('webMessages')" icon="fa-envelope" :active="request()->routeIs(['webMessages', 'messageReply'])">
                Messages
            </x-admin.nav-link>

            <p class="admin-nav-section-title mb-0 mt-3">Site</p>
            <x-admin.nav-link :href="route('settings')" icon="fa-cogs" :active="request()->routeIs('settings')">
                Site settings
            </x-admin.nav-link>
        </div>
    </div>
    <div class="sb-sidenav-footer px-3 py-3">
        <div class="small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.08em; opacity: 0.7;">Signed in</div>
        <div class="text-white small fw-semibold text-truncate" title="{{ Auth::user()->name ?? '' }}">
            {{ Auth::user()->name ?? 'Admin' }}
        </div>
    </div>
</nav>
