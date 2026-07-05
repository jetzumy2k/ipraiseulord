<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
Come, Holy Spirit, fill the hearts of Your faithful and kindle in them the fire of Your love.

V. Send forth Your Spirit and they shall be created.
R. And You shall renew the face of the earth.

Let us pray: O God, who by the light of the Holy Spirit did instruct the hearts of the faithful, grant that by the same Holy Spirit we may be truly wise and ever enjoy His consolations. Through Christ our Lord. Amen.

Act of Consecration to the Holy Spirit: O Holy Spirit, divine Spirit of light and love, I consecrate to You my understanding, my heart, and my will, my whole being for time and for eternity. May my understanding be always submissive to Your heavenly inspirations and to the teaching of the Catholic Church. May my heart be ever inflamed with love of God and of my neighbor. May my will be ever conformed to the divine will. Amen.
PRAYER;

$themes = [
    1 => ['The Promise of the Spirit', 'Before His Ascension, Jesus promised to send the Holy Spirit to teach, guide, and sanctify the Church. The Apostles waited in prayer with Mary in the upper room.'],
    2 => ['Prayer with Mary', 'The first novena in the Church was prayed by the Apostles and the Blessed Virgin during the nine days between the Ascension and Pentecost. Today we unite our prayer with Mary\'s.'],
    3 => ['Gifts of the Holy Spirit', 'We ask for wisdom, understanding, counsel, fortitude, knowledge, piety, and fear of the Lord, that we may live as true children of God.'],
    4 => ['Fruits of the Holy Spirit', 'The Spirit produces charity, joy, peace, patience, kindness, goodness, generosity, gentleness, faithfulness, modesty, self-control, and chastity in the soul.'],
    5 => ['The Spirit and the Church', 'On Pentecost the Church was born. We pray for the renewal of the whole Church through a new outpouring of the Holy Spirit.'],
    6 => ['The Spirit and the Sacraments', 'The Holy Spirit is invoked in Baptism, Confirmation, Holy Orders, and every sacrament. We ask for a deeper life of grace through the sacraments.'],
    7 => ['Mission and Evangelization', 'Empowered by the Spirit, the Apostles went forth to preach Christ to all nations. We pray for missionaries and for the courage to witness to our faith.'],
    8 => ['Unity of Christians', 'The Spirit is the bond of unity in the Church. We pray for reconciliation among all who believe in Christ and for the conversion of the world.'],
    9 => ['Pentecost Sunday', 'On this final day we beg the Holy Spirit to descend upon us as upon the Apostles, with wind and fire, renewing the face of the earth.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Pentecost Novena',
    'slug' => 'pentecost',
    'category' => 'common',
    'patron_saint' => 'Holy Spirit',
    'description' => 'The original novena of the Church, prayed from Ascension Thursday to Pentecost Saturday, asking the Holy Spirit to renew the face of the earth and set our hearts on fire with divine love.',
    'opening_prayer' => 'Almighty and ever-living God, You gave the disciples the Holy Spirit to make them witnesses to the Gospel throughout the world. Grant that the same Spirit may come to us and fill us with His gifts, that we may faithfully proclaim the faith and glorify Your name.',
    'closing_prayer' => 'Come, Holy Spirit, fill the hearts of Your faithful and enkindle in them the fire of Your love. Send forth Your Spirit and renew the face of the earth. Amen.',
    'days' => $days,
];
