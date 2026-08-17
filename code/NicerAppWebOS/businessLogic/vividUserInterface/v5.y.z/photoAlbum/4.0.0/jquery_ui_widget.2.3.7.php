<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" style="width:100%;height:100%;">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

<title>Plupload - jQuery UI Widget</title>

<!--<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" />-->
<link rel="stylesheet" href="/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/jquery.ui.plupload.2.3.7.css" type="text/css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>

<!-- production -->
<!--<script type="text/javascript" src="/NicerAppWebOS/3rd-party/plupload-2.3.6/js/plupload.full.min.js"></script>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/plupload-2.3.6/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>-->

<!-- debug -->
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/plupload-2.3.7/js/moxie.js"></script>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/plupload-2.3.7/js/plupload.dev.js"></script>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/plupload-2.3.7/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>
<!--  -->

</head>
<body style="font: 13px Verdana; width:100%;height:100%;overflow:hidden;margin:0px;padding:0px;">

<form id="form" method="post" action="../dump.php" style="width:100%;height:100%;text-align:center">
	<div id="uploader">
		<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
	</div>
	<br />
</form>

<script type="text/javascript">
// Initialize the widget when the DOM is ready
$(function() {
	$("#uploader").plupload({
		// General settings
		runtimes : 'html5,flash,silverlight,html4',
		url : './upload.2.3.7.php?codePath=<?php echo $_GET['codePath']?>',

		// User can upload no more then 20 files in one go (sets multiple_queues to false)
		max_file_count: 100 * 1000,
		
		chunk_size: '1mb',

		// Resize images on clientside if we can
		/*
		resize : {
			width : 200, 
			height : 200, 
			quality : 90,
			crop: true // crop to exact dimensions
		},*/
		
		filters : {
			// Maximum file size
			max_file_size : '1000mb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"},
				{title : "Zip files", extensions : "zip"}
			],
            exclude_folders : [ 'thumbs' ]
		},

		// Rename files by clicking on their titles
		rename: true,
		
		// Sort files
		sortable: true,

		// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		dragdrop: true,

		// Views to activate
		views: {
			list: true,
			thumbs: true, // Show thumbs
			active: 'thumbs'
		},

		// Flash settings
		flash_swf_url : '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/Moxie.2.3.7.swf',

		// Silverlight settings
		silverlight_xap_url : '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/Moxie.2.3.7.xap',
		
        init: {
            PostInit: function() {
                /*document.getElementById('uploader').onclick = function() {
                    uploader.start();
                    return false;
                };*/
            },
            BeforeUpload: function (up, file) {
                // send relativePath along
                /*
                debugger;
                if(map[file.id] !== undefined) {
                    var mapName = map[file.id];
                    //window.top.na.m.log (555, {file:file,relativePath:mapName});
                    file.relativePath = mapName;
                    up.setOption('multipart_params', {
                        relativePath: mapName
                    });
                    var rp = mapName.substr(1);
                    if (rp && rp!=='') {
                        window.top.na.cms.currentNode_createPath (rp);
                    } else debugger;
                } else debugger; // hits for files that it shouldn't hit for!
                */
                var 
                relativePath = file.relativePath.replace('/'+file.name, ''),
                rp = relativePath.substr(1);
                
                up.setOption('multipart_params', {
                    relativePath: relativePath
                });
                
                if (rp && rp!=='') {
                    if (window.top.na.cms)
                      window.top.na.cms.currentNode_createPath (rp);
                    else
                      window.top.na.blog.currentNode_createPath (rp);
                }                
            },
            
            UploadComplete : function (up, files) {
                if (window.top.na.cms)
                  window.top.na.cms.mediaUploadComplete (up, files);
                else
                  window.top.na.blog.mediaUploadComplete (up, files);
            }
        }		
	});

    plupload.addFileFilter('exclude_folders', function(folders, file, cb) {
      var undef;
      var pass = true;
      for (var i=0; i < folders.length; i++) {
        if (file.relativePath.indexOf(folders[i])!==-1) pass = false;
      }
      if (!pass) {
        cb(false);
      } else {
        cb(true);
      }
    });


	// Handle the case when form was submitted before uploading has finished
	$('#form').submit(function(e) {
		// Files in queue upload them first
		if ($('#uploader').plupload('getFiles').length > 0) {

			// When all files are uploaded submit form
			$('#uploader').on('complete', function() {
				$('#form')[0].submit();
			});

			$('#uploader').plupload('start');
		} else {
			alert("You must have at least one file in the queue.");
		}
		return false; // Keep the form from submitting
	});
});

let map = {};
	
// all relative paths are built here
// this code is old code found at the plupload site, and does not work because .readEntries() is an async function.
// working code is included below this commented-out code..
/*
traverseFileTree = function (item, path) {
var dirReader = null;
    path = path || '';
    //debugger;
    if (item.isFile) {
        let filename = item.name;
        item.file(function(file) {
            // careful here, could be several files of the same name
            // we assume files will be in the same order here than in plupload
            if(map[filename] === undefined) {
                map[filename] = [];
            }
            map[filename].push(path);
            console.log ('FILE : '+path);
        });
    } else if (item.isDirectory) {
        dirReader = item.createReader();
        dirReader.readEntries(function (entries) {
            var n = 0;
            for (n = 0; n < entries.length; n++) {
                console.log ('DIR  : '+path+item.name+'/');
                traverseFileTree(entries[n], path + item.name + "/");
                
            }
        });
    }
};

// bind another handler to the drop event to build an object representing the folder structure
document.getElementById('uploader').addEventListener('drop', function(e) {
    var items = e.dataTransfer.items, n, item;
    for(n = 0; n < items.length; n++) {
        item = items[n].webkitGetAsEntry();
        if(item) {
            traverseFileTree(item);
        } else debugger;
    }
    debugger;
}, false);	
*/

// many thanks to http://120.27.46.75:81/webhtml/.svn/pristine/5a/5aa3ae5cf228450c853910d093eea41ea2aa6741.svn-base
"use strict";"object"!=typeof window.CP&&(window.CP={}),window.CP.PenTimer={programNoLongerBeingMonitored:!1,timeOfFirstCallToShouldStopLoop:0,_loopExits:{},_loopTimers:{},START_MONITORING_AFTER:2e3,STOP_ALL_MONITORING_TIMEOUT:5e3,MAX_TIME_IN_LOOP_WO_EXIT:2200,exitedLoop:function(o){this._loopExits[o]=!0},shouldStopLoop:function(o){if(this.programKilledSoStopMonitoring)return!0;if(this.programNoLongerBeingMonitored)return!1;if(this._loopExits[o])return!1;var t=this._getTime();if(0===this.timeOfFirstCallToShouldStopLoop)return this.timeOfFirstCallToShouldStopLoop=t,!1;var i=t-this.timeOfFirstCallToShouldStopLoop;if(i<this.START_MONITORING_AFTER)return!1;if(i>this.STOP_ALL_MONITORING_TIMEOUT)return this.programNoLongerBeingMonitored=!0,!1;try{this._checkOnInfiniteLoop(o,t)}catch(n){return this._sendErrorMessageToEditor(),this.programKilledSoStopMonitoring=!0,!0}return!1},_sendErrorMessageToEditor:function(){try{if(this._shouldPostMessage()){var o={action:"infinite-loop",line:this._findAroundLineNumber()};parent.postMessage(JSON.stringify(o),"*")}else this._throwAnErrorToStopPen()}catch(t){this._throwAnErrorToStopPen()}},_shouldPostMessage:function(){return document.location.href.match(/boomerang/)},_throwAnErrorToStopPen:function(){throw"We found an infinite loop in your Pen. We've stopped the Pen from running. Please correct it or contact support@codepen.io."},_findAroundLineNumber:function(){var o=new Error,t=0;if(o.stack){var i=o.stack.match(/boomerang\S+:(\d+):\d+/);i&&(t=i[1])}return t},_checkOnInfiniteLoop:function(o,t){if(!this._loopTimers[o])return this._loopTimers[o]=t,!1;var i=t-this._loopTimers[o];if(i>this.MAX_TIME_IN_LOOP_WO_EXIT)throw"Infinite Loop found on loop: "+o},_getTime:function(){return+new Date}},window.CP.shouldStopExecution=function(o){return window.CP.PenTimer.shouldStopLoop(o)},window.CP.exitedLoop=function(o){window.CP.PenTimer.exitedLoop(o)};

// Drop handler function to get all files
// many thanks to https://codepen.io/anon/pen/gBJrOP
async function getAllFileEntries(dataTransferItemList) {
  let fileEntries = [];
  // Use BFS to traverse entire directory/file structure
  let queue = [];
  // Unfortunately dataTransferItemList is not iterable i.e. no forEach
  for (let i = 0; i < dataTransferItemList.length; i++) {if (window.CP.shouldStopExecution(0)) break;
    var file = dataTransferItemList[i].webkitGetAsEntry();
    queue.push(file);
  }window.CP.exitedLoop(0);
  while (queue.length > 0) {if (window.CP.shouldStopExecution(1)) break;
    let entry = queue.shift();
    if (entry.isFile) {
      fileEntries.push(entry);
    } else if (entry.isDirectory) {
      let reader = entry.createReader();
      queue.push(...(await readAllDirectoryEntries(reader)));
    };
  }window.CP.exitedLoop(1);
  return fileEntries;
}

// Get all the entries (files or sub-directories) in a directory by calling readEntries until it returns empty array
async function readAllDirectoryEntries(directoryReader) {
  let entries = [];
  let readEntries = await readEntriesPromise(directoryReader);
  while (readEntries.length > 0) {if (window.CP.shouldStopExecution(2)) break;
    entries.push(...readEntries);
    readEntries = await readEntriesPromise(directoryReader);
  }window.CP.exitedLoop(2);
  return entries;
}

// Wrap readEntries in a promise to make working with readEntries easier
async function readEntriesPromise(directoryReader) {
  try {
    return await new Promise((resolve, reject) => {
      directoryReader.readEntries(resolve, reject);
    });
  } catch (err) {
    console.log(err);
  }
}

var elDrop = document.getElementById('uploader');
/*
we don't need this:
var elItems = document.getElementById('items');

elDrop.addEventListener('dragover', function (event) {
  event.preventDefault();
  elItems.innerHTML = 0;
});*/

elDrop.addEventListener('drop', async function (event) {
  event.preventDefault();
  let items = await getAllFileEntries(event.dataTransfer.items);
    for (var i=0; i<items.length; i++) {
        var 
        it = items[i],
        mapName = it.fullPath.replace('/'+it.name, '');

        map[it.fullPath] = mapName; // the only problem remaining is having files with the same name put into multiple sub-folders that you're uploading in the same batch (something to fix another time)
    }
});
</script>
</body>
</html>
