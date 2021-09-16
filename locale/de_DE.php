<?php
CONST LANG = [
    "Registration form" => "Registrierungsformular",
    "Core data" => "Kerndaten",
    #: inc_register_form.php:67
    "First name" => "Vorname",

    #: inc_register_form.php:74
    "Last name" => "Nachname",

    #: inc_register_form.php:84
    "Street" => "Stra&szlig;e",

    #: inc_register_form.php:91
    "House number" => "Hausnummer",

    #: inc_register_form.php:101
    "ZIP code" => "PLZ",

    #: inc_register_form.php:108
    "City" => "Stadt",

    #: inc_register_form.php:118
    "State/Province" => "Bundesland/Provinz",

    #: inc_register_form.php:125
    "Country" => "Land",

    #: inc_register_form.php:143
    "Email" => "Email",

    #: inc_register_form.php:150
    "Phonenumber" => "Telefonnummer",

    #: inc_register_form.php:176
    "I read the |Privacy Policy| and agree to the data processing" => "Ich habe die |Datenschutzerkl&auml;rung| gelesen und stimme der Datenverarbeitung zu",

    #: inc_register_form.php:184
    "In the next step your are required to verify your email-address or phonenumber. After that your access code is generated which is used to verify your data before entering the location." => "Im n&auml;chsten Schritt m&uuml;ssen Sie ihre Email oder Telefon best&auml;tigen. Im Anschluss erhalten den Zugangscode, welcher zur Verifierung vor dem Zutritt zum Gel&auml;nde dient.",

    #: inc_register_form.php:188
    "Register" => "Registrieren",

    #: inc_register_form.php:198
    "Privacy Policy" => "Datenschutzerkl&auml;rung",

    #: inc_register_form.php:204
    "Close" => "Schlie&szlig;en",

    "Please Verify your Email" => "Bitte verifizieren Sie ihre Email",

    "Hi,\r\n\r\nPlease use this code to verify your Email: %s\r\n\r\nBest Regards,\r\n%s\r\n" => "Hallo,\r\n\r\nBitte benutzen Sie diesen Code zur Verifizierung ihrer Email: %s\r\n\r\nMfG\r\n%s\r\n",

    "Verify your Email" => "Verifizieren Sie ihre Email",
    "Please enter your verification code" => "Bitte geben Sie ihren Verifizierungs-Code ein",
    "Verification Code" => "Verifizierungs-Code",
    "Submit" => "Absenden",
    "Resend Code" => "Code erneut senden",
    "Please also check your SPAM-Folder" => "Bitte pr&uuml;fen Sie auch ihren SPAM Ordner",
    "The code you entered was invalid" => "Der eingebene Code ist ung&uuml;ltig",
    "User Details" => "Nutzerdetails",
    "Thank you for registering. You now may print this document to get access to the Location. If you don't have a printer you can also send this to your Email or use Browsers integrated PDF printer to save this as a PDF or take screenshot of the browser window, and show this document digitally on entrance." => "Vielen Dank f&uuml;r die Registrierung. Bitte drucken Sie dieses Dokument um Zugang zu erhalten. Sollten Sie keinen Drucker besitzen, k&ouml;nnen Sie sich dieses Dokument per Email senden oder den PDF-Drucker des Browsers verwenden um es als PDF zu speichen und es digital am Eingang vorzeigen.",
    "Phone number" =>  "Telefonnummer",
    "Address" => "Adresse",
    "If you get test COVID-19 positive within 14-days of the admission time, please tell us and the health authorities, that you attended this event." => "Sollten Sie innhalb von 14-Tagen nach Zugang positiv aus COVID-19 getestet werden, teilen Sie uns dieses Bitte mit und informieren Sie die Gesundheitsbeh&ouml;rde &uuml;ber die Teilnahme an dieser Veranstaltung",
    "Print" => "Drucken",
    "Send via email" => "per Email senden",
    "Done" => "Fertig",

];

function LANG($msg) {
    if(key_exists($msg, LANG)){
        return LANG[$msg];
    } else {
        return $msg;
    }
}