<?php
#
# Make a copy of this file named `private.php`
# and edit the settings in that file to config
# this installation. jstark December 18, 2016
#
define('API_KEY', 'asdf1234asdf1234asdf'); // You can find this on your user page in drip: https://www.getdrip.com/user/edit
define('ACCOUNT_ID', '1234567'); // You can find this in your drip url ala: https://jstark.co/2gPfFP5
define('APP_NAME', 'MyAppName'); // An arbitrary name you make up
define('STATUS_FILTER', 'sent'); // Possible values: draft, scheduled, sent, all
define('MAX_ITERATIONS', 5); // Avoid infinite loop, just in case (increase this is you have more than 500 broadcasts)
define('DELETE_EVERYTHING_AFTER_STRING', '{%'); // A string found near the end of each message used to trim off everything after
define('PAGE_FOOTER', "----\n\n[« Back to home](https://mysite.com/)"); // Arbitrary content that will be appended to the end of each file
define('APPEND_SUBJECT_TO_FILENAME', true); //
