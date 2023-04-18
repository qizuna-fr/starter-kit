
import {useClickOutside, useTransition} from "stimulus-use";
import {Controller} from "@hotwired/stimulus";
import {enter , leave} from "el-transition";

export default class extends Controller {

    static targets = ['dropdown']

    connect() {
        useClickOutside(this , {
            element: this.dropdownTarget,
            events: ['mousedown'],
            onlyVisible: true
        })
    }

    close() {
        this.leave(this.dropdownTarget.getElementsByClassName('dropdown')[0]);
    }

    open() {
        this.enter(this.dropdownTarget.getElementsByClassName('dropdown')[0]);
    }

    toggle() {
        const dropdown = this.dropdownTarget.getElementsByClassName('dropdown')[0]
        const isHidden = dropdown.classList.contains('hidden')

        if(isHidden) {
            enter(dropdown)
        } else {
            leave(dropdown)
        }
    }

    clickOutside(e){
        e.preventDefault()
        leave(this.dropdownTarget.getElementsByClassName('dropdown')[0])
    }



}
