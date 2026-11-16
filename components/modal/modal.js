(function (Drupal, once) {
  Drupal.behaviors.helloModal = {
    attach(context) {
      once('hello-modal-open', '[data-modal-open]', context).forEach((button) => {
        button.addEventListener('click', () => {
          const dialog = document.getElementById(button.dataset.modalOpen);

          if (dialog && typeof dialog.showModal === 'function') {
            dialog.showModal();
          }
        });
      });

      once('hello-modal-autoload', 'dialog.modal[data-modal-open-on-load]', context).forEach((dialog) => {
        if (typeof dialog.showModal === 'function') {
          dialog.showModal();
        }
      });
    },
  };
})(Drupal, once);
