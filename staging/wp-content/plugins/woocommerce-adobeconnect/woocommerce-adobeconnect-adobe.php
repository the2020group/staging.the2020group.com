<?php
class AdobeConnectClient_ {

	protected $username;
	protected $password;
	protected $base;
	protected $folder;

	/**
	 * @var string filepath to cookie-jar file
	 */
	private $cookie;

	/**
	 * @var resource
	 */
	private $curl;

	/**
	 * @var bool
	 */
	private $is_authorized = false;

	/**
	 *
	 */
	public function __construct ($username, $password, $base, $folder) {
		$this->cookie = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cookie_'.time().'.txt';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_REFERER, BASE_DOMAIN);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//this is needed to connect to https servers - http://stackoverflow.com/a/16909594/1021634
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."\certs\cacert.pem");

		$this->curl = $ch;
		$this->makeAuth();
	}

	/* make auth-request with stored username and password @return AdobeConnectClient */
	public function makeAuth() {
		$this->makeRequest('login',
			array(
				'login'    => USERNAME,
				'password' => PASSWORD
			)
		);
		$this->is_authorized = true;
		return $this;
	}

	/**
	 * get common info about current user
	 *
	 * @return array
	 */
	public function getCommonInfo() {
		return $this->makeRequest('common-info');
	}

	/**
	 * create user
	 *
	 * @param string $email
	 * @param string $password
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $type
	 *
	 * @return array
	 */
	public function createUser ($email, $password, $first_name, $last_name, $type = 'guest', $prefix = null, $notification = null) {



		$result = $this->makeRequest('principal-update',
			array(
				'first-name'   => $first_name,
				'last-name'    => $last_name,
				'email'        => $email,
				'password'     => $password,

				'type'         => $type,
				'login'		   => ($prefix ? $prefix.strtolower($first_name[0].$last_name) : strtolower($first_name[0].$last_name)),
				'send-email'   => ($notification ? 'true' : 'false'),
				'has-children' => 0
			)
		);

		return $result;
	}


	public function addDetails($user_id,$field,$value) {



		$result = $this->makeRequest('acl-field-update',
			array('acl-id' => $user_id,
				  'field-id'     => $field,
				  'value'		=>$value

				)
			);

		return $result;
	}

	/**
	 * @param string $email
	 * @param bool   $only_id
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 *
	 */
	public function getUserByEmail($email, $only_id = false) {
		$result = $this->makeRequest('report-bulk-users',
			array(
				'filter-email' => $email
			)
		);

		if (empty($result['report-bulk-users'])) {	// Removed if (empty($result['principal-list'])) {
			//throw new Exception('Cannot find user');
		}
		if ($only_id) {
			return $result['report-bulk-users']['row']['@attributes']['principal-id'];
		}
		return $result;
	}

	/**
	 * update user fields
	 *
	 * @param string $email
	 * @param array  $data
	 *
	 * @return mixed
	 */
	public function updateUser($email, array $data = array()) {
		$principal_id = $this->getUserByEmail($email, true);
		$data['principal-id'] = $principal_id;
		return $this->makeRequest('principal-update', $data);
	}

	/**
	 * get all users list
	 *
	 * @return array
	 */
	public function getUsersList() {
		$users = $this->makeRequest('principal-list');
		$result = array();
		foreach($users['principal-list']['principal'] as $key => $value) {
			$result[$key] = $value['@attributes'] + $value;
		};
		unset($result[$key]['@attributes']);
		return $result;
	}

	/**
	 * get all meetings
	 *
	 * @return array
	 */public function getAllMeetings() {
		return $this->makeRequest('report-my-meetings');
	}

	public function getAllAvailableMeetings() {
		$meetings = $this->makeRequest('report-bulk-objects',
			array(
				'filter-type' => ''
			)
		);

		$result = array();

		if(!$meetings){
			echo 'Please disable the Enhanced Security feature in Adobe Connect (Administration -> Account ->More Settings). This list of available meetings will not show up otherwise';
			return;
		}

		foreach($meetings['report-bulk-objects']['row'] as $key => $value) {
			$result[$key] = $value['@attributes'] + $value;
		}
		unset($result[$key]['@attributes']);
		return $result;
	}

	public function getAllContent() {


		$meetings = $this->makeRequest('sco-contents',
			array(
				'sco-id' => '1337485551'
			)
		);


		$result = array();

		if(!$meetings){
			echo 'Please disable the Enhanced Security feature in Adobe Connect (Administration -> Account ->More Settings). This list of available meetings will not show up otherwise';
			return;
		}

		foreach($meetings['scos']['sco'] as $key => $value) {
			$result[$key] = $value['@attributes'] + $value;
		}

		return $result;
	}
	/**
	 * create meeting-folder
	 *
	 * @param string $name
	 * @param string $url
	 *
	 * @return array
	 */
	public function createFolder($name, $url) {
		$result = $this->makeRequest('sco-update',
			array(
				'type'       => 'folder',
				'name'       => $name,
				'folder-id'  => FOLDER_ID,
				'depth'      => 1,
				'url-path'   => $url
			)
		);
		return $result['sco']['@attributes']['sco-id'];
	}

	/**
	 * create meeting
	 *
	 * @param int    $folder_id
	 * @param string $name
	 * @param string $date_begin
	 * @param string $date_end
	 * @param string $url
	 *
	 * @return array
	 */
	public function createMeeting($folder_id, $name, $date_begin, $date_end, $url) {
		$result = $this->makeRequest('sco-update',
			array(
				'type'       => 'meeting',
				'name'       => $name,
				'folder-id'  => $folder_id,
				'date-begin' => $date_begin,
				'date-end'   => $date_end,
				'url-path'   => $url
			)
		);
		return $result['sco']['@attributes']['sco-id'];
	}

	/**
	 * invite user to meeting
	 *
	 * @param int    $meeting_id
	 * @param string $email
	 *
	 * @return mixed
	 */
	public function inviteUserToMeeting($meeting_id, $email) {


		$user_id = $this->getUserByEmail($email, true);

		$result = $this->makeRequest('permissions-update',
			array(
				'principal-id'  => $user_id,
				'acl-id'        => $meeting_id,
				'permission-id' => 'view'
			)
		);

		return $result;
	}

  public function eventRegister($args, $company, $postcode) {

    $event = $this->makeRequest('event-registration-details',array('sco-id' => $args['sco-id']));

    var_dump($event);

    $extra_args = array();
    foreach($event['event-fields']['field'] as $field) {
      if(isset($field['description'])) {
        switch ($field['description']) {
          case 'Company Name':
            $extra_args[] = array($field['@attributes']['interaction-id'] => 'interaction-id');
            $extra_args[] = array($company => 'response');
            break;
          case 'Post Code':
            $extra_args[] = array($field['@attributes']['interaction-id'] => 'interaction-id');
            $extra_args[] = array($postcode => 'response');
            break;
          default:
            break;
        }
      }
    }

    foreach ($extra_args as $value) {
      $flipped_args[key($value)] = current($value);
    }

    //error_log(print_r($args,1));
    $result = $this->makeRequest('event-register', $args, $flipped_args);
    return $result;
  }

	public function __destruct() {
		@curl_close($this->curl);
	}

	/**
	 * @param       $action
	 * @param array $params
	 * @return mixed
	 * @throws Exception
	 */
	 private function makeRequest($action, array $params = array(), $flipped_params = array()) {
		$url = BASE_DOMAIN;
		$url .= '/api/xml?action='.$action;
		$url .= '&'.http_build_query($params);

	    /*foreach ($flipped_params as $key => $value) {
    	  $url .= '&'.urlencode($value).'='.urlencode($key);
    	}*/
    	error_log('');
    	error_log('');
    	error_log('');
    	error_log('');
    	error_log('');
		error_log('Request: '.$url);
    	error_log('');
    	error_log('');
    	error_log('');
    	error_log('');
    	error_log('');

		curl_setopt($this->curl, CURLOPT_URL, $url);
		$result = curl_exec($this->curl);
		$xml = simplexml_load_string($result);
		$json = json_encode($xml);
		$data = json_decode($json, TRUE); // nice hack!
		if (!isset($data['status']['@attributes']['code']) || $data['status']['@attributes']['code'] !== 'ok') {
			//throw new Exception('Coulnd\'t perform the action: '.$action);
		}
		return $data;
	}
}
