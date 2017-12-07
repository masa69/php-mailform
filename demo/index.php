<?php

require '../app.php';

try {
	$error = null;

	$requests = $_POST;
	$files = (!empty($_FILES) && is_array($_FILES)) ? $_FILES : null;

	$posts['name']  = (!empty($requests['name']))  ? Escaper::string($requests['name'])  : '';
	$posts['email'] = (!empty($requests['email'])) ? Escaper::string($requests['email']) : '';
	$posts['body']  = (!empty($requests['body']))  ? Escaper::string($requests['body'])  : '';

	$trySend = false;

	foreach ($posts as $post) {
		if ($post !== '') {
			$trySend = true;
		}
	}

	if ($trySend) {

		$to = $posts['email'];
		$bcc = 'psychedelic.nekopunch@gmail.com';

		$mail = new Mail([
			'to'      => $to,
			'bcc'     => $bcc,
			'subject' => "[contact us] thanks, {$posts['name']}",
			'template' => 'contactUs',
			'messageParams' => $posts,
		]);

		$mail->send($files);

		header("location: thanks.php");
	}

} catch (Exception $e) {
	$error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP mailform</title>
</head>
<body>
	<strong style="color: red"><?php echo $error ?></strong>
	<form action="" method="POST">
		<div>
			<span>Name: </span><input type="text" name="name" value="<?php echo $posts['name'] ?>">
		</div>
		<div>
			<span>Email: </span><input type="text" name="email" value="<?php echo $posts['email'] ?>">
		</div>
		<div>
			<span>Body: </span>
			<textarea name="body" cols="30" rows="10"><?php echo $posts['body'] ?></textarea>
		</div>
		<input type="submit">
	</form>
</body>
</html>