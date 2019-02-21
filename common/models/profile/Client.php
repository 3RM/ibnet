<?php

namespace sammaye\solr;

use Yii;
use yii\base\Component;
use Solarium\Client as SolrClient;

/**
 * This is the Solr client, representing the connection
 */
class Client extends Component
{
	/**
     * @param array $options
     */
	public $options = [];

	/**
 	 * @param object $solr
 	 */
	public $solr;


	public function init()
	{
		$this->solr = new SolrClient($this->options);
	}

	public function __call($name, $params)
	{
		if(method_exists($this->solr, $name)){
			return call_user_func_array([$this->solr, $name], $params);
		}
		parent::call($name, $params); // We do this so we don't have to implement the exceptions ourselves
	}
}