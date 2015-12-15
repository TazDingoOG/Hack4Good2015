<?php

class Utils
{
    /** generate a clean, lowercase, no whitespace name (e.g. for URL)  */
    public static function generateCleanName($name)
    {
        $clean = strtolower($name);

        // TODO: umlaut replacement doesn't work :/
        /*$clean = str_replace("ä", "ae", $clean);
        $clean = str_replace("ö", "oe", $clean);
        $clean = str_replace("ü", "ue", $clean);
        $clean = str_replace("ß", "ss", $clean);*/

        $clean = preg_replace('/[\s.]+/', "-", $clean); // replace whitespace
        $clean = preg_replace('/[^a-z0-9-]+/', '', $clean); // remove filthy characters

        return $clean;
    }

    /**
     * Try to find missing icon files, and place them in the array
     *
     * @param array $requests - array of requests
     * @param string $icon_dir - directory where the icons are located, with trailing slash
     * @param string $icon_base_url - url to prepend for the embedded images
     */
    public static function generateIconUrls(&$requests, $icon_dir, $icon_base_url)
    {
        foreach ($requests as &$req) {
            if (!empty($req['image'])) {
                $req['image_url'] = $icon_base_url . $req['image'];
            } else {
                $cleanname = self::generateCleanName($req['name']);

                $imgfile = $cleanname . '.svg';
                if (is_file($icon_dir . $imgfile)) {
                    $req['image_url'] = $icon_base_url . $imgfile;
                }
            }
        }
    }
}

?>