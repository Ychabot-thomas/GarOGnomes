<?php
/**
 * Created by PhpStorm.
 * User: Teddy
 * Date: 14/03/2019
 * Time: 11:00
 */

 if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options

return [
    // Captcha configuration for contact page
    'ContactCaptcha' => [
        'UserInputID' => 'captchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
        'ImageStyle' => ImageStyle::AncientMosaic,
    ],

];