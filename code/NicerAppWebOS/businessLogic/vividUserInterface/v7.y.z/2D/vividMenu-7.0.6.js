class naVividMenu {
  constructor(menuElId = 'siteMenu', openBtnId = 'btnShowStartMenu') {
    this.menu = document.getElementById(menuElId);
    if (!this.menu) return;

    this.menu.className = 'vividMenu vividMenu_vertical vertical';

    const btn =
    document.getElementById(openBtnId) ||
    document.getElementById(openBtnId + '_container');
    if (btn) btn.menu = this.menu;

    if (this.menu.dataset.initialized) return;
    this.menu.dataset.initialized = 'true';

    const openPanels = new Set();
    let hideTimer = null;
    let openTimer = null;

    const clearHide = () => {
      clearTimeout(hideTimer);
      hideTimer = null;
    };
    const clearOpen = () => {
      clearTimeout(openTimer);
      openTimer = null;
    };

    const restorePanel = (sm) => {
      sm.style.display = 'none';
      sm.style.opacity = '0';
      openPanels.delete(sm);
      if (sm._homeParent) {
        if (sm._homeNext && sm._homeNext.parentNode === sm._homeParent) {
          sm._homeParent.insertBefore(sm, sm._homeNext);
        } else {
          sm._homeParent.appendChild(sm);
        }
      }
    };

    const closeAll = () => {
      for (const sm of [...openPanels]) restorePanel(sm);
    };

      /** Ancestors + self, via links set at init (valid after move-to-body) */
      const chainUp = (sm) => {
        const set = new Set();
        let p = sm;
        while (p) {
          set.add(p);
          p = p._parentSubmenu || null;
        }
        return set;
      };

      /** sm is node or nested under node in the panel chain */
      const isSelfOrDescendant = (node, sm) => {
        let p = sm;
        while (p) {
          if (p === node) return true;
          p = p._parentSubmenu || null;
        }
        return false;
      };

      const basePanelStyle = {
        position: 'fixed',
        display: 'none',
        opacity: '0',
        top: 'auto',
        left: '12px',
        width: '25vw',
        maxWidth: '25vw',
        minWidth: '0',
        height: 'auto',
        maxHeight: '65vh',
        overflow: 'auto',
        padding: '12px',
        borderRadius: '12px',
        background: 'rgba(15, 15, 35, 0.92)',
        border: '2px solid rgba(100, 180, 255, 0.55)',
        boxShadow: '0 8px 24px rgba(0,0,0,0.6)',
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: '12px',
        listStyle: 'none',
        margin: '0',
        boxSizing: 'border-box',
        zIndex: '1000000001'
      };

      const prepareItems = (root = this.menu) => {
        root.querySelectorAll('li').forEach((li) => {
          li.classList.add('menu-item', 'vividMenu_item');
          li.style.listStyle = 'none';
          if (li.querySelector(':scope > ul')) li.classList.add('has-submenu');
          li.style.position = 'relative';
        });
      };

      const initSubMenu = (item) => {
        const submenu = item.querySelector(':scope > ul');
        if (!submenu || item.dataset.initDone) return;
        item.dataset.initDone = 'true';

        submenu.classList.add('submenu');
        submenu._sourceItem = item;

        // Parent flyout UL while still in-tree (null = under root mainUL)
        const homeParentUl = item.parentElement;
        submenu._parentSubmenu =
        homeParentUl &&
        homeParentUl.tagName === 'UL' &&
        !homeParentUl.classList.contains('vividMenu_mainUL')
        ? homeParentUl
        : null;

        let depth = 0;
        let p = item.parentElement;
        while (p && p !== this.menu) {
          if (p.tagName === 'UL' && !p.classList.contains('vividMenu_mainUL')) depth++;
          p = p.parentElement;
        }
        submenu._depth = depth;

        // Hidden by default — do NOT open or register here
        Object.assign(submenu.style, basePanelStyle);
        submenu.querySelectorAll('li').forEach((li) => {
          li.style.listStyle = 'none';
        });

        const openSubmenu = () => {
          clearHide();
          clearOpen();

          // Keep only this panel + its parents; close siblings / other branches
          const keep = chainUp(submenu);
          for (const sm of [...openPanels]) {
            if (!keep.has(sm)) restorePanel(sm);
          }

          if (!submenu._homeParent) {
            submenu._homeParent = submenu.parentNode;
            submenu._homeNext = submenu.nextSibling;
          }
          document.body.appendChild(submenu);

          const taskbarH = document.getElementById('siteTaskbar')?.offsetHeight || 60;
          const d = submenu._depth || 0;

          Object.assign(submenu.style, {
            position: 'fixed',
            display: 'flex',
            opacity: '1',
            top: 'auto',
            left: (12+submenu._depth*72)+'px',
                        width: '25vw',
            maxWidth: '25vw',
            height: 'auto',
            maxHeight: '65vh',
            bottom: taskbarH + 12 + d * 72 + 'px',
            zIndex: String(1000000001 + d)
          });

          openPanels.add(submenu);

          // Wire deeper rows inside this panel (safe: initDone guards)
          prepareItems(submenu);
          submenu.querySelectorAll('li.has-submenu').forEach(initSubMenu);
        };

        const scheduleOpen = () => {
          clearHide();
          clearOpen();
          openTimer = setTimeout(() => {
            if (!item.matches(':hover')) return;
            openSubmenu();
          }, 500);
        };

        const scheduleHide = () => {
          clearOpen();
          clearHide();
          hideTimer = setTimeout(() => {
            // still on this row or this panel? keep
            if (item.matches(':hover') || submenu.matches(':hover')) return;

            // still on a *deeper* panel under this one? keep (moving L2 → L3)
            for (const sm of openPanels) {
              if (sm === submenu) continue;
              if (!sm.matches(':hover')) continue;
              if (isSelfOrDescendant(submenu, sm)) return;
            }

            // left this submenu → close it and anything deeper
            for (const sm of [...openPanels]) {
              if (isSelfOrDescendant(submenu, sm)) restorePanel(sm);
            }
          }, 500);
        };

        item.addEventListener('mouseenter', scheduleOpen);
        item.addEventListener('mouseleave', scheduleHide);

        submenu.addEventListener('mouseenter', () => {
          clearOpen();
          clearHide();
        });
        submenu.addEventListener('mouseleave', scheduleHide);
      };

      const showRootMenu = () => {
        const rootUL =
        this.menu.querySelector(':scope > ul.vividMenu_mainUL') ||
        this.menu.querySelector(
          ':scope > ul:not(.vividMenu_layout):not(.vividMenu_segments)'
        );

        Object.assign(this.menu.style, {
          position: 'fixed',
          display: 'flex',
          visibility: 'visible',
          opacity: '1',
          left : 12,
          bottom: '70px',
          top: 'auto',
          zIndex: '999999999'
        });

        if (rootUL) {
          rootUL.classList.remove('submenu');
          if (!rootUL.children.length) {
            const layout = this.menu.querySelector('.vividMenu_layout');
            if (layout) rootUL.innerHTML = layout.innerHTML;
          }
          Object.assign(rootUL.style, {
            display: 'flex',
            visibility: 'visible',
            opacity: '1'
          });
        }

        closeAll();
        this.menu.querySelectorAll('li.has-submenu').forEach((li) => {
          delete li.dataset.initDone;
        });
        prepareItems(this.menu);
        this.menu.querySelectorAll('li.has-submenu').forEach(initSubMenu);
      };

      if (btn && !btn.dataset.naMenuWired) {
        btn.dataset.naMenuWired = 'true';
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          showRootMenu();
        });
      }

      prepareItems(this.menu);
      this.menu.querySelectorAll('li.has-submenu').forEach(initSubMenu);

      document.addEventListener('click', (e) => {
        const onPanel = [...openPanels].some((p) => p.contains(e.target));
        if (
          !this.menu.contains(e.target) &&
          !(btn && btn.contains(e.target)) &&
          !onPanel
        ) {
          closeAll();
        }
      });

      console.log('naVividMenu: ready');
  }
}
