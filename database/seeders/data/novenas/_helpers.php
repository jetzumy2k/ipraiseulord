<?php

/**
 * Shared helpers for Catholic novena seed data.
 */

if (! function_exists('novena_day')) {
    /**
     * @return array{day_number: int, title: string, content: string, prayer: string}
     */
    function novena_day(int $number, string $theme, string $content, string $prayer): array
    {
        return [
            'day_number' => $number,
            'title' => "Day {$number}: {$theme}",
            'content' => $content,
            'prayer' => $prayer,
        ];
    }
}

if (! function_exists('saint_jude_daily_prayer')) {
    function saint_jude_daily_prayer(): string
    {
        return <<<'PRAYER'
Most holy Apostle, Saint Jude, faithful servant and friend of Jesus, the Church honors and invokes you universally as the patron of difficult cases, of things almost despaired of. Pray for me, I am so helpless and alone.

Intercede with God for me, that He bring visible and speedy help where help is almost despaired of. Come to my assistance in this great need that I may receive the consolation and help of Heaven in all my necessities, tribulations, and sufferings, particularly (mention your request here), and that I may praise God with you and all the saints forever.

I promise, O Blessed Saint Jude, to be ever mindful of this great favor granted me by God and to always honor you as my special and powerful patron, and to gratefully encourage devotion to you. Amen.

May the Most Sacred Heart of Jesus be adored and loved in all the tabernacles until the end of time. Amen.

May the Most Sacred Heart of Jesus be praised and glorified now and forever. Amen.

Saint Jude, pray for us and hear our prayers. Amen.

Blessed be the Sacred Heart of Jesus.
Blessed be the Immaculate Heart of Mary.
Blessed be Saint Jude Thaddeus, in all the world and for all Eternity.
PRAYER;
    }
}

if (! function_exists('divine_mercy_day_prayer')) {
    function divine_mercy_day_prayer(string $intention): string
    {
        return "Today bring to Me {$intention} and immerse them in the ocean of My mercy. In this way you will console Me in the bitter grief into which the loss of souls plunges Me.\n\n"
            ."Most Merciful Jesus, whose very nature is to have compassion on us and to forgive us, do not look upon our sins but upon our trust which we place in Your infinite goodness. Receive us all into the abode of Your Most Compassionate Heart, and never let us escape from It. We beg this of You by Your love which unites You to the Father and the Holy Spirit.\n\nEternal Father, I offer You the Body and Blood, Soul and Divinity of Your dearly beloved Son, Our Lord Jesus Christ, in atonement for our sins and those of the whole world. For the sake of His sorrowful Passion, have mercy on us and on the whole world. (Repeat two more times)\n\nHoly God, Holy Mighty One, Holy Immortal One, have mercy on us and on the whole world. (Repeat two more times)";
    }
}

if (! function_exists('st_joseph_closing_prayers')) {
    function st_joseph_closing_prayers(): string
    {
        return <<<'PRAYER'
Saint Joseph, I, your unworthy child, greet you. You are the faithful protector and intercessor of all who love and venerate you. You know that I have special confidence in you and that, after Jesus and Mary, I place all my hope of salvation in you, for you are especially powerful with God and will never abandon your faithful servants. Therefore I humbly invoke you and commend myself, with all who are dear to me and all that belong to me, to your intercession. I beg of you, by your love for Jesus and Mary, not to abandon me during life and to assist me at the hour of my death.

Glorious Saint Joseph, spouse of the Immaculate Virgin, obtain for me a pure, humble, charitable mind, and perfect resignation to the divine Will. Be my guide, my father, and my model through life that I may merit to die as you did in the arms of Jesus and Mary.

Loving Saint Joseph, faithful follower of Jesus Christ, I raise my heart to you to implore your powerful intercession in obtaining from the Divine Heart of Jesus all the graces necessary for my spiritual and temporal welfare, particularly the grace of a happy death, and the special grace I now implore: (mention your request).

Guardian of the Word Incarnate, I feel confident that your prayers in my behalf will be graciously heard before the throne of God. Amen.

MEMORARE: Remember, most pure spouse of Mary, ever Virgin, my loving protector, Saint Joseph, that no one ever had recourse to your protection or asked for your aid without obtaining relief. Confiding, therefore, in your goodness, I come before you and humbly implore you. Despise not my petitions, foster-father of the Redeemer, but graciously receive them. Amen.
PRAYER;
    }
}

if (! function_exists('therese_opening_block')) {
    function therese_opening_block(): string
    {
        return <<<'PRAYER'
Come Holy Spirit and fill the hearts of the faithful, and kindle in them the fire of divine love.

V. Send forth Your Spirit and they shall be created.
R. And You shall renew the face of the earth.

Let us pray: O God, who have instructed the hearts of the faithful by the light of the Holy Spirit; grant that by the gift of the same Spirit, we may be ever truly wise and rejoice in His consolation, through Christ our Lord. Amen.

Acts of Faith, Hope, and Love: O my God! I believe in Thee: strengthen my faith. All my hopes are in Thee: do Thou secure them. I love Thee: teach me to love Thee daily more and more.

The Act of Contrition: O my God! I am heartily sorry for having offended You, and I detest all my sins, because I dread the loss of heaven and the pains of hell, but most of all because they offend You, my God, who are all good and deserving of all my love. I firmly resolve, with the help of Your grace, to confess my sins, to do penance, and to amend my life. Amen.
PRAYER;
    }
}

if (! function_exists('therese_closing_prayer')) {
    function therese_closing_prayer(): string
    {
        return 'O Lord, You have said: Unless you become as little children you shall not enter the kingdom of heaven; grant us, we beg You, so to follow, in humility and simplicity of heart, the footsteps of the Virgin blessed Thérèse, that we may attain to an everlasting reward. Amen.';
    }
}
