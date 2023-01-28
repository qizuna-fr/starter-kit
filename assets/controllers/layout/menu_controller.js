import {useTransition} from "stimulus-use";
import {Controller} from "@hotwired/stimulus";

export default class extends Controller {

    static targets = [ 'menuContainer']

    connect() {
        useTransition(this, {
            element: this.menuContainerTarget,
            enterActive: 'transition ease-in-out duration-300 transform',
            enterFrom: '-translate-x-full',
            enterTo: 'translate-x-0',
            leaveActive: 'transition ease-in-out duration-300 transform',
            leaveFrom: 'translate-x-0',
            leaveTo: '-translate-x-full',
            hiddenClass: 'hidden',
            // set this, because the item *starts* in an open state
            transitioned: false,
        });
    }


    close() {
        this.leave();
        this.menuBackdropTarget.classList.toggle('hidden')
    }

    open() {
        this.menuBackdropTarget.classList.toggle('hidden')
        this.enter();
    }

    toggle() {
        this.toggleTransition();
    }

}
