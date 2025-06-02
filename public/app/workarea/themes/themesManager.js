import ThemeList from './themeList.js';

export default class ThemesManager {
    constructor(app) {
        this.app = app;
        this.list = new ThemeList(this);
        this.symfonyURL = this.app.eXeLearning.symfony.basePath;
        this.selected = null;
    }

    /**
     *
     * @param {*} id
     * @param {*} save
     */
    async selectTheme(id, save, forceReload, isSync = false) {
        let themeSelected = this.getTheme(id);
        // Try to load the default theme if can't get the selected theme
        if (!themeSelected) {
            themeSelected = this.getTheme(eXeLearning.config.defaultTheme);
        }
        // Select the theme and save it in the database
        if (themeSelected) {
            let prevThemeSelected = this.selected; // Previous theme selected
            this.selected = themeSelected;
            if (
                !prevThemeSelected ||
                forceReload ||
                prevThemeSelected.id != this.selected.id
            ) {
                // In case of syncAction don't reload page
                if (isSync == false) {
                    await this.selected.select();
                } else {
                    await this.selected.select(true);
                }
            }
            if (save) {
                await this.app.user.preferences.apiSaveProperties({
                    theme: id,
                });
            }
        }
    }

    /**
     *
     */
    getThemeIcons() {
        if (this.selected.icons) {
            return this.selected.icons;
        } else {
            //return this.iconsDefault;
            return {};
        }
    }

    /**
     *
     * @param {*} id
     * @returns
     */
    getTheme(id) {
        return this.list.getThemeInstalled(id);
    }

    /**
     *
     */
    async loadThemesFromAPI() {
        await this.list.load();
    }
}
