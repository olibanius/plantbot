<?php

if (is_file('/var/tmp/forcefeed.flag')) {
    echo 'ok';
    shell_exec('php plantbot.php forcefeed');
    unlink('/var/tmp/forcefeed.flag');
} else {
    echo 'no flag';
}
