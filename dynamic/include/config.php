<?php
namespace Jonglaria;

class Config implements \ArrayAccess{
    private $ini;
    public function __construct($ini_file = 'config.ini')
    {
        $this->ini = parse_ini_file($ini_file);
    }
    public function offsetSet($offset, $value){
        # TODO Currently only for reading
    }
    public function offsetGet($offset): mixed{
        return $this->ini[$offset];
    }
    public function offsetExists($offset): bool{
        return NULL != $this->ini[$offset];
    }
    public function offsetUnset($offset){
        # TODO
    }
}

?>
