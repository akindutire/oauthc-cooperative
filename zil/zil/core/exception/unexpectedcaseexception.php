<?php
namespace zil\core\exception;

use \Exception;
use zil\core\scrapper\Info;

class UnexpectedCaseException extends Exception{

    protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code    = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown

    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
    }
    
    public function __toString()
    {
        $err = "<h1>Error::</h1><br> <b>{$this->message}</b> in {$this->file}({$this->line})\n\n";
        foreach($this->getTrace() as $i => $error){
            $err .= "<br><b>".$i.' '.$error['file'].'('.$error['line'].')'."</b>";
        }
        return $err;
    }


}
?>