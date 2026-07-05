<?php

require __DIR__.'/_helpers.php';

$meditations = [
    1 => [
        'The Love of the Sacred Heart',
        'The Sacred Heart of Jesus is the symbol of His infinite love for humanity. He loved us to the end, giving His life on the Cross. Today we contemplate this love and ask for hearts that respond with generous love.',
        "O most holy Heart of Jesus, fountain of every blessing, I adore You, I love You, and with a lively sorrow for my sins, I offer You this poor heart of mine. Make me humble, patient, pure, and wholly obedient to Your will. Grant, good Jesus, that I may live in You and for You. Protect me in the midst of danger; comfort me in my afflictions; give me health of body, assistance in my temporal needs, Your blessing on all that I do, and the grace of a holy death. Amen.",
    ],
    2 => [
        'Reparation for Sins',
        'Many offenses are committed against the loving Heart of Jesus: indifference, ingratitude, coldness, and rejection of His grace. Today we offer prayers of reparation and ask pardon for ourselves and for the whole world.',
        "O sweet Jesus, Whose overflowing charity for men is requited by so much forgetfulness, negligence, and contempt, behold us prostrate before Your altar eager to repair by a special homage the cruel indifference and outrages of ungrateful men. We now offer, all that we can, in reparation for the injuries You receive in the Sacrament of Your love. Amen.",
    ],
    3 => [
        'Consecration to the Sacred Heart',
        'Christ invites every soul to consecrate itself to His Sacred Heart. This consecration is a gift of self, a pledge of love, and a promise of trust in His merciful kingship.',
        "O Sacred Heart of Jesus, I consecrate myself to You anew this day. Take my body, my soul, my faculties, my whole being. I give You my liberty, my memory, my understanding, and my will. All that I have and all that I am, I offer to You without reserve. Dispose of me according to Your good pleasure. Amen.",
    ],
    4 => [
        'The Eucharistic Heart of Jesus',
        'In the Most Blessed Sacrament, the Heart of Jesus beats with love for each soul. Today we adore Him truly present and ask for a deeper love of the Holy Eucharist.',
        "O Jesus, present in the Blessed Sacrament, I adore You and thank You for remaining with us until the end of time. Increase my faith in Your Real Presence. Make me hunger for You, the Bread of Life, and draw me often to visit and adore You in the tabernacle. Amen.",
    ],
    5 => [
        'Mercy and Forgiveness',
        'The Heart of Jesus is a fountain of mercy. He forgives repentant sinners and calls us to forgive others from the heart as He has forgiven us.',
        "Most merciful Jesus, You Yourself have said that You are meek and humble of heart. Grant that we may be like You in patience and humility. Pour Your mercy upon us and upon all sinners. Help us to forgive injuries and to seek reconciliation, that we may be children of Your merciful Heart. Amen.",
    ],
    6 => [
        'Peace in Families',
        'The Sacred Heart blesses Christian families when they live in love, prayer, and fidelity. Today we entrust our homes to the Heart of Jesus and to the Immaculate Heart of Mary.',
        "O most loving Jesus, deign to bless our families. Reign in our homes by Your peace, govern them by Your wisdom, and keep them by Your power. Bless all parents, children, and every member of our household. May the spirit of faith, hope, and charity dwell among us and make our family a little church in the world. Amen.",
    ],
    7 => [
        'Conversion of Sinners',
        'The Heart of Jesus desires the salvation of every soul. We pray today for the conversion of sinners, especially those farthest from God and those who have abandoned the faith.',
        "O Sacred Heart of Jesus, look with pity on those who do not know You, on those who deny You, and on poor sinners who have turned away from Your love. Touch their hearts, enlighten their minds, and draw them back to Your wounded Heart. Use us as instruments of Your mercy in the world. Amen.",
    ],
    8 => [
        'Comfort for the Afflicted',
        'Christ comforts the sorrowful, the sick, the lonely, and the dying. Today we present to His Sacred Heart all who suffer in body or spirit.',
        "O Sacred Heart of Jesus, fountain of every consolation, I present to You all who suffer: the sick, the poor, the grieving, the persecuted, and the dying. Give them strength, hope, and the grace to unite their sufferings with Yours for the salvation of souls. Comfort them with Your divine tenderness. Amen.",
    ],
    9 => [
        'Final Consecration and Thanksgiving',
        'On this final day we renew our consecration to the Sacred Heart and thank God for His countless gifts of love.',
        "Sacred Heart of Jesus, I place all my trust in You. Immaculate Heart of Mary, pray for us. Saint Joseph, pray for us. O Sacred Heart, reign over our families, our parish, our nation, and the whole world. May every heart love, honor, and glorify You now and forever. Amen.",
    ],
];

$days = [];
foreach ($meditations as $number => [$theme, $content, $prayer]) {
    $days[] = novena_day($number, $theme, $content, $prayer);
}

return [
    'title' => 'Sacred Heart of Jesus',
    'slug' => 'sacred-heart-of-jesus',
    'category' => 'common',
    'patron_saint' => 'Sacred Heart of Jesus',
    'description' => 'Nine days of devotion to the Sacred Heart of Jesus, honoring His infinite love and seeking reparation for sins against His most loving Heart.',
    'opening_prayer' => 'O most holy Heart of Jesus, fountain of every blessing, I adore You, I love You, and with a lively sorrow for my sins, I offer You this poor heart of mine. Make me humble, patient, pure, and wholly obedient to Your will.',
    'closing_prayer' => 'Sacred Heart of Jesus, I place all my trust in You. Immaculate Heart of Mary, pray for us. Saint Joseph, pray for us.',
    'days' => $days,
];
