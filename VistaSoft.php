<?php
/**
 * Description of VistaSoft
 *
 * @author Fabio Gonzaga
 */
class VistaSoft {
    
    /**
     * Instancia VistaSoft
     * propriedade para a implementacao do design pattern singleton
     * @var VistaSoft
     * @static
     */
    private static $_instance = null;

    /**
     * Retorna VistaSoft
     * Esse metodo verifica se ja existe na memoria uma instancia
     * da classe de VistaSoft
     * Se existir apenas retorna
     * se nao instancia
     * @param void
     * @return VistaSofts
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


//    private $apikey = 'c9fdd79584fb8d369a6a579af1a8f681';
//    private $urlbase = 'sandbox-rest.vistahost.com.br';
    private $apikey = 'YOUR_API_KEY';
//    private $url_client = 'acacia12-db.vistahost.com.br';
    private $url_client = 'YOUR_URL_CLIENT';
    private $method;
    private $fields = array();
    private $filter = array();
    private $order = array();
    private $data = array();
    private $pagination = array();
    private $advFilter = array();
    
    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * 
     * @param string $url
     */
    private function request($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $resp = curl_exec($ch);
        $result = json_decode($resp, true);
        return $result;
    }
    
    public function getField($fieldname) {
        $this->fields[] = $fieldname;
        return $this;
    }

    public function setFilter($key, $value) {
        $this->filter += array($key => $value);
        return $this;
    }

    public function setOrder($field, $direction = "asc") {
        $this->order += array($field => strtolower($direction));
        return $this;
    }

    public function setPagination($page, $quantity) {
        $this->pagination += array("pagina" => $page, "quantidade" => $quantity);
        return $this;
    }

    public function images() {
        $this->fields[] = array('Foto' => array('Foto', 'Ordem', 'FotoPequena', 'Destaque', 'Tipo', 'Descricao'));
        return $this;
    }
 
    public function imagesEmpreendimento(){
        $this->fields[] = array('FotoEmpreendimento' => array('Foto', 'Ordem', 'FotoPequena', 'Destaque'));
        return $this;
    }

    public function videos() {
        $this->fields[] = array('Video' => array('Codigo', 'Descricao', 'Video'));
        return $this;
    }

    public function range($field, $min_value = 0, $max_value = 0) {
        $this->advFilter += array("And" => array($field => array($min_value, $max_value)));
    }

    private function join() {
        if (count($this->fields) > 0) {
            $this->data["fields"] = $this->fields;
        }
        if (count($this->filter) > 0) {
            $this->data["filter"] = $this->filter;
        }
        if (count($this->advFilter) > 0) {
            $this->data["advFilter"] = $this->advFilter;
        }
        if (count($this->order) > 0) {
            $this->data["order"] = $this->order;
        }
        if (count($this->pagination) > 0) {
            $this->data["paginacao"] = $this->pagination;
        }
        return $this;
    }

    /*
     * @param int (optional), use to open details 
     */
    public function search($id = null) {
        $postFields = json_encode($this->join()->data);
        $url = 'http://' . $this->url_client . $this->method . '?key=' . $this->apikey . '&showtotal=1';
        $url .= '&pesquisa=' . $postFields;
        //echo $url;
        if (!is_null($id))
            $url .= '&imovel=' . $id;
        return $this->request($url);
    }

    /**
     * Clear query parameters
     */
    public function clearSearch() {
        $this->fields = array();
        $this->filter = array();
        $this->order = array();
        $this->pagination = array();
        $this->advFilter = array();
        $this->data = array();
    }

    /**
     * Debug/Test Mode, Check your API Key 
     */
    public function ping() {
        $url = 'http://' . $this->url_client . '/reloadcache?key=' . $this->apikey;
        return $this->request($url);
    }

    /**
     * Debug/Test Mode, Check if all data are ok
     */
    public function printAll() {
        print_r($this->fields);
        print_r($this->filter);
        print_r($this->order);
        print_r($this->pagination);
        print_r($this->advFilter);
    }

    /**
     * Debug/Test Mode, Check your url
     */
    public function degub($url) {
        return $this->request($url);
    }

    public function clearCache(){
        $this->setMethod('/reloadcache');
        
    }
    
}

