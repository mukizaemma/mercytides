<?php

namespace App\Support;

/**
 * Default public-site copy for Mercy Tides Foundation (Uganda).
 * Used as fallbacks when admin content is empty; seeded via MercyTidesContentSeeder.
 */
class MercyTidesContent
{
    public static function vision(): string
    {
        return '<p><strong>“Breaking Barriers, Bridging A Better Future.”</strong></p>';
    }

    public static function mission(): string
    {
        return '<p>Our mission is to empower underprivileged unwed young and teenage mothers by providing vocational training and business support—enabling them to break free from the cycle of poverty and economic dependency, and bridging the gap to better standards of living.</p>
<p>We are dedicated to helping disadvantaged unwed young mothers overcome barriers of poverty through vocational training, entrepreneurship, and Christ-centered community care.</p>';
    }

    public static function overview(): string
    {
        return '<p><strong>Mercy Tides Foundation</strong> is a Christian non-profit organization located in Uganda—the Pearl of Africa. We serve communities in the outskirts of Kampala: <strong>District:</strong> Wakiso · <strong>County:</strong> Kyadondo · <strong>Sub County:</strong> Makindye · <strong>Parish:</strong> Bunamwaya · <strong>Village:</strong> Bunamwaya–Ngobe B.</p>
<p>In reference to Uganda’s history of high poverty and cultural norms that have often disrespected the dignity of women and girls, many families have been torn apart. Countless young women have endured rape by strangers, parents, or relatives. Homelessness and the search for work or a better life have pushed many into ungodly sexual behavior—leading to increased numbers of unwed and teenage mothers across the nation.</p>
<p>Mercy Tides Foundation envisions a vibrant ministry where hurting, depressed, and frustrated unwed young mothers encounter the gospel of our Lord Jesus Christ and find new love, acceptance, help, hope, forgiveness, guidance, encouragement, and prayer—leading to holistic transformation of their lives and families.</p>';
    }

    public static function problemStatement(): string
    {
        return 'Uganda faces deep poverty and harmful cultural norms that have left many young women vulnerable—experiencing abuse, family breakdown, homelessness, and cycles of exploitation that result in unwed and teenage motherhood without skills, support, or hope.';
    }

    public static function solutionStatement(): string
    {
        return 'Mercy Tides Foundation walks alongside unwed young mothers through vocational training, entrepreneurship development, discipleship, renewable energy support, and leadership formation—equipping them to build dignified livelihoods and Christ-centered homes.';
    }

    /** @return list<string> */
    public static function objectives(): array
    {
        return [
            'Offering vocational training programs tailored to local community challenges and demands.',
            'Providing mentorship and coaching to develop entrepreneurial skills.',
            'Facilitating access to microfinance and startup resources.',
            'Supporting businesses and startups led by unwed young and teenage mothers.',
            'Conducting monthly discipleship gatherings and annual conferences for mothers and their children.',
            'Providing renewable solar energy solutions to sponsored mothers and communities served.',
        ];
    }

    /** Font Awesome icon classes aligned with {@see objectives()} order. */
    public static function objectiveIcons(): array
    {
        return [
            'fas fa-graduation-cap',
            'fas fa-handshake',
            'fas fa-coins',
            'fas fa-store',
            'fas fa-users',
            'fas fa-solar-panel',
        ];
    }

    /** @return list<string> */
    public static function coreValues(): array
    {
        return [
            'Christ-centered compassion and gospel hope',
            'Dignity and respect for every mother and child',
            'Holistic transformation—spiritual, social, and economic',
            'Integrity, accountability, and transparency',
            'Partnership with churches, schools, and communities',
            'Gender equality and inclusive community development',
        ];
    }

    public static function programOfferingsHtml(): string
    {
        return '<p><strong>Vocational Training</strong> — Comprehensive programs in high-demand sectors including construction, hospitality, information technology, agriculture, fashion and design, culinary arts, welding, plumbing, solar installation, and more—covering technical and soft skills for employability and self-sufficiency.</p>
<p><strong>Entrepreneurship Development</strong> — Mentorship, business planning workshops, and networking that help mothers launch and sustain income-generating ventures and contribute to local economic development.</p>
<p><strong>Holistic Leadership &amp; Discipleship</strong> — Short-term courses and conferences building critical thinking, communication, financial literacy, work ethics, emotional intelligence, parenting, mental health, trauma-informed counselling, and evangelism camps for mothers and children.</p>
<p><strong>Renewable Energy</strong> — Solar electricity solutions for sponsored mothers and partner communities.</p>
<p>Through strategic partnerships with churches, businesses, schools, and international organizations, we leverage shared expertise and resources to expand our reach and long-term sustainability.</p>';
    }

    public static function holisticLeadershipHtml(): string
    {
        return '<p>We believe in building skills that are not only technical but develop the whole person—training a new generation of leaders who are <strong>ethical and economically independent</strong>.</p>
<p>Our holistic leadership pathways include critical thinking and problem-solving, communication and teamwork, leadership and creativity, financial literacy, work ethics and professionalism, emotional intelligence, computer and multimedia skills, public speaking, AI awareness, customer care, research, and community engagement.</p>
<p>Certified coaches address gender education, parenting, mental health, personal hygiene, and family budgeting. Counselling sessions support healing from trauma. Evangelism camps create space to preach the gospel to mothers and children—leading to transformation in both physical and spiritual life.</p>';
    }

    public static function whereWeWorkHtml(): string
    {
        return '<p>We are rooted in <strong>Uganda</strong>, serving unwed and teenage mothers in Wakiso District and surrounding communities near Kampala.</p>
<p><strong>Location:</strong> Outskirts of Kampala · Wakiso District · Kyadondo County · Makindye Sub County · Bunamwaya Parish · Bunamwaya–Ngobe B Village.</p>
<p>Our primary focus is mothers facing poverty and related vulnerabilities in communities with high unemployment and limited access to education and training. We strive for a diverse, inclusive environment that promotes gender equality and social cohesion.</p>';
    }

    /** @return list<array{names: string, position: string, bio: string}> */
    public static function leadershipTeam(): array
    {
        return [
            [
                'names' => 'Mr. MAGAMBO Jonathan',
                'position' => 'President & Founder',
                'bio' => '<p>Founding leader of Mercy Tides Foundation, providing vision and oversight for the ministry’s mission to empower unwed young mothers in Uganda through vocational training, entrepreneurship, and Christ-centered care.</p>',
            ],
            [
                'names' => 'Mr. MAGAMBO Samuel Kato',
                'position' => 'Director General (Secretary)',
                'bio' => '<p>Director General and Secretary, supporting organizational governance, partnerships, and the day-to-day coordination of programs and outreach.</p>',
            ],
            [
                'names' => 'Mrs. MAGAMBO Margaret',
                'position' => 'Community Programs Director',
                'bio' => '<p>Leads community programs, ensuring mothers and children receive holistic support through training, discipleship, and local engagement across Wakiso and partner communities.</p>',
            ],
        ];
    }

    public static function getInvolvedWhy(): string
    {
        return '<p>Every unwed young mother we walk with carries a story of courage — and <strong>you can be part of her next chapter</strong>. Mercy Tides Foundation equips mothers in Uganda through vocational training, entrepreneurship, discipleship, and practical community care.</p>
<p>When you get involved, you help break cycles of poverty and isolation. Your gift, time, partnership, or visit can provide skills, dignity, solar support, mentorship, and Christ-centered hope for mothers and their children.</p>
<p>Whether you give financially, volunteer your expertise, partner as a church or organization, or visit to encourage mothers face to face — <strong>your yes matters</strong>. Join us in breaking barriers and bridging a better future.</p>';
    }

    /** @return array<string, string> */
    public static function getInvolvedWays(): array
    {
        return [
            'donation' => 'Donation',
            'volunteer' => 'Volunteer',
            'partner' => 'Partner',
            'visit_mothers' => 'Visit the mothers',
        ];
    }

    /**
     * @return list<array{key: string, icon: string, title: string, text: string}>
     */
    public static function getInvolvedWayCards(): array
    {
        return [
            ['key' => 'donation', 'icon' => 'fa-hand-holding-heart', 'title' => 'Donation', 'text' => 'Fund training, care, and practical support for mothers and children.'],
            ['key' => 'volunteer', 'icon' => 'fa-hands-helping', 'title' => 'Volunteer', 'text' => 'Share skills, mentorship, and time to strengthen our programs.'],
            ['key' => 'partner', 'icon' => 'fa-handshake', 'title' => 'Partner', 'text' => 'Churches, NGOs, schools, and businesses walking with us long term.'],
            ['key' => 'visit_mothers', 'icon' => 'fa-users', 'title' => 'Visit the mothers', 'text' => 'Encourage mothers in person and witness transformation firsthand.'],
        ];
    }

    /** @return array<string, array{label: string, route: string, icon: string, caption: string, intro: string}> */
    public static function sponsorshipTypes(): array
    {
        return [
            'child' => [
                'label' => 'Sponsor a Child',
                'route' => 'sponsorship.child',
                'icon' => 'fa-child',
                'caption' => 'Give a child education, nutrition, and a safe place to grow through consistent monthly support.',
                'intro_title' => 'Help a child grow with hope',
                'intro' => '<p>Through child sponsorship, you help cover school essentials, meals, healthcare, and mentoring for children in our care. Each profile shares their story so you can pray, give, and follow their progress with Mercy Tides Foundation.</p>',
                'grid_title' => 'Children waiting for a sponsor',
                'grid_lead' => 'Meet a child, learn their story, and partner with Mercy Tides in their education and care.',
            ],
            'young_mother' => [
                'label' => 'Sponsor a young mother',
                'route' => 'sponsorship.youngMother',
                'icon' => 'fa-female',
                'caption' => 'Walk alongside a young mother with vocational training, discipleship, and practical support.',
                'intro_title' => 'Every mother deserves a path forward',
                'intro' => '<p>Unwed young mothers in Uganda often face stigma, interrupted schooling, and the weight of providing alone. Through Mercy Tides, each mother you meet here is learning a trade, growing in faith, and caring for her child with renewed dignity. Your sponsorship does not buy a label — it walks with her through training, childcare, mentorship, and the practical support that helps her rebuild a stable home.</p>',
                'grid_title' => 'Mothers waiting for a sponsor',
                'grid_lead' => 'Choose a mother, read her story, and become part of her next chapter — skills, hope, and a brighter future for her child.',
                'pillars' => [
                    [
                        'icon' => 'fa-hands-helping',
                        'title' => 'Skills that sustain',
                        'text' => 'Vocational training and tools so she can earn with dignity and provide for her child.',
                    ],
                    [
                        'icon' => 'fa-heart',
                        'title' => 'Care for mother & child',
                        'text' => 'Childcare support, mentoring, and practical help while she grows in confidence.',
                    ],
                    [
                        'icon' => 'fa-pray',
                        'title' => 'Faith & belonging',
                        'text' => 'Christ-centered discipleship and a community that reminds her she is not alone.',
                    ],
                ],
            ],
            'family' => [
                'label' => 'Sponsor a family',
                'route' => 'sponsorship.family',
                'icon' => 'fa-home',
                'caption' => 'Support a whole household — mother and children together — with holistic, lasting care.',
                'intro_title' => 'Support a whole household',
                'intro' => '<p>Family sponsorship meets practical needs across the home: food, shelter, education, and spiritual encouragement. You help a family move from survival toward stability and hope for the next generation.</p>',
                'grid_title' => 'Families waiting for a sponsor',
                'grid_lead' => 'Choose a family, walk with them in prayer and giving, and help build a more stable home.',
            ],
        ];
    }

    public static function sponsorshipTypeLabel(string $type): string
    {
        return self::sponsorshipTypes()[$type]['label'] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Cards for the main /sponsorship hub page.
     *
     * @return list<array{key: string, title: string, text: string, icon: string, route: string}>
     */
    public static function sponsorshipHubCards(): array
    {
        $cards = [];
        foreach (self::sponsorshipTypes() as $key => $meta) {
            $cards[] = [
                'key' => $key,
                'title' => $meta['label'],
                'text' => $meta['caption'],
                'icon' => $meta['icon'],
                'route' => $meta['route'],
            ];
        }

        $cards[] = [
            'key' => 'get_involved',
            'title' => 'Get involved',
            'text' => 'Volunteer, partner, give, or visit — choose how you want to stand with Mercy Tides.',
            'icon' => 'fa-hand-holding-heart',
            'route' => 'getInvolved',
        ];

        return $cards;
    }

    /** @return array<string, string> */
    public static function sponsorshipDonationPreferences(): array
    {
        return [
            'monthly' => 'Monthly giving',
            'one_time' => 'One-time gift',
            'in_kind' => 'In-kind support (goods / supplies)',
            'pledge' => 'I want to discuss options first',
        ];
    }

    /**
     * @return list<array{key: string, icon: string, label: string}>
     */
    public static function partnershipInterestOptions(): array
    {
        return [
            ['key' => 'training', 'icon' => 'fa-graduation-cap', 'label' => 'Skills development & training'],
            ['key' => 'equipment', 'icon' => 'fa-toolbox', 'label' => 'Equipment or materials'],
            ['key' => 'fundraising', 'icon' => 'fa-hand-holding-heart', 'label' => 'Fundraising or sponsorship'],
            ['key' => 'volunteering', 'icon' => 'fa-hands-helping', 'label' => 'Volunteering'],
            ['key' => 'sales_ambassador', 'icon' => 'fa-store', 'label' => 'Sales & ambassador programmes'],
            ['key' => 'wholesale', 'icon' => 'fa-boxes', 'label' => 'Wholesale / bulk orders'],
            ['key' => 'corporate', 'icon' => 'fa-building', 'label' => 'Corporate or institutional partnership'],
            ['key' => 'other', 'icon' => 'fa-ellipsis-h', 'label' => 'Other'],
        ];
    }

    /** @return array<string, string> */
    public static function partnershipInterestLabels(): array
    {
        $labels = [];
        foreach (self::partnershipInterestOptions() as $option) {
            $labels[$option['key']] = $option['label'];
        }

        return $labels;
    }

    public static function field(?string $value, string $default): string
    {
        $trimmed = trim(strip_tags(html_entity_decode((string) $value)));

        return $trimmed !== '' ? (string) $value : $default;
    }

    public static function plain(?string $value, string $default): string
    {
        $trimmed = trim(strip_tags(html_entity_decode((string) $value)));

        return $trimmed !== '' ? $trimmed : $default;
    }
}
