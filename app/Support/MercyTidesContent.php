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
