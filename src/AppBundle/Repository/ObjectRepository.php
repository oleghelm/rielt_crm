<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Object;
use AppBundle\Entity\Favourite;
use Doctrine\ORM\EntityRepository;

class ObjectRepository extends EntityRepository
{
    public function getObjectsForCall($user){
        $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
        $queryBuilder->orderBy('ob.lastUpdate', 'asc');
        $queryBuilder->andWhere('ob.user = :user')->setParameter('user', $user);
        $date = new \DateTime('now -14 days');
        $queryBuilder->andWhere('ob.lastUpdate <= :lastUpdate')->setParameter('lastUpdate', $date);
        $queryBuilder->andWhere('ob.status NOT IN (:status)')->setParameter('status', ['archive','saled']);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    
    public function getImportant(){
        $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
//        $queryBuilder->orderBy('ob.lastUpdate', 'asc');
//        $queryBuilder->andWhere('ob.user = :user')->setParameter('user', $user);
//        $date = new \DateTime('now -14 days');
//        $queryBuilder->andWhere('ob.lastUpdate <= :lastUpdate')->setParameter('lastUpdate', $date);
        $queryBuilder->andWhere('ob.status NOT IN (:status)')->setParameter('status', ['saled','archive']);
        $queryBuilder->andWhere('(ob.important = :important OR ob.exclusive = :important)')->setParameter('important', true);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    
    public function getExportParams($object){
        $queryBuilder = $this->_em->getRepository('AppBundle:ObjectParam')->createQueryBuilder('obp');
        $queryBuilder->andWhere('obp.object = :object')->setParameter('object',$object);
        $queryBuilder->leftJoin('obp.param', 'param');
        $queryBuilder->andWhere('param.useInExport = :useInExport')->setParameter('useInExport',true);
//        $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
//        $queryBuilder->leftJoin('ob.params', 'object_param');
//        $queryBuilder->leftJoin('object_param.param', 'parametr');
//        $queryBuilder->andWhere('parametr.useInExport = :useInExport')->setParameter('useInExport',true);
//        $queryBuilder->andWhere('ob = :object')->setParameter('object',$object);
        
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    
    public function getFilteredObjects($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
        $queryBuilder->leftJoin('ob.location', 'location');
        $queryBuilder->leftJoin('ob.company', 'company');
        $queryBuilder->leftJoin('ob.client', 'client');       
        if($filter && is_array($filter)){
            if(isset($filter['name']) && $filter['name']['val'] !=""){
                $queryBuilder->andWhere('ob.name LIKE :obname')
                        ->setParameter('obname','%'.$filter['name']['val'].'%');
            }
            if(isset($filter['user'])){
                if(is_array($filter['user']['val']))
                    $queryBuilder->andWhere('cl.user IN '.'('.implode(',',$filter['user']['val']).')');
                elseif($filter['user']['val']!="")
                    $queryBuilder->andWhere('ob.user = :user')
                            ->setParameter('user', $filter['user']['val']);
            }
            if(isset($filter['location'])){
                if(is_array($filter['location']['val']))
                    $queryBuilder->andWhere('location.id IN '.'('.implode(',',$filter['location']['val']).')');
                elseif($filter['location']['val']!="")
                    $queryBuilder->andWhere('location = :location')
                            ->setParameter('location', $filter['location']['val']);
            }
            if(isset($filter['company'])){
                if(is_array($filter['company']['val']))
                    $queryBuilder->andWhere('company.id IN '.'('.implode(',',$filter['company']['val']).')');
                elseif($filter['company']['val']!="")
                    $queryBuilder->andWhere('company = :company')
                            ->setParameter('company', $filter['company']['val']);
            }
            if(isset($filter['client'])){
                if(is_array($filter['client']['val']))
                    $queryBuilder->andWhere('ob.client IN '.'('.implode(',',$filter['client']['val']).')');
                elseif($filter['client']['val']!="")
                    $queryBuilder->andWhere('ob.client = :client')
                            ->setParameter('client', $filter['client']['val']);
            }
            if(isset($filter['clientstr'])){
                if($filter['clientstr']['val']!="")
                    $queryBuilder->andWhere('(client.name LIKE :clientstr OR client.phones LIKE :clientstr)')
                            ->setParameter('clientstr', '%'.$filter['clientstr']['val'].'%');
            }
            if(isset($filter['code'])){
                if($filter['code']['val']!="")
                    $queryBuilder->andWhere('(ob.name LIKE :code OR ob.code LIKE :code)')
                            ->setParameter('code', '%'.$filter['code']['val'].'%');
            }
            if(isset($filter['id'])){
                if($filter['id']['val']!="")
                    $queryBuilder->andWhere('(ob.id = :id)')
                            ->setParameter('id', $filter['id']['val']);
            }
            
            if(isset($filter['status'])){
                if(is_array($filter['status']['val']))
                    $queryBuilder->andWhere('ob.status IN '.'(:status)')
                        ->setParameter('status', $filter['status']['val']);
                elseif($filter['status']['val']!="")
                    if($filter['status']['val']=='all'){}
//                        $queryBuilder->andWhere('ob.status = :status')
//                                ->setParameter('status', $filter['status']['val']);
                    else
                    $queryBuilder->andWhere('ob.status = :status')
                            ->setParameter('status', $filter['status']['val']);
            } else {
                    $queryBuilder->andWhere('ob.status != :status')
                            ->setParameter('status', 'archive');
            }
            
            if(isset($filter['type'])){
                if(is_array($filter['type']['val']))
                    $queryBuilder->andWhere('ob.type IN '.'(:type)')
                        ->setParameter('type', $filter['type']['val']);
                elseif($filter['type']['val']!="")
                    $queryBuilder->andWhere('ob.type = :type')
                            ->setParameter('type', $filter['type']['val']);
            }
            if(isset($filter['domria'])){
                    $queryBuilder->andWhere('ob.domria = :domria')
                            ->setParameter('domria', $filter['domria']['val']);
            }
            
            if(isset($filter['lastUpdateStart']) && $filter['lastUpdateStart']['val']!=""){
                $queryBuilder->andWhere('ob.lastUpdate >= '.'(:lastUpdateStart)')
                    ->setParameter('lastUpdateStart', $filter['lastUpdateStart']['val']);
            }
            if(isset($filter['lastUpdateEnd']) && $filter['lastUpdateEnd']['val']!=""){
                $queryBuilder->andWhere('ob.lastUpdate <= '.'(:lastUpdateEnd)')
                    ->setParameter('lastUpdateEnd', $filter['lastUpdateEnd']['val']);
            }
            
            $price_type = '';
            if(isset($filter['price_type']) && $filter['price_type']['val']!="full"){$price_type='_'.$filter['price_type']['val'];}
            $currency = '';
            if(isset($filter['currency']) && $filter['currency']['val']!="dol"){$currency='_'.$filter['currency']['val'];}
            
            if(isset($filter['min_price'])){
                $queryBuilder->andWhere('ob.price'.$price_type.$currency.' >= :min_price')
                        ->setParameter('min_price', $filter['min_price']['val']);
            }
            if(isset($filter['max_price'])){
                $queryBuilder->andWhere('ob.price'.$price_type.$currency.' <= :max_price')
                        ->setParameter('max_price', $filter['max_price']['val']);
            }
            if(isset($filter['rooms'])){
                if(is_array($filter['rooms']['val']))
                    $queryBuilder->andWhere('ob.rooms IN (:rooms)')
                        ->setParameter('rooms', $filter['rooms']['val']);
                else
                    $queryBuilder->andWhere('ob.rooms = :rooms')
                        ->setParameter('rooms', $filter['rooms']['val']);
            }
            if(isset($filter['special'])){
                if(in_array('exclusive', $filter['special']['val']))
                    $queryBuilder->andWhere('ob.exclusive = true');
                if(in_array('important', $filter['special']['val']))
                    $queryBuilder->andWhere('ob.important = true');
                if(in_array('advertising', $filter['special']['val']))
                    $queryBuilder->andWhere('ob.advertising = true');
                if(in_array('domria', $filter['special']['val']))
                    $queryBuilder->andWhere('ob.domria = true');
            }
            foreach($filter as $key=>$param){
                if(is_numeric($key)){// || (isset($param['type']) && strpos($param['type'],'diapazon')!==false)
                    switch ($param['type']){
                        case 'select':
                            $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                            $queryBuilder->andWhere('object_param'.$key.'.property IN (:param'.$key.')')
                                        ->setParameter('param'.$key, $param['val']);
                            break;
                        case 'text':
                            $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                            $queryBuilder->andWhere('object_param'.$key.'.string LIKE :param'.$key)
                                        ->setParameter('param'.$key, '%'.$param['val'].'%');
                            break;
                        case 'integer':
                            $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                            $queryBuilder->andWhere('object_param'.$key.'.number = :param'.$key)
                                        ->setParameter('param'.$key, $param['val']);
                            break;
                        case 'float':
                            $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                            $queryBuilder->andWhere('object_param'.$key.'.floatnumber = :param'.$key)
                                        ->setParameter('param'.$key, $param['val']);
                            break;
                        case 'diapazon':
                            if(isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.number <= :param'.$key.'_max AND object_param'.$key.'.number >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_max', $param['val']['max'])
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(isset($param['val']['min']) && !isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.number >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(!isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.number <= :param'.$key.'_max')
                                            ->setParameter('param'.$key.'_max', $param['val']['max']);
                            }
                            break;
                        case 'floatdiapazon':
                            if(isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.floatnumber <= :param'.$key.'_max AND object_param'.$key.'.floatnumber >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_max', $param['val']['max'])
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(isset($param['val']['min']) && !isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.floatnumber >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(!isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('ob.params', 'object_param'.$key, 'WITH', 'object_param'.$key.'.param = '.$key);
                                $queryBuilder->andWhere('object_param'.$key.'.floatnumber <= :param'.$key.'_max')
                                            ->setParameter('param'.$key.'_max', $param['val']['max']);
                            }
                            break;
                            
                    }
                }
            }
            
        } else {
            $queryBuilder->andWhere('ob.status != :status1')
                            ->setParameter('status1', 'archive');
            $queryBuilder->andWhere('ob.status != :status2')
                            ->setParameter('status2', 'saled');
        }
//        dump($filter);
        //favourite
        if(isset($filter['favourite'])){
//            $queryBuilder->leftJoin('ob.id', 'favourite');
            $queryBuilder->leftJoin('AppBundle:Favourite', 'favourite', 'WITH', 'favourite.object=ob');
            $queryBuilder->andWhere('favourite.user = :fuser')->setParameter('fuser', $filter['favourite']);
        }
        
        $queryBuilder->addOrderBy('ob.id','desc');
        
        $query = $queryBuilder->getQuery();
//        dump($queryBuilder);
        return $query;
    }
    
    public function findAllObjects()
    {
        return $this->createQueryBuilder('object')
            ->orderBy('object.name', 'ASC');
    }

    /*
     * @return ArrayCollection|Object[]
     */
    public function getStatistcByCompanies(){
            $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
    //        $queryBuilder->orderBy('ob.lastUpdate', 'asc');
    //        $queryBuilder->andWhere('ob.user = :user')->setParameter('user', $user);
    //        $date = new \DateTime('now -14 days');
    //        $queryBuilder->andWhere('ob.lastUpdate <= :lastUpdate')->setParameter('lastUpdate', $date);
//            $queryBuilder->andWhere('ob.status NOT IN (:status)')->setParameter('status', ['saled','archive']);
//            $queryBuilder->andWhere('(ob.important = :important OR ob.exclusive = :important)')->setParameter('important', true);
            $query = $queryBuilder->getQuery()->execute();
            return $query;
        }
        
        public function changeUserInObjects($user, $ids){
            $queryBuilder = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('ob');
            $queryBuilder->update()
                    ->set('ob.user',':user')
                    ->setParameter('user', $user)
                    ->where('ob.id IN (:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()->execute()
                    ;
        }
}