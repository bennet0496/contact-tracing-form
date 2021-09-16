<?php
if(!defined("INCLUDED"))
    die();

define("HERE", dirname(__FILE__));

const DB_SERVER = "localhost";
const DB_PORT = "3306";

const DB_USER = "covid";
const DB_PASS = "C0v1D";

const DB_NAME = "covid";


const PERSON_LIMIT = 500;
const EVENT_NAME = "ACME REGISTRATION";

const LOGO_FS_PATH = HERE."/img/logo.png";
const LOGO_WEB_PATH = "img/logo.png";

const MAIL_FROM = "no-reply@example.com";
const MAIL_SERVER = "smtp.mailtrap.io";
const MAIL_PORT = 2525;
const MAIL_SSL = false;

const MAIL_LOGIN = "***REMOVED***";
const MAIL_PASSWORD = "***REMOVED***";


const TRUSTED_HOSTS = [];

const RESPONSIBLE =<<<EOT
Prof. Dr. John Doe<br />
Acme Corporation <br />
42 High Street <br />
City XY 55555 <br />
Phone: +0 800 555-1234
EOT;

const ORGANISATION = "ACME Corporation";
$ORGANISATION = ORGANISATION;

const PRIVACY_POLICY =<<<EOT
<p>The data processing is necessary due to current legal corona regulaions in Saxony (Sächs. Corona-Schutzverordnung). Your personal data is stored solely for the purpose of maintaining access to the institute cafeteria. The data is not forwarded to any third parties. The data will be deleted</p>
<ul>
    <li>4 weeks after the return of the chip to the institute (no further access to the cafeteria)</li>
</ul>
<p>or</p>
<ul>
    <li>once the legal corona restricitions on gastronomy are lifted permanently.</li>
</ul>
EOT;


const REQUIRE_VACCINATION_DATE = true;
const REQUIRE_VACCINATION_STATUS = true;
const REQUIRE_RECOVERY_DATE = true;
const REQUIRE_RECOVERY_STATUS = true;
const REQUIRE_TEST_DATE = true;
const REQUIRE_TEST_STATUS = true;
const REQUIRE_TEST_AGENCY_OF = ['on-site test', 'self-test', 'test-center'];
const REQUIRE_TEST_TYPE_OF = ['Rapid Antigen', 'PCR'];


const ISO_CODES = HERE."/include/iso_3digit_alpha_country_codes.csv";

