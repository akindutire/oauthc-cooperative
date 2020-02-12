<?php
/**
 * Author: Akindutire Ayomide Samuel
 */

 namespace zil\core\middleware;

 use zil\factory\Session as SessionFactory;

 class TemplateEngine{

      public function __construct()
      {
         
      }

      public function mutate(string $pageContext) : string{
        $pageContext = preg_replace(
            [   
                '/\{\![\s]*csrf[\s]*\!\}/',
                '/\{\!\!(.+)\!\!\}/',
                '/(\@)(extend)(\(.+\))/i',
                '/(\@)(include)(\(.+\))/i',
                '/\{\!([^!}]+)\!\}/', 
                '/(\@)(if|foreach|while|for)(\(.+\))/i', 
                '/\@end(if|foreach|while|for)/i', 
                '/(\@)(elseif)(\(.+\))/i', 
                '/(\@)(else)/i', 
                '/(\@)(label)\((\'|\")*([\w]+)(\'|\")*\)/i',
                '/(\@)(goto)\((\'|\")*([\w]+)(\'|\")*\)/i',
            ],
            [
                '<!--ZDX_BINDING_CSRF_STRICTLY_FOR_ENCRYPTION-->',
                '<?php $1; ?>',
                '',
                '<?php include_once$3; ?>',
                '<?php echo $1; ?>', 
                '<?php $2 $3 { ?>', 
                '<?php } ?>', 
                '<?php } $2 $3 { ?>', 
                '<?php } $2 { ?>',
                '<?php $4: ?>',
                '<?php $2 $4: ?>',
            ],
            $pageContext
            );

        return $pageContext;
      }
    
 }
?>