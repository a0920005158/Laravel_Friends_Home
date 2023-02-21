<?php
abstract class paramHandle
{

    public function paramCheck($str)
    {
        $len_OLD = mb_strlen($str, 'utf-8');
        $tmp = strtolower($str);

        $pregStr = 'select|insert|update|delete|union|into|load_file|outfile|script|drop|http|truncate|having|shutdown';
        $w = explode('|', $pregStr);

        for ($i = 0; $i < sizeof($w); $i++) {
            $tmp = str_replace($w[$i], '', $tmp);
        }

        $len_NEW = mb_strlen($tmp, 'utf-8');

        if ($len_OLD != $len_NEW) {
            return false;
        }

        return true;
    }

    public function getParam($paramName)
    {
        $param = $this->request->get($paramName);
        if ($param != null) {
            if ($this->paramCheck($param)) {
                return $param;
            }
        }
        return null;
    }
}
