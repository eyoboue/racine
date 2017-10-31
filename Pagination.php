<?php

namespace Racine;

class Pagination
{
    const DEFAULT_PER_PAGE = 25;
    const DEFAULT_PAGE = 1;
    
    /**
     * @var int
     */
    protected $total;
    /**
     * @var int
     */
    protected $per_page;
    /**
     * @var int
     */
    protected $current_page = 1;
    /**
     * @var int
     */
    protected $last_page;
    /**
     * @var string
     */
    protected $next_page_url = null;
    /**
     * @var string
     */
    protected $prev_page_url = null;
    /**
     * @var int
     */
    protected $from;
    /**
     * @var int
     */
    protected $to;
    
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * Pagination constructor.
     */
    public function __construct($attributes)
    {
        foreach ($attributes as $key => $value){
            if(property_exists(get_class($this), $key)){
                $this->{$key} = $value;
            }
        }
        
        if((int)$this->per_page <= 0){
            $this->per_page = self::DEFAULT_PER_PAGE;
        }
        
        if((int)$this->current_page <= 0){
            $this->current_page = self::DEFAULT_PAGE;
        }
    }
    
    public function get()
    {
        return [
            'total' => $this->total,
            'per_page' => $this->per_page,
            'current_page' => $this->current_page,
            'last_page' => $this->last_page,
            'next_page_url' => $this->next_page_url,
            'prev_page_url' => $this->prev_page_url,
            'from' => $this->from,
            'to' => $this->to,
            
            'data' => $this->getData(),
        ];
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
}