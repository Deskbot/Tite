<?php
/*
 * Represents the basis for anything that comes out of the database
*/
abstract class Entity {
	protected $id;
	protected static $table;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function equals(self $obj) {
		return $this->id === $obj->getId();
	}
	
	public function get($attr) {
		if (property_exists($this, $attr)) {
			$getterName = self::attrToGetter($attr);
			
			return $this->$getterName();
			
		} else {
			throw New Exception("invalid attribute ({$attr}) supplied to " . get_class($this) . '::get()');
		}
	}
	
	public function getStandard($attr) {
		if (!isset($this->$attr)) {
			$db = Database::get();
			$query = "SELECT {$attr} FROM " . static::$table . " WHERE id=? LIMIT 1";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			
			$this->$attr = $resultArr[0];
		}
		
		return $this->$attr;
	}
	
	public function getMany(array $values) {
		$fieldList = implode(',', $values);
		
		$db = Database::get();
		$dataQuery = "SELECT ? FROM " . static::$table . " WHERE id=? LIMIT 1;";
		$result = $db->preparedQuery($dataQuery, array($fieldList, $this->id));
		$resultArr = $result->fetchArray(SQLITE3_ASSOC);
		
		$valuesList = array_combine($list, $resultArr);
		
		foreach ($valuesList as $key => $val) {
			$this->$key = $value;
		}
		
		return $valuesList;
	}
	
	public function set($attr, $value) {
		if (property_exists($this, $attr)) {
			
			$this->$attr = $value;
			
			return true;
		}
		
		return false;
	}
	
	//static methods
	
	protected static function attrToGetter($name) {
		return 'get' . strtoupper($name[0]) . substr($name, 1);
	}
}

/*
 * User instances are complete with data.
 * if a user is not logged in, their loginId is redundant
 * the static methods can be used to provide info without the need to create an instances
 * self::get() returns a User object with the right data from the datase via a given email and password
*/
class User extends Entity {
	protected $name, $email, $loginId, $loggedIn, $groups;
	protected static $table = 'Users';
	
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function getName() {
		if (!isset($this->name)) {
			$db = Database::get();
			$query = "SELECT name FROM Users WHERE id=?";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			$this->name = $resultArr[0];
		}
		
		return $this->name;
	}
	
	public function getEmail() {
		if (!isset($this->email)) {
			$db = Database::get();
			$query = "SELECT email FROM Users WHERE id=?";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			$this->email = $resultArr[0];
		}
		
		return $this->email;
	}
	
	public function isLoggedIn() {
		return $this->loggedIn;
	}
	
	public function getGroups() {
		if (!isset($this->groups)) {
			$db = Database::get();
			$query = "
				SELECT Groups.id
				FROM Groups
				INNER JOIN GroupsToUsers
				ON Groups.id = GroupsToUsers.groupId
				WHERE GroupsToUsers.userId=? AND GroupsToUsers.accepted=1
			";
			$result = $db->preparedQuery($query, array($this->id));
			
			$this->groups = array();
			while ($groupArr = $result->fetchArray(SQLITE3_NUM)) {
				$this->groups[] = new Group($groupArr[0]);
			}
		}
		
		return $this->groups;
	}
	
	public function isInGroup(Group $testGroup) {
		if (isset($this->groups)) {
			foreach ($this->groups as $group) {
				if ($group->equals($testGroup)) {
					return true;
				}
			}
			
			return false;
			
		} else {
			$db = Database::get();
			$query = "SELECT COUNT(*) FROM GroupsToUsers WHERE userId=? AND groupId=? AND accepted=1";
			$result = $db->preparedQuery($query, array($this->id, $testGroup->getId()));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			
			return $resultArr[0] === 1;
		}
	}
	
	public function findMatchingGroupsNotJoined($criteria) {
		$criteriaList = explode(' ', $criteria);
		$criteriaStr = '';
		foreach ($criteriaList as $str) {
			$strEsc = Sqlite3::escapeString($str);
			//$criteriaStr .= "G.name LIKE '%$strEsc%' AND ";
			$criteriaStr .= "name LIKE '%$strEsc%' AND ";
		}
		$criteriaStr = substr($criteriaStr, 0, strlen($criteriaStr) - 4);
		
		/*$db = Database::get();
		$query = "
			SELECT
				G.id AS id,
				G.name AS name
			FROM Groups AS G
			LEFT JOIN GroupsToUsers AS GTU
			ON G.id = GTU.groupId
			WHERE ((GTU.userId = ? AND GTU.accepted = 0) OR IFNULL(GTU.userId, NULL)) AND ($criteriaStr)
			GROUP BY G.id;
		";
		$result = $db->preparedQuery($query, array($this->id));
		
		$matchingGroups = array();
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$newGroup = new Group($resultArr['id']);
			$newGroup->setName($resultArr['name']);
			$matchingGroups[] = $newGroup;
		}
		
		*/
		
		$db = Database::get();
		$query = "SELECT groupId FROM GroupsToUsers WHERE userId=?";
		$result = $db->preparedQuery($query, array($this->id));
		
		$theseGroupIds = array();
		
		while ($resultArr = $result->fetchArray(SQLITE3_NUM)) {
			$theseGroupIds[] = $resultArr[0];
		}
		
		$allGroupIds = Group::getAllIdsWhere($criteriaStr);
		
		$matchingGroupIds = array_diff($allGroupIds, $theseGroupIds);
		
		$matchingGroups = array();
		foreach ($matchingGroupIds as $groupId) {
			$matchingGroups[] = new Group($groupId);
		}
		
		return $matchingGroups;
	}
	
	//setters
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function setLoginId($id) {
		$this->loginId = $id;
		return $this;
	}
	
	public function setLoggedIn($bool) {
		$this->loggedIn = $bool;
		return $this;
	}
	
	//static methods
	
	//returns a User object from the email and password provided by getting data from the db
	public static function getByLogin($email, $password) {
		$db = Database::get();
		$dataQuery = "SELECT id, name, passHash, salt FROM Users WHERE email=?";
		$dataResult = $db->preparedQuery($dataQuery, array($email));
		$dataResultArr = $dataResult->fetchArray(SQLITE3_ASSOC);
		
		if ($dataResultArr) {
			$id = $dataResultArr['id'];
			$name = $dataResultArr['name'];
			$correctHash = $dataResultArr['passHash'];
			$salt = $dataResultArr['salt'];
			
			$resultHash = password_hash($password, $salt);
			
			$loggedIn = $correctHash === $resultHash;
			
			if ($loggedIn) {
				$loginId = rand_hex(128);
				
				$loginIdQuery = "UPDATE Users SET loginId=? WHERE id=?";
				$db->preparedQuery($loginIdQuery, array($loginId, $id));
				
				Cookie::set('id', $id);
				Cookie::set('loginId', $loginId);
				
			} else {
				$loginId = '';
			}
			
			$user = new self($id);
			$user->setName($name);
			$user->setLoginId($loginId);
			$user->setLoggedIn($loggedIn);
			
			return $user;
		}
		
		return false;
	}
	
	public static function checkPasswordCorrect($userId, $password) {
		$db = Database::get();
		$dataQuery = "SELECT salt FROM Users WHERE id=?";
		$dataResult = $db->preparedQuery($dataQuery, array($userId));
		$dataResultArr = $dataResult->fetchArray(SQLITE3_NUM);
		
		$salt = $dataResultArr[0];
		$passHash = password_hash($password, $salt);
		
		$validationQuery = "SELECT COUNT(*) FROM Users WHERE id=? AND passHash=?";
		$result = $db->preparedQuery($validationQuery, array($userId, $passHash));
		$resultArr = $result->fetchArray(SQLITE3_NUM);
		
		return $resultArr[0] !== 0;
	}
	
	public static function checkLoggedIn() {
		if (isset($_COOKIE['id']) && isset($_COOKIE['loginId'])) {
			$db = Database::get();
			$query = "SELECT COUNT(*) FROM Users WHERE id=? AND loginId=?";
			$result = $db->preparedQuery($query, array($_COOKIE['id'], $_COOKIE['loginId']));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			
			return $resultArr[0] === 1;
		}
		
		return false;
	}
	
	public static function emailExists($email) {
		$db = Database::get();
		$result = $db->preparedQuery("SELECT COUNT(email) FROM Users WHERE email=?", array($email));
		$row = $result->fetchArray(SQLITE3_NUM);

		return $row[0] !== 0;
	}
	
	public static function emailIsInLimbo($email) {
		$db = Database::get();
		$result = $db->preparedQuery("SELECT COUNT(email) FROM RegisterLimbo WHERE email=?", array($email));
		$row = $result->fetchArray(SQLITE3_NUM);
		return $row[0] !== 0;
	}
	
	public static function isNameValid($name) {
		$pattern = '/[a-z]/i';
		
		return trim($name) !== '' && preg_match($pattern, $name);
	}
	
	public static function isPasswordValid($password) {
		return strlen($password) >= 6;
	}
}

class Group extends Entity {
	protected $name, $users, $joinRequests, $membersCount;
	protected static $table = 'Groups';
	
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function getName() {
		if (!isset($this->name)) {
			$db = Database::get();
			$dataQuery = "SELECT name FROM Groups WHERE id=?";
			$dataResult = $db->preparedQuery($dataQuery, array($this->id));
			$dataResultArr = $dataResult->fetchArray(SQLITE3_NUM);
			
			$this->name = $dataResultArr[0];
		}
		
		return $this->name;
	}
	
	public function getUsers() {
		if (!isset($this->users)) {
			$db = Database::get();
			$dataQuery = "
				SELECT
					GroupsToUsers.userId AS id,
					Users.name AS name
				FROM GroupsToUsers
				INNER JOIN Users
				ON Users.id = GroupsToUsers.userId
				WHERE GroupsToUsers.groupId=? and GroupsToUsers.accepted=1
				GROUP BY Users.id
			";
			$dataResult = $db->preparedQuery($dataQuery, array($this->id));
			
			$this->users = array();
			
			while ($userInfo = $dataResult->fetchArray(SQLITE3_ASSOC)) {
				$newUser = new User($userInfo['id']);
				$newUser->setName($userInfo['name']);
				$this->users[] = $newUser;
			}
		}
		
		return $this->users;
	}
	
	public function getBills() {
		$db = Database::get();
		$query = "SELECT id FROM Bills WHERE groupId=?";
		$result = $db->preparedQuery($query, array($this->id));
		
		$bills = array();
		while ($resultArr =  $result->fetchArray(SQLITE3_NUM)) {
			$bills[] = new Bill($resultArr[0]);
		}
		
		return $bills;
	}
	
	public function getActiveBills() {
		$db = Database::get();
		$query = "SELECT id FROM Bills WHERE groupId=? AND complete=0";
		$result = $db->preparedQuery($query, array($this->id));
		
		$bills = array();
		while ($resultArr =  $result->fetchArray(SQLITE3_NUM)) {
			$bills[] = new Bill($resultArr[0]);
		}
		
		return $bills;
	}
	
	public function getJoinRequests() {
		if (!isset($this->joinRequests)) {
			$db = Database::get();
			$query = "SELECT id, userId FROM GroupsToUsers WHERE groupId=? AND accepted=0";
			$result = $db->preparedQuery($query, array($this->id));
			
			$this->joinRequests = array();
			while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
				$this->joinRequests[$resultArr['id']] = new User($resultArr['userId']);
			}
		}
		
		return $this->joinRequests;
	}
	
	public function countMembers() {
		if (!isset($this->membersCount)) {
			$db = Database::get();
			$query = "SELECT COUNT(*) FROM GroupsToUsers WHERE groupId=?";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			
			$this->membersCount = $resultArr[0];
		}
		
		return $this->membersCount;
	}
	
	public function getUrl() {
		return URL . 'groups/view.php?groupId=' . urlencode($this->id);
	}
	
	//setters
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	//static methods
	
	public function getAllIdsWhere($cond) {
		$db = Database::get();
		$query = "SELECT id FROM Groups WHERE $cond;";
		$result = $db->preparedQuery($query, array());
		
		$list = array();
		while ($resultArr = $result->fetchArray(SQLITE3_NUM)) {
			$list[] = $resultArr[0];
		}
		
		return $list;
	}
}

class Bill extends Entity {
	protected $name, $group, $payments, $amount, $dateCreated, $deadline, $leftover;
	protected static $table = 'Bills';
	
	public function __construct($id) {
		parent::__construct($id);
	}	
	
	public function getAmount() {
		if (!isset($this->amount)) {
			$db = Database::get();
			$query = "SELECT amount FROM Bills WHERE id=?";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr  = $result->fetchArray(SQLITE3_NUM);
			
			$this->amount = $resultArr[0];
		}
		
		return $this->amount;
	}
	
	public function getGroup() {
		if (!isset($this->group)) {
			$db = Database::get();
			$query = "
				SELECT
					Bills.groupId AS groupId,
					Groups.name AS name
				FROM Bills
				INNER JOIN Groups
				ON Groups.id = Bills.groupId
				WHERE Bills.id=?
				LIMIT 1
			";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_ASSOC);
			
			$this->group = new Group($resultArr['groupId']);
			$this->group->set('name', $resultArr['name']);
		}
		
		return $this->group;
	}
	
	public function getPayments() {
		if (!isset($this->payments)) {
			$db = Database::get();
			$query = "SELECT id, userId, amountPaid FROM Payments WHERE billId=?";
			$result = $db->preparedQuery($query, array($this->id));
			
			$this->payments = array();
			while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
				$newPayment = new Payment($resultArr['id']);
				$newPayment->set('userId', $resultArr['userId']);
				$newPayment->set('amountPaid', $resultArr['amountPaid']);
				$this->payments[] = $newPayment;
			}
		}
		
		return $this->payments;
	}
	
	public function getName() {
		return $this->getStandard('name');
	}
	
	public function getDescription() {
		return $this->getStandard('description');
	}
	
	public function getDateCreated() {
		return $this->getStandard('dateCreated');
	}
	
	public function getDeadline() {
		return $this->getStandard('deadline');
	}
	
	public function getLeftover() {
		return $this->getStandard('leftover');
	}
	
	public function getUrl() {
		return URL . 'bills/view.php?billId=' . urlencode($this->id);
	}
	
	public function getUsersPaid() {
		$db = Database::get();
		$query = "
			SELECT U.id
			FROM Bills AS B
			INNER JOIN GroupsToUsers AS GTU
			ON GTU.groupId = B.groupId
			INNER JOIN Users AS U
			ON GTU.userId = U.id
			INNER JOIN Payments AS P
			ON P.userId = U.id
			WHERE billId=? AND P.amountPaid>=?
			GROUP BY U.id
		";
		$result = $db->preparedQuery($query, array($this->id, $this->getAmount()));
		
		$usersPayed = array();
		while ($resultArr = $result->fetchArray(SQLITE3_NUM)) {
			$usersPayed[] = new User($resultArr[0]);
		}
		
		return $usersPayed;
	}
}

class Payment extends Entity {
	protected $id, $user, $billId, $userId, $amountPaid;
	protected static $table = 'Payments';
	
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function getUser() {
		if (!isset($this->user)) {
			$db = Database::get();
			$query = "
				SELECT
					Users.id AS userId,
					Users.name AS name
				FROM Payments
				INNER JOIN Users
				ON Users.id = Payments.id
				WHERE Payments.id=?
			";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_ASSOC);
			
			$this->user = new User($resultArr['userId']);
			$this->user->set('name', $resultArr['name']);
		}
		
		return $this->user;
	}
	
	public function getAmountPaid() {
		if (!isset($this->amountPaid)) {
			$db = Database::get();
			$query = "SELECT amountPaid FROM Payments WHERE id=?";
			$result = $db->preparedQuery($query, array($this->id));
			$resultArr = $result->fetchArray(SQLITE3_NUM);
			
			$this->amountPaid = $resultArr[0];
		}
		
		return $this->amountPaid;
	}
	
	public function getAmountPaidAsString() {
		return self::amountToString($this->getAmountPaid());
	}
	
	public static function getByBillAndUser($billId, $userId) {
		$db = Database::get();
		$query = "SELECT id, amountPaid FROM Payments WHERE billId=? AND userId=? LIMIT 1";
		$result = $db->preparedQuery($query, array($billId, $userId));
		$resultArr = $result->fetchArray(SQLITE3_ASSOC);
		
		if ($resultArr) { //there are rows
			
			$newPayment = new self($resultArr['id']);
			$newPayment->set('amountPaid', $resultArr['amountPaid']);
			
			return $newPayment;
			
		} else {
			return null;
		}
	}
	
	public static function amountToString($amount) {
		$pounds = floor($amount / 100);
		$pence = $amount % 100;
		$pence = $pence < 10 ? '0' . $pence : $pence;
		
		return '&pound;' . $pounds . '.' . $pence;
	}
}














