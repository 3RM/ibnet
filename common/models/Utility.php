<?php
namespace common\models;

use yii;
use yii\base\Model;

/**
 * Utility Class
 */
class Utility
{
	/**
     * "Pretty Print" an array
     * Use: Utility:pp($array);
     */
    public static function pp($var, $die=True) {
        echo '<pre>'; var_dump($var); echo '</pre>'; 
        if ($die) die;
    }

   /**
     * Round to nearest multiple of 5
     * Credit: SW4 @ http://stackoverflow.com/questions/4133859/round-up-to-nearest-multiple-of-five-in-php
     * 
     * @return string
     */
    public static function roundUpToAny($n,$x=5)
    {       
        return (round($n)%$x === 0) ? round($n) : round(($n+$x/2)/$x)*$x;
    }

    /**
     * Generate a unique random string
     * @return string
     */
    public static function generateUniqueRandomString($model, $attribute, $length=12, $alphaNum=false) 
    {     
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = $alphaNum ?
          substr(str_shuffle($permitted_chars), 0, $length) :
          Yii::$app->getSecurity()->generateRandomString($length);

        if (!$model->findOne([$attribute => $randomString])) {
            return $randomString;
        } else {
            return $this->generateUniqueRandomString($attribute, $length);
        }            
    }

    /**
     * Check if remote file exists
     * Credit: https://stackoverflow.com/questions/10444059/file-exists-returns-false-even-if-file-exist-remote-url/24654023
     * 
     * @return string
     */
    public static function remoteFileExists($url)
    {
        $file_headers = @get_headers($url);
        if($file_headers[0] == 'HTTP/1.0 404 Not Found') {
            return false; // The file at $url does not exist
        } else if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found'){
            return false; // The file at $url does not exist, and I got redirected to a custom 404 page
        } else {
            return true;
        }
    }

    /**
     * Performs a GET request on a given URL.
     * 
     * @param string $url The URL that you want to send the GET request to.
     * @return string The output.
     * @throws Exception If the request fails.
     */
    public static function get($url, $headers = NULL)
    {
        //Is this a valid string?
        if (!self::isValidString($url)) {
            return false;
            // throw new HTTPException('$url is not a valid string.');
        }
        //Figure out whether we need to use cURL or not.
        $useCurl = true;
        if (!function_exists('curl_init')) {
            $useCurl = false;
        }
        //If cURL is not installed, use file_get_contents.
        if ($useCurl === false) {
            $res = file_get_contents($url);
            if ($res === false) {
                //Something went wrong.
                return false;
                // throw new HTTPException('Request failed: ' . $url);
            }
            return $res;
        }
        //cURL is installed.
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        isset($headers) ? curl_setopt($ch, CURLOPT_HTTPHEADER, $headers) : NULL;

        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
            // throw new HTTPException('Request failed: ' . $url . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        return $res;       
    }

    /**
     * Performs a POST request on a given URL.
     * 
     * @param string $url The URL that you want to send the POST request to.
     * @param array $post The post fields, e.g. $post = ['user' => 'user1', 'pass' => 'pass1']
     * @return string The output.
     * @throws Exception If the request fails.
     */
    public static function post($url, $params)
    {
        //Is this a valid string?
        if (!self::isValidString($url)) {
            // return false;
            throw new HTTPException('$url is not a valid string.');
        }
        //Figure out whether we need to use cURL or not.
        $useCurl = true;
        if (!function_exists('curl_init')) {
            $useCurl = false;  
        }
        //If cURL is not installed, use file_get_contents.
        if ($useCurl === false) {
            $res = file_get_contents($url);
            if ($res === false) {
                //Something went wrong.
                // return false;
                throw new HTTPException('Request failed: ' . $url);
            }
            return $res;
        }
        //cURL is installed.
        $ch = curl_init($url); 
        $opts = self::$CURL_OPTS;  
        $opts[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
        curl_setopt_array($ch, $opts);
        $res = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // return false;
            throw new HTTPException('Request failed: ' . $url . ' - ' . curl_error($ch));
        }
        curl_close($ch);
        
        return $res;       
    }

    /**
     * Check to see whether a variable is a valid string or not.
     * 
     * @param mixed $var
     * @return boolean TRUE if it is a valid string. FALSE if it isn't.
     */
    protected static function isValidString($var){
        if(!is_string($var)){
            return false;
        } else{
            if(strlen(trim($var)) == 0){
                return false;
            }
        }
        return true;
    }

    /**
     * Returns text truncated to specified length; truncates at $break.
     * Original PHP code by Chirp Internet: www.chirp.com.au
     * 
     * @param string $input
     * @param int $limit
     * @param string $break
     * @param string $pad
     * @return string
     */
    public static function trimText($string, $limit, $break=".", $pad="...", $strip=true)
    {
        // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit) return $string;
    
        // If $break is "~", then ignore $break and limit based on string count
        if ($break == "~") {


        // is $break present between $limit and the end of the string?
        } elseif (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
              $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $strip == true ? strip_tags($string) : $string;
    }


    /**
     * Get size of a server directory
     * 
     * @param mixed $dir
     * @return boolean TRUE if it is a valid string. FALSE if it isn't.
     */
    public static function getTotalSize($dir)
    {
        $dir = rtrim(str_replace('\\', '/', $dir), '/');
    
        if (is_dir($dir) === true) {
            $totalSize = 0;
            $os        = strtoupper(substr(PHP_OS, 0, 3));
            // If on a Unix Host (Linux, Mac OS)
            if ($os !== 'WIN') {
                $io = popen('/usr/bin/du -sb ' . $dir, 'r');
                if ($io !== false) {
                    $totalSize = intval(fgets($io, 80));
                    pclose($io);
                    return $totalSize;
                }
            }
            // If on a Windows Host (WIN32, WINNT, Windows)
            if ($os === 'WIN' && extension_loaded('com_dotnet')) {
                $obj = new \COM('scripting.filesystemobject');
                if (is_object($obj)) {
                    $ref       = $obj->getfolder($dir);
                    $totalSize = $ref->size;
                    $obj       = null;
                    return $totalSize;
                }
            }
            // If System calls did't work, use slower PHP 5
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
            return $totalSize;
        } else if (is_file($dir) === true) {
            return filesize($dir);
        }
    }

    /**
     * Return a url-friendly name
     * @param string $name
     * @return string
     */
    public static function urlName($name)
    {
        $convertName = self::convert_accent_characters($name);
        return preg_replace("/[^a-zA-Z0-9-]/", "", str_replace(' ', '-', strtolower(trim($convertName))));
    }

    /**
     * Converts all UTF-8 accent characters to ASCII characters.
     *
     * Extracted from https://github.com/rap2hpoutre/convert-accent-characters/blob/master/src/convert_accent_characters.php
     *
     * @param $string
     * @param null $locale
     * @return string
     */
    public static function convert_accent_characters($string, $locale = null)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
        $chars = [
            // Decompositions for Latin-1 Supplement
            'ª' => 'a', 'º'=>'o', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
            'Þ' => 'TH', 'ß' => 's', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 
            'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y', 'Ø' => 'O',
            // Decompositions for Latin Extended-A
            'Ā' => 'A', 'ā' => 'a', 'Ă' => 'A', 'ă' => 'a', 'Ą' => 'A', 'ą' => 'a', 'Ć' => 'C', 'ć' => 'c', 'Ĉ' => 'C', 'ĉ' => 'c', 
            'Ċ' => 'C', 'ċ' => 'c', 'Č' => 'C', 'č' => 'c', 'Ď' => 'D', 'ď' => 'd', 'Đ' => 'D', 'đ' => 'd', 'Ē' => 'E', 'ē' => 'e', 
            'Ĕ' => 'E', 'ĕ' => 'e', 'Ė' => 'E', 'ė' => 'e', 'Ę' => 'E', 'ę' => 'e', 'Ě' => 'E', 'ě' => 'e', 'Ĝ' => 'G', 'ĝ' => 'g', 
            'Ğ' => 'G', 'ğ' => 'g', 'Ġ' => 'G', 'ġ' => 'g', 'Ģ' => 'G', 'ģ' => 'g', 'Ĥ' => 'H', 'ĥ' => 'h', 'Ħ' => 'H', 'ħ' => 'h', 
            'Ĩ' => 'I', 'ĩ' => 'i', 'Ī' => 'I', 'ī' => 'i', 'Ĭ' => 'I', 'ĭ' => 'i', 'Į' => 'I', 'į' => 'i', 'İ' => 'I', 'ı' => 'i', 
            'Ĳ' => 'IJ', 'ĳ' => 'ij', 'Ĵ' => 'J', 'ĵ' => 'j', 'Ķ' => 'K', 'ķ' => 'k', 'ĸ' => 'k', 'Ĺ' => 'L', 'ĺ' => 'l', 'Ļ' => 'L', 
            'ļ' => 'l', 'Ľ' => 'L', 'ľ' => 'l', 'Ŀ' => 'L', 'ŀ' => 'l', 'Ł' => 'L', 'ł' => 'l', 'Ń' => 'N', 'ń' => 'n', 'Ņ' => 'N', 
            'ņ' => 'n', 'Ň' => 'N', 'ň' => 'n', 'ŉ' => 'n', 'Ŋ' => 'N', 'ŋ' => 'n', 'Ō' => 'O', 'ō' => 'o', 'Ŏ' => 'O', 'ŏ' => 'o',
            'Ő' => 'O', 'ő' => 'o', 'Œ' => 'OE', 'œ' => 'oe', 'Ŕ' => 'R', 'ŕ' => 'r', 'Ŗ' => 'R', 'ŗ' => 'r', 'Ř' => 'R', 'ř' => 'r', 
            'Ś' => 'S', 'ś' => 's', 'Ŝ' => 'S', 'ŝ' => 's', 'Ş' => 'S', 'ş' => 's', 'Š' => 'S', 'š' => 's', 'Ţ' => 'T', 'ţ' => 't', 
            'Ť' => 'T', 'ť' => 't', 'Ŧ' => 'T', 'ŧ' => 't', 'Ũ' => 'U', 'ũ' => 'u', 'Ū' => 'U', 'ū' => 'u', 'Ŭ' => 'U', 'ŭ' => 'u',
            'Ů' => 'U', 'ů' => 'u', 'Ű' => 'U', 'ű' => 'u', 'Ų' => 'U', 'ų' => 'u', 'Ŵ' => 'W', 'ŵ' => 'w', 'Ŷ' => 'Y', 'ŷ' => 'y', 
            'Ÿ' => 'Y', 'Ź' => 'Z', 'ź' => 'z', 'Ż' => 'Z', 'ż' => 'z', 'Ž' => 'Z', 'ž' => 'z', 'ſ' => 's',
            // Decompositions for Latin Extended-B
            'Ș' => 'S', 'ș' => 's', 'Ț' => 'T', 'ț' => 't',
            // Euro Sign
            '€' => 'E',
            // GBP (Pound) Sign
            '£' => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            'Ơ' => 'O', 'ơ' => 'o', 'Ư' => 'U', 'ư' => 'u',
            // grave accent
            'Ầ' => 'A', 'ầ' => 'a', 'Ằ' => 'A', 'ằ' => 'a', 'Ề' => 'E', 'ề' => 'e', 'Ồ' => 'O', 'ồ' => 'o', 'Ờ' => 'O', 'ờ' => 'o', 
            'Ừ' => 'U', 'ừ' => 'u', 'Ỳ' => 'Y', 'ỳ' => 'y',
            // hook
            'Ả' => 'A', 'ả' => 'a', 'Ẩ' => 'A', 'ẩ' => 'a', 'Ẳ' => 'A', 'ẳ' => 'a', 'Ẻ' => 'E', 'ẻ' => 'e', 'Ể' => 'E', 'ể' => 'e', 
            'Ỉ' => 'I', 'ỉ' => 'i', 'Ỏ' => 'O', 'ỏ' => 'o', 'Ổ' => 'O', 'ổ' => 'o', 'Ở' => 'O', 'ở' => 'o', 'Ủ' => 'U', 'ủ' => 'u', 
            'Ử' => 'U', 'ử' => 'u', 'Ỷ' => 'Y', 'ỷ' => 'y',
            // tilde
            'Ẫ' => 'A', 'ẫ' => 'a', 'Ẵ' => 'A', 'ẵ' => 'a', 'Ẽ' => 'E', 'ẽ' => 'e', 'Ễ' => 'E', 'ễ' => 'e', 'Ỗ' => 'O', 'ỗ' => 'o', 
            'Ỡ' => 'O', 'ỡ' => 'o', 'Ữ' => 'U', 'ữ' => 'u', 'Ỹ' => 'Y', 'ỹ' => 'y',
            // acute accent
            'Ấ' => 'A', 'ấ' => 'a', 'Ắ' => 'A', 'ắ' => 'a', 'Ế' => 'E', 'ế' => 'e', 'Ố' => 'O', 'ố' => 'o', 'Ớ' => 'O', 'ớ' => 'o', 
            'Ứ' => 'U', 'ứ' => 'u',
            // dot below
            'Ạ' => 'A', 'ạ' => 'a', 'Ậ' => 'A', 'ậ' => 'a', 'Ặ' => 'A', 'ặ' => 'a', 'Ẹ' => 'E', 'ẹ' => 'e', 'Ệ' => 'E', 'ệ' => 'e', 
            'Ị' => 'I', 'ị' => 'i', 'Ọ' => 'O', 'ọ' => 'o', 'Ộ' => 'O', 'ộ' => 'o', 'Ợ' => 'O', 'ợ' => 'o', 'Ụ' => 'U', 'ụ' => 'u', 
            'Ự' => 'U', 'ự' => 'u', 'Ỵ' => 'Y', 'ỵ' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            'ɑ' => 'a',
            // macron
            'Ǖ' => 'U', 'ǖ' => 'u',
            // acute accent
            'Ǘ' => 'U', 'ǘ' => 'u',
            // caron
            'Ǎ' => 'A', 'ǎ' => 'a', 'Ǐ' => 'I', 'ǐ' => 'i', 'Ǒ' => 'O', 'ǒ' => 'o', 'Ǔ' => 'U', 'ǔ' => 'u', 'Ǚ' => 'U', 'ǚ' => 'u',
            // grave accent
            'Ǜ' => 'U', 'ǜ' => 'u',
        ];
        // Used for locale-specific rules
        if ('de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale) {
            $chars['Ä'] = 'Ae';
            $chars['ä'] = 'ae';
            $chars['Ö'] = 'Oe';
            $chars['ö'] = 'oe';
            $chars['Ü'] = 'Ue';
            $chars['ü'] = 'ue';
            $chars['ß'] = 'ss';
        } elseif ('da_DK' === $locale) {
            $chars['Æ'] = 'Ae';
            $chars['æ'] = 'ae';
            $chars['Ø'] = 'Oe';
            $chars['ø'] = 'oe';
            $chars['Å'] = 'Aa';
            $chars['å'] = 'aa';
        } elseif ('ca' === $locale) {
            $chars['l·l'] = 'll';
        } elseif ('sr_RS' === $locale || 'bs_BA' === $locale) {
            $chars['Đ'] = 'DJ';
            $chars['đ'] = 'dj';
        }
        $string = strtr($string, $chars);
        return $string;
    }

    public static function time_elapsed_string($datetime, $full = false) {

        if(!$datetime) { 
          return 'never'; 
        }

        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * change any color string (hex or color name) to hex
     * @param $string
     * @param $prefix optional prefix
     * @return string
     */
    public static function colorFilter($string, $prefix='#')
    {
        $string = $string[0] == '#' ? substr($string, 1) : $string; //remove leading #
        $string = trim(strtolower($string));
        $webColors = [
            'aliceblue'             => 'F0F8FF',
            'antiquewhite'          => 'FAEBD7',
            'aqua'                  => '00FFFF',
            'aquamarine'            => '7FFFD4',
            'azure'                 => 'F0FFFF',
            'beige'                 => 'F5F5DC',
            'bisque'                => 'FFE4C4',
            'black'                 => '000000',
            'blanchedalmond'        => 'FFEBCD',
            'blue'                  => '0000FF',
            'blueviolet'            => '8A2BE2',
            'brown'                 => 'A52A2A',
            'burlywood'             => 'DEB887',
            'cadetblue'             => '5F9EA0',
            'chartreuse'            => '7FFF00',
            'chocolate'             => 'D2691E',
            'coral'                 => 'FF7F50',
            'cornflowerblue'        => '6495ED',
            'cornsilk'              => 'FFF8DC',
            'crimson'               => 'DC143C',
            'cyan'                  => '00FFFF',
            'darkblue'              => '00008B',
            'darkcyan'              => '008B8B',
            'darkgoldenrod'         => 'B8860B',
            'darkgray'              => 'A9A9A9',
            'darkgrey'              => 'A9A9A9',
            'darkgreen'             => '006400',
            'darkkhaki'             => 'BDB76B',
            'darkmagenta'           => '8B008B',
            'darkolivegreen'        => '556B2F',
            'darkorange'            => 'FF8C00',
            'darkorchid'            => '9932CC',
            'darkred'               => '8B0000',
            'darksalmon'            => 'E9967A',
            'darkseagreen'          => '8FBC8F',
            'darkslateblue'         => '483D8B',
            'darkslategray'         => '2F4F4F',
            'darkslategrey'         => '2F4F4F',
            'darkturquoise'         => '00CED1',
            'darkviolet'            => '9400D3',
            'deeppink'              => 'FF1493',
            'deepskyblue'           => '00BFFF',
            'dimgray'               => '696969',
            'dimgrey'               => '696969',
            'dodgerblue'            => '1E90FF',
            'firebrick'             => 'B22222',
            'floralwhite'           => 'FFFAF0',
            'forestgreen'           => '228B22',
            'fuchsia'               => 'FF00FF',
            'gainsboro'             => 'DCDCDC',
            'ghostwhite'            => 'F8F8FF',
            'gold'                  => 'FFD700',
            'goldenrod'             => 'DAA520',
            'gray'                  => '808080',
            'grey'                  => '808080',
            'green'                 => '008000',
            'greenyellow'           => 'ADFF2F',
            'honeydew'              => 'F0FFF0',
            'hotpink'               => 'FF69B4',
            'indianred'             => 'CD5C5C',
            'indigo'                => '4B0082',
            'ivory'                 => 'FFFFF0',
            'khaki'                 => 'F0E68C',
            'lavender'              => 'E6E6FA',
            'lavenderblush'         => 'FFF0F5',
            'lawngreen'             => '7CFC00',
            'lemonchiffon'          => 'FFFACD',
            'lightblue'             => 'ADD8E6',
            'lightcoral'            => 'F08080',
            'lightcyan'             => 'E0FFFF',
            'lightgoldenrodyellow'  => 'FAFAD2',
            'lightgray'             => 'D3D3D3',
            'lightgrey'             => 'D3D3D3',
            'lightgreen'            => '90EE90',
            'lightpink'             => 'FFB6C1',
            'lightsalmon'           => 'FFA07A',
            'lightseagreen'         => '20B2AA',
            'lightskyblue'          => '87CEFA',
            'lightslategray'        => '778899',
            'lightslategrey'        => '778899',
            'lightsteelblue'        => 'B0C4DE',
            'lightyellow'           => 'FFFFE0',
            'lime'                  => '00FF00',
            'limegreen'             => '32CD32',
            'linen'                 => 'FAF0E6',
            'magenta'               => 'FF00FF',
            'maroon'                => '800000',
            'mediumaquamarine'      => '66CDAA',
            'mediumblue'            => '0000CD',
            'mediumorchid'          => 'BA55D3',
            'mediumpurple'          => '9370DB',
            'mediumseagreen'        => '3CB371',
            'mediumslateblue'       => '7B68EE',
            'mediumspringgreen'     => '00FA9A',
            'mediumturquoise'       => '48D1CC',
            'mediumvioletred'       => 'C71585',
            'midnightblue'          => '191970',
            'mintcream'             => 'F5FFFA',
            'mistyrose'             => 'FFE4E1',
            'moccasin'              => 'FFE4B5',
            'navajowhite'           => 'FFDEAD',
            'navy'                  => '000080',
            'oldlace'               => 'FDF5E6',
            'olive'                 => '808000',
            'olivedrab'             => '6B8E23',
            'orange'                => 'FFA500',
            'orangered'             => 'FF4500',
            'orchid'                => 'DA70D6',
            'palegoldenrod'         => 'EEE8AA',
            'palegreen'             => '98FB98',
            'paleturquoise'         => 'AFEEEE',
            'palevioletred'         => 'DB7093',
            'papayawhip'            => 'FFEFD5',
            'peachpuff'             => 'FFDAB9',
            'peru'                  => 'CD853F',
            'pink'                  => 'FFC0CB',
            'plum'                  => 'DDA0DD',
            'powderblue'            => 'B0E0E6',
            'purple'                => '800080',
            'rebeccapurple'         => '663399',
            'red'                   => 'FF0000',
            'rosybrown'             => 'BC8F8F',
            'royalblue'             => '4169E1',
            'saddlebrown'           => '8B4513',
            'salmon'                => 'FA8072',
            'sandybrown'            => 'F4A460',
            'seagreen'              => '2E8B57',
            'seashell'              => 'FFF5EE',
            'sienna'                => 'A0522D',
            'silver'                => 'C0C0C0',
            'skyblue'               => '87CEEB',
            'slateblue'             => '6A5ACD',
            'slategray'             => '708090',
            'slategrey'             => '708090',
            'snow'                  => 'FFFAFA',
            'springgreen'           => '00FF7F',
            'steelblue'             => '4682B4',
            'tan'                   => 'D2B48C',
            'teal'                  => '008080',
            'thistle'               => 'D8BFD8',
            'tomato'                => 'FF6347',
            'turquoise'             => '40E0D0',
            'violet'                => 'EE82EE',
            'wheat'                 => 'F5DEB3',
            'white'                 => 'FFFFFF',
            'whitesmoke'            => 'F5F5F5',
            'yellow'                => 'FFFF00',
            'yellowgreen'           => '9ACD32',
        ];
    
        return isset($webColors[$string]) ? $prefix . $webColors[$string] : $prefix . $string;
    }
}