<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Worker_Factory {
    public function create($type = null, $queue = null, $flag = null) {
        if ($type === null) {
            return false;
        }

        $typeClass = 'Worker_' . ucfirst($type);
        if (class_exists($typeClass)) {
            return new $typeClass($queue, $flag);
        }
    }
}