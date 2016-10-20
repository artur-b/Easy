<?php

namespace app\helpers;

class Mail
{
    private static
        $MAIL = null,
        $DATA = [        
            'host'  => 'smtp.gmail.com',
            'user'  => 'user',
            'pass'  => 'pass',
            'from'  => 'no_reply@gmail.com',
            'html' => true,
            'char' => 'UTF-8',
            'smtp' => true,
            'fnam' => 'no_reply',
            'subj' => '',
            'body' => '',
            'debg' => false,
            'port' => 587,
            'smsc' => 'tls',
            'atch' => array()
        ];

    private static function loadPhpmailer()
    {
        self::$MAIL = new \PHPMailer();
    }

    public static function SetProperty($name = false, $value)
    {
        if (!$name || $value === false) return false;

        if (isset(self::$DATA[ $name ])) self::$DATA[ $name ] = $value;
    }

    public static function SetProperties($settings = false)
    {
        if (!$settings) return false;
        if (!is_array($settings)) return false;

        foreach ($settings as $key => $value) if (isset(self::$DATA[ $key ])) self::$DATA[ $key ] = $value;
    }    

    public static function Adv()
    {
        if (!self::$MAIL) self::loadPhpmailer();

        return self::$MAIL;
    }

    public static function Send($emails = false)
    {

        if (!$emails) return false;

        if (!self::$MAIL) self::loadPhpmailer();

        if (self::$DATA['subj'] == '') return false;
        if (self::$DATA['body'] == '') return false;

        self::$MAIL->IsSMTP();
        self::$MAIL->WordWrap = 50;
        self::$MAIL->IsHTML(self::$DATA['html']);

        $replyto_email = self::$DATA['from'];
        $replyto_name  = self::$DATA['fnam'];

        if (isset(self::$DATA['replyto_email']) && isset(self::$DATA['replyto_name'])) {
            $replyto_email = self::$DATA['replyto_email'];
            $replyto_name  = self::$DATA['replyto_name'];
        }

        self::$MAIL->AddReplyTo($replyto_email, $replyto_name);
        self::$MAIL->SetFrom(self::$DATA['from'], self::$DATA['fnam']);

        self::$MAIL->Host      = self::$DATA['host'];
        self::$MAIL->SMTPAuth  = self::$DATA['smtp'];
        self::$MAIL->SMTPDebug = self::$DATA['debg'];
        self::$MAIL->Username  = self::$DATA['user'];
        self::$MAIL->Password  = self::$DATA['pass'];
        self::$MAIL->CharSet   = self::$DATA['char'];
        self::$MAIL->Subject   = self::$DATA['subj'];
        self::$MAIL->Body      = self::$DATA['body'];
        
        // PHP 5.6+ cert verification hack
        /*
        self::$MAIL->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));
         * 
         */

        if (self::$DATA['port']) self::$MAIL->Port = self::$DATA['port'];
        if (self::$DATA['smsc']) self::$MAIL->SMTPSecure = self::$DATA['smsc'];

        if (is_array($emails)) foreach ($emails as $key => $email) self::$MAIL->AddAddress($email);
        else self::$MAIL->AddAddress($emails);

        $status = self::$MAIL->Send();

        self::clearAll();
        
        return $status;
    }

    public static function clearAll() 
    {
        self::$DATA['subj'] = '';
        self::$DATA['body'] = '';
        self::$DATA['atch'] = array();

        self::$MAIL->clearAddresses();
        self::$MAIL->ClearAttachments();
        self::$MAIL->Body    = "";
        self::$MAIL->Subject = "";
    }
        
}
