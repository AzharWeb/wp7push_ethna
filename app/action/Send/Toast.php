<?php
/**
 *  Send/Toast.php
 *
 *  @author     {$author}
 *  @package    Mpnstest
 *  @version    $Id$
 */

require_once('wp7_push.php');

/**
 *  send_toast Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Mpnstest
 */
class Mpnstest_Form_SendToast extends Mpnstest_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'user_id' => array(
			'type'      => VAR_TYPE_INT,
			'form_type' => FORM_TYPE_SELECT,
			'name'      => 'Target User',
			'option'    => 'optionUsers',
			'required'  => true,
		),
		'title' => array(
			'type'      => VAR_TYPE_STRING,
			'form_type' => FORM_TYPE_TEXT,
			'name'      => 'Title',
			'required'  => true,
		),
		'message' => array(
			'type'      => VAR_TYPE_STRING,
			'form_type' => FORM_TYPE_TEXT,
			'name'      => 'Message',
			'required'  => true,
		),
    );

	function optionUsers() {
		$db =& $this->backend->getDb();
		
		$returns = array();
		$list = $db->getAll('select * from devices where deleted = 0');
		foreach ($list as $l) {
			$returns[$l['id']] = $l['id'];
		}
		return $returns;
	}
}

/**
 *  send_toast action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Mpnstest
 */
class Mpnstest_Action_SendToast extends Mpnstest_ActionClass
{
    /**
     *  preprocess of send_toast Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		$db =& $this->backend->getDb();

		$user_id = $this->af->get('user_id');

		if ($this->af->validate() > 0) {
			return 'send_index';
		}

		$ret = $db->getRow('select * from devices where id = ?;', array($user_id));
		if (Ethna::isError($ret)) {
			$this->ae->add(null, 'db execution failed');
			return 'send_index';
		}
		if (empty($ret)) {
			$this->ae->add('user_id', 'not registed user id');
			return 'send_index';
		}
		$this->af->setApp('user', $ret);

        return null;
    }

    /**
     *  send_toast action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user    = $this->af->getApp('user');
		$title   = $this->af->get('title');
		$message = $this->af->get('message');

		$client = new WindowsPhonePushClient($user['channel_url']);
		$ret = $client->send_toast($title, $message);

		$this->af->setApp('subscription_status',      $ret['X-SubscriptionStatus']);
		$this->af->setApp('notification_status',      $ret['X-NotificationStatus']);
		$this->af->setApp('device_connection_status', $ret['X-DeviceConnectionStatus']);
		
        return 'send_toast';
    }
}

?>
