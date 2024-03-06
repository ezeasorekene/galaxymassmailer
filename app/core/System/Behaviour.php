<?php

namespace ezeasorekene\App\Core\System;

use Josantonius\Session\Facades\Session as Session;

class Behaviour
{

    /**
     * Redirect a user to a specific page
     * @param string $url The url will be redirected to
     * @param int $code The code to be appended to the header
     * @return string Returns sanitized url path
     */
    public static function redirect(string $url, int $code = 302)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        header("Location: {$url}", true, $code);
        exit;
    }


    /**
     * Sanitize and parse url paths
     * @param string $path The path string you intend to parse
     * @return mixed Returns sanitized url path
     */
    public static function parseUrl($thePath = '')
    {
        if (!empty($thePath)) {
            $path = $thePath;
        } elseif (!empty($_GET['url'])) {
            $path = $_GET['url'];
        } else {
            $path = "landing/index";
        }

        if (isset($path)) {
            return $url = explode('/', trim(parse_url($path, PHP_URL_PATH), '/'));
        }
    }

    /**
     * Check if the app is in production mode
     * @return bool Returns true if app is in production else return false
     */
    public static function isAppInProduction(): bool
    {
        if (isset($_ENV['APP_MODE']) && strtolower($_ENV['APP_MODE']) == "production") {
            return true;
        }
        return false;
    }

    /**
     * Sanitize strings for use with the database
     * @param string $data The string you intend to sanitize
     * @return string Returns the sanitized string
     */
    public static function sanitize($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = addslashes($data);
        return $data;
    }

    /**
     * Replace Mojibake strings for proper reading
     * @param string $data The Mojibake string you intend to sanitize
     * @return string Returns the sanitized string
     */
    public static function sanitizeMojibake($data)
    {
        $data = str_replace("Â", "", $data);
        $data = str_replace("â€™", "'", $data);
        $data = str_replace("â€œ", '"', $data);
        $data = str_replace("â€", '"', $data);
        $data = str_replace('â€“', '-', $data);
        $data = str_replace('â€', '"', $data);
        return $data;
    }

    /**
     * Format Nigerian mobile numbers correctly
     * @param string $phone The number you intend to format
     * @return mixed $phone|false Returns the Nigerian mobile number with +234 if it is correct else if will return false
     */
    public static function formatNigerianPhone($phone)
    {
        if (is_numeric($phone)) {
            $thePhoneNumber = self::sanitize($phone);

            //Filter the phone number and remove empty spaces and hyphen -
            $thePhoneNumber = str_replace(" ", "", $thePhoneNumber);
            $thePhoneNumber = str_replace("-", "", $thePhoneNumber);

            //Get the first character using substr.
            $firstCharacter = substr($thePhoneNumber, 0, 1);
            $get234 = substr($thePhoneNumber, 0, 3);
            if ($firstCharacter != "+" && $firstCharacter == "0")
                $thePhoneNumber = substr_replace($thePhoneNumber, "+234", 0, 1);
            elseif ($get234 == "234")
                $thePhoneNumber = "+" . $phone;
            elseif ($firstCharacter != "+" && $firstCharacter != "0")
                $thePhoneNumber = "+234" . $phone;
            else
                $thePhoneNumber = $phone;

            // return the formatted Nigerian phone number
            return $thePhoneNumber;
        } else {
            return false;
        }
    }

    /**
     * Display flash notice messages
     * @return mixed Returns the message with appropriate colour coding
     */
    public static function flashMessage()
    {
        if (Session::has('flash_message')) {
            $message = Session::get('flash_message');
            $colour = Session::get('flash_colour');

            empty($colour) ? $colour = "success" : "";

            Session::remove('flash_message');
            Session::remove('flash_colour');

            if (is_array($message)) {
                echo
                    '
                    <!--begin::Alert-->
                    <div class="alert bg-' . $colour . ' d-flex flex-column flex-sm-row p-5 mb-10">

                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                        <!--begin::Content-->
                        <span><ol>';

                foreach ($message as $msg) {
                    echo '<li>' . $msg . '</li>';
                }

                echo
                    '</ol></span>
                        <!--end::Content-->
                    </div>
                    <!--end::Wrapper-->

                    </div>
                    <!--end::Alert-->
                    ';
                return;
            } else {

                return
                    '
                <!--begin::Alert-->
                <div class="alert bg-' . $colour . ' d-flex flex-column flex-sm-row p-5 mb-10">
    
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                        <!--begin::Content-->
                        <span>'

                    . $message .

                    '</span>
                        <!--end::Content-->
                    </div>
                    <!--end::Wrapper-->
    
                </div>
                <!--end::Alert-->
                ';
            }
        }
    }

    /**
     * Set Display message
     */
    public static function setflashMessage($message, $colour)
    {
        Session::set('flash_message', $message);
        Session::set('flash_colour', $colour);
    }

    /**
     * Convert number to money format properly
     */
    public static function formatMoney($number, $currency = '₦', $decimal = '.')
    {
        $zero = '00';
        $broken_number = explode($decimal, $number);
        if ($broken_number !== false) {
            if (isset($broken_number[1])) {
                $second = $broken_number[1];
            } else {
                $second = $zero;
            }
            $money = number_format($broken_number[0], 0) . $decimal . $second;
        } else {
            $money = number_format($number, 0) . $decimal . $zero;
        }
        return $currency . $money;
    }


    public static function generateToken($word = ''): string
    {
        !empty($word) ? $value = $word : $value = uniqid("GalaxyPHP", true);
        $hashed_value = hash('sha256', $value); // Generate a SHA-256 hash of the value
        $token = bin2hex(random_bytes(16)) . $hashed_value; // Generate a random string and concatenate it with the hashed value to create the token
        return $token;
    }

    public static function generateKey($prefix = '', int $bytes = 16, $caps = false): string
    {
        $prefix = $prefix ?? "";
        $key = $prefix . bin2hex(random_bytes($bytes)); // Generate a random string and concatenate it with the hashed value to create the token
        if ($caps) {
            return strtoupper($key);
        } else {
            return $key;
        }
    }

    /*****
    Displays the time in 24 or 12 hours format as declayed in $format
    *****/
    public static function getCurrentTime($format = 12)
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        if (isset($format) && ($format == '24')) {
            $time = date("H:i:s");
        } else {
            $time = date("g:ia");
        }
        return $time;
    }

    /*****
    Displays the date in the format declayed in $format
    *****/
    public static function getCurrentDate($format = '0')
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        if (isset($format) && ($format == '1')) {
            $time = date("d-m-Y");
        } elseif (isset($format) && ($format == '2')) {
            $time = date("jS F, Y");
        } elseif (isset($format) && ($format == '3')) {
            $time = date("l, jS F, Y");
        } elseif (isset($format) && ($format == '4')) {
            $time = date("F jS, Y");
        } else {
            $time = date("Y-m-d");
        }
        return $time;
    }

    /*****
    Displays the date with time in 24 or 12 hours format as declayed in $format
    *****/
    public static function getCurrentDatewithTime($format = '24')
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        if (isset($format) && ($format == '12')) {
            $time = date("Y-m-d g:ia");
        } else {
            $time = date("Y-m-d H:i:s");
        }
        return $time;
    }

    /*****
    Checks if a given date is weekend - Saturday or Sunday
    *****/
    public static function checkWeekend($date)
    {
        $date = self::formatDate($date, $format = "Y-m-d");
        //Returns true if the day is weekend
        if (date('N', strtotime($date)) >= 6) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*****
    Checks the difference between two dates
    *****/
    public static function calculateDateDifference($date1, $date2, $prefix = "0", $suffix = "0")
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        switch ($prefix) {
            case '1':
                $date1 = date_create($date1); //Usually the current day in most cases
                $date2 = date_create($date2); //Usually a day in the future in most cases
                $diff = date_diff($date1, $date2);
                //Activate or deactivate the suffix days
                switch ($suffix) {
                    case '1': //Activates the suffix
                        return $diff->format("%R%a days");
                    default: //Deactivates the suffix
                        return $diff->format("%R%a");
                }

            default:
                $date1 = date_create($date1);
                $date2 = date_create($date2);
                $diff = date_diff($date1, $date2);
                //Activate or deactivate the suffix days
                switch ($suffix) {
                    case '1': //Activates the suffix
                        return $diff->format("%a days");
                    default: //Deactivates the suffix
                        return $diff->format("%a");
                }
        }
    }

    /*****
    Checks the difference between two years
    *****/
    public static function calculateYearDifference($date1, $date2, $prefix = "0", $suffix = "0")
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        switch ($prefix) {
            case '1':
                $date1 = date_create($date1);
                $date2 = date_create($date2);
                $diff = date_diff($date1, $date2)->y;
                //Activate or deactivate the suffix days
                switch ($suffix) {
                    case '1': //Activates the suffix
                        return $diff . " years old";
                    default: //Deactivates the suffix
                        return $diff;
                }

            default:
                $date1 = date_create($date1);
                $date2 = date_create($date2);
                $diff = date_diff($date1, $date2)->y;
                //Activate or deactivate the suffix days
                switch ($suffix) {
                    case '1': //Activates the suffix
                        return $diff . " years old";
                    default: //Deactivates the suffix
                        return $diff;
                }
        }
    }

    //Format dates properly
    public static function formatDate($date, $format = "jS F, Y")
    {
        if (empty($date) || is_null($date)) {
            return null;
        } else {
            if (empty($format)) {
                $format = "jS F, Y";
            }
            $data = str_replace("/", "-", $date);
            $data = str_replace(".", "-", $data);
            $data = str_replace("_", "-", $data);
            $data = date_create($data);
            $data = date_format($data, $format);
            return $data;
        }
    }

    //Substract dates properly
    public static function substractDates($date, $days, $format = "jS F, Y")
    {
        $data = date_create($date);
        date_sub($data, date_interval_create_from_date_string("{$days}"));
        $data = date_format($data, $format);
        return $data;
    }


    //Great the user based on the time of the day
    public static function greetUser()
    {
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        if (date("Hi") <= "1159") {
            echo "Good morning";
        } elseif ((date("Hi") >= "1200") && (date("Hi") <= "1659")) {
            echo "Good afternoon";
        } elseif ((date("Hi") >= "1700") && (date("Hi") <= "2059")) {
            echo "Good evening";
        } elseif ((date("Hi") >= "2100")) {
            echo "Good night";
        }
    }

    /*****
    Decodes the HTML elements from database to display properly
    *****/
    public static function decodeHTML($data)
    {
        $data = html_entity_decode($data);
        $data = htmlspecialchars_decode($data);
        return $data;
    }

    //Replace strings in a word or sentence
    public static function replaceString($data, $find, $replace = "")
    {
        //$find is supplied as an array
        foreach ($find as $single) {
            $data = str_replace($single, $replace, $data);
        }
        return $data;
    }

    /*****
    Get Client Real IP
    *****/
    public static function getClientIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }


    public static function appendAorAn($word)
    {
        $vowelArry = array('a', 'e', 'i', 'o', 'u'); // array of vowel
        $prefix = in_array(strtolower(substr($word, 0, 1)), $vowelArry) ? "an" : "a"; // logic to add prefix
        $updated_word = $prefix . " " . $word; // updated word
        return $updated_word;
    }

    //Check password strength
    public static function checkPasswordStrength(string $password, int $length = 8)
    {
        $response = $password;
        if (strlen($password) < $length) {
            $response = "Password must contain at least {$length} characters!";
        } elseif (!preg_match("#[0-9]+#", $password)) {
            $response = "Password must contain at least 1 number!";
        } elseif (!preg_match("#[A-Z]+#", $password)) {
            $response = "Password must contain at least 1 capital letter!";
        } elseif (!preg_match("#[a-z]+#", $password)) {
            $response = "Password must contain at Least 1 lowercase letter!";
        }
        return $response;
    }

    public static function formatOrdinalNumber(int $number, bool $caps = false): string
    {
        $suffix = 'th';

        // Check if the number ends with 11, 12, or 13
        if (in_array($number % 100, [11, 12, 13])) {
            $suffix = 'th';
        } else {
            // For other numbers, use the last digit to determine the suffix
            switch ($number % 10) {
                case 1:
                    $suffix = 'st';
                    break;
                case 2:
                    $suffix = 'nd';
                    break;
                case 3:
                    $suffix = 'rd';
                    break;
                default:
                    $suffix = 'th';
                    break;
            }
        }

        $suffix = $caps === true ? strtoupper($suffix) : $suffix;

        return $number . $suffix;
    }

    public static function getMaxContentLength(): int
    {
        $maxContentLength = ini_get('post_max_size');
        if (is_numeric($maxContentLength)) {
            $maxContentLength = (int) $maxContentLength;
        } else {
            $unit = strtoupper(substr($maxContentLength, -1));
            $maxContentLength = (int) $maxContentLength;
            switch ($unit) {
                case 'K':
                    $maxContentLength *= 1024; // Convert from KB to bytes
                    break;
                case 'M':
                    $maxContentLength *= 1024 * 1024; // Convert from MB to bytes
                    break;
                case 'G':
                    $maxContentLength *= 1024 * 1024 * 1024; // Convert from GB to bytes
                    break;
            }
        }
        return $maxContentLength;
    }

}