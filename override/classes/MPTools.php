<?php
class MPTools {
    /** V2 **/
    public static $geocodePessacDomont = array(
        'pessac' => array('lat'=>'44.8350088', 'lng'=>'-0.587268999999992', 'zoom'=>10, 'radius'=>26000, 'distanceMax'=>26, 'numDep'=>33),
        'domont' => array('lat'=>'49.0256376', 'lng'=>'2.3202582999999777', 'zoom'=>11, 'radius'=>13000, 'distanceMax'=>13, 'numDep'=>95),
    );
    
    const LIMITE_HOUR_COMMAND_TODAY = 11;
    
    /** V1 **/
    public static $listJourSmall    = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
    public static $listJour         = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    public static $listMois         = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
    public static $listMoisDiffere  = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
    public static $dayOfWeek        = array('', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    public static $listRegionCp     = array('idf'=>array(95,92,75,78,77), 'aquitaine'=>array(33));
    public static $listRegionName   = array('idf'=>'Ile-de-France', 'aquitaine'=>'Aquitaine');
    
    /** V2 **/
    /**
     * calcul de la distance 3D
     * @param int $lat1
     * @param int $lon1
     * @param int $lat2
     * @param int $lon2
     * @param int $alt1
     * @param int $alt2
     * @return int
     */
    public static function distance($lat1, $lon1, $lat2, $lon2, $alt1=0, $alt2=0) 
    {
        //rayon de la terre
        $r = 6366;
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $lon1 = deg2rad($lon1);
        $lon2 = deg2rad($lon2);

        //recuperation altitude en km
        //$alt1 = $alt1/1000;
        //$alt2 = $alt2/1000;

        //calcul précis
        $dp= 2 * asin(sqrt(pow (sin(($lat1-$lat2)/2) , 2) + cos($lat1)*cos($lat2)* pow( sin(($lon1-$lon2)/2) , 2)));

        //sortie en km
        $d = $dp * $r;
        return $d;
        
        //Pythagore a dit que :
        //$h = sqrt(pow($d,2)+pow($alt2-$alt1,2));

        //return $h;
    }
    
    /**
     * Recupere les geocode pour un code postal
     * @param int $cp
     * @return array
     */
    public static function getGeocodeFromCp($cp) 
    {
        $geocoder = "http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false";
        
        // Requête envoyée à l'API Geocoding
        $query = sprintf($geocoder, urlencode(utf8_encode($cp . ' ' . $cp . ' FRANCE')));

        $result = json_decode(file_get_contents($query));
        $json = $result->results[0];

        return array(
            'lat'   => (string) $json->geometry->location->lat,
            'lng'   => (string) $json->geometry->location->lng,
        );
    }
    
    /**
     * Ajoute la notion de bio
     * @param int $bio
     * @return boolean
     */
    public static function addCookieBio($bio) {
        $cookie = new MPCookie();
        $cookie->writeCookie('bio', $bio, 365);
        return false;
    }
    
    /**
     * Test si le cp de l'internaute est proche d'un centre MP
     * @param int $cp
     * @return boolean
     */
    public static function isCpEnable($cp) {
        $numDep = 0;
        $latLng = self::getGeocodeFromCp($cp);
        foreach(self::$geocodePessacDomont as $value) {
            $distance = self::distance($value['lat'], $value['lng'], $latLng['lat'], $latLng['lng'], 1, 1);
            if($distance<=$value['distanceMax']) {
                $numDep = $value['numDep'];
                break;
            }
        }
        $cookie = new MPCookie();
        if(empty($numDep)) {
            $cookie->writeCookie('deliveryDep', self::$geocodePessacDomont['pessac']['numDep'], 365);
            return false;
        } else {
            $cookie->writeCookie('cp', $cp, 365);
            $cookie->writeCookie('deliveryDep', $numDep, 365);
            return $numDep;
        }
    }
    
    public static function getListDates() {
        $dateTime = new DateTime();
        $listDate = array();
        for($i=0;$i<=11;$i++) {
            $listDate[$i]['value'] = $dateTime->format('Ym');
            $listDate[$i]['label'] = self::$listMoisDiffere[$dateTime->format('n')] . ' ' . $dateTime->format('Y');
            $dateTime->modify('-1 month');
        }
        
        return $listDate;
    }
    
    /** V1 **/
    public static function isGoodDay($jour) {
        if(in_array(ucfirst($jour), self::$listJour)) {
            return true;
        }
        
        throw new Exception('Mauvais jour');
    }

    public static function changeCookie($numDep) {
        setcookie('loc', $numDep, time()+60*60*24*360, '/');
        echo filter_input(INPUT_COOKIE, 'loc');
    }

    public static function getListDays($smallStringDay = false, $nbDays = 7, $addDays = 0, $removeMonday = true) {
        $now        = new DateTime();
        $listDays   = array();
        if($addDays) {
            $now->modify('+' . (int) $addDays . ' day');
        }
        for($i=1;$i<=$nbDays;$i++) {
            //$now->add(new DateInterval('P1D'));
            $now->modify('+1 day');
            if($removeMonday && $now->format('w')==1) {
                continue;
            }
            if($smallStringDay) {
                $listDays[$now->format('Y-m-d')] = self::$listJourSmall[$now->format('w')] . $now->format(' d') . ' ' . self::$listMois[$now->format('n')-1] . $now->format(' Y');
            } else {
                $listDays[$now->format('Y-m-d')] = self::$listJour[$now->format('w')] . $now->format(' d') . ' ' . self::$listMois[$now->format('n')-1] . $now->format(' Y');   
            }
        }
        
        return $listDays;
    }

    /**
     * @param MarketDto $marketDto
     * @return DateTime
     */
    public static function whatIsNextDateForThisMarket(MarketDto $marketDto, $addWeek = false) {
        $now        = new DateTime();
        $nextDay    = clone($now);
        $day = 1;
        $useDelay = true;
        $dateTime = new DateTime();
        if($dateTime->format('H')<self::LIMITE_HOUR_COMMAND_TODAY) {
            $useDelay = false;
        }
        if($addWeek) {
            $nextDay->modify('+7 day');
        }
        do {
            if($useDelay) {
                $nextDay->modify('+1 day');
            }
            $useDelay = true;
            $stringNd = self::$listJour[$nextDay->format('w')];
            $isOpen = $marketDto->{'get'.$stringNd}();
            $day++;
        } while($isOpen!=1 && $day<=8);
        
        if($isOpen!=1) {
            return null;
        }
        return $nextDay;
    }

    /**
     * @param string $day
     * @return DateTime
     */
    public static function getNextDateFromDay($day) {
        $now        = new DateTime();
        $nextDay    = clone($now);
        $addDay = true;
        if($now->format('H')<self::LIMITE_HOUR_COMMAND_TODAY) {
            $addDay = false;
        }
        do {
            if($addDay) {
                $nextDay->modify('+1 day');
            }
            $addDay = true;
        } while($day!=strtolower(self::$listJour[$nextDay->format('w')]));

        return $nextDay;
    }

    public static function getLimitCommandeDateFromDay($day) {
        $array = array_keys(self::$listJour, ucfirst($day));
        $key = $array[0];
        switch($key) {
            case 0 :
                $keyLimit = 5;
                break;
            case 1 :
                $keyLimit = 6;
                break;
            case 2 :
                $keyLimit = 0;
                break;
            case 3 :
                $keyLimit = 1;
                break;
            case 4 :
                $keyLimit = 2;
                break;
            case 5 :
                $keyLimit = 3;
                break;
            case 6 :
                $keyLimit = 4;
                break;
        }
        $dayLimit = strtolower(self::$listJour[$keyLimit]);
        return self::getNextDateFromDay($dayLimit, false);
    }
    
    public static function getStringDayFromDate($string) {
        $date = DateTime::createFromFormat('Y-m-d', $string);
        return self::$listJour[$date->format('w')];
    }

    public static function getStringDayFromInterval($interval) {
        $date = new DateTime();
        $date->add(new DateInterval('P' . $interval . 'D'));
        return self::$listJour[$date->format('w')];
    }

    public static function isPast($string) {
        $date   = DateTime::createFromFormat('Y-m-d', $string);
        $now    = new DateTime();
        return ($now>$date);
    }
    
    public static function rewrite($string) {
        $string = strtolower($string);
        $string = str_replace(' ', '-', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('&', '', $string);
        $string = str_replace('"', '', $string);
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $string = strtr( $string, $unwanted_array );
        return $string;
    }

    public static function formatHeure($heure) {
        $retour = $heure[0].$heure[1].'h'.$heure[2].$heure[3];
        return $retour;
    }
    
    public static function isEmail($email) {
        return preg_match('/^([a-zA-Z0-9_.\-+])+\@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/', $email);
    }
    
    public static function isTel($tel) {
        return preg_match('/^([0-9.]+)([0-9.]+)([0-9.]+)([0-9.]+)([0-9.]+)$/', $tel);
    }
    
    public static function isName($name) {
        return preg_match('/^([a-zA-Z]+)$/', self::rewrite($name));
    }

    public static function isCp($cp) {
        return preg_match('/^([0-9.]{5})$/', $cp);
    }
    
    public static function ExtractFirstMarket($market) {
        $marketPerDay = array_shift($market);
        $marketFirstMarket = array_shift($marketPerDay);
        return array(array($marketFirstMarket));
    }
    
    public static function orderMarketListByDate($listMarket) {
        $tmp = array();
        foreach($listMarket as $marketDto) {
            $tmp[$marketDto->getNextDateOpenWithFormat('Ymd')][] = $marketDto;
        }
        
        ksort($tmp);
        return $tmp;
    }
    
    public static function isCpEnableForDelivry($cp, $dep) {
        $latlngDistanceMax = self::getLatLngFromDepartement($dep);
        $bordeauLat = $latlngDistanceMax['lat'];
        $bordeauLng = $latlngDistanceMax['lng'];
        $distanceMax = $latlngDistanceMax['distanceMax']; //en km
        $latLng = self::getGeocodeFromCp($cp);
        $distance = self::distance($bordeauLat, $bordeauLng, $latLng['lat'], $latLng['lng'], 1, 1);
        return ($distance<=$distanceMax);
    }
    
    public static function getAdresse($lat, $lng) {
        $content = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat .',' . $lng . '&sensor=false');
 
	if(!$content)
		return false;
 
	$json = json_decode($content, true);
        if(!$json || $json['status'] != 'OK')
		return false;
 
	if(!isset($json['results'][0]['formatted_address']) && empty($json['results'][0]['formatted_address']))
		return false;
 
	$region = $json['results'][0]['address_components'][4]['short_name'];
        
        return $region;
    }
    
    public static function getLatLngFromDepartement($departement) {
        switch(strtoupper($departement)) {
            case 'AQUITAINE' :
                $latlng = array('lat'=>'44.8350088', 'lng'=>'-0.587268999999992', 'zoom'=>10, 'radius'=>26000, 'distanceMax'=>26);
                break;
            case 'IDF' :
                $latlng = array('lat'=>'49.0256376', 'lng'=>'2.3202582999999777', 'zoom'=>11, 'radius'=>13000, 'distanceMax'=>13);
                break;
            default:
                $latlng = array('lat'=>'44.8350088', 'lng'=>'-0.587268999999992', 'zoom'=>10, 'radius'=>26000, 'distanceMax'=>26);
                break;
        }
        
        return $latlng;
    }
    
    public static function isIDF($cp) {
        $nbCP = strlen($cp);
        if($nbCP==5) {
            $numDep = substr($cp, 0, 2);
            if(in_array($numDep, self::$listRegionCp['idf'])) {
                return true;
            }
        }
        return false;
    }
    
    public static function getCookie($var) {
        $content = filter_input(INPUT_COOKIE, $var);
        return $content;
    }
}