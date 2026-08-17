class naVividMenu {
  constructor(menuElId = 'siteMenu', openBtnId = 'btnShowStartMenu') {
    this.menu = document.getElementById(menuElId);
    if (!this.menu) return;

    $(this.menu).attr('class', 'vividMenu vividMenu_vertical vertical');

    const btn = document.getElementById(openBtnId)
    || document.getElementById(openBtnId + '_container');
    if (btn) btn.menu = this.menu;

    if (this.menu.dataset.initialized) return;
    this.menu.dataset.initialized = 'true';

    // Constructor-scope (once), shared by all items:
    let branchHideTimer = null;
    const openPanels = new Set(); // each open submenu UL registers itself

    const cancelBranchHide = () => {
      clearTimeout(branchHideTimer);
      branchHideTimer = null;
    };

    const isPointerOverMenuSystem = () => {
      if (this.menu.matches(':hover')) return true;
      if (btn && btn.matches(':hover')) return true;
      for (const panel of openPanels) {
        if (panel.matches(':hover')) return true;
      }
      return false;
    };

    // Per-item helpers inside initSubMenu(item):
    const cancelHide = () => cancelBranchHide();

    const isPointerOverBranch = () => {
      // still on this row
      if (item.matches(':hover')) return true;
      // still on this panel
      if (submenu.matches(':hover')) return true;
      // still on any open descendant panel (L3+), even if reparented to body
      for (const panel of openPanels) {
        if (!panel.matches(':hover')) continue;
        // panel is this submenu or was nested under this item's original branch
        if (panel === submenu) return true;
        if (item.contains(panel)) return true;
        if (panel._homeParent && item.contains(panel._homeParent)) return true;
        if (panel._sourceItem && (item === panel._sourceItem || item.contains(panel._sourceItem))) {
          return true;
        }
      }
      return false;
    };

    const scheduleHide = () => {
      cancelHide();
      branchHideTimer = setTimeout(() => {
        // left the whole menu (all fixed panels + root)? close everything
        if (!isPointerOverMenuSystem()) {
          closeAll();
          openPanels.clear();
          return;
        }
        // still somewhere in this branch? keep this panel
        if (isPointerOverBranch()) return;

        // left this branch only — hide this panel (not descendant logic here)
        hideSubmenu();
        openPanels.delete(submenu);
      }, 280);
    };

    let currentlyOpen = null;

    const closeAll = () => {
      this.menu.querySelectorAll('ul.submenu, li > ul').forEach(sm => {
        if (sm.classList.contains('vividMenu_mainUL')) return;
        sm.style.display = 'none';
        sm.style.opacity = '0';
      });
      currentlyOpen = null;
    };

    // Close other branches, but keep ancestors of `keepItem` open
    const closeOthers = (keepItem) => {
      this.menu.querySelectorAll('li.has-submenu').forEach(li => {
        const sm = li.querySelector(':scope > ul');
        if (!sm) return;
        // keep this item’s submenu and any ancestor li’s submenu
        if (keepItem === li || keepItem.contains(li) || li.contains(keepItem)) {
          return;
        }
        sm.style.display = 'none';
        sm.style.opacity = '0';
      });
    };

    const openSubmenu = () => {
      submenu._sourceItem = item;
      openPanels.add(submenu);
      cancelHide();

      closeOthers(item);   // NOT closeAll()
      submenu.classList.add('submenu');
      submenu.style.display = 'flex';
      submenu.style.opacity = '1';
      currentlyOpen = submenu;
      openPanels.add (submenu);

      const rect = item.getBoundingClientRect();
      let left = rect.right + 8;
      let top = rect.top;
      const approxWidth = Math.min(420, window.innerWidth * 0.45);
      if (left + approxWidth > window.innerWidth - 8) {
        left = Math.max(8, rect.left - approxWidth - 8);
      }
      if (top < 8) top = 8;
      submenu.style.left = left + 'px';
      submenu.style.top = top + 'px';
      submenu.style.position = 'fixed';
      submenu.style.zIndex = '1000000001';
    };

    const showRootMenu = () => {
      const menu = this.menu;
      const rootUL = menu.querySelector(':scope > ul.vividMenu_mainUL')
      || menu.querySelector(':scope > ul:not(.vividMenu_layout):not(.vividMenu_segments)');

      btn.menu.style.cssText += 'bottom:300px;display:fixed!important;visibility:visible!important;opacity:1!important;z-index:999999999!important;';
      // keep your layout intent; adjust bottom/top as needed:
      // menu.style.bottom = '60px'; menu.style.top = 'auto';

      if (rootUL) {
        rootUL.classList.remove('submenu');
        rootUL.style.cssText += ';display:flex!important;visibility:visible!important;opacity:1!important;';

      }

      // if mainUL is still empty, fall back to first layout (debug / recovery)
      if (rootUL && !rootUL.children.length) {
        const layout = btn.menu.querySelector('.vividMenu_layout');
        if (layout) {
          rootUL.innerHTML = layout.innerHTML;
          rootUL.style.display = 'flex';
        }
      }

      closeAll();
      prepareItems();
      //sbtn.menu.querySelectorAll('.has-submenu').forEach(initSubMenu);
      delete menu.dataset.initialized; // only if you fully re-construct
      prepareItems();
      menu.querySelectorAll('li.has-submenu').forEach(li => {
        delete li.dataset.initDone;
      });
      menu.querySelectorAll('li.has-submenu').forEach(initSubMenu);

    };

    // … keep prepareItems / initSubMenu / openSubmenu as they are,
    // but REMOVE the openBtnId block from inside initSubMenu …

    // Wire the button ONCE, here:
    if (btn && !btn.dataset.naMenuWired) {
      btn.dataset.naMenuWired = 'true';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        $('#siteLogin').stop(true, true).fadeOut('fast').css({ top: -750 });
        showRootMenu();
      });
    }

    const prepareItems = () => {
      this.menu.querySelectorAll('li').forEach(li => {
        li.classList.add('menu-item', 'vividMenu_item');
        if (li.querySelector(':scope > ul')) {
          li.classList.add('has-submenu');
        }
        li.style.position = 'relative';
        li.style.height = 'auto';
        if (!li.hasAttribute('tabindex')) li.setAttribute('tabindex', '0');
      });
    };

    const initSubMenu = (item) => {
      if (item.dataset.initDone) return;
      item.dataset.initDone = 'true';

      const submenu = item.querySelector(':scope > ul');
      if (!submenu) return;

      submenu.classList.add('submenu');
      Object.assign(submenu.style, {
        position: 'fixed',
        zIndex: '1000000001',
        display: 'none',
        opacity: '0',
        minWidth: '220px',
        maxHeight: '65vh',
        overflow: 'auto',
        padding: '12px',
        borderRadius: '12px',
        background: 'rgba(15, 15, 35, 0.92)',
                    border: '2px solid rgba(100, 180, 255, 0.55)',
                    boxShadow: '0 8px 24px rgba(0,0,0,0.6)',
                    flexDirection: 'row',
                    flexWrap: 'wrap',
                    gap: '12px'
      });

      let hideTimer = null;

      const cancelHide = () => {
        clearTimeout(hideTimer);
        hideTimer = null;
      };

      const hideSubmenu = () => {
        submenu.style.display = 'none';
        submenu.style.opacity = '0';
        if (currentlyOpen === submenu) currentlyOpen = null;
        submenu.style.display = 'none';
        submenu.style.opacity = '0';
        openPanels.delete (submenu);
        if (submenu._homeParent) {
          submenu._homeParent.insertBefore(submenu, submenu._homeNext);
        }
      };



        const scheduleHide = () => {
          cancelHide();
          hideTimer = setTimeout(() => {
            // still over item or this submenu (or a child submenu)? keep open
            if (item.matches(':hover') || submenu.matches(':hover')) return;
            if (submenu.querySelector(':hover')) return;
            hideSubmenu();
          }, 250);
        };

        const openSubmenu = (item, submenu) => {
          if (!submenu._homeParent) {
            submenu._homeParent = submenu.parentNode;
            submenu._homeNext = submenu.nextSibling;
          }
          document.body.appendChild(submenu);

          const depth = (() => {
            let d = 0, p = item.parentElement;
            while (p && p !== this.menu) {
              if (p.tagName === 'UL' && !p.classList.contains('vividMenu_mainUL')) d++;
              p = p.parentElement;
            }
            return d;
          })();

          const taskbarH = document.getElementById('siteTaskbar')?.offsetHeight || 60;
          Object.assign(submenu.style, {
            position: 'fixed',
            display: 'flex',
            top: 'auto',
            left: '12px',
            bottom: (taskbarH + 12 + depth * 72) + 'px',
                        zIndex: String(1000000001 + depth),
                        opacity: '1'
          });

          panelMeta.set(submenu, {
            item,
            parentPanel: item.parentElement?.closest?.('ul:not(.vividMenu_mainUL)') || null
          });
          openPanels.add(submenu);
        };
    }

    const initAll = () => {
      prepareItems();
      this.menu.querySelectorAll('.has-submenu').forEach(initSubMenu);
    };

    initAll();
    new MutationObserver(initAll).observe(this.menu, { childList: true, subtree: true });

    document.addEventListener('click', (e) => {
      if (!this.menu.contains(e.target) && !(btn && btn.contains(e.target))) {
        closeAll();
      }
    });

    console.log('naVividMenu: ready', this.menu);
  }
}
