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

    // constructor scope
    const openPanels = new Set();
    let hideTimer = null;
    let openTimer = null;

    const clearHide = () => { clearTimeout(hideTimer); hideTimer = null; };
    const clearOpen = () => { clearTimeout(openTimer); openTimer = null; };


    //const openPanels = new Set();

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

    /** This panel + every parent panel up to root */
    const chainUp = (sm) => {
      const set = new Set();
      let p = sm;
      while (p) {
        set.add(p);
        p = p._parentSubmenu || null;
      }
      return set;
    };

    /** True if `sm` is `node` or a descendant of `node` in the panel chain */
    const isSelfOrDescendant = (node, sm) => {
      let p = sm;
      while (p) {
        if (p === node) return true;
        p = p._parentSubmenu || null;
      }
      return false;
    };
    /** Items (LIs) that form the path from root → currentItem after panels were moved to body */
    const pathItemsFor = (currentItem) => {
      const path = new Set();
      let el = currentItem;

      while (el && el !== document.body) {
        if (el.tagName === 'LI') path.add(el);

        // if we're inside a flyout panel, include its owning row
        if (el.tagName === 'UL' && el._sourceItem) {
          path.add(el._sourceItem);
          el = el._sourceItem; // continue upward from that row
          continue;
        }
        el = el.parentElement;
      }

      // any open panel that currently contains currentItem is also on the path
      for (const sm of openPanels) {
        if (sm.contains(currentItem) && sm._sourceItem) {
          path.add(sm._sourceItem);
        }
      }
      return path;
    };

    /** Keep only panels whose source row is on the path to `currentItem` */
    const keepOnlyPath = (currentItem) => {
      const path = pathItemsFor(currentItem);
      for (const sm of [...openPanels]) {
        if (sm._sourceItem && path.has(sm._sourceItem)) continue;
        if (sm.contains(currentItem)) continue; // panel we're inside
        restorePanel(sm);
      }
    };

    const closeAll = () => {
      for (const sm of [...openPanels]) restorePanel(sm);
    };

    const prepareItems = () => {
      this.menu.querySelectorAll('li').forEach((li) => {
        li.classList.add('menu-item', 'vividMenu_item');
        if (li.querySelector(':scope > ul')) li.classList.add('has-submenu');
        li.style.position = 'relative';
      });
    };

    const initSubMenu = (item) => {
      const submenu = item.querySelector(':scope > ul');
      if (!submenu || item.dataset.initDone) return;
      item.dataset.initDone = 'true';

      submenu._sourceItem = item;

      // parent flyout UL, or null if under root mainUL — computed while still in-tree
      const homeParentUl = item.parentElement; // the UL that currently contains this li
      submenu._parentSubmenu =
      homeParentUl && !homeParentUl.classList.contains('vividMenu_mainUL')
      ? homeParentUl
      : null;

      let depth = 0;
      let p = item.parentElement;
      while (p && p !== this.menu) {
        if (p.tagName === 'UL' && !p.classList.contains('vividMenu_mainUL')) depth++;
        p = p.parentElement;
      }
      submenu._depth = depth;


      const taskbarH = document.getElementById('siteTaskbar')?.offsetHeight || 60;
      Object.assign(submenu.style, {
        position: 'fixed',
        display: 'flex',
        opacity: '1',
        top: 'auto',
        left: '12px',
        width: '25vw',
        maxWidth: '25vw',
        bottom: taskbarH + 12 + depth * 72 + 'px',
        zIndex: String(1000000001 + depth)
      });

      openPanels.add(submenu);

      // wire deeper levels inside this panel
      submenu.querySelectorAll('li').forEach((li) => {
        if (li.querySelector(':scope > ul')) li.classList.add('has-submenu');
      });
      submenu.querySelectorAll('li.has-submenu').forEach(initSubMenu);
    };

      const scheduleOpen = () => {
        clearHide();
        clearOpen();
        openTimer = setTimeout(() => {
          if (!item.matches(':hover')) return;
          openSubmenu();
        }, 150);
      };

      const openSubmenu = () => {
        clearHide();
        clearOpen();

        const keep = chainUp(submenu); // parents + self

        for (const sm of [...openPanels]) {
          if (!keep.has(sm)) restorePanel(sm); // siblings & other branches gone
        }

        if (!submenu._homeParent) {
          submenu._homeParent = submenu.parentNode;
          submenu._homeNext = submenu.nextSibling;
        }
        document.body.appendChild(submenu);

        const taskbarH = document.getElementById('siteTaskbar')?.offsetHeight || 60;
        const depth = submenu._depth || 0;

        Object.assign(submenu.style, {
          position: 'fixed',
          display: 'flex',
          flexDirection: 'row',
          flexWrap: 'wrap',
          opacity: '1',
          top: 'auto',
          left: '12px',          // same left for all — stack by bottom, not columns
          width: '25vw',
          maxWidth: '25vw',
          height: 'auto',        // critical: no full-viewport strips
          maxHeight: '65vh',
          overflow: 'auto',
          bottom: taskbarH + 12 + depth * 72 + 'px',
          zIndex: String(1000000001 + depth)
        });

        openPanels.add(submenu);

        submenu.querySelectorAll('li.has-submenu').forEach(initSubMenu);
      };

      const scheduleHide = () => {
        clearOpen();
        clearHide();
        hideTimer = setTimeout(() => {
          if (item.matches(':hover')) return;
          for (const sm of openPanels) {
            if (sm.matches(':hover')) return;
          }

          // close this panel and any deeper panels that hang under it
          for (const sm of [...openPanels]) {
            if (isSelfOrDescendant(submenu, sm)) restorePanel(sm);
          }
        }, 220);
      };

      item.addEventListener('mouseenter', scheduleOpen);
      item.addEventListener('mouseleave', scheduleHide);
      submenu.addEventListener('mouseenter', () => { clearOpen(); clearHide(); });
      submenu.addEventListener('mouseleave', scheduleHide);

    const showRootMenu = () => {
      const rootUL =
      this.menu.querySelector(':scope > ul.vividMenu_mainUL') ||
      this.menu.querySelector(':scope > ul:not(.vividMenu_layout):not(.vividMenu_segments)');

      Object.assign(this.menu.style, {
        position: 'fixed',
        display: 'flex',
        visibility: 'visible',
        opacity: '1',
        left: '12px',
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

      this.menu.querySelectorAll('li.has-submenu').forEach((li) => {
        delete li.dataset.initDone;
      });
      prepareItems();
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

    prepareItems();
    this.menu.querySelectorAll('li.has-submenu').forEach(initSubMenu);

    document.addEventListener('click', (e) => {
      const onPanel = [...openPanels].some((p) => p.contains(e.target));
      if (!this.menu.contains(e.target) && !(btn && btn.contains(e.target)) && !onPanel) {
        closeAll();
      }
    });

    console.log('naVividMenu: ready');
  }
}
