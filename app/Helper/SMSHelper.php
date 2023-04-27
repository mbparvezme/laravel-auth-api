<?php

namespace App\Helper;

use Exception;

use Infobip\ApiException;
use Infobip\Configuration;
use Infobip\Api\SendSmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;
use GuzzleHttp\Client;


class SMSHelper
{

  private $config;
  private $client;
  private $number;
  private $text;

  public function __construct()
  {
    $this->config = (new Configuration())->setHost(env('INFOBIP_API_URL'))
    ->setApiKeyPrefix('Authorization', 'App')
    ->setApiKey('Authorization', env('INFOBIP_API_KEY'));
    $this->client = new Client();
  }

  public function sms( string $text ){
    $this->text = $text;
    return $this;
  }

  public function to( string $number ){
    $this->number = $number;
    return $this;
  }

  public function send()
  {
    $sendSmsApi = new SendSMSApi($this->client, $this->config);
    $destination = (new SmsDestination())->setTo($this->number);
    $message =  (new SmsTextualMessage())
                ->setFrom(env('APP_NAME'))
                ->setText($this->text)
                ->setDestinations([$destination]);
    $request = (new SmsAdvancedTextualRequest())->setMessages([$message]);

    try {
      return $sendSmsApi->sendSmsMessage($request) ? TRUE : FALSE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

}
