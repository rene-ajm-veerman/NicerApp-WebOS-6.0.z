<?php 
/* LICENSE __FOR THIS FILE ONLY(!)__ : LGPL Lesser GNU Public License - free for all use including commercial use.
*/
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/../../../3rd-party/vendor/autoload.php');
require_once ('/usr/share/php/Mail.php');
require_once ('/usr/share/php/Mail/mime.php');
error_reporting(E_ALL & ~E_NOTICE); // PHP8.0.11 throws weird notices without cause, which mess up the JSON responses of this PHP file.

function webmail_get_current_datetime_stamp() {
    return date('Ymd_His');
}

function webmail_save_config ($data) {
    $f = fopen (dirname(__FILE__).'/config.json', 'w');
    if ($f!==false) {
        fwrite ($f, $data);
        fclose ($f);
        return true;
    } else return 'ERROR : could not open "'.dirname(__FILE__).'/config.json" for writing!';
}

function webmail_test_email_server ($config) {
    $c = json_decode ($config, true);
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']=='true'?'/imap/ssl':'')
        //.($c['IMAP']['sslCertificateCheck']=='true'?'':'/novalidate-cert')
        .($c['IMAP']['sslCertificateCheck']=='true'?'':'/notls')
        .'}';
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword'], OP_HALFOPEN);
    
    if ($mbox!==false) return 'SUCCESS'; else return 'FAIL';
}

function webmail_get_mailboxes ($config) {
    $c = $config;
    //var_dump ($c);
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']=='true'?'/imap/ssl':'/imap')
        //.($c['IMAP']['sslCertificateCheck']=='true'?'':'/novalidate-cert')
        .'}';
    //echo $connectString; var_dump ($c);
    $mbox = @imap_open($connectString, $c['userID'], $c['userPassword']);
    if ($mbox===false) return array($connectString=>'FAIL:'.json_encode(imap_errors(),JSON_PRETTY_PRINT));
    return imap_list($mbox, $connectString, '*');
}

function webmail_get_mailbox_content ($serverConfig, $serverIdx, $mailboxes, $mailboxIdx, $pageIdx, $perPage) {
    $c = $serverConfig;
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']=='true'?'/imap/ssl':'')
        //.($c['IMAP']['sslCertificateCheck']=='true'?'':'/novalidate-cert')
        .($c['IMAP']['sslCertificateCheck']=='true'?'':'/notls')
        .'}'.$mailboxes[$mailboxIdx];
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword']);
    if ($mbox===false) return 'FAIL - '.$connectString;
    
    $MC = @imap_check($mbox);
    
    $lowerIdx = $MC->Nmsgs - (($pageIdx+1) * $perPage); if ($lowerIdx < 1) $lowerIdx = 1;
    $upperIdx = $MC->Nmsgs -  ($pageIdx * $perPage);

    // Fetch an overview for all messages in INBOX
    $result = @imap_fetch_overview($mbox,$lowerIdx.':'.$upperIdx,0);
    //return json_encode(array('lowerIdx'=>$lowerIdx,'upperIdx'=>$upperIdx,'mc'=>$MC, 'result'=>$result));
    foreach ($result as $overview) {
        $overview->totalMsgs = $MC->Nmsgs;
        $overview->subject = @imap_utf8($overview->subject);
        $overview->from = @imap_utf8($overview->from);
        $overview->to = @imap_utf8($overview->to);
    }
    /*
    $r = '';
    foreach ($result as $overview) {
        $r .= "#{$overview->msgno} ({$overview->date}) - From: {$overview->from}
        {$overview->subject}<br/>";
    }*/
    imap_close($mbox);
    return json_encode($result);
}

function webmail_get_mail_content ($serverConfig, $serverIdx, $mailboxes, $mailboxIdx, $mailIdx, $stripColors = true) {
    $c = $serverConfig;
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']=='true'?'/imap/ssl':'')
        //.($c['IMAP']['sslCertificateCheck']=='true'?'':'/novalidate-cert')
        .($c['IMAP']['sslCertificateCheck']=='true'?'':'/notls')
        .'}'.$mailboxes[$mailboxIdx];
    //return $connectString;
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword']);
    if ($mbox===false) return 'FAIL - '.$connectString;
   
    $msg = '';
    $structure = imap_fetchstructure($mbox, $mailIdx);
    
    $headers = imap_fetchheader($mbox, $mailIdx, FT_PREFETCHTEXT);
    $data = imap_body($mbox, $mailIdx);//getPart($mbox, $mailIdx, $partNumber, $part->encoding);
    $parser = new PhpMimeMailParser\Parser();
    
    $parser->setText($headers."\n".$data);
    $data2 = $parser->getMessageBody('html');
    if ($data2==='') $data2 = '<pre>'.$parser->getMessageBody('text').'</pre>';
    
    if ($stripColors) {
        $i = stripos($data2, 'background');
        while ($i !== false) {
            $j = strpos($data2, ';', $i);
            if ($j===false) $j = strpos($data2, '"', $i);
            $data2 = substr($data2, 0, $i-1).substr($data2,$j+1);
            $i = stripos($data2, 'background');
        }
    };
    return $data2;
}

function webmail_get_mail_headers ($serverConfig, $serverIdx, $mailboxes, $mailboxIdx, $mailIdx) {
    $c = $serverConfig;
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']=='true'?'/imap/ssl':'')
        //.($c['IMAP']['sslCertificateCheck']=='true'?'':'/novalidate-cert')
        .($c['IMAP']['sslCertificateCheck']=='true'?'':'/notls')
        .'}'.$mailboxes[$mailboxIdx];
    //return $connectString;
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword']);
    if ($mbox===false) return 'FAIL - '.$connectString;
   
    $msg = '';
    $structure = imap_fetchstructure($mbox, $mailIdx);
    
    $headers = imap_fetchheader($mbox, $mailIdx, FT_PREFETCHTEXT);
    return $headers;
}


function is_base64($s) {
    if (($b = base64_decode($s, TRUE)) === FALSE) {
        return FALSE;
    }

    // now check whether the decoded data could be actual text
    $e = mb_detect_encoding($b);
    if (in_array($e, array('UTF-8', 'ASCII'))) { // YMMV
        return TRUE;
    } else {
        return FALSE;
    }
}

function getFilenameFromPart($part) {
	$filename = '';
	
	if($part->ifdparameters) {
		foreach($part->dparameters as $object) {
            if(strtolower($object->attribute) == 'filename') {
				$filename = $object->value;
			}
		}
	}

	if(!$filename && $part->ifparameters) {
		foreach($part->parameters as $object) {
			if(strtolower($object->attribute) == 'name') {
				$filename = $object->value;
			}
		}
	}
	
	return $filename;
}

/*
function webmail_send_mail ($serverConfig, $from, $to, $subject, $body) {
    $c = $serverConfig;
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']?'/imap/ssl':'')
        .($c['IMAP']['sslCertificateCheck']?'':'/novalidate-cert')
        .'}INBOX.Sent';
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword']);

    $envelope = array( //https://www.php.net/manual/en/function.imap-mail-compose.php
        'from' => $from,
        'to' => $to,
        'subject' => $subject
    );
    $body = array(
        array(),
        array(
            'type' => TYPETEXT,
            'subtype' => 'plain',
            'description' => $subject,
            'content.data' => $body
        )
    );
    $mail = str_replace("\r","",imap_mail_compose($envelope, $body));
    
    $headers = array();
    // To send HTML mail, the Content-type header must be set
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';

    // Additional headers
    $headers[] = 'To: '.$to.'';
    $headers[] = 'From: '.$from.'';

    // Mail it
    $r = imap_mail($to, $subject, $mail, implode("\r\n",$headers));    // TODO : NEVER ACTUALLY DELIVERS#@!!@!
    if (!$r) $r = error_get_last();
    // Put in Sent folder
    if ($r) { imap_append($mbox,$connectString,$mail ,"\\Seen"); };
    return json_encode($r);
}*/

function webmail_send_mail ($serverConfig, $from, $to, $subject, $headers, $body) {
    $c = $serverConfig;
    if (is_array($c) && array_key_exists ('SMTP', $c) && array_key_exists('usePEAR', $c['SMTP']) && $c['SMTP']['usePEAR']) {
        return webmail_send_mail_PEAR ($serverConfig, $from, $to, $subject, $headers, $body);
    } else {
        return webmail_send_mail_default ($serverConfig, $from, $to, $subject, $headers, $body);
    }
}

function webmail_send_mail_PEAR ($serverConfig, $from, $to, $subject, $headers, $bodyHTML) {
// many thanks to https://stackoverflow.com/questions/12783435/pear-mail-how-to-send-plain-text-text-html-in-utf-8
    $c = $serverConfig;
    /*
    $connectString = 
        '{'.$c['IMAP']['domain'].':'.$c['IMAP']['port']
        .($c['IMAP']['requiresSSL']?'/imap/ssl':'')
        .($c['IMAP']['sslCertificateCheck']?'':'/novalidate-cert')
        .'}INBOX.Sent';
    $mbox = imap_open($connectString, $c['userID'], $c['userPassword']);
    */
    
    $pear_params = array (
        'host' => $c['SMTP']['domain'],
        'port' => $c['SMTP']['port for TLS'],
        'auth' => true,
        'AUTH' => true,
        'socket_options' => array('ssl' => array('verify_peer'=>false,'verify_peer_name' => false, 'allow_self_signed' => true)),
        'username' => $c['userID'],//array_key_exists('userID_smtp',$c) ? $c['userID_smtp'] : $c['userID'],
        'password' => $c['userPassword'],
        'localhost' => 'smtp.nicer.app'
    );

    global $naVersion;
    $headers = array_merge($headers, array (
        'From' => $from,
        'Return-Path' => $from,
        'To' => $to,
        'Subject' => $subject,
        'X-Mailer' => $naVersion,
        'Content-Type'  => 'text/html; charset=UTF-8'
    ));

    $mime_params = array(
        'text_encoding' => '7bit',
        'text_charset'  => 'UTF-8',
        'html_charset'  => 'UTF-8',
        'head_charset'  => 'UTF-8'
    );

    $mime = new Mail_mime();

    $mime->setTXTBody(strip_tags($bodyHTML));
    $mime->setHTMLBody($bodyHTML);

    $body = $mime->get($mime_params);
    $headers = $mime->headers($headers);
    $mail_object = Mail::factory('smtp', $pear_params);
    
    $mail = $mail_object->send($to, $headers, $body);    
    if (PEAR::isError($mail)) {
        echo 'FAIL : '.$mail->getMessage();
    } else {
        echo 'SUCCESS';
    }
}

function webmail_send_mail_default ($serverConfig, $from, $to, $subject, $headers, $body) {
    //$body = quoted_printable_encode($body); // message will bounce when sent from rene.veerman.netherlands@gmail.com to rene.veerman.netherlands@gmail.com
    $bodyWrapped = wordwrap ($body, 70, "\r\n");
    global $naVersionNumber;
    global $naVersion; 
    $headers2 = 
        'From: '.$from."\r\n"
        .'Reply-To: '.$from."\r\n"
        ."MIME-Version: 1.0" . "\r\n"
        ."Content-type:text/html;charset=utf-8" . "\r\n"
        .'X-Mailer:' . $naVersion. "\r\n";
    foreach ($headers as $hn => $hv) $headers2 .= $hn.':'.$hv."\r\n";
    $r = mail ($to, $subject, $bodyWrapped, $headers2);
    if ($r === true) echo 'SUCCESS'; else echo 'FAIL webmail_send_mail_default';
}
?>
