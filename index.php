<?
ob_start('ob_gzhandler');
header('Content-Type: application/rss+xml; charset=UTF-8');
$id = 'nnancliccjabjjmipbpjkfbijifaainp';
$c = curl_init('https://chrome.google.com/reviews/json/search');
$extURL = 'http://chrome.google.com/extensions/permalink?id='.$id;
$fields = http_build_query(array(
	'req' => '{"searchSpecs":[{"requireComment":true,"entities":[{"url":"'.$extURL.'"}],"groups":["public_comment"],"matchExtraGroups":true,"startIndex":0,"numResults":10,"includeNickNames":true}],"applicationId":94}',
));
$options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $fields,
);
curl_setopt_array($c, $options);
$json = curl_exec($c);
curl_close($c);

$data = json_decode(utf8_encode($json), true);
if ($data['channelHeader']['errorCode']) return;
$comments = $data['searchResults'][0]['annotations'];

echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<rss version="2.0">
<channel>
<title>Chrome comments</title>
<? foreach($comments as $comment):?>
<?
$author = $comment['entity']['nickname'];
if (!$author) $author = 'anonymous';
$msg = preg_replace('/[\n\r\t]/', ' ', htmlspecialchars($author . ': ' . $comment['comment']));
?>
<item>
	<title><?= $msg ?></title>
	<description><?= $msg ?></description>
	<link><?= $extURL ?>#<?= $comment['timestamp'] ?></link>
	<pubDate><?= date('r', $comment['timestamp']) ?></pubDate>
</item>
<? endforeach; ?>
</channel>
</rss>