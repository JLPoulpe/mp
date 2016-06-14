<?php
class statisticsNews extends ObjectModel {
    public $idStatisticsNews;
    public $dateNews;
    public $idUser;
    public $dateTimeAction;
    public $type;
    
    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table' => 'statistics_news',
        'primary' => 'id_statistics_news',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id_statistics_news'    =>  array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
            'date_news'             =>  array('type' => self::TYPE_DATE, 'required' => true),
            'id_user'               =>  array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
            'datetime_open'         =>  array('type' => self::TYPE_DATE, 'required' => true),
            'type'                  =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 10),
        ),
    );

    public function __construct($id_supplier_account = null)
    {
        parent::__construct($id_supplier_account);
    }
    
    public function addOpen($idUser, $dateNews) {
        $dateTime = new DateTime();
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'statistics_news (date_news, id_user, datetime_action, type) VALUES (\'' . $dateNews . '\',' . (int) $idUser . ',\'' . $dateTime->format('Y-m-d H:i:s') . '\', \'open\');';
        try {
            $result = Db::getInstance()->execute($sql);
            return $result;
        }catch(PDOException $e) {
            d($e);
            return false;
        }
    }
    
    public function addClick($idUser, $dateNews) {
        $dateTime = new DateTime();
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'statistics_news (date_news, id_user, datetime_action, type) VALUES (\'' . $dateNews . '\',' . (int) $idUser . ',\'' . $dateTime->format('Y-m-d H:i:s') . '\', \'click\');';
        try {
            $result = Db::getInstance()->execute($sql);
            return $result;
        }catch(PDOException $e) {
            d($e);
            return false;
        }
    }
    
    
    
    private function prepareDto($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDto($row);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result);
        }
    }
    
    private function createDto($result)
    {
        $statisticsNews = null;
        if($result) {
            $statisticsNews = new $statisticsNewsDto();
            $statisticsNews->setIdStatisticsNews(isset($result['id_statistics_news']) ? $result['id_statistics_news'] : 0);
            $statisticsNews->setIdUser(isset($result['id_user']) ? $result['id_user'] : 0);
            $statisticsNews->setDateTimeAction(isset($result['datetime_action']) ? $result['datetime_action'] : '');
            $statisticsNews->setDateNews(isset($result['date_news']) ? $result['date_news'] : '');
            $statisticsNews->setType(isset($result['type']) ? $result['type'] : '');
        }
        return $statisticsNews;
    }
}