<?php
require '../config/private.php';
#
# Set up the template for the curl cmd
$template = 'curl -H "User-Agent: %s" -H "Content-Type: application/json" -u "%s" "https://api.getdrip.com/v2/%s/broadcasts?status=%s&page=%s"';
#
# Make sure a dir with perms 777 exists in webroot
$output_dir = $_SERVER['DOCUMENT_ROOT'] . '/output/' . time() . '/';
if (!file_exists($output_dir)) {
    mkdir($output_dir, 0700, true);
}
#
# Loop through broadcasts in chunks of 100
for ($page_number=1; $page_number < MAX_ITERATIONS; $page_number++) {
    #
    # Inject user specific vars into cmd
    $cmd = sprintf($template
        , APP_NAME
        , API_KEY
        , ACCOUNT_ID
        , STATUS_FILTER
        , $page_number
    );
    #
    # Grab the output
    $json = shell_exec($cmd);
    #
    # Convert json to php array so we can work with it
    $data = json_decode($json);
    // print_r($data);die;
    #
    # Exit if there are no broadcasts
    if (count($data->broadcasts) == 0) {
        break;
    }
    #
    # Loop through outputting md files to web dir
    foreach ($data->broadcasts as $broadcast) {
        #
        # Subject will be used as title in the document; format it as you like here
        $title = $broadcast->subject;
        #
        # Build a filename using the id and optionally filesafe subject
        $name = str_pad($broadcast->id, 10, '0', STR_PAD_LEFT);
        #
        #
        if (APPEND_SUBJECT_TO_FILENAME) {
            #
            # Create a filesystem safe subject
            $filesafe_subject = $broadcast->subject;
            $filesafe_subject = str_replace(' ', '_', (trim($filesafe_subject)));
            $filesafe_subject = preg_replace( "/[^a-zA-Z0-9_\[\]]/", "", $filesafe_subject);
            $name.= '_' . $filesafe_subject;
        }
        $filename = $output_dir . $name . '.md';
        // echo $filename . "\n"; continue;
        #
        # Format the date if you like
        if (isset($broadcast->send_at)) {
            $date = 'Sent on ' . date('F jS, Y', strtotime($broadcast->send_at));
        } else {
            $date = 'Written on ' . date('F jS, Y', strtotime($broadcast->created_at));
        }
        #
        # Clean up the body a bit
        if (!empty(DELETE_EVERYTHING_AFTER_STRING)) {
            list($body, $junk) = explode(DELETE_EVERYTHING_AFTER_STRING, $broadcast->html_body, 2);
        } else {
            $body = $broadcast->html_body;
        }
        $body = str_ireplace(array('<p>', '</p>', '<div>', '</div>', '<ul>', '</ul>', '<ol>', '</ol>', '</li>', '</h2>', '</h3>', '</h4>', '<br />', '&#9;'), '', $body);
        $body = str_ireplace(array('<strong>', '</strong>'), '**', $body);
        $body = str_ireplace(array('<em>', '</em>'), '_', $body);
        $body = str_ireplace('<h1>', '# ', $body);
        $body = str_ireplace('<h2>', '## ', $body);
        $body = str_ireplace('<h3>', '### ', $body);
        $body = str_ireplace('<h4>', '#### ', $body);
        $body = str_ireplace('<li>', '* ', $body);
        $body = str_ireplace('&#8212;', '—', $body);
        $body = str_ireplace('&nbsp;', ' ', $body);
        $body = trim($body);
        #
        # Assemble the file contents
        $contents = $title . "\n";
        $contents.= "====\n\n";
        $contents.= $date . "\n\n";
        $contents.= $body . "\n\n";
        $contents.= PAGE_FOOTER;
        #
        # Output content to file
        file_put_contents($filename, $contents);
    }
    #
    # Pause to avoid rate limiting
    sleep(1);
}
die("done\n");
