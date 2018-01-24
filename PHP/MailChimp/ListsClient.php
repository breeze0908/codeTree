<?php

namespace App\Http\Lib\MailChimp;

use App\Http\Lib\MailChimp\Client;

/**
 * Campaign客户端
 */
class ListsClient extends Client {
    /**
     * 向list中更新用户
     * @apiUrl {PUT} /lists/{list_id}/members/{subscriber_hash}
     *
     * @param string $list_id       mailchimp中list的ID
     * @param string $email_address 邮箱地址
     * @param arrray $merge_fields  模板参数
     */
	public function addOrUpdateListMember($list_id, $email_address, $merge_fields) {
		$args = [
			'status_if_new' => 'subscribed',
			'email_address' => $email_address,
			'merge_fields'  => $merge_fields,
		];

		$subscriber_hash = md5($email_address);
		$result          = $this->MailChimp->put("lists/{$list_id}/members/{$subscriber_hash}", $args);

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_API_PUT_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
            return false;
		}
        $this->writeLog('info', 'MAILCHIMP_API_PUT_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
		return $result;
	}
}