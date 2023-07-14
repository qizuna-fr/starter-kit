import {Controller} from "@hotwired/stimulus";
import {enter, leave} from 'el-transition';

export default class extends Controller {

    connect() {
        console.log("✅notification" , this.element)
        const el = this.element

        const timer = setTimeout(function() {
            leave(el)
        }, 10000); // 1s = 1000ms

    }

    disconnect() {
        clearTimeout(timer)
    }

    close() {
        leave(this.element)
    }



}
