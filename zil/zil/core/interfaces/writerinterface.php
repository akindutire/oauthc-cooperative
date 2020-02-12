<?php
namespace zil\core\interfaces;

use zil\core\scrapper\Info;

    interface Writer
    {

        public function destroy(Info $Info, string $name);

    }
?>