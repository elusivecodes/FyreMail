# FyreMail

**FyreMail** is a free, open-source email library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Mailers](#mailers)
    - [Sendmail](#sendmail)
    - [SMTP](#smtp)
- [Emails](#emails)



## Installation

**Using Composer**

```
composer require fyre/mail
```

In PHP:

```php
use Fyre\Mail\MailManager;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$config` is a  [*Config*](https://github.com/elusivecodes/FyreConfig).

```php
$mailManager = new MailManager($container);
```

Default configuration options will be resolved from the "*Mail*" key in the [*Config*](https://github.com/elusivecodes/FyreConfig).

**Autoloading**

It is recommended to bind the *MailManager* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(MailManager::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$mailManager = $container->use(MailManager::class);
```


## Methods

**Build**

Build a [*Mailer*](#mailers).

- `$options` is an array containing configuration options.

```php
$mailer = $mailManager->build($options);
```

[*Mailer*](#mailers) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).

**Clear**

Clear all instances and configs.

```php
$mailManager->clear();
```

**Get Config**

Get a [*Mailer*](#mailers) config.

- `$key` is a string representing the [*Mailer*](#mailers) key.

```php
$config = $mailManager->getConfig($key);
```

Alternatively, if the `$key` argument is omitted an array containing all configurations will be returned.

```php
$config = $mailManager->getConfig();
```

**Has Config**

Determine whether a [*Mailer*](#mailers) config exists.

- `$key` is a string representing the [*Mailer*](#mailers) key, and will default to `MailManager::DEFAULT`.

```php
$hasConfig = $mailManager->hasConfig($key);
```

**Is Loaded**

Determine whether a [*Mailer*](#mailers) instance is loaded.

- `$key` is a string representing the [*Mailer*](#mailers) key, and will default to `MailManager::DEFAULT`.

```php
$isLoaded = $mailManager->isLoaded($key);
```

**Set Config**

Set the [*Mailer*](#mailers) config.

- `$key` is a string representing the [*Mailer*](#mailers) key.
- `$options` is an array containing configuration options.

```php
$mailManager->setConfig($key, $options);
```

**Unload**

Unload a [*Mailer*](#mailers).

- `$key` is a string representing the [*Mailer*](#mailers) key, and will default to `MailManager::DEFAULT`.

```php
$unloaded = $mailManager->unload($key);
```

**Use**

Load a shared [*Mailer*](#mailers) instance.

- `$key` is a string representing the [*Mailer*](#mailers) key, and will default to `MailManager::DEFAULT`.

```php
$mailer = $mailManager->use($key);
```

[*Mailer*](#mailers) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).


## Mailers

You can load a specific mailer by specifying the `className` option of the `$options` variable above.

Custom mailers can be created by extending `\Fyre\Mail\Mailer`, ensuring all below methods are implemented.

**Email**

Create an [*Email*](#emails).

```php
$email = $mailer->email();
```

**Get Client**

Get the client hostname.

```php
$client = $mailer->getCliet();
```

**Send**

Send an [*Email*](#emails).

- `$email` is an [*Email*](#emails).

```php
$mailer->send($email);
```


### Sendmail

The Sendmail mailer can be loaded using custom configuration.

- `$key` is a string representing the mailer key.
- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\Mail\Handlers\SendmailMailer`.
    - `charset` is a string representing the character set, and will default to "*utf-8*".
    - `client` is a string representing the client hostname.

```php
$mailer = $mailManager->build($options);
```


### SMTP

The SMTP mailer can be loaded using custom configuration.

- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\Mail\Handlers\SmtpMailer`.
    - `host` is a string representing the SMTP host, and will default to "*127.0.0.1*".
    - `username` is a string representing the SMTP username.
    - `password` is a string representing the SMTP password.
    - `port` is a number indicating the SMTP port, and will default to *465*.
    - `auth` is a boolean indicating whether to authenticate, and will default to *false*.
    - `tls` is a boolean indicating whether to use TLS encryption, and will default to *false*.
    - `dsn` is a boolean indicating whether to use delivery status notification, and will default to *false*.
    - `keepAlive` is a boolean indicating whether to use a persistent connection, and will default to *false*.
    - `charset` is a string representing the character set, and will default to "*utf-8*".
    - `client` is a string representing the client hostname.

```php
$mailer = $mailManager->build($options);
```


## Emails

**Add Attachments**

Add attachments.

- `$attachments` is an array containing the attachments, where the key is the filename and the value is an array of attachment data.
    - `file` is a string representing a path to a file.
    - `content` is a string representing the file data.
    - `mimeType` is a string representing the MIME content type.
    - `contentId` is a string representing the content ID.
    - `disposition` is a string representing the content disposition.

```php
$email->addAttachments($attachments);
```

For each attachment, a `file` or `content` must be supplied.

If the `mimeType` is omitted it will determined automatically from the file data.

If the `disposition` is omitted, it will default to "*inline*" if a `contentId` is provided, otherwise "*attachment*".

**Add Bcc**

Add a bcc address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->addBcc($email, $name);
```

**Add Cc**

Add a cc address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->addCc($email, $name);
```

**Add Reply To**

Add a reply to address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->addReplyTo($email, $name);
```

**Add To**

Add a to address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->addTo($email, $name);
```

**Get Attachments**

Get the attachments.

```php
$attachments = $email->getAttachments();
```

**Get Bcc**

Get the bcc addresses.

```php
$bcc = $email->getBcc();
```

**Get Body HTML**

Get the HTML body string.

```php
$html = $email->getBodyHtml();
```

**Get Body Text**

Get the text body string.

```php
$text = $email->getBodyText();
```

**Get Boundary**

Get the boundary.

```php
$boundary = $email->getBoundary();
```

**Get Cc**

Get the cc addresses.

```php
$cc = $email->getCc();
```

**Get Charset**

Get the character set.

```php
$charset = $email->getCharset();
```

**Get Format**

Get the email format.

```php
$format = $email->getFormat();
```

**Get From**

Get the from addresses.

```php
$from = $email->getFrom();
```

**Get Headers**

Get the additional headers.

```php
$headers = $email->getHeaders();
```

**Get Message ID**

Get the message ID.

```php
$messageId = $email->getMessageId();
```

**Get Priority**

Get the priority.

```php
$priority = $email->getPriority();
```

**Get Read Receipt**

Get the read recipient addresses.

```php
$readReceipt = $email->getReadReceipt();
```

**Get Recipients**

Get the recipient addresses.

```php
$recipients = $email->getRecipients();
```

**Get Reply To**

Get the reply to addresses.

```php
$replyTo = $email->getReplyTo();
```

**Get Return Path**

Get the return path addresses.

```php
$returnPath = $email->getReturnPath();
```

**Get Sender**

Get the sender addresses.

```php
$sender = $email->getSender();
```

**Get Subject**

Get the subject.

```php
$subject = $email->getSubject();
```

**Get To**

Get the to addresses.

```php
$to = $email->getTo();
```

**Send**

Send the email.

```php
$email->send();
```

**Set Attachments**

Set the attachments.

- `$attachments` is an array containing the attachments, where the key is the filename and the value is an array of attachment data.
    - `file` is a string representing a path to a file.
    - `content` is a string representing the file data.
    - `mimeType` is a string representing the MIME content type.
    - `contentId` is a string representing the content ID.
    - `disposition` is a string representing the content disposition.

```php
$email->setAttachments($attachments);
```

For each attachment, a `file` or `content` must be supplied.

If the `mimeType` is omitted it will determined automatically from the file data.

If the `disposition` is omitted, it will default to "*inline*" if a `contentId` is provided, otherwise "*attachment*".

**Set Bcc**

Set the bcc addresses.

- `$emails` is an array containing the email addresses, or key-value pairs of email addresses and names.

```php
$email->setBcc($emails);
```

**Set Body Html**

Set the body HTML.

- `$html` is a string representing the body HTML.

```php
$email->setBodyHtml($html);
```

**Set Body Text**

Set the body text.

- `$text` is a string representing the body text.

```php
$email->setBodyText($text);
```

**Set Cc**

Set the cc addresses.

- `$emails` is an array containing the email addresses, or key-value pairs of email addresses and names.

```php
$email->setCc($emails);
```

**Set Charset**

Set the character set.

- `$charset` is a string representing the character set.

```php
$email->setCharset($charset);
```

**Set Format**

Set the email format.

- `$format` is a string representing the email format, and must be one of either "*html*", "*text*", or "*both*".

```php
$email->setFormat($format);
```

**Set From**

Set the from address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->setFrom($email, $name);
```

**Set Headers**

Set additional headers.

- `$headers` is an array containing additional headers.

```php
$email->setHeaders($headers);
```

**Set Priority**

Set the priority.

```php
$email->setPriority($priority);
```

**Set Read Receipt**

Set the read recipient address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->setReadReceipt($email, $name);
```

**Set Reply To**

Set the reply to addresses.

- `$emails` is an array containing the email addresses, or key-value pairs of email addresses and names.

```php
$email->setReplyTo($emails);
```

**Set Return Path**

Set the return path address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->setReturnPath($email, $name);
```

**Set Sender**

Set the sender address.

- `$email` is a string representing the email address.
- `$name` is a string representing the name, and will default to the email address.

```php
$email->setSender($email, $name);
```

**Set Subject**

Set the subject.

- `$subject` is a string representing the subject.

```php
$email->setSubject($subject);
```

**Set To**

Set the to addresses.

- `$emails` is an array containing the email addresses, or key-value pairs of email addresses and names.

```php
$email->setTo($emails);
```