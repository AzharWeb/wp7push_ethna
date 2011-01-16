<?php
/**
 *  Api/Regist.php
 *
 *  @author     {$author}
 *  @package    Mpnstest
 *  @version    $Id$
 */

/**
 *  api_regist Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Mpnstest
 */
class Mpnstest_Form_ApiRegist extends Mpnstest_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'channel' => array(
			'type'      => VAR_TYPE_STRING,
			'form_type' => FORM_TYPE_TEXT,
			'name'      => 'Channel URL',
			'required'  => true,
		),
		'user_id' => array(
			'type'      => VAR_TYPE_STRING,
			'form_type' => FORM_TYPE_TEXT,
			'name'      => 'User ID',
		),
    );
}

/**
 *  api_regist action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Mpnstest
 */
class Mpnstest_Action_ApiRegist extends Mpnstest_ActionClass
{
    /**
     *  preprocess of api_regist Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0) {
			$this->af->setApp('return_code', 'invalid parameter');
			return 'api_regist';
		}
        return null;
    }

    /**
     *  api_regist action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$db =& $this->backend->getDb();

		$channel = $this->af->get('channel');
		$user_id = $this->af->get('user_id');

		$db->begin();

		if (empty($user_id)) {
			$ret = $db->query('insert into devices (channel_url, created) values (?, NOW());', array($channel));
			if (Ethna::isError($ret)) {
				$db->rollback();
				$this->af->setApp('return_code', 'failed');
				return 'api_regist';
			}

			// ADOdbの機能を直接呼び出す
			$user_id = $db->db->Insert_ID();
		}
		else {
			$ret = $db->getRow('select id from devices where id = ?;', array($user_id));
			if (empty($ret)) {
				$db->rollback();
				$this->af->setApp('return_code', 'not registed user id');
				return 'api_regist';
			}

			$ret = $db->query('update devices set channel_url = ? where id = ?;', array($channel, $user_id));
			if (Ethna::isError($ret)) {
				$db->rollback();
				$this->af->setApp('return_code', 'failed');
				return 'api_regist';
			}
		}

		$ret = $db->commit();
		if (Ethna::isError($ret)) {
			$db->rollback();
			$this->af->setApp('return_code', 'failed');
			return 'api_regist';
		}

		$this->af->setApp('return_code', 'success');
		$this->af->setApp('user_id', $user_id);
		return 'api_regist';
    }
}

?>
