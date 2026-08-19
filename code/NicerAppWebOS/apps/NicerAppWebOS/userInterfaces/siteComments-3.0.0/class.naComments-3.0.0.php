<?php
// MIT licensed (C) 2026 rene.veerman.netherlands@gmail.com + grok.com

class class_naComments {
    public $cn = 'class_naComments';
    public $themes = [
        'simple' => [
            'btnMainCommentAdd' => 'btnCssVividButton.greenBlue.png',
            'btnDeleteComment' => 'btnCssVividButton.yellow2a.png',
            'btnAddReply' => 'btnCssVividButton.yellow2a.png',
            'btnEditComment'    => 'btnCssVividButton.blue1a.png',
            'btnHideComment'    => 'btnCssVividButton.blue1a.png'
        ],
        'DutchCulture' => [
            'btnMainCommentAdd' => 'btnCssVividButton.orange1c.png',
            'btnDeleteComment' => 'btnCssVividButton.yellow1a.png',
            'btnAddReply' => 'btnCssVividButton.yellow1a.png',
            'btnEditComment'    => 'btnCssVividButton.blue1a.png',
            'btnHideComment'    => 'btnCssVividButton.blue1a.png'
        ]
    ];
    public $theme = 'simple';//'DutchCulture';
    public $mode = 'PHP-Apache-nginx';


    /**
     * Scan the entire comments database and enqueue screenshots
     * for every unique URL found in rootItemJSON + inside msgHTML.
     *
     * @param array $options {
     *     @var int  $retain     Seconds to keep existing screenshots
     *     @var bool $force      Force re-capture
     *     @var int  $limit      Max unique URLs to process (0 = no limit)
     *     @var bool $dryRun     Only return the list, don't enqueue
     *     @var bool $includeMsg Also scan URLs inside comment text (default true)
     * }
     */
    public function enqueueScreenshotsFromAllComments(array $options = []): array
    {
        $retain     = (int)($options['retain']     ?? 86400 * 7); // 7 days default
        $force      = (bool)($options['force']      ?? false);
        $limit      = (int)($options['limit']      ?? 0);
        $dryRun     = (bool)($options['dryRun']     ?? false);
        $includeMsg = (bool)($options['includeMsg'] ?? true);

        require_once dirname(__FILE__, 5) . '/businessLogic/class.screenshots.php';

        global $naWebOS;
        $screenshots = new naScreenshots();   // uses the auto-detect constructor

        // -------------------------------------------------
        // 1. Collect unique URLs from the comments database
        // -------------------------------------------------
        $uniqueUrls = [];

        $db  = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase($dbName, true);

        $bookmark = null;
        $pageSize = 200;

        do {
            $query = [
                'selector' => new stdClass(),
                'fields'   => ['rootItemJSON', 'msgHTML'],
                'limit'    => $pageSize,
            ];
            if ($bookmark) $query['bookmark'] = $bookmark;

            $result = $cdb->find($query);
            $docs   = $result->body->docs ?? [];

            foreach ($docs as $doc) {
                // From rootItemJSON (the page the comment belongs to)
                if (!empty($doc->rootItemJSON)) {
                    $root = json_decode($doc->rootItemJSON, true);
                    if (is_array($root) && !empty($root['url'])) {
                        $uniqueUrls[trim($root['url'])] = true;
                    }
                }

                // From links inside the comment text itself
                if ($includeMsg && !empty($doc->msgHTML)) {
                    $found = $this->extractUrlsFromHtml($doc->msgHTML);
                    foreach ($found as $u) {
                        $uniqueUrls[$u] = true;
                    }
                }
            }

            $bookmark = $result->body->bookmark ?? null;

            if ($limit > 0 && count($uniqueUrls) >= $limit) {
                break;
            }

        } while ($bookmark && $bookmark !== 'nil' && count($docs) > 0);

        $urls = array_keys($uniqueUrls);
        if ($limit > 0) {
            $urls = array_slice($urls, 0, $limit);
        }

        // -------------------------------------------------
        // 2. Enqueue them
        // -------------------------------------------------
        $summary = [
            'totalUniqueUrls' => count($urls),
            'enqueued'        => 0,
            'skipped'         => 0,
            'errors'          => [],
            'urls'            => []
        ];

        foreach ($urls as $url) {
            try {
                //var_dump ($dryRun);
                if ($dryRun) {
                    $summary['urls'][] = $url;
                    $summary['enqueued']++;
                    continue;
                }

                $job = $screenshots->enqueue($url, [
                    'retain'   => $retain,
                    'force'    => $force,
                    'priority' => 5,
                    'meta'     => [
                        'source'    => 'comments-full-scan',
                        'scannedAt' => date('c')
                    ]
                ]);

                $status = $job['status'] ?? 'unknown';

                $summary['urls'][] = [
                    'url'    => $url,
                    'status' => $status
                ];

                if ($status === 'pending') {
                    $summary['enqueued']++;
                } else {
                    $summary['skipped']++;
                }

            } catch (Throwable $e) {
                $summary['errors'][] = [
                    'url'   => $url,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $summary;
    }

    /**
     * Extract unique http/https URLs from HTML
     */
    public function extractUrlsFromHtml(string $html): array
    {
        $urls = [];

        // 1. URLs inside href="..."
        if (preg_match_all('/href\s*=\s*["\'](https?:\/\/[^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $url) {
                $urls[$this->cleanUrl($url)] = true;
            }
        }

        // 2. Plain URLs in the text (most important for your case)
        // This catches https://cnn.com even when it is just typed
        if (preg_match_all('/https?:\/\/[^\s<>"\'\)\]]+/i', $html, $m)) {
            foreach ($m[0] as $url) {
                $urls[$this->cleanUrl($url)] = true;
            }
        }

        return array_keys($urls);
    }

    private function cleanUrl(string $url): string
    {
        // Remove trailing punctuation that is often accidentally included
        return rtrim($url, '.,;:!?)]}');
    }




    public function getHTMLandCSS ($post = null, $rootItemJSON=null) {

        //return 'The comments feature is experiencing problems right now; sorry.';
        //echo '<pre>'; var_dump ($_SERVER); var_dump ($rootItemJSON); echo '</pre>'; //exit;
        if (strpos($this->mode,'ajax')===false) return '';

        if (is_array($post) && $post!==null) {
            $rootItemJSON = json_encode([
                'url' => $post['url']
            ]);
        } else {
            global $naURL;
            global $naWebOS;
            $u = 'https://'.$naWebOS->domain.$_SERVER['REQUEST_URI'];
            $json = '{"url":"'.$u.'"}';
            //echo $json; exit;
            if (is_null($rootItemJSON)) $rootItemJSON = $json;//str_replace('\/','/',substr($json,1  ,strlen($json)-2));
        };
        $rootItemJSON = str_replace('\\/', '/', $rootItemJSON);



        // -------------------------------------------------
        // 1. Extract the main page URL
        // -------------------------------------------------
        $pageUrl = '';
        if ($rootItemJSON) {
            $root = is_string($rootItemJSON) ? json_decode($rootItemJSON, true) : $rootItemJSON;
            $pageUrl = is_array($root) ? ($root['url'] ?? '') : '';
        }

        // -------------------------------------------------
        // 2. Prepare screenshots manager
        // -------------------------------------------------
        require_once dirname(__FILE__, 5) . '/boot.php';
        require_once dirname(__FILE__, 5) . '/businessLogic/class.screenshots.php';
        global $naWebOS;
        $uDB2 = $naWebOS->dbsAdmin->findConnection('couchdb');
        $screenshots = new naScreenshots($uDB2);

        // Enqueue the main page
        /*
        if ($pageUrl) {
            $screenshots->enqueue($pageUrl, [
                'retain'   => 86400 * 7,
                'priority' => 10
            ]);
        }*/

        // -------------------------------------------------
        // 3. Ensure we have a screenshot for the main page
        // -------------------------------------------------
        $retainSeconds = 86400 * 7; // keep screenshots for 7 days (adjust as you like)

        /*
        if ($pageUrl) {
            // This will return the existing ready one if still fresh,
            // otherwise it enqueues a new job
            $screenshots->enqueue($pageUrl, [
                'retain'   => $retainSeconds,
                'priority' => 10,          // high priority for the current page
                'meta'     => ['source' => 'comments-header']
            ]);
        }*/

        // -------------------------------------------------
        // 4. Optionally process a few jobs right now
        //    (so the user often sees the new screenshot after a refresh)
        // -------------------------------------------------
        // Keep this number low so the page stays fast
        /*
        $screenshots->processQueue([
            'maxJobs'      => 2,           // process max 2 jobs in this request
            'sleepSeconds' => 0,
            'verbose'      => false,
            'releaseStale' => true
        ]);
        */

        // -------------------------------------------------
        // 5. Continue with the normal comments rendering
        // -------------------------------------------------
        // ... your existing CouchDB query code for comments stays the same ...



        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $in = &$_GET;
        $fields = [
            '_id', 'parentID', 'datetimeStr',
            'clientDatetime', 'clientTZoffset', 'clientIP', 'clientUsername',
            'editedDatetime', 'editedDatetimeStr', 'editedTZoffset',
            'msgHTML', 'hidden'
        ];

        $dbName = $db->dataSetName ('cms_comments');
        try {
            $cdb->setDatabase($dbName, true);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $findCommand = [
            'selector' => [
                'rootItemJSON' => $rootItemJSON//, // unwrap from ajax call's data field
//                'parentID' => '#'
            ],
            'fields' => &$fields,
            'limit' => 200,
            'sort' => [ ['datetimeStr'=>'desc'], ['parentID'=>'desc'] ],
            'use_index' => '_design/20986d30727811fa9067a1e39a306d1b35fcf6d5'
        ];
        //echo '<pre>'; var_dump ($_SERVER); echo '</pre>';
        //echo '<pre>'; echo json_encode ($findCommand, JSON_PRETTY_PRINT); echo '</pre>';
        //exit();

        $bm = 'abc';
        $oldBM = 'def';
        $results = [];
        while ($bm!==$oldBM) {
            if ($bm!=='abc') $findCommand['bookmark'] = $bm;
            $call = $cdb->find($findCommand);
            //echo '<pre>'; echo json_encode ($call, JSON_PRETTY_PRINT); echo '</pre>';

            $oldBM = $bm;
            if (
                isset($call)
                && property_exists($call,'body')
                && property_exists($call->body, 'bookmark')
                && is_string($call->body->bookmark)
                && $call->body->bookmark !== ''
                && $call->body->bookmark !== 'nil'
            ) {
                $bm = $call->body->bookmark;
            } else {
                $bm = 'abc';
            };

            $results = array_merge_recursive($results, $this->transformResults_findCommand ($call));
        }
        //exit();

        // Also scan the comments that were just loaded for any mentioned URLs
        foreach ($results as &$comment) {
            if (!empty($comment['msgHTML'])) {
                $foundUrls = $this->extractUrlsFromHtml($comment['msgHTML']);
                //echo json_encode($foundUrls,JSON_PRETTY_PRINT);
                foreach ($foundUrls as $u) {
                    $img = $screenshots->buildFilePath($u);
                    global $naWebOS;
                    $comment['msgHTML'] = str_replace ($u, $u, $comment['msgHTML']).'<br/><a href="'.htmlspecialchars($u).'" target="_blank"><img style="width:100%" src="'.str_replace($naWebOS->domainPath,'',$img['absolute']).'_thumb.png"/></a>';
                    $screenshots->enqueue($u, [
                        'retain'   => 86400 * 7,
                        'priority' => 5,
                        'meta'     => ['source' => 'comment-text']
                    ]);
                }
            }
        }

        // Process a couple of jobs right away
        $screenshots->processQueue([
            'maxJobs' => 3,
            'verbose' => false
        ]);

        return
        '<link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-3.0.0/na.comments.css">'
        . $this->formatHeader($rootItemJSON)
        . '<div class="naComment_results">'
        . $this->formatResults($results, $post)
        . '</div>'
        . $this->formatFooter();
    }

    public function transformResults_findCommand ($call) {
        $fncn = $this->cn.'::transformResults_findCommand()';
        if (!is_object($call)) {
            echo '<pre>'; var_dump($call); echo '</pre>';
            trigger_error ($fncn.' : invalid $call');
            exit();
        }
        if (!property_exists($call,'body')) {
            echo '<pre>'; var_dump($call); echo '</pre>';
            trigger_error ($fncn.' : invalid $call->body');
            exit();
        }
        if (!property_exists($call->body,'docs')) return [];
        return json_decode(json_encode($call->body->docs),true);
    }

   public function formatHeader($rootItemJSON = null) {
        global $naWebOS;

        // Extract the page URL
        $pageUrl = '';
        if ($rootItemJSON) {
            $root = is_string($rootItemJSON) ? json_decode($rootItemJSON, true) : $rootItemJSON;
            $pageUrl = is_array($root) ? ($root['url'] ?? '') : '';
        }
        //$screenshotHtml = $this->getPageScreenshotHtml($pageUrl);

        $html =
        '<div class="naComment_header_div">' . PHP_EOL
            . '<h2 class="naComments_header">Comments</h2>' . PHP_EOL
            //. $screenshotHtml
            .$naWebOS->html_vividButton (
                1000, 'float:right',

                'btnAddNewComment',
                'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                '',
                'na.c.onclick_btnAddComment(event);',
                '',
                '',

                1001, 'Add comment',
                'btnCssVividButton_outerBorder.png',
                'btnCssVividButton.png',
                $this->themes[$this->theme]['btnMainCommentAdd'],
                'btnDocument2.png',
                                         '', 'Add comment', '', ''
            ).PHP_EOL
            .$naWebOS->html_vividButton (
                1000, 'float:right',

                'btnDateRange_lower',
                'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                '',
                'na.c.onclick_btnAddComment(event);',
                '',
                '',

                1001, 'Pick begin date',
                'btnCssVividButton_outerBorder.png',
                'btnCssVividButton.png',
                'btnCssVividButton.yellow1a.png',
                'pngtree-calendar-3d-icon-isolated-on-a-transparent-background-symbolizing-schedules-and-png-image_20358144.png',

                '', 'Pick begin date', '', ''
            ).PHP_EOL
            .$naWebOS->html_vividButton (
                1000, 'float:right',

                'btnDateRange_upper',
                'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                '',
                'na.c.onclick_btnAddComment(event);',
                '',
                '',

                1001, 'Pick end date',
                'btnCssVividButton_outerBorder.png',
                'btnCssVividButton.png',
                'btnCssVividButton.yellow1a.png',
                'calendar.png',
                '', 'Pick end date', '', ''
            ).PHP_EOL
            .'</div>'.PHP_EOL;
        return $html;
    }

    public function formatResults ($results, $post) {
        // The free help of grok.com was invaluable and quick to get this function to do exactly what I want for now :)
        global $naLAN;
        global $naIP;
        global $naWebOS;
        global $naUsername;
        $html = '';
        global $cr;
        $cr = $results;
        if (is_array($post) && array_key_exists('openIDs',$post)) {
            $openIDs = json_decode($post['openIDs']);
        } else {
            $openIDs = [];
        }

        function buildCommentTree(array $flatComments, string $idField = '_id', string $parentField = 'parentID'): array
        {
            $byId = [];
            $tree  = [];

            // Index by ID + collect root nodes
            foreach ($flatComments as $comment) {
                $id = $comment[$idField];
                $byId[$id] = $comment;
                $byId[$id]['children'] = [];           // prepare children array

                $parentId = $comment[$parentField] ?? null;

                if ($parentId === null || $parentId === '#' || $parentId === 0) {
                    $tree[] = &$byId[$id];
                } else {
                    // We'll attach later if parent exists
                }
            }

            // Attach children
            foreach ($byId as $id => &$comment) {
                $parentId = $comment[$parentField] ?? null;
                if ($parentId !== null && $parentId !== '#' && $parentId !== 0) {
                    if (isset($byId[$parentId])) {
                        $byId[$parentId]['children'][] = &$comment;
                    }
                    // else → orphan → already ignored / can log
                }
            }

            return $tree;
        }

        function sortTreeNewestFirst(array &$nodes): void
        {
            // Sort current level newest → oldest
            usort($nodes, function($a, $b) {
                return $a['datetimeStr'] >= $b['datetimeStr'] ? -1 : 1;
            });

            // Recurse into children
            foreach ($nodes as &$node) {
                if (!empty($node['children'])) {
                    //usort($nodes['children'], fn($a, $b) => $b['datetimeStr'] <=> $a['datetimeStr']);
                    sortTreeNewestFirst($node['children']);
                }
            }
        }
        function sortTreeTopLevelNewestOnly(array &$nodes, int $depth = 0): void {
            if ($depth === 0) {
                // ONLY sort the root level newest → oldest
                usort($nodes, fn($a, $b) => $b['datetimeStr'] <=> $a['datetimeStr']);
            }
            // do NOT sort deeper levels → they stay oldest → newest

            foreach ($nodes as &$node) {
                if (!empty($node['children'])) {
                    sortTreeTopLevelNewestOnly($node['children'], $depth + 1);
                }
            }
        }

        // ────────────────────────────────────────────────
        // Usage in your code:
        $flatList = $results;  // ← from DB or wherever

        $tree = buildCommentTree($flatList, '_id', 'parentID');   // adjust field names if different

        // Now sort the whole structure reverse-chronologically per level
        sortTreeTopLevelNewestOnly($tree);
        //echo '<pre>t777;'; var_dump ($tree); echo '</pre>'; exit();

        function printTree (&$tree, &$openIDs, &$t) {
            $html = '';
            foreach ($tree as $idx => $rootItem) {
                $html .= printItem ($rootItem, $openIDs, $t);
                if (count($rootItem['children'])>0) {
                    $html .= printTree ($rootItem['children'], $openIDs, $t);
                }
            }
            return $html;
        };

        function printItem($it, $openIDs, $t) {
            global $naLAN;
            global $naIP;
            global $naWebOS;
            global $naUsername;
            $its_id = $it['_id'];
            if (in_array($its_id,$openIDs)) $style='style="display:block"'; else $style='';
            $html = '<div id="naComment_'.($its_id!=='#'?$its_id:'_').'" class="naComment_entry" '.$style.'>'.PHP_EOL;
                //$html .= json_encode ($it, JSON_PRETTY_PRINT);
                $html .= $naWebOS->html_vividButton(
                    1001, 'float:left',

                    'btnExpandComment',
                    'vividButton_icon_50x50 vbExpandComment', '_50x50', 'vbExpandComment',
                    '',
                    'na.c.onclick_btnExpandComment(event);',
                    '',
                    '',

                    1001, 'Expand comment',
                    'btnCssVividButton_outerBorder.png',
                    'btnCssVividButton.png',
                    'btnCssVividButton.greenYellow.png',
                    'btnPlus_shaded.png',
                    '', '', '', ''
                ).PHP_EOL;
                if (array_key_exists('clientUsername',$it) && $it['clientUsername']==$naUsername) {
                    $html .= $naWebOS->html_vividButton(
                        1001, 'float:right',

                        'btnRemoveComment',
                        'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                        '',
                        'na.c.onclick_btnRemoveComment(event);',
                        '',
                        '',

                        1001, 'Remove comment',
                        'btnCssVividButton_outerBorder.png',
                        'btnCssVividButton.png',
                        $t->themes[$t->theme]['btnDeleteComment'],
                        'btnTrashcan_red.png',
                        '', '', '', ''
                    ).PHP_EOL;
                    $html .= $naWebOS->html_vividButton(
                        1001, 'float:right',
                        'btnEditComment',
                        'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                        '',
                        'na.c.onclick_btnEditComment(event);',
                                                        '',
                                                        '',
                                                        1001, 'Edit comment',
                                                        'btnCssVividButton_outerBorder.png',
                                                        'btnCssVividButton.png',
                                                        $t->themes[$t->theme]['btnEditComment'],
                                                        'btnDocument.png',          // or any icon you already have
                                                        '', '', '', ''
                    ).PHP_EOL;
                    $h = (
                        array_key_exists('hidden',$it)
                        && $it['hidden']
                    );
                    if ($h) {
                        $filter = 'opacity:0.4;filter:greyscale(1) brightness(0.6)';
                    } else {
                        $filter = '';
                    }
                    $html .= $naWebOS->html_vividButton(
                        1001, 'float:right;'.$filter,
                        'btnHideComment',
                        'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
                        '',
                        'na.c.onclick_btnToggleHidden(event);',
                                                        (isset($it['hidden']) && $it['hidden'] ? 'Show comment' : 'Hide comment'),
                                                        '',
                                                        1001, '(Un)hide comment',
                                                        'btnCssVividButton_outerBorder.png',
                                                        'btnCssVividButton.png',
                                                        $t->themes[$t->theme]['btnEditComment'],
                                                        '1660_blk_19329_zoom.upperBodyOnly.256x256.png',          // or any icon you already have
                                                        '', '', '', ''
                    ).PHP_EOL;
                }
                $html .= "\t".'<div style="display:none">'.PHP_EOL;
                $html .= "\t\t".'<span class="naComment_id">'.$it['_id'].'</span>'.PHP_EOL;
                $html .= "\t\t".'<span class="naComment_parentID">'.$it['parentID'].'</span>'.PHP_EOL;
                if (array_key_exists('clientIP', $it)) $html .= "\t\t".'<span class="naComment_clientIP">'.$it['clientIP'].'</span>'.PHP_EOL;
                $html .= "\t\t".'<span class="naComment_clientDatetime">'.$it['clientDatetime'].'</span>'.PHP_EOL;
                $html .= "\t\t".'<span class="naComment_clientTZoffset">'.$it['clientTZoffset'].'</span>'.PHP_EOL;
                if (array_key_exists('clientUsername', $it)) $html .= "\t\t".'<span class="naComment_clientUsername">'.$it['clientUsername'].'</span>'.PHP_EOL;
                $html .= "\t".'</div>'.PHP_EOL;


            $html .= "\t".'<div class="naComment_header">';

	$loginResult = $_SESSION['loginResult'];
            if (array_key_exists('clientUsername', $it)) {
                $html .= "\t".'<span class="naComment_username">'
                . (array_key_exists('displayName',$loginResult)?$loginResult['displayName']:$it['clientUsername'])
                . '</span>'.PHP_EOL;
            }

            $html .=
            '<span class="naComment_clientDatetime" style="display:none;">'.$it['clientDatetime'].'</span>'
            .'<span class="naComment_clientTZoffset" style="display:none;">'.$it['clientTZoffset'].'</span>'
            .'<span class="naComment_datetime" '
            . 'onclick="na.c.onclick_datetime(event)" '
            . 'style="cursor:pointer;" '
            . 'title="Click to link to this comment">'
            . naDateTimeHeader($it['clientDatetime'], $it['clientTZoffset']);
            $html .= '</span>'.PHP_EOL;

            if (!empty($it['editedDatetime'])) {
                $html .=
                    '<span class="naComment_editedDatetime" style="display:none;">'.$it['editedDatetime'].'</span>'
                    .'<span class="naComment_editedTZoffset" style="display:none;">'.$it['editedTZoffset'].'</span>'
                    .'<span class="naComment_edited" title="Last edited">'
                    . ' (edited ' . naDateTimeHeader($it['editedDatetime'], $it['editedTZoffset'] ?? 0) . ')'
                    .'</span>';
            }



            $html .= "\t".'</div>'.PHP_EOL;


            //$html .= $t->mode.'<br/>';
            // $it2 = $it;
            // $p = [];
            $h = (
                array_key_exists('hidden',$it)
                && $it['hidden']
            );
            // $dbg = [
            //     array_key_exists('hidden',$it),
            //     array_key_exists('hidden',$it) && $it['hidden'],
            //     $h,
            //     $it
            // ];

            //walkArray ($it2, $it2, null, 'wav', false, $p);
            //$html .= '<pre>'.json_encode($dbg,JSON_PRETTY_PRINT).'</pre>';
            if (
                strpos($t->mode,'ajax')!==false
                && $h!==true
            ) {
		$mh = $it['msgHTML'];
		$mh = str_replace('<iframe', '<iframe loading="lazy" defer=""', $mh);
		$mh = str_replace('allowfullscreen>', 'allowfullscreen="">', $mh);
                $html .= "\t".'<div class="naComment_msgHTML" style="opacity:'.($h?0.4:1).'">'.$mh.'</div>'.PHP_EOL;

                // Show small screenshots for any URLs mentioned in this comment
                $mentionedUrls = $t->extractUrlsFromHtml($it['msgHTML'] ?? '');
                $html .= $t->getLinkedScreenshotsHtml($mentionedUrls);
            };
                $html .= "\t".'<div class="naComment_subComments">';
                $html .= "\t".'</div>'.PHP_EOL;
                $html .=
                    "\t".$naWebOS->html_vividButton (
                        1001, 'margin-left:30px;',

                        'btnAddSubComment',
                        'vividButton_icon_50x50 vbAddSubComment', '_50x50', 'vbAddSubComment',
                        '',
                        'na.c.onclick_btnAddComment(event);',
                        '',
                        '',

                        1001, 'Add reply',

                        'btnCssVividButton_outerBorder.png',
                        'btnCssVividButton.png',
                        $t->themes[$t->theme]['btnAddReply'],
                        'btnDocument2.png',

                        '',

                        'Add reply',
                        '', ''
                    ).PHP_EOL;
            $html .= '</div>'.PHP_EOL;
            return $html;
        }

        $html = printTree($tree, $openIDs, $this);

        return $html;
    }

     public function formatFooter() {
        global $naWebOS;
        $html =
            '<div class="naComments_footer">'.PHP_EOL
            .'</div>'.PHP_EOL;
        return $html;
    }

    public function getEditor() {
        $fn = __DIR__.'/htmlSnippet_commentsEditor.php';
        $html = require_return ($fn);
        return $html;
    }

    public function add($in=null) {
        $fncn = $this->cn.'::add($in)';
        global $naWebOS;
        if (!is_array($in)) trigger_error ($fncn.' : !is_array($in)', E_USER_ERROR);
        if (!array_key_exists('rec',$in)) trigger_error ($fncn.' : !array_key_exists("rec",$in)', E_USER_ERROR);
        $rec = json_decode($in['rec'], true);
        $rec['_id'] = randomString(20);
        global $naIP; $rec['clientIP'] = $naIP;
        $rec['clientDatetime'] =
        $rec['datetimeServer'] = time();
        $rec['datetimeStr'] = naDateTimeStr($rec['clientDatetime'], $rec['clientTZoffset']);
        $rec['msgHTML'] = str_replace ('<p><span class="backdropped"', '<p class="backdropped"', $rec['msgHTML']);
        $rec['msgHTML'] = str_replace ('</span>', '', $rec['msgHTML']);
        $rec['msgHTML'] = str_replace ('<p>', '<p class="backdropped">', $rec['msgHTML']);
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase ($dbName);
        $cdb->post ($rec);
        $results = [$rec];
        $rec['resultHTML'] = $this->formatResults($results, $rec);
        echo json_encode($rec);
    }

/*    public function edit($in = null) {
        $fncn = $this->cn.'::edit($in)';
        global $naWebOS, $naIP, $naUsername;

        if (!is_array($in)) trigger_error($fncn.' : !is_array($in)', E_USER_ERROR);
        if (!array_key_exists('rec', $in)) trigger_error($fncn.' : missing rec', E_USER_ERROR);

        $rec = json_decode($in['rec'], true);
        if (empty($rec['id'])) {
            echo json_encode(['error' => 'Missing comment id']);
            return;
        }

        $db     = $naWebOS->dbs->findConnection('couchdb');
        $cdb    = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase($dbName);

        try {
            $call = $cdb->get($rec['id']);
            $doc  = $call->body;

            // security: only the original author may edit
            if (!isset($doc->clientUsername) || $doc->clientUsername !== $naUsername) {
                echo json_encode(['error' => 'Not allowed']);
                return;
            }

            // update message
            $doc->msgHTML = str_replace('<p><span class="backdropped"', '<p class="backdropped"', $rec['msgHTML']);
            $doc->msgHTML = str_replace('</span>', '', $doc->msgHTML);
            $doc->msgHTML = str_replace('<p>', '<p class="backdropped">', $doc->msgHTML);

            // ─── last-edited timestamp ───
            $now = time();
            $doc->editedDatetime   = $now;
            $doc->editedTZoffset   = $rec['clientTZoffset'] ?? ($doc->clientTZoffset ?? 0);
            $doc->editedDatetimeStr = naDateTimeStr($now, $doc->editedTZoffset);

            // keep original creation time intact
            // (optional) also refresh clientIP if you want
            // $doc->clientIP = $naIP;

            $cdb->put($doc->_id, $doc);

            echo json_encode(['ok' => true, 'id' => $doc->_id]);
        } catch (Exception $e) {
            echo json_encode([
                'errorHTML'       => 'Could not edit comment',
                'couchdbErrorMsg' => $e->getMessage()
            ]);
        }
    }
 */
    public function edit($in = null) {
    $fncn = $this->cn.'::edit($in)';
    global $naWebOS, $naIP, $naUsername;

    if (!is_array($in)) trigger_error($fncn.' : !is_array($in)', E_USER_ERROR);
    if (!array_key_exists('rec', $in)) trigger_error($fncn.' : missing rec', E_USER_ERROR);

    $rec = json_decode($in['rec'], true);
    if (empty($rec['id'])) {
        echo json_encode(['error' => 'Missing comment id']);
        return;
    }

    $db     = $naWebOS->dbs->findConnection('couchdb');
    $cdb    = $db->cdb;
    $dbName = $db->dataSetName('cms_comments');
    $cdb->setDatabase($dbName);

    try {
        $call = $cdb->get($rec['id']);
        $doc  = $call->body;

        // security: only the original author may edit
        if (!isset($doc->clientUsername) || $doc->clientUsername !== $naUsername) {
            echo json_encode(['error' => 'Not allowed']);
            return;
        }

        // ───────────────────────────────────────────────
        // 1. Build revision history entry (the "onedit")
        // ───────────────────────────────────────────────
        $this->onedit($doc, $rec);   // ← the new helper

        // ───────────────────────────────────────────────
        // 2. Apply the actual content change
        // ───────────────────────────────────────────────
        $doc->msgHTML = str_replace('<p><span class="backdropped"', '<p class="backdropped"', $rec['msgHTML']);
        $doc->msgHTML = str_replace('</span>', '', $doc->msgHTML);
        $doc->msgHTML = str_replace('<p>', '<p class="backdropped">', $doc->msgHTML);

        // last-edited timestamp
        $now = time();
        $doc->editedDatetime    = $now;
        $doc->editedTZoffset    = $rec['clientTZoffset'] ?? ($doc->clientTZoffset ?? 0);
        $doc->editedDatetimeStr = naDateTimeStr($now, $doc->editedTZoffset);

        // keep original creation time intact
        // (optional) $doc->clientIP = $naIP;

        $cdb->put($doc->_id, $doc);

        echo json_encode([
            'ok'      => true,
            'id'      => $doc->_id,
            'history' => $doc->history ?? []   // optional: return the history to the client
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'errorHTML'       => 'Could not edit comment',
            'couchdbErrorMsg' => $e->getMessage()
        ]);
    }
}

/**
 * Called just before a comment document is updated.
 * Pushes a snapshot of the current (pre-edit) state into $doc->history.
 *
 * @param object $doc   The CouchDB document (stdClass) that is about to be saved
 * @param array  $rec   The incoming edit payload from the client
 */
private function onedit($doc, array $rec): void
{
    // Initialise history array if it does not exist yet
    if (!isset($doc->history) || !is_array($doc->history)) {
        $doc->history = [];
    }

    // Snapshot of the version that is about to be overwritten
    $snapshot = [
        'rev'               => $doc->_rev ?? null,          // CouchDB revision at the moment of edit
        'msgHTML'           => $doc->msgHTML ?? '',
        'editedDatetime'    => $doc->editedDatetime    ?? $doc->clientDatetime    ?? null,
        'editedDatetimeStr' => $doc->editedDatetimeStr ?? $doc->datetimeStr       ?? null,
        'editedTZoffset'    => $doc->editedTZoffset    ?? $doc->clientTZoffset    ?? 0,
        'editedBy'          => $doc->clientUsername    ?? null,   // who wrote this version
        'editedAt'          => time(),                            // server time of this history push
        // add any other fields you want to keep historically
        // 'clientIP'       => $doc->clientIP ?? null,
    ];

    // Push to the front (newest first)
    array_unshift($doc->history, $snapshot);

    // Keep only the last N revisions (adjust as you like)
    $maxHistory = 30;
    if (count($doc->history) > $maxHistory) {
        $doc->history = array_slice($doc->history, 0, $maxHistory);
    }
}

    public function toggleHidden($in = null) {
        $fncn = $this->cn.'::toggleHidden($in)';
        global $naWebOS, $naUsername;

        if (!is_array($in)) trigger_error($fncn.' : !is_array($in)', E_USER_ERROR);
        if (!array_key_exists('id', $in)) trigger_error($fncn.' : missing id', E_USER_ERROR);

        $db     = $naWebOS->dbs->findConnection('couchdb');
        $cdb    = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase($dbName, true);

        try {
            $call = $cdb->get($in['id']);
            $doc  = $call->body;

            // Only allow owner or admin (adjust as you like)
            $isOwner = (isset($doc->clientUsername) && $doc->clientUsername === $naUsername);
            // You can add more permission checks here

            $doc->hidden = !(isset($doc->hidden) && $doc->hidden === true);

            $cdb->put($doc->_id, $doc);

            echo json_encode([
                'ok'     => true,
                'id'     => $doc->_id,
                'hidden' => $doc->hidden
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'errorHTML'       => 'Could not toggle hidden state',
                'couchdbErrorMsg' => $e->getMessage()
            ]);
        }
    }

    public function remove($in=null) {
        global $naWebOS;
        $fncn = $this->cn.'::add($in)';
        $debug = false;
        if (!is_array($in)) trigger_error ($fncn.' : !is_array($in)', E_USER_ERROR);
        if (!array_key_exists('rec',$in)) trigger_error ($fncn.' : !array_key_exists("rec",$in)', E_USER_ERROR);
        $rec = json_decode($in['rec'], true);

        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase ($dbName);

        try {
            $call = $cdb->get($rec['id']);
            //echo '<pre>'; var_dump($call); echo '</pre>';
            $ids = [$rec['id']];
            $cdb->delete ($call->body->_id, $call->body->_rev);
            echo '{"deleted" : {"dbName":"'.$dbName.'","ids":'.json_encode($ids).'}}';
        } catch (Exception $e) {
            echo '{"errorHTML" : "Could not delete comment", "couchdbErrorMsg" : "'.$e->getMessage().'"}';
        }
    }

    public function addIndexes() {
        return false;

        global $naWebOS;
        $debug = false;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $dbName = $db->dataSetName('cms_comments');
        $cdb->setDatabase ($dbName);

        $rec = [
            'index' => [
                'fields' => [ 'datetimeStr', 'parentID' ]
            ],
            'name' => 'sortIndex_datetimeStrParentID',
            'type' => 'json'
        ];
        try {
            $cdb->setIndex ($rec);
        } catch (Exception $e) {
            if ($debug) { echo '<pre style="color:red">'; var_dump ($e); echo '</pre>'; exit(); }
        }

        $rec = [
            'index' => [
                'fields' => [ 'clientIP', 'clientDatetime', 'clientTZoffset' ]
            ],
            'name' => 'sortIndex_clientIndexes',
            'type' => 'json'
        ];
        try {
            $cdb->setIndex ($rec);
        } catch (Exception $e) {
            if ($debug) { echo '<pre style="color:red">'; var_dump ($e); echo '</pre>'; exit(); }
        }

    }

    /**
     * Explicitly process pending screenshot jobs from PHP.
     *
     * @param array $options {
     *     @var int    $maxJobs       Maximum number of jobs to process in this run (default 10)
     *     @var string $workerId      Identifier for this worker (default auto-generated)
     *     @var int    $sleepSeconds  Pause between jobs (default 1)
     *     @var bool   $verbose       Print progress to output (default true when CLI)
     *     @var bool   $releaseStale  First release any stale locks (default true)
     * }
     *
     * @return array  Summary of the run
     */
    public function processQueue(array $options = []): array
    {
        $maxJobs      = (int)($options['maxJobs']      ?? 10);
        $workerId     = $options['workerId']           ?? ('php-' . gethostname() . '-' . getmypid());
        $sleepSeconds = (int)($options['sleepSeconds'] ?? 1);
        $verbose      = $options['verbose']            ?? false;
        $releaseStale = $options['releaseStale']       ?? true;

        $summary = [
            'workerId'    => $workerId,
            'startedAt'   => date('c'),
            'processed'   => 0,
            'succeeded'   => 0,
            'failed'      => 0,
            'skipped'     => 0,
            'jobs'        => [],
            'errors'      => []
        ];

        if ($releaseStale) {
            $released = $this->releaseStaleLocks();
            if ($verbose && $released > 0) {
		    echo "<p class=\"phpError\">[" . date('H:i:s') . "] Released {$released} stale lock(s)</p>";
            }
        }

        $processed = 0;

        while ($processed < $maxJobs) {
            $job = $this->claimNextJob($workerId);

            if (!$job) {
                if ($verbose) {
                    echo "<p class=\"phpError\">[" . date('H:i:s') . "] No more pending jobs.</p>";
                }
                break;
            }

            $url = $job['url'] ?? '(unknown)';

            if ($verbose) {
		    echo "<p class=\"phpError\">[" . date('H:i:s') . "] Processing: {$url}</p>";
            }

            try {
                $result = $this->processJob($job);

                $status = $result['status'] ?? 'unknown';

                $summary['jobs'][] = [
                    'url'    => $url,
                    'status' => $status,
                    '_id'    => $result['_id'] ?? null
                ];

                if ($status === 'ready') {
                    $summary['succeeded']++;
                } elseif ($status === 'failed') {
                    $summary['failed']++;
                } else {
                    $summary['skipped']++;
                }

                if ($verbose) {
                    echo "[" . date('H:i:s') . "] → {$status}\n";
                }

            } catch (Throwable $e) {
                $summary['failed']++;
                $summary['errors'][] = [
                    'url'   => $url,
                    'error' => $e->getMessage()
                ];

                if ($verbose) {
                    echo "[" . date('H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
                }
            }

            $processed++;
            $summary['processed'] = $processed;

            if ($sleepSeconds > 0 && $processed < $maxJobs) {
                sleep($sleepSeconds);
            }
        }

        $summary['finishedAt'] = date('c');

        return $summary;
    }



    /**
     * Get the big screenshot HTML for the main page (used in the header)
     */
    public function getPageScreenshotHtml(string $url): string
    {
        if (empty($url)) return '';

        try {
            require_once dirname(__FILE__, 5) . '/businessLogic/class.screenshots.php';

            global $naWebOS;
            // Adjust this line to however you normally obtain a uDB2 instance
            $uDB2 = $naWebOS->dbs->findConnection('couchdb'); // or your actual method
            // You may need to wrap it properly into a uDB2 object depending on your setup

            $screenshots = new naScreenshots($uDB2);
            $report = $screenshots->createDatabaseAndIndexes();
            $record = $screenshots->findByUrl($url);

            if (!$record || ($record['status'] ?? '') !== 'ready') {
                return '';
            }

            $imgSrc = '/siteData/' . ltrim($record['relativePath'] ?? '', '/');

            return
            '<div class="naComment_pageScreenshot" style="margin:12px 0 18px 0;">' .
            '<a href="' . htmlspecialchars($url) . '" target="_blank" class="nomod noPushState" title="Open original page">' .
            '<img src="' . htmlspecialchars($imgSrc) . '" ' .
            'alt="Screenshot of page" ' .
            'style="max-width:100%; max-height:320px; border-radius:10px; box-shadow:0 3px 12px rgba(0,0,0,0.35);" />' .
            '</a>' .
            '</div>';
        } catch (Throwable $e) {
            return '';
        }
    }

    /**
     * Build small thumbnail screenshots for URLs mentioned inside a comment
     */
    public function getLinkedScreenshotsHtml(array $urls, int $max = 5): string
    {
        if (empty($urls)) return '';

        try {
            require_once dirname(__FILE__, 5) . '/businessLogic/class.screenshots.php';

            global $naWebOS;
            $uDB2 = $naWebOS->dbs->findConnection('couchdb'); // adjust as needed
            $screenshots = new naScreenshots($uDB2);

            $html = '<div class="naComment_linkedScreenshots" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:10px;">';
            $count = 0;

            foreach ($urls as $url) {
                if ($count >= $max) break;

                $record = $screenshots->findByUrl($url);
                if (!$record || ($record['status'] ?? '') !== 'ready') continue;

                $imgSrc = '/siteData/' . ltrim($record['relativePath'] ?? '', '/').'_thumb.png';

                $html .=
                '<a href="' . htmlspecialchars($url) . '" target="_blank" class="nomod noPushState" ' .
                'style="display:inline-block; width:100%;line-height:0;" title="' . htmlspecialchars($url) . '">' .
                '<img src="' . htmlspecialchars($imgSrc) . '" ' .
                'alt="Linked page screenshot" ' .
                'style="width:100%; border-radius:7px; box-shadow:0 2px 6px rgba(0,0,0,0.3); transition:transform 0.15s;" ' .
                'onmouseover="" ' .
                'onmouseout="" />' .
                '</a>';

            $count++;
            }

            $html .= '</div>';
            return $count > 0 ? $html : '';

        } catch (Throwable $e) {
            return '';
        }
    }
}
function wav($cd) {
    if (array_key_exists('msgHTML',$cd['params']['v'])) {
        $cd['params']['v']['msgHTML'] = htmlspecialchars($cd['params']['v']['msgHTML']);
    }
};

