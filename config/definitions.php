<?php

declare(strict_types = 1);

use Aura\Sql\ExtendedPdo;
use Delight\Auth\Auth;
use Delight\I18n\Codes;
use Delight\I18n\I18n;
use Pu239\Radiance;
use function DI\autowire;
use Imdb\Config;
use Jobby\Jobby;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Pu239\Ach_bonus;
use Pu239\Achievement;
use Pu239\Achievementlist;
use Pu239\Ban;
use Pu239\Block;
use Pu239\Bonuslog;
use Pu239\Bookmark;
use Pu239\BotReplies;
use Pu239\BotTriggers;
use Pu239\Bounty;
use Pu239\Cache;
use Pu239\Casino;
use Pu239\CasinoBets;
use Pu239\Coin;
use Pu239\Comment;
use Pu239\Database;
use Pu239\FailedLogin;
use Pu239\Forum;
use Pu239\HappyLog;
use Pu239\Image;
use Pu239\ImageProxy;
use Pu239\IP;
use Pu239\Message;
use Pu239\Mood;
use Pu239\Notify;
use Pu239\Offer;
use Pu239\Peer;
use Pu239\Phpzip;
use Pu239\Poll;
use Pu239\PollVoter;
use Pu239\Post;
use Pu239\Referrer;
use Pu239\Request;
use Pu239\Searchcloud;
use Pu239\Session;
use Pu239\Settings;
use Pu239\Sitelog;
use Pu239\Snatched;
use Pu239\Torrent;
use Pu239\Upcoming;
use Pu239\User;
use Pu239\Userblock;
use Pu239\Usersachiev;
use Pu239\Wiki;
use Rakit\Validation\Validator;
use Scriptotek\GoogleBooks\GoogleBooks;
use SlashTrace\EventHandler\DebugHandler;
use SlashTrace\Sentry\SentryHandler;
use SlashTrace\SlashTrace;

return [
    Auth::class => DI\factory(function (ContainerInterface $c) {
        $pdo = $c->get(PDO::class);
        $auth = new Auth($pdo, null, null, PRODUCTION);

        return $auth;
    }),
    PDO::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        $dsn = !$env['db']['use_socket'] ? "{$env['db']['type']}:host={$env['db']['host']};port={$env['db']['port']};dbname={$env['db']['database']};charset={$env['db']['charset']}" : "{$env['db']['type']}:unix_socket={$env['db']['socket']};dbname={$env['db']['database']};charset={$env['db']['charset']}";
        $username = $env['db']['username'];
        $password = $env['db']['password'];
        $attributes = $env['db']['attributes'];
        $pdo = new ExtendedPdo($dsn, $username, $password, $attributes);

        return $pdo;
    }),
    SlashTrace::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        if (!PRODUCTION) {
            $slashtrace = new SlashTrace();
            $slashtrace->addHandler(new DebugHandler());
            $slashtrace->register();
        } else {
            if (!empty($env['api']['sentry'])) {
                $handler = new SentryHandler("{$env['api']['sentry']}");
                $slashtrace = new SlashTrace();
                $slashtrace->addHandler($handler);
                $slashtrace->register();
            }
        }
    }),
    mysqli::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        if ($env['db']['use_socket']) {
            $mysqli = new mysqli($env['db']['host'], $env['db']['username'], $env['db']['password'], $env['db']['database'], 0, $env['db']['socket']);
        } else {
            $mysqli = new mysqli($env['db']['host'], $env['db']['username'], $env['db']['password'], $env['db']['database'], $env['db']['port']);
        }
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        return $mysqli;
    }),
    Redis::class => DI\factory(function (ContainerInterface $c) {
        $client = new Redis();
        $env = $c->get('env');
        if (!$env['redis']['use_socket']) {
            $client->connect($env['redis']['host'], $env['redis']['port']);
        } else {
            $client->connect($env['redis']['socket']);
        }
        $client->select($env['redis']['database']);

        return $client;
    }),
    Memcached::class => DI\factory(function (ContainerInterface $c) {
        $client = new Memcached();
        $env = $c->get('env');
        if (!count($client->getServerList())) {
            if (!$env['memcached']['use_socket']) {
                $client->addServer($env['memcached']['host'], $env['memcached']['port']);
            } else {
                $client->addServer($env['memcached']['socket'], 0);
            }
        }

        return $client;
    }),
    GoogleBooks::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        if (!empty($env['api']['google'])) {
            $books = new GoogleBooks([
                'key' => $env['api']['google'],
                'country' => 'US',
            ]);
        } else {
            $books = new GoogleBooks(['country' => 'US']);
        }

        return $books;
    }),
    Config::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        $config = new Config();
        $config->language = $env['language']['imdb'];
        $config->cachedir = IMDB_CACHE_DIR;
        $config->throwHttpExceptions = 0;
        $config->default_agent = get_random_useragent();

        return $config;
    }),
    PHPMailer::class => DI\factory(function (ContainerInterface $c) {
        $env = $c->get('env');
        if ($env['mail']['smtp_enable']) {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $env['mail']['smtp_host'];
            $mail->SMTPAuth = $env['mail']['smtp_auth'];
            $mail->Username = $env['mail']['smtp_username'];
            $mail->Password = $env['mail']['smtp_password'];
            $mail->SMTPSecure = $env['mail']['smtp_secure'];
            $mail->Port = $env['mail']['smtp_port'];

            return $mail;
        }

        return null;
    }),
    Validator::class => DI\factory(function () {
        $validator = new Validator();

        return $validator;
    }),
    I18n::class => DI\factory(function () {
        $i18n = new I18n([
            Codes::AF_ZA,
            Codes::DA_DK,
            Codes::DE_DE,
            Codes::EN_US,
            Codes::ES_ES,
            Codes::ET_EE,
            Codes::FI_FI,
            Codes::FR_FR,
            Codes::IT_IT,
            Codes::KO_KR,
            Codes::NB_NO,
            Codes::NL_NL,
            Codes::PL_PL,
            Codes::RO_RO,
            Codes::RU_RU,
            Codes::SV_SE,
            Codes::ZH_CN,
            Codes::ZH_TW,
        ]);

        return $i18n;
    }),
];
