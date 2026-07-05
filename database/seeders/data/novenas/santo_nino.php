<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = <<<'PRAYER'
Santo Niño, King of kings and Lord of lords, bless us and keep us in Your love. Grant what we ask through Your powerful intercession, if it be for the greater glory of God and the good of our souls. Viva Pit Señor! Amen.

Our Father. Hail Mary. Glory Be.
PRAYER;

$themes = [
    1 => ['The Child Jesus as Our Model', 'The Holy Child of Cebu teaches us to approach God with simplicity, trust, and joy. Today we ask for childlike faith that seeks the Father\'s will above all.'],
    2 => ['Obedience and Humility', 'Though King of the universe, Jesus became a child obedient to Mary and Joseph. We pray for humility and obedience in our daily duties.'],
    3 => ['Blessing of Children', 'Parents and godparents entrust their children to the Santo Niño, asking for protection, purity, and perseverance in the faith.'],
    4 => ['Healing and Wholeness', 'Devotees throughout the Philippines and the world seek the Santo Niño\'s blessing for the sick and suffering. We present our illnesses and burdens to Him.'],
    5 => ['Protection from Harm', 'The Child Jesus protected the Holy Family in danger. We ask His protection over our homes, communities, and nation from every evil.'],
    6 => ['Prosperity of the Gospel', 'The Sinulog and countless feasts honor the Santo Niño as Lord of the land. We pray that the Gospel may flourish and that many souls may come to know Christ.'],
    7 => ['Unity of the Faithful', 'The devotion to the Santo Niño unites families, parishes, and nations in worship. We pray for unity in the Church and peace among all people.'],
    8 => ['Gratitude for Blessings', 'Today we thank the Santo Niño for blessings received and miracles granted through His intercession, especially in the history of the Filipino people.'],
    9 => ['Renewed Commitment to Christ', 'On this final day we renew our baptismal promises and commit ourselves to follow the Child Jesus with renewed fervor and love.'],
];

$days = [];
foreach ($themes as $number => [$theme, $content]) {
    $days[] = novena_day($number, $theme, $content, $dailyPrayer);
}

return [
    'title' => 'Santo Niño',
    'slug' => 'santo-nino',
    'category' => 'common',
    'patron_saint' => 'Santo Niño de Cebu',
    'description' => 'A beloved Filipino devotion to the Holy Child Jesus, seeking His blessing upon families, communities, and the nation. Traditionally prayed before the feast of the Santo Niño on the third Sunday of January.',
    'opening_prayer' => 'Santo Niño, King of kings and Lord of lords, we come before You with childlike trust, asking for Your blessing upon our homes and our land. Look upon us with mercy and grant us the grace to follow You all the days of our life.',
    'closing_prayer' => 'Santo Niño, bless our families, guide our leaders, and lead us ever closer to Your Sacred Heart. Viva Pit Señor!',
    'days' => $days,
];
