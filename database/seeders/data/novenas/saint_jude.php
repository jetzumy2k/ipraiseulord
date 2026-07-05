<?php

require __DIR__.'/_helpers.php';

$dailyPrayer = saint_jude_daily_prayer();

$reflections = [
    'Saint Jude, apostle and martyr, you remained faithful to Christ even when many forgot your name because of the traitor Judas Iscariot. On this first day, we ask for the grace of steadfast faith.',
    'In your Epistle you urged the faithful to contend for the faith once delivered to the saints. Today we pray for perseverance in the true doctrine of the Catholic Church.',
    'You stood at the foot of the Cross with the Blessed Mother and the beloved disciple. We entrust to you our heaviest crosses and darkest trials.',
    'Saint Jude, patron of hopeless cases, teach us to hope against hope and to trust that God can bring good even from desperate situations.',
    'Many turn to you when human remedies have failed. Today we present our most urgent needs, confident that your powerful intercession moves the Heart of Christ.',
    'You labored for the Gospel in hardship and persecution. Obtain for missionaries, priests, and all who spread the faith the courage to persevere.',
    'Saint Jude, friend of Jesus, help us to grow in personal friendship with the Lord through prayer, the sacraments, and acts of charity.',
    'As the Church honors you on the feast of Saints Simon and Jude, we pray for unity among all Christians and for the conversion of sinners.',
    'On this final day, we thank God for the gift of your intercession and promise to honor you and encourage devotion to you among our family and friends.',
];

$days = [];
foreach ($reflections as $index => $reflection) {
    $number = $index + 1;
    $days[] = novena_day(
        $number,
        match ($number) {
            1 => 'Faithful Apostle',
            2 => 'Defender of the Faith',
            3 => 'Companion in Suffering',
            4 => 'Patron of Hopeless Cases',
            5 => 'Speedy Help in Need',
            6 => 'Missionary Zeal',
            7 => 'Friend of Jesus',
            8 => 'Unity and Conversion',
            9 => 'Thanksgiving and Promise',
            default => 'Day '.$number,
        },
        $reflection,
        $dailyPrayer,
    );
}

return [
    'title' => 'Saint Jude Thaddeus',
    'slug' => 'saint-jude',
    'category' => 'common',
    'patron_saint' => 'Saint Jude Thaddeus',
    'description' => 'The traditional novena to Saint Jude, patron of hopeless cases and desperate situations. Pray for nine consecutive days, especially from October 19–27 before his feast on October 28.',
    'opening_prayer' => 'Saint Jude, glorious apostle, faithful servant and friend of Jesus, the name of the traitor who delivered the beloved Master into the hands of His enemies has caused you to be forgotten by many, but the Church honors and invokes you universally as the patron of hopeless cases, of things despaired of. Pray for me who am so miserable; make use, I implore you, of that particular privilege accorded to you, to bring visible and speedy help where help is almost despaired of.',
    'closing_prayer' => 'May the most just, most high, and most adorable Will of God be in all things done, praised, and magnified, forever and ever. Amen. Saint Jude, apostle and martyr, pray for us.',
    'days' => $days,
];
