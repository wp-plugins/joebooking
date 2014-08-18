<?php
include_once( dirname(__FILE__) . '/class.phpmailer.php' );

class ntsEmail {
	var $subject;
	var $body;
	var $from;
	var $fromName;
	var $debug;
	var $disabled = false;
	var $error;
	var $charSet = '';
	var $isHtml = TRUE;

	function ntsEmail(){
		$this->isHtml = TRUE;
		$this->subject = '';
		$this->body = '';
		$this->error = '';

		$this->disabled = false;

		$this->mail = new ntsPHPMailer();

		$lm =& ntsLanguageManager::getInstance(); 
		$this->setLanguage( $lm->getDefaultLanguage() );

	/* from, from name, and debug settings */
		$conf =& ntsConf::getInstance();
		$this->disabled = $conf->get('emailDisabled');

		$this->from = $conf->get('emailSentFrom');
		$this->fromName = $conf->get('emailSentFromName');
		$this->debug = ( $conf->get('emailDebug') ) ? true : false;

	/* smtp settings */
		$smtpHost = $conf->get('smtpHost');
		$smtpUser = $conf->get('smtpUser');
		$smtpPass = $conf->get('smtpPass');
		$smtpSecure = $conf->get('smtpSecure');

		if( $smtpHost ){
			$this->mail->IsSMTP();
			$this->mail->Host = $smtpHost;
			if( $smtpUser && $smtpPass ){
				$this->mail->SMTPAuth = true;
				$this->mail->Username = $smtpUser;
				$this->mail->Password = $smtpPass;
				$this->mail->SMTPSecure = $smtpSecure;
				}
			}

	/* logger */
		$loggerFile = dirname(__FILE__) . '/ntsEmailLogger.php';
		if( file_exists($loggerFile) ){
			$this->logger = true;
			include_once( $loggerFile );
			}
		else {
			$this->logger = false;
			}
		}

    function setLanguage( $lng )
	{
		$lm =& ntsLanguageManager::getInstance(); 
		$languageConf = $lm->getLanguageConf( $lng );
		if( isset($languageConf['charset']) ){
			$this->mail->CharSet = $languageConf['charset'];
			}
	}

	function addAttachment( $string, $filename ){
		$this->mail->AddStringAttachment( $string, $filename );
		}

	function addFileAttachment( $path, $filename ){
		$this->mail->AddAttachment( $path, $filename );
		}

	function setSubject( $subject ){
		$this->subject = $subject;
		}
	function setBody( $body ){
		$this->body = $body;
		}
	function setFrom( $from ){
		$this->from = $from;
		}
	function setFromName( $fromName ){
		$this->fromName = $fromName;
		}

	function sendToOne( $toEmail ){
		$toArray = array( $toEmail );
		return $this->_send( $toArray );
		}

	function getBody(){
		return $this->body;
		}

	function getSubject(){
		return $this->subject;
		}

	function _send( $toArray = array() ){
		if( $this->disabled )
			return true;

		$this->mail->SetLanguage( 'en', dirname(__FILE__) . '/' );
		$this->mail->From = $this->from;
		$this->mail->FromName = $this->fromName;
		$this->mail->IsHTML( $this->isHtml );

		$text = $this->getBody();

		$this->mail->Subject = $this->getSubject();
		if( $this->isHtml )
		{
//			$this->mail->Body = nl2br( $text );
			$this->mail->Body = $text;
		}
		else
		{
			$this->mail->Body = $text;
		}

		$this->mail->AltBody = strip_tags( $text );

		if( $this->logger ){
			$log = new ntsEmailLogger();
			$log->setParam( 'from_email', $this->mail->From );
			$log->setParam( 'from_name', $this->mail->FromName );
			$log->setParam( 'subject', $this->mail->Subject );
			$log->setParam( 'body', $this->mail->Body );
			$log->setParam( 'alt_body', $this->mail->AltBody );
			}

		reset( $toArray );
		if( defined('NTS_DEVELOPMENT') && NTS_DEVELOPMENT )
		{
			$msg = 'Email to ' . join( ', ', $toArray ) . ':<br>' . $this->getSubject();
			ntsView::addAnnounce( $msg, 'info' );
		}
		elseif( $this->debug ){
			echo '<PRE>';
			echo "<BR>-------------------------------------------<BR>";
			foreach( $toArray as $to ){
				echo "To:<BR><I>$to</I><BR>";
				}
			echo "====<BR>";
			echo "From:<BR><I>$this->from</I> <B>$this->fromName</B><BR>";
			echo 'Subj:<BR><I>' . $this->getSubject() . '</I><BR>';
			echo 'Msg:<BR><I>' . $text . '</I><BR>';
			echo "<BR>-------------------------------------------<BR>";

			if( $attachements = $this->mail->GetAttachments() ){
				echo "Attachements:<BR>";
				foreach( $attachements as $att ){
					echo $att[1];
					echo "<BR>-------------------------------------------<BR>";
					echo $att[0] . '<br>';
					echo "<BR>===========================================<BR>";
					}
				}

			echo '</PRE>';

			/* add log */
			if( $this->logger ){
				reset( $toArray );
				foreach( $toArray as $to ){
					$log->setParam( 'to_email', $to );
					$log->add();
					}
				}
			}
		else {
//			$this->mail->WordWrap = 50; // set word wrap to 50 characters

			$this->mail->ClearAddresses();
			foreach( $toArray as $to ){
				$this->mail->AddAddress( $to );
				}

			if( ! $this->mail->Send() ){
				$errTxt = "Mailer Error: " . $this->mail->ErrorInfo;
//				ntsView::addAnnounce( $errTxt, 'error' );
				$this->error = $errTxt;
				return false;
				}
			else {
				/* add log */
				if( $this->logger ){
					reset( $toArray );
					foreach( $toArray as $to ){
						$log->setParam( 'to_email', $to );
						$log->add();
						}
					}
				}
			}
		return true;
		}

	function getError(){
		return $this->error;
		}
	}
?>