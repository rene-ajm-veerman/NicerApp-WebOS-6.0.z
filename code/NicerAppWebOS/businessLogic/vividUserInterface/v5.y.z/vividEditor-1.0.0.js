// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
class naVividEditor {
    constructor(selector) {
        var t = this;
        $(selector).css({ display : 'none' });

        var
        id = $(selector)[0].id,
        html = '<div id="'+id+'_toolbar" class="vividEditor_toolbar">';

        //html+=      '<div class="vividEditor_toolbar_caretPosition"></div>';
        //html+=      '<label>Start: <input id="start-container" type="text"/><input id="start-offset" type="number"/></label><br/>';
        //html+=      '<label>End: <input id="end-container" type="text"/><input id="end-offset" type="number"/></label><br/>';
        //html+=      '<button id="set">Set</button>';
        html+=      '<div id="'+id+'_toolbar__blockType" class="vividEditor_toolbar_dropdown">';
        html+=          '<div id="'+id+'_toolbar__blockType_selected" class="vividEditor_toolbar_dropdown__selected vividDropDownBox_selected vividScrollpane">Paragraph</div>';
        html+=          '<div id="'+id+'_toolbar__blockType_selector" class="vividEditor_toolbar_dropdown__selector vividDropDownBox_selector"><div class="vividScrollpane"><div>Heading 1</div><div>Heading 2</div><div>Heading 3</div><div>Heading 4</div><div>Paragraph</div></div></div>';
        html+=      '</div>';
        html+=      '<div id="'+id+'_toolbar__textAnimation" class="vividEditor_toolbar_dropdown">';
        html+=          '<div id="'+id+'_toolbar__textAnimation_selected" class="vividEditor_toolbar_dropdown__selected vividDropDownBox_selected vividScrollpane">No animations</div>';
        html+=          '<div id="'+id+'_toolbar__textAnimation_selector" class="vividEditor_toolbar_dropdown__selector vividDropDownBox_selector"><div class="vividScrollpane"><div>Heading 1</div><div>Heading 2</div><div>Heading 3</div><div>No animations</div></div></div>';
        html+=      '</div>';
        html+= '</div>';
        html+= '<div id="'+id+'_main" class="vividEditor_main" contenteditable="true">';
        html+= '<h1>Heading 1</h1>';
        html+= '<p>You can begin editing your text here.<br/>Press Shift-Enter to start a new paragraph.</p>';
        html+= '</div>';

        $(selector).after(html);

        var el = $('#'+id+'_main')[0];
        t.el = el;
        t.id = id;
        t.t = t;
        el.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                document.execCommand('insertLineBreak')
                event.preventDefault()
            }
            else if (event.key === 'Enter' && event.shiftKey) {
                var el = $('.vividEditor_main')[0];
                var range = document.createRange()
                var sel = window.getSelection()
                document.execCommand('insertLineBreak')
                document.execCommand('insertBrOnReturn', false, false);
                var el2 = el.childNodes[el.childNodes.length-1];
                range.setStart(el2, el2.childNodes.length-1)
                range.collapse(true)

                sel.removeAllRanges()
                sel.addRange(range)
                document.execCommand('insertHTML', false, '<p>&nbsp;</p>');
                range.setStart(el.childNodes[el.childNodes.length-1], 0)
                range.collapse(true)

                sel.removeAllRanges()
                sel.addRange(range)
                event.preventDefault()
            }
        });

        el.addEventListener('keyup', (event) => {
            if (event.isComposing || event.keyCode === 229) {
                return;
            }

            t.setToolbar_blockType(t);
            t.setToolbar_textAnimation(t);

            /* let's NOT auto-save things, but rather let the end-user press the "publish" button.
            clearTimeout (t.timeout_save);
            t.timeout_save = setTimeout (function() {
                var
                tree = $('#jsTree').jstree(true),
                sel = tree.get_node(tree.get_selected()[0]);
                na.cms.saveEditorContent(sel, function(rec) {
                });
            }, 1000);
            */
        });

        el.addEventListener('mouseup', (event) => {
            if (event.currentTarget===t.el) {
            t.setToolbar_blockType(t);
            t.setToolbar_textAnimation(t);
            }

            /* let's NOT auto-save things, but rather let the end-user press the "publish" button.
            clearTimeout (t.timeout_save);
            t.timeout_save = setTimeout (function() {
                var
                tree = $('#jsTree').jstree(true),
                sel = tree.get_node(tree.get_selected()[0]);
                na.cms.saveEditorContent(sel, function(rec) {
                });
            }, 1000);
            */
        });



        $('#'+id+'_toolbar__blockType').hover(function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            $('#'+id+'_toolbar__blockType > .vividDropDownBox_selector').fadeIn('normal');
        }, function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            na.site.settings.current.timeout_onmouseout_themes = setTimeout (function() {
                $('#'+id+'_toolbar__blockType > .vividDropDownBox_selector').fadeOut('normal');
            }, 500);
        });
        $('#'+id+'_toolbar__blockType > .vividDropDownBox_selector').mouseover(function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
        });
        $('#'+id+'_toolbar__blockType > .vividDropDownBox_selector > .vividScrollpane > div').mousedown(function(evt) {
            $('#'+id+'_toolbar__blockType > .vividDropDownBox_selected').html($(this).html());
            $('#'+id+'_toolbar__blockType > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
            $(this).addClass('selected');
            switch ($(this).html()) {
                case 'Heading 1': t.setMain_blockType(t,'H1'); break;
                case 'Heading 2': t.setMain_blockType(t,'H2'); break;
                case 'Heading 3': t.setMain_blockType(t,'H3'); break;
                case 'Heading 4': t.setMain_blockType(t,'H4'); break;
                case 'Paragraph': t.setMain_blockType(t,'P'); break;
            }
        });


        $('#'+id+'_toolbar__textAnimation').hover(function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selector').fadeIn('normal');
        }, function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            na.site.settings.current.timeout_onmouseout_themes = setTimeout (function() {
                $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selector').fadeOut('normal');
            }, 500);
        });
        $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selector').mouseover(function() {
            clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
        });
        $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selector > .vividScrollpane > div').mousedown(function(evt) {
            $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selected').html($(this).html());
            $('#'+id+'_toolbar__textAnimation > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
            $(this).addClass('selected');
            switch ($(this).html()) {
                case 'Heading 1': t.setMain_textAnimation(t,'H1'); break;
                case 'Heading 2': t.setMain_textAnimation(t,'H2'); break;
                case 'Heading 3': t.setMain_textAnimation(t,'H3'); break;
                case 'No animations': t.setMain_textAnimation(t,false); break;
            }
        });



        na.m.waitForCondition ('vividEditor initialized?', function () {
            return (
                $('#nb_url2_value').val()!==''
                && na.cms.settings.current.db
            );
        }, function () {

            na.cms.tinymce_link_list(function(ret) {
                var html = '';
                for (var i=0; i<ret.length; i++) {
                    var dit = ret[i];
                    html += '<div>'+dit.value+'</div>';
                };
                $('#naInsertLink_dialog_input_link_selector > .vividScrollpane').html(html);
            });

            $('#naInsertLink_dialog_input_link').hover(function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
                $('#naInsertLink_dialog_input_link > .vividDropDownBox_selector').fadeIn('normal');
            }, function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
                na.site.settings.current.timeout_onmouseout_themes = setTimeout (function() {
                    $('#naInsertLink_dialog_input_link > .vividDropDownBox_selector').fadeOut('normal');
                }, 500);
            });
            $('#naInsertLink_dialog_input_link > .vividDropDownBox_selector').mouseover(function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            });
            $('#naInsertLink_dialog_input_link > .vividDropDownBox_selector > .vividScrollpane > div').mousedown(function(evt) {
                $('#naInsertLink_dialog_input_link > .vividDropDownBox_selected').html($(this).html());
                $('#naInsertLink_dialog_input_link > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
                $(this).addClass('selected');
                t.setMain_link(t, event, $(this).html());
            });

            var html =
                '<div>None</div>'
                +'<div>New window</div>';
            $('#naInsertLink_dialog_input_target_selector > .vividScrollpane').html(html);

            $('#naInsertLink_dialog_input_target').hover(function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
                $('#naInsertLink_dialog_input_target > .vividDropDownBox_selector').fadeIn('normal');
            }, function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
                na.site.settings.current.timeout_onmouseout_themes = setTimeout (function() {
                    $('#naInsertLink_dialog_input_target > .vividDropDownBox_selector').fadeOut('normal');
                }, 500);
            });
            $('#naInsertLink_dialog_input_target > .vividDropDownBox_selector').mouseover(function() {
                clearTimeout(na.site.settings.current.timeout_onmouseout_themes);
            });
            $('#naInsertLink_dialog_input_target > .vividDropDownBox_selector > .vividScrollpane > div').mousedown(function(evt) {
                $('#naInsertLink_dialog_input_target > .vividDropDownBox_selected').html($(this).html());
                $('#naInsertLink_dialog_input_target > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
                $(this).addClass('selected');
                t.setMain_target(t, event, $(this).html());
            });

        }, 100);




        return this;
    }


    // utility functions
    setToolbar_blockType (t) {
        var
        pos = t.getCaretPosition(t.el),
        el = pos.start.container[0].el,
        posSimple = t.getCaretPosition_simple(t.el),
        newEl = document.createElement(pos.start.container[0].tag); // seems to do nothing, yet makes it work. strange.
        for (var i=pos.start.container[0].index; i<pos.end.container[0].index; i++) {
            $(newEl).append($(pos.end.container[0].el.children[i]).clone(true,true));
        };
        var
        tag = pos.start.container[pos.start.container.length-1].tag;
        t.pos = pos;
        t.posSimple = posSimple;
        t.sel = $.extend({},window.getSelection());
        t.rng = window.getSelection().getRangeAt(0);
        t.rngStr = t.rng.toString();


        t.tod1 = t.el.innerHTML.slice(0,posSimple[0]);
        t.tod2 = t.el.innerHTML.slice(posSimple[0],posSimple[1]);
        t.tod3 = t.el.innerHTML.slice(posSimple[1],-1);
        $('#naInsertLink_dialog_input_tod').html($(document.createElement('div')).html(t.tod2));

        switch(tag) {
            case 'H1': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 1'); break;
            case 'H2': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 2'); break;
            case 'H3': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 3'); break;
            case 'H4': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 4'); break;
            case 'P': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Paragraph'); break;
            default:
                tag = pos.start.container[pos.start.container.length-2].tag;
                switch(tag) {
                    case 'H1': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 1'); break;
                    case 'H2': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 2'); break;
                    case 'H3': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 3'); break;
                    case 'H4': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Heading 4'); break;
                    case 'P': $('#'+t.id+'_toolbar__blockType > .vividDropDownBox_selected').html('Paragraph'); break;
                }
                break;
        }
    }

    setMain_blockType (t, tagName) {
        $(t.el).focus()
        var
        pos = t.getCaretPosition(t.el),
        tag = pos.start.container[0].tag,
        el = pos.start.container[0].el;
        if (tag===tagName) var newEl = el; else var newEl = document.createElement(tagName);

        newEl.innerHTML = $(el).html();

        el.parentNode.replaceChild (newEl, el);
    }

    setToolbar_textAnimation (t) {
        var
        pos = t.getCaretPosition(t.el),
        tag = (
            pos.start.container.length >= 2
            ? pos.start.container[pos.start.container.length-2].tag
            : pos.start.container[pos.start.container.length-1].tag
        );
        switch (tag) {
            case 'H1': $('#'+t.id+'_toolbar__textAnimation > .vividDropDownBox_selected').html('Heading 1'); break;
            case 'H2': $('#'+t.id+'_toolbar__textAnimation > .vividDropDownBox_selected').html('Heading 2'); break;
            case 'H3': $('#'+t.id+'_toolbar__textAnimation > .vividDropDownBox_selected').html('Heading 3'); break;
            case 'P' : $('#'+t.id+'_toolbar__textAnimation > .vividDropDownBox_selected').html('No animations'); break;
        }
    }

    setMain_textAnimation (t, tagName) {
        $(t.el).focus()
        var
        pos = t.getCaretPosition(t.el),
        tag = pos.start.container[0].tag,
        el = pos.start.container[0].el;

        if (tag===tagName) var newEl = el; else var newEl = document.createElement(tagName);

        $(el).removeClass('contentSectionTitle1').removeClass('contentSectionTitle2').removeClass('contentSectionTitle3');

        switch (tagName) {
            case 'H1' : newEl.innerHTML = '<span class="contentSectionTitle1_span">'+$(el).html()+'</span>'; break;
            case 'H2' : newEl.innerHTML = '<span class="contentSectionTitle2_span">'+$(el).html()+'</span>';  break;
            case 'H3' : newEl.innerHTML = '<span class="contentSectionTitle3_span">'+$(el).html()+'</span>';  break;
            default :
                var newEl = document.createElement('p');
                newEl.innerHTML = $(el).html(); break;
        }

        el.parentNode.replaceChild(newEl, el);
    }

    insertLink (evt) {
        $('#naInsertLink_dialog').fadeIn('slow');
    }

    setMain_link (t, evt, link) {
        $('#naInsertLink_dialog_input_url').val(link);
    }

    setMain_target (t, evt, link) {
    }

    saveLink (t, evt) {

        var html = '<a href="'+$('#naInsertLink_dialog_input_url').val()+'">' + t.tod2 + '</a>'
        t.el.innerHTML = t.tod1 + html + t.tod3;
        /*
        var sel, range, node;
        if (window.getSelection) {
            sel = t.sel;
            if (sel.getRangeAt && sel.rangeCount) {
                range = t.rng;//window.getSelection().getRangeAt(0);

                var html = '<a href="'+$('#naInsertLink_dialog_input_url').val()+'">' + t.tod + '</a>'
                range.deleteContents();

                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
            }
        } else if (document.selection && document.selection.createRange) {
            range = document.selection.createRange();
            range.collapse(false);
            range.pasteHTML(html);
        }
        */
    }

    // helper functions
    getCaretPosition (editableDiv) { // many thanks to https://stackoverflow.com/questions/46434775/accounting-for-brs-in-contenteditable-caret-position?noredirect=1&lq=1 and https://www.freecodecamp.org/news/three-dots-operator-in-javascript/
        const $el = $(editableDiv);

        function pathFromNode(node, reference) {
        function traverse(node, acc) {
            if (node === reference) {
            acc.shift();
            return acc;
            } else {
            const parent = node.parentNode;
            if (!parent) return acc;
            const index = [...parent.childNodes].indexOf(node);
            return traverse(parent, [{index:index,tag:parent.tagName,el:parent}, ...acc]);
            }
        }
        return traverse(node, []);
        }

        function nodeFromPath(path, reference) {
        if (path.length === 0) {
            return reference;
        } else {
            const [index, ...rest] = path;
            const next = reference.childNodes[index];
            return nodeFromPath(rest, next);
        }
        }

        function getCaret(el) {
        const range = document.getSelection().getRangeAt(0);
        return {
            start: {
            container: pathFromNode(range.startContainer, el),
            offset: range.startOffset
            },
            end: {
            container: pathFromNode(range.endContainer, el),
            offset: range.endOffset
            }
        };
        }

        function setCaret(el, start, end) {
        const range = document.createRange();
        range.setStart(nodeFromPath(start.container, el), start.offset);
        range.setEnd(nodeFromPath(end.container, el), end.offset);
        sel = document.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
        }

        return getCaret($el[0]);
    };

    getCaretPosition_simple (el) {
        // many thanks to https://stackoverflow.com/questions/3972014/get-contenteditable-caret-position


        // node_walk: walk the element tree, stop when func(node) returns false
        function node_walk(node, elem, func) {
            var result = func(node, elem);
            for(node = node.firstChild; result !== false && node; node = node.nextSibling) {
                if (parent.parentNode!==node.parentNode) parent = node;
                result = node_walk(node, elem, func);
            }
            return result;
        };

        // getCaretPosition: return [start, end] as offsets to elem.textContent that
        //   correspond to the selected portion of text
        //   (if start == end, caret is at given position and no text is selected)
        function getCaretPosition(elem) {
        var debug = true;
        var sel = window.getSelection();
        var pos = [0, 0];
        var pos2 = [0, 0];

        if(sel.anchorNode === elem)
            pos = pos2 = [sel.anchorOffset, sel.extentOffset];
        else {
            var debug = true;
            var nodes_to_find = [sel.anchorNode, sel.extentNode];
            if(!elem.contains(sel.anchorNode) || !elem.contains(sel.extentNode))
            return undefined;
            else {
            var found = [0,0];
            var i;
            if (debug) console.log ('before node_walk');
            node_walk(elem, elem, function(node, elem) {
                for(i = 0; i < 2; i++) {
                if(node == nodes_to_find[i]) {
                    found[i] = true;
                    if(found[i == 0 ? 1 : 0])
                    return false; // all done
                }
                }

                function s(i,idx) {
                    return 'status ('+i+', '+idx+') ';
                }

                for(i = 0; i < 2; i++) {
                    if(found[i]!==true){
                        if (node!==elem) {
                            if (i===1) console.log (i, node);
                            if (node.tagName=='P') {
                                if (i===0) pos[i] += node.outerHTML.replace(node.innerHTML,'').replace(/<\/p>/,'').length;
                                if (i===1) pos[i] += node.outerHTML.replace(/<[^>]+>/,'').replace(node.innerHTML,'').length;
                                //if (i===1) {
                                    console.log (s(i,1), node.outerHTML.replace(node.innerHTML,'').length, pos, '"'+elem.innerHTML.substr(pos[0],pos[1]-pos[0])+'"', '"'+elem.innerHTML.substr(pos[0],10)+'"');
                                //}
                            } else if (node.tagName=='BR') {
                                if (i===1) pos[i] += node.outerHTML.replace(node.innerHTML,'').length;
                                //if (i===1) {
                                    console.log (s(i,2), node.outerHTML.replace(node.innerHTML,'').length, pos, '"'+elem.innerHTML.substr(pos[0],pos[1]-pos[0])+'"', '"'+elem.innerHTML.substr(pos[0],10)+'"');
                                //}
                            } else if (typeof node.outerHTML=='string' && node.outerHML!=='') {
                                pos[i] += node.outerHTML.replace(node.innerHTML,'').length;
                                //if (i===1) {
                                    console.log (s(i,3), node.outerHTML.replace(node.innerHTML,'').length, pos, '"'+elem.innerHTML.substr(pos[0],pos[1]-pos[0])+'"', '"'+elem.innerHTML.substr(pos[0],10)+'"');
                                //}
                            } else if (node.textContent && node.innerHTML) {
                                pos[i] += node.textContent.length + (node.innerHTML.match(/&nbsp;/g).length*6);// - (node.innerHTML.match(/&nbsp;/g).length);
                                //if (i===1) {
                                    console.log (s(i,4), node.textContent.length + (node.innerHTML.match(/&nbsp;/g).length*6), pos, ' "'+elem.innerHTML.substr(pos[0],pos[1]-pos[0])+'"', '"'+elem.innerHTML.substr(pos[0],10)+'"');
                                //}
                            } else if (node.textContent) {
                                pos[i] += node.textContent.length;
                                //if (i===1) {
                                    console.log (s(i,5), node.textContent.length, pos, '"'+elem.innerHTML.substr(pos[0],pos[1]-pos[0])+'"', '"'+elem.innerHTML.substr(pos[0],10)+'"');
                                //}

                            }
                        }
                        if (node.textContent && !node.firstChild)
                        pos2[i] += node.textContent.length;
                    }
                    //console.log (0, elem.innerHTML.substr(pos[0], pos[1] - pos[0]));
                }
                //for(i = 0; i < 2; i++) { if ( found[i]!==0 && node.tagName=='BR') pos[i] += 4; }
                //for(i = 0; i < 2; i++) { if (node.tagName=='BR') pos2[i] += 1; }
            });
            pos[0] += sel.anchorOffset;
            pos[1] += sel.extentOffset;
            pos2[0] += sel.anchorOffset;
            pos2[1] += sel.extentOffset;
            var
            m11 = pos2[0] < pos2[1] ? pos2[1] : pos2[0],
            m12 = pos[0] < pos[1] ? pos[1] : pos[0],
            m13 = pos[0] < pos[1] ? pos[0] : pos[1],
            m2 = elem.innerHTML.substr(m13,m12-m13).match(/&nbsp;/g),
            m3 = elem.innerHTML.substr(0,m12).match(/<br>/g),
            m1 = elem.innerHTML.substr(0,m12).match(/<\/p>/g),
            m4 = elem.innerHTML.substr(0,m12).match(/<p>/g),
            p1 = {
                fragOpen : elem.innerHTML.substr(0,m12).match(/<p>/g),
                fragClose : elem.innerHTML.substr(0,m12).match(/<\/p>/g),
                insideOpen : elem.innerHTML.substr(m13,m12-m13).match(/<p>/g),
                insideClose : elem.innerHTML.substr(m13,m12-m13).match(/<\/p>/g)
            };

            debugger;
            if (p1.fragOpen && p1.fragClose) {
                pos[0] -= (p1.fragOpen.length*3)-(p1.fragClose.length*4);
                pos[1] -= (p1.fragOpen.length*3)-(p1.fragClose.length*4);
            } else {
                if (p1.insideOpen) {
                    pos[0] -= (p1.insideOpen.length*3);
                    pos[1] -= (p1.insideOpen.length*3);
                }
                if (p1.fragClose) {
                    pos[0] -= (p1.fragClose.length*4);
                    pos[1] -= (p1.fragClose.length*4);
                }
            }
            /*
            if (p1.insideClose) {
                pos[0] -= (p1.insideClose.length*4);
                pos[1] -= (p1.insideClose.length*4);
            }*/
            debugger;
            /*
            console.log (1, elem.innerHTML.substr(pos[0], pos[1] - pos[0]));
            pos[0] -= m1?(m1.length*4):0;
            pos[1] -= m1?(m1.length*4):0;
            console.log (2, elem.innerHTML.substr(pos[0], pos[1] - pos[0]));
            if (m4&&m1) {
                pos[0] -= m4&&m1?(m4.length>m1.length?m1.length*4:m4.length*4):0;
                pos[1] -= m4&&m1?(m4.length>m1.length?m1.length*4:m4.length*4):0;
            } else if (m4) {
                pos[0] -= m4?(m4.length*4):0;
                pos[1] -= m4?(m4.length*4):0;
            }
            console.log (3, elem.innerHTML.substr(pos[0], pos[1] - pos[0]));
            pos[0] += m3?m3.length+1:0;
            pos[1] += m3?m3.length+1:0;
             console.log (4, elem.innerHTML.substr(pos[0], pos[1] - pos[0]));
            */
            if (debug) {
                console.log (pos);
                console.log ('after node_walk');
            }
            }
        }
        if(pos[0] <= pos[1])
            return pos;
        return [pos[1], pos[0]];
        }





        function printCaretPosition(el){
            function cursor_position(el) {
                var sel = document.getSelection();
                sel.modify("extend", "forward", "character");
                var selText = sel.toString();
                debugger;
                var pos = el.innerHTML.indexOf(selText.replace('\n','<br/>'));
                //if(sel.anchorNode != undefined) sel.collapseToEnd();

                return [ pos, pos + sel.toString().length ];
            }
            //console.log( cursor_position(el), 'length:', el.textContent.trim().length )
            return cursor_position(el);
        }

        //return printCaretPosition(el);
        return getCaretPosition(el);
    }
};
