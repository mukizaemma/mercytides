<?php

namespace App\Support;

class SiteCopyCatalog
{
    /** @var array<string, string>|null */
    private static ?array $defaults = null;

    /** @var array<string, array<string, mixed>>|null */
    private static ?array $groups = null;

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function groups(): array
    {
        if (self::$groups !== null) {
            return self::$groups;
        }

        return self::$groups = [
            'objectives' => [
                'label' => 'Daily objectives',
                'intro' => 'The numbered cards under “What guides our work every day” on the homepage, About, and Vision & Mission.',
                'preview' => 'home',
                'objectives' => true,
                'fields' => [
                    self::field('objectives.title', 'Section title', 'What guides our work every day'),
                    self::field('objectives.caption_home', 'Homepage introduction', 'From skills training to solar support, every objective keeps mothers and children in Uganda at the centre.', 'textarea'),
                    self::field('objectives.caption_pages', 'About and Vision & Mission introduction', 'How we serve unwed young mothers, their children, and partner communities in Uganda.', 'textarea'),
                    self::field('home.objectives.more_btn', 'Button under the homepage cards', 'Vision, mission & more'),
                ],
            ],
            'homepage' => [
                'label' => 'Homepage',
                'intro' => 'Headings and introductions on the homepage. Slides, programs, news, metrics, mothers, and partners themselves stay in their own admin screens.',
                'preview' => 'home',
                'fields' => [
                    self::heading('Hero'),
                    self::field('home.hero.title', 'Fallback headline (when a slide has no headline)', 'Breaking Barriers, Bridging A Better Future'),
                    self::field('home.hero.lede', 'Default supporting line', 'Empowering young mothers and families in Uganda through skills, enterprise, and Christ-centered care.', 'textarea', 'Used when a slide does not have its own supporting line. Edit each slide under Home slides to override this.'),
                    self::field('home.hero.empty_lede', 'Message when no slides exist yet', 'Add hero slides from the admin dashboard to showcase mothers, children, and community impact.', 'textarea'),
                    self::field('home.hero.btn_involved', 'Primary button', 'Get Involved'),
                    self::field('home.hero.btn_sponsorship', 'Secondary button', 'Sponsorship'),
                    self::heading('The gap and our response'),
                    self::field('home.gap.title', 'Section title', 'Barriers young mothers face — and how we respond'),
                    self::field('home.gap.lead', 'Introduction', 'Across communities near Kampala, unwed and teenage mothers often lack skills, safety, and hope. Mercy Tides walks with them toward dignity, livelihood, and Christ-centered care.', 'textarea'),
                    self::field('home.gap.problem_title', 'Problem card title', 'Why mothers need support'),
                    self::field('home.gap.solution_title', 'Response card title', 'Skills, enterprise & care'),
                    self::heading('About band'),
                    self::field('home.about.title', 'Section title', 'About Mercy Tides Foundation'),
                    self::field('home.about.cta_story', 'Primary button', 'Our story'),
                    self::field('home.about.cta_where', 'Secondary button', 'Where we work'),
                    self::heading('Programs, impact, and calls to action'),
                    self::field('home.programs.title', 'Programs section title', 'Our programs in Uganda'),
                    self::field('home.programs.lead', 'Programs introduction', 'Vocational training, entrepreneurship, discipleship, and community care that turn barriers into sustainable livelihoods.', 'textarea'),
                    self::field('home.mission_label', 'Mission card label', 'Mission'),
                    self::field('home.vision_label', 'Vision card label', 'Vision'),
                    self::field('home.impact.title', 'Impact section title', 'Change you can measure in Uganda'),
                    self::field('home.impact.lead', 'Impact introduction', 'Mothers equipped, families strengthened, and communities renewed through skills, discipleship, and Christ-centered care.', 'textarea'),
                    self::field('home.partner.title', 'Partnership banner title', 'Walk with mothers & children through training, care, and sponsorship', 'textarea'),
                    self::field('home.partner.text', 'Partnership banner text', 'Churches, partners, and friends can help fund vocational skills, entrepreneurship, discipleship, and family support in Wakiso and nearby communities. Every gift and partnership strengthens local-led hope.', 'textarea'),
                    self::field('home.partner.stat1_title', 'Highlight 1 title', 'Transparent'),
                    self::field('home.partner.stat1_text', 'Highlight 1 text', 'Clear updates on impact'),
                    self::field('home.partner.stat2_title', 'Highlight 2 title', 'Local-led'),
                    self::field('home.partner.stat2_text', 'Highlight 2 text', 'Rooted in Ugandan communities'),
                    self::field('home.partner.stat3_title', 'Highlight 3 title', 'Faith-centered'),
                    self::field('home.partner.stat3_text', 'Highlight 3 text', 'Practical care & discipleship'),
                    self::field('home.partner.btn_involved', 'Primary button', 'Get involved'),
                    self::field('home.partner.btn_sponsorship', 'Secondary button', 'Sponsorship'),
                    self::heading('Mothers, updates, partners, and products'),
                    self::field('home.mothers.title', 'Mothers section title', 'Mothers in our program'),
                    self::field('home.mothers.lead', 'Mothers introduction', 'Meet some of the young mothers we walk with — toward independence, dignity, and a brighter future for their children.', 'textarea'),
                    self::field('home.mothers.more', 'Mothers button', 'View more mothers'),
                    self::field('home.news.title', 'Updates section title', 'Recent updates'),
                    self::field('home.news.more', 'Updates button', 'View all updates'),
                    self::field('home.partners.title', 'Partners section title', 'Our Partners'),
                    self::field('home.products.title', 'Products section title', 'Made with skill — products from our programs'),
                    self::field('home.products.lead', 'Products introduction (when no products intro is saved)', 'A glimpse of what we craft. Full details, specifications, and how to order are on each product page.', 'textarea'),
                ],
            ],
            'pages' => [
                'label' => 'About & pages',
                'intro' => 'Page titles, captions, and section headings. Longer stories (mission, overview, founder’s story, core values, where we work) stay on About & homepage.',
                'preview' => 'backgroundDetails',
                'fields' => [
                    self::heading('About / Our Story'),
                    self::field('about.page_title', 'Page title', 'Our Story'),
                    self::field('about.page_caption', 'Header caption', 'A Christian non-profit serving unwed young mothers in Uganda — the Pearl of Africa.', 'textarea'),
                    self::field('about.overview_title', 'Overview heading', 'Overview'),
                    self::field('about.founder_link_title', 'Founder link title', 'Read the founder’s story'),
                    self::field('about.founder_link_text', 'Founder link text', 'How a visit to Luwero-Ndeeba became Mercy Tides Foundation.'),
                    self::field('founder.eyebrow', 'Eyebrow above the founder tagline', 'The story behind Mercy Tides'),
                    self::field('about.core_values_title', 'Core values heading', 'Our Core Values'),
                    self::field('about.leadership_title', 'Leadership heading on About', 'Foundation Leadership'),
                    self::heading('Vision & Mission'),
                    self::field('mission.page_title', 'Page title', 'Vision & Mission'),
                    self::field('mission.page_caption', 'Header caption', 'Breaking Barriers, Bridging A Better Future — empowering unwed young mothers in Uganda.', 'textarea'),
                    self::field('mission.card_mission', 'Mission card title', 'Our mission'),
                    self::field('mission.card_vision', 'Vision card title', 'Our vision'),
                    self::heading('Programs & values, where we work, leadership'),
                    self::field('approach.page_title', 'Programs & values page title', 'Programs & Core Values'),
                    self::field('approach.page_caption', 'Programs & values caption', 'Vocational training, entrepreneurship, holistic leadership, and discipleship for mothers in Uganda.', 'textarea'),
                    self::field('approach.offerings_title', 'Offerings card title', 'Program offerings'),
                    self::field('model.page_title', 'Where we work page title', 'Where We Work'),
                    self::field('model.page_caption', 'Where we work caption', 'Serving unwed young mothers in Wakiso District and communities near Kampala, Uganda.', 'textarea'),
                    self::field('model.location_title', 'Location card title', 'Our location & focus'),
                    self::field('model.placeholder_title', 'Map placeholder title', 'Wakiso District, Uganda'),
                    self::field('model.placeholder_detail', 'Map placeholder detail', 'Bunamwaya Parish · Makindye Sub County · Kyadondo County'),
                    self::field('team.page_title', 'Leadership page title', 'Foundation Leadership'),
                    self::field('team.page_caption', 'Leadership page caption', 'Mercy Tides Foundation is led by a committed team serving mothers and families in Uganda.', 'textarea'),
                    self::heading('Programs, impact, updates, gallery'),
                    self::field('programs.page_title', 'Programs page title', 'Our Programs'),
                    self::field('impact.page_title', 'Impact page title', 'Our Impact'),
                    self::field('impact.mothers_title', 'Mothers heading on Impact', 'Young Mothers We Support'),
                    self::field('impact.mothers_lead', 'Mothers introduction on Impact', 'Real women. Real courage. Each portrait is part of a larger story of transformation.', 'textarea'),
                    self::field('impact.testimonials_title', 'Testimonials heading on Impact', 'Testimonials'),
                    self::field('updates.page_title', 'Updates page title', 'Recent Updates'),
                    self::field('gallery.page_title', 'Gallery page title', 'Gallery'),
                    self::field('gallery.intro', 'Gallery introduction', 'Moments from our programs, mothers, and community — photos and films that tell the story of hope in motion.', 'textarea'),
                    self::field('events.page_title', 'Events page title', 'Upcoming Events'),
                    self::field('events.section_title', 'Events section heading', 'Upcoming Events'),
                    self::field('testimonials.page_title', 'Testimonials page title', 'Our Testimonials'),
                    self::field('mothers.page_title', 'Mothers gallery page title', 'Mothers in our program'),
                    self::field('mothers.page_lead', 'Mothers gallery introduction', 'Meet the young mothers Mercy Tides walks alongside — each portrait is a story of resilience, faith, and new beginnings.', 'textarea'),
                ],
            ],
            'support' => [
                'label' => 'Giving & contact',
                'intro' => 'Fixed introductions on donate, get involved, contact, volunteer, and apply. Payment details, support options, and form questions stay in their own screens.',
                'preview' => 'getInvolved',
                'fields' => [
                    self::heading('Donate'),
                    self::field('donate.page_title', 'Page title', 'Donate'),
                    self::field('donate.page_caption', 'Header caption', 'Your gift equips mothers with skills, hope, and Christ-centered community support in Uganda.', 'textarea'),
                    self::field('donate.sponsor_caption', 'Caption when giving toward a person', 'You are giving toward sponsorship for {name} — thank you for walking alongside her.', 'textarea', 'Use {name} where the person’s name should appear.'),
                    self::field('donate.aside_title', 'Side card title', 'Your gift creates lasting change'),
                    self::field('donate.benefits', 'Side card points (one per line)', "Vocational training & mentorship\nChrist-centered care for mothers & children\nSolar & community support where needed", 'lines'),
                    self::field('donate.sponsor_note', 'Note under the side card', 'Prefer to sponsor someone specific?'),
                    self::field('donate.sponsor_link', 'Link next to that note', 'View sponsorship profiles'),
                    self::field('donate.form_title', 'Form title', 'Make a gift'),
                    self::field('donate.form_lead', 'Form introduction', 'Choose how you would like to give, enter your details, then follow the steps for your selected option.', 'textarea'),
                    self::heading('Get involved'),
                    self::field('getinvolved.page_title', 'Page title', 'Get Involved'),
                    self::field('getinvolved.page_caption', 'Header caption', 'Stand with young mothers in Uganda — choose a clear way to support her journey.', 'textarea'),
                    self::field('getinvolved.partnership_title', 'Introduction title', 'Your partnership changes lives'),
                    self::field('getinvolved.form_title', 'Form title', 'Choose a way to support mothers'),
                    self::field('getinvolved.form_lead', 'Form introduction', 'These are the same support paths used across Mercy Tides. Pick one, share your details, then send via WhatsApp or email.', 'textarea'),
                    self::heading('Contact'),
                    self::field('contact.page_title', 'Page title', 'Contact'),
                    self::field('contact.page_caption', 'Header caption', 'Reach our team in Uganda — and choose how you want to support young mothers.', 'textarea'),
                    self::field('contact.ways_title', 'Form title', 'Choose a way to support'),
                    self::field('contact.ways_lead', 'Form introduction', 'Pick one of the five support paths below, share your details, then send via WhatsApp or email.', 'textarea'),
                    self::field('contact.find_title', 'Map title', 'Find us in Uganda'),
                    self::field('contact.find_lead', 'Map introduction', 'Visit by appointment — message us to coordinate a time.'),
                    self::heading('Volunteer and apply for support'),
                    self::field('volunteer.page_title', 'Volunteer page title', 'Volunteer'),
                    self::field('volunteer.page_caption', 'Volunteer caption', 'Share your time, skills, and heart with mothers and families served by Mercy Tides Foundation in Uganda.', 'textarea'),
                    self::field('volunteer.card1_title', 'Card 1 title', 'Mentoring & training'),
                    self::field('volunteer.card1_text', 'Card 1 text', 'Coach mothers in vocational skills, business basics, or life skills during workshops and site visits.', 'textarea'),
                    self::field('volunteer.card2_title', 'Card 2 title', 'Programs & events'),
                    self::field('volunteer.card2_text', 'Card 2 text', 'Help with discipleship gatherings, conferences, outreach, and community activities.', 'textarea'),
                    self::field('volunteer.card3_title', 'Card 3 title', 'Remote support'),
                    self::field('volunteer.card3_text', 'Card 3 text', 'Communications, design, fundraising, prayer partnerships, and professional advice from anywhere.', 'textarea'),
                    self::field('volunteer.form_title', 'Volunteer form title', 'Volunteer application'),
                    self::field('volunteer.form_lead', 'Volunteer form introduction', 'Tell us about yourself and how you would like to serve. Our team will follow up by email or phone.', 'textarea'),
                    self::field('apply.page_title', 'Apply page title', 'Apply for Support'),
                    self::field('apply.page_caption', 'Apply caption', 'Mothers in Uganda can request training, mentorship, and holistic care through Mercy Tides Foundation.', 'textarea'),
                    self::field('apply.form_title', 'Apply form title', 'Support application'),
                    self::field('apply.form_lead', 'Apply form introduction', 'Tell us about yourself and your situation. When you are ready, send your application to our team via WhatsApp or Email.', 'textarea'),
                    self::heading('Sponsorship hub and profile labels'),
                    self::field('sponsorship.hub_title_page', 'Sponsorship page title', 'Sponsorship'),
                    self::field('sponsorship.hub_caption', 'Sponsorship header caption', 'Choose how you would like to walk with mothers and children in Uganda — sponsor someone specific or explore other ways to get involved.', 'textarea'),
                    self::field('sponsorship.hub_title', 'Sponsorship introduction title', 'Start your sponsorship journey'),
                    self::field('sponsorship.hub_lead', 'Sponsorship introduction', 'Each path opens a dedicated page where you can meet the people we serve, read their stories, and take the next step.', 'textarea'),
                    self::field('sponsorship.hub_extra_title', '“Get involved” card title on the hub', 'Get involved'),
                    self::field('sponsorship.hub_extra_text', '“Get involved” card on the hub', 'Volunteer, partner, give, or visit — choose how you want to stand with Mercy Tides.', 'textarea'),
                    self::field('sponsorship.ways_title', 'Ways to support heading', 'Ways to support our mothers'),
                    self::field('sponsorship.ways_lead', 'Ways to support introduction', 'Practical paths you can choose when you walk with a mother — the same options shown on each mother’s profile.', 'textarea'),
                    self::field('profile.story_title', 'Profile: story heading', 'Her story'),
                    self::field('profile.challenges_title', 'Profile: challenges heading', 'Challenges she faces'),
                    self::field('profile.vision_title', 'Profile: vision heading', 'Her vision'),
                    self::field('profile.video_label', 'Profile: video label', 'Hear from {name}', 'text', 'Use {name} for the first name.'),
                    self::field('profile.ways_title', 'Profile: ways to support heading', 'Ways to support {name}', 'text', 'Use {name} for the first name.'),
                    self::field('profile.ways_lead', 'Profile: ways to support introduction', 'Choose a focus. We will follow up with secure giving options — nothing is charged on this page.', 'textarea'),
                    self::field('profile.choose_action', 'Profile: card action', 'Choose this'),
                    self::field('profile.sponsor_cta', 'Profile: sponsor button', 'Sponsor {name}', 'text', 'Use {name} for the first name.'),
                    self::field('profile.related_mothers', 'Related heading for mothers', 'More mothers to support'),
                    self::field('profile.related_children', 'Related heading for children', 'More children to support'),
                    self::field('profile.related_families', 'Related heading for families', 'More families to support'),
                    self::field('profile.related_default', 'Related heading fallback', 'More profiles to support'),
                    self::field('profile.related_lead', 'Related introduction', 'Meet others seeking skills, care, and a path toward dignity.'),
                    self::heading('Footer and search description'),
                    self::field('meta.description', 'Search description', 'Mercy Tides Foundation empowers mothers and families in Uganda through vocational training, entrepreneurship, discipleship, and community care.', 'textarea'),
                    self::field('footer.tagline', 'Footer tagline', 'Breaking barriers and bridging a better future for mothers and children in Uganda.', 'textarea'),
                    self::field('footer.copy_name', 'Copyright name', 'Mercy Tides'),
                ],
            ],
            'sponsorship' => [
                'label' => 'Sponsorship pages',
                'intro' => 'Introductions on Sponsor a Child, Sponsor a young mother, and Sponsor a family. Individual profiles are edited under Mothers & sponsorship.',
                'preview' => 'sponsorship.hub',
                'fields' => self::sponsorshipFields(),
            ],
            'navigation' => [
                'label' => 'Menu & footer',
                'intro' => 'Labels in the top menu and footer. Links stay the same; only the words change.',
                'preview' => 'home',
                'fields' => [
                    self::heading('Top menu'),
                    self::field('nav.home', 'Home', 'Home'),
                    self::field('nav.about', 'About', 'About'),
                    self::field('nav.our_story', 'Our Story', 'Our Story'),
                    self::field('nav.founder', 'Founder’s Story', 'Founder’s Story'),
                    self::field('nav.mission', 'Vision & Mission', 'Vision & Mission'),
                    self::field('nav.leadership', 'Leadership Team', 'Leadership Team'),
                    self::field('nav.programs_values', 'Programs & Values', 'Programs & Values'),
                    self::field('nav.where', 'Where We Work', 'Where We Work'),
                    self::field('nav.programs', 'Programs', 'Programs'),
                    self::field('nav.sponsorship', 'Sponsorship', 'Sponsorship'),
                    self::field('nav.impact', 'Impact', 'Impact'),
                    self::field('nav.updates', 'Updates', 'Updates'),
                    self::field('nav.gallery', 'Gallery', 'Gallery'),
                    self::field('nav.contact', 'Contact us', 'Contact us'),
                    self::heading('Footer'),
                    self::field('footer.explore_heading', 'Explore heading', 'Explore'),
                    self::field('footer.about', 'About us', 'About us'),
                    self::field('footer.programs', 'Programs', 'Programs'),
                    self::field('footer.impact', 'Impact', 'Impact'),
                    self::field('footer.updates', 'Updates', 'Updates'),
                    self::field('footer.gallery', 'Gallery', 'Gallery'),
                    self::field('footer.sponsorship', 'Sponsorship', 'Sponsorship'),
                    self::field('footer.get_involved', 'Get involved', 'Get involved'),
                    self::field('footer.contact', 'Contact', 'Contact'),
                    self::field('footer.programs_heading', 'Our programs heading', 'Our programs'),
                    self::field('footer.btn_sponsorship', 'Sponsorship button', 'Sponsorship'),
                    self::field('footer.btn_involved', 'Get involved button', 'Get Involved'),
                    self::field('footer.mini_sponsorship', 'Bottom sponsorship link', 'Sponsorship'),
                    self::field('footer.mini_involved', 'Bottom get involved link', 'Get involved'),
                ],
            ],
            'other' => [
                'label' => 'Other pages',
                'intro' => 'Campaigns, products, and order pages. Product records themselves stay in the products admin. These fields cover the fixed wording around them.',
                'preview' => 'campaigns',
                'fields' => [
                    self::heading('Campaigns'),
                    self::field('campaigns.why_title', 'Support heading', 'Why Your Support Matters'),
                    self::field('campaigns.why_html', 'Support text', '<p>Teenage pregnancy has left many girls in our communities raising children alone, without skills or support. Each program under <strong>Mercy Tides</strong> equips young mothers with practical skills — helping them regain confidence, earn a sustainable income, and raise healthy children.</p><p><strong>Your donation or partnership helps provide:</strong> quality training, tools, materials, and psychosocial support. Every contribution makes a difference — no amount is too small to transform a life.</p><p>Want to sponsor a full course, partner with us, or request a donation receipt? Reach out through our contact page.</p><p><strong>Explore the available causes below</strong> and select the one that touches your heart — or get in touch to join us in a more structured way.</p>', 'html'),
                    self::field('campaigns.causes_title', 'Causes heading', 'Our Causes'),
                    self::heading('Products and orders'),
                    self::field('products.page_title', 'Products page title', 'Our Products'),
                    self::field('products.page_caption', 'Products page caption', 'Pieces made through Mercy Tides programs. We produce to order for partners, retailers, and organisations.', 'textarea'),
                    self::field('products.fallback_intro', 'Products introduction when none is saved on About', '<h4>Our Products</h4><p>We develop pieces tailored to partner needs. Share what you are looking for and our team will follow up with options, timelines, and pricing.</p>', 'html'),
                    self::field('products.pricing_note', 'Pricing note on a product', 'Indicative guide pricing. We produce to order for partners, bulk buyers, and organisations — not retail shelf stock.', 'textarea'),
                    self::field('order.page_title', 'Request an order title', 'Request an order'),
                    self::field('order.page_caption', 'Request an order caption', 'We produce to order for partners, retailers, and organisations. Share what you need and we will follow up with timelines and pricing.', 'textarea'),
                    self::field('order.cta_title', 'Get involved band title', 'How to get involved'),
                    self::field('order.cta_lead', 'Get involved band introduction', 'Choose a collaboration path below, then continue to the Get involved page to submit the form and connect with our communications team directly.', 'textarea'),
                    self::field('order.card1_title', 'Card 1 title', 'Partnerships'),
                    self::field('order.card1_text', 'Card 1 text', 'Collaborate with us through strategic, social, or institutional partnerships.', 'textarea'),
                    self::field('order.card2_title', 'Card 2 title', 'Skills & Capacity'),
                    self::field('order.card2_text', 'Card 2 text', 'Support development through training, coaching, or technical assistance.', 'textarea'),
                    self::field('order.card3_title', 'Card 3 title', 'Funding & Sponsorship'),
                    self::field('order.card3_text', 'Card 3 text', 'Invest in programs that strengthen livelihoods and expand community impact.', 'textarea'),
                ],
            ],
        ];
    }

    public static function default(string $key): string
    {
        if (self::$defaults === null) {
            $defaults = [];
            foreach (self::groups() as $group) {
                foreach ($group['fields'] as $field) {
                    if (! empty($field['key'])) {
                        $defaults[$field['key']] = (string) ($field['default'] ?? '');
                    }
                }
            }
            self::$defaults = $defaults;
        }

        return self::$defaults[$key] ?? '';
    }

    /** @return list<string> */
    public static function keysFor(string $group): array
    {
        $keys = [];
        foreach (self::groups()[$group]['fields'] ?? [] as $field) {
            if (! empty($field['key'])) {
                $keys[] = $field['key'];
            }
        }

        return $keys;
    }

    public static function has(string $group): bool
    {
        return isset(self::groups()[$group]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function sponsorshipFields(): array
    {
        $fields = [];
        foreach (MercyTidesContent::sponsorshipTypeBlueprint() as $key => $meta) {
            $fields[] = self::heading($meta['label']);
            $fields[] = self::field("sponsorship.{$key}.label", 'Page title', $meta['label']);
            $fields[] = self::field("sponsorship.{$key}.caption", 'Header caption', $meta['caption'], 'textarea');
            $fields[] = self::field("sponsorship.{$key}.intro_title", 'Introduction title', $meta['intro_title']);
            $fields[] = self::field("sponsorship.{$key}.intro", 'Introduction', $meta['intro'], 'html');
            $fields[] = self::field("sponsorship.{$key}.grid_title", 'Profiles heading', $meta['grid_title']);
            $fields[] = self::field("sponsorship.{$key}.grid_lead", 'Profiles introduction', $meta['grid_lead'], 'textarea');
            foreach ($meta['pillars'] ?? [] as $index => $pillar) {
                $n = $index + 1;
                $fields[] = self::field("sponsorship.{$key}.pillar{$n}_icon", "Highlight {$n} icon", $pillar['icon'], 'text', 'Font Awesome name, for example fa-heart.');
                $fields[] = self::field("sponsorship.{$key}.pillar{$n}_title", "Highlight {$n} title", $pillar['title']);
                $fields[] = self::field("sponsorship.{$key}.pillar{$n}_text", "Highlight {$n} text", $pillar['text'], 'textarea');
            }
        }

        return $fields;
    }

    /** @return array{type: string, label: string} */
    private static function heading(string $label): array
    {
        return ['type' => 'heading', 'label' => $label];
    }

    /** @return array<string, string> */
    private static function field(string $key, string $label, string $default, string $type = 'text', ?string $help = null): array
    {
        $field = [
            'key' => $key,
            'label' => $label,
            'default' => $default,
            'type' => $type,
        ];
        if ($help) {
            $field['help'] = $help;
        }

        return $field;
    }
}
