export default class NavbarFile {
    constructor(menu) {
        this.menu = menu;
        this.button = this.menu.navbar.querySelector('#dropdownStyles');
    }

    /**
     *
     */
    setEvents() {
        this.setStyleManagerEvent();
    }

    /**************************************************************************************
     * LISTENERS
     **************************************************************************************/

    /**
     * Style Manager
     * Styles
     *
     */
    setStyleManagerEvent() {
        this.button.addEventListener('click', () => {
            if (eXeLearning.app.project.checkOpenIdevice()) return;
            this.styleManagerEvent();
        });
    }

    /**************************************************************************************
     * EVENTS
     **************************************************************************************/

    /**
     * Show Style Manager modal
     *
     */
    styleManagerEvent() {
        eXeLearning.app.modals.stylemanager.show(eXeLearning.app.themes.list);
    }
}
