<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
O holy Saint Anthony, gentlest of saints, your love for God and charity for His creatures made you worthy when on earth to possess miraculous powers. Miracles waited on your word, which you were ever ready to speak for those in trouble or anxiety.

Encouraged by this thought, I implore you to obtain for me (mention your request). O gentle and loving Saint Anthony, whose heart was ever full of human sympathy, whisper my petition into the ears of the sweet Infant Jesus, who loved to be folded in your arms; and the gratitude of my heart will ever be yours. Amen.

Our Father. Hail Mary. Glory Be.
PRAYER;

$themes = [
    1 => ['Finder of Lost Things', 'Saint Anthony is renowned throughout the world as the saint who helps find what is lost. Today we ask his help for what we have lost in body or spirit.'],
    2 => ['Preacher of the Gospel', 'Saint Anthony preached with burning zeal and eloquence, drawing sinners back to Christ. We pray for priests, deacons, and all who proclaim the Word.'],
    3 => ['Lover of the Poor', 'Anthony gave himself to the poor and lived in radical simplicity. We ask for generous hearts toward those in need.'],
    4 => ['Devotion to the Child Jesus', 'Saint Anthony held a special love for the Infant Jesus, who appeared to him in a vision. We ask for childlike love of Christ.'],
    5 => ['Patience in Trials', 'Anthony endured misunderstanding, illness, and the hardships of missionary life. We pray for perseverance in our own trials.'],
    6 => ['Conversion of Sinners', 'Through his preaching and holiness, many sinners were converted. We entrust to him loved ones who have strayed from the faith.'],
    7 => ['Peace in Families', 'Families invoke Saint Anthony for harmony and reconciliation. Today we pray for peace in our homes and among our relatives.'],
    8 => ['Trust in Providence', 'Anthony trusted God completely, even in poverty and uncertainty. We ask for the grace to abandon ourselves to Divine Providence.'],
    9 => ['Thanksgiving', 'On this final day we thank God for the life and intercession of Saint Anthony and promise to honor him by charity and devotion.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Saint Anthony of Padua',
    'slug' => 'saint-anthony',
    'category' => 'common',
    'patron_saint' => 'Saint Anthony of Padua',
    'description' => 'The traditional novena to Saint Anthony of Padua, patron of lost things, the poor, and seekers of the truth. His feast is celebrated on June 13.',
    'opening_prayer' => 'O holy Saint Anthony, you are known for the power and abundance of your miracles. Jesus came into your arms as a humble little child. Pray for me to the Child Jesus for my needs and the needs of those I love.',
    'closing_prayer' => 'Saint Anthony, wonder-worker and friend of the poor, pray for us. May we imitate your love for Christ and your charity toward all. Amen.',
    'days' => $days,
];
