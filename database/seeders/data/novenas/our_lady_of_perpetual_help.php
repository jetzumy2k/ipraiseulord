<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
O Mother of Perpetual Help, grant that I may ever invoke thy most powerful name, which is the safeguard of the living and the salvation of the dying. O Purest Mary, O Sweetest Mary, let thy name henceforth be ever on my lips. Delay not, O Blessed Lady, to help me whenever I call on thee. In all my necessities, in all my trials, thou art my hope, O Lady of Perpetual Help. Amen.

Three Hail Marys in honor of Mary's power, wisdom, and love.
PRAYER;

$themes = [
    1 => ['Confidence in Mary\'s Intercession', 'Under the title of Our Lady of Perpetual Help, Mary is invoked as the Mother who never abandons her children. Today we place our trust in her constant intercession before the throne of her Son.'],
    2 => ['Trust in Divine Mercy', 'The Child Jesus in the icon clings to His Mother while the Archangels present the instruments of the Passion. Mary leads us to the merciful Heart of her Son.'],
    3 => ['Healing of Body and Soul', 'Countless faithful have experienced physical and spiritual healing through devotion to Our Lady of Perpetual Help. We present our infirmities to her maternal care.'],
    4 => ['Protection of Families', 'Mary protects the home, guards children, and strengthens spouses in fidelity and love. We entrust our families to her perpetual help.'],
    5 => ['Guidance for the Church', 'The Mother of the Church intercedes for pastors, religious, missionaries, and all the faithful that the Gospel may flourish throughout the world.'],
    6 => ['Consolation for the Sorrowful', 'Mary stood beneath the Cross and understands every grief. Today we ask her to comfort the afflicted, the bereaved, and all who weep.'],
    7 => ['Strength in Temptation', 'As Mother of Perpetual Help, Mary assists souls in spiritual combat. We beg her help against temptation and for perseverance in virtue.'],
    8 => ['Hope for the Dying', 'At the hour of death, Mary is the refuge of sinners and the help of the dying. We pray for a happy death for ourselves and for all the faithful departed.'],
    9 => ['Perseverance in Faith', 'On this final day we thank Our Lady for her protection and ask for the grace to remain faithful to Christ and His Church until death.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Our Lady of Perpetual Help',
    'slug' => 'our-lady-of-perpetual-help',
    'category' => 'common',
    'patron_saint' => 'Our Lady of Perpetual Help',
    'description' => 'This novena honors Mary under the title of Our Lady of Perpetual Help, asking her maternal intercession for our needs and the needs of the whole Church.',
    'opening_prayer' => 'O Mother of Perpetual Help, grant that I may ever invoke thy most powerful name, which is the safeguard of the living and the salvation of the dying. O Purest Mary, O Sweetest Mary, let thy name henceforth be ever on my lips.',
    'closing_prayer' => 'O Mother of Perpetual Help, grant that I may ever invoke thy most powerful name. Obtain for me from thy beloved Son all the graces I need for my salvation. Amen.',
    'days' => $days,
];
