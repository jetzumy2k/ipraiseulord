<?php

namespace App\Services;

class CitationNormalizer
{
    /** @var array<string, string> */
    protected array $bookAliases = [
        'Gn' => 'Genesis',
        'Gen' => 'Genesis',
        'Ex' => 'Exodus',
        'Exo' => 'Exodus',
        'Lv' => 'Leviticus',
        'Lev' => 'Leviticus',
        'Nm' => 'Numbers',
        'Num' => 'Numbers',
        'Dt' => 'Deuteronomy',
        'Deut' => 'Deuteronomy',
        'Jos' => 'Joshua',
        'Jgs' => 'Judges',
        'Jg' => 'Judges',
        'Ru' => 'Ruth',
        '1 Sm' => '1 Samuel',
        '1 S' => '1 Samuel',
        '2 Sm' => '2 Samuel',
        '2 S' => '2 Samuel',
        '1 Kgs' => '1 Kings',
        '1 K' => '1 Kings',
        '2 Kgs' => '2 Kings',
        '2 K' => '2 Kings',
        '1 Chr' => '1 Chronicles',
        '2 Chr' => '2 Chronicles',
        'Ezr' => 'Ezra',
        'Neh' => 'Nehemiah',
        'Tb' => 'Tobit',
        'Jdt' => 'Judith',
        'Est' => 'Esther',
        'Esth' => 'Esther',
        '1 Mc' => '1 Maccabees',
        '2 Mc' => '2 Maccabees',
        'Jb' => 'Job',
        'Ps' => 'Psalm',
        'PS' => 'Psalm',
        'Prv' => 'Proverbs',
        'Prov' => 'Proverbs',
        'Eccl' => 'Ecclesiastes',
        'Qo' => 'Ecclesiastes',
        'Sg' => 'Song of Songs',
        'Wis' => 'Wisdom',
        'Sir' => 'Sirach',
        'Is' => 'Isaiah',
        'Jer' => 'Jeremiah',
        'Lam' => 'Lamentations',
        'Bar' => 'Baruch',
        'Ez' => 'Ezekiel',
        'Dn' => 'Daniel',
        'Dan' => 'Daniel',
        'Hos' => 'Hosea',
        'Jl' => 'Joel',
        'Am' => 'Amos',
        'Ob' => 'Obadiah',
        'Jon' => 'Jonah',
        'Mi' => 'Micah',
        'Na' => 'Nahum',
        'Hb' => 'Habakkuk',
        'Zep' => 'Zephaniah',
        'Hg' => 'Haggai',
        'Zec' => 'Zechariah',
        'Mal' => 'Malachi',
        'Mt' => 'Matthew',
        'M' => 'Matthew',
        'Mk' => 'Mark',
        'Lk' => 'Luke',
        'Jn' => 'John',
        'JN' => 'John',
        'Acts' => 'Acts',
        'Rom' => 'Romans',
        '1 Cor' => '1 Corinthians',
        '2 Cor' => '2 Corinthians',
        'Gal' => 'Galatians',
        'Eph' => 'Ephesians',
        'Phil' => 'Philippians',
        'Col' => 'Colossians',
        '1 Thes' => '1 Thessalonians',
        '1 Thess' => '1 Thessalonians',
        '2 Thes' => '2 Thessalonians',
        '2 Thess' => '2 Thessalonians',
        '1 Tm' => '1 Timothy',
        '1 Tim' => '1 Timothy',
        '2 Tm' => '2 Timothy',
        '2 Tim' => '2 Timothy',
        'Ti' => 'Titus',
        'Tit' => 'Titus',
        'Phlm' => 'Philemon',
        'Heb' => 'Hebrews',
        'HEB' => 'Hebrews',
        'Jas' => 'James',
        '1 Pt' => '1 Peter',
        '1 Pet' => '1 Peter',
        '2 Pt' => '2 Peter',
        '2 Pet' => '2 Peter',
        '1 Jn' => '1 John',
        '1 JN' => '1 John',
        '2 Jn' => '2 John',
        '3 Jn' => '3 John',
        'Jude' => 'Jude',
        'Rv' => 'Revelation',
        'Rev' => 'Revelation',
    ];

    public function normalize(string $citation): string
    {
        $citation = trim($citation);

        if ($citation === '') {
            return '';
        }

        $citation = preg_replace('/^(?:Cf\.|See|SEE)\s+/i', '', $citation) ?? $citation;
        $citation = explode('|', $citation)[0];
        $citation = trim($citation);

        if (preg_match('/^(\d?\s?[A-Za-z]+(?:\s[A-Za-z]+)?)\s+([\d:;,\sa-zA-Z—\-]+)$/u', $citation, $matches)) {
            $book = trim($matches[1]);
            $passage = trim($matches[2]);
            $book = $this->bookAliases[$book] ?? $this->bookAliases[ucwords(strtolower($book))] ?? $book;

            if (str_starts_with(strtolower($book), 'psalm') || strtolower($book) === 'psalm') {
                return 'Psalm '.$passage;
            }

            if (preg_match('/^\d+$/', $book) && preg_match('/^(\d+):/', $passage)) {
                return 'Psalm '.$passage;
            }

            return $book.' '.$passage;
        }

        if (preg_match('/^(\d+):/', $citation)) {
            return 'Psalm '.$citation;
        }

        return $citation;
    }
}
