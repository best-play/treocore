<?php
declare(strict_types = 1);

namespace Espo\Modules\TreoCrm\Configs;

return [
    'database' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'port' => '',
        'charset' => 'utf8',
        'dbname' => '',
        'user' => '',
        'password' => '',
    ],
    'useCache' => false, // todo set true
    'recordsPerPage' => 20,
    'recordsPerPageSmall' => 5,
    'applicationName' => 'TreoPim',
    'version' => '5.0.2',
    'timeZone' => 'UTC',
    'dateFormat' => 'MM/DD/YYYY',
    'timeFormat' => 'hh:mm a',
    'weekStart' => 0,
    'thousandSeparator' => ',',
    'decimalMark' => '.',
    'exportDelimiter' => ';',
    'currencyList' => ['USD'],
    'defaultCurrency' => 'USD',
    'baseCurrency' => 'USD',
    'currencyRates' => [],
    'outboundEmailIsShared' => true,
    'outboundEmailFromName' => 'TreoPim',
    'outboundEmailFromAddress' => '',
    'smtpServer' => '',
    'smtpPort' => 25,
    'smtpAuth' => true,
    'smtpSecurity' => '',
    'smtpUsername' => '',
    'smtpPassword' => '',
    'languageList' => [
        'en_GB',
        'en_US',
        'es_MX',
        'cs_CZ',
        'da_DK',
        'de_DE',
        'es_ES',
        'fr_FR',
        'id_ID',
        'it_IT',
        'lt_LT',
        'nb_NO',
        'nl_NL',
        'tr_TR',
        'sr_RS',
        'ro_RO',
        'ru_RU',
        'pl_PL',
        'pt_BR',
        'uk_UA',
        'vi_VN',
        'zh_CN'
    ],
    'language' => 'en_US',
    'logger' =>
    [
        'path' => 'data/logs/espo.log',
        'level' => 'WARNING', /** DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY */
        'rotation' => true,
        'maxFileNumber' => 30,
    ],
    'authenticationMethod' => 'Espo',
    'globalSearchEntityList' =>
    [
        'Account',
        'Contact',
        'Lead',
        'Opportunity',
    ],
    'tabList' => ["Account", "Contact", "Lead", "Opportunity", "Case", "Email", "Calendar", "Meeting", "Call", "Task", "_delimiter_", "Document", "Campaign", "KnowledgeBaseArticle", "Stream", "User"],
    'quickCreateList' => ["Account", "Contact", "Lead", "Opportunity", "Meeting", "Call", "Task", "Case", "Email"],
    'exportDisabled' => false,
    'adminNotifications' => true,
    'adminNotificationsNewVersion' => true,
    'adminNotificationsCronIsNotConfigured' => true,
    'assignmentEmailNotifications' => false,
    'assignmentEmailNotificationsEntityList' => ['Lead', 'Opportunity', 'Task', 'Case'],
    'assignmentNotificationsEntityList' => ['Meeting', 'Call', 'Task', 'Email'],
    "portalStreamEmailNotifications" => true,
    'streamEmailNotificationsEntityList' => ['Case'],
    'emailMessageMaxSize' => 10,
    'notificationsCheckInterval' => 10,
    'disabledCountQueryEntityList' => ['Email'],
    'maxEmailAccountCount' => 2,
    'followCreatedEntities' => false,
    'b2cMode' => false,
    'restrictedMode' => false,
    'theme' => 'TreoDarkTheme',
    'massEmailMaxPerHourCount' => 100,
    'personalEmailMaxPortionSize' => 10,
    'inboundEmailMaxPortionSize' => 20,
    'authTokenLifetime' => 0,
    'authTokenMaxIdleTime' => 120,
    'userNameRegularExpression' => '[^a-z0-9\-@_\.\s]',
    'addressFormat' => 1,
    'displayListViewRecordCount' => true,
    'dashboardLayout' => [
        (object) [
            'name' => 'My Espo',
            'layout' => [
                (object) [
                    'id' => 'default-activities',
                    'name' => 'Activities',
                    'x' => 2,
                    'y' => 2,
                    'width' => 2,
                    'height' => 2
                ],
                (object) [
                    'id' => 'default-stream',
                    'name' => 'Stream',
                    'x' => 0,
                    'y' => 0,
                    'width' => 2,
                    'height' => 4
                ],
                (object) [
                    'id' => 'default-tasks',
                    'name' => 'Tasks',
                    'x' => 2,
                    'y' => 0,
                    'width' => 2,
                    'height' => 2
                ]
            ]
        ]
    ],
    'calendarEntityList' => ['Meeting', 'Call', 'Task'],
    'activitiesEntityList' => ['Meeting', 'Call'],
    'historyEntityList' => ['Meeting', 'Call', 'Email'],
    'lastViewedCount' => 20,
    'cleanupJobPeriod' => '1 month',
    'cleanupActionHistoryPeriod' => '15 days',
    'cleanupAuthTokenPeriod' => '1 month',
    'currencyFormat' => 1,
    'currencyDecimalPlaces' => null,
    'aclStrictMode' => false,
    'aclAllowDeleteCreated' => false,
    'inlineAttachmentUploadMaxSize' => 20,
    'isInstalled' => false
];

