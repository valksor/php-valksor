<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Text;

final class Functions
{
    use Traits\_Br2nl;
    use Traits\_CamelCase;
    use Traits\_CleanText;
    use Traits\_Compare;
    use Traits\_Contains;
    use Traits\_ContainsAny;
    use Traits\_CountryName;
    use Traits\_CyrillicToLatin;
    use Traits\_HtmlEntityDecode;
    use Traits\_IsHex;
    use Traits\_KeepNumeric;
    use Traits\_LastPart;
    use Traits\_LatinToCyrillic;
    use Traits\_LimitChars;
    use Traits\_LimitWords;
    use Traits\_LongestSubstrLength;
    use Traits\_Nl2br;
    use Traits\_NormalizedValue;
    use Traits\_OneSpace;
    use Traits\_PascalCase;
    use Traits\_Pluralize;
    use Traits\_RandomString;
    use Traits\_ReverseUTF8;
    use Traits\_Sanitize;
    use Traits\_SanitizeFloat;
    use Traits\_ScalarToString;
    use Traits\_Sha;
    use Traits\_Shuffle;
    use Traits\_Singularize;
    use Traits\_SnakeCaseFromCamelCase;
    use Traits\_SnakeCaseFromSentence;
    use Traits\_StripSpace;
    use Traits\_StrStartWithAny;
    use Traits\_ToString;
    use Traits\_TruncateSafe;
    use Traits\_UniqueId;

    public const string BASIC = self::EN_LOWERCASE . self::EN_UPPERCASE . self::DIGITS;
    public const string DIGITS = '0123456789';
    public const string EN_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    public const string EN_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const string EXTENDED = self::BASIC . self::SYMBOLS;
    public const string LV_LOWERCASE = 'aābcčdeēfgģhiījkķlļmnņoprsštuūvzž';
    public const string LV_UPPERCASE = 'AāBCČDEĒFGĢHIĪJKĶLĻMNŅOPRSŠTUŪVZŽ';
    public const array MAP_CYRILLIC = [
        'е', 'ё', 'ж', 'х', 'ц', 'ч', 'ш', 'щ', 'ю', 'я',
        'Е', 'Ё', 'Ж', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ю', 'Я',
        'а', 'б', 'в', 'г', 'д', 'з', 'и', 'й', 'к', 'л',
        'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ъ',
        'ы', 'ь', 'э', 'А', 'Б', 'В', 'Г', 'Д', 'З', 'И',
        'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т',
        'У', 'Ф', 'Ъ', 'Ы', 'Ь', 'Э',
    ];
    public const array MAP_LATIN = [
        'ye', 'ye', 'zh', 'kh', 'ts', 'ch', 'sh', 'shch', 'yu', 'ya',
        'Ye', 'Ye', 'Zh', 'Kh', 'Ts', 'Ch', 'Sh', 'Shch', 'Yu', 'Ya',
        'a', 'b', 'v', 'g', 'd', 'z', 'i', 'y', 'k', 'l',
        'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ʺ',
        'y', '–', 'e', 'A', 'B', 'V', 'G', 'D', 'Z', 'I',
        'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T',
        'U', 'F', 'ʺ', 'Y', '–', 'E',
    ];
    public const string SYMBOLS = '!@#$%^&*()_-=+;:.,?';
    public const string UTF32LE = 'UTF-32LE';
    public const string UTF8 = 'UTF-8';
}
