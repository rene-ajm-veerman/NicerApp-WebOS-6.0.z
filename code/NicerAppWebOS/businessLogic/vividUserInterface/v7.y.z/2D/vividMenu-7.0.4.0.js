class naVividMenu {
  constructor(menuElId = 'siteMenu', openBtnId = 'btnShowStartMenu') {
    const menu = document.getElementById(menuElId);
    if (!menu) return;

    const openBtn = document.getElementById(openBtnId);

    if (menu.dataset.initialized) return;
    menu.dataset.initialized = 'true';

    // Add classes
    menu.querySelectorAll('li').forEach(li => {
      li.classList.add('menu-item');
      if (li.querySelector(':scope > ul')) li.classList.add('has-submenu');
    });
    menu.querySelectorAll('ul ul').forEach(ul => ul.classList.add('submenu'));
    /*
    if (menu.firstElementChild?.tagName === 'UL') {
      menu.firstElementChild.classList.add('menu-root');
    }*/
    $($('vividMenuLayout')[2]).addClass('menu-root');

    // Ultra-high z-index base (no override later)
    const baseZ = 990000000;
    menu.style.zIndex = baseZ;

    const submenus = menu.querySelectorAll('.submenu');
    submenus.forEach((sm, index) => {
      sm.style.zIndex = baseZ + (index + 1) * 100; // Big steps for deep levels
    });

    // Main menu positioning
    menu.style.position = 'fixed';
    menu.style.top = 'auto';
    menu.style.left = '40px';
    menu.style.width = 'fit-content';
    menu.style.bottom = '330px';
    menu.style.display = 'none';

    // Toggle main menu
    if (openBtn) {
      openBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        $('.vividMenu_mainUL',menu).css({display:'block'});
      });
    }

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && (!openBtn || e.target !== openBtn)) {
        menu.style.display = 'none';
        submenus.forEach(sm => sm.style.display = 'none');
      }
    });

    // Submenu hover with full bridging
    const openDelay = 300; // Reduced to 0 for immediate opening
    const closeDelay = 1000; // Increased to 500ms for longer grace period
    let openTimeout, closeTimeout;

    const initSubMenu = (item) => {
      const submenu = item.querySelector(':scope > .submenu');
      if (!submenu) return;

      submenu.style.display = 'none';
      submenu.style.position = 'absolute';

      const openSubmenu = () => {
        submenu.style.display = 'flex';
        submenu.style.opacity = '1';

        // Temporarily position off-screen to measure natural size
        submenu.style.left = '-9999px';
        submenu.style.top = '-9999px';
        submenu.style.right = 'auto';
        submenu.style.bottom = 'auto';

        // Reset to parent for accurate positioning
        submenu.style.left = '';
        submenu.style.top = '';
        submenu.style.right = '';
        submenu.style.bottom = '';

        const parentRect = item.getBoundingClientRect();

        // Available space on each side
        const spaceRight = window.innerWidth - parentRect.right;
        const spaceLeft = parentRect.left;
        const spaceBottom = window.innerHeight - parentRect.bottom;
        const spaceTop = parentRect.top;

        var sh = spaceRight > spaceLeft ? spaceRight : spaceLeft;
        var sv = spaceTop > spaceBottom ? spaceTop : spaceBottom;

        submenu.style.width = (
          (sh / 2.5) > parentRect.width * 3
          ? parentRect.width * 3
          : sh / 2.5
        ) + 'px';

        const submenuWidth = submenu.offsetWidth;
        const submenuHeight = submenu.offsetHeight;

        // Default: try right first (classic cascade)
        let bestH = 'right';
        let bestV = 'top';

        // Choose horizontal direction with most space
        if (spaceLeft > spaceRight && spaceLeft >= submenuWidth) {
            bestH = 'left';
        }

        // Choose vertical direction with most space (only if horizontal isn't viable)
        if (spaceBottom < submenuHeight && spaceTop > spaceBottom && spaceTop >= submenuHeight) {
            bestV = 'bottom';
        }

        // Apply best horizontal
        if (bestH === 'right') {
            submenu.style.left = '100%';
            submenu.style.right = 'auto';
        } else {
            submenu.style.left = 'auto';
            submenu.style.right = '100%';
        }

        // Apply best vertical
        if (bestV === 'top') {
            submenu.style.top = '0';
            submenu.style.bottom = 'auto';
        } else {
            submenu.style.top = 'auto';
            submenu.style.bottom = '0';
        }

        // Final safeguard: adjust if still overflowing
        const finalRect = submenu.getBoundingClientRect();
        if (finalRect.right > window.innerWidth) {
            submenu.style.left = 'auto';
            submenu.style.right = '0'; // Pull back
        }
        if (finalRect.bottom > window.innerHeight) {
            submenu.style.top = 'auto';
            submenu.style.bottom = '0';
        }

        // Viewport constraints
        submenu.style.maxHeight = `${window.innerHeight - 40}px`;
        //submenu.style.overflowY = 'auto';
      };

      const closeSubmenu = () => {
        submenu.style.display = 'none';
      };

      // Open on parent hover
      item.addEventListener('mouseenter', () => {
        clearTimeout(closeTimeout);
        openTimeout = setTimeout(openSubmenu, openDelay);
      });

      // Start close when leaving parent
      item.addEventListener('mouseleave', () => {
        clearTimeout(openTimeout);
        closeTimeout = setTimeout(closeSubmenu, closeDelay);
      });

      // *** Critical fix: Cancel close when entering submenu ***
      submenu.addEventListener('mouseenter', () => {
        clearTimeout(closeTimeout);
        clearTimeout(openTimeout); // in case delayed
      });

      // Close when finally leaving submenu
      submenu.addEventListener('mouseleave', () => {
        closeTimeout = setTimeout(closeSubmenu, closeDelay);
      });
    }

    // Removed initSubMenu(menu) as it's likely unnecessary for the container div
    menu.querySelectorAll('.vividMenu_mainUL .has-submenu').forEach(item => initSubMenu(item));
  }
}
