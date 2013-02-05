<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Exchange_Factory
{
	public function create($name) {
        if ($name === null) {
            return false;
        }

        $typeClass = 'Exchange_' . ucfirst($name);
        if (class_exists($typeClass)) {
            return new $typeClass;
        }
    }
}