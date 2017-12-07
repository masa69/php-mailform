<?php

class MailTemplates
{
	private $template = null;


	function __construct(array $params = null) {
		if (!is_array($params)) {
			return;
		}
		if (isset($params['template'])) {
			$this->template = $params['template'];
		}
	}


	/**
	 * メール文面を作る
	 */
	public function createBody(array $posts) {

		if (empty($this->template)) {
			throw new Exception('MailTemplates::createBody: template is required.', 400);
		}
		if (empty($posts) || !is_array($posts)) {
			throw new Exception('MailTemplates::createBody: posts is invalid.', 400);
		}


		// メールの文面
		switch ($this->template) {
			case 'contactUs':
				return <<< EOS
以下の内容で受け付けました。

名前:
{$posts['name']}

メールアドレス:
{$posts['email']}

内容:
{$posts['body']}

EOS;
			default:
				throw new Exception('MailTemplates::createBody: template is invalid.', 400);
		}
	}
}