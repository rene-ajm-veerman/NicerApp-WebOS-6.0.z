<?php 

namespace Rene_AJM_Veerman\phpHardCrypto;

const PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE = 534288;
const PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE = 16777216;
const PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_SENSITIVE = 33554432;
const PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_SENSITIVE = 1073741824;

class class_phpHardCrypto implements interface_class_phpHardCrypto {

    
    protected $ns = 'Rene_AJM_Veerman\phpHardCrypto';
    protected $cn = 'Rene_AJM_Veerman\phpHardCrypto\class_phpHardCrypto->';
    
    protected $hashAlgos = [];
    protected $hashAlgo = 'sha512';
    protected $storagePath = null; 

    public function __construct () {
        $fncn = '__construct ()';
        $this->hashAlgos = hash_algos();
        $this->storagePath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'nicerapp'.DIRECTORY_SEPARATOR.'siteCache'.DIRECTORY_SEPARATOR.'hashValues';
        $this->checkStoragePath();
    }
    
    public function __destruct () {
    }
    
    private function checkStoragePath () {
        $fncn = 'checkStoragePath';
        if (!is_dir($this->storagePath)) {
            trigger_error ($this->cn.$fncn.' : !is_dir("'.$this->storagePath.'")', E_USER_ERROR);
        }
        if (!is_writeable($this->storagePath)) {
            trigger_error ($this->cn.$fncn.' : !is_writeable("'.$this->storagePath.'")', E_USER_ERROR);
        }
    }
    
    public function setStoragePath ($storagePath) {
        $this->storagePath = $storagePath;
        $this->checkStoragePath();
    }
    
    public function getStoragePath () {
        return $this->storagePath;
    }

    public function getHashAlgos() {
        return $this->hashAlgos;
    }
        
    public function setHashAlgo ($name) {
        foreach ($this->hashAlgos as $idx => $hn) { 
            if ($name===$hn) {
                $this->hashAlgo = $name;
                return true;
            }
        }
        return false;
    }
    
    public function fsRandomString ($length) {
        $seed = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $r = '';
        for ($i=0; $i<$length; $i++) {
            $r .= substr ($seed, rand(0,strlen($seed)), 1);
        };
        return $r;
    }
    
    public function getKey($cookieID, $username, $userPassword, $accountDetails) {    
        $fncn = 'getKey($cookieID)';
    
        $filePerms_ownerUser = 'rene'; 
        $filePerms_ownerGroup = 'www-data'; 
        $filePerms_perms_readonly = 0640;
        $filePerms_perms_publicWriteableExecutable = 0777; // note : these are the file permissions for PUBLICLY ACCESSIBLE FILES only!

        $fnKey = $this->storagePath.DIRECTORY_SEPARATOR.'key-'.$cookieID.'-'.$username.'.txt';
        //echo '<pre>'.$this->cn.$fncn.' : $fnKey = '.$fnKey.'</pre>'.PHP_EOL;
        //echo '<pre>'.$this->cn.$fncn.' : file_exists($fnKey) = '; var_dump(file_exists($fnKey)); echo '</pre>'.PHP_EOL;
        //echo '<pre>'.$this->cn.$fncn.' : is_readable($fnKey) = '; var_dump(is_readable($fnKey)); echo '</pre>'.PHP_EOL;
        if (!file_exists($fnKey) || !is_readable($fnKey)) {
            $key = sodium_crypto_secretbox_keygen();
            file_put_contents($fnKey, bin2hex($key));
            chmod ($fnKey, $filePerms_perms_publicWriteableExecutable);
            
            chown ($fnKey, $filePerms_ownerUser);
            chgrp ($fnKey, $filePerms_ownerGroup);
            chmod ($fnKey, $filePerms_perms_readonly);
            
            $fnAccountCredentials = $this->storagePath.DIRECTORY_SEPARATOR.encode_base64_url($this->fsRandomString(20)).'.ac.txt';
            $accountCredentials = [
                'username' => $username,
                'key' => encode_base64_url($key),
                'key2' => encode_base64_url($key2),
                'fnAccountCredentials' => $fnAccountCredentials,
                'fnAccountDetails' => $fnAccountDetails
            ];
            $jsonAccountCredentials = json_encode ($accountCredentials);
            
            $enc = $this->safeEncrypt($jsonAccountCredentials, $key); 
            //echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            file_put_contents ($fnAccountCredentials, $enc);
            chmod ($fnAccountCredentials, $filePerms_perms_publicWriteableExecutable);
            
            chown ($fnAccountCredentials, $filePerms_ownerUser);
            chgrp ($fnAccountCredentials, $filePerms_ownerGroup);
            chmod ($fnAccountCredentials, $filePerms_perms_readonly);
            
        } else {
            $key = hex2bin(file_get_contents($fnKey));
        }

        $files = getFilePathList($this->storagePath, false, '/.*\.ac\.txt/', null, array('file'), 1);
        //echo '<pre>$files = '; var_dump ($files); echo '</pre>'.PHP_EOL;
        foreach ($files as $idx => $acfn) {
            $enc = file_get_contents($acfn);
            $dec = $this->safeDecrypt($enc, $key); 
            if ($debug) { echo '<pre>';var_dump ($dec); echo '</pre><br/>'.PHP_EOL; };
            if ($dec !== false) {
                $accountCredentials = json_decode($dec,true);
                $key = decode_base64_url($accountCredentials['key']);
                $key2 = decode_base64_url($accountCredentials['key2']);
                $fnAccountCredentials = $accountCredentials['fnAccountCredentials'];
                $fnAccountDetails = $accountCredentials['fnAccountDetails'];
                break;
            }
        }
        if ($debug) {
            echo '<pre>';var_dump ($dec); echo '</pre><br/>'.PHP_EOL;
            echo '<pre>';var_dump ($accountCredentials); echo '</pre><br/>'.PHP_EOL;
            echo '<pre>';var_dump (is_null($fnAccountDetails)); echo '</pre><br/>'.PHP_EOL;
            echo '<pre>';var_dump ($fnAccountCredentials); echo '</pre><br/>'.PHP_EOL;
        }
        if ($dec===false) {
            $key2 = sodium_crypto_pwhash_scryptsalsa208sha256_str (
                $userPassword, 
                PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE, 
                PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
            );
            //echo '<pre>'.$this->cn.$fncn.' : sodium_crypto_pwhash_scryptsalsa208sha256_str() = '; var_dump ($key2); echo '</pre>'.PHP_EOL;
            //$hashingResult = sodium_crypto_pwhash_scryptsalsa208sha256_str_verify($key2, $userPassword);
            //echo '<pre>'.$this->cn.$fncn.' : sodium_crypto_pwhash_scryptsalsa208sha256_str_verify() = '; var_dump ($hashingResult); echo '</pre>'.PHP_EOL;

            
            $fnAccountCredentials = $this->storagePath.DIRECTORY_SEPARATOR.encode_base64_url($this->fsRandomString(20)).'.ac.txt';
            $fnAccountDetails = $this->storagePath.DIRECTORY_SEPARATOR.encode_base64_url($this->fsRandomString(20)).'.ad.txt';
            $accountCredentials = [
                'username' => $username,
                'key' => encode_base64_url($key),
                'key2' => encode_base64_url($key2),
                'fnAccountCredentials' => $fnAccountCredentials,
                'fnAccountDetails' => $fnAccountDetails
            ];
            $jsonAccountCredentials = json_encode ($accountCredentials);
            //echo '<pre>'.$this->cn.$fncn.' : $jsonAccountCredentials = '; var_dump($jsonAccountCredentials); var_dump(json_last_error_msg()); echo '</pre>';
            
            //Encrypt & Decrypt $acountCredentials
            $enc = $this->safeEncrypt($jsonAccountCredentials, $key); 
            //echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            file_put_contents ($fnAccountCredentials, $enc);
            chmod ($fnAccountCredentials, $filePerms_perms_publicWriteableExecutable);
            
            //chown ($fnAccountCredentials, $filePerms_ownerUser);
            chgrp ($fnAccountCredentials, $filePerms_ownerGroup);
            chmod ($fnAccountCredentials, $filePerms_perms_readonly);

            
            
            $jsonAccountDetails = json_encode($accountDetails);
            $enc = $this->safeEncrypt($jsonAccountDetails, $key); //generates random  encrypted string (Base64 related)
            //echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            file_put_contents ($fnAccountDetails, $enc);
            chmod ($fnAccountDetails, $filePerms_perms_publicWriteableExecutable);
            
            //chown ($fnAccountDetails, $filePerms_ownerUser);
            chgrp ($fnAccountDetails, $filePerms_ownerGroup);
            chmod ($fnAccountDetails, $filePerms_perms_readonly);
        
            $key2 = sodium_crypto_pwhash_scryptsalsa208sha256_str (
                $userPassword, 
                PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE, 
                PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
            );
            $accountCredentials = [
                'username' => $username,
                'key' => encode_base64_url($key),
                'key2' => encode_base64_url($key2),
                'fnAccountCredentials' => $fnAccountCredentials,
                'fnAccountDetails' => $fnAccountDetails
            ];
            $jsonAccountCredentials = json_encode ($accountCredentials);
            //echo '<pre>'.$this->cn.$fncn.' : $jsonAccountCredentials = '; var_dump($jsonAccountCredentials); var_dump(json_last_error_msg()); echo '</pre>';
            
            //Encrypt & Decrypt $acountCredentials
            $enc = $this->safeEncrypt($jsonAccountCredentials, $key); 
            //echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            file_put_contents ($fnAccountCredentials, $enc);
            chmod ($fnAccountCredentials, $filePerms_perms_publicWriteableExecutable);
            
            //chown ($fnAccountCredentials, $filePerms_ownerUser);
            chgrp ($fnAccountCredentials, $filePerms_ownerGroup);
            chmod ($fnAccountCredentials, $filePerms_perms_readonly);
            
            
            
        } else {
            
            $jsonAccountDetails = json_encode($accountDetails);
            $enc = $this->safeEncrypt($jsonAccountDetails, $key); //generates random  encrypted string (Base64 related)
            if ($debug) echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            if ($debug) { echo '<pre>'.$this->cn.$fncn.' : $fnAccountDetails = '; var_dump ($fnAccountDetails); echo '</pre>'.PHP_EOL; }
            if (!isset($fnAccountDetails) || $fnAccountDetails===null) $fnAccountDetails = $this->storagePath.DIRECTORY_SEPARATOR.encode_base64_url($this->fsRandomString(20)).'.ad.txt';
            if ($debug) echo '<pre>'.$this->cn.$fncn.' : $fnAccountDetails = '.$fnAccountDetails.'</pre>'.PHP_EOL; 
            file_put_contents ($fnAccountDetails, $enc);
            chmod ($fnAccountDetails, $filePerms_perms_publicWriteableExecutable);
            
            //chown ($fnAccountDetails, $filePerms_ownerUser);
            chgrp ($fnAccountDetails, $filePerms_ownerGroup);
            chmod ($fnAccountDetails, $filePerms_perms_readonly);
            
            $key2 = sodium_crypto_pwhash_scryptsalsa208sha256_str (
                $userPassword, 
                PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE, 
                PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
            );
            $accountCredentials = [
                'username' => $username,
                'key' => encode_base64_url($key),
                'key2' => encode_base64_url($key2),
                'fnAccountCredentials' => $fnAccountCredentials,
                'fnAccountDetails' => $fnAccountDetails
            ];
            $jsonAccountCredentials = json_encode ($accountCredentials);
            if ($debug) { echo '<pre>'.$this->cn.$fncn.' : $jsonAccountCredentials = '; var_dump($jsonAccountCredentials); var_dump(json_last_error_msg()); echo  '</pre>'; }
            
            //Encrypt & Decrypt $acountCredentials
            $enc = $this->safeEncrypt($jsonAccountCredentials, $key); 
            //echo '<pre>'.$this->cn.$fncn.' : $enc = '.$enc.'</pre>'.PHP_EOL;
            $ret = file_put_contents ($fnAccountCredentials, $enc);
            if ($debug) { echo '<pre>'.$this->cn.$fncn.' : $ret= '; var_dump($ret); echo '</pre>'.PHP_EOL; };
            chmod ($fnAccountCredentials, $filePerms_perms_publicWriteableExecutable);
            
            //chown ($fnAccountCredentials, $filePerms_ownerUser);
            chgrp ($fnAccountCredentials, $filePerms_ownerGroup);
            chmod ($fnAccountCredentials, $filePerms_perms_readonly);
        };
        return [
            'key' => $key, 
            'key2' => $key2,
            'fnAccountCredentials' => $fnAccountCredentials,
            'fnAccountDetails' => $fnAccountDetails
        ];
    }
    
    public function login($cookieID, $username, $userPassword, $accountDetails) {
        $fncn = 'login($cookieID, $username, $userPassword, $accountDetails)';
        $debug = false;
        
        $filePerms_ownerUser = 'rene'; 
        $filePerms_ownerGroup = 'www-data'; 
        $filePerms_perms_readonly = 0640;
        $filePerms_perms_publicWriteableExecutable = 0777; // note : these are the file permissions for PUBLICLY ACCESSIBLE FILES only!
        
        $keyFindResult = $this->getKey($cookieID, $username, $userPassword, $accountDetails);
        //echo '<pre>'.$this->cn.$fncn.' : $keyFindResult = '; var_dump ($keyFindResult); echo '</pre>'; 
        $key = $keyFindResult['key'];
        $fnAccountCredentials = $keyFindResult['fnAccountCredentials'];
        
        //$files = getFilePathList($this->storagePath, false, '/.*\.ac\.txt/', null, array('file'), 1);
        //echo '<pre>$files = '; var_dump ($files); echo '</pre>'.PHP_EOL;
        
        $enc = file_get_contents($fnAccountCredentials);
        $dec = $this->safeDecrypt($enc, $key); 
        //echo '<pre>'.$this->cn.$fncn.' : $dec = '; var_dump ($dec); echo '</pre><br/>'.PHP_EOL; 
        if ($dec !== false) {
            $accountCredentials = json_decode($dec,true);
            //echo '<pre>'.$this->cn.$fncn.' : $accountCredentials = '; var_dump ($accountCredentials); echo '</pre><br/>'.PHP_EOL; exit();
            $key = decode_base64_url($accountCredentials['key']);
            $key2 = decode_base64_url($accountCredentials['key2']);
            $fnac = $accountCredentials['fnAccountCredentials'];
            $fnad = $accountCredentials['fnAccountDetails'];
        }
    
        
        if ($dec===false) return false;
            
        $enc = file_get_contents($fnad);
        //$key = $this->getKey($cookieID, $username, $userPassword);
        $dec = $this->safeDecrypt($enc, $key); 
        if ($dec !== false) {
            $accountDetails = json_decode($dec,true);
        } else return false;
        
        sodium_memzero ($key);
        sodium_memzero ($fnac);
        sodium_memzero ($fnad);
        sodium_memzero ($cookieID);
        sodium_memzero ($username);
        sodium_memzero ($userPassword);
        
        return [
            'success' => true,
            'accountCredentials' => $accountCredentials,
            'accountDetails' => $accountDetails
        ];
    
    }
        
    
    /**
    * From : https://www.php.net/manual/en/intro.sodium.php
    * Encrypt a message
    *
    * @param string $message - message to encrypt
    * @param string $key - encryption key
    * @return string
    */
    public function safeEncrypt($message, $key) {
        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    /**
    * Decrypt a message
    *
    * @param string $encrypted - message encrypted with safeEncrypt()
    * @param string $key - encryption key
    * @return string
    */
    function safeDecrypt($encrypted, $key) {  
        $fncn = 'safeDecrypt($encrypted, $key)';
        $decoded = base64_decode($encrypted);
        if ($decoded === false) {
            //trigger_error ($this->cn.$fncn.' : The encoding failed', E_USER_WARNING);
            return false;
        }
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
            //trigger_error ($this->cn.$fncn.' : The message was truncated', E_USER_WARNING);
            return false;
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if ($plain === false) {
            //trigger_error ($this->cn.$fncn.' : The message was tampered with in transit', E_USER_ERROR);
            return false;            
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }
}
?>
