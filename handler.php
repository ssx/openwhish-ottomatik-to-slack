<?php
function parse(array $args) : array
{
  try {
    // You could do something with these like calculating the time
    // it look to run the job.
    $started_at = $args["started_at"];
    $completed_at = $args["completed_at"];

    // If the job passed, we'll send it to the #ops channel, however
    // if it fails then we'll send it to the #notifications channel
    // with a notice
    if ($args['state'] === 'success') {
      $text = $args["text"]." stored in service *".$args['archive']['name']."*.";
      $icon = ":white_check_mark:";
      $webhook_url = "https://hooks.slack.com/services/REPLACE_TOKEN";
      $channel = "ops";
    } else {
      $text = "@channel, a failed backup occurred: ".$args["text"];
      $icon = ":fire:";
      $webhook_url = "https://hooks.slack.com/services/REPLACE_TOKEN";
      $channel = "notifications";
    }

    $data = [
      'text' => $text,
      'channel' => $channel,
      'username' => 'Ottomatik',
      'icon_emoji' => $icon,
      'link_names' => 1
    ];

    $client = new \GuzzleHttp\Client();
    $r = $client->request('POST', $webhook_url, [ 'json' => $data ]);
  } catch (Exception $e) {
    die("Error sending request: ".$e->getMessage());
  }

  return [
      'date' => date('r'),
      'status_code' => $r->getStatusCode(),
      'args' => $args // Useful for debugging
   ];
}
