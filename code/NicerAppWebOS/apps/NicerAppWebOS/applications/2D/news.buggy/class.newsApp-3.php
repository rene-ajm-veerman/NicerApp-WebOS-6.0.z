<?php 
// Copyright (c) and All Right Reserved (r) 2018 by Rene AJM Veerman, Amsterdam, Netherlands.
// The License for this web-app "news" / "newsApp" is located at http://nicer.app/LICENSE.txt

require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms_publicWriteableExecutable;
    global $filePerms_perms_readonly;


require_once (dirname(__FILE__).'/sources-list.php');
require_once (dirname(__FILE__).'/functions.php');
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/3rd-party/geoLite2/vendor/autoload.php');
use GeoIp2\Database\Reader;


$prevLevel = 0; global $prevLevel;

class newsApp3_class {
    public $fs = array(); // factorySettings
    public $ds = array(); // dataSources
    public $d = array(); // data
    public $curlOps = array(); // cURL operations
    public static $prevLevel = 0;

    public $daysToKeepData = 30 * 3; // 3 administrative calendar months
    
    public $cdbConfig;
    public $cdb;

    public static function about () {
        $r = array(
            'name' => 'newsApp',
            'address' => 'https://nicer.app/news',
            'copyright' => 'Copyright (c) 2018-2022 by Rene AJM Veerman',
            'license' => 'https://nicer.app/LICENSE.txt',
            'firstCreated' => '2018',
            'lastModified' => '2022-05-30 12:05 CEST (Amsterdam.NL timezone, summertime)',
            'version' => '3.1.1'
        );
        return $r;
    }
    
	public function __construct ($factorySettings=null) {
        global $newsApp3_dataSources;
        global $naWebOS;
        $this->ds = $newsApp3_dataSources;
        $this->fs = $factorySettings;
        
        $debug = false;//$naDebugAll;
        

        $this->db = $naWebOS->dbsAdmin->findConnection('couchdb');
        $this->cdb = $this->db->cdb;

        $dbName = $this->db->dataSetName('app_2D_news__rss_items');
        $this->cdb->setDatabase($dbName, false);
    }
    
    public static function &dataSources () {
        return $this->ds;
    }
    
    public function checkIPrange() {
        $fncn = 'newsapp3_class->checkIPrange()';
        return true;
        global $naIP;
        global $naLAN;
        $libraryPath = dirname(__FILE__).'/../../../../../3rd-party/geoLite2/GeoLite2-Country.mmdb'; 
            // this file needs to be updated every Tuesday from https://dev.maxmind.com/geoip/geolite2-free-geolocation-data?lang=en
            // this is done using the following script from the linux commandline at your server :
            //      cd [...]/NicerAppWebOS/3rd-party/geoLite2
            //      php downloadDBs.php
        $fuckTheLaw = false;
        $allowed = true;
        
        $passIPrangeCheck = true;
        
        $reader = new Reader($libraryPath);
        try {
            $t = $reader->country($naIP);
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            $allowed = false;
        }

        if (!$allowed || is_null($t)) $passIPrangeCheck = false; // IP shielded, disallow service.
        
        /*
        ***** sent : 2023-03-21 04:37am CET (Amsterdam.NL time)
        ***** this section has become outdated now that China has decided to become a global broker of
        ***** diplomatic efforts.

        ---------------------------------------------------------------------
        // from : Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
        // to : Humanity
        // sent : 2022-02-16(Friday) +-23:00 to 2022-02-17(Saturday) 11:30
        // blocking all access from the (Middle-)Eastern Alliance's leading countries :
        / *
             https://www.facebook.com/gavanHoverswell/posts/pfbid0Pmat6KJA5UQQ8oAyBZVYzpgCc5L26oWZUR9hB7GZuj7TwL97qQ8ZY19GN43qZSMJl

             https://www.facebook.com/gavanHoverswell/posts/pfbid02C2DosQxziB5bbDDS8Kbri6QRKyJ1vmzupkknw9TscT5H9FS5CNUNdCBBu2ZYHQi2l

             https://www.facebook.com/gavanHoverswell/posts/pfbid02HkmQWY8jbP8oTeYDPD6iq5mahmtWYXp1SULWWMRCUSQKMZAzBRbkX9aAYLmaHFLel

             https://www.facebook.com/gavanHoverswell/posts/pfbid0MmAe2GkQSLtKQQdk2x9cdSpxP3C3dHhfJdzT9CvhMZiZC4VGBJfN5EKzWWDCGm92l
         * /
        if ($t->raw['country']['iso_code']==='CN') $passIPrangeCheck = false; // China
        if ($t->raw['country']['iso_code']==='IR') $passIPrangeCheck = false; // Iran (a new ally to China)
        if ($t->raw['country']['iso_code']==='PK') $passIPrangeCheck = false; // Pakistan (a new ally to China)
        if ($t->raw['country']['iso_code']==='RU') $passIPrangeCheck = false; // Russia (an old ally to China, and human rights violator in Ukraine (the right to die peacefully with your younger family nearby, and the right to live in peace))
        if ($t->raw['country']['iso_code']==='NK') $passIPrangeCheck = false; // North-Korea (an old ally to China)
        if ($t->raw['country']['iso_code']==='BY') $passIPrangeCheck = false; // Belarus (strong ally of Russia, and co-conspirator in the attrocities commited against Ukraine)

        // other potential terrorist-suplying nations.
        // for evidence, search at google.com for '<country-name> terrorism supporters'
        // also : i *really* don't want to educate terror groups and authoritarian regimes on how to defeat the West, Israel (which the Muslims should have *befriended* instead of fought, from the day it was created just after WW2), nor how to defeat it economically.
        if ($t->raw['country']['iso_code']==='AF') $passIPrangeCheck = false; // Afghanistan
        if ($t->raw['country']['iso_code']==='LY') $passIPrangeCheck = false; // Libya
        if ($t->raw['country']['iso_code']==='LB') $passIPrangeCheck = false; // Lebanon
        if ($t->raw['country']['iso_code']==='PS') $passIPrangeCheck = false; // Palestine
        if ($t->raw['country']['iso_code']==='SY') $passIPrangeCheck = false; // Syria
        if ($t->raw['country']['iso_code']==='ML') $passIPrangeCheck = false; // Mali
        */

        /*
        // from : Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
        // to : Humanity
        // sent : 2022-02-17(Saturday) +-08:30 to 2022-02-17(Saturday) 11:42 CEST (Amsterdam.NL's timezone)
        Saturday, 10:30CEST : look for 'checkIPrange' at https://github.com/NicerEnterprises/nicerapp/blob/main/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/class.newsApp-3.php for info on how these negotiations between western and (middle-)eastern civilian warriors are progressing, folks..

        it reads, as the latest status-quo between the western warriors and the (M-)E warriors :
        @2022-02-16(Fri) 14:40CEST - 2022-09-17(Fri) 10:26CEST
        i'll have to reconsidering this block list's active status. The fact that i'm keeping it on at all.
        my notes stored on facebook will help me do that.
        as will my Anglic, Divine and telepathic contacts and friends.
        see my https://tinyurl.com/telepathy-manual for more info.

        i can ofcourse, spend some of my own future wealth on the needy in developing countries, but we got needy right here.
        and our enemies, if they can even speak diplomatically and truthfully during negotiations, would go like : "wealth generated by western people chatting digitally amongst-themselves-only making profits in reportedly excess amounts and percentages off of the backs of people in the developing world!"

        well, i don't trust all reports in the newspapers or local rumor circuits.
        but my enemies are hereby informed of the fact, or reminded about how with just a little legitimate (white-hat) digital business-people skills[1], they can start a movement of moderate decency Islam than can work itself into national politics (by having an army that acts strictly defensively - there is zero pride or honor in prolonged war), and also in geo politics to help end the wars between the West and the (Middle-)Eastern Alliance and thus enable that blissful life that we've been promising our followers, by recognizing that moderate warriors have a sort of hero-duty, a debt of honor and future afterlives self-worth levels that any of them gets to enjoy.

        realize that tough stances during diplomacy or economic tensions/wars or real wars for that matter, will only get you and your adversaries further from turning that promise into reality. and this has been true for millenia already. especially the men and boys among each generation are in my view obligated to help end that suffering, but as the doctor says : it's impossible to bandage a gasping wound.
        and surgery here requires tactical and strategic strikes against the enemies. not to anger them, but to warn them of the possible consequences of attacking the foundations that we spent centuries building (our Western countries and all the buildings in them, each down to the plumbing and gas-lines in our streets, and our own populations {native and migrant properly mixed together} that inhabit *those* areas on the globe. our own areas.).

        Anger as Iran woman dies after morality police arrest
        Activists have urged those responsible for her "suspicious" death be brought to justice, though Tehran has denied responsibility.
        Local : 2022-09-17(Saturday) 03 : 50 : 13
        Remote : 2022-09-17(Saturday) 03 : 50 : 13
        Found via https://nicer .app/news-world-headlines

        [1] based pretty much on my source code and tutorials like they are available today on https://github.com/NicerEnterprises/NicerAppWebOS
        */

        if (
            $naLAN
            || $fuckTheLaw
        ) $passIPrangeCheck = true;
        
        
        return $passIPrangeCheck;
    }
    
    public function searchNewsItems($searchKeys) {
        global $naWebOS;
        $debug = false;
        
        $data = [];
        
        $dbName = $this->cdb->dataSetName('app__app.2D.news__rss_items');
        $this->cdb->setDatabase($dbName, false);
        
        $dateBeginStr = str_replace('.','0',urldecode($_GET['dateBegin']));
        $dateEndStr = str_replace('.','0',urldecode($_GET['dateEnd']));
        $dateBeginStr = str_replace ('GMT000', 'GMT+0000', $dateBeginStr);
        $dateEndStr = str_replace ('GMT000', 'GMT+0000', $dateEndStr);
        $dateBeginStr = str_replace ('GMT-01000', 'GMT-1000', $dateBeginStr);
        $dateEndStr = str_replace ('GMT-01000', 'GMT-1000', $dateEndStr);
        $dateBegin = new DateTime($dateBeginStr);
        //echo $_GET['dateBegin'].' to '.$_GET['dateEnd'].'<br/>'.PHP_EOL;
        $dateScanning = new DateTime($dateBeginStr);
        $dateEnd = new DateTime($dateEndStr);
        //$dateEnd = $dateEnd->add (new DateInterval('PT2M'));
        
        $dateBegin->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $dateScanning->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $dateEnd->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        
        
        $dateBegin = $dateBegin->format('U');
        $dateEnd = $dateEnd->format('U');
        
        $startkey = $dateBegin;//[ intval(date('Y', $dateBegin)), intval(date('m', $dateBegin)), intval(date('d', $dateBegin)), intval(date('H', $dateBegin)), intval(date('i', $dateBegin)), intval(date('s', $dateBegin)) ];
        $endkey = $dateEnd;//[ intval(date('Y', $dateEnd)), intval(date('m', $dateEnd)), intval(date('d', $dateEnd)), intval(date('H', $dateEnd)), intval(date('i', $dateEnd)), intval(date('s', $dateEnd)) ];
        
        $searchKeys = str_replace('%20', ' ', $searchKeys);
        $searchKeys = explode (' ', $searchKeys);
        
        $section = str_replace ('__', '/', $_REQUEST['section']);
        $section = str_replace ('_', ' ', $section);
        $section = '/'.$section;
        
        
        if (intval($_REQUEST['loads'])===1) {
            $searchPubDate = [
                '$or' => [
                    [
                        '$gt' => intval($startkey),
                        '$lt' => intval($endkey)
                    ],
                    [
                        'pdy' => 1970
                    ]
                ]
            ];
        } else {
            $searchPubDate = [
                '$gt' => intval($startkey),
                '$lt' => intval($endkey)
            ];
        }
        
        // search the specified date-time range ($startkey to $endkey) for all possible combinations of the specified $searchKeys.
        $findCommand = array (
            'selector' => array (
                'p' => [
                    '$regex' => '^'.$section.'.*'
                ],
                'pd' => $searchPubDate,
            ),
            'use_index' => 'primaryIndex',
            'fields' => array ('_id', '_rev', 't', 'de', 'm', 'am', 'pd', 'pubDate', 'da', 'dd', 'c', 'cc' )
        );

        
        $or = [];        
        $and = [];
        for ($tIdx = 0; $tIdx < count($searchKeys); $tIdx++) $and[] = [ 't' => $this->searchKey($searchKeys[$tIdx]) ];
        $or[] = [ '$and' => $and ];
        $and = [];
        for ($deIdx = 0; $deIdx < count($searchKeys); $deIdx++) {
            for ($tIdx = 0; $tIdx < $deIdx; $tIdx++) $and[] = [ 't' => $this->searchKey($searchKeys[$tIdx]) ];
            for ($deIdx2 = $deIdx; $deIdx2 < count($searchKeys); $deIdx2++) $and[] = [ 'de' => $this->searchKey($searchKeys[$deIdx2]) ];
            $or[] = [ '$and' => $and ];
            $and = [];
        }
        
        $findCommand['selector']['$or'] = $or;
        
        global $naWebOS;
        $arr2 = [];
        $done = false;
        $bookmark = null;
        while (!$done) {
            $go = true;
            try {
                $dbName = $naWebOS->dbs->findConnection('couchdb')->dataSetName('app__app_2d_news__rss_items');
                $naWebOS->dbs->findConnection('couchdb')->cdb->setDatabase ($dbName, false);
                if (!is_null($bookmark)) {
                    $findCommand['bookmark'] = $bookmark;
                    unset ($findCommand['selector']);
                }
                $call = $naWebOS->dbs->findConnection('couchdb')->cdb->find ($findCommand);
                if ($debug) { echo '$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).PHP_EOL.'$call='.json_encode($call,JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL; }
            } catch (Exception $e) {
                //echo '$findCommand : Exception->getMessage()='.$e->getMessage().PHP_EOL;
                $go = false;
                $done = true;
            }
            if ($go) {
                if (!is_null($call) && !is_null($call->body) && count($call->body->docs)===0) $done = true;
                if (!$done && !is_null($call) && !is_null($call->body)) $bookmark = $call->body->bookmark; else $bookmark = null;
                //var_dump ($call->body); die();
                if (!$done) foreach ($call->body->docs as $idx => $doc) {
                    $arr2[] = $doc;
                }                
            }
        }
        
        
        echo json_encode ($arr2);
    }

    public function searchKey ($sk) {
        if (substr($sk, 0, 1)=='-') {
            $ret = [ '$neq' => [ '$regex' => '(?i)'.$sk ] ];
        } else {
            $ret = [ '$regex' => '(?i)'.$sk ];
        }
        return $ret;
    }
    
    
    public static function sortHoursMinutes () {
        $date = new DateTime();
        $dateStr = $date->format('Y/n/j');
        $h = &chaseToPath ($this->d, $dateStr);
        ksort ($h);
        //var_dump ($h);
        foreach ($h as $m=>&$s) {
            ksort ($m);
        }
    }
    
    public static function sortResultsByNumber ($a, $b) {
        return $b - $a;
    }
    
    public static function sortResultsByDateTime ($a, $b) {
        if (
            !array_key_exists('date',$b)
            || !array_key_exists('date',$a)
            || !is_numeric($b['date']) 
            || !is_numeric($a['date'])
            
        ) {
        //echo'<pre>'; var_dump ($a); var_dump ($b); echo '</pre>'; 
        }
        return $b['date'] - $a['date']; // REVERSE DATE SORT -> NEWEST ITEMS FIRST
    }
    
    
    public function writeOutMenuIfNeeded () {
    
        unlink (dirname(__FILE__).'/mainmenu.php');
        if (!file_exists(dirname(__FILE__).'/mainmenu.php')) {
            $ctRSSlist = filectime(dirname(__FILE__).'/sources-list.php');
            $ctMenuFile = 0;
        } else {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $ctRSSlist = filectime(dirname(__FILE__).'\\sources-list.php');
                $ctMenuFile = filectime(dirname(__FILE__).'\\mainmenu.php');
            } else {
                $ctRSSlist = filectime(dirname(__FILE__).'/sources-list.php');
                $ctMenuFile = filectime(dirname(__FILE__).'/mainmenu.php');
            };
        }
		
        if ($ctMenuFile < $ctRSSlist) {
            //$htmlMenu = '<ul style="display:none;">'.PHP_EOL."\t".'<li><a href="#">News</a>'.PHP_EOL."\t".'<ul>'.PHP_EOL;
            $htmlMenu = '<ul style="display:none;"><li><a href="/news">News</a><ul>'.PHP_EOL;
            $rewriteRules = '';
            $reverseRewriteRulesCSVlines = '';
            $keyCount = 0;
            $valueCount = 0;
            $params = array (
                //'d' => &$this->d,
                'html' => &$htmlMenu,
                'rewriteRules' => &$rewriteRules,
                'reverseRewriteRulesCSVlines' => &$reverseRewriteRulesCSVlines,
                'prevLevel' => 0,
                'keyCount' => &$keyCount,
                'valueCount' => &$valueCount
            );
        
            walkArray ($this->ds['RSS_list'], 'newsapp3_class::writeOutMenuIfNeeded_walk_key', 'newsapp3_class::writeOutMenuIfNeeded_walk_value', false, $params);
			global $prevLevel;
			$prevLevel = 0;
            
            $htmlMenu .= "\t".'</ul>'.PHP_EOL.'</li>'.PHP_EOL.'</ul>'.PHP_EOL;
            
            global $filePerms_ownerUser;
            global $filePerms_ownerGroup;
            global $filePerms_perms_publicWriteableExecutable;
            global $filePerms_perms_readonly;
            $fn = dirname(__FILE__).'/mainmenu.php';
            file_put_contents ($fn, $htmlMenu);
            $x = chgrp ($fn, $filePerms_ownerGroup);
            $y = chown ($fn, $filePerms_ownerUser);
            $z = chmod ($fn, $filePerms_perms_publicWriteableExecutable);

            $fn = dirname(__FILE__).'/mainmenu.rewriteRules.htaccess.txt';
            file_put_contents ($fn, $params['rewriteRules']);
            $x = chgrp ($fn, $filePerms_ownerGroup);
            $y = chown ($fn, $filePerms_ownerUser);
            $z = chmod ($fn, $filePerms_perms_publicWriteableExecutable);

            $fn = dirname(__FILE__).'/mainmenu.reverseRewriteRulesCSVlines.csv';
            file_put_contents ($fn, $params['reverseRewriteRulesCSVlines']);
            $x = chgrp ($fn, $filePerms_ownerGroup);
            $y = chown ($fn, $filePerms_ownerUser);
            $z = chmod ($fn, $filePerms_perms_publicWriteableExecutable);

            $fn = dirname(__FILE__).'/mainmenu.keyCount.txt';
            file_put_contents ($fn, $params['keyCount']);
            $x = chgrp ($fn, $filePerms_ownerGroup);
            $y = chown ($fn, $filePerms_ownerUser);
            $z = chmod ($fn, $filePerms_perms_publicWriteableExecutable);

            $fn = dirname(__FILE__).'/mainmenu.valueCount.txt';
            file_put_contents ($fn, $params['valueCount']);
            $x = chgrp ($fn, $filePerms_ownerGroup);
            $y = chown ($fn, $filePerms_ownerUser);
            $z = chmod ($fn, $filePerms_perms_publicWriteableExecutable);
        }
       $exc = 'php '.realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/maintenance.scripts/htaccess.build.php';
       exec ($exc, $output, $result);
       $dbg = [
        'exc' => $exc,
        'output' => $output,
        'result' => $result
        ];
        //var_dump ($dbg);
    }
    
    public static function writeOutMenuIfNeeded_walk_key ($cd) {
        if (is_array($cd['v'])) {
            $path = $cd['path'].'/'.$cd['k'];
            $path = substr($path,1);
            $path2 = str_replace (' ', '_', $path);
            $path2a = str_replace('/','__', $path2);
            $indent = str_pad ('', $cd['level']+2, "\t");

            global $prevLevel;

            //$x = 'KEY : '.$cd['path'].' - '.$cd['k'].' - '.$prevLevel.' - '.$cd['params']['prevLevel'].' - '.($cd['level']); var_dump ($x);

            //[1]
            if ($prevLevel > $cd['level'] ) {
                for ($i=$prevLevel; $i>$cd['level'] ; $i--) {
                    $cd['params']['html'].= $indent.'</ul>'.PHP_EOL;
                }
            }

            $json = '{"/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news":{"section":"'.$path2a.'"}}';
            $href = '/apps/'.encode_base64_url($json);
            $href = '/'.foldMenuKey($cd['k']);
            $cd['params']['html'] .= $indent.'<li><a href="'.$href.'">'.$cd['k'].'</a>';
            $cd['params']['keyCount'] = $cd['params']['keyCount'] + 1;

            $doUL = false;
            if (is_array($cd['v'])) {
                $doUL = true;
                $cd['level']++;
            };

            if ($doUL) {
                $cd['params']['html'] .= PHP_EOL.$indent.'<ul>'.PHP_EOL;//<li><a href="'.$href.'">'.$cd['k'].'</a></li><ul>'.PHP_EOL;
            } else {
                $cd['params']['html'] .= '</li>'.PHP_EOL;
            }

            $url = '^'.foldMenuKey($cd['k']).'$';
            $lineEnding = ' [N]';
            $line =
                'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
                .'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
                .'RewriteCond %{REQUEST_METHOD} ^(GET)$'.PHP_EOL
                .'RewriteCond %{HTTP:X-Requested-With} XMLHttpRequest'.PHP_EOL
                .'RewriteRule '.$url.' /apps-content/'.encode_base64_url($json).$lineEnding.PHP_EOL.PHP_EOL

                .'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
                .'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
                .'RewriteCond %{REQUEST_METHOD} ^(GET)$'.PHP_EOL
                .'RewriteRule '.$url.' /apps/'.encode_base64_url($json).$lineEnding.PHP_EOL.PHP_EOL.PHP_EOL;
            $cd['params']['rewriteRules'] .= $line;
            $cd['params']['reverseRewriteRulesCSVlines'] .= '"'.$url.'","'.encode_base64_url($json).'"'.PHP_EOL;
            //{ var_dump ($cd); }

            $prevLevel = $cd['level'];
        }
    }

    public function foldMenuKey ($k) {
        $i = str_replace(' - ', '.',$k);
        $i = str_replace(' ', '-',$i);
        $r = strtolower('news-'.$i);
        return $r;
    }

    public function unfoldMenuKey ($fk) {
        $i = str_replace('news-','',$fk);
        $i = str_replace('-',' ', $i);
        $i = str_replace('.',' - ', $i);
        return $i;
    }
    
    public static function writeOutMenuIfNeeded_walk_value ($cd) {
        /*
        $cd['params']['valueCount'] = $cd['params']['valueCount'] + 1;
        $path = substr($cd['path'],1);
        //echo substr($path.'/'.$cd['k'],1).PHP_EOL; die();
        $path2 = str_replace (' ', '_', $path);
        $path2a = str_replace('/','__', $path2);
        if ($cd['level'] > 0) $indent = str_pad ('', $cd['level']+3, "\t"); else $indent='';
        $json = '{"/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news":{"section":"'.$path2a.'"}}';
        //$href = '/apps-content/'.encode_base64_url($json);
        $href = '/'.foldMenuKey($cd['k']);
        $cd['params']['html'] .= $indent.'<li><a href="'.$href.'">'.$cd['k'].'</a></li>'.PHP_EOL;

            $url = '^'.foldMenuKey($cd['k']).'$';
            $lineEnding = ' [N]';
            $line =
                'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
                .'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
                .'RewriteCond %{REQUEST_METHOD} ^(GET)$'.PHP_EOL
                .'RewriteCond %{HTTP:X-Requested-With} XMLHttpRequest'.PHP_EOL
                .'RewriteRule '.$url.' /apps-content/'.encode_base64_url($json).$lineEnding.PHP_EOL.PHP_EOL

                .'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
                .'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
                .'RewriteCond %{REQUEST_METHOD} ^(GET)$'.PHP_EOL
                .'RewriteRule '.$url.' /apps/'.encode_base64_url($json).$lineEnding.PHP_EOL.PHP_EOL.PHP_EOL;
        $cd['params']['rewriteRules'] .= $line;
        //var_dump ($cd);
        global $prevLevel;
        //$x = 'VALUE : '.$cd['params']['path'].' - '.$cd['params']['k'].' - '.$cd['params']['prevLevel'].' - '.$cd['params']['level'].' - '.$cd['params']['rewriteRules']; var_dump ($x);
        */
    }

    public function fetch () {
        $fncn = 'newsapp3_class->fetch()';
        
        $d = new DateTime();
        $newCount = 0;
        $this->d = [];
        $params = array (
            'd' => &$this->d,
            'date' => $d->format('Y/n/j/H/i/s'), 
            'this' => &$this,
            'newCount' => &$newCount
        );

        set_time_limit (60);
        startDuration ('rewriteMenuFiles');
        $this->writeOutMenuIfNeeded();
        $waitTime = getDuration('rewriteMenuFiles');
        
        $logstr = $fncn.' : Rewriting menu files took '.$waitTime.' seconds.'.PHP_EOL;
        echo $logstr; error_log ($logstr);
        
        
        // prep cURL operations
        walkArray ($this->ds['RSS_list'], null, 'newsApp3_class::fetch_walk_value', false, $params);
        
        /*echo 'pre curl : ';
        foreach ($this->curlOps as $idx => $curlOp) {
            echo $idx.' - '.$curlOp['cd']['path'].'/'.$curlOp['cd']['k'];
            echo PHP_EOL;
        }*/
        
        // execute cURL operations asynchronously
        $mh = curl_multi_init();
        $params['mh'] = $mh;
        curl_multi_setopt ($mh, CURLMOPT_MAX_HOST_CONNECTIONS, 2); // Pass a number that specifies the maximum number of connections to a single host. Recommended you dont push this above 2.
        curl_multi_setopt($mh, CURLMOPT_PIPELINING, /* depracated : CURLPIPE_HTTP1  | */CURLPIPE_MULTIPLEX); // use whatever fancyness the HTTP layer of the operating system supports
        
        foreach ($this->curlOps as $idx => $curlOp) {
            curl_multi_add_handle ($mh, $curlOp['ch']);
        }
        
        // execute all queries simultaneously, and continue when all are complete
        $active = null;
        $status = null;

        $timeBegin = time();
        $secondsBetweenStatusReports = 1;
        $timeCurrent = $timeBegin;

        do {
            //ob_start();
            $status = curl_multi_exec($mh, $active);
            //ob_end_clean();

            if ( time() > $timeCurrent + $secondsBetweenStatusReports ) {
                $queuedMsgs = null;
                $cmir = curl_multi_info_read ($mh, $queuedMsgs);
                //var_dump ($cmir); var_dump ($queuedMsgs); echo PHP_EOL;
                $timeCurrent = time();
            }
            
            // windows server bugfix before the bug even crawl in.
            if (curl_multi_select($mh) == -1) {
                    usleep(100);
            }
        } while ($active && $status == CURLM_OK);

        $duration = getDuration('fetch');
        $fetchIntervalInMinutes = 5;
        $waitTime = round((60*$fetchIntervalInMinutes)-$duration);
        $minutes = floor($waitTime/60);
        $secs = $waitTime - ($minutes * 60);
        $minutesSpent = floor($duration/60);
        $secondsSpent = $duration - ($minutesSpent * 60);
        
        $logstr = $fncn.' : Fetching news for '.file_get_contents(dirname(__FILE__).'/mainmenu.valueCount.txt').' RSS pages, '.file_get_contents(dirname(__FILE__).'/mainmenu.keyCount.txt').' menu-items, took '.$minutesSpent.' minutes, '.$secondsSpent.' seconds; '.$newCount.' new items.'.PHP_EOL;
        echo $logstr; error_log ($logstr);
        
        startDuration('processing');

        
        // all of our requests are done, we can now process the results
        foreach ($this->curlOps as $idx => $curlOp) {
            //if ($idx > 4) break;

            if (curl_errno($curlOp['ch'])) {
                //$responses[$key] = ['data' => null, 'info' => null, 'error' => curl_error($curlOp['ch'])];
                $curlOp['error'] = curl_error($curlOp['ch']);
                $msg = 'CURL operation (path='.$curlOp['cd']['path'].', url='.$curlOp['url'].') ERROR : '.$curlOp['error'];
                echo $msg.PHP_EOL;
                //trigger_error ($msg, E_USER_WARNING);
            } else {
                // save successful response
                $curlOp['rss'] = curl_multi_getcontent ($curlOp['ch']);
                $curlOp['info'] = curl_getinfo($curlOp['ch']);
                //echo 'CURL operation (path='.$curlOp['cd']['path'].', url='.$curlOp['url'].', info='.json_encode($curlOp['info']).') '.strlen($curlOp['rss']).' bytes retrieved'.PHP_EOL;
                if (strlen($curlOp['rss'])===0) echo 'CURL operation (path='.$curlOp['cd']['path'].', url='.$curlOp['url'].') '.strlen($curlOp['rss']).' bytes retrieved'.PHP_EOL;
                $this->fetch_processCurlResults ($curlOp);
            }

            curl_multi_remove_handle($mh, $curlOp['ch']);
            curl_close ($curlOp['ch']);
            //echo 'pre fetch_pcr : '.$curlOp['cd']['path'].'/'.$curlOp['cd']['k'].PHP_EOL;
        }
        curl_multi_close($mh);
        
        // prep for next cURL operations
        $this->curlOps = array();
        
        return $newCount;
    }

    public static function fetch_walk_value ($cd) {
        global $naWebOS;
        global $naIP;
        $url = $cd['v'];
        $path = $cd['path'].'/'.$cd['k']; 
        
        $curlOp = array(
            'ch' => curl_init(),
            'url' => $url,
            'cd' => array (
                'path' => $cd['path'],
                'k' => $cd['k'],
                'v' => &$cd['v'],
                'params' => $cd['params'],
                'level' => $cd['level']
            )
        );
        curl_setopt ($curlOp['ch'], CURLOPT_URL, $url);
        //curl_setopt ($curlOp['ch'], CURLOPT_HTTPHEADER, 0);//$headers);
        curl_setopt ($curlOp['ch'], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curlOp['ch'], CURLOPT_FOLLOWLOCATION, true);
        curl_setopt ($curlOp['ch'], CURLOPT_TIMEOUT, 10);
        
        $cd['params']['this']->curlOps[] = $curlOp;
    }
    
    public function fetch_processCurlResults ($curlOp) {
        $cd = $curlOp['cd'];
        $rss = $curlOp['rss'];
        $url = $curlOp['url'];

        $nc1 = $cd['params']['newCount'];
        
        $items = $cd['params']['this']->processItems ($cd, $rss, $url, $cd['path'], $cd['k'], $cd['level']);
        //var_dump ($rss);
        //var_dump ($items);
        
        $nc2 = $cd['params']['newCount'];
        
        if (is_array($items) and count($items)>0) {
            //$d = &chaseToPath($cd['params']['d'], $cd['params']['date'].$cd['path'].'/'.$cd['k'], true);
            $ni = ($nc2-$nc1)<2 ? '1 new item' : ($nc2-$nc1).' new items';
            //echo 'Processed : '.$cd['params']['date'].$cd['path'].'/'.$cd['k'].' ('.$ni.').<br/>'.PHP_EOL;
            
            $cd['params']['d'] = array_merge ($cd['params']['d'], $items);

            /*
            $fn = $cd['params']['this']->urlToFilename($url);
            $fnDetails = dirname(__FILE__).'/newsItems/settings/'.$fn;
            createDirectoryStructure (dirname($fnDetails));
            
            $urlDetails = $cd['params']['this']->processURL($cd, $rss, $url);
            if ($urlDetails!==false) file_put_contents ($fnDetails, json_encode($urlDetails));
            */
        }
    }
    
    public static function processURL ($cd, $rss, $rssURL) {
        $r = array();
        $anythingAdded = false;

        preg_match_all ('|<lastBuildDate>(.*)</lastBuildDate>|msU', $rss, $matches2);
        if (array_key_exists(1,$matches2) && array_key_exists(0,$matches2[1])) {
            $r['lastBuildDate'] = $matches2[1][0];
            $anythingAdded = true;
        }
        
        preg_match_all ('|<.*updatePeriod>(.*)</.*updatePeriod>|msU', $rss, $matches2);
        if (array_key_exists(1,$matches2) && array_key_exists(0,$matches2[1])) {
            $r['updatePeriod'] = $matches2[1][0];
            $anythingAdded = true;
        }

        preg_match_all ('|<.*updateFrequency>(.*)</.*updateFrequency>|msU', $rss, $matches2);
        if (array_key_exists(1,$matches2) && array_key_exists(0,$matches2[1])) {
            $r['updateFrequency'] = $matches2[1][0];
            $anythingAdded = true;
        }
        
        if ($anythingAdded) {
            return $r;
        } else {
            return false;
        }
    }
    
    public static function processItems ($cd, $rss, $rssURL, $path, $k, $level) {
        //preg_match_all ('|<pubDate>(.*)</pubDate>|msU', $rss, $matches2);
        //$rssURL_pubDate = $matches2[1][0];
        $debug = false;
        
        preg_match_all ('|<item>(.*)</item>|msU', $rss, $matches);
        //file_put_contents (dirname(__FILE__).'/matches.rss.txt', json_encode($matches, JSON_PRETTY_PRINT));
        //echo '<pre>'; echo $rssURL.'<br/>'; var_dump (array_key_exists(0, $matches)); var_dump ($matches); echo '</pre>';
        $r = array();
        if (array_key_exists(0, $matches)) {
            foreach ($matches[0] as $idx => $item) {
                //file_put_contents (dirname(__FILE__).'/item.rss.txt', json_encode($item,JSON_PRETTY_PRINT));
                $item = $cd['params']['this']->processItem ($cd, $item, $rss, $rssURL, $path, $level);
                //file_put_contents (dirname(__FILE__).'/item2.rss.txt', json_encode($item,JSON_PRETTY_PRINT));
                // $item['rssPubDate'] = $rssURL_pubDate; often completely unreliable!
                
                    $add = ($item !== false);
                    
                    // if the item is not found in the database, put it in the database.
                    if ($add) {
                        if ($debug) { echo '<pre>'; var_dump ($item); echo '</pre>'; }
                        $cd['params']['newCount']++;
                        $r[] = $item;
                    } else {
                        if ($debug) echo 'Found '.$path.'/'.$k.' --> '.$item['u'].' --> not adding.'.PHP_EOL;
                    }
                //}
            }
        }
        return $r; 
    }

    public static function processItem ($cd, $item, $rss, $rssURL, $path, $level) {
        $debug = false;

        $item = $cd['params']['this']->escapeCData($item);
        if (strpos($item,'&lt;')!==false) $item = html_entity_decode($item);
        
        $pubDate = $cd['params']['this']->getField ('|<pubDate>(.*)</pubDate>|msU', $item);
        
        // stage 1 - cast a wide net
        // gather all the data you can possibly gather, even if it does mean more CPU-expensive regular expression
        // searches
        // any extra RSS data that might be found for new news sources needs to be extracted here.
        $desc = mb_convert_encoding ( $cd['params']['this']->getField ('|<description>(.*)</description>|msU', $item), 'UTF-8', 'UTF-8');
        /* TOO STRICT! :
        if (is_string($desc) && $desc!=='' && strpos($desc,'<')!==false) {
            mb_detect_order("ASCII,UTF-8,ISO-8859-1,windows-1252,iso-8859-15");
            libxml_use_internal_errors(true);
            libxml_clear_errors();
            if (!empty($desc)) {
                    if (empty($encod))
                            $encod  = mb_detect_encoding($desc);
                    $headpos        = mb_strpos($desc,'<head>');
                    if (FALSE=== $headpos)
                            $headpos= mb_strpos($desc,'<HEAD>');
                    if (FALSE!== $headpos) {
                            $headpos+=6;
                            $desc = mb_substr($desc,0,$headpos) . '<meta http-equiv="Content-Type" content="text/html; charset='.$encod.'">' .mb_substr($desc,$headpos);
                    }
                    $desc=mb_convert_encoding($desc, 'HTML-ENTITIES', $encod);
            }
            $xml = simplexml_load_string($desc);
            $dom = New DOMDocument();
            $dom->loadHTML($desc);
            if (count(libxml_get_errors())>0) {
                $dbg = [
                    'desc' => $desc,
                    'libxml_get_errors' => libxml_get_errors()
                ];
                var_dump ($dbg);
                return false;
            }
        }
        */

        $desc2 = mb_convert_encoding ( $cd['params']['this']->getField ('|<content:encoded>(.*)</content:encoded>|msU', $item), 'UTF-8', 'UTF-8');
        /*
        if (is_string($desc2) && $desc2!=='' && strpos($desc2,'<')!==false) {
            mb_detect_order("ASCII,UTF-8,ISO-8859-1,windows-1252,iso-8859-15");
            libxml_use_internal_errors(true);
            libxml_clear_errors();
            if (!empty($desc)) {
                    if (empty($encod))
                            $encod  = mb_detect_encoding($desc2);
                    $headpos        = mb_strpos($desc2,'<head>');
                    if (FALSE=== $headpos)
                            $headpos= mb_strpos($desc2,'<HEAD>');
                    if (FALSE!== $headpos) {
                            $headpos+=6;
                            $desc2 = mb_substr($desc2,0,$headpos) . '<meta http-equiv="Content-Type" content="text/html; charset='.$encod.'">' .mb_substr($desc2,$headpos);
                    }
                    $desc2 = mb_convert_encoding($desc2, 'HTML-ENTITIES', $encod);
            }
            $xml = simplexml_load_string($desc2);
            $dom = New DOMDocument();
            $dom->loadHTML($desc2);
            if (count(libxml_get_errors())>0) {
                $dbg = [
                    'desc2' => $desc2,
                    'libxml_get_errors' => libxml_get_errors()
                ];
                var_dump ($dbg);
                return false;
            }
        }*/

        $newItem = array (
            '_id' => $cd['params']['this']->getField ('|<url>(.*)</url>|msU', $item),
            'link' => $cd['params']['this']->getField ('|<link>(.*)</link>|msU', $item),
            'title' => mb_convert_encoding ( $cd['params']['this']->getField ('|<title>(.*)</title>|msU', $item), 'UTF-8', 'UTF-8'),
            'desc' => $desc,
            'desc2' => $desc2,
            'c' => $path,
            'date' => $pubDate,
            'level' => $level,
            'media' => mb_convert_encoding ( $cd['params']['this']->getField ('|<media:group>(.*)</media:group>|msU', $item, 0), 'UTF-8', 'UTF-8'),
            'commentRSS' => mb_convert_encoding ( $cd['params']['this']->getField ('|<wfw:commentRss>(.*)</wfw:commentRss>|msU', $item), 'UTF-8', 'UTF-8'),
            'commentsCount' => $cd['params']['this']->getField ('|<slash:comments>(.*)</slash:comments>|msU', $item)
        );

        
        
        // stage 2 - customizations
        $media = null;
        
        if (is_string($newItem['_id'])) {
            $url = $newItem['_id'];
        } else if (is_string($newItem['link'])) {
            $url = $newItem['link'];
        } else {
            //echo $item;
            //var_dump ($newItem);
            return false;
        }
        
        //if ($newItem['media']!=='') $debug = true;
        //if ($rssURL == 'https://news.google.com/news/rss/headlines/section/topic/WORLD?ned=us&hl=en&gl=US') $debug=true;
        //if ($rssURL=='http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/front_page/rss.xml') $debug=true;
        
        if ($debug) var_dump ($newItem['media']);
        
        
        
        if ($newItem['media']!=='') {
            $media = $cd['params']['this']->getField ('|<media:content .*>(.*)</media:content>|msU', $item);
            if ($debug) { echo '1 - '; var_dump ($media); };
            if (is_string($media) && $media!=='') {
                $media = array (
                    $media
                );
            };
            if (!is_array($media)) {
                $media = null;
            }
            
            if (is_null($media)) {
                $media = $cd['params']['this']->getField('|<media(.*)/>|msU', $item, 0);
                if ($debug)  { echo '2a - '; var_dump ($media); };
                if (is_string($media) && $media!=='') {
                    $media = array (
                        $media
                    );
                };
                if (!is_array($media)) {
                    $media = null;
                }
            } 
            
            if (is_null($media)) {
                $media = $cd['params']['this']->getField('|<enclosure.*url=".*".*/>|msU', $item, 0);
                if ($debug)  { echo '3 - '; var_dump ($media); }
                if (is_string($media) && $media!=='') {
                    $media = array (
                        $media
                    );
                }; 
                if (!is_array($media)) {
                    $media = null;
                }
            }
        }
        
        
        $newMedia = array();
        $allMedia = array();
        if (is_array($media)) {
            $minWidth = null;
            $m1 = null;
            foreach ($media as $idx => $mediaTag) {
                if ($debug)  { echo '4a - '; var_dump ($mediaTag); };
                
                // [1] normally you'd use longer keynames (u, w, h, etc) 
                // but since the database is going to grow huge and needs to be searched through in RAM memory,
                // we're using short keynames.
                $parts = array (
                    'u' => $cd['params']['this']->getField('|url="(.*)"|msU', $mediaTag),
                    'w' => $cd['params']['this']->getField('|width="(.*)"|msU', $mediaTag),
                    'h' => $cd['params']['this']->getField('|height="(.*)"|msU', $mediaTag),
                    'm' => $cd['params']['this']->getField('|medium="(.*)"|msU', $mediaTag),
                    't' => strpos($mediaTag,':thumbnail')!==false,
                    'c' => strpos($mediaTag,':content')!==false
                );
                
                if ($parts['u']===false) {
                    continue;
                };
                
                $width = (
                    is_numeric($parts['w'])
                    ? (int)$parts['w']
                    : 400
                );
                if (is_null($minWidth)) $minWidth = $width;
                if (!array_key_exists(0,$newMedia)) $newMedia[0] = $parts;
                if ($width < $minWidth && $width > 400) {
                    if ($debug) { echo '4b - '; var_dump ($width); var_dump ($minWidth); }; 
                    $minWidth = $width;
                    $newMedia[0] = $parts;
                }
                
                //$parts['img'] = '<img src="'.$parts['url'].'" style="width:'.$parts['width'].'px;height:'.$parts['height'].'px;"/>';
                //$parts['img'] = '<img src="'.$parts['url'].'" style="width:256px;height:144px;"/>';
                $allMedia[$idx] = $parts;
            }
            
            
            /*
            $desc = $newItem['desc'];
            if (strpos($desc,'<table')!==false) {
                $desc = '<table cellpadding="2" cellspacing="3">';
                $desc.= '<tr><td>'.html_entity_decode($newItem['desc']).'</td></tr>';
                $desc.= '<tr><td class="newsApp__item__mediaMultiple"></td></tr>';
                $desc.= '</table>';
            } else {
                $desc = '<table cellpadding="2" cellspacing="3">';
                $desc.= '<tr>';
                $desc.= '<td class="newsApp__item__mediaSingle"></td>';
                $desc.= '<td>'.html_entity_decode($newItem['desc']).'</td>';
                $desc.= '</tr></table>';
            }
            */
        };
        
        //if (strpos($rssURL, 'news.google.com')!==false) {
            if ($debug) { echo '4 - '; var_dump ($newMedia); }
            //exit();
        //};
        if (is_string($newItem['desc2']) && $newItem['desc2']!=='') {
            $desc = $newItem['desc2'];
        } else {
            $desc = $newItem['desc'];
        };
        $date = $newItem['date'];
        if (is_string($date) && $date!=='') $date = strtotime($date); 
            else {
            $date = time();
            $date = $date - 60*60; // to get to UTC time. (subtract or add your own timezone difference to GMT/UTC, measured in seconds)
        }

        $downloadDate = new DateTime();

        // no checks done here, done at $this->processItems() instead
        //if (strlen($desc)!==101 && $url!==false) {        
        
            // again, short keynames [1]
            $newItem = array (
                //'rssURL' => $rssURL,
                '_id' => $url,
                't' => $newItem['title'],
                'de' => $desc,
                'm' => $newMedia,
                'am' => $allMedia,
                
                // unfortunately 3 date fields is the minimum for any kind of accuracy given that RSS feeds may sometimes list completely bogus data in the pubDate field, or not supply a pubDate field for their news items at all.
                'pd' => intval(date('Y',$date))===1970 ? time() - (60*60) : intval($date),
                'pubDate' => $newItem['date'],
                'p' => $path,
                'dd' => intval($downloadDate->format('U')),
                
                'c' => $newItem['commentRSS'],
                'cc' => $newItem['commentsCount']
                
                //'path' => $path, // path gets worked out in javascript on the browser-side.
                //'level' => $level // level too
            );
        /*} else {
            $newItem = false;
        }*/
        
        //if (strpos($rssURL, 'http://feeds.nos.nl/nosnieuwsbuitenland?format=xml')!==false) { echo '<pre>$newItem='; var_dump ($newItem); exit(); }
        
        if ($debug) exit();

        // stage 3
        return $newItem;
    }
    
    public function uploadToDB () {
    
        echo 'Total records fetched : '.count($this->d).PHP_EOL;
        
        $updateConflicts = 0;
        $batchSize = 100; // how many docs per HTTP call from server::PHP to server::couchdb
        
        startDuration ('bulkDocs');
        
        for ($i=0; $i<count($this->d); $i = $i + $batchSize) {
            $callData = [
                'docs' => []
            ];
            
            for ($j=0; $j<$batchSize && $i+$j<count($this->d); $j++) {
                $callData['docs'][] = $this->d[$i+$j];
            }
            
            $callData = json_encode($callData);


            startDuration ('bulkDocs_batch');

            try {
                /*
                echo PHP_EOL;
                var_dump ($callData);
                echo PHP_EOL;
                */
                $ret = $this->cdb->bulkDocs ($callData);
            } catch (Throwable $e) {
                echo '<pre style="color:red">$this->cdb->bulkDocs($callData) : '; var_dump ($e->getMessage()); echo '</pre>';
                error_log ('$this->cdb->bulkDocs($callData) : '.json_encode($e,JSON_PRETTY_PRINT));
            } catch (Exception $e) {
                echo '<pre style="color:red">$this->cdb->bulkDocs($callData) : '; var_dump ($e->getMessage()); echo '</pre>';
                error_log ('$this->cdb->bulkDocs($callData) : '.json_encode($e,JSON_PRETTY_PRINT));
            }
            
            $updateConflictsMark = $updateConflicts;
            
            if (isset($ret))
            foreach ($ret->body as $idx => $doc) {
                if (
                    property_exists($doc,'error') 
                    && $doc->error==='conflict' 
                    && $doc->reason==='Document update conflict.'
                ) $updateConflicts++;
            } else $updateConflicts += $j;

            $msg = displayDuration('bulkDocs').' ('.displayDuration('bulkDocs_batch').') : '.($i+$j).' documents (attempted to upload '.$j.' RSS documents, '.($updateConflicts-$updateConflictsMark).' duplicates in this batch) ('.$updateConflicts.' total duplicates so far).'.PHP_EOL;
            echo $msg;
            error_log ($msg);
        }
            
        $msg =
            'Duplicates : '.$updateConflicts.PHP_EOL
            .'New items : '.(count($this->d)-$updateConflicts).PHP_EOL;
        echo $msg;
        error_log($msg);
    }

    public static function urlToFilename ($url) {
        $fp = $url;
        $fp = str_replace ('://','---', $fp);
        $fp = str_replace   ('/','._.', $fp);
        $fp = str_replace   ('?','-_-', $fp);
        $fp = str_replace   ('&','_-_', $fp);
        $fp = str_replace   ('=','___', $fp);
        $fp = str_replace   ('%','...', $fp);
        return $fp;
    }
    
    
    public static function escapeCData ($src) {
        $r = $src;
        $r = str_replace ('<![CDATA[', '', $r);
        $r = str_replace (']]>', '', $r);
        //$r = html_entity_decode ($r);
        return $r;
    }

    public static function getField ($regx, $item, $matchSet=1) {
        preg_match_all ($regx, $item, $matches);
        if (array_key_exists($matchSet,$matches)) {
            if (count($matches[$matchSet])==1) {
                return $matches[$matchSet][0];
            } else if (count($matches[$matchSet])>1) {
                return $matches[$matchSet];
            }
        }
        return false;
    }
    
    function padIt ($n) {
        $n1 = (int)$n;
        if ($n1 < 10) {
            return str_pad($n1, 2, '0', STR_PAD_LEFT);
        } else {
            return $n;
        }
    }    
    
}
?>
