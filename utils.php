<?php

class Utils
{
    /** generate a clean, lowercase, no whitespace name (e.g. for URL)  */
    public static function generateCleanName($name)
    {
        $clean = strtolower($name);
        $clean = str_replace('ä', 'ae', $clean);
        $clean = str_replace('ö', 'oe', $clean);
        $clean = str_replace('ü', 'ue', $clean);
        $clean = str_replace('ß', 'ss', $clean);

        $clean = preg_replace('/[\w]+/', "-", $clean); // replace whitespace
        $clean = preg_replace('/[^a-z0-9-]+/', '', $clean); // remove filthy characters
        return $clean;
    }
}

?>