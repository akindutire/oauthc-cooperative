<?php
namespace zil\core\interfaces;

use zil\core\scrapper\Info;

    interface ViewWriter
    {

        public function destroy(Info $Info, string $viewName, ?string $hostRnderer);

    }
?>