export default class ModeButton {
    constructor() {
        this.mode = 'advanced';
        this.modeMenuHeadButton = document.querySelector('#exe-mode-check');
    }

    /**
     * Mode strings
     */
    modesText = {
        default: _('Default'),
        advanced: _('Advanced'),
    };

    /**
     * Init element
     *
     */
    init() {
        this.updateMode(this.mode);
        this.addEventClick();
    }

    /**
     * Add event click to button
     *
     */
    addEventClick() {
        this.modeMenuHeadButton.addEventListener('click', (event) => {
            switch (this.mode) {
                case 'default':
                    this.updateMode('advanced');
                    break;
                case 'advanced':
                    this.updateMode('default');
                    break;
            }
        });
    }

    /**
     * Set mode attributes
     *
     */
    updateMode(mode) {
        this.mode = mode;
        this.modeMenuHeadButton.setAttribute('mode', mode);
        this.modeMenuHeadButton.title = _('Mode:') + ' ' + this.modesText[mode];
        document.querySelector('body').setAttribute('mode', mode);
    }
}
