<?php
class PermissionHelper extends AppHelper {

    /* Returns all access code
     *
     */

    public function getAccessCodes($access_code=null){

        if($access_code == 0)
            return array('0');

        $x=$access_code;
        $a=1;

        while ( $x >= $a){

            $x1=decbin($x);
            $a1=decbin($a);
            if(strlen($a1) < strlen($x1)){
                $count=strlen($x1) - strlen($a1);
                for($i=1;$i<=$count;$i++){
                    $a1= '0' . $a1;
                }
            }

            $and=($x1 & $a1);
            if($and > 0)
                $b[]=$a;
            $a=$a*2;
        }
        return array_values($b);
    }
}