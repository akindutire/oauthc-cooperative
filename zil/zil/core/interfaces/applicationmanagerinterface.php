<?php
namespace zil\core\interfaces;

use zil\core\scrapper\Info;

    interface ApplicationManager
    {
        public function useApp(string $app_name);

        public function showApps();

        public function createApp(Info $Info);

        public function exitApp(string $app_name);
        
        public function destroy(Info $Info, ?string $name);

    }
?>