import {useClickOutside} from "stimulus-use";
import {Controller} from "@hotwired/stimulus";
import {enter, leave} from "el-transition";

export default class extends Controller {

    static targets = ['dropdown']

    connect() {
        useClickOutside(this, {
            element: this.dropdownTarget.getElementsByClassName('dropdown')[0],
            events: ['mousedown'],
            onlyVisible: true
        })

        this.dropdown = this.dropdownTarget.getElementsByClassName('dropdown')[0]
    }

    close() {
        this.leave(this.dropdown);
    }

    open() {
        this.enter(this.dropdown);
    }


    toggle() {
        const isHidden = this.dropdown.classList.contains('hidden')

        if (isHidden) {
            enter(this.dropdown)
        } else {
            leave(this.dropdown)
        }
    }

    clickOutside(e) {
        e.preventDefault()
        leave(this.dropdown)
    }


}
