<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
Glorious Saint Rita, you who so wonderfully participated in the Passion of our Lord Jesus Christ, obtain for me the grace to suffer with resignation the troubles of this life and to protect me in all my needs, both spiritual and temporal.

Saint Rita, advocate of the impossible, pray for us. Obtain for us from God the favors we ask through your intercession, if they be for His greater glory and the salvation of our souls. Amen.

Our Father. Hail Mary. Glory Be.
PRAYER;

$themes = [
    1 => ['Wife and Mother', 'Saint Rita bore the crosses of a difficult marriage and the loss of her husband and sons with heroic patience, trusting always in God\'s providence.'],
    2 => ['Augustinian Nun', 'After her family duties ended, Rita entered the Augustinian convent at Cascia, living a life of prayer, penance, and charity.'],
    3 => ['The Wound of the Thorn', 'Rita received a wound on her forehead from a thorn of Christ\'s crown, a sign of her union with the Passion of Jesus.'],
    4 => ['Patience in Suffering', 'Known as the saint of impossible cases, Rita endured suffering without complaint and teaches us to offer our pains to God.'],
    5 => ['Peacemaker', 'Rita worked for reconciliation between feuding families. We ask her help for peace in our families and communities.'],
    6 => ['Intercessor for the Desperate', 'Countless souls turn to Saint Rita when all human hope seems lost. Today we present our most difficult needs to her.'],
    7 => ['Devotion to the Passion', 'Rita\'s love for the Passion of Christ was the center of her spirituality. We ask for grace to meditate on Christ\'s sufferings with love.'],
    8 => ['The Rose of Cascia', 'The rose that bloomed in winter symbolizes Rita\'s power with God. We pray with confidence in her intercession.'],
    9 => ['Feast of Saint Rita', 'On this final day we thank God for the life of Saint Rita and ask to imitate her faith, hope, and charity until death.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Saint Rita of Cascia',
    'slug' => 'saint-rita',
    'category' => 'common',
    'patron_saint' => 'Saint Rita of Cascia',
    'description' => 'The novena to Saint Rita of Cascia, patroness of impossible causes, abused wives, and widows. Her feast is celebrated on May 22.',
    'opening_prayer' => 'O holy protectress of those who are in great need, Saint Rita, you who were always powerful in your intercession before the Divine Majesty, obtain for us from God the favors we ask, if they be for the greater glory of God and the good of our souls.',
    'closing_prayer' => 'Saint Rita, advocate of the impossible, pray for us. May we imitate your patience and love, and come at last to the joy of heaven. Amen.',
    'days' => $days,
];
