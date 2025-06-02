import Modal from '../modal.js';

export default class ModalAbout extends Modal {
    constructor(manager) {
        let id = 'modalAbout';
        let titleDefault;
        super(manager, id, titleDefault, false);
    }

    /**
     *
     * @param {*} data
     */
    show(data) {
        // Set title
        this.titleDefault = _('About eXeLearning');
        let time = this.manager.closeModals() ? this.timeMax : this.timeMin;
        setTimeout(() => {
            data = data ? data : {};
            this.setTitle(this.titleDefault);
            this.modal.show();
        }, time);
    }
}
