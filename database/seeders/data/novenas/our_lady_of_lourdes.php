<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
O ever-Immaculate Virgin, Mother of Mercy, Health of the Sick, Refuge of Sinners, Comfort of the Afflicted, you know my wants, my troubles, my sufferings. Look with mercy upon me. By appearing in the Grotto of Lourdes, you were pleased to make it a privileged sanctuary from which you dispense your favors, and already many sufferers have obtained the cure of their infirmities, both spiritual and corporal.

I come, therefore, with the most unbounded confidence to implore your maternal intercession. Obtain, O loving Mother, the granting of my requests. (Mention your petition.)

Through your mercy, console me in the pains, trials, and miseries of this life. Grant me the grace to imitate your virtues, that I may one day share your glory and bless you forever in heaven. Amen.

Our Father. Hail Mary. Glory Be.
PRAYER;

$themes = [
    1 => ['The Apparitions at Lourdes', 'Between February 11 and July 16, 1858, the Blessed Virgin appeared eighteen times to Bernadette Soubirous in the grotto of Massabielle, identifying herself as the Immaculate Conception.'],
    2 => ['The Grotto of Massabielle', 'Mary chose a humble grotto in the Pyrenees to manifest her love. God delights in revealing His glory through the lowly and the poor in spirit.'],
    3 => ['The Spring of Healing', 'At Mary\'s command, Bernadette uncovered a spring whose waters have brought spiritual and physical healing to countless pilgrims.'],
    4 => ['Penance and Conversion', 'Our Lady called for prayer and penance for the conversion of sinners. Today we examine our conscience and offer sacrifices for those who do not know God.'],
    5 => ['Processions and Candles', 'Pilgrims process with candles as a sign of faith and prayer. We ask Mary to keep the light of faith burning in our hearts.'],
    6 => ['The Sick and Suffering', 'Lourdes is a place of special solace for the sick. We entrust all who suffer to Our Lady of Lourdes and to her Son\'s merciful love.'],
    7 => ['Immaculate Conception', 'Mary\'s words at Lourdes confirmed the dogma of the Immaculate Conception proclaimed four years earlier by Pope Pius IX.'],
    8 => ['Prayer for the Church', 'The Church is entrusted to Mary\'s maternal care. We pray for the Holy Father, the clergy, and all the faithful throughout the world.'],
    9 => ['Pilgrimage of Faith', 'On this final day we renew our faith and promise to live as pilgrims toward heaven, following the path shown by Our Lady of Lourdes.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Our Lady of Lourdes',
    'slug' => 'our-lady-of-lourdes',
    'category' => 'common',
    'patron_saint' => 'Our Lady of Lourdes',
    'description' => 'A novena to Our Lady of Lourdes, Health of the Sick and Comforter of the Afflicted, whose apparitions to Saint Bernadette Soubirous continue to draw millions of pilgrims. Her feast is February 11.',
    'opening_prayer' => 'O Brilliant star of purity, Mary Immaculate, Our Lady of Lourdes, glorious in your assumption, triumphant in your coronation, show unto us the mercy of the Eternal Father in the land of the living. Obtain for us the grace to serve God faithfully in this life and to enjoy the vision of His glory in heaven.',
    'closing_prayer' => 'Our Lady of Lourdes, pray for us. Saint Bernadette, pray for us. May we be healed in body and soul and come at last to the joys of everlasting life. Amen.',
    'days' => $days,
];
