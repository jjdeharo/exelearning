import Toast from './toasts/toast.js';
import ToastDefault from './toasts/generic/toastDefault.js';

export default class ToastManagement {
    constructor(app) {
        this.app = app;
        this.default = null;
    }

    /**
     *
     */
    init() {
        this.default = new ToastDefault(this);
    }

    /**
     *
     */
    createToast(data) {
        let tmpToastId = `tmp-toast-${eXeLearning.app.common.generateId()}`;
        let tmpToastElement = this.default.toastElement.cloneNode(true);
        tmpToastElement.id = tmpToastId;
        document
            .querySelector('body > .toasts-container')
            .append(tmpToastElement);
        let tmpToast = new Toast(this, tmpToastId);
        tmpToast.show(data);
        return tmpToast;
    }
}
