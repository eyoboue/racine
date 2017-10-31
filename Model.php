<?php

namespace Racine;

abstract class Model extends \ActiveRecord\Model
{
    
    public static function paginate($queryParams = [], $paginateParams = [])
    {
        $request = Application::getInstance()->getRequest();
        $baseUri = $request->getSchemeAndHttpHost().$request->getBaseUrl();
        $baseQs = $request->query->all();
        if(isset($baseQs['page'])){
            unset($baseQs['page']);
        }
        if(isset($baseQs['per_page'])){
            unset($baseQs['per_page']);
        }
        
        $page = isset($paginateParams['page'])
            ? (int)$paginateParams['page']
            : (isset($_GET['page']) ? (int)$_GET['page'] : Pagination::DEFAULT_PAGE)
        ;
        
        $perPage = isset($paginateParams['per_page'])
            ? (int)$paginateParams['per_page']
            : (isset($_GET['per_page']) ? (int)$_GET['per_page'] : Pagination::DEFAULT_PER_PAGE)
        ;
    
        $pagination = [
            'total' => (int)self::count($queryParams),
            'per_page' => $perPage,
            'current_page' => $page,
        ];
        $pagination['last_page'] = ceil($pagination['total']/$perPage);
        
        $queryParams['offset'] = ($page-1)*$perPage;
        $queryParams['limit'] = $perPage;
    
        $pagination['from'] = $queryParams['offset']+1;
        $pagination['to'] = $queryParams['offset'] + $perPage;
        if($pagination['to'] > $pagination['total']){
            $pagination['to'] = $pagination['total'];
        }
        
        if($pagination['current_page'] <= 1){
            $pagination['prev_page_url'] = null;
        }else{
            $pagination['prev_page_url'] = $baseUri.'?'.http_build_query(array_merge_recursive($baseQs, ['page'=>$page-1]));
        }
        
        if($pagination['current_page'] >= $pagination['last_page']){
            $pagination['next_page_url'] = null;
        }else{
            $pagination['next_page_url'] = $baseUri.'?'.http_build_query(array_merge_recursive($baseQs, ['page'=>$page+1]));
        }
    
        $models = self::all($queryParams);
        /*$include = [];
        if(isset($paginateParams['include'])){
            $include = $paginateParams['include'];
        }elseif (isset($queryParams['include'])){
            $include = $queryParams['include'];
        }*/
        
        $pagination['data'] = array_map(function ($value) use ($paginateParams){
            return $value->to_array($paginateParams);
        }, $models);
    
        return new Pagination($pagination);
    }
}