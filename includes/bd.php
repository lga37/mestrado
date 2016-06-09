<?php
if(ENV==="PRODUCTION"){
    require("../config/config.prod.php");
}else{
    require("../config/config.php");
}

$cn = new mysqli(HOST,USER,PASS,NAME);
if($cn->connect_error) {
  	echo $cn->connect_error;die;
  	#trigger_error('Cannot connect to database. ' . $cn->connect_error);
}

#no footer if($cn) $cn->close();

function getRefArray($a) { 
    if (strnatcmp(phpversion(),'5.3')>=0) { 
        $ret = array(); 
        foreach($a as $key => $val) { 
            $ret[$key] = &$a[$key]; 
        } 
        return $ret; 
    } 
    return $a; 
} 

function param_type($param){
    echo gettype($param);
    if (ctype_digit((string) $param))
        return $param <= PHP_INT_MAX ? 'i' : 's';

    if (is_numeric($param))
        return 'd';

    return 's';
}

function iQuery($sql, array $campos=array()) { 
	global $cn;
	#echo count($campos);
    #var_dump($campos);die;
	if($campos){
		$letras="";
		foreach ($campos as $key => $value) {
			$letras .= param_type($value);
		}
		array_unshift($campos, $letras);
		
	}
	#echo '<br>',$letra;
	echo $sql,'<br>Campos:';print_r($campos);
	#print_r($campos);
	#echo count($campos);
	#print_r($where);
	#die;
    $result = [];
    $stmt = $cn->stmt_init();
    if ($stmt->prepare($sql)) { 
        if(count($campos)>0){
        	#echo "oiiiiiiii";
	        $method = new ReflectionMethod('mysqli_stmt', 'bind_param'); 
	        $campos = getRefArray($campos);
	        $r1 = $method->invokeArgs($stmt, $campos);    
	        
	        if(!$r1) 
	        	die('bind_param() failed: ' . htmlspecialchars($stmt->error));
        	
        }
       
        $r2 = $stmt->execute(); 
		if(!$r2) 
			die('execute() failed: ' . $stmt->error);
        $meta = $stmt->result_metadata(); 
        if (!$meta) {            
            $result['affected_rows'] = $stmt->affected_rows; 
            $result['insert_id'] = $stmt->insert_id; 
        } else { 
            $stmt->store_result(); 
            $num_of_rows = $stmt->num_rows; #pegando o num d linhas
            $params = array(); 
            $row = array(); 

            while ($field = $meta->fetch_field()) { 
                $params[] = &$row[$field->name]; 
            } 
            $meta->close(); 
            if(!call_user_func_array([$stmt,'bind_result'], $params)){
            	echo "erro em bind_result". $cn->error ;die;
            }
            while ($stmt->fetch()) { 
                $obj = []; 
                foreach($row as $key => $val) { 
                    $obj[$key] = $val; 
                } 
                $result[] = $obj; 
            } 
            $stmt->free_result(); 
        } 
        $stmt->close(); 
	} else {
		die('prepare() failed: ' . $stmt->error);	
	}

    #return json_encode($result); 
    return $result; 
} 


function save(array $post, $tabela){
    foreach($post as $k=>$v){
        if(is_null($v) || empty($v)){
            unset($post[$k]);
        }
    }

    if(isset($post['id'])){
        $PK="id=?";
        $id=$post['id'];
        unset($post['id']);
        
        $valores=array_values($post);
        $campos=array_keys($post);
        $campos=implode("=?,",$campos);
        $campos.="=?";
        $sql = sprintf("UPDATE %s SET %s WHERE %s;",$tabela, $campos ,$PK);
        $valores[]=$id;
        #echo $sql,'<hr>';
        #print_r($valores);
        $retorno = iQuery($sql, $valores);
        $ret = $retorno['affected_rows']==1? $id : false;

    } else {
        $campos=array_keys($post);
        $campos=implode("=?,",$campos);
        $campos.="=?";
        $valores=array_values($post);
        $sql = sprintf("INSERT %s SET %s;",$tabela, $campos);
        #echo $sql,'<hr>';
        #print_r($valores);
        $retorno = iQuery($sql, $valores);
        #echo $retorno['insert_id']>1? msg('OK','com sucesso',$tipo='success'):msg('Erro','erro ou ja fez',$tipo='danger');
        $ret = $retorno['insert_id']>0? $retorno['insert_id'] : false;
        
    }
    #ele retorna o id ou false.
    return $ret;
    
}