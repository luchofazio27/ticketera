<?php

if (function_exists('imap_open')) {
    echo "IMAP OK";
} else {
    echo "IMAP NO INSTALADO";
}