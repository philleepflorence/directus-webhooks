<?php

/**
 * Directus – <http://getdirectus.com>
 *
 * @link      The canonical repository – <https://github.com/directus/directus>
 * @copyright Copyright 2006-2017 RANGER Studio, LLC – <http://rangerstudio.com>
 * @license   GNU General Public License (v3) – <http://www.gnu.org/copyleft/gpl.html>
 */

namespace Directus\Webhooks;

use Directus\Bootstrap;
use Directus\Database\TableGateway\RelationalTableGateway as TableGateway;
use Directus\Database\TableSchema;
use Directus\Util\ArrayUtils;

/**
 * Webhooks Emitter
 *
 * This is the Webhooks core Emitter and cURL Handler
 *
 * @author Philleep Florence <email@philleep.com>
 */
class Emitter
{
    /**
     * List of actions that match the events in the webhooks collection rows
     *
     * @var array
     */
     
    protected $actions = [];
    
    /**
     * Activate the emitter trigger during application destruction
     *
     * @var boolean
     */
     
    protected $active = false;
    
    /**
     * List of paths and methods to ignore - will not activate hooks if matched
     *
     * @var array
     */
     
    protected $exclude = [
	    [
		    "method" => "GET",
		    "path" => "/api/1.1/messages/rows"
	    ],
	    [
		    "method" => "POST",
		    "path" => "/api/1.1/auth/login"
	    ]
    ];
    
    /**
     * List of all active webhooks to keep in memory
     *
     * @var array
     */
     
    protected $items = [];
    
    /**
     * Application constructor - get all active rows from webhooks collection.
     *
     */

    public function __construct($acl, $db)
    {
	    $settings = Bootstrap::get('settings');
	    
	    $this->active = filter_var(( ArrayUtils::get($settings, "webhooks.active") ), FILTER_VALIDATE_BOOLEAN);
	    
	    $method = $_SERVER['REQUEST_METHOD'];
	    $uri = $_SERVER['REQUEST_URI'];
	    
	    if ($this->active) {
		    foreach ($this->exclude as $exclude) {
			    if ($method === $exclude['method'] && stripos($uri, $exclude['method']) >= 0) {
				    $this->active = false;
				    break;
			    }
		    }
	    }
	    
	    if ($this->active) {
		    $this->acl = $acl;
		    $this->db = $db;
	    }
    }
    
    /**
     * Application boot - activates or deactivates webhooks
     *
     */
    
    public function boot()
    {
	    $canView = false;
	    
	    if ($this->active) {
		    $canView = $this->acl->canView('app_webhooks');
	    }
	    
	    if ($canView) {
		    $tableGateway = new TableGateway("app_webhooks", $this->db, $this->acl);		    
		    $items = $tableGateway->getItems([
			    "columns" => "id,name,url,type,events,collections,secret",
			    "depth" => 0
		    ]);
		    $items = ArrayUtils::get($items, "data");
		    
		    $this->items = $items;
	    }
	    else {
		    $this->active = false;
	    }
    }
    
    /**
     * Processes and adds triggers to the actions array if applicable
     *
     * @param $name - string
     * @param $params - array
     *
     * @return void
     */
    public function run($event, $params = [])
    {
        if ($this->active) {
	        $params = is_array($params) ? $params : (array) $params;
	        
	        $collection = ArrayUtils::get($params, 0);
	        $collection = is_string($collection) ? $collection : null;
	        
	        foreach ($this->items as $item) {
		        $collections = explode(',', ArrayUtils::get($item, 'collections', ''));
		        $collections = array_filter($collections);
		        $events = explode(',', ArrayUtils::get($item, 'events', ''));
		        $isCollection = ( is_string($collection) && count($collections) && in_array($collection, $collections) ) || ( !is_string($collection) && !count($collections) );
		        $isEvent = in_array($event, $events);
		        
		        if ($isCollection && $isEvent) {
			        array_push($this->actions, [
				        "collection" => $collection,
				        "event" => $event,
				        "action" => $item
			        ]);
		        }	        
	        }        
        }
    }
    
    /**
     * Application destructor - trigger all actions cached in memory using Multi cURL.
     *
     */

    public function __destruct()
    {
        if ($this->active && count($this->actions)) {
	        # An array that will contain all of the information for each cURL request
	        
	        $requests = [];
	        
	        # Initiate a multiple cURL handle - Create a normal cURL handle for each request.
	        
	        $curl_multi_init = curl_multi_init();
	        
	        foreach ($this->actions as $key => $action) {
		        $url = ArrayUtils::get($action, "action.url");
		        
		        $fields = [
			        "collection" => ArrayUtils::get($action, "collection"),
			        "event" => ArrayUtils::get($action, "event"),
			        "secret" => ArrayUtils::get($action, "action.secret")
		        ];
		        $contentType = ArrayUtils::get($action, "action.type");
		        $headers = ["Content-Type: { $contentType }"];
		        $method = ArrayUtils::get($action, 'action.method', 'POST');
		        $query = $method === 'GET' ? http_build_query($fields) : json_encode($fields);
		        $username = ArrayUtils::get($action, 'action.username');
		        $password = ArrayUtils::get($action, 'action.password');
		        
		        $requests[$key] = [];
				$requests[$key]['url'] = $url;
				$requests[$key]['curl_handle'] = curl_init($url);
	    
			    if ($username && $password) 
			    {
				    curl_setopt($requests[$key]['curl_handle'], CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				    curl_setopt($requests[$key]['curl_handle'], CURLOPT_USERPWD, "{ $username }: { $password }");
			    }
				
				# Configure the options for this request.
				
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_HTTPHEADER, $headers);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_HEADER, TRUE);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_CUSTOMREQUEST, $method);
				
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_POSTFIELDS, $query);
				
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_TIMEOUT, 10);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($requests[$key]['curl_handle'], CURLOPT_SSL_VERIFYPEER, false);
				
				# Add our normal / single cURL handle to the cURL multi handle.
				
				curl_multi_add_handle($curl_multi_init, $requests[$key]['curl_handle']);
	        }
	        	        
	        $processing = false;
	        
			do {
				curl_multi_exec($curl_multi_init, $processing);
			} 
			while ($processing);
			
			# Loop through the requests that we executed.
			
			foreach($requests as $key => $request){
				
				# Remove the handle from the multi handle.
				
				curl_multi_remove_handle($curl_multi_init, $request['curl_handle']);
				
				# Get the response content and the HTTP status code.
				
				$http_code = curl_getinfo($request['curl_handle'], CURLINFO_HTTP_CODE);
				$requests[$key]['http_code'] = $http_code;
				
				# Close the handle.
				
				curl_close($requests[$key]['curl_handle']);
			}
			
			# Close the multi handle.
			
			curl_multi_close($curl_multi_init);
        }       
    }
}
