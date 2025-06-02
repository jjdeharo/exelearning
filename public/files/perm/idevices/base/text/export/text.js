/**
 * Form iDevice
 *
 * Released under Attribution-ShareAlike 4.0 International License.
 * Author: SDWEB - Innovative Digital Solutions
 *
 * License: http://creativecommons.org/licenses/by-sa/4.0/
 */

var $text = {

    ideviceClass: "textIdeviceContent",

    durationId: "textInfoDurationInput",
    durationTextId: "textInfoDurationTextInput",
    participantsId: "textInfoParticipantsInput",
    participantsTextId: "textInfoParticipantsTextInput",
    mainContentId: "textTextarea",
    feedbackTitleId: "textFeedbackInput",
    feedbackContentId: "textFeedbackTextarea",

    defaultBtnFeedbackText: "Show Feedback",

    /**
     * eXe idevice engine
     * Json idevice api function
     * Engine execution order: 1
     *
     * Get the base html of the idevice view
     *
     * @param {Object} data
     * @param {Number} accesibility
     * @param {String} template
     * @returns {String}
     */
    renderView: function (data, accessibility, template) {
        // Generate html content from data values
        let infoContentHTML = this.createInfoHTML(
            data[this.durationId] === "" ? "" : data[`${this.durationTextId}`],
            data[this.durationId],
            data[this.participantsId] === "" ? "" : data[this.participantsTextId],
            data[this.participantsId]
        );

        let mainContentHTML = data[this.mainContentId];

        const temp = document.createElement('div');
        temp.innerHTML = mainContentHTML;

        const btnDiv = temp.querySelector('.feedback-button');
        if (btnDiv) {
            const inputEl = btnDiv.querySelector('input.feedbackbutton');
            if (inputEl) {
                data[this.feedbackTitleId] = inputEl.value;
            }
            btnDiv.remove();
        }

        const fbDiv = temp.querySelector('.feedback.js-feedback');
        if (fbDiv) {
            const content = fbDiv.innerHTML
            data[this.feedbackContentId] = content;
            fbDiv.remove();
        }

        mainContentHTML = temp.innerHTML;
        data[this.mainContentId] = mainContentHTML;

        const feedbackContentHTML = data[this.feedbackContentId] === "" ?
            "" :
            this.createFeedbackHTML(
                data[this.feedbackTitleId] === "" ?
                    this.defaultBtnFeedbackText :
                    data[this.feedbackTitleId],
                data[this.feedbackContentId]
            );

        const activityContent = infoContentHTML
            + mainContentHTML
            + feedbackContentHTML
            + `<p class="clearfix"> </p>`;

        let htmlContent = `<div class="${this.ideviceClass}">`;
        htmlContent += this.createMainContent(activityContent);
        htmlContent += `</div>`;

        // Use template export/text.html
        // Insert the html content inside the template
        // Save html in database
        return template.replace("{content}", htmlContent);
    },


    /**
     * Json idevice api function
     * Engine execution order: 2
     *
     * Add the behavior and other functionalities to idevice
     *
     * @param {Object} data
     * @param {Number} accesibility
     * @returns {Boolean}
     */
    renderBehaviour(data, accesibility) {
        if (data.ideviceId) {
            const $btns = $(`#${data.ideviceId} .feedbacktooglebutton`);
            $btns.off('click');
            $btns.closest('.feedback-button').removeClass('clearfix');
            $btns.on('click', function () {
                $(this).parent().next().toggle();
            });
        }

        $(document).off('click', '.feedbackbutton');
        $(document).on('click', '.feedbackbutton', function () {
            $(this)
                .closest('.feedback-button').removeClass('clearfix')
                .closest('.idevice_node')
                .find('.js-feedback')
                .toggleClass('js-hidden');
        });
    },

    /**
     * Json idevice api function
     * Engine execution order: 3
     *
     * Initialize idevice parameters if necessary
     *
     * @param {Object} data
     * @param {Number} accesibility
     */
    init: function (data, accesibility) {

    },

    /**
     * Json idevice api function
     * Engine execution order: 3
     *
     * Initialize idevice parameters if necessary
     *
     * @param {String} content
     */
    createMainContent(content) {
        let html = `
      <div class="exe-text-activity">
        <div>
          ${content}
        </div>
      </div>`;
        return html;
    },

    /**
   * Json idevice api function
   * Engine execution order: 3
   *
   * Initialize idevice parameters if necessary
   *
   * @param {String} durationText
   * @param {String} durationValue
   * @param {String} participantsText
   * @param {String} participantsValue
   */
    createInfoHTML(durationText, durationValue, participantsText, participantsValue) {
        let html = `
      <dl>
        <div class="inline">
          <dt><span title="${durationText}">${durationText}</span></dt>
          <dd>${durationValue}</dd>
        </div>
        <div class="inline">
          <dt><span title="${participantsText}">${participantsText}</span></dt>
          <dd>${participantsValue}</dd>
        </div>
      </dl>`
        return html;
    },

    /**
     * html basic feedback template
     *
     * @param {String} title
     * @param {String} content
     */
    createFeedbackHTML(title, content) {
        let html = `
      <div class="iDevice_buttons feedback-button js-required">
        <input type="button" class="feedbacktooglebutton" value="${title}" />
      </div>
      <div class="feedback js-feedback js-hidden">
      ${content}
      </div>
      `;
        return html;
    }

}