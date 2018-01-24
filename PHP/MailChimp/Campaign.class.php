<?php

namespace App\Http\Lib\MailChimp;

use App\Http\Lib\MailChimp\Client;

/**
 * Campaign客户端
 */
class Campaign extends Client {

	/**
	 * 获取所以Campaign信息
	 *
	 * @param  integer $offset  当前页
	 * @param  integer $count 每页数
	 * @return mixed
	 */
	public function getAllCampaigns($arg = [], $offset = 0, $count = 10) {
		$result = $this->MailChimp->get('campaigns', array_merge($arg, compact('offset', 'count')));

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_COMPAIGN_GETALL_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
			return false;
		}

		return $result;
	}

	/**
	 * 获取所以Campaign的link信息
	 *
	 * @param  string $campaign_id campaign_id
	 * @param  string $arg 接口参数
	 * @param  integer $offset  当前页
	 * @param  integer $count 每页数
	 * @return mixed
	 */
	public function getClickDetails($campaign_id, $arg = [], $offset = 0, $count = 10) {
		$result = $this->MailChimp->get("reports/{$campaign_id}/click-details", array_merge($arg, compact('offset', 'count')));

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_COMPAIGN_GETALL_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
			return false;
		}

		return $result;
	}

	/**
	 * 获取所以Campaign的link点击详细信息
	 *
	 * @param  string $campaign_id campaign_id
	 * @param  string $link_id link_id
	 * @param  string $arg 接口参数
	 * @param  integer $offset  当前页
	 * @param  integer $count 每页数
	 * @return mixed
	 */
	public function getLinksClickDetails($campaign_id, $link_id, $arg = [], $offset = 0, $count = 10) {
		$result = $this->MailChimp->get("reports/{$campaign_id}/click-details/{$link_id}/members", array_merge($arg, compact('offset', 'count')));

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_COMPAIGN_GETALL_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
			return false;
		}

		return $result;
	}


	/**
	 * 获取所以Campaign的sent-to详细信息
	 * @param  string $campaign_id campaign_id
	 * @param  string $arg 接口参数
	 * @param  integer $offset  当前页
	 * @param  integer $count 每页数
	 * @return mixed
	 */
	public function getSentToDetails($campaign_id, $arg = [], $offset = 0, $count = 10) {
		$result = $this->MailChimp->get("reports/{$campaign_id}/sent-to", array_merge($arg, compact('offset', 'count')));

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_COMPAIGN_GETALL_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
			return false;
		}

		return $result;
	}

	/**
	 * 获取所以Campaign的email-activity详细信息
	 * @param  string $campaign_id campaign_id
	 * @param  string $arg 接口参数
	 * @param  integer $offset  当前页
	 * @param  integer $count 每页数
	 * @return mixed
	 */
	public function geEmailActivity($campaign_id, $arg = [], $offset = 0, $count = 10) {
		$result = $this->MailChimp->get("reports/{$campaign_id}/email-activity", array_merge($arg, compact('offset', 'count')));

		if (!$this->MailChimp->success()) {
			$this->writeLog('error', 'MAILCHIMP_COMPAIGN_GETALL_ERROR', $this->getLastError(), $this->getLastRequest(), $this->getLastResponse());
			return false;
		}

		return $result;
	}

}