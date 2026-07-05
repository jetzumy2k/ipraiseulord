<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
O Mary, conceived without sin, pray for us who have recourse to thee. O Virgin Immaculate, we consecrate ourselves to thee and ask for the grace to imitate thy purity and love.

Hail Mary (three times).
PRAYER;

$themes = [
    1 => ['Mary\'s Privileged Grace', 'From the first moment of her conception, Mary was preserved from all stain of original sin by a singular grace of God. Today we contemplate this mystery and rejoice in God\'s power to sanctify.'],
    2 => ['Purity of Heart', 'Mary\'s Immaculate Conception is the model of interior purity. We ask for grace to guard our hearts, minds, and senses for the glory of God.'],
    3 => ['Maternal Protection', 'As Mother of the Redeemer and Mother of the Church, Mary protects her children. We flee to her Immaculate Heart in every danger.'],
    4 => ['Victory Over Sin', 'Mary\'s preservation from sin foreshadows the victory of Christ over evil. We pray for deliverance from sin and for strength in temptation.'],
    5 => ['Devotion to the Rosary', 'The Immaculate Virgin is the Queen of the Rosary. Today we commit ourselves to praying the Rosary for peace in the world and conversion of sinners.'],
    6 => ['Intercession for the Church', 'Mary intercedes for the whole Church. We pray for the Holy Father, bishops, priests, religious, and all the faithful.'],
    7 => ['Imitation of Mary\'s Virtues', 'Faith, humility, obedience, charity, and silence marked the life of the Immaculate Virgin. We ask for grace to imitate these virtues.'],
    8 => ['Preparation for Her Feast', 'As we approach the feast of the Immaculate Conception on December 8, we renew our love for Mary and our consecration to her.'],
    9 => ['Consecration to the Immaculate Heart', 'On this final day we consecrate ourselves entirely to the Immaculate Heart of Mary, asking her to lead us always to Jesus.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Immaculate Conception',
    'slug' => 'immaculate-conception',
    'category' => 'common',
    'patron_saint' => 'Blessed Virgin Mary',
    'description' => 'This novena prepares us to honor Mary\'s Immaculate Conception, celebrating her preservation from original sin from the first moment of her existence. Traditionally prayed from November 29 through December 7.',
    'opening_prayer' => 'O Mary, conceived without sin, pray for us who have recourse to thee. O Virgin Immaculate, we consecrate ourselves to thee and ask for the grace to imitate thy purity and love.',
    'closing_prayer' => 'O Mary, conceived without sin, pray for us who have recourse to thee. Grant that we may always imitate thy purity and love, and come at last to the glory of thy Son, Jesus Christ. Amen.',
    'days' => $days,
];
