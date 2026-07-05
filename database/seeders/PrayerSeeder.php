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
            [
                'title' => 'Prayer to Saint Joseph',
                'category' => 'common',
                'description' => 'Traditional prayer to Saint Joseph, patron of the Universal Church.',
                'content' => "O Saint Joseph, whose protection is so great, so strong, so prompt before the throne of God, I place in you all my interests and desires. O Saint Joseph, do assist me by your powerful intercession, and obtain for me from your Divine Son all spiritual blessings through Jesus Christ, Our Lord; so that having engaged here below your heavenly power, I may offer my thanksgiving and homage to the most loving of Fathers. O Saint Joseph, I never weary contemplating you and Jesus asleep in your arms; I dare not approach while He reposes near your heart. Press Him in my name and kiss His fine head for me, and ask Him to return the kiss when I draw my dying breath. Saint Joseph, patron of departing souls, pray for me. Amen.",
            ],
            [
                'title' => 'Prayer to Saint Anthony',
                'category' => 'common',
                'description' => 'Prayer to Saint Anthony of Padua, patron of lost things.',
                'content' => "O holy Saint Anthony, gentlest of saints, your love for God and charity for His creatures made you worthy when on earth to possess miraculous powers. Encouraged by this thought, I implore you to obtain for me (mention your request). O gentle and loving Saint Anthony, whose heart was ever full of human sympathy, whisper my petition into the ears of the sweet Infant Jesus, who loved to be folded in your arms; and the gratitude of my heart will ever be yours. Amen.",
            ],
            [
                'title' => 'Sub Tuum Praesidium',
                'category' => 'liturgy',
                'description' => 'The oldest known Marian prayer, dating to the third century.',
                'content' => "We fly to thy protection, O holy Mother of God; despise not our petitions in our necessities, but deliver us always from all dangers, O glorious and blessed Virgin. Amen.",
            ],
            [
                'title' => 'Litany of Loreto',
                'category' => 'liturgy',
                'description' => 'The Litany of the Blessed Virgin Mary, approved for public and private prayer.',
                'content' => "Lord, have mercy. Christ, have mercy. Lord, have mercy.\nChrist, hear us. Christ, graciously hear us.\n\nGod the Father of heaven, have mercy on us.\nGod the Son, Redeemer of the world, have mercy on us.\nGod the Holy Spirit, have mercy on us.\nHoly Trinity, one God, have mercy on us.\n\nHoly Mary, pray for us.\nHoly Mother of God, pray for us.\nHoly Virgin of virgins, pray for us.\nMother of Christ, pray for us.\nMother of divine grace, pray for us.\nMother most pure, pray for us.\nMother most chaste, pray for us.\nMother inviolate, pray for us.\nMother undefiled, pray for us.\nMother most amiable, pray for us.\nMother most admirable, pray for us.\nMother of good counsel, pray for us.\nMother of our Creator, pray for us.\nMother of our Savior, pray for us.\nVirgin most prudent, pray for us.\nVirgin most venerable, pray for us.\nVirgin most renowned, pray for us.\nVirgin most powerful, pray for us.\nVirgin most merciful, pray for us.\nVirgin most faithful, pray for us.\nMirror of justice, pray for us.\nSeat of wisdom, pray for us.\nCause of our joy, pray for us.\nSpiritual vessel, pray for us.\nVessel of honor, pray for us.\nSingular vessel of devotion, pray for us.\nMystical rose, pray for us.\nTower of David, pray for us.\nTower of ivory, pray for us.\nHouse of gold, pray for us.\nArk of the covenant, pray for us.\nGate of heaven, pray for us.\nMorning star, pray for us.\nHealth of the sick, pray for us.\nRefuge of sinners, pray for us.\nComforter of the afflicted, pray for us.\nHelp of Christians, pray for us.\nQueen of angels, pray for us.\nQueen of patriarchs, pray for us.\nQueen of prophets, pray for us.\nQueen of apostles, pray for us.\nQueen of martyrs, pray for us.\nQueen of confessors, pray for us.\nQueen of virgins, pray for us.\nQueen of all saints, pray for us.\nQueen conceived without original sin, pray for us.\nQueen assumed into heaven, pray for us.\nQueen of the most holy Rosary, pray for us.\nQueen of families, pray for us.\nQueen of peace, pray for us.\n\nLamb of God, who take away the sins of the world, spare us, O Lord.\nLamb of God, who take away the sins of the world, graciously hear us, O Lord.\nLamb of God, who take away the sins of the world, have mercy on us.\n\nPray for us, O holy Mother of God, that we may be made worthy of the promises of Christ.\n\nLet us pray: Grant, we beseech thee, O Lord God, that we thy servants may enjoy perpetual health of mind and body, and by the glorious intercession of the Blessed Mary, ever Virgin, be delivered from present sorrow and enjoy everlasting happiness. Through Christ our Lord. Amen.",
            ],
            [
                'title' => 'Te Deum',
                'category' => 'liturgy',
                'description' => 'The ancient hymn of thanksgiving to the Holy Trinity.',
                'content' => "We praise thee, O God; we acknowledge thee to be the Lord. All the earth doth worship thee, the Father everlasting. To thee all Angels cry aloud, the Heavens and all the Powers therein. To thee Cherubim and Seraphim continually do cry: Holy, Holy, Holy, Lord God of hosts; Heaven and earth are full of the Majesty of thy glory. The glorious company of the Apostles praise thee. The goodly fellowship of the Prophets praise thee. The noble army of Martyrs praise thee. The holy Church throughout all the world doth acknowledge thee: the Father of an infinite Majesty; thine adorable, true, and only Son; also the Holy Ghost the Comforter. Thou art the King of Glory, O Christ. Thou art the everlasting Son of the Father. When thou tookest upon thee to deliver man, thou didst humble thyself to be born of a Virgin. When thou hadst overcome the sharpness of death, thou didst open the Kingdom of Heaven to all believers. Thou sittest at the right hand of God, in the glory of the Father. We believe that thou shalt come to be our Judge. We therefore pray thee, help thy servants, whom thou hast redeemed with thy precious blood. Make them to be numbered with thy Saints in glory everlasting. O Lord, save thy people, and bless thine heritage. Govern them, and lift them up for ever. Day by day we magnify thee; and we worship thy Name ever, world without end. Vouchsafe, O Lord, to keep us this day without sin. O Lord, have mercy upon us, have mercy upon us. O Lord, let thy mercy be upon us, as our trust is in thee. O Lord, in thee have I trusted; let me never be confounded.",
            ],
            [
                'title' => 'Stabat Mater',
                'category' => 'liturgy',
                'description' => 'Medieval hymn meditating on the sorrowful Mother at the foot of the Cross.',
                'content' => "At the Cross her station keeping, stood the mournful Mother weeping, close to Jesus to the last. Through her heart, His sorrow sharing, all His bitter anguish bearing, now at length the sword has passed. O how sad and sore distressed was that Mother highly blessed of the sole-begotten One! Christ above in torment hangs, she beneath beholds the pangs of her dying glorious Son. Is there one who would not weep, whelmed in miseries so deep, Christ's dear Mother to behold? Can the human heart refrain from partaking in her pain, in that Mother's pain untold? Bruised, derided, cursed, defiled, she beheld her tender Child all with bloody scourges rent; for the sins of His own nation, saw Him hang in desolation, till His spirit forth He sent. O thou Mother! fount of love! Touch my spirit from above, make my heart with thine accord. Make me feel as thou hast felt; make my soul to glow and melt with the love of Christ my Lord. Holy Mother! pierce me through, in my heart each wound renew of my Savior crucified. Let me share with thee His pain, who for all my sins was slain, prostrate at His cross to bide. Christ, when Thou shalt call me hence, be Thy Mother my defense, be Thy cross my victory. While my body here decays, may my soul Thy goodness praise, safe in paradise with Thee. Amen.",
            ],
            [
                'title' => 'Fatima Prayer',
                'category' => 'rosary',
                'description' => 'Prayer taught by the Angel of Peace at Fatima, traditionally said after each decade of the Rosary.',
                'content' => "O my Jesus, forgive us our sins, save us from the fires of hell; lead all souls to Heaven, especially those in most need of Thy mercy. Amen.",
            ],
            [
                'title' => 'Chaplet of Divine Mercy',
                'category' => 'common',
                'description' => 'Opening and closing prayers of the Chaplet of Divine Mercy.',
                'content' => "You expired, Jesus, but the source of life gushed forth for souls, and the ocean of mercy opened up for the whole world. O Fount of Life, unfathomable Divine Mercy, envelop the whole world and empty Yourself out upon us.\n\nO Blood and Water, which gushed forth from the Heart of Jesus as a fountain of mercy for us, I trust in You.\n\nEternal Father, I offer You the Body and Blood, Soul and Divinity of Your dearly beloved Son, Our Lord Jesus Christ, in atonement for our sins and those of the whole world.\n\nFor the sake of His sorrowful Passion, have mercy on us and on the whole world.\n\nHoly God, Holy Mighty One, Holy Immortal One, have mercy on us and on the whole world.\n\nEternal God, in whom mercy is endless and the treasury of compassion inexhaustible, look kindly upon us and increase Your mercy in us, that in difficult moments we might not despair nor become despondent, but with great confidence submit ourselves to Your holy will, which is Love and Mercy itself. Amen.",
            ],
            [
                'title' => 'Prayer of Saint Benedict',
                'category' => 'common',
                'description' => 'Prayer to Saint Benedict, father of Western monasticism.',
                'content' => "O glorious Saint Benedict, sublime model of virtue, pure vessel of God's grace! Behold me humbly kneeling at your feet. I implore you in your loving kindness to pray for me before the throne of God. To you I have recourse in the dangers that daily surround me. Shield me against my selfishness and my indifference to God and to my neighbor. Inspire me to imitate you in all things. May your blessing be with me always, so that I may see and serve Christ in others and work for His kingdom. Amen.",
            ],
            [
                'title' => 'Prayer to Saint Rita',
                'category' => 'common',
                'description' => 'Prayer to Saint Rita of Cascia, patroness of impossible causes.',
                'content' => "O glorious Saint Rita, you who so wonderfully participated in the Passion of our Lord Jesus Christ, obtain for me the grace to suffer with resignation the troubles of this life and to protect me in all my needs, both spiritual and temporal. Saint Rita, advocate of the impossible, pray for us. Amen.",
            ],
            [
                'title' => 'Benedictus',
                'category' => 'liturgy',
                'description' => 'The Canticle of Zechariah from the Gospel of Luke, prayed at Morning Prayer.',
                'content' => "Blessed be the Lord, the God of Israel; he has come to his people and set them free. He has raised up for us a mighty savior, born of the house of his servant David. Through his holy prophets he promised of old that he would save us from our enemies, from the hands of all who hate us. He promised to show mercy to our fathers and to remember his holy covenant. This was the oath he swore to our father Abraham: to set us free from the hands of our enemies, free to worship him without fear, holy and righteous in his sight all the days of our life. You, my child, shall be called the prophet of the Most High; for you will go before the Lord to prepare his way, to give his people knowledge of salvation by the forgiveness of their sins. In the tender compassion of our God the dawn from on high shall break upon us, to shine on those who dwell in darkness and the shadow of death, and to guide our feet into the way of peace. Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.",
            ],
            [
                'title' => 'Nunc Dimittis',
                'category' => 'liturgy',
                'description' => 'The Canticle of Simeon, prayed at Night Prayer.',
                'content' => "Lord, now you let your servant go in peace; your word has been fulfilled: my own eyes have seen the salvation which you have prepared in the sight of every people: a light to reveal you to the nations and the glory of your people Israel. Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.",
            ],
            [
                'title' => 'Prayer Before Mass',
                'category' => 'liturgy',
                'description' => 'Preparation for participation in the Holy Sacrifice of the Mass.',
                'content' => "Almighty and ever-living God, I approach the sacrament of Your only-begotten Son, our Lord Jesus Christ. I come sick to the physician of life, unclean to the fountain of mercy, blind to the light of eternal brightness, poor and needy to the Lord of heaven and earth. Therefore I ask for the fullness of Your mercy, that my heart may be filled with love and my soul may be fed with the food of life. May I receive the Body and Blood of Christ, who gave Himself for me, and may I be united with Him forever. Amen.",
            ],
            [
                'title' => 'Prayer After Mass',
                'category' => 'liturgy',
                'description' => 'Thanksgiving after receiving Holy Communion.',
                'content' => "Soul of Christ, sanctify me. Body of Christ, save me. Blood of Christ, inebriate me. Water from the side of Christ, wash me. Passion of Christ, strengthen me. O good Jesus, hear me. Within Your wounds hide me. Permit me not to be separated from You. From the wicked enemy defend me. At the hour of my death call me and bid me come to You, that with Your saints I may praise You forever and ever. Amen.",
            ],
            [
                'title' => 'Consecration to Mary',
                'category' => 'common',
                'description' => 'Act of consecration to the Immaculate Heart of Mary.',
                'content' => "O Mary, my Queen and my Mother, I give myself entirely to you, and to show my devotion to you, I consecrate to you this day my eyes, my ears, my mouth, my heart, my entire being without reserve. Since I am your own, keep me and guard me as your property and possession. Amen.",
            ],
            [
                'title' => 'Prayer for the Pope',
                'category' => 'liturgy',
                'description' => 'Prayer for the Supreme Pontiff and his intentions.',
                'content' => "V. Let us pray for our Pope.\nR. May the Lord preserve him and give him life, and make him blessed upon the earth, and deliver him not up to the will of his enemies.\n\nV. Let us pray for our benefactors.\nR. Deign, O Lord, to reward with eternal life all those who do good to us for Your name's sake. Amen.",
            ],
            [
                'title' => 'Sign of the Cross',
                'category' => 'common',
                'description' => 'The fundamental Christian prayer marking the beginning and end of prayer.',
                'content' => "In the name of the Father, and of the Son, and of the Holy Spirit. Amen.",
            ],
            [
                'title' => 'Rosary Opening Prayers',
                'category' => 'rosary',
                'description' => 'Traditional prayers said at the beginning of the Rosary.',
                'content' => "Sign of the Cross.\n\nI believe in God, the Father almighty, Creator of heaven and earth, and in Jesus Christ, his only Son, our Lord, who was conceived by the Holy Spirit, born of the Virgin Mary, suffered under Pontius Pilate, was crucified, died and was buried; he descended into hell; on the third day he rose again from the dead; he ascended into heaven, and is seated at the right hand of God the Father almighty; from there he will come to judge the living and the dead. I believe in the Holy Spirit, the holy catholic Church, the communion of saints, the forgiveness of sins, the resurrection of the body, and life everlasting. Amen.\n\nOur Father...\n\nHail Mary (three times) for an increase of faith, hope, and charity.\n\nGlory Be...",
            ],
            [
                'title' => 'Rosary Closing Prayers',
                'category' => 'rosary',
                'description' => 'Traditional prayers said at the end of the Rosary.',
                'content' => "Hail, holy Queen, Mother of mercy, our life, our sweetness, and our hope. To thee do we cry, poor banished children of Eve. To thee do we send up our sighs, mourning and weeping in this valley of tears. Turn then, most gracious advocate, thine eyes of mercy toward us, and after this our exile, show unto us the blessed fruit of thy womb, Jesus. O clement, O loving, O sweet Virgin Mary. Pray for us, O holy Mother of God, that we may be made worthy of the promises of Christ. Amen.\n\nO God, whose only-begotten Son, by His life, death, and resurrection, has purchased for us the rewards of eternal life, grant, we beseech Thee, that meditating upon these mysteries of the most holy Rosary of the Blessed Virgin Mary, we may imitate what they contain and obtain what they promise, through the same Christ our Lord. Amen.",
            ],
            [
                'title' => 'Litany of the Sacred Heart',
                'category' => 'liturgy',
                'description' => 'Litany honoring the Sacred Heart of Jesus.',
                'content' => "Lord, have mercy. Christ, have mercy. Lord, have mercy.\nChrist, hear us. Christ, graciously hear us.\n\nGod the Father of heaven, have mercy on us.\nGod the Son, Redeemer of the world, have mercy on us.\nGod the Holy Spirit, have mercy on us.\nHoly Trinity, one God, have mercy on us.\n\nHeart of Jesus, Son of the eternal Father, have mercy on us.\nHeart of Jesus, formed by the Holy Spirit in the womb of the Virgin Mother, have mercy on us.\nHeart of Jesus, substantially united to the Word of God, have mercy on us.\nHeart of Jesus, of infinite majesty, have mercy on us.\nHeart of Jesus, holy temple of God, have mercy on us.\nHeart of Jesus, house of God and gate of heaven, have mercy on us.\nHeart of Jesus, burning furnace of charity, have mercy on us.\nHeart of Jesus, vessel of justice and love, have mercy on us.\nHeart of Jesus, full of goodness and love, have mercy on us.\nHeart of Jesus, abyss of all virtues, have mercy on us.\nHeart of Jesus, most worthy of all praise, have mercy on us.\nHeart of Jesus, king and center of all hearts, have mercy on us.\nHeart of Jesus, in whom are all the treasures of wisdom and knowledge, have mercy on us.\nHeart of Jesus, in whom dwells all the fullness of the Godhead, have mercy on us.\nHeart of Jesus, in whom the Father was well pleased, have mercy on us.\nHeart of Jesus, of whose fullness we have all received, have mercy on us.\nHeart of Jesus, desire of the everlasting hills, have mercy on us.\nHeart of Jesus, patient and rich in mercy, have mercy on us.\nHeart of Jesus, rich unto all who call upon You, have mercy on us.\nHeart of Jesus, fount of life and holiness, have mercy on us.\nHeart of Jesus, propitiation for our sins, have mercy on us.\nHeart of Jesus, saturated with revilings, have mercy on us.\nHeart of Jesus, crushed for our iniquities, have mercy on us.\nHeart of Jesus, made obedient unto death, have mercy on us.\nHeart of Jesus, pierced with a lance, have mercy on us.\nHeart of Jesus, source of all consolation, have mercy on us.\nHeart of Jesus, our life and resurrection, have mercy on us.\nHeart of Jesus, our peace and reconciliation, have mercy on us.\nHeart of Jesus, victim for our sins, have mercy on us.\nHeart of Jesus, salvation of those who hope in You, have mercy on us.\nHeart of Jesus, hope of those who die in You, have mercy on us.\nHeart of Jesus, delight of all saints, have mercy on us.\n\nLamb of God, who take away the sins of the world, spare us, O Lord.\nLamb of God, who take away the sins of the world, graciously hear us, O Lord.\nLamb of God, who take away the sins of the world, have mercy on us.\n\nJesus, meek and humble of heart, make our hearts like unto Thine.",
            ],
            [
                'title' => 'Prayer for the Dead',
                'category' => 'common',
                'description' => 'Traditional prayer for the faithful departed.',
                'content' => "Eternal rest grant unto them, O Lord, and let perpetual light shine upon them. May they rest in peace. Amen.\n\nO Lord, deal not with us according to our sins, nor reward us according to our iniquities. Let us pray also for our deceased brethren, relatives, and benefactors. Vouchsafe, O Lord, to grant them eternal rest, and let perpetual light shine upon them. Through Christ our Lord. Amen.",
            ],
            [
                'title' => 'Prayer to Saint Michael (Long Form)',
                'category' => 'liturgy',
                'description' => 'The full Leonine prayer to Saint Michael the Archangel.',
                'content' => "O glorious Prince of the heavenly hosts, Saint Michael the Archangel, defend us in the battle and in the terrible warfare that we are waging against the principalities and powers, against the rulers of this world of darkness, against the evil spirits. Come to the aid of man, whom Almighty God created immortal, made in His own image and likeness, and redeemed at a great price from the tyranny of Satan.\n\nFight this day the battle of the Lord, together with the holy angels, as already thou hast fought the leader of the proud angels, Lucifer, and his apostate host, who were powerless to resist thee, nor was there place for them any longer in Heaven. That cruel, ancient serpent, who is called the devil or Satan who seduces the whole world, was cast into the abyss with his angels. Behold, this primeval enemy and slayer of men has taken courage. Transformed into an angel of light, he wanders about with all the multitude of wicked spirits, invading the earth in order to blot out the name of God and of His Christ, to seize upon, slay and cast into eternal perdition souls destined for the crown of eternal glory.\n\nThis wicked dragon pours out, as a most impure flood, the venom of his malice on men of depraved mind and corrupt heart, the spirit of lying, of impiety, of blasphemy, and the pestilent breath of impurity, and of every vice and iniquity. These most crafty enemies have filled and inebriated with gall and bitterness the Church, the spouse of the immaculate Lamb, and have laid impious hands on her most sacred possessions.\n\nIn the Holy Place itself, where has been set up the See of the most holy Peter and the Chair of Truth for the light of the world, they have raised the throne of their abominable impiety, with the iniquitous design that the Shepherd may be struck and the flock scattered.\n\nArise then, O invincible Prince, bring help against the attacks of the lost spirits to the people of God, and give them the victory. They venerate thee as their protector and patron; in thee holy Church glories as her defense against the malicious power of hell; to thee has God entrusted the souls of men to be established in heavenly beatitude. Oh, pray to the God of peace that He may put Satan under our feet, so far conquered that he may no longer be able to hold men in captivity and harm the Church. Offer our prayers in the sight of the Most High, so that they may quickly find mercy in the sight of the Lord; and vanquishing the dragon, the ancient serpent, who is the devil and Satan, do thou again make him captive in the abyss, that he may no longer seduce the nations. Amen.",
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
