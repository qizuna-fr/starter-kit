import {Controller} from "@hotwired/stimulus";
import {enter, leave, toggle} from 'el-transition';

export default class extends Controller {

    static targets = ['menu']

    connect() {
        const menuItems = this.menuTarget.querySelectorAll('a[role="menuitem"]');
        menuItems.forEach(item => {
            item.addEventListener('click', this.onSelected.bind(this));
        });
    }

    toggle(e) {
        e.preventDefault()
        toggle(this.menuTarget)
    }

    onSelected(e) {
       leave(this.menuTarget)
    }



}
