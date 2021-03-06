<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Shopware_Controllers
 * @subpackage ProductFeed
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author     Marcel Schmäing
 * @author     $Author$
 */

use Shopware\Models\ProductFeed\ProductFeed as ProductFeed,
    Doctrine\ORM\AbstractQuery;
/**
 * Shopware Backend Controller for the Voucher Module
 *
 * Backend Controller for the product feed backend module.
 * Displays all feeds in an Ext.grid.Panel and allows to delete,
 * add and edit feeds. On the detail page the feeds data are displayed and can be edited
 */
class Shopware_Controllers_Backend_ProductFeed extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Entity Manager
     * @var null
     */
    protected $manager = null;

    /**
     * @var \Shopware\Models\Article\Repository
     */
    protected $articleRepository = null;

    /**
     * @var \Shopware\Models\Shop\Repository
     */
    protected $shopRepository = null;

    /**
     * @var \Shopware\Models\ProductFeed\Repository
     */
    protected $productFeedRepository = null;

    /**
     * Helper function to get access to the productFeed repository.
     * @return \Shopware\Models\ProductFeed\Repository
     */
    private function getProductFeedRepository() {
    	if ($this->productFeedRepository === null) {
    		$this->productFeedRepository = Shopware()->Models()->getRepository('Shopware\Models\ProductFeed\ProductFeed');
    	}
    	return $this->productFeedRepository;
    }

    /**
     * Helper function to get access to the shop repository.
     * @return \Shopware\Models\Shop\Repository
     */
    private function getShopRepository() {
    	if ($this->shopRepository === null) {
    		$this->shopRepository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
    	}
    	return $this->shopRepository;
    }

    /**
     * Helper function to get access to the article repository.
     * @return \Shopware\Models\Article\Repository
     */
    private function getArticleRepository() {
    	if ($this->articleRepository === null) {
    		$this->articleRepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Article');
    	}
    	return $this->articleRepository;
    }


    /**
     * Internal helper function to get access to the entity manager.
     * @return null
     */
    private function getManager() {
        if ($this->manager === null) {
            $this->manager= Shopware()->Models();
        }
        return $this->manager;
    }

    /**
     * Registers the different acl permission for the different controller actions.
     *
     * @return void
     */
    protected function initAcl()
    {
        /**
         * permission to list all feeds
         */
        $this->addAclPermission('getFeedsAction', 'read','Insufficient Permissions');

        /**
         * permission to show detail information of a feed
         */
        $this->addAclPermission('getDetailFeedAction', 'read','Insufficient Permissions');

        /**
         * permission to delete the feed
         */
        $this->addAclPermission('deleteFeedAction', 'delete','Insufficient Permissions');
    }


    /**
     * returns a JSON string to the view containing all Product Feeds
     *
     * @return void
     */
    public function getFeedsAction()
    {
        try {
            /** @var $repository \Shopware\Models\ProductFeed\Repository */
            $repository = Shopware()->Models()->ProductFeed();
            $dataQuery = $repository->getListQuery(
                $this->Request()->getParam('sort', array()),
                $this->Request()->getParam('start'),
                $this->Request()->getParam('limit')
            );

            $totalCount = Shopware()->Models()->getQueryCount($dataQuery);
            $feeds = $dataQuery->getArrayResult();

            $this->View()->assign(array('success' => true, 'data' => $feeds, 'totalCount' => $totalCount));
        }
        catch (Exception $e) {
            $this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
        }
    }

    /**
     * returns a JSON string to the view containing the detail information of an Product Feed
     *
     * @return void
     */
    public function getDetailFeedAction()
    {
        $feedId = intval($this->Request()->feedId);
        $feed = $this->getFeed($feedId);
        $this->View()->assign(array('success' => true, 'data' => $feed));
    }

    /**
     * Returns an array with feed data for the passed feed id.
     * @param $id
     * @return mixed
     */
    private function getFeed($id) {
        /** @var $repository \Shopware\Models\ProductFeed\Repository */
        $repository = Shopware()->Models()->ProductFeed();
        $dataQuery = $repository->getDetailQuery($id);
        $feed = $dataQuery->getArrayResult();
        return $feed[0];
    }

    /**
     * returns a JSON string to the view containing the supplier information of an Product Feed
     *
     * @return void
     */
    public function getSuppliersAction()
    {
        $filter = $this->Request()->filter;
        $usedIds = $this->Request()->usedIds;

        $offset = $this->Request()->getParam('start', null);
        $limit = $this->Request()->getParam('limit', 20);

        $dataQuery = $this->getArticleRepository()
                          ->getSuppliersWithExcludedIdsQuery($usedIds, $filter, $offset, $limit);
        $total = Shopware()->Models()->getQueryCount($dataQuery);

        $data = $dataQuery->getArrayResult();

        //return the data and total count
        $this->View()->assign(array('success' => true, 'data' => $data, 'total' => $total));
    }

    /**
     * returns a JSON string to the view containing the shops information of an Product Feed we can't use the base
     * store because we need all shops and children
     *
     * @return void
     */
    public function getShopsAction()
    {
        $shopQuery = $this->getShopRepository()->getBaseListQuery();
        $data = $shopQuery->getArrayResult();

        //return the data and total count
        $this->View()->assign(array('success' => true, 'data' => $data, 'total' => count($data)));
    }


    /**
     * returns a JSON string to the view containing the article information of an Product Feed
     *
     * @return void
     */
    public function getArticlesAction()
    {
        $filter = $this->Request()->filter;
        $usedIds = $this->Request()->usedIds;

        $offset = $this->Request()->getParam('start', null);
        $limit = $this->Request()->getParam('limit', 20);

        $dataQuery = $this->getArticleRepository()
                          ->getArticlesWithExcludedIdsQuery($usedIds, $filter, $offset, $limit);
        $total = Shopware()->Models()->getQueryCount($dataQuery);
        $data = $dataQuery->getArrayResult();

        //return the data and total count
        $this->View()->assign(array('success' => true, 'data' => $data, 'total' => $total));
    }

    /**
     * Creates or updates a new Product Feed
     *
     * @return void
     */
    public function saveFeedAction()
    {
        $params = $this->Request()->getParams();

        $feedId = $params["feedId"];
        if(!empty($feedId)){
            //edit Product Feed
            $productFeed = Shopware()->Models()->ProductFeed()->find($feedId);
            //clear all previous associations
            $productFeed->getCategories()->clear();
            $productFeed->getSuppliers()->clear();
            $productFeed->getArticles()->clear();
        }
        else{
            //new Product Feed
            $productFeed = new ProductFeed();
            //to set this value initial
            $productFeed->setLastExport("now");
        }

        if (empty($params['shopId'])) {
            $params['shopId'] = null;
        }
        if (empty($params['categoryId'])) {
            $params['categoryId'] = null;
        }
        if (empty($params['customerGroupId'])) {
            $params['customerGroupId'] = null;
        }
        if (empty($params['languageId'])) {
            $params['languageId'] = null;
        }

        //save data of the category tree
        $params['categories'] = $this->prepareAssociationDataForSaving('categories','Shopware\Models\Category\Category',$params);

        //save data of the supplier filter
        $params['suppliers'] = $this->prepareAssociationDataForSaving('suppliers','Shopware\Models\Article\Supplier',$params);

        //save data of the article filter
        $params['articles'] = $this->prepareAssociationDataForSaving('articles','Shopware\Models\Article\Article',$params);

        $params['attribute'] = $params['attribute'][0];
        $productFeed->fromArray($params);

        //just for future use
        $productFeed->setExpiry(date("d-m-Y", time()));
        $productFeed->setLastChange(date("d-m-Y", time()));

        try {
            Shopware()->Models()->persist($productFeed);
            Shopware()->Models()->flush();

            $data = $this->getFeed($productFeed->getId());
            $this->View()->assign(array('success' => true, 'data' => $data));
        }
        catch (Exception $e) {
            $this->View()->assign(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Deletes a Feed from the database
     *
     * @return void
     */
    public function deleteFeedAction()
    {
        try {
            /**@var $model \Shopware\Models\ProductFeed\ProductFeed*/
            $model = Shopware()->Models()->ProductFeed()->find($this->Request()->id);
            Shopware()->Models()->remove($model);
            Shopware()->Models()->flush();
            $this->View()->assign(array('success' => true, 'data' => $this->Request()->getParams()));
        }
        catch (Exception $e) {
            $this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
        }
    }

    /**
     * helper method to prepare the association request data to save it directly
     * into the model via fromArray
     *
     * @param $paramString
     * @param $modelName
     * @param $params
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function prepareAssociationDataForSaving($paramString, $modelName, $params){
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        if (!empty($params[$paramString])) {
            foreach($params[$paramString] as $param ) {
                $model = Shopware()->Models()->find($modelName, $param['id']);
                $collection->add($model);
            }
        }
        return $collection;
    }

}