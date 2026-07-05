<?php

require __DIR__.'/_helpers.php';

$intentions = [
    1 => ['all mankind, especially sinners', 'All Mankind, Especially Sinners'],
    2 => ['the souls of priests and religious', 'Priests and Religious'],
    3 => ['all devout and faithful souls', 'Devout and Faithful Souls'],
    4 => ['those who do not believe in God and those who do not yet know Him', 'Those Who Do Not Believe'],
    5 => ['the souls of separated brethren', 'Separated Brethren'],
    6 => ['the meek and humble souls and the souls of children', 'The Meek, Humble, and Children'],
    7 => ['the souls who especially glorify and give thanks to My mercy', 'Souls Who Glorify Mercy'],
    8 => ['the souls detained in purgatory', 'The Souls in Purgatory'],
    9 => ['the souls who have become lukewarm', 'The Lukewarm Souls'],
];

$days = [];
foreach ($intentions as $number => [$intention, $theme]) {
    $days[] = novena_day(
        $number,
        $theme,
        "This intention was revealed by Our Lord to Saint Faustina Kowalska as part of the Divine Mercy novena, to be prayed from Good Friday through the Saturday before Divine Mercy Sunday.",
        divine_mercy_day_prayer($intention),
    );
}

return [
    'title' => 'Divine Mercy',
    'slug' => 'divine-mercy',
    'category' => 'common',
    'patron_saint' => 'Jesus Christ, Divine Mercy',
    'description' => 'The Chaplet of Divine Mercy novena revealed to Saint Faustina Kowalska. Pray from Good Friday through the Saturday before Divine Mercy Sunday, entrusting the world to the infinite mercy of God.',
    'opening_prayer' => 'O Blood and Water, which gushed forth from the Heart of Jesus as a fountain of mercy for us, I trust in You. O Eternal God, in whom mercy is endless and the treasury of compassion inexhaustible, look kindly upon us and increase Your mercy in us, that in difficult moments we might not despair nor become despondent, but with great confidence submit ourselves to Your holy will, which is Love and Mercy itself.',
    'closing_prayer' => 'Eternal Father, I offer You the Body and Blood, Soul and Divinity of Your dearly beloved Son, Our Lord Jesus Christ, in atonement for our sins and those of the whole world. For the sake of His sorrowful Passion, have mercy on us and on the whole world. Holy God, Holy Mighty One, Holy Immortal One, have mercy on us and on the whole world. Jesus, I trust in You.',
    'days' => $days,
];
