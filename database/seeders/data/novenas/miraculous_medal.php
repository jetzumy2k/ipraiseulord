<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
O Immaculate Virgin Mary, we kneel before your image of the Miraculous Medal and gaze upon the symbol of your loving kindness. We ask you to grant us the favors we request through your powerful intercession, if they be for the glory of God and the good of our souls.

Remember, O most gracious Virgin Mary, that never was it known that anyone who fled to thy protection, implored thy help, or sought thy intercession was left unaided. Inspired with this confidence, we fly unto thee, O Virgin of virgins, our Mother; to thee do we come, before thee we stand, sinful and sorrowful. O Mother of the Word Incarnate, despise not our petitions, but in thy mercy hear and answer us. Amen.

O Mary, conceived without sin, pray for us who have recourse to thee.
PRAYER;

$themes = [
    1 => ['Mary Crushes the Serpent', 'On the Miraculous Medal, Mary stands upon the globe with the serpent beneath her feet, signifying her victory over Satan through the grace of the Immaculate Conception.'],
    2 => ['Rays from Her Hands', 'The rays streaming from Mary\'s hands symbolize the graces she obtains for those who ask. Some rays are missing, for many graces are never requested.'],
    3 => ['The Prayer on the Medal', 'The words "O Mary, conceived without sin, pray for us who have recourse to thee" encircle the image, inviting constant invocation of the Immaculate Virgin.'],
    4 => ['The Hearts of Jesus and Mary', 'On the reverse, the Sacred Heart of Jesus and the Immaculate Heart of Mary are surrounded by twelve stars, calling us to devotion to both Hearts.'],
    5 => ['The Cross and the M', 'The cross surmounting the M unites Mary with the sacrifice of her Son. We ask for grace to unite our sufferings with Christ.'],
    6 => ['Conversion of Sinners', 'Our Lady appeared to Saint Catherine Labouré in 1830, asking that a medal be made so that graces would abound for those who wear it with confidence.'],
    7 => ['Protection of the Faithful', 'Millions wear the Miraculous Medal as a sign of trust in Mary\'s protection. Today we ask her to guard us in body and soul.'],
    8 => ['Graces for the Dying', 'Mary promised that all who wear the medal would receive great graces, especially if they wear it around the neck with confidence.'],
    9 => ['Thanksgiving and Consecration', 'On this final day we thank Mary for the gift of the Miraculous Medal and renew our consecration to her Immaculate Heart.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Miraculous Medal',
    'slug' => 'miraculous-medal',
    'category' => 'common',
    'patron_saint' => 'Our Lady of the Miraculous Medal',
    'description' => 'The novena of the Miraculous Medal, revealed by the Blessed Virgin Mary to Saint Catherine Labouré in Paris in 1830. Traditionally prayed from November 18–26 before the feast on November 27.',
    'opening_prayer' => 'O Virgin Mother of God, Mary Immaculate, we dedicate and consecrate ourselves to you under the title of Our Lady of the Miraculous Medal. May the medal be for each of us a sure sign of your affection and a constant reminder of our duties toward you.',
    'closing_prayer' => 'O Mary, conceived without sin, pray for us who have recourse to thee. O Mary, we are yours; grant us your protection and your love. Amen.',
    'days' => $days,
];
