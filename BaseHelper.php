<?php
/**
 * Created by PhpStorm.
 * User: Yarmaliuk Mikhail
 * Date: 19.06.2018
 * Time: 12:10
 */

namespace Kakadu\Yii2BaseHelpers;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\rbac\Role;
use yii\validators\EachValidator;
use yii\validators\Validator;
use yii\web\JsExpression;

/**
 * Class    BaseHelper
 * @package Kakadu\Yii2BaseHelpers
 * @author  Yarmaliuk Mikhail
 * @author  Konstantin Timoshenko
 * @version 2.0
 */
class BaseHelper
{
    /**
     * Default mysql datetime field
     *
     * @var string
     */
    public const DEFAULT_DATETIME = '1001-01-01 00:00:00';

    /**
     * Default mysql date field
     *
     * @var string
     */
    public const DEFAULT_DATE = '1001-01-01';

    public const MONTHS_SHORT    = 1;
    public const MONTHS_GENITIVE = 2;

    /**
     * Russian alphabet
     */
    public const RUS_ABC = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];

    /**
     * English alphabet
     */
    public const ENG_ABC = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    /**
     * Array site roles
     *
     * @var array
     */
    private static $_array_roles = [];

    /**
     * Return array site roles
     *
     * @param int $userRole
     *
     * @return array
     */
    public static function getArrayRoles(int $userRole = 0): array
    {
        if (empty(self::$_array_roles)) {
            $roles = [];
            foreach (Yii::$app->authManager->getRoles() as $role) {
                if ($userRole >= $role->data['value']) {
                    $roles[$role->data['value']] = $role;
                }
            }

            self::$_array_roles = $roles;
        }

        return self::$_array_roles;
    }

    /**
     * Get role on role_id
     *
     * @param $role_id
     *
     * @return bool|Role
     */
    public static function getRole($role_id)
    {
        if (empty(self::$_array_roles)) {
            self::getArrayRoles();
        }

        return isset(self::$_array_roles[$role_id]) ? self::$_array_roles[$role_id] : false;
    }

    /**
     * Simple multi-bytes ucfirst()
     *
     * @param string $str
     *
     * @return string
     */
    public static function mbUcfirst(string $str): string
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));

        return $fc . mb_substr($str, 1);
    }

    /**
     * Reverse string. Works with multi-byte.
     *
     * @see strrev
     *
     * @param string $str
     *
     * @return string
     */
    public static function mbStrrev(string $str): string
    {
        $charArray = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $charArray = array_reverse($charArray);

        return implode('', $charArray);
    }

    /**
     * Remove from string \r \n \r\n
     * Trim string
     *
     * @param string $string
     * @param string $replace
     * @param bool   $spaces
     *
     * @return string string
     */
    public static function clearString(string $string, string $replace = '', bool $spaces = false): string
    {
        $str = str_replace(["\r", "\n", "\r\n", '&nbsp;', '&amp'], $replace, $string);

        if ($spaces) {
            /**
             * @todo: Trim unicode/UTF-8 whitespaces.
             * @see http://markushedlund.com/dev/trim-unicodeutf-8-whitespace-in-php
             */
            $str = static::collapseSpaces($str);
        }

        $str = trim($str);

        return $str;
    }

    /**
     * Replaces all weird whitespace characters (except new line) with regular whitespace
     * @see https://stackoverflow.com/a/25956935/2422712
     *
     * @param string $string
     *
     * @return string
     */
    public static function collapseSpaces(string $string): string
    {
        $string = (string) preg_replace('/\h+/u', ' ', $string);
        $string = trim($string);

        return $string;
    }

    /**
     * Remove as many emoticons as possible.
     *
     * @see https://stackoverflow.com/a/41831874/2422712
     *
     * @todo: compare with emojione/emoji-regex.
     * @see https://github.com/emojione/emoji-regex/blob/master/src/php/index.php
     *
     * @param string $text
     * @param string $replacement
     *
     * @return string
     */
    public static function removeEmoji(string $text, string $replacement = ''): string
    {
        // Emoticons
        $text = (string) preg_replace('/[\x{1F600}-\x{1F64F}]/u', $replacement, $text);
        // Miscellaneous Symbols and Pictographs
        $text = (string) preg_replace('/[\x{1F300}-\x{1F5FF}]/u', $replacement, $text);
        // Transport And Map Symbols
        $text = (string) preg_replace('/[\x{1F680}-\x{1F6FF}]/u', $replacement, $text);
        // Miscellaneous Symbols
        $text = (string) preg_replace('/[\x{2600}-\x{26FF}]/u', $replacement, $text);
        // Dingbats
        $text = (string) preg_replace('/[\x{2700}-\x{27BF}]/u', $replacement, $text);
        // Flags
        $text = (string) preg_replace('/[\x{1F1E6}-\x{1F1FF}]/u', $replacement, $text);
        // Others
        $text = (string) preg_replace('/[\x{1F910}-\x{1F95E}]/u', $replacement, $text);
        $text = (string) preg_replace('/[\x{1F980}-\x{1F991}]/u', $replacement, $text);
        $text = (string) preg_replace('/[\x{1F9C0}]/u', '', $text);
        $text = (string) preg_replace('/[\x{1F9F9}]/u', '', $text);

        return $text;
    }

    /**
     * Get array from string (textarea).
     * Explode by PHP_EOL (\n or \r\n) and clear values.
     *
     * @param $string
     *
     * @return array
     */
    public static function explodeEOLString(string $string): array
    {
        $result = explode(PHP_EOL, $string);

        array_walk($result, function (&$value) {
            $value = self::clearString($value);
        });

        $result = array_filter($result);

        return $result;
    }

    /**
     * Get dns attributes
     * Example: getDsnAttribute('dbname', Yii::$app->getDb()->dsn);
     *
     * @param $name
     * @param $dsn
     *
     * @return null
     */
    public static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    /**
     * Обрезать строку по длине сохраняя целостность слов
     *
     * @param string $str      строка
     * @param int    $length   длина, до скольки символов обрезать
     * @param string $postfix  постфикс, который добавляется к строке
     * @param string $encoding кодировка, по-умолчанию 'UTF-8'
     *
     * @return string обрезанная строка
     */
    public static function cutStringByLength(string $str, int $length, string $postfix = '...', string $encoding = 'UTF-8'): string
    {
        if (mb_strlen($str, $encoding) <= $length) {
            return $str;
        }

        $tmp = mb_substr($str, 0, $length, $encoding);

        return mb_substr($tmp, 0, mb_strripos($tmp, ' ', 0, $encoding), $encoding) . $postfix;
    }

    /**
     * Обрезать строку без сохранения целостности слов
     *
     * @param string $str
     * @param int    $length
     * @param string $postfix
     * @param string $encoding
     *
     * @return string
     */
    public static function cutString(string $str, int $length, string $postfix = '...', string $encoding = 'UTF-8'): string
    {
        return mb_substr($str, 0, $length, $encoding) . (mb_strlen($str, $encoding) > $length ? $postfix : null);
    }

    /**
     * Transliterate string
     *
     * @param string $string
     * @param bool   $replace_symbols
     *
     * @return string
     */
    public static function transliterate(string $string, $replace_symbols = true): string
    {
        if ($replace_symbols) {
            $string = static::clearString($string, ' ', true);
            $string = preg_replace('/\s+/', '-', $string);
            $string = preg_replace('/[^0-9a-zа-яёЁ-]/ui', '', $string);
        }

        $rus = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' '];
        $lat = ['A', 'B', 'V', 'G', 'D', 'E', 'E', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'TS', 'Ch', 'Sh', 'Sch', 'Y', 'I', 'J', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'j', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', 'y', 'i', 'j', 'e', 'yu', 'ya', '-'];

        return str_replace($rus, $lat, $string);
    }

    /**
     * Get js validator rules for attribute model
     *
     * @param ActiveRecord|Model $model
     * @param string             $attribute
     * @param array              $options
     *
     * @return array
     */
    public static function getJsValidationFieldRules($model, string $attribute, array $options = [])
    {
        $attribute = Html::getAttributeName($attribute);

        $inputID = Html::getInputId($model, $attribute);

        $options_field['id']        = Html::getInputId($model, $attribute);
        $options_field['name']      = $attribute;
        $options_field['container'] = ".field-$inputID";
        $options_field['input']     = "#$inputID";
        $options_field['error']     = '.help-block';

        if (!in_array($attribute, $model->activeAttributes(), true)) {
            return [];
        }

        $validators = [];

        foreach ($model->getActiveValidators($attribute) as $validator) {
            /* @var $validator \yii\validators\Validator */

            if ($validator instanceof EachValidator) {
                $rule = $validator->rule;
                array_unshift($rule, $attribute);
                $validator = Validator::createValidator($rule[1], $model, (array) $rule[0], array_slice($rule, 2));
            }

            $js = $validator->clientValidateAttribute($model, $attribute, \Yii::$app->view);
            if ($validator->enableClientValidation && $js != '') {
                if ($validator->whenClient !== null) {
                    $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                }
                $validators[] = $js;
            }
        }

        if (!empty($validators)) {
            $options_field['validate'] = new JsExpression('function (attribute, value, messages, deferred, \$form) {' . implode('', $validators) . '}');
        }

        return array_merge($options, $options_field);
    }

    /**
     * Get number position letter
     *
     * @param string $letter
     *
     * @return int
     */
    public static function getNumberPositionLetter(string $letter): int
    {
        $letter = (string) mb_strtolower($letter);

        if (($founded = array_search($letter, self::RUS_ABC)) !== false) {
            return $founded + 1;
        } elseif (($founded = array_search($letter, self::ENG_ABC)) !== false) {
            return $founded + 1;
        }

        return 0;
    }

    /**
     * Get letter by number position in alphabet
     *
     * @param int   $position
     * @param array $alphabet
     *
     * @return int
     */
    public static function getLetterNumberPosition(int $position, array $alphabet = self::ENG_ABC): ?string
    {
        return $alphabet[$position] ?? null;
    }

    /**
     * Get formatted date with month (rus/eng)
     *
     * @param int    $date
     * @param string $format
     * @param int    $type
     *
     * @return false|string
     */
    public static function getFormattedMonthDate(int $date, string $format = 'd M Y', int $type = self::MONTHS_SHORT)
    {
        switch ($type) {
            case self::MONTHS_GENITIVE:
                $months = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
            break;

            case self::MONTHS_SHORT:
            default:
                $months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
        }

        $number_month = date('n', $date) - 1;

        return date(str_replace('M', $months[$number_month], $format), $date);
    }

    /**
     * Get month title
     *
     * @param int $month
     *
     * @return string
     */
    public static function getMonthTitle(int $month): string
    {
        $months = [
            1  => Yii::t('app', 'январь'),
            2  => Yii::t('app', 'февраль'),
            3  => Yii::t('app', 'март'),
            4  => Yii::t('app', 'апрель'),
            5  => Yii::t('app', 'май'),
            6  => Yii::t('app', 'июнь'),
            7  => Yii::t('app', 'июль'),
            8  => Yii::t('app', 'август'),
            9  => Yii::t('app', 'сентябрь'),
            10 => Yii::t('app', 'октябрь'),
            11 => Yii::t('app', 'ноябрь'),
            12 => Yii::t('app', 'декабрь'),
        ];

        return $months[$month] ?? '';
    }

    /**
     * Get days week
     *
     * @param bool $short
     *
     * @return array
     */
    public static function getDaysWeek(bool $short = false): array
    {
        if (!$short) {
            return [
                'monday'    => Yii::t('app', 'Понедельник'),
                'tuesday'   => Yii::t('app', 'Вторник'),
                'wednesday' => Yii::t('app', 'Среда'),
                'thursday'  => Yii::t('app', 'Четверг'),
                'friday'    => Yii::t('app', 'Пятница'),
                'saturday'  => Yii::t('app', 'Суббота'),
                'sunday'    => Yii::t('app', 'Воскресенье'),
            ];
        }

        return [
            'monday'    => Yii::t('app', 'Пн'),
            'tuesday'   => Yii::t('app', 'Вт'),
            'wednesday' => Yii::t('app', 'Ср'),
            'thursday'  => Yii::t('app', 'Чт'),
            'friday'    => Yii::t('app', 'Пт'),
            'saturday'  => Yii::t('app', 'Сб'),
            'sunday'    => Yii::t('app', 'Вс'),
        ];
    }

    /**
     * Get random array
     *
     * @param int $min
     * @param int $max
     * @param int $limit Max elements
     *
     * @return array
     */
    public static function getRandomArray(int $min, int $max, int $limit): array
    {
        if ($min > $max || $min === $max || $max < 1) {
            return [];
        } elseif ($limit === 1) {
            return [0 => 0];
        }

        $range = \range($min, $max);
        $limit = min(\count($range), $limit); // if limit > array length - null returned

        return array_rand($range, $limit); // returns random unique array
    }

    /**
     * Download file
     *
     * @param string  $file
     * @param string  $content_type
     * @param boolean $delete_file
     *
     * @return void
     */
    public static function fileForceDownload(string $file, string $content_type = 'application/octet-stream', bool $delete_file = false)
    {
        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);

            if ($delete_file) {
                @unlink($file);
            }

            exit;
        }
    }

    /**
     * @param string $name
     * @param string $content_type
     */
    public static function setFileDownloadHeaders(string $name, string $content_type = 'application/octet-stream')
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename=' . $name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
    }

    /**
     * Leave the number of decimal places
     *
     * @param string $number
     * @param int    $precision
     *
     * @return string
     */
    public static function numberFormat(string $number, int $precision = 0): string
    {
        preg_match('/^[0-9]+.[0-9]{0,' . $precision . '}/', $number, $matches);

        if (!empty($matches[0])) {
            return rtrim($matches[0], "., \t\n\r\0\x0B");
        }

        return $number;
    }

    /**
     * Rounds a float half up
     *
     * @param string $number
     * @param int    $precision
     *
     * @return string
     */
    public static function floatRoundUp(string $number, int $precision = 0): string
    {
        preg_match('/^[0-9]+.[0-9]{0,' . $precision . '}/', $number, $matches);

        if (!empty($matches[0])) {
            $upNumber = (int) substr($number, strpos($number, $matches[0]) + strlen($matches[0]), 1);

            if (is_int($upNumber)) {
                $upNumber = 1 / str_pad(1, $precision + 1, 0);

                return $matches[0] + $upNumber;
            }
        }

        return $number;
    }

    /**
     * Get host name
     *
     * @return string
     */
    public static function getHostName()
    {
        // Special for zooqi
        if (!empty($_SERVER['HTTP_ZOOQIHOST'])) {
            $hostname = $_SERVER['HTTP_ZOOQIHOST'];
        } else {
            $hostname = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST'] ?? null), -2, 2));
        }

        return $hostname;
    }

    /**
     * Get host info
     *
     * @return array
     */
    public static function getHostInfo(): array
    {
        $hostname = self::getHostName();

        $domain  = explode('.', $_SERVER['HTTP_HOST'] ?? null);
        $domains = [];

        if (!empty($domain)) {
            $count_domains = count($domain);

            for ($i = 0; $i < $count_domains; $i++) {
                $domains['domain' . ($i + 1)] = $domain[$count_domains - 1 - $i];
            }
        }

        return array_merge([
            'hostname' => $hostname,
            'scheme'   => $_SERVER['REQUEST_SCHEME'] ?? null,
        ], $domains);
    }

    /**
     * Build absolute url link
     *
     * @param array $options
     *
     * @return string
     */
    public static function buildLink(array $options): string
    {
        $scheme = $options['scheme'] ?? self::getHostInfo()['scheme'] . '://';
        $host   = $options['host'] ?? null;
        $path   = $options['path'] ?? null;

        if (!empty($scheme)) {
            $host = preg_replace('/^https?:\/\//', '', $host);
        }

        $url = Url::to(array_merge([$path], $options['params'] ?? []));

        return $scheme . rtrim($host, '/') . $url;
    }

    /**
     * Get info about geocoordinates
     *
     * @param string $lat
     * @param string $lon
     *
     * @return array
     */
    public static function getGeocoordinatesInfo(string $lat, string $lon): array
    {
        $country = null;

        if (!empty($lat) && empty($lon)) {
            $geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ru');
            $output  = json_decode($geocode);

            if (!empty($output)) {
                for ($j = 0; $j < count($output->results[0]->address_components); $j++) {
                    $cn = [$output->results[0]->address_components[$j]->types[0]];

                    if (in_array('country', $cn)) {
                        $country = $output->results[0]->address_components[$j]->long_name;
                    }
                }
            }
        }

        return [
            'country' => $country,
        ];
    }

    /**
     * Get date interval array
     *
     * @param string $start
     * @param int    $count_days
     * @param bool   $revers
     * @param string $format
     *
     * @return array
     */
    public static function getDateIntervalArray(string $start, int $count_days, bool $revers = true, string $format = 'Y-m-d'): array
    {
        $days = [];

        if ($count_days > 0) {
            if (is_numeric($start)) {
                $start = date('Y-m-d', $start);
            }

            $days[] = $start;

            for ($i = 0; $i < $count_days - 1; $i++) {
                $days[] = date($format, strtotime($days[$i] . '' . ($revers ? '-' : '+') . '1 day'));
            }
        }

        if ($revers) {
            $days = array_reverse($days);
        }

        return $days;
    }

    /**
     * Plural form
     *
     * @param int   $number
     * @param array $after
     *
     * @return string
     */
    public static function pluralForm(int $number, array $after): string
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Get post date
     * Just like in vk.com
     *
     * @param int $timestamp
     *
     * @return string
     */
    public static function getPostDate(int $timestamp): string
    {
        $current = time();

        $minutes       = [Yii::t('app', 'минуту'), Yii::t('app', 'минуты'), Yii::t('app', 'минут')];
        $minutes_count = [
            0 => '',
            1 => '',
            2 => Yii::t('app', 'две'),
            3 => Yii::t('app', 'три'),
            4 => Yii::t('app', 'четыре'),
            5 => Yii::t('app', 'пять'),
        ];

        $hours       = [Yii::t('app', 'час'), Yii::t('app', 'часа'), Yii::t('app', 'часов')];
        $hours_count = [2 => Yii::t('app', 'два')] + $minutes_count;

        $interval = date_diff(date_create("@$current"), date_create("@$timestamp"));

        if ($timestamp + 10 > $current) { // up to 10 sec
            return Yii::t('app', 'только что');
        } elseif ($timestamp + 60 > $current) { // up to 15 sec
            return Yii::t('app', 'около минуты назад');
        } elseif ($timestamp + 60 * 60 * 24 > $current) { // up to 1 hour and up to 24 hour

            $number       = $interval->h ? : $interval->i;
            $number_count = $interval->h ? $hours_count : $minutes_count;
            $plural       = $interval->h ? $hours : $minutes;

            $result = (isset($number_count[$number]) ? $number_count[$number] : $number)
                . ' '
                . self::pluralForm($number ? : 1, $plural)
                . ' '
                . Yii::t('app', 'назад');
        } elseif ($timestamp + 60 * 60 * (24 * 2) > $current) { // up to 48 hour
            $result = Yii::t('app', 'вчера в {time}', ['time' => date('H:i', $timestamp)]);
        } else {
            $result = Yii::t('app', '{date} в {time}', [
                'date' => self::getFormattedMonthDate($timestamp, 'd M'),
                'time' => date('H:i', $timestamp),
            ]);
        }

        return trim($result);
    }

    /**
     * Array filter recursive
     *
     * @param      $input
     * @param null $callback
     *
     * @return array
     */
    public static function arrayFilterRecursive(array $input, \Closure $callback = null): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = self::arrayFilterRecursive($value, $callback);
            }
        }

        if ($callback) {
            return array_filter($input, $callback);
        }

        return array_filter($input);
    }

    /**
     * Get csv file content chunked
     *
     * @param string   $file
     * @param \Closure $callback
     * @param string   $delimeter
     * @param int      $stringSize
     *
     * @return bool
     */
    public static function filecsvGetContentsChunked(string $file, \Closure $callback, string $delimeter = ',', int $stringSize = 0): bool
    {
        try {
            if (($handle = fopen($file, 'r')) !== false) {
                while (($data = fgetcsv($handle, $stringSize, $delimeter)) !== false) {
                    call_user_func_array($callback, [$data]);
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            trigger_error('file_get_contents_chunked::' . $e->getMessage(), E_USER_NOTICE);

            return false;
        }

        return true;
    }

    /**
     * DateInterval to seconds
     *
     * @param \DateInterval $dateInterval
     *
     * @return int seconds
     */
    public static function dateIntervalToSeconds($dateInterval): int
    {
        $reference = new \DateTimeImmutable();
        $endTime   = $reference->add($dateInterval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }
}
