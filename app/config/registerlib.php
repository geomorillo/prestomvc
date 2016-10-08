<?php

return [
    "tracy" => [
        "path" => LIB_PATH . "tracy" . DS . "tracy.php",
        "callback" => function() {
            if (ENABLE_DEBUG) {
                \Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);
            } else {
                \Tracy\Debugger::enable(\Tracy\Debugger::PRODUCTION, LOG_PATH);
            }
        }
    ],
    "notorm" => [
        "path" => LIB_PATH . "notorm" . DS . "NotORM.php",
        "callback" => function() {
            
        }
    ],
    "notormdbpanel" => [
        "path" => LIB_PATH . "notorm-tracy-panel" . DS . "NotOrmTracyPanel.php",
        "callback" => function() {
            
        }
    ]
];
