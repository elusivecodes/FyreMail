<?php

namespace Fyre\Mail\Handlers;

use Fyre\Mail\Email;
use Fyre\Mail\Exceptions\SmtpException;
use Fyre\Mail\Mailer;

use const STREAM_CLIENT_CONNECT;
use const STREAM_CRYPTO_METHOD_TLS_CLIENT;

use function array_key_first;
use function base64_encode;
use function fclose;
use function fgets;
use function fwrite;
use function preg_replace;
use function str_starts_with;
use function stream_context_create;
use function stream_set_timeout;
use function stream_socket_client;
use function stream_socket_enable_crypto;
use function strlen;
use function substr;

/**
 * SmtpMailer
 */
class SmtpMailer extends Mailer
{

    protected static array $defaults = [
        'host' => '127.0.0.1',
        'username' => null,
        'password' => null,
        'port' => '465',
        'auth' => false,
        'tls' => false,
        'dsn' => false,
        'keepAlive' => false
    ];

    protected $socket;

    /**
     * SmtpMailer wakeup.
     */
    public function __wakeup(): void
    {
        $this->socket = null;
    }

    /**
     * Send an email.
     * @param Email $email The email to send.
     */
    public function send(Email $email): void
    {
        static::checkEmail($email);

        if (!$this->socket) {
            $this->connect();
            $this->authenticate();
        }

        $from = $email->getReturnPath();

        if ($from === []) {
            $from = $email->getFrom();
        }

        $fromAddress = array_key_first($from);
        $this->sendCommand('from', $fromAddress);

        $recipients = $email->getRecipients();

        foreach ($recipients AS $recipient => $name) {
            $this->sendCommand('to', $recipient);
        }

        $this->sendCommand('data');

        $headers = $email->getFullHeaderString();
        $this->sendData($headers);

        $body = $email->getFullBodyString();
        $body = preg_replace('/^\./m', '..$1', $body);

        $this->sendData($body);

        $this->sendCommand('dot');

        $this->end();
    }

    /**
     * Authenticate the connection.
     * @throws SmtpException if the authentication failed.
     */
    protected function authenticate(): void
    {
        $this->sendData('AUTH LOGIN');

        $reply = $this->getData();

        if (str_starts_with($reply, '503')) {
            return;
        }

        if (!str_starts_with($reply, '334')) {
            throw SmtpException::forAuthFailed();
        }

        $username = base64_encode($this->config['username']);
        $this->sendData($username);

        $reply = $this->getData();
        if (!str_starts_with($reply, '334')) {
            throw SmtpException::forAuthFailed();
        }

        $password = base64_encode($this->config['password']);
        $this->sendData($password);

        $reply = $this->getData();
        if (!str_starts_with($reply, '235')) {
            throw SmtpException::forAuthFailed();
        }
    }

    /**
     * Connect to the server.
     * @throws SmtpException if the connection could not be established.
     */
    protected function connect(): void
    {
        $this->socket = stream_socket_client(
            $this->config['host'].':'.$this->config['port'],
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ])
        );

        if (!$this->socket) {
            throw SmtpException::forConnectionFailed();
        }

        stream_set_timeout($this->socket, 5);

        $welcome = $this->getData();

        $this->sendCommand('hello');

        if ($this->config['tls']) {
            $this->sendCommand('starttls');

            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            $this->sendCommand('hello');
        }

        if ($this->config['auth']) {
            $this->authenticate();
        }
    }

    /**
     * Close the connection.
     */
    protected function end(): void
    {
        if ($this->config['keepAlive']) {
            $this->sendCommand('reset');
        } else {
            $this->sendCommand('quit');
        }
    }

    /**
     * Read data from the socket.
     * @return string The data.
     */
    protected function getData(): string
    {
        $data = '';
        while (($str = fgets($this->socket, 512)) !== false) {
            $data .= $str;
        }

        return $data;
    }

    /**
     * Send a command.
     * @param string $command The command.
     * @param string $data The data.
     * @throws SmtpException If the response was not valid.
     */
    protected function sendCommand(string $command, string $data = ''): void
    {
        switch ($command) {
            case 'hello':
                if ($this->config['auth']) {
                    $message = 'EHLO';
                } else {
                    $message = 'HELO';
                }

                $message .= ' '.$this->getClient();
                $response = '250';
                break;
            case 'starttls':
                $message = 'STARTTLS';
                $response = '220';
                break;
            case 'from':
                $message = 'MAIL FROM:<'.$data.'>';
                $response = '250';
                break;
            case 'to':
                $message = 'RCPT TO:<'.$data.'>';
                if ($this->config['dsn']) {
                    $message .= ' NOTIFY=SUCCESS,DELAY,FAILURE ORCPT=rfc822;'.$data;
                }
                $response = '250';
                break;
            case 'data':
                $message = 'DATA';
                $response = '354';
                break;
            case 'dot':
                $message = '.';
                $response = '250';
                break;
            case 'reset':
                $message = 'RSET';
                $response = '250';
                break;
            case 'quit':
                $message = 'QUIT';
                $response = '221';
                break;
        }

        $this->sendData($message);

        $reply = $this->getData();

        if (!str_starts_with($reply, $response)) {
            throw SmtpException::forInvalidResponse();
        }

        if ($command === 'quit') {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    /**
     * Send data to the socket.
     * @param string $data The data to send.
     * @throws SmtpException If the data could not be sent.
     */
    protected function sendData(string $data): void
    {
        $data .= "\r\n";
        $length = strlen($data);
        $written = 0;
        while ($written < $length) {
            if (($result = fwrite($this->socket, substr($data, $written))) === false) {
                throw SmtpException::forInvalidData();
            }

            $written += $result;
        }
    }

}
