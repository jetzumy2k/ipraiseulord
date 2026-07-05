<?php

namespace Database\Seeders;

use App\Models\Prayer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PrayerSeeder extends Seeder
{
    public function run(): void
    {
        $prayers = [
            [
                'title' => 'Our Father',
                'category' => 'common',
                'description' => 'The Lord\'s Prayer, taught by Jesus Christ.',
                'content' => "Our Father, who art in heaven, hallowed be thy name; thy kingdom come; thy will be done on earth as it is in heaven. Give us this day our daily bread; and forgive us our trespasses as we forgive those who trespass against us; and lead us not into temptation, but deliver us from evil. Amen.",
            ],
            [
                'title' => 'Hail Mary',
                'category' => 'common',
                'description' => 'The Angelic Salutation to the Blessed Virgin Mary.',
                'content' => "Hail Mary, full of grace, the Lord is with thee. Blessed art thou among women, and blessed is the fruit of thy womb, Jesus. Holy Mary, Mother of God, pray for us sinners, now and at the hour of our death. Amen.",
            ],
            [
                'title' => 'Glory Be',
                'category' => 'common',
                'description' => 'The Doxology praising the Holy Trinity.',
                'content' => "Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.",
            ],
            [
                'title' => 'Apostles\' Creed',
                'category' => 'common',
                'description' => 'The ancient profession of faith used at Baptism.',
                'content' => "I believe in God, the Father almighty, Creator of heaven and earth, and in Jesus Christ, his only Son, our Lord, who was conceived by the Holy Spirit, born of the Virgin Mary, suffered under Pontius Pilate, was crucified, died and was buried; he descended into hell; on the third day he rose again from the dead; he ascended into heaven, and is seated at the right hand of God the Father almighty; from there he will come to judge the living and the dead. I believe in the Holy Spirit, the holy catholic Church, the communion of saints, the forgiveness of sins, the resurrection of the body, and life everlasting. Amen.",
            ],
            [
                'title' => 'Act of Contrition',
                'category' => 'common',
                'description' => 'A prayer expressing sorrow for sin.',
                'content' => "O my God, I am heartily sorry for having offended Thee, and I detest all my sins because of thy just punishments, but most of all because they offend Thee, my God, who art all good and deserving of all my love. I firmly resolve with the help of Thy grace to sin no more and to avoid the near occasion of sin. Amen.",
            ],
            [
                'title' => 'Morning Offering',
                'category' => 'common',
                'description' => 'Offer the day to the Sacred Heart of Jesus.',
                'content' => "O Jesus, through the Immaculate Heart of Mary, I offer You my prayers, works, joys, and sufferings of this day for all the intentions of Your Sacred Heart, in union with the Holy Sacrifice of the Mass throughout the world, in reparation for my sins, for the intentions of all my relatives and friends, and in particular for the intentions of the Holy Father. Amen.",
            ],
            [
                'title' => 'The Angelus',
                'category' => 'liturgy',
                'description' => 'Traditional prayer commemorating the Incarnation, prayed at 6am, noon, and 6pm.',
                'content' => "V. The Angel of the Lord declared unto Mary.\nR. And she conceived of the Holy Spirit.\nHail Mary...\n\nV. Behold the handmaid of the Lord.\nR. Be it done unto me according to thy word.\nHail Mary...\n\nV. And the Word was made flesh.\nR. And dwelt among us.\nHail Mary...\n\nV. Pray for us, O holy Mother of God.\nR. That we may be made worthy of the promises of Christ.\n\nLet us pray: Pour forth, we beseech Thee, O Lord, Thy grace into our hearts; that we, to whom the Incarnation of Christ, Thy Son, was made known by the message of an angel, may by His Passion and Cross be brought to the glory of His Resurrection. Through the same Christ our Lord. Amen.",
            ],
            [
                'title' => 'Memorare',
                'category' => 'common',
                'description' => 'Prayer of trust in the intercession of the Blessed Virgin Mary.',
                'content' => "Remember, O most gracious Virgin Mary, that never was it known that anyone who fled to thy protection, implored thy help, or sought thy intercession was left unaided. Inspired with this confidence, I fly unto thee, O Virgin of virgins, my Mother; to thee do I come, before thee I stand, sinful and sorrowful. O Mother of the Word Incarnate, despise not my petitions, but in thy mercy hear and answer me. Amen.",
            ],
            [
                'title' => 'Hail Holy Queen',
                'category' => 'common',
                'description' => 'The Salve Regina, traditionally prayed at the end of the Rosary.',
                'content' => "Hail, holy Queen, Mother of mercy, our life, our sweetness, and our hope. To thee do we cry, poor banished children of Eve. To thee do we send up our sighs, mourning and weeping in this valley of tears. Turn then, most gracious advocate, thine eyes of mercy toward us, and after this our exile, show unto us the blessed fruit of thy womb, Jesus. O clement, O loving, O sweet Virgin Mary. Pray for us, O holy Mother of God, that we may be made worthy of the promises of Christ. Amen.",
            ],
            [
                'title' => 'Prayer to Saint Michael',
                'category' => 'common',
                'description' => 'Prayer for protection against evil.',
                'content' => "Saint Michael the Archangel, defend us in battle. Be our protection against the wickedness and snares of the devil. May God rebuke him, we humbly pray, and do thou, O Prince of the heavenly hosts, by the power of God, cast into hell Satan and all the evil spirits who prowl about the world seeking the ruin of souls. Amen.",
            ],
            [
                'title' => 'Act of Faith',
                'category' => 'common',
                'description' => 'Profession of belief in God and His revelation.',
                'content' => "O my God, I firmly believe that you are one God in three divine Persons, Father, Son, and Holy Spirit. I believe that your divine Son became man and died for our sins, and that he will come to judge the living and the dead. I believe these and all the truths which the holy Catholic Church teaches, because you have revealed them, who can neither deceive nor be deceived. Amen.",
            ],
            [
                'title' => 'Act of Hope',
                'category' => 'common',
                'description' => 'Expression of trust in God\'s promises.',
                'content' => "O my God, relying on your infinite mercy and promises, I hope to obtain pardon of my sins, the help of your grace, and life everlasting, through the merits of Jesus Christ, my Lord and Redeemer. Amen.",
            ],
            [
                'title' => 'Act of Love',
                'category' => 'common',
                'description' => 'Expression of love for God above all things.',
                'content' => "O my God, I love you above all things, with my whole heart and soul, because you are all-good and worthy of all love. I love my neighbor as myself for the love of you. I forgive all who have injured me, and ask pardon of all whom I have injured. Amen.",
            ],
            [
                'title' => 'Grace Before Meals',
                'category' => 'common',
                'description' => 'Blessing before eating.',
                'content' => "Bless us, O Lord, and these thy gifts, which we are about to receive from thy bounty, through Christ our Lord. Amen.",
            ],
            [
                'title' => 'Grace After Meals',
                'category' => 'common',
                'description' => 'Thanksgiving after eating.',
                'content' => "We give thee thanks, almighty God, for all thy benefits, who livest and reignest, world without end. Amen.",
            ],
            [
                'title' => 'Prayer of Saint Francis',
                'category' => 'other',
                'description' => 'Prayer for peace attributed to Saint Francis of Assisi.',
                'content' => "Lord, make me an instrument of your peace. Where there is hatred, let me sow love; where there is injury, pardon; where there is doubt, faith; where there is despair, hope; where there is darkness, light; where there is sadness, joy. O Divine Master, grant that I may not so much seek to be consoled as to console; to be understood as to understand; to be loved as to love. For it is in giving that we receive; in pardoning that we are pardoned; and in dying that we are born to eternal life. Amen.",
            ],
            [
                'title' => 'Prayer to the Holy Spirit',
                'category' => 'liturgy',
                'description' => 'Invocation of the Holy Spirit for guidance.',
                'content' => "Come, Holy Spirit, fill the hearts of your faithful and kindle in them the fire of your love. Send forth your Spirit and they shall be created, and you shall renew the face of the earth. O God, who by the light of the Holy Spirit did instruct the hearts of the faithful, grant that by the same Holy Spirit we may be truly wise and ever enjoy his consolations. Through Christ our Lord. Amen.",
            ],
            [
                'title' => 'Regina Caeli',
                'category' => 'liturgy',
                'description' => 'Easter season prayer replacing the Angelus.',
                'content' => "V. Queen of Heaven, rejoice, alleluia.\nR. For he whom you merited to bear, alleluia.\nV. Has risen, as he said, alleluia.\nR. Pray for us to God, alleluia.\nV. Rejoice and be glad, O Virgin Mary, alleluia.\nR. For the Lord has truly risen, alleluia.\n\nLet us pray: O God, who gave joy to the world through the resurrection of Thy Son, our Lord Jesus Christ, grant we beseech Thee, that through the intercession of the Virgin Mary, His Mother, we may obtain the joys of everlasting life. Through the same Christ our Lord. Amen.",
            ],
            [
                'title' => 'Anima Christi',
                'category' => 'liturgy',
                'description' => 'Prayer of devotion to the Passion of Christ.',
                'content' => "Soul of Christ, sanctify me. Body of Christ, save me. Blood of Christ, inebriate me. Water from the side of Christ, wash me. Passion of Christ, strengthen me. O good Jesus, hear me. Within thy wounds hide me. Suffer me not to be separated from thee. From the malicious enemy defend me. In the hour of my death call me, and bid me come unto thee, that with thy saints I may praise thee forever and ever. Amen.",
            ],
            [
                'title' => 'Eternal Rest',
                'category' => 'common',
                'description' => 'Prayer for the faithful departed.',
                'content' => "Eternal rest grant unto them, O Lord, and let perpetual light shine upon them. May they rest in peace. Amen.",
            ],
            [
                'title' => 'Magnificat',
                'category' => 'liturgy',
                'description' => 'The Canticle of Mary from the Gospel of Luke.',
                'content' => "My soul proclaims the greatness of the Lord, my spirit rejoices in God my Savior, for he has looked with favor on his lowly servant. From this day all generations will call me blessed: the Almighty has done great things for me, and holy is his Name. He has mercy on those who fear him in every generation. He has shown the strength of his arm, he has scattered the proud in their conceit. He has cast down the mighty from their thrones, and has lifted up the lowly. He has filled the hungry with good things, and the rich he has sent away empty. He has come to the help of his servant Israel, for he has remembered his promise of mercy, the promise he made to our fathers, to Abraham and his children forever. Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.",
            ],
            [
                'title' => 'Prayer to Guardian Angel',
                'category' => 'common',
                'description' => 'Prayer asking one\'s guardian angel for protection.',
                'content' => "Angel of God, my guardian dear, to whom God's love commits me here, ever this day be at my side, to light and guard, to rule and guide. Amen.",
            ],
            [
                'title' => 'Divine Praises',
                'category' => 'liturgy',
                'description' => 'Prayer of adoration of the Blessed Sacrament.',
                'content' => "Blessed be God. Blessed be his holy Name. Blessed be Jesus Christ, true God and true man. Blessed be the name of Jesus. Blessed be his most Sacred Heart. Blessed be his most Precious Blood. Blessed be Jesus in the most holy Sacrament of the Altar. Blessed be the Holy Spirit, the Paraclete. Blessed be the great Mother of God, Mary most holy. Blessed be her holy and Immaculate Conception. Blessed be her glorious Assumption. Blessed be the name of Mary, Virgin and Mother. Blessed be Saint Joseph, her most chaste spouse. Blessed be God in his angels and in his saints.",
            ],
            [
                'title' => 'Nicene Creed',
                'category' => 'liturgy',
                'description' => 'The profession of faith proclaimed at Mass.',
                'content' => "I believe in one God, the Father almighty, maker of heaven and earth, of all things visible and invisible. I believe in one Lord Jesus Christ, the Only Begotten Son of God, born of the Father before all ages. God from God, Light from Light, true God from true God, begotten, not made, consubstantial with the Father; through him all things were made. For us men and for our salvation he came down from heaven, and by the Holy Spirit was incarnate of the Virgin Mary, and became man. For our sake he was crucified under Pontius Pilate, he suffered death and was buried, and rose again on the third day in accordance with the Scriptures. He ascended into heaven and is seated at the right hand of the Father. He will come again in glory to judge the living and the dead and his kingdom will have no end. I believe in the Holy Spirit, the Lord, the giver of life, who proceeds from the Father and the Son, who with the Father and the Son is adored and glorified, who has spoken through the prophets. I believe in one, holy, catholic and apostolic Church. I confess one Baptism for the forgiveness of sins and I look forward to the resurrection of the dead and the life of the world to come. Amen.",
            ],
            [
                'title' => 'Prayer for Vocations',
                'category' => 'other',
                'description' => 'Prayer for priestly and religious vocations.',
                'content' => "O God, who chose the Apostles to make disciples of all nations, mercifully grant that your Church, by their teaching and example, may always be one in the faith and in the communion of love. Grant that your people, called to holiness and anointed by the Holy Spirit as prophet, priest, and king, may ever praise you in word and in deed. Raise up worthy ministers of your altar, and shepherd your people in the way of salvation. Through Christ our Lord. Amen.",
            ],
        ];

        foreach ($prayers as $prayer) {
            Prayer::updateOrCreate(
                ['slug' => Str::slug($prayer['title'])],
                array_merge($prayer, ['is_active' => true])
            );
        }
    }
}
