(function (Drupal, once) {
  Drupal.behaviors.helloTabList = {
    attach(context) {
      once('hello-tab-list', '[data-tab-list]', context).forEach((tabListRoot) => {
        const tablist = tabListRoot.querySelector('[role="tablist"]');
        const tabs = Array.from(tabListRoot.querySelectorAll('[role="tab"]'));
        const panels = Array.from(tabListRoot.querySelectorAll('[role="tabpanel"]'));

        if (!tablist || !tabs.length) {
          return;
        }

        const activate = (targetTab) => {
          tabs.forEach((tab) => {
            const selected = tab === targetTab;
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            tab.setAttribute('tabindex', selected ? '0' : '-1');
          });

          panels.forEach((panel) => {
            panel.hidden = panel.id !== targetTab.getAttribute('aria-controls');
          });
        };

        tablist.addEventListener('click', (event) => {
          const tab = event.target.closest('[role="tab"]');

          if (tab && tabs.includes(tab)) {
            activate(tab);
          }
        });

        tablist.addEventListener('keydown', (event) => {
          const currentIndex = tabs.indexOf(event.target);

          if (currentIndex === -1) {
            return;
          }

          let nextIndex;

          switch (event.key) {
            case 'ArrowRight':
            case 'ArrowDown':
              nextIndex = (currentIndex + 1) % tabs.length;
              break;

            case 'ArrowLeft':
            case 'ArrowUp':
              nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
              break;

            case 'Home':
              nextIndex = 0;
              break;

            case 'End':
              nextIndex = tabs.length - 1;
              break;

            default:
              return;
          }

          event.preventDefault();
          tabs[nextIndex].focus();
          activate(tabs[nextIndex]);
        });
      });
    },
  };
})(Drupal, once);
