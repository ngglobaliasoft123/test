<?php
/**
 * ConvertToASCII file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\API;

/**
 * ConvertToASCII file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ConvertToASCII
{
    /**
     * Normalize non-ASCII characters to ASCII counterparts where possible.
     *
     * @param string $str //str
     *
     * @return $str
     */
    private function _squashCharacters($str)
    {
        static $normalizeChars = null;
        if ($normalizeChars === null) {
            $normalizeChars = [
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'Ae',
                'Ā'=>'A','Ă'=>'A','Ą'=>'A', 'Ǻ'=>'A',
                'Ç'=>'C','Ć'=>'C','Ĉ'=>'C','Ċ'=>'C','Č'=>'C',
                'Đ'=>'D',
                'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
                'Ē'=>'E','Ĕ'=>'E','Ė'=>'E','Ę'=>'E','Ě'=>'E',
                'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
                'Ð'=>'Dj',
                'Ñ'=>'N',
                'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
                'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U',
                'Ý'=>'Y',
                'Þ'=>'B',
                'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae',
                'ā'=>'a','ă'=>'a','ą'=>'a', 'ǻ'=>'a',
                'ç'=>'c','ć'=>'c','ĉ'=>'c','ċ'=>'c','č'=>'c',
                'đ'=>'d',
                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
                'ē'=>'e','ĕ'=>'e','ė'=>'e','ę'=>'e','ě'=>'e',
                'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
                'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
                'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',
                'ņ'=>'n',
                'ý'=>'y',
                'þ'=>'b',
                'ÿ'=>'y',
                'Š'=>'S', 'š'=>'s', 'ś' => 's', 'ſ'=>'s',
                'Ž'=>'Z', 'ž'=>'z',
                'ƒ'=>'f',
                'Ĝ'=>'G', 'ğ'=>'g', 'Ġ'=>'G', 'ġ'=>'g', 'Ģ'=>'G', 'ģ'=>'g',
                'Ĥ'=>'H', 'ĥ'=>'h', 'Ħ'=>'H', 'ħ'=>'h',
                'Ĩ'=>'I', 'ĩ'=>'i', 'Ī'=>'I', 'ī'=>'i', 'Ĭ'=>'I', 'ĭ'=>'i', 'Į'=>'I', 'į'=>'i', 'İ'=>'I', 'ı'=>'i',
                'Ĳ'=>'IJ', 'ĳ'=>'ij',
                'Ĵ'=>'j', 'ĵ'=>'j',
                'Ķ'=>'K', 'ķ'=>'k', 'ĸ'=>'k',
                'Ĺ'=>'L', 'ĺ'=>'l', 'Ļ'=>'L', 'ļ'=>'l', 'Ľ'=>'L', 'ľ'=>'l', 'Ŀ'=>'L', 'ŀ'=>'l', 'Ł'=>'L', 'ł'=>'l',
                'Ń'=>'N', 'ń'=>'n', 'Ņ'=>'N', 'ņ'=>'n', 'Ň'=>'N', 'ň'=>'n', 'ŉ'=>'n', 'Ŋ'=>'N', 'ŋ'=>'n',
                'Ō'=>'O', 'ō'=>'o', 'Ŏ'=>'O', 'ŏ'=>'o', 'Ő'=>'O', 'ő'=>'o', 'Œ'=>'OE', 'œ'=>'oe',
                'Ŕ'=>'R', 'ŕ'=>'r', 'Ŗ'=>'R', 'ŗ'=>'r', 'Ř'=>'R', 'ř'=>'r',
                'Ś'=>'S', 'ś'=>'s', 'Ŝ'=>'S', 'ŝ'=>'s', 'Ş'=>'S', 'ş'=>'s', 'Š'=>'S', 'š'=>'s',
                'Ţ'=>'T', 'ţ'=>'t', 'Ť'=>'T', 'ť'=>'t', 'Ŧ'=>'T', 'ŧ'=>'t',
                'Ũ'=>'U', 'ũ'=>'u', 'Ū'=>'U', 'ū'=>'u', 'Ŭ'=>'U', 'ŭ'=>'u', 'Ů'=>'U', 'ů'=>'u', 'Ű'=>'U', 'ű'=>'u',
                'Ǔ'=>'U', 'ǔ'=>'u', 'Ǖ'=>'U', 'ǖ'=>'u', 'Ǘ'=>'U', 'ǘ'=>'u', 'Ǚ'=>'U', 'ǚ'=>'u', 'Ǜ'=>'U', 'ǜ'=>'u',
                'Ų'=>'U', 'ų'=>'u',
                'Ŵ'=>'W', 'ŵ'=>'w',
                'Ŷ'=>'Y', 'ŷ'=>'y',
                'Ǐ'=>'I', 'ǐ'=>'i',
                'Ǒ'=>'O', 'ǒ'=>'o',
                'Ź'=>'Z', 'ź'=>'z', 'Ż'=>'Z', 'ż'=>'z', 'Ž'=>'Z', 'ž'=>'z', 'ſ'=>'f',
                'Ǽ'=>'AE', 'ǽ'=>'ae',
                'Ǿ'=>'O', 'ǿ'=>'o'
            ];
        }
        return strtr($str, $normalizeChars);
    }
    /**
     * Convert all fields in $item to ASCII.
     *
     * Do this by first normalizing the characters (á -> a, ñ -> n, etc.). If any
     * non-ASCII characters remain, replace with a default value.
     *
     * @param array $item   //Array or object containing fields to convert
     * @param array $ignore //The ignore
     *
     * @return null
     */
    public function transliterator(&$item, array $ignore=null)
    {
        if (is_array($item)) {
            foreach ($item as $field => &$value) {
                if (is_array($value)) {
                    $this->transliterator($value);
                } else {
                    // Skip fields in the $ignore array.
                    if ($ignore && in_array($field, $ignore)) {
                        continue;
                    }
                    $this->strReplaceString($value);
                }
            }
        } else {
            $this->strReplaceString($item);
        }
    }

    /**
     * ConvertToASCII strReplaceString
     * replace all fields in $item to ASCII.
     *
     * Do this by first normalizing the characters (á -> a, ñ -> n, etc.). If any
     * non-ASCII characters remain, replace with a default value.
     *
     * @param string $value    //value
     * @param array  $template //The template
     * @param array  $default  //The default
     *
     * @return $value
     */
    public function strReplaceString(&$value, $template=null, $default='')
    {
        // Normalize non-ASCII characters with ASCII counterparts.
        $value = $this->_squashCharacters($value);
        // Replace fields that contain non-ASCII characters with a default.
        if (mb_convert_encoding($value, 'ascii') !== $value) {
            // If template is provided, use the template field, if set.
            if ($template) {
                if (is_object($template) && isset($template->{$field})) {
                    $value = $template->{$field};
                } elseif (is_array($template) && isset($template[$field])) {
                    $value = $template[$field];
                } else {
                    $value = $default;
                }
            } else {
                $value = $default;
            }
        }
        return $value;
    }
}
