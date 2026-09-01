<?php

require_once __DIR__ . '/env.php';

// ENVIRONMENT SETTING
define("APP_ENV", env("APP_ENV", "production")); // 'production' or 'development'
define("APP_NAME", env("APP_NAME", "Komune"));
define("APP_DESCRIPTION", "Platform Digital Administrasi, Keuangan & Edukasi Komunitas");
define("APP_VERSION", "2.0.0");

///CONFIG API BNI
define("BNI_CLIENT_ID", env("BNI_CLIENT_ID", ""));
define("BNI_SECRET_KEY", env("BNI_SECRET_KEY", ""));
define("BNI_URL_TARGET", env("BNI_URL_TARGET", "https://api.bni-ecollection.com/"));
define("BNI_DIFF_LIMIT", (int)env("BNI_DIFF_LIMIT", 480));

///VA BNI
define("KODE_KOMUNITAS", env("KODE_KOMUNITAS", "345"));
define("KODE_SEKOLAH", KODE_KOMUNITAS); // Backward-compatibility alias
define("BNI_VA", "988" . BNI_CLIENT_ID . KODE_KOMUNITAS);

// CONFIG KOMUNITAS (KOMUNE)
define("KOMUNE_NAME", env("KOMUNE_NAME", "Komune"));
define("KOMUNE_NAME_SHORT", env("KOMUNE_NAME_SHORT", "Komunitas Warga"));
define("KOMUNE_DOMAIN", env("APP_URL", "https://demo.sekolah-smartbilling.net/komune"));

// Aliases for compatibility
define("SCHOOL_NAME", KOMUNE_NAME);
define("SCHOOL_NAME_SHORT", KOMUNE_NAME_SHORT);
define("SCHOOL_NAME_SEME", KOMUNE_NAME);
define("SCHOOL_APP", KOMUNE_NAME);
define("SCHOOL_DOMAIN", KOMUNE_DOMAIN);

// PASSWORDS & SALT
define("PASS_KOMUNE", env("PASS_DEFAULT", "komune."));
define("PASS_SEKOLAHKU", PASS_KOMUNE); // compatibility
define("PASS_OFFICER", "officer");

// IDENTIFIER
define("KOMUNE_CODE", "komune");
define("ABS", KOMUNE_CODE); // compatibility

// PUBLIC CONTROLLER (Bypass session requirement)
$arr = ["doc", "feed"];
define("PUBLIC_CONTROLLER", $arr);