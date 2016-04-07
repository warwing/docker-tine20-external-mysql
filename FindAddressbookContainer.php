<?php
require_once 'bootstrap.php';

class AuthenticationHelper {

	private $loginName;
	private $request;
	
	public function __construct() {
		$this->request = new \Zend\Http\PhpEnvironment\Request ();
	}
	
	/**
	 * fetch auch from PHP_AUTH*
	 *
	 * @param \Zend\Http\PhpEnvironment\Request $request
	 * @return array
	 */
	private function _getPHPAuthData(\Zend\Http\PhpEnvironment\Request $request) {
		if ($request->getServer ( 'PHP_AUTH_USER' )) {
			return array (
					$request->getServer ( 'PHP_AUTH_USER' ),
					$request->getServer ( 'PHP_AUTH_PW' )
			);
		}
	}
	
	/**
	 * fetch basic auth credentials
	 *
	 * @param \Zend\Http\PhpEnvironment\Request $request
	 * @return array
	 */
	private function _getBasicAuthData(\Zend\Http\PhpEnvironment\Request $request) {
		if ($header = $request->getHeaders ( 'Authorization' )) {
			return explode ( ":", base64_decode ( substr ( $header->getFieldValue (), 6 ) ), 2 );
		} elseif ($header = $request->getServer ( 'HTTP_AUTHORIZATION' )) {
			return explode ( ":", base64_decode ( substr ( $header, 6 ) ), 2 );
		} else {
			// check if (REDIRECT_)*REMOTE_USER is found in SERVER vars
			$name = 'REMOTE_USER';
	
			for($i = 0; $i < 5; $i ++) {
				if ($header = $request->getServer ( $name )) {
					return explode ( ":", base64_decode ( substr ( $header, 6 ) ), 2 );
				}
					
				$name = 'REDIRECT_' . $name;
			}
		}
	}
	
	/**
	 * read auth data from all available sources
	 *
	 * @param \Zend\Http\PhpEnvironment\Request $request
	 * @throws Tinebase_Exception_NotFound
	 * @return array
	 */
	private function _getAuthData(\Zend\Http\PhpEnvironment\Request $request) {
		if ($authData = $this->_getPHPAuthData ( $request )) {
			return $authData;
		}
	
		if ($authData = $this->_getBasicAuthData ( $request )) {
			return $authData;
		}
	
		throw new Tinebase_Exception_NotFound ( 'No auth data found' );
	}
	
	public function checkAuthentication() {
		
		try {
			list ( $loginName, $password ) = $this->_getAuthData ( $this->request );
		} catch ( Tinebase_Exception_NotFound $tenf ) {
			header ( 'WWW-Authenticate: Basic realm="WebDAV for Tine 2.0"' );
			header ( 'HTTP/1.1 401 Unauthorized' );
			
			return false;
		}
	
		Tinebase_Core::initFramework ();
	
		if (Tinebase_Controller::getInstance ()->login ( $loginName, $password, $this->request, 'WebDAV' ) !== true) {
			header ( 'WWW-Authenticate: Basic realm="WebDAV for Tine 2.0"' );
			header ( 'HTTP/1.1 401 Unauthorized' );
			
			return false;
		}
		
		$this->loginName = $loginName;
		return true;
	}
	
	public function getRequest() {
		return $this->request;
	}
	
	public function getLoginName() {
		return $this->loginName;
	}
}

$configData = @include ('config.inc.php');
if ($configData === false) {
	echo 'UNKNOWN STATUS / CONFIG FILE NOT FOUND (include path: ' . get_include_path () . ")\n";
	exit ( 3 );
}

$authHelper = new AuthenticationHelper();

// Don't proceed - otherwise authentication doesn't work..
if ( $authHelper->checkAuthentication() !== true ) {
	exit(0);
}

$config = new Zend_Config ( $configData );

$dbConfig = $config->get ( 'database' );

$db = Zend_Db::factory ( 'Pdo_Mysql', array (
		'host' => $dbConfig->get ( 'host' ),
		'username' => $dbConfig->get ( 'username' ),
		'password' => $dbConfig->get ( 'password' ),
		'dbname' => $dbConfig->get ( 'dbname' ) 
) );

$db->setFetchMode ( Zend_Db::FETCH_OBJ );

$sql = "SELECT 
	      adr.id userAdrId, con.id container_id
		 FROM tine20_accounts acc
		 JOIN tine20_container_acl acl 
		   ON acc.id = acl.account_id
 		  AND acl.account_grant = 'syncGrant'
		 JOIN tine20_container con 
		   ON acl.container_id = con.id
		  AND con.model = 'Addressbook_Model_Contact'
		 join tine20_addressbook adr 
		   on acc.email = adr.email 
		  and adr.type = 'user'
		WHERE login_name = ?";
$result = $db->fetchAll ( $sql, $authHelper->getLoginName() );

$adrId = $result [0]->userAdrId;
$containerId = $result [0]->container_id;

header ( "HTTP/1.1 301 Moved Permanently" );
header ( "Location: https://tine.gordt.net/addressbooks/" . $adrId . "/" . $containerId );

?>
