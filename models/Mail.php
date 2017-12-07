<?php


class Mail {

	private $to = null;

	private $from = null;

	private $bcc = null;

	private $subject = '無題';

	private $template = null;

	private $message = '';


	function __construct(array $params = null) {

		if (!is_array($params)) {
			return;
		}
		if (isset($params['to'])) {
			$this->to = $params['to'];
		}
		if (isset($params['from'])) {
			$this->from = $params['from'];
		}
		if (isset($params['bcc'])) {
			$this->bcc = $params['bcc'];
		}
		if (isset($params['subject'])) {
			$this->subject = $params['subject'];
		}
		if (isset($params['template'])) {
			$this->template = $params['template'];
		}
		if (isset($params['message'])) {
			$this->message = $params['message'];
		}

		if (empty($this->message)) {
			$this->createBody($params['messageParams']);
		}
	}


	public function setTo(string $to) {
		if (!empty($to)) {
			$this->to = $to;
		}
	}


	public function setBcc(string $bcc) {
		if (!empty($to)) {
			$this->bcc = $bcc;
		}
	}


	public function setSubject(string $subject) {
		if (!empty($subject)) {
			$this->subject = $subject;
		}
	}


	/**
	 * メール内容を取得する
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->message;
	}


	/**
	 * メールを送る
	 */
	public function send(array $files = null) {

		self::beforeSendValidation($this->to);

		mb_language("Ja") ;
		mb_internal_encoding("UTF-8");

		if (empty($files)) {
			$header = (!empty($this->bcc)) ? "From:{$this->from}\r\nBcc:{$this->bcc}\r\n" : "From:{$this->from}";
			mb_send_mail($this->to, $this->subject, $this->message, $header);
			return;
		}

		// ファイルががアップロードされている場合、メールに添付する
		$boundary = time();

		$header  = "From: {$this->from}\r\n";

		if (!empty($this->bcc)) {
			$header .= "Bcc: {$this->bcc}\r\n";
		}

		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

		$body  = "--{$boundary}\r\n";
		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
		$body .= "\r\n";
		$body .= "{$this->message}\r\n";
		$body .= "--{$boundary}\r\n";

		// 添付ファイルの処理
		$cnt = count($files);

		foreach ($files as $file) {
			$handle = fopen($file['tmp_name'], 'r');
			$attachFile = fread($handle, filesize($file['tmp_name']));
			fclose($handle);
			$attachEncode = base64_encode($attachFile);

			$body .= "Content-Type: {$file['type']}; name=\"{$file['name']}\"\r\n";
			$body .= "Content-Transfer-Encoding: base64\r\n";
			$body .= "Content-Disposition: attachment; filename=\"{$file['name']}\"\r\n";
			$body .= "\r\n";
			$body .= chunk_split($attachEncode) . "\r\n";

			if (--$cnt === 0) {
				$body .= "--{$boundary}--\r\n";
			} else {
				$body .= "--{$boundary}\r\n";
			}
		}
		mb_send_mail($this->to, $this->subject, $body, $header);
	}


	/**
	 * メール内容を作る
	 */
	private function createBody(array $posts) {
		$posts = self::beforeCreateBodyValidation($posts);
		$templates = new MailTemplates(['template' => $this->template]);
		$this->message = $templates->createBody($posts);
	}


	/**
	 * メール面文を作成する前のバリデーション
	 *
	 * @param array $posts
	 * @return vold
	 */
	private static function beforeCreateBodyValidation(array $posts) {
		if (!isset($posts)) {
			throw new Exception('Mail::beforeCreateBodyValidation: posts is required.', 400);
		}
		if (!is_array($posts)) {
			throw new Exception('Mail::beforeCreateBodyValidation: posts must be array type.', 400);
		}

		/**
		 * 名前
		 */
		if (empty($posts['name'])) {
			throw new Exception('Name is required', 400);
		}


		/**
		 * かな
		 */
		/*if (empty($posts['nameKana'])) {
			throw new Exception('かなは必須です', 400);
		}

		$posts['nameKana'] = mb_convert_kana($posts['nameSeiKana'], 'HVs');*/


		/**
		 * メールアドレス
		 */
		if (empty($posts['email'])) {
			throw new Exception('Email is required', 400);
		}

		$posts['email'] = mb_convert_kana($posts['email'], 'as');
		$posts['email'] = str_replace('ー', '-', $posts['email']);
		$posts['email'] = str_replace('＿', '_', $posts['email']);
		$posts['email'] = str_replace('＠', '@', $posts['email']);

		if (!preg_match('/^[0-9a-z-._]+@[0-9a-z-._]+[0-9a-z]+$/i', $posts['email'])) {
			throw new Exception('メールアドレスの形式が不正です', 400);
		}


		/**
		 * 名前
		 */
		if (empty($posts['body'])) {
			throw new Exception('Body is required', 400);
		}


		return $posts;
	}


	/**
	 * メールを送る前のバリデーション
	 *
	 * @param string|array $emails
	 * @return vold
	 */
	private function beforeSendValidation($emails) {
		if (!isset($emails)) {
			throw new Exception('emails is required', 400);
		}

		$emails = (is_array($emails)) ? $emails : [$emails];

		foreach ($emails as $email) {
			if (empty($email)) {
				throw new Exception('メールアドレスは必須です', 400);
			}
			if (!preg_match('/^[0-9a-z-._]+@[0-9a-z-._]+[0-9a-z]+$/i', $email)) {
				throw new Exception('メールアドレスの形式が不正です', 400);
			}
		}
	}

}