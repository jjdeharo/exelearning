import Modal from '../modal.js';

export default class ModalFilemanager extends Modal {
    constructor(manager) {
        let id = 'modalFileManager';
        let pluginParameter = 0;
        let titleDefault;
        super(manager, id, titleDefault, false);
        this.url =
            `${window.eXeLearning.symfony.baseURL}${window.eXeLearning.symfony.basePath}/filemanager/index/` +
            pluginParameter;
    }

    /**
     *
     * @param {*} data
     */
    show(data) {
        // Set title
        this.titleDefault = _('File Manager');
        let time = this.manager.closeModals() ? this.timeMax : this.timeMin;
        setTimeout(() => {
            data = data ? data : {};
            this.setTitle(this.titleDefault);
            this.setIframe();
            // Add class hide to iframe
            this.modalElementBody.classList.add('hide');
            // Show modal
            this.modal.show();
            // Remove class hide to iframe
            setTimeout(() => {
                this.modalElementBody.classList.remove('hide');
            }, 1000);
        }, time);
    }

    /**
     * Sets the filemanager iframe view
     *
     */
    setIframe() {
        let sessionId = eXeLearning.app.project.odeSession;
        let iframe = `<iframe  id="filemanageriframe" src="${this.url}/${sessionId}"></iframe>`;
        this.setBody(iframe);
    }
}
