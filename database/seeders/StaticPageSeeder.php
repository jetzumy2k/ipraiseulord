<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'meta_description' => 'How Praise U Lord collects, uses, and protects your personal information.',
                'content' => <<<'HTML'
<h2>Privacy Policy</h2>
<p>Last updated: January 1, 2026</p>
<p>Praise U Lord ("we," "our," or "us") is committed to protecting the privacy of visitors who use our Catholic prayer and liturgical resources. This policy explains how we handle information in accordance with the dignity of the human person and the common good.</p>

<h3>Information We Collect</h3>
<ul>
<li><strong>Contact information</strong> — name and email address when you submit a contact form or prayer request.</li>
<li><strong>Usage data</strong> — anonymous analytics such as pages visited, prayers viewed, and general device information to improve our services.</li>
<li><strong>Donation information</strong> — processed through third-party providers; we do not store full payment card details on our servers.</li>
</ul>

<h3>How We Use Your Information</h3>
<p>We use collected information solely to respond to inquiries, provide spiritual content, improve the website experience, and process voluntary donations. We do not sell personal data to third parties.</p>

<h3>Cookies and Analytics</h3>
<p>We may use cookies and similar technologies to remember preferences and measure site performance. You may disable cookies in your browser settings, though some features may be limited.</p>

<h3>Data Retention and Security</h3>
<p>We retain personal data only as long as necessary for the purposes described. Reasonable technical and organizational measures are taken to safeguard information against unauthorized access.</p>

<h3>Your Rights</h3>
<p>You may request access to, correction of, or deletion of your personal data by contacting us. We will respond in a timely manner consistent with applicable law.</p>

<h3>Children</h3>
<p>Our services are intended for general audiences. We do not knowingly collect personal information from children under 13 without parental consent.</p>

<h3>Contact</h3>
<p>For privacy-related questions, please use our <a href="/contact-us">Contact Us</a> page.</p>
HTML,
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'meta_description' => 'Terms governing the use of Praise U Lord Catholic resources and website.',
                'content' => <<<'HTML'
<h2>Terms and Conditions</h2>
<p>Last updated: January 1, 2026</p>
<p>Welcome to Praise U Lord. By accessing this website, you agree to these Terms and Conditions. If you do not agree, please refrain from using our services.</p>

<h3>Purpose of This Site</h3>
<p>Praise U Lord provides Catholic prayers, novenas, mass guides, Scripture readings, and related spiritual resources for personal devotion and catechesis. Content is offered for edification and is not a substitute for the teaching authority of the Magisterium or the guidance of a parish priest.</p>

<h3>Acceptable Use</h3>
<p>You agree to use this site respectfully and lawfully. You shall not:</p>
<ul>
<li>Post or transmit content that is offensive, heretical, or contrary to Catholic teaching.</li>
<li>Attempt to disrupt, hack, or compromise the security of the website.</li>
<li>Use automated tools to scrape content without prior written permission.</li>
<li>Misrepresent our content as official Church teaching without proper context.</li>
</ul>

<h3>Intellectual Property</h3>
<p>Original content on this site is owned by Praise U Lord unless otherwise noted. Scripture texts may be subject to translation copyrights of their respective publishers. You may share links and brief excerpts for non-commercial devotional use with proper attribution.</p>

<h3>Disclaimer</h3>
<p>Liturgical calendars, mass readings, and feast day information are provided for convenience. Always consult your local parish and the official liturgical calendar of your diocese for authoritative guidance. We make no warranties regarding the completeness or accuracy of all content.</p>

<h3>Donations</h3>
<p>Voluntary donations support the maintenance of this ministry. Donations are not payment for goods or services unless explicitly stated. Refund policies, if any, will be communicated at the time of donation.</p>

<h3>Limitation of Liability</h3>
<p>Praise U Lord shall not be liable for any indirect, incidental, or consequential damages arising from use of this website or reliance on its content.</p>

<h3>Changes</h3>
<p>We reserve the right to modify these terms at any time. Continued use of the site after changes constitutes acceptance of the revised terms.</p>
HTML,
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'meta_description' => 'Learn about the mission of Praise U Lord to spread Catholic faith through prayer and Scripture.',
                'content' => <<<'HTML'
<h2>About Praise U Lord</h2>
<p><em>"Glorify the Lord, O my soul; and all that is within me, glorify his holy name!"</em> — Psalm 103:1</p>

<p>Praise U Lord is a Catholic digital ministry dedicated to helping the faithful encounter Christ through prayer, Scripture, and the riches of the liturgical tradition. Rooted in the teachings of the Roman Catholic Church, we seek to make timeless devotions accessible to believers everywhere — at home, at work, and on the journey of daily life.</p>

<h3>Our Mission</h3>
<p>To glorify God and serve souls by providing trustworthy Catholic prayers, novenas, mass guides, and Scripture resources that deepen faith, foster devotion to the Blessed Virgin Mary and the saints, and unite the faithful with the Sacred Liturgy.</p>

<h3>What We Offer</h3>
<ul>
<li><strong>Daily Prayers</strong> — Classic Catholic prayers for morning, evening, and every moment of the day.</li>
<li><strong>Novenas</strong> — Nine-day devotions to Our Lady, the Sacred Heart, the saints, and more.</li>
<li><strong>Mass Guides</strong> — Readings and reflections to prepare for the Holy Sacrifice of the Mass.</li>
<li><strong>Holy Scripture</strong> — Catholic editions of the Bible including the full 73-book canon.</li>
<li><strong>Daily Psalms & Proverbs</strong> — Wisdom from God's Word to inspire your day.</li>
</ul>

<h3>Our Faith</h3>
<p>We profess the faith of the One, Holy, Catholic, and Apostolic Church. We honor the Pope as the successor of Saint Peter, revere the Blessed Sacrament, and look to Mary, Mother of God, as our model and advocate. All content is offered in fidelity to the Magisterium and in the spirit of <em>lex orandi, lex credendi</em> — the law of prayer is the law of belief.</p>

<h3>Support Our Ministry</h3>
<p>Praise U Lord is sustained by the prayers and generous support of the faithful. If this ministry has blessed you, consider making a donation or sharing our resources with family and friends. Together, let us praise the Lord!</p>
HTML,
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch with the Praise U Lord team for questions, prayer requests, or feedback.',
                'content' => <<<'HTML'
<h2>Contact Us</h2>
<p>We welcome your questions, prayer intentions, feedback, and suggestions. Whether you need assistance with our website, wish to share a testimony, or have a concern about our content, we are here to listen.</p>

<h3>How to Reach Us</h3>
<p>Please use the contact form on this page to send us a message. We aim to respond within 2–3 business days. For urgent spiritual needs, we encourage you to contact your local parish priest or diocesan office.</p>

<h3>Prayer Requests</h3>
<p>Submit your prayer intentions through our contact form. Our team will remember your intentions in our daily prayers and, when appropriate, include them in our community prayer list. All intentions are treated with confidentiality and reverence.</p>

<h3>Content Corrections</h3>
<p>As faithful Catholics, we strive for accuracy in prayers, liturgical information, and Scripture references. If you notice an error or have a concern about doctrinal content, please let us know with specific details so we may review and correct it promptly.</p>

<h3>Partnerships & Media</h3>
<p>For parish partnerships, media inquiries, or collaboration opportunities with Catholic organizations, please indicate the nature of your request in your message.</p>

<h3>Mailing Address</h3>
<p>Praise U Lord Ministry<br>
Manila, Philippines<br>
Email: admin@praiseulord.com</p>

<p><em>May the peace of Christ be with you.</em></p>
HTML,
            ],
        ];

        $pageSections = [
            [
                'slug' => 'home',
                'page_route' => 'home',
                'title' => 'Welcome',
                'meta_description' => 'Welcome to Praise U Lord — daily scripture, prayer, and Catholic devotion.',
                'content' => <<<'HTML'
<p class="lead">A place for daily scripture, prayer, and Catholic devotion — inspired by the beauty of sacred reading.</p>
HTML,
            ],
            [
                'slug' => 'donate',
                'page_route' => 'donate',
                'title' => 'Donate',
                'meta_description' => 'Support Praise U Lord and help us share the Gospel through Bible reading and Catholic faith resources.',
                'content' => <<<'HTML'
<p class="lead">Your generosity helps us share the Gospel and maintain this ministry.</p>
<p>Choose a donation method below. If you give via bank transfer or e-wallet, you may email your receipt so we can acknowledge your gift.</p>
HTML,
            ],
            [
                'slug' => 'contact',
                'page_route' => 'contact',
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch with the Praise U Lord team for questions, feedback, and support.',
                'content' => <<<'HTML'
<p class="lead">We would love to hear from you.</p>
<p>Send us your questions, prayer intentions, feedback, or suggestions using the form below. We aim to respond within 2–3 business days.</p>
HTML,
            ],
            [
                'slug' => 'bible',
                'page_route' => 'bible',
                'title' => 'Holy Bible',
                'meta_description' => 'Read the complete Holy Bible online in many languages and translations.',
                'content' => <<<'HTML'
<p class="text-muted">Read Scripture in the translation and language of your choice.</p>
HTML,
            ],
            [
                'slug' => 'mass-guide',
                'page_route' => 'mass-guide',
                'title' => 'Mass Guide',
                'meta_description' => 'Follow the full order of Mass with priest and people responses, daily readings, and liturgical feast information.',
                'content' => <<<'HTML'
<p class="text-muted mb-0">Full order of Mass with priest and people responses.</p>
HTML,
            ],
            [
                'slug' => 'fiesta-calendar',
                'page_route' => 'fiesta-calendar',
                'title' => 'Fiesta Calendar',
                'meta_description' => 'Browse Catholic feast days for Jesus Christ, the Holy Trinity, Mary, saints, and angels throughout the year.',
                'content' => <<<'HTML'
<p class="text-muted mb-0">Feast days of Jesus Christ, the Holy Trinity, Mary, the saints, and the angels.</p>
HTML,
            ],
            [
                'slug' => 'novenas',
                'page_route' => 'novenas',
                'title' => 'Novenas',
                'meta_description' => 'Nine-day Catholic novenas with leader and congregation responses for popular devotions and patron saints.',
                'content' => <<<'HTML'
<p class="text-muted">Nine days of prayer and devotion.</p>
HTML,
            ],
            [
                'slug' => 'prayers',
                'page_route' => 'prayers',
                'title' => 'Prayers',
                'meta_description' => 'Traditional and common Catholic prayers for daily devotion, worship, and spiritual growth.',
                'content' => <<<'HTML'
<p class="text-muted">Traditional and daily prayers for every occasion.</p>
HTML,
            ],
            [
                'slug' => 'ai-advice',
                'page_route' => 'ai-advice',
                'title' => 'AI Spiritual Advice',
                'meta_description' => 'Ask faith questions and receive answers grounded in Bible verses and Catholic teaching.',
                'content' => <<<'HTML'
<p class="text-muted">Ask a question and receive guidance rooted in scripture.</p>
HTML,
            ],
        ];

        foreach (array_merge($pages, $pageSections) as $page) {
            StaticPage::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'page_route' => $page['page_route'] ?? null,
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'meta_description' => $page['meta_description'],
                    'is_published' => true,
                ]
            );
        }
    }
}
