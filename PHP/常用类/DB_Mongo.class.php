<?php 
class DB_Mongo{
	protected $_link = null;
	protected $_pool            =   array(); //Mongodb host list
	protected $_connectedPool   =   array(); //connected pool
	protected $_mongo           =   null; // MongoDb Object
    protected $_collection      =   null; // MongoCollection Object
    protected $_dbName          =   ''; // dbName
    protected $_collectionName  =   ''; // collectionName
    protected $_cursor          =   null; // MongoCursor Object
	public    $lastInsID        =   '';       
	public    $queryStr         =   '';
	static public $_instance = null;	
	
	static public function getInstance(){
		if(is_null(self::$_instance)){
			self::$_instance  =  new DB_Mongo();
		}
		return self::$_instance;
	}
	
	public function __construct(){
		global $configs;
		$this->_pool['write'][] = $configs['hosts']['master'];
		
		$this->_pool['read'] = array();
		foreach($configs['hosts']['slave'] as $h){
			$this->_pool['read'][] = $h;
		}
	}
	
	private function _getHandle($type = 'read'){
		if(!in_array($type, array('read', 'write'))) $this->logRecord(debug_backtrace(), "undefined handle type~");
		
		if(!isset($this->_connectedPool[$type])){
			if(!$len = count($this->_pool[$type])) $this->logRecord(debug_backtrace(), "no $type hosts~");
			
			$index = rand(0, $len-1);
			$host = $this->_pool[$type][$index];
			$this->_connectedPool[$type] = $this->_connect($host);
		}
		
		return $this->_connectedPool[$type];
	}
	
	private function _connect($hosts = array(
		'server' => 'mongodb://localhost:27017',
		'options' => array("connect" => TRUE, "persist" => ""),
	)){
    	if (($conn = new MongoClient($hosts['server'], $hosts['options'])) === FALSE) {
			$this->logRecord(debug_backtrace(), "Connect failed!");
    	}
		return $conn;
    }
	
	private function _getMongoHandle($type, $options=array()){
		if(!in_array($type, array('read', 'write'))) $this->logRecord(debug_backtrace(), 'undefined Mongo type~');
		
		if(isset($options['dbName'])) $this->_dbName = $options['dbName'];

		if(!$this->_dbName){
			$this->logRecord(debug_backtrace(), "not set dbName or default dbName");
		}
		
		$handle = $this->_getHandle($type);
        $handle->setReadPreference(MongoClient::RP_NEAREST, array());
        $this->_mongo = $handle->selectDB($this->_dbName);
		
		return $this->_mongo;
	}
	
	public function _getCollectionHandle($type, $options=array()){
		if(isset($options['collectionName']))  $this->_collectionName = $options['collectionName'];
		
		if(!$this->_collectionName){
			$this->logRecord(debug_backtrace(), "not set collectionName or default collectionName");
		}
		$this->_mongo = $this->_getMongoHandle($type, $options);
		$this->_collection = $this->_mongo->selectCollection($this->_collectionName);
		return $this->_collection;
	}
	
	public function insert($options){
		$data = $options['data'];
		$this->_collection = $this->_getCollectionHandle('write', $options);
		if($options['debug']) {
            $this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.insert(';
            $this->queryStr   .= $data?json_encode($data):'{}';
            $this->queryStr   .= ')';
			echo $this->queryStr;exit();
        }
		
		$result = $this->_collection->insert($data);
		if($result){
			$_id    = $data['_id'];
			if(is_object($_id)) {
				$_id = $_id->__toString();
			}
			return $_id;
		}else{
			return false;
		}
	}

    public function update($options) {
		if(!isset($options['where']) || empty($options['where'])){
			$this->logRecord(debug_backtrace(), "update function must set where options!");
		}
		
        $query   = $this->parseWhere($options['where']);
        $set  =  $this->parseSet($options['data']);
		$this->_collection = $this->_getCollectionHandle('write', $options);
		
        if($options['debug']) {
            $this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.update(';
            $this->queryStr   .= $query?json_encode($query):'{}';
            $this->queryStr   .=  ','.json_encode($set).')';
			echo $this->queryStr;exit();
        }

		if(isset($options['limit']) && $options['limit'] == 1) {
			$other['multiple'] = false;
		}
		if(isset($options['all']) && $options['all'] == 1){
			$other['multiple'] = true;
		}
		if(isset($options['upsert']) && $options['upsert'] == 1) {
			$other['upsert'] = true;
		}else{
			$other['upsert'] = false;
		}
		
		return $this->_collection->update($query,$set,$other);
    }
	
	public function select($options = array()){
		$query     =  $this->parseWhere(isset($options['where'])?$options['where']:array());
        $field     =  $this->parseField(isset($options['fields'])?$options['fields']:array());
		$this->_collection = $this->_getCollectionHandle('read', $options);
		

		if($options['debug']) {
			$this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.find(';
			$this->queryStr  .=  $query? json_encode($query):'{}';
			if(is_array($field) && count($field)) {
				foreach ($field as $f=>$v)
					$_field_array[$f] = $v ? 1 : 0;

				$this->queryStr  .=  $field? ', '.json_encode($_field_array):', {}';
			}
			$this->queryStr  .=  ')';
		}
		
		
		$_cursor   = $this->_collection->find($query,$field);
		if(!empty($options['order'])) {
			$order   =  $this->parseOrder($options['order']);
			if($options['debug']) {
				$this->queryStr .= '.sort('.json_encode($order).')';
			}
			$_cursor =  $_cursor->sort($order);
		}
		if(isset($options['page']) && !empty($options['page'])) { 
			list($length,$page)   =   $options['page'];
			$page    =  $page>0 ? $page : 1;
			$length  =  $length>0 ? $length : (isset($options['limit']) && is_numeric($options['limit'])?$options['limit']:20);
			$offset  =  $length*((int)$page-1);

			if($options['debug']) {
				$this->queryStr .= '.skip('.intval($offset).').limit('.intval($length).')';
			}
			$_cursor =  $_cursor->skip(intval($offset))->limit(intval($length));
		}else if(isset($options['limit']) && $options['limit'] != ''){
			$options['limit'] = explode(',', $options['limit']);
			list($offset, $length)   =   $options['limit'];
			if($length != 0){
				if($options['debug']) {
					$this->queryStr .= '.skip('.intval($offset).').limit('.intval($length).')';
				}
				$_cursor =  $_cursor->skip(intval($offset))->limit(intval($length));
			}else{
				if($options['debug']) {
					$this->queryStr .= '.skip('.intval($offset).')';
				}
				$_cursor =  $_cursor->skip(intval($offset));
			}
		}

		if($options['debug']) {
			echo $this->queryStr;exit();
		}
		
		return $this->mongObjectToArray($_cursor);
		
	}

	public function group($options = array()){
		if(!isset($options['data'])) $this->logRecord(debug_print_backtrace(), "group function must have data argument");
		
		$this->_collection = $this->_getCollectionHandle('read', $options);
		$data = $options['data'];
		
		$keys = isset($data['keys']) ? $data['keys'] : array();
		$initial = isset($data['initial']) ? $data['initial'] : array();
		$reduce = isset($data['reduce']) ? $data['reduce'] : array();
		$options = isset($data['options']) ? $data['options'] : array();
		
        if(isset($options['debug']) && $options['debug']) {
            $this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.find({key:'.json_encode($keys).',cond:'.
            json_encode($options) . ',reduce:' .
            json_encode($reduce).',initial:'.
			json_encode($initial).'})';
        }
	
		$group = $this->_collection->group($keys,$initial,$reduce,$options);
		
		return $group;
	}

    /*
    聚合函数
    */
    public function aggregate($options = array()){
        if(!isset($options['data'])){
            $this->logRecord(debug_print_backtrace(), "aggregate function must have argument");
        }
        $this->_collection = $this->_getCollectionHandle('read', $options);
        $data = $options['data'];
        
        $match = isset($data['match']) ? $data['match'] : array();
        $group = isset($data['group']) ? $data['group'] : array();
        $project = isset($data['project'])?$data['project']:array();
        $sort = isset($data['sort']) ? $data['sort'] : array();
        $limit = isset($data['limit']) ? intval($data['limit']) : 10;
        
        $pipe = array(
            array('$match' => $match),
            array('$group' => $group),
            array('$sort' => $sort),
            array('$limit' => $limit),
        );
        $aggregate = $this->_collection->aggregate($pipe);
        return $aggregate;
    }

	
	/*不允许删除数据，只允许改变数据状态
    public function delete($options=array()) {
		if(!isset($options['where']) || empty($options['where'])){
			$this->logRecord(debug_backtrace(), "delete function must set where options");
		} 
		
        $query   = $this->parseWhere($options['where']);
        if($options['debug']) {
            $this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.remove('.json_encode($query).')';
			return $this->queryStr;
        }

		$this->_collection = $this->_getCollectionHandle('write', $options);
		return $this->_collection->remove($query);
    }*/
	
    public function count($options=array()){
        $query  =  $this->parseWhere(isset($options['where'])?$options['where']:array());
		$this->_collection = $this->_getCollectionHandle('read', $options);
		
        if($options['debug']) {
            $this->queryStr   =  $this->_dbName.'.'.$this->_collectionName;
            $this->queryStr   .= $query?'.find('.json_encode($query).')':'';
            $this->queryStr   .= '.count()';
			echo $this->queryStr;exit();
        }
		return $this->_collection->count($query);
    }
	
    public function getTables(){
		if($options['debug']) {
            return  $this->_dbName.'.getCollenctionNames()';
        }
		
		$this->_mongo = $this->_getMongoHandle($type);
        $list   = $this->_mongo->listCollections();
        $info =  array();
        foreach ($list as $collection){
            $info[]   =  $collection->getName();
        }
        return $info;
    }
	
	public function parseWhere($where){
		/*留扩展功能*/
		return $where;
	}
	
	public function parseField($fields){
        if(empty($fields)) {
            $fields    = array();
        }
        if(is_string($fields)) {
			if($fields == '*') return array();
            $_fields    = explode(',',$fields);
            $fields     = array();
            foreach ($_fields as $f)
                $fields[$f] = true;
        }elseif(is_array($fields)) {
            $_fields    = $fields;
            $fields     = array();
            foreach ($_fields as $f=>$v) {
                if(is_numeric($f))
                    $fields[$v] = true;
                else
                    $fields[$f] = $v ? true : false;
            }
        }
        return $fields;
    }
	
	protected function parseSet($data) {
		return $data;
        $result   =  array();
        foreach ($data as $key=>$val){
            if(is_array($val)) {
                switch($val[0]) {
                    case 'inc':
                        $result['$inc'][$key]  =  (int)$val[1];
                        break;
                    case 'set':
                    case 'unset':
                    case 'push':
                    case 'pushall':
                    case 'addtoset':
                    case 'pop':
                    case 'pull':
                    case 'pullall':
                        $result['$'.$val[0]][$key] = $val[1];
                        break;
                    default:
                        $result['$set'][$key] =  $val;
                }
            }else{
                $result['$set'][$key]    = $val;
            }
        }
        return $result;
    }
	
	public function parseOrder($order){
		/*留作扩展，感觉以后用不到~*/
		return $order;
	}
	
	public function mongObjectToArray($curor){
		$arr = array();
		foreach($curor as $doc){
			$doc['_id'] = (string)$doc['_id'];
			array_push($arr, $doc);
		}
		return $arr;
	}
	
	public function __destruct() {
		if(isset($this->_connectedPool['read'])) $this->_close($this->_connectedPool['read']);
		if(isset($this->_connectedPool['write'])) $this->_close($this->_connectedPool['write']);
    }
	
	private function _close($instance){
		if ($instance instanceof Mongo) $instance->close();
	}
	
	private function logRecord($debug_backtrace, $message){
		customErrorHandel($debug_backtrace, $message);
	}
	
	private function debug($options, $type='select'){
		
	}
}
?>