<?php
namespace Jonglaria;

class Config implements \ArrayAccess{
    private $ini;
    public function __construct($ini_file = 'config.ini')
    {
        $ini = parser_ini_file($ini_file);
    }
    public function offsetSet($offset, $value){
        # TODO Currently only for reading
    }
    public function offsetGet($offset){
        return $ini[$offset];
    }
    public function offsetExists($offset){
        return NULL != $ini[$offset];
    }
    public function offsetUnset($offset){
        # TODO
    }
}

?>
