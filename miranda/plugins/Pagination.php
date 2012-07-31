<?php
namespace miranda\plugins;

class Pagination
{
    private $model;
    private $page;
    private $per_page;
    private $where;
    private $where_values;
    private $order_by;
    private $sort_order;
    
    public function __construct($model, $page, $per_page = 10)
    {
        $this -> model		= $model;
        $this -> page		= (int) intval($page);
        $this -> per_page	= (int) intval($per_page);
    }
    
    public function perPage($amount)
    {
        $this -> per_page = (int) intval($amount);
	
	return $this;
    }
    
    public function where($where, $values)
    {
        $this -> where		= $where;
        $this -> where_values	= $values;
	
	return $this;
    }
    
    public function order_by($order_by, $sort_order = 'ASC')
    {
        $this -> order_by	= $order_by;
        $this -> sort_order	= strtoupper($sort_order) == 'ASC' ? 'ASC' : 'DESC';
	
	return $this;
    }
    
    public function fetch()
    {
        $model	= $this -> model;
        $limit	= ($this -> page - 1) * $this -> per_page;
        $limit	= $limit . ',' . $this -> per_page;
	
        if(isset($this -> where, $this -> where_values))
		return $model::where($this -> where, $this -> where_values, $limit, $this -> order_by, $this -> sort_order);
	else
		return $model::find($limit, $this -> order_by, $this -> sort_order);
    }
    
}
?>