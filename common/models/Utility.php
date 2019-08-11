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
     * @param  object $model Model of attribute to check uniqueness against
     * @param  string $attribute Model attribute
     * @param  integer $length Length of returned string
     * @param  boolean $alphaNum Whether to restrict string to alphanumeric characters
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
        } else if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found') {;
            return false; // The file at $url does not exist, and I got redirected to a custom 404 page
        } else {
            return true;
        }
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
     * change any color string (hex or color name) to hex
     * @param $string
     * @param $prefix optional prefix
     * @return string
     */
    public static function colorToHex($string, $prefix='#')
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